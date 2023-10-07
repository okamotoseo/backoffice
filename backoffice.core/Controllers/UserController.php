<?php

class UserController extends MainController
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
	public $panel = 'Usuários';

	
    public function Profile(){
    	
    	$this->title = 'Perfil';
    	
    	$this->menu = array("Profile" => "active");
    	
        // Parametros da função
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        // Carrega o modelo para este view
        $modelo = $this->load_model('User/UserProfileModel');
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/User/UserProfileView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    public function Register(){
    	 
    	$this->title = 'Cadastro de Usuário';
    	 
    	$this->menu = array("Register" => "active");
    	 
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    	
    	
    	$userModel = $this->load_model('User/UserRegisterModel');
    	
    	if($userModel->ValidateForm()){
    	    
    	    $userModel->Save();
    	    
    	}
    	
    	$userModel->Load();

    	$storeModel = $this->load_model('Admin/StoreModel');
//     	pre($storeModel);die;
    	$storesList = $storeModel->ListStores();
    	
    	$permissionModel = $this->load_model('Admin/PermissionModel');
    	
    	$permissions = $permissionModel->ListPermissions();
    	
    	
    	$permissionGrouprModel = $this->load_model('User/PermissionsGroupModel');
    	
    	
    	if($permissionGrouprModel->ValidateForm()){
    		
    			
    		$permissionGrouprModel->Save();
    		
    		$permissionGrouprModel->Load();
    			
    	}
    	
    	$key = array_search('p_group', $this->parametros);
    	if(!empty($key)){
    		$permissionGrouprModel->p_group = get_next($this->parametros, $key);
    	}
    	
    	$key = array_search('ResetDefault', $this->parametros);
    	
    	if(!empty($key)){
    		
    		$permissionGrouprModel->p_group = get_next($this->parametros, $key);
    		
    	 	$permissionGrouprModel->CreateDefaultStorePermissions();
    	}
    	
    	$modules = $permissionGrouprModel->GetPermissionsGroupValues(); 
    	
    	
    	
    	$lista = $userModel->ListUsers();
    	
    
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/User/UserRegisterView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    }
    
    
 
}
