<?php


class CategoriesXmlController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }


	

	public function CategoriesXml()
	{
		 
		$this->title = 'Categorias Shopee: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
		 
		$categoryModel = parent::load_model('Products/CategoryModel');
		 
		$categoriesShopeeModel = parent::load_module_model('Shopee/Models/Products/CategoriesModel');

		$categoriesRoot = $categoriesShopeeModel->ListCategoriesRoot();
		
		$categoriesHierarchyShopee = $categoriesShopeeModel->ListCategoriesHierarchy();
		
		$categoriesRelationshipShopee = $categoriesShopeeModel->ListCategoiesIdsRelationship();
		
		$listCategoriesXml = $categoryModel->ListCategoryGoogleXml();
		 
		require ABSPATH . "/Modules/Shopee/Views/Products/CategoriesXmlView.php";
		 
		 
	}
	

}
?>