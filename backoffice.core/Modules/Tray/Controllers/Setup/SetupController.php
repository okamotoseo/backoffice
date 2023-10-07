<?php
class SetupController extends MainController
{
    
    
    private  $consumer_key = '0414c3cd359f374ca8745ba8ecf28cf23a1a30951d05e90cdb50a79bfcfaf18c';
    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Setup() 
	{
	    
	    
	    $this->title = 'Configuração  Tray: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $setupModel = parent::load_module_model('Tray/Models/Setup/SetupModel');
	    
	    $setupModel->Load();
	    
	    $url = str_replace('web_api', '', $setupModel->api_host);
	   
	    $setupModel->url = $url."/auth.php?response_type=code&consumer_key={$this->consumer_key}&https://backoffice.sysplace.com.br/Modules/Configuration/Tray/Setup";
	    
	    
	    if(!isset($setupModel->access_token) AND isset($_REQUEST['url'])){
	        
	        $setupModel->url = $_REQUEST['url']."/auth.php?response_type=code&consumer_key={$this->consumer_key}&https://backoffice.sysplace.com.br/Modules/Configuration/Tray/Setup";
	        
	        require ABSPATH . "/Modules/Tray/Views/Setup/SetupAuthView.php";
	        
	        return;
	        
	    }
	    
	    
	    if($setupModel->ValidateForm()){
	        
	        $setupModel->Save();
	        
	        $setupModel->Load();
	        
	    }
	    
	    
        require ABSPATH . "/Modules/Tray/Views/Setup/SetupView.php";
        
	}

}
?>