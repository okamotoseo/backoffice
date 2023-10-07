<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Soap.php';
require_once $path .'/../Models/Catalog/ProductsModel.php';
require_once $path .'/../Models/Catalog/AttributesModel.php';
require_once $path .'/../Models/Catalog/CategoriesModel.php';
require_once $path .'/../Models/Catalog/InventoryModel.php';
require_once $path .'/../Models/Products/ProductsTempModel.php';
require_once $path .'/../Models/Products/AttributesRelationshipModel.php';
require_once $path .'/../Models/Products/AttributesValuesModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
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
    if(isset($_SERVER ['argv'] [3])){
        $paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
        $accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
    }
    
    $request = "System";
    
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
	switch($action){
	    
	        
	    case "import_products_ids" :
	        
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Importação de Códigos e Categorias", $request);
	        $count = 1;
	        $productsModel = new ProductsModel($db, null, $storeId);
	        $products = $productsModel->catalogProductList();
	        logSyncEnd($db, $syncId, $count);
	        $categoriesModel = new CategoriesModel($db, null, $storeId);
	        $idToUse = array(27, 44, 49, 55,73, 100, 83, 117, 554, 109, 112, 145);
	        foreach($products as $key => $product){
	            pre($product);
// 	            $categoriesModel->categories = $product->category_ids;
                
//                 $hierarchy = $categoriesModel->catalogCategoryHierarchy();
//                 $category = '';
//                 foreach($hierarchy as $ind => $name){
//                     $parts = explode(">", $name);
//                     $sql = "SELECT * FROM onbi_categories_relationship WHERE  store_id = ? AND onbi_parent_id = 2 AND onbi_name LIKE ? ";
//                     $query = $db->query($sql, array($storeId, trim($parts[0])));
//                     $res = $query->fetch(PDO::FETCH_ASSOC);

//                     if(in_array($res['onbi_category_id'], $idToUse)){

//                         $category = $hierarchy[$ind];
//                         break;

//                     }

//                 }
//                 if(empty($category)){
//                     pre($hierarchy);
//                     echo "Sem categoria";
//                     echo "<br><br>";die;
//                 }else{
//                     echo $category;
//                     echo "<br><br>";
//                 }die;
                
//                 $category = isset($hierarchy[1]) ? trim($hierarchy[1]) : "" ;
                
// //                 echo $key." - ".$product->product_id." - ".$category."<br>";
                
// 	            $categoryJson = json_encode($product->category_ids);
	            
// 	            $websitesJson = json_encode($product->website_ids);
	            
// 	            $sqlVerify = "SELECT product_id FROM module_onbi_products_tmp 
//                 WHERE store_id = {$storeId} AND product_id = '{$product->product_id}' ";
// 	            $verifyQuery = $db->query($sqlVerify);
// 	            $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	            
// 	            if(!isset($verify['product_id'])){
// 	                if(!empty($category)){
//         	            $query = $db->insert("module_onbi_products_tmp", array(
//         	                "store_id" => $storeId,
//         	                "product_id" => $product->product_id,
//         	                "sku" => $product->sku,
//         	                "title" => $product->name,
//         	                "set_attribute" => $product->set,
//         	                "type" => $product->type,
//         	                "categories_ids" => $categoryJson,
//         	                "websites" => $websitesJson,
//         	                "category" => $category
        	                
//         	            ));
// 	                }else{
// 	                    $query = $db->insert("module_onbi_products_tmp", array(
// 	                        "store_id" => $storeId,
// 	                        "product_id" => $product->product_id,
// 	                        "sku" => $product->sku,
// 	                        "title" => $product->name,
// 	                        "set_attribute" => $product->set,
// 	                        "type" => $product->type,
// 	                        "categories_ids" => $categoryJson,
// 	                        "websites" => $websitesJson
	                        
// 	                    ));
	                    
// 	                }
//     	            if(!$query){
//     	                pre($query);
//     	            }
	            
// 	            }
// 	            if(isset($verify['product_id'])){
// 	                if(!empty($category)){
//     	                $query = $db->update("module_onbi_products_tmp",
//     	                    array("store_id", "product_id"),
//     	                    array($storeId, $verify['product_id']),
//     	                    array("sku" => $product->sku,
//         	                    "title" => $product->name,
//         	                    "set_attribute" => $product->set,
//         	                    "type" => $product->type,
//         	                    "categories_ids" => $categoryJson,
//     	                        "websites" => $websitesJson,
//     	                        "category" => $category
//     	                ));
// 	                }else{
// 	                    $query = $db->update("module_onbi_products_tmp",
// 	                        array("store_id", "product_id"),
// 	                        array($storeId, $verify['product_id']),
// 	                        array("sku" => $product->sku,
// 	                            "title" => $product->name,
// 	                            "set_attribute" => $product->set,
// 	                            "type" => $product->type,
// 	                            "categories_ids" => $categoryJson,
// 	                            "websites" => $websitesJson
// 	                        ));
	                    
// 	                }
// 	                if(!$query){
// 	                    pre($query);
// 	                }
	                
// 	            }
// 	            $count++;

	        }
	        logSyncEnd($db, $syncId, $count);
	        echo "Total de produtos atualizados: {$count}";

	        break;
	        //product tmp category
	    case "import_categories_hierarchy":
	        
	        $categoriesModel = new CategoriesModel($db, null, $storeId);
	        
	        $categoriesIds = $categoriesModel->ListCategoriesIds();
	        
	        foreach($categoriesIds as $key => $categories){
	            
	            $categories_ids = json_decode($categories['categories_ids']);
	            
	            $categoriesModel->categories = $categories_ids;
            
	            $hierarchy = $categoriesModel->catalogCategoryHierarchy();
	            
	            echo $categories['product_id']." - ".$hierarchy[1]."<br>";
	            
	            if(isset($hierarchy[1])){
	                
	                $query = $db->update('module_onbi_products_tmp', 
	                    array("store_id", "product_id"), 
	                    array($storeId, $categories['product_id']), 
	                    array('category' => $hierarchy[1])
	                    );
	            }else{
	                //TODO: erro nao localizou categoria hierarchy
	                pre($hierarchy);
	                pre($categories);
	            }

// 	            die;
	        }
	        
	        break;
	        
	        
	    case "import_products_info" :
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Importação de Attributos dos Produtos", $request);
	        $count = 1;
	        $sql = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId}";
