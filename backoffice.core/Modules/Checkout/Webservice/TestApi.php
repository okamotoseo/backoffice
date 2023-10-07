<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';

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
    
//     $moduleConfig = getModuleConfig($db, $storeId, 9);

    require_once $path.'/../../../vendor/autoload.php';
    
    // Configura credenciais
    MercadoPago\SDK::setAccessToken('TEST-524940906731972-102506-44ca4b3454c346815aa08c7c37367893-260984855');
    
    // Cria um objeto de preferência
    $preference = new MercadoPago\Preference();
    
    // Cria um item na preferência
    $item = new MercadoPago\Item();
    $item->title = 'Meu produto 2';
    $item->quantity = 2;
    $item->unit_price = 88;
    $preference->items = array($item);
    $preference->save();
    echo 123;
    echo "<script src='https://www.mercadopago.com.br/integrations/v1/web-payment-checkout.js'
    		data-preference-id='{$preference->id}'>
    		</script>";
    
    
    
}