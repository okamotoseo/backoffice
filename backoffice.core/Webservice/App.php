<?php
header("Content-Type: text/html; charset=utf-8");
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Models/Home/DashboardModel.php';
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
		
		case"logout":
			$userId = isset($_REQUEST["user_id"]) && $_REQUEST["user_id"] != "" ? $_REQUEST["user_id"] : null ;
		
			if(!isset($userId)){
				return;
			}

			$query = $db->update('users',
					array('store_id', 'id'),
					array($storeId, $userId),
					array('session_id'  => ''));
			
			if($query){
				echo "success|{$userId}";
			}
			
			
			
			break;
	    
	    case "dashboard-orders":
	        
	        $dashboardModelo = new  DashboardModel($db, null, $storeId);
	        $ordersDay = $dashboardModelo->getOrdersDay();
	        $orders = [];
	        $ordersMonth = [];
	        $count = 0;
	        
	        $marketplaces = array("Mercadolivre" => 0, 
	            "Amazon" => 0, 
	            "Lojas Americanas" => 0, 
	            "Submarino" => 0, 
	            "Ecommerce" => 0,  
	            "Shoptime" => 0,  
	            "Shopee" => 0,  
	            "Tray" => 0,  
	            "Televendas" => 0,  
	            "MAGAZINE LUIZA" => 0);
	        
	        foreach($ordersDay as $key => $rowOrder){
	            
	            foreach($marketplaces as $mkt => $val){
	                if(!isset($orders[$rowOrder['date']][$mkt])){
	                       $orders[$rowOrder['date']][$mkt] = 0.00;
	                }
// 	                if(!isset($orders['label'][$mkt]['value'])){
// 	                    $ordersMonth['label'][$mkt]['value'] = $val;
// 	                }
	            }
	            
	            $orders[$rowOrder['date']][$rowOrder['Marketplace']] += $rowOrder['totalDay'];
	            
	        }
// 	        $ordersMonth[] = array('label' => $rowOrder['Marketplace'], 'value' => $rowOrder['totalDay'];
	        $report = [];
	        foreach($orders as $key => $order){
// 	            pre($order);
// 	            $resport[] = array("y" => $key, 'item1' => $order['Mercadolivre'], 'item2' => $order['Lojas Americanas'] , 'item3' => $order['Onbi'] );
// 	            $resport[] = array('y' => $key, '1' => $order['Mercadolivre'], '2' => $order['Lojas Americanas']);
	            $vals['y'] = $key;
	            $i = 1;
	            foreach($order as $k => $val){
	                
	                $vals[$i] = $val;
	                $i++;
	                
	            }
	            $resport[] = $vals;
	        }
	        
	        $json = json_encode($resport);
	        echo $json;die;
	        
	        
// 	        $ordersMonth = $dashboardModelo->getOrdersMonth();
// 	        pre($ordersMonth);die;
	        
	        
	        break;
	    
	        
	    case "insert-handling-pack-in" :
	        $PedidoId = $_REQUEST['pedido_id'];
	        
	        $shippindSendId = 86;
	        
	        echo $sqlVerify = "SELECT id, PedidoId, shipping_id FROM orders WHERE store_id = {$storeId} AND PedidoId LIKE '{$PedidoId}'";
            $queryOrder = $db->query($sqlVerify);
            $orderRes = $queryOrder->fetch(PDO::FETCH_ASSOC);
            $PedidoId = $orderRes['PedidoId'];
            
 
            echo $sqlVerify = "SELECT * FROM shipping_send_code WHERE store_id = {$storeId} AND
            shipping_send_id = {$shippindSendId} AND code LIKE '{$orderRes['shipping_id']}'";
            $queryVerify = $db->query($sqlVerify);
            $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