// 	        $sql .= isset($_REQUEST['product_id']) ? " AND product_id = {$_REQUEST['product_id']}" : "";die;
	        $query = $db->query($sql);
	        $productsIds = $query->fetchAll(PDO::FETCH_ASSOC);
	        $productsModel = new ProductsModel($db, null, $storeId);
	        
	        $productsTempModel = new ProductsTempModel($db, null, $storeId);
	        
	        $attributesRelationshipModel = new AttributesRelationshipModel($db, null, $storeId);
	        
	        $attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
	       
	        $additionalAttributes = $attributesRelationshipModel->GetAttributesToImport();
	        
	        $attributesRelationship = $attributesRelationshipModel->GetAttributesRelationship();
	        
	        foreach($productsIds as $key => $ids){
	            
	            $productsModel->product_id = $ids['product_id'];
	            
	            $productsModel->additional_attributes = $additionalAttributes;
	            
	            $product = $productsModel->catalogProductInfo();
	            $attributes = array();
	            if(isset($product->additional_attributes)){
	                
    	            foreach($product->additional_attributes as $key => $attribute){
    	                
//     	                if(!empty($attribute->value)){
    	                    
    	                    $attributes[$attribute->key] = $attribute->value;
//     	                }
    
    	            }
    
	                $addAttributes = array();
	                
	                foreach($attributes as $name => $value){
	                    
	                    $attrName = isset($attributesRelationship[$name]) ? $attributesRelationship[$name] : $name ;
	                    
	                    if(property_exists($productsTempModel, $attrName)){
	                        
	                        $productsTempModel->{$attrName} = $value;
	                        
	                    }else{
	                        
	                        if(is_numeric($value)){
	                            
	                            $attributesValuesModel->attribute_code = $attrName;
	                            $attributesValuesModel->value_id = $value;
	                            $value = $attributesValuesModel->GetAttributesRelationshipValue();
	                            echo $attrName." - ".$value."<br>";
	                            
	                        }
	                        
	                        $addAttributes[] = array(
	                            "attribute_id" => $attrName,
	                            "value" => $value
	                        );
	                        
	                    }
	                    
	                }
    	            
    	            if(isset($addAttributes)){
    	                
    	                $queryAp = $db->query("SELECT id FROM available_products
                        WHERE store_id = {$storeId} AND sku LIKE '{$ids['sku']}'");
    	                $resAp = $queryAp->fetch(PDO::FETCH_ASSOC);
    	                
    	                if(isset($resAp['id'])){
        	                
    	                    $attributesValuesModel->product_id = $resAp['id'];
        	                $attributesValuesModel->attributesValues = $addAttributes;
        	                $attributesValuesModel->Save();
        	                
    	                }
    	            }
    	            
	                $productsTempModel->product_id = $product->product_id;
	                
	                $productsTempModel->type = $product->type;
	                
	                $productsTempModel->set_attribute = $product->set;
	                
	                $productsTempModel->categories_ids = json_encode($product->category_ids);
    	            
    	            $productsTempModel->websites = json_encode($product->websites);
    	            
    	            $productsTempModel->created_at = date('Y-m-d H:i:s', strtotime($product->created_at));
    	            
    	            $productsTempModel->updated_at = date('Y-m-d H:i:s', strtotime($product->updated_at));
    	            
    	            $productsTempModel->Save();
    	            
    	            $count++;
    

    	            
	            }else{
	                pre($product);
	            }
	            
	           
	            
	        }
	        logSyncEnd($db, $syncId, $count);
	        echo "Total de produtos atualizados: {$count}";
	        
	        break;
	        
	    case "import_attributes_values" :
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Importação de Valores de Atributos", $request);
	        $imported = 0;
	        
	        $sql = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId} ORDER BY product_id DESC";
	        // 	        $sql .= isset($_REQUEST['product_id']) ? " AND product_id = {$_REQUEST['product_id']}" : "";die;
	        $query = $db->query($sql);
	        $productsIds = $query->fetchAll(PDO::FETCH_ASSOC);
