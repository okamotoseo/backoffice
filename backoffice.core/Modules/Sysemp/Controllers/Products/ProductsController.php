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
		if($this->userdata['cpf'] != '30456130802'){
			echo "<h1>Módulo em Manutenção</h1>";
			return;
		}else{
			echo $this->form_msg = '<h2>MODO DEBUG: Este Módulo esta em manutenção ou desenvolvimento... </h2>';
		}
	    $this->title = 'Configuração módulo Adj SIG: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Sysemp/Views/js/ModuleAdj.js");
	    
	    $productsModel = parent::load_module_model('Sysemp/Models/Products/ProductsModel');
	    
// 	    if(isset($productsModel->token) && isset($productsModel->password)){
	        
	        if($productsModel->ValidateForm()){
	        	
	            $products = $productsModel->ListProducts();
	            
	        }
	        require ABSPATH . "/Modules/Sysemp/Views/Products/ProductsView.php";
        
// 	    }else{
	    	
// 	    	gotoPage('Modules', 'Configuration/Adj/Setup');
	    	
// 	    	return;
// 	    }
	}

}
?>