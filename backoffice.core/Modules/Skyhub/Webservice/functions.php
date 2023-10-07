<?php




// function clearStringBreakLine($text){
    
//     $textDescription = str_replace("<br>", "\n", $textDescription);
//     $textDescription = str_replace("<br />", "\n", $textDescription);
//     $textDescription = str_replace("</p>", "</p>\n\n", $textDescription);
//     //    $description .= "\n\n";
//     $description .= "\t".$textDescription;
    
// }

function getStockPriceRelacional($db, $storeId, $productId ){
    
    $stock = '';
    $priceRel = '';
    $sqlRelational = "SELECT * FROM product_relational WHERE store_id = {$storeId} AND product_id = {$productId} ";
    $queryRelational = $db->query($sqlRelational);
    while($productRelational =  $queryRelational->fetch(PDO::FETCH_ASSOC)){
        
        $selectQtdRel = "SELECT id, sku, title, quantity, sale_price, promotion_price, blocked 
        FROM `available_products` WHERE store_id = {$storeId} AND `id` = {$productRelational['product_relational_id']}";
        $queryQtdRel = $db->query($selectQtdRel);
        $resStockPriceRel = $queryQtdRel->fetch(PDO::FETCH_ASSOC);
        if($stock == ''){
            $stock = $resStockPriceRel['quantity'];
        }else{
            
            $stock =  $stock > $resStockPriceRel['quantity'] ? $resStockPriceRel['quantity'] : $stock  ;
        }
        
        if($productRelational['dynamic_price'] == 'T'){
            
            
            $price = $resStockPriceRel['sale_price'];
            $priceRel += $price * intval($productRelational['qtd']) ;
            
        }
        
        if($productRelational['dynamic_price'] == 'F'){
             
            
            $price = $productRelational['fixed_unit_price'];
            $priceRel += $price * intval($productRelational['qtd']) ;
            
            
            
        }
        
        
    }
    
    return array("price" => $priceRel, "qty" => $stock);
}

?>