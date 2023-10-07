<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';

require_once $path .'/../../../Models/Customers/ManageCustomersModel.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';
require_once $path .'/../Models/Products/ProductsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$orderId = isset($_REQUEST["order_id"]) && $_REQUEST["order_id"] != "" ? $_REQUEST["order_id"] : null ;
$pedidoId = isset($_REQUEST["pedido_id"]) && $_REQUEST["pedido_id"] != "" ? $_REQUEST["pedido_id"] : null ;
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 9);

    require_once $path.'/../../../vendor/autoload.php';
    
//     $email   = 'atendimento.fanlux@gmail.com';
//     $apiKey  = 'oKEbYMqXJoEuHNqV_9h7';
    
//     $email   = 'willians.seo@gmail.com';
//     $apiKey  = 'wdDvgzzG5tdsf9y3CKfM';
//     $xAccountKey = 'xk21bPa9jQ';
//     $baseUri = 'https://api.skyhub.com.br';
    
    $email   = $moduleConfig['email'];
    $apiKey  = $moduleConfig['api_key'];
    $xAccountKey = $moduleConfig['account_key'];
    $baseUri = $moduleConfig['base_uri'];
    
    /** @var \SkyHub\Api $api */
    $api = new SkyHub\Api($email, $apiKey, $xAccountKey, $baseUri);
    switch($action){
       
        case "list_orders":
//             echo 123;die;
            /** @var \SkyHub\Api\Handler\Request\Sales\OrderHandler $requestHandler */
            $requestHandler = $api->order();
//             $response = $requestHandler->order();
            /**
             * GET A LIST OF ORDERS (THIS IS DIFFERENT FROM THE QUEUE)
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
             */
//            

            $PedidoId = $_REQUEST['pedido_id'];
            
            
            if(isset($PedidoId)){
            	 
            	$parts = explode('-', $PedidoId);
            	switch(trim($parts[0])){
            		case '2': $PedidoId = "Lojas Americanas-".trim($parts[1]); break;
            
            	}
            	$response = $requestHandler->order($PedidoId);
            	 
            }else{
            	$response = $requestHandler->orders();
            }

            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                $statusCode = $response->statusCode();
                
                if($statusCode == '200'){
                    
                    $tax = 0.155;
                    
                    $order = json_decode($body);
                    if(isset($order->orders[0])){
                    	$order = $order->orders[0];
                    }
                    
//                     pre($order);
                    
                    $orderItems = $order->items;
                    $orderCustomer = $order->customer;
                    $orderPayments = $order->payments;
                    $orderBillingAddress = $order->billing_address;
                    $orderShippingAddress = $order->shipping_address;
                    $customerModel = new  ManageCustomersModel($db);
                    
                    $tipoPessoa = 1;
                    $numDigit = strlen($orderCustomer->vat_number);
                    $tipoPessoa = $numDigit >= 14 ? 2 : 1;
                    
                    $customerModel->store_id = $storeId;
                    $customerModel->Codigo = $orderCustomer->vat_number;
                    $customerModel->TipoPessoa = $tipoPessoa;
                    $customerModel->Nome = $orderCustomer->name;
                    $customerModel->Responsavel = 'Skyhub';
                    
                    $genero = isset($orderCustomer->gender) ? $orderCustomer->gender : '' ;
                    if(!empty($genero)){
                        switch($genero){
                            case "male" : $genero = "Masculino";break;
                            case "female" : $genero = "Feminino";break;
                        }
                    }
                    $customerModel->Genero = $genero;
                    $customerModel->Email =$orderCustomer->email;
                    $customerModel->CPFCNPJ = $orderCustomer->vat_number;
                    $customerModel->Telefone = $orderCustomer->phones[0];
                    $customerModel->TelefoneAlternativo = isset($orderCustomer->phones[1]) ? $orderCustomer->phones[1] : '' ;
                    $customerModel->DataNascimento = $orderCustomer->date_of_birth;
                    $customerModel->DataCriacao = date("Y-m-d H:i:s", strtotime($order->placed_at));
                    $customerModel->Endereco = $orderShippingAddress->street;
                    $customerModel->Numero = $orderShippingAddress->number;
                    $customerModel->Complemento = $orderShippingAddress->complement;
                    $customerModel->Bairro = $orderShippingAddress->neighborhood;
                    $customerModel->CEP = $orderShippingAddress->postcode;
                    $customerModel->Cidade = $orderShippingAddress->city;
                    $customerModel->Estado = $orderShippingAddress->region;
                    $customerModel->Marketplace = $order->channel;
                    $customerId = $customerModel->Save();
//                     echo "-------Customer-----------<br>";
//                     pre($customerModel);
                    
                    if(isset($customerId)){
                        $code = explode("-", $order->code);
                        $pedidoId = end($code);
                        
                        $ordersModel = new OrdersModel($db);
                        
                        $ordersModel->store_id = $storeId;
                        $ordersModel->customer_id = $customerId;
                        $ordersModel->PedidoId = $pedidoId;
                        $ordersModel->Nome = $orderCustomer->name;
                        $ordersModel->CPFCNPJ = $customerModel->CPFCNPJ;
                        $ordersModel->NomeDestino  = $orderCustomer->name;
                        $ordersModel->Email = $orderCustomer->email;
                        $ordersModel->Telefone = $orderCustomer->phones[0];
                        $ordersModel->Endereco = $orderShippingAddress->street;
                        $ordersModel->Complemento = $orderShippingAddress->complement;
                        $ordersModel->Bairro = $orderShippingAddress->neighborhood;
                        $ordersModel->Numero = $orderShippingAddress->number;
                        $ordersModel->Cidade = $orderShippingAddress->city;
                        $ordersModel->Estado = $orderShippingAddress->region;
                        $ordersModel->Cep = $orderShippingAddress->postcode;
                        $ordersModel->shipping_id = $order->shipping_method;
                        $ordersModel->DataPedido = date("Y-m-d H:i:s", strtotime($order->placed_at));
                        if(!empty($orderPayments)){
                            $ordersModel->FormaPagamento = $orderPayments[0]->method;
                            if($orderPayments->parcels > 0 ){
                                $ordersModel->Parcelas = $orderPayments->parcels;
                                $ordersModel->ValorParcelas = $orderPayments->total_ordered / $orderPayments->parcels;
                            }
                        }
                        $subTotal = 0;
                        foreach($orderItems as $k => $item){
                            $subTotal += !empty($item->special_price) ? $item->special_price *  $item->qty : $item->original_price *  $item->qty ;
                        }
                        $ordersModel->Subtotal = $subTotal;
                        $ordersModel->ValorPedido = $order->total_ordered;
                        //                     $ordersModel->ValorPedido = $order->total_ordered - $order->shipping_cost;
                        $ordersModel->ValorFrete = $order->shipping_cost;
                        $ordersModel->ValorCupomDesconto = $order->discount;
                        $ordersModel->MarketplaceTaxa = $ordersModel->ValorPedido * $tax;
                        $status = getSystemDefaultPaymentStatus($order->status->type);
                        $ordersModel->Status = $status['code'];
                        $ordersModel->Canal = "Skyhub";
                        $ordersModel->Marketplace = $order->channel;
                        
                        $orderId =  $ordersModel->Save();
//                         echo "-------OrderId-----------<br>";
//                         pre($ordersModel);
                    }
                    
                    if(isset($orderId)){
                        
                        foreach ($orderItems as $keyItem => $orderItem){
                            
                            $sku = isset($orderItem->id) ? $orderItem->id : $orderItem->product_id;
                            $sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}'";
                            $queryAP = $db->query($sqlAP);
                            $availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
                            $urlImage =  getUrlImageFromSku($db, $storeId, $sku);
                            $orderItems = new OrderItemsModel($db);
                            $orderItems->store_id = $storeId;
                            $orderItems->order_id = $orderId;
                            $orderItems->PedidoId = $pedidoId;
                            $orderItems->PedidoItemId = isset($orderItem->product_id) ? $orderItem->product_id : $orderItem->id;
                            $orderItems->SKU = $sku;
                            $orderItems->Nome = $orderItem->name;
                            $orderItems->Quantidade = $orderItem->qty;
                            $orderItems->PrecoUnitario = $item->original_price ;
                            $orderItems->PrecoVenda = !empty($item->special_price) ? $item->special_price : $item->original_price ;
                            $orderItems->TaxaVenda = $orderItems->PrecoVenda * $tax;
                            $orderItems->UrlImagem = $urlImage[0];
                            $orderItems->Marketplace = 'Skyhub';
                            $orderItemId =  $orderItems->Save();
//                             echo "-------Item id-----------<br>";
//                             pre($orderItems);
                            
                        }
                        
                        if(isset($orderPayments)){
                            foreach ($orderPayments as $keyP => $orderPayment){
//                                 pre($orderPayment);
                                $orderPayments = new OrderPaymentsModel($db);
                                $orderPayments->store_id = $storeId;
                                $orderPayments->order_id = $orderId;
                                $orderPayments->PedidoId = $pedidoId;
                                $orderPayments->PagamentoId = !empty($orderPayment->sefaz->id_payment) ? $orderPayment->sefaz->id_payment : $keyP + 1 ; 
                                if($orderPayments->NumeroParcelas > 0 ){
                                    $orderPayments->NumeroParcelas = $orderPayments->parcels;
                                    $orderPayments->ValorParcela = $orderPayments->total_ordered / $orderPayments->parcels;
                                }
                                
                                $orderPayments->ValorTotal = $orderPayment->value;
                                $formaPagamento = $orderPayment->sefaz->name_card_issuer != 'null'  ?  $orderPayment->sefaz->name_card_issuer : $orderPayment->sefaz->name_payment ;
                                $orderPayments->FormaPagamento =  isset($formaPagamento) ? $formaPagamento : $orderPayment->method;
                                $orderPayments->Metodo = $orderPayment->method;
                                $orderPayments->NumeroAutorizacao = !empty($orderPayment->authorization_id) ? $orderPayment->authorization_id : '';
                                $orderPayments->DataAutorizacao = date("Y-m-d H:i:s", strtotime($orderPayment->transaction_date));
                                $status = !empty($orderPayment->status) ? getSystemDefaultPaymentStatus($orderPayment->status) :  getSystemDefaultPaymentStatus($order->status->type) ;
                                $orderPayments->Situacao = $status['code'];
                                $orderPayments->MarketplaceTaxa = !empty($ordersModel->MarketplaceTaxa) ? $ordersModel->MarketplaceTaxa : '0.00';
                                $orderPayments->Marketplace = $order->channel;
                                
                                $orderPaymentsId = $orderPayments->Save();
//                                 echo "-------Payments-----------<br>";
//                                 pre($orderPaymentsId);
//                                 pre($orderPayments);
                                
                            }
                        }
                        
                        if(!empty($orderItemId)){
//                             $response = $entityInterface->delete($order->code);
                            $statusCode = $response->statusCode();
                            if($request != 'System'){
                                echo "success|Pedido {$order->code} atualizado com sucesso!";
                            }
                        }else{
                            echo "error|falha ao importar item do pedido {$order->code}";
                        }
                        
                    }
                    
                }else{
                    
                    if($statusCode != '204'){
                        echo "error|".$response->message();
                    }
                }
            }
            
            
            break;
        

        case "add_shipping_exception":
            
            $shippingException = isset($_REQUEST["shipping_exception"]) && $_REQUEST["shipping_exception"] != "" ? $_REQUEST["shipping_exception"] : null ;
            
            if(!$shippingException){
                
                return;
                
            }
            
            $dateCreatedTo = date("Y-m-d\TH:i:sP");
            
            //'2012-10-06T04:13:00-03:00'
            
            /** @var \SkyHub\Api\Handler\Request\Sales\OrderHandler $requestHandler */
            $requestHandler = $api->order();
            
            /**
             * CREATE AN SHIPPING EXCEPTION TO ORDER
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
             */
            $response = $requestHandler->shipmentException($pedidoId, $dateCreatedTo, $shippingException);
