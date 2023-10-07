<?php 

class AttributeSetModel extends REST
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
        
        if(!isset($this->filters)){
        	return array();
        }
         
        $this->MakeCriteria();
        
        return $this->get("/rest/{$this->storeView}/V1/products/attribute-sets/sets/list", $this->searchCriteria);
        
    }
    
    
    public function catalogProductAttributeSetCreate(){
        
        if(!isset($this->attribute_set_name)){
            return array();
        }
        $data = array(
        		'entityTypeCode' => 4,
        		'attributeSet' => array('attribute_set_name' => $this->attribute_set_name),
        		'skeletonId' => $this->skeleton_id
        );
        $opts = array(
        		CURLOPT_CUSTOMREQUEST => "POST",
        		CURLOPT_POSTFIELDS => json_encode($data),
        		CURLOPT_HTTPHEADER => array(
        				"Authorization: Bearer {$this->token}",
        				"Content-Type: application/json",
        				"cache-control: no-cache",
        				"Content-Lenght: " . strlen(json_encode($data))
        		));
        return $this->execute("/rest/{$this->storeView}/V1/eav/attribute-sets", $opts, $data, false);
        
    }
    
    public function catalogProductAttributeSetAttributeAdd(){
        
        if(!isset($this->attribute_code)){
            
            return array();
        }
        
        $params = array(
        		'attributeCode' => $this->attribute_code,
        		'attributeGroupId' => $this->attribute_group_id,
        		'attributeSetId' => $this->attribute_set_id,
        		'sortOrder' => 99
        		
        );
        $response = $this->post("/rest/{$this->storeView}/V1/products/attribute-sets/attributes", $params);
        
        return $response;
        
    }
    
    
    
    public function catalogSetAttributesGroups(){
    	if(!isset($this->filters)){
    		return array();
    	}
    	 
    	$this->MakeCriteria();
    
    	return $this->get("/rest/{$this->storeView}/V1/products/attribute-sets/groups/list", $this->searchCriteria);
    
    }
    public function catalogSetAttributesGroupsAdd(){
    	
    	if(!isset($this->attribute_set_id)){
    		return array();
    	}
    	$params = array('group' => array(
    			'attribute_group_id' => 'sysplace',
    			'attribute_group_name' => 'Sysplace',
    			'attribute_set_id' => $this->attribute_set_id,
    		)
    	
    	);
    
    	return $this->post("/rest/{$this->storeView}/V1/products/attribute-sets/groups", $params);
    
    }
    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
    
    
    
}
?>