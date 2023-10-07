<?php 

class RecommendationsModel extends MWS
{

    public $db;
    
    public $controller;
    
    public $store_id;
    
    public $service;
    
    public $uri = '/var/www/html/app_mvc/Modules/Amazon/library/MWSRecommendationsPHPClientLibrary-2013-04-01/src/';
    
    public $config = array (
       'ServiceURL' => "https://mws.amazonservices.com/Recommendations/2013-04-01",
	   'ProxyHost' => null,
	   'ProxyPort' => -1,
	   'ProxyUsername' => null,
	   'ProxyPassword' => null,
	   'MaxErrorRetry' => 3,
    );
    

	
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
	        
	    
	        parent::__construct($this->db, $this->uri, $this->store_id);
	        
	        $this->service = new MWSRecommendationsSectionService_Client(
	            $this->AWS_ACCESS_KEY_ID,
	            $this->AWS_SECRET_ACCESS_KEY,
	            $this->APPLICATION_NAME,
	            $this->APPLICATION_VERSION,
	            $this->config
	            );
	        
	    }
	    
	}

    
	
	/**
  * Get List Recommendations Action Sample
  * Gets competitive pricing and related information for a product identified by
  * the MarketplaceId and ASIN.
  *
  * @param MWSRecommendationsSectionService_Interface $service instance of MWSRecommendationsSectionService_Interface
  * @param mixed $request MWSRecommendationsSectionService_Model_ListRecommendations or array of parameters
  */

  function invokeListRecommendations(MWSRecommendationsSectionService_Interface $service, $request)
  {
      try {
        $response = $service->ListRecommendations($request);
        
        
//         $result = $response->getListRecommendationsResult();
//         pre($result);die;
//         $res = $result->getInventoryRecommendations();
        
//         pre($res);die;
        
        
        
        echo ("Service Response\n");
        echo ("=============================================================================\n");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        echo $dom->saveXML();
        echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

     } catch (MWSRecommendationsSectionService_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
	}
	
// 	function invokeCategoryQuery(MWSRecommendationsSectionService_Interface $service, $request)
// 	{
// 		try {
// 			$response = $service->ListRecommendations($request);
	
// 			echo ("Service Response\n");
// 			echo ("=============================================================================\n");
	
// 			$dom = new DOMDocument();
// 			$dom->loadXML($response->toXML());
// 			$dom->preserveWhiteSpace = false;
// 			$dom->formatOutput = true;
// 			echo $dom->saveXML();
// 			echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
	
// 		} catch (MWSRecommendationsSectionService_Exception $ex) {
// 			echo("Caught Exception: " . $ex->getMessage() . "\n");
// 			echo("Response Status Code: " . $ex->getStatusCode() . "\n");
// 			echo("Error Code: " . $ex->getErrorCode() . "\n");
// 			echo("Error Type: " . $ex->getErrorType() . "\n");
// 			echo("Request ID: " . $ex->getRequestId() . "\n");
// 			echo("XML: " . $ex->getXML() . "\n");
// 			echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
// 		}
// 	}
	
	
	
    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
	
    
    /************************************************************************
     * Setup request parameters and uncomment invoke to try out
     * sample for List Matching Products Action
     ***********************************************************************/
	
	public function ListRecommendationsRequest(){
	    
	    if(!isset($this->seller_id)){
	        return array();
	    }
	   
	    $request = new MWSRecommendationsSectionService_Model_ListRecommendationsRequest();
    	
    	$request->setSellerId($this->seller_id);
    	
    	$request->setMarketplaceId($this->site_id);
    	
    	$request->setRecommendationCategory('Inventory');
    	
    	return $this->invokeListRecommendations($this->service, $request);
    	
	}
	
// 	public function ListRecommendationsRequest(){
		 
// 		if(!isset($this->seller_id)){
// 			return array();
// 		}
	
// 		$request = new MWSRecommendationsSectionService_Model_CategoryQuery();
		 
// 		$request->setSellerId($this->seller_id);
		 
// 		$request->setMarketplaceId($this->site_id);
		
// 		return $this->invokeCategoryQuery($this->service, $request);
		 
// 	}
	
	

}
?>