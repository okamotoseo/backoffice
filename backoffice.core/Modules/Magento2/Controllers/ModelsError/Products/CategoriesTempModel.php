<?php

class CategoriesTempModel extends MainModel
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
        $sql = "SELECT category FROM module_onbi_products_tmp 
        WHERE `store_id` = ? GROUP BY category ORDER BY product_id DESC";
        
        $query = $this->db->query($sql, array($this->store_id));
       
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
} 