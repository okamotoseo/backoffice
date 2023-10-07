<?php 

class CustomerModel extends Soap
{


	public $customer_id;
    
	public $attributes;
	
	public $complexFilter = array();
	
	




	
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
	

    
    public function getFilterCustomer($filter = array()){
        //141.386.667-02
        //'083.867.019-94'
        foreach($filter as $key => $value){
            
            switch($key){
                
                case "taxvat" :
                    
                    $this->complexFilter['complex_filter'][] = array(
                        'key' => $key,
                        'value' => array('key' => 'eq', 'value' => $value)
                    );
                    
                    break;
                    
                    
            }
            
            
        }
        
        return $this->complexFilter;
        
    }

    
    public function customerCustomerList(){
        
        
        $complexFilter = $this->getFilterCustomer();
        
        $response = $this->soapClient->customerCustomerList($this->session_id, $this->complexFilter);
        
        return $response;
        
    }
    
    public function customerCustomerInfo(){
        
        if(!isset($this->customer_id)){
            return array();
        }
        $response = $this->soapClient->customerCustomerInfo($this->session_id, $this->customer_id,  $this->attributes);
        
        return $response;
        
    }
    
   
    
    public function customerCustomerCreate($customerEntity){
        
        if(empty($customerEntity)){
            return array();
        }

        $response = $this->soapClient->customerCustomerCreate( $this->session_id, (array)$customerEntity );
        
        return $response;
    }

    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/

    
    
}
?>