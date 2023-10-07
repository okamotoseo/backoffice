<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
// print_r($path);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Soap.php';
require_once $path .'/../Models/Catalog/CategoriesModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

//
$addCategory = isset($_REQUEST["add_category"]) && !empty($_REQUEST["add_category"]) ? $_REQUEST["add_category"] : null ;

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
	        /**
	         * Importa Categorias do Ecommerce
	         */
	    case "import_categories_hierarchy":
	       
	        $rootCategoryId = 2;
	        
	        $categoriesModel = new CategoriesModel($db, null, $storeId);
	        
	        $categoriesTrees = $categoriesModel->catalogCategoryTree();

	        $categoriesTrees = (array)$categoriesTrees;

	        foreach((array)$categoriesTrees['children'][0]->children as $key => $categories){
// 	            pre($categories);
	            
	            importCategories($db, $storeId, $categories);
	            
	           echo "<br>";
	            
	        }

	        
	        break;
	        
	    case "add_category_relationship":
	        
	        $onbiCategoryId = $_REQUEST['onbi_category_id'];
	        $categoryId = $_REQUEST['category_id'];
	        $parentId = $_REQUEST['parent_id'];
	        
	        if(!empty($onbiCategoryId)){
	            
	            $sql = "SELECT * FROM `category` WHERE store_id = {$storeId}
                AND id = '{$categoryId}' and parent_id = {$parentId}";
	            $query = $db->query($sql);
	            $category = $query->fetch(PDO::FETCH_ASSOC);
	            
	            if(!empty($category['hierarchy'])){
	                
	                $sqlUpdate = "UPDATE `onbi_categories_relationship` SET category_id = {$categoryId}, 
                        parent_id = {$parentId}, hierarchy = '{$category['hierarchy']}'
					WHERE store_id = {$storeId} AND onbi_category_id = {$onbiCategoryId}";
	                $queryUpdate = $db->query($sqlUpdate);
	                
	                if($queryUpdate){
	                    echo "success|Relacionamento cadastrado com successo!";
	                }
	                
	            }else{
	                echo "error|Erro ao relacionar categoria";
	            }
	            
	        }
	        
	        break;
	    
	}
	
}


function importCategories($db, $storeId, $category = array()){
    
    $category = (array)$category;
    pre($category);
    echo $category['parent_id']." - ".$category['category_id']." - ".$category['name']."<br>";
    $sqlParent = "SELECT * FROM onbi_categories_relationship
    WHERE store_id = {$storeId} AND onbi_category_id = '{$category['category_id']}' 
    AND onbi_parent_id = '{$category['parent_id']}'";
    $query = $db->query($sqlParent);
    $resParent = $query->fetch(PDO::FETCH_ASSOC);
    
    if(!isset($resParent['category_id']) and !isset($resParent['parent_id'])){

        echo $parentId = isset($resParent['category_id']) ? $resParent['category_id'] : null ;
    
        addOnbiCategoryRelationship($db, $storeId, $parentId, $category['category_id'], $category['parent_id'], $category['name']);
    
    }
    
    if(isset($category['children'])){
        foreach($category['children'] as $key => $value){
            importCategories($db, $storeId, (array)$value);
            
        }
        
    }
    
    
}

function addOnbiCategoryRelationship($db, $storeId,  $parentId = null, $onbiCategoryId, $onbiParentId, $onbiCategoryName){
    
    if($onbiParentId == 2 ){
        
        $parentId = 0;
        
    }
    if(!isset($parentId)){
        
        $sqlParent = "SELECT category_id FROM onbi_categories_relationship
        WHERE store_id = {$storeId} AND onbi_category_id = '{$onbiParentId}'";
        $query = $db->query($sqlParent);
        $resParent = $query->fetch(PDO::FETCH_ASSOC);
        
        $parentId = isset($resParent['category_id']) ? $resParent['category_id'] : null ;
        
    }    
    
    if($parentId == 0){
        
        $hierarchy = friendlyText($onbiCategoryName);
        
    }else{
        
        $sql = "SELECT hierarchy FROM category WHERE `id`= {$parentId} AND store_id = {$storeId}";
        $query = $db->query($sql);
        $row = $query->fetch(PDO::FETCH_OBJ);
        $hierarchy = $row->hierarchy.' > '. friendlyText($onbiCategoryName);
    }
    
    if(isset($parentId)){
        
        $sql = "SELECT * FROM `category` WHERE `store_id` = {$storeId}
    				AND category LIKE '".addslashes($onbiCategoryName)."' AND parent_id = {$parentId}";
        $query = $db->query($sql);
        $res = $query->fetch(PDO::FETCH_ASSOC);

        if(!isset($res['category'])){
            $categoryId = '';
            //Busca nas configurações do modulo se vai importar as categorias 
            $moduleConfig = getModuleConfig($db, $storeId, 5);
            if($moduleConfig['import_categories']){
                $query = $db->insert('category', array(
                    'store_id' => $storeId,
                    'category' => friendlyText($onbiCategoryName),
                    'parent_id' => $parentId,
                    'hierarchy' => $hierarchy
                ));
                $categoryId = $db->last_id;
            }
            
        }else{
            $categoryId = $res['id'];
        }
            
            
        $sqlRel = "SELECT * FROM onbi_categories_relationship
        WHERE store_id = {$storeId} AND category_id = '{$categoryId}'";
        $queryRel = $db->query($sqlRel);
        $resRel = $queryRel->fetch(PDO::FETCH_ASSOC);
        if(!isset($resRel['category_id'])){
    
            $query = $db->insert('onbi_categories_relationship', array(
                'store_id' => $storeId,
                'parent_id' => $parentId,
                'category_id' => $categoryId,
                'onbi_parent_id' => $onbiParentId,
                'onbi_category_id' => $onbiCategoryId,
                'onbi_name' => friendlyText($onbiCategoryName),
                'hierarchy' => $hierarchy
            ));
            
        }else{
            pre($resRel);  
        }
            


    }else{
        echo $sqlParent."<br>";
    }
}

