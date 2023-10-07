<?php

class AdminController extends MainController
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
	public $panel = 'Administração';
	
	
	public function Translates(){
	

		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		 
		$this->menu = array("Admin" => "active", "Translates" => "active");
		 
		$this->title = 'Traduções de Atributos';
	
		$translatesModel = $this->load_model('Admin/TranslatesModel');
	
		if($translatesModel->ValidateForm()){
	
			$translatesModel->Save();
	
		}
	
		$list = $translatesModel->ListTranslates();
	
		require ABSPATH . '/Views/_includes/header.php';
		 
		require ABSPATH . '/Views/Admin/TranslatesView.php';
		 
		require ABSPATH . '/Views/_includes/footer.php';
	
	}
	
	public function ManageCharge() {
	    
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->menu = array("Admin" => "active", "Charge" => "active");
	    
	    $this->title = 'Cobrança de contas';
	    
	    $accountModel = $this->load_model('Admin/AccountModel');
	    
	    $storeModel = $this->load_model('Admin/StoreModel');
	    
	    $salesModel = $this->load_model('Report/SalesModel');
	    
	    $accountModel->Load();
	    
	    $storeModel->Load();
	    
	    
	    if($salesModel->ValidateForm()){
	        
	        $list = $salesModel->GetSales();
	        
	    }else{
	        
	        
	        $month_ini = new DateTime("first day of this month");
	        
	        $month_end = new DateTime("last day of this month");
	        
	        $salesModel->DataPedido = $month_ini->format("d/m/Y");
	        
	        $salesModel->DataPedidoAte = $month_end->format("d/m/Y");
	        
	        $salesModel->month = date('F', $salesModel->DataPedido);
	        
	        $salesModel->year = date('Y', $salesModel->DataPedido);
	        
	        $list = $salesModel->GetSales();
	        
	    }
	    $accountModel->reportSales = $list;
	    
	    $total = $accountModel->Charge();
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Admin/ManageChargeView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
	public function Account() {
	    
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->menu = array("Admin" => "active", "Account" => "active");
	    
	    $this->title = 'Cadastro de contas';
	    
	    $accountModel = $this->load_model('Admin/AccountModel');
	    
	    $storeModel = $this->load_model('Admin/StoreModel');
	    
	    if($accountModel->ValidateForm()){
	        
	        $accountModel->Save();
	        
	        $storeModel->account_id = $accountModel->account_id;
	        
	        if($storeModel->ValidateForm()){
	        	
	            $storeModel->Save();
	            
	            $userModel = $this->load_model('User/UserRegisterModel');
	            
	            $userModel->NewUserAccount($accountModel, $storeModel);
	            
	        }
	        
	    }
	    
	    $accountModel->Load();
	    
	    $storeModel->Load();
	    
	    $moduleModel = $this->load_model('Modules/ModulesModel');
	    
	    $modules = $moduleModel->ListModules();
	    

	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Admin/AccountView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
	public function AccountManagement() {
	     
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = 'Gerênciar Contas';
	    
	    $this->menu = array("Admin" => "active", "Management" => "active");
	    
	    $accountManagementModel = $this->load_model('Admin/AccountModel');
	    
	    $storeModel = $this->load_model('Admin/StoreModel');
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Admin/AccountManagementView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
	
	public function Store() {
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = 'Gerênciar Lojas';
	    
	    $this->menu = array("Admin" => "active", "Management" => "active");
	    
	    $storeModel = $this->load_model('Admin/StoreModel');
	    
	    if($storeModel->ValidateForm()){
	        
	        $storeModel->Save();
	    }
	    
	    $moduleModel = $this->load_model('Modules/ModulesModel');
	    
	    $modules = $moduleModel->ListModules();
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Admin/StoreView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
	public function Documentations() {
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = 'Gerênciar Documentação';
	    
	    $this->menu = array("Admin" => "active", "Documentations" => "active");
	    
	    $documentationsModel = $this->load_model('Admin/DocumentationsModel');
	    
	    if($documentationsModel->ValidateForm()){
	        
	        $documentationsModel->Save();
	    }
	    
	    
	    $list = $documentationsModel->ListDocuments();
	    
	    $moduleModel = $this->load_model('Modules/ModulesModel');
	    
	    $modules = $moduleModel->ListModules();

	    $modules[] = array('name' => 'Clientes');
	    $modules[] = array('name' => 'Pedidos');
	    $modules[] = array('name' => 'Produtos');
	    $modules[] = array('name' => 'Preços');
	    $modules[] = array('name' => 'Relatórios');
	    $modules[] = array('name' => 'Sistema');
	    
	    asort($modules);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Admin/DocumentationsView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	

}
?>
