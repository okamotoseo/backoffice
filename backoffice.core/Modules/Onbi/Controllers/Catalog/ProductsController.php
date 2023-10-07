<?php



class ProductsController extends MainController
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
	    
	    
	    $productsTemp = $productsTempModel->catalogProductList();
	    
	    require ABSPATH . "/Modules/Onbi/Views/Products/ProductsTempView.php";
        
        
	}
	
	public function Products()
	{
	    
	    $this->title = 'Produtos Ecommerce Onbi: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $this->includes = array("js" => "/Modules/Onbi/Views/js/ModuleOnbi.js");
	    
	    $productsModel = parent::load_module_model('Onbi/Models/Catalog/ProductsModel');
	    
	    $products = $productsModel->catalogProductList();
	    
	    require ABSPATH . "/Modules/Onbi/Views/Catalog/ProductsView.php";
	    
	    
	}

}
?>