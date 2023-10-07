<?php 

class VtexCategoryModel  extends MainModel
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
	    
	    require_once '/var/www/html/app_mvc/Modules/Vtex/Class/class-Rest.php';
	    
	    require_once '/var/www/html/app_mvc/Modules/Vtex/Class/class-Vtex.php';
	    
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
	    
	    $query = $this->db->query('SELECT * FROM `module_vtex_categories` WHERE store_id = ?', 
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	public function CategoryRelationshipAttr()
	{
	    $query = $this->db->query('SELECT * FROM `module_vtex_categories` WHERE store_id = ?',
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
	    if ( !isset($this->hierarchy) ) {
	        
	        return array();
	    }
	    
	    $sql = "SELECT * FROM `module_vtex_categories` WHERE store_id = ? AND `category` LIKE ? LIMIT 1";
	    
	    $query = $this->db->query($sql, array($this->store_id, trim($this->hierarchy)));
	    
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    return  $res;
	    
	}
	
	public function defaultCategoriesVtex()
	{
	    
	   $vtex = new Vtex($this->db, $this->store_id);
	    
	   $resultTree = $vtex->rest->get("catalog_system/pub/category/tree/3");
	   if(!empty($resultTree['body'])){
    	   foreach($resultTree['body'] as $k => $tree){

    	       $this->hierarchy[] = array('id' => $tree->id, 'name' => $tree->name);
    	       if(isset($tree->children[0])){
    	           
    	           foreach($tree->children as $i => $child){
    	               
    	               $hierarchy .= $child->name." > ";
    	               $this->hierarchy[] = array('id' => $child->id, 'name' => $tree->name." > ".$child->name);
    	               if(isset($child->children[0])){
        	               foreach($child->children as $i => $child2){
        	                   
        	                   $this->hierarchy[] = array('id' => $child2->id, 'name' => $tree->name." > ".$child->name." > ".$child2->name);
        	                   
        	                   if($child2->hasChildren){
        	                       foreach($child2->children as $j => $child3){
        	                           
        	                           $this->hierarchy[] = array('id' => $child3->id, 'name' => $tree->name." > ".$child->name." > ".$child2->name." > ".$child3->name);
        	                           
        	                       }
        	                       
        	                   }
        	                   
        	               }
    	               }
    	              
    	           }
    	           
    	       }
	       }
	       
	   }
	   
	   return $this->hierarchy;
	    
	}
	
}

?>