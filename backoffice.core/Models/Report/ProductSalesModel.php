<?php 
class ProductSalesModel extends MainModel
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
    
    public $ValorParcelas;
    
    public $ValorPedido;
    
    public $ValorPedidoAte;
    
    public $ValorCupomDesconto;
    
    public $AnaliseFraude;
    
    public $Marketplace;
    
    public $sku;
    
    public $nome;
    
    public $brand;
    
    public $reference;
    
    public $today;
    
    public $group_by;
    
    
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
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['generate-product-sales'] ) ) {
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
            
        } else {
            
            $this->pagina_atual = in_array('Page', $this->parametros ) ? get_next($this->parametros, array_search('Page', $this->parametros)) : 1 ;
            
            $this->linha_inicial = ($this->pagina_atual -1) * QTDE_REGISTROS;
            
            return;
            
        }
        
    }
    

    
    public function TotalOrders(){
        
        $sql = "SELECT count(*) as total FROM `orders`  WHERE `store_id` = ?";
        
        $query = $this->db->query( $sql ,array( $this->store_id));

        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }
    
    /*
     * 
     * TODO: add brand filter in join sale products report
     * 
     * 
     */    
    public function GetJoinAvailableProductsFilter()
    {
        
        $where_fields_join = '';
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'parent_id': $where_fields_join .= " AND available_products.{$key} LIKE '{$this->$key}'";break;
                    case 'reference': $where_fields_join .= " AND available_products.{$key} LIKE '{$this->$key}'";break;
                    case 'category': $where_fields_join .= " AND available_products.{$key} LIKE '{$this->$key}%'";break;
                    case 'brand': $where_fields_join .= " AND available_products.{$key} LIKE '{$this->$key}'";break;
                    
                }
            }
            
        }
        
        return $where_fields_join;
        
    }
   
    public function ListProductSales()
    {
        
        $sql = "SELECT orders.id, orders.Status, orders.fiscal_key, order_items.* FROM orders 
        LEFT JOIN order_items ON order_items.order_id = orders.id
        WHERE orders.store_id = {$this->store_id} AND orders.DataPedido 
        BETWEEN '{$this->today} 00:00:00' AND '{$this->today} 23:59:59'
        AND orders.status != 'cancelled' AND orders.status != 'pending'";
        $query = $this->db->query($sql);
        $items = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach($items as $key => $value){
        	
        	$queryAP = $this->db->query("SELECT id as product_id, cost FROM available_products WHERE store_id = {$this->store_id} 
        	AND sku LIKE '{$value['SKU']}'");
        	$products = $queryAP->fetch(PDO::FETCH_ASSOC);
        	$items[$key]['product_id'] = $products['product_id'];
        	$items[$key]['cost'] = $products['cost'];
        	
        }

        foreach($items as $sku => $item){
        	
           $queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?", 
               	array($item['store_id'], $item['order_id'], $item['id']));
           
           while($rowItemAttr = $queryItemAttributes->fetch(PDO::FETCH_ASSOC)){
				
           	$items[$sku]['attributes'][]  = array(
           			'name' => $rowItemAttr['Nome'],
           			'value' => $rowItemAttr['Valor']
           		);
           	
           }
           
       }
       
      return $items;
        
    }
    
    
    public function GetOrdersFilter(){
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
        	
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "orders.{$key} = {$this->$key} AND ";break;
                    case 'DataPedido':
                        $dataPedido = implode('-', array_reverse(explode('/', $this->$key)));
                        $dataPedidoAte = !empty($this->DataPedidoAte) ? implode('-', array_reverse(explode('/', $this->DataPedidoAte))) : date('Y-m-d') ;
                        $where_fields .= "orders.{$key} BETWEEN '{$dataPedido} 00:00:00' AND '{$dataPedidoAte} 23:59:59' AND ";
                        break;
                        
                    case 'DataPedidoAte':
                        if(empty($this->DataPedido)){
                            $dataPedido = implode('-', array_reverse(explode('/', $this->$key)));
                            $where_fields .= "orders.DataPedido BETWEEN '00-00-2000' AND '{$this->$key}' AND ";
                        }
                        
                        break;
                    case 'Marketplace': 
                    	 $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND "; 
                    	break;
                    
                }
                
            }
            
        }
            
        $where_fields = substr($where_fields, 0,-4);
        return $where_fields;
    }
    
    public function GetOrdersItemsFilter(){
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'nome': $where_fields .= "order_items.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'store_id': $where_fields .= "order_items.{$key} = {$this->$key} AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
    }
    
    public function GetGroupBy(){
        
        $groupBy = '';
        
        if(empty($this->group_by)){
            return $groupBy;
        }
        
        switch($this->group_by){
            case "sku": $groupBy = " GROUP BY available_products.parent_id "; break;
            case "DataPedido": $groupBy = " GROUP BY available_products.reference "; break;
            case "PrecoUnitario": $groupBy = " GROUP BY available_products.color "; break;
            case "brand": $groupBy = " GROUP BY available_products.brand "; break;
            case "ean": $groupBy = " GROUP BY available_products.ean "; break;
            default: $groupBy = ""; break;
            
        }
        
        return $groupBy;
        
    }
    
    
    public function GetSalesProductSku()
    {
    	$where_fields = $this->GetOrdersFilter();
    	
    	$where_items_fields = $this->GetOrdersItemsFilter();
    	
    	$rightColumn = $whereRightJoin = '';
    	if(!empty($this->brand)){
    		$rightColumn = ', available_products.brand';
    		$whereRightJoin = "RIGHT JOIN available_products ON available_products.sku = order_items.SKU
    				AND available_products.brand = '{$this->brand}'
    				AND available_products.store_id = order_items.store_id";
    	}
    	
		if(!empty($this->sku)){
			
			$skus = explode(',', $this->sku);
			
			
			
	    	foreach($skus as $sku){
	    		$sku = trim($sku);
	    		$sql = "SELECT orders.id, orders.Status, orders.fiscal_key, order_items.* {$rightColumn}
	    		FROM order_items
	    		{$whereRightJoin}
	    		RIGHT JOIN orders ON order_items.order_id = orders.id
	    		WHERE {$where_items_fields} AND order_items.SKU LIKE '{$sku}'  AND order_items.order_id IN (
	    		SELECT orders.id as order_id FROM orders WHERE {$where_fields} AND orders.status != 'cancelled' AND  orders.status != 'canceled' AND orders.status != 'pending'
	    		)";
	    		
// 	    		$queryItems = $this->db->query( "SELECT order_items.*, available_products.id as product_id FROM order_items
//             		LEFT JOIN available_products ON available_products.sku = order_items.SKU AND order_items.store_id = available_products.store_id
//             		AND available_products.parent_id != ''
//             		WHERE order_items.store_id = ? AND order_items.order_id = ?", array($row['store_id'], $row['id']));
	    		
	    		
	    		$query = $this->db->query($sql);
	    		$itemSku = $query->fetchAll(PDO::FETCH_ASSOC);
	    		
	    		foreach($itemSku as $key => $value){
	    				
	    			$queryAP = $this->db->query("SELECT id as product_id, cost FROM available_products WHERE store_id = {$this->store_id}
	    			AND sku LIKE '{$value['SKU']}'");
	    			$products = $queryAP->fetch(PDO::FETCH_ASSOC);
	    			$itemSku[$key]['product_id'] = $products['product_id'];
	    			$itemSku[$key]['cost'] = $products['cost'];
	    				
	    		}
	    		
	    		
	    		$itemsSku[] = $itemSku;
	    	}
	    	
		}else{
			
			
			$sql = "SELECT orders.id, orders.Status, order_items.* {$rightColumn}
			FROM order_items
			{$whereRightJoin}
			RIGHT JOIN orders ON order_items.order_id = orders.id
			WHERE {$where_items_fields} AND order_items.order_id IN (
			SELECT orders.id as order_id FROM orders WHERE {$where_fields} AND orders.status != 'cancelled' AND  orders.status != 'canceled'  AND orders.status != 'pending'
			)";
			$query = $this->db->query($sql);
			$itemSku = $query->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($itemSku as $key => $value){
				 
				$queryAP = $this->db->query("SELECT id as product_id, cost FROM available_products WHERE store_id = {$this->store_id}
				AND sku LIKE '{$value['SKU']}'");
				$products = $queryAP->fetch(PDO::FETCH_ASSOC);
				$itemSku[$key]['product_id'] = $products['product_id'];
				$itemSku[$key]['cost'] = $products['cost'];
				 
			}
			
			$itemsSku[] = $itemSku;
			
		}
		
    	$response = array();
    	foreach($itemsSku as $i => $items){
	    	foreach($items as $sku => $item){
	    
	    		$queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
	    				array($item['store_id'], $item['order_id'], $item['id'])
	    				);
	    		while($rowItemAttr = $queryItemAttributes->fetch(PDO::FETCH_ASSOC)){
	    			$itemsSku[$i][$sku]['attributes'][]  = array(
	    					'name' => $rowItemAttr['Nome'],
	    					'value' => $rowItemAttr['Valor']
	    			);
	    			
	    		}
// 	    		pre($itemsSku[$i][$sku]);die;
	    		$response[] = $itemsSku[$i][$sku];
    		}
    		
    
    	}
    	return $response;
    
    }
    
    
    
}
?>