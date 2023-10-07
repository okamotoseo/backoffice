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
	    
	    $this->title = 'Integração de Produtos Tray: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Tray/Views/js/ModuleTray.js");
	    
	    $productsModel = parent::load_module_model('Tray/Models/Products/ProductsModel');
	    
	    
        if($productsModel->ValidateForm()){
        	
            $products = $productsModel->GetProducts();
            
            $totalReg = $productsModel->TotalGetProducts();
            
        }else{
            $products = $productsModel->ListProducts();
            
            $totalReg = $productsModel->TotalProducts();
        }
        require ABSPATH . "/Modules/Tray/Views/Products/ProductsView.php";
        
        
	}

}
?>