<?php
header("Content-Type: text/html; charset=utf-8");
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/../Models/Products/PublicationsModel.php';
require_once $path .'/functions.php';


$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

if (empty ( $action ) and empty ( $storeId )) {
	if(isset($_SERVER ['argv'] [1])){
		$paramAction = explode ( "=", $_SERVER ['argv'] [1] );
		$action = $paramAction [0] == "action" ? $paramAction [1] : null;
	}
	if(isset($_SERVER ['argv'] [2])){
		$paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
		$storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
	}
	if(isset($_SERVER ['argv'] [3])){
		$paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
		$accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
	}
	
	$request = "System";
}
if(isset($storeId)){
    
    $db = new DbConnection();
    
	switch($action){
	    case "generate_ean":
	        
	        
	        if(!empty($productId)){
	            echo "success|";
	            echo IncluiDigito($productId);
	        }
	        break;
	    
	    case 'update_price_manager':
	        $fixed = isset($_REQUEST['fixed']) && !empty($_REQUEST['fixed']) ? validatePrice($_REQUEST['fixed']) : null ;
	        $percent = isset($_REQUEST['percent']) && !empty($_REQUEST['percent']) ? intval($_REQUEST['percent']) / 100 : null ;
	        $action_price = isset($_REQUEST['action_price']) && !empty($_REQUEST['action_price']) ? $_REQUEST['action_price'] : null ;
	        $brand = isset($_REQUEST['brand']) && !empty($_REQUEST['brand']) ? trim($_REQUEST['brand']) : null ;
	        if(isset($brand) && isset($action_price)){
	            if(isset($fixed) OR isset($percent)){
    	            $sql = "SELECT id, sku, sale_price FROM `available_products`
    				WHERE store_id = {$storeId} AND `brand` LIKE '{$brand}'";
    	            $query = $db->query($sql);
    	            $products = $query->fetchAll(PDO::FETCH_ASSOC);
    	            if(isset($products[0])){
        	            foreach($products as $k => $product){
        	                pre($product);
        	                $newPrice = $product['sale_price'];
        	                if($action_price == 'increase'){
            	                if(isset($fixed)){
            	                    $newPrice = $newPrice + $fixed;
            	                    $newPrice = validatePrice($newPrice);
            	                    $products[$k]['new_price'] = $newPrice;
            	                }
            	                if(isset($percent)){
            	                    $newPrice = $newPrice + ( $percent * $newPrice );
            	                    $newPrice = validatePrice($newPrice);
            	                    $products[$k]['new_price'] = $newPrice;
            	                }
        	                }
        	                if($action_price == 'decrease'){
        	                    if(isset($fixed)){
        	                        $newPrice = $newPrice - $fixed;
        	                        $newPrice = validatePrice($newPrice);
        	                        $products[$k]['new_price'] = $newPrice;
        	                    }
        	                    if(isset($percent)){
        	                        $newPrice = $newPrice - ( $percent * $newPrice );
        	                        $newPrice = validatePrice($newPrice);
        	                        $products[$k]['new_price'] = $newPrice;
        	                    }
        	                }
        	                if($newPrice != $product['sale_price']){
        	                    $data = array('sale_price' => $newPrice);
        	                    
        	                    $query = $db->update('available_products', array('store_id', 'id'), array($storeId, $product['id']), $data);
        	                    if($query->rowCount()){
        	                        $queryUpdateAP = $db->update('available_products',
        	                            array('store_id','id'),
        	                            array($storeId, $product['id']),
        	                            array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
        	                            );
        	                        $sqlRelational = "SELECT product_id FROM product_relational WHERE store_id = {$storeId} AND product_relational_id = {$product['id']} ";
        	                        $queryRelational = $db->query($sqlRelational);
        	                        while($productRelational =  $queryRelational->fetch(PDO::FETCH_ASSOC)){
        	                            $queryUpdateAP =  $db->update('available_products',
        	                                array('store_id','id'),
        	                                array($storeId, $productRelational['product_id']),
        	                                array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
        	                                );
        	                        }
        	                        $dataLog['update_price_manager'] =  $products[$k];
        	                        $db->insert('products_log', array(
        	                            'store_id' => $storeId,
        	                            'product_id' => $product['id'],
        	                            'description' => 'Atualização de Preços',
        	                            'user' => $request,
        	                            'created' => date('Y-m-d H:i:s'),
        	                            'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
        	                        ));
        	                        echo "success|";
        	                        pre($dataLog);
        	                    }
        	                    
        	                }
        	               
        	            }
    	            }
    	            
    	        }
	            
	        }
	        
	        
	        
	        
	        break;
	        
	        
		
		case 'update_price':
			
			if(empty($productId)){
				return false;
			}
			
			$newPrice = validatePrice($_REQUEST['new_price']);
			
			if($newPrice < 1){
				return false;
			}
			
			$data = array(
				'sale_price' => $newPrice
			);
			$query = $db->update('available_products', array('store_id', 'id'), array($storeId, $productId), $data);
			
			if($query->rowCount()){
				echo "success|";
				pre($data);
			}
			break;
		
		case 'update_publications':
			
			$countAmazon = $countSkyhub = $countMeli = $countEcommerce = 0 ;
			$publicationsModel = new PublicationsModel($db);
			$publicationsModel->store_id = $storeId;
			$publicationsModel->user = $request;
			
			
			$sqlVerifyTmp = "SELECT * FROM az_products_feed WHERE store_id = {$storeId} ";
			$queryVerify = $db->query($sqlVerifyTmp);
			while($productsTmp = $queryVerify->fetch(PDO::FETCH_ASSOC)){
				$publicationsModel->publication_code = $productsTmp['az_ASIN'];
				$publicationsModel->product_id = $productsTmp['product_id'];
				$publicationsModel->sku = $productsTmp['sku'];
				$publicationsModel->marketplace = 'Amazon';
				$publicationsModel->url ="https://www.amazon.com.br/dp/{$productsTmp['az_ASIN']}";
				$publicationsModel->Save();
				$countAmazon++;
			
			}
			pre($countAmazon);
			
// 			$sqlVerifyTmp = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId} ";
// 			$queryVerify = $db->query($sqlVerifyTmp);
// 			while($productsTmp = $queryVerify->fetch(PDO::FETCH_ASSOC)){
				
// 				$selectId = "SELECT id, sku FROM `available_products`
// 				WHERE store_id = {$storeId} AND `sku` LIKE '{$productsTmp['sku']}'";
// 				$queryId = $db->query($selectId);
// 				$resId = $queryId->fetch(PDO::FETCH_ASSOC);
// 				if(!empty($resId['id'])){
// 					$publicationsModel->publication_code = $productsTmp['product_id'];
// 					$publicationsModel->product_id = $resId['id'];
// 					$publicationsModel->sku = $resId['sku'];
// 					$publicationsModel->marketplace = 'Ecommerce';
// 					$publicationsModel->Save();
// 					$countEcommerce++;
// 				}
				
// 			}
// 			pre($countEcommerce);
			
			$sqlProducts = "SELECT * FROM module_skyhub_products WHERE store_id = {$storeId}";
			$queryProducts = $db->query($sqlProducts);
			$products = $queryProducts->fetchAll(PDO::FETCH_ASSOC);
			if(!empty($products)){
				
				foreach($products as $kp => $product){
					
					$sqlVariations = "SELECT * FROM module_skyhub_products_variations
					WHERE store_id = {$storeId} AND id_product = '{$product['id']}'";
					$queryVariations = $db->query($sqlVariations);
					$productVariations = $queryVariations->fetchAll(PDO::FETCH_ASSOC);
					if(count($productVariations) > 0 ){
						foreach($productVariations as $kv => $productVariation){
							$publicationsModel->publication_code = $productVariation['id'];
							$publicationsModel->product_id = $productVariation['product_id'];
							$publicationsModel->sku = $productVariation['sku'];
							$publicationsModel->marketplace = 'B2W';
							$publicationsModel->Save();
							$countSkyhub++;
						}
					}else{
						$publicationsModel->publication_code = $product['id'];
						$publicationsModel->product_id = $product['product_id'];
						$publicationsModel->sku = $product['sku'];
						$publicationsModel->marketplace = 'B2W';
						$publicationsModel->Save();
						$countSkyhub++;
					}
				}
			}
			$sqlProduct = "SELECT * FROM `ml_products` WHERE store_id = {$storeId}";
			$query = $db->query($sqlProduct);
			while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
				
				$sql = "SELECT * FROM ml_products_attributes WHERE store_id = {$storeId}
				AND product_id = {$rowProduct['id']} AND status != 'delete' GROUP BY variation_id";
				$queryAttr = $db->query($sql);
				$num_rows = $queryAttr->rowCount();
				
				if($num_rows > 0){
					while($row =  $queryAttr->fetch(PDO::FETCH_ASSOC)){
						$selectId = "SELECT id FROM `available_products` 
						WHERE store_id = {$storeId} AND `sku` LIKE '{$row['sku']}'";
						$queryId = $db->query($selectId);
						$resId = $queryId->fetch(PDO::FETCH_ASSOC);
						if(!empty($resId['id'])){
							$publicationsModel->publication_code = $row['product_id'];
							$publicationsModel->product_id = $resId['id'];
							$publicationsModel->sku = $row['sku'];
							$publicationsModel->marketplace = 'Mercadolivre';
							$publicationsModel->url = $rowProduct['permalink'];
							$publicationsModel->Save();
							$countMeli++;
						}
					}
				}else{
					$selectId = "SELECT id FROM `available_products`
					WHERE store_id = {$storeId} AND `sku` LIKE '{$rowProduct['sku']}'";
					$queryId = $db->query($selectId);
					$resId = $queryId->fetch(PDO::FETCH_ASSOC);
					if(!empty($resId['id'])){
						$publicationsModel->publication_code = $rowProduct['id'];
						$publicationsModel->product_id = $resId['id'];
						$publicationsModel->sku = $rowProduct['sku'];
						$publicationsModel->marketplace = 'Mercadolivre';
						$publicationsModel->url = $rowProduct['permalink'];
						$publicationsModel->Save();
						$countMeli++;
					}
				}
				
			}
			break;
		
		case 'update_color_brand':
			$sql = "SELECT count(*) as count, brand FROM available_products WHERE store_id = {$storeId} AND brand != '' group by brand";
			$query = $db->query($sql);
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $key => $value){
				pre($value['count']."  --  ".$value['brand']);
				$sqlUpdate = "UPDATE available_products SET color = '".trim($value['color'])."', brand = '".trim($value['brand'])."'
						WHERE store_id = {$storeId} AND id = {$value['id']}";
			}
			
			break;
	    
	    case "copy_available_products":
	        
	        if(isset($productId)){
	            
	            $productIds = is_array($productId) ? $productId : array($productId) ;
	            
	            foreach($productIds as $ind => $id){
	            	
	            	$rand = rand(3, 3);
	        
    	           	$sql = "INSERT INTO `available_products`(`account_id`,`store_id`, `sku`, `parent_id`,  `title`, `color`, `variation_type`, `variation`, `brand`, `reference`, 
                    `collection`, `category`, `quantity`, `price`, `start_promotion`, `end_promotion`, `sale_price`, `promotion_price`, `cost`, 
                    `weight`, `height`, `width`, `length`, `ean`, `ncm`, `description`)
        
                    SELECT `account_id`, `store_id`, CONCAT(sku, '-copy-{$rand}') as sku, CONCAT(parent_id, '-copy-{$rand}') as parent_id,`title`, `color`, `variation_type`, `variation`, `brand`, `reference`, `collection`, `category`, `quantity`, `price`, 
                    `start_promotion`, `end_promotion`, `sale_price`, `promotion_price`, `cost`, `weight`,  `height`, `width`, `length`, `ean`, `ncm`, `description` 
                    FROM `available_products` WHERE store_id = {$storeId} AND id = {$id}";
        	        $query = $db->query($sql);
        	        $stmt= $db->query("SELECT LAST_INSERT_ID()");
        	        $newProductId = $stmt->fetchColumn();
        	        if($query){
        	            
        	            if(!empty($newProductId)){
        	                echo "success|Produto copiado com sucesso!|{$newProductId}";
        	            }else{
        	                echo "error|Erro ao inserir novo produto";
        	            }
        	           
        	        }else{
        	            echo "error|Erro ao copiar produto";
        	        }
    	        
	            }
	        
	        }
	        
	        break;
	        
	    case "copy_available_products_all":
	        
	        if(isset($productId)){
	            
	            $productIds = is_array($productId) ? $productId : array($productId) ;
	            
	            foreach($productIds as $ind => $id){
	                
	                $rand = rand(3, 3);
	                
	                $sql = "INSERT INTO `available_products`(`account_id`,`store_id`, `sku`, `parent_id`,  `title`, `color`, `variation_type`, `variation`, `brand`, `reference`,
                    `collection`, `category`, `quantity`, `price`, `start_promotion`, `end_promotion`, `sale_price`, `promotion_price`, `cost`,
                    `weight`, `height`, `width`, `length`, `ean`, `ncm`, `description`)
                    
                    SELECT `account_id`, `store_id`, CONCAT(sku, '-copy-{$rand}') as sku, CONCAT(parent_id, '-copy-{$rand}') as parent_id,`title`, `color`, `variation_type`, `variation`, `brand`, `reference`, `collection`, `category`, `quantity`, `price`,
                    `start_promotion`, `end_promotion`, `sale_price`, `promotion_price`, `cost`, `weight`,  `height`, `width`, `length`, `ean`, `ncm`, `description`
                    FROM `available_products` WHERE store_id = {$storeId} AND id = {$id}";
	                $query = $db->query($sql);
	                $stmt= $db->query("SELECT LAST_INSERT_ID()");
	                $newProductId = $stmt->fetchColumn();
	                if($query){
	                    
        	            $sqlAttr = "INSERT INTO `attributes_values`(`store_id`, `product_id`, `id_attribute`, `attribute_id`, `name`, `value`, `marketplace`)
                            SELECT `store_id`, {$newProductId} as product_id, `id_attribute`, `attribute_id`, `name`, `value`, `marketplace` FROM `attributes_values`
                            WHERE store_id = {$storeId} AND product_id = {$id}";
        	            $queryAttr = $db->query($sqlAttr);
	                    
	                    if(!empty($newProductId)){
	                        
        	            	$urlImages = getUrlImageFromId($db, $storeId, $id);
        	            	if(isset($urlImages[0])){
        	            		$from = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$id}";
        	            		$to = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$newProductId}";
        	            		xcopyImageProductId($from,$to, $id, $newProductId);

        	            		updateImageThumbnail($db, $storeId, $newProductId);
        	            	}
	                        
	                    }
	                    
	                    echo "success|Produto copiado com sucesso!|{$newProductId}";
	                }else{
	                    echo "error|Erro ao copiar produto";
	                }
	                
	            }
	            
	        }
	        
	        break;
	        
	        case "delete_product":
	        	
	        	$userId = isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null ;
	        	if(!isset($userId)){
	        		echo "error|Você não tem permissão para excluír o produto...";
// 	        		exit;
	        	}
	        	
