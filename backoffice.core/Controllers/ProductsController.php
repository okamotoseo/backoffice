<?php

class ProductsController extends MainController
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
	public $panel = 'Produtos';
	
	

	
    public function AvailableProducts(){
        
        
//         require ABSPATH."/library/phpqrcode/qrlib.php";
        
//         ob_start();
//         QRCode::png('tes de qr code');
//         $imageString = base64_encode( ob_get_contents() );
        
//         ob_end_clean();
//         // Exibe a imagem diretamente no navegador codificada em base64.
//         echo '<img src="data:image/png;base64,' . $imageString . '"></center>';
        
//         die;
         
    	$this->control_panel = 'Disponíveis';
    	
    	$this->title = 'Produtos Disponíveis';
    	
    	$this->menu = array("Products" => "active", "AvailableProducts" => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $availableProductModel = $this->load_model('Products/AvailableProductsModel');
        
        if($availableProductModel->ValidateForm()){
//             pre($availableProductModel);die;
            $list = $availableProductModel->GetAvailableProductsMarketplaces();
//             pre($list);die;
            $totalReg = $availableProductModel->TotalGetAvailableProductsMarketplace();
        }else{
            
            $list = $availableProductModel->ListAvailableProductsMarketplaces();
            
            $totalReg = $availableProductModel->TotalAvailableProducts();
        	
        }
        
        $categoryModel = $this->load_model('Products/CategoryModel');
         
        $listCategory = $categoryModel->ListCategory();
        
        $listCategoriesByProducts = $availableProductModel->ListCategoriesByProducts();
        
        $brandsModel = $this->load_model('Products/BrandsModel');
        
        $listBrands = $brandsModel->ListBrands();
        
//         $categoryModel = $this->load_model('Products/CategoryModel');
        
        $categories = explode(">", $availableProductModel->category);
        
        $listCategoriesRoot = $categoryModel->ListCategoriesRoot();
        
        $listCategoriesFromRoot = $categoryModel->ListCategoryFromRoot(trim($categories[0]));

//         $listCategoriesByProducts = array_merge($listCategoriesFromRoot, $listCategoriesRoot);
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Products/AvailableProductsView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }

    
    public function Category(){
    	 
    	$this->title = 'Categoria de Produtos';
    	 
    	$this->menu = array("Products" => "active", "Category" => "active");
    	 
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    	
    	$categoryModel = $this->load_model('Products/CategoryModel');
    	
    	$setAttributesModel = $this->load_model('Products/SetAttributesModel');
    	
    
    	if($categoryModel->ValidateForm()){
    		
    		$categoryModel->Save();
    		
    	}
    	
//     	$list = $categoryModel->ListCategory();
    	$list = $categoryModel->ListCategoryItems();
    	
    	
    	$listSetAttributes = $setAttributesModel->ListSetAttributes();
    	
    	
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/Products/CategoryView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    	
    	
    	
    }
    
    public function Brands(){
    
    	$this->title = 'Marcas de Produtos';
    
    	$this->menu = array("Products" => "active", "Brands" => "active");
    
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    
    	$brandsModel = $this->load_model('Products/BrandsModel');
    
    	if($brandsModel->ValidateForm()){
    
    		$brandsModel->Save();
    
    	}
    	 
    	$list = $brandsModel->ListProductsBrands();
    
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/Products/BrandsView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    }
    
    public function Colors(){
    
    	$this->title = 'Cores de Produtos';
    
    	$this->menu = array("Products" => "active", "Colors" => "active");
    
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    
    	$colorsModel = $this->load_model('Products/ColorsModel');
    
    	if($colorsModel->ValidateForm()){
    
    		$colorsModel->Save();
    
    	}
    
    	$list = $colorsModel->ListColors();
    
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/Products/ColorsView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    }
    
    public function Attributes(){
    	
    	$this->title = 'Atributos de Produtos';
    
    	$this->menu = array("Products" => "active", "Attributes" => "active");
    
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    
    	$attributesModel = $this->load_model('Products/AttributesModel');
    
    	if($attributesModel->ValidateForm()){
    
    		$attributesModel->Save();
    
    	}
    
    	$list = $attributesModel->ListAttributes();
    
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/Products/AttributesView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    }
    
    public function SetAttributes(){
    	 
    	$this->title = 'Conjunto de Atributos';
    
    	$this->menu = array("Products" => "active", "SetAttributes" => "active");
    
    	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
    	
    	$attributesModel = $this->load_model('Products/AttributesModel');
    	
    	$setAttributesModel = $this->load_model('Products/SetAttributesModel');
    	
    	$categoryModel = $this->load_model('Products/CategoryModel');
    
    	if($setAttributesModel->ValidateForm()){
    	    
    		$setAttributesModel->Save();
    
    	}
    
    	$listSetAttrHierarchy = $setAttributesModel->ListSetAttributesHierarchy();
    	
    	$attributesList = $attributesModel->ListAttributes();
    	
    	$listCategoriesRoot = $categoryModel->ListCategoriesRoot();
    	
    	$listCategoriesFromRoot = $categoryModel->ListCategoryFromRoot();
    	
    	require ABSPATH . '/Views/_includes/header.php';
    
    	require ABSPATH . '/Views/Products/SetAttributesView.php';
    
    	require ABSPATH . '/Views/_includes/footer.php';
    
    }
    
    
    public function Product(){
        
        $this->title = 'Cadastro de Produtos';
        $this->menu = array("Products" => "active", "Product" => "active");
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        $tabs = array(
        		"available-products",
        		"attributes",
        		"fotos",
        		"product-description",
        		"product-relational",
        		"publications",
        		"log"
        );
        
        $productsLogModel = $this->load_model('Products/ProductsLogModel');
        $brandsModel = $this->load_model('Products/BrandsModel');
        $colorsModel = $this->load_model('Products/ColorsModel');
        $setAttributesModel = $this->load_model('Products/SetAttributesModel');
        $availableProductModel = $this->load_model('Products/AvailableProductsModel');
        $attributesValuesModel = $this->load_model('Products/AttributesValuesModel');
        $productDescriptionModel = $this->load_model('Products/ProductDescriptionModel');
        $productRelationalModel = $this->load_model('Products/ProductRelationalModel');
        $publicationsModel = $this->load_model('Products/PublicationsModel');
        
        if($availableProductModel->ValidateForm()){
        	
        	$tabs['available-products'] = 'active';
            
            $availableProductModel->brand = friendlyText($availableProductModel->brand);
            $brandsModel->store_id = $availableProductModel->store_id;
            $brandsModel->brand = trim($availableProductModel->brand);
            $brandsModel->Save();
            
            $availableProductModel->color = friendlyText($availableProductModel->color);
            $colorsModel->store_id = $availableProductModel->store_id;
            $colorsModel->color = trim($availableProductModel->color);
            $colorsModel->Save();
            $availableProductModel->Save();
            
            $productDescriptionModel->product_id = trim($availableProductModel->id);
            $productDescriptionModel->parent_id = trim($availableProductModel->parent_id);
            $productDescriptionModel->sku = trim($availableProductModel->sku);
            
            $productDescriptionModel->productDescriptions[] = array(
                "title" => trim($availableProductModel->title), 
                "description" => trim($availableProductModel->description), 
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
            
            $tabs['attributes'] = 'active';
        }
        
        $productDescriptionModel->set_attribute_id = !empty($setAttributeId) ? $setAttributeId : '';
        
        if($productDescriptionModel->ValidateForm()){
            $productDescriptionModel->Generate();
        }
        
        
        if($productRelationalModel->ValidateForm()){
        	
        	$tabs['product-relational'] = 'active';
        	
            $productRelationalModel->Save();
        }
        
        $attributesValuesModel->category = $categoryId;//"MLB";
        $attributesValuesModel->product_id = $availableProductModel->id;
        $attributesValuesModel->set_attribute_id = $setAttributeId;
        
        //Carrega Inputs Marketplace
        $listInputsAttrMkt = $setAttributesModel->ListInputsMarketplace();
        //Carrega Inputs Padrão Sysplace
        $listInputsAttr = $setAttributesModel->ListInputsAttr();
        //Carrega os valores com os alias
        $attributesValues = $attributesValuesModel->LoadAlias();
        //Carrega os produtos relacionados
        $listRelational = $productRelationalModel->ListRelational();
        
        $productsLogModel->product_id = $availableProductModel->id;
        $listLog = $productsLogModel->GetProductsLog();
        
        $categoryModel = $this->load_model('Products/CategoryModel');
        
        $categories = explode(">", $availableProductModel->category);
        
        $listCategoriesRoot = $categoryModel->ListCategoriesRoot();
        
        $listCategoriesFromRoot = $categoryModel->ListCategoryFromRoot(trim($categories[0]));
        
        $parentsProduct = $availableProductModel->LoadParent();
        $shippingReference = $shippingRate = '';
        if(!empty($categoryId)){
        	
        	$shipmentsModel = parent::load_module_model('Mercadolivre/Models/Api/ShipmentsRestModel');
        	
        	$shipmentsModel->category_id = $categoryId;
        	$shippingReferences = $shipmentsModel->getShippingReference();
        	
        	if(isset($shippingReferences['body']->dimensions)){
        		
        		$shippingReference = $shippingReferences['body']->dimensions;
        		
        		if(empty(trim($availableProductModel->height)) ){
        			$availableProductModel->height =  $shippingReference->height;
        		}
        		if(empty(trim($availableProductModel->length)) ){
        			$availableProductModel->length =  $shippingReference->length;
        		}
        		if(empty(trim($availableProductModel->width)) ){
        			$availableProductModel->width =  $shippingReference->width;
        		}
        		if(empty(trim($availableProductModel->weight)) ){
        			$availableProductModel->weight =  $shippingReference->weight;
        		}
        	}
        	
        	$peso = $availableProductModel->weight < 100 ? $availableProductModel->weight * 1000 : $availableProductModel->weight ;
        	$shippingPrice = $shipmentsModel->getShippingPrice(array(
        			'mlb_category_id' => $categoryId,
        			'mlb_item_price' => $availableProductModel->sale_price,
        			'mlb_item_altura' => ceil($availableProductModel->height),
        			'mlb_item_largura' => ceil($availableProductModel->length),
        			'mlb_item_profundidade' => ceil($availableProductModel->width),
        			'mlb_item_peso' => $peso
        	));
        	
			if(isset($shippingPrice['body']->coverage->all_country->list_cost)){
        		$shippingRate = $shippingPrice['body']->coverage->all_country->list_cost; 
			}
        	
        }
        
        $advertsModel = parent::load_module_model('Mercadolivre/Models/Adverts/AdvertsModel');
        
        $advertsModel->store_id = $availableProductModel->store_id;
        
        $advertsModel->sku = $availableProductModel->parent_id;
        
        $mlPublications = $advertsModel->GetParentsAdverts();
        
        $publicationsModel->sku = $availableProductModel->sku;
        
        $publicationsModel->parent_id = $availableProductModel->parent_id;
        
        $ecommerce = isset($this->moduledata['Ecommerce'][0]) ? strtolower(trim($this->moduledata['Ecommerce'][0])) : 'ecommerce';
         
        $ecommerce = ucfirst($ecommerce);
         
        if($ecommerce == 'Mg2'){
        	$ecommerce = 'Magento2';
        }
        
        $ecPublications = $publicationsModel->getEcAdverts($ecommerce);
        
        $skPublications = $publicationsModel->getSkAdverts();
        
//         $azPublications = $publicationsModel->getAzAdverts();
        
//         $azCategoryModel = parent::load_module_model('Amazon/Models/Map/AzCategoryModel');
        
//         $azCategoryModel->category = $availableProductModel->category;
        
//         $azCategoryRelationship = $azCategoryModel->getCategoryRelationship();
        
//         if(isset($azCategoryRelationship['tree_id'])){
        	
//         	$azAttributesModel = parent::load_module_model('Amazon/Models/Map/AzAttributesModel');
        	
//         	$azRefinementsModel = parent::load_module_model('Amazon/Models/Map/AzRefinementsModel');
        	
//         	$azBaseXsdModel = parent::load_module_model('Amazon/Models/Map/AzBaseXsdModel');
        	
//         	$azFeedProductTypeModel = parent::load_module_model('Amazon/Models/Map/AzFeedProductTypeModel');
        	
//         	$azFeedProductTypeModel->tree_id = $azCategoryRelationship['tree_id'];
        	
//         	$listAttributesRequired = $azFeedProductTypeModel->GetFeedProductTypeValues();
// 	        $azAttributesModel->category = $availableProductModel->category;
	        
// 	        if($azAttributesModel->LoadXsd()){
	        	
// 	        	$simpleValueXsd = $azAttributesModel->xsdSimpleType();
	        	
// 		        $azAttributesModel->LoadXsdBase();
		        
// 		        $azBaseXsdModel->xpathBase = $azAttributesModel->xpathBase;

// 		        $simpleValueBase = $azBaseXsdModel->baseXsdSimpleType();
		       
// 		        $simpleValueXsd = array_merge($simpleValueXsd, $simpleValueBase);
		        
// 		        $listAttributesRequiredXsd = $azAttributesModel->ListAttributesRequired();
// 		        $azRefinementsModel->tree_id = $azAttributesModel->tree_id;
// 		        $listRefinements = $azRefinementsModel->GetCategoryRefinements();
		        
		        
// 		        foreach($listRefinements as $j => $refinement){
// 		        	$listAttributesRequiredXsd[] = $refinement;
// 		        }
// 		        $listAttributesRequiredXsd = array_reverse($listAttributesRequiredXsd);
		        
// 		        $azAttributesRelationship = $azAttributesModel->GetAzAttributesRelationship();
		        
// 	        }
        
//     	}
    	
    	
        $this->includes = array( "js" => array(
            
            "/Modules/Mercadolivre/Views/js/Mercadolivre.js",
        		
//         	"/Modules/Amazon/Views/js/Amazon.js",
        		
        	"/Modules/{$ecommerce}/Views/js/Module{$ecommerce}.js",
            
            "/Modules/Skyhub/Views/js/ModuleSkyhub.js"
            
            )
        );
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Products/ProductView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
    }
    
    public function Label(){
        
        $this->control_panel = 'Disponíveis';
        
        $this->title = 'Produtos Disponíveis';
        
        
        $this->menu = array("Products" => "active", "AvailableProducts" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $availableProductModel = $this->load_model('Products/AvailableProductsModel');
        
        $storeModel = $this->load_model('Admin/StoreModel');
        
        $key = array_search('id', $parametros);
        
        if(!empty($key)){
            
            $availableProductModel->id = get_next($parametros, $key);
            
            $availableProductModel->Load();
            
            $storeModel->id = $availableProduct->store_id;
            
            $storeModel->Load();
        }
        
//         $this->includes = array("js" => "/Views/js/orderDetail.js");
        
        require ABSPATH . '/Views/_includes/header_popup.php';
        
        require ABSPATH . '/Views/Products/LabelView.php';
        
        require ABSPATH . '/Views/_includes/footer_popup.php';
        
    }
 
}
