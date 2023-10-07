<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
// ini_set ("libxml_disable_entity_loader", false);
// header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../library/sdk-v2/CNovaApiLojistaV2.php';
require_once $path .'/../Class/class-Viavarejo.php'; 
require_once $path .'/../Models/Products/ProductsModel.php';
require_once $path .'/../Models/Products/ProductVariationsModel.php';
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 10);
    
    $vivarejoApi = new Viavarejo($moduleConfig);
    
    $api_client = $vivarejoApi->api_client;
    
    
    switch ($action){
    	
    	case "get_categories";
    		
    		$categories_api = new \CNovaApiLojistaV2\CategoriesApi($api_client); 
	
	    	try {
	    	
	    		$get_categories_response = $categories_api->getCategories(0, 5);
	    		pre($get_categories_response);
	    		if(isset($get_categories_response->categories)){
		    		foreach ($get_categories_response->categories as $categorie) {
		    			echo ($categorie->id . ' - ' . $categorie->name . "\n");
		    		}
	    		}
	    		
	    	
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