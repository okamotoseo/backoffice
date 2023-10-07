<?php
class ProductXml extends GenderAgeGroup
{
	private $urlXml;
	private $storeId;
	private $count = 0;
	private $countUpdate = 0;
	private $countInsert = 0;
	/**
	 * Retorna o valor de(a/o) urlXml.
	 *
	 * @return string
	*/
	public function getUrlXml() {
	         return $this->urlXml;
	}
	
	/**
	 * Seta o valor de(a/o) urlXml.
	 *
	 * @param string $urlXml
	 */
	public function setUrlXml($urlXml) {
	         $this->urlXml = $urlXml;
	}
	
	/**
	 * Retorna o valor de(a/o) storeId.
	 *
	 * @return integer
	*/
	public function getStoreId() {
	         return $this->storeId;
	}
	
	/**
	 * Seta o valor de(a/o) storeId.
	 *
	 * @param integer $storeId
	 */
	public function setStoreId($storeId) {
	         $this->storeId = $storeId;
	}
	
	/**
	 * Retorna o valor de(a/o) countUpdate.
	 *
	 * @return int
	*/
	public function getCountUpdate() {
	         return $this->countUpdate;
	}
	
	/**
	 * Seta o valor de(a/o) countUpdate.
	 *
	 * @param int $countUpdate
	 */
	public function setCountUpdate($countUpdate) {
	         $this->countUpdate = $countUpdate;
	}
	
	/**
	 * Retorna o valor de(a/o) countInsert.
	 *
	 * @return int
	*/
	public function getCountInsert() {
	         return $this->countInsert;
	}
	
	/**
	 * Seta o valor de(a/o) countInsert.
	 *
	 * @param int $countInsert
	 */
	public function setCountInsert($countInsert) {
	         $this->countInsert = $countInsert;
	}
	
	/**
	 * Retorna o valor de(a/o) count.
	 *
	 * @return int
	*/
	public function getCount() {
	         return $this->count;
	}
	
	/**
	 * Seta o valor de(a/o) count.
	 *
	 * @param int $count
	 */
	public function setCount($count) {
	         $this->count = $count;
	}
	
	public function ImportXml() {
				
		
		$rss = simplexml_load_file ($this->urlXml, 'SimpleXMLElement', LIBXML_NOCDATA);

		foreach ($rss->channel->item as $key => $entry){


			$namespaces = $entry->getNameSpaces(true);

			
			$tag = $entry->children($namespaces['g']);

			$itemGroupId = $tag->item_group_id;
			$title = mysql_real_escape_string(trim($entry->title));
			$description = mysql_real_escape_string($entry->description);
			$brand = mysql_real_escape_string(trim($tag->brand));
			$image_link = $tag->image_link;
			$parts = explode('-', $tag->id);
			$id = $parts[0];
			if($parts[1] == 0){
			    
				$GenderAgeGroup = new GenderAgeGroup();
				$GenderAgeGroup->setProductType($tag->product_type);
				$GenderAgeGroup->genderAgeGroup();
				$gender = $GenderAgeGroup->getGender();
				$ageGroup = $GenderAgeGroup->getAgeGroup();
				
				$sqlcategory = "SELECT category.category_google_id, category_google.category 
						FROM category JOIN category_google ON category.category_google_id =  category_google.id
						WHERE category.category LIKE '{$tag->product_type}'";
				$categoryGoogle = mysql_fetch_assoc(mysql_query($sqlcategory));
				
				$sqlVerify = "SELECT product_xml.id, available_products.parent_id 
				FROM `product_xml` LEFT JOIN available_products ON product_xml.sku = available_products.sku 
				WHERE product_xml.store_id = {$this->storeId} AND product_xml.id = {$id}";
				$resId = mysql_fetch_array(mysql_query($sqlVerify));
				
// 				if($tag->mpn == "03807434"){
					if(empty($resId['id'])){
					 	$sqlProductInsert = "INSERT INTO `product_xml`
					 	(id, `store_id`,  `sku`, `item_group_id`, `title`, `description`, `size`, `gender`, `age_group`, `brand`,`google_product_category`,`image_link`, `product_type`,
					 	`availability`, `sale_price`, `mpn`,  `months`, `amount`, `condition_product`, `link`, `color`)  
					 	VALUES ( {$id}, {$this->storeId}, '{$tag->mpn}','{$itemGroupId}',
					 	'{$title}', 
					 	'{$description}',
					 	'{$tag->size}',  
					 	'{$gender}', 
					 	'{$ageGroup}', 
					 	'{$brand}', 
					 	'{$categoryGoogle['category']}', 
					 	'{$image_link}', 
					 	'{$tag->product_type}', 
					 	'{$tag->availability}', 
					 	'{$tag->price}', 
					 	'{$tag->mpn}', 
					 	{$tag->installment->months}, 
			 			'{$tag->installment->amount}', 
					 	'new', 
					 	'{$entry->link}', 
					 	'{$tag->color}')";
						$res = mysql_query($sqlProductInsert);
						if (!$res) {
							echo mysql_error();
							echo "<br>";
							echo "<textarea>";
							print_r($sqlProductInsert);
							echo "</textarea>";
							echo "<br>";
						}else{
							$this->updateAvailableProduct($resId['parent_id'], $tag->mpn);
							$countInsert++;
						}
					 		 	
					}else{	
						$sqlProductUpdate = "UPDATE `product_xml` SET
						`sku`='{$tag->mpn}',
						`item_group_id`={$itemGroupId},
						`title`='{$title}',
						`description`='{$description}',
						`size`='{$tag->size}',
						`gender`='{$gender}',
						`age_group`='{$ageGroup}',
						`brand`='{$brand}',
						`google_product_category`='{$categoryGoogle['category']}',
						`image_link`='{$image_link}',
						`product_type`='{$tag->product_type}',
						`availability`='{$tag->availability}',
						`sale_price`='{$tag->price}',
						`mpn`='{$tag->mpn}',
						`months`={$tag->installment->months},
						`amount`='{$tag->installment->amount}',
						`condition_product`='new',
						`link`='{$entry->link}',
						`color`='{$tag->color}'
						WHERE id = {$resId['id']} AND store_id = {$this->storeId}";
						$result = mysql_query($sqlProductUpdate);
						if (!$result) {
						    echo mysql_error();
						    echo "<br>";
							echo "<textarea>";
							print_r($sqlProductUpdate);
							echo "</textarea>";
							echo "<br>";
						}else{
							$this->updateAvailableProduct($resId['parent_id'], $tag->mpn);
							$countUpdate++;
						}
						
					}
					
					$this->ImportBrands($brand);
					
					if(isset($tag->additional_image_link)){
						$additionalImageLink = array(0 => $image_link);
						foreach($tag->additional_image_link as $key => $value){
							$additionalImageLink[] = $value;
						}
						$this->updateImageLink($id, $additionalImageLink);
						$this->importImage($tag->mpn, $additionalImageLink);
						
					}
					
// 				}
			}

			$sqlItemGroupInsert = "INSERT INTO `product_xml_variations`(`sku`, `store_id`, `item_group_id`, `size`, `color`, `link`) 
					VALUES ('{$tag->mpn}', {$this->storeId}, {$itemGroupId},'{$tag->size}','{$tag->color}', '{$entry->link}')
			ON DUPLICATE KEY UPDATE `sku`='{$tag->mpn}', `item_group_id`={$itemGroupId},`size`='{$tag->size}',
			`color`='{$tag->color}',`link`='{$entry->link}'";
			mysql_query($sqlItemGroupInsert);
			
		}
		
		$this->setCountInsert($countInsert);
		$this->setCountUpdate($countUpdate);
		$this->setCount( $countInsert + $countUpdate );
		
	}
	
