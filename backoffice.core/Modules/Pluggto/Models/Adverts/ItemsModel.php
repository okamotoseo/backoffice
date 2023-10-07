<?php

class ItemsModel extends MainModel
{

    public $store_id;
    
    public $sku;
    
    public $product_id;
    
    public $parent_id;
    
    public $title;
    
    public $category_id;
    
    public $category_publish;
    
    public $price;
    
    public $currency_id = "BRL";
    
    public $available_quantity = 0;
    
    public $buying_mode = "buy_it_now";
    
    public $listing_type_id = "gold";
    
    public $condition = "new";
    
    public $status;
    
    public $attribute_types;
    
    public $description;
    
    public $warranty = "Garantia de fabrica mediante análise.";
    
    public $variations = array();
    
    public $attributes = array();
    
    public $shipping = array (
            "mode" => "me2",
            "local_pick_up" => false
        );
    
    public $product;
    
    public $attributes_values = array();
    
    

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
    
    public function getItemStockPrice(){
        if(!isset($this->attribute_types)){
            
            return array('not_isset_attribute_types');
        }
        
        $item = array();
        
        
        if(!empty( $this->price)){
        	if( $this->available_quantity > 0 ){
		       	$item = array (
		    	    "status" => $this->status,
		            "price" => $this->price,
		            "available_quantity" => $this->available_quantity
		        );
	       	}else{
	       		$item = array (
	       				"status" => $this->status,
	       				"available_quantity" => $this->available_quantity
	       		);
	       	}
        }else{
        	if( $this->available_quantity > 0 ){
        		$item = array (
            		"status" => $this->status,
            		"available_quantity" => $this->available_quantity
            		);
        	}else{
        		$item = array (
        				"status" => $this->status
        		);
        	}
        }
                
        return $item;
        
        
        
    }
    
    public function getItem()
    {
        
        if(!isset($this->attribute_types)){
            
            return array();
        }
        switch ($this->attribute_types) {
            case 'attributes':
                $item = array (
                    "title" => $this->title,
                    "category_id" => $this->category_publish,
                    "price" => $this->price,
                    "currency_id" => $this->currency_id,
                    "available_quantity" => $this->available_quantity,
                    "buying_mode" => $this->buying_mode,
                    "listing_type_id" => $this->listing_type_id,
                    "description" => array("plain_text" => $this->description),
//                     "warranty" => $this->warranty,
                    "video_id" => $this->getVideosIds(),
                    "condition" => $this->condition,
                    "attributes" => $this->getAttributesVariations(),
                    "pictures" => $this->getPictures(),
                    "seller_custom_field" => $this->sku,
                    "shipping" => $this->shipping
                );
                break;
            
            case 'variations':
                $item = array (
                    "title" => $this->title,
                    "category_id" => $this->category_publish,
                    "price" => $this->price,
                    "currency_id" => $this->currency_id,
                    "available_quantity" => $this->available_quantity,
                    "buying_mode" => $this->buying_mode,
                    "listing_type_id" => $this->listing_type_id,
                    "description" => array("plain_text" => $this->description),
//                     "warranty" => $this->warranty,
                    "condition" => $this->condition,
                    "attributes" => $this->getAttributesVariations(),
                    "pictures" => $this->getPictures(),
                    "variations" => $this->getVariations(),
                    "shipping" => $this->shipping
                );
                
//                 pre($item);die;
                break;
        }

        return $item;
        
        
    }
    
