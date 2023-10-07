<?php


class ReportOrderController extends ModulesController
{


	public $includes;
	
	public $form ;
	
	public $layout = "ReportOrder/";
	

	public function ReportOrder() 
	{
	    
	    $this->form = "ReportOrderForm";
	    $this->title = 'Relatório de pedidos';
        
        $reportOrderModel = $this->load_module_model('Seta/Models/Report/ReportOrderModel');
        $reportOrderModel->ValidateForm();
        if( method_exists($reportOrderModel, $reportOrderModel->report_model)){
            $list = $reportOrderModel->{$reportOrderModel->report_model}();
            $this->box['form'] = $this->widgets['expandable'];
            $this->layout .= $reportOrderModel->report_model."Layout";
        }
        
        require ABSPATH . "/Modules/Seta/Views/Report/ReportView.php";
        
        
        
	}

}
?>