<?php 

class MediaModel extends REST
{

    public $store_id;

	public $product_id;
	
	public $sku;
	
	public $identifierType = 'SKU';
	    
	public $set_id = '4';
	
	public $file = array();
	
	public $label;
	
	public $position = 0;
	
	public $types;
	
	public $media;
	
	public $excludes = 0;
	
	
	
	
	
	
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
	
	
	public function catalogProductAttributeMediaTypes(){
	    
	    if(!isset($this->set_attribute_id)){
	        return array();
	    }
	    
	    $params = array();

	    return $this->get("/rest/{$this->storeView}/V1/products/media/types/{$this->set_attribute_id}", $params);
	    
	}
	
	
	
	public function catalogProductAttributeMediaCreate(){
	    
	    if(!isset($this->media)){
	        return array();
	    }
	    
	    return $this->post("/rest/{$this->storeView}/V1/products/{$this->sku}/media", $this->media);
	}
	
	
	public function catalogProductAttributeMediaList(){
	    
	    if(!isset($this->sku)){
	        return array();
	    }
	    
	    $params = array();
	    
	    return $this->get("/rest/{$this->storeView}/V1/products/{$this->sku}/media", $params);
	    
	}
	
	/************************************************************************************************/
	/************************************** Custom **************************************************/
	/************************************************************************************************/
  
    
}
	

?>