    public function getVideosIds(){
        
        $sql = "SELECT * FROM attributes_values WHERE store_id = {$this->store_id} AND product_id = {$this->product_id} 
            AND attribute_id LIKE 'referencia_youtube1'";
        $query = $this->db->query($sql);
        $attrValues = $query->fetch(PDO::FETCH_ASSOC);
        
        $videoId = isset($attrValues['value']) ? $attrValues['value'] : '' ;
        return $videoId;
        
        
    }
    
    
    public function getPictures(){
        $pictures = array();
        $pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$this->store_id}/products/{$this->product_id}";
        $pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$this->store_id}/products/{$this->product_id}";
        if(file_exists($pathRead)){
            $iterator = new DirectoryIterator($pathRead);
            foreach ( $iterator as $key => $entry ) {
                $file = $entry->getFilename();
                if($file != '.' AND $file != '..'){
                    $parts = explode("-", $file);
                    $array = array_slice($parts, -2);
                    
                    $picturesArray[$array[0]] = array('source' =>  $pathShow.'/'.$file);
                    
                }
            }
            ksort($picturesArray);
            
            foreach ($picturesArray as $key => $pics) {
                $pictures[] = $pics;
            }
           
            
        }
            
        return $pictures;
        
    }
    
    public function getAttributes()
    {
        $this->getAttributesValues();
        if(!isset($this->attributes_values)){
            return array();
        }
        foreach($this->attributes_values as $key => $attrValue){
            if(!empty($attrValue['tag'])){
                $tags = json_decode($attrValue['tag']);
                $attrValue['tags'] = $tags;
                if(!empty($attrValue['value'])){
                    $this->attributes[] = array(
                        "id" =>  $attrValue['attribute_id'],
                        "value_name"=> $attrValue['value']
                    );
                }
            }
        }
        return $this->attributes;
    }
    
    public function getValueIdMlAttribute( $attribute_id, $value){
       $sqlValueId = "SELECT value_id FROM `ml_attributes_required`
    WHERE store_id = {$this->store_id} AND attribute_id LIKE '{$attribute_id}' AND value LIKE '{$value}' LIMIT 1";
        $queryValueId = $this->db->query($sqlValueId);
        $resValueId = $queryValueId->fetch(PDO::FETCH_ASSOC);
        if(!$queryValueId){
            return '';
        }
        
        return $resValueId['value_id'];
    }
    
    /**
     * Separa os atributos da variação dos atributos do produto
     * de acordo com as especificação das tags da tabela atributos requerido
     */
    public function getAttributesVariations()
    {
        $this->getAttributesValues();
        if(!isset($this->attributes_values)){
            return array();
        }
        
        foreach($this->attributes_values as $key => $attrValue){
            if(!empty($attrValue['tag'])){
                $tags = json_decode($attrValue['tag']);
                $attrValue['tags'] = $tags;
                
                if($this->attribute_types == 'variations'){
                    if(!isset($tags->defines_picture) AND !isset($tags->allow_variations) AND !isset($tags->variation_attribute)){
                        if(!empty($attrValue['value'])){
                            $mlValueId = $this->getValueIdMlAttribute($attrValue['attribute_id'], $attrValue['value']);
                            $this->attributes[] = array(
                                "id" =>  $attrValue['attribute_id'],
                                "value_id" => $mlValueId,
                                "value_name"=> $attrValue['value']
                            );
                        }
                    }
                }
                if($this->attribute_types == 'attributes'){
                    if(!isset($tags->defines_picture) AND !isset($tags->allow_variations)){
                        if(!empty($attrValue['value'])){
                            $mlValueId = $this->getValueIdMlAttribute($attrValue['attribute_id'], $attrValue['value']);
                            $this->attributes[] = array(
                                "id" =>  $attrValue['attribute_id'],
                                "value_id" => $mlValueId,
                                "value_name"=> $attrValue['value']
                            );
                        }
                    }
                }
            }
        }
        return $this->attributes;
    }
    
    /**
     * Recupera os valores dos atributos
     */
    public function getAttributesValues()
    {
        
        if(!isset($this->category_id)){
            
            return array();
        }
        
        $sqlAttrValue = "SELECT attributes_values.store_id, attributes_values.product_id,attributes_values.attribute_id, attributes_values.value, 
        ml_attributes_required.tag,  ml_attributes_required.category_id,  ml_attributes_required.value_type,  ml_attributes_required.value_id  
        FROM attributes_values
        LEFT JOIN ml_attributes_required ON attributes_values.attribute_id = ml_attributes_required.attribute_id
        AND attributes_values.store_id = ml_attributes_required.store_id 
        AND ml_attributes_required.category_id = '{$this->category_id}'
        WHERE attributes_values.store_id = {$this->store_id} AND attributes_values.product_id = '{$this->product_id}' 
        GROUP BY attributes_values.attribute_id";
        $query = $this->db->query($sqlAttrValue);
        $attrValues = $query->fetchAll(PDO::FETCH_ASSOC);
//         pre($attrValues);
        foreach($attrValues as $key => $attribute){
            
            if(empty($attribute['tag'])){
                
                $sqlAttrRel = "SELECT 
                ml_attributes_relationship.ml_attribute_id,
                ml_attributes_required.*
                FROM ml_attributes_relationship
                RIGHT JOIN ml_attributes_required 
                ON ml_attributes_relationship.ml_attribute_id = ml_attributes_required.attribute_id
                AND ml_attributes_relationship.ml_category_id = ml_attributes_required.category_id
                AND ml_attributes_required.category_id = '{$this->category_id}'
                WHERE ml_attributes_relationship.store_id = {$this->store_id} 
                AND ml_attributes_relationship.attribute = '{$attribute['attribute_id']}'";
                $queryRel = $this->db->query($sqlAttrRel);
                $attrRel = $queryRel->fetch(PDO::FETCH_ASSOC);
                
                if(!empty($attrRel)){
                    
                    foreach($attrValues as $ind => $values){
                        if($values['attribute_id'] === $attrRel['attribute_id']){
                            $attrValues[$ind] = array();
                        }
                    }
                    
                    $attrValues[$key]['category_id'] = $attrRel['category_id'];
                    $attrValues[$key]['attribute_id'] = $attrRel['attribute_id'];
                    $attrValues[$key]['tag'] = $attrRel['tag'];
                    $attrValues[$key]['value_type'] = $attrRel['value_type'];
                    $attrValues[$key]['value_id'] = $attrRel['value_id'];
                }

            
            }
            
            
        }
        $attrValues = array_filter($attrValues);
//         pre($attrValues);die;
        $sqlReq = "SELECT store_id, attribute_id, tag, category_id, value_type, value_id FROM ml_attributes_required
        WHERE store_id = '{$this->store_id}' AND category_id = '{$this->category_id}'
        GROUP BY attribute_id";
        $query = $this->db->query($sqlReq);
        $attrReq = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($attrReq as $key => $attr){
            $exists = false;
            foreach($attrValues as $key2 => $attrvalue){
                if($attr['attribute_id'] == $attrvalue['attribute_id']){
                    $exists = true;
                    break;
                }
            }
            if(!$exists){
                $attrValues[] = array_slice($attr, 0, 2, true) + 
                array("value" => "") + array_slice($attr, 2, count($attr) -1, true);
                $exists = true;
            }
          
        }
        $sql = "SELECT * FROM `available_products` WHERE store_id = {$this->store_id} AND `id` = {$this->product_id}";
        $query = $this->db->query($sql);
        $product = $query->fetch(PDO::FETCH_ASSOC);
//         pre($attrValues);die;
        foreach($attrValues as $key => $attr){
            
            switch($attr['attribute_id']){
                case "BRAND": $attrValues[$key]['value'] = $product['brand']; break;
                case "EAN": $attrValues[$key]['value'] = $product['ean']; break;
                case "GTIN": $attrValues[$key]['value'] = $product['ean']; break;
                case "PACKAGE_HEIGHT": $attrValues[$key]['value'] = $product['height']." cm"; break;
                case "PACKAGE_LENGTH": $attrValues[$key]['value'] = $product['length']." cm"; break;
                case "PACKAGE_WIDTH": $attrValues[$key]['value'] = $product['width']." cm"; break;
                case "PACKAGE_WEIGHT": $attrValues[$key]['value'] = $product['weight']; break;
                case "SELLER_SKU": $attrValues[$key]['value'] = $product['sku']; break;
                case "HEAD_DIAMETER": 
                    if(!empty($attr['value'])){
                        $headDiameter =  str_replace(',', '.', $attr['value']);
                        $headDiameter =  str_replace('cm', ' cm', $headDiameter);
                        $headDiameter = str_replace('  ', ' ', $headDiameter);
                        $attrValues[$key]['value'] = $headDiameter;
                    }
                    break;
                case "MODEL": $attrValues[$key]['value'] = empty($attr['value']) ? $product['reference'] : $attr['value']; break;
            }
            if(!empty($attr['value_type'])){
                switch ($attr['value_type']) {
                    case 'number': $attrValues[$key]['value'] = preg_replace("/[^0-9]/", "", $attrValues[$key]['value']); break;
                }
            }
            
        }
        
        
        $this->attributes_values = $attrValues;
        return $attrValues;
        
    }
    
    
    public function getVariations()
    {
        if(!isset($this->attribute_types)){
            return array();
        }
        $sqlVerifyVariation = "SELECT `id`,`sku`, `parent_id`, `variation`, `quantity`, price, ean, color 
        FROM `available_products` WHERE store_id = {$this->store_id} AND `parent_id` LIKE '{$this->parent_id}' ORDER BY id ASC";
        $query = $this->db->query($sqlVerifyVariation);
        $variations = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($variations as $ind => $variation){
            $this->product_id = $variation['id'];
            $this->getAttributesValues();
            if(!isset($this->attributes_values)){
                return array();
            }
            foreach($this->attributes_values as $key => $attrValue){
                if(!empty($attrValue['tag'])){
                    $tags = json_decode($attrValue['tag']);
                    $attrValue['tags'] = $tags;
                    
                    $this->variations["{$ind}"]['price'] = $this->price;
                    $this->variations["{$ind}"]['available_quantity'] = $variation['quantity'];
                    $this->variations["{$ind}"]['seller_custom_field'] = $variation['sku'];
                    $this->variations["{$ind}"]['seller_sku'] = $variation['sku'];
                    
                    if(isset($tags->variation_attribute)){
                        if(!empty($attrValue['value'])){
                            $this->variations["{$ind}"]["attributes"][] = array(
                                "id" =>  $attrValue['attribute_id'],
                                "value_name"=> $attrValue['value']
                            );
                        }
                    }
                    if(isset($tags->allow_variations)){
                        if(isset($tags->defines_picture) AND !isset($this->variations["{$ind}"]["picture_ids"])){
                            $value  = !empty($attrValue['value']) ? $attrValue['value'] : $variation['color'] ;
                            $value = ucwords(implode(" ", array_unique(explode("/", $value))));
                            $value = ucwords(implode(" ", array_unique(explode("-", $value))));
                            $this->variations["{$ind}"]["attribute_combinations"][] = array(
                                "id" =>  $attrValue['attribute_id'],
                                "value_name"=>  $value
                             );
                            $pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$this->store_id}/products/{$variation['id']}";
                            $pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$this->store_id}/products/{$variation['id']}";
                            if(file_exists($pathRead)){
                                $iterator = new DirectoryIterator($pathRead);
                                if(!isset($color)){
                                    $color = $row['color'];
                                    unset($colorsImages);
                                    foreach ( $iterator as $key => $entry ) {
                                        $file = $entry->getFilename();
                                        if($file != '.' AND $file != '..'){
                                            $colorsImages[] =  $pathShow.'/'.$file;
                                        }
                                    }
                                    sort($colorsImages);
                                    $this->variations["{$ind}"]["picture_ids"] = $colorsImages;
                                }else{
                                    if(strtoupper($color) == strtoupper($row['color'])){
                                        asort($colorsImages);
                                        $this->variations["{$ind}"]["picture_ids"] = $colorsImages;
                                        unset($color);
                                        unset($colorsImages);
                                    }else{
                                        $color = $row['color'];
                                        unset($colorsImages);
                                        foreach ( $iterator as $key => $entry ) {
                                            $file = $entry->getFilename();
                                            if($file != '.' AND $file != '..'){
                                                $colorsImages[] =  $pathShow.'/'.$file;
                                            }
                                        }
                                        sort($colorsImages);
                                        $this->variations["{$ind}"]["picture_ids"] = $colorsImages;
                                    }
                                }
                                
                            }else{
                                
                                if(isset($this->variations[$ind -1]["picture_ids"])){
                                    $this->variations["{$ind}"]["picture_ids"] = $this->variations[$ind -1]["picture_ids"];
                                }
                            }
                            
                        }else{
                            
                            if(!isset($tags->defines_picture)){
                                
                                $valueName = '';
                                
                                switch(trim($attrValue['attribute_id'])){
                                    case "VOLTAGE": $valueName = $variation['variation']; break;
                                    case "OPERATING_VOLTAGE": $valueName = $variation['variation']; break;
                                    case "SIZE": $valueName = $variation['variation']; break;
                                    case "POWER": 
                                        $valueName =  str_replace(',', '.', $attrValue['value']);
                                        $valueName =  str_replace('W', ' W', $valueName);
                                        $valueName =  str_replace('w', ' W', $valueName);
                                        $valueName =  str_replace('  ', ' ', $valueName);
                                        break;
                                    default: $valueName = $attrValue['value']; break;
                                    
                                }
                                
                                $this->variations["{$ind}"]["attribute_combinations"][] = array(
                                    "id" =>  $attrValue['attribute_id'],
                                    "value_name"=> $valueName
                                );
                                
                               
                            }
                            if(isset($tags->defines_picture) AND isset($this->variations["{$ind}"]["picture_ids"])){
                                if(!empty($attrValue['value'])){
                                    $this->variations["{$ind}"]["attribute_combinations"][] = array(
                                        "id" =>  $attrValue['attribute_id'],
                                        "value_name"=> $attrValue['value']
                                        
                                    );
                                }
                            }
                            
                        }
                        
                        
                    }
                }
            }
        }
        return $this->variations;
    }
    
    
    

} 