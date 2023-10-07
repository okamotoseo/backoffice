<?php


class CategoriesController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        $this->store_id = $controller->userdata['store_id'];
        
        
    }


	

	public function Categories()
	{
		
		$setupModel = parent::load_module_model('Shopee/Models/Setup/SetupModel');
		
		$setupModel->store_id = $this->store_id;
		
		$setupModel->Load();
		
		$this->title = "Mapear Categorias Shopee {$setupModel->input_data}: <b>".$this->userdata['stores'][$this->userdata['store_id']]."</b>";
		 
		$categoryModel = parent::load_model('Products/CategoryModel');
		 
		$categoriesShopeeModel = parent::load_module_model('Shopee/Models/Products/CategoriesModel');

		$categoriesRoot = $categoriesShopeeModel->ListCategoriesRoot();
		
		$categoriesHierarchyShopee = $categoriesShopeeModel->ListCategoriesHierarchy();
		
		
		if($setupModel->input_data == 'default'){
		
    		$categoriesRelationshipShopee = $categoriesShopeeModel->ListCategoiesIdsRelationship();
    		
    		$listCategories = $categoryModel->ListCategory();
    		 
    		require ABSPATH . "/Modules/Shopee/Views/Products/CategoriesView.php";
		 

		}
	    
	    
		if($setupModel->input_data == 'google_xml'){
	    
    	    $categoriesRelationshipShopee = $categoriesShopeeModel->ListHierarchyRelationship();
    	    
    	    $listCategoriesXml = $categoryModel->ListCategoryGoogleXml();
    	    
    	    require ABSPATH . "/Modules/Shopee/Views/Products/CategoriesXmlView.php";
	    
		}
	    
	    
	}
	

}
?>