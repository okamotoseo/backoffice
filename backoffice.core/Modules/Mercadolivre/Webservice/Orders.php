<?php
// header("Access-Control-Allow-Origin: *");
// die;
// echo phpinfo();die;
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
header("Content-Type: text/html; charset=utf-8");
ini_set ("display_errors", true);
// die;

$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Models/Customers/ManageCustomersModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';
require_once $path .'/../Models/Api/ShipmentsRestModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
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
    if(isset($_SERVER ['argv'] [3])){
        $paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
        $accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
    }
    
    $request = "System";
}

$db = new DbConnection();

$condition = isset($storeId)  ?  "WHERE store_id = {$storeId}" : '' ;

$sqlStores = "SELECT * FROM `module_mercadolivre` {$condition} ORDER BY store_id DESC";

$queryStores = $db->query($sqlStores);

while($stores = $queryStores->fetch(PDO::FETCH_ASSOC)){
    
    $storeId = $stores['store_id'];
   
    require_once $path .'/verifyToken.php';
    
	switch($action){
	    
	    case "list_notifications":
	        $limit = 9;
	        $offset = 1;
	        
	        do{
               
    	        $getOrder = "/missed_feeds?app_id={$resMlConfig['app_id']}&access_token={$resMlConfig ['access_token']}&topic=created_orders&offset={$offset}&limit={$limit}";
    	        $result = $meli->get($getOrder);
    // 	        pre($result);die;
    	        foreach($result['body']->messages as $key => $content){
//     	            pre($content);
        	        $sqlVerify = "SELECT id FROM `ml_notifications` WHERE store_id = {$storeId}
                    AND application_id = {$content->application_id} AND resource LIKE '{$content->resource}'
                    AND user_id LIKE '{$content->user_id}' AND topic LIKE '{$content->topic}'";
        	        $queryVerify = $db->query($sqlVerify);
        	        $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
        	        if(!isset($verify['id'])){
        	            
        	            $sent = isset($content->sent) ? date("Y-m-d H:i:s", strtotime($content->sent) ) :  date("Y-m-d H:i:s") ;
        	            
        	            $received = isset($content->received) ? date("Y-m-d H:i:s", strtotime($content->received) ) :  date("Y-m-d H:i:s") ;
        	            
//         	            $query = $db->insert('ml_notifications', array(
//         	                "store_id" => $storeId,
//         	                "resource" => $content->resource,
//         	                "user_id" => $content->user_id,
//         	                "topic" => $content->topic,
//         	                "application_id" => $content->application_id,
//         	                "attempts" => $content->attempts,
//         	                "sent" => $sent,
//         	                "received" => $received
//         	            ));
//         	            if($query){
//         	                pre($sent);
//         	            }else{
//         	                pre($query);
            	            pre(array(
            	                "store_id" => $storeId,
            	                "resource" => $content->resource,
            	                "user_id" => $content->user_id,
            	                "topic" => $content->topic,
            	                "application_id" => $content->application_id,
            	                "attempts" => $content->attempts,
            	                "sent" => $sent,
            	                "received" => $received
            	            ));
//         	            }
        	        }
        	        
//         	        pre(array(
//         	            "store_id" => $storeId,
//         	            "resource" => $content->resource,
//         	            "user_id" => $content->user_id,
//         	            "topic" => $content->topic,
//         	            "application_id" => $content->application_id,
//         	            "attempts" => $content->attempts,
//         	            "sent" => $sent,
//         	            "received" => $received
//         	        ));
        	        
//         	        $parts = explode("/", $content->resource);
//     	            $orderId = end($parts);
//     	            $getOrderNotification = "/orders/search?seller={$resMlConfig['seller_id']}&q={$orderId}&access_token={$resMlConfig ['access_token']}";
    	            
    	            
//     	            $res = $meli->get($getOrderNotification);
    	            
//     	            pre($res);
        	        
    	        }
    	        $offset += 10;
    	        
	        }while($offset <= 300);
	        
	        break;
	    
        case 'list_orders':
        		
        	$limit = 50;
        	$offset = 0;
        	
        	do{
        		$logOrder = array();
        		$ordersCount = 0;
        		
        		$dateCreatedFrom =  date("Y-m-d\TH:i:s", strtotime("-5 hour")).".000-00:00";
        		$dateCreatedTo = date("Y-m-d\TH:i:s", strtotime("+3 hour")).".000-00:00";
        		
        		$getOrder = "/orders/search?seller={$resMlConfig['seller_id']}&order.date_created.from={$dateCreatedFrom}&order.date_closed.to={$dateCreatedTo}&access_token={$resMlConfig ['access_token']}&offset={$offset}&limit={$limit}";
        		$result = $meli->get($getOrder);
        
//         		pre($result['body']->paging);
        
        		$resultsOrder = isset($result['body']->results) ? $result['body']->results : array($result['body']) ;
        		if($resultsOrder){
        				
        			foreach($resultsOrder as $key => $order){
//         				pre($order);
//         				pre(array('date_created' => $order->date_created, 'id' => $order->id, 'tags' => $order->tags));
        				$logOrder[$key][] = array('date_created' => $order->date_created, 'id' => $order->id, 'tags' => $order->tags);
        				$mlOrderId = isset($order->pack_id) && !empty($order->pack_id) ? $order->pack_id : $order->id;
        				
        				$getOrderBillingInfo = "/orders/{$order->id}/billing_info";
        				$resOrderBillingInfo = $meli->get($getOrderBillingInfo);
        				$billingInfo = $resOrderBillingInfo['body']->billing_info;
        				
        				
        				$orderBuyer = new \stdClass;
        				if(isset($billingInfo->additional_info)){
            				foreach($billingInfo->additional_info as $i => $buyer){
            				    $prop = strtolower($buyer->type);
            				    $orderBuyer->{$prop} = $buyer->value;
            				}
        				}
        				$sku = isset($order->order_items[0]->item->seller_sku) ? $order->order_items[0]->item->seller_sku : $order->order_items[0]->item->seller_custom_field ;
        				
        				$partes = explode("T", $order->date_created);
        				
//         				$tipoPessoa = $order->buyer->billing_info->doc_type == "CNPJ" ? 2 : 1 ;
        				$tipoPessoa = $billingInfo->doc_type == "CNPJ" ? 2 : 1 ;
        				
        				$customerModel = new  ManageCustomersModel($db);
        				$customerModel->store_id = $storeId;
        				$customerModel->Codigo = $order->buyer->id;
        				$customerModel->TipoPessoa = $tipoPessoa;
        
        				if(empty($orderBuyer->first_name)){
        					$customerModel->Nome = $order->buyer->nickname;
        				}else{
        				    $customerModel->Nome = $orderBuyer->first_name." ".$orderBuyer->last_name;
        				}
        				$customerModel->Apelido = $order->buyer->nickname;
        				$customerModel->Email = $order->buyer->email;
        				$customerModel->CPFCNPJ = $billingInfo->doc_number;
        
        				$customerModel->Telefone = isset($order->buyer->phone->area_code) ? $order->buyer->phone->area_code : "";
        				$customerModel->Telefone .= isset($order->buyer->phone->number) ? $order->buyer->phone->number : '' ;
        
        				$customerModel->TelefoneAlternativo = isset($order->buyer->alternative_phone->area_code) ? $order->buyer->alternative_phone->area_code : "";
        				$customerModel->TelefoneAlternativo .= isset($order->buyer->alternative_phone->number) ? $order->buyer->alternative_phone->number : '' ;
        
        				$customerModel->DataCriacao = date("Y-m-d H:i:s", strtotime($order->date_created));
        				$shipmentsRestModel = new ShipmentsRestModel($db, null, $storeId);
        				$shipmentsRestModel->shipment_id = trim($order->shipping->id.'');
        				$shipping = $shipmentsRestModel->getShipment();
        				$shipping = $shipping['body'];
        
        				$state =  explode("-", $shipping->receiver_address->state->id);
        				$customerModel->Endereco = $shipping->receiver_address->street_name;
        				$customerModel->Numero = $shipping->receiver_address->street_number;
        				$customerModel->Complemento = $shipping->receiver_address->comment;
        				$customerModel->Cidade = $shipping->receiver_address->city->name;
        				$customerModel->Estado = trim(end($state));
        				$customerModel->CEP = $shipping->receiver_address->zip_code;
        
        				$customerModel->Marketplace = "Mercadolivre";
        				$customerId = $customerModel->Save();
        				$logOrder[$key][] = array('Customer' => $customerId);
        				if(isset($customerId)){
        
        					$ordersModel = new OrdersModel($db);
        					$ordersModel->store_id = $storeId;
        					$ordersModel->customer_id = $customerId;
        					$ordersModel->PedidoId = $mlOrderId;
        					$ordersModel->CPFCNPJ = $billingInfo->doc_number;
        					if(empty($orderBuyer->first_name)){
        
        						$ordersModel->Nome = $order->buyer->nickname;
        						$ordersModel->NomeDestino  = $order->buyer->nickname;
        
        					}else{
        					    $ordersModel->Nome = $orderBuyer->first_name." ".$orderBuyer->last_name;
        					    $ordersModel->NomeDestino= $orderBuyer->first_name." ".$orderBuyer->last_name;
        
        					}
        
        					$ordersModel->Email = $order->buyer->email;
        					$ordersModel->Telefone = isset($order->buyer->phone->area_code) ? $order->buyer->phone->area_code : "";
        					$ordersModel->Telefone .= isset($order->buyer->phone->number) ? $order->buyer->phone->number : '' ;
        
        					if(isset($shipping->receiver_address)){
        						$ordersModel->Endereco = $shipping->receiver_address->street_name;
        						$ordersModel->Complemento = $shipping->receiver_address->comment;
        						$ordersModel->Numero = $shipping->receiver_address->street_number;
        						$ordersModel->Cidade = $shipping->receiver_address->city->name;
        						$ordersModel->Estado = trim(end($state));
        						$ordersModel->Cep = $shipping->receiver_address->zip_code;
        					}
        
        					$ordersModel->DataPedido = date("Y-m-d H:i:s", strtotime($order->date_created));
        					$ordersModel->FormaPagamento = $order->payments[0]->payment_method_id;
        					$ordersModel->Parcelas = $order->payments[0]->installments;
        					$ordersModel->ValorParcelas = $order->payments[0]->installment_amount;
        
        					$ordersModel->ValorFrete = $shipping->shipping_option->cost;
        
        					$ordersModel->Subtotal = $order->total_amount;
        
        					foreach ($order->payments as $keyP => $orderPayment){
        						if($orderPayment->status == 'approved' ){
        							$ordersModel->ValorPedido += ($orderPayment->transaction_amount + $orderPayment->shipping_cost);//$orderPayment->total_paid_amount;
        							$ordersModel->ValorCupomDesconto +=  $orderPayment->coupon_amount;
        
        						}else{
        
        							$message = "Pedido {$order->id} com status {$orderPayment->status}";
        						}
        
        					}
        					if(empty($ordersModel->ValorPedido)){
        						$ordersModel->ValorPedido = ($order->total_amount + $shipping->shipping_option->cost);
        					}
        
        					$ordersModel->Status = $order->status;
        						
        					$ordersModel->Marketplace = 'Mercadolivre';
        					
        					$newSubtotal = $ordersModel->Subtotal;
        						
        					$newValorPedido = $ordersModel->ValorPedido;
        
        					if(isset($order->pack_id) && !empty($order->pack_id)){
        
        						$queryOrders = $db->query("SELECT * FROM orders
        								WHERE store_id = {$storeId} AND PedidoId = '{$mlOrderId}'");
        						$res = $queryOrders->fetch(PDO::FETCH_ASSOC);
        
        						$logOrder[$key][] = array('status_atual' => $res['Status']);
        						
        						$resSubtotal = isset($res['Subtotal']) ? $res['Subtotal'] : 0 ;
        							
        						$resValorFrete = isset($res['ValorFrete']) ? $res['ValorFrete'] : 0 ;
        							
        						$resValorCupomDesconto = isset($res['ValorCupomDesconto']) ? $res['ValorCupomDesconto'] : 0 ;
        							
        						$resValorPedido = isset($res['ValorPedido']) ? $res['ValorPedido'] : 0 ;
        							
        						$ordersPack = isset($res['orders_pack']) ? json_decode($res['orders_pack']) : array();
        							
        						$logOrder[$key][] = array('id' => $order->id, 'pack' => $ordersPack);
        						
        						if(in_array($order->id, $ordersPack)){
        
        							$ordersModel->Subtotal = $resSubtotal;
        
        							$ordersModel->ValorCupomDesconto = $resValorCupomDesconto;
        
        							$ordersModel->ValorFrete = $resValorFrete;
        							
        							$ordersModel->ValorPedido = $resValorPedido;
        							
        
        
        						}else{
        							
        							$ordersModel->Subtotal = $ordersModel->Subtotal + $resSubtotal;
        
        							$ordersModel->ValorCupomDesconto =  $ordersModel->ValorCupomDesconto + $resValorCupomDesconto;
        
        							$ordersModel->ValorFrete = $ordersModel->ValorFrete + $resValorFrete;
        
        							$ordersModel->ValorPedido = $ordersModel->ValorPedido + $resValorPedido;
        							
        
        						}
        						
        						if($ordersModel->ValorPedido == $ordersModel->Subtotal){
        							if($ordersModel->ValorFrete > 0){
        									
        								$newValorPedido = $ordersModel->ValorPedido + $ordersModel->ValorFrete;
        							}
        						
        						}
        						
        						if($ordersModel->Subtotal > $ordersModel->ValorPedido){
        							$newSubtotal = $ordersModel->ValorPedido - $ordersModel->ValorFrete;
        						
        						}
        						
        						$ordersModel->setOrdersPack($order->id);
        						if(!empty($ordersPack)){
        							$ordersModel->orders_pack = json_decode($res['orders_pack']);
        						}
        
        
        					}
        
        					if($newValorPedido < $shipping->order_cost){
        							
        							
        						$newValorPedido = $shipping->order_cost;
        					
        							
        					}
        						
        					$ordersModel->ValorPedido = $newValorPedido;
        						
        					$ordersModel->Subtotal = $newSubtotal;
        					
        					$ordersModel->shipping_id = trim($order->shipping->id.'');
        						
        					$ordersModel->logistic_type = isset($order->shipping->logistic_type) ? $order->shipping->logistic_type : null ;
        						
        					$orderId =  $ordersModel->Save();
        					
        					$logOrder[$key][] = array('OrderId' => $orderId);
        
        				}
        
        				if(isset($orderId)){
        
        					foreach ($order->order_items as $keyItem => $orderItem){
        						$cmv = 0;
        						$sku = isset($orderItem->item->seller_sku) ? $orderItem->item->seller_sku : $orderItem->item->seller_custom_field;
        						
        						$sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}'";
        						$queryAP = $db->query($sqlAP);
        						$availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
        
        						if(!isset($availableProduct['sku'])){
        
        							$partsId = explode("_", str_replace('MLB', '', $sku) );
        
        							$sqlSKU = "SELECT sku FROM ml_products_attributes WHERE store_id = {$storeId} AND
        							product_id = '{$partsId[0]}' AND variation_id = '{$partsId[1]}' AND sku != '' LIMIT 1";
        							$querySKU = $db->query($sqlSKU);
        							$resSku = $querySKU->fetch(PDO::FETCH_ASSOC);
        
        							$sku = $resSku['sku'];
        							
        							$cmv = $resSku['cost'];
        
        						}else{
        							$cmv = $availableProduct['cost'];
        						}
        						$cmv = $cmv * $orderItem->quantity;
        						$cmv = number_format($cmv, 2, '.', '');
        						$urlImage =  getUrlImageFromSku($db, $storeId, $sku);
        						$orderItems = new OrderItemsModel($db);
        						$orderItems->store_id = $storeId;
        						$orderItems->order_id = $orderId;
        						$orderItems->PedidoId = $mlOrderId;
        						$orderItems->PedidoItemId = $orderItem->item->id;
        						$orderItems->SKU = $sku;
        						$orderItems->CMV = $cmv;
        						$orderItems->Nome = $orderItem->item->title;
        						$orderItems->Quantidade = $orderItem->quantity;
        						$orderItems->TipoAnuncio = isset($orderItem->listing_type_id) ? $orderItem->listing_type_id : '' ;
        						$orderItems->PrecoUnitario = $orderItem->full_unit_price;
        						$orderItems->PrecoVenda = $orderItem->unit_price;
        						$orderItems->TaxaVenda = isset($orderItem->sale_fee) ? $orderItem->sale_fee : '' ;
        						$orderItems->UrlImagem = isset($urlImage[0]) ? $urlImage[0] : null ;
        						$orderItems->DataPedido = date("Y-m-d H:i:s", strtotime($order->date_created));
        						$orderItems->Marketplace = 'Mercadolivre';
        						$orderItemId =  $orderItems->Save();
        						
        						$logOrder[$key][] = array('ItemId' => $orderItemId);
        
        						if(isset($orderItem->item->variation_attributes) AND isset($orderItemId)){
        
        							foreach ($orderItem->item->variation_attributes as $keyVA => $orderItemAttribute){
        
        								$orderItemAttributes = new OrderItemAttributesModel($db);
        								$orderItemAttributes->store_id = $storeId;
        								$orderItemAttributes->order_id = $orderId;
        								$orderItemAttributes->item_id = $orderItemId;
        								$orderItemAttributes->PedidoId = $mlOrderId;
        								$orderItemAttributes->PedidoItemId = $orderItem->item->id;
        								$orderItemAttributes->Nome = $orderItemAttribute->name;
        								$orderItemAttributes->Valor = $orderItemAttribute->value_name;
        
        								$orderItemAttributeId = $orderItemAttributes->Save();
        								
        								$logOrder[$key][] = array('ItemAttrId' => $orderItemAttributeId);
        
        							}
        						}
        					}
        				}
        
        				if(isset($orderId)){
        						
        					if(isset($order->payments)){
        
        						foreach ($order->payments as $keyP => $orderPayment){
        
        							$orderPayments = new OrderPaymentsModel($db);
        							$orderPayments->store_id = $storeId;
        							$orderPayments->order_id = $orderId;
        							$orderPayments->PedidoId = $mlOrderId;
        							$orderPayments->PagamentoId = $orderPayment->id;
        							$orderPayments->NumeroParcelas = $orderPayment->installments;
        							$orderPayments->ValorParcela = $orderPayment->installment_amount;
        							$orderPayments->ValorTotal = ($orderPayment->transaction_amount + $orderPayment->shipping_cost);
        							$orderPayments->Desconto = $orderPayment->coupon_amount;
        							$orderPayments->FormaPagamento = $orderPayment->payment_type;
        							$orderPayments->Metodo = $orderPayment->payment_method_id;
        							$orderPayments->NumeroAutorizacao = $orderPayment->authorization_code;
        							$orderPayments->DataAutorizacao = date("Y-m-d H:i:s", strtotime($orderPayment->date_approved));
        							$orderPayments->Situacao = $orderPayment->status;
        							$orderPayments->MarketplaceTaxa = isset($orderPayment->marketplace_fee) ? $orderPayment->marketplace_fee : '0.00';
        							$orderPayments->Marketplace = "Mercadolivre";
        							$orderPaymentsId = $orderPayments->Save();
        							$logOrder[$key][] = array('Payments' => $orderPaymentsId);
        						}
        					}
        					$ordersCount++;
        				}
        			}
        		}
        		$offset += 50;
        	}while($offset <= $result['body']->paging->total);
//         	pre($logOrder);
        	break;
	        
	    case "import_order":
	     
	        $syncId =  logSyncStart($db, $storeId, "Mercadolivre", $action, "Importação de pedidos.", $request);
	        $totalOrder = 0;
	        $ordersCount = 0;
	       	$dateFrom =  date("Y-m-d H:i:s", strtotime("-1 day", strtotime("now")));
	        $dateTo = date("Y-m-d")." 23:59:59";
// 	        $dateTo = "2020-01-18 23:59:59";
	        
	        
	        if(isset($_REQUEST['order_id'])){
	            $orderId = $_REQUEST['order_id'];
	            $notifications[] = array("resource" => "/".$orderId);
	              
	        }else{
	            
// 	            echo $sql = "SELECT * FROM `ml_notifications` WHERE store_id = {$storeId}
//                 AND topic LIKE 'orders_v2' AND received BETWEEN  '{$dateFrom}' AND '{$dateTo}'";
	            $sql = "SELECT * FROM `ml_notifications` WHERE store_id = {$storeId} and status != 'imported'
                AND topic LIKE 'orders_v2' AND received BETWEEN  '{$dateFrom}' AND '{$dateTo}'";
// 	            $sql = "SELECT * FROM `ml_notifications` WHERE store_id = {$storeId} 
// 	            AND topic LIKE 'orders_v2' AND received BETWEEN  '{$dateFrom}' AND '{$dateTo}'";
// 	            echo $sql = "SELECT * FROM `ml_notifications` WHERE store_id = {$storeId}
//                 AND topic LIKE 'orders_v2' AND received BETWEEN  '{$dateFrom}' AND '{$dateTo}'";
	            $queryNotify = $db->query($sql);
	        
	            $notifications = $queryNotify->fetchAll(PDO::FETCH_ASSOC);
	            
	        }
// 	        pre($notifications);
	        if(isset($notifications[0])){
    	        foreach($notifications as $key => $notification){
    	            $parts = explode("/", $notification['resource']);     
    	            $orderId = end($parts);
    	            $getOrder = "/orders/search?seller={$resMlConfig['seller_id']}&q={$orderId}&access_token={$resMlConfig ['access_token']}";
    	            
    	            
    	            if(empty($orderId)){
//     	                $dateCreatedFrom =  date("Y-m-d", strtotime("-2 day", strtotime("now")))."T00:00:00.000-00:00";
    	                $dateCreatedFrom =  date("Y-m-d\TH:i:s", strtotime("-36 hour")).".000-00:00";
    	                $dateCreatedTo = date("Y-m-d")."T23:59:59.000-00:00";
    	                $getOrder = "/orders/search?seller={$resMlConfig['seller_id']}&order.date_created.from={$dateCreatedFrom}&order.date_closed.to={$dateCreatedTo}&access_token={$resMlConfig ['access_token']}";
    	            
    	            }
    	            $result = $meli->get($getOrder);
    	            $resultsOrder = isset($result['body']->results) ? $result['body']->results : array($result['body']) ;
    	            if($resultsOrder){
    	                
    	                $totalOrder = count($resultsOrder);
    	                
    	                foreach($resultsOrder as $key => $order){  
    	                    
                            $mlOrderId = isset($order->pack_id) && !empty($order->pack_id) ? $order->pack_id : $order->id;
                            
                            $getOrderBillingInfo = "/orders/{$order->id}/billing_info";
                            $resOrderBillingInfo = $meli->get($getOrderBillingInfo);
                            $billingInfo = $resOrderBillingInfo['body']->billing_info;
                            
                            $orderBuyer = new \stdClass;
                            foreach($billingInfo->additional_info as $i => $buyer){
                                $prop = strtolower($buyer->type);
                                $orderBuyer->{$prop} = $buyer->value;
                            }
                            
    	                    $sku = isset($order->order_items[0]->item->seller_sku) ? $order->order_items[0]->item->seller_sku : $order->order_items[0]->item->seller_custom_field ;
    	                    $partes = explode("T", $order->date_created);
    	                    
    	                    $tipoPessoa = $billingInfo->doc_type == "CNPJ" ? 2 : 1 ;
    	                    
    	                    $customerModel = new  ManageCustomersModel($db);
    	                    $customerModel->store_id = $storeId;
    	                    $customerModel->Codigo = $order->buyer->id;
    	                    $customerModel->TipoPessoa = $tipoPessoa;
    	                    
    	                    
    	                    if(empty($orderBuyer->first_name)){
    	                        
    	                        $customerModel->Nome = $order->buyer->nickname;
    	                        
    	                    }else{
    	                        $customerModel->Nome = $orderBuyer->first_name." ".$orderBuyer->last_name;
    	                        
    	                    }
    	                    
    	                    $customerModel->Apelido = $order->buyer->nickname;
    	                    $customerModel->Email = $order->buyer->email;
    	                    $customerModel->CPFCNPJ = $billingInfo->doc_number;
    	                    
    	                    $customerModel->Telefone = isset($order->buyer->phone->area_code) ? $order->buyer->phone->area_code : "";
    	                    $customerModel->Telefone .= isset($order->buyer->phone->number) ? $order->buyer->phone->number : '' ;
    	                    
    	                    
    	                    $customerModel->TelefoneAlternativo = isset($order->buyer->alternative_phone->area_code) ? $order->buyer->alternative_phone->area_code : "";
    	                    $customerModel->TelefoneAlternativo .= isset($order->buyer->alternative_phone->number) ? $order->buyer->alternative_phone->number : '' ;
    	                    
    	                    $customerModel->DataCriacao = date("Y-m-d H:i:s", strtotime($order->date_created));
    	                    $shipmentsRestModel = new ShipmentsRestModel($db, null, $storeId);
    	                    $shipmentsRestModel->shipment_id = $order->shipping->id;
    	                    $shipping = $shipmentsRestModel->getShipment();
    	                    $shipping = $shipping['body'];
    	                    pre($shipping);
    	                    
    	                    $state =  explode("-", $shipping->receiver_address->state->id);
    	                    $customerModel->Endereco = $shipping->receiver_address->street_name;
    	                    $customerModel->Numero = $shipping->receiver_address->street_number;
    	                    $customerModel->Complemento = $shipping->receiver_address->comment;
    	                    $customerModel->Cidade = $shipping->receiver_address->city->name;
    	                    $customerModel->Estado = trim(end($state));
    	                    $customerModel->CEP = $shipping->receiver_address->zip_code;
    	                    
    	                    $customerModel->Marketplace = "Mercadolivre";
    	                    $customerId = $customerModel->Save();
            	            echo "-------Customer-----------<br>";
            	            pre($customerId);
    	                    
    	                    if(isset($customerId)){
    	                        
    	                        $ordersModel = new OrdersModel($db);
    	                        $ordersModel->store_id = $storeId;
    	                        $ordersModel->customer_id = $customerId;
    	                        $ordersModel->PedidoId = $mlOrderId;
    	                        $ordersModel->CPFCNPJ = $billingInfo->doc_number;
    	                        
    	                        if(empty($orderBuyer->first_name)){
    	                            
    	                            $ordersModel->Nome = $order->buyer->nickname;
    	                            $ordersModel->NomeDestino  = $order->buyer->nickname;
    	                            
    	                        }else{
    	                            $ordersModel->Nome = $orderBuyer->first_name." ".$orderBuyer->last_name;
    	                            $ordersModel->NomeDestino= $orderBuyer->first_name." ".$orderBuyer->last_name;
    	                            
    	                        }
    	                        
    	                        $ordersModel->Email = $order->buyer->email;
    	                        $ordersModel->Telefone = isset($order->buyer->phone->area_code) ? $order->buyer->phone->area_code : "";
    	                        $ordersModel->Telefone .= isset($order->buyer->phone->number) ? $order->buyer->phone->number : '' ;
    	                        
    	                        if(isset($shipping->receiver_address)){
    	                            $ordersModel->Endereco = $shipping->receiver_address->street_name;
    	                            $ordersModel->Complemento = $shipping->receiver_address->comment;
    	                            $ordersModel->Numero = $shipping->receiver_address->street_number;
    	                            $ordersModel->Cidade = $shipping->receiver_address->city->name;
    	                            $ordersModel->Estado = trim(end($state));
    	                            $ordersModel->Cep = $shipping->receiver_address->zip_code;
    	                        }
    	                        
    	                        $ordersModel->DataPedido = date("Y-m-d H:i:s", strtotime($order->date_created));
    	                        $ordersModel->FormaPagamento = $order->payments[0]->payment_method_id;
    	                        $ordersModel->Parcelas = $order->payments[0]->installments;
    	                        $ordersModel->ValorParcelas = $order->payments[0]->installment_amount;
    	                        
    	                        $ordersModel->ValorFrete = $shipping->shipping_option->cost;
    	                        
    	                        $ordersModel->Subtotal = $order->total_amount;
    	                        
    	                        foreach ($order->payments as $keyP => $orderPayment){
    	                            if($orderPayment->status == 'approved' ){
    	                                $ordersModel->ValorPedido += ($orderPayment->transaction_amount + $orderPayment->shipping_cost);//$orderPayment->total_paid_amount;
    	                                $ordersModel->ValorCupomDesconto +=  $orderPayment->coupon_amount;
    	                                
    	                            }else{
    	                                
    	                                $message = "Pedido {$order->id} com status {$orderPayment->status}";
    // 	                                notifyAdmin($message);
    	                            }
    	                            
    	                        }
    	                        if(empty($ordersModel->ValorPedido)){
    	                            $ordersModel->ValorPedido = ($order->total_amount + $shipping->shipping_option->cost);
    	                        }
    	                        
    	                        pre($ordersModel->ValorPedido);
    	                        
    	                        $ordersModel->Status = $order->status;
    	                        
    	                        $ordersModel->Marketplace = 'Mercadolivre';
    	                        
    	                        $newSubtotal = $ordersModel->Subtotal;
    	                        	
    	                        $newValorPedido = $ordersModel->ValorPedido;
    	                        
    	                        if(isset($order->pack_id) && !empty($order->pack_id)){
    	                               
    	                            $queryOrders = $db->query("SELECT * FROM orders
                                    WHERE store_id = {$storeId} AND PedidoId = '{$mlOrderId}'");
    	                            $res = $queryOrders->fetch(PDO::FETCH_ASSOC);
    	                            
    	                            
    	                            $resSubtotal = isset($res['Subtotal']) ? $res['Subtotal'] : 0 ;
    	                            
    	                            $resValorFrete = isset($res['ValorFrete']) ? $res['ValorFrete'] : 0 ;
    	                            
    	                            $resValorCupomDesconto = isset($res['ValorCupomDesconto']) ? $res['ValorCupomDesconto'] : 0 ;
    	                            
    	                            $resValorPedido = isset($res['ValorPedido']) ? $res['ValorPedido'] : 0 ;
    	                            
    	                            $ordersPack = isset($res['orders_pack']) ? json_decode($res['orders_pack']) : array();
    	                            
    	                            pre(array('id' => $order->id, 'pack' => $ordersPack));
    	                            
    	                            
    	                            if(in_array($order->id, $ordersPack)){
    	                            	
    	                            	$ordersModel->ValorCupomDesconto = $resValorCupomDesconto;
    	                            	 
    	                            	$ordersModel->Subtotal = $resSubtotal;
    	                            	
    	                            	$ordersModel->ValorFrete = $resValorFrete;
    	                            	
    	                            	$ordersModel->ValorPedido = $resValorPedido;
    	                            	 
    	                                    
    	                            }else{
    	                            	
    	                            	 
    	                            	$ordersModel->ValorCupomDesconto =  $ordersModel->ValorCupomDesconto + $resValorCupomDesconto;
    	                            	 
    	                            	$ordersModel->Subtotal = $ordersModel->Subtotal + $resSubtotal;
    	                            	
    	                            	$ordersModel->ValorFrete = $ordersModel->ValorFrete + $resValorFrete;
    	                            	
    	                            	$ordersModel->ValorPedido = $ordersModel->ValorPedido + $resValorPedido;
    	                            	 
    	                                    
    	                            }
    	                            pre($ordersModel);

    	                            if($ordersModel->ValorPedido == $ordersModel->Subtotal){
    	                            	if($ordersModel->ValorFrete > 0){
    	                            			
    	                            		$newValorPedido = $ordersModel->ValorPedido + $ordersModel->ValorFrete;
    	                            	}
    	                            
    	                            }
    	                            
    	                            if($ordersModel->Subtotal > $ordersModel->ValorPedido){
    	                            	$newSubtotal = $ordersModel->ValorPedido - $ordersModel->ValorFrete;
    	                            
    	                            }
    	                            
    	                            $ordersModel->setOrdersPack($order->id);
    	                            if(empty($ordersPack[0]) && !empty($res['orders_pack'])){
    	                            	$ordersModel->orders_pack = json_decode($res['orders_pack']);
    	                            }
    	                            
    	                        }
    	                        
    	                        
    	                        if($newValorPedido < $shipping->order_cost){
    	                        		
    	                        		
    	                        	$newValorPedido = $shipping->order_cost;
    	                        
    	                        		
    	                        }
    	                        	
    	                        $ordersModel->ValorPedido = $newValorPedido;
    	                        	
    	                        $ordersModel->Subtotal = $newSubtotal;
    	                        
    	                        $ordersModel->shipping_id = trim($order->shipping->id."");
    	                        $ordersModel->logistic_type = isset($order->shipping->logistic_type) ? $order->shipping->logistic_type : '';
//     	                        pre($ordersModel);
    	                        $orderId =  $ordersModel->Save();
                    	        echo "-------OrderId-----------<br>";
                    	        pre($orderId);
                    	        
    	                    }
    	                    
    	                    if(isset($orderId)){
    	                        
    	                        foreach ($order->order_items as $keyItem => $orderItem){
    	                        	$cmv = 0;
    	                            $sku = isset($orderItem->item->seller_sku) ? $orderItem->item->seller_sku : $orderItem->item->seller_custom_field;
    	                            $sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}'";
    	                            $queryAP = $db->query($sqlAP);
    	                            $availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
    	                            
    	                            
    	                            if(!isset($availableProduct['sku'])){
    	                                
    	                                $partsId = explode("_", str_replace('MLB', '', $sku) );
    	                                
    	                                 $sqlSKU = "SELECT sku FROM ml_products_attributes WHERE store_id = {$storeId} AND
                                        product_id = '{$partsId[0]}' AND variation_id = '{$partsId[1]}' AND sku != '' LIMIT 1";
    	                                $querySKU = $db->query($sqlSKU);
    	                                $resSku = $querySKU->fetch(PDO::FETCH_ASSOC);
    	                                
    	                                $sku = $resSku['sku'];
    	                                $cmv = $resSku['cost'];
    	                                
    	                            }else{
    	                            	$cmv = $availableProduct['cost'];
    	                            }
    	                            $cmv = $cmv * $orderItem->quantity;
    	                            $cmv = number_format($cmv, 2, '.', '');
    	                            $urlImage =  getUrlImageFromSku($db, $storeId, $sku);
    	                            $orderItems = new OrderItemsModel($db);
    	                            $orderItems->store_id = $storeId;
    	                            $orderItems->order_id = $orderId;
    	                            $orderItems->PedidoId = $mlOrderId;
    	                            $orderItems->PedidoItemId = $orderItem->item->id;
    	                            $orderItems->SKU = $sku;
    	                            $orderItems->CMV = $cmv;
    	                            $orderItems->Nome = $orderItem->item->title;
    	                            $orderItems->Quantidade = $orderItem->quantity;
    	                            $orderItems->TipoAnuncio = isset($orderItem->listing_type_id) ? $orderItem->listing_type_id : '' ;
    	                            $orderItems->PrecoUnitario = $orderItem->full_unit_price;
    	                            $orderItems->PrecoVenda = $orderItem->unit_price;
    	                            $orderItems->TaxaVenda = isset($orderItem->sale_fee) ? $orderItem->sale_fee : '' ;
    	                            $orderItems->UrlImagem = isset($urlImage[0]) ? $urlImage[0] : null ;
    	                            $orderItems->DataPedido = date("Y-m-d H:i:s", strtotime($order->date_created));
    	                            $orderItems->Marketplace = 'Mercadolivre';
    	                            
    	                            $orderItemId =  $orderItems->Save();
                	                echo "-------Item id-----------<br>";
                	                pre($orderItemId);
    	                            
    	                            if(isset($orderItem->item->variation_attributes) AND isset($orderItemId)){
    	                                
    	                                foreach ($orderItem->item->variation_attributes as $keyVA => $orderItemAttribute){
    	                                    
    	                                    $orderItemAttributes = new OrderItemAttributesModel($db);
    	                                    $orderItemAttributes->store_id = $storeId;
    	                                    $orderItemAttributes->order_id = $orderId;
    	                                    $orderItemAttributes->item_id = $orderItemId;
    	                                    $orderItemAttributes->PedidoId = $mlOrderId;
    	                                    $orderItemAttributes->PedidoItemId = $orderItem->item->id;
    	                                    $orderItemAttributes->Nome = $orderItemAttribute->name;
    	                                    $orderItemAttributes->Valor = $orderItemAttribute->value_name;
    	                                    
    	                                    $orderItemAttributeId = $orderItemAttributes->Save();
                    	                    echo "-------Item Attr-----------<br>";
                    	                    pre($orderItemAttributeId);
    	                                    
    	                                }
    	                            }
    	                        }
    	                    }
    	                    
    	                    
    	                    
    	                    if(isset($orderId)){
    	                        if(isset($order->payments)){
    	                            
    	                            foreach ($order->payments as $keyP => $orderPayment){
    	                                
    	                                $orderPayments = new OrderPaymentsModel($db);
    	                                $orderPayments->store_id = $storeId;
    	                                $orderPayments->order_id = $orderId;
    	                                $orderPayments->PedidoId = $mlOrderId;
    	                                $orderPayments->PagamentoId = $orderPayment->id;
    	                                $orderPayments->NumeroParcelas = $orderPayment->installments;
    	                                $orderPayments->ValorParcela = $orderPayment->installment_amount;
    	                                $orderPayments->ValorTotal = ($orderPayment->transaction_amount + $orderPayment->shipping_cost);
    	                                $orderPayments->Desconto = $orderPayment->coupon_amount;
    	                                $orderPayments->FormaPagamento = $orderPayment->payment_type;
    	                                $orderPayments->Metodo = $orderPayment->payment_method_id;
    	                                $orderPayments->NumeroAutorizacao = $orderPayment->authorization_code;
    	                                $orderPayments->DataAutorizacao = date("Y-m-d H:i:s", strtotime($orderPayment->date_approved));
    	                                $orderPayments->Situacao = $orderPayment->status;
    	                                $orderPayments->MarketplaceTaxa = isset($orderPayment->marketplace_fee) ? $orderPayment->marketplace_fee : '0.00';
    	                                $orderPayments->Marketplace = "Mercadolivre";
    	                                pre($orderPayments);
    	                                $orderPaymentsId = $orderPayments->Save();
    	                                
                    	                echo "-------Payments-----------<br>";
                    	                pre($orderPaymentsId);
    	                                
    	                                
    	                                
    	                            }
    	                        }
    	                    }
    	                    
    	                    $ordersCount++;
    	                    
    	                    if(isset($notification['id'])){
    	                        
    	                        if(!empty($orderId)){
    	                            
        	                        if(!empty($orderItemId)){
        	                            
        	                            if(!empty($orderPaymentsId)){
        	                                
        	                                if(!empty($mlOrderId)){
                    	                        $queryUpdate = $db->update('ml_notifications', 
                    	                            array('store_id', 'id'),
                    	                            array($storeId, $notification['id']), 
                    	                                array(
                    	                                       'status' => 'imported',
                    	                                    'information' => $mlOrderId
                    	                           ));
                    	                        
                    	                        if($queryUpdate){
                    	                            echo "success|{$notification['id']}|{$mlOrderId}";
                    	                        }
                	                        
        	                                }
        	                                
        	                            }
            	                        
        	                        }
        	                        
    	                        }
    	                        
    	                    }
    	                    
    	                    
    	                    
    	                }
    	                    
                    }
                    
                }
	        }
            
            logSyncEnd($db, $syncId, $totalOrder);
            break;
	    
	    case "group_orders":
	        
	        $orderId = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : null ;
	        
	        $getOrder = "/orders/search?seller={$resMlConfig['seller_id']}&q={$orderId}&access_token={$resMlConfig ['access_token']}";
	        
	        if(empty($orderId)){
	            $dateCreatedFrom =  date("Y-m-d\TH:i:s", strtotime("-1 day", strtotime("now"))).".000-00:00";
	            $dateCreatedTo = date("Y-m-d")."T23:59:59.000-00:00";
	            echo $getOrder = "/orders/search?seller={$resMlConfig['seller_id']}&order.date_created.from={$dateCreatedFrom}&order.date_closed.to={$dateCreatedTo}&access_token={$resMlConfig ['access_token']}";
	        }
	        
	        $result = $meli->get($getOrder);
	        
	        pre($result['body']->results);die;
	        
	        break;
	        
	    case "update_shipping_costs":
// 	        die;
	        $shipmentsRestModel = new ShipmentsRestModel($db, null, $storeId);
	        
// 	        $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND Marketplace LIKE 'Mercadolivre'
// 	        AND shipping_id != '' AND DataPedido >= '2020-09-01' AND DataPedido <= '2021-01-14 23:59:59'";
	        
	        $today = date('Y-m-d H:i:s');
	        $startDate = date("Y-m-d H:i:s", strtotime("-2 day"));
	        
	        
	        echo $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND Marketplace LIKE 'Mercadolivre'
	        AND shipping_id != '' AND DataPedido >= '{$startDate}' AND DataPedido <= '{$today}'";
// 	        die;  
	        if(isset($_REQUEST['order_id'])){
	        	
	        	$orderId = $_REQUEST['order_id'];
	        	
	        	$sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND Marketplace LIKE 'Mercadolivre'  AND PedidoId LIKE '{$orderId}'";
	        }
	        
	        
// 	        AND orders.DataPedido >= '2021-01-07' AND orders.DataPedido <= '2021-01-07 23:59:59'  AND orders.status != 'cancelled'
// 	        		AND  orders.status != 'canceled'  AND orders.status != 'pending'";
	        $queryShipments = $db->query($sql);
	        $orders = $queryShipments->fetchAll(PDO::FETCH_ASSOC);
	        foreach($orders as $key => $order){
	        	
	            $shipmentsRestModel->shipment_id = $order['shipping_id'];
	            $shipping = $shipmentsRestModel->getShipment();
	            
	            if(isset($shipping['body']->order_id)){
	            	
		            $shipping = $shipping['body'];
					$update = false;
		            
					echo "<br> DataPedido: ".$order['DataPedido'];
		            echo "<br> Id: ".$shipping->id." - PedidoId: ".$shipping->order_id;
		            
		            $Subtotal = number_format($shipping->order_cost, 2, '.', '');
		            echo "<br> Subtotal: ".$order['Subtotal']." - ".$Subtotal;
		            if($Subtotal > $order['Subtotal'] OR $Subtotal < $order['Subtotal']){
		            	$update = true;
		            	echo "<br>Subtotal diferente ST";
		            }
		            $ValorFrete = number_format($shipping->shipping_option->cost, 2, '.', '');
		            echo "<br> ValorFrete: ".$order['ValorFrete']." - ".$ValorFrete;
		            if($ValorFrete > $order['ValorFrete'] OR $ValorFrete < $order['ValorFrete']){
		            	$update = true;
		            	echo "<br>ValorFrete diferente VF";
		            }
		            $FreteCusto = number_format($shipping->shipping_option->list_cost, 2, '.', '');
		            echo "<br> FreteCusto: ".$order['FreteCusto']." - ".$FreteCusto;
		            if($FreteCusto > $order['FreteCusto'] OR $FreteCusto < $order['FreteCusto']){
		            	$update = true;
		            	echo "<br>FreteCusto diferente FC";
		            }
		            
		            $varValorPedido = (float) $shipping->order_cost + (float) $shipping->shipping_option->cost;
		            $varValorPedido = number_format($varValorPedido, 2, '.', '');
		            echo "<br> ValorPedido: ".$order['ValorPedido']." - ".$varValorPedido;
		            if($varValorPedido > $order['ValorPedido'] OR $varValorPedido < $order['ValorPedido']){
		            	$update = true;
		            	echo "<br>ValorPedido diferente VP";
		            }
		            if($update){
		            	echo "<br>UPDATE<br>";
		            	$data = array('Subtotal' => $Subtotal,
		            				'ValorFrete' => $ValorFrete,
		            				'FreteCusto' => $FreteCusto,
		            				'ValorPedido' => $varValorPedido
		            			);
		            	$queryOrders = $db->update('orders',
		            			array('store_id','id'),
		            			array($storeId, $order['id']),
		            			$data);
		            	if(!$queryOrders){
		            		pre($queryOrders);die;
		            	}
		            	pre($data);
		            	
		            	
		            }else{
		            	echo "<br>NO--<br>";
		            }
		           
		            
		            
		            echo "<br>====================================================";
		            echo "<br> Base Cost: ".$shipping->base_cost;
		            echo "<br> Cost: ".$shipping->shipping_option->cost;
		            echo "<br> list_cost: ".$shipping->shipping_option->list_cost;
		            echo "<br> Ratio: ".$shipping->cost_components->ratio;
		            echo "<br>====================================================<br>";
		            echo "====================================================<br>";
	            
	            }else{
	            	pre($order);
	            	pre($shipping);
	            }
	            
	            
	        }
	        
	        break;
	    
		    
	}
    	
}

