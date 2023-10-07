<?php 

class CartModel extends Soap
{

	/**
	 * @var int
	 * Class Unique ID
	 */

	public $cart_id;
	
	public $storeView;
	
	public $licence;
	
	public $totals;
    
	
	
	




	
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
	
    
   
	public function shoppingCartInfo(){
	    
	    if(!isset($this->cart_id)){
	        
	        return array();
	    }
	    
	    $response = $this->soapClient->shoppingCartInfo($this->session_id, $this->cart_id, $this->storeView);
	    
	    return $response;
	    
	}
	
	public function shoppingCartTotals(){
	    
	    if(!isset($this->cart_id)){
	        
	        return array();
	    }
	    
	    $response = $this->soapClient->shoppingCartTotals($this->session_id, $this->cart_id, $this->storeView);
	    
	    return $response;
	    
	}
	
	public function shoppingCartLicense(){
	    
	    if(!isset($this->cart_id)){
	        
	        return array();
	    }
	    
	    $response = $this->soapClient->shoppingCartLicense($this->session_id, $this->cart_id, $this->storeView);
	    
	    return $response;
	    
	}
	
    public function shoppingCartCreate(){

        $response = $this->soapClient->shoppingCartCreate($this->session_id, $this->storeView);
        
        return $response;
    }

    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
}
    
?>