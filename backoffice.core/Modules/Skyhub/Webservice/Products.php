<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Products/PublicationsModel.php';

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
    
    $moduleConfig = getModuleConfig($db, $storeId, 9);
//     pre($moduleConfig);die;
    require_once $path.'/../../../vendor/autoload.php';
    
//     $email   = 'atendimento.fanlux@gmail.com';
//     $apiKey  = 'oKEbYMqXJoEuHNqV_9h7';
    
//     $email   = 'willians.seo@gmail.com';
//     $apiKey  = 'wdDvgzzG5tdsf9y3CKfM';
//     $xAccountKey = 'xk21bPa9jQ';
//     $baseUri = 'https://api.skyhub.com.br';
    
    $email   = $moduleConfig['email'];
    $apiKey  = $moduleConfig['api_key'];
    $xAccountKey = $moduleConfig['account_key'];
    $baseUri = $moduleConfig['base_uri'];
    
    /** @var \SkyHub\Api $api */
    $api = new SkyHub\Api($email, $apiKey, $xAccountKey, $baseUri);
    
//     pre($api);die;
    switch($action){
        
        case "list_products":
            
            $entityInterface = $api->product()->entityInterface();
            
            $response = $entityInterface->products();
        
            if( method_exists( $response, 'body' ) ){
                
                $products = json_decode($response->body());
                
            }else{
                
                pre($response->message());
            }
            
            break;
            
            
        case "get_product":
            
            $entityInterface = $api->product()->entityInterface();
            
            $response = $entityInterface->products();
            
            if( method_exists( $response, 'body' ) ){
                
                $products = json_decode($response->body());
                pre($products);
                
            }else{
                
                pre($response->message());
            }
            
            break;
        

            
            
        case "update_stock_price";
        
            die;
            $syncId =  logSyncStart($db, $storeId, "Skyhub", $action, "Atualização do estoque e preço skyhub", $request);
            
            $availableProducts = new AvailableProductsModel($db);
            $availableProducts->store_id = $storeId;
            $salePriceModel = new SalePriceModel($db, null, $storeId);
            $productModel = new ProductsModel($db);
            $productModel->store_id = $storeId;
            $productVariationsModel = new ProductVariationsModel($db);
            $productVariationsModel->store_id = $storeId;
            
            $productId = isset($_REQUEST["product_id"])  ? intval($_REQUEST["product_id"]) : NULL ;
            
            $dateFrom =  date("Y-m-d H:i:s",  strtotime("-2 hour") );
            
            $sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}'";
            if(isset($productId)){
                $sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
            }
            if(isset($_REQUEST['all'])){
            	$sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId}";
            }
            $totalUpdated = 0;
            $queryAP = $db->query($sqlProduct);
            while($rowProduct = $queryAP->fetch(PDO::FETCH_ASSOC)){
                $sqlProducts = "SELECT * FROM module_skyhub_products 
                WHERE store_id = {$storeId} AND product_id = '{$rowProduct['id']}'";
                $queryProducts = $db->query($sqlProducts);
                $products = $queryProducts->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($products)){
                	
                    foreach($products as $kp => $product){
                    	
                    	/**
                    	 * TODO: Criar regra para verificar se houve alteração nos valores q serão enviados
                    	 * Para isso é necessário salvar a quantidade enviada em cada interação para criar um parametro
                    	 * de verificação de alteração.
                    	 * @var Ambiguous $qtd
                    	 */
                    	$qtd = $rowProduct['quantity'] > 0 ? $rowProduct['quantity'] : 0 ;
                        
                        $salePriceModel->sku = trim($product['sku']);
                        $salePriceModel->product_id = $product['product_id'];
                        $salePriceModel->marketplace = "Skyhub";
//                         $salePrice = $salePriceModel->getSalePrice();
//                         $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                        
//                         $salePrice = empty($stockPriceRel['price']) ? $salePrice : number_format($stockPriceRel['price'], 2) ;
                        
                        $salePrice = $salePriceModel->getSalePrice();
                        
                        $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                        
                        $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
                        
                        $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                         
                        if ($rowProduct['blocked'] == "T"){
                        	$qtd = 0;
                        	echo "error|Produto Bloqueado...";
//                         	continue;
                        }
//                         if($storeId == 4){
//                         	$qtd = 0;
//                         }
                        $attributes = [
                            'id' => $rowProduct['id'],
                            'sku' => $rowProduct['sku'],
                            'price_default' => $rowProduct['price'],
                            'promotion_price_default' => $rowProduct['promotion_price'],
                            'qty' => $qtd,
                            'price' => $salePrice,
                            'promotional_price' => 0,
                        ];
                        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $entityInterface */
                        $requestHandler = $api->product();
                        
                        $response = $requestHandler->update(  
                            $product['sku'],
                            $attributes = [
                                'qty' => $qtd,
                                'price' => $salePrice,
                                'promotional_price' => 0
                            ]);
            
                        if( method_exists( $response, 'statusCode' ) ){
                        	
                            if($response->statusCode() == '204'){
                            	
                                $totalUpdated++;
                                $dataLog['update_stock_price_skyhub'] = array(
                                		'request' => $attributes,
                                		'response' => $response->statusCode());
                                
                                $db->insert('products_log', array(
                                		'store_id' => $storeId,
                                		'product_id' => $rowProduct['id'],
                                		'description' => 'Atualização Produto Skyhub',
                                		'user' => $request,
                                		'created' => date('Y-m-d H:i:s'),
                                		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
                                ));
                                
                                echo 'success|';
                                $query = $db->update('module_skyhub_products',
                                		array('store_id','id'),
                                		array($storeId, $product['id']),
                                		array('price' => $salePrice)
                                		);
                                
                                
                            }
                            
                        }else{
                            
                            pre($response->message());
                        }
                    
                        $sqlVariations = "SELECT * FROM module_skyhub_products_variations
                        WHERE store_id = {$storeId} AND id_product = '{$product['id']}'";
                        $queryVariations = $db->query($sqlVariations);
                        $productVariations = $queryVariations->fetchAll(PDO::FETCH_ASSOC);
                        if(!empty($productVariations)){
                            
                            foreach($productVariations as $kv => $productVariation){
                            	
                                echo "productVariation";
                                
                                $selectQtd = "SELECT id, sku, quantity, sale_price, promotion_price, blocked
                                FROM `available_products` WHERE store_id = {$storeId} AND `id` = {$productVariation['product_id']}";
                                $queryQtd = $db->query($selectQtd);
                                $resStockPrice = $queryQtd->fetch(PDO::FETCH_ASSOC);
                                
                                $qtd = $resStockPrice['quantity'] > 0 ? $resStockPrice['quantity'] : 0 ;
                                $salePriceModel->sku = $resStockPrice['sku'];
                                $salePriceModel->product_id = $resStockPrice['id'];
//                                 $salePrice = $salePriceModel->getSalePrice();
//                                 $stockPriceRel = $salePriceModel->getStockPriceRelacional();
//                                 $salePrice = empty($stockPriceRel['price']) ? $salePrice : number_format($stockPriceRel['price'], 2) ;
                                $salePrice = $salePriceModel->getSalePrice();
                                $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                                $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
                                
                                $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                                if ($resStockPrice['blocked'] == "T"){
                                	$qtd = 0;
                                	echo "error|Produto Bloqueado...";
//                                 	continue;
                                }
//                                 if($storeId == 4){
//                                 	$qtd = 0;
//                                 }
                                $attributes = [
                                    'qty' => $qtd,
                                    'price' => $salePrice,
                                    'promotional_price' => 0
                                ];
//                                 pre($product['sku']."  -  ".$productVariation['sku']);
                                $specifications = [
                                    [
                                        'key' => 'price',
                                        'value' => $salePrice,
                                    ],
                                    [
                                        'key' => 'promotional_price',
                                        'value' => 0,
                                    ]
                                ];
                                
                                /** @var \SkyHub\Api\EntityInterface\Catalog\Product $entityInterface */
                                $requestHandler = $api->productVariations();
                                
                                $response = $requestHandler->update(
                                    $productVariation['sku'],
                                        $productVariation['sku'],
                                		$qtd,
                                        NULL,
                                        array(),
                                        $specifications
                                    );
                                
                                if( method_exists( $response, 'statusCode' ) ){
                                    if($response->statusCode() == '204'){
                                        
                                        $totalUpdated++;
                                        $dataLog['update_stock_price_skyhub'] = array(
                                        		'request' => $attributes,
                                        		'response' => $response->statusCode()
                                        );
                                        $db->insert('products_log', array(
                                        		'store_id' => $storeId,
                                        		'product_id' => $resStockPrice['id'],
                                        		'description' => 'Atualização Variação Produto Skyhub',
                                        		'user' => $request,
                                        		'created' => date('Y-m-d H:i:s'),
                                        		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
                                        ));
                                    }
                                    
                                }else{
                                    
                                    pre($product['sku']."  ".$response->message());
                                }
                                
        
                                
                            }
                            
                        }
                        
                    }
                    
                }else{
                	echo "nao existe<br>";
                }
                
            }
            logSyncEnd($db, $syncId, $totalUpdated);
        
        break;
        
        case "export_products":
        	
            $availableProducts = new AvailableProductsModel($db);
            $availableProducts->store_id = $storeId;
            $salePriceModel = new SalePriceModel($db, null, $storeId);
            $salePriceModel->marketplace = "Skyhub";
            $attributesValues = new AttributesValuesModel($db);
            $attributesValues->store_id = $storeId;
            $productModel = new ProductsModel($db);
            $productModel->store_id = $storeId;
            $productVariationsModel = new ProductVariationsModel($db);
            $productVariationsModel->store_id = $storeId;
            $publicationsModel = new PublicationsModel($db);
            $publicationsModel->store_id = $storeId;
            
            $typeRequest = "update";
            if(isset($_REQUEST['type']) AND !empty($_REQUEST['type'])){
                $typeRequest = $_REQUEST['type'];
            }
            
            switch($typeRequest){
                case "enabled": $status = true; break;
                case "disabled": $status = false; break;
                default: $status = true; break; 
                
            }
            $productIds = is_array($productId) ? $productId : array($productId) ;
            
            if(empty($productIds[0])){
            
                $dateFrom =  date("Y-m-d H:i:s",  strtotime("-24 hour") );
                
                $sqlProduct = "SELECT id FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}'";
                
                $queryAP = $db->query($sqlProduct);
                
                $productIds = $queryAP->fetchAll(PDO::FETCH_ASSOC);
            
            }
            foreach($productIds as $ind => $id){
                $id = is_array($id) ? $id['id'] :  $id ;
                $parentId = getParentIdFromId($db, $storeId, $id);
                
                if(!empty($parentId)){
                	
                    $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}' LIMIT 1";
                    $query = $db->query($sql);
                    $products = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach($products as $key => $product){
                    	
                        
                        $images = getUrlImageFromId($db, $storeId,$product['id']);
                        
                        if(!empty($images[0])){
                            
//                             $weight = str_replace(",", ".", $product['weight']);
//                             $height = str_replace(",", ".", $product['height']);
//                             $width = str_replace(",", ".", $product['width']);
//                             $length = str_replace(",", ".", $product['length']);
                            
                            $weight = $product['weight'];
                            $height = $product['height'];
                            $width = $product['width'];
                            $length = $product['length'];
                            
                            /** @var \SkyHub\Api\EntityInterface\Catalog\Product $entityInterface */
                            $entityInterface = $api->product()->entityInterface();
                            
                            $categories =  explode(">", $product['category']);
                            $parentCategory = trim($categories[0]);
                            $childCategory = trim(end($categories));
                            
//                             $price = str_replace(",", ".", $product['sale_price']);
//                             $price = number_format($price, 2, '.', '');
                            $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
                            $salePriceModel->sku = $product['sku'];
                            $salePriceModel->product_id = $product['id'];
                            
                            $salePrice = $salePriceModel->getSalePrice();
                            
                            $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                            
                            $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
                            
                            $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                            
                            if ($product['blocked'] == "T"){
                            	$qtd = 0;
                            	echo "error|Produto Bloqueado... {$product['id']}";
//                             	continue;
                            }
                            
//                             pre($salePrice);
                            
                            $salePriceProduct = $salePrice;
                            $entityInterface->setSku($product['sku'])
                                ->setName($product['title'])
                                ->setDescription(strip_tags($product['description']))
                                ->setStatus($status)
                                ->setQty($qtd)
                                ->setPrice($salePrice)
//                                 ->setPromotionalPrice($product['promotion_price'])
                                ->setCost($product['cost'])
                                ->setWeight($weight)
                                ->setHeight($height)
                                ->setWidth($width)
                                ->setLength($length)
                                ->setBrand($product['brand'])
                                ->setEan($product['ean'])
                                ->addCategory(titleFriendly($parentCategory), $parentCategory)
                                ->addCategory(titleFriendly($childCategory), $product['category']);
                            
                            foreach($images as $i => $image){
                                $entityInterface->addImage($image);
                            }
                            
                            if(isset($product['color']) and !empty(trim($product['color']))){
                                $entityInterface->addVariationAttribute('color')->addSpecification('color', $product['color']);
                            }
                            if(!empty($product['variation_type']) && !empty($product['variation'])){
                                $entityInterface->addVariationAttribute($product['variation_type'])->addSpecification($product['variation_type'], $product['variation']);
                            }
                            $entityInterface->addSpecification('store_stock_cross_docking', 2)
                                ->addSpecification('store_stock_qty', $qtd)
                                ->addSpecification('store_stock_store_code', strtoupper(trim($moduleConfig['store_info']['store'])));
                            
                            $attributesValues->product_id = $product['id'];
                            $resAttributesValues =  $attributesValues->GetAttributesValues();
                            
                            foreach($resAttributesValues as $k => $attrValue){
                                if(!empty($attrValue['attribute']) && trim($attrValue['value']) != ''){
                                    $entityInterface->addSpecification($attrValue['attribute'], $attrValue['value']);
                                }else{
                                    $queryML = $db->query("SELECT name FROM ml_attributes_required WHERE attribute_id LIKE '{$attrValue['attribute_id']}'");
                                    $resML = $queryML->fetch(PDO::FETCH_ASSOC);
                                    if(!empty($resML['name']) && trim($attrValue['value']) != ''){
                                        $entityInterface->addSpecification($resML['name'], $attrValue['value']);
//                                         $resAttributesValues[$k]['attribute'] = $resML['name'];
                                    }
                                }
                            }
                            
                            /** @var \SkyHub\Api\EntityInterface\Catalog\Product\Variation $variation */
                            $sqlVariations = "SELECT * FROM available_products WHERE
                            store_id = {$storeId}  AND  parent_id LIKE '{$product['parent_id']}'";
                            $queryVariations = $db->query($sqlVariations);
                            $variations = $queryVariations->fetchAll(PDO::FETCH_ASSOC);
//                             pre($variations);
                            if(isset($variations[0])){
                                $count = 0;
                                if(count($variations) > 1){
                                    foreach($variations as $k => $productVariation){
//                                         pre($productVariation);
                                        if(!empty($productVariation['id'])){
                                            
                                            $imagesVariations = getUrlImageFromId($db, $storeId, $productVariation['id']);
                                            $qtd = $productVariation['quantity'] > 0 ? $productVariation['quantity'] : 0 ;
                                            $salePriceModel->sku = $productVariation['sku'];
                                            $salePriceModel->product_id = $productVariation['id'];
                                      
                                            $salePrice = $salePriceModel->getSalePrice();
                                            $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                                            $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
                                            
                                            
                                            $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                                            if ($productVariation['blocked'] == "T"){
                                            	$qtd = 0;
                                            	echo "error|Produto Bloqueado...";
//                                             	continue;
                                            }
                                            
                                            $variation = $entityInterface->addVariation(
                                                $productVariation['sku'], 
                                            	$qtd, 
                                                $productVariation['ean']
                                            );
                                            
                                            
                                            foreach($imagesVariations as $j => $imageVariation){
                                                $variation->addImage($imageVariation);
                                            }
                                            
                                            
                                            $variation->addSpecification('price', $salePrice);
                                            
                                            if(trim($productVariation['color'] != '')){
                                                $variation->addSpecification('color', $productVariation['color']);
                                            }
                                            if(!empty($productVariation['variation_type']) && !empty($productVariation['variation'])){
                                                $variation->addSpecification($productVariation['variation_type'], $productVariation['variation']);
                                            }
                                            
                                            $attributesValues->product_id = $productVariation['id'];
                                            $resAttributesValues =  $attributesValues->GetAttributesValues();
                                            foreach($resAttributesValues as $k => $attrValue){
                                                if(!empty($attrValue['attribute']) && !empty($attrValue['value'])){
                                                    $variation->addSpecification($attrValue['attribute'], $attrValue['value']);
                                                }
                                            }
                                            
                                            $productVariations[$count] = array(
                                                'sku' => $productVariation['sku'],
                                                'product_id' => $productVariation['id'],
                                                'parent_id'=> $productVariation['parent_id'],
                                                'ean' => $productVariation['ean']
                                                
                                            );
                                            
                                            if(trim($productVariation['color']) != ''){
                                                $productVariations[$count]['color'] = $productVariation['color'];
                                            }
                                            
                                            if(!empty($productVariation['variation_type']) && !empty($productVariation['variation'])){
                                                $productVariations[$count]['variation_type'] = $productVariation['variation_type'];
                                                $productVariations[$count]['variation'] = $productVariation['variation'];
                                            }
                                           
                                            $count++;
                                            
                                        }
                                    }
                                }
                            }
                            
                           
                        
                            /**
                             * CREATE A PRODUCT
                             *
                             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
                             */
                            
                            if($typeRequest == "update"){
                                
                                
                                $sqlVerify = "SELECT id FROM module_skyhub_products 
                                    WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}'";
                                $queryVerify = $db->query($sqlVerify);
                                $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                                //                             pre($variations);
                                if(isset($verify['id']) && !empty($verify['id'])){
                                    
//                                     pre($entityInterface);
                                    
                                    $response = $entityInterface->update();
                                    
//                                     pre($response);
                                
//                                     $dataLog['update_products_skyhub'] = array(
//                                     		'request' => $entityInterface->export(),
//                                     		'response' => $response->statusCode()
//                                     );
                                   
//                                     $db->insert('products_log', array(
//                                     		'store_id' => $storeId,
//                                     		'product_id' => $product['id'],
//                                     		'description' => 'Atualização Produto Skyhub',
//                                     		'user' => $request,
//                                     		'created' => date('Y-m-d H:i:s'),
//                                     		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT)
//                                     ));
                                
                                }
                                
                            }
                            
                            if($typeRequest == "create"){
                                
                                $response = $entityInterface->create();
                                $dataLog['export_products_skyhub'] = array(
                                		'request' => $entityInterface->export(),
                                		'response' => $response->statusCode()
                                );
                                
                                
                                $db->insert('products_log', array(
                                		'store_id' => $storeId,
                                		'product_id' => $product['id'],
                                		'description' => 'Novo Produto Exportado Skyhub',
                                		'user' => $request,
                                		'created' => date('Y-m-d H:i:s'),
                                		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT)
                                ));
                            }
                            if( method_exists( $response, 'statusCode' ) ){
                                
                                if($response->statusCode() == '201' OR $response->statusCode() == '204'){
                                    
                                    $productModel->sku = $product['sku'];
                                    $productModel->product_id = $product['id'];
                                    $productModel->parent_id = $product['parent_id'];
                                    $productModel->price = $salePriceProduct;
                                    $idProduct = $productModel->save();
                                    
                                    $publicationsModel->publication_code = $idProduct;
                                    $publicationsModel->product_id = $product['id'];
                                    $publicationsModel->sku = $product['sku'];
                                    $publicationsModel->marketplace = 'B2W';
                                    $publicationsModel->user = $request;
                                    $publicationsModel->Save();
                                    
                                    if(!empty($idProduct)){
                                        if(isset($productVariations) AND !empty($productVariations)){
                                           
                                            foreach($productVariations as $j => $variation){
                                            	
                                                $productVariationsModel->id_product = $idProduct;
                                                $productVariationsModel->sku = $variation['sku'];
                                                $productVariationsModel->product_id = $variation['product_id'];
                                                $productVariationsModel->parent_id = $variation['parent_id'];
                                                $productVariationsModel->ean = $variation['ean'];
                                                $productVariationsModel->color = isset($variation['color']) ? $variation['color'] : '' ;
                                                $productVariationsModel->variation_type = $variation['variation_type'];
                                                $productVariationsModel->variation = $variation['variation'];   
                                                $resProductVariations = $productVariationsModel->save();
                                                
                                                
                                                $publicationsModel->publication_code = $idProduct;
                                                $publicationsModel->product_id = $variation['product_id'];
                                                $publicationsModel->sku = $variation['sku'];
                                                $publicationsModel->marketplace = 'B2W';
                                                $publicationsModel->user = $request;
                                                $publicationsModel->Save();
                                            }
                                        }
                                        
//                                         echo "success|Produto atualizado com sucesso!";
                                    }else{
                                        echo "error|Erro ao atualizar produto codigo {$product['id']}";
                                    }
                                    
                                }else{
                                    echo "error|".$response->message();
                                }
                                
                            }else{
                                
                                echo "error|".$response->message();
                                
                                pre($sqlVerify);
                            }
                            
                            $productVariations = array();
                        }
                    
                    }
                
                }else{
                    echo "no parent<br>";
                }
                
            }
            
            break;
            
        case "delete_products":
            
            $productModel = new ProductsModel($db);
            $productModel->store_id = $storeId;
            
            $productIds = is_array($productId) ? $productId : array($productId) ;
