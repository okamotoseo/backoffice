<?php

function setMlLog($db, $storeId, $key = null, $id = null, $type, $section, $information){
    
    $db->insert("ml_log", array(
        "store_id" => $storeId,
        "key" => $key,
        "id" => $id,
        "type" => $type,
        "section" => $section,
        "information" => $information
    ));
    
    
}

function getLayoutDescription($db, $storeId, $productId, $MlCategory){
    
    $textDescriptions = array();
    $description = '';
    
    $sqlAttr = "SELECT * FROM attributes_values WHERE attribute_id NOT IN (
    
            SELECT attribute FROM ml_attributes_relationship
            WHERE ml_attributes_relationship.store_id = {$storeId}
            AND ml_attributes_relationship.ml_category_id LIKE '{$MlCategory}'
            
    ) AND attribute_id NOT IN (
            SELECT attribute_id FROM ml_attributes_required
            WHERE ml_attributes_required.store_id = {$storeId}
            AND ml_attributes_required.category_id LIKE '{$MlCategory}'
            
    )
    AND attributes_values.store_id = {$storeId} AND attributes_values.product_id = {$productId}
    AND attributes_values.value != ''";
    
    $query = $db->query($sqlAttr);
    $attrInf = $query->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($attrInf as $attribute => $value){
        $sql = "SELECT attribute FROM attributes WHERE store_id = {$storeId} AND alias = '{$value['attribute_id']}'";
        $queryAttr = $db->query($sql);
        $attributeName = $queryAttr->fetch(PDO::FETCH_ASSOC);
        if(!empty($attributeName['attribute']) AND !empty($value['value'])){
            
            $description .= $attributeName['attribute'].": ".$value['value'];
            $description .= "\n\n";
        }else{
            // atributos sem relacionamento com tabela attributes
        }
    }
    
    $sql = "SELECT sku, reference, weight, height, width, length FROM available_products
   WHERE store_id = {$storeId} AND id = '{$productId}'";
    $query = $db->query($sql);
    $defaultAttr = $query->fetch(PDO::FETCH_ASSOC);
    foreach($defaultAttr as $key => $attr){
        switch($key){
            case "sku": $description .= "SKU: ".$attr."\n\n"; break;
            case "reference": $description .= "Referência: ".$attr."\n\n"; break;
            
            
        }
    }
    
    
    $textTitle = '';
    $textDescription = '';
    $sqlDescriptions = "SELECT title, description, marketplace FROM product_descriptions
   WHERE store_id = {$storeId} AND product_id = {$productId} AND marketplace = 'Mercadolivre'";
    $queryMlDesc = $db->query($sqlDescriptions);
    $mlDescription =  $queryMlDesc->fetch(PDO::FETCH_ASSOC);
    
    if(isset($mlDescription['description']) AND !empty($mlDescription['description'])){
        $textDescription = $mlDescription['description'];
        
    }
    if(isset($mlDescription['title']) AND !empty($mlDescription['title'])){
        $textTitle = $mlDescription['title'];
    }
    
    if(empty($textDescription)){
        $sqlDescriptions = "SELECT title, description, marketplace FROM product_descriptions
       WHERE store_id = {$storeId} AND product_id = {$productId} AND marketplace = 'default'";
        $queryDfDesc = $db->query($sqlDescriptions);
        $dfDescription =  $queryDfDesc->fetch(PDO::FETCH_ASSOC);
        if(isset($dfDescription['description']) AND !empty($dfDescription['description'])){
            $textDescription = $dfDescription['description'];
        }
        
    }
    
    if(empty($textTitle)){
        $sqlDescriptions = "SELECT title, marketplace FROM product_descriptions
       WHERE store_id = {$storeId} AND product_id = {$productId} AND marketplace = 'default'";
        $queryDfDesc = $db->query($sqlDescriptions);
        $dfDescription =  $queryDfDesc->fetch(PDO::FETCH_ASSOC);
        if(isset($dfDescription['title']) AND !empty($dfDescription['title'])){
            $textTitle = $dfDescription['title'];
        }
        
    }
    
    if(empty($textDescription)){
        $sql = "SELECT description, title FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
        $query = $db->query($sql);
        $apDescription =  $query->fetch(PDO::FETCH_ASSOC);
        $textDescription = !empty($apDescription['description']) ? $apDescription['description'] : '';
        
    }
    if(empty($textTitle)){
        $sql = "SELECT description, title FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
        $query = $db->query($sql);
        $apDescription =  $query->fetch(PDO::FETCH_ASSOC);
        $textTitle = !empty($apDescription['title']) ? $apDescription['title'] : '';
        
    }
    
    
    //    $description .= "\n\n";
    $description .= "\t".$textDescription;
    
    $textDescriptions['title'] = substr ( $textTitle, 0, 60 );
    $textDescriptions['description'] = $description;
    
    return $textDescriptions;
    
}

