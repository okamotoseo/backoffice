<?php

class ItemsRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $category_id;
    
    public $item_id;
    
    public $id_product;
    
    public $productData = array();
    
    public $productVariantData = array();
    
    public $dataFilter = array();
    
    public $variation_id;
    
    
    

    
    
    
    
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
    
    
    
    function getProducts(){
        
        if(empty($this->dataFilter)){
            return;
        }else{
            $this->dataFilter['access_token'] = $this->access_token;
        }
        
        $result = $this->tray->get ( '/products', $this->dataFilter);
        
        return $result;
        
    }
    function getProduct(){
        
   
        $this->dataFilter['access_token'] = $this->access_token;
        
        $result = $this->tray->get ( "/products/{$this->id_product}", $this->dataFilter);
        
        return $result;
        
    }
    
    function postProduct(){
        
        if(!isset($this->productData)){
            return array();
        }
        
        $result = $this->tray->post ( '/products', $this->productData, array ('access_token' => $this->access_token));
        
        return $result;
        
    }
    
    
    function postProductVariation(){
        
        if(!isset($this->productVariantData)){
            return array();
        }
        
        $result = $this->tray->post ( '/products/variants/', $this->productVariantData, array ('access_token' => $this->access_token));
        
        return $result;
        
    }
    
    public function putProduct(){
        
        if(!isset($this->id_product)){
            return array();
        }
        
        $result = $this->tray->put("/products/{$this->id_product}", $this->productData, array ('access_token' => $this->access_token));
        
        return $result;
    }
    
    public function putProductVariation(){
        
        if(!isset($this->variation_id)){
            return array();
        }
        
        $result = $this->tray->put("/products/variants/{$this->variation_id}", $this->productVariantData, array ('access_token' => $this->access_token));
        
        return $result;
    }
    
    public function deleteProduct(){
        
        if(!isset($this->id_product)){
            return array();
        }
        
        //         delete($path, $params)
        $result = $this->tray->delete("/products/{$this->id_product}", array ('access_token' => $this->access_token));
        
        return $result;
    }
    public function deleteVariations(){
        
        if(!isset($this->variation_id)){
            return array();
        }
        
        //         delete($path, $params)
        $result = $this->tray->delete("/products/variants/{$this->variation_id}", array ('access_token' => $this->access_token));
        
        return $result;
    }
    
    
    
    
   
    
}

?>