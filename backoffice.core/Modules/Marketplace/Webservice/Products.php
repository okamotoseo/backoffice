<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;
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

	switch($action){
		
		case'update_stock_price':
			$sql = "UPDATE module_marketplace_products a 
					INNER JOIN available_products b ON a.seller_store_id = b.store_id AND a.seller_product_id = b.id 
					SET a.seller_quantity = if(b.blocked != 'T', b.quantity, '0'), a.seller_sale_price = b.sale_price";
			$query = $db->query($sql);
			
			break;
		
		
		
    	
    	case "remove_available_product":
    		
    		if(isset($productId)){
    			 
    			$idProducts = is_array($productId) ? $productId : array($productId) ;
    		
    			 
    			foreach($idProducts as $i => $productId){
    				$sql = "DELETE FROM module_marketplace_products WHERE id = {$productId}";
    				$queryDelete = $db->query($sql);
    				
    				
    				if(!$queryDelete){
    					pre($result);
    				}else{
    					$sql = "DELETE FROM publications WHERE marketplace LIKE ? AND publication_code = ? ";
    					$query = $db->query($sql, array('Marketplace', $productId));
    				}
    				 
    			}
    			if(isset($queryDelete)){
    				if($queryDelete){
    					echo "success|{$updated} Produto excluido com sucesso!";
    				}
    			}
    			 
    		}
    		break;
            
    	}
    	
}