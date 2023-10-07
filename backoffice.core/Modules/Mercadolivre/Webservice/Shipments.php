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
		
		case 'shipping_preferences':
			pre($moduleConfig);
			$result = $meli->get ( "/users/{$moduleConfig['seller_id']}/shipping_preferences");
			pre($result);die;
			
			break;
	    
	   case "Shipment": 
	       
	       $syncId =  logSyncStart($db, $storeId, "Mercadolivre", $action, "Atualização da Situação do Pedido", $request);
	       $exported = 0;
	       
	       
	       $orderId = isset($_REQUEST["order_id"])  ? intval($_REQUEST["order_id"]) : NULL ;
	       
	       $sql = "SELECT * FROM orders WHERE store_id = {$storeId} 
            AND Status = 'delivered' AND shipping_id IS NOT NULL ORDER BY id DESC LIMIT 300"; 
	       
// 	       echo $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND DataPedido >= '2019-11-01' AND DataPedido <= '2019-11-30 23:59:59' AND shipping_id IS NOT NULL"; 
	       
	       if(isset($orderId)){
// 	           $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND PedidoId = {$orderId} 
//                  AND shipping_id IS NOT NULL   AND Status != 'delivered'"; 
	           
	           $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND PedidoId = {$orderId}
                 AND shipping_id IS NOT NULL "; 
	       }
	       
	       $query = $db->query($sql);
	       
	       $orders = $query->fetchAll(PDO::FETCH_ASSOC);
	       foreach($orders as $key => $order){
	           
               if(!empty($order['shipping_id'])){
                   
                   $result = $meli->get ( "/shipments/{$order['shipping_id']}", 
                   array (
    	               'access_token' => $resMlConfig ['access_token'],
                       'siteId' => 'MLB'
    	           ) );
//                    pre($result);die;
                   if($result['httpCode'] == 200){
                     
                       if($result['body']->status != 'processing' AND $result['body']->status != 'ready_to_ship'){
                       
                           $sqlUpdate = "UPDATE orders SET Status = '{$result['body']->status}',
                            FreteCusto = '{$result['body']->shipping_option->list_cost}'
                            WHERE store_id = {$storeId} AND id = {$order['id']}";
                           $db->query($sqlUpdate);
                           $exported++;
                       
                       }
                      
                       
                   }
//                    else{
//                        pre($result);
//                    }
                   
                   
               }
        	     
	       }
	       
	       logSyncEnd($db, $syncId, $exported);
	       
	   break;
		    
	}
}
    	

