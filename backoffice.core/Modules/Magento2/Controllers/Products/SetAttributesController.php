<?php



class SetAttributesController extends MainController
{
	

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        $this->moduledata = getModuleConfig($this->db, $this->userdata['store_id'], 11);
        
        
    }


	
	public function SetAttributes()
	{
	    $this->title = 'Conjunto de Attributos Ecommerce: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $this->includes = array("js" => "/Modules/Magento2/Views/js/ModuleMagento2.js");
	    
	    $setAttributesMg2Model = parent::load_module_model('Magento2/Models/Catalog/AttributeSetModel');
	    
	    $setAttributesRelationshipModel = parent::load_module_model('Magento2/Models/Products/SetAttributesRelationshipModel');
	    
	    $setAttributes = $setAttributesRelationshipModel->ListSetAttributesRelationship();
	    
	    $setAttributesMg2Model->filters[] = array('field' => 'attribute_set_name', 'value' => '', 'condition_type' => 'neq' );
	    
	    $setAttributesMg2 = $setAttributesMg2Model->catalogProductAttributeSetList();
	    
	   
		
	    require ABSPATH . "/Modules/Magento2/Views/Products/SetAttributesView.php";
	    
	    
	}


}
?>