function getItemArrayDefault($db, $meli, $storeId, $sku){
    $categoryIdBrand = "";
    $categoryId = "";
    $itemAttributes = array();
    
    $sql = "SELECT * FROM `available_products` WHERE store_id = {$storeId} AND sku = '{$sku}'";
    $query = $db->query($sql);
    $resProduct = $query->fetch(PDO::FETCH_ASSOC);
    
    $mlCategoryId = getMlCategoryId($db, $storeId, $resProduct['category']);
    
    $htmlDescription = getLayoutDescription ($db, $storeId, $resProduct['id'], $mlCategoryId );
    
    $itemAttributes = getItemAttributes($db, $storeId, $resProduct ['sku'], $mlCategoryId);
    
    $attributes = getAttributesArray($db, $storeId, $itemAttributes, $mlCategoryId);
    
    $brand = ucwords ( strtolower ( trim ( removeAcentosNew($resProduct ['brand']) ) ) );
    
    
    $result = getCategories ( $meli, $mlCategoryId );
    
    if (! empty ( $result ['body']->children_categories )) {
        foreach ( $result ['body']->children_categories as $key => $category ) {
            if (strpos($category->name, $brand)) {
                $categoryIdBrand = $category->id;
                $result2 = getCategories ( $meli, $category->id );
                foreach ( $result2 ['body']->children_categories as $key2 => $category2 ) {
                    if ($category2->name == "Outros Modelos") {
                        $categoryIdBrand = $category2->id;
                    }
                }
            }
        }
        if (empty ( $categoryIdBrand )) {
            foreach ( $result ['body']->children_categories as $key => $category ) {
                if ($category->name == "Outras Marcas"	 OR $category->id == "MLB199584" OR $category->id == "MLB199572") {
                    $categoryIdBrand = $category->id;
                    $result2 = getCategories ( $meli, $category->id );
                    foreach ( $result2 ['body']->children_categories as $key2 => $category2 ) {
                        if ($category2->name == "Outros Modelos") {
                            $categoryIdBrand = $category2->id;
                        }
                    }
                }
            }
        }
    } else {
        $categoryIdBrand = $mlCategoryId;
        
        if ($result ['body']->name  == "Outras Marcas") {
            
            $rootCategories = $result['body']->path_from_root;
            $prevCategory =  $rootCategories[count($rootCategories)-2];
            $mlCategories = getCategories ( $meli, $prevCategory->id );
            if (! empty ( $mlCategories ['body']->children_categories )) {
                foreach ( $mlCategories ['body']->children_categories as $key => $category ) {
                    $parts = explode(" ", $category->name);
                    if (in_array($brand, $parts)) {
                        $categoryIdBrand = $category->id;
                    }
                }
            }
            
        }
    }
    
    
    
    if(!empty($resProduct['parent_id'])){
        $sqlStock = "SELECT sum(quantity) as total FROM `available_products`
        WHERE store_id = {$storeId} AND  `parent_id` LIKE '{$resProduct['parent_id']}'";
        $query = $db->query($sqlStock);
        $totalParent = $query->fetch(PDO::FETCH_ASSOC);
        
        $resProduct['quantity'] += $totalParent['total'];
        
    }
    $price = str_replace ( ",", ".", getSalePriceMl ($db, $storeId, $resProduct ['sku'] ) );
    
    $arrayAttributeCombination = getAttributeCombination($db, $storeId, $resProduct['sku'], $resProduct['parent_id'], $resProduct['color'], $mlCategoryId, $resProduct['category']);
    //     pre($arrayAttributeCombination);die;
    foreach($arrayAttributeCombination as $key => $value){
        if($value['available_quantity'] > 0){
            $attributeCombination[] = $value;
        }
    }
    
    //     pre($attributeCombination);
    //     die;
    //     $listType = $price >= 100 ? "gold_pro" : "bronze" ;
    
    $listType = 'bronze';
    $item = array (
        "title" => "{$htmlDescription ['title']}",
        "category_id" => "{$categoryIdBrand}",
        "price" => $price,
        "currency_id" => "BRL",
        "available_quantity" => $resProduct['quantity'],
        "buying_mode" => "buy_it_now",
        "listing_type_id" => $listType,
        "condition" => "new",
        "description" => array("plain_text" => "{$htmlDescription['description']}"),
        "warranty" => "Garantia de fabrica mediante análise.",
        "variations" => $attributeCombination,
        "attributes" => $attributes,
        "shipping" => array (
            "mode" => "me2",
            "local_pick_up" => false
        )
        );
    return $item;
}


