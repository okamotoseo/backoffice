<?php
class ProductsController extends MainController
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

	public function Products() 
	{
	    
	    $this->title = 'Produtos do Marketplace: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Marketplace/Views/js/Marketplace.js");
	    
	    $productsModel = parent::load_module_model('Marketplace/Models/Products/ProductsModel');
	    
	        
	        if($productsModel->ValidateForm()){
	        	
	        	$productsFeed = $productsModel->GetProducts();
	            
	        }else{
	        	
	        	$productsFeed = $productsModel->ListProducts();
	        }
	        $totalReg = $productsModel->TotalProducts();
	        
	        require ABSPATH . "/Modules/Marketplace/Views/Products/ProductsView.php";
        
        
	}

}
?>