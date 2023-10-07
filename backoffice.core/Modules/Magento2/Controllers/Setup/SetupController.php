<?php
class SetupController extends MainController
{

    public $includes;
    
    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Setup() 
	{
	    
	    $this->title = 'Configuração Módulo Magento2 Ecommerce: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    
	    
	    $setupModel = parent::load_module_model('Magento2/Models/Setup/SetupModel');
        
        if($setupModel->ValidateForm()){
            
            $setupModel->Save();
            
        }
        
        $setupModel->Load();
        
        require ABSPATH . "/Modules/Magento2/Views/Setup/SetupView.php";
        
        echo "<script src='".HOME_URI."/Modules/Magento2/Views/js/ModuleMagento2.js' language='javascript'></script>"; 
        
	}

}
?>