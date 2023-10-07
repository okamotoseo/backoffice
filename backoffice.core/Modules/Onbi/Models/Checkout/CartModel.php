<?php 

class CartModel extends Soap
{

	/**
	 * @var int
	 * Class Unique ID
	 */

	public $cart_id;
	
	public $storeView = null;
	
	public $licence;
	
	public $totals;
	
	public $customer = array();
	
	public $product = array();
	
	public $marketplace;
	
	public $paymentMethod;
	
	public $shippingMethod;
	
	public $payment;
	
	public $order_id;
    
	
	
	




	
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

        $this->cart_id = $this->soapClient->shoppingCartCreate($this->session_id, $this->storeView);
        
        return $this->cart_id;
    }

    public function shoppingCartCustomerSet(){
        
        if(!isset($this->customer)){
            return array();
        }
        
        $response = $this->soapClient->shoppingCartCustomerSet($this->session_id, $this->cart_id, $this->customer);
        
        return $response;
    }
    
    public function shoppingCartProductAdd(){
        
        if(!isset($this->product)){
            return array();
        }
        
        $response = $this->soapClient->shoppingCartProductAdd($this->session_id, $this->cart_id, array($this->product));

        return $response;
    }
    
    public function shoppingCartCustomerAddresses($address){
        
        if(!isset($address)){
            return array();
        }

        $response = $this->soapClient->shoppingCartCustomerAddresses($this->session_id, $this->cart_id, (array)$address);
//         pre($response);
        return $response;
    }
    
    public function shoppingCartPaymentList(){
        
        if(!isset($this->cart_id)){
            
            return array();
        }
       
        $response = $this->soapClient->shoppingCartPaymentList($this->session_id, $this->cart_id);
        
        return $response;
    }
    
    public function shoppingCartPaymentMethod(){
        
        if(!isset($this->payment)){
            return array();
        }
        
        $response = $this->soapClient->shoppingCartPaymentMethod($this->session_id, $this->cart_id, $this->payment);
        
        return $response;
    }
    
    public function shoppingCartShippingList(){
        
        if(!isset($this->cart_id)){
            
            return array();
        }
        
        $response = $this->soapClient->shoppingCartShippingList($this->session_id, $this->cart_id);
        
        return $response;
    }
    
    public function shoppingCartShippingMethod(){
        
        if(!isset($this->shippingMethod)){
            return array();
        }
        
        $response = $this->soapClient->shoppingCartShippingMethod($this->session_id, $this->cart_id, $this->shippingMethod);
        
        return $response;
    }
    
    public function shoppingCartOrder(){
        
        if(!isset($this->cart_id)){
            return array();
        }
        
        $this->order_id = $this->soapClient->shoppingCartOrder($this->session_id, $this->cart_id, null, null);

        return $this->order_id;
    }
    
    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
    public function GetPaymentMethodCodeFromMarketplace(){
        
        if(!isset($this->marketplace)){
            
            return array();
        }
        
        $methods = $this->shoppingCartPaymentList();
        
        foreach($methods as $key => $method){
            
            if(strtolower($this->marketplace) == strtolower($method->title)){
                $this->paymentMethod = $method->code;
                break;
            }
        }
        
        if(empty($this->paymentMethod)){
            
            
            $method = $methods[0];
            $this->paymentMethod = $method->code;
            
        }
        
        return $method;
        
    }
    
    public function GetShippingMethodCodeFromMarketplace(){
        
        if(!isset($this->marketplace)){
            
            return array();
        }
        
        $methods = $this->shoppingCartShippingList();
        
        if(empty($methods)){
            
            return array();
        }
        
        foreach($methods as $key => $method){
            
            if(strtolower($this->marketplace) == strtolower($method->carrier)){
                $this->shippingMethod = $method->code;
                break;
            }
        }
        
        if(empty($this->shippingMethod)){
            
            
            $method = $methods[0];
            $this->shippingMethod = $method->code;
            
        }
        
        return $method;
        
    }
}
    
?>