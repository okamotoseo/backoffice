<?php

class ReportController extends MainController
{

	/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = true;
	
	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required = 'any';
	
	/**
	 * $panel
	 *
	 * Painel de controle
	 *
	 * @access public
	 */
	public $panel = 'Relatório';

	public function Sales(){
	    
	    $this->title = 'Pedidos';
	    
	    $this->menu = array("Report" => "active", "Sales" => "active", "ReportSales" => "active");
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $salesModel = $this->load_model('Report/SalesModel');
	    
	    $availableProductModel = $this->load_model('Products/AvailableProductsModel');
	    
	    $ordersModel = $this->load_model('Orders/ManageOrdersModel');
	    
	    
	    if($salesModel->ValidateForm()){
	    	
	        $list = $salesModel->GetSales();
	        
	    }else{
	        
	        $list = $salesModel->ListSales();
	        
	    }
	    
	    $marketplaceOrder = $ordersModel->listMarketplaceOrders();
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Report/SalesView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
	public function Returns(){
	    
	    $this->title = 'Pedidos Devolvidos';
	    
	    $this->menu = array("Report" => "active", "Sales" => "active", "ReportReturns" => "active");
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $returnsModel = $this->load_model('Report/ReturnsModel');
	    
	    $availableProductModel = $this->load_model('Products/AvailableProductsModel');
	    
	    $ordersModel = $this->load_model('Orders/ManageOrdersModel');
	    
	    
	    if($returnsModel->ValidateForm()){
	        
	        $list = $returnsModel->GetReturns();
	        
	    }
	    
	    $marketplaceOrder = $ordersModel->listMarketplaceOrders();
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Report/ReturnsView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
    public function ProductSales(){
    	
    	$this->title = 'Vendas de produtos';
    	
    	$this->menu = array("Report" => "active", "Sales" => "active", "ProductSales" => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $productSalesModel = $this->load_model('Report/ProductSalesModel');
        
        $availableProductModel = $this->load_model('Products/AvailableProductsModel');
        
        $ordersModel = $this->load_model('Orders/ManageOrdersModel');
        
        
        if($productSalesModel->ValidateForm()){
            
//             $list = $productSalesModel->GetSalesProductSkuFanlux();
        	$list = $productSalesModel->GetSalesProductSku();
            
        }
        
        $brandsModel = $this->load_model('Products/BrandsModel');
        
        $brands = $brandsModel->ListProductsBrands();
        
        $marketplaceOrder = $ordersModel->listMarketplaceOrders();
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Report/ProductSalesView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    public function BrandSales(){
    	 
    	$this->title = 'Vendas de produtos por marca';
    	 
    	$this->menu = array("Report" => "active", "Sales" => "active", "BrandSales" => "active");
    	 
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    
    	$brandSalesModel = $this->load_model('Report/BrandSalesModel');
    
    	$availableProductModel = $this->load_model('Products/AvailableProductsModel');
    
    	$brandsModel = $this->load_model('Products/BrandsModel');
    	
    	$ordersModel = $this->load_model('Orders/ManageOrdersModel');
    
    
    	if($brandSalesModel->ValidateForm()){
    
    		$list = $brandSalesModel->GetBrandGroupSales();
    
    	}
    	
    	$brandsModel = $this->load_model('Products/BrandsModel');
    	
    	$brands = $brandsModel->ListProductsBrands();
    	
    
    	$listBrands = $brandsModel->ListBrands();
    	
    	$marketplaceOrder = $ordersModel->listMarketplaceOrders();
    
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/Report/BrandSalesView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    }
    
    public function Inventory(){
    	 
    	$this->title = 'Inventario de produtos publicados';
    	 
    	$this->menu = array("Report" => "active", "Inventory" => "active");
    	 
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    
    	$inventoryModel = $this->load_model('Report/InventoryModel');
    	
    	$brandsModel = $this->load_model('Products/BrandsModel');
    
//     	$availableProductModel = $this->load_model('Products/AvailableProductsModel');
    
    	if($inventoryModel->ValidateForm()){
    
    		$list = $inventoryModel->GetProduct();
    
    	}else{
    
    		$list = $inventoryModel->ListProduct();
    
    	}
    	
    	$brands = $brandsModel->ListProductsBrands();
    	
    	$listBrands = $brandsModel->ListBrands();
    	
    
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/Report/InventoryView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    }
    
    public function Questions(){
    
    	$this->title = 'Perguntas por produto';
    	
    	$this->control_panel = 'Perguntas';
    	
    	$this->menu = array("Report" => "active", "Questions" => "active");
    
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    
    	$questionsModel = $this->load_model('Sac/QuestionsModel');
    	
    	$ordersModel = $this->load_model('Orders/ManageOrdersModel');
    
//     	if($questionsModel->ValidateForm()){
    
//     		$list = $questionsModel->GetProducts();
    
//     	}else{
    
//     		$list = $questionsModel->ListProducts();
    
//     	}

    	$list = $questionsModel->ReportQuestions();
    	
    	
    	$marketplaceOrder = $ordersModel->listMarketplaceOrders();
    
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/Report/QuestionsView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    }
    
 
}
