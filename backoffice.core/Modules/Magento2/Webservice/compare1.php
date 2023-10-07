<?php
// set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../Class/class-Magento2.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Catalog/ProductsModel.php';
require_once $path .'/../Models/Catalog/MediaModel.php';
require_once $path .'/../Models/Catalog/CategoriesModel.php';
require_once $path .'/../Models/Catalog/InventoryModel.php';
require_once $path .'/../Models/Catalog/AttributesModel.php';
require_once $path .'/../Models/Catalog/AttributeSetModel.php';
require_once $path .'/../Models/Products/ProductsTempModel.php';
require_once $path .'/../Models/Products/SetAttributesRelationshipModel.php';
require_once $path .'/../Models/Products/AttributesValuesModel.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
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
		/**
		 * configurable
		 */
		case "create_product_magento2":
				
			if(!isset($productId)){
				echo "error|Selecione um produto...";
				exit;
			}
		
			require_once $path ."/../Models/Products/Templates/ItemDescriptionStoreId_{$storeId}.php";
		
			$setAttributesRelationship = new SetAttributesRelationshipModel($db, null, $storeId);
			$catalogAttributesModel = new AttributesModel($db, null, $storeId);
			$catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
			$attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
			$catalogProducts = new ProductsModel($db, null, $storeId);
			$availableProducts = new AvailableProductsModel($db);
			$mediaProducts = new MediaModel($db, null, $storeId);
			$salePriceModel = new SalePriceModel($db, null, $storeId);
			$salePriceModel->marketplace = "Marketplace";
			
			$productIds = is_array($productId) ? $productId : array($productId) ;
			foreach($productIds as $i => $id){
				pre($id);
				$queryAP = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$id}");
				$productsAP = $queryAP->fetch(PDO::FETCH_ASSOC);
				
				if(!empty($productsAP['parent_id'])){
					$childSkus = array();
					$msgError = '';
					$productsLink = array();
					$queryParent = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$productsAP['parent_id']}'");
					$products = $queryParent->fetchAll(PDO::FETCH_ASSOC);
					$totalChild = count($products);
					
					
					
					/**********************************************************************************/
					/*************************** Configurable Product *********************************/
					/**********************************************************************************/
					if(