<?php
set_time_limit ( 30000 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Models/Products/AttributesValuesModel.php';
require_once $path .'/../Models/Products/ProductDescriptionModel.php';
require_once $path .'/functions.php';
require_once '../../../Views/_uploads/images.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
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
    
    $request = "System";
    
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
    $moduleConfig = getModuleConfig($db, $storeId, 8);
    switch($action){
            
        case "import_images_xml":
            $count = 0;
//             $xml = "/var/www/html/app_mvc/Modules/Google/xml/criteo";
//             $xml = "/var/www/html/app_mvc/Modules/Google/xml/newXml/teste.xml";
//         	$xml = "/var/www/html/app_mvc/Modules/Google/xml/newXml/googleshopping-parent.xml";
//         	$xml = "/var/www/html/app_mvc/Modules/Google/xml/newXml/googleshopping-parent-3.xml";
//         	$xml = "/var/www/html/app_mvc/Modules/Google/xml/newXml/googleshopping-sku-2.xml";
            $xml = "/var/www/html/app_mvc/Modules/Google/xml/image/googleshopping.xml";
        	
        	

            $rss = simplexml_load_file ($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
//             pre($rss->channel);
            foreach ($rss->channel->item as $key => $entry){
            	$namespaces = $entry->getNameSpaces(true);
            	$tag = $entry->children($namespaces['g']);
                $parentId = trim($tag->id."");
                //$parentId = $tag->id ; trim(substr($tag->id."",0, -2));
                $sqlVerify = "SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}' ORDER BY variation ASC";
//                 $sqlVerify = "SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}' ORDER BY variation ASC";
                $verifyQuery = $db->query($sqlVerify);
                $products = $verifyQuery->fetchAll(PDO::FETCH_ASSOC);
                
                if(isset($products[0]['id'])){
                	
                	$exist = false;
                    $title = "".$tag->title;
                    $productId = $products[0]['id'];
                    $target_dir =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$productId}/";
                    
                    foreach($products as $i => $product){
                    	
                    	if(is_dir("/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$product['id']}/")){
                    		$productId = $product['id'];
                    		$target_dir =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$product['id']}/";
                    		$exist = true; break;
                    	}
                    }
                    
                    
                    if (!$exist) {
                    	echo "novo";
//                     	pre($tag);
                        @mkdir($target_dir);
                    }
                    
                    $dir = scandir($target_dir);
                    
                    $fileId = count($dir) -1;//ref . e .. files
                    
                    if($fileId  > 1){
                    	
//                     	$urls = getUrlImageFromId($db, $storeId, $productId);
//                     	pre($urls);
                    	
                    }else{
                    	pre($tag);
                    	pre(json_encode(array(
                    			'id' => $tag->id."",
                    			'title' => $tag->title."",
                    			'color' => $tag->color."",
                    			'size' => $tag->size."",
                    			'gender' => $tag->gender."",
                    			'color' => $tag->color.""
                    			
                    	), JSON_PRETTY_PRINT));
                    	
                    	$urlImages = array();
                    	foreach($tag->additional_image_link as $k => $url){
                    	    $urlImages[] = $url."";
                    	}
                    	array_unshift($urlImages, $tag->image_link."");
                    	pre($urlImages);
                    	
                    	$maxWidth = '1000';
                    	$maxHeight = null;//mantem as proporcoes
                    	foreach($urlImages as $k => $imageUrl){
                    		
                    		echo "<img src='{$imageUrl}' width='200px' />";
                    		
                    		$ext = explode(".", $imageUrl);
                    		
                    		$title = imageFileNameFriendly($tag->title.'-'.$fileId.'-'.$productId);
                    		
//                     		$fileName = time().'-'.$title.'.'.end($ext);

                    		$fileName = $title.'.'.end($ext);
                    		
                    		$output = $target_dir.basename($fileName);
//                     		pre($imageUrl);
//                     		$res = createImage($imageUrl, $output, '900', null);
                    		shell_exec("wget -q \"{$imageUrl}\" -O {$output}");
//                     		pre($res);
                    		$fileId++;
                    	
                    	}
                    	$count++;
//                     	die;
//                     	if($count == 10 ){
//                     		echo "1000";die;
//                     	}
                    	echo '<br><br>';
                    	
                    }
                    
                }
                
            }
            
            break;
            
    }
    
}

