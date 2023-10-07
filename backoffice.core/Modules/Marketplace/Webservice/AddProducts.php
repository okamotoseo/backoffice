<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../../../Models/Products/PublicationsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;
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
    
    $sqlStore = "SELECT * FROM stores WHERE id = {$storeId}";
    $queryStore = $db->query($sqlStore);
    $storeInfo = $queryStore->fetch(PDO::FETCH_ASSOC);
    
    switch($action){
    	
    	case 'add_all_available_products':
    		$errorLog = array();
    		$publicationsModel = new PublicationsModel($db);
    		if(!isset($productId)){
    			
//     			if(isset($_REQUEST['all'])){
	    			$sql = "SELECT id FROM available_products WHERE store_id = {$storeId} AND blocked = 'F' AND quantity > 0
	    			AND parent_id IS NOT NULL AND parent_id > 1  AND EAN != '' AND EAN IS NOT NULL
	    			AND id NOT IN (SELECT product_id as id FROM product_relational WHERE store_id = {$storeId}) ";
	    			if($storeId == 6){
	    				$sql = "SELECT id FROM available_products WHERE store_id = {$storeId} AND blocked = 'F' AND quantity > 0
	    				AND color  != '' AND parent_id > 1  AND EAN != '' AND EAN IS NOT NULL AND thumbnail != '' GROUP BY parent_id";
	    			}
	    			if($storeId == 4){
	    				$sql = "SELECT id FROM available_products WHERE store_id = {$storeId} AND blocked = 'F' AND quantity > 0
	    				AND color  != '' AND parent_id > 1  AND EAN != '' AND EAN IS NOT NULL ";
	    			}
	    			if($storeId == 3){
// 	    				$sql = "SELECT id, parent_id FROM available_products WHERE store_id = {$storeId} AND blocked = 'F' AND quantity > 0
// 	    				AND color  != '' AND parent_id > 1  AND   EAN != '' AND category != '' AND  collection IN ('V8', 'V9', 'V20', 'V21') GROUP BY parent_id  LIMIT 50 ";
	    				$sql = "SELECT id, parent_id FROM available_products WHERE store_id = {$storeId} AND blocked = 'F' AND quantity > 0
	    				AND color  != '' AND parent_id > 1  AND  category != '' AND  collection IN ('V8', 'V9', 'V20', 'V21') GROUP BY parent_id";
	    			}
	    			$queryAll = $db->query($sql);
	    			$resAll = $queryAll->fetchAll(PDO::FETCH_ASSOC);
	    			foreach($resAll as $a => $ids){
	    				
	    				$productId[] = $ids['id'];
	    			}
    			
//     			}
    			
    		}
    		$productIds = is_array($productId) ? $productId : array($productId) ;
    		pre($productIds);
    		foreach($productIds as $i => $productId){
    			
    			
    			$sqlParent = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
    			$queryParent = $db->query($sqlParent);
    			$parentIds = $queryParent->fetchAll(PDO::FETCH_ASSOC);
    			
    			$updateError = $insertError = $updated = $inserted = 0;
    			
    			foreach($parentIds as $j => $parentId){
    				
    				

    				$sqlParent = "SELECT * FROM available_products WHERE store_id = {$storeId} 
    				AND parent_id LIKE '{$parentId['parent_id']}' AND quantity > 0 ";
	    			$queryAP = $db->query($sqlParent);
	    			$products = $queryAP->fetchAll(PDO::FETCH_ASSOC);
	    			
	    			foreach($products as $key => $product){

	    				if(empty(trim($product['ean']))){
	    					
	    					$product['ean'] = $product['id'];
// 	    					echo "error|produto sem EAN";
// 	    					continue;
	    				
	    				}
	    				
	    				$weight = !empty($product['weight']) ? $product['weight'] : '1.000' ;
	    				
	    				$height =  !empty($product['height']) ? $product['height'] : '20' ;
	    				
	    				$width = !empty($product['width']) ? $product['width'] : '20' ;
	    				
	    				$length =  !empty($product['length']) ? $product['length'] : '20' ;
	    				
	    				$images = getTotalImagesFromParent($db, $storeId, $product['parent_id']);
	    			
	    				$storeName = $storeInfo['store'];
	    				
	    				$sqlVerify = "SELECT  module_marketplace_products.*  
	    				FROM  module_marketplace_products WHERE module_marketplace_products.seller_store_id = ?
	    				AND module_marketplace_products.seller_sku LIKE ?"; 
	    				
	    				$queryVerify = $db->query($sqlVerify, array($storeId, $product['sku']));
	    				$ProductVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);

	    				if ( isset($ProductVerify['id'] ) ) {
	    					
	    					$data = array(
	    							'seller_store' => $storeName,
	    							'seller_product_id' => $product['id'],
	    							'seller_sku' => $product['sku'],
	    							'seller_parent_id' => $product['parent_id'],
	    							'seller_title' => defaultTextPattern($product['title']),
	    							'seller_color' => defaultTextPattern($product['color']),
	    							'seller_brand' => defaultTextPattern($product['brand']),
	    							'seller_variation_type' => $product['variation_type'],
	    							'seller_variation' => $product['variation'],
	    							'seller_reference' => $product['reference'],
	    							'seller_collection' => $product['collection'],
	    							'seller_category' => $product['category'],
	    							'seller_quantity' => $product['quantity'],
	    							'seller_sale_price' => $product['sale_price'],
	    							'seller_weight' => $weight,
	    							'seller_height' => $height,
	    							'seller_width' => $width,
	    							'seller_length' => $length,
	    							'seller_ean' => !empty($product['ean']) ? $product['ean'] : $product['id'],
	    							'seller_description' => $product['description']
	    					);
	    					
	    					$query = $db->update('module_marketplace_products', 
	    							array('seller_store_id', 'id'), 
	    							array($storeId, $ProductVerify['id']), 
	    							$data
	    							);
	    					 
	    					if ( ! $query ) {
	    						$updateError++;
	    					} else {
	    			
	    						if($query->rowCount()){
	    								
	    							$db->update('module_marketplace_products',
	    									array('seller_store_id','id'),
	    									array($storeId, $ProductVerify['id']),
	    									array('updated' => date("Y-m-d H:i:s"))
	    									);
	    								
	    							unset($data['description']);
	    							unset($verify['description']);
	    							$dataLog['update_module_marketplace_products'] = array(
	    									'before' => $verify,
	    									'after' => $data
	    							);
	    							$db->insert('products_log', array(
	    									'store_id' => $storeId,
	    									'product_id' => $ProductVerify['id'],
	    									'description' => "Atualização De Informação do Produto do Marketplace Sysplace",
	    									'user' => $request,
	    									'created' => date('Y-m-d H:i:s'),
	    									'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	    							));
	    							$updated++;
	    								
	    						}
	    					}
	    			
	    			
	    				} else {
	    					
	    					$disabled = false;
	    					
	    					
	    					$sqlKit = "SELECT product_id FROM product_relational 
	    					WHERE store_id = {$storeId} AND product_id = {$product['id']}";
	    					$queryKit = $db->query($sqlKit);
	    					$resKit = $queryKit->fetch(PDO::FETCH_ASSOC);
	    					
	    					if(isset($resKit['product_id'])){
	    						$disabled = true;
	    						$errorLog[$product['sku']][] = "Produtos Tipo Kit não estnao disponíveis....";
	    					}
	    					
	    					if($images < 1 ){
	    						$errorLog[$product['sku']][] = "Produto {$product['sku']} sem foto...";
	    						$disabled = true;
	    					}
	    					if($storeId == 4){
	    						
	    						if($product['sale_price'] <= 14){
	    							$disabled = true;
	    							$errorLog[$product['sku']][] = "{$product['sale_price']} abaixo do valor permitido...";
	    						
	    						}
	    						
	    						
	    					}
	    					
	    					if($storeId == 3){
	    						
	    						$sqlParentQtd = "SELECT count(id) as total FROM available_products
	    						WHERE store_id = {$storeId} AND parent_id LIKE '{$product['parent_id']}' AND quantity > 0";
	    						$queryParentQtd = $db->query($sqlParentQtd);
	    						$parentQtd = $queryParentQtd->fetch(PDO::FETCH_ASSOC);
	    						
	    						if($parentQtd['total'] < 3 && $product['variation_type'] != 'unidade' && is_number($product['variation'])){
	    							$disabled = true;
	    							$errorLog[$product['sku']][] = "Departamento exige 3 variações disponísveis para inserção, esse produto possui apenas {$parentQtd['total']}...";
	    						}
	    							
	    						
	    						$parts = explode('>', $product['category']);
	    						
	    						if(count($parts) == 1 ){
	    							$disabled = true;
	    							$errorLog[$product['sku']][] = "Categoria {$product['category']} não possui subcategorias de espcificação...";
	    							
	    							
	    						}else{
	    						
	    							$subcategory = strtolower(trim(end($parts)));
	    							
	    							switch($subcategory){
	    								
	    								case 'chinelo': 
	    									$disabled = true;
	    									$errorLog[$product['sku']][] = "{$subcategory} Categoria não permitida...";
	    									break;
	    								case 'chinelos': 
	    									$disabled = true;
	    									$errorLog[$product['sku']][] = "{$subcategory} Categoria não permitida...";
	    									
	    									break;
	    								
	    							}
	    						
	    						}
	    						
	    						if($product['sale_price'] <= 50){
	    							$disabled = true;
	    							$errorLog[$product['sku']][] = "{$product['sale_price']} abaixo do valor permitido...";
	    							
	    						}
	    							
	    					}
	    					
	    					if($disabled == false){
	    						
		    					$data = array(
		    							'seller_store_id' => $storeId,
		    							'seller_store' => $storeName,
		    							'seller_product_id' => $product['id'],
		    							'seller_sku' => $product['sku'],
		    							'seller_parent_id' => $product['parent_id'],
		    							'seller_title' => defaultTextPattern($product['title']),
		    							'seller_color' => defaultTextPattern($product['color']),
		    							'seller_brand' => defaultTextPattern($product['brand']),
		    							'seller_variation_type' => $product['variation_type'],
		    							'seller_variation' => $product['variation'],
		    							'seller_reference' => $product['reference'],
		    							'seller_collection' => $product['collection'],
		    							'seller_category' => $product['category'],
		    							'seller_quantity' => $product['quantity'],
		    							'seller_sale_price' => $product['sale_price'],
		    							'seller_weight' => $weight,
		    							'seller_height' => $height,
		    							'seller_width' => $width,
		    							'seller_length' => $length,
		    							'seller_ean' => !empty($product['ean']) ? $product['ean'] : $product['id'],
		    							'seller_description' => $product['description'],
		    							'created' => date('Y-m-d H:i:s'),
		    							'updated' => date('Y-m-d H:i:s')
		    					);
		    					$query = $db->insert('module_marketplace_products', $data);
		    			
		    					if ( ! $query ) {
		    						 
		    						$insertError++;
		    						 
		    					} else {
		    						 
		    						$id = $db->last_id;
		    						if(!empty($id)){
			    						$inserted++;
			    						$publicationsModel->store_id = $storeId;
			    						$publicationsModel->publication_code = $id;
			    						$publicationsModel->product_id = $product['id'];
			    						$publicationsModel->sku = $product['sku'];
			    						$publicationsModel->marketplace = 'Marketplace';
			    						$publicationsModel->user = $request;
			    						$publicationsModel->Save();
			    						
			    						 
			    						$dataLog['insert_available_products'] = $data;
			    			
			    						$db->insert('products_log', array(
			    								'store_id' => $storeId,
			    								'product_id' => $product['id'],
			    								'description' => "Novo Produto Cadastrado Marketplace",
			    								'user' => $request,
			    								'created' => date('Y-m-d H:i:s'),
			    								'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
			    						));
		    						}
		    					}
	    					
	    					}else{
	    						$insertError++;
	    					}
	    				}
	    			}
	    			 
	    			if(!$query){
	    				$adicionarError = $insertError > 0 ? "Adicionar {$insertError}" : '';
	    				$atualizarError = $updateError > 0 ? "Atualizar {$updateError}" : '';
	    				
	    				 
	    			}else{
	    				$adicionar = $inserted > 0 ? "Adicionados {$inserted}" : '';
	    				$atualizar = $updated > 0 ? "Atualizados {$updated}" : '';
	    				
	    			}
	    			
	    		}
    		}
    		
    		if(count($errorLog) > 0){
    			
    			echo "error|Não foi possivel {$adicionarError} {$atualizarError} produtos do Feed Marketplace Sysplace...";
    			pre($errorLog);
    		}else{
    			echo "success|Produto {$adicionar} {$atualizar} com sucesso!";
    		}
    		
    		break;
    		
    		
    	}
    	
    	
}
 