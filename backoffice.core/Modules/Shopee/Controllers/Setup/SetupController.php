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
	    
	    
	    $this->title = 'Configuração  Shopee: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $setupModel = parent::load_module_model('Shopee/Models/Setup/SetupModel');
	    
	    $setupModel->Load();
	    
	    
	    if($setupModel->ValidateForm()){
	        
	        $setupModel->Save();
	        
	        $setupModel->Load();
	        
	    }
	    
	    
        require ABSPATH . "/Modules/Shopee/Views/Setup/SetupView.php";
        
	}

}
?>