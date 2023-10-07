<?php 

class ProductsModel extends MWS
{

    public $db;
    
    public $controller;
    
    public $store_id;
    
    public $error;
    
    public $connection;
    
    public $az_idType;
    
    public $az_Id;
	
    public $az_ASIN; 
    
	public $az_Binding;
	
	public $az_Brand;
	
	public $az_Color;
	
	public $az_Height;
	
	public $az_Length;
	
	public $az_Width;
	
	public $az_Weight;
	
	public $az_Label;
	
	public $az_Amount;
	
	public $az_CurrencyCode;
	
	public $az_Manufacturer;
	
	public $az_Model;
	
	public $az_MaterialType;
	
	public $az_NumberOfItems;
	
	public $az_HeightPackage;
	
	public $az_LengthPackage;
	
	public $az_WidthPackage;
	
	public $az_WeightPackage;
	
	public $az_PackageQuantity;
	
	public $az_PartNumber;
	
	public $az_ProductGroup;
	
	public $az_ProductTypeName;
	
	public $az_Publisher;
	
	public $az_Size;
	
	public $az_SmallImage;
	
	public $az_Studio;
	
	public $az_Title;
	
	public $az_Relationship;
	
	public $az_ASINRelationship;
	
	public $az_ProductCategoryId;
	
	public $az_Rank;
	
	public $az_SalesRank;
	
	public $created;
	
	public $updated;
	
	private $service;
    
    public $ids = array();
    
    public $queryId;
    
    public $uri = '/var/www/html/app_mvc/Modules/Amazon/library/MWSProductsPHPClientLibrary-2011-10-01/src/';
    
    public $config = array (
        'ServiceURL' => "https://mws.amazonservices.com/Products/2011-10-01",
        'ProxyHost' => null,
        'ProxyPort' => -1,
        'ProxyUsername' => null,
        'ProxyPassword' => null,
        'MaxErrorRetry' => 3,
    );
    
	private $pound = 0.45359237;
	
	private $inches = 2.54;
	
	
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
	        
