<?php
class XsdController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Xsd() 
	{
	    
	    $this->title = 'XSD de Categorias Específicas Amazon: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $xsdModel = parent::load_module_model('Amazon/Models/Xsd/XsdModel');
	    
	    
	    if($xsdModel->ValidateForm()){
	        
	        $xsdModel->Save();
	        
	    }
	    
	    $listXsd = $xsdModel->ListXsd();
	    
        require ABSPATH . "/Modules/Amazon/Views/Xsd/XsdView.php";
        
	}

}
?>