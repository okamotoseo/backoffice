<?php

class CategoriesModel extends MainModel
{

    public $store_id;
    
    public $category;
    
    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->parametros = $this->controller->parametros;
        
        $this->userdata = $this->controller->userdata;
        
        $this->store_id = $this->controller->userdata['store_id'];
        
        
    }
    

    
    public function ListCategoriesTemp()
    {
        $sql = "SELECT category FROM viavarejo_products_tmp 
        WHERE `store_id` = ? GROUP BY category ORDER BY product_id DESC";
        
        $query = $this->db->query($sql, array($this->store_id));
       
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListCategoriesViavarejo()
    {
//         $sql = "SELECT * FROM viavarejo_categories_relationship WHERE `store_id` = ? GROUP BY viavarejo_category_id";
    	$sql = "SELECT * FROM viavarejo_categories_relationship WHERE `store_id` = ? ORDER BY viavarejo_hierarchy ASC";
        $query = $this->db->query($sql, array($this->store_id));
        
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
     
    
    
    public function GetCategoriesIdsViavarejo()
    {
    
    	if(!isset($this->hierarchy)){
    		return array();
    	}
    
    	$parts = explode(">", $this->hierarchy);
    	$categories_ids = array();
    	foreach($parts as $key => $category){
    
    		$sql = "SELECT id, parent_id FROM viavarejo_categories_relationship WHERE store_id = {$this->store_id} AND name LIKE '".trim($category)."'";
    		$query = $this->db->query($sql);
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		$categories_ids[] = array('id' => $res['id'], 'parent_id' =>  $res['parent_id']);
    
    	}
    	 
    	return $categories_ids;
    }
    
    
} 