<?php 

function getProductImageShopee($db, $storeId, $productId){
    
    
    $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$productId} ORDER BY id ASC";
    $query = $db->query($sql);
    $product = $query->fetch(PDO::FETCH_ASSOC);
   
    $images = getPathImageFromSku($db, $storeId, $product['sku']);
    
    $quality = 80;
    $i= 0;
    $picture_source = array();
    $pathSave = "/var/www/html/app_mvc/Modules/Shopee/img/";
    $pathShow = "https://backoffice.sysplace.com.br/Modules/Shopee/img/";
    foreach($images as $key => $filePath){
        
        $imageOutput = $filePath;
        
        $sizeOrig = floatval(filesize( $filePath ));
        if($sizeOrig > 250000){
            $dif = ceil((($sizeOrig - 250000) / $sizeOrig) * 100);
            $type = exif_imagetype($filePath);
            $partsFileName = explode('/', $filePath);
            $partsName = explode(".", end($partsFileName));
            $imageOutput = $pathShow.$product['id'].'-'.$i.'.'.end($partsName);
            $imageInput = $pathSave.$product['id'].'-'.$i.'.'.end($partsName);
            
            switch($type){
                case 2: $image = imagecreatefromjpeg($filePath); break;
                case 3: $image = imagecreatefrompng($filePath); break;
                case 1: $image = imagecreatefromgif($filePath); break;
            }
            
            $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
            imagealphablending($bg, TRUE);
            imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagedestroy($image);
            imagejpeg($bg, $imageInput, $quality);
            imagedestroy($bg);
            $newsize = floatval(filesize($imageInput));
            if($newsize > 250000){
                echo "error|A imagem é maior que o permitido -> {$newsize}";
            }
            
        }
        
        $imageOutput = str_replace("/var/www/html/app_mvc/", "https://backoffice.sysplace.com.br/", $imageOutput);
        $picture_source[$i] = $imageOutput;
        $i++;
    }
    
    return $picture_source;
}

?>