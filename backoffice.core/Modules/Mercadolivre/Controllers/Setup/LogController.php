<?php
class LogController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Log() 
	{
	    
	    
	    $this->title = 'Log do Modulo Mercadolivre: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $mlLogModel = parent::load_module_model('Mercadolivre/Models/Setup/LogModel');
	    
	    $mlLogModel->ValidateForm();
	    
	    $totalReg = $mlLogModel->TotalLogs();
	    
	    $logList = $mlLogModel->ListLogs();
        
        require ABSPATH . "/Modules/Mercadolivre/Views/Setup/LogView.php";
	}

}
?>