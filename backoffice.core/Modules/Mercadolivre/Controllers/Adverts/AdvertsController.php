<?php
class AdvertsController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function Adverts() 
	{
	    
	    $this->title = 'Anúncios publicados: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $availableProductDescModel = parent::load_model('Products/AvailableProductsModel');
	    
	    $advertsModel = parent::load_module_model('Mercadolivre/Models/Adverts/AdvertsModel');
	    
	    if($advertsModel->ValidateForm()){
	        
// 	        $list = $advertsModel->GetAdvertsAvailableProducts();
	        $list = $advertsModel->GetAdverts();
	        
	    }else{
	        
// 	        $list = $advertsModel->ListAdvertsAvailableProducts();
	        
	        $list = $advertsModel->ListAdverts();
	        
	    }

	    $totalReg = $advertsModel->TotalAdverts();
	    
	    $advertsStatus = $advertsModel->getAdvertsStatus();
	    
	    
// 	    $advertsIds = $advertsModel->ListIdAdverts();
	    
// 	    $categoryModel =  parent::load_model('Products/CategoryModel');
	    
// 	    $listCategory = $categoryModel->ListCategory();
	    
// 	    $brandsModel =  parent::load_model('Products/BrandsModel');
	    
// 	    $listBrands = $brandsModel->ListBrands();
	    
        require ABSPATH . "/Modules/Mercadolivre/Views/Adverts/AdvertsView.php";
        
	}
	


}
?>