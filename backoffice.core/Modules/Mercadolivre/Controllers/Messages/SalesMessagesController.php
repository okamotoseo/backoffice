<?php
class SalesMessagesController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function SalesMessages() 
	{
	    
	    $this->title = 'Mesagens de pós venda Mercadolivre: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $mlMessagesModel = parent::load_module_model('Mercadolivre/Models/Messages/MessagesModel');
	    
	    
	    if($mlMessagesModel->ValidateForm()){
	        
	        $mlMessagesModel->Save();
	        
	    }
	    
	    $listMessages = $mlMessagesModel->ListMessages();
	    
        require ABSPATH . "/Modules/Mercadolivre/Views/Messages/SalesMessagesView.php";
        
	}

}
?>