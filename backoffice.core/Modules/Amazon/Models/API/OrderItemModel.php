<?php 

class OrderItemModel extends MWS
{

    public $db;
    
    public $controller;
    
    public $store_id;
    
    public $AmazonOrderId;
    
    public $NextToken;
    
    public $orderItems;
    
    public $service;
    
    public $uri = '/var/www/html/app_mvc/Modules/Amazon/library/MWSOrdersPHPClientLibrary-2013-09-01/src/';
    
    public $config = array (
        'ServiceURL' => "https://mws.amazonservices.com/Orders/2013-09-01",
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
	        
	        $this->service = new MarketplaceWebServiceOrders_Client(
	            $this->AWS_ACCESS_KEY_ID,
	            $this->AWS_SECRET_ACCESS_KEY,
	            $this->APPLICATION_NAME,
	            $this->APPLICATION_VERSION,
	            $this->config);
	        
	    }
	    
	}

    
	
	public function Save(){
	    
	    $query = $this->db->query("SELECT id FROM module_amazon_match_products
            WHERE store_id = {$this->store_id} AND id = '{$this->id}'");
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    if ( ! empty( $res['id'] ) ) {
	        
	        $query = $this->db->update('module_amazon_match_products', 'id', $res['id'], array(
	               'idType'  => $this->idType,
    	           'ASIN'  => $this->ASIN,
	               'updated'  => date('Y-m-d H:i:s')
	              )
	         );
	        
	    } else {
	        
	        $query = $this->db->insert('module_amazon_match_products', array(
	            'id' => $this->id,
	            'store_id' => $this->store_id,
    	        'idType'  => $this->idType,
    	        'ASIN'  => $this->ASIN,
    	        'created'  =>  date('Y-m-d H:i:s'),
    	        'updated'  => date('Y-m-d H:i:s')
    	        ));
	        
	    }
	    
	    
	}
	
   
     /**
      * Get List Order Items Action Sample
      * Gets competitive pricing and related information for a product identified by
      * the MarketplaceId and ASIN.
      *
      * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
      * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrderItems or array of parameters
      */
     
     function invokeListOrderItems(MarketplaceWebServiceOrders_Interface $service, $request)
     {
         try {
             $response = $service->ListOrderItems($request);
             
             $dom = new DOMDocument();
             $dom->loadXML($response->toXML());
             $dom->preserveWhiteSpace = false;
             $dom->formatOutput = true;
             $orderObj =  new SimpleXmlElement($dom->saveXML());
             pre($orderObj);
//              echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
             
         } catch (MarketplaceWebServiceOrders_Exception $ex) {
             echo("Caught Exception: " . $ex->getMessage() . "\n");
             echo("Response Status Code: " . $ex->getStatusCode() . "\n");
             echo("Error Code: " . $ex->getErrorCode() . "\n");
             echo("Error Type: " . $ex->getErrorType() . "\n");
             echo("Request ID: " . $ex->getRequestId() . "\n");
             echo("XML: " . $ex->getXML() . "\n");
             echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
         }
     }
	
    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
	
    
    /************************************************************************
     * Setup request parameters and uncomment invoke to try out
     * sample for List Orders Action
     ***********************************************************************/
     
     public function ListOrderItemsRequest(){
	    
         if(!isset($this->AmazonOrderId)){
	        return array();
	    }
	 
	    
	    $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
	    $request->setSellerId($this->seller_id);
	    $request->setMarketplaceId($this->site_id);
	    $request->setMWSAuthToken($this->token);
	    $request->setAmazonOrderId($this->AmazonOrderId);
	    
	    pre($request);die;
	    return $this->invokeListOrderItems($this->service, $request);
	
	
	}
	
	

}
?>