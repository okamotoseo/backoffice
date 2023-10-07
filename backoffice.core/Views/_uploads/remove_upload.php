<?php
require_once '../../config.php';

// print_r($_POST);die;
$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$fileName = isset($_REQUEST["key"]) && $_REQUEST["key"] != "" ? $_REQUEST["key"] : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$new = isset($_REQUEST["new_"]) && $_REQUEST["new_"] != "" ? $_REQUEST["new_"] : null ;

if(isset($storeId)){

	switch($action){
		case 'remove_image_product': 
			$productImagePath = ABSPATH . "/Views/_uploads/store_id_{$storeId}/products/{$productId}/{$fileName}";
			if(file_exists($productImagePath)){
				shell_exec("rm -rf \"{$productImagePath}\" ");
				echo "success|";
				
				
// 				if(unlink($productImagePath)){
// 					clearstatcache();
// 					if(isset($new)){
// 						echo "success|{$fileName}";die;
// 					}
// 					die('{}');
// 				}else {
// 					echo 'error|Erro ao excluír imagem!'; 
// 				}
				
			}
			break;
		
	}
	
}

?>