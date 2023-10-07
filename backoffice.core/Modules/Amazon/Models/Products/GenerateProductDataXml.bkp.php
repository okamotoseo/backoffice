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
	    
	    $salePriceModel = new SalePriceModel($this->db, null, $this->store_id);
		
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
		
		$sqlParents = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND parent_id IS NOT NULL AND parent_id > 1  AND EAN != '' AND EAN IS NOT NULL";
		
		if($this->store_id == '3'){
		    $sqlParents = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND category LIKE 'Masculino%' AND parent_id IS NOT NULL AND parent_id > 1  AND EAN != '' AND EAN IS NOT NULL LIMIT 15";
		}
		
		
		$sqlParents = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND parent_id LIKE '6938-exported' LIMIT 1";
		$query = $this->db->query($sqlParents);
		while($rowAP = $query->fetch(PDO::FETCH_ASSOC)){
			pre($rowAP);
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
    			
//     			$searchTerms = $DescriptionData->appendChild($dom->createElement('SearchTerms'));
//     			$searchTerms->appendChild($dom->createCDATASection("Ventilador de Teto"));
//     			$DescriptionData->appendChild($searchTerms);
    			
//     			$salePriceModel->sku = $rowAP['sku'];
//     			$salePriceModel->marketplace = "Amazon";
//     			$salePrice = $salePriceModel->getSalePrice();
//     			$MSRP = $dom->createElement('MSRP');
//     			$currency = $dom->createAttribute("currency");
//     			$cur = $MSRP->appendChild($currency);
//     			$currencyValue = $dom->createTextNode("BRL");
//     			$cur->appendChild($currencyValue);
//     			$MSRP->appendChild($dom->createCDATASection(trim($salePrice)));
//     			$DescriptionData->appendChild($MSRP);
    			$countryOfOrigin = $DescriptionData->appendChild($dom->createElement('CountryOfOrigin'));
    			$countryOfOrigin->appendChild($dom->createCDATASection("BR"));
    			$DescriptionData->appendChild($countryOfOrigin);
    			
    			$itemType = $DescriptionData->appendChild($dom->createElement('ItemType'));
    			$itemType->appendChild($dom->createCDATASection("Casa > Ar e Ventilação > Ventiladores > Ventiladores de Teto"));
    			$DescriptionData->appendChild($itemType);
    			
// 		$productType = $ProductData->appendChild($dom->createElement('ProductType'));
// 				$productType->appendChild($dom->createCDATASection("HOME"));
// 				$productType->appendChild($dom->createCDATASection("Ar e Ventilação"));
// 				$productType->appendChild($dom->createCDATASection("Ventiladores"));
// 				$productType->appendChild($dom->createCDATASection("Ventiladores de Teto"));
				
// 				$weight = $DescriptionData->appendChild($dom->createElement('PackageWeight'));
// 				$unitMeasure= $dom->createAttribute("unitOfMeasure");
// 				$unit = $weight->appendChild($unitMeasure);
//     			$unitValue = $dom->createTextNode("KG");
//     			$unit->appendChild($unitValue);
//     			$weight->appendChild($dom->createCDATASection(trim($rowAP['weight'])));
//     			$DescriptionData->appendChild($weight);
    			
//     			$pesoCubico =  ( $lengthVal * $widthVal * $heightVal ) / $correiosVal ;//correios
//     			$peso = $weight >= $pesoCubico ? $weight : $pesoCubico; 
//     			$shippingWeight = $DescriptionData->appendChild($dom->createElement('ShippingWeight'));
//     			$unitMeasure= $dom->createAttribute("unitOfMeasure");
//     			$unit = $shippingWeight->appendChild($unitMeasure);
//     			$unitValue = $dom->createTextNode("KG");
//     			$unit->appendChild($unitValue);
//     			$shippingWeight->appendChild($dom->createCDATASection(number_format($peso, 2)));
//     			$DescriptionData->appendChild($shippingWeight);
    			
//     			$usedFor = $DescriptionData->appendChild($dom->createElement('UsedFor'));
//     			$usedFor->appendChild($dom->createCDATASection(trim($rowAP['title'])));
//     			$DescriptionData->appendChild($usedFor);
    			
// ItemWeight
// ItemVolume
// Voltage
// SizeName
// SizeMap
				
				$item->appendChild($DescriptionData);
					
				$ProductData = $dom->createElement("ProductData");
				
				$home = $ProductData->appendChild($dom->createElement('HomeImprovement'));
				
				$productType = $home->appendChild($dom->createElement('ProductType'));
				
				$smallHomeAppliances= $productType->appendChild($dom->createElement('BuildingMaterials'));

				$smallHomeAppliances->appendChild($dom->createElement('ColorMap', 'White'));
				
				$productType->appendChild($smallHomeAppliances);
				
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