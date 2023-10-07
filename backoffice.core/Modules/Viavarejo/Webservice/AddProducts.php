<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
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
			 
		case "export_products":
			$availableProducts = new AvailableProductsModel($db);
			$availableProducts->store_id = $storeId;
			$salePriceModel = new SalePriceModel($db, null, $storeId);
			$attributesValues = new AttributesValuesModel($db);
			$attributesValues->store_id = $storeId;
			$productModel = new ProductsModel($db);
			$productModel->store_id = $storeId;
			$productVariationsModel = new ProductVariationsModel($db);
			$productVariationsModel->store_id = $storeId;
			 
			$loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
			$productsSent = array();
			$productIds = is_array($productId) ? $productId : array($productId) ;
			if(!isset($productIds[0])){
				$sql = "SELECT id FROM available_products WHERE store_id = {$storeId} 
				AND category != '' AND parent_id != '' AND quantity > 0 LIMIT 20";
				$query = $db->query($sql);
				$ids = $query->fetchAll(PDO::FETCH_ASSOC);
				foreach($ids as $i => $id){
					$productIds[$i] = $id['id'];
				}

			}
			foreach($productIds as $ind => $id){

				$parentId = getParentIdFromId($db, $storeId, $id);

				if(!empty($parentId)){

					$sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}'";
					$query = $db->query($sql);
					$products = $query->fetchAll(PDO::FETCH_ASSOC);
					foreach($products as $key => $product){

						$images = getUrlImageFromId($db, $storeId,$product['id']);

						if(!empty($images[0])){
							$files = array();
							$productApi = new \CNovaApiLojistaV2\model\Product();
							foreach ($images as $key => $file){
								if(!empty($file)){
									$files[] = $file;
								}
							}
							$productApi->images = $files;
							 
							$productApi->sku_id = $product['id'];
							$productApi->sku_seller_id = $product['sku'];
							$productApi->product_seller_id = $product['parent_id'];
							$productApi->title = $product['title'];
							$productApi->description = $product['description'];
							$productApi->brand = $product['brand'];
							$productApi->gtin = array($product['ean']);
//             					$productApi->categories = array ($product['category']);
							 
							$productApi->categories = array ('Teste>API');
							$salePriceModel->sku = $product['sku'];
							$salePriceModel->marketplace = "Viavarejo";
							$salePrice = $salePriceModel->getSalePrice();
							$price = new \CNovaApiLojistaV2\model\ProductLoadPrices();
							$price->default = $salePrice;
							$price->offer = $salePrice;
							$productApi->price = $price;
							 
							$stock = new \CNovaApiLojistaV2\model\ProductLoadStock();
							$stock->quantity = $product['quantity'];
							$stock->cross_docking_time = 1;
							$productApi->stock = $stock;
							 
							$dimensions = new \CNovaApiLojistaV2\model\Dimensions();
							$dimensions->weight = str_replace(",", ".", $product['weight']);
							$dimensions->length = str_replace(",", ".", $product['length']);
							$dimensions->width = str_replace(",", ".", $product['width']);
							$dimensions->height = str_replace(",", ".", $product['height']);
							$productApi->dimensions = $dimensions;
							 
							$product_attr_color = array();
							if(isset($product['color']) && !empty($product['color'])){
								$product_attr_color = new \CNovaApiLojistaV2\model\ProductAttribute();
								$product_attr_color->name = 'Cor';
								$product_attr_color->value = $product['color'];
								$productApi->attributes[] = $product_attr_color;
							}
							 
							 
							$attributesValues->product_id = $product['id'];
							$resAttributesValues =  $attributesValues->GetAttributesValues();
							foreach($resAttributesValues as $k => $attrValue){

								if(!empty($attrValue['attribute']) && trim($attrValue['value']) != ''){
									 
									$productAttr = new \CNovaApiLojistaV2\model\ProductAttribute();
									$productAttr->name = trim($attrValue['attribute']);
									$productAttr->value = $attrValue['value'];
									$productApi->attributes[] = $productAttr;
									 
								}else{
									 
									$queryML = $db->query("SELECT name FROM ml_attributes_required WHERE attribute_id LIKE '{$attrValue['attribute_id']}'");
									$resML = $queryML->fetch(PDO::FETCH_ASSOC);
									 
									if(!empty($resML['name']) && trim($attrValue['value']) != ''){

										$productAttr = new \CNovaApiLojistaV2\model\ProductAttribute();
										$productAttr->name = trim($resML['name']);
										$productAttr->value = $attrValue['value'];
										$productApi->attributes[] = $productAttr;
									}
								}
							}
							 
							$product_attr_size = array();
							if(isset($product['variation']) && !empty($product['variation'])){
								if(isset($product['variation_type']) && !empty($product['variation_type'])){
									$product_attr_size = new \CNovaApiLojistaV2\model\ProductAttribute();
									$product_attr_size->name = ucfirst($product['variation_type']);
									$product_attr_size->value = $product['variation'];
									$productApi->attributes[] = $product_attr_size;
								}
							}
							 
//             					$productApi->attributes =  array($product_attr_color, $product_attr_size);
							 
							$apiProducts[] = $productApi;
							 
							$productsSent[] = $product;
							 
						}
					}
					 
				}

			}

			try {
				pre($apiProducts);
				$res = $loads->postProducts($apiProducts);
				pre($res);
			} catch (\CNovaApiLojistaV2\client\ApiException $e) {
				$errors = deserializeErrors($e->getResponseBody(), $api_client);
				if ($errors != null) {
					foreach ($errors->errors as $error) {
						echo ($error->code . ' - ' . $error->message . "\n");
					}
				} else {
					pre($res);
					$res = $e->getMessage();
					pre($res);
				}
			}
			if($errors == null){
				foreach($productsSent as $key => $product){
					$productModel->sku = $product['sku'];
					$productModel->product_id = $product['id'];
					$productModel->parent_id = $product['parent_id'];
					$productModel->status = 'sent';
					$idProduct = $productModel->save();
					pre($idProduct);
				}
					
				if(isset($idProduct)){
					echo "success|Enviado copm sucesso".count($apiProducts);
				}
					
			}

			break;
			 
	}

}
