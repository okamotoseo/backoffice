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
	    $key = array_search('Category', $this->parametros);
	    
	    if(isset($key)){
	       $mlCategoryId = get_next($this->parametros, $key);
	    }
	    
	    $this->title = 'Mapear Atributos Mercadolivre: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Mercadolivre/Views/js/Map.js");
	    
	    $attributesModel = parent::load_model('Products/AttributesModel');
	    
	    
	    $mlAttributesModel = parent::load_module_model('Mercadolivre/Models/Map/MlAttributesModel');
	    
	    
	    $mlAttributesModel->category_id = $mlCategoryId;
	    
	    $listAttributes = $attributesModel->GetAttributes($mlAttributesModel->category_id);
	    
	    $listAttributesRequired = $mlAttributesModel->ListAttributesRequired();
	    
        require ABSPATH . "/Modules/Mercadolivre/Views/Map/AttributesView.php";
        
	}

}
?>