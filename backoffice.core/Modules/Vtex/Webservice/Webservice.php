<?php
set_time_limit ( 300 );

$path = dirname(__FILE__);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Rest.php';
require_once $path .'/../Class/class-Vtex.php';
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 17);
    
	switch($action){
	   
	    case "Products":
// 	        die;
	        $Id = isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : null ;
	        
	        $vtex = new Vtex($db, $storeId);
	        
	        if(isset($Id)){
	            $sql = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId} AND Id = {$Id}";
	        }else{
// 	            $sql = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId} AND activationReturnCode IS NULL
//                  AND Id IN ( 
//                     SELECT ProductId as Id FROM module_vtex_variation WHERE store_id = {$storeId} AND httpCode LIKE '200' 
//                     AND activationReturnCode IS NULL GROUP BY ProductId
//                 ) ORDER BY Id DESC";
	            
	            $sql = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId} AND Id IN (
                    
                     SELECT ProductId as Id FROM `module_vtex_variation` WHERE `httpCode` = 200 AND `activationReturnCode` IS NULL
                     
                ) ORDER BY Id DESC";
	            
	            
// 	            $sql = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId} AND Id IN (
//                     SELECT ProductId as Id FROM module_vtex_variation WHERE store_id = {$storeId} AND httpCode LIKE '200'
//                     AND activationReturnCode = 400 GROUP BY ProductId
//                 ) ORDER BY Id DESC";
	
	        }
	        
	        
	        $sql = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId} limit 2";
	        $query = $db->query($sql);
	        
	        $resProduct = $query->fetchAll(PDO::FETCH_ASSOC);
	        
	        pre(count($resProduct));
	        foreach($resProduct as $k => $product){
	            
	            $resProductSpec = $vtex->rest->get("catalog/pvt/product/{$product['Id']}");
	            
	       
	            
	            if(!empty(trim($resProductSpec['body']))){
	                
    	            $data = $resProductSpec['body'];
    	            
    	            pre($data);die;
    	            
    	            if($moduleConfig['input_data'] == 'google_xml'){
    	                $sqlXml = "SELECT * FROM module_google_xml_products WHERE store_id = {$storeId} 
                            AND id LIKE '{$product['RefId']}%' AND product_type IS NOT NULL";
    	                $queryXml = $db->query($sqlXml);
    	                
    	                $dataProduct = $queryXml->fetch(PDO::FETCH_ASSOC);
    	                
    	                if(isset($dataProduct['product_type'])){
    	                    
    	                    $sqlCategory = "SELECT * FROM `module_vtex_categories`
                            WHERE store_id = {$storeId} AND hierarchy LIKE '{$dataProduct['product_type']}'";
    	                    $queryCategory = $db->query($sqlCategory);
    	                    $categories = $queryCategory->fetch(PDO::FETCH_ASSOC);
    	                    
    	                    if(!empty($categories['id_category'])){
    	                        
    	                        $depParts = explode(">", $categories['hierarchy_vtex']);
    	                        $departament = trim($depParts[1]);
    	                        switch($departament){
    	                            case 'Feminino': $DepartamentId = 2; break;
    	                            case 'Masculino': $DepartamentId = 4; break;
    	                            case 'Menina': $DepartamentId = 6; break;
    	                            case 'Menino': $DepartamentId = 8; break;
    	                            default: $DepartamentId = 2 ; break;
    	                        }
    	                        
    	                        $data->DepartmentId = $DepartamentId;
    	                        $data->CategoryId = $categories['id_category'];
        	                    $title = $dataProduct['title'];
        	                    $description = $dataProduct['description'];
        	                    $data->Name = $title;
        	                    $data->LinkId = titleFriendly($title.'-'.$product['RefId']);
        	                    $data->Description = $description;
        	                    $data->IsActive = true;
        	                    $data->IsVisible = true;
        	                    $data->ShowWithoutStock = false;
        	                    $data->MetaTagDescription = $title;
        	                    $data->DescriptionShort = substr($description, 0, 250);
        	                    $data->Title = $title;
        	                    $data = (array) $data;
        	                    
        	                    unset($data['Id']);
        	                    pre($data);
//         	                    $result = $vtex->rest->put("catalog/pvt/product/{$product['Id']}", $data, array());
//     	                        $queryUpdate = $db->update('module_vtex_products',
//     	                            array('store_id', 'Id'),
//     	                            array($storeId, $product['Id']),
//     	                            array('activationReturnCode' => $result['httpCode']));
//         	                    pre($result);
        	                    if($result['httpCode'] == 200){
        	                        
        	                        $sql = "SELECT * FROM module_vtex_variation WHERE store_id = {$storeId} AND ProductId LIKE {$product['Id']}";
            	                    $query = $db->query($sql);
            	                    $resVar = $query->fetchAll(PDO::FETCH_ASSOC);
            	                    foreach($resVar as $k => $variation){
            	                       
            	                        $resVariationSku = $vtex->rest->get("catalog/pvt/stockkeepingunit/{$variation['Id']}");
            	                       
            	                        if(!empty($resVariationSku['body'])){
            	                            
            	                            $variationData = $resVariationSku['body'];
                                            $variationData->IsActive = true;
            	                            $variationData->ActivateIfPossible = true;
            	                            $variationData = (array) $variationData;
            	                            unset($variationData['Videos']);
            	                            
//             	                            $resVariation = $vtex->rest->put("catalog/pvt/stockkeepingunit/{$variation['Id']}", $variationData, array());
            	                            pre($resVariation['httpCode']);
//             	                            if($resVariation['httpCode'] != 200){
//             	                               pre($variationData);
//             	                               pre($resVariation);
//             	                            }
            	                            
            	                            
            	                            
        	                                $queryUpdate = $db->update('module_vtex_variation',
        	                                    array('store_id', 'Id'),
        	                                    array($storeId, $variation['Id']),
        	                                    array('activationReturnCode' => $resVariation['httpCode']));
            	                            
            	                        }
            	                        
            	                    }
            	                    
            	                    
        	                    }
        	                    
    	                    }else{
    	                        echo "error|Sem categoria...\n";
    	                        pre($sqlCategory);
    	                    }
    	                    
    	                }else{
    	                    echo "sem|product_type\n";
    	                    pre($sqlXml);
    	                    $queryUpdate = $db->update('module_vtex_products',
    	                        array('store_id', 'Id'),
    	                        array($storeId, $product['Id']),
    	                        array('activationReturnCode' => 0));
    	                }
    	                
    	            }

	            }
	            
	        }
	        
	        break;
	        
	        
	    case 'export_images':
	        
	        $refId = isset($_REQUEST['refid']) ? $_REQUEST['refid'] : null ;
	        
	        $vtex = new Vtex($db, $storeId);
	        
	        if(isset($refId)){
	            $sql = "SELECT * FROM module_vtex_variation WHERE store_id = {$storeId} AND RefId LIKE '{$refId}%'";
	        }else{
	            $sql = "SELECT * FROM module_vtex_variation WHERE store_id = {$storeId}
                    AND httpCode IS NULL ORDER BY ProductId ASC ";
// 	            $sql = "SELECT * FROM module_vtex_variation WHERE store_id = {$storeId}
//                     AND httpCode != '200' ORDER BY ProductId ASC ";
// 	            $sql = "SELECT * FROM module_vtex_variation WHERE store_id = {$storeId}
//                     AND activationReturnCode = 400 AND httpCode != '200' LIMIT 10";
	        }
	        pre($sql); 
	        $query = $db->query($sql);
	        $resVar = $query->fetchAll(PDO::FETCH_ASSOC);
	        pre(count($resVar));
