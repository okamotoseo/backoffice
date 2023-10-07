<?php 

class SubmitFeedModel extends MWS
{

    public $db;
    
    public $controller;
    
    public $id;
    
    public $store_id;
    
    public $FeedSubmissionId;
    
    public $FeedType;
    
    public $SubmittedDate;
    
    public $FeedProcessingStatus;
    
    public $StartedProcessingDate;
    
    public $CompletedProcessingDate;
    
    public $feed;
    
    public $service;
    
    public $uri = '/var/www/html/app_mvc/Modules/Amazon/library/amazon-mws-v20090101-php-2016-09-21/src/';
    
    public $config =array (
        'ServiceURL' => "https://mws.amazonservices.com",
        'ProxyHost' => null,
        'ProxyPort' => -1,
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
	       
	       $this->service = new MarketplaceWebService_Client(
	           $this->AWS_ACCESS_KEY_ID,
	           $this->AWS_SECRET_ACCESS_KEY,
	           $this->config,
	           $this->APPLICATION_NAME,
	           $this->APPLICATION_VERSION);
	    
	    }
	    
	}

	
    
	public function Save(){
	    
	    $query = $this->db->query("SELECT id FROM module_amazon_feed
            WHERE store_id = {$this->store_id} AND FeedSubmissionId = '{$this->FeedSubmissionId}'");
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    if ( ! empty( $res['id'] ) ) {
	        
	        $query = $this->db->update('module_amazon_feed', 'id', $res['id'], array(
	            'store_id'  => $this->store_id,
	            'SubmittedDate'  => $this->SubmittedDate,
	            'FeedProcessingStatus'  => $this->FeedProcessingStatus,
	            'StartedProcessingDate'  => $this->StartedProcessingDate,
	            'CompletedProcessingDate'  => $this->CompletedProcessingDate
	            
	        ));
	        
	      
	    } else {
	        
	        $query = $this->db->insert('module_amazon_feed', array(
	            'store_id'  => $this->store_id,
	            'FeedSubmissionId'  => $this->FeedSubmissionId,
	            'FeedType'  => $this->FeedType,
	            'SubmittedDate'  => $this->SubmittedDate,
	            'FeedProcessingStatus'  => $this->FeedProcessingStatus,
	            'StartedProcessingDate'  => $this->StartedProcessingDate,
	            'CompletedProcessingDate'  => $this->CompletedProcessingDate
	        ));
	        
	        
	    }
	    
	    
	}
    
    
	public function ListFeedSubmitted()
	{
	    $query = $this->db->query('SELECT * FROM `module_amazon_feed`  WHERE `store_id` = ? ORDER BY SubmittedDate ASC',
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	
	/**
	 * Submit Feed Action Sample
	 * Uploads a file for processing together with the necessary
	 * metadata to process the file, such as which type of feed it is.
	 * PurgeAndReplace if true means that your existing e.g. inventory is
	 * wiped out and replace with the contents of this feed - use with
	 * caution (the default is false).
	 *
	 * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
	 * @param mixed $request MarketplaceWebService_Model_SubmitFeed or array of parameters
	 */
	public function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request)
	{
	    try {
	        
// 	        pre($request);die;
	        $response = $service->submitFeed($request);
	        
	        if ($response->isSetSubmitFeedResult()) {
	            
	            $submitFeedResult = $response->getSubmitFeedResult();
	            
	            if ($submitFeedResult->isSetFeedSubmissionInfo()) {
	               
	                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
	                
	                if ($feedSubmissionInfo->isSetFeedSubmissionId()){
	                    $this->FeedSubmissionId = $feedSubmissionInfo->getFeedSubmissionId();
	                }
	                if ($feedSubmissionInfo->isSetFeedType()){
	                    $this->FeedType = $feedSubmissionInfo->getFeedType();
	                }
	                if ($feedSubmissionInfo->isSetSubmittedDate()){
	                    $this->SubmittedDate = $feedSubmissionInfo->getSubmittedDate()->format("Y-m-d H:i:s");
	                }
	                if ($feedSubmissionInfo->isSetFeedProcessingStatus()){
	                    $this->FeedProcessingStatus = $feedSubmissionInfo->getFeedProcessingStatus();
	                }
	                if ($feedSubmissionInfo->isSetStartedProcessingDate()){
	                    $this->StartedProcessingDate = $feedSubmissionInfo->getStartedProcessingDate()->format("Y-m-d H:i:s");
	                }
	                if ($feedSubmissionInfo->isSetCompletedProcessingDate()){
	                    $this->CompletedProcessingDate = $feedSubmissionInfo->getCompletedProcessingDate()->format("Y-m-d H:i:s");
	                }
	            }
	        }
	        if ($response->isSetResponseMetadata()) {
	            
	            $responseMetadata = $response->getResponseMetadata();
	            
	            if ($responseMetadata->isSetRequestId()){
	                $this->RequestId = $responseMetadata->getRequestId();
	            }
	        }
	        
	        $this->Save();
	        
	        
	        
	    } catch (MarketplaceWebService_Exception $ex) {
	        
	        echo("Caught Exception: " . $ex->getMessage() . "\n");
	        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
	        echo("Error Code: " . $ex->getErrorCode() . "\n");
	        echo("Error Type: " . $ex->getErrorType() . "\n");
	        echo("Request ID: " . $ex->getRequestId() . "\n");
	        echo("XML: " . $ex->getXML() . "\n");
	        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
	        
	        $query = $this->db->insert('module_amazon_feed_exception', array(
	            'store_id'  => $this->store_id,
	            'service'  => 'SubmitFeed',
	            'RequestID'  => $ex->getRequestId(),
	            'CaughtException'  => $ex->getMessage(),
	            'StatusCode'  => $ex->getStatusCode(),
	            'ErrorCode'  => $ex->getErrorCode(),
	            'ErrorType'  => $ex->getErrorType(),
	            'XML'  => $ex->getXML(),
	            'ResponseHeaderMetadata' => $ex->getResponseHeaderMetadata()
	        ));
	        
	    }
	}
    

    
    
	/**
	 * Get Feed Submission Result Action Sample
	 * retrieves the feed processing report
	 *
	 * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
	 * @param mixed $request MarketplaceWebService_Model_GetFeedSubmissionResult or array of parameters
	 */
	function invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request)
	{
	    
	    $path = UP_ABSPATH."/store_id_{$this->store_id}/FeedSubmissionResult.xml";
	    
	    
	    try {
	        
	        $handle = fopen($path, 'w+');
	        $result = $service->getFeedSubmissionResult($request);
	        fclose($handle);
	        $tempFile = $path;
	        $response = file_get_contents($tempFile);
	        
	        $xml = new SimpleXMLElement($response);
	        $result = new StdClass();
	        $result->report = $xml->Message->ProcessingReport;
	        
	        $result->summary = $result->report->ProcessingSummary;
	        $result->result = array();
	        if ( isset($result->report->Result) )
	        {
	            foreach ($result->report->Result as $item)
	            {
	                $result->result[] = $item;
	                $productid = $feed_id['product_id'];
	                $resultcode = $item->ResultCode[0];
	                $resultdescription = str_replace("'", "", $item->ResultDescription[0]);
	                
	                if(!empty($item))
	                {
	                    if($resultcode == "Warning")
	                    {
	                        $data = array(
	                            'feed_status' => 3
	                        );
	                        $this->products_model->update_feed($productid, $data);
	                    }
	                    else
	                    {
	                        $data = array(
	                            'feed_status' => 4
	                        );
	                        $this->products_model->update_feed($productid, $data);
	                    }
	                    $additional_data = array(
	                        'product_id' => $productid,
	                        'error_type' => $resultcode,
	                        'error_description' => $resultdescription
	                    );
	                    $this->products_model->insert_error($additional_data);
	                }
	            }
	        }
	        
	        else
	        {
	            $result->result = null;
	            $data = array(
	                'feed_status' => 1
	            );
	            $this->products_model->update_feed($productid, $data);
	        }
	        return $result;
	    }
	    catch (MarketplaceWebService_Exception $ex)
	    {
	        pre("Caught Exception: " . $ex->getMessage() . "\n");
	        $ErrorMessage = $ex->getMessage();
	        pre("Response Status Code: " . $ex->getStatusCode() . "\n");
	        pre("Error Code: " . $ex->getErrorCode() . "\n");
	        $ErrorCode = $ex->getStatusCode();
	        pre("Error Type: " . $ex->getErrorType() . "\n");
	        pre("Request ID: " . $ex->getRequestId() . "\n");
	        pre("XML: " . $ex->getXML() . "\n");
	        pre("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
	        
	        $query = $this->db->insert('module_amazon_feed_exception', array(
	            'store_id'  => $this->store_id,
	            'service'  => 'SubmitFeedResult',
	            'RequestID'  => $ex->getRequestId(),
	            'CaughtException'  => $ex->getMessage(),
	            'StatusCode'  => $ex->getStatusCode(),
	            'ErrorCode'  => $ex->getErrorCode(),
	            'ErrorType'  => $ex->getErrorType(),
	            'XML'  => $ex->getXML(),
	            'ResponseHeaderMetadata' => $ex->getResponseHeaderMetadata()
	        ));
	        
	    }
	    @unlink($path);
	}
	
    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
	public function GetFeedSubmissionResultRequest(){
	    
// 	    echo $path = UP_ABSPATH."/store_id_{$this->store_id}/FeedSubmissionResult.xml";die;
	    $path =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$this->store_id}/xml/FeedSubmissionResult-{$this->FeedSubmissionId}.xml";
	    
        $handle = fopen($path, 'w+');
	    
    	$request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest();
    	$request->setMerchant($this->seller_id);
    	$request->setFeedSubmissionId($this->FeedSubmissionId);
    	$request->setFeedSubmissionResult($handle);
    	$request->setMWSAuthToken($this->token); // Optional
    	
//     	$this->invokeGetFeedSubmissionResult($this->service, $request);

    	try {
    	    
    	    $handle = fopen($path, 'w+');
    	    $result = $this->service->getFeedSubmissionResult($request);
    	    fclose($handle);
    	    $tempFile = $path;
    	    $response = file_get_contents($tempFile);
    	    
    	    $xml = new SimpleXMLElement($response);
    	    $result = new StdClass();
    	    $result->report = $xml->Message->ProcessingReport;
    	    
    	    $result->summary = $result->report->ProcessingSummary;
//     	    $result->result = array();
    	    if ( isset($result->report->Result) )
    	    {
    	        foreach ($result->report->Result as $item)
    	        {
    	            $resultcode = $item->ResultCode[0];
//     	            pre($item);
    	   
    	        }
    	    }
    	    
    	    return $result;
    	}
    	catch (MarketplaceWebService_Exception $ex)
    	{
    	    pre("Caught Exception: " . $ex->getMessage() . "\n");
    	    $ErrorMessage = $ex->getMessage();
    	    pre("Response Status Code: " . $ex->getStatusCode() . "\n");
    	    pre("Error Code: " . $ex->getErrorCode() . "\n");
    	    $ErrorCode = $ex->getStatusCode();
    	    pre("Error Type: " . $ex->getErrorType() . "\n");
    	    pre("Request ID: " . $ex->getRequestId() . "\n");
    	    pre("XML: " . $ex->getXML() . "\n");
    	    pre("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
    	    
    	    $query = $this->db->insert('module_amazon_feed_exception', array(
    	        'store_id'  => $this->store_id,
    	        'service'  => 'GetFeedSubmissionResultRequest',
    	        'RequestID'  => $ex->getRequestId(),
    	        'CaughtException'  => $ex->getMessage(),
    	        'StatusCode'  => $ex->getStatusCode(),
    	        'ErrorCode'  => $ex->getErrorCode(),
    	        'ErrorType'  => $ex->getErrorType(),
    	        'XML'  => $ex->getXML(),
    	        'ResponseHeaderMetadata' => $ex->getResponseHeaderMetadata()
    	    ));
    	    
    	}
    	@unlink($path);
	}
	
	public function submitFeed(){
	    
	    if(!isset($this->feed)){
	        return array();
	    }
	    
	    /********* Begin Comment Block *********/
	    $feedHandle = @fopen('php://memory', 'rw+');
	    fwrite($feedHandle, $this->feed);
	    rewind($feedHandle);
	    
	    $request = new MarketplaceWebService_Model_SubmitFeedRequest();
	    $request->setMerchant($this->seller_id);
	    $request->setMarketplaceIdList(array("Id" => array($this->site_id)));
	    
	    $request->setFeedType($this->FeedType);
	    $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
	    rewind($feedHandle);
	    $request->setPurgeAndReplace(false);
	    $request->setFeedContent($feedHandle);
	    $request->setMWSAuthToken($this->token); 
	    
	    rewind($feedHandle);
	    /********* End Comment Block *********/
	    $this->invokeSubmitFeed($this->service, $request);
	    @fclose($feedHandle);
	    
	}
	
    public function submitFeedProducts(){
        
        if(!isset($this->feed)){
            return array();
        }
        
        /********* Begin Comment Block *********/
        $feedHandle = @fopen('php://memory', 'rw+');
        fwrite($feedHandle, $this->feed);
        rewind($feedHandle);
        
        $request = new MarketplaceWebService_Model_SubmitFeedRequest();
        $request->setMerchant($this->seller_id);
        $request->setMarketplace( $this->site_id);
//         $request->setMarketplaceIdList( array("Id" => array($this->site_id)));
//         $request->setMarketplaceId($this->site_id);
        $request->setFeedType('_POST_PRODUCT_DATA_');
        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
        rewind($feedHandle);
        $request->setPurgeAndReplace(false);
        $request->setFeedContent($feedHandle);
        $request->setMWSAuthToken($this->token); // Optional
        
        rewind($feedHandle);
        
        /********* End Comment Block *********/
        $this->invokeSubmitFeed($this->service, $request);
        @fclose($feedHandle);
        
    }
    
    public function submitFeedPrice(){
        
        if(!isset($this->feed)){
            return array();
        }
        
        /********* Begin Comment Block *********/
        $feedHandle = @fopen('php://memory', 'rw+');
        fwrite($feedHandle, $this->feed);
        rewind($feedHandle);
        $request = new MarketplaceWebService_Model_SubmitFeedRequest();
        $request->setMerchant($this->seller_id);
        $request->setMarketplaceIdList( array("Id" => array($this->site_id)));
        $request->setFeedType('_POST_PRODUCT_PRICING_DATA_');
        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
        rewind($feedHandle);
        $request->setPurgeAndReplace(false);
        $request->setFeedContent($feedHandle);
        $request->setMWSAuthToken($this->token); // Optional
        
        rewind($feedHandle);
        /********* End Comment Block *********/
        $this->invokeSubmitFeed($this->service, $request);
        @fclose($feedHandle);
        
    }
    
    
    public function submitFeedInventory(){
        
        if(!isset($this->feed)){
            return array();
        }
        
        /********* Begin Comment Block *********/
        $feedHandle = @fopen('php://memory', 'rw+');
        fwrite($feedHandle, $this->feed);
        rewind($feedHandle);
        $request = new MarketplaceWebService_Model_SubmitFeedRequest();
        $request->setMerchant($this->seller_id);
        $request->setMarketplaceIdList( array("Id" => array($this->site_id)));
        $request->setFeedType('_POST_INVENTORY_AVAILABILITY_DATA_');
        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
        rewind($feedHandle);
        $request->setPurgeAndReplace(false);
        $request->setFeedContent($feedHandle);
        $request->setMWSAuthToken($this->token); // Optional
        
        rewind($feedHandle);
        /********* End Comment Block *********/
        $this->invokeSubmitFeed($this->service, $request);
        @fclose($feedHandle);
        
    }
    
    
    public function getFeedSubmitted($statusId = null){
        
        if(!isset($this->seller_id)){
            return array();
        }
        $request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest();
        $request->setMerchant($this->seller_id);
        $request->setMarketplace($this->site_id);
        $request->setMWSAuthToken($this->token); 
        //         $request->setMarketplaceIdList( array("Id" => array($this->site_id)));
        $requestByNextToken = new MarketplaceWebService_Model_GetFeedSubmissionListByNextTokenRequest();
        $requestByNextToken->setMerchant($this->seller_id);
        
        $status[1] = "_SUBMITTED_";
        $status[2] = "_CANCELLED_";
        $status[3] = "_IN_SAFETY_NET_";
        $status[4] = "_IN_PROGRESS_";
        $status[5] = "_UNCONFIRMED_";
        $status[6] = "_AWAITING_ASYNCHRONOUS_REPLY_";
        $status[7] = "_DONE_";
        
        if(isset($statusId) AND !empty($statusId)){
            $status = $status[$statusId];
        }
        
        $statusList = new MarketplaceWebService_Model_StatusList();
        
        $feedSubmissionInfoList = array();
        
        foreach($status as $key => $st){
            
            try{
                
                $request->setFeedProcessingStatusList($statusList->withStatus($st));
                
                $response =  $this->service->getFeedSubmissionList($request);
                
                $getFeedSubmissionListResult = $response->getGetFeedSubmissionListResult();
                
                $feedSubmissionInfoList[$st] = $getFeedSubmissionListResult->getFeedSubmissionInfoList();
            
                if ($getFeedSubmissionListResult->isSetHasNext()){
                    
                    if($getFeedSubmissionListResult->getHasNext()){
                        
                        $nextToken = $getFeedSubmissionListResult->getNextToken(); 
        
                        do{
                            $requestByNextToken->setNextToken($nextToken);
                            
                            $responseByNextToken = $this->service->getFeedSubmissionListByNextToken($requestByNextToken);
                            
                            $getFeedSubmissionListByNextTokenResult = $responseByNextToken->getGetFeedSubmissionListByNextTokenResult();
                            
                            $feed = $getFeedSubmissionListByNextTokenResult->getFeedSubmissionInfoList();
                            
                            array_push($feedSubmissionInfoList[$st], $feed[0]);
                            
                            if($getFeedSubmissionListByNextTokenResult->isSetHasNext()){
                                
                                $nextToken = $getFeedSubmissionListByNextTokenResult->getNextToken();
                                
                            }else{
                                $nextToken = '';
                            }
                    
                        
                        }while (!empty($nextToken));
                        
                    }
                }
    
    	    } catch (MarketplaceWebService_Exception $ex) {
    	        
    	        pre("Caught Exception: " . $ex->getMessage());
    	        pre("Response Status Code: " . $ex->getStatusCode());
    	        pre("Error Code: " . $ex->getErrorCode());
    	        pre("Error Type: " . $ex->getErrorType());
    	        pre("Request ID: " . $ex->getRequestId());
    	        pre("XML: " . $ex->getXML());
    	        pre("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata());
    	        
    	        $query = $this->db->insert('module_amazon_feed_exception', array(
    	            'store_id'  => $this->store_id,
    	            'service'  => 'FeedSubmitted',
    	            'RequestID'  => $ex->getRequestId(),
    	            'CaughtException'  => $ex->getMessage(),
    	            'StatusCode'  => $ex->getStatusCode(),
    	            'ErrorCode'  => $ex->getErrorCode(),
    	            'ErrorType'  => $ex->getErrorType(),
    	            'XML'  => $ex->getXML(),
    	            'ResponseHeaderMetadata' => $ex->getResponseHeaderMetadata()
    	        ));
    	    }
        
        }
        
        return $feedSubmissionInfoList;
    
    }
    
    
    
}
?>