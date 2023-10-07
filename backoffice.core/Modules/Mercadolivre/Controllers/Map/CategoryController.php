<?php
class CategoryController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Category() 
	{
	    
	    $this->title = 'Mapear Categorias Mercadolivre: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Mercadolivre/Views/js/Map.js");
	    
	    $categoryModel = parent::load_model('Products/CategoryModel');
	    
	    $mlCategoryModel = parent::load_module_model('Mercadolivre/Models/Map/MlCategoryModel');
	    
	    $listCategories = $categoryModel->ListCategoryChild();
	    $listCategories = $categoryModel->ListCategory();
	    
	    $listCategoryRelationship = $mlCategoryModel->CategoryRelationshipAttr();
// 	    pre($listCategoryRelationship);die;
	    
// 	    $listCategoryRelationship = $mlCategoryModel->CategoryRelationship();
	    
	    $listDefaultCategoriesMl = $mlCategoryModel->defaultCategoriesML();
	    
	    
	    
        require ABSPATH . "/Modules/Mercadolivre/Views/Map/CategoryView.php";
        
	}

}
?>