	 public function updateImageLink($productId,  $imagesLinks){
		$sql = "DELETE FROM product_image WHERE product_id = {$productId} AND store_id = {$this->storeId}";
		mysql_query($sql);
		foreach ($imagesLinks as $additional_image_link){
			if($additional_image_link != ""){
				$sqlImage ="INSERT INTO `product_image`(`product_id`,`store_id`, `url`)
				VALUES ({$productId}, {$this->storeId}, '{$additional_image_link}')";
				mysql_query($sqlImage);
			}
		}
	}
	
	public function importImage($sku,  $imagesLinks){
		$path = "/var/www/html/fortcalcados.com.br/upload/files/produtos";
		$sqlVerifyVariation = "SELECT `parent_id` FROM `available_products` WHERE `sku` = '{$sku}'";
		$row = mysql_fetch_assoc(mysql_query($sqlVerifyVariation));
		$directory = $row['parent_id'];
		
		$uploadDir = "{$path}/{$directory}";
		if (!file_exists($uploadDir)) {
			mkdir($uploadDir, 0777, true);
		}
		
		foreach($imagesLinks as $url){
			$url = str_replace("/460/", "/800/", $url);
			$parts =  explode("/", $url);
			$order = (count($parts)-2);
			$info =  explode("-", $parts["{$order}"]);
			$titleParts= explode("?", end($parts));
			$title = $titleParts[0];
			
			if(count($info) > 1){
				$ind = end($info);
			}else{
// 				unlink("{$uploadDir}/0-{$title}");
				$ind = 1;
			}
			
			$output = "{$uploadDir}/{$ind}-{$title}";
			if(!file_exists($output)){
			    shell_exec("wget -q \"{$url}\" -O {$output}");
			}
		}
	}
	
	public function ImportBrands($brand){
		// TODO: Definir um padrão de texto para marca e um label.
		// EX. d'metalo, NIKE, Nike
		$sql = "SELECT count(id) as total FROM brands WHERE store_id = {$this->storeId} AND brand = '{$brand}'";
		$info = mysql_fetch_array(mysql_query($sql));
		if(empty($info['total'])){
			$sql ="INSERT INTO brands (`store_id`, `brand`) VALUES ({$this->storeId}, '{$brand}')";
			mysql_query($sql);
		}
	}

	public function updateAvailableProduct($parentId, $sku){
		if(!empty($parentId)){
			$sql = "SELECT count(sku) as total FROM available_products WHERE xml = 'F'  AND parent_id LIKE '{$parentId}'";
			$res = mysql_fetch_assoc(mysql_query($sql));
			if($res['total'] > 0){
				$update = "UPDATE available_products SET xml = 'T' WHERE store_id = {$this->storeId} AND parent_id LIKE '{$parentId}' AND xml = 'F'";
				mysql_query($update);
			}
		}
		else{
			echo $sku;
			echo "<br>";
		}
	
	}
		
}
?>