<?php 

class MediaModel extends Soap
{

    public $store_id;

	public $product_id;
	
	public $sku;
	
	public $storeView = '1';
	
	public $identifierType = 'SKU';
	    
	public $set_id = '4';
	
	public $file = array();
	
	public $label;
	
	public $position = 0;
	
	public $types = array('thumbnail','small_image', 'image');
	
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
	    
	    if(!isset($this->set_id)){
	        return array();
	    }
	    
	    $response = $this->soapClient->catalogProductAttributeMediaTypes($this->session_id, $this->set_id);
	    
	    return $response;
	    
	}
	
// 	public function catalogProductCurrentStore(){
	    
	    
// 	    $response = $this->soapClient->catalogProductCurrentStore($this->session_id);
	    
// 	    return $response;
	    
// 	}
	
	
	
	public function catalogProductAttributeMediaCreate(){
	    
	    if(empty($this->file)){
	        return array();
	    }
	    
	    $types = $this->position == 0 ?  array('image') : array('small_image') ;
	    $media = array(
	        'file' => $this->file,
	        'label' => $this->label,
	        'position' => $this->position,
	        'types' => $types,
	        'excludes' => $this->excludes
	    );
	    $result = $this->soapClient->catalogProductAttributeMediaCreate(
	        $this->session_id,
	        $this->product_id,
	        $media,
	        $this->storeView
	        );
	    
	    if(is_soap_fault($result)){
	        
	        return $result->faultstring;
	    }
	    
	    return $result;
	}
	
	
	public function catalogProductAttributeMediaList(){
	    
	    if(!isset($this->product_id)){
	        
	        return array();
	        
	    }
	    
	    $response = $this->soapClient->catalogProductAttributeMediaList($this->session_id, $this->product_id, $this->storeView, $this->storeView, 'ID');
	    
	    
	    return $response;
	    
	}
	
	/************************************************************************************************/
	/************************************** Custom **************************************************/
	/************************************************************************************************/
	    
	    
    
  
    
}
	

?>