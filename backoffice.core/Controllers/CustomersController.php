<?php

class CustomersController extends MainController
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
	public $panel = 'Clientes';

	
    public function ManageCustomers(){
    	
    	$this->title = 'Clientes';
    	
    	$this->menu = array("Customer" => "active", "ManageCustomers"  => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $customerModel = $this->load_model('Customers/ManageCustomersModel');
        
        
        if($customerModel->ValidateForm()){
        	        	
            $cutomers = $customerModel->GetCustomers();
            
            $totalReg = $customerModel->TotalGetCustomers();
        	
        }else{
        	
            $cutomers = $customerModel->ListCustomers();
            
            $totalReg = $customerModel->TotalCustomers();
        }
        
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Customers/ManageCustomersView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    public function RegisterCustomers(){
        
        $this->title = 'Cadastro de Clientes';
        
        $this->menu = array("Customer" => "active", "RegisterCustomers" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $customerModel = $this->load_model('Customers/ManageCustomersModel');
        
        
        if($customerModel->ValidateForm()){
                
            $customerModel->Save();
            
        }
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Customers/RegisterCustomersView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
}