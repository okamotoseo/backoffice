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
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Api/CategoriesRestModel.php';
require_once $path .'/../Models/Api/ItemsRestModel.php';
require_once $path .'/../Models/Adverts/ItemsModel.php';
require_once $path .'/../Models/Map/MlCategoryModel.php';
require_once $path .'/../Models/Price/PriceModel.php';
require_once $path .'/functions.php';


$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? intval($_REQUEST["product_id"]) : null ;
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
    if(isset($_SERVER ['argv'] [3])){
        $paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
        $accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
    }
    
    $request = "System";
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
    require_once $path .'/verifyToken.php';
    
    
	switch($action){
	    
	    
	    case "import_ads_informations":
	        
	        
	        $itemsRestModel = new ItemsRestModel($db, null, $storeId);
	        
	        $sqlProduct = "SELECT * FROM `ml_products` WHERE store_id = {$storeId}";
	        
	        if(isset($productId)){
	            $sqlProduct = "SELECT * FROM ml_products WHERE store_id = {$storeId} AND id LIKE '{$productId}'";
	        }
	        
	        $query = $db->query($sqlProduct);
	        while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
	            pre($rowProduct);
	            
	            $itemsRestModel->item_id = $rowProduct['id'];
	            $result = $itemsRestModel->getItem();
	            
	            pre($result);
	            die;
	            if($result['body']->status == 'active'){
	                if(isset($result['body']->variations)){
	                    foreach($result['body']->variations as $key => $variation){
	                        if(!empty($variation->seller_custom_field)){
	                            //     	                        pre($variation);
	                            $sqlProduct = "SELECT sku FROM `ml_products_attributes`
                                WHERE store_id = {$storeId} AND variation_id = {$variation->id}";
	                            $queryAttr = $db->query($sqlProduct);
	                            $rowProduct = $queryAttr->fetch(PDO::FETCH_ASSOC);
	                            if(empty($rowProduct['sku'])){
	                                echo "{$count} - {$rowProduct['sku']} <br>";
	                                $count++;
	                            }
	                        }
	                    }
	                    // 	                    echo $variation->id." - ".$variation->seller_custom_field;
	                    // 	                    echo "<br>";
	                }
	            }
	            
	        }
	        
	        break;
	        
	    case "import_ads_variations":
	        
	        
	        $itemsRestModel = new ItemsRestModel($db, null, $storeId);
	        
	        $sqlProduct = "SELECT * FROM `ml_products` WHERE store_id = {$storeId}";
	        
	        $adsId = isset($_REQUEST["ads_id"])  ? intval($_REQUEST["ads_id"]) : NULL ;
	        
	        
	        if(isset($adsId)){
	           $sqlProduct = "SELECT * FROM ml_products WHERE store_id = {$storeId} AND id = {$adsId}";
	        }
	        $count = 0;
	        $query = $db->query($sqlProduct);
	        while($rowProduct = $query->fetch(PDO::FETCH_ASSOC)){
	            
	            $itemsRestModel->item_id = $rowProduct['id'];
	            
	            $result = $itemsRestModel->getItem();
	            pre($result);
	            saveItem ($db, $storeId, $result ['body'], $rowProduct['sku'] );
	            

	                if(isset($result['body']->variations)){
	                    
	                    
	                    $sqlDeleteVariations = "DELETE FROM ml_products_attributes  WHERE `store_id` = {$storeId} AND `product_id` = {$rowProduct['id']}";
	                    
	                    $query = $db->query($sqlDeleteVariations);
	                    
	                    saveItemVariations ($db, $storeId, $result ['body']->id, $result['body']->variations );
	                    
	                    echo "success|";

	                    	                   
	                }
	            
	        }
	        
	        break;
	        
	    case "import_ads":
	        
	        
	        $itemsRestModel = new ItemsRestModel($db, null, $storeId);
	        
	        
	        $adsId = isset($_REQUEST["ads_id"])  ? intval($_REQUEST["ads_id"]) : NULL ;
	        
	        $select = "SELECT id FROM ml_products WHERE id = {$adsId}";
	        $query = $db->query($select);
	        $res = $query->fetch(PDO::FETCH_ASSOC);
	        if(empty($res['id'])){
	            
		        $itemsRestModel->item_id =$adsId;
	            
	            $result = $itemsRestModel->getItem();
	            
	            if(!isset($sku)){
	            	$sku = !empty($result ['body']->variations[0]->seller_custom_field) ? $result ['body']->variations[0]->seller_custom_field : 'imported-'.rand() ;
	            }
	           
	            
	            saveItem ($db, $storeId, $result ['body'], $sku );
	            
	            if(!empty($result ['body']->variations)){
	                
	                
	                saveItemVariations ($db, $storeId, $result ['body']->id, $result ['body']->variations );
	                
	            }
	            
	            echo "success|Produto cadastrado com sucesso!|{$result['body']->permalink}|";
	            
	        }else{
	        	
	        	echo "error|Já existe um anúncio com o mesmo código MLB{$adsId}";
	        }
            
	            
	        
	        break;
	}
	    
		
	
}




