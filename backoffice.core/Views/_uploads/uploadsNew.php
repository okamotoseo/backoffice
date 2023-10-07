<?php
error_reporting(E_ALL | E_STRICT);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, cachehack=".time());
header("Cache-Control: no-store, must-revalidate");
header("Cache-Control: post-check=-1, pre-check=-1", false);
header("Content-Type: text/html; charset=utf-8");
clearstatcache();
require_once '../../config.php';
require_once '../../Class/class-DbConnection.php';
require_once './images.php';

function pre($array){

	echo "<pre>";
	print_r($array);
	echo "</pre>";

}
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$position = isset($_REQUEST["position"]) && $_REQUEST["position"] != "" ? $_REQUEST["position"] : 0 ;

	$target_dir =  UP_ABSPATH."/store_id_{$storeId}/products/{$productId}/";
	$target_dir_thumb =  UP_ABSPATH."/store_id_{$storeId}/thumbnail/{$productId}/";
	$target_dir_video =  UP_ABSPATH."/store_id_{$storeId}/video/{$productId}/";
	
	if(!isset($productId)){
		echo "error|Imagem sem Id do Produto";
		exit;
	}
	
	$db = new DbConnection();
	$query = $db->query("SELECT title FROM available_products WHERE store_id = {$storeId} AND id = {$productId}");
	$res = $query->fetch(PDO::FETCH_ASSOC);
	$title = trim($res['title']);
	
	$fileId = $position;
	
	if (isset($title)) {
		$name = $title;
// 	    $title = imageFileNameFriendly($title.'-'.mt_rand(1000, 9999).'-'.$fileId.'-'.$productId);
	    $title = imageFileNameFriendly($title.'-'.$fileId.'-'.$productId);
	} else{
	    $name = $_FILES["file"]["name"];
// 	    $title = imageFileNameFriendly($_FILES["file"]["name"].'-'.mt_rand(1000, 9999).'-'.$fileId.'-'.$productId);
	    $title = imageFileNameFriendly($_FILES["file"]["name"].'-'.$fileId.'-'.$productId);
	}
	
	if (!file_exists($target_dir)) {
		@mkdir($target_dir);
	}
	
	$target_file = $target_dir . basename($_FILES["file"]["name"]);
	$ext = explode(".", $_FILES["file"]["name"]);
