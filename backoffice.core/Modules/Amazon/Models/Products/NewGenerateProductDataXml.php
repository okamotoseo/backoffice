<?php
class GenerateProductDataXml  extends MainModel
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

	public function GetXml()
	{
		
		$azAttributesModel = new AzAttributesModel($this->db, null, $this->store_id);
	    $salePriceModel = new SalePriceModel($this->db, null, $this->store_id);
	    $attributesValues = new AttributesValuesModel($this->db, null);
	    $attributesValues->store_id =  $this->store_id;
	   
	    $count = 0;
	    
	    $fileName = "../Feed/product_data_store_id_{$this->store_id}.xml";
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
		
		$MessageType = $dom->createElement("MessageType", "Product");
		$rss->appendChild($MessageType);
		$PurgeAndReplace = $dom->createElement("PurgeAndReplace", "true");
		$rss->appendChild($PurgeAndReplace);
// 		$sqlParents = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND parent_id IS NOT NULL and parent_id > 1
//         AND EAN IS NOT NULL AND EAN NOT IN (SELECT id as EAN FROM module_amazon_match_products WHERE store_id = {$this->store_id}) GROUP BY parent_id LIMIT 1";
		$ind = 1;
// 		$sqlParents = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND parent_id IS NOT NULL and parent_id > 1
//         AND EAN != '' AND EAN NOT IN (SELECT id as EAN FROM module_amazon_match_products WHERE store_id = {$this->store_id}) LIMIT 10";
		
		
	    $sqlParents = "SELECT az_products_feed.*, available_products.* FROM az_products_feed 
	    LEFT JOIN available_products ON available_products.id = az_products_feed.product_id AND
	    available_products.store_id = az_products_feed.store_id
	    WHERE az_products_feed.store_id = {$this->store_id} AND az_products_feed.sku LIKE '189'";
	    
		$query = $this->db->query($sqlParents);
		while($rowAP = $query->fetch(PDO::FETCH_ASSOC)){
// 			pre($rowAP);
		    $ind++;
		        
    		$Message = $dom->createElement("Message");
    
    		$MessageID = $dom->createElement("MessageID", $ind);
    		$Message->appendChild($MessageID);
    		$OperationType = $dom->createElement("OperationType", "Update"); //Update, Delete, patialUpdate
    		$Message->appendChild($OperationType);
//     		$parentId = $rowAP['parent_id'];
// 		    $sqlAP = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND parent_id = '{$parentId}'";
// 		    $queryAP = $this->db->query($sqlAP);
// 			while($rowAP = $queryAP->fetch(PDO::FETCH_ASSOC)){		
			    
				$item = $dom->createElement("Product");
					
				$id = $dom->createElement("SKU", "{$rowAP['sku']}");
				$item->appendChild($id);
				
				$StandardProductID = $dom->createElement("StandardProductID");
				$type = $dom->createElement("Type", "EAN");
				$StandardProductID->appendChild($type);
				$value = $dom->createElement("Value", trim($rowAP['ean']));
				$StandardProductID->appendChild($value);
				$item->appendChild($StandardProductID);
				
				$ProductTaxCode = $dom->createElement("ProductTaxCode", "A_GEN_NOTAX");
				$item->appendChild($ProductTaxCode);

				$DescriptionData = $dom->createElement("DescriptionData");
				$title = $DescriptionData->appendChild($dom->createElement('Title'));
				$title->appendChild($dom->createCDATASection(trim($rowAP['title'])));
				$DescriptionData->appendChild($title);
				
				$brand = $DescriptionData->appendChild($dom->createElement('Brand'));
				$brand->appendChild($dom->createCDATASection(trim($rowAP['brand'])));
				$DescriptionData->appendChild($brand);
				
				$description = $DescriptionData->appendChild($dom->createElement('Description'));
				$description->appendChild($dom->createCDATASection(substr(trim(strip_tags($rowAP['description'])), 0, 2000)));
				$DescriptionData->appendChild($description);
				
				$bullet = $DescriptionData->appendChild($dom->createElement('BulletPoint'));
				$bullet->appendChild($dom->createCDATASection($rowAP['title']));
				$DescriptionData->appendChild($bullet);
				
				
				$lengthVal = !empty($rowAP['length']) ? $rowAP['length'] : 15 ;
				$widthVal =  !empty($rowAP['width']) ? $rowAP['width'] : 15;
				$heightVal =  !empty($rowAP['height']) ? $rowAP['height'] : 15 ;
				$weightVal =  !empty($rowAP['weight']) ? $rowAP['weight'] : 1.0 ;
				$correiosVal = 6000;
				
    			$dimensions = $DescriptionData->appendChild($dom->createElement('ItemDimensions'));
    			
	    			$length = $dom->createElement("Length");
	    			$unitMeasure= $dom->createAttribute("unitOfMeasure");
	    			$unit = $length->appendChild($unitMeasure);
	    			$unitValue = $dom->createTextNode("CM");
	    			$unit->appendChild($unitValue);
	    			$length->appendChild($dom->createCDATASection($lengthVal));
	    			$dimensions->appendChild($length);
	    			
	    			$width = $dom->createElement("Width");
	    			$unitMeasure= $dom->createAttribute("unitOfMeasure");
	    			$unit = $width->appendChild($unitMeasure);
	    			$unitValue = $dom->createTextNode("CM");
	    			$unit->appendChild($unitValue);
	    			$width->appendChild($dom->createCDATASection($widthVal));
	    			$dimensions->appendChild($width);
	    			
	    			$height = $dom->createElement("Height");
	    			$unitMeasure= $dom->createAttribute("unitOfMeasure");
	    			$unit = $height->appendChild($unitMeasure);
	    			$unitValue = $dom->createTextNode("CM");
	    			$unit->appendChild($unitValue);
	    			$height->appendChild($dom->createCDATASection($heightVal));
	    			$dimensions->appendChild($height);
    			
    			$DescriptionData->appendChild($dimensions);
    			
    			$itemWeight = $DescriptionData->appendChild($dom->createElement('ItemWeight'));
    			$unitMeasure= $dom->createAttribute("unitOfMeasure");
    			$unit = $itemWeight->appendChild($unitMeasure);
    			$unitValue = $dom->createTextNode("KG");
    			$unit->appendChild($unitValue);
    			$itemWeight->appendChild($dom->createCDATASection($weightVal));
    			$DescriptionData->appendChild($itemWeight);
    			
    			
    			$votageVal =110;
    			$voltage = $DescriptionData->appendChild($dom->createElement('Voltage'));
    			$unitMeasure= $dom->createAttribute("unitOfMeasure");
    			$unit = $voltage->appendChild($unitMeasure);
    			$unitValue = $dom->createTextNode("volts");
    			$unit->appendChild($unitValue);
    			$voltage->appendChild($dom->createCDATASection($votageVal));
    			$DescriptionData->appendChild($voltage);
    			
    			$countryOfOrigin = $DescriptionData->appendChild($dom->createElement('CountryOfOrigin'));
    			$countryOfOrigin->appendChild($dom->createCDATASection("BR"));
    			$DescriptionData->appendChild($countryOfOrigin);
    			
    			$itemType = $DescriptionData->appendChild($dom->createElement('ItemType'));
    			$itemType->appendChild($dom->createCDATASection("Casa > Ar e Ventilação > Ventiladores > Ventiladores de Teto"));
    			$DescriptionData->appendChild($itemType);
				
				$item->appendChild($DescriptionData);
				
				
				$azAttributesModel->category = $rowAP['category'];
				
				$azAttributesModel->LoadXsd();
				
				$azAttributesModel->LoadXsdBase();
				
					
				$ProductData = $dom->createElement("ProductData");
				
				$home = $ProductData->appendChild($dom->createElement($rowAP['xsdName']));
				
				$home->appendChild($dom->createElement('Parentage', 'parent'));
				
				$variationData = $home->appendChild($dom->createElement('VariationData'));
				
				$variationData->appendChild($dom->createElement('VariationTheme', 'Size-Color'));
				
				$home->appendChild($variationData);
				
				$productType = $home->appendChild($dom->createElement($rowAP['set_attribute']));
				
				$choice= $productType->appendChild($dom->createElement($rowAP['choice']));
				
				$attributesValues->product_id = $rowAP['id'];
				
				$attrValues = $attributesValues->GetAttributesValues();
				
				
// pre($azAttributesModel);die;
				
// 				$listAttributesRequired = $azAttributesModel->ListAttributesRequired();
				
// 				pre($listAttributesRequired);
				
// 				$listAttributesRequired = $azAttributesModel->GetProductType();
// 				pre($listAttributesRequired);die;
				$listAttributesRequired = $azAttributesModel->GetProductTypeChoice();
				
				$azAttributesRelationship = $azAttributesModel->GetAzAttributesRelationship();
				
// 				pre($listAttributesRequired);die;
// 				pre($attrValues);die;
				
				foreach ($listAttributesRequired as $key => $attr){
					
// 					if(!empty($attr['name'])){
					if(!empty($attr['type'])){
						
						
// 						switch($attr['type']){
// 							case "xsd:positiveInteger": $value = 1; break;
// 							case "LengthDimension": $value = 10; break;
// 							case "HeightDimension": $value = 10; break;
// 							case "VolumeDimension": $value = 10; break;
// 							case "WidthDimension": $value = 10; break;
// 							case "WeightDimension": $value = 10; break;
// 							default: $value = 'teste'; break;
							
							
// 						}
// 						$elementValue = $dom->createElement($attr['name'], $value);
						
// // 						$choice->appendChild($dom->createElement($attr['name'], $value['value']));
			
						
// 						$choice->appendChild($elementValue);
						
// 						$type = $dom->createAttribute("type");
// 						$elementValue->appendChild($type);
// 						$typeValue = $dom->createTextNode($attr['type']);
// 						$type->appendChild($typeValue);
						
						
						
						
						
// 						$rss = $dom->createElement("AmazonEnvelope");
// 						$dom->appendChild($rss);
						
// 						$version = $dom->createAttribute("xmlns:xsi");
// 						$rss->appendChild($version);
// 						$versionValue = $dom->createTextNode("http://www.w3.org/2001/XMLSchema-instance");
// 						$version->appendChild($versionValue);
						
// 						$version = $dom->createAttribute("xsi:noNamespaceSchemaLocation");
// 						$rss->appendChild($version);
// 						$versionValue = $dom->createTextNode("amzn-envelope.xsd");
// 						$version->appendChild($versionValue);
						
						
						
						
						foreach($attrValues as $k => $value){
							
							if(!empty($value['value'])){
							
								if($value['attribute_id'] == $attr['name']){
									
									$choice->appendChild($dom->createElement($attr['name'], $value['value']));
									
								}else{
									
									//verifi relationship attribute
									foreach($azAttributesRelationship as $i => $attrRel){
										//Verify if current attribute stead in attribute relationship
										if($attr['name'] == $attrRel['az_attribute']){
											//if exist verify attribute value again
											if($value['attribute_id'] == $attrRel['attribute']){
												//if exist add attribute and value
												$choice->appendChild($dom->createElement($attr['name'], $value['value']));
												continue;
											}
											
										}
										
									}
									
								}
								
							}
							
						}
						
					}
					
				}
				

// 				$choice->appendChild($dom->createElement('ColorMap', 'White'));
				
				$productType->appendChild($choice);
				
				$home->appendChild($productType);
				
// 				$variationData = $productType->appendChild($dom->createElement('VariationData'));
				
				
// 				$variationData->appendChild($dom->createElement("Parentage", "parent"));
				
// 				$variationTheme  =$variationData->appendChild($dom->createElement("VariationTheme"));
				
// 				$variationTheme->appendChild($dom->createElement("Color", "{$rowAP['color']}"));
				
// 				$variationData->appendChild($variationTheme);
				
// 				$productType->appendChild($variationData);
				
				$ProductData->appendChild($home);
				
				$item->appendChild($ProductData);
					
				$Message->appendChild($item);
				
				$count++;
// 			}
// 			pre($Message);die;
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