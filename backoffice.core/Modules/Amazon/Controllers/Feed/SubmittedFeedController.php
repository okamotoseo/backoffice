<?php
class SubmittedFeedController extends MainController
{

    
    public function __construct($db = false, $controller = null)
    {
    	
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        
    }

    public function SubmittedFeed() 
	{
	    
	    $this->title = 'Listagem de Feed: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	        
	    $this->includes = array("js" => "/Modules/Amazon/Views/js/Amazon.js");
	    
	    $feedModel = parent::load_module_model('Amazon/Models/Feed/FeedModel');
	    
	        
        if($feedModel->ValidateForm()){
        	
            $feeds = $feedModel->ListFeedSubmitted();
            
        }else{
            
            $feeds = $feedModel->ListFeedSubmitted();
            
        }
        require ABSPATH . "/Modules/Amazon/Views/Feed/FeedView.php";
        
        
        
	}

}
?>