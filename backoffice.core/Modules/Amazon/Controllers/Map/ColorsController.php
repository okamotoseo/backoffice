<?php
class ColorsController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Colors() 
	{
	    
	    $this->title = 'Mapear Cores Mercadolivre: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Mercadolivre/Views/js/Map.js");
	    
	    $colorsModel = parent::load_model('Products/ColorsModel');
	    
	    $mlColorsModel = parent::load_module_model('Mercadolivre/Models/Map/MlColorsModel');
	    
	    $listColors = $colorsModel->ListColors();
	    
// 	    $listAllowedCollors = $mlColorsModel->ListAllowedColors();
	    
	    $listAllowedCollors = $mlColorsModel->ListMlAttributeColors();
// 	    pre($listAllowedCollors);
	    
	    $listColorsRelationship = $mlColorsModel->ColorsRelationship();
	    
        require ABSPATH . "/Modules/Mercadolivre/Views/Map/ColorsView.php";
        
	}

}
?>