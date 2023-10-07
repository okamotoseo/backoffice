<?php

set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-ConfigMws.php';
require_once $path .'/../Class/class-MWS.php';
require_once $path .'/../Models/API/ProductsModel.php';
require_once $path .'/functions.php';


$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;
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
    
    $request = "System";
    
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
    switch($action){
        
        case "list_matching_products":
            
        	
            $productsModel = new ProductsModel($db, null, $storeId);

            $productsModel->queryId = $_REQUEST['ean'];
            
            $responseProducts = $productsModel->ListMatchingProductsRequest();
            
            pre($responseProducts);
            
            break;
        case 'unmatch_products':
        	
        	$sql = "DELETE FROM az_products_feed WHERE store_id = {$storeId}";
        	$query = $db->query($sql);
        	if(!$query){
        		echo 'error|Erro ao remover combinações.';die;
        	}
        	echo 'success|';
        	
        	break;
        	
        case 'unmatch_products_not_published':
        		
        		$sql = "SELECT id, sku FROM az_products_feed WHERE store_id = {$storeId} AND connection LIKE 'match'";
        		$query = $db->query($sql);
        		$fetch = $query->fetchAll(PDO::FETCH_ASSOC);
        		$ids = array();
        		foreach($fetch as $k => $product){
        			
        			$sqlVerify = "SELECT id, title, extra_information, price FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}'";
        			$verifyQuery = $db->query($sqlVerify);
        			$verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
        			
        			if(isset($verify['id'])){
        			
	        			$parts = explode('/', $verify['extra_information']);
	        			$parts = count($parts) > 1 ? $parts : array() ; 
	        			sort($parts);
		        		$publications = getPublicationsBySku($db, $storeId, $product['sku']);
		        		if(empty($publications)){
		        			if(intval($parts[0]) > 1 OR $verify['price'] < 9){
		        				pre($verify);
				        		$data = array(
				        				'connection' => 'not_match',
				        				'updated' => date("Y-m-d H:i:s")
				        		
				        		);
				        		
				        		$query = $db->update('az_products_feed', array('store_id', 'sku'), array($storeId,  $product['sku']), $data);
				        		pre($query);
				        		pre($data);
		        			}
		        		}
	        			
	        		
        			}
        		
        		}
        		echo 'success|';
        		 
        	break;
        case "get_matching_products":
            
            $productsModel = new ProductsModel($db, null, $storeId);
            $idList = new MarketplaceWebServiceProducts_Model_IdListType();

            $sql = "SELECT count(product_id) as total FROM az_products_feed WHERE az_products_feed.store_id = {$storeId} AND connection != 'match'";
            
            $queryCount = $db->query($sql);
            $count = $queryCount->fetch(PDO::FETCH_ASSOC);
            $totalReg = $count['total'];
            $limit = 1;
            $offset = 0;
            $pages = ceil($totalReg/$limit);
//             $pages = 5;
            
            do{
                
                $sqlParents = "SELECT az_products_feed.* FROM az_products_feed WHERE az_products_feed.store_id = {$storeId} AND connection != 'match' LIMIT {$limit} OFFSET {$offset}";
                $query = $db->query($sqlParents);
                $fetch = $query->fetchAll(PDO::FETCH_ASSOC);
                $ids = array();
                foreach($fetch as $k => $ean){
                    
                    $ids[] = $ean['ean'];
                    
                }
                
//                 $ids[] = '7898461962967';
                $idList->setId($ids);
                $productsModel->GetMatchingProductForIdRequest($idList);
                $offset = $offset + $limit;
                
            }while($offset < $pages);
            
            
            break;
        
       
    }
}
            
   