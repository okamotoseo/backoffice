<?php 

class CategoriesModel extends REST
{

    public $db; 
    
    public $sku;
    
    public $store_id;
    
	public $website;
	
	public $storeView = 'default';
	
	public $parentCategory;

	public $category;
	
	public $categories;
	
	public $category_id;
	
	public $parent_id;
	
	public $categoriesHierarchy = array();
	
	public $categoryInfo = array();
	
	public $categoriesInfo = array();
	
	public $categoriesLevel = array();
	
	public $searchCriteria = array();




	
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
	
	
	public function getCategoryList(){
	
		$this->MakeCriteria();
	
		$opts = array(
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_POSTFIELDS => json_encode($this->searchCriteria),
				CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer {$this->token}",
						"Content-Type: application/json",
						"cache-control: no-cache",
						"Content-Lenght: " . strlen(json_encode($this->searchCriteria))
				));
		return $this->execute("/rest/{$this->storeView}/V1/categories/list", $opts, $this->searchCriteria, true);
	
	}
	
	public function catalogCategoryTree(){
	    
	    if(!isset($this->session_id)){
	        return array();
	    }
	    
	    $result = $this->soapClient->catalogCategoryTree($this->session_id, $this->parentCategory);
	    
	    
	    return $result;
	    
	}
	
    public function catalogCategoryLevel(){
        
        if(!isset($this->session_id)){
            return array();
        }
        $result = $this->soapClient->catalogCategoryLevel(
            $this->session_id, 
            $this->website, 
            $this->storeView, 
            $this->parentCategory
            );
            
        return $result;
        
    }
    
    public function catalogCategoryInfo(){
        
        if(!isset($this->session_id)){
            return array();
        }
        $this->categoryInfo = $this->soapClient->catalogCategoryInfo(
            $this->session_id,
            $this->category,
            $this->storeView
            );
        
        return $this->categoryInfo;
        
    }
    
    public function catalogCategoriesInfo(){
        
        if(!isset($this->categories)){
            return array();
        }
        foreach($this->categories as $key => $category){
            
            $this->categoriesInfo[] = $this->soapClient->catalogCategoryInfo($this->session_id, $category);
            
        }
        
        return $this->categoriesInfo;
        
    }
/************************************************************************************************/
/************************************** Custom **************************************************/
/************************************************************************************************/
    
    
    public function GetCatalogCategoryFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "mg2_categories_relationship.{$key} = {$this->$key} AND ";break;
                    case 'parent_id': $where_fields .= "mg2_categories_relationship.{$key} = {$this->$key} AND ";break;
                    case 'category_id': $where_fields .= "mg2_categories_relationship.{$key} = {$this->$key} AND ";break;
                    case 'mg2_parent_id': $where_fields .= "mg2_categories_relationship.{$key} = {$this->$key} AND ";break;
                    case 'mg2_category_id': $where_fields .= "mg2_categories_relationship.{$key} = {$this->$key} AND ";break;
                    case 'hierarchy': $where_fields .= "mg2_categories_relationship.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
    public function GetCategoriesRelationship(){
        
        $where_fields = $this->GetCatalogCategoryFilter();
        
        $sql = "SELECT * FROM mg2_categories_relationship WHERE {$where_fields}";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetch(PDO::FETCH_ASSOC);
        
    }
	
}

?>