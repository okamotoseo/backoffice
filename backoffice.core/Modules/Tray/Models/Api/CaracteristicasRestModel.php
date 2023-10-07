<?php

class CaracteristicasRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $product_id;
    
    public $cateristica_id;
    
    public $name;
    
    public $category;
    
    public $caracteristicaData = array();
    
    public $dataFilter = array (
    		"sort"=>'id_desc'
    
    );

//     public $dataFilter = array();
    
    
    
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
    
    function postCaracteristica(){
        
        if(!isset($this->product_id)){
            return array();
        }
        
        $result = $this->tray->post ( "/products/{$this->product_id}/properties", $this->caracteristicaData, array ('access_token' => $this->access_token));
        
        return $result;
        
    }
    
    
    public function getCaracteristica(){
        
        if(empty($this->dataFilter)){
        	return;
        }else{
        	$this->dataFilter['access_token'] = $this->access_token;
        }
          
        $result = $this->tray->get("/products/properties", $this->dataFilter);
        
        return $result;
    }
    
    
/************************************************************************************************/
/************************************** Custom **************************************************/
/************************************************************************************************/
 
    
    
    public function getCaracteristicaIs(){
        
    }
    
    
    
}

?>