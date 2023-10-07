<?php
ini_set ("display_errors", true);
set_time_limit ( 300 );
header("Content-Type: text/html; charset=utf-8");

$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
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
	$moduleConfig = getModuleConfig($db, $storeId, 2);
	require_once $path .'/verifyToken.php';

	switch($action){
	  
		case "get_shipping_price":
			
			$productIds = is_array($productId) ? $productId : array($productId) ;
			
// 			foreach($productIds as $i => $id){
					
// 				$queryAP = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$id}");
// 				$products = $queryAP->fetchAll(PDO::FETCH_ASSOC);
// 				$totalChild = count($products);
					
// 				foreach($products as $ind => $product){
					 //"/users/{$moduleConfig['seller_id']}/shipping_options/free?currency_id=BRL&listing_type_id=gold_pro&condition=new&category_id=MLB26426&item_price=80&verbose=true&dimensions=15x30x5,150"
					$result = $meli->get ( "/users/{$moduleConfig['seller_id']}/shipping_options/free?currency_id=BRL&listing_type_id=gold_pro&condition=new&
					category_id=MLB26426&item_price=80&verbose=true&dimensions=15x30x5,150");
					pre($result);die;
// 					if($result['httpCode'] == 200){
						 
// 						if($result['body']->status != 'processing' AND $result['body']->status != 'ready_to_ship'){
							 
// 							$sqlUpdate = "UPDATE orders SET Status = '{$result['body']->status}',
// 							FreteCusto = '{$result['body']->shipping_option->list_cost}'
// 							WHERE store_id = {$storeId} AND id = {$order['id']}";
// 							$db->query($sqlUpdate);
// 							$exported++;
							 
// 						}
		
						 
// 					}else{
// 						pre($result);
// 					}
			
// 				}
				
// 			}

		break;

	}
}
 

