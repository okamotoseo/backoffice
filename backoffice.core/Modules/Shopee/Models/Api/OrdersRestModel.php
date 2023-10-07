<?php

class OrdersRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $id;
    
    
    
    
    
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
    
    
    
    function getOrders(){
        
        $result = $this->tray->get ( '/orders', array (
            'access_token' => $this->access_token,
            "sort" => "id_desc",  
            "limit" => "50"
        ));
        
        return $result;
        
    }
    
    function getCompleteOrder(){
        
        if(!isset($this->id)){
            return array();
        }
        
        $result = $this->tray->get ( "/orders/{$this->id}/complete", array (
            'access_token' => $this->access_token
        ));
        
        return $result;
        
    }
    
    
    
}

?>