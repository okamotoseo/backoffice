<?php
class CategoryController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->store_id = $controller->userdata['store_id'];
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Category() 
	{
	    $setupModel = parent::load_module_model('Vtex/Models/Setup/SetupModel');
	    
	    $setupModel->store_id = $this->store_id;
	    
	    $res = $setupModel->Load();
	    
	    $this->title = 'Mapear Categorias Vtex: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $categoryModel = parent::load_model('Products/CategoryModel');
	    
	    $vtexCategoryModel = parent::load_module_model('Vtex/Models/Products/VtexCategoryModel');
        
        
        if($setupModel->input_data == 'default'){
            
            $listCategories = $categoryModel->ListCategoryChild();
            
            $listCategoryRelationship = $vtexCategoryModel->CategoryRelationship();
            
            $listDefaultCategoriesVtex = $vtexCategoryModel->defaultCategoriesVtex();
            
            require ABSPATH . "/Modules/Vtex/Views/Products/CategoryView.php";
            
            
        }
        
        
        if($setupModel->input_data == 'google_xml'){
            
            $listCategoriesXml = $categoryModel->ListCategoryGoogleXml();
            
            $listCategoryRelationship = $vtexCategoryModel->CategoryRelationship();
            
            $listDefaultCategoriesVtex = $vtexCategoryModel->defaultCategoriesVtex();
            
            require ABSPATH . "/Modules/Vtex/Views/Products/CategoryXmlView.php";
            
        }
        
	}

}
?>