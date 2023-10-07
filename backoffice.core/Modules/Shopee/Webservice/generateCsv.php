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
        
        case "update_published":
            $published = array('5143', '1792', '5185', '10001', '6081', '524', '5936', '1861', '7781', '7784', '6825', '200051851', '6808', '1918', '86', '10011', '200060230', '393', '178', '4561', '4558', '4559', '4560', '4556', '1720', '200050129', '5966', '1928', '5947', '542', '543', '4478', '1745', '1917', '4590', '821', '629', '630', '1890', '200052764', '200504', '545', '3993', '1915', '10011', '7785', '2587', '5936', '2004201', '3796', '6827', '1923', '4908', '1789', '200062582', '200063220', '1719', '1916', '2610', '1085', '6828', '6341', '3991', '5793', '4481', '4906', '200052769', '6074', '2004906', '6074', '6823', '6521', '200059728', '85', '4428', '2004908', '5174', '5880', '5876', '5877', '5878', '5879', '631', '4285', '583', '4620', '632', '1979', '4433', '4169', '6883', '3991', '1861', '4436', '5782', '5784', '5154', '5166', '2588', '999', '180', '5146', '2004909', '607', '20008891', '5727', '6206', '821', '1787', '85', '200051851', '1764', '5722', '1021', '938', '939', '84', '1810', '4578', '4579', '4580', '4581', '4582', '4583', '4890', '4632', '4633', '2000148160040', '1812', '2692', '200050027', '6925', '6928', '6929', '6930', '6931', '6926', '6927', '6924', '5167', '6714', '6715', '1590', '894', '895', '2004907', '4520', '6706', '4907', '6079', '5729', '997', '4285', '2200', '7786', '6206', '84', '494', '6922', '6923', '6918', '6919', '6916', '6917', '6920', '6921', '547', '7840', '7839', '6267', '631', '2001430', '1088', '545', '4906', '1916', '178', '6520', '7544', '2609', '4729', '604', '605', '5971', '200060186', '583', '4254', '1035', '6809', '1037', '4257', '5726', '5172', '6341', '200051703', '4460', '4461', '4458', '4459', '5948', '7562', '5781', '5148', '7774', '7777', '5884', '5777', '5787', '5156', '1070', '3947', '6195', '2421', '2608', '1037', '5966', '2245', '4482', '4551', '4552', '4553', '4554', '4555', '6567', '5992', '5728', '1890', '1927', '2718', '2719', '6078', '1852', '4487', '7786', '4304', '5950', '200809', '1021', '3947', '5171', '547', '200050584', '930', '1924', '5724', '607', '4476', '4432', '2422', '5145', '1919', '3992', '4718', '3955', '4718', '894', '895', '200052821', '4483', '3994', '5947', '1926', '5970', '254', '5809', '2275', '1040', '4485', '3944', '5104', '6268', '5179', '2000148160040', '5152', '200050039', '1070', '6328', '3908', '6367', '120', '629', '630', '1120', '4970', '4971', '5374', '6328', '4545', '4546', '4547', '4548', '4549', '4550', '4892', '5150', '2690', '4437', '5147', '498', '5165', '7525', '5972', '4280', '5747', '3993', '524', '5153', '6882', '6073', '998', '2724', '2721', '997', '4516', '4433', '1921', '1088', '6933', '6934', '6935', '254', '2612', '200050584', '546', '120', '6888', '4518', '649', '771', '3966', '542', '543', '4495', '547', '4909', '1035', '4491', '5151', '494', '6716', '7459', '5774', '5776', '1040', '5962', '606', '4488', '6882', '5950', '7525', '5285', '5178', '200504', '6938', '6939', '200815', '6076', '6244', '5082', '545', '200050583', '938', '939', '4909', '180', '5173', '393', '2000149410022', '2200', '999', '2610', '1035', '4543', '632', '200052767', '930', '5872', '5873', '5874', '5875', '4435', '5781', '998', '604', '605', '498', '1921', '649', '771', '20008891', '5721');
                
            
                foreach($published as $k => $sku){
                    
                    if(!empty($sku)){
                        $queryRes = $db->update('module_shopee_products', 
                            array('store_id', 'sku'),
                            array($storeId, $sku),
                            array('published' => 'T')
                        );
                        
//                         pre($sku);
                        
                    
                    }
                    
                    
                }
            
            break;
    	
        case "add_all_available_products":
            
            $salePriceModel = new SalePriceModel($db, null, $storeId);
            $salePriceModel->marketplace = "Shopee";
            $productImages = array();
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
//             $sql = "SELECT * FROM `available_products` WHERE store_id = {$storeId}
//    			AND ean != '' and quantity > 3 AND parent_id IS NOT NULL AND variation != '' AND blocked != 'T'
//             AND id NOT IN (SELECT product_id as id FROM product_relational WHERE store_id = {$storeId})";
            $sql = "SELECT * FROM `available_products` WHERE store_id = {$storeId}
   			AND ean != '' and quantity > 3 AND parent_id IS NOT NULL AND variation != ''
            AND id NOT IN (SELECT product_id as id FROM module_shopee_products WHERE store_id = {$storeId} AND published = 'T')
       		AND id NOT IN (SELECT product_id as id FROM product_relational WHERE store_id = {$storeId})";
            $query = $db->query($sql);
            $products = $query->fetchAll(PDO::FETCH_ASSOC);
            
            if(!empty($products[0])){
                
                foreach($products as $key => $product){
                   
                    
                    $sql = "SELECT  module_shopee_categories_relationship.*  FROM  module_shopee_categories_relationship
           			WHERE module_shopee_categories_relationship.store_id = {$storeId} AND 
                    module_shopee_categories_relationship.hierarchy LIKE '{$product['category']}'";
                    $query = $db->query($sql);
                    $categoryRel = $query->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($categoryRel['id_category'])){
                        
                        if(empty($categoryEnabled) OR in_array($categoryRel['id_category'], $categoryEnabled)){
                            
                            $parentImages = getUrlImageFromParentId($db, $storeId, $product['parent_id']);
                            
                            if(!empty($parentImages[0])){
                                
                                $productImages[$j]['sku'] = $product['sku'];
                                $productImages[$j]['title'] = $product['sku'];
                                $productImages[$j]['color'] = $product['color'];
                                $productImages[$j]['variation'] = $product['variation'];
                                $productImages[$j]['thumbnail'] = "<img src='{$product['thumbnail']}' />";
                                
                                $images = getProductImageShopee($db, $storeId, $product['id']);
                                
//                                 $images = getUrlImageFromId($db, $storeId, $product['id']);
                                pre($images);
                                
                                $maxImage = 8;
                                
//                                 $images[0] = isset($images[0]) && !empty($images[0]) ? str_replace('https:', 'http:', $images[0]) : "" ;
                                
                                $imagePrincipal = isset($images[0]) && !empty($images[0]) ? "{$images[0]}" : " " ;
                                
//                                 $imagePrincipal = str_replace('https:', 'http:', $imagePrincipal);
                                
                                $imagesText = isset($images[0]) && !empty($images[0]) ? "{$images[0]};"  : " ;" ;
                                pre($images);
                                for ($i = 0; $i < $maxImage; $i++) {
                                    if(isset($images[$i])){
//                                         $images[$i] = str_replace('https:', 'http:', $images[$i]);
    //                                     $images[$i] = isset($images[$i]) && !empty($images[$i]) ? str_replace('https:', 'http:', $images[$i]) : "" ;
                                        if(!empty($images[$i])){
                                            $productImages[$j]['images'][] = "<a href='{$images[$i]}' target='_blank'>{$images[$i]}</a>";
                                            
                                        }
                                    }
                                    echo !empty($images[$i]) ? "{$i} - {$images[$i]};" : "{$i} - {$images[0]};";
                                   echo "<br><br>";
                                   $imagesText .= !empty($images[$i]) ? "{$images[$i]};" : "{$images[0]};" ;
                                   
                                }
                                $j++;
                                
                                $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
                                
                                $salePriceModel->sku = trim($product['sku']);
                                
                                $salePriceModel->product_id = $product['id'];
                                
                                $salePrice = $salePriceModel->getSalePrice();
                                
                                $stockPriceRel = $salePriceModel->getStockPriceRelacional();
                                
                                $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
                                
                                $salePrice = ceil($salePrice) - 0.10;
                                
                                $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
                                
                                if ($product['blocked'] == "T"){
                                    $qtd = 0;
                                }
                                
                                if($qtd > 0){
                                    
//                                     $pathShow = "https://backoffice.sysplace.com.br/Modules/Shopee/img/";
//                                     $pathSave = "/var/www/html/app_mvc/Modules/Shopee/img/";
//                                     $imgVarPath = $imagePrincipal;
//                                     $imgVarPath = str_replace('https://backoffice.sysplace.com.br/', '/var/www/html/app_mvc/', $imgVarPath);
//                                     $partsFileName = explode('/', $imagePrincipal);
//                                     $partsName = explode(".", end($partsFileName));
//                                     $imageOutput = $pathShow.$product['id'].'.'.end($partsName);
//                                     $imageInput = $pathSave.$product['id'].'.'.end($partsName);
//                                     createImage($imgVarPath, $imageInput, 1, 1);
                                    
                                    if(!isset($parents[$product['parent_id']])){
                                        $parents[$product['parent_id']] = 1;
                                        $total++;
                                    }
                                    
                                    $peso = ceil($product['weight']);
                                    $h = ceil( $product['height']);
                                    $w = ceil( $product['width']);
                                    $l = ceil( $product['length']);
                                    
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
                                    $variationType = ucfirst(strtolower($product['variation_type']));
                                    
                                    echo "{$product['sku']} - {$qtd} - {$salePrice} - {$title}<br>";
                                    
                                    $csvRow = "{$categoryRel['id_category']};{$title};{$description};{$product['parent_id']};{$product['parent_id']};Cor;{$product['color']};{$imagePrincipal};{$variationType};{$product['variation']};{$salePrice};{$qtd};{$product['sku']};{$imagesText}{$peso};{$w};{$l};{$h};Ativar;;\n";
                                    $csvRow = iconv(mb_detect_encoding($csvRow), 'Windows-1252//TRANSLIT', $csvRow);
                                    fwrite($fp, $csvRow);
                                    $regCount++;
                                    
                                    $sqlVerify = "SELECT * FROM module_shopee_products WHERE store_id = {$storeId} 
                                    AND sku LIKE '{$product['sku']}'";
                                    $queryVerify = $db->query($sqlVerify);
                                    $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                                    if(!isset($resVerify['id'])){
                                        $queryRes = $db->insert('module_shopee_products', array(
                                            'store_id' => $storeId,
                                            'product_id' => $product['id'],
                                            'sku' => $product['sku'],
                                            'parent_id' => $product['parent_id'],
                                            'id_category' => $categoryRel['id_category'],
                                            'created' => date("Y-m-d H:i:s")
                                        ));
                                    }else{
//                                         $queryRes = $db->update('module_shopee_products', 
//                                             array('store_id', 'id'), 
//                                             array($storeId, $resVerify['id']), 
//                                             array('product_id' => $product['id'],
//                                                 'parent_id' => $product['parent_id'],
//                                                 'id_category' => $categoryRel['id_category']
//                                             ));
                                        
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
                    echo "success|{$total}|<a href='https://backoffice.sysplace.com.br/Views/_uploads/store_id_4/csv/{$fileName}?".time()."'>{$fileName}</a>";
                }else{
                    echo "error|Erro ao gerar arquivo csv do relatório";
                }
            }else{
                echo "error|Erro ao gerar arquivo csv do relatório";
            }
            break;
    	    
    }
    
    
    
    
}