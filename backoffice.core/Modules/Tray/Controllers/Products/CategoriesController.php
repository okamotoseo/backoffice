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
		 
		$categoriesTrayModel = parent::load_module_model('Tray/Models/Products/CategoriesModel');
		 
		$categoriesTray = $categoriesTrayModel->ListCategoriesTray();
		 
		$listCategories = $categoryModel->ListCategory();
		 
		require ABSPATH . "/Modules/Tray/Views/Products/CategoriesView.php";
		 
		 
	}


}
?>