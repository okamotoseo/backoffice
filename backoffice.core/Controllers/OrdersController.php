<?php

class OrdersController extends MainController
{

	/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = true;
	
	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required = 'any';
	
	/**
	 * $panel
	 *
	 * Painel de controle
	 *
	 * @access public
	 */
	public $panel = 'Pedidos';
	
	
	public function Returns(){
		 
		$this->title = 'Trocas e Devoluções';
		 
		$this->menu = array("Orders" => "active", "Returns" => "active");
		 
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		
		require ABSPATH . '/Views/_includes/header.php';
		
		$returnsModel = $this->load_model('Orders/ReturnsModel');
		
		if($returnsModel->ValidateForm()){
			
			$totalReg = $returnsModel->TotalOrderReturns();
		
			$list = $returnsModel->GetOrderReturns();
		
		}else{
			
			$totalReg = $returnsModel->TotalOrderReturns();
			
			$list = $returnsModel->ListOrderReturns();
			
		}
	
	
		require ABSPATH . '/Views/Orders/ReturnsView.php';
		
		require ABSPATH . '/Views/Orders/ReturnFormView.php';
	
		require ABSPATH . '/Views/_includes/footer.php';
	
	}
	
    public function Manage(){
    	
    	$this->title = 'Pedidos';
    	
    	$this->menu = array("Orders" => "active", "Manage" => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        require ABSPATH . '/Views/_includes/header.php';
        
        $ordersModel = $this->load_model('Orders/ManageOrdersModel');
        
        if($ordersModel->ValidateForm()){

            $list = $ordersModel->GetOrderDetails();
            
        }else{
            
            $list = $ordersModel->ListOrderDetails();
        }
        
        $totalReg = $ordersModel->TotalOrders();
        
        $statusOrder = $ordersModel->listStatusOrders();
        
        $paymentsOrder = $ordersModel->listPaymentsOrders();
        
        $ufOrder = $ordersModel->listUfOrders();
        
        $marketplaceOrder = $ordersModel->listMarketplaceOrders();
        
        
        
        require ABSPATH . '/Views/Orders/ManageOrdersView.php';
        
        require ABSPATH . '/Views/Orders/OccurrenceFormView.php';
        
        require ABSPATH . '/Views/Orders/ReturnFormView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    
    
    public function RegisterOrder(){
        
        $this->title = 'Incluir pedido';
        
        $this->menu = array("Orders" => "active", "RegisterOrder" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        require ABSPATH . '/Views/_includes/header.php';
        
        $customerModel = $this->load_model('Customers/ManageCustomersModel');
        
        $ordersModel = $this->load_model('Orders/OrdersModel');

        $orderItemsModel = $this->load_model('Orders/OrderItemsModel');
        
        $orderItemAttributesModel = $this->load_model('Orders/OrderItemAttributesModel');
        
        $orderPaymentsModel = $this->load_model('Orders/OrderPaymentsModel');
        
        if($customerModel->ValidateForm()){
            $customerId = $customerModel->Save();
        }
        
        if($ordersModel->ValidateForm()){
            $ordersModel->customer_id = $customerId;
            $ordersModel->Cep = $customerModel->CEP;
            $ordersModel->PedidoId = rand();
            $ordersModel->NomeDestino = $customerModel->Nome;
            $ordersModel->Status = 'Pending';
            $ordersModel->ValorParcelas = $ordersModel->ValorPedido;
            $ordersModel->ValorDesconto = $ordersModel->ValorCupomDesconto;
            $ordersModel->Canal = 'Sysplace';
//             $ordersModel->Marketplace = 'Televendas';

            $ordersModel->DataPedido = date("Y-m-d H:i:s");
            
            if(isset($customerId)){
            	
                $orderId = $ordersModel->Save();
               
            }
        }
        
        if($orderItemsModel->ValidateForm()){
        	$items = array();
            $orderItemsModel->order_id = $orderId;
            $orderItemsModel->PedidoId = $ordersModel->PedidoId;
            $orderItemsModel->DataPedido = $ordersModel->DataPedido;
            if(isset($orderItemsModel->orderItems)){
            	
                foreach($orderItemsModel->orderItems as $key => $item){
                	$items[] = $item;
                    $orderItemsModel->PedidoItemId = $item['id'];
                    $orderItemsModel->SKU = $item['sku'];
                    $orderItemsModel->Quantidade = $item['Quantidade'];
                    $orderItemsModel->TaxaVenda = 0;
                    $orderItemsModel->Nome = $item['title'];
                    $orderItemsModel->PrecoVenda = $item['PrecoVenda'];
                    $orderItemsModel->PrecoUnitario = $item['PrecoVenda'];
                    $orderItemsModel->UrlImagem = !empty($item['image']) ? $item['image'] : null ;
                    $orderItemId = $orderItemsModel->Save();
                    
                    
                    if(!empty($orderItemId)){
                        if(isset($item['item_attributes'])){
                            foreach ($item['item_attributes'] as $attribute => $attributeValue){
                                $orderItemAttributesModel->order_id = $orderId;
                                $orderItemAttributesModel->item_id = $orderItemId;
                                $orderItemAttributesModel->PedidoId = $ordersModel->PedidoId;
                                $orderItemAttributesModel->PedidoItemId = $item['id'];
                                $orderItemAttributesModel->Nome = $attribute;
                                $orderItemAttributesModel->Valor = $attributeValue;
                                $orderItemAttributeId = $orderItemAttributesModel->Save();
                            }
                        }
                    }
                }
            }
        }
        
        
        if($orderPaymentsModel->ValidateForm()){
            $orderPaymentsModel->order_id = $orderId;
            $orderPaymentsModel->PedidoId = $ordersModel->PedidoId;
            $orderPaymentsModel->PagamentoId = 1;
            $orderPaymentsModel->Situacao = 'Pending';
            $orderPaymentsModel->NumeroParcelas = '1';
            $orderPaymentsModel->Metodo = 'Outros';
            $orderPaymentsModel->ValorParcelas = $ordersModel->ValorPedido;
            $orderPaymentsModel->ValorDesconto = $ordersModel->ValorCupomDesconto;
            $orderPaymentsModel->ValorTotal = $ordersModel->ValorPedido;
            $orderPaymentsModel->Marketplace = 'Sysplace';
            $orderPaymentsModel->MarketplaceTaxa = '0';
            $orderPaymentsId = $orderPaymentsModel->Save();
        }
        
       
//         if(isset($items) &&  $ordersModel->FormaPagamento == 'Mercadopago'){
        		
//         	require  ABSPATH.'/vendor/autoload.php';
        		
//         	MercadoPago\SDK::setAccessToken('TEST-524940906731972-102506-44ca4b3454c346815aa08c7c37367893-260984855');
        		
//         	$preference = new MercadoPago\Preference();
        	
//         	$payerMP = new MercadoPago\Payer();
// 	        $payerMP->name = 'João';
// 	        $payerMP->surname= 'Silva';
// 	        $payerMP->email= 'user@email.com';
// 	        $payerMP->phone = array(
// 	            'area_code' =>'11',
// 	            'number' => '4444-4444'
// 	        );
// 	        $payerMP->identification = array(
// 	            'type' => 'CPF',
// 	            'number' => '19119119100'
// 	        );
// 	        $payerMP->address = array(
// 	            'street_name' => 'Street',
// 	            'street_number' => 123,
// 	            'zip_code' => '06233200'
// 	        );
// 	        $preference->payer = $payerMP;
//         	foreach($items as $i => $item){
//         		$itemMP = new MercadoPago\Item();
//         		$itemMP->id = $item['sku'];
//         		$itemMP->title = $item['title'];
//         		$itemMP->quantity = $item['Quantidade'];
//         		$itemMP->unit_price = $item['sale_price'];
//         		$itemsMP[] = $itemMP;
        
//         	}
//         	$preference->items = $itemsMP;
//         	$preference->save();
        		
//         }
        
        
        
        
        require ABSPATH . '/Views/Orders/RegisterOrderView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    
    public function OrderDetail(){ 
        
        $this->title = 'Informações Detalhadas do Pedido';
        
        $this->menu = array("Orders" => "active", "Manage" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        require ABSPATH . '/Views/_includes/header_popup.php';
        
        $ordersModel = $this->load_model('Orders/ManageOrdersModel');
        
        $storeModel = $this->load_model('Admin/StoreModel');

        $key = array_search('id', $this->parametros);
        
        if(!empty($key)){
            
            $ordersModel->id = get_next($this->parametros, $key);
            
            $ordersModel->pagina_atual = 1;
            
            $ordersModel->linha_inicial = 0;
            
            $order = $ordersModel->GetOrderDetails();
            
            $order = $order[0];
            
            $storeModel->id = $order['store_id'];
            
            $storeModel->Load();
        }
        
        $this->includes = array("js" => "/Views/js/orderDetail.js");
        
        
        
        require ABSPATH . '/Views/Orders/OrderDetailView.php';
        
        require ABSPATH . '/Views/_includes/footer_popup.php';
        
    }
    
    
    
    public function OrderContentDeclaration(){
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/Interfaces/ItemBagInterface.php';
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/Interfaces/ItemInterface.php';
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/Interfaces/PessoaInterface.php';
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/Core/Controller.php';
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/Core/Entity.php';
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/Core/ItemBag.php';
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/Entities/Item.php';
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/Entities/Pessoa.php';
        
        require ABSPATH . '/library/mozgbrasil/declara_conteudo/src/DeclaracaoConteudo.php';
        
        $this->title = 'Informações Detalhadas do Pedido';
        
        $this->menu = array("Orders" => "active", "Manage" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $ordersModel = $this->load_model('Orders/ManageOrdersModel');
        
        $storeModel = $this->load_model('Admin/StoreModel');
        
        $key = array_search('id', $this->parametros);
        
        if(!empty($key)){
            
            $ordersModel->id = get_next($this->parametros, $key);
            
            $ordersModel->pagina_atual = 1;
            
            $ordersModel->linha_inicial = 0;
            
            $order = $ordersModel->GetOrderDetails();
            $order = $order[0];
            
            $storeModel->id = $order['store_id'];
            
            $storeModel->Load();
            
            $remetente = new Click4Web\DeclaracaoConteudo\Entities\Pessoa([
                'nome' =>  $storeModel->store,
                'doc' =>  $storeModel->cnpj,
                'endereco' =>  $storeModel->address.", ".$storeModel->number,
                'cidade' => $storeModel->city,
                'estado' => $storeModel->state, 
                'cep' => $storeModel->postalcode,
                'pedido' => $order['PedidoId']
            ]);
            
             
            $destinatario = new Click4Web\DeclaracaoConteudo\Entities\Pessoa();
            
            
            $destinatario->setNome($order['Nome'])
                ->setDoc($order['CPFCNPJ'])
                ->setEndereco($order['Endereco'].", ".$order['Numero'])
                ->setCidade($order['Cidade'])
                ->setEstado($order['Estado'])
                ->setCep($order['Cep']);
            
            $total = 0;  
            
            $orderItens = [];
            
            foreach($order['items'] as $keyItem => $rowItem){
                
                $precoUnitario = number_format($rowItem['PrecoUnitario'], 2, '.', '');
                
                $precoVenda = number_format($rowItem['PrecoVenda'] * $rowItem['Quantidade'], 2, '.', '');
                $orderItens[] = [
                        'descricao' => $rowItem['Nome'],
                        'quantidade' => $rowItem['Quantidade'],
                        'valor' => $precoVenda,
                        'peso' => $rowItem['peso'] 
                    ];
                
                $total += $precoVenda;
            }
            
            $itens = new \Click4Web\DeclaracaoConteudo\Core\ItemBag($orderItens);
            
            $declaracao = new \Click4Web\DeclaracaoConteudo\DeclaracaoConteudo(
                $remetente,
                $destinatario,
                $itens,
                $total // Valor Total (R$)
            );
            
        }
        
        require ABSPATH . '/Views/Orders/ContentDeclaration.php';
        
        
    }
    
}
