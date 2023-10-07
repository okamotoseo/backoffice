<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../library/sdk-v2/CNovaApiLojistaV2.php';
require_once $path .'/../Class/class-Viavarejo.php';
require_once $path .'/../Models/Products/ProductsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 10);
    // 'sandbox-mktplace.viavarejo.com.br/api/v2/api-front-categories-v3/jersey';
    $vivarejoApi = new Viavarejo($moduleConfig,);
    
    $api_client = $vivarejoApi->api_client;
    
    switch ($action){
    	
    	case "get_categories":
    		
//     		$productModel = new ProductsModel($db);
    		
//     		$productModel->store_id = $storeId;
    		$categories_api = new \CNovaApiLojistaV2\CategoriesApi($api_client);
    		pre($categories_api);
    		$_limit = 100;
    		
    		$_offset = 0;
    		
    		try {
    			
    			$get_categories_response = $categories_api->getCategories(0,5);
//     			$get_categories_response = $categories_api->getCategoryById();
    			pre($get_categories_response);
    			
//     			foreach ($result->seller_items as $k => $item){
//     				pre($item);
//     				$sql = "SELECT id, sku, parent_id FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$item['sku_seller_id']}'";
//     				$query = $db->query($sql);
//     				$products = $query->fetch(PDO::FETCH_ASSOC);
    				
//     				if(isset($products['id'])){
    				
// 		    			$productModel->sku = $item['sku_seller_id'];
// 		    			$productModel->product_id = $products['id'];
// 		    			$productModel->parent_id = $products['parent_id'];
// 		    			$productModel->status = 'sent';
// 		    			$idProduct = $productModel->save();
		    			
// 		    			if(empty($idProduct)){
// 		    				echo "error|";
// 			    			pre($result);
// 		    			}else{
// 		    				echo "success|";
// 		    				pre($idProduct);
// 		    			}
	    			
//     				}
    			
//     			}
    			
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
    		
    				$res = $e->getMessage();
    				pre($res);
    			}
    		}
    		
    		break;
    		
    	case "get_seller_items_by_sku":
    		
    		$sellerItemsApi = new \CNovaApiLojistaV2\SellerItemsApi($api_client);
    		$site = 'EX';
    		$sku = 10011;//$_REQUEST['sku'];
    		
    		try {
    			$result = $sellerItemsApi->getSellerItemBySkuSellerId($sku, $site);
    			
    			echo "error|";
    			pre($result);
    			
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
   					$res = $e->getMessage();
   					pre($res);
   				}
   			}
    		
   			break;
    			
    			
    			/**
    			 * Operação para consulta da atualização massiva de status de produtos enviado.
    			 */
    	case "get_seller_itens_status":
    			
    			$loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
    			$_offset = 0; 
    			$_limit = 10;
    			try {
    					
    				$res = $loads->getSellerItemsStatusUpdatingStatus($_offset, $_limit);
    				
    				pre($res);
    					
    			} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    				$errors = deserializeErrors($e->getResponseBody(), $api_client);
    				if ($errors != null) {
    					foreach ($errors->errors as $error) {
    						echo ($error->code . ' - ' . $error->message . "\n");
    					}
    				} else {
    					pre($res);
    					$res = $e->getMessage();
    					pre($res);
    				}
    			}
    			
    		break;
            			
    }
    
}