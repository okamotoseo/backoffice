<?php 

class DirectoryModel extends Soap
{

	/**
	 * @var string
	 * Class Unique ID
	 */

	public $country_id = "BR";
	
	public $uf;
	
	public $region_id;
	
	public $code;
   
	



	
	public function __construct($db = false,  $controller = null, $storeId = null)
	{
	    $this->db = $db;
	    
	    $this->store_id = $storeId;
	    
	    $this->controller = $controller;
	    
	    if(isset($this->controller)){
	        
    	    $this->parametros = $this->controller->parametros;
    	    
    	    $this->userdata = $this->controller->userdata;
    	    
    	    $this->store_id = $this->controller->userdata['store_id'];
    	    
	    }
	    if(isset($this->store_id)){
	    
	        parent::__construct($this->db, $this->store_id);
	    
	    }
	    
	}

	
    

    
    public function directoryRegionList(){
        
        if(!isset($this->country_id)){
            
            return array();
        }
        
        $response = $this->soapClient->directoryRegionList($this->session_id, $this->country_id);
        return $response;
        
    }
    
    

    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
    
    public function GetRegionIdFromUf(){
        
        if(!isset($this->uf)){
            
            return array();
        }
        
        $regions = $this->directoryRegionList();
        
        foreach($regions as $key => $region){
            
            if($this->uf == $region->code){
                $this->region_id = $region->region_id;
                break;
            }
        }
        return $region;
        
    }
    
    public function GetRegionCodeFromId(){
        
        if(!isset($this->region_id)){
            
            return array();
        }
        
        $regions = $this->directoryRegionList();
        
        foreach($regions as $key => $region){
            
            if($this->region_id == $region->region_id){
                $this->code = $region->code;
                break;
            }
        }
        return $region;
        
    }
   
    
    
}
?>