/**
 * Pega os atributos referente a variação do item
 */
function getAttributeCombination($db, $storeId, $sku, $parentId, $colors, $mlCategoryId, $productType){
    $ind = 0;
    $variation = array();
    
    $sqlVerifyVariation = "SELECT `id`,`sku`, `parent_id`, `variation`, `quantity`, price, ean, color FROM `available_products`
	WHERE store_id = {$storeId} AND `parent_id` LIKE '{$parentId}' ORDER BY id ASC";
    $query = $db->query($sqlVerifyVariation);
    while($row = $query->fetch(PDO::FETCH_ASSOC)){
        $attrComb = array();
        $sqlRequired = "SELECT * FROM `ml_attributes_required` WHERE category_id = '{$mlCategoryId}' GROUP BY attribute_id";
        $query2 = $db->query($sqlRequired);
        while($rowRequired = $query2->fetch(PDO::FETCH_ASSOC)){
            
            $tags = json_decode($rowRequired['tag']);
            if(isset($tags->allow_variations) AND !isset($tags->defines_picture)){
                $attrValue = "SELECT ml_attributes_relationship.attribute, ml_attributes_relationship.ml_attribute_id,
                attributes_values.value
                FROM ml_attributes_relationship
                LEFT JOIN attributes_values on attributes_values.attribute_id = ml_attributes_relationship.attribute
                AND attributes_values.product_id = {$row['id']}
                AND attributes_values.store_id = {$storeId}
                WHERE ml_attributes_relationship.store_id = '{$storeId}'
                AND ml_attributes_relationship.ml_category_id = '{$mlCategoryId}'
                AND ml_attributes_relationship.ml_attribute_id  LIKE '{$rowRequired['attribute_id']}'";
                
                $attrValue = $db->query($attrValue);
                $relationship = $attrValue->fetch(PDO::FETCH_ASSOC);
                if(isset($relationship['value'])){
                    $variation["{$ind}"]["attribute_combinations"][] = array(
                        "id" => $relationship['ml_attribute_id'],
                        "value_name"=> $relationship['value']
                    );
                    
                }else{
                    
                    switch ($rowRequired['attribute_id']){
                        
                        case "VOLTAGE":
                            $variations = trim($row['variation']);
                            switch($variations){
                            	case "bivolt": $row['variation'] = "110V/220V";break;
                                case "110V": $row['variation'] = "110V";break;
                                case "127V": $row['variation'] = "110V";break;
                                case "220V": $row['variation'] = "220V";break;
                                case "380V": $row['variation'] = "380V";break;
                                case "127V / 220": $row['variation'] = "110V/220V (Bivolt)";break;
                                case "110V / 220": $row['variation'] = "110V/220V (Bivolt)";break;
                                case "127/220V": $row['variation'] = "110V/220V (Bivolt)";break;
                                default: $row['variation'] = "110V";break;
                                
                            }
                            
                            break;
                    }
                    $mlValueId = getValueIdMlAttribute($db, $storeId, $rowRequired['attribute_id'], $row['variation']);
                    $variation["{$ind}"]["attribute_combinations"][] = array(
                        "id" => $rowRequired['attribute_id'],
                        "value_id"=> $mlValueId,
                        "value_name"=> $row['variation']
                    );
                    
                }
                
            }
            if(isset($tags->defines_picture)){
                
                if(!isset($attrComb['id'])){
                    
                    $mlValueId = getValueIdMlAttribute($db, $storeId, $rowRequired['attribute_id'], $row['color']);
                    
                    $attrComb = array(
                        "id" =>  $rowRequired['attribute_id'],
                        "value_id"=> $mlValueId,
                        "value_name"=> ucfirst(strtolower($row['color']))
                        
                    );
                    
                    $variation["{$ind}"]["attribute_combinations"][] = $attrComb;
                    
                    $pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$row['id']}";
                    $pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$row['id']}";
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
                            $variation["{$ind}"]["picture_ids"] = $colorsImages;
                        }else{
                            if(strtoupper($color) == strtoupper($row['color'])){
                                asort($colorsImages);
                                $variation["{$ind}"]["picture_ids"] = $colorsImages;
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
                                $variation["{$ind}"]["picture_ids"] = $colorsImages;
                                
                            }
                            
                        }
                        
                        
                    }else{
                        if(isset($variation[$ind -1]["picture_ids"])){
                            $variation["{$ind}"]["picture_ids"] = $variation[$ind -1]["picture_ids"];
                        }
                    }
                }
                
                
            }
            
            
        }
        
        // Atributos padrões para quantidade preço e sku
        
        $variation["{$ind}"]["available_quantity"] = $row["quantity"];
        $variation["{$ind}"]["price"] = str_replace ( ",", ".", getSalePriceMl ($db, $storeId, $row['sku'] ) );;
        $variation["{$ind}"]["seller_custom_field"] = "{$row['sku']}";
        $variation["{$ind}"]["attributes"][] = array("id" =>  "EAN", "value_name"=> $row['ean']);
        $variation["{$ind}"]["attributes"][] = array("id" =>  "GTIN", "value_name"=> $row['ean']);
        
        $ind++;
    }
    return $variation;
}
function getValueIdMlAttribute($db, $storeId, $attribute_id, $value){
    $sqlValueId = "SELECT value_id FROM `ml_attributes_required`
    WHERE store_id = {$storeId} AND `attribute_id` LIKE '{$attribute_id}' AND `value` LIKE '{$value}' LIMIT 1";
    $queryValueId = $db->query($sqlValueId);
    $resValueId = $queryValueId->fetch(PDO::FETCH_ASSOC);
    if(!$queryValueId){
        return '';
    }
    
    return $resValueId['value_id'];
}

