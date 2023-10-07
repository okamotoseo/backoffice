<?php 

class InventoryModel extends REST
{

	public $store_id;
	
	public $response;
	
	public $product_id;
	
	public $sku;
	
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
	
	
    
    
    public function catalogInventoryStockItemList(){
        
        if(!isset($this->products_ids)){
            
            return array();
        }
        
//         if(isset($this->stock_id)){
            
//             $response = $this->soapClient->catalogInventoryStockItemList($this->session_id, $this->products_ids, $this->stock_id);
            
//         }else{
            
//             $response = $this->soapClient->catalogInventoryStockItemList($this->session_id, $this->products_ids);
//         }
        
//         return $response;
        
    }
    
    
    public function catalogInventoryStockItemUpdate(){
    
    	if(empty($this->product_id)){
    		return array();
    	}
    
    	$data = array("stock_item" => array(
            "item_id" => $this->product_id,
            "qty" => $this->qty,
            "is_in_stock" => $this->is_in_stock
		));
    
    	return $this->put("/rest/{$this->storeView}/V1/products/{$this->sku}/stockItems/{$this->product_id}", $data);
    }
    
    
}
?>