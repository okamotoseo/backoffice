<?php 

class AttributesModel extends Soap
{

	/**
	 * @var string
	 * Class Unique ID
	 */

	public $attribute_id;
	
	public $set_id;
	
	public $attributes;
	
	public $attributesInfo = array();
	
	public $attribute_data = array();
	
	public $attribute_option = array();
	

	

	
	
	
	
	
	
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
	
    
    public function catalogProductAttributeSetList(){
        
        $response = $this->soapClient->catalogProductAttributeSetList($this->session_id);
        
        return $response;
        
    }
    
    public function catalogProductAttributeList(){
        
        if(!isset($this->set_id)){
            return array();
        }
        
        $response = $this->soapClient->catalogProductAttributeList($this->session_id, $this->set_id);
        
        return $response;
        
    }
    
    
    
    public function catalogProductAttributeInfo(){
        
        if(!isset($this->attribute_id)){
            
            return array();
            
        }
        
        $response = $this->soapClient->catalogProductAttributeInfo($this->session_id, $this->attribute_id);

        
        return $response;
        
    }
    
    public function catalogProductAttributeOptions(){
        
        if(!isset($this->attribute_id)){
            
            return array();
            
        }
        
        $response = $this->soapClient->catalogProductAttributeOptions($this->session_id, $this->attribute_id);
        
        
        return $response;
        
    }
    
    public function catalogProductAttributeRemove(){
        
        if(!isset($this->attribute_id)){
            
            return array();
            
        }
        
        $response = $this->soapClient->catalogProductAttributeRemove($this->session_id, $this->attribute_id);
        
        return $response;
        
    }
    
    public function catalogProductAttributeCreate(){
        
        if(!isset($this->attribute_data)){
            
            return array();
            
        }
        
        $response = $this->soapClient->catalogProductAttributeCreate($this->session_id, $this->attribute_data);
        
        return $response;
        
    }
    
    
    public function catalogProductAttributeAddOption(){
        
        if(!isset($this->attribute_id)){
            
            return array();
            
        }
        
        $response = $this->soapClient->catalogProductAttributeAddOption($this->session_id, $this->attribute_id, $this->attribute_option);
        
        return $response;
        
    }
    
    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
    
    public function LisAttributes(){
        
        $attributeSet = $this->catalogProductAttributeSetList();
        $attributesGroups = array();
        foreach($attributeSet as $key => $setAttr){
            $this->set_id = $setAttr->set_id;
        
           $attributesGroups =  $this->catalogProductAttributeList();
           
           foreach($attributesGroups as $key => $value){

               if(isset($this->attributes)){

                   $exist = false;
                   foreach($this->attributes as $ind => $attr){
                       
                       if($attr->attribute_id == $value->attribute_id){
                           $exist = true;
                       }
                       
                   }
                   if(!$exist){
                       $this->attributes[] = $value;
                   }
               }else{
                   $this->attributes[] = $value;
               }
               
           }
            
        }
        return $this->attributes;
    }
    
    
    public function ListCatalogProductAttributeInfo(){
        
        $attributes = $this->LisAttributes();
        
        foreach($attributes as $key => $attr){
            
            $this->attribute_id = $attr->attribute_id;
            
            $this->attributesInfo[] = $this->catalogProductAttributeInfo();
        
        }
        
        return $this->attributesInfo;
        
    }
    
    public function GetCatalogProductAttribute()
    {
        
        $additionalAttributes['additional_attributes'] = array();
        
        $attributes = $this->ListCatalogProductAttributeInfo();
        
        foreach($attributes as $key => $attr){
//             pre($attr);die;
            if(isset($attr->frontend_label[0]->label)){
                
                $additionalAttributes['additional_attributes'][] = $attr->attribute_code;
                
            }
            
        }
        
        return $additionalAttributes;
        
    }
    
    
}
?>