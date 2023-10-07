<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../Class/class-Magento2.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Catalog/ProductsModel.php';
require_once $path .'/../Models/Catalog/MediaModel.php';
require_once $path .'/../Models/Catalog/CategoriesModel.php';
require_once $path .'/../Models/Catalog/InventoryModel.php';
require_once $path .'/../Models/Catalog/AttributesModel.php';
require_once $path .'/../Models/Catalog/AttributeSetModel.php';
require_once $path .'/../Models/Products/ProductsTempModel.php';
require_once $path .'/../Models/Products/SetAttributesRelationshipModel.php';
require_once $path .'/../Models/Products/AttributesValuesModel.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
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
	
	switch($action){

		case "export_stock":
			$syncId =  logSyncStart($db, $storeId, "Mg2", $action, "Atualização do estoque ecommerce", $request);

			$inventoryModel = new InventoryModel($db, null, $storeId);
			$salePriceModel = new SalePriceModel($db, null, $storeId);
			$salePriceModel->marketplace = "Mg2";
			 
			$productId = isset($_REQUEST["product_id"])  ? intval($_REQUEST["product_id"]) : NULL ;
			$dateFrom =  date("Y-m-d H:i:s",  strtotime("-24 hour") );
			$sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}' AND sku IN (
				SELECT sku FROM mg2_products_tmp WHERE store_id = {$storeId})";
			if(isset($productId)){
				$sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
			}
			if(isset($_REQUEST['all'])){
				$sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND sku IN (
				SELECT sku FROM mg2_products_tmp WHERE store_id = {$storeId})";
			}
			 
			$totalUpdated = 0;
			$queryParent = $db->query($sqlParent);
			while($rowParent = $queryParent->fetch(PDO::FETCH_ASSOC)){
				if(!empty($rowParent['parent_id'])){
		
					$sqlProduct = "SELECT id, sku, parent_id, quantity, blocked, price, sale_price FROM available_products
					WHERE store_id = {$storeId} AND parent_id LIKE '{$rowParent['parent_id']}' ";
 					$query = $db->query($sqlProduct);
					 
					while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
		
						$sqlMg2Products = "SELECT * FROM mg2_products_tmp WHERE store_id = {$storeId}
						AND sku LIKE '{$rowProduct['sku']}'";
						$queryMg2Products = $db->query($sqlMg2Products);
						$resMg2Products = $queryMg2Products->fetch(PDO::FETCH_ASSOC);
						if(!empty($resMg2Products['product_id'])){
							 
							$qtd = $rowProduct['quantity'] > 0 ? $rowProduct['quantity'] : 0 ;
							$salePriceModel->sku = trim($rowProduct['sku']);
							$salePriceModel->product_id = $rowProduct['id'];
							$salePrice = $salePriceModel->getSalePrice();
							$stockPriceRel = $salePriceModel->getStockPriceRelacional();
							
							$salePrice = empty($stockPriceRel['price']) ? $salePrice : number_format($stockPriceRel['price'], 2) ;
							$qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
							 
							if ($rowProduct['blocked'] == "T"){
								$qtd = 0;
								echo "error|Produto Bloqueado...";
							}
							
							
							$inventoryModel->sku = $resMg2Products['sku'];
							$inventoryModel->product_id = $resMg2Products['product_id'];
							$inventoryModel->qty = $qtd;
							$inventoryModel->is_in_stock = $qtd > 0 ? 1 : 0 ;
							$resProducts = $inventoryModel->catalogInventoryStockItemUpdate();
							if($resProducts == 1){
								$totalUpdated++;
								$dataLog['export_stock_Mg2'] = array(
										'request' => json_encode(array('manage_stock' => 1,'qty' => $inventoryModel->qty, 'is_in_stock' => $inventoryModel->is_in_stock, 'sku' => $rowProduct['sku'])),
										'response' => json_encode(array('success'))
								);
								$db->insert('products_log', array(
										'store_id' => $storeId,
										'product_id' => $rowProduct['id'],
										'description' => 'Atualização do Estoque Produto Magento Mg2',
										'user' => $request,
										'created' => date('Y-m-d H:i:s'),
										'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
								));
							}else{
								echo "Erro não foi possivel conectar {$rowProduct['sku']}<br>";
							}
						}else{
							echo "nao existe {$rowProduct['sku']}<br>";
						}
					}
				}
			}
			logSyncEnd($db, $syncId, $totalUpdated);
			break;
			
	case "export_price":
	        
	        $syncId =  logSyncStart($db, $storeId, "Magento2", $action, "Atualização do preço ecommerce", $request);
	        $productsModel = new ProductsModel($db, null, $storeId);
	        $salePriceModel = new SalePriceModel($db, null, $storeId);
	        $salePriceModel->marketplace = "Ecommerce";
	        $productId = isset($_REQUEST["product_id"])  ? intval($_REQUEST["product_id"]) : NULL ;
	        $dateFrom =  date("Y-m-d H:i:s",  strtotime("-24 hour") );
	        $sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}'";
	        if(isset($productId)){
	            $sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
	        }
	        $totalUpdated = 0;
	        
	        $queryParent = $db->query($sqlParent);
	        
	        while($rowParent = $queryParent->fetch(PDO::FETCH_ASSOC)){
	        
				if(!empty($rowParent['parent_id'])){
			        
			       	$sqlProduct = "SELECT id, sku, parent_id, quantity, blocked, price, sale_price FROM available_products WHERE store_id = {$storeId} 
			       	AND parent_id LIKE '{$rowParent['parent_id']}'";
			        
			        $query = $db->query($sqlProduct);
			        
			        while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
			        	
			            $sqlMg2Products = "SELECT * FROM mg2_products_tmp WHERE store_id = {$storeId} 
		                AND sku LIKE '{$rowProduct['sku']}' OR store_id = {$storeId} AND sku LIKE '{$rowProduct['sku']}-x' ";
			            $queryMg2Products = $db->query($sqlMg2Products);
			            $resMg2ProductsAll = $queryMg2Products->fetchAll(PDO::FETCH_ASSOC);
			            if(isset($resMg2ProductsAll)){
		    	            foreach($resMg2ProductsAll as $i => $resMg2Products){
		        	            if(!empty($resMg2Products['product_id'])){
		        	            	$salePriceModel->sku = $rowProduct['sku'];
		        	            	$salePriceModel->product_id = $rowProduct['id'];
		        	            	$salePrice = $salePriceModel->getSalePrice();
		        	            	$stockPriceRel = $salePriceModel->getStockPriceRelacional();
		        	            	$salePrice = empty($stockPriceRel['price']) ? $salePrice : number_format($stockPriceRel['price'], 2)  ;
		        	                
		        	                $productsModel->product_id = $resMg2Products['product_id'];
		        	                $productsModel->sku = $resMg2Products['sku'];
		        	                $result = $productsModel->catalogProductUpdate(array("sku" => $resMg2Products['sku'], "price" => $salePrice));
		//         	                pre($result);die;
		        	                if($result){
		        	                    $totalUpdated++;
		        	                    $dataLog['export_price_mg2'] = array(
		        	                    		'request' => json_encode(array("sku" => $resMg2Products['sku'], "price" => $salePrice)),
		        	                    		'response' => json_encode(array('httpCode' => $result['httpCode']))
		        	                    );
		        	                    pre($dataLog);
		        	                    $db->insert('products_log', array(
		        	                    		'store_id' => $storeId,
		        	                    		'product_id' => $rowProduct['id'],
		        	                    		'description' => 'Atualização do Preço Produto Magento Mg2',
		        	                    		'user' => $request,
		        	                    		'created' => date('Y-m-d H:i:s'),
		        	                    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
		        	                    ));
		        	                }
		        	                
		        	            }
		    	            }
			            }
			        }
				}
	        }
	        logSyncEnd($db, $syncId, $totalUpdated);
	        
	        break;
	}
	
	
}