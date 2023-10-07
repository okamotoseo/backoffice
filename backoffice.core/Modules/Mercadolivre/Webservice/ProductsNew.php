<?php
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/ProductDescriptionModel.php';
require_once $path .'/../../../Models/Products/PublicationsModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Api/CategoriesRestModel.php';
require_once $path .'/../Models/Api/ItemsRestModel.php';
require_once $path .'/../Models/Api/PicturesRestModel.php';
require_once $path .'/../Models/Adverts/ItemsModel.php';
require_once $path .'/../Models/Map/MlCategoryModel.php';
require_once $path .'/functions.php';



$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$type = isset($_REQUEST["type"]) && $_REQUEST["type"] != "" ? $_REQUEST["type"] : 'single' ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

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
        

        
        case "new_ads_product":
            
            $title = isset($_REQUEST["title"]) && !empty($_REQUEST["title"]) ? trim($_REQUEST["title"]) : null ;
            
            $listingTypes = isset($_REQUEST["listing_types"]) && !empty($_REQUEST["listing_types"]) ? trim($_REQUEST["listing_types"]) : null ;
            
            $templateDescription = $path ."/../Models/Adverts/Templates/ItemDescriptionStoreId_{$storeId}.php";

            if(file_exists($templateDescription)){
            	require_once $templateDescription;
            	
            }else{
            	require_once $path ."/../Models/Adverts/Templates/ItemDescriptionDefault.php";;
            }
            
            $availableProducts = new AvailableProductsModel($db);
            
            $publicationsModel = new PublicationsModel($db);
            
            $picturesRestModel = new PicturesRestModel($db, null, $storeId);
            
            $productIds = is_array($productId) ? $productId : array($productId) ;
            foreach($productIds as $i => $id){
                $availableProducts->store_id = $storeId;
                $availableProducts->id = $id;
                
                $products = $availableProducts->GetAvailableProducts();
                $product = $products[0];
                
                if($type == 'multiple'){
                    
                    $sqlVerifyAds = "SELECT id FROM ml_products WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}'";
                    $queryAds = $db->query($sqlVerifyAds);
                    $verifyAds = $queryAds->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($verifyAds['id'])){
                        continue;
                    }
                }
                
                $availableProducts->category = $product['category'];
                $setAttributeId = $availableProducts->getSetAttributeRelationship();
                
                $mlCategory = new MlCategoryModel($db, null, $storeId);
                $mlCategory->hierarchy = trim($product['category']);
                $mlCategoryRel = $mlCategory->getCategoryRelationship();
                if(empty($mlCategoryRel)){
                    
                    echo "error|Categoria sem Mapeamento Mercadolivre {$product['category']}";
                    continue;
                }
                if(empty($mlCategoryRel['attribute_types'])){
                    
                    echo "error|Atualizar Mapeamento de Categorias do Mercadolivre {$product['category']}";
                    continue;
                }
                if( $mlCategoryRel['attribute_types'] == 'attributes'){
                    $sqlVerifyVariation = "SELECT count(id) as total FROM `available_products`
                    WHERE store_id = {$storeId} AND `parent_id` LIKE '{$product['parent_id']}'";
                    $query = $db->query($sqlVerifyVariation);
                    $counVariations = $query->fetch(PDO::FETCH_ASSOC);
                    if($counVariations['total'] > 1){
                        echo "error|Para essa categoria não é permitido publicar produtos com variação";
                        continue;
                    }
                    
                }
                $categoriesRestModel = new CategoriesRestModel($db, null, $storeId);
                $categoriesRestModel->brand = trim($product['brand']);
                $categoriesRestModel->category_id = $mlCategoryRel['category_id'];
                $mlCategoryToPublish = $categoriesRestModel->getCategoryPublish();
                $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
                
                $salePriceModel = new SalePriceModel($db, null, $storeId);
                $salePriceModel->marketplace = "Mercadolivre";
                $salePriceModel->sku = $product['sku'];
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
                
                $itemDescriptionModel = new ItemDescriptionModel($db, null, $storeId);
                $itemDescriptionModel->product_id = $product['id'];
                $itemDescriptionModel->sku = $product['sku'];
                $itemDescriptionModel->parent_id = $product['parent_id'];
                $itemDescriptionModel->category_id = $mlCategoryRel['category_id'];
                $itemDescriptionModel->set_attribute_id = $setAttributeId;
                $itemDescriptionModel->getTemplateDescription();
                
                $itemsModel = new ItemsModel($db, null, $storeId);
                $itemsModel->product = $product;
                if(isset($listingTypes) && !empty($listingTypes)){
                    //gold_pro
                    $itemsModel->listing_type_id = $listingTypes;
                }
                $itemsModel->attribute_types = $mlCategoryRel['attribute_types'];
                $itemsModel->category_id = $mlCategoryRel['category_id'];
                $itemsModel->category_publish = $mlCategoryToPublish;
                $itemsModel->product_id = $product['id'];
                $itemsModel->sku = $product['sku'];
                $itemsModel->parent_id = $product['parent_id'];
                $itemsModel->price = $salePrice;
                $itemsModel->available_quantity = $qtd;
                $textTitle = isset($title) && !empty($title) ? $title : $product['title']; //$itemDescriptionModel->title;
                $itemsModel->title = substr ( $textTitle, 0, 60 );
                $itemsModel->description = strip_tags($itemDescriptionModel->description);
                $item = $itemsModel->getItem();
                
                if($salePrice < 1){
                	echo "error|Produto sem Preço";
                	continue;
                }
                if($qtd < 1){
                	echo "error|Produto sem Estoque";
                	continue;
                }
                if(empty($item)){
                    echo "error|Sistema em Manutenção";
                    continue;
                }
