<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);

require_once $path .'/../../Class/class-DbConnection.php';
require_once $path .'/../../Functions/global-functions.php';
require_once './images.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$produtId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? intval($_REQUEST["product_id"]) : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;

if (empty ( $action ) and empty ( $storeId )) {
    
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
    
    
    switch($action){
    	
    	
    	case "update_image_thumbnail":
    		
    		$sql = "SELECT * FROM available_products WHERE store_id = {$storeId} ORDER BY id DESC";
    		if(!empty($produtId)){
    			$sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$produtId}";
    		}
//     		pre($sql);die;
    		$query = $db->query($sql);
    		while($res =  $query->fetch(PDO::FETCH_ASSOC)){
    			$count = 1;
    			$pathShow = "/Views/_uploads/store_id_{$storeId}/products/{$res['id']}";
    			$pathShowThumb = "/Views/_uploads/store_id_{$storeId}/thumbnail/{$res['id']}";
    			$pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$res['id']}";
    			
    			if(file_exists($pathRead)){
    				$picturesArray = array();
    				$iterator = new DirectoryIterator($pathRead);
    				foreach ( $iterator as $key => $entry ) {
    					$file = $entry->getFilename();
//     					pre($file);
    					if($file != '.' AND $file != '..'){
    						$count++;
    						$fileSize = $entry->getSize();
    						$parts = explode("-", $file);
    						$array = array_slice($parts, -2);
    		    	
    						$picturesArray[$array[0]] = array(
    								'source' =>  $pathShow.'/'.$file,
    								"file_size" => $fileSize,
    								"path_show" => $pathShow,
    								"file" => $file
    						);
    					}
    				}
    				ksort($picturesArray);
//     				pre($picturesArray);
    				foreach ($picturesArray as $key => $pics) {
    					if(!empty($pics['file'])){
							$width = '160';
	    					$ext = explode(".", $pics['file']);
	    					$fileNameThumbnail = "thumbnail_{$width}_".$res['id'].'.'.end($ext);
	    					$target_dir_thumb =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/thumbnail/{$res['id']}/";
	    					$filePathThumbnail = $target_dir_thumb . basename($fileNameThumbnail);
	    					if (!file_exists($target_dir_thumb)) {
	    						@mkdir($target_dir_thumb);
	    					}
	    					$filePath = $pathRead."/" . basename($pics['file']);
	    					
	    					$thumbnailImage = createThumbnail($filePath, $filePathThumbnail, $width);
	    					
	    		    		$queryAP = $db->update('available_products',
	    		    				array('store_id', 'id'),
	    		    				array($storeId, $res['id']), 
	    		    				array('thumbnail' => "{$pathShowThumb}/{$fileNameThumbnail}", 'image' => $pics['source']
	    		    				));
	    		    		
	    		    		if(!$queryAP){
	    		    			pre($queryAP);
	    		    		}
	    		    		
	    					echo "<img src='{$pathShowThumb}/{$fileNameThumbnail}' alt='Product Image' ><br>";
	    					break;
    					}
    		    
    				}
    			
    			}
    			
    			
    		}
    		break;
    		
    	
    	case "list_image":
    		
    		$sql = "SELECT id, sku, thumbnail, image, title FROM available_products WHERE store_id = {$storeId}";
    		if(!empty($produtId)){
    			$sql = "SELECT * FROM available_products store_id = {$storeId} AND id = {$produtId}";
    		}
    		$query = $db->query($sql);
    		while($res =  $query->fetch(PDO::FETCH_ASSOC)){
    			$count = 1;
    			$pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$res['id']}";
	    		$pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$res['id']}";
	    		if(file_exists($pathRead)){
	    			pre($res);
	    			$picturesArray = array();
	    			$iterator = new DirectoryIterator($pathRead);
	    			foreach ( $iterator as $key => $entry ) {
	    				$file = $entry->getFilename();
	    				if($file != '.' AND $file != '..'){
	    					$count++;
	    					$fileSize = $entry->getSize();
	    					$parts = explode("-", $file);
	    					$array = array_slice($parts, -2);
	    					 
	    					$picturesArray[$array[0]] = array(
	    							'source' =>  $pathShow.'/'.$file,
	    							"file_size" => $fileSize,
	    							"path_show" => $pathShow,
	    							"file" => $file
	    					);
	    				}
	    			}
	    			ksort($picturesArray);
	    			pre($picturesArray);
	    			 
	    			foreach ($picturesArray as $key => $pics) {
	    				
	    				echo $pics['file']."<br>";
	    				
	    				echo "\t\t<img src='{$pics['path_show']}/{$pics['file']}' alt='Product Image' width='50px'><br>";
	    				
	    				
	    			}
	    			
	    		}
    		}
    		break;
        
        
        case "sort_image":
            
            $newIndex = $_REQUEST['new_index'];
            $oldIndex = $_REQUEST['old_index'];
            $pics = array();
            $pathShow = $uri . "/Views/_uploads/store_id_{$storeId}/products/{$produtId}";
            $pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$produtId}";
            if(file_exists($pathRead)){
                $iterator = new DirectoryIterator($pathRead);
                foreach ( $iterator as $key => $entry ) {
                    $file = $entry->getFilename();
                    if($file != '.' AND $file != '..'){
                        $fileSize = $entry->getSize();
                        $parts = explode("-", $file);
                        $array = array_slice($parts, -2);
                        $ind = trim($array[0]);
                        $id = trim(end($array));
                        $partsTitle = explode("-{$ind}-", $file);
                        $picturesArray[$array[0]] = array(
                            'source' =>  $pathShow.'/'.$file,
                            "file_size" => $fileSize,
                            "path_show" => $pathShow,
                            "file" => $file,
                            "title" => $partsTitle[0],
                            "ind" => $ind,
                            "id" => $id
                        );
                    }
                }
                ksort($picturesArray);
                foreach ($picturesArray as $key => $pic) {
                    $pics[] = $pic;
                }
            }
            echo "old:{$oldIndex} - new:{$newIndex}\n";
            
            if($oldIndex >= $newIndex){
                
                $ind = $newIndex;
                
                do{
                    echo $ind."\n";
                    $pics[$ind];
                    pre($pics[$ind]);
                    $ind++;
                    
                }while(!empty($pics[$ind]));
                
            }
            
            
            break;
            
            
    }
}


?>