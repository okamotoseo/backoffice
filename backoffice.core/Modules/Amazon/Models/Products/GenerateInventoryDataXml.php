<?php
class GenerateInventoryDataXml  extends MainModel
{
    
    /**
     * @var string
     * Class Unique ID
     */
    public $store_id;
    
    public $merchant_id;
    
    
    
    
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
        
    }

	public function GetXml(){
		
	    
	    $fileName = "../Feed/inventory_data_store_id_{$this->store_id}.xml";
	
		$dom = new DOMDocument('1.0', 'utf-8');
		$dom->substituteEntities = true;
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;

		$rss = $dom->createElement("AmazonEnvelope");
		$dom->appendChild($rss);

		$version = $dom->createAttribute("xmlns:xsi");
		$rss->appendChild($version);
		$versionValue = $dom->createTextNode("http://www.w3.org/2001/XMLSchema-instance");
		$version->appendChild($versionValue);

		$version = $dom->createAttribute("xsi:noNamespaceSchemaLocation");
		$rss->appendChild($version);
		$versionValue = $dom->createTextNode("amzn-envelope.xsd");
		$version->appendChild($versionValue);

		
		$header = $dom->createElement("Header");
		$DocumentVersion = $dom->createElement("DocumentVersion", "1.0");
		$header->appendChild($DocumentVersion);
		$MerchantIdentifier = $dom->createElement("MerchantIdentifier", "{$this->merchant_id}");
		$header->appendChild($MerchantIdentifier);
		$rss->appendChild($header);
		
		$MessageType = $dom->createElement("MessageType", "Inventory");
		$rss->appendChild($MessageType);
		$PurgeAndReplace = $dom->createElement("PurgeAndReplace", "true");
		$rss->appendChild($PurgeAndReplace);
// 		$sqlParents = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND parent_id IS NOT NULL and parent_id > 1
//         AND EAN IS NOT NULL AND EAN NOT IN (SELECT id as EAN FROM module_amazon_match_products WHERE store_id = {$this->store_id}) GROUP BY parent_id LIMIT 1";
		$ind = 1;
// 		$sqlParents = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND parent_id IS NOT NULL AND parent_id > 1 AND EAN != ''";
// 		$sqlParents = "SELECT az_products_feed.*, available_products.* FROM az_products_feed
// 		LEFT JOIN available_products ON available_products.id = az_products_feed.product_id AND
// 		available_products.store_id = az_products_feed.store_id
// 		WHERE az_products_feed.store_id = {$this->store_id}";
		
		$sqlParents = "SELECT az_products_feed.*, available_products.* FROM az_products_feed
		RIGHT JOIN available_products ON available_products.id = az_products_feed.product_id AND
		available_products.store_id = az_products_feed.store_id
		WHERE az_products_feed.store_id = {$this->store_id} AND az_products_feed.connection LIKE 'match'
		AND az_products_feed.product_id NOT IN (SELECT product_id FROM product_relational WHERE store_id = {$this->store_id})";
		
		if($this->store_id == 1){
// 			$sqlParents = "SELECT az_products_feed.*, available_products.* FROM az_products_feed
// 			LEFT JOIN available_products ON available_products.id = az_products_feed.product_id
// 			WHERE az_products_feed.store_id = 4 AND az_products_feed.connection = 'match' LIMIT 20";
// 			$salePriceModel->store_id = 4;
		}
		$query = $this->db->query($sqlParents);
		while($rowAP = $query->fetch(PDO::FETCH_ASSOC)){
		    $ind++;
		        
    		$Message = $dom->createElement("Message");
    
    		$MessageID = $dom->createElement("MessageID", $ind);
    		$Message->appendChild($MessageID);
    		$OperationType = $dom->createElement("OperationType", "Update"); //Update, Delete, patialUpdate
    		$Message->appendChild($OperationType);
			    
			$item = $dom->createElement("Inventory");
				
			$id = $dom->createElement("SKU", "{$rowAP['sku']}");
			$item->appendChild($id);
			$qtd = $rowAP['quantity'] > 1 ? $rowAP['quantity'] : 0 ;
			$qtd = $rowAP['connection'] != 'match' ? 0 : $qtd ;
			
			if ($rowAP['blocked'] == "T"){
				$qtd = 0;
			}
// 			if($this->store_id == 4){
// 				$qtd = 0;
// 			}
			$Quantity = $dom->createElement("Quantity", $qtd );
			$item->appendChild($Quantity);
			
			$Fulfillment = $dom->createElement("FulfillmentLatency", 2 );
			$item->appendChild($Fulfillment);
			
// 			$Available = $dom->createElement("Available", true );  
// 			$item->appendChild($Available);

			$Message->appendChild($item);
			
			$rss->appendChild($Message);
			
			$ind++;
		}
		

		$dom->appendChild($rss);
// 		pre($rss);die;
		if(file_exists($fileName)){
			unlink($fileName);
		}

		$dom->save($fileName, LIBXML_NOCDATA);
// 		header("Content-Type: text/xml");
		return $dom->saveXML($dom->documentElement, LIBXML_NOCDATA);

	
	}
	
}