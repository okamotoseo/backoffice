<?php
class DashboardController extends MainController
{

    public $includes;
    
    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
        
    }

	public function Dashboard() 
	{
	    
	    $this->title = 'Painel de Controle SETA ERP: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $this->includes = array("js" => "/Modules/Seta/Views/js/Dashboard.js");
	    
	    $dashboardModel = $this->load_module_model('Seta/Models/Dashboard/SetaDashboardModel');
	    
// 	    $list = $dashboardModel->StoreSales();
	    $list = $dashboardModel->Sales();
	    
        require ABSPATH . "/Modules/Seta/Views/Dashboard/DashboardView.php";
        
        
        
	}

}
?>