//             pre($response);die;
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                $statusCode = $response->statusCode();
                if($statusCode == '201'){
                    $status = getSystemDefaultPaymentStatus('SHIPMENT_EXCEPTION');
                    $db->query("UPDATE orders SET status = '{$status['code']}' WHERE store_id = {$storeId} AND id = {$orderId}");
                }
                if($request != 'System'){
                    echo "success|$pedidoId|$statusCode|".$status['code'];
                }
                
            }else{
                echo "error|".$response->message();
            }
            
            break;
            
        case "add_shipping":
            
            $shippingCode = isset($_REQUEST["shipping_code"]) && $_REQUEST["shipping_code"] != "" ? $_REQUEST["shipping_code"] : null ;
            $shippingType = isset($_REQUEST["shipping_type"]) && $_REQUEST["shipping_type"] != "" ? $_REQUEST["shipping_type"] : null ;
            $shippingMethod = isset($_REQUEST["shipping_method"]) && $_REQUEST["shipping_method"] != "" ? $_REQUEST["shipping_method"] : null ;
            $shippingDelivered = isset($_REQUEST["shipping_delivered"]) && $_REQUEST["shipping_delivered"] != "" ? $_REQUEST["shipping_delivered"] : null ;
            
            
            $query = $db->query("SELECT * FROM order_items WHERE store_id = {$storeId} AND order_id = {$orderId}");
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            /**
             * CREATE AN SHIPPING EXCEPTION TO ORDER
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
             */
            foreach($res as $key => $item){
                $items[] = array(
                    'sku' => $item['SKU'],
                    'qty' => $item['Quantidade'],
                    );
            }

            /** @var \SkyHub\Api\Handler\Request\Sales\OrderHandler $requestHandler */
            $requestHandler = $api->order();
            
            $response = $requestHandler->shipment(
                $pedidoId,
                $items,
                $shippingCode,
                $shippingType,
                $shippingMethod,
                'www.correios.com.br'
                );
            
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                $statusCode = $response->statusCode();
                if($statusCode == '201'){
                    $status = getSystemDefaultPaymentStatus('SHIPPED');
                    echo $sqlStatus = "UPDATE orders SET status = '{$status['code']}' WHERE store_id = {$storeId} AND id = {$orderId}";
                    $queryStatus = $db->query($sqlStatus);
                
                    $queryNfKey = $db->query("SELECT fiscal_key FROM orders WHERE store_id = {$storeId} AND id = {$orderId}");
                    $resNfKey = $queryNfKey->fetch(PDO::FETCH_ASSOC);
                    if(!empty($resNfKey['fiscal_key'])){
                        
                        $res = invoiceOrder ($db, $api, $storeId, $orderId, $pedidoId, $resNfKey['fiscal_key']);
                    }
                    
                    if($shippingDelivered){
                        
                        $responseDelivered = $requestHandler->delivery($pedidoId);
                        if( method_exists( $responseDelivered, 'body' ) ){
                            
                            $statusCode = $responseDelivered->statusCode();
                            if($statusCode == '201'){
                                $status = getSystemDefaultPaymentStatus('DELIVERED');
                                $db->query("UPDATE orders SET status = '{$status['code']}' WHERE store_id = {$storeId} AND id = {$orderId}");
                            }
                            
                        }else{
                            echo "error|".$response->message();
                        }
                        
                    }
                    
                    
                }
                echo "success|$shippingCode|$shippingType|$shippingMethod|$pedidoId|$statusCode|".$status['code'];
            }else{
                echo "error|".$response->message();
            }
            
            break;
            
        case "delivery_order":
            
            /** @var \SkyHub\Api\Handler\Request\Sales\OrderHandler $requestHandler */
            $requestHandler = $api->order();
            
            /**
             * SET AN ORDER AS DELIVERED
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
             */
            $response = $requestHandler->delivery($pedidoId);
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                $statusCode = $response->statusCode();
                pre($statusCode);
                if($statusCode == '201'){
                    $status = getSystemDefaultPaymentStatus('DELIVERED');
                    echo $sqlStatus = "UPDATE orders SET status = '{$status['code']}' WHERE store_id = {$storeId} AND id = {$orderId}";
                    $db->query($sqlStatus);
                }
                echo "success|$pedidoId|$statusCode|".$status['code'];
                
            }else{
                echo "error|".$response->message();
            }
            
            break;
            
        case "cancel_order":
            /** @var \SkyHub\Api\Handler\Request\Sales\OrderHandler $requestHandler */
            $requestHandler = $api->order();
            
            /**
             * CANCEL AN ORDER
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
             */
            $response = $requestHandler->cancel($pedidoId);
            
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                $statusCode = $response->statusCode();
                if($statusCode == '201'){
                    $db->query("UPDATE orders SET status = 'canceled' WHERE store_id = {$storeId} AND PedidoId = '{$pedidoId}'");
                }
            }else{
                echo "error|".$response->message();
            }
            
            
            break;
            
        case "get_orders_queue":
            
            $tax = 0.155;
            /** @var \SkyHub\Api\Handler\Request\Sales\Order\QueueHandler $requestHandler */
            $requestHandler = $api->queue();
            
            /** @var \SkyHub\Api\EntityInterface\Sales\Order\Queue $entityInterface */
            $entityInterface = $requestHandler->entityInterface();
            
            /**
             * GET A LIST OF ORDERS IN THE QUEUE
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response 
             */
            $response = $entityInterface->orders();
            
            if( method_exists( $response, 'body' ) ){
                $body = $response->body();
                
                $statusCode = $response->statusCode();
                
                if($statusCode == '200'){
                    
                    $tax = 0.155;
                    
                    $order = json_decode($body);
                    pre($order);
                    $orderItems = $order->items;
                    $orderCustomer = $order->customer;
                    $orderPayments = $order->payments;
                    $orderBillingAddress = $order->billing_address;
                    $orderShippingAddress = $order->shipping_address;
                    $customerModel = new  ManageCustomersModel($db);
                    
                    $tipoPessoa = 1;
                    $numDigit = strlen($orderCustomer->vat_number);
                    $tipoPessoa = $numDigit >= 14 ? 2 : 1;
                    
                    $customerModel->store_id = $storeId;
                    $customerModel->Codigo = $orderCustomer->vat_number;
                    $customerModel->TipoPessoa = $tipoPessoa;
                    $customerModel->Nome = $orderCustomer->name;
                    $customerModel->Responsavel = 'Skyhub';
                    
                    $genero = isset($orderCustomer->gender) ? $orderCustomer->gender : '' ;
                    if(!empty($genero)){
                        switch($genero){
                            case "male" : $genero = "Masculino";break;
                            case "female" : $genero = "Feminino";break;
                        }
                    }
                    $customerModel->Genero = $genero;
                    $customerModel->Email =$orderCustomer->email;
                    $customerModel->CPFCNPJ = $orderCustomer->vat_number;
                    $customerModel->Telefone = $orderCustomer->phones[0];
                    $customerModel->TelefoneAlternativo = isset($orderCustomer->phones[1]) ? $orderCustomer->phones[1] : '' ;
                    $customerModel->DataNascimento = $orderCustomer->date_of_birth;
                    $customerModel->DataCriacao = date("Y-m-d H:i:s", strtotime($order->placed_at));
                    $customerModel->Endereco = $orderShippingAddress->street;
                    $customerModel->Numero = $orderShippingAddress->number;
                    $customerModel->Complemento = $orderShippingAddress->complement;
                    $customerModel->Bairro = $orderShippingAddress->neighborhood;
                    $customerModel->CEP = $orderShippingAddress->postcode;
                    $customerModel->Cidade = $orderShippingAddress->city;
                    $customerModel->Estado = $orderShippingAddress->region;
                    $customerModel->Marketplace = $order->channel;
                    
                    $customerId = $customerModel->Save();
                    echo "-------Customer-----------<br>";
                    pre($customerModel);
                    
                    if(isset($customerId)){
                        $code = explode("-", $order->code);
                        $pedidoId = end($code);
                        
                        $ordersModel = new OrdersModel($db);
                        
                        $ordersModel->store_id = $storeId;
                        $ordersModel->customer_id = $customerId;
                        $ordersModel->PedidoId = $pedidoId;
                        $ordersModel->Nome = $orderCustomer->name;
                        $ordersModel->CPFCNPJ = $customerModel->CPFCNPJ;
                        $ordersModel->NomeDestino  = $orderCustomer->name;
                        $ordersModel->Email = $orderCustomer->email;
                        $ordersModel->Telefone = $orderCustomer->phones[0];
                        $ordersModel->Endereco = $orderShippingAddress->street;
                        $ordersModel->Complemento = $orderShippingAddress->complement;
                        $ordersModel->Bairro = $orderShippingAddress->neighborhood;
                        $ordersModel->Numero = $orderShippingAddress->number;
                        $ordersModel->Cidade = $orderShippingAddress->city;
                        $ordersModel->Estado = $orderShippingAddress->region;
                        $ordersModel->Cep = $orderShippingAddress->postcode;
                        $ordersModel->shipping_id = $order->shipping_method;
                        $ordersModel->DataPedido = date("Y-m-d H:i:s", strtotime($order->placed_at));
                        if(!empty($orderPayments)){
                            $ordersModel->FormaPagamento = $orderPayments[0]->method;
                            if($orderPayments[0]->parcels > 0 ){
                                $ordersModel->Parcelas = $orderPayments[0]->parcels;
                                $ordersModel->ValorParcelas = $orderPayments->total_ordered / $orderPayments[0]->parcels;
                            }
                        }
                        $subTotal = 0;
                        foreach($orderItems as $k => $item){
                            $subTotal += !empty($item->special_price) ? $item->special_price *  $item->qty : $item->original_price *  $item->qty ;
                        }
                        $ordersModel->Subtotal = $subTotal;
                        $ordersModel->ValorPedido = $order->total_ordered;
                        //                     $ordersModel->ValorPedido = $order->total_ordered - $order->shipping_cost;
                        $ordersModel->ValorFrete = $order->shipping_cost;
                        $ordersModel->ValorCupomDesconto = $order->discount;
                        $ordersModel->MarketplaceTaxa = $ordersModel->ValorPedido * $tax;
                        $status = getSystemDefaultPaymentStatus($order->status->type);
                        $ordersModel->Status = $status['code'];
                        $ordersModel->Canal = "Skyhub";
                        $ordersModel->Marketplace = $order->channel;
                        
                        $orderId =  $ordersModel->Save();
                        echo "-------OrderId-----------<br>";
                        pre($ordersModel);
                    }
                    
                    if(isset($orderId)){
                        
                        foreach ($orderItems as $keyItem => $orderItem){
                            
                            $sku = isset($orderItem->id) ? $orderItem->id : $orderItem->product_id;
                            
                            $sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}'";
                            $queryAP = $db->query($sqlAP);
                            $availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
                            $urlImage =  getUrlImageFromSku($db, $storeId, $sku);
                            $orderItems = new OrderItemsModel($db);
                            $orderItems->store_id = $storeId;
                            $orderItems->order_id = $orderId;
                            $orderItems->PedidoId = $pedidoId;
                            $orderItems->PedidoItemId = isset($orderItem->product_id) ? $orderItem->product_id : $orderItem->id;
                            $orderItems->SKU = $sku;
                            $orderItems->Nome = $orderItem->name;
                            $orderItems->Quantidade = $orderItem->qty;
                            $orderItems->PrecoUnitario = $item->original_price ;
                            $orderItems->PrecoVenda = !empty($item->special_price) ? $item->special_price : $item->original_price ;
                            $orderItems->TaxaVenda = $orderItems->PrecoVenda * $tax;
                            $orderItems->UrlImagem = $urlImage[0];
                            $orderItems->Marketplace = 'Skyhub';
                            $orderItemId =  $orderItems->Save();
                            echo "-------Item id-----------<br>";
                            pre($orderItems);
                            
                        }
                        
                        if(isset($orderPayments)){
                            foreach ($orderPayments as $keyP => $orderPayment){
                                pre($orderPayment);
                                $orderPayments = new OrderPaymentsModel($db);
                                $orderPayments->store_id = $storeId;
                                $orderPayments->order_id = $orderId;
                                $orderPayments->PedidoId = $pedidoId;
//                                 $orderPayments->PagamentoId = $orderPayment->sefaz->id_payment;
                                $orderPayments->PagamentoId = !empty($orderPayment->sefaz->id_payment) ? $orderPayment->sefaz->id_payment : $keyP + 1 ;
                                if($orderPayments->NumeroParcelas > 0 ){
                                    $orderPayments->NumeroParcelas = $orderPayments->parcels;
                                    $orderPayments->ValorParcela = $orderPayments->total_ordered / $orderPayments->parcels;
                                }
                                
                                $orderPayments->ValorTotal = $orderPayment->value;
                                $formaPagamento = $orderPayment->sefaz->name_card_issuer != 'null'  ?  $orderPayment->sefaz->name_card_issuer : $orderPayment->sefaz->name_payment ;
//                                 $orderPayments->FormaPagamento =  $formaPagamento;
                                $orderPayments->FormaPagamento =  isset($formaPagamento) ? $formaPagamento : $orderPayment->method;
                                $orderPayments->Metodo = $orderPayment->method;
                                $orderPayments->NumeroAutorizacao = !empty($orderPayment->authorization_id) ? $orderPayment->authorization_id : '';
                                $orderPayments->DataAutorizacao = date("Y-m-d H:i:s", strtotime($orderPayment->transaction_date));
                                $status = !empty($orderPayment->status) ? getSystemDefaultPaymentStatus($orderPayment->status) :  getSystemDefaultPaymentStatus($order->status->type) ;
                                $orderPayments->Situacao = $status['code'];
                                $orderPayments->MarketplaceTaxa = !empty($ordersModel->MarketplaceTaxa) ? $ordersModel->MarketplaceTaxa : '0.00';
                                $orderPayments->Marketplace = $order->channel;
                                
                                $orderPaymentsId = $orderPayments->Save();
//                                 echo "-------Payments-----------<br>";
//                                 pre($orderPaymentsId);
//                                 pre($orderPayments);
                                
                            }
                        }
                        
                        if(!empty($orderItemId)){
                            $response = $entityInterface->delete($order->code);
                            $statusCode = $response->statusCode();
                            if($request != 'System'){
                                echo "success|Pedido {$order->code} atualizado com sucesso!";
                            }
                        }else{
                            if($request != 'System'){
                                echo "error|falha ao importar item do pedido {$order->code}";
                            }
                        }
                        
                    }
                    
                }else{
                    
                    if($statusCode != '204'){
                        echo "error|".$response->message();
                    }
                }
            }
            
            break;
            
        case "invoice":
            
            $syncId =  logSyncStart($db, $storeId, "Skyhub", $action, "Exportação XML Nota Fiscal.", $request);
            $exported = 0;
            require_once $path .'/../../Sysemp/Class/class-PgConnection.php';
            
            $pg = new PgConnection($db, $storeId);
            
            $orderId = isset($_REQUEST["order_id"])  ? intval($_REQUEST["order_id"]) : NULL ;
            
            $dateFrom =  date("Y-m-d", strtotime("-7 day", strtotime("now")));
            $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND Canal LIKE 'Skyhub' AND Status != 'cancelled' AND
           DataPedido >= '{$dateFrom}' ORDER BY id_nota_saida DESC";
            
            if(isset($orderId)){
                $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND id = {$orderId}";
            }
            $query = $db->query($sql);
            
            $orders = $query->fetchAll(PDO::FETCH_ASSOC);
            
