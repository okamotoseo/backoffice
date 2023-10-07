<?php

set_time_limit ( 300 );
$path = dirname(__FILE__);
// header("Content-Type: text/html; charset=ISO");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/ProductDescriptionModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/functions.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$type = isset($_REQUEST["type"]) && $_REQUEST["type"] != "" ? $_REQUEST["type"] : 'single' ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;

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
        
        case "update_published":
            $published = array();
            
            foreach($published as $k => $sku){
                
                if(!empty($sku)){
                    $queryRes = $db->update('module_shopee_products',
                        array('store_id', 'sku'),
                        array($storeId, $sku),
                        array('published' => 'T')
                        );
                    
                }
                
            }
            break;
            
        case "add_all_products_xml":
            $j = 0;
            $fileName = "products_shopee.csv";
            $fp = fopen($path ."/../../../Views/_uploads/store_id_{$storeId}/csv/{$fileName}", 'a+');
            
            ftruncate($fp, 0);
            
//             $categoryBlocked = array();
//             $categoryEnabled = array('22880');

            $categoryEnabled = array();
            
            $parents = array();
            $total = 0 ;
            $regCount = 0;
            $sql = "SELECT * FROM `module_google_xml_products` WHERE store_id = {$storeId}
            AND product_type IN (SELECT hierarchy as product_type FROM module_shopee_categories_xml_relationship WHERE store_id = {$storeId})
            AND id NOT IN (SELECT product_id as id FROM module_shopee_products WHERE store_id = {$storeId} AND published = 'T')";
            $query = $db->query($sql);
            $products = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($products[0])){
                
                foreach($products as $key => $product){
                    pre($product);
                    $sql = "SELECT  module_shopee_categories_xml_relationship.*  FROM  module_shopee_categories_xml_relationship
           			WHERE module_shopee_categories_xml_relationship.store_id = {$storeId} AND
                    module_shopee_categories_xml_relationship.hierarchy LIKE '{$product['product_type']}'";
                    $query = $db->query($sql);
                    $categoryRel = $query->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($categoryRel['id_category'])){
                        
                        if(empty($categoryEnabled) OR in_array($categoryRel['id_category'], $categoryEnabled)){
                            
                            if(!empty($product['image_link'])){
                                
                                $maxImage = 8;
                                
                                $imagePrincipal = $product['image_link'];
                                
                                $imagesText = $product['image_link'].";".$product['image_link'] ;
                                for ($i = 0; $i < $maxImage; $i++) {
                                    $imagesText .= isset($product["additional_image_link_{$i}"]) && !empty($product["additional_image_link_{$i}"]) ? "{$product["additional_image_link_{$i}"]};" : ";" ;
                                    
                                }
                                $j++;
                                
                                $qtd = 1;
                                
                                $salePrice = $product['price'];
                                
                                if($qtd > 0){
                                    
                                    if(!isset($parents[$product['item_group_id']])){
                                        $parents[$product['item_group_id']] = 1;
                                        $total++;
                                    }
                                    
                                    $peso = isset($product['shipping_weight']) ? $product['shipping_weight']/1000 : 1 ;
                                    $h = isset( $product['height']) ? ceil( $product['height']) : 20;
                                    $w = isset( $product['width']) ? ceil( $product['width']) : 20 ;
                                    $l = isset( $product['length']) ? ceil( $product['length']) : 20 ;
                                    
                                    $description = strip_tags($product['description']);
                                    $description = str_replace(';', ' ',$description);
                                    $description = str_replace('*',  ' ',$description);
                                    $description = str_replace('•', ' -',$description);
                                    $description = str_replace('\n', ' ',$description);
                                    $description = str_replace('\R', ' ',$description);
                                    $description = str_replace('\r', ' ',$description);
                                    $description = str_replace('\t', ' ',$description);
                                    $description = str_replace('&nbsp', ' ', $description);
                                    $description = str_replace('  ',  ' ',$description);
                                    $description = trim(preg_replace('/\s\s+/', ' ', $description));
                                    
                                    $description = substr($description, 0, 5000);
                                    
                                    $title = substr($product['title'], 0, 255);
                                    
                                    echo "{$product['id']} - {$qtd} - {$salePrice} - {$title}<br>";
                                    
                                    $csvRow = "{$categoryRel['id_category']};{$title};{$description};{$product['item_group_id']};{$product['item_group_id']};Cor;{$product['color']};{$imagePrincipal};Tamanho;{$product['size']};{$salePrice};{$qtd};{$product['id']};{$imagesText}{$peso};{$w};{$l};{$h};Ativar;;\n";
                                    $csvRow = iconv(mb_detect_encoding($csvRow), 'Windows-1252//TRANSLIT', $csvRow);
                                    fwrite($fp, $csvRow);
                                    $regCount++;
                                    
                                    $sqlVerify = "SELECT * FROM module_shopee_products WHERE store_id = {$storeId}
                                    AND sku LIKE '{$product['id']}'";
                                    $queryVerify = $db->query($sqlVerify);
                                    $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                                    pre($resVerify);
                                    if(!isset($resVerify['id'])){
                                        $queryRes = $db->insert('module_shopee_products', array(
                                            'store_id' => $storeId,
                                            'product_id' => $product['id'],
                                            'sku' => $product['id'],
                                            'parent_id' => $product['item_group_id'],
                                            'id_category' => $categoryRel['id_category'],
                                            'created' => date("Y-m-d H:i:s")
                                        ));
                                    }else{
                                        $queryRes = $db->update('module_shopee_products',
                                            array('store_id', 'id'),
                                            array($storeId, $resVerify['id']),
                                            array('product_id' => $product['id'],
                                                'parent_id' => $product['item_group_id'],
                                                'id_category' => $categoryRel['id_category']
                                            ));
                                        
                                    }
                                }
                            }
                        }
                    }
//                     if($regCount == 44){
//                         break;
//                     }
                    
                }
                if(fclose($fp)){
                    echo "success|{$total}|<a href='https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/csv/{$fileName}?".time()."'>{$fileName}</a>";
                }else{
                    echo "error|Erro ao gerar arquivo csv do relatório 1";
                }
            }else{
                echo "error|Erro ao gerar arquivo csv do relatório";
            }
            break;
            
    }
    
    
    
    
}