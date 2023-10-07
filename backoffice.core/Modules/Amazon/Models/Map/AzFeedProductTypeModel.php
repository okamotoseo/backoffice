<?php 

class AzFeedProductTypeModel extends MainModel
{
    
    public $id;
    
    public $tree_id;
	
	public $feed_product_type;
	
	public $attribute;
	
	public $value;
	
	public $name;
	
	public $created;
	
	public $category;
	
	private $attributes = array();

	
	
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
	}
	
	
	public function GetFeedProductTypeValues(){
		
		if(empty($this->tree_id)){
			return array();
		}
		
		$sqlProductType = "SELECT * FROM `az_feed_product_type` WHERE attribute LIKE 'recommended_browse_nodes' AND `value` LIKE '{$this->tree_id}'";
		$queryProductType = $this->db->query($sqlProductType);
		$resProductType = $queryProductType->fetch(PDO::FETCH_ASSOC);
		$sql = "SELECT * FROM `az_feed_product_type` WHERE feed_product_type LIKE '{$resProductType['feed_product_type']}' AND attribute != 'recommended_browse_nodes' GROUP BY value";
		$query = $this->db->query($sql);
		$feedProductsType = $query->fetchAll(PDO::FETCH_ASSOC);
		foreach($feedProductsType as $key => $productType){
			
			if(!isset($this->attributes[$productType['attribute']]['attribute'])){
				$translateInfo = translate($this->db, $productType['attribute'], $this->store_id);
				$this->attributes[$productType['attribute']] = array(
								'name' => $productType['name'],
								'attribute' => $productType['attribute'],
								'label' => $translateInfo['translate'],
								'placeholder' => $translateInfo['exemple'],
								'alias' => $translateInfo['alias'],
								'required' => $translateInfo['required'],
								'description' => $translateInfo['description']
			
						);
			}
				
			$this->attributes[$productType['attribute']]['values'][] = $productType['value'];
			
		}
		
		return $this->attributes;
	
	}
	
	
}

?>