//             pre($orders); 
            
            foreach($orders as $key => $order){
                $nfe = '';
                $sql = "SELECT * FROM nota_saida WHERE id_nota_saida = {$order['id_nota_saida']}";
                $query = $pg->query($sql);
                $rowFatura = $query->fetch(PDO::FETCH_ASSOC);
                $idNotaSaidaFatura = $rowFatura['id_nota_saida_fatura'] > 0 ? $rowFatura['id_nota_saida_fatura'] : $order['id_nota_saida'] ;
                if(isset($idNotaSaidaFatura)){
                    
                    $sql = "SELECT encode(arquivo,  'escape') as arquivo FROM nota_saida_xml WHERE id_nota_saida = '{$idNotaSaidaFatura}'";
                    $query = $pg->query($sql);
                    $row = $query->fetch(PDO::FETCH_ASSOC);
                    
                    if(!empty($row['arquivo'])){
                        $nfe = simplexml_load_string($row['arquivo']);
                        $nfKey =  xml_attribute($nfe->NFe->infNFe, 'Id');
                    }
                    
                }
//                 pre($nfKey);
                
                if(!empty($nfKey)){
                    
                
                    $digitsCount = strlen($nfKey);
                    if($digitsCount > 44){
                        $sub = $digitsCount - 44;
                        
                        $nfKey = substr($nfKey, $sub);
                    }
    //                 $teste  = '99999999999999999999999999999999999999999999';
    
                    $pedidoId = !empty($pedidoId) ? $pedidoId : $order['PedidoId'] ;
                    
                    $orderId = !empty($orderId) ? $orderId : $order['id'] ;
                    
                    $res = invoiceOrder ($db, $api, $storeId, $orderId, $order['Marketplace']."-".$pedidoId, $nfKey);
//                     pre($res);die;
                    if($res){
                        $exported++;
                        $sqlUpdate = "UPDATE orders SET status = 'invoiced' , fiscal_key = '{$nfKey}'
                                    WHERE store_id = {$storeId} AND id = {$order['id']}";
                        echo "success|$orderId|$pedidoId";
                    }else{
                        $sqlUpdate = "UPDATE orders SET fiscal_key = NULL
                                    WHERE store_id = {$storeId} AND id = {$order['id']}";
                        echo "{$order['id_nota_saida']} - OrderId: {$order['id']} -  PedidoId: {$order['PedidoId']} - DataPedido: {$order['DataPedido']} - Nome: {$order['Nome']} - {$order['Status']}<br>";
                        echo "error|Erro ao enviar chave da nota fiscal";
                    }
                    
                    $db->query($sqlUpdate);
                    
                }else{
//                     $error = date('d/m/Y H:i:s')."Erro chave da nota fiscal não encontrada";
//                     $sqlUpdateError = "UPDATE orders SET fiscal_key = NULL, error = '{$error}'
//                                     WHERE store_id = {$storeId} AND id = {$order['id']}";
                    
                    echo  "warning|O pedido ainda não foi faturado";
                    
//                     $db->query($sqlUpdateError);
                    
                    
                }
               
            
            }
            break;
            
        case "invoice_onbi":
            
            
            $syncId =  logSyncStart($db, $storeId, "Skyhub", $action, "Exportação XML Nota Fiscal.", $request);
            $exported = 0;
            
            $orderId = isset($_REQUEST["order_id"])  ? intval($_REQUEST["order_id"]) : NULL ;
