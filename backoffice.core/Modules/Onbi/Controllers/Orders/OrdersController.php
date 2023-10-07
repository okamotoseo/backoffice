<?php
class OrdersController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

	public function Orders() 
	{
	    
	    $this->title = 'Pedidos Ecommerce Onbi: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Onbi/Views/js/ModuleOnbi.js");
	    
	    $ordersModel = parent::load_module_model('Onbi/Models/Orders/OrdersModel');
	    
	    $orders = $ordersModel->ListOrders();
        
        require ABSPATH . "/Modules/Onbi/Views/Orders/OrdersView.php";
        
        
	}

}
?>