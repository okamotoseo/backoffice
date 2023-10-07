<?php 

class ProductsModel extends REST
{

	public $store_id;
	
	public $response;
	
	public $product_id;
	
	public $sku;
	
	public $parentSku;
	
	public $childSku;
	
	public $attribute_set_id;
	
	public $type_id;
	
	public $attributes;
	
	public $additional_attributes;
	
	public $categoriesHierarchy;
	
	public $filters;
	
	public $product_configurable_id;
	
	public $associated_ids;
	
	public $categoriesInfo = array();
	
	public $configurable_attributes = array();
	
	public $product;
	
	public $option;
	
	
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
	
	
	/**
	 *	CONDITION	NOTES
	 *	eq			Equals.
	 *	finset		A value within a set of values
	 *	from		The beginning of a range. Must be used with to
	 *	gt			Greater than
	 *	gteq		Greater than or equal
	 *	in			In. The value can contain a comma-separated list of values.
	 *	like		Like. The value can contain the SQL wildcard characters when like is specified.
	 *	lt			Less than
	 *	lteq		Less than or equal
	 *	moreq		More or equal
	 *	neq			Not equal
	 *	nfinset		A value that is not within a set of values
	 *	nin			Not in. The value can contain a comma-separated list of values.
	 *	notnull		Not null
	 *	null		Null
	 *	to			The end of a range. Must be used with from
	 */
	
	/************************************************************************************************/
	/************************************** Custom **************************************************/
	/************************************************************************************************/
	
	public function getProducts(){
		if(!isset($this->filters)){
			return array();
		}
		
		$this->MakeCriteria();
		
		$opts = array(
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_POSTFIELDS => json_encode($this->searchCriteria),
				CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer {$this->token}",
						"Content-Type: application/json",
						"cache-control: no-cache",
						"Content-Lenght: " . strlen(json_encode($params))
								));
		return $this->execute("/rest/{$this->storeView}/V1/products", $opts, $this->searchCriteria);
		
	}
	
	public function catalogProductCreate(){
		
		if(!isset($this->product)){
			return array();
		}
	
		
		return $this->post("/rest/{$this->storeView}/V1/products", $this->product);
	}
	
	public function catalogProductUpdate($Entity){
		
		if(empty($this->sku)){
			return array();
		}
	
		$data['product'] = $Entity;
	
	
		return $this->put("/rest/{$this->storeView}/V1/products/{$this->sku}", $data);
	}
	
	public function catalogConfigurableProductsOptions(){
	
	
		if(!isset($this->parentSku)){
			return array();
		}
		return $this->post("/rest/{$this->storeView}/V1/configurable-products/{$this->parentSku}/options", $this->option);
	
	}
	
	public function catalogConfigurableProductsChild(){
		

		if(!isset($this->parentSku)){
			return array();
		}
		
		
		return $this->post("/rest/{$this->storeView}/V1/configurable-products/{$this->parentSku}/child", array('childSku' => "{$this->childSku}"));
		
	}
	
	public function catalogProductDelete(){
	
		if(!isset($this->sku)){
			return array();
		}
		
		$params = array();
		
		return $this->delete("/rest/{$this->storeView}/V1/products/{$this->sku}/", $params);
	
	
	}
    
}
?>