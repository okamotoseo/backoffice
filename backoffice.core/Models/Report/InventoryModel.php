<?php 


class InventoryModel extends MainModel
{
    
	public $id;
	
	public $account_id;
	
	public $store_id;
	
	public $sku;
	
	public $parent_id;
	
	public $title;
	
	public $color;
	
	public $variation_type;
	
	public $variation;
	
	public $brand;
	
	public $reference;
	
	public $category;
	
	public $quantity = 0;
	
	public $price;
	
	public $sale_price;
	
	public $promotion_price;
	
	public $cost;
	
	public $weight;
	
	public $height;
	
	public $width;
	
	public $length;
	
	public $ean;
	
	public $description;
	
	public $updated;
	
	public $xml;
	
	public $flag;
	
	public $blocked;
	
	public $marketplace;
	
	public $stock;
    
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
    
    
   
    public function ListProduct()
    {
        
//         $sql = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND qty_erp > 0 AND id NOT IN (
//         	SELECT product_id FROM product_relational WHERE store_id = {$this->store_id}
//         ) AND sku IN (
//         	SELECT sku FROM ml_products WHERE store_id = {$this->store_id}
//         ) ORDER BY brand";
        $sql = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND qty_erp > 0 AND id NOT IN (
        	SELECT product_id FROM product_relational WHERE store_id = {$this->store_id}
        ) ORDER BY brand, qty_erp, quantity DESC";
        $query = $this->db->query($sql);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $result;
        
    }
    
    public function GetAvailableProductsFilter()
    {
    
    	$where_fields = "";
    	$values = array();
    	$class_vars = get_class_vars(get_class($this));
    	foreach($class_vars as $key => $value){
    		if(!empty($this->{$key})){
    			switch($key){
    				case 'title': $where_fields .= "available_products.{$key} LIKE UPPER('".trim($this->$key)."') AND ";break;
    				case 'category':
    					if($this->$key == 'uncategorized'){
    						$where_fields .= "(available_products.{$key} LIKE '' OR available_products.{$key} IS NULL)  AND ";
    					}else{
    						$where_fields .= "available_products.{$key} LIKE '{$this->$key}%' AND ";
    					}
    					break;
    				case 'brand': $where_fields .= "available_products.{$key} LIKE '{$this->$key}' AND ";break;
    				case 'ean': $where_fields .= "available_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
    				case 'blocked': $where_fields .= "available_products.{$key} LIKE '".strtoupper($this->$key)."' AND ";break;
    				case 'stock':
    					if($this->$key == 'withStock'){
    						$where_fields .= "available_products.quantity > 0 AND ";
    					}else{
    						$where_fields .= "available_products.quantity <= 0 AND ";
    					}
    					break;
    			}
    		}
    
    	}
    
    	$where_fields = substr($where_fields, 0,-4);
    
    	return $where_fields;
    
    }
    
    
    
    
    public function GetProduct()
    {
        
        $where_fields = $this->GetAvailableProductsFilter();
        
//         $sql = "SELECT * FROM available_products WHERE available_products.store_id = {$this->store_id} AND {$where_fields} AND available_products.id NOT IN (
//         	SELECT product_id FROM product_relational WHERE store_id = {$this->store_id}
//         ) AND available_products.sku IN (
//         	SELECT sku FROM ml_products WHERE store_id = {$this->store_id}
//         ) ORDER BY brand";
        $sql = "SELECT * FROM available_products WHERE available_products.store_id = {$this->store_id} AND {$where_fields} AND available_products.id NOT IN (
        	SELECT product_id FROM product_relational WHERE store_id = {$this->store_id}
        )  ORDER BY brand, qty_erp, quantity DESC";
        $query = $this->db->query($sql);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
        
    }
    
    
    
}
?>