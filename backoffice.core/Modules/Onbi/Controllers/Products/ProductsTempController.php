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
	    
	    $this->title = 'Produtos Temporarios: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>"; 
	        
	    $this->includes = array("js" => "/Modules/Onbi/Views/js/ModuleOnbi.js");
	    
	    $productsTempModel = parent::load_module_model('Onbi/Models/Products/ProductsTempModel');
	    
	    
	    if($productsTempModel->ValidateForm()){
	    	 
	    	$productsTemp = $productsTempModel->GetProductIdBySku();
	    
	    	$totalReg = $productsTempModel->TotalGetProducts();
	    
	    }else{
	    	$productsTemp = $productsTempModel->ListProductIdBySku();
	    
	    	$totalReg = $productsTempModel->TotalProductsTemp();
	    }
	    require ABSPATH . "/Modules/Onbi/Views/Products/ProductsTempView.php";
        
        
	}

}
?>