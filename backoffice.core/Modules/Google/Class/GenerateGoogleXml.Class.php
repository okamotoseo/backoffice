<?php
class GenerateGoogleXml
{
	private $xmlId;

	private $storeId;
	
	/**
	 * Retorna o valor de(a/o) xmlId.
	 *
	 * @return int
	 */
	public function getXmlId() {
		return $this->xmlId;
	}
	
	/**
	 * Seta o valor de(a/o) xmlId.
	 *
	 * @param int $xmlId
	 */
	public function setXmlId($xmlId) {
		$this->xmlId = $xmlId;
	}
	
	
	/**
	 * Retorna o valor de(a/o) storeId.
	 *
	 * @return int
	*/
	public function getStoreId() {
	         return $this->storeId;
	}
	
	/**
	 * Seta o valor de(a/o) storeId.
	 *
	 * @param int $storeId
	 */
	public function setStoreId($storeId) {
	         $this->storeId = $storeId;
	}

	public function googleXml(){
		
		$sqlInformation = "SELECT * FROM `xml_name` WHERE store_id = {$this->storeId} AND id = {$this->xmlId}";
		$result = mysql_query($sqlInformation);
		while($xmlInformation = mysql_fetch_assoc($result)){
			$fileName = "./xml/channels/store_id_{$this->storeId}/{$xmlInformation['title_friendly']}.xml";
	
			$dom = new DOMDocument('1.0', 'utf-8');
			$dom->substituteEntities = true;
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
	
			$rss = $dom->createElement("rss");
			$dom->appendChild($rss);
	
			$version = $dom->createAttribute("version");
			$rss->appendChild($version);
			$versionValue = $dom->createTextNode("2.0");
			$version->appendChild($versionValue);
	
			$version = $dom->createAttribute("xmlns:g");
			$rss->appendChild($version);
			$versionValue = $dom->createTextNode("http://base.google.com/ns/1.0");
			$version->appendChild($versionValue);
	
			$version = $dom->createAttribute("xmlns:c");
			$rss->appendChild($version);
			$versionValue = $dom->createTextNode("http://base.google.com/cns/1.0");
			$version->appendChild($versionValue);
	
			$chanel = $dom->createElement("channel");
	
			$titleChanel = $dom->createElement("title", "{$xmlInformation['name']}");
			$chanel->appendChild($titleChanel);
			$linkChanel = $dom->createElement("link", "{$xmlInformation['url_store']}");
			$chanel->appendChild($linkChanel);
			$descriptionChanel = $dom->createElement("description", "{$xmlInformation['description']}");
			$chanel->appendChild($descriptionChanel);
	
			$categoryBlocked = array();
			$sqlCategoryBlocked = "SELECT category FROM xml_category_blocked WHERE store_id = {$this->storeId} AND xml_id = {$xmlInformation['id']}";
	
			$rescategory = mysql_query($sqlCategoryBlocked);
	
			while($row = mysql_fetch_assoc($rescategory)){
				$categoryBlocked[] = $row['category'];
			}
	
			$sqlProductId = "SELECT xml_product.xml_name_id, xml_product.product_id, 
				product_xml.id, product_xml.sku, product_xml.title, product_xml.description, product_xml.size,
				product_xml.gender,product_xml.age_group, product_xml.brand, product_xml.google_product_category,
				product_xml.image_link, product_xml.product_type, product_xml.availability,
				product_xml.quantity, product_xml.price, product_xml.sale_price, product_xml.mpn,
				product_xml.months, product_xml.amount, product_xml.condition_product, product_xml.link,
				product_xml.color  FROM xml_product JOIN product_xml ON product_xml.id = xml_product.product_id 
				WHERE xml_product.store_id = {$this->storeId} AND xml_product.xml_name_id = {$xmlInformation['id']} 
				AND xml_product.type != 'blocked'";
			$resProductId = mysql_query($sqlProductId);
			while($row = mysql_fetch_assoc($resProductId)){
				
				$parentId = getParentId($row['sku']);

				if(!in_array($row['product_type'], $categoryBlocked)){
					if(!empty($parentId)){
						$sqlAP = "SELECT sku, size, sale_price, promotion_price, ean FROM available_products WHERE store_id = {$this->storeId} AND parent_id = '{$parentId}' AND quantity > 0";
						$resAP = mysql_query($sqlAP);
						while($rowAP = mysql_fetch_assoc($resAP)){		
							
							$item = $dom->createElement("item");
								
							$id = $dom->createElement("g:id", "{$rowAP['sku']}");
							$item->appendChild($id);
							
							$itemGroupId = $dom->createElement("g:item_group_id", "{$parentId}");
							$item->appendChild($itemGroupId);
			
							$title = $item->appendChild($dom->createElement('title'));
							$title->appendChild($dom->createCDATASection(trim($row['title'])));
			
							$parts = explode("?", $row['link']);
							$linkItem = $parts[0].$xmlInformation['parameters'];
							$link = $dom->createElement("link", "{$linkItem}");
							$item->appendChild($link);
							
							$price = $dom->createElement("g:price", "{$rowAP['sale_price']}");
							$item->appendChild($price);
							
							if($rowAP['promotion_price'] > 0){
								$salePrice = $dom->createElement("g:sale_price", "{$rowAP['promotion_price']}");
								$item->appendChild($salePrice);
							}
// 							$sale_price = $dom->createElement("g:sale_price", "{$row['sale_price']}");
// 							$item->appendChild($sale_price);
			
							$description = $item->appendChild($dom->createElement('g:description'));
							$description->appendChild($dom->createCDATASection(trim(strip_tags($row['description']))));
								
							$gender = $dom->createElement("g:gender", "{$row['gender']}");
							$item->appendChild($gender);
			
							$ageGroup = $dom->createElement("g:age_group", "{$row['age_group']}");
							$item->appendChild($ageGroup);

							$sizeSystem = $dom->createElement("g:size_system", "BR");
							$item->appendChild($sizeSystem);
							
							$size = $dom->createElement("g:size", trim($rowAP['size']));
							$item->appendChild($size);
								
							$brand = $item->appendChild($dom->createElement('g:brand'));
							$brand->appendChild($dom->createCDATASection(trim($row['brand'])));
			
							$google_product_category = $dom->createElement("g:google_product_category", "{$row['google_product_category']}");
							$item->appendChild($google_product_category);
			                 
							$image = explode("?", $row['image_link']);
							$image_link = $dom->createElement("g:image_link", "{$image[0]}");
							$item->appendChild($image_link);
			
							$sqlImage = "SELECT * FROM product_image WHERE store_id = {$this->storeId} AND product_id = {$row['id']} ORDER BY position ASC";
							$response = mysql_query($sqlImage);
							while ($additional_image = mysql_fetch_assoc($response)) {
// 							    print_r($additional_image['url']);die;
							    
							    $aImage = explode("?", $additional_image['url']);
							    
							    $additional_image_link = $dom->createElement("g:additional_image_link", "{$aImage[0]}");
								$item->appendChild($additional_image_link);
							}
								
							$product_type = $dom->createElement("g:product_type", "{$row['product_type']}");
							$item->appendChild($product_type);
								
							$availability = $dom->createElement("g:availability", "{$row['availability']}");
							$item->appendChild($availability);
								
								
							$mpn = $dom->createElement("g:mpn", "{$row['mpn']}");
							$item->appendChild($mpn);
							
							$ean = isset($rowAP['ean']) ? trim($rowAP['ean']) : "";
							if(empty($ean)){
							    $sqlGtin = "SELECT gtin FROM  `product_gtin` WHERE  `id` =  {$parentId}";
							    $resGtin = mysql_fetch_assoc(mysql_query($sqlGtin));
							    $ean = !empty($resGtin['gtin']) ? trim($resGtin['gtin']) : "";
							}
							
							
							$gtin = $dom->createElement("g:gtin", "{$ean}");
							$item->appendChild($gtin);
			
							$color = $dom->createElement("g:color", "{$row['color']}");
							$item->appendChild($color);
								
							$condition_product = $dom->createElement("g:condition", "{$row['condition_product']}");
							$item->appendChild($condition_product);
								
							$installment = $dom->createElement("g:installment");
			
							$months = $dom->createElement("g:months", "{$row['months']}");
							$installment->appendChild($months);
			
							$amount = $dom->createElement("g:amount", "{$row['amount']}");
							$installment->appendChild($amount);
			
							$item->appendChild($installment);
								
							$chanel->appendChild($item);
							
// 							$count++;
						}
					}
				}
			}
			$rss->appendChild($chanel);
	
			$dom->appendChild($rss);
	
			if(file_exists($fileName)){
				unlink($fileName);
			}
	
			$dom->save($fileName, LIBXML_NOCDATA);
			header("Content-Type: text/html");
			$dom->saveXML($dom->documentElement, LIBXML_NOCDATA);
	
			$updated = "UPDATE `xml_name` SET `updated`='".date('Y-m-d H:i:s')."' WHERE store_id = {$this->storeId} AND id = {$xmlInformation['id']}";
			mysql_query($updated);
		}
	
	}
	
}