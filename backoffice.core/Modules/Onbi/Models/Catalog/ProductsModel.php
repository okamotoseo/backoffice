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
	
	public $storeView = 'default';
	
	public $categoriesInfo = array();
	
	public $categoriesHierarchy;
	
	public $filters = array(
	    "type" => "simple,configurable", 
	    "status" => 1,
	);
	
	public $complexFilter;
	
	public $product_configurable_id;
	
// 	"sku" => "3M-5369T-20M",
// 	"sku2" => "ALCANCE-QUIMICA-NURA-700ML"
	public $associated_ids;
	
	public $configurable_attributes = array();

	
	

// 	"sku" => "3M-5369T-20M"
	
// 	'sku' => 'ALC-LIMPA-VIDROS-FUNCIONAL-700ML'

	
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
	
    
//     public function getFilterProduct(){
        
//         $complexFilter = array(
//             'complex_filter' => array(
//                 array(
//                     'key' => 'type',
//                     'value' => array('key' => 'in', 'value' => 'simple,configurable')
//                 ),
//                 array(
//                     'key' => 'status',
//                     'value' => array('key' => 'eq', 'value' => '1')
//                 )
//             )
//         );
        
//         return $complexFilter;
        
//     }
    
    public function getFilterProduct(){
        //141.386.667-02
        //'083.867.019-94'
        $this->complexFilter = array();
        
        foreach($this->filters as $key => $value){
            
            switch($key){
                
                case "status" :
                    
                    $this->complexFilter['complex_filter'][] = array(
                        'key' => $key,
                        'value' => array('key' => 'eq', 'value' => $value)
                    );
                    
                    break;
                    
                case "type" :
                    
                    $this->complexFilter['complex_filter'][] = array(
                        'key' => $key,
                        'value' => array('key' => 'in', 'value' => $value)
                    );
                    
                    break;
                    
                case "sku" :
                    
                    $this->complexFilter['complex_filter'][] = array(
                        'key' => $key,
                        'value' => array('key' => 'eq', 'value' => $value)
                    );
                    
                    
                    break;
                
                    
                    
            }
            
            
        }
//         pre($this->complexFilter);die;
        return $this->complexFilter;
        
    }
    
    public function catalogProductList(){
        
        
        $this->getFilterProduct();
        
        $response = $this->soapClient->catalogProductList($this->session_id, $this->complexFilter, $this->storeView);

        return $response;
        
    }
    
    public function catalogProductInfo(){
        
        if(!isset($this->product_id)){
            return array();
        }
        $response = $this->soapClient->catalogProductInfo($this->session_id, $this->product_id, $this->storeView, $this->additional_attributes);
        
        return $response;
        
    }
    
    public function catalogProductInfoSku(){
    
    	if(!isset($this->sku)){
    		return array();
    	}
    	$response = $this->soapClient->catalogProductInfo($this->session_id, $this->sku, $this->storeView);
    
    	return $response;
    
    }
    
    public function catalogProductListOfAdditionalAttributes(){
        
        if(!isset($this->set_id)){
            return array();
        }
        
        $response = $this->soapClient->catalogProductListOfAdditionalAttributes($this->session_id, $this->type,  $this->set_id);
        
        return $response;
        
    }
    
    public function catalogProductUpdate($Entity){
        if(empty($this->product_id)){
            return array();
        }
        
        $data = array($this->session_id,
        		$this->product_id,
        		(array) $Entity,
        		$this->storeView);
        
        $result = $this->soapClient->catalogProductUpdate(
        		$this->session_id,
        		$this->product_id,
        		(array) $Entity,
        		$this->storeView
        		);
        
        
        return $result;
    }
    
    public function catalogProductCreate($Entity){
        

        $result = $this->soapClient->catalogProductCreate(
                $this->session_id, 
                $this->type, 
                $this->set_id, 
                "{$this->sku}", 
                (array) $Entity,
                $this->storeView
                
            );
        
        return $result;
    }
    
    
    public function catalogProductTypeConfigurableAssign(){
        
        
        $result = $this->soapClient->catalogProductTypeConfigurableAssign(
            $this->session_id,
            $this->product_configurable_id,
            $this->associated_ids,
            array('color', 'voltagem')
            );
        
        return $result;
    }
    
    public function catalogProductDelete(){
        
        if(empty($this->product_id)){
            return array();
        }
        
        $result = $this->soapClient->catalogProductDelete( $this->session_id, $this->product_id );
        
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