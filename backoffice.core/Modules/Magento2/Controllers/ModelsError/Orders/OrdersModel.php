<?php 

class OrdersModel extends MainModel
{


	public $store_id;




	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->store_id = $this->controller->userdata['store_id'];
	    
	    
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
	
	
	public function ListOrders(){
		
		
		$soapClient = new Soap($this->db, $this->store_id);
		
		$res = $soapClient->getOrderInfo();
		
		return $res;
		
		
	}
	
	
	
}

?>