function getItemAttributes($db, $storeId, $sku, $mlCategoryId){
    $sql = "SELECT id,  brand, reference, weight, height, width, length FROM available_products
    WHERE store_id = {$storeId} AND sku LIKE '{$sku}' LIMIT 1 ";
    $query = $db->query($sql);
    $product = $query->fetch(PDO::FETCH_ASSOC);
    //     $product['brand'] = trim(removeAcentosNew($product['brand']));
    //     $product['modelo'] = trim(removeAcentosNew($product['reference']));
    //     $attributes['title'] = substr ( $product['title'], 0, 60 );
    $attributes = array();
    if(!empty($product['reference'])){
        $attributes['reference'] = trim(removeAcentosNew($product['reference']));
    }
    
    foreach($product as $key => $value){
        $sqlRel = "SELECT ml_attribute_id FROM ml_attributes_relationship
        WHERE store_id = '{$storeId}'  AND ml_category_id = '{$mlCategoryId}' AND attribute  = '{$key}'";
        $queryRel = $db->query($sqlRel);
        $resRel = $queryRel->fetch(PDO::FETCH_ASSOC);
        if(isset($resRel['ml_attribute_id'])){
            $attributes[$resRel['ml_attribute_id']] = $value;
            unset($attributes[$key]);
        }else{
            switch($key){
                case "reference": $attributes['MODEL'] = $value;break;
                case "brand": $attributes['BRAND'] = trim(removeAcentosNew($value));break;
                default: $attributes[$key] = $value;break;
                
            }
            
        }
        
    }
    
    $sqlDesc = "SELECT attribute_id, value FROM attributes_values
    WHERE store_id = {$storeId} AND product_id = {$product['id']} AND value != '' ";
    $query = $db->query($sqlDesc);
    while ($rowDesc = $query->fetch(PDO::FETCH_ASSOC)){
        
        $sqlReq = "SELECT attribute_id FROM ml_attributes_required
            WHERE store_id = '{$storeId}' AND category_id = '{$mlCategoryId}'
            AND attribute_id  = '{$rowDesc['attribute_id']}'";
        $queryReq = $db->query($sqlReq);
        $resReq = $queryReq->fetch(PDO::FETCH_ASSOC);
        if(isset($resReq['attribute_id'])){
            
            $attributes[$resReq['attribute_id']] = $rowDesc['value'];
        }else{
            
            $sqlRel = "SELECT ml_attribute_id FROM ml_attributes_relationship
                WHERE store_id = '{$storeId}' AND ml_category_id = '{$mlCategoryId}'
                AND attribute  = '{$rowDesc['attribute_id']}'";
            $queryRel = $db->query($sqlRel);
            $resRel = $queryRel->fetch(PDO::FETCH_ASSOC);
            
            if(isset($resRel['ml_attribute_id'])){
                $attributes[$resRel['ml_attribute_id']] = $rowDesc['value'];
            }else{
                $attributes[$rowDesc['attribute_id']] = $rowDesc['value'];
            }
        }
    }
    return $attributes;
    
}
//pegar os atributos solicitados pelo ml
function getAttributesArray($db, $storeId, $attributes, $mlCategoryId){
    $attrArray = array();
    foreach($attributes as $key => $value){
        
        $sql = "SELECT value_id, value_type, tag FROM ml_attributes_required
        WHERE store_id = '{$storeId}' AND category_id = '{$mlCategoryId}'
        AND attribute_id  LIKE '{$key}' LIMIT 1";
        $query = $db->query($sql);
        $type = $query->fetch(PDO::FETCH_ASSOC);
        
        switch ($type['value_type']) {
            case 'number': $value = preg_replace("/[^0-9]/", "", $value); break;
        }
        
        if(!empty($value)){
            switch($key){
                case "width":
                    $type['value_id'] = 'cm';
                    $value = $value.'cm';
                    break;
                case "length":
                    $type['value_id'] = 'cm';
                    $value = $value.'cm';
                    break;
                case "height":
                    $type['value_id'] = 'cm';
                    $value = $value.'cm';
                    break;
                case "weight":
                    $type['value_id'] = 'kg';
                    $value = $value.'kg';
                    break;
                    
            }
            $tags = json_decode($type['tag']);
            
            if(!isset($tags->allow_variations) AND !isset($tags->defines_picture)){
                $mlValueId = getValueIdMlAttribute($db, $storeId, $key, $value);
                
                $attrArray[] = array("id" =>  "{$key}",  "value_id"=> $mlValueId, "value_name"=> $value);
            }
        }
        
    }
   
    return $attrArray;
    
}

