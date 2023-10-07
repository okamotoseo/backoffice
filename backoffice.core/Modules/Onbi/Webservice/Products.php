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
		
        
	    case "export_price":
	        
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Atualização do preço ecommerce", $request);
	        $productsModel = new ProductsModel($db, null, $storeId);
	        $salePriceModel = new SalePriceModel($db, null, $storeId);
	        $salePriceModel->marketplace = "Ecommerce";
	        
	        $productId = isset($_REQUEST["product_id"])  ? intval($_REQUEST["product_id"]) : NULL ;
	        $dateFrom =  date("Y-m-d H:i:s",  strtotime("-10 hour") );
	        $sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}'";
	        if(isset($productId)){
	        	$sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
	        }
	        
	        if(isset($_REQUEST['all'])){
	        	$sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId}";
	        }
	        
	        $totalUpdated = 0;
	        $queryParent = $db->query($sqlParent);
	        while($rowParent = $queryParent->fetch(PDO::FETCH_ASSOC)){
	        	 
	        	if(!empty($rowParent['parent_id'])){
	        		
	        		$sqlProduct = "SELECT id, sku, parent_id, quantity, blocked, price, sale_price FROM available_products 
	        		WHERE store_id = {$storeId} AND parent_id LIKE '{$rowParent['parent_id']}' ";
	        	
			        $query = $db->query($sqlProduct);
			        while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
			            $sqlOnbiProducts = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId} 
		                AND sku LIKE '{$rowProduct['sku']}' OR store_id = {$storeId} AND sku LIKE '{$rowProduct['sku']}-x'";
			            $queryOnbiProducts = $db->query($sqlOnbiProducts);
			            $resOnbiProductsAll = $queryOnbiProducts->fetchAll(PDO::FETCH_ASSOC);
			            if(isset($resOnbiProductsAll)){
		    	            foreach($resOnbiProductsAll as $i => $resOnbiProducts){
		        	            if(!empty($resOnbiProducts['product_id'])){
		        	            	$salePriceModel->sku = $rowProduct['sku'];
		        	            	$salePriceModel->product_id = $rowProduct['id'];
		        	            	$salePrice = $salePriceModel->getSalePrice();
		        	            	$stockPriceRel = $salePriceModel->getStockPriceRelacional();
		        	            	$salePrice = empty($stockPriceRel['price']) ? $salePrice : number_format($stockPriceRel['price'], 2) ;
		        	                
		        	                //$productsModel->product_id = $resOnbiProducts['sku'];
		        	                $productsModel->product_id = $resOnbiProducts['product_id'];
		        	                $productsModel->storeView = 0;
// 		        	                pre($productsModel);
		        	                $result = $productsModel->catalogProductUpdate(array("websites" => array(1), "price" => $salePrice));
// 		        	                pre($result);
		        	                if($result == 1){
		        	                    $totalUpdated++;
		        	                    $dataLog['export_price_onbi'] = array(
		        	                    		'request' => json_encode(array("websites" => array(1), "price" => $salePrice, 'sku' => $rowProduct['sku'])),
		        	                    		'response' => json_encode(array('success'))
		        	                    );
		        	                    pre($dataLog);
		        	                    $db->insert('products_log', array(
		        	                    		'store_id' => $storeId,
		        	                    		'product_id' => $rowProduct['id'],
		        	                    		'description' => 'Atualização do Preço Produto Magento Onbi',
		        	                    		'user' => $request,
		        	                    		'created' => date('Y-m-d H:i:s'),
		        	                    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
		        	                    ));
		        	                }
		        	            }
		    	            }
			            }
			        }
	        	}        
	        }
	        logSyncEnd($db, $syncId, $totalUpdated);
	        
	        break;
	        
	        
	    case "export_stock":
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Atualização do estoque ecommerce", $request);
	        $inventoryModel = new InventoryModel($db, null, $storeId);
	        $salePriceModel = new SalePriceModel($db, null, $storeId);
	        $salePriceModel->marketplace = "Ecommerce";
	        
	        $productId = isset($_REQUEST["product_id"])  ? intval($_REQUEST["product_id"]) : NULL ;
	        $dateFrom =  date("Y-m-d H:i:s",  strtotime("-3 hour") );
	        $sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}' ";
	        if(isset($productId)){
	        	$sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
	        }
	        if(isset($_REQUEST['all'])){
	        	$sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId}";
	        }
	        
	        $totalUpdated = 0;
	        $queryParent = $db->query($sqlParent);
	        while($rowParent = $queryParent->fetch(PDO::FETCH_ASSOC)){
	        	 
	        	if(!empty($rowParent['parent_id'])){
	        		 
	        		$sqlProduct = "SELECT id, sku, parent_id, quantity, blocked, price, sale_price FROM available_products 
	        		WHERE store_id = {$storeId} AND parent_id LIKE '{$rowParent['parent_id']}' ";
	        
			        $query = $db->query($sqlProduct);
			        
			        while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
			        	
			            $sqlOnbiProducts = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId} 
			            AND sku LIKE '{$rowProduct['sku']}'";
			            $queryOnbiProducts = $db->query($sqlOnbiProducts);
			            $resOnbiProducts = $queryOnbiProducts->fetch(PDO::FETCH_ASSOC);
			            
			            if(!empty($resOnbiProducts['product_id'])){
			                
			                $qtd = $rowProduct['quantity'] > 0 ? $rowProduct['quantity'] : 0 ;
			                $salePriceModel->sku = $rowProduct['sku'];
			                $salePriceModel->product_id = $rowProduct['id'];
			                $salePrice = $salePriceModel->getSalePrice();
			                $stockPriceRel = $salePriceModel->getStockPriceRelacional();
			                $salePrice = empty($stockPriceRel['price']) ? $salePrice : number_format($stockPriceRel['price'], 2)  ;
			                $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
			                if ($rowProduct['blocked'] == "T"){
			                	$qtd = 0;
			                	echo "error|Produto Bloqueado...";
// 			                	continue;
			                }
			                $inventoryModel->sku = $rowProduct['sku'];
		    	            $inventoryModel->product_id = $resOnbiProducts['product_id'];
		    	            $inventoryModel->qty = $qtd;
		    	            $inventoryModel->is_in_stock = $qtd > 0 ? 1 : 0 ;
		    	            $resProducts = $inventoryModel->catalogInventoryStockItemUpdate();
		    	            if($resProducts == 1){
		        	           $totalUpdated++;
		        	           $dataLog['export_stock_onbi'] = array(
		        	           		'request' => json_encode(array('manage_stock' => 1,'qty' => $inventoryModel->qty, 'is_in_stock' => $inventoryModel->is_in_stock, 'sku' => $rowProduct['sku'])),
		        	           		'response' => json_encode(array($resProducts))
		        	           		);
		        	           pre($dataLog);
		        	           $db->insert('products_log', array(
		        	           		'store_id' => $storeId,
		        	           		'product_id' => $rowProduct['id'],
		        	           		'description' => 'Atualização do Estoque Produto Magento Onbi',
		        	           		'user' => $request,
		        	           		'created' => date('Y-m-d H:i:s'),
		        	           		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
		        	           ));
		    	            }else{
		    	            	echo "Erro não foi possivel conectar {$rowProduct['sku']}<br>";
		    	            }
			            }else{
			            	echo "nao existe {$rowProduct['sku']}<br>";
			            }
			        }
	        	}
	        }
	        logSyncEnd($db, $syncId, $totalUpdated);
	        break;
	        
	    case "import_products_ids" :
	        
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Importação de Códigos e Categorias", $request);
	        $import = $count = 1;
	        $productsModel = new ProductsModel($db, null, $storeId);
	        
	        $products = $productsModel->catalogProductList();
	        $categoriesModel = new CategoriesModel($db, null, $storeId);
            
	        $idToUse = array(27, 44, 49, 55,73, 100, 83, 117, 554, 109, 112);
            
	        foreach($products as $key => $product){
                
	            $categoryJson = json_encode($product->category_ids);
	            
	            $websitesJson = json_encode($product->website_ids);
	            
	            $sqlVerify = "SELECT product_id FROM module_onbi_products_tmp 
                WHERE store_id = {$storeId} AND product_id = '{$product->product_id}' ";
	            $verifyQuery = $db->query($sqlVerify);
	            $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	            
	            if(!isset($verify['product_id'])){
                        
                    $query = $db->insert("module_onbi_products_tmp", array(
                        "store_id" => $storeId,
                        "product_id" => $product->product_id,
                        "sku" => $product->sku,
                        "title" => $product->name,
                        "set_attribute" => $product->set,
                        "type" => $product->type,
                        "categories_ids" => $categoryJson,
                        "websites" => $websitesJson
                        
                    ));
                    $import++;
    	            if(!$query){
    	                pre($query);
    	            }
	            
	            }
	            if(isset($verify['product_id'])){
	 
                    $query = $db->update("module_onbi_products_tmp",
                        array("store_id", "product_id"),
                        array($storeId, $verify['product_id']),
                        array("sku" => $product->sku,
                            "title" => $product->name,
                            "set_attribute" => $product->set,
                            "type" => $product->type,
                            "categories_ids" => $categoryJson,
                            "websites" => $websitesJson
                        ));
	                    
	                if(!$query){
	                    pre($query);
	                }
	                
	            }
	            $count++;

	        }
	        logSyncEnd($db, $syncId, $count);
	        echo "Total de produtos atualizados: {$count} Importados: {$import}";

	        break;
	    case "import_categories_hierarchy":
	        
	        $categoriesModel = new CategoriesModel($db, null, $storeId);
	       
	        $idToUse = array(27, 44, 49, 55,73, 100, 83, 117, 554, 109, 112, 554);
    	        $categoriesIds = $categoriesModel->ListCategoriesIds();
    	        foreach($categoriesIds as $key => $categories){
    	            pre($categories);
    	            $categoriesModel->categories = json_decode($categories['categories_ids']);
                    $hierarchy = $categoriesModel->catalogCategoryHierarchy();
                    $category = '';
                    foreach($hierarchy as $ind => $name){
                        $parts = explode(">", $name);
                        $onbiName = trim($parts[0]);
                        $sql = "SELECT * FROM onbi_categories_relationship WHERE 
                        store_id = {$storeId} AND onbi_parent_id = 2 AND onbi_name LIKE '{$onbiName}' ";
                        $query = $db->query($sql);
                        $res = $query->fetch(PDO::FETCH_ASSOC);
                        if(in_array($res['onbi_category_id'], $idToUse)){
                            if(count($parts) > 0){
                                $category = $hierarchy[$ind];
                                break;
                            }
    
                        }
    
                    }
                    pre($hierarchy);
                    $category = !empty($category) ?  trim($category) : trim($hierarchy[1]) ;
                    echo $categories['category_present'];
                    echo "<br>";
                    echo $category;
                    echo "<br><br>";
    	            if(!empty($category)){
    	                
    	                $query = $db->update('module_onbi_products_tmp', 
    	                    array("store_id", "product_id"), 
    	                    array($storeId, $categories['product_id']), 
    	                    array('category' => $category)
    	                    );
    	                if(!$query){
    	                    pre($query);
    	                }
    	            }else{
    	                pre("erro");
    	            }
    	            
    
    	        }
    	      
	        break;
	        
	        
	    case "import_products_info" :
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Importação de Attributos e Preço dos Produtos", $request);
	        $count = 0;
	        
	        $sql = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId}";
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
	            
	            pre($product);
	            
	            $attributes = array();
	            if(isset($product->additional_attributes)){
	                
    	            foreach($product->additional_attributes as $key => $attribute){
    	                
	                    $attributes[$attribute->key] = $attribute->value;
    
    	            }
    	            if(isset($product->special_from_date)){
        	            if($product->special_from_date <= date('Y-m-d H:i:s') && $product->special_to_date >=  date('Y-m-d 00:00:00')){
//         	                pre(array(
//         	                    'sku' => $attributes['sku'],
//         	                    'price' => $attributes['price'],
//         	                    'promotion_price' => $attributes['special_price'],
//         	                    'dif' => ($attributes['price'] - $attributes['special_price'])
//         	                ));
        	                
        	            }else{
//         	                pre($product->special_from_date);
//         	                pre($product->special_to_date);
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
// 	                            echo $attrName." - ".$value."<br>";
	                        }
	                        
	                        $addAttributes[] = array(
	                            "attribute_id" => $attrName,
	                            "value" => $value
	                        );
	                    }
	                }
    	            if(count($addAttributes) > 0){
    	                
    	                $queryAp = $db->query("SELECT id FROM available_products
                        WHERE store_id = {$storeId} AND sku LIKE '{$ids['sku']}'");
    	                $resAp = $queryAp->fetch(PDO::FETCH_ASSOC);
    	                
    	                if(isset($resAp['id'])){
    	                	pre('attributos para salvar');
        	                pre($addAttributes);
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
    	            pre('save this values');
    	            pre($productsTempModel->product_id);
    	            pre($productsTempModel->sale_price);
    	            
    	            $productsTempModel->Save();
    	            
    	            $count++;
    	           
	            }else{
// 	                pre($product);
	            }
	        }
	        logSyncEnd($db, $syncId, $count);
	        
	        echo "Total de produtos atualizados: {$count}";
	        
	        break;
	        
	    case "import_attributes_values" :
	        
	        $syncId =  logSyncStart($db, $storeId, "Onbi", $action, "Importação de Valores de Atributos", $request);
	        
	        $imported = 0;
	        
	        $sql = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId} ORDER BY product_id DESC";
	        
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
	                            
