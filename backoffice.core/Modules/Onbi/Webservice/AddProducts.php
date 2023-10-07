<?php
set_time_limit ( 3000 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");
// header("Access-Control-Allow-Origin: *");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../../../Models/Products/PublicationsModel.php';
require_once $path .'/../Class/class-Soap.php';
require_once $path .'/../Models/Catalog/ProductEntity.php';
require_once $path .'/../Models/Catalog/ProductsModel.php';
require_once $path .'/../Models/Catalog/MediaModel.php';
require_once $path .'/../Models/Catalog/CategoriesModel.php';
require_once $path .'/../Models/Catalog/InventoryModel.php';
require_once $path .'/../Models/Catalog/AttributesModel.php';
require_once $path .'/../Models/Catalog/AttributeSetModel.php';
require_once $path .'/../Models/Products/ProductsTempModel.php';
require_once $path .'/../Models/Products/SetAttributesRelationshipModel.php';
require_once $path .'/../Models/Products/AttributesValuesModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$idProduct = isset($_REQUEST["id_product"]) && $_REQUEST["id_product"] != "" ? $_REQUEST["id_product"] : null ;
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 5);
	switch($action){
	    
	        
	    case "create_product_magento":
// 	    	echo "error|Módulo em manutenção...";die;
	        require_once $path ."/../Models/Products/Templates/ItemDescriptionStoreId_{$storeId}.php";
	        
	        $setAttributesRelationship = new SetAttributesRelationshipModel($db, null, $storeId);
	        $catalogAttributesModel = new AttributesModel($db, null, $storeId);
	        $catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
	        $attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
	        $catalogProducts = new ProductsModel($db, null, $storeId);
	        $availableProducts = new AvailableProductsModel($db);
	        $mediaProducts = new MediaModel($db, null, $storeId);
	        $salePriceModel = new SalePriceModel($db, null, $storeId);
	        $salePriceModel->marketplace = "Ecommerce";
	        $publicationsModel = new PublicationsModel($db);
	        $publicationsModel->store_id = $storeId;
	        
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
                	            if(empty($categoryIds[0]['id'])){
                	            	echo $msgError = "error|Sem relacionamento de categoria...";
                	            	$log[$product['sku']][] = $msgError;
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
                                   
                                   $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
                                   $salePriceModel->sku = $product['sku'];
                                   $salePriceModel->product_id = $product['id'];
                                   $salePrice = $salePriceModel->getSalePrice();
                                   $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                                   $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
                                   $productEntity->price = $salePrice;
//                                    pre($salePrice);die;
                                   if ($product['blocked'] == "T"){
	                                   	$qtd = 0;
	                                   	echo $msgError = "error|Produto Bloqueado...";
	                                   	$log[$product['sku']][] = $msgError;
	                                   	continue;
                                   }
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
                                   		'key' => 'weight',
                                   		'value' => $weight
                                   );
                                   $productEntity->additional_attributes['single_data'][] = array(
                                   		'key' => 'volume_altura',
                                   		'value' => "{$product['height']}"
                                   );
                                   $productEntity->additional_attributes['single_data'][] = array(
                                   		'key' => 'volume_largura',
                                   		'value' => "{$product['width']}"
                                   );
                                   $productEntity->additional_attributes['single_data'][] = array(
                                   		'key' => 'volume_comprimento',
                                   		'value' => "{$product['length']}"
                                   );
                                   $productEntity->weight = $weight;
                                   $productEntity->volume_altura = $product['height'];
                                   $productEntity->volume_largura = $product['width'];
                                   $productEntity->volume_comprimento =$product['length'];
                                   $setAttributesRelationship->set_attribute_id = $categorySetId['set_attribute_id'];
                                   $setAttributeRel = $setAttributesRelationship->GetSetAttributeRelationship();
                                   $setAttributeRel = isset($setAttributeRel[0]) ? $setAttributeRel[0] : '' ;
                                   if(!isset($setAttributeRel['onbi_attribute_set_id'])){
                                       echo $msgError = "error|Relacione o conjunto de attributos.";
                                       $log[$product['sku']][] = $msgError;
                                       continue;
                                   }
                                   $productEntity->set_attribute = $setAttributeRel['onbi_attribute_set_id'];
                                   $catalogProducts->set_id = $setAttributeRel['onbi_attribute_set_id'];
                                   $catalogProducts->sku = $product['sku'];
                                   $catalogAttributesModel->set_id = $setAttributeRel['onbi_attribute_set_id'];
                    	       }
                    	       $productEntity->stock_data =  array(
                    	           'manage_stock' => 1,
                    	           'qty' => $qtd,
                    	           'is_in_stock' => $qtd > 0 ? 1 : 0 
                    	           
                    	       );
                    	       
                    	       pre($productEntity);
                               $result = $catalogProducts->catalogProductCreate($productEntity);
                               $onbiProductId = '';
                               if(is_soap_fault($result)){
                                  $msgError .= "SKU:{$product['sku']} ".$result->faultstring." \n";
                                  $log[$product['sku']][] = $result;
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
	                                       	$fileName = $product['title'];
	                                       	$fileNameSize = strlen($product['title']);
	                                       	 
	                                       	if($fileNameSize > 60){
	                                       		$fileName = substr($product['title'], ($fileNameSize - 59));
	                                       	}
                                           $mediaProducts->file = array(
                                               'content' =>  base64_encode(file_get_contents($urlImage)), 
                                               'mime' => image_type_to_mime_type(exif_imagetype($urlImage)),
                                           	   'name' => $fileName
                                               
                                           );
                                           $mediaProducts->product_id = $onbiProductId;
                                           $mediaProducts->label = $fileName;
                                          
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
                	               $productsTempModel->Save();
                	               
                	               $publicationsModel->publication_code = $onbiProductId;
                	               $publicationsModel->product_id = $product['id'];
                	               $publicationsModel->sku = $product['sku'];
                	               $publicationsModel->marketplace = 'Ecommerce';
                	               $publicationsModel->user = $request;
                	               $publicationsModel->Save();
                	               
                	               echo "success|Produto cadastrado com sucesso!|{$product['sku']}";
                	               
                	               
                               }
                	        }
                	        
            	        }else{
            	            $msgError .= "SKU:{$product['sku']} já cadastrado \n";
            	            $log[$product['sku']][] = $msgError;
            	        }
        	        }
	            }
	        }
	        
	        if(!empty($log[$product['sku']])){
	            
	            echo $msgError = 'error|'.json_encode($log, JSON_PRETTY_PRINT);
	            
	        }else{
	            echo "success";
	        }

	        break;
	        
	    case "create_product_relational_magento":
