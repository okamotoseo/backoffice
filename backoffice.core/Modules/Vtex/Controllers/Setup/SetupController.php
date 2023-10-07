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
	    
	    
	    $this->title = 'Configuração  Mercadolivre: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $setupModel = parent::load_module_model('Mercadolivre/Models/Setup/SetupModel');
	    
	    $setupModel->Load();
	    
	    if(isset($setupModel->app_id) and isset($setupModel->secret_key)){
	        
    	    $meli = new Meli($setupModel->app_id, $setupModel->secret_key);
    	    
    	    $setupModel->url = $meli->getAuthUrl($setupModel->uri, Meli::$AUTH_URL[$setupModel->site_id]);
    	    
	    }
	    
	    if($setupModel->ValidateForm()){
	        
	        $setupModel->Save();
	        
	        $setupModel->Load();
	        
	    }
	    
	    
        require ABSPATH . "/Modules/Mercadolivre/Views/Setup/SetupView.php";
        
	}

}
?>