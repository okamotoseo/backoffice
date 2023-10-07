<?php
class ProductsController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

	public function Products() 
	{
	    
	    $this->title = 'Configuração módulo Adj SIG: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Adj/Views/js/ModuleAdj.js");
	    
	    $productsModel = parent::load_module_model('Adj/Models/Products/ProductsModel');
	    
// 	    if(isset($productsModel->token) && isset($productsModel->password)){
	        
	        if($productsModel->ValidateForm()){
	        	
	            $products = $productsModel->ListProducts();
	            
	        }
	        require ABSPATH . "/Modules/Adj/Views/Products/ProductsView.php";
        
// 	    }else{
	    	
// 	    	gotoPage('Modules', 'Configuration/Adj/Setup');
	    	
// 	    	return;
// 	    }
        
        
	}

}
?>