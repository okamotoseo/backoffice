<?php
header("Content-Type: text/html; charset=utf-8");
define( 'HOME_URI', 'https://'.$_SERVER['HTTP_HOST']);
$path = dirname(__FILE__);

require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? $_REQUEST["store_id"] : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;


if(isset($storeId)){
    
    $db = new DbConnection();
    
    $storeConfig = getStoreConfig($db, $storeId);
    
    switch($action){
    	
    	case 'rate_shipping':
    	    
    	    $cepOrigem = '17526330';
    	    
    	    $cepDestino = isset($_REQUEST["cep_destino"]) && $_REQUEST["cep_destino"] != "" ? $_REQUEST["cep_destino"] : null ;
    	    
    	    if(!isset($cepDestino)){
    	        
    	        return;
    	    }
    	    
    	    $weight = isset($_REQUEST["weight"]) && $_REQUEST["weight"] != "" ? $_REQUEST["weight"] : null ;
    	    if(!isset($weight)){
    	        $weight = isset($storeConfig['Checkout']['weight_min']) && !empty($storeConfig['Checkout']['weight_min']) ? $storeConfig['Checkout']['weight_min'] : '0.3' ;
    	    }
    	    $height = isset($_REQUEST["height"]) && $_REQUEST["height"] != "" ? $_REQUEST["height"] : null ;
    	    if(!isset($height)){
    	        $height = isset($storeConfig['Checkout']['height']) && !empty($storeConfig['Checkout']['height_min']) ? $storeConfig['Checkout']['height_min'] : 16 ;
    	    }
    	    $width = isset($_REQUEST["width"]) && $_REQUEST["width"] != "" ? $_REQUEST["width"] : null ;
    	    if(!isset($width)){
    	        $width = isset($storeConfig['Checkout']['lenwidthgth']) && !empty($storeConfig['Checkout']['width_min']) ? $storeConfig['Checkout']['width_min'] : 2;
    	    }
    	    $length = isset($_REQUEST["length"]) && $_REQUEST["length"] != "" ? $_REQUEST["length"] : null ;
    	    if(!isset($length)){
    	        $length = isset($storeConfig['Checkout']['length_min']) && !empty($storeConfig['Checkout']['length_min']) ? $storeConfig['Checkout']['length_min'] : 11 ;
    	    }
    	    
    	    $total_peso = 0;
    	    $total_cm_cubico = 0;
    	    
//     	    echo 123;die;
    	    $sql = "SELECT * FROM shipping_rate WHERE store_id = {$storeId}  
            AND '{$cepDestino}' BETWEEN start_postalcode AND end_postalcode 
            AND '{$weight}' BETWEEN CAST(start_weight as decimal(5,2)) AND CAST(end_weight as decimal(5,2)) ";
    	    $query = $db->query($sql);
    	    $res =  $query->fetch(PDO::FETCH_ASSOC);
    	    $response[] = array(
    	        'Codigo' => 'RTE',
    	        'Servico' => 'Rodonaves',
    	        'Valor' => $res['price'],
    	        'PrazoEntrega' => $res['max_delivery_time'],
    	        'medidas' => $res
    	    );
    	    echo $callback . '(' . json_encode($response) . ')';
    	    
    	    break;
    	    
    }
    
    
}