// 	        	if(!verifyActionPermisssion($db, $storeId, 'products', 'delete', $userId)){
// 	        		echo 123;die;
		        	$productIds = is_array($productId) ? $productId : array($productId) ;
		        	
		        	if(!empty ( $productIds )){
		        		
	        			foreach($productIds as $ind => $id){
	        				
	        				$sql = "DELETE FROM `available_products` WHERE store_id = {$storeId} AND id =  {$id}";
	        				$db->query($sql);
	        				$sql = "DELETE FROM `attributes_values` WHERE store_id = {$storeId} AND product_id =  {$id}";
	        				$db->query($sql);
	        				$sql = "DELETE FROM `products_log` WHERE store_id = {$storeId} AND product_id =  {$id}";
	        				$db->query($sql);
	        				
	        				$productImagePath = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$id}";
	        				
	        				shell_exec("rm -rf \"{$productImagePath}\" ");
	        				
	        				echo "success|{$id}";
	        			}
	        			
		        	}else{
		        		echo "warning|Você não tem permissão para excluír o produto...";
		        		exit;
		        	}
		        	
// 	        	}else{
// 	        		echo "sem permissão...";
// 	        	}
	        	break;
	        	
	        	
        	case "delete_multiple_products":
      				
        		if($storeId == 7 ){
        			die;
        			$count = 0;
        			$sql = "SELECT id, sku, parent_id, title, category, brand FROM `available_products` WHERE `store_id` = {$storeId}";
        			$query = $db->query($sql);
        			$products = $query->fetchAll(PDO::FETCH_ASSOC);
        			
        			foreach($products as $key => $product){
	        			if(!empty ( $product['id'] )){
	        				echo $count++;
	        				echo "<br>";
        					echo $sql = "DELETE FROM `available_products` WHERE store_id = {$storeId} AND id =  {$product['id']}";
        					$db->query($sql);
        					echo $sql = "DELETE FROM `attributes_values` WHERE store_id = {$storeId} AND product_id =  {$product['id']}";
        					$db->query($sql);
        					echo $sql = "DELETE FROM `products_log` WHERE store_id = {$storeId} AND product_id =  {$product['id']}";
        					$db->query($sql);
        					echo "<br>";
        					
        					$productImagePath = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$id}";
        					 
        					shell_exec("rm -rf \"{$productImagePath}\" ");
	        	
	        			}
        			}
        		}

        		break;
        		
        		
        	case "expire_products_log":
        	    
        	    $sql = "DELETE FROM `products_log` WHERE created <  '".date("Y-m-d H:i:s", strtotime("-45 day"))."'";
        	    $query = $db->query($sql);
        	    
        	    
        	    
        	    break;
	}
	
}