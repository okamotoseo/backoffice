<?php
error_reporting(E_ALL | E_STRICT);
require_once '../../config.php';
// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


/**
 * Torna URL amigavel SEO
 * @param string $brand
 * @return boolean
 */
function imageFileNameFriendly($title){
    $title =  utf8_encode(RemoveAcentos($title));
    $titleFriendly = strtolower($title);
    $titleFriendly = str_replace('"',"", $titleFriendly);
    $titleFriendly = str_replace("'","", $titleFriendly);
    $titleFriendly = str_replace("  "," ", $titleFriendly);
    $titleFriendly = str_replace(" "," ", $titleFriendly);
    $titleFriendly = str_replace(" ","-", $titleFriendly);
    $titleFriendly = str_replace("/","", $titleFriendly);
    $titleFriendly = str_replace(".","-", $titleFriendly);
    $titleFriendly = str_replace(",","-", $titleFriendly);
    $titleFriendly = str_replace("---","-", $titleFriendly);
    $titleFriendly = str_replace("--","-", $titleFriendly);
    return $titleFriendly;
}
function RemoveAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$string);
}

$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$title = isset($_REQUEST["title"]) && $_REQUEST["title"] != "" ? $_REQUEST["title"] : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$fileId = isset($_REQUEST["file_id"]) && $_REQUEST["file_id"] != "" ? $_REQUEST["file_id"] : null ;


$target_dir =  UP_ABSPATH."/store_id_{$storeId}/products/{$productId}/";

if (!file_exists($target_dir)) {
    @mkdir($target_dir);
}

$dir = scandir($target_dir);
$fileId = count($dir) -1;//ref . e .. files

$target_file = $target_dir . basename($_FILES["file"]["name"]);

$ext = explode(".", $_FILES["file"]["name"]);
if (isset($title)) {
    $title = imageFileNameFriendly($title.'-'.$fileId.'-'.$productId);
    
    $fileName = $title.'.'.end($ext);
    
} elseif (!empty($_FILES)) {
    
    $title = imageFileNameFriendly($_FILES["file"]["name"].'-'.$fileId.'-'.$productId);
    
    $fileName = $title.'.'.end($ext);
    
}

$filePath = $target_dir . basename($fileName);

$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if($check !== false) {
//         echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
//         echo "File is not an image.";
        $uploadOk = 0;
    }
// Check if file already exists
if (file_exists($filePath)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
// if ($_FILES["file"]["size"] > 500000) {
//     echo "Sorry, your file is too large.";
//     $uploadOk = 0;
// }
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
            die('{}');
            echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
        } else {
            die("{error:'Sorry, there was an error uploading your file.'}");
            echo "Sorry, there was an error uploading your file.";
        }
    }

 