// 	        pre($productsIds);die;
	        $productsModel = new ProductsModel($db, null, $storeId);
	        
	        $productsTempModel = new ProductsTempModel($db, null, $storeId);
	        
	        $attributesRelationshipModel = new AttributesRelationshipModel($db, null, $storeId);
	        
	        $attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
	        
	        $additionalAttributes = $attributesRelationshipModel->GetAttributesToImport();
	        
	        $attributesRelationship = $attributesRelationshipModel->GetAttributesRelationship();
	        
	        foreach($productsIds as $key => $ids){
	            
	            $productsModel->product_id = $ids['product_id'];
	            
	            $productsModel->additional_attributes = $additionalAttributes;
	            
	            $product = $productsModel->catalogProductInfo();
// 	            pre($product);die;
	            $attributes = array();
	            
	            if(isset($product->additional_attributes)){
	                
	                foreach($product->additional_attributes as $key => $attribute){
	                    
	                    if(!empty($attribute->value)){
	                        
	                        $attributes[$attribute->key] = $attribute->value;
	                    }
	                    
	                }
	                
	                $addAttributes = array();
	                
	                foreach($attributes as $name => $value){
	                    
	                    $attrName = isset($attributesRelationship[$name]) ? $attributesRelationship[$name] : $name ;
	                    
	                    if(property_exists($productsTempModel, $attrName)){
	                        
	                        $productsTempModel->{$attrName} = $value;
	                        
	                    }else{
	                        
	                        if(is_numeric($value)){
	                            
	                            $attributesValuesModel->attribute_code = $attrName;
	                            
	                            $attributesValuesModel->value_id = $value;
	                            
	                            $value = $attributesValuesModel->GetAttributesRelationshipValue();
	                            
	                            echo $attrName." - ".$value."<br>";
	                            
	                        }
	                        $addAttributes[] = array(
	                            "attribute_id" => $attrName,
	                            "value" => $value
	                        );

	                    }
	                    
	                }
	                
	                if(isset($addAttributes)){
	                    
	                    $queryAp = $db->query("SELECT id FROM available_products
                        WHERE store_id = {$storeId} AND sku LIKE '{$ids['sku']}'");
	                    $resAp = $queryAp->fetch(PDO::FETCH_ASSOC);
	                    if(isset($resAp['id'])){
	                        
	                        $attributesValuesModel->product_id = $resAp['id'];
	                        
	                        $attributesValuesModel->attributesValues = $addAttributes;
	                        
	                        $attributesValuesModel->Save();
	                        $imported++;
	                        
	                    }
	                }
	            }
	        }
	        logSyncEnd($db, $syncId, $imported);
	        break;
	        
	    case "update_stock":
	        
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Atualização de estoque", $request);
	        
	        $productsTempModel = new ProductsTempModel($db, null, $storeId);
	        
	        $inventoryModel = new InventoryModel($db, null, $storeId);
	        $totalProductsTemp = $productsTempModel->TotalProductsTemp();
	        
	        $limit = 200;
	        $offset = 0;
	        $count = 0;
	        do{
	            
	            $productsTemp = $productsTempModel->ListProductsIds( $offset, $limit);
    	        $inventoryModel->products_ids = $productsTemp;
    	        
    	        $inventory = $inventoryModel->catalogInventoryStockItemList();
    	        
    	        if(isset($inventory[0])){
         	        foreach($inventory as $key => $value){
         	            pre($value);
         	            $qty = $value->qty > 0 ? intval($value->qty) : 0 ;
        	            $query = $db->update("module_onbi_products_tmp",
        	                array("store_id", "product_id"),
        	                array($storeId, $value->product_id),
        	                array("qty" => $qty)
        	                );
        
        	            if(!$query){
        	                pre($query);
        	            }
        	            if($query->rowCount()){
        	                $count++;
        	            }
        	        }
    	        }else{
    	            //TODO set log error
    	        }
    	        
    	        $offset = $offset + $limit;
    	        
	        }while($offset <= $totalProductsTemp);
	        
	        logSyncEnd($db, $syncId, $count);
	        echo "Total de produtos atualizados: {$count}";
	        
	        break;
	        
	    case 'update_available_products':
	        
	        
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Importação de Produtos Dísponiveis.", $request);
	        $imported = 0;
	        $atualized = 0;
	        $query = $db->query("SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId}");
	        $productsTmp = $query->fetchAll(PDO::FETCH_ASSOC);
// 	        pre($productsTmp);die;
	        foreach($productsTmp as $key => $value){
	            if(isset($value["brand"])){
	                $sqlBrand = "SELECT brand FROM brands WHERE store_id = {$storeId} AND ecommerce_id = {$value["brand"]} ";
	                $queryBrand = $db->query($sqlBrand);
	                $brand = $queryBrand->fetch(PDO::FETCH_ASSOC);
	            }
	            if(isset($value["color"])){
	                $sqlColor = "SELECT color FROM colors WHERE store_id = {$storeId} AND color LIKE '{$value["color"]}' ";
	                $queryColor = $db->query($sqlColor);
	                $color = $queryColor->fetch(PDO::FETCH_ASSOC);
	            }
	            $sku = trim($value['sku']);
	            $parentId = trim($value['sku']);
	            $title = $value['title'];
	            $description = $value['description'];
	            $category =  isset($value["category"]) ? friendlyText($value['category']) : '';
	            $variation =  isset($value["variation"]) ? trim($value["variation"]) : '';
	            $reference =  isset($value["reference"]) ? trim($value["reference"]) : '';
	            $quantity = $value['qty'] > 0 ? $value['qty'] : 0 ;
	            $ean = isset($value["ean"]) ? trim($value["ean"]) : '';
	            $ncm = isset($value["ncm"]) ? trim($value["ncm"]) : '';
	            
	            $price = isset($value["price"]) ? number_format($value["price"], 2, '.', '') : '0.00';
	            $salePrice = isset($value["sale_price"]) ? number_format($value["sale_price"], 2, '.', '') : '0.00';
	            $promotionPrice = isset($value["promotion_price"]) ? number_format($value["promotion_price"], 2, '.', '') : '0.00';
	            
// 	            $price = isset($value["price"]) ? $value["price"] : '0.00';
// 	            $salePrice = isset($value["sale_price"]) ? $value["sale_price"] : '0.00';
// 	            $promotionPrice = isset($value["promotion_price"]) ? $value["promotion_price"] : '0.00';
	            
	            $weight = isset($value["weight"]) ?  number_format($value["weight"], 3) : '';
	            $height = isset($value["height"]) ? $value["height"] : '';
	            $width =  isset($value["width"]) ? $value["width"]  : '';
	            $length =  isset($value["length"]) ? $value["length"] : '';
	            $brand =  isset($brand['brand']) ? $brand['brand'] : "";
	            $color =  isset($color['color']) ? $color['color'] : "";
	            $cost =  isset($color['cost']) ? $color['cost'] : "0.00";
	            
	            $sqlVerify = "SELECT id, sku FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}' ";
	            $verifyQuery = $db->query($sqlVerify);
	            $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	            
	            if(!isset($verify['id'])){

	                $queryAP = $db->insert('available_products', array(
	                    'store_id' => $storeId,
	                    'sku' => $sku,
	                    'parent_id' => $parentId,
	                    'title' => $title,
	                    'color' => $color,
	                    'variation' => $variation,
	                    'reference' => $reference,
	                    'quantity' => $quantity,
	                    'description' => $description,
	                    'price' => $price,
	                    'sale_price' => $salePrice,
	                    'promotion_price' => $promotionPrice,
	                    'category' => $category,
	                    'brand' => $brand,
	                    'weight' => $weight,
	                    'height' => $height,
	                    'width' => $width,
	                    'length' => $length,
	                    'ean' => $ean,
	                    'ncm' => $ncm,
	                    'cost' => $cost
	                    
	                ));
	                
	                $imported++;
	                
	            }else{
// 	                $queryAP = $db->update( 'available_products',
// 	                    array('store_id','id'),
// 	                    array($storeId, $verify['id']),
// 	                    array('parent_id' => $parentId,
// 	                        'title' => $title,
// 	                        'color' => $color,
// 	                        'variation' => $variation,
// 	                        'reference' => $reference,
// 	                        'quantity' => $quantity,
// 	                        'description' => $description,
// 	                        'price' => $salePrice,
// 	                        'sale_price' => $salePrice,
// 	                        'promotion_price' => $promotionPrice,
// 	                        'category' => $category,
// 	                        'brand' => $brand,
// 	                        'weight' => $weight,
// 	                        'height' => $height,
// 	                        'width' => $width,
// 	                        'length' => $length,
// 	                        'ean' => $ean,
// 	                        'ncm' => $ncm,
// 	                        'cost' => $cost
	                        
// 	                    ));
// 	                pre($verify);
// 	                pre($quantity);die;
	                $queryAP = $db->update( 'available_products',
	                    array('store_id','id'),
	                    array($storeId, $verify['id']),
	                    array('parent_id' => $parentId,
	                        'quantity' => $quantity,
	                        'price' => $salePrice,
	                        'cost' => $cost
	                        
	                    ));
	                
	                if($queryAP->rowCount()){
	                    
	                    $db->update('available_products',
	                        array('store_id','id'),
	                        array($storeId, $verify['id']),
	                        array('flag' => 1)
	                        );
	                    
	                    $db->update('ml_products',
	                        array('store_id','id'),
	                        array($storeId, $sku),
	                        array('flag' => 1)
	                        );
	                    
	                    
	                    $atualized++;
	                }
	                
	                
	            }
	            
	            if(!$queryAP){
	                pre($queryAP);
	            }
	            
	            
	        }
	        
	        logSyncEnd($db, $syncId, $imported."/".$atualized);
	        echo "Total de produtos importado: {$imported} atualizados: {$atualized}";
	        break;
	    
	}
	
}

