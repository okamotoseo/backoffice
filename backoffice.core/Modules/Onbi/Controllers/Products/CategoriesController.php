<?php



class CategoriesController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }


	
	public function Categories()
	{
	    
	    $this->title = 'Categorias Ecommerce: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $this->includes = array("js" => "/Modules/Onbi/Views/js/ModuleOnbi.js");
	    
	    $categoryModel = parent::load_model('Products/CategoryModel');
	    
	    $categoriesOnbiModel = parent::load_module_model('Onbi/Models/Products/CategoriesModel');
	    
// 	    $categoriesTemp = $categoriesTempModel->ListCategoriesTemp();
	    $categoriesOnbi = $categoriesOnbiModel->ListCategoriesOnbi();
	    
	    $listCategories = $categoryModel->ListCategory();
	    
	    require ABSPATH . "/Modules/Onbi/Views/Products/CategoriesView.php";
	    
	    
	}


}
?>