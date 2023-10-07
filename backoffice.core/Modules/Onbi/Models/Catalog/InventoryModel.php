<?php 

class InventoryModel extends Soap
{



    public $product_id;
    
    public $sku;
    
    public $products_ids = array();
	
    public $store_id;
    
    public $stock_id = 0;
    
    public $qty;
    
    public $is_in_stock;
	




	
	public function __construct($db = false,  $controller = null, $storeId = null)
	{
	    $this->db = $db;
	    
	    $this->store_id = $storeId;
	    
	    $this->controller = $controller;
	    
	    if(isset($this->controller)){
	        
    	    $this->parametros = $this->controller->parametros;
    	    
    	    $this->userdata = $this->controller->userdata;
    	    
    	    $this->store_id = $this->controller->userdata['store_id'];
    	    
	    }
	    if(isset($this->store_id)){
	    
	        parent::__construct($this->db, $this->store_id);
	    
	    }
	    
	}
	
	
	public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
            	if(!empty($value)){
	                if(property_exists($this,$property)){
	                   $this->{$property} = $value;
	                }
            	}
                
            }
            
            return true;
            
        } else {
            
        	
            return;
            
        }
	    
	}
    
    
    public function catalogInventoryStockItemList(){
        
        if(!isset($this->products_ids)){
            
            return array();
        }
        
        if(isset($this->stock_id)){
            
            $response = $this->soapClient->catalogInventoryStockItemList($this->session_id, $this->products_ids, $this->stock_id);
            
        }else{
            
            $response = $this->soapClient->catalogInventoryStockItemList($this->session_id, $this->products_ids);
        }
        
        return $response;
        
    }
    
    public function catalogInventoryStockItemUpdate(){
        
        if(!isset($this->product_id)){
            
            return array();
        }
        
            
        $response = $this->soapClient->catalogInventoryStockItemUpdate($this->session_id, $this->sku, 
            array(
            	'manage_stock' => 1,
                'qty' => $this->qty, 
                'is_in_stock' => $this->is_in_stock
                
            ));
        
        return $response;
        
    }
    
    
}
?>