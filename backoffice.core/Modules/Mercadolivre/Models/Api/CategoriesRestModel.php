<?php

class CategoriesRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $category_id;
    
    public $categoryIdBrand;
    
    public $brand;

    
    
    
    
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
    
    public function getCategory(){
        
        if(!isset($this->category_id)){
            return array();
        }
        $result = $this->meli->get("/categories/{$this->category_id}");
        
        return $result;
    }
    
    
    
/************************************************************************************************/
/************************************** Custom **************************************************/
/************************************************************************************************/
 

    public function getCategoryPublish()
    {
        
        $result = $this->getCategory();
        if (! empty ( $result ['body']->children_categories )) {
            foreach ( $result ['body']->children_categories as $key => $category ) {
                
                if (strpos($category->name, $this->brand)) {
                    $this->category_id = $category->id;
                    $result2 = $this->getCategory();
                    foreach ( $result2 ['body']->children_categories as $key2 => $category2 ) {
                        if ($category2->name == "Outros Modelos") {
                            $this->category_id = $category2->id;
                        }
                    }
                }
            }
            if (empty ( $this->category_id )) {
                foreach ( $result ['body']->children_categories as $key => $category ) {
                    if ($category->name == "Outras Marcas"	 OR $category->id == "MLB199584" OR $category->id == "MLB199572") {
                        $this->category_id = $category->id;
                        $result2 = $this->getCategory();
                        foreach ( $result2 ['body']->children_categories as $key2 => $category2 ) {
                            if ($category2->name == "Outros Modelos") {
                                $this->category_id = $category2->id;
                            }
                        }
                    }
                }
            }
        } else {
            if ($result ['body']->name  == "Outras Marcas") {
                $rootCategories = $result['body']->path_from_root;
                $prevCategory =  $rootCategories[count($rootCategories)-2];
                $this->category_id = $prevCategory->id;
                $mlCategories = $this->getCategory();
                
                if (! empty ( $mlCategories ['body']->children_categories )) {
                    $exist = false;
                    foreach ( $mlCategories ['body']->children_categories as $key => $category ) {
                        
                        if ($this->brand == $category->name) {
                            
                            $this->category_id = $category->id;
                            $exist = true;
                            
                        }
                        
                    }
                    
                    if(!$exist){
                        
                        foreach ( $mlCategories ['body']->children_categories as $key => $category ) {
                            $parts = explode(" ", $category->name);
                            
                            if (in_array("Outras", $parts) OR in_array("Outros", $parts))  {
                                $this->category_id = $category->id;
                                $exist = true;
                                
                            }
                            
                        }
                        
                    }
                }
                
            }
        }
        return $this->category_id;
        
        
    }
    
    
    
}

?>