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
	    
	    
	    $this->title = 'Configuração  Checkout: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $setupModel = parent::load_module_model('Checkout/Models/Setup/SetupModel');
	    
	    $setupModel->Load();
	    
	    if($setupModel->ValidateForm()){
	        
	        $setupModel->Save();
	        
	        $setupModel->Load();
	    }
	    
        require ABSPATH . "/Modules/Checkout/Views/Setup/SetupView.php";
        
	}

}
?>