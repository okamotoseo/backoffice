<?php 
function getFilterAttribute($db, $storeId, $attributeId, $categoryId, $term){
    
    $sql = "SELECT * FROM ml_attributes_required WHERE store_id = ? AND category_id = ? AND attribute_id = ?";
    
    if(!empty($sql)){
        $query = $db->query($sql, array($storeId, $categoryId, $attributeId));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $arr[] = array(
                "value_id" => "{$row['value_id']}",
                "label" => $row['value'],
                "value" => $row['value']
            );
        }
        return $arr;
        
    }else{
        
        return false;
    }
    
}
function getProductFilterId($db, $storeId,  $productId, $type, $term, $limit){
	$term = strtoupper($term);
	switch ($type){
	    
	    case "id":
	        $sql = "SELECT * FROM available_products
			WHERE store_id = {$storeId}  AND id LIKE '{$term}%'
            AND id NOT IN (SELECT product_id AS id FROM product_relational WHERE store_id = {$storeId} AND product_id = {$productId} )
            ORDER BY sku LIMIT {$limit}";
	        if(empty($productId)){
	            
	            $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id LIKE '{$term}%'
                ORDER BY id LIMIT {$limit}";
	            
	            
	        }
	        
	        break;
	        
	        
		case "sku":
			$sql = "SELECT * FROM available_products
			WHERE store_id = {$storeId}   AND sku LIKE '{$term}%' 
            AND id NOT IN (SELECT product_id AS id FROM product_relational WHERE store_id = {$storeId} AND product_id = {$productId} )
            ORDER BY sku LIMIT {$limit}";
			if(empty($productId)){
			    
			     $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$term}%'
                ORDER BY sku LIMIT {$limit}";
			    
			    
			}
			
			break;
			
		case "title":
		    $sql = "SELECT * FROM available_products
			WHERE store_id = {$storeId}   AND title LIKE '{$term}%'
            AND id NOT IN (SELECT product_id AS id FROM product_relational WHERE store_id = {$storeId} AND product_id = {$productId} )
            ORDER BY sku LIMIT {$limit}";
		    if(empty($productId)){
		        
		        $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND title LIKE '{$term}%'
                ORDER BY id LIMIT {$limit}";
		        
		        
		    }
		    break;
		    
		case "reference":
		    $sql = "SELECT * FROM available_products
			WHERE store_id = {$storeId}  AND reference LIKE '{$term}%'
            AND id NOT IN (SELECT product_id AS id FROM product_relational WHERE store_id = {$storeId} AND product_id = {$productId} )
            ORDER BY sku LIMIT {$limit}";
		    if(empty($productId)){
		        
		        $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND reference LIKE '{$term}%'
                ORDER BY id LIMIT {$limit}";
		        
		        
		    }
		    break;

	}
	if(!empty($sql)){
	    $query = $db->query($sql);
	    while ($row = $query->fetch(PDO::FETCH_ASSOC)){
	        $arr[] = array(
	            "id" => "{$row['id']}",
	            "product_id" => "{$row['product_id']}",
	            "sku" => "{$row['sku']}",
	            "variation" => "{$row['variation']}",
	            "color" => "{$row['color']}",
	            "quantity" => $row['quantity'],
	            "sale_price" => number_format($row['sale_price'], 2, '.', ''),
	            "label" => trim("{$row['sku']} - {$row['title']}"),
	            "value" => trim("{$row['title']}")
	        );
	    }
	    return $arr;
	    
	}else{
	    
		return false;
	}

}

function getFilterId($db, $storeId, $type, $term, $limit){
    $term = strtoupper($term);
    switch ($type){

            
        case "color":
            $sql = "SELECT id, color as title FROM colors WHERE store_id = {$storeId} AND color LIKE '{$term}%'  ORDER BY id DESC LIMIT {$limit}";
            break;
        case "brand":
            $sql = "SELECT id, brand as title FROM brands WHERE store_id = {$storeId} AND brand LIKE '{$term}%'  ORDER BY id DESC LIMIT {$limit}";
            break;
            
    }
    if(!empty($sql)){
        $query = $db->query($sql);
        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $arr[] = array(
                "id" => "{$row['id']}",
                "label" => trim("{$row['title']}"),
                "value" => trim("{$row['title']}")
            );
        }
        return $arr;
        
    }else{
        
        return false;
    }
    
}




function getCustomerFilterCpfCnpj($db, $storeId, $type, $term, $limit){
    
    $term = strtoupper($term);
    
    switch ($type){
            
        case "cpfcnpj":
            
            $sql = "SELECT * FROM customers WHERE store_id = {$storeId} AND CPFCNPJ LIKE '{$term}%' LIMIT {$limit}";
     
            break;
            
    }
    if(!empty($sql)){
        $query = $db->query($sql);
        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
//             pre($row);die;

            $row['DataNascimento'] = dateBr($row['DataNascimento'],'/');
            $arr[] = array_merge($row, array(
                "id" => "{$row['id']}",
                "label" => trim("{$row['CPFCNPJ']} - {$row['Nome']}"),
                "value" => trim("{$row['CPFCNPJ']}")
            ));
        }
        return $arr;
        
    }else{
        
        return false;
    }
    
}

?>