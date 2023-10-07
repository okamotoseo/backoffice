<?php 

class AzCategoryModel  extends MainModel
{
    
    public $id;

	public $store_id;
	
	public $category;

	public $category_id;

	public $path_from_root;
	
	public $defaultCategories;
	
	public $hierarchy;


	
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
	
	public function CategoryRelationship(){
	    
	    $query = $this->db->query('SELECT * FROM `ml_category_relationship` WHERE store_id = ?', 
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	    
	}
	
	public function CategoryRelationshipAttr()
	{
	    $query = $this->db->query('SELECT * FROM `ml_category_relationship` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    $mlCategory = array();
	    while($row = $query->fetch(PDO::FETCH_ASSOC)){
	        
	       $sql = "SELECT count(distinct attribute_id) as totalAttr FROM ml_attributes_required
            WHERE store_id = '{$row['store_id']}' AND category_id = '{$row['category_id']}'";
	        $queryAttributes = $this->db->query($sql);
	         $total = $queryAttributes->fetch(PDO::FETCH_ASSOC);
	         $row['total_attributes'] = $total['totalAttr'];
	        
	        
	        $mlCategory[] = $row;
	    }
	    
	    
	    return $mlCategory;
	    
	}
	
	public function getCategoryRelationship()
	{
	    if ( !isset($this->category) ) {
	        
	        return array();
	    }
	    
	    $sql = "SELECT * FROM `az_category_relationship` WHERE store_id = ? AND `category` LIKE ? LIMIT 1";
	    
	    $query = $this->db->query($sql, array($this->store_id, trim($this->category)));
	    
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
		if(isset($res['id'])){
		    foreach($res as $key => $value)
		    {
		    	$column_name = str_replace('-','_',$key);
		    	$this->{$column_name} = $value;
		    }
	    
	    	return  $res;
	    	
		}else{
			return array();
		}
	    
	}
	
	public function defaultCategoriesAz()
	{
	    
	    	
	    	$sql = "SELECT * FROM `az_category_xsd` WHERE allow = 'T'";
	    
		    $query = $this->db->query($sql);
		    
		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
		    		
		    $this->defaultCategories =  json_encode($res);
		    
		    return $res;
	    
	    
	    
	}
	
}

?>