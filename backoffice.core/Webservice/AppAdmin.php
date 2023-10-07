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
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
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
	    
	    case "charge_accounts":
	        
	        require_once $path.'/../Models/Report/SalesModel.php';
	        
	        $ReportSalesModel = new  SalesModel($db);
	        $ReportSalesModel->store_id = $storeId;
	        $ReportSalesModel->DataPedido = "01/11/2021";
	        $ReportSalesModel->DataPedidoAte = "26/11/2021";
	        $sales = $ReportSalesModel->GetSales();
// 	        pre($sales);
	        $totalPedido = 0.00;
	        foreach($sales as $k => $order){
	            
	            $totalPedido += $order['ValorPedido'];
	            
	            
	        }
	        pre($totalPedido);
	        break;
		
		case "export_products_inter_stores":
			
			
			if(isset($productId)){
				
				$storeToImport = $_REQUEST['store_import'];
				 
				$productIds = is_array($productId) ? $productId : array($productId) ;
				 
				foreach($productIds as $ind => $id){
					
					 
					echo $sql = "INSERT INTO `available_products`(`account_id`,`store_id`, `sku`, `parent_id`,  `title`, `color`, `variation_type`, `variation`, `brand`, `reference`,
					`collection`, `category`, `quantity`, `price`, `start_promotion`, `end_promotion`, `sale_price`, `promotion_price`, `cost`,
					`weight`, `height`, `width`, `length`, `ean`, `ncm`, `description`)
			
					SELECT {$storeToImport} as account_id, {$storeToImport} as store_id, CONCAT(sku, '-exported') as sku, CONCAT(parent_id, '-exported') as parent_id,`title`, `color`, `variation_type`, `variation`, `brand`, `reference`, `collection`, `category`, `quantity`, `price`,
					`start_promotion`, `end_promotion`, `sale_price`, `promotion_price`, `cost`, `weight`,  `height`, `width`, `length`, `ean`, `ncm`, `description`
					FROM `available_products` WHERE id = {$id}";
					
					$query = $db->query($sql);
					$stmt= $db->query("SELECT LAST_INSERT_ID()");
					$newProductId = $stmt->fetchColumn();
					if($query){
						 
						echo $sqlAttr = "INSERT INTO `attributes_values`(`store_id`, `product_id`, `id_attribute`, `attribute_id`, `name`, `value`, `marketplace`)
						SELECT {$storeToImport}  as store_id, {$newProductId} as product_id, `id_attribute`, `attribute_id`, `name`, `value`, `marketplace` FROM `attributes_values`
						WHERE store_id = {$storeId} AND product_id = {$id}";
						$queryAttr = $db->query($sqlAttr);
						echo "success|Produto duplicado com sucesso!";
					}else{
						echo "error|Erro ao dulicar produto";
					}
					 die;
				}
				 
			}
			
			
			break;
			
			
	    
	    case "copy_available_products":
	        
	        if(isset($productId)){
	            
	            $productIds = is_array($productId) ? $productId : array($productId) ;
	            
	            foreach($productIds as $ind => $id){
	        
    	           $sql = "INSERT INTO `available_products`(`account_id`,`store_id`, `sku`, `parent_id`,  `title`, `color`, `variation_type`, `variation`, `brand`, `reference`, 
                    `collection`, `category`, `quantity`, `price`, `start_promotion`, `end_promotion`, `sale_price`, `promotion_price`, `cost`, 
                    `weight`, `height`, `width`, `length`, `ean`, `ncm`, `description`)
        
                    SELECT `account_id`, `store_id`, CONCAT(sku, '-copy') as sku, CONCAT(parent_id, '-copy') as parent_id,`title`, `color`, `variation_type`, `variation`, `brand`, `reference`, `collection`, `category`, `quantity`, `price`, 
                    `start_promotion`, `end_promotion`, `sale_price`, `promotion_price`, `cost`, `weight`,  `height`, `width`, `length`, `ean`, `ncm`, `description` 
                    FROM `available_products` WHERE store_id = {$storeId} AND id = {$id}";
        	        $query = $db->query($sql);
        	        $stmt= $db->query("SELECT LAST_INSERT_ID()");
        	        $newProductId = $stmt->fetchColumn();
        	        if($query){
        	            
        	            $sqlAttr = "INSERT INTO `attributes_values`(`store_id`, `product_id`, `id_attribute`, `attribute_id`, `name`, `value`, `marketplace`)
                            SELECT `store_id`, {$newProductId} as product_id, `id_attribute`, `attribute_id`, `name`, `value`, `marketplace` FROM `attributes_values` 
                            WHERE store_id = {$storeId} AND product_id = {$id}";
        	            $queryAttr = $db->query($sqlAttr);
        	            echo "success|Produto duplicado com sucesso!";
        	        }else{
        	            echo "error|Erro ao dulicar produto";
        	        }
    	        
	            }
	        
	        }
	        
	        break;
	        
	}
	
	
}