function getSalePriceMl($db, $storeId, $sku){
    $selectPrice = "SELECT price, sale_price, promotion_price FROM `available_products`
	WHERE store_id = {$storeId} AND `sku`= '{$sku}'";
    $query = $db->query($selectPrice);
    $resStockPrice = $query->fetch(PDO::FETCH_ASSOC);
    $salePrice = $resStockPrice['sale_price'];
    if($resStockPrice['promotion_price'] > 0 AND  $resStockPrice['promotion_price'] < $resStockPrice['sale_price']){
        $salePrice = $resStockPrice['promotion_price'];
    }
    
    $selectTax = "SELECT * FROM `ml_price_rules` WHERE store_id = {$storeId} ";
    $query = $db->query($selectTax);
    while($row = $query->fetch(PDO::FETCH_ASSOC)){
        switch($row['condition']){
            
            case 'sale_price':
                switch($row['operator']){
                    case 'maior':
                        
                        if($resStockPrice[$row['condition']] > $row['value_test']){
                            if($row['rule'] == 'aumentar'){
                                $salePrice = $resStockPrice[$row['condition']];
                                $salePrice += $row['fixed_rate'];
                                $salePrice += ($salePrice * ($row['percentage_rate'] / 100));
                            }
                            if($row['rule'] == 'dominuir'){
                                $salePrice = $resStockPrice[$row['condition']];
                                $salePrice -= $row['fixed_rate'];
                                $salePrice -= ($salePrice * ($row['percentage_rate'] / 100));
                            }
                            
                        }
                        
                        break;
                    case 'menor':
                        
                        if($resStockPrice[$row['condition']] < $row['value_test']){
                            if($row['rule'] == 'aumentar'){
                                $salePrice = $resStockPrice[$row['condition']];
                                $salePrice += $row['fixed_rate'];
                                $salePrice += ($salePrice * ($row['percentage_rate'] / 100));
                            }
                            if($row['rule'] == 'dominuir'){
                                $salePrice = $resStockPrice[$row['condition']];
                                $salePrice -= $row['fixed_rate'];
                                $salePrice -= ($salePrice * ($row['percentage_rate'] / 100));
                            }
                            
                        }
                        
                        break;
                }
                
                break;
                
        }
    }
    
    return number_format(ceil($salePrice)-0.10, 2, '.', '');
}


function getMlCategoryId($db, $storeId, $categoryName){
    $categoryName = trim($categoryName);
    $sql = "SELECT category_id FROM `ml_category_relationship`
    WHERE store_id = {$storeId} AND `category` LIKE '{$categoryName}' LIMIT 1";
    $query = $db->query($sql);
    $res = $query->fetch(PDO::FETCH_ASSOC);
    return  $res['category_id'];
    
}
function getCategories($meli, $category){
    $result = $meli->get("/categories/{$category}");
    return $result;
}


