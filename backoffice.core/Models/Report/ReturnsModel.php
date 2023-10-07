<?php 


class ReturnsModel extends MainModel
{
    
    public $id;
    
    public $store_id;
    
    public $shipping_id;
    
    public $order_id;
    
    public $pedido_id;
    
    public $created;
    
    public $createdAte;

    public $customer_id;
    
    public $PedidoId;
    
    Public $Nome;
    
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
    
    public $FreteCusto;
    
    public $ValorParcelas;
    
    public $ValorPedido;
    
    public $ValorPedidoAte;
    
    public $ValorCupomDesconto;
    
    public $AnaliseFraude;
    
    public $Marketplace;
    
    public $sku;
    
    public $title;
    
    public $reference;
    
    public $today;
    
    public $where_fields_join = '';
    
    public $imposto = 0;
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->today =  date("Y-m-d");
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
        }
    }
    
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST['btn-filter-order']  ) ) {
        	
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                }else{
                    	$required = array('DataPedido');
                    	if( in_array($property, $required) ){
                    		$this->field_error[$property] = "has-error";
                    		$this->form_msg = "<div class='alert alert-danger alert-dismissable'>Por favor informar uma data inicial...</div>";
                    		return false;
                    	}
                    }
                
            }
            
            return true;
            
        } 
        
        return false;
        
    }
    

    
    public function TotalOrders(){
        
        $sql = "SELECT count(*) as total FROM `orders`  WHERE `store_id` = ?";
        
        $query = $this->db->query( $sql ,array( $this->store_id));

        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }
    
    
    public function GetOrderFilter(){
        
        $where_fields = "";
        $where_fields_join = "";
        
        $values = array();
        
        $class_vars = get_class_vars(get_class($this));
        
        
        
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                 
                switch($key){
                    
                    case 'store_id': $where_fields_join .= " AND order_returns.{$key} = {$this->$key}";break; 
                    case 'pedido_id': $where_fields_join .= " AND order_returns.{$key} LIKE '{$this->$key}'";break;
                    case 'type_return': $where_fields_join .= " AND order_returns.{$key} LIKE '{$this->$key}'";break;
                    case 'created': 
                        $where_fields_join .= " AND order_returns.{$key} >= '".dbDate($this->$key)."'";
//                         $this->DataPedido  = $this->$key;
                        break;
                    case 'createdAte':
                        $where_fields_join .= " AND order_returns.created <= '".dbDate($this->$key)." 23:59:59'";
//                         $this->DataPedidoAte  = $this->$key; 
                        break;  
                    
                } 
                
            }
            
        }
        
        $this->where_fields_join = $where_fields_join;
        
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                
                switch($key){
                    case 'id': $where_fields_join .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'store_id': $where_fields .= "orders.{$key} = {$this->$key} AND ";break;
                    case 'PedidoId': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
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
    
    public function GetReturns()
    {
        
        $where_fields = $this->GetOrderFilter();
        
        $sql = "SELECT orders.*, order_returns.type_return, order_returns.reasons, order_returns.status, order_returns.created,
        order_returns.pedido_id
        FROM `orders`  
        RIGHT JOIN order_returns ON order_returns.order_id = orders.id {$this->where_fields_join}
        WHERE {$where_fields}"; 
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        $orderDetail = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            
            $queryOcurrence = $this->db->query( "SELECT * FROM order_occurrence WHERE store_id = ? AND order_id = ?", array($row['store_id'], $row['id']));
            $rowOcurrence['occurrences'] =  $queryOcurrence->fetchAll(PDO::FETCH_ASSOC);
            
            $queryItems = $this->db->query( "SELECT order_items.*, available_products.cost
            FROM order_items
            LEFT JOIN available_products ON available_products.sku = order_items.SKU AND order_items.store_id = available_products.store_id AND available_products.parent_id != ''
            WHERE order_items.store_id = ? AND order_items.order_id = ?", array($row['store_id'], $row['id']));
            
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
    
}
?>