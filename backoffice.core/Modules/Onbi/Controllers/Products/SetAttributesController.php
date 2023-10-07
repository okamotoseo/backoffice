<?php



class SetAttributesController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }


	
	public function SetAttributes()
	{
	    
	    $this->title = 'Conjunto de Attributos Ecommerce: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $this->includes = array("js" => "/Modules/Onbi/Views/js/ModuleOnbi.js");
	    
	    $setAttributesOnbiModel = parent::load_module_model('Onbi/Models/Catalog/AttributeSetModel');
	    
	    $setAttributesRelationshipModel = parent::load_module_model('Onbi/Models/Products/SetAttributesRelationshipModel');
	    
	    $setAttributes = $setAttributesRelationshipModel->ListSetAttributesRelationship();

	    $setAttributesOnbi = $setAttributesOnbiModel->catalogProductAttributeSetList();

	    require ABSPATH . "/Modules/Onbi/Views/Products/SetAttributesView.php";
	    
	    
	}


}
?>