<?php
class AttributesController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

    public function Attributes() 
	{
	    
	    $this->title = 'Mapear Atributos Amazon: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Amazon/Views/js/Map.js");
	    
	    
	    
	    $key = array_search('Category', $this->parametros);
	     
	    if(!empty($key)){
	    	$categoryId = get_next($this->parametros, $key);
	    	
	    	$categoryModel = parent::load_model('Products/CategoryModel');
	    	
	    	$attributesModel = parent::load_model('Products/AttributesModel');
	    	 
	    	$categoryModel->id = $categoryId;
	    	
	    	$categories = $categoryModel->GetCategory();
	    	
	    	$attributesModel->category = $categories[0]['hierarchy'];
	    	
	    	$listAttributes = $attributesModel->GetAttributesFromRootCategory();
	    	
	    }
	    
	    
	    
	    $azAttributesModel = parent::load_module_model('Amazon/Models/Map/AzAttributesModel');
	    
// 	    $listAttributesRequired = $azAttributesModel->ListAttributesRequired();
	    
	    $listAttributesRequired = $azAttributesModel->GetProductType();
	    
	    $azAttributesRelationship = $azAttributesModel->GetAzAttributesRelationship();
	    
        require ABSPATH . "/Modules/Amazon/Views/Map/AttributesView.php";
        
	}

}
?>