// 	    	echo "error|Módulo em manutenção...";die;
	        require_once $path ."/../Models/Products/Templates/ItemDescriptionStoreId_{$storeId}.php";
	        
	        $setAttributesRelationship = new SetAttributesRelationshipModel($db, null, $storeId);
	        $catalogAttributesModel = new AttributesModel($db, null, $storeId);
	        $catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
	        $attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
	        $catalogProducts = new ProductsModel($db, null, $storeId);
	        $availableProducts = new AvailableProductsModel($db);
	        $mediaProducts = new MediaModel($db, null, $storeId);
	        
	        $msgError = '';
	        $idProducts = is_array($idProduct) ? $idProduct : array($idProduct) ;
	        foreach($idProducts as $i => $id){
	            
	            $queryAP = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$id}");
	            $products = $queryAP->fetchAll(PDO::FETCH_ASSOC);

	            foreach($products as $ind => $product){
	                
	                if(isset($product['category'])){
	                    
	                    $parentSku = trim($product['parent_id'])."-x";
	                    
	                    $queryVerify = $db->query("SELECT product_id FROM module_onbi_products_tmp WHERE store_id = {$storeId} AND
                        sku LIKE '{$parentSku}' AND type LIKE 'configurable'");
	                    $productsTmp = $queryVerify->fetch(PDO::FETCH_ASSOC);
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
	                            if(empty($categoryIds[0]['id'])){
	                            	$msgError = "error|Sem relacionamento de categoria...";
	                            	$log[$product['sku']][] = $msgError;
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
	                                
	                                $sqlAR = "SELECT * FROM onbi_attributes_relationship WHERE store_id = {$storeId}
                                   AND relationship LIKE 'color' OR relationship LIKE 'variation'  OR relationship LIKE 'brand'";
	                                $queryAR = $db->query($sqlAR);
	                                $attributesRel = $queryAR->fetchAll(PDO::FETCH_ASSOC);
	                                
	                                foreach($attributesRel as $key => $attributeRel){
	                                    
	                                    switch($attributeRel['relationship']){
	                                        case "color": $name = $attributeRel['attribute']; $value = $product['color']; break;
	                                        case "variation": $name = $attributeRel['attribute']; $value = $product['variation']; break;
	                                        case "brand": $name = $attributeRel['attribute']; $value = $product['brand']; break;
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
                                
	                                
	                                $associatedSkus = array();
	                                $configurableAttributes = array();
	                                
	                                $productEntity = new ProductEntity();
	                                $productEntity->setEntityFromAvailableProducts($product);
	                                $productEntity->short_description = $product['title'];
	                                $productEntity->categories = $onbiCategoriesIds;
	                                $productEntity->visibility = 4; //$totalChild > 1 ? 1 : 4 ;
	                                $weight = intval($product['weight']) > 10 ? $product['weight'] /1000 : $product['weight'];
	                                $productEntity->weight = $weight;
	                                $productEntity->volume_largura = ceil($product['width']);
	                                $productEntity->volume_altura = ceil($product['height']);
	                                $productEntity->volume_comprimento = ceil($product['length']);
	                                
	                                
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
	                                		'key' => 'weight',
	                                		'value' => $weight
	                                );
	                                $productEntity->additional_attributes['single_data'][] = array(
	                                		'key' => 'volume_altura',
	                                		'value' => "{$product['height']}"
	                                );
	                                $productEntity->additional_attributes['single_data'][] = array(
	                                		'key' => 'volume_largura',
	                                		'value' => "{$product['width']}"
	                                );
	                                $productEntity->additional_attributes['single_data'][] = array(
	                                		'key' => 'volume_comprimento',
	                                		'value' => "{$product['length']}"
	                                );
	                                $productEntity->weight = $weight;
	                                $productEntity->volume_altura = $product['height'];
	                                $productEntity->volume_largura = $product['width'];
	                                $productEntity->volume_comprimento =$product['length'];
	                                $setAttributesRelationship->set_attribute_id = $categorySetId['set_attribute_id'];
	                                $setAttributeRel = $setAttributesRelationship->GetSetAttributeRelationship();
	                                $setAttributeRel = isset($setAttributeRel[0]) ? $setAttributeRel[0] : '' ;
	                                
	                                if(!isset($setAttributeRel['onbi_attribute_set_id'])){
	                                    echo $msgError =  "error|Erro por favor relacione o conjunto de attributos.";
	                                    $log[$product['sku']][] = $msgError;
	                                    continue;
	                                }
	                                
	                                $productEntity->set_attribute = $setAttributeRel['onbi_attribute_set_id'];
	                                
	                                $catalogProducts->set_id = $setAttributeRel['onbi_attribute_set_id'];
	                                
	                                $catalogProducts->sku = $parentSku;
	                                
	                                $catalogAttributesModel->set_id = $setAttributeRel['onbi_attribute_set_id'];
	                                
	                            }
            	           
            	            
                	            
                	            $productEntity->sku = $parentSku;
                	            $productEntity->has_options = 1;
                	            $productEntity->type = 'configurable';
                	            $productEntity->stock_data = array(
                	                'use_config_manage_stock' => 1,
                	                'manage_stock' => 1,
                	                'qty' => $product['quantity'],
                	                'is_in_stock' =>  $product['quantity'] > 0 ? 1 : 0 
                	            );
                	            
                	            $catalogProducts->type = $productEntity->type;
                	            $catalogProducts->sku = $productEntity->sku;
//                 	            pre($productEntity);die;
                	            $result = $catalogProducts->catalogProductCreate($productEntity);
                	           
                	            
                	            if(is_soap_fault($result)){
                                   echo "error|".$result->faultstring;
                                   $msgError .= "SKU:{$parentSku} ".$result->faultstring." \n";
                	                pre($result);
                	            }else{
                	                $onbiProductConfigurableId = $result;
                	                $information = "Produto {$result} criado ".$productEntity->type;
                	            }
                	            
                	            if(isset($onbiProductConfigurableId)){
                	                
//                 	                foreach($images as $key => $urlImage){
                	                    
//                 	                    $fileContent = base64_encode(file_get_contents($urlImage));
//                 	                    $info = getimagesize($urlImage);
                	                    
//                 	                    $mediaProducts->file = array(
//                 	                        'content' => $fileContent,
//                 	                        'mime' => $info['mime'],
//                 	                        'name' => $product['title']
                	                        
//                 	                    );
//                 	                    $mediaProducts->product_id = $onbiProductConfigurableId;
//                 	                    $mediaProducts->label = $product['title'];
//                 	                    $mediaProducts->position = $key;
//                 	                    $resultMedia = $mediaProducts->catalogProductAttributeMediaCreate();
                	                    
//                 	                }
                	                
                	                foreach($images as $key => $urlImage){
                	                    if(!empty($urlImage)){
                	                        $fileContent = base64_encode(file_get_contents($urlImage));
                	                        $info = getimagesize($urlImage);
                	                        
                	                        $fileName = $product['title'];
                	                        $fileNameSize = strlen($product['title']);
                	                        if($fileNameSize > 60){
                	                        	$fileName = substr($product['title'], ($fileNameSize - 59));
                	                        }
                	                        
                	                        $mediaProducts->file = array(
                	                            'content' => $fileContent,
                	                            'mime' => $info['mime'],
                	                            'name' => $fileName
                	                            
                	                        );
                	                        
                	                        $mediaProducts->product_id = $onbiProductConfigurableId;
                	                        $mediaProducts->label = $fileName;
                	                        $mediaProducts->position = $key+1;
                	                        
                	                        if($key == 0){
                	                            $mediaProducts->types =  array('image', 'small_image', 'thumbnail');
                	                        }else{
                	                            $mediaProducts->types =  array();
                	                        }
                	                        $resultMedia = $mediaProducts->catalogProductAttributeMediaCreate();
                	                    }
                	                    
                	                }
                	                
                	                
                	                $productsTempModel = new ProductsTempModel($db, null, $storeId);
                	                $productsTempModel->product_id = $onbiProductConfigurableId;
                	                $productsTempModel->sku = $parentSku;
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
                	                $productsTempModel->Save();
                	                
                	                $publicationsModel->publication_code = $onbiProductConfigurableId;
                	                $publicationsModel->product_id = $product['id'];
                	                $publicationsModel->sku = $parentSku;
                	                $publicationsModel->marketplace = 'Ecommerce';
                	                $publicationsModel->user = $request;
                	                
                	                $publicationsModel->Save();
                	                echo "success|Produto cadastrado com sucesso!|{$parentSku}";
                	            }
            	            
	                        }
	                    }
	                }
	            }
	        }
	        break;
	        
        case "update_product_magento":
        	echo "error|Módulo em manutenção...";die;
        	require_once $path ."/../Models/Products/Templates/ItemDescriptionStoreId_{$storeId}.php";
        	$syncId =  logSyncStart($db, $storeId, "Magento", $action, "Atualizaçnao de produtos", $request);
        	$setAttributesRelationship = new SetAttributesRelationshipModel($db, null, $storeId);
        	$catalogAttributesModel = new AttributesModel($db, null, $storeId);
        	$catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
        	$attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
        	$catalogProducts = new ProductsModel($db, null, $storeId);
        	$availableProducts = new AvailableProductsModel($db);
        	$salePriceModel = new SalePriceModel($db, null, $storeId);
        	$salePriceModel->marketplace = "Ecommerce";
        	$updated = 0;
        	$associatedSkus = array();
        	$msgError = '';
        	$productIds = is_array($productId) ? $productId : array($productId) ;
        	
        	if(empty($productIds[0])){
        		$productIds = array();
        		$sqlTmp1 = "SELECT sku FROM module_onbi_products_tmp WHERE store_id = {$storeId} 
        		AND type != 'configurable' AND flag='F' LIMIT 10";
        		$queryTmp1 = $db->query($sqlTmp1);
        		$productsTmpAll1 = $queryTmp1->fetchAll(PDO::FETCH_ASSOC);
        		foreach($productsTmpAll1 as $j => $tmp1){
	        		$sqlAp = "SELECT id FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$tmp1['sku']}'";
	        		$queryAp = $db->query($sqlAp);
	        		$res = $queryAp->fetch(PDO::FETCH_ASSOC);
	        		$productIds[] = $res['id'];
        		}
        	}
        	foreach($productIds as $i => $id){
        		 
        		$sqlParent = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$id}";
        		$queryParent = $db->query($sqlParent);
        		$product = $queryParent->fetch(PDO::FETCH_ASSOC);
        
        
        			$sqlTmp = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}' ORDER BY product_id DESC LIMIT 1";
        			$queryTmp = $db->query($sqlTmp);
        			$productsTmpAll = $queryTmp->fetchAll(PDO::FETCH_ASSOC);
        			
        			foreach($productsTmpAll as $i => $productTmp){
        			
	        			if(isset($productTmp['product_id'])){
	        	        	
	        				$categoryModel = new CategoryModel($db);
	        				$categoryModel->store_id = $product['store_id'];
	        				$categoryModel->hierarchy = $product['category'];
	        				$categorySetId = $categoryModel->GetSetAttributeFromCategory();
	        				$attributesValuesModel->product_id = $product['id'];
	        				$attributesValues = $attributesValuesModel->GetProductAttributesValues();
	        	        	
	        				$sqlAR = "SELECT * FROM onbi_attributes_relationship WHERE store_id = {$storeId} AND relationship LIKE 'color' OR relationship LIKE 'variation' OR relationship LIKE 'brand'";
	        				$queryAR = $db->query($sqlAR);
	        				$attributesRel = $queryAR->fetchAll(PDO::FETCH_ASSOC);
	        				
	        	        	foreach($attributesRel as $key => $attributeRel){
	        	        		
	        	        		switch($attributeRel['relationship']){
	        						case "color": $name = $attributeRel['attribute']; $value = $product['color']; break;
	        						case "variation": $name = $attributeRel['attribute']; $value = strtoupper(trim($product['variation'])); break;
	        						case "brand": $name = $attributeRel['attribute']; $value = $product['brand']; break;
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
	        				$productEntity->product_id = $productTmp['product_id'];
	        				//$productEntity->visibility = 4; //$totalChild > 1 ? 1 : 4 ;
	        				$weight = intval($product['weight']) > 10 ? $product['weight'] /1000 : $product['weight'];
	        				$productEntity->weight = $weight;
	        				unset($productEntity->categories);
	        				
	        				$qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
	        	        	$salePriceModel->sku = $product['sku'];
	        				$salePriceModel->product_id = $product['id'];
	        				$salePrice = $salePriceModel->getSalePrice();
	        				$stockPriceRel = $salePriceModel->getStockPriceRelacional();
	        				$salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
	        				$productEntity->price = $salePrice;
	        				if ($product['blocked'] == "T"){
	        					$qtd = 0;
	        					echo $msgError = "error|Produto Bloqueado...";
	        					$log[$product['sku']][] = $msgError;
	        					continue;
	        				}
	        
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
	        										'value' => $option->value);
	        								if($attributeCode == 'manufacturer'){
	        									$onbiBrandId = $option->value;
	        								}
	        								continue;
	        							}
	        						}
        	        				if(isset($optionSelected['key'])){
        	        					$productEntity->additional_attributes['single_data'][] = $optionSelected;
        	        				}else{
	        							$label = array(array(
        										"store_id" =>  array('0'),
        										"value" => $attributeVal['value'])
	        							);
	        							$catalogAttributesModel->attribute_option = array(
	        									"label" => $label,
	        									"order" => 0,
	        									"is_default" => 1);
	        							$catalogAttributesModel->attribute_id = $attributeCode;
	        							$attributeValueId = $catalogAttributesModel->catalogProductAttributeAddOption();
	        							$productEntity->additional_attributes['single_data'][] = array(
	        									'key' => $attributeCode,
	        									'value' => $attributeValueId );
	        						}
	        						
	        	        		}else{
	        	        				
	        						if(!empty($attributeVal['value'])){
	        							$productEntity->additional_attributes['single_data'][] = array(
	        									'key' => $attributeCode,
	        									'value' => $attributeVal['value']);
	        						}
	        					}
	        				}
	        				
	        					$productEntity->additional_attributes['single_data'][] = array(
	        						'key' => 'weight',
	        						'value' => $weight
	        				);
	        					$productEntity->additional_attributes['single_data'][] = array(
	        						'key' => 'volume_altura',
	        						'value' => "{$product['height']}"
	        				);
	        	        		$productEntity->additional_attributes['single_data'][] = array(
	        						'key' => 'volume_largura',
	        						'value' => "{$product['width']}"
	        				);
	        	        		$productEntity->additional_attributes['single_data'][] = array(
	        						'key' => 'volume_comprimento',
	        						'value' => "{$product['length']}"
	        				);
        	        		$productEntity->weight = $weight;
        	        		$productEntity->volume_altura = $product['height'];
        	        		$productEntity->volume_largura = $product['width'];
        	        		$productEntity->volume_comprimento =$product['length'];
	        	        		
	        	        	$setAttributesRelationship->set_attribute_id = $categorySetId['set_attribute_id'];
	        				$setAttributeRel = $setAttributesRelationship->GetSetAttributeRelationship();
	        				$setAttributeRel = isset($setAttributeRel[0]) ? $setAttributeRel[0] : '' ;
	        	        	
	        	        	if(!isset($setAttributeRel['onbi_attribute_set_id'])){
	        					echo $msgError = "error|Erro por favor relacione o conjunto de attributos.";
	        					$log[$product['sku']][] = $msgError;
	        					continue;
	        				}
	        	        	$productEntity->set_attribute = $setAttributeRel['onbi_attribute_set_id'];
	        				$catalogProducts->set_id = $setAttributeRel['onbi_attribute_set_id'];
	        				$catalogProducts->sku = $product['sku'];
	        				$catalogProducts->product_id = $productTmp['product_id'];
	        				$catalogProducts->storeView = null;
	        				$catalogAttributesModel->set_id = $setAttributeRel['onbi_attribute_set_id'];
	        				 
	        				$productEntity->stock_data =  array(
	        						'manage_stock' => 1,
	        						'qty' => $qtd,
	        						'is_in_stock' => $qtd > 0 ? 1 : 0
	        				);
	        				
	        				pre($productEntity);
