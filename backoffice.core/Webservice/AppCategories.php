<?php
header("Content-Type: text/html; charset=utf-8");
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/../Models/Products/PublicationsModel.php';

require_once $path .'/functions.php';


$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;
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
    
	switch($action){
		
		case 'change_products_category':
			
			$parentIdFrom = $_REQUEST['parent_id_from'];
			$categoryIdFrom = $_REQUEST['category_id_from'];
			$hierarchyFrom = $_REQUEST['category_from'];
			$categoryIdTo = $_REQUEST['category_id_to'];
			$parentIdTo = $_REQUEST['parent_id_to'];
			$categoryTo = $_REQUEST['category_tom'];
			
			$sqlVerifyFrom = "SELECT hierarchy FROM category WHERE store_id = {$storeId} AND parent_id = {$parentIdFrom} AND id = {$categoryIdFrom}";
			$queryFrom = $db->query($sqlVerifyFrom);
			$resultFrom = $queryFrom->fetch(PDO::FETCH_ASSOC);
			
			if(!empty($resultFrom['hierarchy'])){
				
				$sqlVerifyTo = "SELECT hierarchy FROM category WHERE store_id = {$storeId} AND parent_id = {$parentIdTo} AND id = {$categoryIdTo}";
				$queryTo = $db->query($sqlVerifyTo);
				$resultTo = $queryTo->fetch(PDO::FETCH_ASSOC);
				
				if(!empty($resultTo['hierarchy'])){
					
					$queryUpdate = $db->update('available_products',
							array('store_id', 'category'),
							array($storeId, $resultFrom['hierarchy']),
							array('category' => $resultTo['hierarchy'])
							);
					
					if($queryUpdate){
						
						$queryUpdateMl = $db->update('ml_category_relationship',
								array('store_id', 'category'),
								array($storeId, $resultFrom['hierarchy']),
								array('category' => $resultTo['hierarchy'])
								);
						
						if(!$queryUpdateMl){
							echo "error|Erro ao substituir categorias do módulo Mercadolivre.";
						}
						$queryUpdateTray = $db->update('module_tray_categories',
								array('store_id', 'hierarchy', 'parent_id', 'category_id'),
								array($storeId, $resultFrom['hierarchy'], $parentIdFrom, $categoryIdFrom),
								array(
										'hierarchy' => $resultTo['hierarchy'],
										'parent_id' => $parentIdTo,
										'category_id' => $categoryIdTo
									)
								);
						
						if(!$queryUpdateTray){
							echo "error|Erro ao substituir categorias do módulo Tray.";
						}
					}
					
				}
				
			}
			
			if($queryUpdate){
				echo "success|";
			}else{
				echo "error|";
			}
			
			break;
	}
	
}