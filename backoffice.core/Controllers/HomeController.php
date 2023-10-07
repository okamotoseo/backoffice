<?php

class HomeController extends MainController
{
	/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = true;
	
	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required = 'any';
	
	/**
	 * $panel
	 *
	 * Painel de controle
	 *
	 * @access public
	 */
	public $panel = 'Dashboard';
	
	
	/**
	 * Carrega a página "/views/home/HomeView.php"
	 */
	public function index() {
		
		$this->Dashboard();

	}
	
	public function Dashboard() {
		
// 		pre($this);die;
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = 'Dashboard';
	    
	    $this->menu = array("Dashboard" => "active");
	    
	    $this->includes = array("js" => "/Views/js/Dashboard.js");
	    
	    $dashboardModelo = $this->load_model('Home/DashboardModel');
	    
// 	    $ordersDay = $dashboardModelo->getOrdersDay();
// 	    pre($ordersDay);
	    $ordersMonth = $dashboardModelo->getOrdersMonth();
// 	    pre($ordersMonth);die;
	    $result = $dashboardModelo->getTotalOrdersMarketplaces();
	    
// 	    $tiketMedio = $dashboardModelo->getTicketMMarketplaces();
// 	    pre($ticketMedio);die;
	    $documentationsModelo = $this->load_model('Admin/DocumentationsModel');
	    
	    $listLastDocuments = $documentationsModelo->listLastDocuments();
	    
// 	    $listBestSellers = $dashboardModelo->GetBestSellers(10);
	    
	    $listBestSellers = $dashboardModelo->GetBestSalesRecently(10, 90);
	    $listBestSellers530 = $dashboardModelo->GetBestSalesRecently(5, 30);
	    $listBestSellers57 = $dashboardModelo->GetBestSalesRecently(5, 7);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Home/DashboardView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}

}
?>
