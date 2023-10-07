<?php

class DeveloperController extends MainController
{
	/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = true;
	
	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required = 'any';
	
	/**
	 * $panel
	 *
	 * Painel de controle
	 *
	 * @access public
	 */
	public $panel = 'Desenvolvimento';
	
	
	
	
	
	
	public function Modules() {
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = 'Cadastrar de Modulo';
	    
	    $this->menu = array("Developer" => "active", "Modules" => "active");
	    
	    $modulesModel = $this->load_model('Modules/ModulesModel');
	    
	    if($modulesModel->ValidateForm()){
// 	        pre($modulesModel);die;
	        
	        $modulesModel->Save();
	    }
	    
	    
	    $modules = $modulesModel->ListModules();
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Developer/ModulesView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
	public function infoPhp() {
		 
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		 
		$this->title = 'Versão PHP';
		 
		$this->menu = array("Developer" => "active", "infoPhp" => "active");
		 
		require ABSPATH . '/Views/_includes/header.php';
		 
		require ABSPATH . '/Views/Developer/infoPhpView.php';
		 
		require ABSPATH . '/Views/_includes/footer.php';
		 
	}
	
	
	public function Product(){
	    
	    $this->title = 'Cadastro de Produtos';
	    
	    $this->menu = array("Products" => "active", "Product" => "active");
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    
	    $brandsModel = $this->load_model('Products/BrandsModel');
	    
	    $colorsModel = $this->load_model('Products/ColorsModel');
	    
	    $setAttributesModel = $this->load_model('Products/SetAttributesModel');
	    
	    $availableProductModel = $this->load_model('Products/AvailableProductsModel');
	    
	    $attributesValuesModel = $this->load_model('Products/AttributesValuesModel');
	    
	    $productDescriptionModel = $this->load_model('Products/ProductDescriptionModel');
	    
	    $productRelationalModel = $this->load_model('Products/ProductRelationalModel');
	    
	    $publicationsModel = $this->load_model('Products/PublicationsModel');
	    
	    
	    if($availableProductModel->ValidateForm()){
	        
	        $availableProductModel->brand = friendlyText($availableProductModel->brand);
	        $brandsModel->store_id = $availableProductModel->store_id;
	        $brandsModel->brand = $availableProductModel->brand;
	        $brandsModel->Save();
	        
	        $availableProductModel->color = friendlyText($availableProductModel->color);
	        $colorsModel->store_id = $availableProductModel->store_id;
	        $colorsModel->color = $availableProductModel->color;
	        $colorsModel->Save();
	        $availableProductModel->Save();
	        
	        $productDescriptionModel->product_id = $availableProductModel->id;
	        $productDescriptionModel->parent_id = $availableProductModel->parent_id;
	        $productDescriptionModel->sku = $availableProductModel->sku;
	        
	        $productDescriptionModel->productDescriptions[] = array(
	            "title" => $availableProductModel->title,
	            "description" => $availableProductModel->description,
	            "marketplace" => "default"
	            
	        );
	        $productDescriptionModel->set_attribute_id = $availableProductModel->getSetAttributeRelationship();
	        $productDescriptionModel->Generate();
	        
	        
	    }
	    
	    
	    $categoryId = $availableProductModel->getCategoryRelationship();
	    
	    $categoryInfo = $availableProductModel->getCategoryRelationshipInformations();
	    $setAttributeId = $availableProductModel->getSetAttributeRelationship();
	    
	    $setAttributesModel->category = $categoryId;//"MLB";
	    $setAttributesModel->id = $setAttributeId;
	    
	    if($attributesValuesModel->ValidateForm()){
	        $attributesValuesModel->Save();
	    }
	    
	    $productDescriptionModel->set_attribute_id = !empty($setAttributeId) ? $setAttributeId : '';
	    
	    if($productDescriptionModel->ValidateForm()){
	        $productDescriptionModel->Generate();
	    }
	    
	    
	    if($productRelationalModel->ValidateForm()){
	        $productRelationalModel->Save();
	    }
	    
	    
	    $attributesValuesModel->category = $categoryId;//"MLB";
	    
	    $attributesValuesModel->product_id = $availableProductModel->id;
	    
	    $attributesValuesModel->set_attribute_id = $setAttributeId;
	    
	    
	    //Carrega Inputs Marketplace
	    $listInputsAttrMkt = $setAttributesModel->ListInputsMarketplace();
	    //         pre($setAttributesModel);die;
	    
	    //Carrega Inputs Padrão Sysplace
	    $listInputsAttr = $setAttributesModel->ListInputsAttr();
	    
	    
	    //Carrega os valores com os alias
	    $attributesValues = $attributesValuesModel->LoadAlias();
	    
	    //Carrega os produtos relacionados
	    $listRelational = $productRelationalModel->ListRelational();
	    
	    //         //carrega publicacoes
	    //         if(isset($availableProductModel->sku)){
	    
	    //             $publicationsModel->ValidateForm();
	    
	    //             $publicationsModel->sku = $availableProductModel->sku;
	    
	    //             $mlPublications = $publicationsModel->getMlAdverts();
	    //         }
	    
	    $filter = array(
	        "product_id" => $availableProductModel->id,
	        "controller" => "Products"
	    );
	    $listLog = $availableProductModel->listLog($this->db, $filter);
	    
	    $categoryModel = $this->load_model('Products/CategoryModel');
	    
	    $categories = explode(">", $availableProductModel->category);
	    
	    $listCategoriesRoot = $categoryModel->ListCategoriesRoot();
	    
	    $listCategoriesFromRoot = $categoryModel->ListCategoryFromRoot(trim($categories[0]));
	    
	    $brandsModel = $this->load_model('Products/BrandsModel');
	    
	    $listBrands = $brandsModel->ListBrands();
	    
	    $colorsModel = $this->load_model('Products/ColorsModel');
	    
	    $listColors = $colorsModel->ListColors();
	    
	    $parentsProduct = $availableProductModel->LoadParent();
	    
	    
	    $advertsModel = parent::load_module_model('Mercadolivre/Models/Adverts/AdvertsModel');
	    
	    $advertsModel->store_id = $availableProductModel->store_id;
	    
	    $advertsModel->sku = $availableProductModel->parent_id;
	    
	    $mlPublications = $advertsModel->GetParentsAdverts();
	    
	    $publicationsModel->sku = $availableProductModel->sku;
	    
	    $ecPublications = $publicationsModel->getEcAdverts();
	    
	    $skPublications = $publicationsModel->getSkAdverts();
	    //         pre($ecPublications);die;
	    

	    	 
	    $azAttributesModel = parent::load_module_model('Amazon/Models/Map/AzAttributesModel');
	    $azAttributesModel->category = $availableProductModel->category;
	    $azAttributesModel->LoadXsd();
	    $listAttributesRequired = $azAttributesModel->ListAttributesRequired();
	    
	   
	    
	    $this->includes = array( "js" => array(
	        
	        "/Modules/Mercadolivre/Views/js/Mercadolivre.js",
	        
	        "/Modules/Onbi/Views/js/ModuleOnbi.js",
	        
	        "/Modules/Skyhub/Views/js/ModuleSkyhub.js"
	        
	    )
	    );
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Products/NewProductView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
}


}
?>
