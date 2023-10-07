<?php
// set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
error_reporting(E_ALL | E_STRICT);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../../../Models/Products/PublicationsModel.php';
require_once $path .'/../../../Views/_uploads/images.php';
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
			$salePriceModel->marketplace = "Mg2";
			$publicationsModel = new PublicationsModel($db);
			$publicationsModel->store_id = $storeId;
			$publicationsModel->user = $request;
			$publicationsModel->marketplace = 'Magento2';
			$logError = array();
			$productIds = is_array($productId) ? $productId : array($productId) ;
			foreach($productIds as $i => $id){
				$queryAP = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$id}");
				$productsAP = $queryAP->fetch(PDO::FETCH_ASSOC);
				if(!empty($productsAP['parent_id'])){
					
					$childSkus = array();
					$msgError = '';
					$productsLink = array();
					$queryParent = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$productsAP['parent_id']}' ORDER BY variation ASC");
					$products = $queryParent->fetchAll(PDO::FETCH_ASSOC);
					$totalChild = count($products);
					$simpleVisibility = 4;
					/**********************************************************************************/
					/*************************** Configurable Product *********************************/
					/**********************************************************************************/
					if($totalChild > 1 OR $products[0]['variation_type'] == 'tamanho'){
						$simpleVisibility = 1;
						$queryAP = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$products[0]['parent_id']}'
						AND id = {$id}");//quantity > 0 LIMIT 1
						$product = $queryAP->fetch(PDO::FETCH_ASSOC);
						if(isset($product['category']) && !empty($product['category'])){
							
							$sqlVerifyTmp = "SELECT product_id FROM mg2_products_tmp WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}'";
							$queryVerify = $db->query($sqlVerifyTmp);
							$productsTmp = $queryVerify->fetch(PDO::FETCH_ASSOC);
							
							if(!isset($productsTmp['product_id'])){
								
								if($storeId == 7){
									/**
									 * Select best price and analisys from offers
									 */
									$sql = "SELECT * FROM `module_marketplace_products` WHERE seller_ean LIKE '{$product['ean']}'";
									$queryOffers = $db->query($sql);
									$resOffers = $queryOffers->fetch(PDO::FETCH_ASSOC);
								}
								
								$images = getPathImageFromSku($db, $storeId, $product['sku']);
								if(!isset($images[0])){
									$images = getPathImageFromParentId($db, $storeId, $product['parent_id']);
								}
								if(empty($images[0])){
									$logError[$productsAP['id']][] = "error|Produto sem fotos.";
									continue;
								}
								
								if(empty($product['variation_type']) OR empty($product['variation'])){
									$logError[$productsAP['id']][] = "error|Produto deve ter um tipo de variação, tamanho, voltagem...";
									continue;
								}
								
								if(!empty($images[0])){
									$partsCategory = explode('>', $product['category']);
									$rootCategory = trim($partsCategory[0]);
									
									$categoryModel = new CategoryModel($db);
									$categoryModel->store_id = $product['store_id'];
									$categoryModel->hierarchy = $product['category'];
									$categoryModel->root_category = $rootCategory;
									
									$categorySetId = $categoryModel->GetSetAttributeFromRootCategory();
									if(empty($categorySetId)){
										$logError[$productsAP['id']][] = "error|A Categoria {$rootCategory} não possui um Conjunto de Atributos...";
										continue;
									}
									
									$categoryId = $categoryModel->GetCategoriesId();
									$mg2CategoriesIds = array();
									$catalogCategories = new CategoriesModel($db, null, $storeId);
									$catalogCategories->category_id = $categoryId['id'];
									$catalogCategories->parent_id = $categoryId['parent_id'];
									$catalogCategory = $catalogCategories->GetCategoriesRelationship();
									if(empty($catalogCategory['mg2_category_id'])){
										$logError[$productsAP['id']][] = "error|Efetuar o relacionamento da categoria";
										continue;
									}
									$mg2CategoriesIds[] = $catalogCategory['mg2_category_id'];
									$categories_ids[] = array('mg2_category_id' => $catalogCategory['mg2_category_id'], 'mg2_parent_id' =>  $catalogCategory['mg2_parent_id']);
									if(isset($catalogCategory['mg2_parent_id']) && !empty($catalogCategory['mg2_parent_id'])){
										do{
											$sql = "SELECT mg2_category_id, mg2_parent_id FROM mg2_categories_relationship
											WHERE store_id = {$storeId} AND mg2_category_id = '{$catalogCategory['mg2_parent_id']}'";
											$query = $db->query($sql);
											$res = $query->fetch(PDO::FETCH_ASSOC);
											$catalogCategory['mg2_category_id'] = $res['mg2_category_id'];
											$catalogCategory['mg2_parent_id'] = $res['mg2_parent_id'];
											$categories_ids[] = array('mg2_category_id' => $res['mg2_category_id'], 'mg2_parent_id' =>  $res['mg2_parent_id']);
											$mg2CategoriesIds[] = $res['mg2_category_id'];
										}while($catalogCategory['mg2_parent_id'] > 2 );
									}else{
										$logError[$productsAP['id']][] = "error|Produto sem Relacionamento de Categoria"; 
										continue;
									}
									$mg2CategoriesIds[] = 2;//root category
										
									if(!empty($mg2CategoriesIds)){
										
										$setAttributesRelationship->set_attribute_id = $categorySetId['set_attribute_id'];
										$setAttributeRel = $setAttributesRelationship->GetSetAttributeRelationship();
// 										pre($setAttributeRel);
										$setAttributeRel = isset($setAttributeRel[0]) ? $setAttributeRel[0] : '' ;
										if(!isset($setAttributeRel['mg2_attribute_set_id'])){
											$logError[$productsAP['id']][] = "error|Erro por favor relacione o conjunto de attributos.";
											continue;
										}

										/**
										 * Attribute Values
										 * Puxa todos atributos e valores relacionados
										 * @var Ambiguous $productEntity
										 */
										$attributesValuesModel->product_id = $product['id'];
										$attributesValues = $attributesValuesModel->GetProductAttributesValues();
										$queryAR = $db->query("SELECT * FROM mg2_attributes_relationship WHERE store_id = {$storeId}");
										$attributesRel = $queryAR->fetchAll(PDO::FETCH_ASSOC);
										/**
										 *for each attribute Magento2 do:
										 */
										foreach($attributesRel as $key => $attributeRel){
											
											if($attributeRel['attribute_code'] == 'lojas' ){
												
												if($storeId == 7){
												
													if(isset($resOffers['seller_store'])){
													
														$attributesValues[] = array(
																"id" => $attributeRel['attribute_id'],
																"store_id" => $attributeRel['store_id'],
																"attribute_id" => $attributeRel['attribute_code'],
																"name" => $attributeRel['attribute'],
																"value" => !empty($resOffers['seller_store']) ? $resOffers['seller_store'] : 'Depato' 
														);
													}
												}
												
											}
											
											$exist = false;
											$value = $name = '';
											if(!empty($attributeRel['relationship'])){
											
												switch($attributeRel['relationship']){
													case "voltagem": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
													case "unidade": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
													case "tamanho": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
													case "volume": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
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
													
													$attributesValues[] = array(
															"id" => $attributeRel['attribute_id'],
															"store_id" => $attributeRel['store_id'],
															"attribute_id" => $attributeRel['attribute_code'],
															"name" => $attributeRel['attribute'],
															"value" => $product[$attributeRel['relationship']]
													);
													
													$exist = true;
													
												}
												
												
											}else{
												
												/**
												 * Get Map Set Attribute Relationship  
												 * if not exists default attribute relationship, 
												 * verify if exists in  set attribute relationship table and 
												 * replace var attribute_id in object product attribute values resource
												 */
												foreach($attributesValues as $k => $attributeValue){
													$attributeCode = str_replace("-", "_", $attributeValue['attribute_id']);
													if(!empty($attributeValue['marketplace'])){
														$attributeCode = strtolower($attributeCode);
													}
													$sqlAttrRel = "SELECT * FROM mg2_set_attribute_relationships WHERE store_id = {$storeId} 
													AND attribute_set_id = {$setAttributeRel['mg2_attribute_set_id']}  
													AND attribute_id = {$attributeRel['attribute_id']}";
													$querySetAttrRel = $db->query($sqlAttrRel);
													$setAttrRel = $querySetAttrRel->fetch(PDO::FETCH_ASSOC);
													if(!empty($setAttrRel['attribute_code'])){
														
														if($attributeValue['attribute_id'] == $setAttrRel['relationship']){
															
															if(isset($attributesValues[$k]['mapped'])  && !empty($attributesValues[$k]['mapped'])){
																$attributesValues[$k]['attribute_id'] = $setAttrRel['attribute_code'];
																$attributesValues[$k]['name'] = $attributeRel['attribute'];
																$attributesValues[$k]['mapped'] = 'mapped';
																unset($attributesValues[$k]['marketplace']);
																
															}else{
																
																if(!empty($attributesValues[$k]['value'])){
																	$attributesValues[] = array(
																		"id" => $attributeRel['attribute_id'],
																		"store_id" => $attributeRel['store_id'],
																		"attribute_id" => $setAttrRel['attribute_code'],
																		"name" => $attributeRel['attribute'],
																		"value" => $attributesValues[$k]['value']
																	);
																}
																	
															}
															
														}
															
													}
												
												}

											}

										}
										/**
										 * Product Entity Constroe Objeto Produto
										 * @var Ambiguous $productEntity
										 */
										$qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
										$weight = intval($product['weight']) > 10 ? $product['weight'] /1000 : $product['weight'];
// 										$weight = 1;
										$salePriceModel->sku = $product['sku'];
										$salePriceModel->product_id = $product['id'];
										$salePrice = $salePriceModel->getSalePrice();
										$stockPriceRel = $salePriceModel->getStockPriceRelacional();
										$salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
										$salePrice =  (double)filter_var($salePrice, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
										$qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;

										if ($product['blocked'] == "T"){
											$qtd = 0;
											$logError[$productsAP['id']][] = "error|Produto Bloqueado...";
											continue;
										}

										$parenSku = $product['sku'].'-x';
										
										$productEntity = new stdClass();
										
										$title = trim($product['title'])." ".$product['id'];
										$title = implode(" ", array_unique(explode(" ", $title)));
										$productEntity->name = $title;
										
										
										$productEntity->weight = number_format($weight, 4, '.', '');
// 										$productEntity->weight = $weight;
										
										
										$productEntity->type_id = 'configurable';
										$productEntity->visibility = 4;
										$productEntity->sku = $parenSku;
										$productEntity->status = '1';
// 										$productEntity->price = number_format($salePrice, 2);
										$productEntity->price = $salePrice;
										
// 										pre($productEntity);die;
										sort($mg2CategoriesIds);
										foreach($mg2CategoriesIds as $pos => $catVal){
											$productEntity->extension_attributes['category_links'][] = array(
													"position" => $pos,
													"category_id" => $catVal
											);
										}
										$productEntity->extension_attributes['stock_item']  =  array(
												'manage_stock' => 1,
												'qty' => $qtd,
												'is_in_stock' => $qtd > 0 ? true : true
										);
										$count = 0;
										$spotlight = array();
										foreach($attributesValues as $key => $attributeVal){
											$attributeCode = str_replace("-", "_", $attributeVal['attribute_id']);
											if(isset($attributeVal['marketplace'])){
												$attributeCode = strtolower($attributeCode);
											}
											$sqlAttrRel = "SELECT * FROM mg2_attributes_relationship WHERE store_id = {$storeId}
											AND attribute_code LIKE '{$attributeCode}'";
											$queryAttrRel = $db->query($sqlAttrRel);
											$attrRel = $queryAttrRel->fetch(PDO::FETCH_ASSOC);
											
											if($attrRel['frontend_input'] == 'select' OR $attrRel['frontend_input'] == 'multiselect'){
												$optionSelected = array();
												$catalogAttributesModel->attribute_code = $attributeCode;
												$options = $catalogAttributesModel->catalogProductAttributeOptions();
												foreach($options['body'] as $i => $option){
													
													if(!empty(trim($option->label))){
														if(strtolower(trim($option->label)) == strtolower(trim($attributeVal['value']))){
															$optionSelected = array(
																	'attribute_code' => $attributeCode,
																	'value' => $option->value
															);
															
															if($attrRel['spotlight'] == 1){
																$spotlight[$attributeCode]['label'] = trim($attrRel['attribute']);
																$spotlight[$attributeCode]['value'] = trim($option->label);
															}
															
															if($attributeCode == 'manufacturer'){
																$mg2BrandId = $option->value;
															}

														}
													}
												}
												if(!empty($optionSelected['attribute_code'])){

													$productEntity->custom_attributes[] = $optionSelected;

												}else{
													
													if($attrRel['spotlight'] == 1){
														$spotlight[$attributeCode]['label'] = trim($attrRel['attribute']);
														$spotlight[$attributeCode]['value'] = trim($attributeVal['value']);
													}
													
													$catalogAttributesModel->attribute_option['option'] = array(
															"label" => trim($attributeVal['value']),
															"value" => trim($attributeVal['value'])
													);
													$attributeValueId = $catalogAttributesModel->catalogProductAttributeAddOption();
													if(isset($attributeValueId['body']->message)){
														echo "error|{$result['body']->message}";
														pre($attributeVal);
													}else{
														$attributeValueId = str_replace("id_", "",  $attributeValueId['body']);
														$productEntity->custom_attributes[] = array(
																'attribute_code' => $attributeCode,
																'value' => $attributeValueId
														);
													}
												}
											}else{
												if($attributeCode != 'short_description'){
													if(!empty($attributeVal['value'])){
														$productEntity->custom_attributes[] = array(
																'attribute_code' => $attributeCode,
																'value' => "{$attributeVal['value']}"
														);
														if(isset($attrRel['spotlight'])){
															if($attrRel['spotlight'] == 1){
																$spotlight[$attributeCode]['label'] = trim($attrRel['attribute']);
																$spotlight[$attributeCode]['value'] = trim($attributeVal['value']);
															}
														}
													}
												}
											}
										}
										
										if(!empty($spotlight)){
											$countSpotlight = 0;
											
											$shortDescription = "<p>Aproveite {$product['title']} com ✓ Envio em até 24h e ✓ Troca ou Devolução Garantida.</p>
											<ul class='attribute_short_description' >";
											
											foreach($spotlight as $codeAttrMg2 => $valueAttr){
												if(!empty($valueAttr['value'])){
													$countSpotlight++;
													$shortDescription .= "<li><strong>{$valueAttr['label']}</strong>: {$valueAttr['value']}</li>";
												}
// 												if($countSpotlight >= 5){
// 													break;
// 												}
											}
											$shortDescription .= '</ul>';
												
											$productEntity->custom_attributes[] = array(
													'attribute_code' => 'short_description',
													'value' => "{$shortDescription}"
											);
										}
										
										$productEntity->attribute_set_id = $setAttributeRel['mg2_attribute_set_id'];
									}
									
// 									pre($productEntity);
									$catalogProducts->product = (object) array('product' => $productEntity);
									/**
									 * Envia o objeto produto
									 * @var unknown $result
									 */
									$result = $catalogProducts->catalogProductCreate();

									if(isset($result['body']->message)){
										$logError[$productsAP['id']][] = "error|{$result['body']->message}";
										continue;
									}else{
										pre('resultado produto criado');
										pre($result);
										// get product configurable id
										$mg2ProductId = $result['body']->id;
									}
									if(!empty($images) && !empty($mg2ProductId)){
										$position = 0;

										foreach($images as $key => $urlImage){
											if(!empty($urlImage)){
												$media = array();
												$imageName = explode('/', $urlImage);
												$fileName = end($imageName);
												$fileNameSize = strlen($fileName);
												if($fileNameSize > 60){
													$fileName = substr($fileName, ($fileNameSize - 59));
												}
												$fileContent = array(
														'base64_encoded_data' =>  base64_encode(file_get_contents(trim($urlImage))),
														'type' => image_type_to_mime_type(exif_imagetype(trim($urlImage))),
														'name' => trim(str_replace(' ', '', "{$product['sku']}-".$fileName))
												);
												if($key == 0){
													$types =  array('image', 'small_image', 'thumbnail', 'swatch_image');
												}else{
													$types =  array();
												}
												$position = $key+1;
												$media = array(
														'media_type' => 'image',
														'label' => trim($product['title']),
														'disabled' => false,
														'types' => $types,
														'position' => $position,
														'content' => $fileContent,
												);
												$mediaProducts->product_id = $mg2ProductId;
												$mediaProducts->sku = $parenSku;
												$mediaProducts->media = (object) array('entry' => $media);
												
												$resultMedia = $mediaProducts->catalogProductAttributeMediaCreate();
												if(!$resultMedia){
													pre($resultMedia);
												}
											}

										}
										$productsTempModel = new ProductsTempModel($db, null, $storeId);
										$productsTempModel->product_id = $mg2ProductId;
										$productsTempModel->sku = $parenSku;
										$productsTempModel->title = $product['title'];
										$productsTempModel->color = $product['color'];
										$productsTempModel->variation = $product['variation'];
										$productsTempModel->brand = $mg2BrandId;
										$productsTempModel->reference = $product['reference'];
										$productsTempModel->category = $product['category'];
										$productsTempModel->qty = $qtd;
										$productsTempModel->price = $product['sale_price'];
										$productsTempModel->sale_price = $salePrice;
										$productsTempModel->promotion_price = $product['promotion_price'];
										$productsTempModel->cost = $product['cost'];
										$productsTempModel->weight = $weight;
										$productsTempModel->height = $product['height'];
										$productsTempModel->width = $product['width'];
										$productsTempModel->length = $product['length'];
										$productsTempModel->ean = $product['ean'];
										$productsTempModel->image = '';
										$productsTempModel->type_id = $productEntity->type_id;
										$productsTempModel->attribute_set_id = $productEntity->attribute_set_id;
										$productsTempModel->visibility = $productEntity->visibility;
										$productsTempModel->categories_ids = json_encode($mg2CategoriesIds);
										$productsTempModel->created_at = date('Y-m-d H:i:s');
										$productsTempModel->updated_at = date('Y-m-d H:i:s');
										$productsTempModel->status = $productEntity->status;
										$productsTempModel->Save();
										
										$publicationsModel->publication_code = $mg2ProductId;
										$publicationsModel->product_id = $product['id'];
										$publicationsModel->sku = $parenSku;
										$publicationsModel->Save();
										
										
// 										echo "success|Produto cadastrado com sucesso!|{$parenSku}";
									}

								}
									
							}else{
								$logError[$productsAP['id']][] = "SKU:{$product['sku']} já cadastrado \n";
							}
						}

					}
						
					/**********************************************************************************/
					/****************************** Simple Product ************************************/
					/**********************************************************************************/
					foreach($products as $ind => $product){
						if(isset($product['category']) && !empty($product['category'])){
							$sqlVerifyTmp = "SELECT product_id FROM mg2_products_tmp WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}'";
							$queryVerify = $db->query($sqlVerifyTmp);
							$productsTmp = $queryVerify->fetch(PDO::FETCH_ASSOC);
							
							if(!isset($productsTmp['product_id'])){
								
								if($storeId == 7){
									/**
									 * Select best price and analisys from offers
									 */
									$sql = "SELECT * FROM `module_marketplace_products` WHERE seller_ean LIKE '{$product['ean']}'";
									$queryOffers = $db->query($sql);
									$resOffers = $queryOffers->fetch(PDO::FETCH_ASSOC);
										
								}
								$images = getPathImageFromSku($db, $storeId,$product['sku']);
								if(!isset($images[0])){
									$images = getPathImageFromParentId($db, $storeId, $product['parent_id']);
								}
								
								if(empty($images[0])){
									$logError[$productsAP['id']][] = "error|Produto sem fotos.";
									continue;
								}
							
								$product['variation_type'] = trim($product['variation_type']);
								$product['variation_type'] = !empty($product['variation_type']) ? $product['variation_type'] : 'unidade';
								$product['variation'] = trim($product['variation']);
								$product['variation'] = !empty($product['variation']) ? $product['variation'] : 'UN';
								
								if(!empty($images[0])){
									$partsCategory = explode('>', $product['category']);
									$rootCategory = trim($partsCategory[0]);

									$categoryModel = new CategoryModel($db);
									$categoryModel->store_id = $product['store_id'];
									$categoryModel->hierarchy = $product['category'];
									$categoryModel->root_category = $rootCategory;
									$categorySetId = $categoryModel->GetSetAttributeFromRootCategory();
									if(empty($categorySetId)){
										$logError[$productsAP['id']][] = "error|Produto sem Relacionamento de Categoria";
										continue;
									}
									$categoryId = $categoryModel->GetCategoriesId();
									$mg2CategoriesIds = array();
									$catalogCategories = new CategoriesModel($db, null, $storeId);
									$catalogCategories->category_id = $categoryId['id'];
									$catalogCategories->parent_id = $categoryId['parent_id'];
									$catalogCategory = $catalogCategories->GetCategoriesRelationship();
									if(empty($catalogCategory['mg2_category_id'])){
										$logError[$productsAP['id']][] = "error|Efetuar o relacionamento da categoria";
										continue;
									}
									$mg2CategoriesIds[] = $catalogCategory['mg2_category_id'];
									$categories_ids[] = array('mg2_category_id' => $catalogCategory['mg2_category_id'], 'mg2_parent_id' =>  $catalogCategory['mg2_parent_id']);
									if(isset($catalogCategory['mg2_parent_id']) && !empty($catalogCategory['mg2_parent_id'])){
										do{
											$sql = "SELECT mg2_category_id, mg2_parent_id FROM mg2_categories_relationship
											WHERE store_id = {$storeId} AND mg2_category_id = '{$catalogCategory['mg2_parent_id']}'";
											$query = $db->query($sql);
											$res = $query->fetch(PDO::FETCH_ASSOC);
											
											$catalogCategory['mg2_category_id'] = $res['mg2_category_id'];
											$catalogCategory['mg2_parent_id'] = $res['mg2_parent_id'];
											$categories_ids[] = array('mg2_category_id' => $res['mg2_category_id'], 'mg2_parent_id' =>  $res['mg2_parent_id']);
											$mg2CategoriesIds[] = $res['mg2_category_id'];
										}while($catalogCategory['mg2_parent_id'] > 2 );
									}else{
										$logError[$productsAP['id']][] = "error|Produto sem Relacionamento de Categoria";
										continue;
									}
									$mg2CategoriesIds[] = 2;//root category
// 										pre($mg2CategoriesIds);die;
									if(!empty($mg2CategoriesIds)){
										
										$setAttributesRelationship->set_attribute_id = $categorySetId['set_attribute_id'];
										$setAttributeRel = $setAttributesRelationship->GetSetAttributeRelationship();
										$setAttributeRel = isset($setAttributeRel[0]) ? $setAttributeRel[0] : '' ;
										if(!isset($setAttributeRel['mg2_attribute_set_id'])){
											$logError[$productsAP['id']][] = "error|Erro por favor relacione o conjunto de attributos.";
											continue;
										}
											
										$attributesValuesModel->product_id = $product['id'];
										$attributesValues = $attributesValuesModel->GetProductAttributesValues();
										$queryAR = $db->query("SELECT * FROM mg2_attributes_relationship WHERE store_id = {$storeId}");
										$attributesRel = $queryAR->fetchAll(PDO::FETCH_ASSOC);
										
										/**
										 *for each attribute Magento2 do:
										 */
										foreach($attributesRel as $key => $attributeRel){
											
											
											if($attributeRel['attribute_code'] == 'lojas' ){
											
												if($storeId == 7){
											
													if(isset($resOffers['seller_store'])){
															
														$attributesValues[] = array(
																"id" => $attributeRel['attribute_id'],
																"store_id" => $attributeRel['store_id'],
																"attribute_id" => $attributeRel['attribute_code'],
																"name" => $attributeRel['attribute'],
																"value" => !empty($resOffers['seller_store']) ? $resOffers['seller_store'] : 'Depato'
														);
													}
												}
											
											}
											$exist = false;
											$value = $name = '';
											if(!empty($attributeRel['relationship'])){
													
												switch($attributeRel['relationship']){
													case "voltagem": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
													case "unidade": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
													case "tamanho": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
													case "volume": $name = $attributeRel['attribute']; $value = $product['variation']; $exist = true; break;
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
														
													$attributesValues[] = array(
															"id" => $attributeRel['attribute_id'],
															"store_id" => $attributeRel['store_id'],
															"attribute_id" => $attributeRel['attribute_code'],
															"name" => $attributeRel['attribute'],
															"value" => $product[$attributeRel['relationship']]
													);
														
													$exist = true;
														
												}
											
											
											}else{
													
												/**
												 * Get Map Set Attribute Relationship  
												 * if not exists default attribute relationship, 
												 * verify if exists in  set attribute relationship table and 
												 * replace var attribute_id in object product attribute values resource
												 */
												foreach($attributesValues as $k => $attributeValue){
													$attributeCode = str_replace("-", "_", $attributeValue['attribute_id']);
													if(!empty($attributeValue['marketplace'])){
														$attributeCode = strtolower($attributeCode);
													}
													$sqlAttrRel = "SELECT * FROM mg2_set_attribute_relationships WHERE store_id = {$storeId} 
													AND attribute_set_id = {$setAttributeRel['mg2_attribute_set_id']}  
													AND attribute_id = {$attributeRel['attribute_id']}";
													$querySetAttrRel = $db->query($sqlAttrRel);
													$setAttrRel = $querySetAttrRel->fetch(PDO::FETCH_ASSOC);
													if(!empty($setAttrRel['attribute_code'])){
														
														if($attributeValue['attribute_id'] == $setAttrRel['relationship']){
															
															if(isset($attributesValues[$k]['mapped'])  && !empty($attributesValues[$k]['mapped'])){
																$attributesValues[$k]['attribute_id'] = $setAttrRel['attribute_code'];
																$attributesValues[$k]['name'] = $attributeRel['attribute'];
																$attributesValues[$k]['mapped'] = 'mapped';
																unset($attributesValues[$k]['marketplace']);
																
															}else{
																
																if(!empty($attributesValues[$k]['value'])){
																	$attributesValues[] = array(
																		"id" => $attributeRel['attribute_id'],
																		"store_id" => $attributeRel['store_id'],
																		"attribute_id" => $setAttrRel['attribute_code'],
																		"name" => $attributeRel['attribute'],
																		"value" => $attributesValues[$k]['value']
																	);
																}
																	
															}
															
														}
															
													}
												
												}

											}

										}
// 										pre('attributes values');
// 										pre($attributesValues);
										$qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
										$weight = intval($product['weight']) > 10 ? $product['weight'] /1000 : $product['weight'];
										$salePriceModel->sku = $product['sku'];
										$salePriceModel->product_id = $product['id'];
										$salePrice = $salePriceModel->getSalePrice();
										$stockPriceRel = $salePriceModel->getStockPriceRelacional();
										$salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
										$salePrice =  (double)filter_var($salePrice, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
										$qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
											
										if ($product['blocked'] == "T"){
											$qtd = 0;
											$logError[$productsAP['id']][] = "error|Produto Bloqueado...";
											continue;
										}
											
										/**
										 * Product Entity
										 * @var Ambiguous $productEntity
										 */
										$productEntity = new stdClass();
// 										$productEntity->name = trim($product['title'])." ".$product['id'];
// 										$weight = 1;
										$title = trim($product['title'])." ".$product['color'].' '.$product['variation'];
    									$title = implode(" ", array_unique(explode(" ", $title)));
										$productEntity->name = $title;
										$productEntity->weight = number_format($weight, 4, '.', '');
// 										$productEntity->weight = $weight;
										$productEntity->type_id = 'simple';
										$productEntity->visibility = $simpleVisibility;
										$productEntity->sku = $product['sku'];
										$productEntity->status = '1';
// 										$productEntity->price = number_format($salePrice, 2);
										$productEntity->price = $salePrice;
										sort($mg2CategoriesIds);
										foreach($mg2CategoriesIds as $pos => $catVal){
											$productEntity->extension_attributes['category_links'][] = array(
													"position" => $pos,
													"category_id" => $catVal
											);
										}
										$productEntity->extension_attributes['stock_item']  =  array(
												'manage_stock' => 1,
												'qty' => $qtd,
												'is_in_stock' => $qtd > 0 ? true : false
										);
										$count = 0;
										$spotlight = array();
										foreach($attributesValues as $key => $attributeVal){
												
											$attributeCode = str_replace("-", "_", $attributeVal['attribute_id']);
											if(isset($attributeVal['marketplace'])){
												$attributeCode = strtolower($attributeCode);
											}
											$sqlAttrRel = "SELECT * FROM mg2_attributes_relationship WHERE store_id = {$storeId}
											AND attribute_code LIKE '{$attributeCode}'";
											$queryAttrRel = $db->query($sqlAttrRel);
											$attrRel = $queryAttrRel->fetch(PDO::FETCH_ASSOC);
											
// 											if(!empty($attrRel))
											
											if($attrRel['frontend_input'] == 'select' OR $attrRel['frontend_input'] == 'multiselect'){
												$optionSelected = array();
												$catalogAttributesModel->attribute_code = $attributeCode;
												$options = $catalogAttributesModel->catalogProductAttributeOptions();
												foreach($options['body'] as $i => $option){
													
													if(!empty(trim($option->label))){
														if(strtolower(trim($option->label)) == strtolower(trim($attributeVal['value']))){
															$optionSelected = array(
																	'attribute_code' => $attributeCode,
																	'value' => $option->value
															);
															if($attrRel['spotlight'] == 1){
																$spotlight[$attributeCode]['label'] = trim($attrRel['attribute']);
																$spotlight[$attributeCode]['value'] = trim($option->label);
															}
															if($attributeCode == 'manufacturer'){
																$mg2BrandId = $option->value;
															}
														}
													}
												}
												if(!empty($optionSelected['attribute_code'])){

													$productEntity->custom_attributes[] = $optionSelected;
													
													

												}else{
													
													if($attrRel['spotlight'] == 1){
														$spotlight[$attributeCode]['label'] = trim($attrRel['attribute']);
														$spotlight[$attributeCode]['value'] = trim($attributeVal['value']);
													}
													
													$catalogAttributesModel->attribute_option['option'] = array(
															"label" => trim($attributeVal['value']),
															"value" => trim($attributeVal['value'])
													);
													
													$attributeValueId = $catalogAttributesModel->catalogProductAttributeAddOption();
													if(isset($attributeValueId['body']->message)){
														echo "error|{$result['body']->message}";
													}else{
														$attributeValueId = str_replace("id_", "",  $attributeValueId['body']);
														$productEntity->custom_attributes[] = array(
																'attribute_code' => $attributeCode,
																'value' => $attributeValueId
														);
														
													}
													
												}
												
												if($attrRel['is_configurable'] == 1 && !empty($optionSelected['attribute_code']) && !empty($optionSelected['value'])){
													
													if($attrRel['relationship'] == 'color' OR $attrRel['relationship'] == $setAttributeRel['variation_type']){
														
														if(!isset($configurables[$attrRel['attribute_id']]['option'])){
															$configurables[$attrRel['attribute_id']]['option'] = array(
																	"attribute_id" => $attrRel['attribute_id'],
																	"label" => $attrRel['attribute'],
																	"position" => 0,
																	"is_use_default" => true
															);
														}else{
															$configurables[$attrRel['attribute_id']]['option']['values'][] = array("value_index" => $optionSelected['value']);
														}
													}
												
												}
													
											}else{
												
												if($attributeCode != 'short_description'){
												
													if(!empty($attributeVal['value'])){
														$productEntity->custom_attributes[] = array(
																'attribute_code' => $attributeCode,
																'value' => "{$attributeVal['value']}"
														);
														if(isset($attrRel['spotlight'])){
															if($attrRel['spotlight'] == 1){
																$spotlight[$attributeCode]['label'] = trim($attrRel['attribute']);
																$spotlight[$attributeCode]['value'] = trim($attributeVal['value']);
															}
														}
													}
													
												}
											}
										}
										if(!empty($spotlight)){
											$countSpotlight = 0;
											$shortDescription = "<p>Compre {$product['title']} com ✓ Envio em até 24h e ✓ Troca ou Devolução Garantida</p>
											<ul class='attribute_short_description' >";
											foreach($spotlight as $codeAttrMg2 => $valueAttr){
												
												if(!empty($valueAttr['value'])){
													
													$countSpotlight++;
													$shortDescription .= "<li><strong>{$valueAttr['label']}</strong>: {$valueAttr['value']}</li>";
												}
													
											}
											$shortDescription .= '</ul>';
											
											$productEntity->custom_attributes[] = array(
													'attribute_code' => 'short_description',
													'value' => "{$shortDescription}"
											);
										}
										
										
										
										$productEntity->attribute_set_id = $setAttributeRel['mg2_attribute_set_id'];
									}
// 									pre($productEntity);
									$catalogProducts->product = (object) array('product' => $productEntity);
									
									/**
									 * Envia o objeto produto
									 * @var unknown $result
									 */
									$result = $catalogProducts->catalogProductCreate();
								
									if(isset($result['body']->message)){
										$logError[$productsAP['id']][] = "error|{$result['body']->message}";
										continue;
									}else{
										pre($result);
										//get sku child to attach in configurable product
										$childSkus[] = $product['sku'];
										$mg2ProductId = $result['body']->id;
									}
									if(!empty($images) && !empty($mg2ProductId)){
										$position = 0;
											
										foreach($images as $key => $urlImage){
											if(!empty($urlImage)){
												$media = array();
												$imageName = explode('/', trim($urlImage));
												$fileName = end($imageName);
												$fileNameSize = strlen($fileName);
												if($fileNameSize > 60){
													$fileName = substr($fileName, ($fileNameSize - 59));
												}
												
												$fileContent = array(
														'base64_encoded_data' =>  base64_encode(file_get_contents(trim($urlImage))),
														'type' => image_type_to_mime_type(exif_imagetype(trim($urlImage))),
														'name' => trim(str_replace(' ', '', "{$product['sku']}-".$fileName))
												);
												if($key == 0){
													$types =  array('image', 'small_image', 'thumbnail', 'swatch_image');
												}else{
													$types =  array();
												}
												$position = $key+1;
												$media = array(
														'media_type' => 'image',
														'label' => trim($product['title']),
														'disabled' => false,
														'types' => $types,
														'position' => $position,
														'content' => $fileContent,
												);
												$mediaProducts->product_id = $mg2ProductId;
												$mediaProducts->sku = $product['sku'];
												$mediaProducts->media = (object) array('entry' => $media);
												$resultMedia = $mediaProducts->catalogProductAttributeMediaCreate();
// 												echo "media";
// 												pre($resultMedia);
												
												if(!$resultMedia){
													$logError[$productsAP['id']][] = $resultMedia;
												}
											}
										}
// 									pre($images);
									$productsTempModel = new ProductsTempModel($db, null, $storeId);
									$productsTempModel->product_id = $mg2ProductId;
									$productsTempModel->sku = $product['sku'];
									$productsTempModel->title = $product['title'];
									$productsTempModel->color = $product['color'];
									$productsTempModel->variation = $product['variation'];
									$productsTempModel->brand = $mg2BrandId;
									$productsTempModel->reference = $product['reference'];
									$productsTempModel->category = $product['category'];
									$productsTempModel->qty = $qtd;
									$productsTempModel->price = $product['sale_price'];
									$productsTempModel->sale_price = $salePrice;
									$productsTempModel->promotion_price = $product['promotion_price'];
									$productsTempModel->cost = $product['cost'];
									$productsTempModel->weight = $weight;
									$productsTempModel->height = $product['height'];
									$productsTempModel->width = $product['width'];
									$productsTempModel->length = $product['length'];
									$productsTempModel->ean = $product['ean'];
									$productsTempModel->image = "/media/catalog/product/";
 									$productsTempModel->type_id = $productEntity->type_id;
									$productsTempModel->attribute_set_id = $productEntity->attribute_set_id;
									$productsTempModel->visibility = $productEntity->visibility;
									$productsTempModel->categories_ids = json_encode($mg2CategoriesIds);
									$productsTempModel->websites = json_encode($productEntity->websites);
									$productsTempModel->created_at = date('Y-m-d H:i:s');
									$productsTempModel->updated_at = date('Y-m-d H:i:s');
									$productsTempModel->status = $productEntity->status;
									$productsTempModel->Save();
									pre($productsTempModel);
									
									$publicationsModel->publication_code = $mg2ProductId;
									$publicationsModel->product_id = $product['id'];
									$publicationsModel->sku = $product['sku'];
									$publicationsModel->Save();
									
// 									echo "success|Produto cadastrado com sucesso!|{$product['sku']}";
									
									}
										
								}
									
							}else{
								$logError[$productsAP['id']][] = "SKU:{$product['sku']} já cadastrado \n";
							}
						}

					}//end simple products loop
					
					
					if(isset($parenSku)){
						
						$catalogProducts->parentSku = $parenSku;
						pre('Options');
						pre($configurables);
						foreach($configurables as $j => $configOption){
							$catalogProducts->option = $configOption;
							$result = $catalogProducts->catalogConfigurableProductsOptions();
							pre($result);
	
						}
						
						pre('child');
						pre($childSkus);
						foreach($childSkus as $k => $childSku){
							$catalogProducts->childSku = $childSku;
							$result = $catalogProducts->catalogConfigurableProductsChild();
							pre($result); 
						}
						
					}
					unset($parenSku);
					unset($configurables);
					unset($childSkus);
// 					pre($logError);
				}//end not empty parent id

			}//end loop products requested

			if(count($logError) > 0){
				pre($logError);
				echo $msgError = 'error|'.$msgError;
					
			}else{
				echo "success|{$mg2ProductId}";
			}

			break;
	}

}