	        $this->service = new MarketplaceWebServiceProducts_Client(
	            $this->AWS_ACCESS_KEY_ID,
	            $this->AWS_SECRET_ACCESS_KEY,
	            $this->APPLICATION_NAME,
	            $this->APPLICATION_VERSION,
	            $this->config
	            );
	        
	    }
	    
	}

    public function unsetAzProductFeed(){
    	unset($this->az_idType);
    	unset($this->az_Id);
    	unset($this->az_ASIN);
    	unset($this->az_Binding);
    	unset($this->az_Brand);
    	unset($this->az_Color);
    	unset($this->az_Height);
    	unset($this->az_Length);
    	unset($this->az_Width);
    	unset($this->az_Weight);
    	unset($this->az_Label);
    	unset($this->az_Amount);
    	unset($this->az_CurrencyCode);
    	unset($this->az_Manufacturer);
    	unset($this->az_Model);
    	unset($this->az_MaterialType);
    	unset($this->az_NumberOfItems);
    	unset($this->az_HeightPackage);
    	unset($this->az_LengthPackage);
    	unset($this->az_WidthPackage);
    	unset($this->az_WeightPackage);
    	unset($this->az_PackageQuantity);
    	unset($this->az_PartNumber);
    	unset($this->az_ProductGroup);
    	unset($this->az_ProductTypeName);
    	unset($this->az_Publisher);
    	unset($this->az_Size);
    	unset($this->az_SmallImage);
    	unset($this->az_Studio);
    	unset($this->az_Title);
    	unset($this->az_Relationship);
    	unset($this->az_ASINRelationship);
    	unset($this->az_ProductCategoryId);
    	unset($this->az_Rank);
    	unset($this->az_SalesRank);
    }
	
	public function Save(){
		
	    $sql = "SELECT id, ean, connection  FROM az_products_feed WHERE store_id = {$this->store_id} AND ean LIKE '{$this->az_Id}'";
	    $query = $this->db->query($sql);
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    if(empty($this->error)){
	    	
	    	if ( ! empty( $res['ean'] ) ) {
		    	
		        $data =  array(
	                'az_idType' => $this->az_idType,
		        	'az_Id' => $this->az_Id,
					'az_ASIN' => $this->az_ASIN,
	        		'az_Binding' => $this->az_Binding,
	        		'az_Brand' => $this->az_Brand,
	        		'az_Color' => $this->az_Color,
	        		'az_Height' => $this->az_Height,
	        		'az_Length' => $this->az_Length,
	        		'az_Width' => $this->az_Width,
	        		'az_Weight' => $this->az_Weight,
	        		'az_Label' => $this->az_Label,
	        		'az_Amount' => $this->az_Amount,
	        		'az_CurrencyCode' => $this->az_CurrencyCode,
	        		'az_Manufacturer' => $this->az_Manufacturer,
	        		'az_Model' => $this->az_Model,
	        		'az_MaterialType' => $this->az_MaterialType,
	        		'az_NumberOfItems' => $this->az_NumberOfItems,
	        		'az_HeightPackage' => $this->az_HeightPackage,
	        		'az_LengthPackage' => $this->az_LengthPackage,
	        		'az_WidthPackage' => $this->az_WidthPackage,
	        		'az_WeightPackage' => $this->az_WeightPackage,
	        		'az_PackageQuantity' => $this->az_PackageQuantity,
	        		'az_PartNumber' => $this->az_PartNumber,
	        		'az_ProductGroup' => $this->az_ProductGroup,
	        		'az_ProductTypeName' => $this->az_ProductTypeName,
	        		'az_Publisher' => $this->az_Publisher,
	        		'az_Size' => $this->az_Size,
	        		'az_SmallImage' => $this->az_SmallImage,
	        		'az_Studio' => $this->az_Studio,
	        		'az_Title' => $this->az_Title,
	        		'az_ASINRelationship' => $this->az_ASINRelationship,
		        	'az_Relationship' => $this->az_Relationship,
	        		'az_ProductCategoryId' => $this->az_ProductCategoryId,
	        		'az_Rank' => $this->az_Rank,
	        		'az_SalesRank' => $this->az_SalesRank,
	        		'error' => $this->error,
	                'updated' => date('Y-m-d H:i:s')
		        );
		        
		        $query = $this->db->update('az_products_feed', 'id', $res['id'], $data);
		        if(!$query){
		        	pre($query);
		        }else{
		        	if($res['connection'] != 'not_match'){
		        		$query = $this->db->update('az_products_feed', 'id', $res['id'], array('connection' =>  'match', 'updated' => date('Y-m-d H:i:s')));
		        	}
		        }
	        
	   		}
	    
	    }else{
	    	
	    	
	    	if(isset($this->error)){
	    		
// 	    		$sql = "UPDATE az_products_feed SET error = '{$this->error}' WHERE id = {$res['id']}";
	    		$query = $this->db->update('az_products_feed', 'id', $res['id'], array('connection' =>  'not_match', 'error' =>  $this->error, 'updated' => date('Y-m-d H:i:s')));
	    		if(!$query){
	    			pre($query);
	    		}
	    		unset($this->error);
	    	}
	    	 
	    }
	    
	   
	}
	
    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
	
    
    /************************************************************************
     * Setup request parameters and uncomment invoke to try out
     * sample for List Matching Products Action
     ***********************************************************************/
	
	public function ListMatchingProductsRequest(){
	    
	    if(!isset($this->seller_id)){
	        return array();
	    }
	   
    	$request = new MarketplaceWebServiceProducts_Model_ListMatchingProductsRequest();
    	
    	$request->setSellerId($this->seller_id);
    	
    	$request->setMarketplaceId($this->site_id);
    	
    	$request->setMWSAuthToken($this->token);
    	
    	$request->setQuery($this->queryId);
    	
    	try {
    		$response = $this->service->ListMatchingProducts($request);
    	
    		echo ("Service Response\n");
    		echo ("=============================================================================\n");
    	
    		$dom = new DOMDocument();
    		$dom->loadXML($response->toXML());
    		$dom->preserveWhiteSpace = false;
    		$dom->formatOutput = true;
    		echo $dom->saveXML();
    		echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
    	
    	} catch (MarketplaceWebServiceProducts_Exception $ex) {
    		echo("Caught Exception: " . $ex->getMessage() . "\n");
    		echo("Response Status Code: " . $ex->getStatusCode() . "\n");
    		echo("Error Code: " . $ex->getErrorCode() . "\n");
    		echo("Error Type: " . $ex->getErrorType() . "\n");
    		echo("Request ID: " . $ex->getRequestId() . "\n");
    		echo("XML: " . $ex->getXML() . "\n");
    		echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
    	}
	  
	}

	
	public function simpleXmlObjectToArray ( $xmlObject, $out = array () )
    {
    	foreach ( (array) $xmlObject as $index => $node )
    		$out[$index] = ( is_object ( $node ) || is_array($node) )
    		? $this->simpleXmlObjectToArray ( $node )
    		: $node;
    		 
    		return $out;
    }
	/************************************************************************
	 * Setup request parameters and uncomment invoke to try out
	 * sample for Get Matching Product Action
	 ***********************************************************************/
	
	public function GetMatchingProductForIdRequest($idList){
	    
	    if(!isset($this->seller_id)){
	        return array();
	    }
	    
	    $request = new MarketplaceWebServiceProducts_Model_GetMatchingProductForIdRequest();
	    
	    $request->setSellerId($this->seller_id);
	    
	    $request->setMarketplaceId($this->site_id);
	    
	    $request->setMWSAuthToken($this->token);
	    
	    $request->setIdType('EAN');
	    
	    $request->setIdList($idList);
    	try {
    		 
    		$response = $this->service->GetMatchingProductForId($request);
    		
    		if ($response->isSetGetMatchingProductForIdResult()) {
    	
    			$matchingResult = $response->getGetMatchingProductForIdResult();
    			
    			foreach($matchingResult as $k => $result){
    				
    				echo '<br>';
    				echo $this->az_idType = $result->getIdType();
    				echo '  -  ';
    				echo $this->az_Id = $result->getId();
    				echo '<br>';
    				
    				
    				if(!$result->isSetError()){
    	
    					$products = $result->Products->getProduct();
    					
    					foreach($products as $j => $product){
    						
    						
    						$this->az_ASIN = $product->getIdentifiers()->getMarketplaceASIN()->getASIN();
    						
    						if($product->getSalesRankings()->isSetSalesRank()){
    							
	    						$salesRankings = $product->getSalesRankings()->getSalesRank();
	    						
	    						$this->az_ProductCategoryId = isset($salesRankings[1]) ? $salesRankings[1]->getProductCategoryId() : null ;
	    						
	    						$this->az_Rank = isset($salesRankings[1]) ? $salesRankings[1]->getRank() : null ;
    						}
    						
    						if($product->getAttributeSets()->isSetAny()){
    							
    							$attributeSets = $product->getAttributeSets()->getAny();
    							
	    						foreach($attributeSets as  $k => $node){
	    							
	    							$Body = $node->ownerDocument->documentElement->firstChild->firstChild;
	    							$Document = new DOMDocument();
	    							$Document->preserveWhiteSpace = false;
	    							$Document->formatOutput = true;
	    							$Document->appendChild($Document->importNode($Body,true));
	    							$xml_data = $Document->saveXml();
	    							$xml_data = str_replace("ns2:","",$xml_data);
	    							$result2 = simplexml_load_string($xml_data);
	    							
	    							$res2 = $this->simpleXmlObjectToArray ( $result2 );
// 	    							pre($res2);
	    							
	    							if(isset($res2['Product']['Relationships']['VariationParent'])){
	    								$this->az_Relationship = json_encode($res2['Product']['Relationships']);
	    							}
	    							
	    							if(isset($res2['Product']['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN'])){
	    								$this->az_ASINRelationship = $res2['Product']['Relationships']['VariationParent']['Identifiers']['MarketplaceASIN']['ASIN'];
	    							}
	    							
	    							if(isset($res2['Product']['SalesRankings']['SalesRank'])){
	    								$this->az_SalesRank = json_encode($res2['Product']['SalesRankings']);
	    							}
	    							
	    							if(isset($res2['Product']['AttributeSets']['ItemAttributes'])){
	    								
		    							foreach($res2['Product']['AttributeSets']['ItemAttributes'] as $key => $value)
		    							{
		    								$column_name = str_replace('-','_',$key);
		    								
		    								switch($key){
		    									
			    								case'ItemDimensions':
			    									foreach($value as $item => $dimension){
			    										if($item == 'Weight'){
			    											$dimension = number_format($dimension * $this->pound, 3);
			    										}else{
			    											$dimension = ceil($dimension * $this->inches);
			    										}
				    									$column_name = str_replace('-','_','az_'.$item);
				    									$this->{$column_name} = $dimension;
			    									}
			    									break;
			    								
			    								case'PackageDimensions':
			    									foreach($value as $item => $dimension){
			    										if($item == 'Weight'){
			    											$dimension = number_format($dimension * $this->pound, 3);
			    										}else{
			    											$dimension = ceil($dimension * $this->inches);
			    										}
			    										$column_name = str_replace('-','_','az_'.$item."Package");
			    										$this->{$column_name} = $dimension;
			    									}
			    									break;
		    									case "ListPrice":
		    										foreach($value as $item => $listPrice){
		    											$column_name = str_replace('-','_','az_'.$item);
		    											$this->{$column_name} = $listPrice;
		    										}
		    										break;
			    								case "SmallImage": 
				    									$column_name = str_replace('-','_','az_'.$key);
				    									$this->{$column_name} = $value['URL'];
			    									break;
			    									
			    								default:
			    									$column_name = str_replace('-','_','az_'.$key);
			    									if(property_exists($this,$column_name)){
			    										$value = is_array($value) ? json_encode($value) : $value ;
			    										$this->{$column_name} = $value; 
			    									}
		    									break;
		    								}
		    								
		    							}
	    								
	    							}
	    							
	    						}
	    						
    						}
    						
    					}
    					
    					
    				}else{
    					$this->error = $result->getError()->getMessage();
    					pre($this->error);
    					$this->Save();
    					$this->unsetAzProductFeed();
    				}
    				
    				pre($this);
    				$this->Save();
    				$this->unsetAzProductFeed();
    				
    			}
    			
    		}
    		
    	} catch (MarketplaceWebServiceProducts_Exception $ex) {
    		pre("Caught Exception: " . $ex->getMessage() . "\n");
    		pre("Response Status Code: " . $ex->getStatusCode() . "\n");
    		pre("Error Code: " . $ex->getErrorCode() . "\n");
    		pre("Error Type: " . $ex->getErrorType() . "\n");
    		pre("Request ID: " . $ex->getRequestId() . "\n");
    		pre("XML: " . $ex->getXML() . "\n");
    		pre("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
    	}
	    
	}

}




