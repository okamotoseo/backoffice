<?php

function standardizeVariation($type, $variation){
    
    if(strtolower($type) == 'voltagem' && !empty($variation)){
           
        switch(trim($variation)){
            case '110v': $variationStd = '110v'; break;  
            case '127V': $variationStd = '110v'; break;
            case '127 V': $variationStd = '110v'; break;
            case '220 V': $variationStd = '220v'; break;
            case '220V': $variationStd = '220v'; break;
            case '127v / 220': $variationStd = 'Bivolt'; break;
            case '127V / 220': $variationStd = 'Bivolt'; break;
            case '110 / 220': $variationStd = 'Bivolt'; break;
            case 'BIVOLT': $variationStd = 'Bivolt'; break;
            case '127/220V': $variationStd = 'Bivolt'; break;
            case '110V / 220': $variationStd = 'Bivolt'; break;
            case '127 v/220v': $variationStd = 'Bivolt'; break;
            case '110 V': $variationStd = '110v'; break;
            case '110/220': $variationStd = 'Bivolt'; break;
            case '127 / 220v': $variationStd = 'Bivolt'; break;
            case '110V / 220V (Bivolt)': $variationStd = 'Bivolt'; break;
            case '127': $variationStd = '110v'; break;
            case '127 v': $variationStd = '110v'; break;
            default: $variationStd = $variation; break;
            
        } 
        
        return $variationStd;
        
    }
    
    if(strtolower($type) == 'color' && !empty($variation)){
        
        $variationStd = str_replace(' ', '-', trim($variation));
        $variationStd = str_replace('-/-', '/', $variationStd);
        $variationStd = str_replace('-/', '/', $variationStd);
        $variationStd = str_replace('-/', '/', $variationStd);
        $variationStd = str_replace('/-', '/', $variationStd);
        $variationStd = str_replace('-', '/',$variationStd);
        $variationStd = str_replace(' ', '', $variationStd);
        $variationStd = mb_strtoupper(removeAcentosNew(trim($variationStd)), 'UTF-8');
  
        return $variationStd;
        
    }
    
    
    return $variation;
    
}

function convertStatusTray($status){
    
    if(empty($status)){
        return;
    }
    
    $statusSysplace = '';
    
    switch($status){
        
        case "A ENVIAR": $statusSysplace = 'ready_to_ship'; break;
        case "A ENVIAR YAPAY": $statusSysplace = 'ready_to_ship'; break;
        case "ENVIADO": $statusSysplace = 'shipped'; break;
        case "CANCELADO AUT": $statusSysplace = 'cancelled'; break;
        case "CANCELADO": $statusSysplace = 'cancelled'; break;
        case "FINALIZADO": $statusSysplace = 'delivered'; break;
        case "AGUARDANDO YAPAY": $statusSysplace = 'pending_payment'; break;
        case "AGUARDANDO PAGAMENTO": $statusSysplace = 'pending_payment'; break;
        default:  $statusSysplace = $status; break;
    }
    
    return $statusSysplace;
    
}
function getParentIdByProductId($db, $storeId, $productId){
    if(!isset($productId)){
        return array();
    }
    
    $sql = "SELECT parent_id, reference, brand FROM available_products WHERE store_id = {$storeId} AND id = {$productId} ";
    $query = $db->query($sql);
    $rowParentId = $query->fetch(PDO::FETCH_ASSOC);
    if(isset($rowParentId['parent_id'])){
        $parentId = $rowParentId['parent_id'];
    }
    
    return $parentId;
}
function getAttributesValuesFromParentId($db, $storeId, $parendtId){
    $query = $db->query("SELECT id, parent_id, collection, category FROM available_products WHERE store_id = ? AND parent_id LIKE ?",
        array($storeId, $parendtId));
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
//     pre($products);die;
    $collection = '';
    $attributes = array();
    $types = explode(" > ", $products[0]['category']);
    
    
    
    foreach($products as $key => $product){
        $sql = "SELECT attributes_values.value, attributes.attribute, attributes.alias FROM attributes_values
        LEFT JOIN attributes ON attributes_values.attribute_id = attributes.alias
        WHERE attributes_values.store_id = {$storeId} AND attributes_values.product_id = {$product['id']}";
        $query = $db->query($sql);
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        if($res){
            
            foreach($res as $key => $attr){
                if(!in_array($res, $attributes)){
                    switch($attr['value']){
                        case "male": $attr['value'] = 'Masculino';break;
                        case "adult": $attr['value'] = 'Adulto';break;
                    }
                    $attributes[] = $attr;
                }
            }
//             $collection = isset($product['collection']) && !empty($product['collection']) ? $product['collection'] : $collection ;
            
        }
        
        
        
    }
    $attributes[] = array('value' => end($types) , 'attribute' => 'Tipo De Calçado', 'alias'=>  titleFriendly('Tipo De Calçado'));
    $attributes[] = array('value' => $products[0]['collection'] , 'attribute' => 'Coleção', 'alias'=>  titleFriendly('Coleção'));
    $attributes[] = array('value' => 'Masculino' , 'attribute' => 'Gênero', 'alias'=>  titleFriendly('Gênero'));
    $attributes[] = array('value' => 'Adulto' , 'attribute' => 'Faixa Etária', 'alias'=>  titleFriendly('Faixa Etária'));
   
    return $attributes;
    
}

function getAttributesValuesFromId($db, $storeId, $productId){
    
        $sql = "SELECT attributes_values.value, attributes_values.product_id,
        attributes_values.name as attribute, 
        attributes_values.attribute_id as alias 
        FROM attributes_values WHERE attributes_values.store_id = {$storeId} 
        AND attributes_values.product_id = {$productId}";
        
        $query = $db->query($sql);
        $attributes = $query->fetchAll(PDO::FETCH_ASSOC);
    
    return $attributes;
    
}


