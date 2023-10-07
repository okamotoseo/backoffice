<?php 

class AttributesModel extends REST
{

	/**
	 * @var string
	 * Class Unique ID
	 */

	public $attribute_id;
	
	public $attribute_code;
	
	public $attribute_set_id;
	
	public $attributes;
	
	public $attributesInfo = array();
	
	public $attribute_data = array();
	
	public $option = array();
	
	public $filters = array();
	
	public $searchCriteria = array();
	

	
	
	
	
	
	
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
	
	
	public function getAttributes(){
		
		if(!isset($this->filters)){
			return array();		
		}
	
		$this->MakeCriteria();
	
		$opts = array(
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_POSTFIELDS => json_encode($this->searchCriteria),
				CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer {$this->token}",
						"Content-Type: application/json",
						"cache-control: no-cache",
						"Content-Lenght: " . strlen(json_encode($params))
				));
		return $this->execute('/rest/V1/eav/attribute-sets/list', $opts, $this->searchCriteria, false);
	
	}
	
	
	
	/************************************* OLD *************************************/
	/************************************* OLD *************************************/
	/************************************* OLD *************************************/
	
    
//     public function catalogProductAttributeSetList(){
    	
        
//     	if(!isset($this->filters)){
//     		return array();
//     	}
    	
//     	$this->MakeCriteria();
    	
//     	$opts = array(
//     			CURLOPT_CUSTOMREQUEST => "GET",
//     			CURLOPT_POSTFIELDS => json_encode($this->searchCriteria),
//     			CURLOPT_HTTPHEADER => array(
//     					"Authorization: Bearer {$this->token}",
//     					"Content-Type: application/json",
//     					"cache-control: no-cache",
//     					"Content-Lenght: " . strlen(json_encode($this->searchCriteria))
//     			));
//     	return $this->execute("/rest/{$this->storeView}/V1/products/attribute-sets/sets/list", $opts, $this->searchCriteria, false);
        
//     }
    
    public function catalogProductAttributeList(){
        
       if(!isset($this->attribute_set_id)){
            return array();
       }
        
       $params = array();
        
       return $this->get("/rest/{$this->storeView}/V1/products/attribute-sets/{$this->attribute_set_id}/attributes",  $params);
        
    }
    
    
    
    public function catalogProductAttributeInfo(){
        
        if(!isset($this->attribute_code)){
            return array();
        }
        
        $params = array();
        return $this->get("/rest/{$this->storeView}/V1/products/attributes/{$this->attribute_code}", $params);
        
    }
    
    public function catalogProductAttributeOptions(){
    	
        if(!isset($this->attribute_code)){
            
            return array();
            
        }
        $params = array();
        
        return $this->get("/rest/{$this->storeView}/V1/products/attributes/{$this->attribute_code}/options", $params);
        
    }
    public function catalogConfigurableProductAttributeOptionsAll(){
    	 
    	if(!isset($this->parentSku)){
    
    		return array();
    
    	}
    	$params = array();
    
    	return $this->get("/rest/{$this->storeView}/V1/configurable-products/{$this->parentSku}/options/all", $params);
    
    }
    
    
    public function getProductsAttributes(){
    	
    	if(!isset($this->attribute_code)){
    		return array();
    	}
    
    	$params = array();
    	
    	return $this->get("/rest/{$this->storeView}/V1/products/attributes/{$this->attribute_code}", $params);
    
    }
    
    
//     public function catalogProductAttributeRemove(){
        
//         if(!isset($this->attribute_id)){
            
//             return array();
            
//         }
        
//         $response = $this->soapClient->catalogProductAttributeRemove($this->session_id, $this->attribute_id);
        
//         return $response;
        
//     }
    
    public function catalogProductAttributeCreate(){
        
        if(!isset($this->attribute_data)){
            return array();
        }
        
        $response = $this->post("/rest/{$this->storeView}/V1/products/attributes", $this->attribute_data);
        
        return $response;
        
    }
    
    
    public function catalogProductAttributeAddOption(){
        
        if(!isset($this->attribute_code)){
            
            return array();
            
        }
        
        $opts = array(
        		CURLOPT_CUSTOMREQUEST => "POST",
        		CURLOPT_POSTFIELDS => json_encode($this->attribute_option),
        		CURLOPT_HTTPHEADER => array(
        				"Authorization: Bearer {$this->token}",
        				"Content-Type: application/json",
        				"cache-control: no-cache",
        				"Content-Lenght: " . strlen(json_encode($this->attribute_option))
        						));
        
        return $this->execute("/rest/{$this->storeView}/V1/products/attributes/{$this->attribute_code}/options", $opts);
        
    }
    
//     /************************************************************************************************/
//     /************************************** Custom **************************************************/
//     /************************************************************************************************/
    
    
    public function ListCatalogProductAttributeInfo(){
    	$this->attributesInfo = array();
        $attributes = $this->LisAttributes();die;
        pre($attributes);die;
        if(isset($attributes[0])){
	        foreach($attributes[0] as $key => $attr){
	            $this->attributesInfo[] = $attr;
// 	            $this->attributesInfo[$attr->attribute_id][] = $this->catalogProductAttributeInfo();
// 	            pre($this->attributesInfo);
// 	            die;
	        
	        }
        }
        
        return $this->attributesInfo;
        
    }
    
//     public function GetCatalogProductAttribute()
//     {
        
//         $additionalAttributes['additional_attributes'] = array();
        
//         $attributes = $this->ListCatalogProductAttributeInfo();
        
//         foreach($attributes as $key => $attr){
// //             pre($attr);die;
//             if(isset($attr->frontend_label[0]->label)){
                
//                 $additionalAttributes['additional_attributes'][] = $attr->attribute_code;
                
//             }
            
//         }
        
//         return $additionalAttributes;
        
//     }
    
    
}
?>