//             pre($resVerify);die;
            if(empty($resVerify['id'])){
                $created = date("Y-m-d H:i:s");
                $query = $db->insert('shipping_send_code', array(
                    'store_id'  => $storeId,
                    'PedidoId'  => $PedidoId,
                    'shipping_send_id'  => $shippindSendId,
                    'code'  => $orderRes['shipping_id'],
                    'created'  => '2020-05-06 15:00:00',
                    'user'  => 'Willians'
                ));
                $id = $db->last_id;
                if($query){
                    echo "success|{$id}|{$orderRes['shipping_id']}|{$shippindSendId}|".dateTimeBr('2020-05-06 15:00:00', "/")."";
                }
            }
	        break;
	    case "handling-pack-in" :
	        $user = $_POST['user'];
	        $barcode = $_POST['barcode'];
	        $shippindSendId = $_POST['shippind_send_id'];
	        $company = $_POST['company'];
	        
	        $sqlStatus = "SELECT status FROM `shipping_send`  WHERE store_id = {$storeId} AND id = {$shippindSendId}";
	        $queryStatus = $db->query( $sqlStatus );
	        $resStatus = $queryStatus->fetch(PDO::FETCH_ASSOC);
	        $status = $resStatus['status'];

	        if($status != 'closed'){
	        
    	        if(!empty($shippindSendId)){
    	            
    	            $sqlVerify = "SELECT id, PedidoId FROM orders WHERE store_id = {$storeId} AND shipping_id LIKE '{$barcode}'";
    	            $queryOrder = $db->query($sqlVerify);
    	            $orderRes = $queryOrder->fetch(PDO::FETCH_ASSOC);
    	            $PedidoId = $orderRes['PedidoId'];
    	            
    	            if($company == "mercado_envios" AND empty($PedidoId)){
    	                echo "error| Pedido não localizado para o código {$barcode}";
    	                exit;
    	            }
    	            $sqlVerify = "SELECT * FROM shipping_send_code WHERE store_id = {$storeId} AND 
                    shipping_send_id = {$shippindSendId} AND code LIKE '{$barcode}'";
    	            $queryVerify = $db->query($sqlVerify);
    	            $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
    	            if(empty($resVerify['id'])){
    	                $created = date("Y-m-d H:i:s");
    	                $query = $db->insert('shipping_send_code', array(
    	                    'store_id'  => $storeId,
    	                    'PedidoId'  => $PedidoId,
    	                    'shipping_send_id'  => $shippindSendId,
    	                    'code'  => $barcode,
    	                    'created'  => $created,
    	                    'user'  => $user
    	                ));
    	                $id = $db->last_id;
    	                if($query){
    	                    echo "success|{$id}|{$barcode}|{$shippindSendId}|".dateTimeBr($created, "/")."";
    	                }
    	            }
    	        }
	        }else{
	            echo "error| Remessa fechada!";
	        }
	        break;
	        
	        
	        
        case "update_categories" :
            
            $productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
            
            $category = isset($_REQUEST["category"]) && $_REQUEST["category"] != "" ? $_REQUEST["category"] : null ;
            
            $productIds = is_array($productId) ? $productId : array($productId) ;
            
            if($category){
            
                foreach($productIds as $ind => $id){
                    
                    $query = $db->update('available_products', 
                        array('store_id', 'id'), 
                        array($storeId, $id), 
                        array('category'  => trim($category), 'updated'  => date("Y-m-d H:i:s"))
                        );
                }
                if($query){
                    echo "success|Categorias atualizadas com sucesso!";
                }else{
                    echo "error|Erro ao atualiar categoria!";   
                }
            }
            break;
            
        case "update_attributes" :
            
            $productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
            
            $attribute = isset($_REQUEST["attribute"]) && $_REQUEST["attribute"] != "" ? $_REQUEST["attribute"] : null ;
            
            $attributeId = isset($_REQUEST["attribute_id"]) && $_REQUEST["attribute_id"] != "" ? $_REQUEST["attribute_id"] : null ;
            
            $productIds = is_array($productId) ? $productId : array($productId) ;
            
            if($attribute){
                
                foreach($productIds as $ind => $id){
                    
                    $query = $db->update('available_products',
                        array('store_id', 'id'),
                        array($storeId, $id),
                        array($attributeId  => trim($attribute), 'updated'  => date("Y-m-d H:i:s"))
                        );
                }
                if($query){
                    echo "success|Atributo atualizadas com sucesso!";
                }else{
                    echo "error|Erro ao atualiar atributo!";
                }
            }
            break;
                
	    case "close-print-shipping":
	        
	        $shippindSendId = $_POST['shippind_send_id'];
	        
	        if(!empty($shippindSendId)){
	            
	            
	            $sqlVerify = "SELECT * FROM shipping_send WHERE store_id = {$storeId} AND id = {$shippindSendId}";
	            $queryVerify = $db->query($sqlVerify);
	            $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	            if($resVerify['status'] != 'closed'){
    	            if(!empty($resVerify['id'])){
    	                $created = date("Y-d-m H:i:s");
    	                $query = $db->update('shipping_send', 'id', $resVerify['id'], array(
    	                    'status'  => "closed",
    	                    'sent'  => date("Y-m-d H:i:s")
    	                ));

    	                $sqlPedidoIds = "SELECT PedidoId FROM shipping_send_code WHERE store_id = {$storeId} AND shipping_send_id = {$resVerify['id']}";
    	                $queryPedidoIds = $db->query($sqlPedidoIds);
    	                $resPedidosIds = $queryPedidoIds->fetchAll(PDO::FETCH_ASSOC);
    	                foreach($resPedidosIds as $k => $pedidoId){
    	                    $sqlUpdateShipped = "UPDATE orders SET Status = 'shipped' WHERE store_id = {$storeId} AND PedidoId = '{$pedidoId['PedidoId']}'";
    	                    $queryShipped = $db->query($sqlUpdateShipped);
    	                }

    	                
    	            }
	            }
	            
	            echo "success|{$resVerify['id']}";
	        }
	        
	        break;
	        
	        
	        
	    case "remove-pack-shipping" :
	        
	        $shippingSendCodeId = $_POST['shipping_send_code_id'];
	        $barcode = $_POST['barcode'];
	        $shippindSendId = $_POST['shippind_send_id'];
	        if(!empty($shippindSendId) AND !empty($barcode)){
	            
	           $sqlVerify = "SELECT * FROM shipping_send_code WHERE store_id = {$storeId} AND
                shipping_send_id = {$shippindSendId} AND code LIKE '{$barcode}'";
	            $queryVerify = $db->query($sqlVerify);
	            $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	            
	            if(!empty($resVerify['id'])){
	                $sqlDelete = "DELETE FROM shipping_send_code WHERE store_id = {$storeId} AND 
                    shipping_send_id = {$shippindSendId} AND code LIKE '{$barcode}' ";
	                $queryDelete = $db->query($sqlDelete);
	                if($queryDelete){
	                    echo "success|{$resVerify['id']}|{$barcode}|{$shippindSendId}";
	                }
	            }
	        }
	        break;
	        
	    case "remove-shipping-send" :
	        $user = $_POST['user'];
	        $shippindSendId = $_POST['shippind_send_id'];
	        
	        $sqlStatus = "SELECT status FROM `shipping_send`  WHERE store_id = {$storeId} AND id = {$shippindSendId}";
	        $queryStatus = $db->query( $sqlStatus );
	        $resStatus = $queryStatus->fetch(PDO::FETCH_ASSOC);
	        $status = $resStatus['status'];
	        
	        if($status != 'closed'){
	            
	            if(!empty($shippindSendId)){
	                
	                $sqlDeleteSSC = "DELETE FROM shipping_send_code WHERE store_id = {$storeId} AND shipping_send_id = {$shippindSendId} ";
	                $queryDeleteSSC = $db->query($sqlDeleteSSC);
	                
	                $sqlDeleteSS = "DELETE FROM `shipping_send`  WHERE store_id = {$storeId} AND id = {$shippindSendId}";
	                $queryStatusSS = $db->query( $sqlDeleteSS );
	                
	                echo "success|{$shippindSendId}";
	                
	            }
	            
	        }else{
	            echo "error| Remessa fechada!";
	        }
	        break;
	        
	        
	    case "category_attributes":
	        echo $_REQUEST['category'];
	        break;
	        
	        
	    case "add_product_relational":
	        $productId = $_REQUEST['product_id'];
	        echo $productId."teste";
	        break;
	        
	    case "save_qtd_product_relational":
	        $productId = $_REQUEST['product_id'];
	        $qtd = $_REQUEST['qtd'];
	        echo $productId." ".$qtd;
	        break;
	        
	    case "autocomplete_product_id":
	        $productId = $_REQUEST["product_id"];
	        $term = $_REQUEST["term"];
	        $type = !empty($_REQUEST["type"]) ? $_REQUEST["type"] : "sku";
	        
	        $arr = getProductFilterId($db, $storeId,  $productId, $type, $term, 10);
	        
	        echo $callback . '(' . json_encode($arr) . ')';
	        break;
	        
	    case "autocomplete_cpfcnpj":
	        $term = $_REQUEST["term"];
	        $type = !empty($_REQUEST["type"]) ? $_REQUEST["type"] : "cpfcnpj";
	        
	        $arr = getCustomerFilterCpfCnpj($db, $storeId,  $type, $term, 10);
	        
	        echo $callback . '(' . json_encode($arr) . ')';
	        break;
	        
	    case "autocomplete_product_attr":
	        $term = $_REQUEST["term"];
	        $type = $_REQUEST["type"];
	        
	        $arr = getFilterId($db, $storeId, $type, $term, 10);
	        
	        echo $callback . '(' . json_encode($arr) . ')';
	        break;
	        
	    case "autocomplete_attributes":
	        $term = $_REQUEST["term"];
	        $categoryId = $_REQUEST["category_id"];
	        $attributeId = $_REQUEST["attribute_id"];
	        
	        $arr = getFilterAttribute($db, $storeId, $attributeId, $categoryId,  $term);
	        
	        echo $callback . '(' . json_encode($arr) . ')';
	        
	        break;
	        
	    case 'update_parent_id':
	        
	        $parentId = $_REQUEST['parent_id'];
	        $newId =  $_REQUEST['newId'];
	        $color =  $_REQUEST['color'];
	        
	        $sql = "UPDATE available_products SET parent_id = '{$newId}' 
            WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}'
            AND color LIKE '{$color}'";
	        $query = $db->query($sql);
	        if ( ! $query ) {
	           
	            echo "success";
	            
	        }
	        
	        break;
	        
	    case 'block_products':
	        
	        $productId =  $_REQUEST['product_id'];
	        $productIds = is_array($productId) ? $productId : array($productId) ;
	        foreach($productIds as $i => $id){
    	        $sql = "UPDATE available_products SET blocked = 'T'
                WHERE store_id = {$storeId} AND id = {$id}";
    	        $query = $db->query($sql);
    	        if ( ! $query ) {
    	            
    	            echo "success";
    	            
    	        }
	        }
	        break;
	    
	    case "get_category_child" :
	        $root = $_REQUEST['root'];
	        if(isset($root)){
    	        $query = $db->query("SELECT * FROM `category` 
                WHERE hierarchy LIKE '{$root}%' 
                AND `store_id` = ? AND parent_id != 0",
    	        array($storeId)
    	            );
    	        if ( ! $query ) { 
    	            return array();
    	        }
    	        $childCategories = $query->fetchAll(PDO::FETCH_ASSOC);
    	        $htmlChildOption='';
    	        foreach($childCategories as $key => $value){
    	            $htmlChildOption .= "<option value='{$value['hierarchy']}'>{$value['hierarchy']}</option>";
    	            
    	            
    	        }
    	        
    	        echo $htmlChildOption;
	        }
	        
	        break;
	        
	    case "remove_product_relational":
	        $productId = $_REQUEST["product_id"];
	        $productRelationalId = $_REQUEST["product_relational_id"];
	        
	       $sql = "DELETE FROM product_relational 
            WHERE store_id = {$storeId} AND product_id = {$productId} AND product_relational_id = {$productRelationalId}";
	        $query = $db->query($sql);
	        if(!$query){
	            echo "error|Erro ao remover produto relacionado!";
	        }else{
	            echo "success|Produto removido com sucesso!";
	        }
	        break;
	        
	}
	
	
}