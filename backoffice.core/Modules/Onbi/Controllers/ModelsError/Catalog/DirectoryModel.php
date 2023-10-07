<?php 

class ProductsModel extends Soap
{

	/**
	 * @var string
	 * Class Unique ID
	 */

	public $product_id;
    
	public $sku;
	
	public $set_id;
	
	public $type = 'simple';
	
	public $attributes;
	
	public $additional_attributes;
	
	public $categories;
	
	public $storeView;
	
	public $categoriesInfo = array();
	
	public $categoriesHierarchy;
	
	




	
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
	
    
    public function getFilterProduct(){
        
        $complexFilter = array(
            'complex_filter' => array(
                array(
                    'key' => 'type',
                    'value' => array('key' => 'in', 'value' => 'simple,configurable')
                ),
                array(
                    'key' => 'status',
                    'value' => array('key' => 'eq', 'value' => '1')
                )
            )
        );
        
        return $complexFilter;
        
    }
    
    public function catalogProductList(){
        
        
        $complexFilter = $this->getFilterProduct();
        
        $response = $this->soapClient->catalogProductList($this->session_id, $complexFilter);
        
        return $response;
        
    }
    
    public function catalogProductInfo(){
        
        if(!isset($this->product_id)){
            return array();
        }
        $response = $this->soapClient->catalogProductInfo($this->session_id, $this->product_id, $this->storeView, $this->additional_attributes);
        
        return $response;
        
    }
    
    public function catalogProductListOfAdditionalAttributes(){
        
        if(!isset($this->set_id)){
            return array();
        }
        
        $response = $this->soapClient->catalogProductListOfAdditionalAttributes($this->session_id, $this->type,  $this->set_id);
        
        return $response;
        
    }
    
    public function catalogProductUpdate($productEntity){
        
        if(empty($this->sku)){
            return array();
        }
        
        $result = $this->soapClient->catalogProductUpdate(
                $this->session_id, 
                $this->sku, 
                (array) $productEntity, 
                $this->storeView, 
                'SKU'
            );
        
        return $result;
    }
    
    public function catalogProductCreate($productEntity){

        $result = $this->soapClient->catalogProductCreate(
                $this->session_id, 
                $this->type, 
                $this->set_id, 
                "{$this->sku}", 
                (array) $productEntity
            );
        
        return $result;
    }

    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
    public function ListProducts(){
        
        
        $complexFilter = array(
            'complex_filter' => array(
                array(
                    'key' => 'type',
                    'value' => array('key' => 'in', 'value' => 'simple,configurable')
                )
            )
        );
        $productsIds = $this->catalogProductList();
        
        foreach($productsIds as $key => $productId){
            
            $this->product_id = $productId->product_id;
            $products[] = $this->catalogProductInfo();
            
        }
        return $products;
        
        
    }
    
    
}
?>