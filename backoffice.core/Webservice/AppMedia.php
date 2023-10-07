<?php
header("Content-Type: text/html; charset=utf-8");
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/../Models/Products/PublicationsModel.php';
require_once $path .'/../Views/_uploads/images.php';
require_once $path .'/functions.php';


$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

if (empty ( $action ) and empty ( $storeId )) {
	if(isset($_SERVER ['argv'] [1])){
		$paramAction = explode ( "=", $_SERVER ['argv'] [1] );
		$action = $paramAction [0] == "action" ? $paramAction [1] : null;
	}
	if(isset($_SERVER ['argv'] [2])){
		$paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
		$storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
	}
	if(isset($_SERVER ['argv'] [3])){
		$paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
		$accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
	}
	
	$request = "System";
}
if(isset($storeId)){
    
    $db = new DbConnection();
    
	switch($action){
		
		case 'list_images':
			
			
			if(isset($_REQUEST['product_id'])){
				$images = getUrlImageFromId($db, $storeId, $_REQUEST['product_id']);
			}
			if(isset($_REQUEST['sku'])){
				$images = getUrlImageFromSku($db, $storeId, $_REQUEST['sku']);
			}
			if(isset($_REQUEST['parent_id'])){
// 				$images = getPathImageFromParentId($db, $storeId, $_REQUEST['parent_id']);
				$images = getUrlImageFromParentId($db, $storeId, $_REQUEST['parent_id']);
			}
			
			$url = "https://backoffice.sysplace.com.br/Views/_uploads/";
			
			$path = "/var/www/html/app_mvc/Views/_uploads/";
			
			echo "<table><tr>";
			
			foreach($images as $k => $imageUrl){
				
				$imagePath = str_replace($url, $path,  $imageUrl);
				
				list($width, $height, $type, $attr) = getimagesize($imagePath);
				$size = sizeFilter(filesize($imagePath));
				
				
				echo "<td>
						<a href='{$imageUrl}' target='_blank'><img src='{$imageUrl}' width='250px' /></a>
						{$size} x {$width} x {$height}
					</td>";
			}
			
			echo "</tr></table>";
			break;
			
			
	}
	
	
	
}