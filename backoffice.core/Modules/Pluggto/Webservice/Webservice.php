<?php
set_time_limit ( 300 );
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/html; charset=utf-8");

$path = dirname(__FILE__);

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Pluggto.php';
require_once $path .'/../Models/Api/ProductRestModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/functions.php';

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
	        
	    case "add_all_available_products":
	        
	        $salePriceModel = new SalePriceModel($db, null, $storeId);
	        
	        $salePriceModel->marketplace = "Pluggto";
	        
	        $productImages = array();
	        
	        $j = 0;
	        
	        $categoryEnabled = array();
	        
	        $sql = "SELECT * FROM `available_products` WHERE store_id = {$storeId}
   			AND quantity > 0 AND parent_id IS NOT NULL AND variation != ''
       		AND id NOT IN (SELECT product_id as id FROM product_relational WHERE store_id = {$storeId}) 
            GROUP BY parent_id, color LIMIT 10";
	        $query = $db->query($sql);
	        $products = $query->fetchAll(PDO::FETCH_ASSOC);
	        
	        if(!empty($products[0])){
	            
	            $PluggProduct = new ProductRestModel($db, null, $storeId);
	            
	            foreach($products as $key => $product){
	                    
                    if(empty($categoryEnabled)){
                        
                        $parentImages = getUrlImageFromParentIdAndColor($db, $storeId, $product['parent_id'], $product['color']);
                        
                        if(!empty($parentImages[0])){

                            $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
                            $salePriceModel->sku = $product['sku'];
                            $salePriceModel->product_id = $product['id'];
                            $salePrice = $salePriceModel->getSalePrice();
                            $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                            $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
                            $salePrice = ceil($salePrice) - 0.10;
                            $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                            $qtd = $product['blocked'] == "T" ? 0 : $qtd;
                            
                            if($qtd > 0){
                                
                                $PluggProduct = new ProductRestModel($db, null, $storeId);
                                $PluggProduct->name = $product['title'];
                                $PluggProduct->sku = $product['parent_id'];
                                $PluggProduct->external = $product['parent_id'];
                                $PluggProduct->ean = $product['ean'];
                                $PluggProduct->model = $product['collection'];
                                $PluggProduct->brand = $product['brand'];
                                $PluggProduct->short_description = $product['title']." ".$product['color']." ".$product['brand']." ".$product['reference'];
                                $PluggProduct->description = $product['description'];
                                $PluggProduct->categories = [['name' => $product['category']]];
                                
                                $images = getUrlImageFromId($db, $storeId, $product['id']);
                                
                                if(empty($images)){
                                    $images = array();
                                    foreach($parentImages as $k => $parentImg){
                                        if(!in_array($parentImg, $images)){
                                            $images[] = $parentImg;
                                        }
                                    }
                                }
                                $photos = array();
                                foreach($images as $i => $val){
                                    $PluggProduct->photos[] =
                                        [
                                            'url' => $val,
                                            "name" =>  $product['title'],
                                            "title"=> $product['title'],
                                            "order" => $i
                                        ];
                                        
                                        $photos[] =
                                        [
                                            'url' => $val,
                                            "name" =>  $product['title'],
                                            "title"=> $product['title'],
                                            "order" => $i,
                                            'external' => $val,
                                        ];
                                }
                                
                                $attributesValues = array();
                                
                                $attributesValuesParent = getAttributesValuesFromParentId($db, $storeId, $product['parent_id']);
                                
                                foreach($attributesValuesParent as $j => $attrParent){
                                    $variationTypeLabel = $attrParent['label'];
                                    $variationTypeCode = $attrParent['code'];
                                    $attributesValues[] = ["code"=>"{$variationTypeCode}","label"=>"{$variationTypeLabel}","value"=>["code"=>"{$attrParent['value']['code']}","label"=>"{$attrParent['value']['label']}"]];
                                }
                                $PluggProduct->attributes = $attributesValues;
                                
                                $PluggProduct->special_price = $salePrice;
                                $PluggProduct->quantity = $qtd;
                                $PluggProduct->available = $qtd > 0 && !empty($images[0]) ? 1 : 0;
                                $PluggProduct->warranty_time = 3;
                                $PluggProduct->warranty_message = "3 meses de garantia";
                                $PluggProduct->manufacture_time = 0;
                                $PluggProduct->handling_time = 1;
                                $PluggProduct->quantity = $qtd;
                                $PluggProduct->price = number_format($salePrice);
                                $PluggProduct->dimension = [
                                    'weight' => isset($product['weight']) ? $product['weight']/1000 : 1 ,
                                    "length" => isset( $product['length']) ? ceil( $product['length']) : 20,
                                    "width" =>  isset( $product['width']) ? ceil( $product['width']) : 20 ,
                                    "height" => isset( $product['height']) ? ceil( $product['height']) : 20
                                ];
                                
                                
                                $sqlVariation = "SELECT * FROM `available_products` WHERE store_id = {$storeId}  AND parent_id LIKE '{$product['parent_id']}'";
                                $queryVariation = $db->query($sqlVariation);
                                $variations = $queryVariation->fetchAll(PDO::FETCH_ASSOC);
                               
                                foreach($variations as $K => $variation){
                                    
                                    $variationTypeLabel = ucfirst(strtoupper($variation['variation_type']));
                                    $variationTypeCode = $variation['variation_type'];
                                    
                                    $PluggProduct->variations[] = array(
                                        "sku" => $variation['sku'],
                                        "ean" => $variation['ean'],
                                        "name" => $variation['title'],
                                        "external" => $variation['parent_id'],
                                        "price" => $variation['sale_price'],
                                        "special_price" => $salePrice,
                                        "quantity" => $variation['quantity'],
                                        'photos' => $photos,
                                        "attributes" => [[
                                            "code"=>"{$variationTypeCode}","label"=>"{$variationTypeLabel}","value"=>["code"=>"{$variation['variation']}","label"=>"{$variation['variation']}"]
                                        ]]
                                        
                                    );
                                }
                                
                                $data = $PluggProduct->sendProductToPlugg();
                                
                                pre($data);
                                
                            }
                        }
                    }
	            }
	        }
	        
	        break;
	        
	    case "update_product":
	        
	        $PluggProduct = new ProductRestModel($db, null, $storeId);
	        
	        $PluggProduct->categories = [['name' => 'Teste']];
	        
	        $PluggProduct->name = 'Teste de integração sysplace';
	        
	        $PluggProduct->photos = [['url' => 'https://plugg.to/wp-content/uploads/2015/09/MercadoLivre.png']];
	        
	        $PluggProduct->sku = "teste123";
	        
	        // rand(1111, 99999);
	        
	        $PluggProduct->quantity = rand(1, 10);
	        
	        $PluggProduct->price = number_format(rand(1, 1000));
	        
	        $PluggProduct->dimension = ['weight' => 2];
	        
	        $data = $PluggProduct->sendProductToPlugg();
	        
	        pre($data);
	        
	        break;
	        
	        
	    case "delete_product":
	        
	        if(!isset($sku)){
	            
	            return;
	        }
	        
	        $PluggProduct = new ProductRestModel($db, null, $storeId);
	        
	        $PluggProduct->sku = $sku;
	        
	        $data = $PluggProduct->delete();
	         
	        pre($data);
	        
	        break;
	        
	    case "add_product" :
	        
	        $PluggProduct = new ProductRestModel($db, null, $storeId);
	        
	        $PluggProduct->categories = [['name' => 'Teste']];
	        
	        $PluggProduct->name = 'Teste';
	        
	        $PluggProduct->photos = [['url' => 'https://plugg.to/wp-content/uploads/2015/09/MercadoLivre.png']];
	        
	        $PluggProduct->sku = rand(1111, 99999);
	        
	        $PluggProduct->quantity = rand(1, 10);
	        
	        $PluggProduct->price = number_format(rand(1, 1000));
	        
	        $PluggProduct->dimension = ['weight' => 2];
	        
	        $data = $PluggProduct->sendProductToPlugg();
	        
	        pre($data);
	        
	        break;
	        
	        
		    
	}
	
}