function saveCategoryRequiredAttribute($db, $meli, $storeId, $categoryId){
    $result = $meli->get("/categories/{$categoryId}/attributes");
    $valueInsert = "";
    
    if(!empty($result['body'][0]->id)){
        $db->query("DELETE FROM `ml_attributes_required`
        WHERE  store_id = {$storeId} AND`category_id` = '{$categoryId}'");
        //         pre($result['body']);
        foreach ($result['body'] as $key => $attribute){
//             pre($attribute);
            $attrTags = !empty($attribute->tags) ? json_encode($attribute->tags) : "";
            
            $required = $attribute->tags->catalog_required ? true : 0 ;
            
            if(isset($attribute->values)){
                
                $valuesAttribute = $attribute->values;
            }
            
            if(isset($attribute->allowed_units)){
                $valuesAttribute = $attribute->allowed_units;
            }
            
            if(isset($valuesAttribute)){
                foreach($valuesAttribute as $key => $value){
                    
                    $valueInsert .= "($storeId, '{$categoryId}', '{$attribute->id}',
                    '{$attribute->name}','{$attrTags}','{$value->id}','{$value->name}',
                    '{$attribute->value_type}', '{$required}'),";
                }
                unset($valuesAttribute);
            }else{
                $valueInsert .= "($storeId, '{$categoryId}', '{$attribute->id}',
                    '{$attribute->name}','{$attrTags}','','',
                    '{$attribute->value_type}', '{$required}'),";
            }
            $valueInsert = substr($valueInsert, 0, -1);
            
            $sqlInsert = "INSERT INTO `ml_attributes_required`(`store_id`,`category_id`,`attribute_id`,
            `name`, `tag`, `value_id`, `value`, `value_type`, `required`) VALUES {$valueInsert}";
            $db->query($sqlInsert);
            $valueInsert = "";
            
            
        }
    }
}
/*****************************************************************************************************/
/*********************** Cadastrar novos produtos do Mercado Livre no BD******************************/
/*****************************************************************************************************/
/**
 * Salva o produto/auncio no bd
 * @param int $storeId
 * @param array $item
 * @param string $sku
 */
function saveItem($db, $storeId, $item, $sku){
    $pos = strpos($item->thumbnail, "proccesing_image_pt");
    if($pos === false){
        $thumbnail = $item->thumbnail;
    }else{
        $thumbnail = "http://mlb-s1-p.mlstatic.com/{$item->pictures[0]->id}-I.jpg";
    }
    $updated = date("Y-m-d H:i:s");
    $created = date("Y-m-d H:i:s");
    $id = str_replace("MLB", "", $item->id);
    $title = $item->title;
    $select = "SELECT id FROM ml_products WHERE store_id = {$storeId} AND id = {$id}";
    $query = $db->query($select);
    $res = $query->fetch(PDO::FETCH_ASSOC);
    if(empty($res['id'])){
        $sql = "INSERT INTO `ml_products`(`id`, `store_id`, `sku`, `title`, `price`,
		`available_quantity`, `listing_type_id`, `condition_type`, `permalink`, `thumbnail`,
		`original_price`, `category_id`, `status`, `created`, `updated`) VALUES ({$id}, {$storeId}, '{$sku}', '{$title}','{$item->price}',{$item->available_quantity},
		'{$item->listing_type_id}','{$item->condition}','{$item->permalink}','{$thumbnail}',
		'{$item->original_price}','{$item->category_id}' , '{$item->status}', '{$created}', '{$updated}')";
    }else{
        $sql = "UPDATE `ml_products` SET `title`='{$title}',`price`='{$item->price}',
		`available_quantity`={$item->available_quantity}, `listing_type_id`='{$item->listing_type_id}',
		`condition_type`='{$item->condition}',`permalink`='{$item->permalink}',
		`thumbnail`='{$thumbnail}',`original_price`='{$item->original_price}', `category_id`='{$item->category_id}', 
        `status`='{$item->status}', `updated`= '{$updated}', `flag_import_variations`=1, `flag`=1
		WHERE store_id = {$storeId} AND id = {$id}";
    }
    $query = $db->query($sql);
}
/**
 * Salva o conjunto de atributos do produto/anuncio
 * @param int $storeId
 * @param string $mlb
 * @param array $variations
 */
