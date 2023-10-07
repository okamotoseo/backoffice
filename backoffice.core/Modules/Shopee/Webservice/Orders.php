<?php
// die;
// echo phpinfo();die;
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
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
require_once $path .'/SimpleXLSX.php';

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
        
        case "import_order_xlsx":
            
            
//             [0] => ID do pedido
//             [1] => Status do pedido
//             [2] => Cancelar Motivo
//             [3] => Status da Devolução / Reembolso
//             [4] => Número de rastreamento
//             [5] => Opção de envio
//             [6] => Método de envio
//             [7] => Data prevista de envio
//             [8] => Tempo de Envio
//             [9] => Data de criação do pedido
//             [10] => Hora do pagamento do pedido
//             [11] => Nº de referência do SKU principal
//             [12] => Nome do Produto
//             [13] => Número de referência SKU
//             [14] => Nome da variação
//             [15] => Preço original
//             [16] => Preço acordado
//             [17] => Quantidade
//             [18] => Subtotal do produto
//             [19] => Desconto do vendedor
//             [20] => Desconto do vendedor
//             [21] => Reembolso Shopee
//             [22] => Peso total SKU
//             [23] => Número de produtos pedidos
//             [24] => Peso total do pedido
//             [25] => Código do Cupom
//             [26] => Cupom do vendedor
//             [27] => Seller Absorbed Coin Cashback
//             [28] => Cupom Shopee
//             [29] => Indicador do Leve Mais por Menos
//             [30] => Desconto Shopee do "Leve mais por Menos"
//             [31] => Desconto do "Leve mais por Menos"do vendedor
//             [32] => Compensar Moedas Shopee
//             [33] => Total descontado Cartão de Crédito
//             [34] => Valor Total
//             [35] => Taxa de envio pagas pelo comprador
//             [36] => Taxa de transação
//             [37] => Taxa de comissão
//             [38] => Taxa de serviço
//             [39] => Total global
//             [40] => Valor estimado do frete
//             [41] => Nome de usuário (comprador)
//             [42] => Nome do destinatário
//             [43] => Telefone
//             [44] => CPF do Comprador
//             [45] => Endereço de entrega
//             [46] => Cidade
//             [47] => Bairro
//             [48] => Cidade
//             [49] => UF
//             [50] => País
//             [51] => CEP
//             [52] => Observação do comprador
//             [53] => Hora completa do pedido
//             [54] => Nota

/***************************************************************************************************************************/
/***************************************************************************************************************************/
/***************************************************************************************************************************/
            
