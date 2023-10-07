<?php

class CategoriesRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $category_id;
    
    public $categoryIdBrand;
    
    public $category;
    
    public $pageCategories = 1;
    
    public $offsetCategories = 0;
    
    public $limitCategories = 50;
    
    public $categoryData = array();

    
    
    
    
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
    
    function postCategory(){
        
        if(!isset($this->categoryData)){
            return array();
        }
        
        $result = $this->tray->post ( '/categories', $this->categoryData, array ('access_token' => $this->access_token));
        
        return $result;
        
    }
    
    function putCategory(){
    
    	if(!isset($this->category_id) OR !isset($this->categoryData)){
    		return array();
    	}
//     	pre($this->categoryData);die;
    	$result = $this->tray->put ( "/categories/{$this->category_id}", $this->categoryData, array ('access_token' => $this->access_token));
    
    	return $result;
    
    }
    
    public function getCategories(){
        
        if(!isset($this->access_token)){
            return array();
        }
        
        $params = array (
            'access_token' => $this->access_token, 
            "sort"=>'id_asc', 
            "page" => $this->pageCategories,
            "offset" => $this->offsetCategories,
            "limit" => $this->limitCategories
            
        );
        $result = $this->tray->get("/categories", $params);
        
        return $result;
    }
    
    public function getCategory(){
        
        if(!isset($this->category_id)){
            return array();
        }
        
        $params = array (
            'access_token' => $this->access_token
            
        );
        $result = $this->tray->get("/categories/{$this->category_id}", $params);
        
        return $result;
    }
    
    
    public function deleteCategory(){
        
        if(!isset($this->category_id)){
            return array();
        }
        
//         delete($path, $params)
        $result = $this->tray->delete("/categories/", array ('access_token' => $this->access_token));
        
        return $result;
    }
    
/************************************************************************************************/
/************************************** Custom **************************************************/
/************************************************************************************************/
 
    public function getCategoriesList(){
    	
    	$this->categoryData = array();
    	
    	do{
    		 
    		$categories = $this->getCategories();
    		 
    		foreach ($categories['body']->Categories as $k => $categoryArray){
    	
    			$this->categoryData[] = (array) $categoryArray->Category;
    	
    		}
    		 
    		$this->pageCategories++;
    		 
    		$this->offsetCategories += $this->limitCategories;
    		 
    	}while($this->offsetCategories <= $categories['body']->paging->total);
    	
    	return $this->categoryData;
    }
    
}

?>