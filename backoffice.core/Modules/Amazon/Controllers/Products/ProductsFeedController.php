<?php
class ProductsFeedController extends MainController
{

    
	public function __construct($db = false,  $controller = null, $storeId = null)
	{
		$this->db = $db;
			
		$this->store_id = $storeId;
			
		$this->controller = $controller;
	
		if(isset($this->controller)){
	
			$this->parametros = $this->controller->parametros;
	
			$this->userdata = $this->controller->userdata;
	
			$this->store_id = $this->controller->userdata['store_id'];
		}
        
        
    }

	public function ProductsFeed() 
	{
	    
	    $this->title = 'Produtos do Feed: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
// 	    $this->includes = array("js" => "/Modules/Amazon/Views/js/ModuleAmazon.js");
	    
	    $productsModel = parent::load_module_model('Amazon/Models/Products/ProductsModel');
	    
// 	    if(isset($productsModel->token) && isset($productsModel->password)){
	        
	        if($productsModel->ValidateForm()){
	        	
	        	$productsFeed = $productsModel->GetProducts();
	            
	        }else{
	        	
	        	$productsFeed = $productsModel->ListProducts();
	        }
	        $totalReg = $productsModel->TotalProducts();
	        require ABSPATH . "/Modules/Amazon/Views/Products/ProductsView.php";
        
// 	    }else{
	    	
// 	    	gotoPage('Modules', 'Configuration/Adj/Setup');
	    	
// 	    	return;
// 	    }
        
        
	}

}
?>