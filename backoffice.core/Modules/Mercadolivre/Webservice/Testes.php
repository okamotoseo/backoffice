<?php
// die;
// echo phpinfo();die;
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Models/Customer/ManageCustomersModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
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
    if(isset($_SERVER ['argv'] [3])){
        $paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
        $accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
    }
    
    $request = "System";
    
}

$db = new DbConnection();

$condition = isset($storeId)  ?  "WHERE store_id = {$storeId}" : '' ;

$sql = "SELECT * FROM `module_mercadolivre` {$condition} ORDER BY store_id DESC";

$query = $db->query($sql);

while($stores = $query->fetch(PDO::FETCH_ASSOC)){
    
    $storeId = $stores['store_id'];
   
    require_once $path .'/verifyToken.php';
    
	switch($action){
	    case "import_order" :
	        $syncId =  logSyncStart($db, $storeId, "Mercadolivre", $action, "Importação de pedidos.", $request);
	        
            $ordersModelTotal = new OrdersModel($db);
            $ordersModelTotal->store_id = $storeId;
            $totalOrdersPaid = $ordersModelTotal->getTotalOrdersPaidMl();

                $dateCreatedFrom =  date("Y-m-d")."T00:00:00.000-00:00";
                $dateCreatedTo = date("Y-m-d")."T23:59:59.000-00:00";
                $getOrder = "/orders/search?seller={$resMlConfig['seller_id']}&order.date_created.from={$dateCreatedFrom}&order.date_created.to={$dateCreatedTo}&access_token={$resMlConfig ['access_token']}";
                if(isset($_REQUEST['order_id'])){
                    $orderId = $_REQUEST['order_id'];
                    $getOrder = "/orders/search?seller={$resMlConfig['seller_id']}&q={$orderId}&access_token={$resMlConfig ['access_token']}";
                }
                
                
	            $ordersCount = 0;

    	        $result = $meli->get($getOrder);

    	        
    	        if(isset($result['body']->results)){
    	            
    	            $totalOrder = count($result['body']->results);
            	    foreach($result['body']->results as $key => $order){
            	        $storePath = $path."/../Labels/store_id_{$storeId}/{$order->shipping->id}";
            	        $resUlt = shippmentLabels($db, $meli, $storeId, $storePath, $order->id, $order->shipping->id, $resMlConfig ['access_token']);
            	        die;

            	      die;
            	    }
            	    
    	        }

            
            
	        logSyncEnd($db, $syncId, $totalOrder."/".$ordersCount);
	        break;
	    
		    
	}
    	
}

