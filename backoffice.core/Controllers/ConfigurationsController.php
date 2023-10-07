<?php

class ConfigurationsController extends MainController
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
	public $panel = 'Configurações';

	
    public function Management(){
    	
    	$this->title = 'Configurações do sistema';
    	
    	$this->menu = array("Configurations" => "active", "Management" => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $configurationsModel = $this->load_model('Configurations/ConfigurationsModel');
        
        if($configurationsModel->ValidateForm()){
        
        	$configurationsModel->Save();
        }
        
        $configurationsModel->LoadConfigurations();
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Configurations/ManagementView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
 
}