//                 $item['pictures'] = $itemsModel->getPictures();
                
//                 if(empty($item['pictures'])){
//                     echo "error|Produto sem imagem";
//                     continue;
//                 }
//                 pre($item['pictures']);die;
               
                
                $varPics = array();
                $count = 0;
//                 $color ='';
                $color = $product['color'];
//                 if(!isset($item['variations'])){
                    $picsItem = $itemsModel->getPathPictures();
                    foreach($picsItem as $j => $picture){
                        if($count > 1){
                            $count = 0;
                            sleep(10);
                        }
                        $partsDot = explode('.', $picture['source']);
                        $type = "image/".end($partsDot);
                        $partsName = explode('/', $picture['source']);
                        $name = end($partsName);
                        $picturesRestModel->picture =['file' => new \CurlFile("{$picture['source']}", $type, $name)];
                        $resPic = $picturesRestModel->postPictures();
                        pre(array($picturesRestModel->picture, $resPic['body']->id, $resPic['httpCode']));
                        if ($resPic['httpCode'] == 201) {
                            
                            $varPics[] = $resPic['body']->id;
                        }
                        pre($resPic);
                        $count++;
                        
                    }
                    foreach($varPics as $x => $valPic){
                        $item['pictures'][] = array('id' => $valPic);
                    }
