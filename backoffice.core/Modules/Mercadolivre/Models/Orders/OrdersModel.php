<?php 


class OrdersModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
    public $customer_id;
    
    public $PedidoId;
    
    Public $Nome;
    
    public $CPFCNPJ;
    
    Public $Email;
    
    Public $Telefone;
    
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
    
    public $Marketplace;
    
    
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
    
    public function Save(){
        
        
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('orders', 'id', $this->id, array(
                'id'  => $this->id,
                'store_id'  => $this->store_id,
                'customer_id'  => $this->customer_id,
                'PedidoId'  => $this->PedidoId,
                'Nome'  => $this->Nome,
            	'CPFCNPJ'  => $this->CPFCNPJ,
                'Email'  => $this->Email,
                'Telefone'  => $this->Telefone,
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
                	'CPFCNPJ'  => $this->CPFCNPJ,
                    'Email'  => $this->Email,
                    'Telefone'  => $this->Telefone,
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
                    'sent' => 'F',
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
    
    public function ValidateOrder(){
        
        $query = $this->db->query('SELECT * FROM `orders`  WHERE  `store_id` = ?
    				AND PedidoId LIKE ? ORDER BY id DESC',
            array($this->store_id, $this->PedidoId)
            );
        
        $res = $query->fetch(PDO::FETCH_ASSOC);
        if(!isset($res['PedidoId'])){
            
            $this->Save();
            return $this->db->last_id;
        }
        
        return $res['id'];
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
                $this->{$column_name} = $value;
            }
            
        } else {
            
            return;
            
        }
        
    }
    
    
    public function getTotalOrdersPaidMl(){
       $sql = "SELECT COUNT(PedidoId) as total FROM orders 
        WHERE store_id = {$this->store_id} AND Marketplace LIKE 'Mercadolivre' AND Status LIKE 'paid'";
        $query = $this->db->query($sql);
        $total = $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
    }
    
    
    public function Delete()
    {
        if ( chk_array( $this->parametros, 2 ) == 'del' ) {
            
            $id = chk_array( $this->parametros, 3 );
            
            $query = $this->db->query('DELETE FROM orders WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
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