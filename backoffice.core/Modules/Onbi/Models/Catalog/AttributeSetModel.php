<?php 

class AttributeSetModel extends Soap
{

 
    public $store_id;
    
    public $skeleton_id = 4;// padrão
    
    public $attribute_set_name;
    
    public $attribute_id;
    
    public $attribute_set_id;
    
    public $attribute_group_id;
    
    public $sort_order;
	
	




	
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

	
    

    
    public function catalogProductAttributeSetList(){
        
        if(!isset($this->session_id)){
            
            return array();
        }
        
        $response = $this->soapClient->catalogProductAttributeSetList($this->session_id);
        
        return $response;
        
    }
    
    
    public function catalogProductAttributeSetCreate(){
        
        if(!isset($this->attribute_set_name)){
            
            return array();
        }
        
        $response = $this->soapClient->catalogProductAttributeSetCreate($this->session_id, $this->attribute_set_name, $this->skeleton_id);
        
        return $response;
        
    }
    
    public function catalogProductAttributeSetAttributeAdd(){
        
        if(!isset($this->attribute_id)){
            
            return array();
        }
        
        $response = $this->soapClient->catalogProductAttributeSetAttributeAdd($this->session_id, $this->attribute_id, $this->attribute_set_id);
        
        return $response;
        
    }
    
    
    

    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
    
    
    
}
?>