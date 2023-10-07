<?php
class ManageProductsController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

    public function ManageProducts() 
	{
	    
	    $this->title = 'Integração de Produtos Skyhub: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Skyhub/Views/js/ModuleSkyhub.js");
	    
	    $productsModel = parent::load_module_model('Skyhub/Models/Products/ProductsModel');
	    
	    $totalReg = $productsModel->TotalProducts();
	    
        if($productsModel->ValidateForm()){
        	
            $products = $productsModel->GetProducts();
            
        }else{
            $products = $productsModel->ListProducts();
        }
        require ABSPATH . "/Modules/Skyhub/Views/Products/ProductsView.php";
        
        
        
	}

}
?>