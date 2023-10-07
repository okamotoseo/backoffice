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
	    
	    $this->title = 'Integração de Produtos Viavarejo: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Viavarejo/Views/js/Viavarejo.js");
	    
	    $productsModel = parent::load_module_model('Viavarejo/Models/Products/ProductsModel');
	    
	    $totalReg = $productsModel->TotalProducts();
	    
        if($productsModel->ValidateForm()){
        	
            $products = $productsModel->GetProducts();
            
        }else{
            $products = $productsModel->ListProducts();
        }
        require ABSPATH . "/Modules/Viavarejo/Views/Products/ProductsView.php";
        
        
        
	}

}
?>