<?php

class SkyhubOrdersController extends MainController
{

	    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

	
    public function SkyhubOrders(){
    	
    	$this->title = 'Pedidos Skyhub';
    	
    	$this->menu = array("SkyhubOrders" => "active", "SkyhubOrders" => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $ordersModel =  parent::load_module_model('Skyhub/Models/Orders/SkyhubOrdersModel');
        
        
        if($ordersModel->ValidateForm()){
//             pre($ordersModel);die;
            $list = $ordersModel->GetOrderDetails();
            
        }else{
            
            $list = $ordersModel->ListOrderDetails();
        }
        
        $totalReg = $ordersModel->TotalOrders();
        
        
        require ABSPATH . "/Modules/Skyhub/Views/Orders/SkyhubOrdersView.php";
        
        
    }
    
  
    
    
 
}
