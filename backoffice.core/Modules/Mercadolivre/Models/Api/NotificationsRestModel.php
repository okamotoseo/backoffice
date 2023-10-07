<?php

class NotificationsRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $app_id;
    
    public $site_id = 'MLB';
    

    
    
    
    
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
   
    
    function getNotifications(){
        
        if(!isset($this->app_id)){
            return array();
        }
        $result = $this->meli->get ( "/missed_feeds/", array (
        	'app_id' => $this->app_id,
            'access_token' => $this->access_token
        ));
        
        return $result;
        
    }
    
    
}

?>