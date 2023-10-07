<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

if (empty ( $action ) and empty ( $storeId ) ) {
    
    if(isset($_SERVER ['argv'] [1])){
        $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
        $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    }
    
    if(isset($_SERVER ['argv'] [2])){
        $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
        $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
    }
    
    $request = "System";
    
}
	
if(isset($storeId)){
    
    $db = new DbConnection();
    
    function saveLastLog($db, $idProductModule, $log){
    	 
    	if(empty($idProductModule)){
    		return false;
    	}
    	 
    	$log = json_encode($log);
    	 
    	$sql = "UPDATE  module_marketplace_products SET last_log = '{$idProductModule}' WHERE id = {$idProductModule}";
    	$query = $db->query($sql);
    
    	return true;
    	 
    }
    
    switch($action){
    	
    	case 'update_stock_available':
    		
    		$queryAP = $db->query("SELECT * FROM module_marketplace_products limit 100");
    		$products = $queryAP->fetchAll(PDO::FETCH_ASSOC);
    		foreach($products as $ind => $product){
    			
    			$sqlVerify = "SELECT id, ean, variation FROM available_products WHERE store_id = {$storeId} AND ean LIKE '{$product['seller_ean']}' ";
    			$verifyQuery = $db->query($sqlVerify);
    			$verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
    			
    			if(isset($verify['id'])){
// 	    			pre($verify);
	    			// somar todos os eans iguais
	    			$data = array(
	    				'quantity' => $product['seller_quantity'],
	    				'sale_price' => $product['seller_sale_price'] 
	    				
	    			);
	    			
	    			$queryUpdate = $db->update('available_products',
	    					array('store_id' , 'id', 'variation'), 
	    					array($storeId ,  $verify['id'], $verify['variation']), 
	    					$data
	    					);
	    			$logStock['update_stock_available'] = $data;
	    			
	    			if($queryUpdate->rowCount()){
	    				$db->update('available_products',
	    						array('store_id','id'),
	    						array($storeId, $verify['id']),
	    						array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
	    						);
	    					
	    				$imported++;
	    			
	    				$dataLog['update_available_products_marketplace'] = $logStock ;
	    				
	    				$db->insert('products_log', array(
	    						'store_id' => $storeId,
	    						'product_id' => $verify['id'],
	    						'description' => 'Atualização de Estoque Marketplcae',
	    						'user' => $request,
	    						'created' => date('Y-m-d H:i:s'),
	    						'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	    				));
	    					
	    				$sqlRelational = "SELECT product_id FROM product_relational WHERE store_id = {$storeId}
	    				AND product_relational_id = {$verify['id']} ";
	    				
	    				$queryRelational = $db->query($sqlRelational);
	    				
	    				while($productRelational =  $queryRelational->fetch(PDO::FETCH_ASSOC)){
	    					
	    					$db->update('available_products',
	    							array('store_id','id'),
	    							array($storeId, $productRelational['product_id']),
	    							array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
	    							);
	    				}
	    					
	    			}else{
	    				pre(222);
	    			}
	    			
    			}
    			
    		}
    			
    		break;
    		
    		
    	
    	case'add_available_product': 
    		$categoryModel = new CategoryModel($db, null);
    		$sqlStore = "SELECT * FROM stores WHERE id = {$storeId}";
    		$queryStore = $db->query($sqlStore);
    		$storeInfo = $queryStore->fetch(PDO::FETCH_ASSOC);
    		$productIds = is_array($productId) ? $productId : array($productId) ;
    		foreach($productIds as $i => $id){
    			
    			$queryAP = $db->query("SELECT * FROM module_marketplace_products WHERE id = {$id}");
    			$products = $queryAP->fetchAll(PDO::FETCH_ASSOC);
    			foreach($products as $ind => $product){
    				
    				$sqlSellerStore = "SELECT store FROM stores WHERE id = {$product['seller_store_id']}";
    				$querySellerStore = $db->query($sqlSellerStore);
    				$sellerStoreInfo = $querySellerStore->fetch(PDO::FETCH_ASSOC);
    				$storeNameParts = explode(' ', $sellerStoreInfo['store']);
    				$storeNameFriendly = titleFriendly($storeNameParts[0]);
    				
//     				$newSku =  trim($product['seller_sku']."-".trim($storeNameFriendly));
    				$newSku =  trim("SD".trim($product['seller_sku']));
//     				$newParentId =  trim($product['seller_parent_id']."-".trim($storeNameFriendly));
    				$newParentId =  trim("SD".trim($product['seller_parent_id']));
    				do{
	    				$sqlVerifySku = "SELECT count(sku) num_sku FROM available_products WHERE store_id = ? AND sku LIKE ?";
	    				$verifyQuery = $db->query($sqlVerifySku, array($storeId,  $newSku));
	    				$verifySku = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	    				if($verifySku['num_sku'] > 0){
	    					$numSku = $verifySku['num_sku'];
	    					$numSku++;
	    					$newSku .= "-{$numSku}";
	    					$newParentId .= "-{$numSku}";
	    				}
    				}while($verifySku['num_sku'] != 0);
    					
    				$sqlCategory = "SELECT * FROM category WHERE store_id = {$storeId} AND hierarchy
    				LIKE '{$product['seller_category']}' AND type LIKE 'Marketplace'";
    				$queryCategory = $db->query($sqlCategory);
    				$verifyCategory = $queryCategory->fetch(PDO::FETCH_ASSOC);
    				if(empty($verifyCategory['id'])){
    					$categoryModel->store_id = $product['seller_store_id'];
    					$categoryModel->hierarchy = $product['seller_category'];
    					$resCategory = $categoryModel->GetCategoriesName();
    					$categoryParentId = 0;
    					
    					if(empty($resCategory[0])){
    						echo $log =  "error|Can't get categories names from function {$product['seller_category']}";
    						saveLastLog($db, $product['id'], $log);
    						break;
    					}
    					foreach($resCategory as $j => $child){
    						$sql = "SELECT * FROM category WHERE store_id  = ? AND id  = ? AND parent_id = ?";
    						$query = $db->query($sql, array($product['seller_store_id'], $child['id'], $child['parent_id']));
    						$category = $query->fetch(PDO::FETCH_ASSOC);
    						
    						if(empty($category['category'])){
    							echo $log =  "error|Categories ids not found child: {$child['id']} parent: {$child['parent_id']}";
    							saveLastLog($db, $product['id'], $log);
    							break;
    						}
							$sql2 = "SELECT * FROM `category` WHERE `store_id` = {$storeId}
							AND category LIKE '{$category['category']}' AND parent_id = {$categoryParentId}";
							$query2 = $db->query($sql2);
							$resVerify = $query2->fetch(PDO::FETCH_ASSOC);
							if(empty($resVerify['category'])){
									$data = array(
											'store_id' => $storeId,
											'category' => $category['category'],
											'parent_id' => $categoryParentId,
											'hierarchy' => $category['hierarchy'],
											'type' => 'Marketplace'
									);
									pre($data);
									$query = $db->insert('category', $data);
									
									$categoryParentId = $db->last_id;
									
								}else{
									
									$categoryParentId = $resVerify['id'];
								}
	    						
    						}
    							
    					}
    					
    					$title = $product['seller_title'];
    					$title = implode(" ", array_unique(explode(" ", $title)));
    					
    					/**
    					 * Pode ter mesmo ean mas tem que ter variaçnao diferente
    					 * @var seller_variation
    					 */
    					$sqlVerify = "SELECT id, sku, quantity, price, sale_price, cost
    					FROM available_products WHERE store_id = ? AND ean LIKE ? AND variation LIKE ?";
    					$verifyQuery = $db->query($sqlVerify, array($storeId, $product['seller_ean'], $product['seller_variation']));
    					$verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
    					if(!empty($verify['id'])){
    					
    						
    						$id = $verify['id'];
    						
    						if(!empty($id)){
    								$db->update('module_marketplace_products', 'id', $product['id'], array(
    										'product_id_relationship' => $id
    								));
    							
    							
//     							$data = array(
//     									'title' => $title,
//     									'color' => $product['seller_color'],
//     									'variation_type' => $product['seller_variation_type'],
//     									'variation' => $product['seller_variation'],
//     									'reference' => $product['seller_reference'],
//     									'collection' => $product['seller_collection'],
//     									'quantity' => $product['seller_quantity'],
//     									'description' => $product['seller_description'],
//     									'price' => $product['seller_sale_price'],
//     									'sale_price' => $product['seller_sale_price'],
//     									'category' => $product['seller_category'],
//     									'brand' => $product['seller_brand'],
//     									'weight' => $product['seller_weight'],
//     									'height' => $product['seller_height'],
//     									'width' => $product['seller_width'],
//     									'length' => $product['seller_length'],
//     									'ean' => $product['seller_ean']
//     							);
    							
    							$data = array(
    									'quantity' => $product['seller_quantity'],
    									'price' => $product['seller_sale_price'],
    									'sale_price' => $product['seller_sale_price'],
    									'category' => $product['seller_category'],
    									'weight' => $product['seller_weight'],
    									'height' => $product['seller_height'],
    									'width' => $product['seller_width'],
    									'length' => $product['seller_length'],
    									'ean' => $product['seller_ean']
    							);
    							$query = $db->update('available_products', 'id', $id, $data);
    							
    							if($query){
    								echo "success|Produto Atualizado com sucesso!";
    							}
    						
    						
    						}
    						
    						
    					}else{
    						
    						$data = array(
    								'account_id' => $storeInfo['account_id'],
    								'store_id' => $storeId,
    								'sku' => $newSku,
    								'parent_id' => $newParentId,
    								'title' => $title,
    								'color' => $product['seller_color'],
    								'variation_type' => $product['seller_variation_type'],
    								'variation' => $product['seller_variation'],
    								'reference' => $product['seller_reference'],
    								'collection' => $product['seller_collection'],
    								'quantity' => $product['seller_quantity'],
    								'description' => $product['seller_description'],
    								'price' => $product['seller_sale_price'],
    								'sale_price' => $product['seller_sale_price'],
    								'category' => $product['seller_category'],
    								'brand' => $product['seller_brand'],
    								'weight' => $product['seller_weight'],
    								'height' => $product['seller_height'],
    								'width' => $product['seller_width'],
    								'length' => $product['seller_length'],
    								'ean' => $product['seller_ean'],
    						);
    						$queryAP = $db->insert('available_products', $data);
    						$imported++;
    						$id = $db->last_id;
    						if(!empty($id)){
    							
    							$db->update('module_marketplace_products', 'id', $product['id'], array(
    									'product_id_relationship' => $id
    							));
    					
    							$urlImages = getUrlImageFromId($db, $product['seller_store_id'], $product['seller_product_id']);
    							if(isset($urlImages[0])){
	    							$from = "/var/www/html/app_mvc/Views/_uploads/store_id_{$product['seller_store_id']}/products/{$product['seller_product_id']}";
	    							$to = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$id}";
	    							xcopyImageProductId($from,$to, $product['seller_product_id'], $id);
    							}else{
    								
    								$idDir = getIdImageDirFromParentId($db, $product['seller_store_id'], $product['seller_parent_id']);
    								if($idDir){
    									$from = "/var/www/html/app_mvc/Views/_uploads/store_id_{$product['seller_store_id']}/products/{$idDir}";
    									$to = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$id}";
    									xcopyImageProductId($from,$to, $idDir, $id);
    									
    								}
    								
    								
    							}
    							
    							updateImageThumbnail($db, $storeId, $id);
    							
    							$dataLog['import_insert_available_products'] = $data;
    							$db->insert('products_log', array(
    									'store_id' => $storeId,
    									'product_id' => $id,
    									'description' => 'Novo Produto para Marketplace',
    									'user' => $request,
    									'created' => date('Y-m-d H:i:s'),
    									'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
    							));
    						}
    						
    						
    						echo "success|Produto castrado com sucesso!";
    						
    				
	    				/**
	    				 * Importa Conjunto de Attributos e attributos 
	    				 */
	
	    				//cria o conjunto de atributos da categoria raiz
	    				if(!empty($product['seller_category'])){
	    					$catParts = explode('>', $product['seller_category']);
	    					$categoryRoot = is_array($catParts) ? trim($catParts[0]) : trim($catParts) ;
	    					$sqlSetAttr = "SELECT * FROM set_attributes WHERE store_id = '{$storeId}'
	    					AND root_category LIKE '{$categoryRoot}'";
	    					$querySetAttr = $db->query($sqlSetAttr);
	    					$setAttr = $querySetAttr->fetch(PDO::FETCH_ASSOC);
	    					if(empty($setAttr['id'])){
	    						$query = $db->insert('set_attributes', array(
	    								'store_id' => $storeId,
	    								'set_attribute' => $categoryRoot,
	    								'description' => $categoryRoot,
	    								'root_category' => $categoryRoot
	    						));
	    						$setAttrId = $db->last_id;
	    					}else{
	    						$setAttrId = $setAttr['id'];
	    					}
	    					
	    					if(!empty($setAttrId)){
		    					$sqlCategory = "SELECT * FROM `category` WHERE `store_id` = {$storeId}
		    					AND category LIKE '{$categoryRoot}' AND parent_id = 0";
		    					$queryCategory = $db->query($sqlCategory);
		    					$resCategory = $queryCategory->fetch(PDO::FETCH_ASSOC);
		    					if(!empty($resCategory['id'])){
		    						if(empty($resCategory['set_attribute_id'])){
				    					$query = $db->update('category', 
				    							array('store_id', 'id'),
				    							array($storeId, $resCategory['id']),
				    							array('set_attribute_id' => $setAttrId));
		    						}
		    					
		    					}
		    					
	    					}
	    					
	    				}
	    				
	    				//importa os valors dos atributos
	    				if(!empty($setAttrId)){
	    					$sqlAttrVal = "SELECT * FROM attributes_values WHERE store_id = {$product['seller_store_id']}
	    					AND product_id = {$product['seller_product_id']} and value != ''";
	    					$queryAttrVal = $db->query($sqlAttrVal);
	    					$attrValues = $queryAttrVal->fetchAll(PDO::FETCH_ASSOC);
	    					if(isset($attrValues[0])){
	    				
	    						foreach($attrValues as $k => $attrValue){
	    							//cria o atributo se não existir
	    							if(!empty($attrValue['attribute_id'])){
	    								$sqlAttr = "SELECT * FROM attributes WHERE store_id = {$storeId}
	    								AND alias LIKE '{$attrValue['attribute_id']}'";
	    								$queryAttr = $db->query($sqlAttr);
	    								$attr = $queryAttr->fetch(PDO::FETCH_ASSOC);
	    								if(empty($attr['id'])){
	    									$query = $db->insert('attributes', array(
	    											'store_id' => $storeId,
	    											'attribute' => !empty($attrValue['name']) ? $attrValue['name'] : $attrValue['attribute_id'],
	    											'description' => $attrValue['name'],
	    											'alias' => $attrValue['attribute_id']
	    									));
	    									$attrId = $db->last_id;
	    								}else{
	    									$attrId = $attr['id'];
	    								}
	    							}
	    							//cria o relacionamento do atributo com o conjunto de attributos se não existir
	    							if(!empty($attrId)){
	    								 $sqlAttrRel = "SELECT * FROM set_attributes_relationship WHERE store_id = {$storeId}
	    								AND attribute_id = {$attrId} AND set_attribute_id = {$setAttrId}";
	    								$queryAttrRel = $db->query($sqlAttrRel);
	    								$attrRel = $queryAttrRel->fetch(PDO::FETCH_ASSOC);
	    								if(empty($attrRel['attribute_id'])){
	    									$query = $db->insert('set_attributes_relationship', array(
	    											'store_id' => $storeId,
	    											'attribute_id' => $attrId,
	    											'set_attribute_id' => $setAttrId,
	    											'ind' => 0
	    									));
	    									$attrRelId = $db->last_id;
	    								}else{
	    									$attrRelId = $attrRel['id'];
	    								}
	    									
	    							}
	    							
	    							//importa o valor do attributo se não existir
	    							if(!empty($attrId)){
	    								$sqlAttrVerify = "SELECT * FROM attributes_values WHERE store_id = {$storeId}
	    								AND product_id = {$id} AND attribute_id = '{$attrValue['attribute_id']}'";
	    								$queryAttrVerify = $db->query($sqlAttrVerify);
	    								$attrVerify = $queryAttrVerify->fetch(PDO::FETCH_ASSOC);
	    								
	    								if(empty($attrVerify['id'])){
	    									$query = $db->insert('attributes_values', array(
	    											'store_id' => $storeId,
	    											'product_id' => $id,
	    											'id_attribute' => $attrId,
	    											'attribute_id' => $attrValue['attribute_id'],
	    											'name' => $attrValue['name'],
	    											'value' => $attrValue['value'],
	    											'marketplace' => "Marketplace"
	    									));
	    									
	    									$attrValId = $db->last_id;
	    								}else{
	    									
	    									if(empty($attrVerify['value']) && !empty($attrValue['value'])){
	    										$query = $db->update('attributes_values',
	    												array('store_id', 'id'),
	    												array($storeId, $attrVerify['id']),
	    												array('value' => $attrValue['value']
	    										));
	    										
	    									}
	    									
	    									$attrValId = $attrRel['id'];
	    								}
	    								
	    							}
	    								
	    						}
	    						
	    					}
	    					
	    				}
	    				
    					echo "success|Produto atualizado com sucesso!";
    					
    				}
    				
    			}
    			
    		}
    		
    		break;
    	
    }
    	
      
}

// $sql = "SELECT * FROM category WHERE store_id = '{$product['seller_store_id']}'
// AND id = {$child['id']}";
// $query = $db->query($sql);
// $category = $query->fetch(PDO::FETCH_ASSOC);
// pre($category);
 