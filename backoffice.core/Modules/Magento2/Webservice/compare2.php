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
				$queryAP = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$id}");
				$productsAP = $queryAP->fetch(PDO::FETCH_ASSOC);

				if(!empty($productsAP['parent_id'])){
					$configurables = array();
					$childSkus = array();
					$msgError = '';
					$productsLink = array();
					$queryParent = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$productsAP['parent_id']}'");
					$products = $queryParent->fetchAll(PDO::FETCH_ASSOC);
					$totalChild = count($products);



					/**********************************************************************************/
					/*************************** Configurable Product *********************************/
					/**********************************************************************************/
					if($totalChild > 0){
						$queryAP = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$products[0]['parent_id']}'
						AND id = {$id}");//quantity > 0 LIMIT 1
						$product = $queryAP->fetch(PDO::FETCH_ASSOC);
						if(isset($product['category'])){
							$sqlVerifyTmp = "SELECT product_id FROM mg2_products_tmp WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}'";
							$queryVerify = $db->query($sqlVerifyTmp);
							$productsTmp = $queryVerify->fetch(PDO::FETCH_ASSOC);
							if(!isset($productsTmp['product_id'])){

								$images = getPathImageFromSku($db, $storeId,$product['sku']);
								if(!empty($images[0])){
									$partsCategory = explode('>', $product['category']);
									$rootCategory = trim($partsCategory[0]);

									$categoryModel = new CategoryModel($db);
									$categoryModel->store_id = $product['store_id'];
									$categoryModel->hierarchy = $product['category'];
									$categoryModel->root_category = $rootCategory;
									$categorySetId = $categoryModel->GetSetAttributeFromRootCategory();
									$categoryId = $categoryModel->GetCategoriesId();
									$mg2CategoriesIds = array();
									$catalogCategories = new CategoriesModel($db, null, $storeId);
									$catalogCategories->category_id = $categoryId['id'];
									$catalogCategories->parent_id = $categoryId['parent_id'];
									$catalogCategory = $catalogCategories->GetCategoriesRelationship();
									if(empty($catalogCategory['mg2_category_id'])){
										echo "error|Efetuar o relacionamento da categoria";
										continue;
									}
									$mg2CategoriesIds[] = $catalogCategory['mg2_category_id'];
									$categories_ids[] = array('mg2_category_id' => $catalogCategory['mg2_category_id'], 'mg2_parent_id' =>  $catalogCategory['mg2_parent_id']);
									do{
										$sql = "SELECT mg2_category_id, mg2_parent_id FROM mg2_categories_relationship
										WHERE store_id = {$storeId} AND mg2_category_id = '{$catalogCategory['mg2_parent_id']}'";
										$query = $db->query($sql);
										$res = $query->fetch(PDO::FETCH_ASSOC);
										$catalogCategory['mg2_category_id'] = $res['mg2_category_id'];
										$catalogCategory['mg2_parent_id'] = $res['mg2_parent_id'];
										$categories_ids[] = array('mg2_category_id' => $res['mg2_category_id'], 'mg2_parent_id' =>  $res['mg2_parent_id']);
										$mg2CategoriesIds[] = $res['mg2_category_id'];
									}while($catalogCategory['mg2_parent_id'] != 2);

									$mg2CategoriesIds[] = 2;//root category

									if(!empty($mg2CategoriesIds)){

										/**
										 * Attribute Values
										 * Puxa todos atributos e valores relacionados
										 * @var Ambiguous $productEntity
										 */
										$attributesValuesModel->product_id = $product['id'];
										$attributesValues = $attributesValuesModel->GetProductAttributesValues();
										$queryAR = $db->query("SELECT * FROM mg2_attributes_relationship WHERE store_id = {$storeId} AND relationship != ''");
										$attributesRel = $queryAR->fetchAll(PDO::FETCH_ASSOC);

										foreach($attributesRel as $key => $attributeRel){
											$exist = false;
											$value = $name = '';
											switch($attributeRel['relationship']){
												case "color": $name = $attributeRel['attribute']; $value = $product['color']; $exist = true; break;
												case "voltage": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
												case "brand": $name = $attributeRel['attribute'];  $value = $product['brand']; $exist = true; break;
											}
											if($exist){

												$attributesValues[] = array(
														"id" => $attributeRel['attribute_id'],
														"store_id" => $attributeRel['store_id'],
														"attribute_id" => $attributeRel['attribute_code'],
														"name" => $name,
														"value" => $value
												);

											}else{
													
												foreach($attributesValues as $k => $attributeValue){

													if($attributeValue['attribute_id'] == $attributeRel['relat