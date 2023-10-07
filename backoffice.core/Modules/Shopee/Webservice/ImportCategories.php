<?php

set_time_limit ( 300 );
setlocale(LC_ALL, "pt_BR.utf8");
$path = dirname(__FILE__);
header("Content-Type: text/html; charset=utf-8");

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
    	
        case "import_categories":
            $fp = fopen('./categorias.csv', 'r');
            $i = 0;
            $head = fgetcsv($fp, 1000, ';', '"');
            $data = array();
            
            while($column = fgetcsv($fp, 1000, ';', '"'))
            {
                $column = array_combine($head, $column);
                $hierarchy = explode('-', $column['hierarchy']);
                $hierarchy = str_replace('/', ' > ', $hierarchy[1]);
                $categories = explode(' > ', $hierarchy);
                $data[$i]['root'] =  trim($categories[0]);
                $categories = str_replace('  ', ' ', trim($hierarchy)); 
                $data[$i]['hierarchy'] = $categories;
                $data[$i]['id_category'] = trim($column['id_category']);
                $data[$i]['prazo'] = trim($column['prazo']);
                $i++;
            }
            
            
            foreach ($data as $k => $value){
                
                $sqlRel = "SELECT * FROM module_shopee_categories_hierarchy
    			WHERE id_category = '{$value['id_category']}'";
                $queryRel = $db->query($sqlRel);
                $resRel = $queryRel->fetch(PDO::FETCH_ASSOC);
                
                if(empty($resRel['id_category'])){
                    
                    $query = $db->insert('module_shopee_categories_hierarchy', array(
                        'root' => $value['root'],
                        'hierarchy' => $value['hierarchy'],
                        'id_category' => $value['id_category'],
                        'prazo' => $value['prazo']
                    ));
                    
                }else{
                    $query = $db->update('module_shopee_categories_hierarchy',
                        array('id'),
                        array($resRel['id']),
                        array('root' => $value['root'],
                            'hierarchy' => $value['hierarchy'],
                            'id_category' => $value['id_category'],
                            'prazo' => $value['prazo']
                        ));
                }
                
            }
            
            
            break;
        	
    }
    
}