function saveItemVariations($db, $storeId, $mlb, $variations){
    $mlb = str_replace("MLB", "", $mlb);
    foreach ($variations as $variation){
        
        $select = "SELECT id FROM `ml_products_attributes`
        WHERE `store_id` = {$storeId} AND `product_id` = {$mlb} AND `variation_id` = {$variation->id}";
        $query = $db->query($select);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if(empty($row['id'])){
            $sqlInsert = "INSERT INTO `ml_products_attributes`(`store_id`, `sku`, `product_id`, `variation_id`, `name`, `attribute`, `value`, `information`, `flag`)
				VALUES ({$storeId}, '{$variation->seller_custom_field}', {$mlb}, {$variation->id}, 'available_quantity', 'available_quantity', '{$variation->available_quantity}', '', 2),
				({$storeId}, '{$variation->seller_custom_field}', {$mlb}, $variation->id, 'price', 'price', '{$variation->price}', '', 2),
				({$storeId}, '{$variation->seller_custom_field}', {$mlb}, $variation->id, 'sold_quantity', 'sold_quantity', '{$variation->sold_quantity}', '', 2)";
            $query = $db->query($sqlInsert);
        }else{
            $sqlUpdate = "UPDATE `ml_products_attributes` SET `name`='available_quantity',`attribute`='available_quantity',
			`value`='{$variation->available_quantity}',`information`='',`httpCode`= 0, `flag`= 2
			WHERE `store_id`={$storeId} AND `variation_id` = {$variation->id} AND `name` = 'available_quantity';";
            $query = $db->query($sqlUpdate);
            
            $sqlUpdate = "UPDATE `ml_products_attributes` SET `name`='price',`attribute`='price',
			`value`='{$variation->price}',`information`='',`httpCode`= 0, `flag`= 2
			WHERE `store_id`= {$storeId} AND `variation_id` = {$variation->id} AND `name` = 'price';";
            $query = $db->query($sqlUpdate);
            
            $sqlUpdate = "UPDATE `ml_products_attributes` SET `name`='sold_quantity',`attribute`='sold_quantity',
			`value`='{$variation->sold_quantity}',`information`='',`httpCode`= 0, `flag`= 2
			WHERE `store_id`= {$storeId} AND `variation_id` = {$variation->id} AND `name` = 'sold_quantity';";
            $query = $db->query($sqlUpdate);
            
            
            if(!empty($variation->seller_custom_field)){
                
                $pos = stripos($variation->seller_custom_field, "MLB");
                
                if($pos === false){
                    
                    $sqlUpdateSku = "UPDATE `ml_products_attributes` SET `sku`='{$variation->seller_custom_field}'
        			WHERE `store_id`={$storeId} AND `variation_id` = {$variation->id}";
                    $query = $db->query($sqlUpdateSku);
                }
            }
        }
        
        foreach ($variation->attribute_combinations as $attributes){
            $select = "SELECT id FROM `ml_products_attributes` WHERE `store_id` = {$storeId} AND `product_id` = {$mlb}
			AND `variation_id` = {$variation->id} AND name = '{$attributes->name}'";
            $query = $db->query($select);
            $row1 = $query->fetch(PDO::FETCH_ASSOC);
            if(empty($row1['id'])){
                $attributeCombinations = "INSERT INTO `ml_products_attributes`(`store_id`, `sku`, `product_id`, `variation_id`, `name`, `attribute`, `value`, `information`, `flag`)
					VALUES  ({$storeId}, '{$variation->seller_custom_field}', {$mlb}, {$variation->id}, '{$attributes->name}', '{$attributes->id}', '{$attributes->value_id}', '{$attributes->value_name}', 2)";
            }else{
                $attributeCombinations = "UPDATE `ml_products_attributes` SET `attribute`='{$attributes->id}',
					`value`='{$attributes->value_id}',`information`='{$attributes->value_name}',`httpCode`= 0, `flag`= 2
					WHERE `store_id`={$storeId} AND `variation_id` = {$variation->id} AND `name` = '{$attributes->name}'";
            }
            $query = $db->query($attributeCombinations);
        }
        
        
        foreach ($variation->picture_ids as $key => $picture){
            $select = "SELECT id FROM `ml_products_attributes` WHERE `store_id` = {$storeId} AND `product_id` = {$mlb}
				AND `variation_id` = {$variation->id} AND name = 'picture_ids' AND attribute = '{$key}'";
            $query = $db->query($select);
            $row2 = $query->fetch(PDO::FETCH_ASSOC);
            if(empty($row2['id'])){
                $pictureIds = "INSERT INTO `ml_products_attributes`(`store_id`, `sku`, `product_id`, `variation_id`, `name`, `attribute`, `value`, `information`, `flag`)
					VALUES  ({$storeId}, '{$variation->seller_custom_field}', {$mlb}, {$variation->id}, 'picture_ids', '{$key}', '{$picture}', '', 2)";
            }else{
                $pictureIds = "UPDATE `ml_products_attributes` SET `value`='{$picture}',`information`='',`httpCode`= 0, `flag`= 2
					WHERE `store_id`={$storeId} AND `variation_id` = {$variation->id} AND `name` = 'picture_ids' AND `attribute`='{$key}'";
            }
            $query = $db->query($pictureIds);
        }
        
    }
    $sqlUpdateFlag = "UPDATE `ml_products` SET `flag_import_variations`= 2 WHERE `id` = {$mlb} AND `store_id` = {$storeId}";
    $query = $db->query($sqlUpdateFlag);
    
}

