<?php

set_time_limit ( 300 );

$path = dirname(__FILE__);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/ProductDescriptionModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../Class/class-Tray.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Api/CategoriesRestModel.php';
require_once $path .'/../Models/Api/CaracteristicasRestModel.php';

require_once $path .'/../Models/Api/ItemsRestModel.php';
// require_once $path .'/../Models/Adverts/ItemsModel.php';
// require_once $path .'/../Models/Map/MlCategoryModel.php';
// require_once $path .'/../Models/Price/PriceModel.php';
require_once $path .'/functions.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$type = isset($_REQUEST["type"]) && $_REQUEST["type"] != "" ? $_REQUEST["type"] : 'single' ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;

$request = "Manual";
if (empty ( $action ) and empty ( $storeId )) {
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
    	
    	case 'update_product_image':
    		
    		$itemsRestModel = new ItemsRestModel($db, null, $storeId);
    		
    		$idProduct = $_REQUEST['id_product'];
    		
    		if(isset($idProduct)){
    			
    			$idProducts = is_array($idProduct) ? $idProduct : array($idProduct) ;
    		
	    		foreach($idProducts as $k => $id){
	    			
	    			$itemsRestModel->id_product = $id;
	    			$res = $itemsRestModel->putImageProduct();
	    			if(isset($res['httpCode']) && $res['httpCode'] == 200){
	    				echo "success|Imagens Atualizadas com sucesso!";
	    			}else{
	    				echo "error|";
	    				pre($res);
	    			}
	    			
	    		}
    		}
    		break;
        
        
        
        case "update_product":
            
            $itemsRestModel = new ItemsRestModel($db, null, $storeId);
            $salePriceModel = new SalePriceModel($db, null, $storeId);
            $salePriceModel->marketplace = 'Tray'; 
            if($storeId == 6){
            	$salePriceModel->priceType = 'price';
            }
            $productDescription = new ProductDescriptionModel($db);
            $productDescription->store_id = $storeId;
            
            $caracteristicasRestModel = new CaracteristicasRestModel($db, null, $storeId);
            $limit = 50;
            $offset = 0;
            $currentPage = 1;
            $caracteristicas = array();
            do{
            	$caracteristicasRestModel->dataFilter = array( 'sort' => 'id_desc', "page"=> $currentPage, "limit" => $limit, 'offset' => $offset);
            	$res = $caracteristicasRestModel->getCaracteristica();
            	foreach($res['body']->Properties as $key => $value){
            		$caracteristicas[] = array("id" => $value->Property->id, "name" => $value->Property->name, "alias" => titleFriendly($value->Property->name ));
            	}
            	$offset += $limit;
            	$currentPage++;
            
            } while($res['body']->paging->total > $offset);
            
            if(isset($productId)){
                $productIds = is_array($productId) ? $productId : array($productId) ;
            }
            
            if(isset($_REQUEST['parent_id']) AND !empty($_REQUEST['parent_id'])){
                $parentId = $_REQUEST['parent_id'];
                $sql = "SELECT id, parent_id FROM available_products WHERE store_id = {$storeId} AND parent_id  = '{$parentId}' group by parent_id";
            }
            foreach($productIds as $i => $id){
                
                $sql = "SELECT title, reference, color, parent_id, reference, brand, collection FROM available_products WHERE store_id = {$storeId} AND id = {$id} ";
                $query = $db->query($sql);
                $rowParentId = $query->fetch(PDO::FETCH_ASSOC);
                
                if(isset($rowParentId['parent_id'])){
                    
                    $sqlVerify = "SELECT product_id, id_product FROM module_tray_products WHERE store_id = {$storeId} AND parent_id LIKE '{$rowParentId['parent_id']}'";
                    $queryVerify = $db->query($sqlVerify);
                    $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($verify['id_product'])){
                        
                        $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$verify['product_id']} ORDER BY id ASC";
                        $query = $db->query($sql);
                        $product = $query->fetch(PDO::FETCH_ASSOC);
                        
                        $attributesValues = getAttributesValuesFromParentId($db, $storeId, $product['parent_id']);
                        
                        $properties = array();
                        foreach($attributesValues as $j => $val ){
                            foreach($caracteristicas as $k => $caracteristica){
                                if($caracteristica['alias'] == $val['alias']){
                                    if(!in_array(array("property_id" => $caracteristica['id'], "value" => $val['value']), $properties)){
                                        $properties[] =  array("property_id" => $caracteristica['id'], "value" => $val['value']);
                                    }
                                }
                            }
                        }
                        
                        foreach($properties as $k => $props){
                            if($props['property_id'] == '17' OR $props['property_id'] == '25'){
                                $parts = explode(',', $props['value']);
                                $properties[$k]['value'] = $parts[0];
                            }
                        }
                        $color = $product['color'];
                        $images = getUrlImageFromParentId($db, $storeId, $product['parent_id']);
                        $available = !empty($images[0]) ? 1 : 0 ;
                        
                        $data = array();
                        $countImage = 1;
                        sort($images);
                        foreach($images as $key => $image){
                            $data["Product"]["picture_source_{$countImage}"] = $image;
                            $countImage++;
                        }
                        
                        $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
                        $salePriceModel->sku = $product['sku'];
                        $salePriceModel->product_id = $product['id'];
                        $salePrice = $salePriceModel->getSalePrice();
                        $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                        $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'];
                        $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                        if ($product['blocked'] == "T"){
                        	$qtd = 0;
//                         	echo "error|Produto Bloqueado...";
                        }
                        
                        $productDescription->parent_id = $product['parent_id'];
                        $description = $productDescription->GetParentProductDescription();
                        
                        $ean = trim($product['ean']);
                        $title = mb_strtoupper(trim($product['title']), 'UTF-8');
                        $brand = mb_strtoupper(trim($product['brand']), 'UTF-8');
                        
                        if(isset($product['ean']) && !empty($product['ean'])){
                            
                            $data["Product"]["ean"] = $product['ean'];
                            
                        }
                        $data["Product"]["name"] = $title;
                        $data["Product"]["description"] = $product['description'];
                        $data["Product"]["description_small"] = ucwords(strtolower($product['title']));
                        $data["Product"]["price"] = $salePrice;
                        $data["Product"]["brand"] = $brand;
                        
                        
                        
                        $weight = isset($product['weight']) ? (float)  $product['weight'] : (float)  1200;
                        if($weight < 100){
                            $weight = $weight * 1000;
                        }
                        $data["Product"]["weight"] = $weight;
                        $data["Product"]["stock"] = $qtd;
                        $data["Product"]["warranty"] = "Garantia de 1 ano mediante análise de fábrica*";
                        $data["Product"]["warranty_days"] = '90';
                        
                        $sqlCategory = "SELECT * FROM module_tray_categories WHERE store_id = {$storeId}
                                AND hierarchy LIKE '{$product['category']}'";
                        $queryCategory = $db->query($sqlCategory);
                        $category = $queryCategory->fetch(PDO::FETCH_ASSOC);
                        $data["Product"]["category_id"] = $category['id_category'];
                        
                        $categoryParts = explode(">", $product['category']);
                        if(isset($categoryParts[0])){
                            $sqlCategory = "SELECT * FROM module_tray_categories WHERE store_id = $storeId
                                    AND hierarchy LIKE '".trim($categoryParts[0])."'";
                            $queryCategory = $db->query($sqlCategory);
                            $category = $queryCategory->fetch(PDO::FETCH_ASSOC);
                            $data["Product"]["related_categories"] = $category['id_category'];
                        }
                        
                        $colorReplace = str_replace("-", ' ', $color);
                        $colorReplace = str_replace("  ", ' ', $colorReplace);
                        $colorReplace = str_replace("   ", ' ', $colorReplace);
                        $colorReplace = trim($colorReplace);
                        $model = $product['title'];
                        if($storeId == '3'){
	                        $model = str_replace($colorReplace, "", mb_strtoupper($model, 'UTF-8'));
	                        $model = str_replace(trim(strtoupper($product['reference'])), "", mb_strtoupper($model, 'UTF-8'));
	                        $model = str_replace(mb_strtoupper(trim(end($categoryParts)), 'UTF-8'), "", mb_strtoupper($model, 'UTF-8'));
	                        $model = str_replace("MASCULINA", "", mb_strtoupper($model, 'UTF-8'));
	                        $model = str_replace("COURO", "", mb_strtoupper($model, 'UTF-8'));
	                        //                         $model = str_replace(trim($product['brand']), "", mb_strtoupper($model, 'UTF-8'));
	                        $model = str_replace("  ", ' ', $model);
	                        $model = str_replace("   ", ' ', $model);
	                        $model = trim(strtoupper($model));
                    	}
                        $data["Product"]["model"] = !empty($model) ? $model : trim($product['brand']." ".$product['reference']) ;
                        $data["Product"]["available"] = $available;
                        $itemsRestModel->id_product = $verify['id_product'];
                        $itemsRestModel->productData = $data;
//                         pre($data);
                        $result = $itemsRestModel->putProduct();
//                         pre($result);
                        if(isset($result['body']['id'])){
                            
                            $idProduct = $result['body']['id'];
                            
                            if(!empty($idProduct)){
                            	$itemsRestModel->id_product = $idProduct;
                            	$resImage = $itemsRestModel->putImageProduct();
                            	if(!isset($resImage['httpCode']) OR $resImage['httpCode'] == 200){
                            		$logError[] = array('fotos' => 'Erro ao enviar fotos', 'result_image' => $resImage);
                            	}
                            }
                            
                            if(!empty($properties)){
                                
                                $caracteristicasRestModel = new CaracteristicasRestModel($db, null, $storeId);
                                $caracteristicasRestModel->product_id = $verify['id_product'];
                                $caracteristicasRestModel->caracteristicaData = $properties;
                                $res = $caracteristicasRestModel->postCaracteristica();
                                if($res['httpCode'] != '201'){
                                    pre($properties);
                                    pre($res);
                                }
                            }
                            
                            $queryRes = $db->update('module_tray_products',
                                array('store_id', 'id_product'),
                                array($storeId, $idProduct),
                                array('product_id' => $product['id'],
                                	'parent_id' => $product['parent_id'],
                                    'ean' => $ean,
                                    'title' => $title,
                                    'brand' => $brand,
                                	'sku' => $product['sku'],
                                	'price' => $salePrice,
                                	'stock' => $qtd,
                                    'available' => $available,
                                    'reference' => $product['reference'],
                                    'id_product' => $result['body']['id'],
                                    'code' => $result['body']['code'],
                                    'message' => $result['body']['message'],
                                    'updated' => date("Y-m-d H:i:s")
                                ));
                            
                            
                        }
                        
                        if(!empty($verify['id_product'])){
                            
                            $sqlVerifyVar = "SELECT * FROM module_tray_products_variations
                            WHERE store_id = {$storeId} AND id_product = {$verify['id_product']} AND parent_id LIKE '{$product['parent_id']}'";
                            $queryVerifyVar = $db->query($sqlVerifyVar);
                            $variationsVerify = $queryVerifyVar->fetchAll(PDO::FETCH_ASSOC);
                            pre($variationsVerify);
                            foreach($variationsVerify as $k => $verifyVar){
                                
                                if(!empty($verifyVar['id'])){
                                    
                                    $sqlvariation = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$verifyVar['product_id']}";
                                    $queryVariation = $db->query($sqlvariation);
                                    $variation = $queryVariation->fetch(PDO::FETCH_ASSOC);
                                    
                                    if(isset($variation['id'])){
                                        
                                        $dataVariant = array();
                                        
                                        $color = $variation['color'];
                                        
                                        $qtd = $variation['quantity'] > 0 ? $variation['quantity'] : 0 ;
                                        $salePriceModel->sku = $variation['sku'];
                                        $salePriceModel->product_id = $variation['id'];
                                        $salePrice = $salePriceModel->getSalePrice();
                                        $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                                        $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'];
                                        $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                                        if ($variation['blocked'] == "T"){
                                        	$qtd = 0;
                                        	echo "error|Produto Bloqueado...";
                                        }
                                        
                                        
                                        
                                        $dataVariant["Variant"]["product_id"] = $verify['id_product'];
                                        
                                        if(!empty($variation['ean']) OR !empty($product['ean'])){
                                            
                                            $dataVariant["Variant"]["ean"] = !empty($variation['ean']) ? trim($variation['ean']) : trim($product['ean']) ;
                                            
                                        }
                                        $dataVariant["Variant"]["order"] = $variation['variation'];
                                        $dataVariant["Variant"]["price"] = $salePrice;
                                        $dataVariant["Variant"]["cost_price"] = $variation['cost'];
                                        $dataVariant["Variant"]["stock"] = $qtd ;
                                        $dataVariant["Variant"]["minimum_stock"] = 1;
                                        $dataVariant["Variant"]["reference"] = $variation['reference'];
                                        
                                        $dataVariant["Variant"]["weight"] = isset($variation['weight']) ? $variation['weight'] : '1000';
                                        
                                        
                                        $dataVariant["Variant"]["length"] =  isset($variation['length']) ? $variation['length'] : '20';
                                        $dataVariant["Variant"]["width"] = isset($variation['width']) ? $variation['width'] : '20';
                                        $dataVariant["Variant"]["height"] = isset($variation['height']) ? $variation['height'] : '20';
                                        
                                        
                                        if(isset($color)){
                                            $dataVariant["Variant"]["Sku"][0]["type"] = "Cor";
                                            $dataVariant["Variant"]["Sku"][0]["value"] = !empty($color) ? trim(strtoupper($color)) : '' ;
                                        }
                                        
                                        if(isset($variation['variation']) && !empty($variation['variation'])){
                                            $dataVariant["Variant"]["Sku"][1]["type"] = !empty($variation['variation_type']) ? ucfirst(trim($variation['variation_type'])) : "Tamanho";
                                            $dataVariant["Variant"]["Sku"][1]["value"] = $variation['variation'];
                                        }
                                        
                                        $itemsRestModel->variation_id = $verifyVar['variation_id'];
                                        $itemsRestModel->productVariantData = $dataVariant;
                                        $resultVariant = $itemsRestModel->putProductVariation();
                                        
                                        if(isset($resultVariant['body']['id'])){
                                            
                                            $queryRes = $db->update('module_tray_products_variations',
                                                array('store_id', 'variation_id'),
                                                array($storeId, $resultVariant['body']['id']),
                                                array('product_id' => $variation['id'],
                                                    'parent_id' => $variation['parent_id'],
                                                    'sku' => $variation['sku'],
                                                    'variation_type' => $variation['variation_type'],
                                                    'variation' => $variation['variation'],
                                                    'id_product' => $verify['id_product'],
                                                    'variation_id' => $resultVariant['body']['id'],
                                                    'code' => $resultVariant['body']['code'],
                                                    'message' => $resultVariant['body']['message'],
                                                    'updated' => date("Y-m-d H:i:s")
                                                ));
                                        }
                                        
                                    }
                                    
                                }
                                
                            }
                        }
                    }
                }
            }
            if(!empty($queryRes)){
                echo "success|Produto Atualizados com sucesso!";
            }
            
            break;
            /**
             * atualiza as varições disponivei e agrupa pela referencia
             */
        case "update_product_variations":
            echo 'error|atualização de variação....';
            $itemsRestModel = new ItemsRestModel($db, null, $storeId);
            $salePriceModel = new SalePriceModel($db, null, $storeId);
            $salePriceModel->marketplace = 'Tray';
            if($storeId == 6){
            	$salePriceModel->priceType = 'price';
            }
            $productDescription = new ProductDescriptionModel($db);
            $productDescription->store_id = $storeId;
            
            $caracteristicasRestModel = new CaracteristicasRestModel($db, null, $storeId);
            $caracteristicasRestModel->store_id = $storeId;
            $res = $caracteristicasRestModel->getCaracteristica();
