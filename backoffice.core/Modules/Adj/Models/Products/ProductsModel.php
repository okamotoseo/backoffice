<?php 

class ProductsModel extends MainModel
{

	/**
	 * @var string
	 * Class Unique ID
	 */
	public $store_id;

	/**
	 * @var string
	 */
	public $dataUpdate;

	/**
	 * @var string
	 */
	public $description;


	
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
	                	if($property == "dataUpdate"){
	                		$this->{$property} = date( 'Y-m-d', strtotime($value));
	                	}else{
	                    	$this->{$property} = $value;
	                	}
	                    
	                }
            	}
                
            }
            
            return true;
            
        } else {
        	
            return;
            
        }
        
	    
	}
	
	
	public function ListProducts(){
		
		
		$params = array_filter(array(
				'@xdata.type' => 'XData.Default.DTOProduto', 
				'descricao' => $this->description, 
				'dataUpdate' => $this->dataUpdate
		));
		$adj = new Adj($this->db, $this->store_id);
// 		pre($adj);
		$products = $adj->Products($params);
		pre($products);die;
		return $products;
		
		
	}
	
	
	
}

?>