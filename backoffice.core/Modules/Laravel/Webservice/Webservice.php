<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../Class/class-DbConnectionShopping.php';
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
    $dbShopping = new DbConnectionShopping();
    
//     $moduleConfig = getModuleConfig($db, $storeId, 11);

    switch($action){
    	
        case "export_products":
        	
        	$syncId =  logSyncStart($db, $storeId, "Marketplace", $action, "Atualização do estoque e preço skyhub", $request);
        	
        	$availableProducts = new AvailableProductsModel($db);
        	$availableProducts->store_id = $storeId;
        	$salePriceModel = new SalePriceModel($db, null, $storeId);
        	
        	$productId = isset($_REQUEST["product_id"])  ? intval($_REQUEST["product_id"]) : NULL ;
        	
        	$dateFrom =  date("Y-m-d H:i:s",  strtotime("-1 hour") );
        	
        	//             $sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}'";
        	$sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId} AND variation_type != '' AND variation != '' AND parent_id != '' LIMIT 10";
        	if(isset($productId)){
        		$sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
        	}
        	$totalUpdated = 0;
        	
        	$query = $db->query($sqlProduct);
        	
        	$products = $query->fetchAll(PDO::FETCH_ASSOC);
        	
        	foreach($products as $key => $rowProduct){
        			
				$salePriceModel = new SalePriceModel($db, null, $storeId);
				$salePriceModel->marketplace = "Marketplace";
				$salePriceModel->sku = $rowProduct['sku'];
				$salePriceModel->product_id = $rowProduct['id'];
				$salePrice = $salePriceModel->getSalePrice();
        		
        		$images = getUrlImageFromId($db, $storeId, $rowProduct['id']);
        		
        		$rootCategory = explode('>', $rowProduct['category']);
        		$query = $dbShopping->insert('products', array(
	    				'sku' => $rowProduct['sku'],
        				'parent_id' => $rowProduct['parent_id'],
        				'ean' => $rowProduct['ean'],
        				'title' => $rowProduct['title'],
        				'brand' => $rowProduct['brand'],
        				'category' => trim($rootCategory[0]),
        				'hierarchy' => $rowProduct['category'],
        				'color' => $rowProduct['color'],
        				'variation_type' => $rowProduct['variation_type'],
        				'variation' => $rowProduct['variation'],
        				'description' => $rowProduct['description'],
        				'price' => $salePrice,
        				'sale_price' => $salePrice,
        				'promotion_price' => empty($rowProduct['promotion_price']) ? 0.00 : $rowProduct['promotion_price'],
        				'weight' => empty($rowProduct['weight']) ? 1 : $rowProduct['weight'] ,
        				'width' => empty($rowProduct['width']) ? 1 : $rowProduct['width'] ,
        				'height' => empty($rowProduct['height']) ? 1 : $rowProduct['height'] ,
        				'length' => empty($rowProduct['length']) ? 1 : $rowProduct['length'] ,
        				'image' => $images[0],
        				'created_at' => empty($rowProduct['created_at']) ? date('Y-m-d H:i:s') : $rowProduct['created_at'] ,
        				'updated_at' => empty($rowProduct['updated_at']) ? date('Y-m-d H:i:s') : $rowProduct['updated_at'] ,
        				'status' => 'default'
	    				)
	    			);
        	}
        	logSyncEnd($db, $syncId, $totalUpdated);
            break;
            
    }
    
}