// 	$fileName = mt_rand() . $title.'.'.end($ext);
	$fileName =  $title.'.'.end($ext);
	$filePath = $target_dir . basename($fileName);
	
	if(file_exists($filePath)){
		echo "error|Já existe uma imagem nessa posição... {$filePath}";exit;
	}
	
	$fileShow = "/Views/_uploads/store_id_{$storeId}/products/{$productId}/". basename($fileName);
	$uploadOk = 1;
	    
	if (file_exists($filePath)) {
	    echo "Sorry, file already exists.";exit;
	}
	
	
	if (($_FILES["file"]["type"] == "video/mp4")
			
			|| ($_FILES["file"]["type"] == "image/pjpeg")
			|| ($_FILES["file"]["type"] == "image/gif")
			|| ($_FILES["file"]["type"] == "image/jpeg")
			|| ($_FILES["file"]["type"] == "image/png")
			|| ($_FILES["file"]["type"] == "video/webm")
			|| ($_FILES["file"]["type"] == "video/mp4")
			|| ($_FILES["file"]["type"] == "video/wma")
			&& ($_FILES["file"]["size"] < 20000000)){
		
				if ($uploadOk == 0) {
					
					echo "Sorry, your file was not uploaded.";
	
				} else {
					
					$video = false;
					
					if (($_FILES["file"]["type"] == "video/mp4")
							|| ($_FILES["file"]["type"] == "video/webm")
							|| ($_FILES["file"]["type"] == "video/wma")){
						
							$video = true;
							
							$fileNameVideo = "video_".$productId.'.'.end($ext);
							$fileShow = "/Views/_uploads/store_id_{$storeId}/video/{$productId}/". basename($fileNameVideo);
							$filePath = $target_dir_video . basename($fileNameVideo);
							
							if (!file_exists($target_dir_video)) {
								@mkdir($target_dir_video);
							}
									
					}
// 					if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
						
						if (!$video){
							
							createImage($_FILES["file"]["tmp_name"], $filePath, '1000');
						
							if($fileId == 1){
								
								if (!file_exists($target_dir_thumb)) {
									@mkdir($target_dir_thumb);
								}
								
								$width = '160';
								
								$fileNameThumbnail = "thumbnail_{$width}_".$productId.'.'.end($ext);
								
								$filePathThumbnail = $target_dir_thumb . basename($fileNameThumbnail);
								
								if (file_exists($filePathThumbnail)) {
									shell_exec("rm -rf \"{$filePathThumbnail}\" ");
									unlink($filePathThumbnail);
									clearstatcache();
									
								}
								
								$thumbnailImage = createThumbnail($_FILES["file"]["tmp_name"], $filePathThumbnail, $width);
								
								if($thumbnailImage){
									
									$urlThumbnail = "/Views/_uploads/store_id_{$storeId}/thumbnail/{$productId}/{$fileNameThumbnail}";
									$sql = "UPDATE available_products SET thumbnail = '{$urlThumbnail}', image = '{$fileShow}'
									WHERE store_id = {$storeId} AND id = {$productId}";
									$queryRes = $db->query($sql);
								}
								
							}
							
						}else{
							
							if ($video){
								
								$sql = "UPDATE available_products SET video = '{$fileShow}' WHERE store_id = {$storeId} AND id = {$productId}";
								$queryRes = $db->query($sql);
								
							}
							
						}
						
						$size = sizeFilter(filesize($filePath));
						list($width, $height, $type, $attr) = getimagesize($filePath);
						$res = '';
						
						if($video){
							
							$res .= "<li class='item'>
								<div class='product-img''>
									<video width='50' height='50'  controls>
										<source src='{$fileShow}' type='{$fileType}'>
									</video>
								</div>
								<div class='product-info'>
									<a href='{$fileShow}' class='product-title' target='_blank'>{$fileName}</a>
									<a type='button' onclick=\"javascript:removeProductImage(this, '".HOME_URI."', '{$productId}', '{$fileName}');\" class='btn btn-xs pull-right' title='Excluir'><i class='fa fa-trash'></i></a>&nbsp;
									<a type='button' href='{$fileShow}' download class='btn btn-xs pull-right' title='View Details'><i class='fa fa-download'></i></a>
									<span class='product-description small'>{$size}</span>
								</div>
							</li>";
							
						}else{
							
							$res .= "<li class='item'>
								<div class='product-img'>
									<img src='{$fileShow}' alt='Product Image' >
								</div>
								<div class='product-info'>
									<a href='{$fileShow}' class='product-title' target='_blank'>{$fileName}</a>
									<a type='button' onclick=\"javascript:removeProductImage(this, '".HOME_URI."', '{$productId}', '{$fileName}');\" class='btn btn-xs pull-right' title='View Details'><i class='fa fa-trash'></i></a>&nbsp;
									<a type='button' href='{$fileShow}' download class='btn btn-xs pull-right' title='View Details'><i class='fa fa-download'></i></a>
									<span class='product-description small'>{$width} X {$height} - {$size}</span>
								</div>
							</li>";
						}
						
						
						echo json_encode(array('item' => $res));exit;
// 					} else {
// 						die("{error:'Sorry, there was an error uploading your file.'}");
// 					}
				}
	
	}else{
		echo "Sorry, only JPG, JPEG, PNG, GIF, MP3, MP4, WMA files are allowed.";exit;
	}
	
	

function sizeFilter( $bytes )
{
	$label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
	for( $i = 0; $bytes >= 1024 && $i < ( count( $label ) -1 ); $bytes /= 1024, $i++ );
	return( round( $bytes, 2 ) . " " . $label[$i] );
}



/**
 * Torna URL amigavel SEO
 * @param string $brand
 * @return boolean
 */
function imageFileNameFriendly($title){
	$title =  RemoveAcentos($title);
	$titleFriendly = strtolower($title);
	$titleFriendly = str_replace(' – '," - ", $titleFriendly);
	$titleFriendly = str_replace('º',"", $titleFriendly);
	$titleFriendly = str_replace('%',"", $titleFriendly);
	$titleFriendly = str_replace('&',"", $titleFriendly);
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
	$titleFriendly = strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $titleFriendly));

	return $titleFriendly;
}
function RemoveAcentos($str){
	$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
	$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	return str_replace($a, $b, $str);
}

?>