// 	        pre($resVar);
	        foreach($resVar as $k => $variation){
	            
// 	            $sqlXml = "SELECT * FROM module_google_xml_products WHERE store_id = {$storeId} 
//                 AND id LIKE '{$variation['RefId']}'";
// 	            $queryXml = $db->query($sqlXml);
// 	            $resXml = $queryXml->fetch(PDO::FETCH_ASSOC);
// 	            if(!isset($resXml['image_link'])){
	                $groupId = substr($variation['RefId'], 0, 6);
	                $sqlXml = "SELECT * FROM module_google_xml_products WHERE store_id = {$storeId} 
                    AND item_group_id LIKE '{$groupId}' AND image_link IS NOT NULL LIMIT 1";
	                $queryXml = $db->query($sqlXml);
	                
	                $resXml = $queryXml->fetch(PDO::FETCH_ASSOC);
	                
// 	            }
	            
	            pre($sqlXml);
	            if(isset($resXml['image_link'])){
	                pre($variation);
	                $path = "catalog/pvt/stockkeepingunit/{$variation['Id']}/file";
	                $resDelete = $vtex->rest->delete($path, $params = array());
	                
	                $data = array(
	                    'IsMain' => true,
	                    'Label' => str_replace('.', ' ', $resXml['title']),
	                    'Name' => str_replace('.', ' ', $resXml['title']),
	                    'Url' => $resXml['image_link']
	                );
	                
// 	                pre($data);
	                $resPrincipal = $vtex->rest->post($path, $data, $params = array());
	                pre($resPrincipal);
	                
	                if($resPrincipal['httpCode'] == 200){
	                    
	                    $queryUpdate = $db->update('module_vtex_variation',
	                        array('store_id', 'Id'),
	                        array($storeId, $variation['Id']),
	                        array('sent_img' => 'T', 'httpCode' => $resPrincipal['httpCode']));
	                }else{
	                    
	                    $queryUpdate = $db->update('module_vtex_variation',
	                        array('store_id', 'Id'),
	                        array($storeId, $variation['Id']),
	                        array('httpCode' => $resPrincipal['httpCode']));
	                }
	                
	                for ($i = 0; $i < 8; $i++) {
	                    if(isset($resXml["additional_image_link_{$i}"]) && !empty($resXml["additional_image_link_{$i}"])){
	                        $data = array(
	                            'IsMain' => false,
	                            'Label' => str_replace('.', ' ', $resXml['title']),
	                            'Name' => str_replace('.', ' ', $resXml['title']),
	                            'Url' => $resXml["additional_image_link_{$i}"]
	                        );
// 	                        pre($data);
	                        $result = $vtex->rest->post($path, $data, $params = array());
	                        pre($result);
	                    }
	                }
	                
	            }
	            else{
// 	                echo "sem producto correspondente no google xml {$variation['RefId']}\n";
	                $queryUpdate = $db->update('module_vtex_variation',
	                    array('store_id', 'Id'),
	                    array($storeId, $variation['Id']),
	                    array('sent_img' => 'T'));
	            }
	            
	            
	        }
	        
	        break;
	        
	    case "Product_RefId":
	        
