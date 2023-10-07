<?php
ini_set ("display_errors", true);
set_time_limit ( 300 );
header("Content-Type: text/html; charset=utf-8");

$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Models/Customers/ManageCustomersModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Tray.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Api/OrdersRestModel.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/../../../Models/Orders/OrderItemsModel.php';
require_once $path .'/../../../Models/Orders/OrderItemAttributesModel.php';
require_once $path .'/../../../Models/Orders/OrderPaymentsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
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
    
    $request = "System";
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
    
    switch($action){
        
        case "import_order":
        $syncId =  logSyncStart($db, $storeId, "Tray", $action, "Importação de pedidos.", $request);
        $totalOrder = 0;
        $ordersCount = 0;
        $dateFrom =  date("Y-m-d", strtotime("-5 day", strtotime("now")))." 00:00:00";
        $dateTo = date("Y-m-d")." 23:59:59";
        
        $ordersRestModel = new OrdersRestModel($db, null, $storeId);
        $orders = $ordersRestModel->getOrders();

        if(isset($orders['body']->Orders)){
            
            foreach($orders['body']->Orders as $key => $orderObj){
                
                $ordersRestModel->id = $orderObj->Order->id;
                
                $completeOrder = $ordersRestModel->getCompleteOrder();
                
                $order  = $completeOrder['body']->Order;
//                 pre($order);
//                 echo  $order->id. "<br>";
                 
                $customer = $order->Customer;
                $customerModel = new  ManageCustomersModel($db);
                $customerModel->store_id = $storeId;
                $customerModel->Codigo = $customer->id;
                $customerModel->TipoPessoa = !empty($customer->cnpj) ? 2 : 1 ;
                $customerModel->Nome = $customer->name;
                $customerModel->Apelido = $customer->nickname;
                $customerModel->Email = $customer->email;
                $customerModel->CPFCNPJ = !empty($customer->cnpj) ? $customer->cnpj : $customer->cpf ;
                $customerModel->Telefone = isset($customer->cellphone) ? $customer->cellphone : "";
                $customerModel->TelefoneAlternativo = isset($customer->phone) ? $customer->phone : "";
                $customerModel->DataCriacao = $customer->created;
                $customerModel->Endereco = $customer->address;
                $customerModel->Numero = $customer->number;
                $customerModel->Bairro = $customer->neighborhood;
                $customerModel->Complemento = $customer->complement;
                $customerModel->Cidade = $customer->city;
                $customerModel->Estado = $customer->state;
                $customerModel->CEP = $customer->zip_code;
                $customerModel->Marketplace = "Tray";
                $customerId = $customerModel->Save();
                
//                 echo "-------Customer-----------<br>";
//                 pre($customerId);
                
                if(isset($customerId)){
                    
                    $ordersModel = new OrdersModel($db);
                    $ordersModel->store_id = $storeId;
                    $ordersModel->customer_id = $customerId;
                    $ordersModel->PedidoId = $order->id;
                    $ordersModel->Nome = $customer->name;
                    $ordersModel->NomeDestino  =  $customer->name;
                    
                    $ordersModel->Email = $customer->email;
                    $ordersModel->Telefone = isset($customer->cellphone) ? $customer->cellphone : "";
                    
                    $receiverAddress = '';
                    foreach($customer->CustomerAddresses as $key => $addresses){
                        if($addresses->CustomerAddress->type_delivery == 1){
                            $receiverAddress = $addresses->CustomerAddress;
                            break;
                        }
                        
                    }
                    
                    if(!empty($receiverAddress)){
                        $ordersModel->Endereco = $receiverAddress->address;
                        $ordersModel->Bairro = $receiverAddress->neighborhood;
                        $ordersModel->Complemento = $receiverAddress->complement;
                        $ordersModel->Numero = $receiverAddress->number;
                        $ordersModel->Cidade = $receiverAddress->city;
                        $ordersModel->Estado = trim($receiverAddress->state);
                        $ordersModel->Cep = $receiverAddress->zip_code;
                    }
                    
                    $ordersModel->DataPedido = $order->date." ".$order->hour;
                    $ordersModel->FormaPagamento = $order->payment_method;
                    $ordersModel->Parcelas = $order->installment;
                    $ordersModel->ValorFrete = $order->shipment_value;
                    
                    $ordersModel->Subtotal = $order->partial_total;
                    
                    $ordersModel->Canal = $order->point_sale;
                    
                    $ordersModel->ValorPedido = $order->total;
                    $ordersModel->ValorCupomDesconto =  $order->discount;
                    $ordersModel->MarketplaceTaxa = $order->taxes;
                    $ordersModel->Obs = $order->store_note;
                    
                    
                    $ordersModel->Status = convertStatusTray($order->status);
                    $ordersModel->Marketplace = 'Tray';
//                     pre($ordersModel);
                    $orderId =  $ordersModel->Save();
                    
//         	        echo "-------OrderId-----------<br>";
//         	        pre($orderId);
                    
                }
                if(isset($orderId)){
                    
                    $items = $order->ProductsSold;
                    foreach ($items as $keyItem => $item){
                        
                        $orderItem = $item->ProductsSold;
//                         pre($orderItem->product_id);die;
                        if(isset($orderItem->product_id)){
                        	$sqlAP = "SELECT available_products.sku FROM available_products
                        	RIGHT JOIN module_tray_products
                        	ON module_tray_products.product_id = available_products.id
                        	AND module_tray_products.id_product = {$orderItem->product_id}
                        	WHERE available_products.store_id = {$storeId}";
                        }
                        
                        if(!empty($orderItem->variant_id)){
	                        $sqlAP = "SELECT available_products.sku FROM available_products
	                        RIGHT JOIN module_tray_products_variations 
	                        ON module_tray_products_variations.product_id = available_products.id
	                        AND module_tray_products_variations.variation_id = {$orderItem->variant_id}
	                        WHERE available_products.store_id = {$storeId}";
                        }
                        $queryAP = $db->query($sqlAP);
                        $availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
                        $sku = isset($availableProduct['sku']) ? $availableProduct['sku'] : '';
                        if(!empty($sku)){
                            $imageProductSold = isset($orderItem->ProductSoldImage[0]->https) ? $orderItem->ProductSoldImage[0]->https : getUrlImageFromSku($db, $storeId, $sku);
                        }
                        $orderItems = new OrderItemsModel($db);
                        $orderItems->store_id = $storeId;
                        $orderItems->order_id = $orderId;
                        $orderItems->PedidoId = $order->id;
                        $orderItems->PedidoItemId = $orderItem->id;
                        $orderItems->SKU = $sku;
                        $orderItems->Nome = $orderItem->name;
                        $orderItems->Quantidade = $orderItem->quantity;
                        $orderItems->PrecoUnitario = $orderItem->price;
                        $orderItems->PrecoVenda = $orderItem->price;
                        $orderItems->TaxaVenda = isset($orderItem->comissao) ? $orderItem->comissao : '' ;
                        $orderItems->UrlImagem = isset($imageProductSold) ? $imageProductSold : '';
                        $orderItems->Marketplace = 'Tray';
                        $orderItemId =  $orderItems->Save();
//     	                echo "-------Item id-----------<br>";
//     	                pre($orderItemId);
                        
                        if(isset($orderItem->Sku) AND isset($orderItemId)){
                            
                            foreach ($orderItem->Sku as $keyVA => $orderItemAttribute){
                                
                                $orderItemAttributes = new OrderItemAttributesModel($db);
                                $orderItemAttributes->store_id = $storeId;
                                $orderItemAttributes->order_id = $orderId;
                                $orderItemAttributes->item_id = $orderItemId;
                                $orderItemAttributes->PedidoId = $order->id;
                                $orderItemAttributes->PedidoItemId = $orderItem->id;
                                $orderItemAttributes->Nome = strip_tags($orderItemAttribute->type);
                                $orderItemAttributes->Valor = $orderItemAttribute->value;
                                $orderItemAttributeId = $orderItemAttributes->Save();
//         	                    echo "-------Item Attr-----------<br>";
//         	                    pre($orderItemAttributeId);
                                
                            }
                        }
                    }
                }
                if(isset($orderId)){
                    if(isset($order->Payment)){
                        foreach ($order->Payment as $keyP => $payment){
                            $orderPayment = $payment->Payment;
                            
                            $orderPayments = new OrderPaymentsModel($db);
                            $orderPayments->store_id = $storeId;
                            $orderPayments->order_id = $orderId;
                            $orderPayments->PedidoId = $order->id;
                            $orderPayments->PagamentoId = $orderPayment->id;
    //                         $orderPayments->NumeroParcelas = $order->installments;
    //                         $orderPayments->ValorParcela = $orderPayment->installment_amount;
                            $orderPayments->ValorTotal = $orderPayment->value;
    //                         $orderPayments->Desconto = $orderPayment->coupon_amount;
                            $orderPayments->FormaPagamento = $order->payment_method;
                            $orderPayments->Metodo = $order->payment_method_type;
                            $orderPayments->NumeroAutorizacao = $orderPayment->note;
                            $orderPayments->DataAutorizacao = $orderPayment->created;
                            $orderPayments->Situacao = convertStatusTray($order->status);
                            $orderPayments->MarketplaceTaxa = isset($order->total_comission) ? $order->total_comission : '0.00';
                            $orderPayments->Marketplace = "Tray";
                            $orderPaymentsId = $orderPayments->Save();
                            
//         	                echo "-------Payments-----------<br>";
//         	                pre($orderPaymentsId);
                            
                            
                        }
                    }
                }
                $ordersCount++;
                
            }
        }
//         else{
//             pre($orders);
//         }
        logSyncEnd($db, $syncId, $totalOrder."/".$ordersCount);
        
        break;
        
    }
    
}