//                 }
//                 pre($item['pictures']);
               
                if(isset($item['variations'])){
                   
                    foreach($item['variations'] as $i => $combinationsAttr){
                        
                        foreach($combinationsAttr['picture_ids'] as $k => $pictures){
                            
                            if(empty($color)){
                               
                                $color =  $k;
                                $varPics = array();
                                foreach($pictures as $j => $picture){
                                    if($count > 1){
                                        sleep(10);
                                        $count = 0;
                                    }
                                    $partsDot = explode('.', $picture);
                                    $type = "image/".end($partsDot);
                                    $partsName = explode('/', $picture);
                                    $name = end($partsName);
                                    $picturesRestModel->picture =['file' => new \CurlFile("{$picture}", $type, $name)];
                                    $resPic = $picturesRestModel->postPictures();
                                    pre(array($resPic['body']->id, $color, $resPic['httpCode']));
                                    if ($resPic['httpCode'] == 201) {
                                        $varPics[] = $resPic['body']->id;
                                    }
                                    pre($resPic);
                                    
                                    $count++;
                                    sleep(10);
                                }
                                $item['variations'][$i]['picture_ids'] = $varPics;
//                                 $item['variations'][$i]['picture_ids'] = array_merge($item['variations'][$i]['picture_ids'], $varPics);
                                
                                
                            }else{
                                
                                if($color == $k){
                                    $item['variations'][$i]['picture_ids'] = $varPics;
//                                     $item['variations'][$i]['picture_ids'] = array_merge($item['variations'][$i]['picture_ids'], $varPics);
                                }else{
                                    $color =  $k;
                                    $varPics = array();
                                    foreach($pictures as $j => $picture){
                                        if($count > 1){
                                            sleep(10);
                                            $count = 0;
                                        }
                                        $partsDot = explode('.', $picture);
                                        $type = "image/".end($partsDot);
                                        $partsName = explode('/', $picture);
                                        $name = end($partsName);
                                        $picturesRestModel->picture =['file' => new \CurlFile("{$picture}", $type, $name)];
                                        $resPic = $picturesRestModel->postPictures();
                                        pre(array($resPic['body']->id, $color, $resPic['httpCode']));
                                        if ($resPic['httpCode'] == 201) {
                                            $varPics[] = $resPic['body']->id;
                                        }
                                        pre($resPic);
                                        $count++;
                                        
                                        
                                    }
                                    $item['variations'][$i]['picture_ids'] = $varPics;
//                                     $item['variations'][$i]['picture_ids'] = array_merge($item['variations'][$i]['picture_ids'], $varPics);
                                    
                                }
                            
                            }
                        }
    
                    }
                    
                }
                pre($item);
//                 sleep(10);
                $itemsRestModel = new ItemsRestModel($db, null, $storeId);
                $itemsRestModel->item = $item;
                
                $result = $itemsRestModel->postItem();
//                 pre($result);
                if ($result['httpCode'] == 201) {
                     
                    $itemsRestModel->item_id = $result['body']->id;
                    $description = strip_tags($itemDescriptionModel->description);
                    $itemsRestModel->description = array("plain_text" => "{$description}");
                    $resDesc = $itemsRestModel->putItemDescription();

                    $publicationsModel->store_id = $storeId;
                    $publicationsModel->publication_code = str_replace("MLB", "", $result ['body']->id);
                    $publicationsModel->product_id = $product['id'];
                    $publicationsModel->sku = $product['sku'];
                    $publicationsModel->marketplace = 'Mercadolivre';
                    $publicationsModel->url = $result['body']->permalink;
                    $publicationsModel->user = $request;
                    $publicationsModel->Save();
                    
                    saveItem ($db, $storeId, $result ['body'], $product['sku'] );
                    
                    if($mlCategoryRel['attribute_types'] == 'variations'){
                        saveItemVariations ($db, $storeId, $result ['body']->id, $result ['body']->variations );
                    }
                    
                    echo "success|Produto cadastrado com sucesso!|{$product['sku']}|{$result['body']->permalink}|";
                    
                } else {
                    
//                 	$message = json_encode($result, JSON_PRETTY_PRINT);
//                     echo "error|{$message}";
                    $bodyRes = $result['body']->cause;
//                     pre($bodyRes);
                    if(isset($bodyRes)){
                        
                        $messageError = array();
                        
                        foreach($bodyRes as $j => $error){
                            
                            if($error->type == 'error'){
                                $messageError[] = $error;
                            }
                        }
                        
                        $message = json_encode($messageError, JSON_PRETTY_PRINT);
                        
                        echo "error|{$message}";
                    }
                    
                }
                
            }
            
            break;
            
    }
    
    
}

