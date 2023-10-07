<?php
class SetupController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Setup() 
	{
	    
	    $this->title = 'Configuração módulo SETA ERP: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $setupModel = parent::load_module_model('Seta/Models/Setup/SetupModel');
        
        if($setupModel->ValidateForm()){
            
            $setupModel->Save();
            
        }
        
        $setupModel->Load();
        
        require ABSPATH . "/Modules/Seta/Views/Setup/SetupView.php";
        
        
        
	}

}
?>