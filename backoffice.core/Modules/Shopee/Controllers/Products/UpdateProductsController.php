<?php
class UpdateProductsController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        $this->store_id = $controller->userdata['store_id'];
        
        
    }

    public function UpdateProducts() 
	{
        
	    $this->includes = array("js" => "/Modules/Shopee/Views/js/ModuleShopee.js");
	    
	    $this->title = 'Integração de Produtos Shopee: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $setupModel = parent::load_module_model('Shopee/Models/Setup/SetupModel');
	    
	    $setupModel->store_id = $this->store_id;
	    
	    $setupModel->Load();
	    
	    $productsModel = parent::load_module_model('Shopee/Models/Products/ProductsModel');
	    
	    if($productsModel->ValidateForm()){
	        
	        $productsModel->input_data = $setupModel->input_data;
	        
	        $products = $productsModel->Save();

	    }
        
        require ABSPATH . "/Modules/Shopee/Views/Products/UpdateProductsView.php";
        
        
        
	}

}
?>