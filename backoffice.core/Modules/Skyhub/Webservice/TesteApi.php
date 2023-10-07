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

require_once $path .'/../Models/Products/ProductsModel.php';
require_once $path .'/../Models/Products/ProductVariationsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 9);
    
    require_once '../../../vendor/autoload.php';

    
    $email   = $moduleConfig['email'];
    $apiKey  = $moduleConfig['api_key'];
    $xAccountKey = $moduleConfig['account_key'];
    $baseUri = $moduleConfig['base_uri'];
    

    switch($action){
        case "approve_order":
           
            echo $idPedido = "Marketplace-{$pedidoId}";
//             die;
            $res = requestApiApproveOrder( $idPedido );
//             pre($res);die;
            
            $db->query("UPDATE orders SET status = 'approved' WHERE store_id = {$storeId} AND PedidoId LIKE '{$pedidoId}'");
            
            
            break;
            
            
            
            
            
        case "create_order":
            
            $postfields = '{
                "order": {
                    "channel": "Marketplace",
                    "items": [
                        
                        {
                            "id": "1",
                            "qty": 1,
                            "original_price": 17.90,
                            "special_price": 17.90
                        }
                    
                    ],
                    "customer": {
                        "name": "Nome do comprador",
                        "email": "comprador@exemplo.com.br",
                        "date_of_birth": "1995-01-01",
                        "gender": "male",
                        "vat_number": "12312312309",
                        "phones": ["8899999999"]
                    },
                    "billing_address": {
                        "street": "Rua de teste",
                        "number": 1234,
                        "detail": "Ponto de referência teste",
                        "neighborhood": "Bairro teste",
                        "city": "Cidade de teste",
                        "region": "UF",
                        "country": "BR",
                        "postcode": "90000000"
                    },
                    "shipping_address": {
                        "street": "Rua de teste",
                        "number": 1234,
                        "detail": "Ponto de referência teste",
                        "neighborhood": "Bairro teste",
                        "city": "Cidade de teste",
                        "region": "UF",
                        "country": "BR",
                        "postcode": "90000000"
                    },
                        "shipping_method": "Transportadora",
                        "estimated_delivery": "2019-11-30",
                        "shipping_cost": 9.99,
                        "interest": 0.0,
                        "discount": 0.00
                    }
            }';

            
            requestApiCreateOrder( $postfields );
            break;
            
    }
    
}

function requestApiCreateOrder( $postfields ){
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.skyhub.com.br/orders",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postfields,
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json;charset=UTF-8",
            "Content-Type: application/json",
            "Postman-Token: ae4cbe9f-e825-48e3-9567-6de605cfdc89",
            "X-Accountmanager-Key: A4zkgtLSVX",
            "X-Api-Key: TZpBAdK9tR5JTYbSavrP",
            "X-User-Email: dev.sysplace@gmail.com",
            "cache-control: no-cache"
        ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        pre( $response);
    }
    
    
}

function requestApiApproveOrder( $pedidoId ){
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.skyhub.com.br/orders/{$pedidoId}/approval",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => '{"status":"payment_received"}',
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json;charset=UTF-8",
            "Content-Type: application/json",
            "Postman-Token: ae4cbe9f-e825-48e3-9567-6de605cfdc89",
            "X-Accountmanager-Key: A4zkgtLSVX",
            "X-Api-Key: TZpBAdK9tR5JTYbSavrP",
            "X-User-Email: dev.sysplace@gmail.com",
            "cache-control: no-cache"
        ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
    
    
}