$ex = '<Products xmlns:ns2="http://mws.amazonservices.com/schema/Products/2011-10-01/default.xsd" xmlns="http://mws.amazonservices.com/schema/Products/2011-10-01">
  <Product>
    <Identifiers>
      <MarketplaceASIN>
        <MarketplaceId>A2Q3Y263D00KWC</MarketplaceId>
        <ASIN>B076K7F4PP</ASIN>
      </MarketplaceASIN>
    </Identifiers>
    <AttributeSets>
      <ns2:ItemAttributes xml:lang="pt-BR">
        <ns2:Brand>Loren Sid</ns2:Brand>
        <ns2:ItemDimensions>
          <ns2:Height Units="inches">5.90551180500</ns2:Height>
          <ns2:Length Units="inches">16.53543305400</ns2:Length>
          <ns2:Width Units="inches">8.26771652700</ns2:Width>
          <ns2:Weight Units="pounds">8.3775659560000</ns2:Weight>
        </ns2:ItemDimensions>
        <ns2:Label>Loren Sid</ns2:Label>
        <ns2:Manufacturer>Loren Sid</ns2:Manufacturer>
        <ns2:NumberOfItems>50</ns2:NumberOfItems>
        <ns2:PackageDimensions>
          <ns2:Height Units="inches">5.90551180500</ns2:Height>
          <ns2:Length Units="inches">16.53543305400</ns2:Length>
          <ns2:Width Units="inches">8.26771652700</ns2:Width>
          <ns2:Weight Units="pounds">8.3775659560000</ns2:Weight>
        </ns2:PackageDimensions>
        <ns2:PackageQuantity>1</ns2:PackageQuantity>
        <ns2:PartNumber>7896651426404</ns2:PartNumber>
        <ns2:ProductGroup>Home Improvement</ns2:ProductGroup>
        <ns2:ProductTypeName>MAJOR_HOME_APPLIANCES</ns2:ProductTypeName>
        <ns2:Publisher>Loren Sid</ns2:Publisher>
        <ns2:SmallImage>
          <ns2:URL>https://m.media-amazon.com/images/I/31LiA65zBOL._SL75_.jpg</ns2:URL>
          <ns2:Height Units="pixels">75</ns2:Height>
          <ns2:Width Units="pixels">75</ns2:Width>
        </ns2:SmallImage>
        <ns2:Studio>Loren Sid</ns2:Studio>
        <ns2:Title>Ventilador de Teto Loren Sid Diplomata Comercial Branco 3 P&#xE1;s Motor M2 127V</ns2:Title>
      </ns2:ItemAttributes>
    </AttributeSets>
    <Relationships/>
    <SalesRankings/>
  </Product>
</Products>';
?>