<?php
class AdvertsRelationshipController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function AdvertsRelationship() 
	{
	    
	    $this->title = 'Anúncios publicados: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $availableProductDescModel = parent::load_model('Products/AvailableProductsModel');
	    
	    $advertsModel = parent::load_module_model('Mercadolivre/Models/Adverts/AdvertsRelationshipModel');
	    
	    if($advertsModel->ValidateForm()){
	        
	        $list = $advertsModel->GetMlProducts();
	        
	    }else{
	        
	        $list = $advertsModel->ListMlProducts();
	        
	        
	    }
	    $totalReg = $advertsModel->TotalAdverts();
	    
	
	    
        require ABSPATH . "/Modules/Mercadolivre/Views/Adverts/AdvertsRelationshipView.php";
        
	}
	


}
?>