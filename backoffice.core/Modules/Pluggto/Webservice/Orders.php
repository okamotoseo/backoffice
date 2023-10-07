<?php

set_time_limit ( 300 );
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/html; charset=utf-8");

$path = dirname(__FILE__);

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Pluggto.php';
require_once $path .'/../Models/Api/OrderRestModel.php';
require_once $path .'/../../../Models/Customers/ManageCustomersModel.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$type = isset($_REQUEST["type"]) && $_REQUEST["type"] != "" ? $_REQUEST["type"] : 'single' ;
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
	    
	    case "update_order":
	        
	        $totalOrder = 0;
	        $ordersCount = 0;
	        $ordersRestModel = new OrderRestModel($db, null, $storeId);
	            
	        $sqlOrder = "SELECT * FROM orders WHERE store_id = '{$storeId}' AND ERP IS NULL AND Status != 'canceled'";
// 	        $sqlOrder = "SELECT * FROM orders WHERE store_id = '{$storeId}' AND ERP IS NULL
//                 AND Status NOT IN ('approved','pending', 'waiting_invoice', 'under_review', 'invoice_error', 'partial_payment')";
// 	        $sqlOrder = "SELECT * FROM orders WHERE store_id = '{$storeId}' AND Marketplace LIKE 'Mercadolivre'";
	        $queryOrder = $db->query($sqlOrder);
	        $resOrder = $queryOrder->fetchAll(PDO::FETCH_ASSOC);
	        foreach($resOrder as $k => $order){
// 	            pre($order['Status']);
                $ordersRestModel->id  = $order['PedidoId'];
                $orders = $ordersRestModel->get();
                $shipments = $orders->Order->shipments[0];
                $pedidoId = $orders->Order->id;
                $status = $orders->Order->status;
//                 pre($orders->Order);
                if(!empty($pedidoId)){
                        
// 	                       if(isset($shipments->nfe_key) && !empty($shipments->nfe_key)){
                            
                            $tipo = 1;
                            $serie = $shipments->nfe_serie;
                            $idNotaSaida = $shipments->nfe_number;
                            $nfe = $shipments->nfe_key;
                            $dhEmissao = $shipments->nfe_date."";
                            $dhEmissao = explode("T", $dhEmissao);
                            $emissao = $dhEmissao[0];
                            $parts = explode('.', $dhEmissao[1]);
                            $hora_emissao = $parts[0];
                            $valor_total = $orders->Order->total."";
                            $canal = !empty($orders->Order->channel_account) ? $orders->Order->channel_account : $order['Canal'];
                            
    	                    
                        if(!empty($nfe) and $status != 'canceled'){
                            pre(array('fiscal_key' => $nfe,
                                'Status' => $status,
                                'Canal' => $canal,
                                'nf_serie' => $serie,
                                'nf_tipo' => $tipo,
                                'nf_emissao' => $emissao,
                                'nf_hora_emissao' => $hora_emissao,
                                'nf_total' => $valor_total,
                                'nf_info_fisco' => $infoFisco
                            ));
                                $queryUpdate = $db->update('orders',
                                    array('store_id', 'id'),
                                    array($storeId, $order['id']),
                                    array('fiscal_key' => $nfe,
                                        'Status' => $status,
                                        'Canal' => $canal,
                                        'nf_serie' => $serie,
                                        'nf_tipo' => $tipo,
                                        'nf_emissao' => $emissao,
                                        'nf_hora_emissao' => $hora_emissao,
                                        'nf_total' => $valor_total,
                                        'nf_info_fisco' => $infoFisco
                                    ));
//                                 pre($queryUpdate);
                                
                        }else{
                            if($status != $order['Status']){
//                                 pre($status);
//                                 //sem nfe
                                
                                $queryUpdate = $db->update('orders',
                                    array('store_id', 'id'),
                                    array($storeId, $order['id']),
                                    array('Status' => $status, 'Canal' => $canal)
                                    );

                            }
                                
                        }
                        
// 	                        }
                }
                    
	        }
	        
	        break;
	        
	    case "import_order":
	        $syncId =  logSyncStart($db, $storeId, "Pluggto", $action, "Importação de pedidos.", $request);
	        $totalOrder = 0;
	        $dateFrom =  date("Y-m-d", strtotime("-30 day", strtotime("now")));
// 	        $dateFrom =  date("Y-m-d");
	        $dateTo = date("Y-m-d");
