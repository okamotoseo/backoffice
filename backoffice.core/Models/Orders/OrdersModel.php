<?php 

class OrdersModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
    public $customer_id;
    
    public $PedidoId;
    
    public $DataPedido;
    
    public $Ip;
    
    Public $Nome;
    
    Public $CPFCNPJ;
    
    public $RG;
    
    Public $Email;
    
    Public $Telefone;
    
    public $Cep;
    
    public $Endereco;
    
    public $Numero;
    
    public $Bairro;
    
    public $Cidade;
    
    public $Estado;
    
    public $Complemento;
    
    public $NomeDestino;
    
    public $Telefone_entrega;
    
    public $Endereco_entrega;
    
    public $Numero_entrega;
    
    public $Cep_entrega;
    
    public $Bairro_entrega;
    
    public $Cidade_entrega;
    
    public $Estado_entrega;
    
    public $Complemento_entrega;
    
    public $DataPedidoAte;
    
    public $FormaPagamento;
    
    public $Parcelas;
    
    public $PrazoEnvio;
    
    public $Status;
    
    public $Subtotal;
    
    public $ValorFrete;
    
    public $ValorParcelas;
    
    public $ValorPedido;
    
    public $ValorPedidoAte;
    
    public $ValorCupomDesconto;
    
    public $AnaliseFraude;
    
    public $Canal;
    
    public $Marketplace;
    
    public $orders_pack = array();
    
    public $Obs;
    
    public $id_nota_saida;
    
    public $shipping_id;
    
    public $logistic_type;
    

    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
        }
    }
    
    public function getOrdersPack(){
        
        if(empty($this->orders_pack)){
            
            return null;
        }
        return json_encode($this->orders_pack);
        
    }
    
    public function setOrdersPack($order){
       
        if(!empty($order)){
            
            $query = $this->db->query("SELECT orders_pack FROM orders
            WHERE store_id = {$this->store_id} AND PedidoId LIKE '{$this->PedidoId}'");
            $res = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($res['orders_pack'])){
                
                $this->orders_pack = json_decode($res['orders_pack']);
            }
            if(!in_array($order, $this->orders_pack)){
                
                $this->orders_pack[] = $order;
                
            }
           
        
        }
        
    }
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                }else{
                    $req = array();
                    
                    if( in_array($property, $req) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
            
            if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
                $this->Load();
                
            }
            
            if ( chk_array( $this->parametros, 2 ) == 'del' ) {
                
                $this->Delete();
                
            }
            
            return;
            
        }
        
    }
    
    
    public function getEnabledStatusEditable($status){
       
        $enabledStatusEdit = array();
        
        switch($status){
            case "paid": $enabledStatusEdit = array('paid','cancelled', 'canceled', 'invoiced', 'shipped', 'order_shipped', 'delivered'); break;
            case "picking": $enabledStatusEdit = array('paid','cancelled', 'canceled', 'invoiced', 'shipped', 'order_shipped', 'delivered'); break;
            case "approved": $enabledStatusEdit = array('approved', 'cancelled', 'canceled', 'waiting_invoice', 'invoiced', 'invoice_error', 'ready_to_ship', 'shipped', 'delivered'); break;
            case "em monitoramento": $enabledStatusEdit = array('cancelled', 'canceled', 'invoiced','paid', 'approved', 'ready_to_ship', 'shipped'); break;
            case "pagamento pendente": $enabledStatusEdit = array('cancelled', 'canceled', 'invoiced', 'waiting_invoice', 'paid', 'approved','ready_to_ship', 'shipped'); break;
            case "aguardando confirmacao do pagamento": $enabledStatusEdit = array('cancelled', 'canceled', 'invoiced', 'paid', 'approved','ready_to_ship', 'shipped'); break;
            case "pendente": $enabledStatusEdit = array('cancelled', 'canceled', 'invoiced', 'waiting_invoice', 'paid', 'approved','ready_to_ship', 'shipped'); break;
            case "unshipped": $enabledStatusEdit = array('cancelled', 'canceled','invoiced', 'processing', 'shipped', 'order_shipped', 'ready_to_ship', 'delivered'); break;
            case "ready_to_ship": $enabledStatusEdit = array('cancelled', 'canceled','invoiced', 'shipped', 'delivered'); break;
            case "processing": $enabledStatusEdit = array('cancelled', 'canceled','invoiced', 'shipped', 'waiting_invoice', 'order_shipped', 'ready_to_ship', 'delivered'); break;
            case "new": $enabledStatusEdit = array('new', 'cancelled', 'canceled', 'invoiced', 'waiting_invoice', 'paid', 'approved', 'processing', 'ready_to_ship'); break;
            case "pending": $enabledStatusEdit = array('cancelled', 'canceled', 'paid', 'approved', 'waiting_invoice', 'processing', 'invoice_error', 'ready_to_ship', 'shipped','delivered'); break;
            case "pending_payment": $enabledStatusEdit = array('cancelled', 'canceled', 'invoiced', 'waiting_invoice', 'paid', 'approved', 'processing', 'ready_to_ship'); break;
            case "waiting_payment": $enabledStatusEdit = array('cancelled', 'canceled', 'invoiced', 'waiting_invoice', 'paid', 'approved', 'processing', 'ready_to_ship'); break;
            case 'unshipped': $enabledStatusEdit = array('cancelled', 'canceled', 'paid', 'approved','invoiced', 'waiting_invoice', 'processing', 'ready_to_ship', 'shipped', 'delivered'); break;
            case "waiting_invoice": $enabledStatusEdit = array('cancelled', 'canceled', 'approved', 'invoiced', 'waiting_invoice', 'shipped', 'delivered', 'shipping_informed'); break;
            case "under_review": $enabledStatusEdit = array('cancelled', 'canceled', 'approved', 'invoiced', 'waiting_invoice', 'invoiced', 'shipped', 'delivered', 'shipping_informed'); break;
            case "invoice_error": $enabledStatusEdit = array('cancelled', 'canceled', 'approved', 'invoiced', 'waiting_invoice', 'shipped', 'delivered', 'shipping_informed'); break;
            case "invoiced": $enabledStatusEdit = array('cancelled', 'canceled', 'shipped', 'order_shipped', 'waiting_invoice', 'delivered', 'shipping_informed'); break;
            case "shipping_informed": $enabledStatusEdit = array('cancelled', 'canceled','delivered', 'shipped', 'waiting_invoice','refound'); break;
            case "shipping_error": $enabledStatusEdit = array('cancelled', 'canceled', 'invoiced', 'waiting_invoice', 'shipped', 'delivered', 'shipping_informed'); break;
            case "shipped": $enabledStatusEdit = array('cancelled', 'canceled','delivered', 'refound'); break;
            case "delivered": $enabledStatusEdit = array('cancelled', 'canceled'); break;
            default: $enabledStatusEdit = array(); break;
        }
        
        return $enabledStatusEdit;
    }
    
	public function Save(){
	    $sql = "SELECT id, Status, Marketplace FROM orders
            WHERE store_id = {$this->store_id} AND PedidoId LIKE '{$this->PedidoId}'";
	    $query = $this->db->query($sql);
        $res = $query->fetch(PDO::FETCH_ASSOC);
           	 
        if ( ! empty( $res['id'] ) ) {
           	
	        if(empty($this->Status)){
	        	return array();
	        } 
       		$verifyStatus = isset($res['Status']) ? strtolower($res['Status']) : '' ;
       		
       		$enabledStatusEdit = $this->getEnabledStatusEditable($verifyStatus);
       		
       		if(empty($verifyStatus) OR in_array($this->Status, $enabledStatusEdit)) {
                $query = $this->db->update('orders', 'id', $res['id'], array(
                    'store_id'  => $this->store_id,
                    'customer_id'  => $this->customer_id,
                    'PedidoId'  => $this->PedidoId,
                	'DataPedido'  => $this->DataPedido,
                	'Ip'  => $this->Ip,
                		
                    'Nome'  => $this->Nome,
                	'CPFCNPJ'  => $this->CPFCNPJ,
                	'RG'  => $this->RG,
                    'Email'  => $this->Email,
                    'Telefone'  => $this->Telefone,
                    'Cep'  => $this->Cep,
                    'Endereco'  => $this->Endereco,
                	'Numero'  => $this->Numero,
                	'Bairro'  => $this->Bairro,
                	'Cidade'  => $this->Cidade,
                    'Estado'  => $this->Estado,
                	'Complemento'  => $this->Complemento,
                    
                    'NomeDestino'  => $this->NomeDestino,
                	'Telefone_entrega'  => $this->Telefone_entrega,
                	'Cep_entrega'  => $this->Cep_entrega,
                	'Endereco_entrega'  => $this->Endereco_entrega,
                	'Numero_entrega'  => $this->Numero_entrega,
                	'Bairro_entrega'  => $this->Bairro_entrega,
                	'Cidade_entrega'  => $this->Cidade_entrega,
                	'Estado_entrega'  => $this->Estado_entrega,
                	'Complemento_entrega'  => $this->Complemento_entrega,
                    
                    'FormaPagamento'  => $this->FormaPagamento,
                    'Parcelas'  => $this->Parcelas,
                    'PrazoEnvio'  => $this->PrazoEnvio,
                    
                    'Status'  => strtolower($this->Status),
                    'Subtotal'  => $this->Subtotal,
                    'ValorFrete'  => $this->ValorFrete,
                    'ValorParcelas'  => $this->ValorParcelas,
                    'ValorPedido'  => $this->ValorPedido,
                    'ValorCupomDesconto'  => $this->ValorCupomDesconto,
                    'AnaliseFraude'  => $this->AnaliseFraude,
                    'Canal' => $this->Canal,
                    'Marketplace' => $this->Marketplace,
                    'orders_pack' => $this->getOrdersPack(),
                    'shipping_id' => $this->shipping_id,
                    'logistic_type' => $this->logistic_type,
                    'Obs' => $this->Obs
                    
                ));
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    
                    return;
                }else{
                    if($query->rowCount() > 0 ){
                        echo $this->form_msg = '<div class="alert alert-success alert-dismissable">Pedido atualizado com sucesso.</div>';
                    }
                }
                
       		}
       		
//        		else {
//        		    if($verifyStatus != $this->Status){
//        		        pre("Status order update id {$this->PedidoId}");
//        		        pre(array('from' => $verifyStatus, 'to' => $this->Status));
       		        
//        		    }
       		    
//        		}

            return $res['id'];
            
        } else {
            
            if($this->Status != 'canceled' OR $this->store_id == '3'){
                
                if($this->Status != 'cancelled'){
                
                    $query = $this->db->insert('orders', array(
                        'store_id'  => $this->store_id,
                        'customer_id'  => $this->customer_id,
                        'PedidoId'  => $this->PedidoId,
                    	'DataPedido'  => $this->DataPedido,
                    	'Ip'  => $this->Ip,
                    		
                        'Nome'  => $this->Nome,
                    	'CPFCNPJ'  => $this->CPFCNPJ,
                    	'RG'  => $this->RG,
                        'Email'  => $this->Email,
                        'Telefone'  => $this->Telefone,
                        'Bairro'  => $this->Bairro,
                        'Cep'  => $this->Cep,
                        'Endereco'  => $this->Endereco,
                    	'Numero'  => $this->Numero,
                    	'Cidade'  => $this->Cidade,
                    	'Estado'  => $this->Estado,
                    	'Complemento'  => $this->Complemento,
                        
                        'NomeDestino'  => $this->NomeDestino,
                    	'Telefone_entrega'  => $this->Telefone_entrega,
                    	'Cep_entrega'  => $this->Cep_entrega,
                    	'Endereco_entrega'  => $this->Endereco_entrega,
                    	'Numero_entrega'  => $this->Numero_entrega,
                    	'Bairro_entrega'  => $this->Bairro_entrega,
                    	'Cidade_entrega'  => $this->Cidade_entrega,
                    	'Estado_entrega'  => $this->Estado_entrega,
                    	'Complemento_entrega'  => $this->Complemento_entrega,
                        
                        'FormaPagamento'  => $this->FormaPagamento,
                        'Parcelas'  => $this->Parcelas,
                        'PrazoEnvio'  => $this->PrazoEnvio,
                        
                        'Status'  => $this->Status,
                        'Subtotal'  => $this->Subtotal,
                        'ValorFrete'  => $this->ValorFrete,
                        'ValorParcelas'  => $this->ValorParcelas,
                        'ValorPedido'  => $this->ValorPedido,
                        'ValorCupomDesconto'  => $this->ValorCupomDesconto,
                        'AnaliseFraude'  => $this->AnaliseFraude,
                        'Canal' => $this->Canal,
                        'Marketplace' => $this->Marketplace,
                        'orders_pack' => $this->getOrdersPack(),
                        'Obs' => $this->Obs,
                        'sent' => 'F',
                        'shipping_id' => $this->shipping_id,
                        'logistic_type' => $this->logistic_type,
                        'id_nota_saida' => $this->id_nota_saida
                    ));
                    
                    if ( ! $query ) {
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                        return;
                    } else {
                        $this->form_msg = '<div class="alert alert-success alert-dismissable">Pedido cadastrado com sucesso.</div>';
                        return $this->db->last_id;
                    }
                }else{
//                     pre(array('ake' => 'cancelled'));
                }
                
            }else{
//                 pre(array('aka' => 'canceled'));
            }
            
        }
        
        
    }

    
    public function GetProductSales()
    {
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "orders.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Nome': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Apelido': $where_fields .= "orders.{$key} LIKE UPPER('{$this->$key}') AND ";break;
                    case 'CPFCNPJ': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Email': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Cidade': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedido': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedidoAte': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'DataPedido': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'DataPedidoAte': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'FormaPagamento': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Canal': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Marketplace': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        $sql = "SELECT * FROM `orders` WHERE {$where_fields} ORDER BY id DESC";
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
        
    }
    
    
    public function GetOrder()
    {
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "orders.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Nome': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Apelido': $where_fields .= "orders.{$key} LIKE UPPER('{$this->$key}') AND ";break;
                    case 'CPFCNPJ': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Email': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Cidade': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedido': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedidoAte': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'DataPedido': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'DataPedidoAte': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'FormaPagamento': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Canal': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Marketplace': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        $query = $this->db->query("SELECT * FROM `orders` WHERE {$where_fields} ORDER BY id DESC");
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListOrders()
    {
        $query = $this->db->query('SELECT * FROM `orders`  WHERE `store_id` = ? ORDER BY id DESC',
            array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
        if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
            
            $id = chk_array( $this->parametros, 3 );
            
            $query = $this->db->query('SELECT * FROM orders WHERE `id`= ?', array( $id ) );
            
            foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
            {
                $column_name = str_replace('-','_',$key);
                
                if($column_name != 'orders_pack'){
                    
                    $this->{$column_name} = $value;
                    
                }else{
                
                    $this->{$column_name} = json_decode($value, true);
                    
                }
            }
            
        } else {
            
            return;
            
        }
        
    }
    
    
    public function getTotalOrdersPaidMl(){
        $sql = "SELECT COUNT(*) as total FROM orders 
        WHERE store_id = {$this->store_id} AND Marketplace LIKE 'Mercadolivre' AND Status LIKE 'paid'";
        $query = $this->db->query($sql);
        $total = $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
    }
    
    public function ExportOrderDetails()
    {
        $sql = "SELECT * FROM `orders`  WHERE `store_id` = ?  AND sent = 'F' ORDER BY id DESC;";
        
        if (isset($this->id) AND !empty($this->id)){
            $sql = "SELECT * FROM `orders`  WHERE `store_id` = ?  AND id = {$this->id} ORDER BY id DESC;";
        }
       
        $query = $this->db->query($sql, array($this->store_id));

        if ( ! $query ) {
            return array();
        }
        $orderDetail = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            
            $queryCustomer = $this->db->query( "SELECT * FROM customers WHERE store_id = ? AND id = ?", array($row['store_id'], $row['customer_id']));
            $row['customer'] = $queryCustomer->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT * FROM order_items WHERE store_id = {$row['store_id']} AND order_id = {$row['id']}";
            $queryItems = $this->db->query( $sql);
            

            
            while($rowItems = $queryItems->fetch(PDO::FETCH_ASSOC)){
            
                if(empty($rowItems['SKU']) OR $rowItems['SKU'] == 'undefined-'){
                    pre("Item sem SKU {$rowItems['SKU']} orderId {$row['id']}");
                    continue;
                }
            	
                $sqlAP = "SELECT id, kit FROM available_products WHERE store_id = {$row['store_id']} AND sku LIKE '{$rowItems['SKU']}'";
                $queryAP = $this->db->query($sqlAP);
                $availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
                
                if($availableProduct['kit'] == 'T'){
                    
                    $sqlPR = "SELECT available_products.*, product_relational.product_relational_id, product_relational.qtd
                                     FROM product_relational
                                     LEFT JOIN available_products ON available_products.id = product_relational.product_relational_id
                                     WHERE product_relational.store_id = {$row['store_id']} AND product_relational.product_id = {$availableProduct['id']}";
                    $queryPR = $this->db->query($sqlPR);
                    $productsRelational = $queryPR->fetchAll(PDO::FETCH_ASSOC);
                    $numRel = count($productsRelational);
                    $numRel = $numRel > 0 ? $numRel : 1 ; 
                    $PrecoUnitario = $rowItems['PrecoUnitario'] > 0 ? $rowItems['PrecoUnitario'] / $numRel : '0.00';
                    $PrecoVenda = $rowItems['PrecoVenda'] > 0 ? $rowItems['PrecoVenda'] / $numRel : '0.00';
                    $taxaVenda =  $rowItems['TaxaVenda']  > 0 ? $rowItems['TaxaVenda'] / $numRel : '0.00';
                    
                    foreach($productsRelational as $k => $productRelational){
                        
                        $kitItem = array();
                        
                        $qtd = $productRelational['qtd'] > 0 ? $productRelational['qtd'] : 1 ;
                        $qtd = $qtd * $rowItems['Quantidade'];
                        
                        $PrecoUnitarioRatio = $PrecoUnitario > 0 ? $PrecoUnitario / $qtd : '0.00';
                        $PrecoVendaRatio = $PrecoVenda > 0 ? $PrecoVenda / $qtd : '0.00';
                        $taxaVendaRatio =  $taxaVenda  > 0 ?$taxaVenda / $qtd : '0.00';
                        
                        $kitItem['store_id'] = $rowItems['store_id'];
                        $kitItem['order_id'] = $rowItems['order_id'];
                        $kitItem['PedidoId'] = $rowItems['PedidoId'];
                        $kitItem['PedidoItemId'] = $rowItems['PedidoItemId'];
                        $kitItem['SKU'] = $productRelational['sku'];
                        $kitItem['Nome'] = $productRelational['title'];
                        $kitItem['Quantidade'] = $qtd;
                        $kitItem['PrecoUnitario'] = $PrecoUnitarioRatio;
                        $kitItem['PrecoVenda'] = $PrecoVendaRatio;
                        $kitItem['TaxaVenda'] = $taxaVendaRatio;
                        
                        $kitItemAttr = array();
                        if(!empty($productRelational['color'])){
                            $kitItemAttr[] = array("Nome" => "color", "Valor" => $productRelational['color']);
                        }
                        if(!empty($productRelational['variation'])){
                            $kitItemAttr[] = array("Nome" => "variation", "Valor" => $productRelational['variation']);
                        }
                        
                        $kitItem['item_attributes'] = $kitItemAttr;
                        
                        $row['items'][] = $kitItem;
                        
                    }
                    
                    
                }else{
                    
                    $queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
                        array($row['store_id'], $row['id'], $rowItems['id'])
                        );
                    $rowItems['item_attributes'] =  $queryItemAttributes->fetchAll(PDO::FETCH_ASSOC);
                    
                    $row['items'][] = $rowItems;
                    
                }
                
                
                
            }
            
            
            
//             $queryPayments = $this->db->query( "SELECT * FROM order_payments
//             WHERE store_id = ? AND order_id = ? AND Situacao LIKE ?", array($row['store_id'], $row['id'], 'approved'));
            $queryPayments = $this->db->query( "SELECT * FROM order_payments
            WHERE store_id = ? AND order_id = ?", array($row['store_id'], $row['id']));
            $row['payments'] = $queryPayments->fetchAll(PDO::FETCH_ASSOC);
            
            
            $orderDetail[] = $row;
        }
        
        return $orderDetail;
        
    }
    
    public function ExportAllOrderDetails()
    {
        $sql = "SELECT * FROM `orders`  WHERE `store_id` = ?  AND Cidade IS NOT NULL AND sent = 'T' ORDER BY id ASC;";
//         echo 123;die;
        if (isset($this->id)){
            
            $sql = "SELECT * FROM `orders`  WHERE `store_id` = ?  AND id = {$this->id} ORDER BY id DESC;";
        }
        $query = $this->db->query($sql,array($this->store_id));
        
        if ( ! $query ) {
            return array();
        }
        $orderDetail = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $queryCustomer = $this->db->query( "SELECT * FROM customers WHERE store_id = ? AND id = ?", array($row['store_id'], $row['customer_id']));
            $row['customer'] = $queryCustomer->fetch(PDO::FETCH_ASSOC);
            

            $queryItems = $this->db->query( "SELECT * FROM order_items WHERE store_id = ? AND order_id = ?", array($row['store_id'], $row['id']));
            while($rowItems = $queryItems->fetch(PDO::FETCH_ASSOC)){
                
                $queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
                    array($row['store_id'], $row['id'], $rowItems['id'])
                    );
                $rowItems['item_attributes'] =  $queryItemAttributes->fetchAll(PDO::FETCH_ASSOC);
                
                $row['items'][] = $rowItems;
                
            }
            
            $queryPayments = $this->db->query( "SELECT * FROM order_payments
            WHERE store_id = ? AND order_id = ?", array($row['store_id'], $row['id']));
            $row['payments'] = $queryPayments->fetchAll(PDO::FETCH_ASSOC);
            
            
            $orderDetail[] = $row;
        }
        
        
        return $orderDetail;
        
    }
    
    public function Delete()
    {
        if ( chk_array( $this->parametros, 2 ) == 'del' ) {
            
            $id = chk_array( $this->parametros, 3 );
            
            $query = $this->db->query('DELETE FROM orders WHERE `id`= ?', array( $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
}
?>