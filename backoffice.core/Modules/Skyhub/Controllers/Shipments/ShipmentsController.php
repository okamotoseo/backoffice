<?php

class ShipmentsController extends MainController
{

	    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

	
    public function Shipments(){
    	
    	$this->title = 'Listagem de PLPs';
    	
    	$this->menu = array("SkyhubShipments" => "active", "Plps" => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $shipmentModel =  parent::load_module_model('Skyhub/Models/Shipments/ShipmentsModel');
        
        
        $listOrdersReadyToGroupPlp = $shipmentModel->list_order_ready_to_group_plp();
        
        $listplps = $shipmentModel->list_plps();
        if(!empty($listplps)){
        	$listOrdersReadyToCollect = $shipmentModel->list_order_ready_to_collect();
        }
        
        require ABSPATH . "/Modules/Skyhub/Views/Shipments/ShipmentsView.php";
        
        
    }
    
  
    
    
 
}
