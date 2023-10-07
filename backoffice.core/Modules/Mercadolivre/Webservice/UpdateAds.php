<?php
// header("Access-Control-Allow-Origin: *");
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/ProductDescriptionModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Api/CategoriesRestModel.php';
require_once $path .'/../Models/Api/ItemsRestModel.php';
require_once $path .'/../Models/Adverts/ItemsModel.php';
require_once $path .'/../Models/Map/MlCategoryModel.php';
require_once $path .'/functions.php';


$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;
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
    
    require_once $path .'/verifyToken.php';
    
	switch($action){
	    
	    case 'update_image':
	        $itemsModel->product_id =  $product['id'];
	        $pictures = $itemsModel->getPictures();
	        
	        pre( array('pictures' => $pictures));
	        
	        $itemsRestModel->pictures = array('pictures' => $pictures);
	        
	        $res = $itemsRestModel->putItemPictures();
	        pre($res);
	        break;
	    
	    case 'verify_image':
	        $itemsModel = new ItemsModel($db, null, $storeId);
	        $mlCategory = new MlCategoryModel($db, null, $storeId);
	        $itemsRestModel = new ItemsRestModel($db, null, $storeId);
	        
	        $totalUpdated = 0;
	        
	        $adsId = isset($_REQUEST["ads_id"])  ? $_REQUEST["ads_id"] : NULL ;
	        
	        if(isset($adsId)){
	            $adsIds = is_array($adsId) ? $adsId : array($adsId) ;
	            foreach($adsIds as $i => $adsId){
	                // core atualizacao ml products
	                $sqlProduct = "SELECT * FROM `ml_products` WHERE store_id = {$storeId} AND id = {$adsId}";
	                $query = $db->query($sqlProduct);
	                while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
	                    $sql = "SELECT * FROM ml_products_attributes WHERE store_id = {$storeId}
			            	AND product_id = {$rowProduct['id']} AND status != 'delete' AND name LIKE 'picture_ids'";
	                    $queryAttr = $db->query($sql);
	                    $variations = $queryAttr->fetchAll(PDO::FETCH_ASSOC);
	                    
	                    pre($variations);
	                    
	                    foreach($variations as $k => $variation){
	                        
	                       $itemsRestModel->picture_id = $variation['value'];
	                        
	                       $res =  $itemsRestModel->getPictureErrors();
	                       
	                       pre($res);
	                       
	                    }
	                    
	                }
	                
	            }
	            
	            
	        }
	        
	        break;
	    
	    case "update_description":
	        
	        $templateDescription = $path ."/../Models/Adverts/Templates/ItemDescriptionStoreId_{$storeId}.php";
	        
	        if(file_exists($templateDescription)){
	            
	            require_once $templateDescription;
	            
	        }else{
	            require_once $path ."/../Models/Adverts/Templates/ItemDescriptionDefault.php";;
	        }
	        
	        $availableProducts = new AvailableProductsModel($db);
	        $itemsRestModel = new ItemsRestModel($db, null, $storeId);
	        $itemDescriptionModel = new ItemDescriptionModel($db, null, $storeId);
	        
	        
	        $adsId = isset($_REQUEST["ads_id"])  ? $_REQUEST["ads_id"] : NULL ;
	        
	        if(isset($adsId)){
	            
	            
	            $adsIds = is_array($adsId) ? $adsId : array($adsId) ;
	            foreach($adsIds as $i => $adsId){
	                
	                
	                $sqlProduct = "SELECT * FROM `ml_products` WHERE store_id = {$storeId} AND id = {$adsId}";
	                $query = $db->query($sqlProduct);
	                while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
	                    
	                    $selectQtd = "SELECT id, sku, parent_id, blocked
	                                FROM `available_products` WHERE store_id = {$storeId} AND `sku` LIKE '{$rowProduct['sku']}'";
	                    $queryQtd = $db->query($selectQtd);
	                    $product = $queryQtd->fetch(PDO::FETCH_ASSOC);
	                    
	                    if(isset($product['sku'])){
	                        
	                        $availableProducts->store_id = $storeId;
	                        $availableProducts->id =  $product['id'];
	                        $availableProducts->category = $product['category'];
	                        $setAttributeId = $availableProducts->getSetAttributeRelationship();
	                        
	                        $itemDescriptionModel->product_id = $product['id'];
	                        $itemDescriptionModel->sku = $product['sku'];
	                        $itemDescriptionModel->parent_id = $product['parent_id'];
	                        $itemDescriptionModel->category_id = $rowProduct['category_id'];
	                        $itemDescriptionModel->set_attribute_id = $setAttributeId;
	                        $itemDescriptionModel->getTemplateDescription();
	                        
	                        $itemsRestModel->item_id = "MLB".$rowProduct['id'];
// 	                        pre($itemDescriptionModel->description);
	                        $description = strip_tags($itemDescriptionModel->description);
// 	                        pre($description);
	                        $itemsRestModel->description = array("plain_text" => "{$description}");
	                        
	                        $resDesc = $itemsRestModel->putItemDescription();
	                        
	                    }
	                    
	                }
	              
	            }
	            if($resDesc){
	               echo "success|Descrição atualizado com sucesso!|{$product['sku']}\n";
	            }  
	            
	        }
	        
	        break;
		
	    case 'update_stock':
	       
	        $itemsModel = new ItemsModel($db, null, $storeId);
	        $mlCategory = new MlCategoryModel($db, null, $storeId);
	        $itemsRestModel = new ItemsRestModel($db, null, $storeId);
	        $salePriceModel = new SalePriceModel($db, null, $storeId);
	        $salePriceModel->marketplace = "Mercadolivre";
	        
	        $totalUpdated = 0;
	        
	        $syncId =  logSyncStart($db, $storeId, "Mercadolivre", $action, "Atualização de Estoque e Preço Mercadolivre", $request);
	        
	        $dateFrom =  date("Y-m-d H:i:s",  strtotime("-3 hour") );
	        
	        $sql = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}'  AND parent_id != ''  GROUP BY parent_id";
	       
	        if(isset($productId)){
	            $sql = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
	        }
	        
	        if(isset($_GET['all'])){
	            $sql = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} GROUP BY parent_id ORDER BY id DESC";
	        }
	        
	        $queryAP = $db->query($sql);
	        $queryAP->rowCount();
	        $rowsAP = $queryAP->fetchAll(PDO::FETCH_ASSOC);
	        
	        foreach($rowsAP as $j => $rowAP){
	            
	            $sqlSKU = "SELECT id, sku FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$rowAP['parent_id']}'  order by quantity desc";
	            $querySKU = $db->query($sqlSKU);
	            $skus = $querySKU->fetchAll(PDO::FETCH_ASSOC);
	            foreach($skus as $j => $skuAP){
	               
	                // core atualizacao ml products
	                $sqlProduct = "SELECT * FROM `ml_products` WHERE store_id = {$storeId} AND sku LIKE '".trim($skuAP['sku'])."'";
	                $query = $db->query($sqlProduct);
	                while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
	                    
	                   
	                    if($rowProduct['flag_import_variations'] == 1){
	                        
	                       $selectQtd = "SELECT id, sku, quantity, sale_price, promotion_price, blocked, category
                                FROM `available_products` WHERE store_id = {$storeId} AND `sku` LIKE '".trim($rowProduct['sku'])."'";
	                        $queryQtd = $db->query($selectQtd);
	                        $resStockPrice = $queryQtd->fetch(PDO::FETCH_ASSOC);
	                        
	                        if(isset($resStockPrice['sku'])){
	                            $mlCategory->hierarchy = $resStockPrice['category'];
	                            
	                            $mlCategoryRel = $mlCategory->getCategoryRelationship();
	                            
	                            $itemsModel->attribute_types = $mlCategoryRel['attribute_types'];
	                            
	                            $itemsModel->item_id = $rowProduct['id'];
	                            
	                            $itemsModel->sku = trim($resStockPrice['sku']);
	                            
	                            $qtd = $resStockPrice['quantity'] > 0 ? $resStockPrice['quantity'] : 0 ;
	                            
	                            $salePriceModel->sku = trim($resStockPrice['sku']);
	                            
	                            $salePriceModel->product_id = $resStockPrice['id'];
	                            
	                            $salePrice = $salePriceModel->getSalePrice();
	                            
	                            $stockPriceRel = $salePriceModel->getStockPriceRelacional();
	                            
	                            $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
	                            
	                            $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
	                            
	                            if ($resStockPrice['blocked'] == "T"){
	                                $qtd = 0;
	                                echo "error|Produto Bloqueado...";
	                            }
	                            
	                            if($qtd > 0){
	                            	$itemsModel->price = $salePrice;
	                            }
	                            
	                            $itemsModel->available_quantity = $qtd;
	                            
	                            $itemsModel->status = $qtd > 0 ? 'active' : 'paused';
	                           
	                            $itemStockPrice = $itemsModel->getItemStockPrice();
	                            if(!empty($itemStockPrice)){ 
	                            	
// 	                            	unset($itemStockPrice['price']);
	                            	
	                                $itemsRestModel->item_id = $rowProduct['id'];
	                                $itemsRestModel->item = $itemStockPrice;
// 	                                pre($itemStockPrice); 
	                                $result = $itemsRestModel->putItem();
	                                if($result['httpCode'] == 200){
	                                    
	                                    saveItem ($db, $storeId, $result ['body'], $rowProduct['sku'] );
	                                    
	                                    if(!empty($result ['body']->variations)){
	                                        saveItemVariations ($db, $storeId, $result ['body']->id, $result ['body']->variations );
	                                    }
	                                    $sold_quantity = intval($result['body']->sold_quantity);
	                                    $sold_quantity = $sold_quantity > 0 ? $sold_quantity : 0 ;
	                                    $db->update('ml_products',
	                                        array('store_id','id'),
	                                        array($storeId, $rowProduct['id']),
	                                        array('flag' => 2,  
	                                        	'updated' => date('Y-m-d H:i:s'), 
	                                        	'httpCode' =>   $result['httpCode'], 
	                                        	'message' => 'success',
	                                            'sold_quantity' => $sold_quantity,
	                                        	'logistic_type' => ''
	                                        ));
	                                    
	                                    $totalUpdated++;
	                                    
	                                    $dataLog = array();
	                                    $dataLog['update_stock_price_mercadolivre'] = array(
	                                    		'request' => $itemsRestModel->item,
	                                    		'result' => $result['httpCode']
	                                    );
	                                    $db->insert('products_log', array(
	                                    		'store_id' => $storeId,
	                                    		'product_id' => $resStockPrice['id'],
	                                    		'description' => "Mercadolivre Anúncio Atualizado MLB {$rowProduct['id']}",
	                                    		'user' => $request,
	                                    		'created' => date('Y-m-d H:i:s'),
	                                    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	                                    ));
	                                    
	                                    if( $request != "System"){
	                                       echo "success|Produto atualizado com sucesso!|{$resStockPrice['sku']}|{$result['body']->permalink}|\n";
	                                    }
	                                }else{
	                                    
	                                    $faultString = !empty($result['body']->cause[0]->message) ? json_encode(array(date('Y-m-d H:i:s'), $result['body']->cause[0]->message)) : json_encode(array(date('Y-m-d H:i:s'), $result['body'])) ;
	                                    $dataUpdate = array();
	                                    
	                                    $dataUpdate =  array('flag' => 2,'updated' => date("Y-m-d H:i:s"), 'status' => 'error_update', 'message' => $faultString, 'httpCode' =>   $result['httpCode']);
	                                    
	                                    if($result['body']->cause[0]->cause_id == 232 OR $result['body']->cause[0]->cause_id == 217){
	                                        
	                                        $dataUpdate['logistic_type'] = 'fulfillment';
	                                    }
	                                    
	                                    $db->update('ml_products',
	                                        array('store_id','id'),
	                                        array($storeId, $rowProduct['id']),
	                                        $dataUpdate
	                                        );
	                                    
	                                    
	                                    pre("update stock ml_products {$faultString}");
	                                }
	                                
	                            }else{
	                                echo "error estock price";
	                            }
	                            
	                        }
	                        
	                    }
	                    
	                    /*****************************************************************************************************/
	                    /***************************************** UPDATE VARIAÇÂO*******************************************/
	                    /****************************************************************************************************/
	                    if($rowProduct['flag_import_variations'] == 2){ 
	                        
	                        $count = 1;
	                        $atualiza = 0;
	                        $lodVariationdata = array();
	                        $sql = "SELECT * FROM ml_products_attributes WHERE store_id = {$storeId}
                            AND product_id = {$rowProduct['id']} AND status != 'delete' GROUP BY variation_id";
	                        $queryAttr = $db->query($sql);
	                        $num_rows = $queryAttr->rowCount();
	                        
	                        while($row =  $queryAttr->fetch(PDO::FETCH_ASSOC)){
	                            if(empty($row['sku'])){
	                            	
	                            	continue;
	                                
// 	                                if($num_rows == 1){
// 	                                    $sku = trim($rowProduct['sku']);
// 	                                }
	                                
	                                
// 	                                else{
	                                
// 	                                    $sqlParentId = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND sku LIKE '".trim($rowProduct['sku'])."'";
// 	                                    $queryParentId = $db->query($sqlParentId);
// 	                                    $resParentId = $queryParentId->fetch(PDO::FETCH_ASSOC);
// 	                                    $parentId = trim($resParentId['parent_id']);
	                                    
// 	                                    $sqlMlAttr = "SELECT * FROM ml_products_attributes WHERE store_id = {$storeId} AND product_id = {$rowProduct['id']} 
//                                         AND attribute LIKE 'COLOR' AND variation_id = {$row['variation_id']}  ";
// 	                                    $queryMlAttr = $db->query($sqlMlAttr);
// 	                                    $resMlAttr =  $queryMlAttr->fetch(PDO::FETCH_ASSOC);
// // 	                                    pre($resMlAttr);
	                                    
// 	                                    $sqlVariations = "SELECT sku, color FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}'";
// 	                                    $queryVariations = $db->query($sqlVariations);
// 	                                    $resVariations = $queryVariations->fetchAll(PDO::FETCH_ASSOC);
	                                    
// 	                                    foreach($resVariations as $k => $variation){
	                                        
// 	                                        if(strtolower($variation['color']) == strtolower($resMlAttr['information'])){
// 	                                            $sku = trim($variation['sku']);
// 	                                            break;
// 	                                        }
	                                        
// 	                                    }
// 	                                }
	                                
	                                
	                            }
	                                
	                            $sku = trim($row['sku']);
	                            
	                            if(!empty($sku)){
	                                
    	                            $selectQtd = "SELECT id, sku, title, quantity, sale_price, promotion_price, blocked
                                        FROM `available_products` WHERE store_id = {$storeId} AND `sku`  LIKE '{$sku}'";
    	                            $queryQtd = $db->query($selectQtd);
    	                            $resStockPrice = $queryQtd->fetch(PDO::FETCH_ASSOC);
    	                            $qtd = $resStockPrice['quantity'] > 0 ? $resStockPrice['quantity'] : 0 ;
    	                            
    	                            $salePriceModel->sku = trim($resStockPrice['sku']);
    	                            $salePriceModel->product_id = $resStockPrice['id'];
    	                            
    	                            $salePrice = $salePriceModel->getSalePrice();
    	                            $stockPriceRel = $salePriceModel->getStockPriceRelacional();
    	                            
    	                            $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
    	                            $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
    	                            
    	                            if ($resStockPrice['blocked'] == "T"){
    	                            	$qtd = 0;
    	                            	$logError[] = "error|Produto Bloqueado...";
    	                            }
    	                            
//     	                            if($row['attribute'] == 'available_quantity' AND  $row['value'] == 0){
//     	                            	if($qtd == 0){
// 	    	                            	$logError[] = 'Erro não é possivel atualizar um produto sem estoque...';
// 	    	                            	continue;
//     	                            	}
//     	                            }
    	                            
    	                            
    	                            $status = $qtd > 0 ? 'active' : 'paused';
    	                            $data = array(
    	                            		"id" => $row['variation_id'],
    	                            		"seller_custom_field" => $sku,
    	                            		"available_quantity" => $qtd,
    	                            		"status" =>  $status,
    	                            		"price" =>  $salePrice
    	                            );
    	                          
                                    $variantion['variations'][] = $data;
                                    
                                    $lodVariationdata[$resStockPrice['id']] = array('id' => $rowProduct['id'], 'variations' => $data);
	                            }     
							}
							
		                        $condition = "/items/MLB{$rowProduct['id']}";
		                        
		                        $result = $meli->put($condition, $variantion, array('access_token' => $resMlConfig['access_token']));
		                        if ($result['httpCode'] == 200) {
		                            
		                            saveItem ($db, $storeId, $result ['body'], $rowProduct['sku'] );
	                                
		                            if(!empty($result ['body']->variations)){
		                                saveItemVariations ($db, $storeId, $result ['body']->id, $result ['body']->variations );
		                            }
		                            
		                            $totalUpdated++;
		                            
		                            if(isset($lodVariationdata)){
		                            
		                            	foreach($lodVariationdata as $j => $variationData){
		                            		
		                            		$dataLog = array();
		                            		
		                            		$dataLog['update_stock_price_variations_mercadolivre'] = array(
		                            				'request' => $variationData,
		                            				'result' => $result['httpCode']
		                            		);
		                            		
		                            		$db->insert('products_log', array(
		                            				'store_id' => $storeId,
		                            				'product_id' => $j,
		                            				'description' => "Atualização Mercadolivre Variação Anúncio MLB{$variationData['id']}",
		                            				'user' => $request,
		                            				'created' => date('Y-m-d H:i:s'),
		                            				'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
		                            		));
		                            	}
		                            }
		                            
		                            if( $request != "System"){
		                              echo "success|Produto atualizado com sucesso!|{$resStockPrice['sku']}|{$result['body']->permalink}|\n";
		                            }
		                            
		                            $sold_quantity = intval($result['body']->sold_quantity);
		                            $sold_quantity = $sold_quantity > 0 ? $sold_quantity : 0 ;
		                            $db->update('ml_products',
		                                array('store_id','id'),
		                                array($storeId, $rowProduct['id']),
		                                array('flag' => 2,
		                                	'updated' => date("Y-m-d H:i:s"), 
		                                	'httpCode' => $result['httpCode'],
		                                    'sold_quantity' => $sold_quantity,
		                                	'message' => 'success',
	                                        'logistic_type' => ''
		                                ));
		                        
		                        }else{
		                        	
		                            $faultString = !empty(trim($result['body']->cause[0]->message)) ? $result['body']->cause[0]->message : json_encode($result['body']);
		                            
		                            $dataUpdate = array();
		                            
		                            $dataUpdate =  array('flag' => 2,'updated' => date("Y-m-d H:i:s"), 'status' => 'error_update', 'message' => $faultString, 'httpCode' =>   $result['httpCode']);
	
		                            if($result['body']->cause[0]->cause_id == 232 OR $result['body']->cause[0]->cause_id == 217){
		                                
		                                $dataUpdate['logistic_type'] = 'fulfillment';
		                            }
		                            
		                            $db->update('ml_products',
		                                array('store_id','id'),
		                                array($storeId, $rowProduct['id']),
		                                $dataUpdate
		                                );

		                            $partsFault = explode("doesn't have a variation with id", $faultString);
		                            if(count($partsFault) > 0){
		                            	
		                            	$variationId = trim(end($partsFault));
		                            	
		                            	if(!empty($variationId)){
		                            	
			                            	$queryVar = $db->update('ml_products_attributes',
			                            			array('store_id','product_id', 'variation_id'),
			                            			array($storeId, $rowProduct['id'], $variationId),
			                            			array('status' => 'delete'));
			                            	
		                            	}else{
// 		                            		pre($rowProduct);
		                            		pre($faultString);
		                            	}
		                            
		                            }
		                            
		                            
		                        }
		                        unset($variantion);
	                        
	                	}
	                    
	                }
	                
	            }
	            
	        }
	        logSyncEnd($db, $syncId, $totalUpdated);
	        
	        break;
	        
	        
	    case 'update_stock_price':
	        $itemsModel = new ItemsModel($db, null, $storeId);
	        $mlCategory = new MlCategoryModel($db, null, $storeId);
	        $itemsRestModel = new ItemsRestModel($db, null, $storeId);
	        $salePriceModel = new SalePriceModel($db, null, $storeId);
	        $salePriceModel->marketplace = "Mercadolivre";
	        
	        $totalUpdated = 0;
	        
	        $syncId =  logSyncStart($db, $storeId, "Mercadolivre", $action, "Atualização de Estoque e Preço Mercadolivre", $request);
	        
	        $adsId = isset($_REQUEST["ads_id"])  ? $_REQUEST["ads_id"] : NULL ;
	       
	        if(isset($adsId)){
	        	$adsIds = is_array($adsId) ? $adsId : array($adsId) ;
	        	foreach($adsIds as $i => $adsId){
			            // core atualizacao ml products
			            $sqlProduct = "SELECT * FROM `ml_products` WHERE store_id = {$storeId} AND id = {$adsId}";
			            $query = $db->query($sqlProduct);
			            while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
			            	$sql = "SELECT * FROM ml_products_attributes WHERE store_id = {$storeId}
			            	AND product_id = {$rowProduct['id']} AND status != 'delete' GROUP BY variation_id";
			            	$queryAttr = $db->query($sql);
			            	
			            	$num_rows = $queryAttr->rowCount();
			            	if($num_rows < 1 OR $rowProduct['flag_import_variations'] == 1){
			                    $selectQtd = "SELECT id, sku, quantity, sale_price, promotion_price, blocked, category
		                                FROM `available_products` WHERE store_id = {$storeId} AND `sku` LIKE '{$rowProduct['sku']}'";
			                    $queryQtd = $db->query($selectQtd);
			                    $resStockPrice = $queryQtd->fetch(PDO::FETCH_ASSOC);
			                    
			                    if(isset($resStockPrice['sku'])){
			                        
			                        $mlCategory->hierarchy = $resStockPrice['category'];
			                        $mlCategoryRel = $mlCategory->getCategoryRelationship();
			                        $qtd = $resStockPrice['quantity'] > 0 ? $resStockPrice['quantity'] : 0 ;
			                        $salePriceModel->sku = trim($resStockPrice['sku']);
			                        $salePriceModel->product_id = $resStockPrice['id'];
			                        $salePrice = $salePriceModel->getSalePrice();
			                        $stockPriceRel = $salePriceModel->getStockPriceRelacional();
			                        $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
			                        $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
			                        if ($resStockPrice['blocked'] == "T"){
			                            $qtd = 0;
			                            echo "error|Produto Bloqueado...";
			                        }
			                        $itemsModel->attribute_types = $mlCategoryRel['attribute_types'];
			                        $itemsModel->item_id = $rowProduct['id'];
			                        $itemsModel->sku = trim($resStockPrice['sku']);
			                        if($qtd > 0){
			                        	$itemsModel->price = $salePrice;
			                        }
			                        $itemsModel->available_quantity = $qtd;
			                        $itemsModel->status =  $qtd > 0 ? 'active' : 'paused';
			                        $itemStockPrice = $itemsModel->getItemStockPrice();
// 			                        pre($itemStockPrice);
			                        if(!empty($itemStockPrice)){
			                            
			                            $itemsRestModel->item_id = $rowProduct['id'];
			                            $itemsRestModel->item = $itemStockPrice;
// 			                            pre($itemStockPrice);
			                            $result = $itemsRestModel->putItem();
// 			                            pre($result);
			                            if($result['httpCode'] == 200){
			                                
			                                saveItem ($db, $storeId, $result ['body'], $rowProduct['sku'] );
			                                if(!empty($result ['body']->variations)){
			                                    saveItemVariations ($db, $storeId, $result ['body']->id, $result ['body']->variations );
			                                }
			                                
			                                $db->update('ml_products',
			                                    array('store_id','id'),
			                                    array($storeId, $rowProduct['id']),
			                                    array('flag_import_variations' =>  1, 'flag' => 2,  'updated' => date('Y-m-d H:i:s'), 'httpCode' =>   $result['httpCode'], 'message' => 'success'));
			                                
			                                $totalUpdated++;
			                                
			                                $dataLog = array();
			                                $dataLog['update_stock_price_mercadolivre'] = array(
			                                		'request' => $itemsRestModel->item,
			                                		'result' => $result['httpCode']
			                                );
			                                $db->insert('products_log', array(
			                                		'store_id' => $storeId,
			                                		'product_id' => $resStockPrice['id'],
			                                		'description' => "Mercadolivre Anúncio Atualizado MLB{$rowProduct['id']}",
			                                		'user' => $request,
			                                		'created' => date('Y-m-d H:i:s'),
			                                		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
			                                ));
			                                if( $request != "System"){
			                                     echo "success|Produto atualizado com sucesso!|{$resStockPrice['sku']}|{$result['body']->permalink}|\n";
			                                }
			                            }else{
// 			                            	pre("update update_stock_price ml_products {$faultString}");
			                                $faultString = !empty($result['body']->cause[0]->message) ? $result['body']->cause[0]->message : "Erro ao excluir anúncio ";
			                                
			                                $dataUpdate = array();
			                                
			                                $dataUpdate =   array('flag' => 2,'updated' => date("Y-m-d H:i:s"), 'status' => 'error_update', 'message' => $faultString, 'httpCode' =>   $result['httpCode']);
			                                
			                                if($result['body']->cause[0]->cause_id == 232 OR $result['body']->cause[0]->cause_id == 217){
			                                    
			                                    $dataUpdate['logistic_type'] = 'fulfillment';
			                                    
			                                }
			                                
			                                $db->update('ml_products',
			                                    array('store_id','id'),
			                                    array($storeId, $rowProduct['id']),
			                                    $dataUpdate
			                                    );
			                                
			                                
			                               
			                                echo "error|{$faultString}";
			                                
			                            }
			                            
			                        }
			                        
			                    }
			                    
			                }
			                
		                /*****************************************************************************************************/
		                /***************************************** UPDATE VARIAÇÂO*******************************************/
		                /****************************************************************************************************/
		                if($rowProduct['flag_import_variations'] == 2 && $num_rows > 0){
		                    
		                    $count = 1;
		                    $atualiza = 0;
		                    $lodVariationdata = array();
		                    
		                    while($row =  $queryAttr->fetch(PDO::FETCH_ASSOC)){
		                    	
		                        if(empty(trim($row['sku']))){
		                        	continue;
// 		                            if($num_rows == 1){
// 		                                $sku = trim($rowProduct['sku']);
// 		                            }else{
		                                
// 		                               $sqlParentId = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND sku LIKE '".trim($rowProduct['sku'])."'";
// 		                                $queryParentId = $db->query($sqlParentId);
// 		                                $resParentId = $queryParentId->fetch(PDO::FETCH_ASSOC);
// 		                                $parentId = trim($resParentId['parent_id']);
		                                
		                                
// 		                                $sqlMlAttr = "SELECT * FROM ml_products_attributes WHERE store_id = {$storeId} AND product_id = {$rowProduct['id']}
// 		                                    AND attribute LIKE 'COLOR' AND variation_id = {$row['variation_id']}  ";
// 		                                $queryMlAttr = $db->query($sqlMlAttr);
// 		                                $resMlAttr =  $queryMlAttr->fetch(PDO::FETCH_ASSOC);
// 		//                                 pre($resMlAttr);
		                                
// 		                                $sqlVariations = "SELECT sku, color FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}'";
// 		                                $queryVariations = $db->query($sqlVariations);
// 		                                $resVariations = $queryVariations->fetchAll(PDO::FETCH_ASSOC);
		                                
// 		                                foreach($resVariations as $k => $variation){
		                                    
// 		                                    if(strtolower($variation['color']) == strtolower(trim($resMlAttr['information']))){
// 		                                        $sku = trim($variation['sku']);
// 		                                        continue;
// 		                                    }
		                                    
// 		                                }
// 		                            }
		                            
		                            
		                        }else{
		                            
		                            $sku = trim($row['sku']);
		                            
		                        }
		                        if(!empty($sku)){
		                            
		                            $selectQtd = "SELECT id, sku, title, quantity, sale_price, promotion_price, blocked
		                                    FROM `available_products` WHERE store_id = {$storeId} AND `sku` LIKE '{$sku}'";
		                            $queryQtd = $db->query($selectQtd);
		                            $resStockPrice = $queryQtd->fetch(PDO::FETCH_ASSOC);
		                            $qtd = $resStockPrice['quantity'] > 0 ? $resStockPrice['quantity'] : 0 ;
		                            
		                            $salePriceModel->sku = trim($resStockPrice['sku']);
		                            $salePriceModel->product_id = $resStockPrice['id'];
		                            $salePrice = $salePriceModel->getSalePrice();
		                            $stockPriceRel = $salePriceModel->getStockPriceRelacional();
		                            $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
		                            $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
		                            if ($resStockPrice['blocked'] == "T"){
		                            	$qtd = 0;
		                            	echo "error|Produto Bloqueado...";
// 		                            	continue;
		                            }
		                            $status = $qtd > 0 ? 'active' : 'paused';
		                            $data = array(
		                                "id" => $row['variation_id'],
		                                "seller_custom_field" => $sku,
		                                "available_quantity" => $qtd,
		                            	"status" =>  $status,
		                                "price" => $salePrice 
		                            );
		                            $variantion['variations'][] = $data;
		                            
		                            $lodVariationdata[$resStockPrice['id']] = array('id' => $rowProduct['id'], 'variations' => $data);
			                    	}
								}
// 			                    pre($variantion);
			                    $condition = "/items/MLB{$rowProduct['id']}";
			                    $result = $meli->put($condition, $variantion, array('access_token' => $resMlConfig['access_token']));
			                    if($result['httpCode'] == '200'){
			                        if( $request != "System"){
			                         echo "success|{$rowProduct['id']}";
			                        }
			                        saveItem ($db, $storeId, $result ['body'], $rowProduct['sku'] );
			                        if(!empty($result ['body']->variations)){
			                            saveItemVariations ($db, $storeId, $result ['body']->id, $result ['body']->variations );
			                        }
			                        
			                        $db->update('ml_products',
			                            array('store_id','id'),
			                            array($storeId, $rowProduct['id']),
			                            array('flag' => 2,'updated' => date("Y-m-d H:i:s"), 'httpCode' => $result['httpCode'], 'message' => 'success')
			                            );
			                        $totalUpdated++;
			                        
			                        if(isset($lodVariationdata)){
			                        	
			                        	foreach($lodVariationdata as $j => $variationData){
			                        		$dataLog = array();
					                        $dataLog['update_stock_price_variations_mercadolivre'] = array(
					                        		'request' => $variationData,
					                        		'result' => $result['httpCode']
					                        );
					                        $db->insert('products_log', array(
					                        		'store_id' => $storeId,
					                        		'product_id' => $j,
					                        		'description' => "Atualização Mercadolivre Variação Anúncio MLB{$variationData['id']}",
					                        		'user' => $request,
					                        		'created' => date('Y-m-d H:i:s'),
					                        		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
					                        ));
			                        	}
			                        }
			                        
			                    }else{
// 			                    	pre($result['body']);
			                        $faultString = !empty($result['body']->cause[0]->message) ? $result['body']->cause[0]->message : json_encode($result['body']);
			                        
			                        $dataUpdate = array();
			                        
			                        $dataUpdate =  array('flag' => 2,'updated' => date("Y-m-d H:i:s"), 'status' => 'error_update', 'message' => $faultString, 'httpCode' =>   $result['httpCode']);
			                        
			                        if($result['body']->cause[0]->cause_id == 232 OR $result['body']->cause[0]->cause_id == 217){
			                            
			                            $dataUpdate['logistic_type'] = 'fulfillment';
			                        }
			                        
			                        $db->update('ml_products',
			                            array('store_id','id'),
			                            array($storeId, $rowProduct['id']),
			                            $dataUpdate
			                            );
			                        
			                        $partsFault = explode("doesn't have a variation with id", $faultString);
			                        if(count($partsFault) > 0){
			                        	$variationId = trim(end($partsFault));
			                        	if(!empty($variationId)){
				                        	$queryVar = $db->update('ml_products_attributes',
				                        			array('store_id','product_id', 'variation_id'),
				                        			array($storeId, $rowProduct['id'], $variationId),
				                        			array('status' => 'delete'));
				                        	
			                        	}
			                        	
			                        }
			                        
			                        echo "error|{$faultString}";
			                        
			                        
			                    }
			                    
			                    unset($variantion);
		                    
		                }
		                
		            }
		            
		        }
			}
	        logSyncEnd($db, $syncId, $totalUpdated);
	        
	        break;
	        
	        
	        
	}
	    
		
	
}