// 	        $dateTo =  date("Y-m-d", strtotime("-1 day", strtotime("now")));
	        $ordersRestModel = new OrderRestModel($db, null, $storeId);
	        $statusImport = array('delivered','shipped', 'pending', 'approved', 'invoiced','canceled', 'waiting_invoice', 'invoice_error', 'shipping_informed', 'shipping_error', 'under_review');
// 	        $statusImport = array('delivered', 'shipped');

	        foreach($statusImport as $ind => $status){
	            $ordersCount = 0;
	            $ordersRestModel->next  = null;
    	        $ordersRestModel->status   = $status;
    	        $ordersRestModel->created   = "{$dateFrom}to{$dateTo}";
//     	        pre($ordersRestModel->created);
    	        do{
        	        $orderRes = $ordersRestModel->list();
//         	        pre(array('total' => $orderRes->total));
        	        if(isset($orderRes->result[0])){
        	            $orders = $orderRes->result;
        	            foreach($orders as $key => $orderObj){
//         	                pre($orderObj);
        	                $order = $orderObj->Order;
        	                $ordersRestModel->next  = $order->id;
        	                $orderPayments = $order->payments;
        	                $orderItems = $order->items;
        	                $orderShipments = $order->shipments;
        	                $customerModel = new  ManageCustomersModel($db);
        	                $customerModel->store_id = $storeId;
        	                $customerModel->Codigo = $customer->user_id;
        	                
        	                $TipoPessoa = 1;
        	                $cnpCnpj = $order->payer_cpf ;
        	                
        	                if(!empty($order->payer_cnpj)){
        	                    $TipoPessoa = 2;
        	                    $cnpCnpj = $order->payer_cnpj;
        	                }
        	                $customerModel->TipoPessoa = $TipoPessoa;
        	                $customerModel->CPFCNPJ = $cnpCnpj;
        	                
        	                $customerModel->Nome = trim($order->payer_name." ".$order->payer_lastname);
        	                $customerModel->Apelido = $order->payer_lastname;
        	                $customerModel->Email = $order->payer_email;
        	                $customerModel->Telefone = isset($order->payer_phone_area) ? trim($order->payer_phone_area.$order->payer_phone) : "";
        	                $customerModel->TelefoneAlternativo = isset($order->payer_phone2_area) ? trim($order->payer_phone2_area.$order->payer_phone2) : "";
        	                $customerModel->DataCriacao = date("Y-m-d H:i:s", strtotime($order->created));
        	                $customerModel->Endereco = $order->payer_address;
        	                $customerModel->Numero = $order->payer_address_number;
        	                $customerModel->Bairro = $order->payer_neighborhood;
        	                $customerModel->Complemento = $orde->payer_address_complement." ".$order->payer_additional_info;
        	                $customerModel->Cidade = $order->payer_city;
        	                $customerModel->Estado = getUf($order->payer_state);
        	                $customerModel->CEP = $order->payer_zipcode;
        	                $customerModel->Marketplace = !empty($order->channel) ? ucfirst(strtolower($order->channel)) : "Pluggto";
//         	                pre($customerModel);
        	                $customerId = $customerModel->Save();
//         	                echo "-------Customer-----------<br>";
//         	                pre($customerId);
        	                if(isset($customerId)){
        	                    $ordersModel = new OrdersModel($db);
        	                    $ordersModel->store_id = $storeId;
        	                    $ordersModel->customer_id = $customerId;
        	                    $ordersModel->PedidoId = $order->id;
        	                    $ordersModel->Nome = $customerModel->Nome;
        	                    $ordersModel->Endereco = $order->payer_address;
        	                    $ordersModel->Telefone = isset($order->payer_phone_area) ? trim($order->payer_phone_area.$order->payer_phone) : "";
        	                    $ordersModel->Bairro = $order->payer_neighborhood;
        	                    $ordersModel->Complemento = $orde->payer_address_complement." ".$order->payer_additional_info;
        	                    $ordersModel->Numero = $order->payer_number;
        	                    $ordersModel->Cidade = $order->payer_city;
        	                    $ordersModel->Estado = getUf($order->payer_state);
        	                    $ordersModel->Cep = $order->payer_zipcode;
        	                    $ordersModel->Email = $customerModel->Email;
        	                    $ordersModel->Telefone = $customerModel->Telefone;
        	                    
        	                    $ordersModel->NomeDestino  =  !empty( $order->receiver_name) ? trim($order->receiver_name." ".$order->receiver_lastname) :  $customerModel->Nome ;
        	                    if(!empty($order->receiver_address)){
        	                        $ordersModel->Endereco_entrega = $order->receiver_address;
        	                        $ordersModel->Telefone_entrega = isset($order->receiver_phone_area) ? trim($order->receiver_phone_area.$order->receiver_phone) : "";
        	                        $ordersModel->Bairro_entrega = $order->receiver_neighborhood;
        	                        $ordersModel->Complemento_entrega = $order->receiver_address_complement;
        	                        $ordersModel->Numero_entrega = $order->receiver_address_number;
        	                        $ordersModel->Cidade_entrega = $order->receiver_city;
        	                        $ordersModel->Estado_entrega = getUf($order->receiver_state);
        	                        $ordersModel->Cep_entrega = $order->receiver_zipcode;
        	                    }else{
        	                        $ordersModel->Endereco_entrega = $order->payer_address;
        	                        $ordersModel->Telefone_entrega = isset($order->payer_phone_area) ? trim($order->payer_phone_area.$order->payer_phone) : "";
        	                        $ordersModel->Bairro_entrega = $order->payer_neighborhood;
        	                        $ordersModel->Complemento_entrega = $order->payer_address_complement;
        	                        $ordersModel->Numero_entrega = $order->payer_address_number;
        	                        $ordersModel->Cidade_entrega = $order->payer_city;
        	                        $ordersModel->Estado_entrega = getUf($order->payer_state);
        	                        $ordersModel->Cep_entrega = $order->payer_zipcode;
        	                    }
        	                    $ordersModel->DataPedido = date("Y-m-d H:i:s", strtotime($order->created));
        	                    $ordersModel->FormaPagamento = $orderPayments[0]->payment_method;
        	                    $ordersModel->Parcelas = $orderPayments[0]->payment_installments > 0 ? $orderPayments[0]->payment_installments : 1 ;
        	                    $ordersModel->ValorFrete = !empty($order->shipping) ? $order->shipping : 0.00;
    //     	                    $frete = !empty($order->shipping) ? $order->shipping : 0.00;
    //     	                    $ordersModel->ValorFrete = 0.00;
        	                    $ordersModel->Subtotal = $order->subtotal;
    //     	                    $ordersModel->Canal = $order->channel;
    //     	                    $ordersModel->ValorPedido = $frete > 0 ? $order->total - $frete : $order->total;
        	                    $ordersModel->ValorPedido =  $order->total;
        	                    $ordersModel->ValorCupomDesconto =  !empty($order->discount) ? $order->discount : '0.00';
        	                    $ordersModel->MarketplaceTaxa = !empty($order->commission->total_charged) ? $order->commission->total_charged : '0.00';
        	                    $ordersModel->Obs = $order->total_paid;
//         	                    pre($order->status);
        	                    $ordersModel->Status = $order->status;
        	                    $ordersModel->Marketplace = $customerModel->Marketplace;
        	                    $ordersModel->Canal = !empty($order->channel_account) ? $order->channel_account : $customerModel->Marketplace;
        	                    $orderId =  $ordersModel->Save();
//         	                    echo "---------OrderId-----------<br>";
//         	                    pre($orderId);
        	                }
        	                if(isset($orderId)){
        	                    foreach ($orderItems as $keyItem => $item){
        	                        $orderItem = $item;
        	                        $sku = isset($orderItem->variation->sku) && !empty($orderItem->variation->sku) ? $orderItem->variation->sku : $orderItem->sku ;
        	                        $orderItems = new OrderItemsModel($db);
        	                        $orderItems->store_id = $storeId;
        	                        $orderItems->order_id = $orderId;
        	                        $orderItems->PedidoId = $ordersModel->PedidoId;
        	                        $orderItems->PedidoItemId = trim($sku);
        	                        $orderItems->SKU = $sku;
        	                        $orderItems->Nome = $orderItem->name;
        	                        $orderItems->Quantidade = $orderItem->quantity;
        	                        $orderItems->PrecoUnitario = $orderItem->price;
        	                        $orderItems->PrecoVenda = $orderItem->price;
        	                        $orderItems->TaxaVenda = !empty($orderItem->commission->total_charged) ? $orderItem->commission->total_charged : '0.00';
        	                        $orderItems->UrlImagem = $orderItem->photo_url;
        	                        $orderItems->Marketplace = $ordersModel->Marketplace;
        	                        $orderItemId =  $orderItems->Save();
//         	                        echo "----------Item id-----------<br>";
//         	                        pre($orderItemId);
        	                        
        	                        if(isset($orderItem->variation->attributes) AND isset($orderItemId)){
        	                            foreach ($orderItem->variation->attributes as $keyVA => $orderItemAttribute){
        	                                $orderItemAttributes = new OrderItemAttributesModel($db);
        	                                $orderItemAttributes->store_id = $storeId;
        	                                $orderItemAttributes->order_id = $orderId;
        	                                $orderItemAttributes->item_id = $orderItemId;
        	                                $orderItemAttributes->PedidoId = $ordersModel->PedidoId;
        	                                $orderItemAttributes->PedidoItemId = $orderItems->PedidoItemId;
        	                                $orderItemAttributes->Nome = $orderItemAttribute->name;
        	                                $orderItemAttributes->Valor = $orderItemAttribute->value;
        	                                $orderItemAttributeId = $orderItemAttributes->Save();
//         	                                echo "----------Item Attr-----------<br>";
//         	                                pre($orderItemAttributeId);
        	                            }
        	                        }
        	                    }
        	                }
        	                if(isset($orderId)){
        	                    if(isset($orderPayments)){
        	                        foreach ($orderPayments as $keyP => $payment){
        	                            $orderPayments = new OrderPaymentsModel($db);
        	                            $orderPayments->store_id = $storeId;
        	                            $orderPayments->order_id = $orderId;
        	                            $orderPayments->PedidoId = $ordersModel->PedidoId;
        	                            $orderPayments->PagamentoId = $payment->id;
        	                            $orderPayments->NumeroParcelas = $payment->payment_installments;
        	                            $orderPayments->ValorParcela = $payment->payment_quota;
        	                            $orderPayments->ValorTotal = $payment->payment_total;
        	                            $orderPayments->FormaPagamento = !empty($payment->payment_method) ? $payment->payment_method : $payment->payment_type  ;
        	                            $orderPayments->Metodo = !empty($payment->payment_type) ? $payment->payment_type : $payment->payment_method ;
        	                            $orderPayments->NumeroAutorizacao = $orderPayment->payment_additional_info;
        	                            $orderPayments->DataAutorizacao =  date("Y-m-d H:i:s", strtotime($order->modified));//date($order->modified);
        	                            $orderPayments->Situacao = $order->status;
        	                            $orderPayments->MarketplaceTaxa = $orderItems->TaxaVenda;
        	                            $orderPayments->Marketplace = $ordersModel->Marketplace;
        	                            $orderPaymentsId = $orderPayments->Save();
//         	                            echo "-------Payments-----------<br>";
//         	                            pre($orderPaymentsId);
        	                        }
        	                    }
        	                }
        	                
        	               $ordersCount++;
        	            }
        	            
        	       }
    	          
    	        }while($ordersRestModel->limit <= $orderRes->total);
    	        
    	        $ordersRestModel->next  = null;
    	        
//     	        pre(array('orders_count' => $ordersCount));
	        }
	        logSyncEnd($db, $syncId, $totalOrder."/".$ordersCount);
	        
	        break;
	        	        
	    case "list_order":
	        $ordersRestModel = new OrderRestModel($db, null, $storeId);
	        
	        $orderRes = $ordersRestModel->get();
	        
	        // 	        pre($orderRes);die;
	        
	        // 	        $statusImport = array('pending', 'approved', 'invoice_error', 'shipped', 'delivered', 'waiting_invoice', 'canceled','shipping_informed','shipping_error');
	        
	        $statusImport = array('waiting_picking',
	            'processing_picking',
	            'pending_invoice',
	            'waiting_invoice',
	            'processing_invoice',
	            'waiting_expedition',
	            'processing_expedition',
	            'dispatched',
	            'collected');
	        
	        $statusImport = array('waiting_expedition',
	            'processing_expedition',
	            'dispatched',
	            'collected');
	        
	        
	        
	        foreach($statusImport as $ind => $status){
	            
	            echo  $ordersRestModel->status   = $status;
	            
	            $orderRes = $ordersRestModel->list();
	            
	            pre($orderRes->total);
	            
	        }
	        
	        break;
	      
	    case "get_orders":
	        
	        $PluggOrders = new OrderRestModel($db, null, $storeId);
	        
// 	        $PluggOrders->id = '60d7e7589a74044342a716f9';
	        
	        $data = $PluggOrders->get();
	        
	        pre($data);die;
	        
	        break;
	        
		    
	}
	
}

