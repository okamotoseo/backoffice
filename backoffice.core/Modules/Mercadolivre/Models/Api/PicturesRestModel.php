<?php

class PicturesRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $product_id;
    
    public $item_id;
    
    public $picture_id;
    
    public $picture;
    
    public $pictures_assoc;
    
    
    
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
    
    public function postPicturesAssoc(){
        
        if(!isset($this->item_id)){
            return array();
        }
        $result = $this->meli->post ( "/items/{$this->item_id}/pictures", $this->pictures_assoc, array (
            'access_token' => $this->access_token
        ));
        
        return $result;
        
    }
    
    public function postPictures(){
        
        if(!isset($this->picture)){
            return array();
        }
 
        $result = $this->meli->postPicture('/pictures/items/upload', $this->picture, array (
            'access_token' => $this->access_token
        ));
        
        return $result;
        
    }
    
   
    
   public function getPictureErrors(){
        
        if(!isset($this->picture_id)){
            return array();
        }
        $result = $this->meli->get ( "/pictures/MLB{$this->picture_id}/errors", array (
            'access_token' => $this->access_token ));
        
        return $result;
        
    }
   
    
   public function putItemPictures(){
        
        if(!isset($this->item_id)){
            return array();
        }
        
        $result = $this->meli->put ( "/items/{$this->item_id}", $this->pictures, array (
            'access_token' => $this->access_token
        ));
        
        return $result;
        
    }
    
    
    
    
}

?>