// 	        die;
	        $refId = isset($_REQUEST['refid']) ? $_REQUEST['refid'] : null ;
	        $vtex = new Vtex($db, $storeId);
	        if(isset($refId)){
	            $sqlXml = "SELECT * FROM module_google_xml_products WHERE store_id = {$storeId}
                    AND item_group_id LIKE '{$refId} GROUP BY item_group_id ";
	        }else{
	            $sqlXml = "SELECT * FROM module_google_xml_products WHERE store_id = {$storeId} AND item_group_id NOT IN (
                SELECT RefId as item_group_id FROM module_vtex_products WHERE store_id = {$storeId} GROUP BY RefId
                ) GROUP BY item_group_id";
	        }
	        $queryXml = $db->query($sqlXml);
	        
	        $productsXml = $queryXml->fetchAll(PDO::FETCH_ASSOC);
	        
	        pre(count($productsXml));
	        
	        foreach ($productsXml as $k => $ProductXml){
	            
	            $resProductRefId = $vtex->rest->get("catalog_system/pvt/products/productgetbyrefid/{$ProductXml['item_group_id']}");
	            
	            if(!empty($resProductRefId['body'])){
	                
	                $productData = $resProductRefId['body'];
	                pre($productData);die;
// 	                if(isset($productData->Id)){
    	                
//     	                $sqlVerify = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId} AND  Id = {$productData->Id}";
//     	                $queryVerify = $db->query($sqlVerify);
//     	                $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
    	               
//     	                if(!isset($resVerify['Id'])){
    	                
//         	                $data = array(
//         	                    'Id' => $productData->Id,
//         	                    'store_id' => $storeId,
//         	                    'RefId' => $productData->RefId,
//         	                    'Name' => $productData->Name,
//         	                    'Title' =>$productData->Title,
//         	                    'DepartmentId' => $productData->DepartmentId,
//         	                    'CategoryId' => $productData->CategoryId,
//         	                    'LinkId' => $productData->LinkId
//         	                );
//         	                pre(array('product' =>$data));
//         	                $query = $db->insert('module_vtex_products', $data);
//     	                }
    	                
//     	                $resProductId = $vtex->rest->get("catalog_system/pvt/sku/stockkeepingunitByProductId/{$productData->Id}");
//     	                if(!empty($resProductId['body'])){
    	                    
//     	                    $ids = $resProductId['body'];
//     	                    foreach ($ids as $i => $variationSku){
    	                        
//     	                        $variations = $vtex->rest->get("catalog_system/pvt/sku/stockkeepingunitbyid/{$variationSku->Id}");
    	                        
//     	                        if(!empty($variations['body'])){
    	                            
//         	                        $variationData = $variations['body'];
        	                        
//         	                        if(isset($variationData->Id)){
//             	                        $sqlVerifyVar = "SELECT * FROM module_vtex_variation WHERE store_id = {$storeId} AND Id = {$variationData->Id}";
//             	                        $queryVerifyVar = $db->query($sqlVerifyVar);
//             	                        $resVerifyVar = $queryVerifyVar->fetch(PDO::FETCH_ASSOC);
//             	                        if(!isset($resVerifyVar['Id'])){
            	                        
//                 	                        $variation = array(
//                 	                            'Id' => $variationData->Id,
//                 	                            'store_id' => $storeId,
//                 	                            'ProductId' => $variationData->ProductId,
//                 	                            'RefId' => $variationData->AlternateIds->RefId,
//                 	                            'NameComplete' => $variationData->NameComplete,
//                 	                            'ProductName' => $variationData->ProductName,
//                 	                            'Ean' => $variationData->AlternateIds->Ean,
//                 	                            'SkuName' => $variationData->SkuName,
//                 	                            'ImageUrl' => $variationData->ImageUrl
//                 	                        );
//                 	                        pre(array('variation' => $variation));
//                 	                        $query = $db->insert('module_vtex_variation', $variation);
            	                        
//             	                        }
        	                        
//         	                        }else{
//         	                            pre($variations);
//         	                        }
        	                        
//     	                        }
    	                        
//     	                    }
    	                    
//     	                }
    	                
//     	            }
    	            
	            }
	            
	        }
	        
	        break;
	        
	    case "add_category_relationship":
	        
	        
	        $hierarchy = $_REQUEST['hierarchy'];
	        
	        $idCategory = $_REQUEST['id_category'];
	        
	        $categoryVtex = $_REQUEST['category_vtex'];
	        
	        
	        if(!empty($hierarchy)){
	            
	            $sqlCategory = "SELECT * FROM `module_vtex_categories`
                WHERE store_id = {$storeId} AND hierarchy LIKE '{$hierarchy}'";
	            
	            $queryCategory = $db->query($sqlCategory);
	            
	            $categories = $queryCategory->fetch(PDO::FETCH_ASSOC);
	            
	            if(!empty($categories['hierarchy'])){
	                if($hierarchy == $categories['hierarchy']){
	                    
	                    $query = $db->update('module_vtex_categories',
	                        array('store_id', 'hierarchy'),
	                        array($storeId, $hierarchy),
	                        array('id_category' => $idCategory,
	                            'hierarchy_vtex' => $categoryVtex)
	                        );
	                    
	                }
	            }else{
	                
	                if($idCategory){
	                    
	                    $query = $db->insert('module_vtex_categories', array(
	                        'store_id' => $storeId,
	                        'hierarchy' => $hierarchy,
	                        'id_category' => $idCategory,
	                        'hierarchy_vtex' => $categoryVtex
	                    ));
	                }
	            }
	            
	            if($query){
	                echo "success|Relacionamento cadastrado com successo!";
	            }else{
	                echo "error|Erro ao relacionar categoria";
	            }
	            
	        }
	        
        break;
	        
	        
	        
	        
