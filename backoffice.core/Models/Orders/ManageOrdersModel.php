<?php 

class ManageOrdersModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
    public $customer_id;
    
    public $PedidoId;
    
    public $DataPedido;
    
    public $Ip;
    
    public $Nome;
    
    public $CPFCNPJ;
    
    public $RG;
    
    public $Email;
    
    public $Telefone;
    
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
    
    public $Marketplace;
    
    public $sent;
    
    public $printed;
    
    public $ShowItems;
    
    public $records = 10;
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
            if($this->store_id == 4){
            	$this->records = 50;
            }
            
        }
        
        if(!defined('QTDE_REGISTROS')){
        	
        	if($this->store_id == 4){
        		define('QTDE_REGISTROS', 50);
        	}else{
            	define('QTDE_REGISTROS', 10);
        	}
            
        }
        

    }
    
    
    public function ValidateForm() {
        
        if(in_array('records', $this->parametros )){
            $records = get_next($this->parametros, array_search('records', $this->parametros));
            $this->records = isset($records) ? $records : QTDE_REGISTROS ;
        }
        
        if(in_array('Page', $this->parametros )){
            
            $this->pagina_atual =  get_next($this->parametros, array_search('Page', $this->parametros));
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
            
            foreach($this->parametros as $key => $param){
                if(property_exists($this,$param)){
                    $val = get_next($this->parametros, $key);
                    $val = str_replace("_x_", "%", $val);
                    $this->{$param} = $val;
                }
            }
            
            return true;
            
        }else{
            
            $this->pagina_atual = 1;
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
        }
        
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
            
            if(!empty($this->form_msg)){
                
                return false;
            }
            
            return true;
            
            
        } else {
            
            
            if ( in_array('Product', $this->parametros )) {
                
                $key = array_search('Product', $this->parametros);
                
                $productId = get_next($this->parametros, $key);
                $this->id  = is_numeric($productId) ? $productId :  '';
                
                if(!empty($this->id)){
                    
                    $this->Load();
                    
                }
                
            }
            
     
            if ( in_array('del', $this->parametros )) {
                $this->Delete();
            }
            
            return false;
            
        }
        
    }
    
    public function Save(){
        
        
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('orders', 'id', $this->id, array(
                'id'  => $this->id,
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
                
                'Status'  => $this->Status,
                'Subtotal'  => $this->Subtotal,
                'ValorFrete'  => $this->ValorFrete,
                'ValorParcelas'  => $this->ValorParcelas,
                'ValorPedido'  => $this->ValorPedido,
                'ValorCupomDesconto'  => $this->ValorCupomDesconto,
                'AnaliseFraude'  => $this->AnaliseFraude,
                'Marketplace' => $this->Marketplace
                
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Pedido atualizado com sucesso.</div>';
                $this->id = null;
                return;
            }
        } else {

                
                $query = $this->db->insert('orders', array(
                    'id'  => $this->id,
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
                    
                    'Status'  => $this->Status,
                    'Subtotal'  => $this->Subtotal,
                    'ValorFrete'  => $this->ValorFrete,
                    'ValorParcelas'  => $this->ValorParcelas,
                    'ValorPedido'  => $this->ValorPedido,
                    'ValorCupomDesconto'  => $this->ValorCupomDesconto,
                    'AnaliseFraude'  => $this->AnaliseFraude,
                    'Marketplace' => $this->Marketplace
                )
                    );
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    return;
                } else {
                    
                    $this->form_msg = '<div class="alert alert-success alert-dismissable">Pedido cadastrado com sucesso.</div>';
                    return;
                }
                

            
        }
        
        
    }
    
    public function listStatusOrders(){
        
        $sql = "SELECT distinct status FROM `orders`  WHERE store_id = {$this->store_id} AND status != ''";
        
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
        
    }
    
    public function listPaymentsOrders(){
        
        $sql = "SELECT distinct FormaPagamento FROM `orders`  WHERE store_id = {$this->store_id} AND FormaPagamento != ''";
        
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
        
    }
    
    public function listUfOrders(){
        
        $sql = "SELECT distinct Estado FROM `orders`  WHERE store_id = {$this->store_id} AND Estado != ''";
        
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
        
    }
    
    public function listMarketplaceOrders(){
        
        $sql = "SELECT distinct Marketplace FROM `orders`  WHERE store_id = {$this->store_id} AND Marketplace != ''";
        
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
        
    }
    
    public function TotalOrders(){
        
        $where_fields = $this->GetOrderFilter();
        
        $sql = "SELECT count(*) as total FROM `orders`  WHERE {$where_fields}";
        
        $query = $this->db->query( $sql);
        
        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }
    
    public function GetOrderFilter(){
        
        $where_fields = "";
        $values = array();
        
        $class_vars = get_class_vars(get_class($this));
       
        foreach($class_vars as $key => $value){
           
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "orders.{$key} = {$this->$key} AND ";break;
                    case 'PedidoId': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'id': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Nome': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Apelido': $where_fields .= "orders.{$key} LIKE UPPER('{$this->$key}') AND ";break;
                    case 'CPFCNPJ': $where_fields .= "orders.{$key} LIKE '".getNumbers($this->$key)."' AND ";break;
                    case 'Email': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Cidade': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedido': $where_fields .= "orders.{$key} >= '{$this->$key}' AND ";break;
                    case 'ValorPedidoAte': $where_fields .= "orders.ValorPedido <= '{$this->$key}' AND ";break;
                    case 'DataPedido': $where_fields .= "orders.{$key} >= '".dbDate($this->$key)."' AND ";break;
                    case 'DataPedidoAte':$where_fields .= "orders.DataPedido <= '".dbDate($this->$key)." 23:59:59' AND ";break;
                    case 'FormaPagamento': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Marketplace': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Status': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Estado': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'printed': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
    }
    
    
    
    public function ListOrders()
    {
        
        $query = $this->db->query("SELECT * FROM `orders`  WHERE `store_id` = ? ORDER BY id DESC 
            LIMIT {$this->linha_inicial}, " . $this->records.";", array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        
        return $$query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    public function GetOrder()
    {
        $where_fields = $this->GetOrderFilter();
        
        $query = $this->db->query("SELECT * FROM `orders` WHERE {$where_fields} ORDER BY id DESC
        LIMIT {$this->linha_inicial}, " . $this->records.";");
        
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    public function GetOrderDetails()
    {
        
        
        $where_fields = $this->GetOrderFilter();
//         pre($where_fields);die;

        $sqlOrders = "SELECT * FROM `orders` WHERE {$where_fields} ORDER BY id DESC 
        LIMIT {$this->linha_inicial}, " . $this->records.";"; 
        $query = $this->db->query($sqlOrders);
        if ( ! $query ) {
            return array();
        }
        $orderDetail = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $queryCustomer = $this->db->query( "SELECT * FROM customers WHERE store_id = ? AND id = ?", array($row['store_id'], $row['customer_id']));
            $row['customer'] = $queryCustomer->fetch(PDO::FETCH_ASSOC);
            
            $queryItems = $this->db->query( "SELECT order_items.*, available_products.id as product_id, available_products.weight as peso  FROM order_items 
            		LEFT JOIN available_products ON available_products.sku = order_items.SKU AND order_items.store_id = available_products.store_id
            		AND available_products.parent_id != '' 
            		WHERE order_items.store_id = ? AND order_items.order_id = ?", array($row['store_id'], $row['id']));
            while($rowItems = $queryItems->fetch(PDO::FETCH_ASSOC)){
            	
                $queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
                    array($row['store_id'], $row['id'], $rowItems['id'])
                    );
                $rowItems['item_attributes'] =  $queryItemAttributes->fetchAll(PDO::FETCH_ASSOC);
                
                $row['items'][] = $rowItems;
            }
            
            $queryPayments = $this->db->query( "SELECT * FROM order_payments
            WHERE store_id = ? AND order_id = ? ", array($row['store_id'], $row['id']));
            $row['payments'] = $queryPayments->fetchAll(PDO::FETCH_ASSOC);
            
            
            $orderDetail[] = $row;
        }
//         pre($orderDetail);die;
        
        return $orderDetail;
        
    }
    
    
    public function ListOrderDetails()
    {
        $query = $this->db->query("SELECT * FROM `orders`  WHERE `store_id` = ? ORDER BY id DESC
            LIMIT {$this->linha_inicial}, " . $this->records.";",
            array($this->store_id)
        );
        
        if ( ! $query ) {
            return array();
        }
        $orderDetail = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            
            $queryCustomer = $this->db->query( "SELECT * FROM customers WHERE store_id = ? AND id = ?", array($row['store_id'], $row['customer_id']));
            $row['customer'] = $queryCustomer->fetch(PDO::FETCH_ASSOC);
            
            
            $queryItems = $this->db->query( "SELECT order_items.*, available_products.id as product_id FROM order_items 
            		LEFT JOIN available_products ON available_products.sku = order_items.SKU AND order_items.store_id = available_products.store_id
            		AND available_products.parent_id != ''
            		WHERE order_items.store_id = ? AND order_items.order_id = ?", array($row['store_id'], $row['id']));
    
            
            while($rowItems = $queryItems->fetch(PDO::FETCH_ASSOC)){
                
                $queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
                    array($row['store_id'], $row['id'], $rowItems['id'])
                    );
                $rowItems['item_attributes'] =  $queryItemAttributes->fetchAll(PDO::FETCH_ASSOC);
                
                $row['items'][] = $rowItems;
                
            }
            
            $queryPayments = $this->db->query( "SELECT * FROM order_payments
            WHERE store_id = ? AND order_id = ? ", array($row['store_id'], $row['id']));
            $row['payments'] = $queryPayments->fetchAll(PDO::FETCH_ASSOC);
            
            
            $orderDetail[] = $row;
        }
        
        
        return $orderDetail;
        
    }
    
    public function Load()
    {
        if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
            
            $id = chk_array( $this->parametros, 3 );
            
            $query = $this->db->query('SELECT * FROM orders WHERE `id`= ?', array( $id ) );
            
            foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
            {
                $column_name = str_replace('-','_',$key);
                $this->{$column_name} = $value;
            }
            
        } else {
            
            return;
            
        }
        
    }
    
    public function Delete()
    {
        $key = array_search('del', $this->parametros);
        if(!empty($key)){
            $id = get_next($this->parametros, $key);
        }
        
        if(!empty($id)){
            
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