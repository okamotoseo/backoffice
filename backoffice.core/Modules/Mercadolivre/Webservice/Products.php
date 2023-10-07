<?php
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../Class/class-MeliCategories.php';
// require_once $path .'/Class/class-VerifyToken.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
if (empty ( $action ) and empty ( $storeId )) {
    $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
    $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
    $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
    $paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
    $accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
    require_once $path .'/verifyToken.php';
    
	switch($action){

	    case "delete_products_variation_meli" :
	        $adsId = empty ( $_REQUEST ['ads_id'] ) ? null : $_REQUEST ['ads_id'];
	        $variationId = empty ( $_REQUEST ['variation_id'] ) ? null : $_REQUEST ['variation_id'];
	        
	        $sqlDelete = "DELETE FROM ml_products_attributes WHERE store_id = {$storeId} AND variation_id = {$variationId} AND  product_id = {$adsId}";
	        $db->query($sqlDelete);
	        
	        echo "success|{$variationId}";
	        break;
	        
	    case "updte_sku_products_variation_meli" :
	        $adsId = empty ( $_REQUEST ['ads_id'] ) ? null : $_REQUEST ['ads_id'];
	        $variationId = empty ( $_REQUEST ['variation_id'] ) ? null : $_REQUEST ['variation_id'];
	        $newId = empty ( $_REQUEST ['new_id'] ) ? null : $_REQUEST ['new_id'];
	        
	        $sqlUpdate = "UPDATE ml_products_attributes SET sku = '{$newId}' WHERE store_id = {$storeId} AND variation_id = {$variationId} AND  product_id = {$adsId}";
	        $db->query($sqlUpdate);
	        
	        echo "success|{$newId}";
	        break;
	        
	      
	    case "remove_ads_product":
	        $adsId =  isset($_REQUEST ['ads_id']) ? $_REQUEST ['ads_id'] : NULL;
	        if(!empty ( $adsId )){
	            $adsId = is_array($adsId) ? $adsId : array($adsId) ;
	            foreach($adsId as $key => $advertId){
	            
	                $result = closeAdsProduct($meli, $storeId, $advertId, $resMlConfig ['access_token']);
    	            
	                if($result['httpCode'] == 200 || $result['httpCode'] == 400 || $result['httpCode'] == 404){
    	                $sql = "DELETE FROM `ml_products_attributes` WHERE store_id = {$storeId} AND product_id =  {$advertId}";
    	                $db->query($sql);
    	                $sql = "DELETE FROM `ml_products` WHERE store_id = {$storeId} AND id =  {$advertId}";
    	                $db->query($sql);
    	                $sql = "DELETE FROM `ml_responses` WHERE product_id =  {$advertId}";
    	                $db->query($sql);
    	                
    	                $sql = "DELETE FROM `publications` WHERE store_id = {$storeId} AND publication_code =  {$advertId}";
    	                $db->query($sql);
    	                
    	                echo "success|{$advertId}";
    	            }else{
    	                
    	                $faultString = !empty($result['body']->cause[0]->message) ? $result['body']->cause[0]->message : "Erro ao excluir anúncio "; 
    	                    
    	                $db->update('ml_products',
    	                    array('store_id','id'),
    	                    array($storeId, $advertId),
    	                    array('status' => 'error_delete', 'message' => $faultString)
    	                    );
    	                
    	                echo "error|{$faultString}";
    	                
//     	                pre($result);
    	            }
	            }
	            
	        }
	        
	        break;
	        
	    
	    
		case "add_color_relationship":
		    
		    $mlColorId = $_REQUEST['ml_color_id'];
		    $colorId = $_REQUEST['color_id'];
		    $color = $_REQUEST['color'];
		    $sequence = $_REQUEST['number'];
		    $sqlVerify = "SELECT `id` FROM `ml_color_relationship` WHERE store_id = {$storeId} AND color LIKE '{$color}'";
		    $query = $db->query($sqlVerify);
		    $resVerify = $query->fetch(PDO::FETCH_ASSOC);
		    if(empty($resVerify['id'])){
		       $sqlInsert = "INSERT INTO `ml_color_relationship`(`store_id`, `color_id`, `color`, `information_1`)
				VALUES ({$storeId}, {$colorId}, '{$color}', '{$mlColorId}')";
		        $query = $db->query($sqlInsert);
		    }else{
		        if($sequence == 1){
		            $sqlUpdate = "UPDATE `ml_color_relationship` SET `information_1`='{$mlColorId}'
					WHERE store_id = {$storeId} AND id = {$resVerify['id']}";
		        }
		        if($sequence == 2){
		            $sqlUpdate = "UPDATE `ml_color_relationship` SET `information_2`='{$mlColorId}'
					 WHERE store_id = {$storeId} AND id = {$resVerify['id']}";
		        }
		        $query = $db->query($sqlUpdate);
		    }
		    if($query){
		        echo "success|Relacionamento cadastrado com successo!";
		    }
		    break;
		    
		case "add_attribute_relationship":
		        
		        $mlCategoryId = $_REQUEST['ml_category_id'];
		        $mlAttributeId = $_REQUEST['ml_attribute_id'];
		        $attributeId = $_REQUEST['attribute_id'];
		        $attributeAlias = $_REQUEST['attribute_alias'];
		        
		        $sqlVerify = "SELECT `id` FROM `ml_attributes_relationship` 
                WHERE store_id = {$storeId}  AND ml_attribute_id = '{$mlAttributeId}'
                AND ml_category_id = '{$mlCategoryId}'";
		        $query = $db->query($sqlVerify);
		        $resVerify = $query->fetch(PDO::FETCH_ASSOC);
		        if(empty($resVerify['id'])){
		            $query = $db->insert('ml_attributes_relationship', array(
		                'store_id' => $storeId,
		                'attribute_id' => $attributeId,
		                'attribute' => $attributeAlias,
		                'ml_category_id' => $mlCategoryId,
		                'ml_attribute_id' => $mlAttributeId
		            ));
		        }else{
		            
		            $query = $db->update('ml_attributes_relationship', 'id', $resVerify['id'], array(
		                'attribute_id' => $attributeId,
		                'attribute' => $attributeAlias,
		                'ml_category_id' => $mlCategoryId,
		                'ml_attribute_id' => $mlAttributeId
		            ));
		        }
		        if($query){
		            echo "success|{$attributeAlias}|Relacionamento cadastrado com successo!";
		        }
		        break;
		
		case "get_categories" :
		    $categoryId = trim ( $_REQUEST ['category_id'] );
		    $categoryEcommerce = trim ( $_REQUEST ['category'] );
		    $pathRoot = "";
		    $hierarchy = "";
		    $option = "<option value='select'> Selecionar Próxima >></option>";
		    $result = getCategories ( $meli, $categoryId );
// 		    pre($result['body']);
		    if (empty($result['body']->children_categories)){
		        $exist = true;
		    } else {
		        
		        
// 		        foreach($result['body']->children_categories as $key => $category ) {
// 		            if($category->name == "Outras Marcas" OR $category->id == "MLB199584" OR $category->id == "MLB199572"){
// 		                $exist = true;
// 		            }
// 		        }
		    }
		    if (!$exist) {
		        foreach ( $result ['body']->children_categories as $key => $category ) {
		            $option .= "<option value='{$category->id}'>{$category->name}</option>";
		            
// 		            saveCategoryRequiredAttribute($db, $meli, $storeId, $category->id);
		            
		        }
		        foreach ( $result ['body']->path_from_root as $key => $value ) {
		            $ind = rand ();
		            $pathRoot .= "<a onclick=\"getCategories('{$value->id}', {$ind})\" id='{$ind}' category='{$categoryEcommerce}' >{$value->name}</a> > ";
		            $hierarchy .= "{$value->name} > ";
		        }
		        $pathRoot = substr($pathRoot, 0, - 3);
		        $hierarchy = substr($hierarchy, 0, - 3);
		        $hierarchy .= "{$value->name} > ";
		        echo "next|{$pathRoot}|{$option}|<a href='https://api.mercadolibre.com/categories/{$value->id}' target='_blank'>{$value->id}</a>";
		    } else {
		        
		        foreach ( $result ['body']->path_from_root as $key => $value ) {
		            $ind = rand ();
		            $pathRoot .= "<a onclick=\"getCategories('{$value->id}', {$ind})\" id='{$ind}' category='{$categoryEcommerce}' >{$value->name}</a> > ";
		            $hierarchy .= "{$value->name} > ";
		        }
		        $pathRoot = substr ( $pathRoot, 0, - 3 );
		        $pathRootWithSlashes = addslashes($pathRoot);
		        
		        $hierarchy = substr ( $hierarchy, 0, - 3 );
		        $hierarchyWithSlashes = addslashes($hierarchy);
		        $sqlSelect = "SELECT id FROM `ml_category_relationship` 
                WHERE store_id = {$storeId} AND category LIKE '{$categoryEcommerce}'";
		        $query = $db->query($sqlSelect);
		        $res = $query->fetch(PDO::FETCH_ASSOC);
		        

		        $attributeTypes = isset($result['body']->attribute_types) ? $result['body']->attribute_types : "";
		        
		        if (empty($res['id'])) {
		            $sql = "INSERT INTO `ml_category_relationship`(`store_id`, `category_id`, `attribute_types`,`category`, `path_from_root`, `hierarchy`)
					VALUES ({$storeId},'{$value->id}', '{$attributeTypes}', '{$categoryEcommerce}','{$pathRootWithSlashes}', '{$hierarchyWithSlashes}')";
		        } else {
		            $sql = "UPDATE `ml_category_relationship` SET `category_id`='{$value->id}', `attribute_types`='{$attributeTypes}', 
                    `path_from_root`='{$pathRootWithSlashes}', hierarchy = '{$hierarchyWithSlashes}'
					WHERE store_id = {$storeId} AND id = {$res['id']}";
		        }
		        $response = $db->query($sql);
		        
		        saveCategoryRequiredAttribute($db, $meli, $storeId, $value->id);
		        
		        $categories = new Categories();
		        
		        $categories->getMlRootCategories();
		        
		        $option = "<option value='select'>Selecione</option>";
		        foreach($categories->defaultCategories as $key => $category){
		            $option .="<option value='{$category->id}'>{$category->name}</option>";
		        }
		        
		        echo "end|{$pathRoot}|{$option}|<a href='https://api.mercadolibre.com/categories/{$value->id}' target='_blank'>{$value->id}</a>";
		    }
		    break;
		    
		    
		case "update_categories" :
		    
		    $mlbCategory = $_REQUEST['category'];
		    
		    if(!empty($mlbCategory)){
		      $sqlSelect = "SELECT * FROM `ml_category_relationship` WHERE store_id = {$storeId} AND category_id LIKE '{$mlbCategory}'";
		    }else{
		      $sqlSelect = "SELECT * FROM `ml_category_relationship` WHERE store_id = {$storeId};";
		    }
		    
		    $query = $db->query($sqlSelect);
		    
		    while($res = $query->fetch(PDO::FETCH_ASSOC)){
// 		        pre($res);die;
		        $categoryId = trim ( $res ['category_id'] );
		        $categoryEcommerce = trim ( $res ['category'] );
    		    $hierarchy = "";
    		    $result = getCategories ( $meli, $categoryId );
    		    
    		    if (empty($result['body']->children_categories)){
    		        
    		   
    		        foreach ( $result ['body']->path_from_root as $key => $value ) {
    		            $hierarchy .= "{$value->name} > ";
    		        }
    		        
    		        $hierarchy = substr ( $hierarchy, 0, - 3 );
    		        
    		        $hierarchyWithSlashes = addslashes($hierarchy);
    		        
    		        $attributeTypes = isset($result['body']->attribute_types) ? $result['body']->attribute_types : "";
    		        echo $categoryEcommerce."<br>";
    	            echo $sql = "UPDATE `ml_category_relationship` SET `category_id`='{$value->id}', 
                    `attribute_types`='{$attributeTypes}', hierarchy = '{$hierarchyWithSlashes}'
    				WHERE store_id = {$storeId} AND id = {$res['id']}";
    		        $response = $db->query($sql);
    		        
    		        saveCategoryRequiredAttribute($db, $meli, $storeId, $value->id);
    		        pre($value->id);
    		       
    		    }
    		        
		    }
		    break;
		    
	}
	
}

