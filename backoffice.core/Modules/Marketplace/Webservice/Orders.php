<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Customers/ManageCustomersModel.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';
require_once $path .'/../Class/class-ConfigMws.php';
require_once $path .'/../Class/class-MWS.php';
require_once $path .'/../Models/API/OrderModel.php';
require_once $path .'/../Models/API/OrderItemModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$orderId = isset($_REQUEST["order_id"]) && $_REQUEST["order_id"] != "" ? $_REQUEST["order_id"] : null ;
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
        
        case "list_orders":
            
            
            $orderModel = new OrderModel($db, null, $storeId);
            
            $customerModel = new  ManageCustomersModel($db);
//             $dateCreatedFrom =  date("Y-m-d", strtotime("-3 hour", strtotime("now")))."T00:00:00.000-00:00";
            $orderModel->CreatedAfter = date("Y-m-d", strtotime("-1 Day", strtotime("now")))."T00:00:00Z";
            $orders = $orderModel->ListOrdersRequest();
//             pre($orders);die;
            if(isset($orders->ListOrdersResult->Orders)){
                
                foreach($orders->ListOrdersResult->Orders->Order as $k => $order){
                    
                    pre($order);
                    
                    
                    $nome = isset($order->BuyerName) ? $order->BuyerName : 'Sem Nome';
                    $customerModel->store_id = $storeId;
                    $customerModel->Nome = $nome;
                    $customerModel->Email = $order->BuyerEmail;
                    $customerModel->Apelido = $nome;
                    $customerModel->TipoPessoa = '1';
                    if(isset($order->BuyerTaxInfo->CompanyLegalName)){
                        $customerModel->Nome = $order->BuyerTaxInfo->CompanyLegalName;
                        $customerModel->TipoPessoa = '2';
                    }
                    $customerModel->CPFCNPJ = $order->BuyerTaxInfo->TaxClassifications->TaxClassification->Value;
                    $customerModel->Codigo = $customerModel->CPFCNPJ;
                    $customerModel->Telefone = '(00) 00000-0000';
                    $customerModel->DataCriacao = date("Y-m-d H:i:s");
                    $customerModel->Endereco = $order->ShippingAddress->AddressLine1;
                    $customerModel->Cidade = $order->ShippingAddress->City;
                    $customerModel->Bairro = $order->BuyerCounty;
                    $stateRegion = '';
                    if(isset($order->ShippingAddress->StateOrRegion)){
	                    $stateRegion = !is_array($order->ShippingAddress->StateOrRegion) ? get_object_vars($order->ShippingAddress->StateOrRegion[0]) : $order->ShippingAddress->StateOrRegion;
	                    $stateRegion = is_array($stateRegion) ? $stateRegion[0] : $stateRegion ; 
                    }
                    $customerModel->Estado = strlen($stateRegion) > 2  ? getUf($stateRegion) : strtoupper($stateRegion) ;
                    $customerModel->CEP = $order->ShippingAddress->PostalCode;
                    $customerModel->Marketplace = "Amazon";
                    pre($customerModel);
                    echo $customerId = $customerModel->Save();
                    echo "-------Customer-----------<br>";
                    
                    if(isset($customerId)){
                        
                        $orderModel->AmazonOrderId = $order->AmazonOrderId;
                        $orderItem = $orderModel->ListOrderItemsRequest();
                        pre($orderItem);
                        $promotionDiscount = $shippingPriceTotal = 0.00;
                        if(isset($orderItem->ListOrderItemsResult->OrderItems)){
                            foreach($orderItem->ListOrderItemsResult->OrderItems->OrderItem as $j => $item){
                                $shippingPriceTotal += (float) $item->ShippingPrice->Amount;
                                $promotionDiscount += (float) $item->PromotionDiscount->Amount;
                            }
                            
                        }
                        $ordersModel = new OrdersModel($db);
                        $ordersModel->store_id = $storeId;
                        $ordersModel->customer_id = $customerId;
                        $ordersModel->PedidoId = $order->AmazonOrderId;
                        $ordersModel->Nome = $nome;
                        $ordersModel->NomeDestino  = $nome;
                        $ordersModel->Email = $order->BuyerEmail;
                        $ordersModel->Telefone = '(00) 00000-0000';
                        $ordersModel->Endereco = $order->ShippingAddress->AddressLine1;
                        $ordersModel->Bairro = $order->BuyerCounty;
                        $ordersModel->Cidade = $order->ShippingAddress->City;
//                         $stateRegion = is_array($order->ShippingAddress->StateOrRegion) ? $order->ShippingAddress->StateOrRegion : array($order->ShippingAddress->StateOrRegion);
//                         $ordersModel->Estado = count($stateRegion) > 2  ? getUf($order->ShippingAddress->StateOrRegion) : $order->ShippingAddress->StateOrRegion ;
                        $stateRegion = '';
                        if(isset($order->ShippingAddress->StateOrRegion)){
	                        $stateRegion = !is_array($order->ShippingAddress->StateOrRegion) ? get_object_vars($order->ShippingAddress->StateOrRegion[0]) : $order->ShippingAddress->StateOrRegion;
	                        $stateRegion = is_array($stateRegion) ? $stateRegion[0] : $stateRegion ;
                        }
                        $ordersModel->Estado = strlen($stateRegion) > 2  ? getUf($stateRegion) : strtoupper($stateRegion) ;
                        $ordersModel->Cep = $order->ShippingAddress->PostalCode;
                        $ordersModel->DataPedido = date("Y-m-d H:i:s", strtotime($order->PurchaseDate));
                        $ordersModel->FormaPagamento = $order->PaymentMethodDetails->PaymentMethodDetail;
                        $ordersModel->Parcelas = '1';
                        $ordersModel->ValorParcelas = (float) $order->OrderTotal->Amount;
                        $ordersModel->Subtotal = (float) $order->OrderTotal->Amount  - $shippingPriceTotal;
                        $ordersModel->ValorFrete = $shippingPriceTotal;
                        $ordersModel->ValorPedido = (float) $order->OrderTotal->Amount ;
                        $ordersModel->ValorCupomDesconto =  $promotionDiscount;
                        $ordersModel->Status = $order->OrderStatus;
                        $ordersModel->Marketplace = 'Amazon';
                        $ordersModel->Canal = $order->SalesChannel;
                        $ordersModel->shipping_id = $order->shipping->id;
                        $ordersModel->logistic_type = $order->FulfillmentChannel == 'MFN' ? null :  'Fulfillment';
//                         pre($order->AmazonOrderId);
                        pre($ordersModel);
                        echo $orderId =  $ordersModel->Save();
                        echo "-------OrderId-----------<br>";
                        
                    }
                    
                    if(isset($orderId)){
                        
                        if(isset($orderItem->ListOrderItemsResult->OrderItems)){
                            
                            foreach($orderItem->ListOrderItemsResult->OrderItems->OrderItem as $j => $orderItem){
                            
                                $sku = $orderItem->SellerSKU;
                                
                                $sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}'";
                                $queryAP = $db->query($sqlAP);
                                $availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
                                
                                $urlImage =  getUrlImageFromSku($db, $storeId, $sku);
                                $orderItems = new OrderItemsModel($db);
                                $orderItems->store_id = $storeId;
                                $orderItems->order_id = $orderId;
                                $orderItems->PedidoId = $order->AmazonOrderId;
                                $orderItems->PedidoItemId = $orderItem->ASIN;
                                $orderItems->SKU = $sku;
                                $orderItems->Nome = $orderItem->Title;
                                $orderItems->Quantidade = $orderItem->QuantityOrdered;
                                $orderItems->TipoAnuncio = 'Standard';
                                $orderItems->PrecoUnitario = (float) $orderItem->ItemPrice->Amount / $orderItem->QuantityOrdered;
                                $orderItems->PrecoVenda = (float) $orderItem->ItemPrice->Amount / $orderItem->QuantityOrdered;
                                $orderItems->TaxaVenda = (float) $orderItem->ItemTax->Amount;
                                $orderItems->UrlImagem = isset($urlImage[0]) ? $urlImage[0] : null ;
//                                 pre($orderItems);
                                echo $orderItemId =  $orderItems->Save();
                                echo "-------Item id-----------<br>";
                                
                                $attributes = array(
                                    array("Cor" => $availableProduct['color']),
                                    array(ucfirst($availableProduct['variation_type']) => $availableProduct['variation'])
                                );
                                
                                    
                                foreach ($attributes as $keyVA => $orderItemAttribute){
                                    $orderItemAttributes = new OrderItemAttributesModel($db);
                                    $orderItemAttributes->store_id = $storeId;
                                    $orderItemAttributes->order_id = $orderId;
                                    $orderItemAttributes->item_id = $orderItem->OrderItemId;
                                    $orderItemAttributes->PedidoId = $order->AmazonOrderId;
                                    $orderItemAttributes->PedidoItemId = $orderItem->ASIN;
                                    $orderItemAttributes->Nome = key($orderItemAttribute);
                                    $orderItemAttributes->Valor = $orderItemAttribute[key($orderItemAttribute)];
                                    
                                    echo $orderItemAttributeId = $orderItemAttributes->Save();
                                    echo "-------Item Attr-----------<br>";
                                    
                                }
                                
                            }
                            
                        }
                        
                    }
                    
                    if(isset($orderId)){
                                
                        $orderPayments = new OrderPaymentsModel($db);
                        $orderPayments->store_id = $storeId;
                        $orderPayments->order_id = $orderId;
                        $orderPayments->PedidoId = $order->AmazonOrderId;
                        $orderPayments->PagamentoId = 0;
                        $orderPayments->NumeroParcelas = '1';
                        $orderPayments->ValorParcelas = (float) $order->OrderTotal->Amount ;
                        $orderPayments->ValorTotal = (float) $order->OrderTotal->Amount;//$orderPayment->total_paid_amount;
                        $orderPayments->Desconto =  (float) $promotionDiscount;
                        $orderPayments->FormaPagamento = $order->PaymentMethod;
                        $orderPayments->Metodo = $order->PaymentMethodDetails->PaymentMethodDetail;
                        $orderPayments->DataAutorizacao = date("Y-m-d H:i:s", strtotime($order->PurchaseDate));
                        $orderPayments->Situacao = $order->OrderStatus;
                        $orderPayments->MarketplaceTaxa = (float) ($order->OrderTotal->Amount * 0.11 );
                        $orderPayments->Marketplace = "Amazon";
//                         pre($orderPayments);
                        echo $orderPaymentsId = $orderPayments->Save();
                        echo "-------Payments-----------<br>";
                                
                    }
//                     die;,
                }
                
            }
          
            break;
            
    }
}
            
   