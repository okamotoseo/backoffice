<?php

class ItemsRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $category_id;
    
    public $item_id;
    
    public $item = array();

    
    
    
    
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
    
    function postItem(){
        
        if(!isset($this->item)){
            return array();
        }
        $result = $this->meli->post ( '/items', $this->item, array (
            'access_token' => $this->access_token
        ));
        
        return $result;

    }
    
    function putItem(){
        
        if(!isset($this->item_id)){
            return array();
        }
        $result = $this->meli->put ( "/items/MLB{$this->item_id}", $this->item, array (
            'access_token' => $this->access_token
        ));
        return $result;
        
    }
    
    function getItem(){
        
        if(!isset($this->item_id)){
            return array();
        }
        $result = $this->meli->get ( "/items/MLB{$this->item_id}", array (
            'access_token' => $this->access_token ));
        
        return $result;
        
    }
    
    function getItemVariations(){
        
        if(!isset($this->item_id)){
            return array();
        }
        $result = $this->meli->get ( "/items/MLB{$this->item_id}/variations", array (
            'access_token' => $this->access_token ));
        
        return $result;
        
    }
    
    
}

?>