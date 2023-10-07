<?php
set_time_limit ( 300 );
setlocale(LC_ALL, "pt_BR.utf8");
$path = dirname(__FILE__);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
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
        case "save_category_xml_relationship":
            
            $shopeeCategoryId = $_REQUEST['id_category'];
            
            $categoryId = $_REQUEST['category_id'];
            
            $childCategory = trim($_REQUEST['child_category']);
            
            $idHierarchy = trim($_REQUEST['id_hierarchy']);
            
            if(!empty($childCategory)){
                
                $sqlCategoryShopee = "SELECT * FROM `module_shopee_categories_hierarchy` WHERE  id_category = {$shopeeCategoryId} ";
                $queryCategoryShopee = $db->query($sqlCategoryShopee);
                $shopeeCategory = $queryCategoryShopee->fetch(PDO::FETCH_ASSOC);
                
                if(!empty($shopeeCategory)){
                    
                    $sqlCategoryRel = "SELECT * FROM `module_shopee_categories_xml_relationship` WHERE
                        store_id = {$storeId}  AND hierarchy LIKE '{$idHierarchy}'";
                    $queryCategoryRel = $db->query($sqlCategoryRel);
                    $categoriesRel = $queryCategoryRel->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($categoriesRel['id']) && !empty($categoriesRel['id'])){
                        
                        $query = $db->update('module_shopee_categories_xml_relationship',
                            array('store_id', 'id'),
                            array($storeId, $categoriesRel['id']),
                            array('category_id' =>  $categoryId,
                                'hierarchy' =>  $idHierarchy,
                                'id_category' => $shopeeCategory['id_category'],
                                'shopee_root' => $shopeeCategory['root'],
                                'shopee_hierarchy' =>  $shopeeCategory['hierarchy'],
                                'type' => 'XML'
                            )
                            );
                        
                    }else{
                        
                        $query = $db->insert('module_shopee_categories_xml_relationship', array(
                            'store_id' => $storeId,
                            'category_id' =>  $categoryId,
                            'hierarchy' =>  $idHierarchy,
                            'id_category' => $shopeeCategory['id_category'],
                            'shopee_root' => $shopeeCategory['root'],
                            'shopee_hierarchy' =>  $shopeeCategory['hierarchy'],
                            'type' => 'XML'
                        ));
                        
                        
                    }
                    if($query){
                        echo "success|{$categoryId}|Relacionamento cadastrado com successo!";
                    }else{
                        echo "error|Erro ao relacionar categoria";
                    }
                }
            }
            break;
            
        case "save_category_relationship":
            
            $shopeeCategoryId = $_REQUEST['id_category'];
            
            $categoryId = $_REQUEST['category_id'];
            
            if(!empty($categoryId)){
                
                $sqlCategoryShopee = "SELECT * FROM `module_shopee_categories_hierarchy` WHERE  id_category = {$shopeeCategoryId} ";
                $queryCategoryShopee = $db->query($sqlCategoryShopee);
                $shopeeCategory = $queryCategoryShopee->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT * FROM `category` WHERE store_id = {$storeId}  AND id = {$categoryId}";
                $query = $db->query($sql);
                $category = $query->fetch(PDO::FETCH_ASSOC);
                
                if(!empty($category['id']) && !empty($shopeeCategory)){
                    
                    $sqlCategoryRel = "SELECT * FROM `module_shopee_categories_relationship` WHERE 
                        store_id = {$storeId}  AND category_id LIKE '{$category['id']}'";
                    $queryCategoryRel = $db->query($sqlCategoryRel);
                    $categoriesRel = $queryCategoryRel->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($categoriesRel['category_id']) && !empty($categoriesRel['category_id'])){
                        
                        $query = $db->update('module_shopee_categories_relationship',
                            array('store_id', 'id'),
                            array($storeId, $categoriesRel['id']),
                            array('category_id' =>  $category['id'],
                                'hierarchy' =>  $category['hierarchy'],
                                'id_category' => $shopeeCategory['id_category'],
                                'shopee_root' => $shopeeCategory['root'],
                                'shopee_hierarchy' =>  $shopeeCategory['hierarchy']
                                )
                            );
                        
                    }else{
                            
                        $query = $db->insert('module_shopee_categories_relationship', array(
                            'store_id' => $storeId,
                            'category_id' =>  $category['id'],
                            'hierarchy' =>  $category['hierarchy'],
                            'id_category' => $shopeeCategory['id_category'],
                            'shopee_root' => $shopeeCategory['root'],
                            'shopee_hierarchy' =>  $shopeeCategory['hierarchy']
                        ));
                        
                        
                    }
                    if($query){
                        echo "success|{$categoryId}|Relacionamento cadastrado com successo!";
                    }else{
                        echo "error|Erro ao relacionar categoria";
                    }
                }
            }
            break;
            
        case "get_categories_child":
            $id = isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? trim($_REQUEST['id']) : null ;
            $rootCategory = isset($_REQUEST['root_category']) && !empty($_REQUEST['root_category']) ? trim($_REQUEST['root_category']) : null ;
            $children = '';
            $hierarchy = array();
            if(!isset($rootCategory)){
                echo "error|categoria raiz não localizada {$rootCategory}";
                die;
            }
            
            $sql = "SELECT * FROM module_shopee_categories_hierarchy WHERE root LIKE '{$rootCategory}' Order BY hierarchy ASC";
            $query = $db->query($sql);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $i => $res){
                $hierarchy[] = array('id' => $res['id'], 'root' => $res['root'], 'hierarchy' =>  $res['hierarchy'], 'id_category' => $res['id_category']);
            }
            
            if(isset($hierarchy[0])){
                $children = "<option value='select' selected>--Selecione</option>";
//                 $children = "<select class='form-control categories_child' id='children-{$rootCategory}' name='hierarchy-child'>";
                foreach($hierarchy as $i => $child){
                    $children .= "<option value='{$child['id_category']}'>{$child['hierarchy']}</option>"; 
                }
//                 $children .= "</select>";
                echo "success|{$id}|{$children}";
            }else{
                 echo "error|Hierarchy not found...";
            }
            break;
            
    }
    
}