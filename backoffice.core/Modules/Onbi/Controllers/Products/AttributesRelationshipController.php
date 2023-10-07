<?php

class AttributesRelationshipController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

    public function AttributesRelationship() 
	{
	    
	    $this->title = 'Attributos: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>"; 
	        
	    $this->includes = array("js" => "/Modules/Onbi/Views/js/ModuleOnbi.js");
	    
	    $attributesModel = parent::load_module_model('Onbi/Models/Products/AttributesRelationshipModel');
	    
// 	    $attributesModel->ValidateForm();
	    
	    $attributes = $attributesModel->ListAttributes();
	    
// 	    $totalReg = $attributesModel->TotalAttributes();
	    
	    require ABSPATH . "/Modules/Onbi/Views/Products/AttributesRelationshipView.php";
        
        
	}

}
?>