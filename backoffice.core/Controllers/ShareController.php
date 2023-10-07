<?php

class ShareController extends MainController
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
	public $permission_required = 'admin';
	
	/**
	 * $panel
	 *
	 * Painel de controle
	 *
	 * @access public
	 */
	public $panel = 'Compartilhado';
	
	
	
	
	public function Share() {
	    
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->menu = array("Admin" => "active", "Account" => "active");
	    
	    $this->title = 'Cadastro de contas';
	    
	    $accountModel = $this->load_model('Admin/AccountModel');
	    
	    $storeModel = $this->load_model('Admin/StoreModel');
	    
	    
	    if($accountModel->ValidateForm()){
	        
	        $accountModel->Save();
	        
	        $storeModel->account_id = $accountModel->account_id;
	        
	        if($storeModel->ValidateForm()){
	            
	        	
// 	        	pre($storeModel);die;
	        	
	        	
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

}
?>
