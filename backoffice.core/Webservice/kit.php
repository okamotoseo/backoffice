<?php
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);


require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;

if (empty ( $action ) and empty ( $storeId )) {
    $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
    $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
    $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
}
if(isset($storeId)){
    
    $db = new DbConnection();
    
	switch($action){
		
	    case "update_kit":
	        
	        $sql = "SELECT distinct product_id FROM product_relational WHERE store_id = {$storeId}";
	        $query = $db->query($sql);
	        $productKits = $query->fetchAll(PDO::FETCH_ASSOC);
	        foreach($productKits as $i => $productKit){
	            
	            $exit = false;
	            $totalCostItem = 0;
	            $totalSalePrice = 0;
	            $price = 0;
	            $sql = "SELECT * FROM product_relational 
                WHERE store_id = {$storeId} AND product_id = {$productKit['product_id']}";
	            $query = $db->query($sql);
	            $relationals = $query->fetchAll(PDO::FETCH_ASSOC);
	            foreach($relationals as $j => $relational){
	                
	                $sqlAP = "SELECT id, quantity, sku, cost, blocked, sale_price FROM available_products 
                    WHERE store_id = {$storeId} AND id = {$relational['product_relational_id']}";
	                $queryAP = $db->query($sqlAP);
	                $resAP = $queryAP->fetch(PDO::FETCH_ASSOC);

	                if ($resAP['blocked'] == "T"){
	                    $resAP['quantity'] = 0;
	                }
	                
	                $price = $resAP['sale_price'];
	                 
	                if($relational['dynamic_price'] == 'T'){
	                    
	                    if(isset($productRelational['discount_fixed']) && !empty($relational['discount_fixed'])){
	                        $price = $relational['discount_fixed'] > 0 ? ($price - $relational['discount_fixed']) : $price;
	                    }
	                    if(isset($relational['discount_percent']) && !empty($relational['discount_percent'])){
	                        $price = $relational['discount_percent'] > 0 ? ($price -  ( $price * $relational['discount_percent'] ) / 100 ) : $price;
	                    }
	                    
	                }else{
    	                if($relational['dynamic_price'] == 'F'){
    	                    if($relational['fixed_unit_price'] > 0){
    	                        $price = $relational['fixed_unit_price'];
    	                    }
    	                }
	                }
	                if($price > 0){
	                    $totalSalePrice += ($price * $relational['qtd']);
	                }
	                if($resAP['cost'] > 0){
	                	$totalCostItem += ($resAP['cost'] * $relational['qtd']);
	                }
	                if($resAP['quantity'] >= $relational['qtd']){
	                    
	                    $stock[] = floor($resAP['quantity'] / $relational['qtd']);
	                }else{
	                    
	                    //atualiza para zero e sai do laco
	                    $exit = true;
	                }
	                
	            }
// 	            pre($stock);
	            $totalSalePrice = number_format($totalSalePrice, 2, '.', '');
// 	            pre($totalCostItem);
	            if(isset($stock)){
	                
    	            $stockMin = $exit ? 0 :  min($stock) ;
//     	            pre($stockMin);
	                $query = $db->update('available_products',
	                		array('store_id','id'), 
	                		array($storeId, $productKit['product_id']),
	                        array('cost' => $totalCostItem,
	                            'sale_price' =>  $totalSalePrice,
	                            'quantity' => $stockMin, 
	                            'flag' => 1, 
	                            'updated' =>  date("Y-m-d H:i:s")  
	                		)); 
// 	                if($query){
	                pre(array('sku' => $resAP['sku'], 'product_id' => $productKit['product_id'], 'sale_price' =>  $totalSalePrice, 'total_cost' =>  $totalCostItem, 'quantity' => $stockMin, 'flag' => 1, 'updated' =>  date("Y-m-d H:i:s"), 'id' => $productKit['product_id']));
// 	                }
	            
	            }
	           
	            unset($stock);
	            
	        }
	        
	        break;
	   
	}
	
	
}