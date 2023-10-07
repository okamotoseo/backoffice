<?php
class RulesController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Rules() 
	{
	    
	    
	    $this->title = 'Regras de Preços Mercadolivre: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $priceModel = parent::load_module_model('Mercadolivre/Models/Price/RulesModel');
	    
        
	    if($priceModel->ValidateForm()){
            
	        $priceModel->Save();
            
        }
        
        $list = $priceModel->ListRulesPrice();
        
        require ABSPATH . "/Modules/Mercadolivre/Views/Price/RulesView.php";
	}

}
?>