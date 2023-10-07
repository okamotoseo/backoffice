<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);

ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Magento2.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Catalog/CategoriesModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;


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
    
    $moduleConfig = getModuleConfig($db, $storeId, 11);
    
    if(!isset($moduleConfig['type'])){
    	return;
    }
    
	switch($action){
		
        /**
         * Importa Categorias do Ecommerce
         */
	    case "import_categories_hierarchy":
	    	$orderCategories = array();
	        $rootCategoryId = 2;
	        $categoriesModel = new CategoriesModel($db, null, $storeId);
	        $categoriesModel->filters[] = array('field' => 'name', 'value' => 1, 'condition_type' => 'neq' );
	        $categoriesTrees = $categoriesModel->getCategoryList();
	       
	        foreach($categoriesTrees['body']['items'] as $key => $mg2Categories){
	        	
	        	$categoryId = $parentId = null;
	        	$hierarchy = '';
	            if($mg2Categories['parent_id'] >= $rootCategoryId){
	            	
	            	echo $mg2Categories['parent_id']."/".$mg2Categories['id']." - ".$mg2Categories['name']."<br>";

	            	$mg2CategoryId = $mg2Categories['id'];
	            	$mg2ParentId = $mg2Categories['parent_id'];
	            	$mg2CategoryName = $mg2Categories['name'];
	            	
	            	if($mg2ParentId == 2 ){
	            	
	            		$parentId = 0;
	            		$mg2Hierarchy = friendlyText($mg2CategoryName);
	            		$hierarchy = friendlyText($mg2CategoryName);
	            	
	            	}
	            	if(!isset($parentId)){
	            		// Get parent Information
	            		$sqlParent = "SELECT category_id, mg2_hierarchy FROM mg2_categories_relationship
	            		WHERE store_id = {$storeId} AND mg2_category_id = '{$mg2ParentId}'";
	            		$query = $db->query($sqlParent);
	            		$resParent = $query->fetch(PDO::FETCH_ASSOC);
	            		$parentId = isset($resParent['category_id']) && !empty($resParent['category_id']) ? $resParent['category_id'] : null ;
	            		$mg2Hierarchy = !empty($resParent['mg2_hierarchy']) ? $resParent['mg2_hierarchy'].' > '. friendlyText($mg2CategoryName) : '' ;
	            	
	            	}
	            	
	            	if(isset($parentId)){
	            		
	            		//Busca nas configurações do modulo se vai importar as categorias
	            		$sql = "SELECT hierarchy FROM category WHERE `id`= {$parentId} AND store_id = {$storeId}";
	            		$query = $db->query($sql);
	            		$row = $query->fetch(PDO::FETCH_OBJ);
	            		$hierarchy = !empty($row->hierarchy) ? $row->hierarchy.' > '. friendlyText($mg2CategoryName) :  friendlyText($mg2CategoryName);
	            		
	            		
	            		// verifica se existe
	            		$verify = ucwords(strtolower($mg2CategoryName));
	            		$sql = "SELECT * FROM `category` WHERE `store_id` = {$storeId}
	            		AND category LIKE '".addslashes($verify)."' AND parent_id = {$parentId}";
	            		$query = $db->query($sql);
	            		$res = $query->fetch(PDO::FETCH_ASSOC);
	            		if(!isset($res['category'])){
		            		
            				if($moduleConfig['type'] == 'import'){
		            			$query = $db->insert('category', array(
		            					'store_id' => $storeId,
		            					'category' => friendlyText($mg2CategoryName),
		            					'parent_id' => $parentId,
		            					'description' => $mg2CategoryName,
		            					'hierarchy' => $hierarchy,
		            					'type' => 'Magento2'
		            			));
	            	
	            				$categoryId = $db->last_id;
	            			}
	            			
	            	
	            		}else{
	            			$categoryId = $res['id'];
	            		}
	            		
            		}
	            		
	            		
            		$sqlRel = "SELECT * FROM mg2_categories_relationship
            		WHERE store_id = {$storeId} AND mg2_category_id = '{$mg2CategoryId}'";
            		$queryRel = $db->query($sqlRel);
            		$resRel = $queryRel->fetch(PDO::FETCH_ASSOC);
            		if(empty($resRel['mg2_category_id'])){
            			
            			if(!isset($categoryId) OR empty($categoryId)){
            				$hierarchy = '';
            			}
            			$query = $db->insert('mg2_categories_relationship', array(
            					'store_id' => $storeId,
            					'parent_id' => $parentId,
            					'category_id' => $categoryId,
            					'mg2_parent_id' => $mg2ParentId,
            					'mg2_category_id' => $mg2CategoryId,
            					'mg2_hierarchy' => $mg2Hierarchy,
            					'name' => friendlyText($mg2CategoryName),
            					'hierarchy' => $hierarchy
            			));
            			
            		}else{
            			
            			$query = $db->update('mg2_categories_relationship', 
            					array('store_id', 'id'), 
            					array($storeId, $resRel['id']), 
            					array('mg2_hierarchy' => $mg2Hierarchy,
            						'name' => friendlyText($mg2CategoryName)
            					));
            		}
	            }
	        }
	        
	        if($query){
	        	echo "success|Categorias importadas com successo!";
	        }else{
	        	echo "error|Erro ao importar categoria";
	        }
	        
	        break;
	        
	    case "add_category_relationship":
	    	
	    	$mg2ParentId = $_REQUEST['mg2_parent_id'];
	        $mg2CategoryId = $_REQUEST['mg2_category_id'];
	        $categoryId = $_REQUEST['category_id'];
	        $parentId = $_REQUEST['parent_id'];
	        
	        if(!empty($mg2CategoryId)){
	        	
	            $sql = "SELECT hierarchy FROM `category` WHERE store_id = {$storeId}
                AND id = {$categoryId} and parent_id = {$parentId}";
	            $query = $db->query($sql);
	            $category = $query->fetch(PDO::FETCH_ASSOC);
	            
	            if(!empty($category['hierarchy'])){
	            	
		            $sqlCategory = "SELECT * FROM `mg2_categories_relationship` WHERE store_id = {$storeId}
		            AND parent_id = '{$parentId}' AND category_id = '{$categoryId}' AND hierarchy LIKE '{$category['hierarchy']}'";
		            $queryCategory = $db->query($sqlCategory);
		            $categories = $queryCategory->fetch(PDO::FETCH_ASSOC);
		            
		            if(isset($categories['category_id']) && !empty($categories['category_id'])){
		            	
		            	$queryMg2 = $db->update('mg2_categories_relationship',
			            			array('store_id', 'id'),
			            			array($storeId, $categories['id']),
			            			array('parent_id' => '', 'category_id' => '', 'hierarchy' => '')
		            			);
		            	
		            }else{
		            	
		            	$sqlMg2 = "SELECT * FROM `mg2_categories_relationship` WHERE store_id = {$storeId}
		            	AND mg2_category_id = {$mg2CategoryId} and mg2_parent_id = {$mg2ParentId}";
		            	$queryMg2 = $db->query($sqlMg2);
		            	$categoryMg2 = $queryMg2->fetch(PDO::FETCH_ASSOC);
		            	 
		            	if(empty($categoryMg2['category_id'])){
		            		 
		            		$queryMg2 = $db->update('mg2_categories_relationship',
		            				array('store_id', 'id'),
		            				array($storeId, $categoryMg2['id']),
		            				array('category_id' => $categoryId,
	            						'parent_id' => $parentId,
	            						'hierarchy' => $category['hierarchy'],
	            						'updated' => date('Y-m-d H:i:s')
		            				));
		            		
		            		 
		            	}else{
		            		
		            		if($categoryMg2['category_id'] != $categoryId){
		            			//if exist magento category relationship add another one
		            			$queryMg2 = $db->insert('mg2_categories_relationship', array(
		            					'store_id' => $storeId,
		            					'parent_id' => $parentId,
		            					'category_id' => $categoryId,
		            					'mg2_category_id' => $categoryMg2['mg2_category_id'],
		            					'mg2_parent_id' => $categoryMg2['mg2_parent_id'],
		            					'name' => $categoryMg2['name'],
		            					'hierarchy' => $category['hierarchy'],
		            					'mg2_hierarchy' => $categoryMg2['mg2_hierarchy'],
		            					'updated' => date('Y-m-d H:i:s')
		            					));
		            			
		            		}
		            		
		            	}
		            	
		            }
		            if($queryMg2){
		            	echo "success|Relacionamento cadastrado com successo!";
		            }else{
		            	echo "error|Erro ao relacionar categoria";
		            }
	            }
	        }
	        
	        break;
	    
	}
	
}