//             $dateFrom =  date("Y-m-d", strtotime("-10 day", strtotime("now")));
//             $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND Canal LIKE 'Skyhub' AND Status != 'cancelled' AND
//             DataPedido >= '{$dateFrom}' AND id_nota_saida IS NOT NULL  AND fiscal_key IS NULL OR fiscal_key = 0 ORDER BY id_nota_saida DESC";
            if(isset($orderId)){
                echo $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND id = {$orderId}";
            }
            $query = $db->query($sql);
            $orders = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($orders as $key => $order){
//                 pre($order);
                $nfKey = '99999999999999999999999999999999999999999999';
                
                if(!empty($nfKey)){
                    
                    
                    $digitsCount = strlen($nfKey);
                    if($digitsCount > 44){
                        $sub = $digitsCount - 44;
                        
                        $nfKey = substr($nfKey, $sub);
                    }
                    
                    $pedidoId = !empty($pedidoId) ? $pedidoId : $order['PedidoId'] ;
                    
                    $orderId = !empty($orderId) ? $orderId : $order['id'] ;
                    
                    $res = invoiceOrder ($db, $api, $storeId, $orderId, $order['Marketplace']."-".$pedidoId, $nfKey);
                    
                    if($res){
                        $exported++;
                        $sqlUpdate = "UPDATE orders SET status = 'invoiced' , fiscal_key = '{$nfKey}'
                                    WHERE store_id = {$storeId} AND id = {$order['id']}";
                        echo "success|$orderId|$pedidoId";
                    }else{
                        $sqlUpdate = "UPDATE orders SET fiscal_key = NULL
                                    WHERE store_id = {$storeId} AND id = {$order['id']}";
                        echo "{$order['id_nota_saida']} - OrderId: {$order['id']} -  PedidoId: {$order['PedidoId']} - DataPedido: {$order['DataPedido']} - Nome: {$order['Nome']} - {$order['Status']}<br>";
                        echo "error|Erro ao enviar chave da nota fiscal";
                    }
                    
                    $db->query($sqlUpdate);
                    
                   
                    
                }
                
                
            }
            break;
            
    }
    
}
function xml_attribute($object, $attribute){
    
    if(isset($object[$attribute]))
        return (string) $object[$attribute];
        
        
}

function invoiceOrder ($db, $api, $storeId, $orderId, $pedidoId, $nfKey){
    
	if( empty($nfKey) or empty($pedidoId) or empty($orderId) ){
		return false;
	}
    /** @var \SkyHub\Api\Handler\Request\Sales\OrderHandler $requestHandler */
    $requestHandler = $api->order();
    
    /**
     * INVOICE AN ORDER
     * @var SkyHub\Api\Handler\Response\HandlerInterface $response
     */
    $response = $requestHandler->invoice($pedidoId, $nfKey);
  
    if(!method_exists( $response, 'body' ) ){
    	return false;
    }
        
    $body = $response->body();
    $statusCode = $response->statusCode();
    if($statusCode == '201'){
       	$status = getSystemDefaultPaymentStatus('INVOICED');
        $query = $db->query("UPDATE orders SET status = '{$status['code']}' WHERE store_id = {$storeId} AND id = {$orderId}");
        if($query){
    	   	return true;
        }
    }
    
    return false;
    
}