// 	    case "import_ids" :

// 	        $vtex = new Vtex($db, $storeId);
// 	        $from =1;
// 	        $to = 50;
// 	        do{
// 	            pre("catalog_system/pvt/products/GetProductAndSkuIds?_to={$to}&_from=".($from)."");
// 	            $resultIds = $vtex->rest->get("catalog_system/pvt/products/GetProductAndSkuIds?_to={$to}&_from=".($from)."");
//     	        pre($resultIds);
//     	        foreach($resultIds['body']->data as $k => $ids){
    	            
//     	            $products = $vtex->rest->get("catalog/pvt/product/{$k}");
    	            
//     	            $productData = $products['body'];
    	            
//     	            $data = array(
//     	                'Id' => $productData->Id,
//     	                'store_id' => $storeId, 
//     	                'RefId' => $productData->RefId,
//     	                'Name' => $productData->Name,
//     	                'Title' =>$productData->Title,
//     	                'DepartmentId' => $productData->DepartmentId,
//     	                'CategoryId' => $productData->CategoryId,
//     	                'LinkId' => $productData->LinkId
//     	            );
    	            
//     	            $query = $db->insert('module_vtex_products', $data);
    	            
//     	            $dataVariation = array();
    	            
//     	            foreach ($ids as $i => $variationSku){
    	                
