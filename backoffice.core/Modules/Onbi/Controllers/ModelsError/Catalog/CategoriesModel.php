<?php 

class CategoriesModel extends Soap
{

    public $db; 
    
    public $store_id;
    
	public $website;
	
	public $storeView;
	
	public $parentCategory;

	public $category;
	
	public $categories;
	
	public $category_id;
	
	public $parent_id;
	
	public $categoriesHierarchy = array();
	
	public $categoryInfo = array();
	
	public $categoriesInfo = array();
	
	public $categoriesLevel = array();




	
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
    
    
    public function catalogCategoryHierarchy(){
        
        if(!isset($this->categories)){
            return array();
        }

        $catRootId = 2;
        $ind = 0;
        foreach($this->categories as $key => $category){
                $this->category = $category;
                $result = $this->catalogCategoryInfo();
                if($result->parent_id == $catRootId){
                    $ind++;
                    $this->categoriesHierarchy[$ind] = $result->name;
                    
                    
                }
                if($result->parent_id > $catRootId){
                    $this->categoriesHierarchy[$ind] = $this->categoriesHierarchy[$ind]." > ".$result->name;
                    
                    
                }
            
            
        }
//         pre($this->categoriesHierarchy);die;
        return $this->categoriesHierarchy;
        
    }
    
    public function ListCategoriesIds(){
        $sql = "SELECT product_id, categories_ids FROM module_onbi_products_tmp WHERE store_id = ?";
        $query = $this->db->query( $sql, array($this->store_id));
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function GetCatalogCategoryFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "onbi_categories_relationship.{$key} = {$this->$key} AND ";break;
                    case 'parent_id': $where_fields .= "onbi_categories_relationship.{$key} = {$this->$key} AND ";break;
                    case 'category_id': $where_fields .= "onbi_categories_relationship.{$key} = {$this->$key} AND ";break;
                    
                }
            }
            
        }
        
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
    public function GetCategoriesRelationship(){
        
        $where_fields = $this->GetCatalogCategoryFilter();
        
        $sql = "SELECT * FROM onbi_categories_relationship WHERE {$where_fields}";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
	
}

?>