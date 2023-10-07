<?php 

class AddressModel extends Soap
{

	public $customer_id;
	
	public $address_id;
    




	
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
	

    
    public function customerAddressList(){
        
        
        if(!isset($this->customer_id)){
            return array();
        }
        
        $response = $this->soapClient->customerAddressList($this->session_id, $this->customer_id);
        
        return $response;
        
    }
    
    public function customerAddressInfo(){
        
        if(!isset($this->address_id)){
            return array();
        }
        $response = $this->soapClient->customerAddressInfo($this->session_id, $this->address_id);
        
        return $response;
        
    }
    
   
    
    public function customerAddressCreate($addressEntity){
        
        if(empty($addressEntity)){
            
            return array();
        }

        $response = $this->soapClient->customerAddressCreate( $this->session_id, (array)$addressEntity );
        
        return $response;
    }

    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/

    
    
}
?>