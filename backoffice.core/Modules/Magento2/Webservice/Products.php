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
		
		
		case "remove_products_mg2":
			 
			$productsModel = new ProductsModel($db, null, $storeId);
			
			if(isset($productId)){
				 
				$idProducts = is_array($productId) ? $productId : array($productId) ;
				 
				$updated = 0;
				foreach($idProducts as $i => $productId){
			
					$sql = "SELECT * FROM mg2_products_tmp WHERE store_id = {$storeId} AND  product_id = {$productId} ";
					$query = $db->query($sql);
					$products = $query->fetchAll(PDO::FETCH_ASSOC);
					
					foreach($products as $key => $product){
						
						$productsModel->sku = $product['sku'];
						
						$resDelete = $productsModel->catalogProductDelete();
						
						if(isset($resDelete['httpCode']) && $resDelete['httpCode'] == 200){
							$sql = "DELETE FROM mg2_products_tmp WHERE store_id = ? AND product_id = ? ";
							$query = $db->query($sql, array($storeId, $product['product_id']));
							
							$sql = "SELECT id FROM available_products WHERE store_id = ? AND sku LIKE ?";
							$query = $db->query($sql, array($storeId, $product['sku']));
							$productsAP = $query->fetch(PDO::FETCH_ASSOC);
							
							if(!empty($productsAP['id'])){
								 
								$sql = "DELETE FROM publications WHERE store_id = ? AND product_id = ? ";
								$query = $db->query($sql, array($storeId, $productsAP['id']));
								
								$db->insert('products_log', array(
										'store_id' => $storeId,
										'product_id' => $productsAP['id'],
										'description' => 'Produto Removido do Ecommerce',
										'user' => $request,
										'created' => date('Y-m-d H:i:s')
								));
								 
								if(!$query){
									 
									echo  "error|Erro ao excluir produto";
									 
								}else{
									$updated++;
								}
							
							}
						}
					}
				}
			}
			if($updated){
				echo "success|Produto removido com sucesso!";
			}
			 
			break;
		
		case "reset_import_data_magento2":
			
			$sql = "DELETE FROM `mg2_attributes_relationship` WHERE store_id = {$storeId}";
			$query = $db->query($sql);
			
			$sql = "DELETE FROM `mg2_attribute_set_relationship` WHERE store_id = {$storeId}";
			$db->query($sql);
			
			$sql = "DELETE FROM `mg2_set_attribute_relationships` WHERE store_id = {$storeId}";
			$db->query($sql);
			
			$sql = "DELETE FROM `mg2_categories_relationship` WHERE store_id = {$storeId}";
			$db->query($sql);
			
			$sql = "DELETE FROM `mg2_products_tmp` WHERE store_id = {$storeId}";
			$db->query($sql);
			
			$sql = "DELETE FROM publications WHERE store_id = {$storeId}";
			$db->query($sql);

			echo $sucess = "success|Atributos|Conjunto de Atributos|Relacionamento de Atributos|Categorias";
			
			$dataLog['reset_integration_data_magento2'] = array(
					'data' => $sucess
						
			);
			$db->insert('mg2_log', array(
					'store_id' => $storeId,
					'description' => 'Reset dos mapeamentos da integração',
					'user' => $request,
					'created' => date('Y-m-d H:i:s'),
					'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
			));
			
			break;
		
		case "create_product_magento":
			 
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
			 
			$associatedSkus = array();
			$msgError = '';
			$productIds = is_array($productId) ? $productId : array($productId) ;
			 
			foreach($productIds as $i => $id){
				 
				$queryAP = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$id}");
				$products = $queryAP->fetchAll(PDO::FETCH_ASSOC);
				$totalChild = count($products);
				 
				foreach($products as $ind => $product){
		
					if(isset($product['category'])){
						$sqlVerifyTmp = "SELECT product_id FROM module_onbi_products_tmp WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}'";
						$queryVerify = $db->query($sqlVerifyTmp);
						$productsTmp = $queryVerify->fetch(PDO::FETCH_ASSOC);
						//         	            pre($productsTmp);die;
						if(!isset($productsTmp['product_id'])){
							 
							$images = getPathImageFromSku($db, $storeId, $product['sku']);
								
							if(isset($images[0])){
									
								$categoryModel = new CategoryModel($db);
								$categoryModel->store_id = $product['store_id'];
								$categoryModel->hierarchy = $product['category'];
								$categorySetId = $categoryModel->GetSetAttributeFromCategory();
								$categoryIds = $categoryModel->GetCategoriesIds();
								$onbiCategoriesIds[] = '2'; //root category
								 
								$catalogCategories = new CategoriesModel($db, null, $storeId);
								 
								if(!isset($categoryIds[0])){
									continue;
								}
								 
								foreach($categoryIds as $key => $categoryId){
										
									$catalogCategories->category_id = $categoryId['id'];
									$catalogCategories->parent_id = $categoryId['parent_id'];
									$catalogCategory = $catalogCategories->GetCategoriesRelationship();
									$onbiCategoriesIds[] = isset($catalogCategory[0]['onbi_category_id']) ? $catalogCategory[0]['onbi_category_id'] : '';
								}
								 
								if(!empty($onbiCategoriesIds)){
									 
									$attributesValuesModel->product_id = $product['id'];
									$attributesValues = $attributesValuesModel->GetProductAttributesValues();
									 
									$queryAR = $db->query("SELECT * FROM onbi_attributes_relationship WHERE store_id = {$storeId}
									AND relationship LIKE 'color' OR relationship LIKE 'variation' OR relationship LIKE 'brand'");
									$attributesRel = $queryAR->fetchAll(PDO::FETCH_ASSOC);
									foreach($attributesRel as $key => $attributeRel){
		
										switch($attributeRel['relationship']){
											case "color": $name = $attributeRel['attribute']; $value = $product['color']; break;
											case "variation": $name = $attributeRel['attribute']; $value = $product['variation']; break;
											case "brand": $name = $attributeRel['attribute'];  $value = $product['brand']; break;
										}
		
										$attributesValues[] = array(
												"id" => $attributeRel['attribute_id'],
												"store_id" => $attributeRel['store_id'],
												"product_id" => '',
												"attribute_id" => $attributeRel['attribute_code'],
												"name" => $name,
												"value" => $value
										);
		
									}
		
									$configurableAttributes = array();
									$productEntity = new ProductEntity();
									$productEntity->setEntityFromAvailableProducts($product);
									$productEntity->short_description = $product['title'];
									$productEntity->categories = $onbiCategoriesIds;
									$productEntity->visibility = 4; //$totalChild > 1 ? 1 : 4 ;
									$weight = intval($product['weight']) > 10 ? $product['weight'] /1000 : $product['weight'];
									$productEntity->weight = $weight;
									 
									$salePriceModel->sku = $product['sku'];
									$salePriceModel->product_id = $product['id'];
									$salePrice = $salePriceModel->getSalePrice();
									$stockPriceRel = $salePriceModel->getStockPriceRelacional();
									$salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
									$productEntity->price = $salePrice;
									//                                    pre($salePrice);die;
									foreach($attributesValues as $key => $attributeVal){
										 
										$attributeCode  = str_replace("-", "_", $attributeVal['attribute_id']);
										 
										$sqlAttrRel = "SELECT * FROM onbi_attributes_relationship WHERE store_id = {$storeId} AND attribute_code LIKE '{$attributeCode}'";
										$queryAttrRel = $db->query($sqlAttrRel);
										$attrRel = $queryAttrRel->fetch(PDO::FETCH_ASSOC);
		
										if(isset($attrRel['is_configurable']) AND $attrRel['is_configurable']){
		
											$configurableAttributes[] = $attributeCode;
										}
										if($attrRel['frontend_input'] == 'select'){
											$optionSelected = array();
											$onbiBrandId = 0;
											$catalogAttributesModel->attribute_id = $attributeCode;
											$options = $catalogAttributesModel->catalogProductAttributeOptions();
											foreach($options as $i => $option){
												 
												if(strtolower($option->label) == strtolower($attributeVal['value'])){
													$optionSelected = array(
															'key' => $attributeCode,
															'value' => $option->value
													);
		
													if($attributeCode == 'manufacturer'){
														$onbiBrandId = $option->value;
													}
		
													continue;
												}
											}
											 
											if(isset($optionSelected['key'])){
												 
												$productEntity->additional_attributes['single_data'][] = $optionSelected;
		
											}else{
												$label = array(
														array(
																"store_id" =>  array('0'),
																"value" => $attributeVal['value']
														)
												);
												$catalogAttributesModel->attribute_option = array(
														"label" => $label,
														"order" => 0,
														"is_default" => 1
												);
												 
												$catalogAttributesModel->attribute_id = $attributeCode;
												$attributeValueId = $catalogAttributesModel->catalogProductAttributeAddOption();
												$productEntity->additional_attributes['single_data'][] = array(
														'key' => $attributeCode,
														'value' => $attributeValueId
												);
											}
											 
										}else{
											if(!empty($attributeVal['value'])){
												$productEntity->additional_attributes['single_data'][] = array(
														'key' => $attributeCode,
														'value' => $attributeVal['value']
												);
											}
										}
									}
									 
									$productEntity->additional_attributes['single_data'][] = array(
											'key' => 'volume_altura',
											'value' => ceil($product['height'])
											 
									);
									 
									$productEntity->additional_attributes['single_data'][] = array(
											'key' => 'volume_largura',
											'value' => ceil($product['width'])
											 
									);
									 
									$productEntity->additional_attributes['single_data'][] = array(
											'key' => 'volume_comprimento',
											'value' => ceil($product['length'])
											 
									);
									 
									$setAttributesRelationship->set_attribute_id = $categorySetId['set_attribute_id'];
									$setAttributeRel = $setAttributesRelationship->GetSetAttributeRelationship();
									$setAttributeRel = isset($setAttributeRel[0]) ? $setAttributeRel[0] : '' ;
									if(!isset($setAttributeRel['onbi_attribute_set_id'])){
										echo "error|Erro por favor relacione o conjunto de attributos.";
										continue;
									}
									$productEntity->set_attribute = $setAttributeRel['onbi_attribute_set_id'];
									$catalogProducts->set_id = $setAttributeRel['onbi_attribute_set_id'];
									$catalogProducts->sku = $product['sku'];
									$catalogAttributesModel->set_id = $setAttributeRel['onbi_attribute_set_id'];
								}
								$productEntity->stock_data =  array(
										'manage_stock' => 1,
										'qty' => $product['quantity'],
										'is_in_stock' => $product['quantity'] > 0 ? 1 : 0
		
								);
		
								$result = $catalogProducts->catalogProductCreate($productEntity);
								$onbiProductId = '';
								if(is_soap_fault($result)){
									$msgError .= "SKU:{$product['sku']} ".$result->faultstring." \n";
									productLog($db, $storeId, "Onbi","Webservice", $action, 'product', $product['id'], 'error', $result->faultstring, json_encode($result));
								}else{
									$onbiProductId = $result;
									$information = "Produto {$result} criado ".$productEntity->type;
									productLog($db, $storeId, "Onbi","Webservice", $action, 'product', $product['id'], 'success', $information, $result);
								}
								if(empty($onbiProductId)){
									$res = $catalogProducts->catalogProductInfoSku();
									$onbiProductId = isset($res->product_id) ? $res->product_id : $onbiProductId;
								}
								if(isset($onbiProductId)){
									 
									foreach($images as $key => $urlImage){
										 
										if(!empty($urlImage)){
											 
											$mediaProducts->file = array(
													'content' =>  base64_encode(file_get_contents($urlImage)),
													'mime' => image_type_to_mime_type(exif_imagetype($urlImage))
													 
											);
											$mediaProducts->product_id = $onbiProductId;
											$mediaProducts->label = $product['title'];
		
											if($key == 0){
												$mediaProducts->types =  array('image', 'small_image', 'thumbnail');
											}else{
												$mediaProducts->types =  array();
											}
											$mediaProducts->position = $key+1;
											 
											$resultMedia = $mediaProducts->catalogProductAttributeMediaCreate();
											 
											if(!$resultMedia){
												pre($resultMedia);
											}
										}
										 
									}
									$productsTempModel = new ProductsTempModel($db, null, $storeId);
									$productsTempModel->product_id = $onbiProductId;
									$productsTempModel->sku = $product['sku'];
									$productsTempModel->title = $product['title'];
									$productsTempModel->color = $product['color'];
									$productsTempModel->variation = $product['variation'];
									$productsTempModel->brand = $onbiBrandId;
									$productsTempModel->reference = $product['reference'];
									$productsTempModel->category = $onbiCategoriesIds;
									$productsTempModel->qty = $product['quantity'];
									$productsTempModel->price = $product['sale_price'];
									$productsTempModel->sale_price = $salePrice;
									$productsTempModel->promotion_price = $product['promotion_price'];
									$productsTempModel->cost = $product['cost'];
									$productsTempModel->weight = $weight;
									$productsTempModel->height = $product['height'];
									$productsTempModel->width = $product['width'];
									$productsTempModel->length = $product['length'];
									$productsTempModel->ean = $product['ean'];
									$productsTempModel->image = $moduleConfig['wsdl']."/media/catalog/product/".$resultMedia;
									$productsTempModel->description = $productEntity->description;
									$productsTempModel->type = $productEntity->type;
									$productsTempModel->set_attribute = $productEntity->set_attribute;
									$productsTempModel->visibility = $productEntity->visibility;
									$productsTempModel->categories_ids = json_encode($productEntity->category_ids);
									$productsTempModel->websites = json_encode($productEntity->websites);
									$productsTempModel->created_at = date('Y-m-d H:i:s');
									$productsTempModel->updated_at = date('Y-m-d H:i:s');
									$productsTempModel->status = $productEntity->status;
									pre($productsTempModel);
									$productsTempModel->Save();
									echo "success|Produto cadastrado com sucesso!|{$product['sku']}";
								}
							}
							 
						}else{
							$msgError .= "SKU:{$product['sku']} já cadastrado \n";
						}
					}
				}
			}
			 
			if(!empty($msgError)){
				 
				echo $msgError = 'error|'.$msgError;
				 
			}else{
				echo "success";
			}
		
			break;
		
		
		case "attribute_set_name":
				
			$catalogProducts = new ProductsModel($db, null, $storeId);
			$catalogProducts->filters[] = array('field' => 'attribute_set_name', 'value' => '', 'condition_type' => 'neq' );
			$attributes = $catalogProducts->getAttributes();
			pre($attributes);die;
				
			break;
			
			
		case "get_product_sku":
			
			if(!isset($sku)){
				return array();
			}
			$catalogProducts = new ProductsModel($db, null, $storeId);
			$catalogProducts->filters[] = array('field' => 'sku', 'value' => $sku, 'condition_type' => 'like' );
			$products = $catalogProducts->getProducts();
			pre($products);die;
			
		break;
	}
	
	
}