function ml_update_status($db, $storeId, $product_id, $status){
    
    $sql = "UPDATE `ml_products` SET  flag = 2, updated = NOW(), status='{$status}'
    WHERE store_id = {$storeId} AND `id` = {$product_id}";
    $db->query($sql);
    
    
}
function ml_responses($db, $product_id, $variantion_id, $sku, $action, $result){
    
    $error = isset($result['body']->error) ? $result['body']->error : "" ;
    $message = isset($result['body']->message) ? $result['body']->message : "" ;
    $httpCode = isset($result['httpCode']) ? $result['httpCode'] : "" ;
    if(isset($result['body']->cause)){
        foreach ($result['body']->cause as $causes){
            
            $message .= " - ".$causes->code."<br>";
            $message .= $causes->message."<br>";
            
        }
    }
    
    
    $sql = "INSERT INTO `ml_responses`(`product_id`, `variation_id`, `sku`, `action`, `error`, `message`, `httpCode`) VALUES
    ({$product_id}, {$variantion_id}, '{$sku}', '{$action}', '{$error}', '{$message}', '{$httpCode}')";
    $db->query($sql);
    
    $sqlUpdate = "UPDATE `ml_products_attributes` SET `httpCode`='{$httpCode}',`status`='{$action}'
    WHERE product_id = {$product_id} AND variation_id = '{$variantion_id}'";
    $db->query($sqlUpdate);
    
    
}

/*****************************************************************************************************/
/**************************** Atualizar produtos do Mercado Livre ************************************/


function closeAdsProduct($meli, $storeId, $productId, $access_token) {
    $information = array(
        "status" => 'closed'
    );
    $condition = "/items/MLB{$productId}";
    $result = $meli->put($condition, $information, array('access_token' => $access_token));
    return $result;
    
    
}
/*****************************************************************************************************/
/**************************** Etiqueta produtos do Mercado Livre ************************************/


function shippmentLabels($meli, $storeId, $storePath, $pedidoId, $orderdate,  $shippingId, $access_token) {
    
    $labelPathPdf = $storePath."/../Labels/store_id_{$storeId}/{$orderdate}/{$shippingId}.pdf";
    $labelPathZebra = $storePath."/../Labels/store_id_{$storeId}/{$orderdate}/{$shippingId}.txt";
    $labelPath = $storePath."/../Labels/store_id_{$storeId}/{$shippingId}.pdf";
    $storePath .= "/../Labels/store_id_{$storeId}/".$orderdate;
    
    
    if(!is_dir($storePath)){
        mkdir($storePath);
        chmod($storePath, 0777);
        
    }
    if(!file_exists($labelPathPdf)){
        $urlPdf = "https://api.mercadolibre.com/shipment_labels?shipment_ids={$shippingId}&savePdf=Y&access_token={$access_token}";
        $filePdf = file_get_contents($urlPdf);
        file_put_contents($labelPathPdf, $filePdf);
    }
    
    if(!file_exists($labelPathZebra)){
        $urlZebra = "https://api.mercadolibre.com/shipment_labels?shipment_ids={$shippingId}&response_type=zpl2&access_token={$access_token}";
        $fileZebra = file_get_contents($urlZebra);
        file_put_contents($labelPathZebra, $fileZebra);
    }
    //     header("Content-type: application/pdf");
    //     readfile($file);die;
    
}


