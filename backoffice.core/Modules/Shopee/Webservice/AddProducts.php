<?php

set_time_limit ( 300 );

$path = dirname(__FILE__);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/PublicationsModel.php';
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

 		case "create_product_tray":
             
 			$publicationsModel = new PublicationsModel($db);
 			$publicationsModel->store_id = $storeId;
 			$publicationsModel->user = $request;
 			$publicationsModel->marketplace = 'Tray';
 			
            $itemsRestModel = new ItemsRestModel($db, null, $storeId);
            
            $salePriceModel = new SalePriceModel($db, null, $storeId);
            
            $salePriceModel->marketplace = 'Tray';
            
            if($storeId == 6){
            	$salePriceModel->priceType = 'price';
            }
            
            $caracteristicasRestModel = new CaracteristicasRestModel($db, null, $storeId);
            
            $limit = 50;  
            $offset = 0;
            $currentPage = 1;
            
            $caracteristicas = array();
            
            do{
            	$caracteristicasRestModel->dataFilter = array( 'sort' => 'id_desc', "page"=> $currentPage, "limit" => $limit, 'offset' => $offset);
            	$res = $caracteristicasRestModel->getCaracteristica();
            	
            	if(!empty($res['body']->error_code)){
            		echo "error|Erro token expirado...";
            		return;
            		
            	}  
            	
            	foreach($res['body']->Properties as $key => $value){
            		$caracteristicas[] = array("id" => $value->Property->id, "name" => $value->Property->name, "alias" => titleFriendly($value->Property->name));
            	}
            	
            	$offset += $limit;
            	
            	$currentPage++;
            	 
            } while($res['body']->paging->total > $offset);
            

            if(isset($productId)){
                
                $productIds = is_array($productId) ? $productId : array($productId) ;
                
            }
            
            foreach($productIds as $i => $id){
            	
            	$availableVariations = $qtdTotal = 0;
            	
                $sql = "SELECT title, reference, color, parent_id, reference, brand, collection FROM available_products
                    WHERE store_id = {$storeId} AND id = {$id} ";
                $query = $db->query($sql);
                $rowParentId = $query->fetch(PDO::FETCH_ASSOC);
                
                $kit = true; 
                $itensRel = '';
                $sqlRelational = "SELECT * FROM `product_relational` WHERE store_id = {$storeId} AND product_id = {$id} ";
                $resRelational = $db->query($sqlRelational);
                $relational = $resRelational->fetch(PDO::FETCH_ASSOC);
                
                if(isset($relational['product_id'])){
                    
                    if($storeId == 6){ 
                        $kit = false;
                    }else{
                    
                        $sqlRelInfo = "SELECT product_relational.*, available_products.sku, available_products.brand,
                        available_products.variation_type, available_products.variation, available_products.title, 
                        available_products.color, available_products.quantity FROM `product_relational`
                        LEFT JOIN available_products ON available_products.id = product_relational.product_relational_id
                        WHERE product_relational.store_id = {$storeId} AND product_relational.product_id = {$id} 
                        ORDER BY product_relational.id DESC";
                        $resRelInfo = $db->query($sqlRelInfo);
                        $relInfo = $resRelInfo->fetchAll(PDO::FETCH_ASSOC);
                   
                        if(isset($relInfo)){
                            $itensRel = "<ul>";
                            foreach($relInfo as $key => $value){
                                
                                $itensRel .= "<li>{$value['qtd']} x {$value['title']} - {$value['brand']}</li>";
                            }
                            $itensRel .= "</ul>";
                        }
                        
                    }
                    
                }
                if($kit){
                    
                    
	                if(isset($rowParentId['parent_id']) ){
	                    
	                    $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} 
                        AND parent_id LIKE '{$rowParentId['parent_id']}' ORDER BY id ASC";
	                    $query = $db->query($sql);
	                    $products = $query->fetchAll(PDO::FETCH_ASSOC);
	                    $idProduct = '';
	                    
	                    foreach($products as $i => $value){
	                        if($value['quantity'] > 0){
	                            $availableVariations++;
	                        }
	                        $qtdTotal += $value['quantity'];
	                    }
	                    
	                    $product = $products[0];
	                    
	                    foreach($products as $i => $producVal){
	                        if( $producVal['id'] == $id ){
	                            $product = $products[$i];
	                        }
	                    }
	                    
	                    if(empty($itensRel)){
	                       $itensRel = "<ul><li>1 x {$product['title']} - {$product['brand']}</li></ul>";
	                    }
	                        
	                    
	                    $attributesValues = getAttributesValuesFromId($db, $storeId, $product['id']);
	                    
	                    $colorTrim = str_replace(' ', '-', trim($product['color']));
	                    $colorTrim = str_replace('-/-', '/', $colorTrim);
	                    $colorTrim = str_replace('-/', '/', $colorTrim);
	                    $colorTrim = str_replace('-/', '/', $colorTrim);
	                    $colorTrim = str_replace('/-', '/', $colorTrim);
	                    $colorTrim = str_replace('-', '/',$colorTrim);
	                    $colorTrim = str_replace(' ', '', $colorTrim);
	                    $colorTrim = str_replace('/', ' ', $colorTrim);
	                    $colorTrim = ucwords(removeAcentosNew(mb_strtolower(trim($colorTrim), 'UTF-8'))); 
	                    
	                    $attributesValues[] = array(
	                          "value" =>  isset($product['color']) ? $colorTrim : '' ,
	                    	  "attribute" => 'Cor',
	                    	  "alias" => 'cor'
	                    );
	                    
	                    $attributesValues[] = array(
	                    	"value" => $product['reference'],
	                    	"attribute" => 'Referencia',
	                    	"alias" => 'referencia'
	                    );
	                    $variation =  standardizeVariation($product['variation_type'], $product['variation']);
	                    if(strtolower($product['variation_type']) == 'voltagem'){
	                        if(!empty($variation)){
        	                    $attributesValues[] = array(
        	                        "value" => $variation,
        	                        "attribute" => 'Voltagem',
        	                        "alias" => 'voltagem'
        	                    );
	                        }
	                    }
	                    $propertiesDescription = array();
	                    $properties = array(); 
	                    
	                    $count = 0; 
	                    foreach($attributesValues as $j => $val ){
	                        
	                        if(!empty($val['attribute'])){
	                            
    	                        foreach($caracteristicas as $k => $caracteristica){
    	                            
    	                            if($caracteristica['alias'] == $val['alias'] OR $caracteristica['name'] == ucfirst(mb_strtolower($val['attribute'], 'UTF-8')) ){
    	                                
    	                                $value = !empty($val['value']) ? ucfirst(mb_strtolower($val['value'], 'UTF-8') ) : '' ;
    	                                
    	                                if(!in_array(array("property_id" => $caracteristica['id'], "value" => $value), $properties)){
    	                                    
    	                                    if($caracteristica['name'] == 'Voltagem'){
    	                                        $value =  standardizeVariation($caracteristica['name'], $value);
    	                                    }
    	                                    
        	                                $propertiesDescription[] =  array("property_id" => $caracteristica['id'], "value" => $value, 'name' => $caracteristica['name']);
        	                                $properties[] =  array("property_id" => $caracteristica['id'], "value" => $value);
    	                                    
        	                                $count++;
    	                                }
    	                            }
    	                        } 
	                        } 
	                    } 
	                    
	                    $imagesAvailable = getPathImageFromSku($db, $storeId, $product['sku']); 
	                    
	                    $available = !empty($imagesAvailable[0]) ? 1 : 0 ;
	                     
	                    $data = array();
	                    
	                    $sqlVerify = "SELECT id_product FROM module_tray_products WHERE store_id = {$storeId}
	                    AND parent_id LIKE '{$rowParentId['parent_id']}'";
	                    
	                    $queryVerify = $db->query($sqlVerify);
	                    
	                    $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	                    
	                    if(!isset($verify['id_product'])){
	                        
	                        if($qtdTotal > 0){
	                            
	                        	$qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
	                        	$title = mb_strtoupper(trim($product['title']), 'UTF-8');
	                        	
	                        	if($availableVariations == 1){
	                        	    $test = explode($variation, $title); 
	                        	    if(!isset($test[1])){
	                        	        $title .= ' '.mb_strtoupper(trim($variation), 'UTF-8');
	                        	    }
	                        	}
	                        	$brand = mb_strtoupper(trim($product['brand']), 'UTF-8');
	                            
	                        	$salePriceModel = new SalePriceModel($db, null, $storeId);
	                        	
	                            $salePriceModel->marketplace = "Tray";
	                            
	                            if($storeId == 6){
	                            	$salePriceModel->priceType = 'price';
	                            }
	                            $salePriceModel->sku =  trim($product['sku']);
	                            
	                            $salePriceModel->product_id = $product['id'];
	                            
	                            $salePrice = $salePriceModel->getSalePrice();
	                            
	                            $stockPriceRel = $salePriceModel->getStockPriceRelacional();
	                            
	                            $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'];
	                            
	                            $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
	                            
	                            if ($product['blocked'] == "T"){
	                            	$qtd = 0;
	                            	echo "error|Produto Bloqueado...";
	                            	continue;
	                            }
	                            
	                            $data["Product"]['has_variation'] = $availableVariations > 1 ? 1 : 0 ;
	                            $data["Product"]['free_shipping'] = 0 ;
	                            $data["Product"]['hot'] = $qtdTotal > 2 ? 1 : 0 ;
	                            $data["Product"]['release'] = $qtdTotal > 3 ? 1 : 0 ;
	                            
	                            if($storeId == 4){
	                                $data["Product"]['hot'] = 0 ;
	                                $data["Product"]['release'] =0 ;
	                                $data["Product"]['promotion'] = 0;
// 	                                $data["Product"]['free_shipping'] = $salePrice > 79 ? 1 : 0;
	                            }
	                            
	                            $data["Product"]["ean"] = $product['ean'];
	                            $data["Product"]["name"] = $title;
	                            $data["Product"]["modified"] = $product['updated'];
	                            $data["Product"]["description_small"] = strtoupper($product['title']);
	                            $data["Product"]["included_items"] = $itensRel;
	                            
	                            if($storeId == 4){
	                                
	                                $data["Product"]["model"] = $product['sku'];
// 	                                $data["Product"]["additional_message"] = "<div class='additional_message_fanlux'></div>";
	                                
	                                $product['description'] = str_replace('•', '-', $product['description']);
	                                $textDescription = "<div class='description_fanlux' style='padding:5px'>
                                        <div class='description_text_fanlux' style='box-sizing: border-box; padding: 8px; 
                                        line-height: 1.42857; vertical-align: top; border-top: 1px solid rgb(221, 221, 221); 
                                        background-color: rgb(249, 249, 249);'>{$product['description']}</div>

                                        <div class='description_title_fanlux' >
                                            <p style='font-size: 16px !important
        	                                font-weight: 800; background: #e9e9e9; width: 100%; display: inline-block;
        	                                margin: 0 !important; padding: 12px 0 12px 0;  color: #656565;  text-transform: uppercase; line-height: 22px' >
                                            <span class='titulo-iten-new' style='margin-bottom: 5px; padding-left: 20px; font-family: &quot;open sans&quot;
                                             font-size: 14px;'>ITENS INCLUSOS</span></p>
                                            <div class='description_text_fanlux' style='box-sizing: border-box; padding: 8px; 
                                            line-height: 1.42857; vertical-align: top; 
                                            background-color: rgb(249, 249, 249);'>{$itensRel}</div>
                                        </div>
                                        <div class='description_title_fanlux' >
                                            <p style='font-size: 16px !important
        	                                font-weight: 800; background: #e9e9e9; width: 100%; display: inline-block;
        	                                margin: 0 !important; padding: 12px 0 12px 0;  color: #656565;  text-transform: uppercase; line-height: 22px' >
                                            <span class='titulo-iten-new' style='margin-bottom: 5px; padding-left: 20px; font-family: &quot;open sans&quot;
                                             font-size: 14px;'>CARACTERÍSTICAS TÉCNICAS</span></p>
                                        </div>";
	                               
	                                $textDescription .= "<table border='0' cellpadding='0' cellspacing='0' class='table table-striped table-prod' 
                                        style='box-sizing: border-box; border-collapse: collapse; border-spacing: 0px; max-width: 100%; 
                                        width: 100%; margin-bottom: 20px; color: rgb(102, 102, 102); font-family: &quot;Open Sans&quot;, sans-serif; 
                                        font-size: 15px;'>";
	                                asort($propertiesDescription);
	                                foreach($propertiesDescription as $i => $attrDescription){
	                                    
	                                    switch($attrDescription['name']){
	                                        case 'Voltagem': $visibility = false; break;
	                                        case 'é adequado para envío': $visibility = false; break;
	                                        case 'é inflamável': $visibility = false; break;
	                                        case 'Condição do item': $visibility = false; break;
	                                        case 'Cor': $visibility = false; break;
	                                        case 'Embalagem do envío': $visibility = false; break;
	                                        case 'Tensão': $visibility = false; break;
	                                        case 'Clima': $visibility = false; break;
	                                        case 'é silencioso': $visibility = false; break;
	                                        case 'Cor das pás': $visibility = false; break;
	                                        default : $visibility = true; break;
	                                    }
	                                    if($visibility){
    	                                    $textDescription .= "<tr><td style='box-sizing: border-box; padding: 8px; 
                                            line-height: 1.42857; vertical-align: top; border-top: 1px solid rgb(221, 221, 221); 
                                            background-color: rgb(249, 249, 249);'><strong>{$attrDescription['name']}</strong></td>
                                            <td style='box-sizing: border-box; padding: 8px; 
                                            line-height: 1.42857; vertical-align: top; border-top: 1px solid rgb(221, 221, 221); 
                                            background-color: rgb(249, 249, 249);'>{$attrDescription['value']}</td>
                                            </tr>";
	                                    }
	                                }
	                                
	                                $textDescription .= "</table></div>";
	                                $product['description'] = $textDescription;
	                            }
	                            
	                            
	                            $data["Product"]["description"] = $product['description'];
	                            $data["Product"]["price"] = $salePrice;
	                            $data["Product"]["cost_price"] = $product['cost'];
	                            
	                            if($storeId == 4){ 
// 	                                if ($salePrice > 20){
// 	                                    $salePrice = $salePrice - 5;
// 	                                }
	                                if($salePrice < $product['sale_price']){
	                                   $data["Product"]["price"] = $product['sale_price'] ; 
	                                   $data["Product"]["promotional_price"] = $salePrice;
	                                   $data["Product"]["start_promotion"] =  date("Y-m-d H:s:i", strtotime("-1 day"));
                                	   $data["Product"]["end_promotion"] = date('2022-m-d H:s:i');
                                	   
                                	   $difPercent = (($product['sale_price'] - $salePrice) / $salePrice) *100;
                                	  
                                	   if($difPercent > 10 && $salePrice > 149){
                                	       $data["Product"]['hot'] = 1;
                                	       
                                	   }
                                	   if($difPercent < 10){
                                	       $data["Product"]['promotion'] = 1;
                                	   }
	                                }
	                            }
	                            
	                            $data["Product"]["brand"] = $storeId == 6 ? $brand : mb_strtoupper(trim($brand), 'UTF-8') ;
// 	                            $data["Product"]["model"] =  isset($product['color']) ? $colorTrim : '' ;
	                            $weight = isset($product['weight']) ? (float)  $product['weight'] : (float)  1200;
	                            if($weight < 100){ 
	                            	$weight = $weight * 1000; 
	                            }
	                            $data["Product"]["weight"] = $weight;
	                            $data["Product"]["length"] = isset($product['length']) ? $product['length'] : 20;
	                            $data["Product"]["width"] = isset($product['width']) ? $product['width'] : 20;
	                            $data["Product"]["height"] = isset($product['height']) ? $product['height'] : 20;
	                            $data["Product"]["stock"] = $qtd;

	                           $sqlCategory = "SELECT * FROM module_tray_categories WHERE store_id = ? AND hierarchy LIKE ?";
	                            $queryCategory = $db->query($sqlCategory, array($storeId, $product['category'])); 
	                            $category = $queryCategory->fetch(PDO::FETCH_ASSOC);
	                            if(empty($category['id_category'])){
	                                echo "error|Produto Sem Categoria Relacionada...";
// 	                                continue;
	                            } 
	                            
	                            $data["Product"]["category_id"] = $category['id_category'];
	                            
	                            $categoryParts = explode(">", $product['category']);
	                            
	                            if(isset($categoryParts[0])){
	                                $sqlCategory = "SELECT * FROM module_tray_categories WHERE store_id = $storeId
	                                AND hierarchy LIKE '".trim($categoryParts[0])."'";
	                                $queryCategory = $db->query($sqlCategory);
	                                $category = $queryCategory->fetch(PDO::FETCH_ASSOC);
	                                $data["Product"]["related_categories"] = $category['id_category'];
	                            }
	                            
	                            $data["Product"]["available"] = $available;
	                            $data["Product"]["reference"] = $product['reference'];
	                            $data["Product"]["virtual_product"] = "0";
	                            
                             	//$data["Product"]["availability"] = "Disponível em 3 dias";
                             	//$data["Product"]["availability_days"] = 3;
                             	//$data["Product"]["release_date"] = "";
                             	//$data["Product"]["shortcut"] = "";
	                            
	                            $itemsRestModel->productData = $data;
	                            
	                            $result = $itemsRestModel->postProduct();
	                            
	                            if(isset($result['body']['id'])){
	                                
	                                $idProduct = $result['body']['id'];
	                                
	                                if(!empty($attributesValues)){ 
// 	                                    pre($properties); 
	                                    $caracteristicasRestModel = new CaracteristicasRestModel($db, null, $storeId);
	                                    $caracteristicasRestModel->product_id = $idProduct;
	                                    $caracteristicasRestModel->caracteristicaData = $properties;
	                                    $res = $caracteristicasRestModel->postCaracteristica();
// 	                                    pre($res);
	                                    if(!isset($res['httpCode']) OR $res['httpCode'] != 201){
	                                        $logError[] = array('caracteristicas' => $properties, 'message' => 'Erro ao enviar caracteristicas', 'result_caracteristicas' => $res);
// 	                                    	continue;
	                                    }
	                                }
	                                
	                                $queryRes = $db->insert('module_tray_products', array(
	                                    'store_id' => $storeId,
	                                    'product_id' => $id,
	                                	'sku' => $product['sku'],
	                                	'parent_id' => $product['parent_id'],
	                                    'ean' => $product['ean'],
	                                    'title' => $title,
	                                    'brand' => $brand,
	                                	'price' => $salePrice,
	                                	'stock' => $qtd,
	                                    'available' => $available,
	                                    'reference' => $product['reference'],
	                                    'id_product' => $result['body']['id'],
	                                    'code' => $result['body']['code'],
	                                    'message' => $result['body']['message'],
	                                    'created' => date("Y-m-d H:i:s"),
	                                    'updated' => date("Y-m-d H:i:s")
	                                ));
	                                
	                                if(!empty($idProduct)){
	                                	$itemsRestModel->id_product = $idProduct;
	                                	$resImage = $itemsRestModel->putImageProduct();
	                                	if(!isset($resImage['httpCode']) OR $resImage['httpCode'] != 200){
	                                		$logError[] = array('fotos' => 'Erro ao enviar fotos', 'result_image' => $resImage);
	                                		pre($logError);
	                                	}
	                                }
	                                
	                                $publicationsModel->publication_code = $result['body']['id'];
	                                $publicationsModel->product_id = $product['id'];
	                                $publicationsModel->sku = $product['sku'];
	                                $publicationsModel->Save();
	                            }
	                        }
	                    }else{
	                        $idProduct = $verify['id_product'];
	                        echo "produto existe";
	                        continue;
	                        //TODO Fazer update aki
	                    }
	                   
	                    $itemsRestModel->productData = array();
	                   
	                    if(isset($idProduct) AND $availableVariations > 1 ){
	                    	
	                        foreach($products as $key => $product){
	                            $dataVariant = array();
	                            $sqlVerifyVar = "SELECT id FROM module_tray_products_variations
	                            WHERE store_id = {$storeId} AND product_id = {$product['id']} AND sku LIKE '{$product['sku']}'";
	                            $queryVerifyVar = $db->query($sqlVerifyVar);
	                            $verifyVar = $queryVerifyVar->fetch(PDO::FETCH_ASSOC);
	                            if(!isset($verifyVar['id'])){
	                                
	                                if( $product['quantity'] > 0){
	                                   
	                                    $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
	                                    
	                                    $salePriceModel = new SalePriceModel($db, null, $storeId);
	                                    
	                                    $salePriceModel->marketplace = 'Tray';
	                                    
	                                    if($storeId == 6){
	                                    	$salePriceModel->priceType = 'price';
	                                    }
	                                    
	                                    $salePriceModel->sku =  trim($product['sku']);
	                                    
	                                    $salePriceModel->product_id = $product['id'];
	                                    
	                                    $salePrice = $salePriceModel->getSalePrice();
	                                    $stockPriceRel = $salePriceModel->getStockPriceRelacional();
	                                    $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'];
	                                    
	                                    $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
	                                    
	                                    if ($product['blocked'] == "T"){
	                                    	$qtd = 0;
	                                    	echo "error|Produto Bloqueado...";
	                                    	continue;
	                                    }
	                                    
	                                    $dataVariant["Variant"]["product_id"] = $idProduct; 
	                                    $dataVariant["Variant"]["ean"] = $product['ean'];
	                                    $dataVariant["Variant"]["order"] = $product['variation'];
	                                    $dataVariant["Variant"]["price"] = $salePrice;
	                                    $dataVariant["Variant"]["cost_price"] = $product['cost'];
	                                    $dataVariant["Variant"]["stock"] = $qtd;
	                                    $dataVariant["Variant"]["minimum_stock"] = 1;
	                                    $dataVariant["Variant"]["reference"] = $product['reference'];
	                                    $dataVariant["Variant"]["weight"] = isset($product['weight']) ? number_format($product['weight'], 2, '', '') : '1200';
	                                    $dataVariant["Variant"]["length"] =  isset($product['length']) ? $product['length'] : '20';
	                                    $dataVariant["Variant"]["width"] = isset($product['width']) ? $product['width'] : '20';
	                                    $dataVariant["Variant"]["height"] = isset($product['height']) ? $product['height'] : '20';
	                                    if($storeId == 4){
	                                        if ($salePrice > 20){
	                                            $salePrice = $salePrice - 5;
	                                        }
	                                        if($salePrice < $product['sale_price']){
	                                            $dataVariant["Variant"]["price"] = $product['sale_price'] ;
	                                            $dataVariant["Variant"]["promotional_price"] = $salePrice ;
	                                            $dataVariant["Variant"]["start_promotion"] =  date("Y-m-d H:s:i", strtotime("-1 day"));
	                                            $dataVariant["Variant"]["end_promotion"] = date('2022-m-d H:s:i');
	                                            
	                                           
	                                        }
	                                    }
	                                      
	                                    $dataVariant["Variant"]["Sku"][0]["type"] = "Cor";
	                                    
	                                    $colorTrim = str_replace(' ', '-', trim($product['color']));
	                                    $colorTrim = str_replace('-/-', '/', $colorTrim);
	                                    $colorTrim = str_replace('-/', '/', $colorTrim);
	                                    $colorTrim = str_replace('-/', '/', $colorTrim);
	                                    $colorTrim = str_replace('/-', '/', $colorTrim);
	                                    $colorTrim = str_replace('-', '/',$colorTrim);
	                                    $colorTrim = str_replace(' ', '', $colorTrim); 
	                                    $colorTrim = mb_strtoupper(removeAcentosNew(trim($colorTrim)), 'UTF-8');
	                                    
	                                    $colorTrim = str_replace('/', ' ', $colorTrim);
	                                    $colorTrim = ucwords(removeAcentosNew(mb_strtolower(trim($colorTrim), 'UTF-8')));
	                                    
	                                    $dataVariant["Variant"]["Sku"][0]["value"] = isset($product['color']) ? $colorTrim : '' ;
                                        
	                                    $dataVariant["Variant"]["Sku"][1]["type"] = ucfirst(strtolower($product['variation_type'])); //Tamanho";//substituir por variation type
	                                    
	                                    if(strtolower($product['variation_type']) == 'voltagem'){ 
	                                        $variation =  standardizeVariation($product['variation_type'], $product['variation']);
	                                        if(!empty($variation)){
	                                            
	                                            $dataVariant["Variant"]["Sku"][1]["value"] = $variation;
	                                        }
	                                        
	                                    }else{
	                                       
	                                       $dataVariant["Variant"]["Sku"][1]["value"] = $product['variation'];
	                                    
	                                    }
	                                   
	                                    $images = getUrlImageFromSku($db, $storeId, $product['sku']);
	                                    if(isset($images[0])){
	                                        foreach($images as $i => $image){ 
	                                            if($i < 6 && !empty($image)){
    	                                            $ind = $i+1;
    	                                            $dataVariant["Variant"]["picture_source_{$ind}"] = $image ;
	                                            }
	                                        }
	                                    }
	                                    $itemsRestModel->productVariantData = $dataVariant;
	                                    $resultVariant = $itemsRestModel->postProductVariation();
	                                      
	                                    if(isset($resultVariant['body']['id'])){
	                                        
    	                                    if($resultVariant['body']['id']){
    	                                        
    	                                        $queryRes = $db->insert('module_tray_products_variations', array(
    	                                            'store_id' => $storeId,
    	                                            'product_id' => $product['id'],
    	                                            'parent_id' => $product['parent_id'],
    	                                            'sku' => $product['sku'],
    	                                            'variation_type' => $product['variation_type'],
    	                                            'variation' => $product['variation'],
    	                                            'id_product' => $idProduct,
    	                                            'variation_id' => $resultVariant['body']['id'],
    	                                            'code' => $resultVariant['body']['code'],
    	                                            'message' => $resultVariant['body']['message'],
    	                                            'created' => date("Y-m-d H:i:s"),
    	                                            'updated' => date("Y-m-d H:i:s")
    	                                        ));
    	                                        
    	                                        $publicationsModel->publication_code = $resultVariant['body']['code'];
    	                                        $publicationsModel->product_id = $product['id'];
    	                                        $publicationsModel->sku = $product['sku'];
    	                                        $publicationsModel->Save();
    	                                    }
	                                    }else{
	                                        pre($resultVariant);
	                                    }
	                                    
	                                }
	                            }
	                        }
	                    }
	                    if(isset($idProduct)){
		                    unset($idProduct);
	                    }
	                }
                }else{
                	echo "produto tipo kit não permitido";
                }
                
                if(!isset($logError)){
                	echo "success|Produto cadastrado com sucesso!";
                }else{
                	echo "error|Erro ao cadastrar produto ecommerce Tray.";
                	echo json_encode($logError, JSON_PRETTY_PRINT);
                }
                
            }
            
            break;
    }
}