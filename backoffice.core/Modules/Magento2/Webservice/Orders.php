<?php

set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Customers/ManageCustomersModel.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';

require_once $path .'/../Class/class-Soap.php';
require_once $path .'/../Models/Customers/CustomerModel.php';
require_once $path .'/../Models/Sales/SalesModel.php';
require_once $path .'/../Models/Catalog/DirectoryModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 5);
    
    switch($action){
        case "add_comment":
            $salesModel = new SalesModel($db, null, $storeId);
            
            $salesModel->order_increment_id = $_REQUEST['order_id'];
            
            $res = $salesModel->salesOrderAddComment();
            
            pre($res);die;
            
            break;
            
            
        case "import_order":
            
            $salesModel = new SalesModel($db, null, $storeId);
            $customersModel = new CustomerModel($db, null, $storeId);
            $directoryModel = new DirectoryModel($db, null, $storeId);
            
            $orders = $salesModel->salesOrderList();
//             pre($orders);die;
            foreach($orders as $key => $order){
                
                $salesModel->order_increment_id = $order->increment_id;
                $orderInfo = $salesModel->salesOrderInfo();
//                 pre($orderInfo);
                $customersModel->customer_id = $order->customer_id;
                $customer = $customersModel->customerCustomerInfo();
                
                $customerModel = new  ManageCustomersModel($db);
                switch($customer->customer_type){
                    case "3": $tipoPessoa = 1;break;
                    case "4": 
                        $tipoPessoa = 2;
                        $customerModel->RGIE = $customer->document_two;
                    break;
                        
                    default: $tipoPessoa = 1; break;
                }
                
                $customerModel->store_id = $storeId;
                $customerModel->Codigo = $customer->customer_id;
                $customerModel->TipoPessoa = $tipoPessoa;
                $customerModel->Nome = $orderInfo->customer_firstname." ".$orderInfo->customer_lastname;
                $customerModel->Apelido = $orderInfo->customer_firstname;
                $customerModel->Email = $orderInfo->customer_email;
                $customerModel->CPFCNPJ = $customer->taxvat;
                $customerModel->Telefone = $orderInfo->billing_address->telephone;
                $customerModel->TelefoneAlternativo = $orderInfo->shipping_address->telephone;
                $customerModel->DataNascimento = $customer->dob;
                    $parts = explode("T", $customer->created_at);
                $customerModel->DataCriacao = $parts[0];
                $address = explode("\n", $orderInfo->billing_address->street);
                $customerModel->Endereco = $address[0];
                $customerModel->Numero = $address[1];
                $customerModel->Complemento = $address[2];
                $customerModel->Bairro = $address[3];
                $customerModel->CEP = $orderInfo->billing_address->postcode;
                $customerModel->Cidade = $orderInfo->billing_address->city;
                $directoryModel->region_id = $orderInfo->billing_address->region_id;
                    $region = $directoryModel->GetRegionCodeFromId();
                $customerModel->Estado = $region->code;
                $customerModel->Marketplace = "Onbi";
                $customerId = $customerModel->Save();
	            echo "-------Customer-----------<br>";
	            pre($customerId);
                
                if(isset($customerId)){
                    $ordersModel = new OrdersModel($db);
                    $ordersModel->store_id = $storeId;
                    $ordersModel->customer_id = $customerId;
                    $ordersModel->PedidoId = $orderInfo->increment_id;
                    $ordersModel->Nome = $orderInfo->customer_firstname." ".$orderInfo->customer_lastname;
                    $ordersModel->Email = $orderInfo->customer_email;
                    $ordersModel->Telefone = $orderInfo->shipping_address->telephone;
                    $shippingAddress = explode("\n", $orderInfo->shipping_address->street);
                    $ordersModel->Endereco = $shippingAddress[0];
                    $ordersModel->Numero = $shippingAddress[1];
                    $ordersModel->Complemento = $shippingAddress[2];
                    $ordersModel->Bairro = $shippingAddress[3];
                    $ordersModel->Cep = $orderInfo->shipping_address->postcode;
                    $ordersModel->Cidade = $orderInfo->shipping_address->city;
                    
                    $directoryModel->region_id = $orderInfo->shipping_address->region_id;
                    $region = $directoryModel->GetRegionCodeFromId();
                    
                    $ordersModel->Estado = $region->code;
                    $ordersModel->NomeDestino= $orderInfo->shipping_address->firstname." ".$orderInfo->shipping_address->lastname;
                    $ordersModel->DataPedido = $orderInfo->created_at;
                    $ordersModel->FormaPagamento = $orderInfo->payment->method;
                    $ordersModel->Parcelas = 1;
                    $ordersModel->ValorParcelas = $orderInfo->grand_total;
                    
                    $ordersModel->Subtotal = $orderInfo->subtotal;
                    $ordersModel->ValorFrete = $orderInfo->payment->shipping_amount;
                    
//                     $statusHistory = explode("aprovado", $orderInfo->status_history[0]->comment);
//                     $status = count($statusHistory) > 1 ? "approved" : 'processing' ;
//                     $status = 'approved';
//                     $status = $orderInfo->status_history[0]->status;
                    $status = $salesModel->setStatus($orderInfo->status);
                    pre($status);
//                     pre($orderInfo->status);
//                     if($status == 'approved' ){
                        $ordersModel->ValorTotal = $orderInfo->grand_total;
                        $ordersModel->ValorPedido = $orderInfo->grand_total;
                        $ordersModel->ValorCupomDesconto =  $orderInfo->discount_amount;
                        $ordersModel->MarketplaceTaxa = $orderInfo->tax_amount;
                        
//                     }else{
                        
//                         $message = "Pedido {$orderInfo->increment_id} com status {$orderInfo->status_history[0]->comment}";
//                         notifyAdmin($message);
//                     }

                    $ordersModel->Status = $status;
                    $ordersModel->Marketplace = 'Onbi';
                    $ordersModel->shipping_id = getNumbers($orderInfo->shipping_method);
                    $orderId =  $ordersModel->Save();

        	        echo "-------OrderId-----------<br>";
        	        pre($orderId);
        	        pre($ordersModel);
        	        
                }else{
                    setMlLog($db, $storeId, 'order', $order->id, "error", "webservice", "Erro ao importart Cliente: {$customerModel->Nome} | Pedido: {$order->id}");
                }

                
                if(isset($orderId)){
                    foreach ($orderInfo->items as $keyItem => $orderItem){
                        
                        $sku = $orderItem->sku;
//                         $sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}'";
//                         $queryAP = $db->query($sqlAP);
//                         $availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
                        $urlImage =  getUrlImageFromSku($db, $storeId, $sku);
                        $orderItems = new OrderItemsModel($db);
                        $orderItems->store_id = $storeId;
                        $orderItems->order_id = $orderId;
                        $orderItems->PedidoId = $orderInfo->increment_id;
                        $orderItems->PedidoItemId = $orderItem->item_id;
                        $orderItems->SKU = $sku;
                        $orderItems->Nome = $orderItem->name;
                        $orderItems->Quantidade = $orderItem->qty_ordered;
                        $orderItems->TipoAnuncio = 'Onbi';
                        $orderItems->PrecoUnitario = $orderItem->base_price;
                        $orderItems->PrecoVenda = $orderItem->original_price;
                        $orderItems->TaxaVenda = $orderItem->tax_amount;
                        $orderItems->UrlImagem = $urlImage[0];
                        $orderItemId =  $orderItems->Save();
        	            echo "-------Item id-----------<br>";
    	                pre($orderItemId);
//     	                pre($orderItems);
                    }
                    
                }else{
                    
//                     setMlLog($db, $storeId, 'order', $orderInfo->increment_id, "error", "webservice", "Erro ao importart Pedido: {$order->id}");
                }
                
                
                
                if(isset($orderId)){
                    if(isset($orderInfo->payment)){
                        $orderPayments = new OrderPaymentsModel($db);
                        $orderPayments->store_id = $storeId;
                        $orderPayments->order_id = $orderId;
                        $orderPayments->PedidoId = $orderInfo->increment_id;
                        $orderPayments->PagamentoId = $orderInfo->payment->payment_id;
                        $orderPayments->NumeroParcelas = 1;
                        $orderPayments->ValorParcela = $orderInfo->payment->amount_ordered;
                        $orderPayments->ValorTotal = $orderInfo->payment->amount_ordered;
                        $orderPayments->Desconto = '0.00';
                        $orderPayments->FormaPagamento =  $orderInfo->payment->method;
                        $orderPayments->Metodo = $orderInfo->payment->method;
//                      $orderPayments->NumeroAutorizacao = $orderInfo->authorization_code;
                        $orderPayments->DataAutorizacao = $orderInfo->status_history[0]->created_at;
                        $orderPayments->Situacao = $status;
                        $orderPayments->MarketplaceTaxa = $orderInfo->tax_amount;
                        $orderPayments->Marketplace = "Onbi";
                        $orderPaymentsId = $orderPayments->Save();
                        
            	        echo "-------Payments-----------<br>";
            	        pre($orderPaymentsId);
//             	        pre($orderPayments);
                            
                    }else{
                        
                        setMlLog($db, $storeId, 'order', $orderInfo->increment_id, "error", "webservice", "Erro ao importart pagamento do Pedido: {$order->id}");
                    }
                }
                
            }
            
            break;
            
    }
    
    
}
