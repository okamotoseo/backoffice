<?php




function getUrlImageFromParentIdAndColor($db, $storeId, $parendtId, $color){
    $query = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = ? AND parent_id LIKE ? and color like ?",
        array($storeId, $parendtId, $color));
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
    $urlImage = array();
    foreach($products as $key => $product){
        
        $pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$product['id']}";
        $pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$product['id']}";
        
        if(file_exists($pathRead)){
            
            $iterator = new DirectoryIterator($pathRead);
            foreach ( $iterator as $key => $entry ) {
                $file = $entry->getFilename();
                if($file != '.' AND $file != '..'){
                    $urlImage[] = $pathShow."/".$file;
                }
            }
            if(isset($urlImage)){
                sort($urlImage);
                break;
            }
        }
        
        
    }
    
    return $urlImage;
}


function getAttributesValuesFromParentId($db, $storeId, $parendtId){
    $query = $db->query("SELECT id, parent_id, collection, category FROM available_products WHERE store_id = ? AND parent_id LIKE ?",
        array($storeId, $parendtId));
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
    $collection = '';
    $attributes = array();
    $types = explode(" > ", $products[0]['category']);
    
    if(!empty(end($types))){
        $endType = end($types);
        $attributes[] = array('value' =>  array('code' => 'tipo', 'label' => "{$endType}") , 'label' => 'Tipo', 'code'=>  titleFriendly('Tipo De Calçado'));
    }
    if(!empty($products[0]['collection'])){
        $attributes[] = array('value' =>  array('code' => 'colection', 'label' => "{$products[0]['collection']}") , 'label' => 'Coleção', 'code'=>  titleFriendly('Coleção'));
    }
    if(!empty($products[0]['reference'])){
        $attributes[] = array('value' =>  array('code' => 'reference', 'label' => "{$products[0]['reference']}") , 'label' => 'Referência', 'code'=>  titleFriendly('Referência'));
    }
    
    foreach($products as $key => $product){
        $sql = "SELECT attributes_values.value, attributes.attribute as label, attributes.alias as code FROM attributes_values
        LEFT JOIN attributes ON attributes_values.attribute_id = attributes.alias
        WHERE attributes_values.store_id = {$storeId} AND attributes_values.product_id = {$product['id']}";
        $query = $db->query($sql);
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if($res){
            
            foreach($res as $key => $attr){
                
                $exists = false;
                foreach($attributes as $k => $valTest){
                    
                    if(in_array($attr['code'], $valTest)){
                        $exists = true;
                    }
                }
                
                if(!$exists){
                    if(!empty(trim($attr['value']))){
                      
                        switch($attr['value']){
                            case "male": $attr['value'] = array('code' => "masculino", 'label' => 'Masculino');break;
                            case "female": $attr['value'] = array('code' => "feminino", 'label' => 'Feminino');break;
                            case "adult": $attr['value'] = array('code' => "adulto", 'label' => 'Adulto');break;
                            default :
                                $attr['value'] = array('code' => "{$attr['code']}", 'label' => "{$attr['value']}");
                                break;
                        }
                        
                        
                        $attributes[] = $attr;
                        
                    }
                }
            }
            
        }
        
        
        
    }

    return $attributes;
    
}

?>