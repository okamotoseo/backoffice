<?php


class ReportProductController extends ModulesController
{


	public $includes;
	
	public $form ;
	
	public $layout = "ReportProduct/";
	

	public function ReportProduct() 
	{
	    
	    $this->form = "ReportProductForm";
	    $this->title = 'Relatório de produtos';
        
        $reportProductModel = $this->load_module_model('Seta/Models/Report/ReportProductModel');
        
        $reportProductModel->ValidateForm();
        
        if(method_exists($reportProductModel, $reportProductModel->report_model)){
            $list = $reportProductModel->{$reportProductModel->report_model}();
            $this->box['form'] = $this->widgets['expandable'];
            $this->layout .= $reportProductModel->report_model."Layout";
        }
        
        require ABSPATH . "/Modules/Seta/Views/Report/ReportView.php";
        
        
        
	}

}
?>