//             pre($productIds);die;
            foreach($productIds as $ind => $id){
                    
                    $sql = "SELECT id, sku, product_id FROM module_skyhub_products WHERE store_id = {$storeId} AND product_id = {$id}";
                    $query = $db->query($sql);
                    $products = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach($products as $key => $product){
                    	
                        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $entityInterface */
                        $entityInterface = $api->product()->entityInterface();
                        
                        $entityInterface->setSku($product['sku']);
                        $response = $entityInterface->delete();
                        echo "{$id}|";
                        if( method_exists( $response, 'statusCode' ) ){
                            if($response->statusCode() == '204'){
                                
                            	$productModel->id = $product['id'];
                            	$resProduct = $productModel->Delete();
                               
                            }
                            $db->insert('products_log', array(
                            		'store_id' => $storeId,
                            		'product_id' => $product['product_id'],
                            		'description' => 'Produto Removido Skyhub',
                            		'user' => $request,
                            		'created' => date('Y-m-d H:i:s')
                            ));
                            
                        }else{
                            
                            pre($response->message());
                        }
                        
                    }
                
            }
            
            
            break;
        case "update_status_enabled_product":
            
            
            $productIds = is_array($productId) ? $productId : array($productId) ;
            
            $totalUpdated = 0;
            foreach($productIds as $ind => $id){
                
                $sql = "SELECT sku, product_id FROM module_skyhub_products WHERE store_id = {$storeId} AND product_id = '{$id}'";
                $query = $db->query($sql);
                $products = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach($products as $key => $product){
//                     pre($product);
                    /** @var \SkyHub\Api\EntityInterface\Catalog\Product $entityInterface */
                    $requestHandler = $api->product();
                    
                    $response = $requestHandler->update(
                        $product['sku'],
                        $attributes = [
                            'status' => 'enabled'
                        ]);
                    echo "{$id}|";
                    if( method_exists( $response, 'statusCode' ) ){
                        if($response->statusCode() == '204'){
                            
                            echo "Disponível";
                        }
                        
                    }else{
                        
                        pre($response->message());
                    }
                    
                }
                
            }
            
            break;
        case "update_status_disabled_product":
            
            
            $productIds = is_array($productId) ? $productId : array($productId) ;
            
            $totalUpdated = 0;
            foreach($productIds as $ind => $id){
                
                $sql = "SELECT sku, product_id FROM module_skyhub_products WHERE store_id = {$storeId} AND product_id = {$id}";
                $query = $db->query($sql);
                $products = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach($products as $key => $product){
//                     pre($product);
                    /** @var \SkyHub\Api\EntityInterface\Catalog\Product $entityInterface */
                    $requestHandler = $api->product();
                    
                    $response = $requestHandler->update(
                        $product['sku'],
                        $attributes = [
                            'status' => 'disabled'
                        ]);
                    echo "{$id}|";
                    if( method_exists( $response, 'statusCode' ) ){
                        if($response->statusCode() == '204'){
                            
                            echo "Desabilitado";
                        }
                        
                    }else{
                        
                        pre($response->message());
                    }
                    
                }
                
            }
            
            break;
            
    }
    
}

