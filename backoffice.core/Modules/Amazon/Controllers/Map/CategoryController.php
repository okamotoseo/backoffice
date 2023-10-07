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
	    
	    $this->title = 'Mapear Categorias Amazon: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Amazon/Views/js/Map.js");
	    
	    $categoryModel = parent::load_model('Products/CategoryModel');
	    
	    $azCategoryModel = parent::load_module_model('Amazon/Models/Map/AzCategoryModel');
	    
// 	    $listCategories = $categoryModel->ListCategoryChild();
	    
	    $listCategories = $categoryModel->ListCategory();
	    
// 	    $listCategoryRelationship = $azCategoryModel->CategoryRelationshipAttr();
// 	    pre($listCategoryRelationship);die;
	    
// 	    $listCategoryRelationship = $azCategoryModel->CategoryRelationship();
	    
	    $listDefaultCategoriesAz = $azCategoryModel->defaultCategoriesAz();
	    
	    
	    
        require ABSPATH . "/Modules/Amazon/Views/Map/CategoryView.php";
        
	}

}
?>