// 	        				pre($catalogProducts);
	        				$result = $catalogProducts->catalogProductUpdate($productEntity);
	        					
// 	        				pre($result);
	        				
	        				if(is_soap_fault($result)){
	        					 
	        					echo "error|".$result->faultstring;
	        					$msgError .= "SKU:{$parentSku} ".$result->faultstring." \n";
	        					$error = json_encode($result);
	        					$db->update('module_onbi_products_tmp',
	        							array('store_id', 'product_id'),
	        							array($storeId, $productTmp['product_id']),
	        							array('updated_at' => date('Y-m-d H:i:s'), 'error' => $error)
	        							);
	        				}else{
	        				 
		       					$productsTempModel = new ProductsTempModel($db, null, $storeId);
		       					$productsTempModel->product_id =  $productTmp['product_id'];
		       					$productsTempModel->sku = $product['sku'];
		       					$productsTempModel->title = $product['title'];
		       					$productsTempModel->color = $product['color'];
		       					$productsTempModel->variation = $product['variation'];
		       					$productsTempModel->brand = $onbiBrandId;
		       					$productsTempModel->reference = $product['reference'];
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
		       					$productsTempModel->description = $productEntity->description;
		       					$productsTempModel->type = $productEntity->type;
		       					$productsTempModel->set_attribute = $productEntity->set_attribute;
		       					$productsTempModel->visibility = $productEntity->visibility;
		       					$productsTempModel->categories_ids = json_encode($productEntity->category_ids);
		       					$productsTempModel->websites = json_encode($productEntity->websites);
		       					$productsTempModel->updated_at = date('Y-m-d H:i:s');
		       					$productsTempModel->status = $productEntity->status;
		       					$productsTempModel->Save();
		       					echo "success|Produto atualizado com sucesso!|{$product['sku']}";
		       					$updated++;
	        				}
	        					
	        			}
        			}
        		}
        	logSyncEnd($db, $syncId, $updated);
        	break;
        	
        case "update_product_relational_magento":
        	echo "error|Módulo em manutenção...";die;
        	require_once $path ."/../Models/Products/Templates/ItemDescriptionStoreId_{$storeId}.php";
        	 
        	$setAttributesRelationship = new SetAttributesRelationshipModel($db, null, $storeId);
        	$catalogAttributesModel = new AttributesModel($db, null, $storeId);
        	$catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
        	$attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
        	$catalogProducts = new ProductsModel($db, null, $storeId);
        	$availableProducts = new AvailableProductsModel($db);
        	$mediaProducts = new MediaModel($db, null, $storeId);
        	 
        	$associatedSkus = array();
        	$msgError = '';
        	
        	$productIds = is_array($productId) ? $productId : array($productId) ;
        	foreach($productIds as $i => $id){
        		 
        		$queryAP = $db->query("SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$id}");
        		$products = $queryAP->fetchAll(PDO::FETCH_ASSOC);
        
        		foreach($products as $ind => $product){
        			 
        			if(isset($product['category'])){
        				 
        				$parentSku = trim($product['parent_id'])."-x";
        				$sqlTmp = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId} AND
        				sku LIKE '{$parentSku}' AND type LIKE 'configurable' ORDER BY product_id DESC";
        				$queryVerify = $db->query($sqlTmp);
        				$productsTmpAll = $queryVerify->fetchAll(PDO::FETCH_ASSOC);
        				
        				foreach($productsTmpAll as $i => $productsTmp){
        					
	        				if(isset($productsTmp['product_id'])){
	        					
	        						$categoryModel = new CategoryModel($db);
	        						$categoryModel->store_id = $product['store_id'];
	        						$categoryModel->hierarchy = $product['category'];
	        						$categorySetId = $categoryModel->GetSetAttributeFromCategory();
	       							$attributesValuesModel->product_id = $product['id'];
	       							$attributesValues = $attributesValuesModel->GetProductAttributesValues();
	       							
	       							 
	       							$sqlAR = "SELECT * FROM onbi_attributes_relationship WHERE store_id = {$storeId}
	       							AND relationship LIKE 'color' OR relationship LIKE 'variation'  OR relationship LIKE 'brand'";
	       							$queryAR = $db->query($sqlAR);
	       							$attributesRel = $queryAR->fetchAll(PDO::FETCH_ASSOC);
	       							 
	       							foreach($attributesRel as $key => $attributeRel){
	       								 
	       								switch($attributeRel['relationship']){
	       									case "color": $name = $attributeRel['attribute']; $value = $product['color']; break;
	       									case "variation": $name = $attributeRel['attribute']; $value = $product['variation']; break;
	       									case "brand": $name = $attributeRel['attribute']; $value = $product['brand']; break;
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
	       							$associatedSkus = array();
	       							$configurableAttributes = array();
	       							 
	       							$productEntity = new ProductEntity();
	       							$productEntity->setEntityFromAvailableProducts($product);
	       							$productEntity->short_description = $product['title'];
	       							$productEntity->visibility = 4; //$totalChild > 1 ? 1 : 4 ;
	       							$weight = intval($product['weight']) > 10 ? $product['weight'] /1000 : $product['weight'];
	       							$productEntity->weight = $weight;
	       							$productEntity->volume_altura = ceil($product['height']);
	       							$productEntity->volume_largura = ceil($product['width']);
	       							$productEntity->volume_comprimento = ceil($product['length']);
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
	       										$label = array(array(
	       														"store_id" =>  array('0'),
	       														"value" => $attributeVal['value']
	       												));
	       										$catalogAttributesModel->attribute_option = array(
	       												"label" => $label,
	       												"order" => 0,
	       												"is_default" => 1);
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
	        								'key' => 'weight',
	        								'value' => $weight
	        						);
	        						$productEntity->additional_attributes['single_data'][] = array(
	        								'key' => 'volume_altura',
	        								'value' => "{$product['height']}"
	        						);
	        						$productEntity->additional_attributes['single_data'][] = array(
	        								'key' => 'volume_largura',
	        								'value' => "{$product['width']}"
	        						);
	        						$productEntity->additional_attributes['single_data'][] = array(
	        								'key' => 'volume_comprimento',
	        								'value' => "{$product['length']}"
	        						);
	        						$productEntity->weight = $weight;
	        						$productEntity->volume_altura = $product['height'];
	        						$productEntity->volume_largura = $product['width'];
	        						$productEntity->volume_comprimento =$product['length'];
	        						$setAttributesRelationship->set_attribute_id = $categorySetId['set_attribute_id'];
	        						$setAttributeRel = $setAttributesRelationship->GetSetAttributeRelationship();
	        						$setAttributeRel = isset($setAttributeRel[0]) ? $setAttributeRel[0] : '' ;
	        							 
	        						if(!isset($setAttributeRel['onbi_attribute_set_id'])){
	        							echo $msgError = "error|Erro por favor relacione o conjunto de attributos.";
	        							$log[$product['sku']][] = $msgError;
	        							continue;
	        						}
	        							 
	        						$productEntity->set_attribute = $setAttributeRel['onbi_attribute_set_id'];
	        						$catalogProducts->set_id = $setAttributeRel['onbi_attribute_set_id'];
	        						$catalogProducts->sku = $parentSku;
	        						$catalogAttributesModel->set_id = $setAttributeRel['onbi_attribute_set_id'];
	        
	        						 
	        						$productEntity->sku = $parentSku;
	        						$productEntity->has_options = 1;
	        						$productEntity->type = 'configurable';
	        						$productEntity->stock_data = array(
	        								'use_config_manage_stock' => 1,
	        								'manage_stock' => 1,
	        								'qty' => $product['quantity'],
	        								'is_in_stock' =>  $product['quantity'] > 0 ? 1 : 0
	        						);
	        						 
	        						$catalogProducts->type = $productEntity->type;
	        						$catalogProducts->sku = $parentSku;
	        						$catalogProducts->product_id = $productsTmp['product_id'];
	        						
	        						$result = $catalogProducts->catalogProductUpdate($productEntity);
	        						
	        						if(is_soap_fault($result)){
	        							echo "error|".$result->faultstring;
	        							$msgError .= "SKU:{$parentSku} ".$result->faultstring." \n";
	        							$error = json_encode($result);
	        							$log[$product['sku']][] = array($msgError, $error);
	        							$db->update('module_onbi_products_tmp', 
	        									array('store_id', 'product_id'),
	        									array($storeId, $productsTmp['product_id']), 
	        									array('updated_at' => date('Y-m-d H:i:s'), 'error' => $error)
	        									);
	        						}else{
	        							pre($result);
	        							$information = "Produto {$result} atualizado ".$productEntity->type;
		        						$productsTempModel = new ProductsTempModel($db, null, $storeId);
		        						$productsTempModel->product_id = $productsTmp['product_id'];
		        						$productsTempModel->sku = $parentSku;
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
		        						$productsTempModel->description = $productEntity->description;
		        						$productsTempModel->type = $productEntity->type;
		        						$productsTempModel->set_attribute = $productEntity->set_attribute;
		        						$productsTempModel->visibility = $productEntity->visibility;
		        						$productsTempModel->categories_ids = json_encode($productEntity->category_ids);
		        						$productsTempModel->websites = json_encode($productEntity->websites);
		        						$productsTempModel->updated_at = date('Y-m-d H:i:s');
		        						$productsTempModel->status = $productEntity->status;
		        						$productsTempModel->Save();
		        						echo "success|Produto atualizado com sucesso!|{$parentSku}";
	        						}
	        							
	        				}
	        			}
        			}
        		}
        	}
        	break;

	}
	    
	
}