//             [0] => 210708CQFYR231
//             [1] => A Enviar
//             [2] =>
//             [3] =>
//             [4] => QF815581309BR
//             [5] => Correios
//             [6] => Postagem
//             [7] => 2021-07-15 08:02
//             [8] =>
//             [9] => 2021-07-08 07:59
//             [10] => 2021-07-08 08:02
//             [11] =>
//             [12] => Torneira Eletrica Zagonel Agile 4500w 127v Branca
//             [13] => 895
//             [14] => Branca,220v
//             [15] => 78.90
//             [16] => 78.90
//             [17] => 1
//             [18] => 78.90
//             [19] => 0.00
//             [20] => 0.00
//             [21] => 0.00
//             [22] => 1.000
//             [23] => 1
//             [24] => 1.000
//             [25] => FSV-84831389
//             [26] => 0.00
//             [27] => 0.00
//             [28] => 0.00
//             [29] => N
//             [30] => 0.00
//             [31] => 0.00
//             [32] => 0
//             [33] => 0.00
//             [34] => 78.90
//             [35] => 0.00
//             [36] => 0.00
//             [37] => 9.47
//             [38] => 4.73
//             [39] => 78.90
//             [40] => 23.05
//             [41] => cris6750
//             [42] => Cristiane Dellani Castanha
//             [43] => 5549999295805
//             [44] => 07371260945
//             [45] => R Mal Deodoro da Fonseca - E, 400, Sala 1006. Edificio Piemonte, Chapecó, Santa Catarina, 89802140
//             [46] =>
//             [47] => Centro
//             [48] => Chapecó
//             [49] => Santa Catarina
//             [50] => BR
//             [51] => 89802140
//             [52] =>
//             [53] =>
//             [54] => 
            
            if ( $xlsx = SimpleXLSX::parse('/var/www/html/app_mvc/Views/_uploads/store_id_4/xlsx/Order.all.20210708_20210708.xlsx') ) {
                
                $orders = $xlsx->rows();
                
                if(!empty($orders)){
                    
                    foreach($orders as $k => $order){
                        
                        if($k > 0){
                       
                            $customerModel = new  ManageCustomersModel($db);
                            $customerModel->store_id = $storeId;
                            $customerModel->Codigo = $order[0];
                            $customerModel->TipoPessoa = !empty($order[44]) ? 1 : 2 ;
                            $customerModel->Nome = $order[42];
                            $customerModel->Apelido = $order[41];
                            $customerModel->Email = $customer->email;
                            $customerModel->CPFCNPJ = !empty($order[44]) ? $order[44] : $order[44]... ;
                            $customerModel->Telefone = $order[43];
                            $customerModel->TelefoneAlternativo = isset($customer->phone) ? $customer->phone : "";
                            $customerModel->DataCriacao = $customer->created;
                            $customerModel->Endereco = $customer->address;
                            $customerModel->Numero = $customer->number;
                            $customerModel->Bairro = $order[47];
                            $customerModel->Complemento = $customer->complement;
                            $customerModel->Cidade = $order[48];
                            $customerModel->Estado = $order[49];
                            $customerModel->CEP = $order[51];
                            $customerModel->Marketplace = "Tray";
                            $customerId = $customerModel->Save();
                            
                            echo "-------Customer-----------<br>";
                            pre($customerId);
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                        }
                    
                    }
                
                }
            
            }
           
            
            
            break;
        
        case "import_order":
           
        $syncId =  logSyncStart($db, $storeId, "Tray", $action, "Importação de pedidos.", $request);
        $totalOrder = 0;
        $ordersCount = 0;
        $dateFrom =  date("Y-m-d", strtotime("-1 day", strtotime("now")))." 00:00:00";
        $dateTo = date("Y-m-d")." 23:59:59";
        
        $ordersRestModel = new OrdersRestModel($db, null, $storeId);
       
        $orders = $ordersRestModel->getOrders();

        if(isset($orders['body']->Orders)){
            
            foreach($orders['body']->Orders as $key => $orderObj){
                
                $ordersRestModel->id = $orderObj->Order->id;
                
                $completeOrder = $ordersRestModel->getCompleteOrder();
                
                $order  = $completeOrder['body']->Order;
                
                echo  $order->id. "<br>";
                
                $customer = $order->Customer;
                $customerModel = new  ManageCustomersModel($db);
                $customerModel->store_id = $storeId;
                $customerModel->Codigo = $customer->id;
                $customerModel->TipoPessoa = !empty($customer->cnpj) ? 2 : 1 ;
                $customerModel->Nome = $customer->name;
                $customerModel->Apelido = $customer->nickname;
                $customerModel->Email = $customer->email;
                $customerModel->CPFCNPJ = !empty($customer->cpf) ? $customer->cpf : $customer->cnpj ;
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
                
                echo "-------Customer-----------<br>";
                pre($customerId);
                
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
                    
                    $MlOrder = isset($order->MlOrder[0]) ?  $order->MlOrder[0]->MlOrder : '' ;
                    if(!empty($MlOrder)){
                        
                        $ordersModel->Canal = $MlOrder->nickname;
                    
                    }
                    
                    $ordersModel->ValorPedido = $order->total;
                    $ordersModel->ValorCupomDesconto =  $order->discount;
                    $ordersModel->MarketplaceTaxa = $order->taxes;
                    $ordersModel->Obs = $order->store_note;
                    
                    
                    $ordersModel->Status = convertStatusTray($order->status);
                    $ordersModel->Marketplace = 'Tray';
    //                 pre($ordersModel);die;
                    $orderId =  $ordersModel->Save();
                    
        	        echo "-------OrderId-----------<br>";
        	        pre($orderId);
                    
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
    	                echo "-------Item id-----------<br>";
    	                pre($orderItemId);
                        
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
        	                    echo "-------Item Attr-----------<br>";
        	                    pre($orderItemAttributeId);
                                
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
                            
        	                echo "-------Payments-----------<br>";
        	                pre($orderPaymentsId);
                            
                            
                        }
                    }
                }
                $ordersCount++;
                
            }
        }else{
            pre($orders);
        }
        logSyncEnd($db, $syncId, $totalOrder."/".$ordersCount);
        break;
        
    }
    
}

