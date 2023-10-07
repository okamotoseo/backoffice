<?php

class ShipmentsRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $shipment_id;
    
    public $site_id = 'MLB';
    
    public $category_id;
    
    public $dataToGetShipping = array();
    

    
    
    
    
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
   
    
    function getShipment(){
        
        if(!isset($this->shipment_id)){
            return array();
        }
        $result = $this->meli->get ( "/shipments/{$this->shipment_id}", array (
            'access_token' => $this->access_token
        ));
        
        return $result;
        
    }
    
    function getShippingPrice($dataToGetShipping){
//     pre($dataToGetShipping);
    	if(empty($dataToGetShipping['mlb_category_id'])){
    		return array();
    	}
    	 $path = "/users/{$this->seller_id}/shipping_options/free?currency_id=BRL&listing_type_id=gold_pro".
    	"&condition=new&category_id={$dataToGetShipping['mlb_category_id']}&item_price={$dataToGetShipping['mlb_item_price']}".
    	"&verbose=true&dimensions={$dataToGetShipping['mlb_item_altura']}x{$dataToGetShipping['mlb_item_largura']}x{$dataToGetShipping['mlb_item_profundidade']},{$dataToGetShipping['mlb_item_peso']}";
    	$result = $this->meli->get ( $path );
    	
    	return $result;
    
    }
    
    function getShippingReference(){
    	//     pre($dataToGetShipping);
    	if(empty($this->category_id)){
    		return array();
    	}
    	$path = "/categories/{$this->category_id}/shipping_preferences";
    	$result = $this->meli->get ( $path );
    	 
    	return $result;
    
    }
    
    
    
}

?>