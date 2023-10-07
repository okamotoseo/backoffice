<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Soap.php';
require_once $path .'/../Models/Catalog/MediaModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

if (empty ( $action ) and empty ( $storeId ) ) {
    
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
	    
	        
	    case "import_last_products_media" :
	        
	        $mediaModel = new MediaModel($db, null, $storeId);
	            
            $sql = "SELECT id, sku FROM available_products WHERE store_id = {$storeId} AND id > 112010";
            $query = $db->query($sql);
            $products = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($products as $key => $value){
                pre($value);
                $sqlVerify = "SELECT product_id, sku FROM module_onbi_products_tmp 
                WHERE store_id = {$storeId} AND sku LIKE '{$value['sku']}'";
                $verifyQuery = $db->query($sqlVerify);
                $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
                
                
//                 $target_dir =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$value['id']}/-{$value['id']}.";
                
//                 if (file_exists($target_dir)) {
//                     pre($value);
//                     echo $target_dir;
//                     echo "<br>";
//                     unlink($target_dir);
//                 }
                
                $target_dir =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$value['id']}/";
                
                if (!file_exists($target_dir)) {
                    @mkdir($target_dir);
                }
                
                
                $mediaModel->product_id = $verify['product_id'];
                $medias = $mediaModel->catalogProductAttributeMediaList();
                foreach($medias as $k => $media){
                    if($media->exclude == 0){
                        
                        $fileName = basename($media->url);
                        
                        $ext = explode(".", $fileName);
                        
                        if (isset($ext[0])) {
                            
                            $title = imageFileNameFriendly($ext[0].'-'.$media->position.'-'.$value['id']);
                            
                            $fileName = $title.'.'.end($ext);
                            
                            echo $filePath = $target_dir . basename($fileName);
                            echo "<br>";
                            if(!file_exists($filePath)){
                                $imageDowloaded = file_get_contents($media->url);
                                $res =  file_put_contents($filePath, $imageDowloaded);
                                
                            }
                            
                        }
                        
                        
                    }
                    
                    
                }
            }
            echo "reload|{$filePath}";
	        
	        break;
	        
	    case "import_products_media" :
	        
	        $mediaModel = new MediaModel($db, null, $storeId);
	        
	        $productIds = is_array($productId) ? $productId : array($productId) ;
	        
	        foreach($productIds as $i => $id){
	            
    	        $sql = "SELECT id, sku FROM available_products WHERE store_id = {$storeId} AND id = {$id}";
    	        $query = $db->query($sql);
    	        $products = $query->fetchAll(PDO::FETCH_ASSOC);
    	        foreach($products as $key => $value){
    	            
    	            $sqlVerify = "SELECT product_id, sku FROM module_onbi_products_tmp WHERE store_id = {$storeId} AND sku LIKE '{$value['sku']}'";
    	            $verifyQuery = $db->query($sqlVerify);
    	            $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
    	            
    	            $target_dir =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$value['id']}/";
    	            
    	            if (!file_exists($target_dir)) {
    	                @mkdir($target_dir);
    	            }
    	            
    	            
    	            $mediaModel->product_id = $verify['product_id'];
    	            $medias = $mediaModel->catalogProductAttributeMediaList();
    	 
    	            foreach($medias as $k => $media){
    	                if($media->exclude == 0){
        	                
        	                $fileName = basename($media->url);
        	                
        	                $ext = explode(".", $fileName);
        	                
        	                if (isset($ext[0])) {
        	                    
        	                    $title = imageFileNameFriendly($ext[0].'-'.$media->position.'-'.$value['id']);
        	                    
        	                    $fileName = $title.'.'.end($ext);
        	                    
        	                }
        	                
        	                $filePath = $target_dir . basename($fileName);
        	                if(!file_exists($filePath)){
            	                $imageDowloaded = file_get_contents($media->url);
            	                file_put_contents($filePath, $imageDowloaded);
        	                }
    	                }
    	                
    	                
    	            }
    	            pre($medias);
    	        }
    	        echo "reload|{$filePath}";
	        }
	        
	        break;
	        
	    case "import_products_media_ids" :
	        
	        $mediaModel = new MediaModel($db, null, $storeId);
	        
	        $productId = array(12917, 12946, 12970, 13008, 13009, 13010, 13011, 13012, 13013, 13014, 13015, 13016, 13026, 13027, 13029, 13030, 13034, 13035, 13036, 13037, 13038, 13039, 13040, 13041);
	        
	        $productIds = is_array($productId) ? $productId : array($productId) ;
	        
	        foreach($productIds as $i => $id){
	            
	            $sql = "SELECT id, sku FROM available_products WHERE store_id = {$storeId} AND reference LIKE '{$id}'";
	            $query = $db->query($sql);
	            $products = $query->fetchAll(PDO::FETCH_ASSOC);
	            foreach($products as $key => $value){
	                $sqlVerify = "SELECT product_id, sku FROM module_onbi_products_tmp WHERE store_id = {$storeId} AND sku LIKE '{$value['sku']}'";
	                $verifyQuery = $db->query($sqlVerify);
	                $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	                
	                $target_dir =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$value['id']}/";
	                
	                if (!file_exists($target_dir)) {
	                    @mkdir($target_dir);
	                }
	                
	                
	                $mediaModel->product_id = $verify['product_id'];
	                $medias = $mediaModel->catalogProductAttributeMediaList();
	                
	                foreach($medias as $k => $media){
	                    if($media->exclude == 0){
	                        
	                        $fileName = basename($media->url);
	                        
	                        $ext = explode(".", $fileName);
	                        
	                        if (isset($ext[0])) {
	                            
	                            $title = imageFileNameFriendly($ext[0].'-'.$media->position.'-'.$value['id']);
	                            
	                            $fileName = $title.'.'.end($ext);
	                            
	                        }
	                        
	                        $filePath = $target_dir . basename($fileName);
	                        
	                        if(!file_exists($filePath)){
	                            $imageDowloaded = file_get_contents($media->url);
	                            file_put_contents($filePath, $imageDowloaded);
	                        }
	                    }
	                    
	                    
	                }
	                //     	            pre($medias);
	            }
	            echo "reload|{$filePath}";
	        }
	        
	        
	        
	        
	        break;
	    
	}
	
}

