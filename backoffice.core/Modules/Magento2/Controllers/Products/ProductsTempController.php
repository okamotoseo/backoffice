<?php



class ProductsTempController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

	public function ProductsTemp() 
	{
	    
	    $this->title = 'Produtos Integrados: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>"; 
	        
	    $this->includes = array("js" => "/Modules/Magento2/Views/js/ModuleMagento2.js");
	    
	    $productsTempModel = parent::load_module_model('Magento2/Models/Products/ProductsTempModel');
	    
	    if($productsTempModel->ValidateForm()){
	    	
	    	$productsTemp = $productsTempModel->GetProductsTemp();
	    	
	    }else{
	    	
	    	$productsTemp = $productsTempModel->ListProductsTemp();
	    	
	    }
	    
	    
	    $totalReg = $productsTempModel->TotalProductsTemp();
	    
	    require ABSPATH . "/Modules/Magento2/Views/Products/ProductsTempView.php";
        
        
	}

}
?>