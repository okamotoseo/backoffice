<?php 


class SkyhubOrdersModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
    public $customer_id;
    
    public $PedidoId;
    
    Public $Nome;
    
    Public $Email;
    
    public $Bairro;
    
    public $Cep;
    
    public $Cidade;
    
    public $Complemento;
    
    public $DataPedido;
    
    public $DataPedidoAte;
    
    public $Endereco;
    
    public $Estado;
    
    public $Ip;
    
    public $NomeDestino;
    
    public $Numero;
    
    public $FormaPagamento;
    
    public $Parcelas;
    
    public $PrazoEnvio;
    
    public $RG;
    
    public $Status;
    
    public $Subtotal;
    
    public $ValorFrete;
    
    public $ValorParcelas;
    
    public $ValorPedido;
    
    public $ValorPedidoAte;
    
    public $ValorCupomDesconto;
    
    public $AnaliseFraude;
    
    public $Canal = "Skyhub";
    
    public $Marketplace = "Marketplace";
    
    public $sent;
    
    public $ShowItems;
    
    public $records = 20;
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
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
                    $val = str_replace("_", " ", $val);
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
            
            return true;
            
        } else {
            
            if ( in_array('edit', $this->parametros )) {
                
                $this->Load();
                
            }

            
            return;
            
        }
        
    }
    
    public function Save(){
        
        
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('orders', 'id', $this->id, array(
                'id'  => $this->id,
                'store_id'  => $this->store_id,
                'customer_id'  => $this->customer_id,
                'PedidoId'  => $this->PedidoId,
                'Nome'  => $this->Nome,
                'Email'  => $this->Email,
                'Bairro'  => $this->Bairro,
                'Cep'  => $this->Cep,
                'Cidade'  => $this->Cidade,
                'Complemento'  => $this->Complemento,
                'DataPedido'  => $this->DataPedido,
                'Endereco'  => $this->Endereco,
                'Estado'  => $this->Estado,
                'Ip'  => $this->Ip,
                'NomeDestino'  => $this->NomeDestino,
                'Numero'  => $this->Numero,
                'FormaPagamento'  => $this->FormaPagamento,
                'Parcelas'  => $this->Parcelas,
                'PrazoEnvio'  => $this->PrazoEnvio,
                'RG'  => $this->RG,
                'Status'  => $this->Status,
                'Subtotal'  => $this->Subtotal,
                'ValorFrete'  => $this->ValorFrete,
                'ValorParcelas'  => $this->ValorParcelas,
                'ValorPedido'  => $this->ValorPedido,
                'ValorCupomDesconto'  => $this->ValorCupomDesconto,
                'AnaliseFraude'  => $this->AnaliseFraude,
                'Canal' => $this->Canal,
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
                    'Nome'  => $this->Nome,
                    'Email'  => $this->Email,
                    'Bairro'  => $this->Bairro,
                    'Cep'  => $this->Cep,
                    'Cidade'  => $this->Cidade,
                    'Complemento'  => $this->Complemento,
                    'DataPedido'  => $this->DataPedido,
                    'Endereco'  => $this->Endereco,
                    'Estado'  => $this->Estado,
                    'Ip'  => $this->Ip,
                    'NomeDestino'  => $this->NomeDestino,
                    'Numero'  => $this->Numero,
                    'FormaPagamento'  => $this->FormaPagamento,
                    'Parcelas'  => $this->Parcelas,
                    'PrazoEnvio'  => $this->PrazoEnvio,
                    'RG'  => $this->RG,
                    'Status'  => $this->Status,
                    'Subtotal'  => $this->Subtotal,
                    'ValorFrete'  => $this->ValorFrete,
                    'ValorParcelas'  => $this->ValorParcelas,
                    'ValorPedido'  => $this->ValorPedido,
                    'ValorCupomDesconto'  => $this->ValorCupomDesconto,
                    'AnaliseFraude'  => $this->AnaliseFraude,
                    'Canal' => $this->Canal,
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

    
    public function GetOrderFilter(){
        
        $where_fields = "orders.Canal LIKE '{$this->Canal}' AND ";
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
                    case 'CPFCNPJ': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Email': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Cidade': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedido': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedidoAte': $where_fields .= "orders.ValorPedido LIKE '{$this->$key}' AND ";break;
                    case 'DataPedido': $where_fields .= "orders.{$key} >= '".dbDate($this->$key)."' AND ";break;
                    case 'DataPedidoAte':$where_fields .= "orders.DataPedido <= '".dbDate($this->$key)." 23:59:59' AND ";break;
                    case 'FormaPagamento': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Marketplace': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
    }
    
    
    
    public function TotalOrders(){
        
        $where_fields = $this->GetOrderFilter();
        
        $sql = "SELECT count(*) as total FROM `orders`  WHERE {$where_fields}";
        
        $query = $this->db->query( $sql);
        
        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }

    
    
    public function GetOrderDetails()
    {
        
        $where_fields = $this->GetOrderFilter();
//         pre($where_fields);die;
        $query = $this->db->query("SELECT * FROM `orders` WHERE {$where_fields} ORDER BY id DESC 
        LIMIT {$this->linha_inicial}, " . QTDE_REGISTROS.";");
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
            WHERE store_id = ? AND order_id = ? AND Situacao LIKE ?", array($row['store_id'], $row['id'], 'approved'));
            $row['payments'] = $queryPayments->fetchAll(PDO::FETCH_ASSOC);
            
            
            $orderDetail[] = $row;
        }
        
        
        return $orderDetail;
        
    }
    
    
    public function ListOrderDetails()
    {
        $query = $this->db->query("SELECT * FROM `orders`  WHERE `store_id` = ? AND Canal LIKE ? ORDER BY id DESC
            LIMIT {$this->linha_inicial}, " . QTDE_REGISTROS.";",
            array($this->store_id, $this->Canal)
        );
        
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
            WHERE store_id = ? AND order_id = ? AND Situacao LIKE ?", array($row['store_id'], $row['id'], 'approved'));
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
    

    
}
?>