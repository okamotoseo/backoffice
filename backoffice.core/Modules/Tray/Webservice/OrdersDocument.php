<?php
ini_set ("display_errors", true);
set_time_limit ( 300 );
header("Content-Type: text/html; charset=utf-8");

$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Models/Customers/ManageCustomersModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Tray.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Api/OrdersRestModel.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
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
    
    $request = "System";
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
    
    switch($action){
        
        case "export_document":
            $syncId =  logSyncStart($db, $storeId, "Tray", $action, "Exportação XML Nota Fiscal.", $request);
            $exported = 0;
            $orderIds = isset($_REQUEST["order_id"])  ? $_REQUEST["order_id"] : NULL ;
            $ordersRestModel = new OrdersRestModel($db, null, $storeId);
            if(!isset($orderIds)){
                $orderIds = array('all');
            }else{
                $orderIds = is_array($orderIds) ? $orderIds : array($orderIds) ;
            }
            
            $dateFrom =  date("Y-m-d H:i:s", strtotime("-150 days", strtotime("now")));
            if($request == 'Manual'){
                $dateFrom =  date("Y-m-d H:i:s", strtotime("-50 days", strtotime("now")));
            }
            foreach($orderIds as $i => $orderId){
                //NOT IN ('pending', 'shipped', 'delivered', 'canceled') 
                
                $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND Status IN ('invoiced', 'ready_to_ship')
                AND DataPedido >= '{$dateFrom}' AND Marketplace LIKE 'Tray' AND id IN (
                    SELECT order_id as id FROM xml_nota_saida WHERE store_id = {$storeId} AND emissao >= '".date("Y-m-d", strtotime($dateFrom))."'
                ) ORDER BY id DESC";
                
                if($orderId != 'all'){
                    if(!empty($orderId)){
                        $sql = "SELECT * FROM orders WHERE store_id = {$storeId}";
                    }
                }
//                 $sql = "SELECT * FROM orders WHERE store_id = {$storeId}";
//                 pre($sql);die;
                $query = $db->query($sql);
                $orders = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach($orders as $key => $order){
                    
                    if(isset($order['id'])){ 
                        
                        $sqlNf = "SELECT * FROM xml_nota_saida WHERE order_id = {$order['id']}";
                        $queryNf = $db->query($sqlNf);
                        $rowFatura = $queryNf->fetch(PDO::FETCH_ASSOC);
                        
                        if(isset($rowFatura['emissao'])){
    //                         $ordersRestModel->issue_date = $rowFatura['emissao']."".$rowFatura['hora_emissao'];
    //                         $ordersRestModel->number = $rowFatura['id_nota_saida'];
    //                         $ordersRestModel->serie = $rowFatura['serie'];
    //                         $ordersRestModel->key = $rowFatura['chave'];
    //                         $ordersRestModel->xml_danfe = $rowFatura['xml'];
                            
//                             pre($rowFatura['PedidoId']);
//                             pre($rowFatura['emissao']);
                            $ordersRestModel->id = $rowFatura['PedidoId'];
                            $data = array(
                                "issue_date" => $rowFatura['emissao'],
                                "number" => $rowFatura['nota_numero'],
                                "serie" => $rowFatura['serie'],
                                "value" => $rowFatura['valor_total'],
                                "key" => $rowFatura['chave'],
                                "xml_danfe" => $rowFatura['xml']
//                                 ,
//                                 "ProductCfop" => array(array("product_id" => "123",
//                                 "variation_id" => "0", "cfop" => "1234"))
                            );
                            
                            $ordersRestModel->OrderInvoicesPut = $data;
                            
//                             pre($ordersRestModel->OrderInvoicesPut);
                            
                            $result = $ordersRestModel->postOrdersDocument();
                        
//                             pre($result);
                            $exported++;
                        }
                       
                    }
                }
            }
            echo "success|{$exported}";
        logSyncEnd($db, $syncId, $exported);
        
        break;
        
    }
    
}
