<?php

class PlpViewController extends MainController
{

	    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

    
    public function PlpView(){
        
        $this->title = 'PLP';
        
        $this->menu = array("Skyhub" => "active", "Plps" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $shipmentModel =  parent::load_module_model('Skyhub/Models/Shipments/PlpViewModel');
        
        $url = $shipmentModel->PlpView();
        
        
        
        require ABSPATH . "/Modules/Skyhub/Views/Shipments/PlpView.php";
        
        
    }
    
    
  
    
    
 
}
