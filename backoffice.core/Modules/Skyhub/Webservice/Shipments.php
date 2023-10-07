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
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';

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
$orderCode = isset($_REQUEST["order_code"]) && $_REQUEST["order_code"] != "" ? $_REQUEST["order_code"] : null ;
$plpId = isset($_REQUEST["plp_id"]) && $_REQUEST["plp_id"] != "" ? $_REQUEST["plp_id"] : null ;
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 9);
    
    require_once $path.'/../../../vendor/autoload.php';
    
    
    $email   = $moduleConfig['email'];
    $apiKey  = $moduleConfig['api_key'];
    $xAccountKey = $moduleConfig['account_key'];
    $baseUri = $moduleConfig['base_uri'];
    
    /** @var \SkyHub\Api $api */
    $api = new SkyHub\Api($email, $apiKey, $xAccountKey, $baseUri);
    
    
    switch($action){
        
        case 'teste_view':
            
            $plp = '62982';
            
            /** @var SkyHub\Api\ $servicePdf */
            $servicePdf = new SkyHub\Api\Service\ServicePdf(null);
            /** @var \SkyHub\Api $api2 */
            $apiPdf = new SkyHub\Api($email, $apiKey, null, null, $servicePdf);
            
            /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
            $entityInterfacePdf = $apiPdf->plp()->entityInterface();
            
            /**
             * GET PLP PDF.
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
             */
            $entityInterfacePdf->setId("{$plp}");
            $response = $entityInterfacePdf->viewFile();
            
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                file_put_contents('./teste.pdf', $body);
                pre($body);
                
                
            }else{
                echo "error|".$response->message();
            }
            
            
            
            break;
            
            
            
       
        case "ungroup_skyhub_plp":
            /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
            $entityInterface = $api->plp()->entityInterface();
            $plpIds = is_array($plpId) ? $plpId : array($plpId) ;
            foreach($plpIds as $i => $id){
                pre($id);
//                 /**
//                  * UNGROUP A PLP.
//                  * @var SkyHub\Api\Handler\Response\HandlerInterface $response
//                  */
                $entityInterface->setId("{$id}");
                $response = $entityInterface->ungroup();
                
                if( ! method_exists( $response, 'body' ) ){

                    $error[] = "error|".$response->message();
                }
                
            }
            
            if(empty($error)){
                echo "success|";
            }else{
                pre($error);
                    
            }
            

            
            break;
            
        case "group_skyhub_plp":
            /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
            $entityInterface = $api->plp()->entityInterface();
//             pre($entityInterface);die;
            $orderCodes = is_array($orderCode) ? $orderCode : array($orderCode) ;
//             pre($orderCodes);die;
            foreach($orderCodes as $i => $code){
                /**
                 * GROUP ORDERS IN A PLP.
                 * @var SkyHub\Api\Handler\Response\HandlerInterface $response
                 */
                $entityInterface->addOrder("{$code}");
                
            }
//             pre($entityInterface);
            $response = $entityInterface->group();
//             pre($response);
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                $message = json_decode($body);
                echo "success|{$message->message}";
                
                
            }else{
                echo "error|".$response->message();
            }
            
            break;
            
        case "confirm_collect":
            /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
            $entityInterface = $api->plp()->entityInterface();
            $orderCodes = is_array($orderCode) ? $orderCode : array($orderCode) ;
            foreach($orderCodes as $i => $code){
                
                /**
                 * COLLECT ORDERS IN A PLP.
                 * @var SkyHub\Api\Handler\Response\HandlerInterface $response
                 */
                $entityInterface->addOrderCode("{$code}");
                
            }
            $response = $entityInterface->collect();
            
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                $message = json_decode($body);
                echo "success|{$message->message}";
                
                
            }else{
                echo "error|".$response->message();
            }
            
            break;
        
        case "list_plps":
            /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
            $entityInterface = $api->plp()->entityInterface();
//             /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
//             $entityInterfacePdf = $apiPdf->plp()->entityInterface();
            
            
            /**
             * GET A LIST OF PLP's.
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
             */
            $response = $entityInterface->plps();
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                $message = json_decode($body);
                echo "success|{$message->message}";

                
            }else{
                echo "error|".$response->message();
            }
            
            pre($response);
       
            
            break;
            
            
       
        
            
    }
    
}
