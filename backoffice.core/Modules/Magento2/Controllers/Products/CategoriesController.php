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
		 
		$this->includes = array("js" => "/Modules/Magento2/Views/js/ModuleMagento2.js");
		 
		$categoryModel = parent::load_model('Products/CategoryModel');
		 
		$categoriesMg2Model = parent::load_module_model('Magento2/Models/Products/CategoriesModel');
		 
		// 	    $categoriesTemp = $categoriesTempModel->ListCategoriesTemp();
		$categoriesMg2 = $categoriesMg2Model->ListCategoriesMg2();
		 
		$listCategories = $categoryModel->ListCategory();
		 
		require ABSPATH . "/Modules/Magento2/Views/Products/CategoriesView.php";
		 
		 
	}


}
?>