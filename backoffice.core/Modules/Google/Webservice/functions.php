<?php 

function generateCategoriesGenderAge($gender, $ageGroup, $product_type){
    
    switch($gender){
        case "female":
            if( $ageGroup == 'kids'){
                $categoryRoot = "Infantil Menina";
            }else{
                $categoryRoot = "Femininos";
            }
            break;
            
        case "male":
            if($ageGroup == 'kids'){
                $categoryRoot = "Infantil Menino";
            }else{
                $categoryRoot = "Masculinos";
            }
            break;
            
        default: $categoryRoot = ""; break;
    }
    
    $category = $product_type;
    
    if(!empty($categoryRoot)){
        if(!empty($product_type)){
            $category = "{$categoryRoot} > {$product_type}" ;
        }else{
            $category = $categoryRoot;
        }
    }
    
    return $category;
}




?>