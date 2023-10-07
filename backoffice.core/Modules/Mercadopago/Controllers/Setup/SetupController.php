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
	    
	    
	    $this->title = 'Configuração  Mercadopago: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $setupModel = parent::load_module_model('Mercadopago/Models/Setup/SetupModel');
	    
	    $setupModel->Load();
	    
	    if($setupModel->ValidateForm()){
	        
	        $setupModel->Save();
	        
	        $setupModel->Load();
	    }
	    
        require ABSPATH . "/Modules/Mercadopago/Views/Setup/SetupView.php";
        
	}

}
?>