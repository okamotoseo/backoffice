<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
// ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../library/sdk-v2/CNovaApiLojistaV2.php';
require_once $path .'/../Class/class-Viavarejo.php';
require_once $path .'/../../../Models/Customers/ManageCustomersModel.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';
require_once $path .'/../Models/Products/ProductsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$orderId = isset($_REQUEST["order_id"]) && $_REQUEST["order_id"] != "" ? $_REQUEST["order_id"] : null ;
$pedidoId = isset($_REQUEST["pedido_id"]) && $_REQUEST["pedido_id"] != "" ? $_REQUEST["pedido_id"] : null ;
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
    
    $vivarejoApi = new Viavarejo($moduleConfig);
    
    $api_client = $vivarejoApi->api_client;

    
    
    
    switch($action){
    	
    	
    	case 'order_tracking_sent':
    		
    		$orders_api = new \CNovaApiLojistaV2\OrdersApi($api_client);
    		
    		$orders_trackings = '{
    					"order_id" : "11942959",
						"items" : ["1194295901-1"],
						"occurredAt" : "2020-10-31T23:00:00.000-03:00",
						"number" : "DM533516921BR",
						"sellerDeliveryId" : "414634",
						"carrier" : {
							"name" : "eSedex PE"
						},
						"invoice" : {
							"cnpj" : "09.055.134\/0001-84",
							"number" : "55557",
							"serie" : "1",
							"issuedAt" : "2020-10-31T21:03:02.000-03:00",
							"accessKey" : "26150409055134000265550010000555571110894897"
						}
					}';
    		
    		try {
    			
    			$orders_trackings = json_encode(json_decode($orders_trackings), JSON_PRETTY_PRINT);
    			
    			$res = $orders_api->postOrderTrackingSent($orders_trackings, '1194295901');
    			pre($res);
    		
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
    	
    	
    	case 'order_tracking_delivered':
    	
    		$orders_api = new \CNovaApiLojistaV2\OrdersApi($api_client);
    	
    		$orders_trackings = '{
    					"order_id" : "11942959",
						"items" : ["1194295901-1"],
						"occurredAt" : "2020-10-31T23:00:00.000-03:00",
						"number" : "DM533516921BR",
						"sellerDeliveryId" : "414634",
						"carrier" : {
							"name" : "eSedex PE"
						},
						"invoice" : {
							"cnpj" : "09.055.134\/0001-84",
							"number" : "55557",
							"serie" : "1",
							"issuedAt" : "2020-10-31T21:03:02.000-03:00",
							"accessKey" : "26150409055134000265550010000555571110894897"
						}
					}';
    	
    		try {
    			 
    			$orders_trackings = json_encode(json_decode($orders_trackings), JSON_PRETTY_PRINT);
    			 
    			$res = $orders_api->postOrderTrackingDelivered($orders_trackings, '1194295901');
    			pre($res);
    	
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
        	
    }
    
    
}
        	
        	
        	
        	
        	
      
                   