// 	                            echo $attrName." - ".$value."<br>";
	                            
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
    	        $inventoryModel->stock_id = 1; // Estoque miromi RS
    	        $inventory = $inventoryModel->catalogInventoryStockItemList();
    	        pre($inventory);
    	        if(isset($inventory[0])){
         	        foreach($inventory as $key => $value){
         	            if(isset($value->qty)){
         	              $qty = $value->qty > 0 ? intval($value->qty) : 0 ;
         	            }else{
         	                if($value->is_in_stock == 1 ){
         	                    //Caso tenha algum erro da atualização de3 estoque mas indique o produto esta disponivel seta 1 para nao remover
         	                    $qty = 1;
         	                }
         	                
         	            }
         	            
         	            if($value->is_in_stock == 0 ){
         	                $qty = 0;
         	            }
         	            
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

	        $sql = "SELECT * FROM module_onbi_products_tmp WHERE store_id = {$storeId}  AND category IS NOT NULL";
	        $query = $db->query($sql);
	        $productsTmp = $query->fetchAll(PDO::FETCH_ASSOC);
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
	            $weight = isset($value["weight"]) ?  number_format($value["weight"], 3) : '';
	            $height = isset($value["height"]) ? $value["height"] : '';
	            $width =  isset($value["width"]) ? $value["width"]  : '';
	            $length =  isset($value["length"]) ? $value["length"] : '';
	            $brand =  isset($brand['brand']) ? $brand['brand'] : "";
	            $color =  isset($color['color']) ? $color['color'] : "";
	            $cost =  isset($color['cost']) ? $color['cost'] : "0.00";
	            
	            $sqlVerify = "SELECT id, sku, quantity, price, sale_price, cost 
	            FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}' ";
	            $verifyQuery = $db->query($sqlVerify);
	            $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	            
	            if(!isset($verify['id'])){
	               
	                if(!empty($value["brand"])){
	                   
	                	$data = array(
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
    	                    
    	                );
	                	$queryAP = $db->insert('available_products', $data);
    	                $imported++;
    	                $id = $db->last_id;
    	                if(!empty($id)){
    	                	
    	                	$dataLog['insert_available_products_onbi'] = $data;
    	                	
	    	                $db->insert('products_log', array(
	    	                		'store_id' => $storeId,
	    	                		'product_id' => $id,
	    	                		'description' => 'Novo Produto Importado do Ecommerce Magento',
	    	                		'user' => $request,
	    	                		'created' => date('Y-m-d H:i:s'),
	    	                		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	    	                		));
    	                }
	                }
	                
	            }else{
	                $data = array('quantity' => $quantity,
	                        'price' => $salePrice,
	                        'sale_price' => $salePrice,
	                        'cost' => $cost
	                        
	                    );
	                $queryAP = $db->update( 'available_products',
	                    array('store_id','id'),
	                    array($storeId, $verify['id']),
	                		$data );
	                
	                if($queryAP->rowCount()){
	                    
	                    $db->update('available_products',
	                        array('store_id','id'),
	                        array($storeId, $verify['id']),
	                        array('flag' => 1, 'updated' => date('Y-m-d H:i:s'))
	                        );
	                    
	                    $db->update('ml_products',
	                        array('store_id','sku'),
	                        array($storeId, $sku),
	                        array('flag' => 1, 'updated' => date('Y-m-d H:i:s'))
	                        );
	                    
	                    $atualized++;
	                    
	                    $dataLog['update_available_products_onbi'] = array(
	                    		'after' => $data,
	                    		'before' => $verify,
	                    		'product_id' => $verify['id']
	                    );
	                    pre($dataLog);
	                    	$db->insert('products_log', array(
	                    		'store_id' => $storeId,
	                    		'product_id' => $verify['id'],
	                    		'description' => "Atualização do Produto Importado do Ecommerce Magento",
	                    		'user' => $request,
	                    		'created' => date('Y-m-d H:i:s'),
	                    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	                    ));
	                }
	                
	            }
	           
	        }
	        
	        logSyncEnd($db, $syncId, $imported."/".$atualized);
	        
	        echo "Total de produtos importado: {$imported} atualizados: {$atualized}";
	        
	        break;
	        
	   
	        
	    case "remove_product_magento":
	        $productId = $_REQUEST['product_id'];
	        $sku = $_REQUEST['sku'];
	        
	        $productsModel = new ProductsModel($db, null, $storeId);
	        $productsModel->product_id = $productId;
	        
	        if($productsModel->catalogProductDelete()){
    	        
    	        $sql = "DELETE FROM module_onbi_products_tmp WHERE store_id = {$storeId} AND product_id = '{$productId}'";
    	        $query = $db->query($sql);
    	        if(!$query){
    	        	echo  "error|Erro ao excluir produto {$sql}";
    	        }
    	        
    	        $db->insert('products_log', array(
    	        		'store_id' => $storeId,
    	        		'product_id' => $productId,
    	        		'description' => 'Produto Removido do Ecommerce',
    	        		'user' => $request,
    	        		'created' => date('Y-m-d H:i:s')
    	        ));
    	        
	        }
	        
	        
	        echo "success|Produto removido com sucesso!";
	        
	        break;
	        
	    
	}
	
}

