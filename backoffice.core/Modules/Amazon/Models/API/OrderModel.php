<?php 

class OrderModel extends MWS
{

    public $db;
    
    public $controller;
    
    public $id;
    
    public $store_id;
    
    public $CreatedAfter;
    
    public $CreatedBefore;
    
    public $LastUpdatedAfter;
    
    public $LastUpdatedBefore;
    
    public $AmazonOrderId;
    
    public $PurchaseDate;
    
    public $LastUpdatedate;
    
    public $OrderStatus;
    
    public $FulfillmentChannel;
    
    public $SalesChannel;
    
    public $ShipServiceLevel;
    
    public $NumberOfItemsShipped;
    
    public $NumberOfItemsUnshipped;
    
    public $marketplaceId;
    
    public $PaymentMethodDetail;
    
    public $BuyerCounty;
    
    public $CPF;
    
    public $ShipServiceLevelCategory;
    
    public $ShippedByAmazonTFM;
    
    public $OrderType;
    
    public $EarliestShipDate;
    
    public $LastShipDate;
    
    public $IsBusinessOrder;
    
    public $IsPrime;
    
    public $IsPremiumOrder;
    
    public $IsReplacementOrder;
    
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

    
	public function populateOrder($order){
	    
	    foreach($order as $key => $value)
	    {
	        $column_name = str_replace('-','_',$key);
	        $this->{$column_name} = $value;
	    }
	    
	}
	
// 	public function Save(){
	    
// 	    if(!isset($this->PedidoId)){
// 	        return array();
// 	    }
	    
// 	    $query = $this->db->query("SELECT id FROM orders WHERE store_id = {$this->store_id} AND PedidoId = '{$this->PedidoId}'");
// 	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
// 	    if ( ! empty( $res['id'] ) ) {
	        
// 	        $query = $this->db->update('module_amazon_match_products', 'id', $res['id'], array(
// 	               'idType'  => $this->idType,
//     	           'ASIN'  => $this->ASIN,
// 	               'updated'  => date('Y-m-d H:i:s')
// 	              )
// 	         );
	        
// 	    } else {
	        
// 	        $query = $this->db->insert('module_amazon_match_products', array(
// 	            'PedidoId' => $this->AmazonOrderId,
// 	            'store_id' => $this->store_id,
//     	        'idType'  => $this->idType,
//     	        'ASIN'  => $this->ASIN,
//     	        'created'  =>  date('Y-m-d H:i:s'),
//     	        'updated'  => date('Y-m-d H:i:s')
//     	        ));
	        
// 	    }
	    
	    
// 	}
	
	
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
	        $orderItemObj =  new SimpleXmlElement($dom->saveXML());
	        
	        return $orderItemObj;
	        
// 	        pre($orderItemObj);
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
	
    
	/**
      * Get List Orders Action Sample
      * Gets competitive pricing and related information for a product identified by
      * the MarketplaceId and ASIN.
      *
      * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
      * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrders or array of parameters
      */
    
      function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request)
      {
          try {
            $response = $service->ListOrders($request);
            
            
            $dom = new DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
//             echo $dom->saveXML();die;
            $orderObj =  new SimpleXmlElement($dom->saveXML());
            // echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
            
            return $orderObj;
    
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
     
     function invokeGetOrder(MarketplaceWebServiceOrders_Interface $service, $request)
     {
     	try {
     		$response = $service->GetOrder($request);
     
     		echo ("Service Response\n");
     		echo ("=============================================================================\n");
     
     		$dom = new DOMDocument();
     		$dom->loadXML($response->toXML());
     		$dom->preserveWhiteSpace = false;
     		$dom->formatOutput = true;
     		echo $dom->saveXML();
     		echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     
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
     
     public function ListOrdersRequest(){
	    
	    if(!isset($this->seller_id)){
	        return array();
	    }
	    
	    
// 	    $dom = new DOMDocument();
// 	    $dom->loadXML(file_get_contents("/var/www/html/app_mvc/Modules/Amazon/Models/API/orderResponse.xml"));
// 	    $dom->preserveWhiteSpace = false;
// 	    $dom->formatOutput = true;
// 	    echo $dom->saveXML();die;
	    
	    // @TODO: set request. Action can be passed as MarketplaceWebServiceOrders_Model_ListOrders
	    $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
	    $request->setSellerId($this->seller_id);
	    $request->setMarketplaceId($this->site_id);
	    $request->setMWSAuthToken($this->token);
// 	    $request->setCreatedAfter(gmdate('Y-04-30\TH:i:s\Z'));
// 	    pre($this->CreatedAfter);
// 	    pre(gmdate('Y-04-30\TH:i:s\Z'));die;
	    $request->setCreatedAfter($this->CreatedAfter);
	    
	    
	    
	    // object or array of parameters
	    return $this->invokeListOrders($this->service, $request);
	
	
	}
	
	
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
	    $request->setMWSAuthToken($this->token);
	    $request->setAmazonOrderId($this->AmazonOrderId);
	    
	    return $this->invokeListOrderItems($this->service, $request);
	    
	    
	}
	

	public function GetOrderRequest(){
		 
		if(!isset($this->AmazonOrderId)){
			return array();
		}
		 
		 
		$request = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
		$request->setSellerId($this->seller_id);
		$request->setMWSAuthToken($this->token);
		$request->setAmazonOrderId($this->AmazonOrderId);
		 
		return $this->invokeGetOrder($this->service, $request);
		 
		 
	}

}
?>