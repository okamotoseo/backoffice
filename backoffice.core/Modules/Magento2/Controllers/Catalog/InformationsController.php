<?php



class InformationsController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

	public function Informations() 
	{
	    
	    $this->title = 'Informações do produto: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Onbi/Views/js/ModuleOnbi.js");
	    
	    $productsModel = parent::load_module_model('Onbi/Models/Catalog/ProductsModel');
	    
	    $attributesModel = parent::load_module_model('Onbi/Models/Catalog/AttributesModel');
	    
	    $categoriesModel = parent::load_module_model('Onbi/Models/Catalog/CategoriesModel');
	    
	    if ( in_array('Product', $this->parametros )) {
	        
	        $key = array_search('Product', $this->parametros);
	        
	        $productsModel->product_id = get_next($this->parametros, $key);
	        $productInfo = $productsModel->catalogProductListOfAdditionalAttributes();
	        $productInfo = $productsModel->catalogProductInfo();
	        
	    }
// 	    pre($productInfo);die;
	    
// 	    $attributesModel->product_id = $productInfo->product_id;
	    
// 	    $productAttr = $attributesModel->catalogProductAttributeInfo();
	    
// 	    $categoriesModel->categories = $productInfo->categories;
	    
// 	    $categoryInfo = $categoriesModel->catalogCategoryInfo();
	    
	    
	    require ABSPATH . "/Modules/Onbi/Views/Catalog/InformationsView.php";
        
        
	}

}
?>