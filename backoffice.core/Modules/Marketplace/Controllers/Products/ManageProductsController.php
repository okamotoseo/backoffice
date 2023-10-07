<?php
class ManageProductsController extends MainController
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

	
	public function ManageProducts()
	{
		 
		$this->title = 'Produtos Sysplace Marketplace';
		 
		$this->includes = array("js" => "/Modules/Marketplace/Views/js/Marketplace.js");
		 
		$productsModel = parent::load_module_model('Marketplace/Models/Products/ManageProductsModel');
		 
		 
		if($productsModel->ValidateForm()){
	 
			$productsFeed = $productsModel->GetProducts();
			 
		}else{
	
// 			$productsFeed = $productsModel->ListProducts();
			$productsFeed = $productsModel->ListProductsByParent();
		}
		$totalReg = $productsModel->TotalProducts();
		
		$sellers = $productsModel->getSellersByProducts();
		
		$sellerCategories = $productsModel->getSellerCategories();
		 
		require ABSPATH . "/Modules/Marketplace/Views/Products/ManageProductsView.php";
	
	
	}

}
?>