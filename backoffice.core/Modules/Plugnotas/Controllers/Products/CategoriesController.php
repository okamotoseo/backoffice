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
		 
// 		$this->includes = array("js" => "/Modules/Viavarejo/Views/js/ModuleMagento2.js");
		 
		$categoryModel = parent::load_model('Products/CategoryModel');
		 
		$categoriesViavarejoModel = parent::load_module_model('Viavarejo/Models/Products/CategoriesModel');
		 
		// 	    $categoriesTemp = $categoriesTempModel->ListCategoriesTemp();
// 		$categoriesViavarejo = $categoriesMg2Model->ListCategoriesViavarejo();
		 
		$listCategories = $categoryModel->ListCategory();
		 
		require ABSPATH . "/Modules/Viavarejo/Views/Products/CategoriesView.php";
		 
		 
	}


}
?>