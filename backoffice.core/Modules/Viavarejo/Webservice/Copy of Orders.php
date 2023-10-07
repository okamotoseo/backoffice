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

    
//     try {
//     	$orders_api = new  \CNovaApiLojistaV2\OrdersApi($api_client);
//     	$purchased_at = formatDateRange(null, new \DateTime('NOW'), $api_client);
//     	$get_orders_response =  $orders_api->getOrdersByStatusNew($purchased_at, null, null, 0, 100);
//     	pre(json_encode($get_orders_response, JSON_PRETTY_PRINT));
    
//     } catch (ApiException $e) {
//     	echo ($e->getMessage());
//     }
    
    
    switch($action){
    	
    	case 'get_orders_new':
    	
    		$orders_api = new \CNovaApiLojistaV2\OrdersApi($api_client);
    	
    		try {
    	
    			$purchased_at = formatDateRange(null, new \DateTime('NOW'), $api_client);
    			$get_orders_response =  $orders_api->getOrdersByStatusNew($purchased_at, null, null, 0, 100);
    			pre($get_orders_response);
    	
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
    		
    	case 'get_orders_approved':
    		
    		$orders_api = new \CNovaApiLojistaV2\OrdersApi($api_client);
    		
    		try {
    		
    			$purchased_at = formatDateRange(null, new \DateTime('NOW'), $api_client);
    			$get_orders_response =  $orders_api->getOrdersByStatusApproved($purchased_at, null, null, 0, 100);
    			pre($get_orders_response);
    		
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
    	
    	case "get_orders":
    		 
    		$orders_api = new  \CNovaApiLojistaV2\OrdersApi($api_client);
    		 
    		try {
    	
    			$get_orders_response =  $orders_api->getOrders(1000, 100);
    			pre($get_orders_response);
    	
    	
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
    				$res = $e->getMessage();
    				pre(json_encode($res, JSON_PRETTY_PRINT));
    			}
    		}
    		 
    		break;
    		
    	
    	case 'get_orders_sent':
    	
    		$orders_api = new \CNovaApiLojistaV2\OrdersApi($api_client);
    		
    		try {
    		
    			$get_orders_response = $orders_api->getOrdersByStatusSent(null, null, null, 0, 100);
    			pre($get_orders_response);
    		
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
    		
    	case 'get_orders_delivered':
    			 
    		$orders_api = new \CNovaApiLojistaV2\OrdersApi($api_client);
    		
    		try {
    		
    			$get_orders_response = $orders_api->getOrdersByStatusDelivered(null, null, null, 0, 100);
    			pre($get_orders_response);
    		
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
    	
    	
    	case 'export_order_tracking':
    		
//     		$loads_api = new \CNovaApiLojistaV2\LoadsApi($api_client);
    		$orders_api = new \CNovaApiLojistaV2\OrdersApi($api_client);
    		$orders_trackings = '{
    					"order_id" : "11942959",
						"items" : ["1194295901-1"],
						"occurredAt" : "2020-10-25T00:00:00.000-03:00",
						"number" : "DM533516921BR",
						"sellerDeliveryId" : "414634",
						"carrier" : {
							"name" : "eSedex PE"
						},
						"invoice" : {
							"cnpj" : "09.055.134\/0001-84",
							"number" : "55557",
							"serie" : "1",
							"issuedAt" : "2020-11-11T00:00:00.000-03:00",
							"accessKey" : "26150409055134000265550010000555571110894897"
						}
					}';
//     		$orders_trackings = new \CNovaApiLojistaV2\model\OrdersTrackings();
    		
//     		$order_tracking = new \CNovaApiLojistaV2\model\OrderTracking();
    		
//     		$order_id = new \CNovaApiLojistaV2\model\OrderId();
//     		$order_id->id = '1194295901';
//     		$order_tracking->order = $order_id;
    		
//     		$order_tracking->control_point = 'ABC';
//     		$order_tracking->cte = '123';
	    		
//     		$oif = new \CNovaApiLojistaV2\model\OrderItem();
//     		$oif->id = '1194295901-1';
    		
//     		$order_tracking->items = array($oif);
    		
//     		$order_tracking->occurred_at = new \DateTime('NOW');
//     		$order_tracking->seller_delivery_id = '99995439701';
//     		$order_tracking->number = '01092014';
//     		$order_tracking->url = 'servico envio2';
    		
//     		$carrier = new \CNovaApiLojistaV2\model\Carrier();
//     		$carrier->cnpj = '72874279234';
//     		$carrier->name = 'Sedex';
    		
//     		$order_tracking->carrier = $carrier;
    		
//     		$invoice = new \CNovaApiLojistaV2\model\Invoice();
//     		$invoice->cnpj = '72874279234';
//     		$invoice->number = '123';
//     		$invoice->serie = '456';
//     		$invoice->issued_at = new \DateTime('NOW');
//     		$invoice->access_key = '01091111111111111111111111111111111111101092';
//     		$invoice->link_xml = 'link xlm teste5';
//     		$invoice->link_danfe = 'link nfe teste5';
    		
//     		$order_tracking->invoice = $invoice;
    		
// //     		$orders_trackings->trackings = array($order_tracking);
// 			pre($order_tracking);
    		
    		try {
//     			$orders_trackings = json_encode(json_decode($orders_trackings), JSON_PRETTY_PRINT);
    			
    			$res = $orders_api->postOrderTrackingSent($orders_trackings, '1194295901');
    			pre($res);
    		
    			} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
	    			pre($errors);
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
        	
        	
        	
        	
        	
      
                   