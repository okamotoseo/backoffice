<?php 


class SalesModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
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
    
    public $month;
    
    public $year;
    
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
                        if( $property == 'month'){
                            
                            $parts = explode(' ', $value);
                            
                            $this->{$property} = $parts[0];
                            
                            $this->year = $parts[1];

                            $month_ini = new DateTime("first day of {$value}");
                            
                            $month_end = new DateTime("last day of {$value}");
                            
                            $this->DataPedido = $month_ini->format('d/m/Y'); 
                            
                            $this->DataPedidoAte = $month_end->format("d/m/Y");
                           
                        }else{
                            $this->{$property} = $value;
                        }
                        
                    }
                }else{
                    	$required = array('DataPedido');
                    	if( in_array($property, $required) ){
                    		$this->field_error[$property] = "has-error";
                    		$this->form_msg = "<div class='alert alert-danger alert-dismissable'>Por favor informar uma data inicial...</div>";
//                     		return false;
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
    
    public function GetSalesFilter(){
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "orders.{$key} = {$this->$key} AND ";break;
                    case 'DataPedido':
                        $dataPedido = implode('-', array_reverse(explode('/', $this->$key)));
                        $dataPedidoAte = isset($this->DataPedidoAte) ? implode('-', array_reverse(explode('/', $this->DataPedidoAte))) : date('Y-m-d') ;
                        $where_fields .= "orders.{$key} BETWEEN '{$dataPedido} 00:00:00' AND '{$dataPedidoAte} 23:59:59' AND ";
                        break;
                    case 'DataPedidoAte':
                        if(!isset($this->DataPedido)){
                            $dataPedido = implode('-', array_reverse(explode('/', $this->$key)));
                            $where_fields .= "orders.DataPedido BETWEEN '00-00-2000' AND '{$this->$key}' AND ";
                        }
                        break;
                    case 'Marketplace': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
    }
    public function GetSales()
    {
        
        $where_fields = $this->GetOrderFilter();
//                 pre($where_fields);die;
        $query = $this->db->query("SELECT * FROM `orders` WHERE {$where_fields} AND orders.status != 'cancelled' 
        AND  orders.status != 'canceled'  AND orders.status != 'pending'");
        if ( ! $query ) {
            return array();
        }
        $orderDetail = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
//             $queryCustomer = $this->db->query( "SELECT * FROM customers WHERE store_id = ? AND id = ?", array($row['store_id'], $row['customer_id']));
//             $row['customer'] = $queryCustomer->fetch(PDO::FETCH_ASSOC);
            
            $row['FreteCusto'] = $row['FreteCusto'] > 0 ? $row['FreteCusto'] : $row['ValorFrete'] ;
            
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
    
//     public function GetSales()
//     {  
        
//         $where_fields = $this->GetOrderFilter();
        
// //         $sql = "SELECT orders.*, order_items.* , available_products.cost
// //         FROM orders 
// //         LEFT JOIN order_items ON orders.id = order_items.order_id 
// //         LEFT JOIN available_products ON available_products.sku = order_items.SKU
// //         WHERE {$where_fields} AND orders.Status != 'cancelled';";
            
// //         die;
//         $sql  = "SELECT * FROM `orders` WHERE {$where_fields} AND orders.Status != 'cancelled'  AND  orders.status != 'canceled'";
//         $query = $this->db->query($sql);
//         if ( ! $query ) {
//             return array();
//         }
//         $orderDetail = array();
//         $rowAll = $query->fetchAll(PDO::FETCH_ASSOC);
// //         foreach($rowAll as $key => $row){
            
// //             $queryItems = $this->db->query( "SELECT order_items.* FROM order_items WHERE order_items.store_id = ? AND order_items.order_id = ?", array($row['store_id'], $row['id']));
// //             $rowItemsAll = $queryItems->fetchAll(PDO::FETCH_ASSOC);
            
// //             foreach($rowItemsAll as $i => $rowItems){
                
// //                 $queryCost = $this->db->query( "SELECT cost FROM available_products WHERE store_id = ? AND sku = ?", array($row['store_id'], $rowItems['SKU']));
// //                 $rowCost = $queryCost->fetch(PDO::FETCH_ASSOC);
// //                 $rowItemsAll[$i]['cost'] = $rowCost['cost'];
                
                
// // //                 $queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
// // //                     array($row['store_id'], $row['id'], $rowItems['id'])
// // //                     );
// // //                 $rowItems['item_attributes'] =  $queryItemAttributes->fetchAll(PDO::FETCH_ASSOC);
                
// // //                 $row['items'][] = $rowItems;
// //             }
// //             $rowAll[$key]['items'] = $rowItemsAll;
            
// //         }
//         return $rowAll;
        
//     }
    
    
    public function ListSales()
    {
        $query = $this->db->query("SELECT * FROM `orders`  WHERE 
        orders.store_id = ? AND orders.DataPedido BETWEEN '{$this->today} 00:00:00' AND '{$this->today} 23:59:59'
        AND orders.status != 'cancelled' AND  orders.status != 'canceled' AND orders.status != 'pending'",
            array($this->store_id)
        );
        
        if ( ! $query ) {
            return array();
        }
        $orderDetail = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            
//             $queryCustomer = $this->db->query( "SELECT * FROM customers WHERE store_id = ? AND id = ?", array($row['store_id'], $row['customer_id']));
//             $row['customer'] = $queryCustomer->fetch(PDO::FETCH_ASSOC);
            
            $row['FreteCusto'] = $row['FreteCusto'] > 0 ? $row['FreteCusto'] : $row['ValorFrete'] ;
            
//             pre($row);die;
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
            WHERE store_id = ? AND order_id = ? AND Situacao != 'cancelled' AND Situacao != 'canceled'", array($row['store_id'], $row['id']));
            $row['payments'] = $queryPayments->fetchAll(PDO::FETCH_ASSOC);
            
            
            $orderDetail[] = $row;
        }
        
        
        return $orderDetail;
        
    }
    
    
    
}
?>