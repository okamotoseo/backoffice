<?php
class ExportProductsController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        $this->store_id = $controller->userdata['store_id'];
        
        
    }

    public function ExportProducts() 
	{
        
	    $this->includes = array("js" => "/Modules/Shopee/Views/js/ModuleShopee.js");
	    
	    $this->title = 'Integração de Produtos Shopee: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $setupModel = parent::load_module_model('Shopee/Models/Setup/SetupModel');
	    
	    $setupModel->store_id = $this->store_id;
	    
	    $setupModel->Load();
	    
	    $productsModel = parent::load_module_model('Shopee/Models/Products/ProductsModel');
	    
	    $productsModel->input_data = $setupModel->input_data;
	    

	    
	    if($setupModel->input_data == 'default'){
	        
	        if($productsModel->ValidateForm()){
	            
	            $exportProducts = $productsModel->ExportProducts();
	            
	        }
	        
	    }
	    
// 	    pre($exportProducts);
	    if($setupModel->input_data == 'google_xml'){
	        
	        if($productsModel->ValidateForm()){
	            
	            $exportProducts = $productsModel->ExportProductsXml();
	            
	        }
	        
	    }
        
        require ABSPATH . "/Modules/Shopee/Views/Products/ExportProductsView.php";
        
        
        
	}

}
?>