//             pre($res);die;
            if(isset($res)){
                $caracteristicas = array();
                foreach($res['body']->Properties as $key => $value){
                    $caracteristicas[] = array("id" => $value->Property->id, "name" => $value->Property->name, "alias" => titleFriendly($value->Property->name));
                }
            }
            if(isset($productId)){
                $productIds = is_array($productId) ? $productId : array($productId) ;
            }
            
            if(isset($_REQUEST['parent_id']) AND !empty($_REQUEST['parent_id'])){
                $parentId = $_REQUEST['parent_id'];
                $sql = "SELECT id, parent_id FROM available_products WHERE store_id = {$storeId} AND parent_id  = '{$parentId}' group by parent_id";
            }
            
            foreach($productIds as $i => $id){
                
                $sql = "SELECT title, reference, color, parent_id, reference, brand, collection, blocked FROM available_products WHERE store_id = {$storeId} AND id = {$id} ";
                $query = $db->query($sql);
                $rowParentId = $query->fetch(PDO::FETCH_ASSOC);
                
                if(isset($rowParentId['parent_id'])){
                    
                    $sqlVerify = "SELECT product_id, id_product FROM module_tray_products WHERE store_id = {$storeId} AND parent_id LIKE '{$rowParentId['parent_id']}'";
                    $queryVerify = $db->query($sqlVerify);
                    $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($verify['id_product'])){
                        
                        $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$verify['product_id']} ORDER BY id ASC";
                        $query = $db->query($sql);
                        $product = $query->fetch(PDO::FETCH_ASSOC);
                        
                        $attributesValues = getAttributesValuesFromParentId($db, $storeId, $product['parent_id']);
                        
                        $properties = array();
                        foreach($attributesValues as $j => $val ){
                            foreach($caracteristicas as $k => $caracteristica){
                                if($caracteristica['alias'] == $val['alias']){
                                    if(!in_array(array("property_id" => $caracteristica['id'], "value" => $val['value']), $properties)){
                                        $properties[] =  array("property_id" => $caracteristica['id'], "value" => $val['value']);
                                    }
                                }
                            }
                        }
                        
                        foreach($properties as $k => $props){
                            if($props['property_id'] == '17' OR $props['property_id'] == '25'){
                                $parts = explode(',', $props['value']);
                                $properties[$k]['value'] = $parts[0];
                            }
                        }
                        //                         pre($properties);die;
                        $color = $product['color'];
                        $images = getUrlImageFromParentId($db, $storeId, $product['parent_id']);
                        $available = !empty($images) ? 1 : 0 ;
                        
                        $data = array();
                        $countImage = 1;
                        sort($images);
                        foreach($images as $key => $image){
                            $data["Product"]["picture_source_{$countImage}"] = $image;
                            $countImage++;
                        }
                        
                        $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
                        $salePriceModel->sku = $product['sku'];
                        $salePriceModel->product_id = $product['id'];
                        $salePrice = $salePriceModel->getSalePrice();
                        $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                        $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'];
                        $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                        if ($product['blocked'] == "T"){
                        	$qtd = 0;
                        	echo "error|Produto Bloqueado...";
                        }
                        
                        $productDescription->parent_id = $product['parent_id'];
                        $description = $productDescription->GetParentProductDescription();
                        $ean = trim($product['ean']);
                        $title = mb_strtoupper(trim($product['title']), 'UTF-8');
                        $brand = mb_strtoupper(trim($product['brand']), 'UTF-8');
                        if(isset($product['ean']) && !empty($product['ean'])){
                            $data["Product"]["ean"] = trim($product['ean']);
                            $ean = trim($product['ean']);
                        }
                        
                        $data["Product"]["name"] = $title;
                        $data["Product"]["description"] = $product['description'];
                        $data["Product"]["description_small"] = ucwords(strtolower($product['title']));
                        $data["Product"]["price"] = $salePrice;
                        $data["Product"]["brand"] = $brand;
//                         $data["Product"]["weight"] = isset($product['weight']) ? $product['weight'] : '1200';
                        $weight = isset($product['weight']) ? (float)  $product['weight'] : (float)  1200;
                        if($weight < 100){
                            $weight = $weight * 1000;
                        }
                        $data["Product"]["weight"] = $weight;
                        $data["Product"]["stock"] = $qtd;
                        
                        $sqlCategory = "SELECT * FROM module_tray_categories WHERE store_id = {$storeId}
                                AND hierarchy LIKE '{$product['category']}'";
                        $queryCategory = $db->query($sqlCategory);
                        $category = $queryCategory->fetch(PDO::FETCH_ASSOC);
                        $data["Product"]["category_id"] = $category['id_category'];
                        
                        $categoryParts = explode(">", $product['category']);
                        if(isset($categoryParts[0])){
                            $sqlCategory = "SELECT * FROM module_tray_categories WHERE store_id = $storeId
                                    AND hierarchy LIKE '".trim($categoryParts[0])."'";
                            $queryCategory = $db->query($sqlCategory);
                            $category = $queryCategory->fetch(PDO::FETCH_ASSOC);
                            $data["Product"]["related_categories"] = $category['id_category'];
                        }
                        
                        
                        $colorReplace = str_replace("-", ' ', $color);
                        $colorReplace = str_replace("  ", ' ', $colorReplace);
                        $colorReplace = str_replace("   ", ' ', $colorReplace);
                        $colorReplace = trim($colorReplace);
                        $model = $product['title'];
                        $model = str_replace($colorReplace, "", mb_strtoupper($model, 'UTF-8'));
                        $model = str_replace(trim(strtoupper($product['reference'])), "", mb_strtoupper($model, 'UTF-8'));
                        $model = str_replace(mb_strtoupper(trim(end($categoryParts)), 'UTF-8'), "", mb_strtoupper($model, 'UTF-8'));
                        $model = str_replace(mb_strtoupper(trim(removeAcentosNew(end($categoryParts))), 'UTF-8'), "", mb_strtoupper($model, 'UTF-8'));
                        $model = str_replace("MASCULINA", "", mb_strtoupper($model, 'UTF-8'));
                        $model = str_replace("MASCULINO", "", mb_strtoupper($model, 'UTF-8'));
                        $model = str_replace("COURO", "", mb_strtoupper($model, 'UTF-8'));
                        //                         $model = str_replace(trim($product['brand']), "", mb_strtoupper($model, 'UTF-8'));
                        $model = str_replace("  ", ' ', $model);
                        $model = str_replace("   ", ' ', $model);
                        $model = trim(strtoupper($model));
                        $data["Product"]["model"] = !empty($model) ? $model : trim($product['brand']." ".$product['reference']) ;
                        $data["Product"]["available"] = $available;
                        $itemsRestModel->id_product = $verify['id_product'];
                        $itemsRestModel->productData = $data;
                        pre($data);
                        $result = $itemsRestModel->putProduct();
                        pre($result);
                        if(isset($result['body']['id'])){
                            
                            $idProduct = $result['body']['id'];
                            
                            if(!empty($properties)){
                                
                                $caracteristicasRestModel = new CaracteristicasRestModel($db, null, $storeId);
                                $caracteristicasRestModel->product_id = $verify['id_product'];
                                $caracteristicasRestModel->caracteristicaData = $properties;
                                $res = $caracteristicasRestModel->postCaracteristica();
                                if($res['httpCode'] != '201'){
                                    pre($properties);
                                    pre($res);
                                }
                            }
                            
                            $queryRes = $db->update('module_tray_products',
                                array('store_id', 'id_product'),
                                array($storeId, $idProduct),
                                array('product_id' => $product['id'],
                                	'parent_id' => $product['parent_id'],
                                    'ean' => $ean,
                                    'title' => $title,
                                    'brand' => $brand,
                                	'sku' => $product['sku'],
                                	'price' => $salePrice,
                                	'stock' => $qtd,
                                    'available' => $available,
                                    'reference' => $product['reference'],
                                    'id_product' => $result['body']['id'],
                                    'code' => $result['body']['code'],
                                    'message' => $result['body']['message'],
                                    'updated' => date("Y-m-d H:i:s")
                                ));
                            
                            
                        }
                        
                        if(!empty($verify['id_product'])){
                            
                            //                             pre($product['parent_id']);
                            $sqlvariation = "SELECT * FROM available_products WHERE store_id = {$storeId}
                            AND reference LIKE '{$rowParentId['reference']}' AND quantity > 0";
                            $queryVariation = $db->query($sqlvariation);
                            $availableVariations = $queryVariation->fetchAll(PDO::FETCH_ASSOC);
//                                                         pre($queryVar);die;
//                             $availableVariations = array();
                            
//                             foreach($queryVar as $k => $var){
// //                                 if($var['parent_id'] == $rowParentId['parent_id']){
//                                     $availableVariations[] = $var;
// //                                 }
//                             }
// //                             foreach($queryVar as $k => $var){
//                                 if($var['parent_id'] != $rowParentId['parent_id']){
//                                     $availableVariations[] = $var;
//                                 }
//                             }
                            foreach($availableVariations as $j => $variation){
                                
                                
                                $images = getUrlImageFromParentId($db, $storeId, $variation['parent_id']);
                                
                                
                                if(isset($variation['id']) and isset($images[0])){
                                    
                                    $available = !empty($images) ? 1 : 0 ;
                                    
                                    sort($images);
                                    
                                    $dataVariant = array();
                                    
                                    $color = $variation['color'];
                                    
                                    $sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$variation['id']}";
                                    $queryAP = $db->query($sqlAP);
                                    $verifyAP = $queryAP->fetch(PDO::FETCH_ASSOC);
                                    $qtd = $verifyAP['quantity'] > 0 ? $verifyAP['quantity'] : 0 ;
                                    $salePriceModel->sku = $verifyAP['sku'];
                                    $salePriceModel->product_id = $verifyAP['id'];
                                    $salePrice = $salePriceModel->getSalePrice();
                                    $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                                    $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'];
                                    $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                                    if ($verifyAP['blocked'] == "T"){
                                    	$qtd = 0;
                                    	echo "error|Produto Bloqueado...";
                                    }
                                    
                                    $dataVariant["Variant"]["product_id"] = $verify['id_product'];
                                    $dataVariant["Variant"]["ean"] = !empty($variation['ean']) ? trim($variation['ean']) : trim($product['ean']) ;
                                    $dataVariant["Variant"]["order"] = $j;//$variation['variation'];
                                    $dataVariant["Variant"]["price"] = $salePrice;
                                    $dataVariant["Variant"]["cost_price"] = $verifyAP['cost'];
                                    $dataVariant["Variant"]["stock"] = $qtd;
                                    $dataVariant["Variant"]["minimum_stock"] = 1;
                                    $dataVariant["Variant"]["reference"] = $variation['reference'];
                                    
//                                     $dataVariant["Variant"]["weight"] = isset($variation['weight']) ? $variation['weight'] : '1000';
                                    $weight = isset($variation['weight']) ? (float)  $variation['weight'] : (float)  1200;
                                    if($weight < 100){
                                        $weight = $weight * 1000;
                                    }
                                    $dataVariant["Variant"]["weight"] = $weight;
                                    
                                    $dataVariant["Variant"]["length"] =  isset($variation['length']) ? $variation['length'] : '20';
                                    $dataVariant["Variant"]["width"] = isset($variation['width']) ? $variation['width'] : '20';
                                    $dataVariant["Variant"]["height"] = isset($variation['height']) ? $variation['height'] : '20';
                                    
                                    if(isset($color)){
                                        $dataVariant["Variant"]["Sku"][0]["type"] = "Cor";
                                        $dataVariant["Variant"]["Sku"][0]["value"] = !empty($color) ? trim(strtoupper($color)) : '' ;
                                    }
                                    
                                    if(isset($variation['variation']) && !empty($variation['variation'])){
                                        $dataVariant["Variant"]["Sku"][1]["type"] = !empty($variation['variation_type']) ? ucfirst(trim($variation['variation_type'])) : "Tamanho";
                                        $dataVariant["Variant"]["Sku"][1]["value"] = $variation['variation'];
                                    }
                                    $countImage = 1;
                                    foreach($images as $key => $image){
                                        $dataVariant["Variant"]["picture_source_{$countImage}"] = $image;
                                        $countImage++;
                                    }
                                    $itemsRestModel->productVariantData = $dataVariant;
                                    pre($dataVariant);
                                    $sqlVerifyVar = "SELECT * FROM module_tray_products_variations WHERE store_id = {$storeId}
                                    AND id_product = {$verify['id_product']} AND parent_id LIKE '{$variation['parent_id']}' AND product_id = {$variation['id']}";
                                    $queryVerifyVar = $db->query($sqlVerifyVar);
                                    $variationsVerify = $queryVerifyVar->fetch(PDO::FETCH_ASSOC);
//                                     pre($variationsVerify);
                                    if(isset($variationsVerify['id'])){
//                                         pre("update");
                                        $itemsRestModel->variation_id = $variationsVerify['variation_id'];
                                        $resultVariant = $itemsRestModel->putProductVariation();
                                        pre($resultVariant);
                                        if($resultVariant['body']['id']){
                                            
                                            $queryRes = $db->update('module_tray_products_variations',
                                                array('store_id', 'variation_id'),
                                                array($storeId, $resultVariant['body']['id']),
                                                array('product_id' => $variation['id'],
                                                    'parent_id' => $variation['parent_id'],
                                                    'sku' => $variation['sku'],
                                                    'variation_type' => $variation['variation_type'],
                                                    'variation' => $variation['variation'],
                                                    'id_product' => $verify['id_product'],
                                                    'code' => $resultVariant['body']['code'],
                                                    'message' => $resultVariant['body']['message'],
                                                    'updated' => date("Y-m-d H:i:s")
                                                ));
                                            pre($queryRes);
                                        }
                                        
                                    }else{
                                        pre("insert");
                                        //                                         $itemsRestModel->variation_id = null;
                                        $resultVariant = $itemsRestModel->postProductVariation();
                                        pre($resultVariant);
                                        if($resultVariant['body']['id']){
                                            
                                            $queryRes = $db->insert('module_tray_products_variations', array(
                                                'store_id' => $storeId,
                                                'product_id' => $variation['id'],
                                                'parent_id' => $variation['parent_id'],
                                                'sku' => $variation['sku'],
                                                'variation_type' => $variation['variation_type'],
                                                'variation' => $variation['variation'],
                                                'id_product' => $verify['id_product'],
                                                'variation_id' => $resultVariant['body']['id'],
                                                'code' => $resultVariant['body']['code'],
                                                'message' => $resultVariant['body']['message'],
                                                'created' => date("Y-m-d H:i:s"),
                                                'updated' => date("Y-m-d H:i:s")
                                            ));
                                        }
                                    }
                                    
                                }
                                
                                
                            }
                        }
                    }
                }
            }
            if($queryRes){
                echo "success|Produto Atualizados com sucesso!";
            }
            
            break;
            
            
            
            /**
             * Importa dados do produto da tray para sysplace
             */
        case "update_product_information_tray":
            
            $itemsRestModel = new ItemsRestModel($db, null, $storeId);
            $updated = 0;
            $sqlParent = "SELECT * FROM module_tray_products WHERE store_id = {$storeId} ORDER BY id_product DESC";
            $queryParent = $db->query($sqlParent);
            while($rowParent = $queryParent->fetch(PDO::FETCH_ASSOC)){
                
                $itemsRestModel->id_product = $rowParent['id_product'];
                $result = $itemsRestModel->getProduct();
//                 pre($result);
// 				pre($rowParent['id_product']);
                $data = array('ean' => trim($result['body']->Product->ean),
                        'title' => trim($result['body']->Product->name),
                        'brand' => trim($result['body']->Product->brand),
                		'price' => trim($result['body']->Product->price),
                        'reference' => $result['body']->Product->reference,
                        'stock' => $result['body']->Product->stock,
                        'available' => $result['body']->Product->available,
                        'images' => $result['body']->Product->image,
                    	'thumbs' => $result['body']->Product->ProductImage[0]->thumbs->{90}->https,
                        'weight' => $result['body']->Product->weight,
		                'height' => $result['body']->Product->height,
		                'width' => $result['body']->Product->width,
		                'length' => $result['body']->Product->length,
                        'url' => $result['body']->Product->url->https
                    );
                
                pre($data);
                $query = $db->update('module_tray_products',
                    array('store_id', 'id'),
                    array($storeId, $rowParent['id']), $data);
                
                if(!$query){
                    pre($result);
                }else{
                    $updated++;
                }
                
            }
            
            if($query){
                echo "success|{$updated} Informações importadas com sucesso!";
            }
            
            break;
            
            
        case "delete_products":
            
            
            if(isset($productId)){
            	
            	$idProducts = is_array($productId) ? $productId : array($productId) ;
            	
            	$itemsRestModel = new ItemsRestModel($db, null, $storeId);
            	
            	$updated = 0;
            	
            	foreach($idProducts as $i => $productId){
	                
            		$sql = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
            		$query = $db->query($sql);
            		$productsAP = $query->fetch(PDO::FETCH_ASSOC);
	               
	                $sql = "SELECT * FROM module_tray_products WHERE store_id = {$storeId} AND  product_id = {$productId} OR parent_id LIKE '{$productsAP['parent_id']}'";
	                $query = $db->query($sql);
	                $products = $query->fetchAll(PDO::FETCH_ASSOC);
	                foreach($products as $key => $product){
	                   
	                    $itemsRestModel->id_product = $product['id_product'];
	                    
	                    $res = $itemsRestModel->deleteProduct();
	                   
// 	                    if($res['httpCode'] == '200'){
	                        
	                        $sql = "DELETE FROM module_tray_products WHERE store_id = {$storeId} AND id_product = {$product['id_product']}";
	                        $queryDelete = $db->query($sql);
	                        $sql = "DELETE FROM module_tray_products_variations WHERE store_id = {$storeId} AND id_product = {$product['id_product']}";
	                        $queryDelete = $db->query($sql);
	                        $sql = "DELETE FROM publications WHERE store_id = ? AND product_id = ? ";
	                        $query = $db->query($sql, array($storeId, $product['product_id']));
	                        
// 	                        $db->insert('products_log', array(
// 	                        		'store_id' => $storeId,
// 	                        		'product_id' => $productId,
// 	                        		'description' => 'Produto Removido do Ecommerce Tray',
// 	                        		'user' => $request,
// 	                        		'created' => date('Y-m-d H:i:s')
// 	                        ));
// 	                    }
	                    
	                    if(!$query){
	                        pre($result);
	                    }else{
	                        $updated++;
	                    }
	                    
	                }
	                if(isset($queryDelete)){
		                if($queryDelete){
		                    echo "success|{$updated} Produto excluido com sucesso!";
		                }
	                }
	                
	            }
	            
            }
            
            break;
            
        case "update_stock_price":
            
            $syncId =  logSyncStart($db, $storeId, "Tray", $action, "Atualização de estoque ecomemrce tray.", $request);
            $updatedVariation = $updatedProduct = 0;
            $itemsRestModel = new ItemsRestModel($db, null, $storeId);
            $salePriceModel = new SalePriceModel($db, null, $storeId);
            $salePriceModel->marketplace = 'Tray';
            if($storeId == 6){
            	$salePriceModel->priceType = 'price';
            }
            
            $productId = isset($_REQUEST["product_id"])  ? intval($_REQUEST["product_id"]) : NULL ;
            $dateFrom =  date("Y-m-d H:i:s",  strtotime("-24 hour") );
           
            $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id IN (
            	SELECT product_id as id  FROM module_tray_products WHERE store_id = {$storeId}
            )";
            if(isset($productId)){
                $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
            }
            $query = $db->query($sql);
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $sqlParent = "SELECT * FROM module_tray_products WHERE store_id = {$storeId} AND product_id = {$row['id']}";
                $queryParent = $db->query($sqlParent);
                while($rowParent = $queryParent->fetch(PDO::FETCH_ASSOC)){
                	
                	$log = array();
                	$logError = array();
                	
                	$qtd = $row['quantity'] > 0 ? $row['quantity'] : 0 ;
                    $salePriceModel->sku = $row['sku'];
                    $salePriceModel->product_id = $row['id'];
                    $salePrice = $salePriceModel->getSalePrice();
                    $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                    $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'];
                    $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                    if ($row['blocked'] == "T"){
                    	$qtd = 0;
//                     	echo "error|Produto Bloqueado...";
                    	$data["Product"]['hot'] = 0;
                    }
                   
                    $data = array();
                    $data["Product"]["price"] = $salePrice;
                   
                 
                    if($storeId == 4){
                        
//                         $data["Product"]["promotional_price"] = '';
//                         $data["Product"]["start_promotion"] =  "";
//                         $data["Product"]["end_promotion"] = '';
                        
//                         $increaseInt = 0 ; 
                       
// //                         if ($salePrice > 5 && $salePrice <= 20 ){
// //                             $increaseInt = 2;
// //                         }
// //                         if ($salePrice > 20 && $salePrice <= 79 ){
// //                             $increaseInt = 3;
// //                         }
// //                         if ($salePrice > 79  && $salePrice <= 248 ){
// //                             $increaseInt = 15;
// //                         }
// //                         if ($salePrice > 248  && $salePrice <= 600 ){
// //                             $increaseInt = 25;
// //                         }
// //                         if ($salePrice > 600){
// //                             $increaseInt = 35;
// //                         }
                        
//                         if($salePrice < 77){
                            
//                             $salePrice = ceil($salePrice * 1.05) - 0.10;
                            
//                             $price = (ceil($salePrice * 1.3) + $increaseInt) - 0.10;
                            
//                         }else{
                            
//                             $salePrice = ceil($salePrice) - 0.10;
                            
//                             $price = (ceil($salePrice * 1.4) + $increaseInt) - 0.10;
//                         }
                        
//                         if($salePrice < $price){
                           
// //                             if(isset($stockPriceRel['items']) && $stockPriceRel['items'] > 0){
// //                                 if($salePrice > 79 && $stockPriceRel['items'] <= 10){
// //                                     $salePrice = $salePrice -  ($stockPriceRel['items'] * 1);
// //                                 }
// //                             }
                            
//                             $data["Product"]["price"] = $price;
//                             $data["Product"]["promotional_price"] = ceil($salePrice) - 0.30;
//                             $data["Product"]["start_promotion"] =  "2021-11-01 00:00:00"; //date("Y-m-d H:s:i", strtotime("NOW"));
//                             $data["Product"]["end_promotion"] = date('2022-11-01 00:00:00');
//                             $data["Product"]["warranty"] = "Garantia de 1 ano mediante análise de fábrica*";
//                             $data["Product"]["warranty_days"] = '90';
                            
//                             echo $difPercent = (($price - $salePrice) * 100) / $price;
//                             echo '<br>';
// //                             $data["Product"]['hot'] = 0;
//                             if($difPercent > 30){
//                                 $data["Product"]['hot'] = 1;
//                             }
//                         }else{
//                             $data["Product"]["price"] =  ceil($salePrice) - 0.30;
//                             $data["Product"]["end_promotion"] =  date("Y-m-d H:s:i", strtotime("NOW"));
//                         }
                    }
                    
                    
                    if($qtd < 1){
                        $data["Product"]['hot'] = 0;
                        $data["Product"]["promotional_price"] = '';
                        $data["Product"]["start_promotion"] =  "";
                        $data["Product"]["end_promotion"] = ''; date("Y-m-d H:s:i", strtotime("NOW"));
                    }
                    $data["Product"]['free_shipping'] = 0 ;
//                     pre($data);
                    $itemsRestModel->id_product = $rowParent['id_product'];
                    $itemsRestModel->productData = $data;
                    $resultProduct = $itemsRestModel->putProduct();
//                     pre($resultProduct);
                    $log[$row['sku']][] = array(
                    		'tray' => 'PUT Price',
                    		'id_product' => $rowParent['id_product'],
                    		'sent' => $data,
                    		'response' => $resultProduct
                    	);
                    /**
                     * If doesnt have variations update stock direct in product
                     */
                    $sql = "SELECT * FROM module_tray_products_variations WHERE store_id = {$storeId}
                    AND id_product = {$rowParent['id_product']} AND parent_id LIKE '{$rowParent['parent_id']}'";
                    $queryAttr = $db->query($sql);
                    $num_rows = $queryAttr->rowCount();
                    if($num_rows < 1 ){
	                    $data = array();
	                    $data["Product"]["stock"] = $qtd;
	                    $itemsRestModel->id_product = $rowParent['id_product'];
	                    $itemsRestModel->productData = $data;
	                    $resultProduct = $itemsRestModel->putProduct();
	                    $log[$row['sku']][] = array(
	                    		'tray' => 'PUT Stock',
	                    		'id_product' => $rowParent['id_product'],
	                    		'sent' => $data,
	                    		'response' => $resultProduct
	                    	);
                    }
                    if($num_rows > 0 ){
	                    while($rowVariations =  $queryAttr->fetch(PDO::FETCH_ASSOC)){
	                        if(!empty($rowVariations['sku'])){ 
	                            $sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$rowVariations['product_id']}";
	                            $queryAP = $db->query($sqlAP);
	                            $verifyAP = $queryAP->fetch(PDO::FETCH_ASSOC);
	                            $qtd = $verifyAP['quantity'] > 0 ? $verifyAP['quantity'] : 0 ;
	                            $salePriceModel->sku = $verifyAP['sku'];
	                            $salePriceModel->product_id = $verifyAP['id'];
	                            $salePrice = $salePriceModel->getSalePrice();
	                            $stockPriceRel = $salePriceModel->getStockPriceRelacional();
	                            $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'];
	                            $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
	                            if ($verifyAP['blocked'] == "T"){
	                            	$qtd = 0;
// 	                            	echo "error|Produto Bloqueado...";
	                            }
	                            $dataVariant = array();
	                            $dataVariant["Variant"]["price"] = $salePrice;
	                           
	                            if($storeId == 4){
	                                
// 	                                $dataVariant["Variant"]["promotional_price"] = '';
// 	                                $dataVariant["Variant"]["start_promotion"] =  "";
// 	                                $dataVariant["Variant"]["end_promotion"] = '';
	                                
// 	                                if ($salePrice > 8 && $salePrice <= 12 ){
// 	                                    $salePrice = 19.70;
// 	                                }
// 	                                if ($salePrice > 5 && $salePrice <= 8 ){
// 	                                    $salePrice = 14.70;
// 	                                }
// 	                                if ($salePrice <= 5){
// 	                                    $salePrice = 9.70;
// 	                                }
                                    
// 	                                if($salePrice < 79){
// 	                                    $price = (ceil($salePrice * 1.3) + 2) - 0.10;
// 	                                }else{
// 	                                    $price = (ceil($salePrice * 1.4) + 15) - 0.10;
// 	                                }
	                                
// 	                                if($salePrice < $price){
	                                    
// // 	                                    if(isset($stockPriceRel['items']) && $stockPriceRel['items'] > 0){
// // 	                                        if($salePrice > 79 && $stockPriceRel['items'] <= 10){
// // 	                                            $salePrice = $salePrice -  ($stockPriceRel['items'] * 1);
// // 	                                        }
// // 	                                    }
	                                    
// 	                                    $dataVariant["Variant"]["price"] = $price;
// 	                                    $dataVariant["Variant"]["promotional_price"] = ceil($salePrice) - 0.30;
// 	                                    $dataVariant["Variant"]["start_promotion"] =  date("Y-m-d H:s:i", strtotime("NOW"));
// 	                                    $dataVariant["Variant"]["end_promotion"] = date('2022-m-d H:s:i');
// 	                                    $dataVariant["Variant"]['hot'] = 0;
// 	                                    $difPercent = (($price - $salePrice) * 100) / $price;
// 	                                    if($difPercent >= 30){
// 	                                        $dataVariant["Variant"]['hot'] = 1;
	                                        
// 	                                    }
// 	                                }else{
// 	                                    $data["Product"]["end_promotion"] =  date("Y-m-d H:s:i", strtotime("NOW"));
	                                   
	                                    
// 	                                }
	                            }
	                            if($qtd < 1){
	                                $dataVariant["Variant"]["promotional_price"] = '';
	                                $dataVariant["Variant"]["start_promotion"] =  "";
	                                $dataVariant["Variant"]["end_promotion"] = '';
	                            }
	                            $dataVariant["Variant"]['free_shipping'] = 0 ;
	                            
	                            $itemsRestModel->variation_id = $rowVariations['variation_id'];
	                            $itemsRestModel->productVariantData = $dataVariant;
	                            $resultVariant = $itemsRestModel->putProductVariation();
	                            $log[$row['sku']][] = array(
	                            		'tray' => 'PUT Variation Price',
	                            		'id_product' => $rowParent['id_product'],
	                            		'variation_id' => $rowVariations['variation_id'],
	                            		'sent' => $dataVariant,
	                            		'response' => $resultVariant
	                            );
	                            $dataVariant = array();
	                            $dataVariant["Variant"]["stock"] = $qtd;
	                            $itemsRestModel->variation_id = $rowVariations['variation_id'];
	                            $itemsRestModel->productVariantData = $dataVariant;
	                            $resultVariant = $itemsRestModel->putProductVariation();
	                            $log[$row['sku']][] = array(
	                            		'tray' => 'PUT Variation Stock',
	                            		'id_product' => $rowParent['id_product'],
	                            		'variation_id' => $rowVariations['variation_id'],
	                            		'sent' => $dataVariant,
	                            		'response' => $resultVariant
	                            );
	                            if(isset($resultVariant['body'])){
	                                if($resultVariant['httpCode'] == 200){
	                                	$updatedVariation++;
	                                    $queryRes = $db->update('module_tray_products_variations',
	                                        array('store_id','variation_id', 'id_product'),
	                                        array($storeId, $rowVariations['variation_id'], $rowVariations['id_product']),
	                                        array('code' => $resultVariant['body']['code'],
	                                            'message' => $resultVariant['body']['message'],
	                                            'updated' => date("Y-m-d H:i:s")
	                                        ));
	                                }
	                            }
	                            
	                        }
	                    }
                	}
                    
                    
                    
                    if(isset($resultProduct['body'])){
                    	if($resultProduct['httpCode'] == 200){
                    		$updatedProduct++;
                    		$queryProducts = $db->update('module_tray_products',
                    				array('store_id','id'),
                    				array($storeId, $rowParent['id']),
                    				array('price' => $salePrice,
                    						'stock' => $qtd,
                    						'sku' => $row['sku'],
                    						'updated' => date("Y-m-d H:i:s"),
                    						'code' => $resultProduct['body']['code'],
                    						'message' => $resultProduct['body']['message'],
                    						'log' => json_encode($log, JSON_PRETTY_PRINT)
                    				));
                    		pre($log);
                    		
                    		
                    	}
                    
                    }
                    
                }
            }
            logSyncEnd($db, $syncId, $updatedVariation);
            break;
            
        case "update_attributes":
            
            $itemsRestModel = new ItemsRestModel($db, null, $storeId);
            $count = 0;
            $caracteristicasTray = array();
            $caracteristicasRestModel = new CaracteristicasRestModel($db, null, $storeId);
            $limit = 50;
            $offset = 0;
            $currentPage = 1;
            do{

            	$caracteristicasRestModel->dataFilter = array( 
            			"sort"=>'id_desc',
            			"page"=> $currentPage, 
            			"limit" => $limit, 
            			"offset" => $offset
            			
            	);
            	
	            $res = $caracteristicasRestModel->getCaracteristica();
	            foreach ($res['body']->Properties as $k => $propertie){
	            	$caracteristicasTray[] = $propertie;
	            }
	            $offset += $limit;
	            $currentPage++;
	            
            } while($res['body']->paging->total > $offset);
            
            $caracteristicas = array();
            foreach($caracteristicasTray as $key => $value){
                $caracteristicas[] = array("id" => $value->Property->id, "name" => $value->Property->name, "alias" => titleFriendly($value->Property->name));
            }
            
            if(isset($productId)){
                $productIds = is_array($productId) ? $productId : array($productId) ;
            }
            
            if(isset($_REQUEST['parent_id']) AND !empty($_REQUEST['parent_id'])){
                $parentId = $_REQUEST['parent_id'];
                $sql = "SELECT id, parent_id FROM available_products WHERE store_id = {$storeId} AND parent_id  = '{$parentId}' group by parent_id";
            }
            foreach($productIds as $i => $id){
                
                $sql = "SELECT title, reference, color, parent_id, reference, brand, collection FROM available_products
                    WHERE store_id = {$storeId} AND id = {$id} ";
                $query = $db->query($sql);
                $rowParentId = $query->fetch(PDO::FETCH_ASSOC);
                
                if(isset($rowParentId['parent_id'])){
                    
                    $sqlVerify = "SELECT product_id, id_product FROM module_tray_products WHERE store_id = {$storeId} AND parent_id LIKE '{$rowParentId['parent_id']}'";
                    $queryVerify = $db->query($sqlVerify);
                    $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                    
                    $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$verify['product_id']} ORDER BY id ASC";
                    $query = $db->query($sql);
                    $products = $query->fetchAll(PDO::FETCH_ASSOC);
                    $product = $products[0];
                    
                    $attributesValues = getAttributesValuesFromParentId($db, $storeId, $product['parent_id']);
                    
                    $color = explode("-", $product['color']);
                    $attributesValues[] = array(
                    		"value" => $color[0],
                    		"attribute" => 'Cor',
                    		"alias" => 'cor'
                    );
                    $attributesValues[] = array(
                    		"value" => $product['reference'],
                    		"attribute" => 'Referencia',
                    		"alias" => 'referencia'
                    );
                    
                    $properties = array();
                    foreach($attributesValues as $j => $val ){
                        foreach($caracteristicas as $k => $caracteristica){
                            if($caracteristica['alias'] == $val['alias'] OR $caracteristica['name'] == $val['attribute']){
                                if(!in_array(array("property_id" => $caracteristica['id'], "value" => $val['value']), $properties)){
                                    $properties[] =  array("property_id" => $caracteristica['id'], "value" => $val['value']);
                                }
                            }
                        }
                    }
//                     pre($properties);die;
                    foreach($properties as $k => $props){
                        if($props['property_id'] == '17' OR $props['property_id'] == '25'){
                            $parts = explode(',', $props['value']);
                            $properties[$k]['value'] = $parts[0];
                        }
                    }
                    if(!empty($properties)){
                        
                        $caracteristicasRestModel = new CaracteristicasRestModel($db, null, $storeId);
                        $caracteristicasRestModel->product_id = $verify['id_product'];
                        $caracteristicasRestModel->caracteristicaData = $properties;
                        pre($properties);
                        $count++;
                        $res = $caracteristicasRestModel->postCaracteristica();
                        pre($res);
                        if($res['httpCode'] != '201'){
                        	
                            $queryRes = $db->update('module_tray_products',
                                array('store_id', 'id_product'),
                                array($storeId, $verify['id_product']),
                                array('code' => $res['body']['code'],
                                    'message' => $res['body']['message'],
                                    'updated' => date("Y-m-d H:i:s")
                                ));
                        }
                    }
                    
                }
                
                if($count > 0){
                    echo "success|{$count} Atributos Atualizados com sucesso!";
                }else{
                	pre($res);
                }
                
            }
            
            break;
            
            
        case "remove_inative_products":
            
            $itemsRestModel = new ItemsRestModel($db, null, $storeId);
            $limit = 50;
            $offset = 0;
            $currentPage = 1;
            do{
                
                $itemsRestModel->dataFilter = array( "page"=> $currentPage, "limit" => $limit, "limit" => $offset, "available" => 0, "stock" => 0, "activation_date" => "0000-00-00");
                
                $res = $itemsRestModel->getProducts();
                //                 pre($res);die;
                foreach($res['body']->Products as $k => $obj){
                    //                     pre($obj->Product);die;
                    $itemsRestModel->product_id = $obj->Product->id;
                    $resDel = $itemsRestModel->deleteProduct();
                    if($resDel['httpCode'] == '200'){
                        $sql = "DELETE FROM module_tray_products WHERE store_id = {$storeId} AND id_product = {$obj->Product->id}";
                        $db->query($sql);
                        $sql = "DELETE FROM module_tray_products_variations WHERE store_id = {$storeId} AND id_product = {$obj->Product->id}";
                        $db->query($sql);
                    }else{
                        pre($resDel);
                    }
                    
                    
                }
                
                $offset += $limit;
                $currentPage++;
                
                
            } while($res['body']->paging->total > $offset);
            
            break;
            /**
             * Remove produtos não encontrados na tray
             */
        case "delete_products_removed":
            //             die;
            $itemsRestModel = new ItemsRestModel($db, null, $storeId);
            
            $sqlParent = "SELECT * FROM module_tray_products WHERE store_id = {$storeId}";
            $queryParent = $db->query($sqlParent);
            while($rowParent = $queryParent->fetch(PDO::FETCH_ASSOC)){
                
                $itemsRestModel->id_product = $rowParent['id_product'];
                $result = $itemsRestModel->getProduct();
                if($result['httpCode'] == '404'){
                    $sql = "DELETE FROM module_tray_products WHERE store_id = {$storeId} AND id_product = {$rowParent['id_product']}";
                    $db->query($sql);
                    $sql = "DELETE FROM module_tray_products_variations WHERE store_id = {$storeId} AND id_product = {$rowParent['id_product']}";
                    $db->query($sql);
                }else{
                    pre($result);
                }
                
                
            }
            break;
            
            
            
        case "update_available_products_ean_from_tray":
            
            $sql = "SELECT available_products.*, module_tray_products.ean FROM available_products
            RIGHT JOIN module_tray_products ON module_tray_products.product_id =  available_products.id
            AND module_tray_products.ean IS NOT NULL AND module_tray_products.ean != ''
            WHERE available_products.store_id = {$storeId} AND available_products.ean IS NULL OR available_products.ean = ''";
            $query = $db->query($sql);
            echo $query->rowCount();
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                if(isset($row['ean']) && !empty($row['ean'])){
                    $queryRes = $db->update('available_products',
                        array('store_id', 'parent_id'),
                        array($storeId, $row['parent_id']),
                        array('ean' => trim($row['ean']),
                            'updated' => date("Y-m-d H:i:s")
                        ));
                    if(!$queryRes){
                        pre($queryRes);
                        pre($row);
                        
                    }else{
                        echo "<br>success|<br>";
                        echo $queryRes->rowCount();
                        echo "<br>";
                    }
//                     die;
                }
            }
            break;
            
    }  
     
}