//     	                $variations = $vtex->rest->get("catalog_system/pvt/sku/stockkeepingunitbyid/{$variationSku}");
    	                
//     	                $variationData = $variations['body'];
    	                
//     	                $variation = array(
//     	                    'Id' => $variationData->Id,
//     	                    'store_id' => $storeId, 
//     	                    'ProductId' => $variationData->ProductId,
//     	                    'RefId' => $variationData->AlternateIds->RefId,
//     	                    'NameComplete' => $variationData->NameComplete,
//     	                    'ProductName' => $variationData->ProductName,
//     	                    'RefId' => $variationData->AlternateIds->RefId,
//     	                    'Ean' => $variationData->AlternateIds->Ean,
//     	                    'SkuName' => $variationData->SkuName,
//     	                    'ImageUrl' => $variationData->ImageUrl
//     	                );
//     	                $dataVariation[] = $variation;
    	                
//     	                $query = $db->insert('module_vtex_variation', $variation);
    	                
//     	            }
// //     	            $data['variations'] = $dataVariation;
    	            
// //     	            pre($data);
    	            
//     	        }
//     	        $from = $from + $to;
//     	        $to = $from + 50; //$resultIds['body']->range->to;
//     	        pre($from);
//     	        pre($to);
//     	        echo $total = $resultIds['body']->range->total;
    	        
// 	        }while($from <= $total);
	        
	        
	        
// 	        break;
	        
// 	    case "Product_Specifications":
	        
// 	        $vtex = new Vtex($db, $storeId);
	        
// 	        $sql = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId}";
	        
// 	        $query = $db->query($sql);
	        
// 	        $resProduct = $query->fetchAll(PDO::FETCH_ASSOC);
	        
// 	        foreach($resProduct as $k => $product){
	            
// 	            pre($product);
	            
// 	            pre("catalog_system/pvt/products/{$product['Id']}/specification");
	            
// 	            $resProductSpec = $vtex->rest->get("catalog_system/pvt/products/{$product['Id']}/specification");
	            
// 	            pre($resProductSpec);
	            
// 	        }
	        
// 	        break;
	        
// 	    case "Variation_Specifications":
	        
// 	        $vtex = new Vtex($db, $storeId);
	        
// 	        $sql = "SELECT * FROM module_vtex_variation WHERE store_id = {$storeId} LIMIT 10";
	        
// 	        $query = $db->query($sql);
	        
// 	        $resVar = $query->fetchAll(PDO::FETCH_ASSOC);
	        
// 	        foreach($resVar as $k => $variation){
	            
// 	            pre($variation);
	            
// 	            pre("catalog/pvt/stockkeepingunit/{$variation['Id']}/specification");
	            
// 	            $resSpec = $vtex->rest->get("catalog/pvt/stockkeepingunit/{$variation['Id']}/specification");
	            
// 	            pre($resSpec);
	            
// 	        }
	        
// 	        break;
	        
// 	    case "Variation":
	        
	        
// 	        $skuId = isset($_REQUEST['skuid']) ? $_REQUEST['skuid'] : null ;
	        
// 	        pre("catalog/pvt/stockkeepingunit/{$skuId}");
	        
// 	        $resSpec = $vtex->rest->get("catalog/pvt/stockkeepingunit/{$skuId}");
	        
// 	        pre($resSpec);
	        
	        
// 	        break;
		    
	}
	
}

