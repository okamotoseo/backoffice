<?php
class ImportAdvertsController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

	public function ImportAdverts() 
	{
	    
	    $this->title = 'Anúncios publicados: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $availableProductDescModel = parent::load_model('Products/AvailableProductsModel');
	    
	    $importAdvertsModel = parent::load_module_model('Mercadolivre/Models/Adverts/ImportAdvertsModel');
	    
	    $key = array_search('AdsId', $this->parametros);
	     
	    if(isset($key)){
	    	$importAdvertsModel->product_id = get_next($this->parametros, $key);
	    	
	    	$list = $importAdvertsModel->GetMlProducts();
	    }
	    
	    
	        
	        
	        
	        
	        
	
	    
        require ABSPATH . "/Modules/Mercadolivre/Views/Adverts/ImportAdvertsView.php";
        
	}
	


}
?>