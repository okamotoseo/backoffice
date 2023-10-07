<?php
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 30000 );
$path = dirname(__FILE__);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../Class/class-DbConnection.php';
require_once $path .'/../../Class/class-MainModel.php';
require_once $path .'/../../Functions/global-functions.php';
require_once $path .'/Class/class-PgConnection.php';
require_once $path .'/Class/class-UpdateAvailableProducts.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

if (empty ( $action ) and empty ( $storeId )) {
    $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
    $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
    $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
    $request = "System";
}

if(isset($storeId)){
    $db = new DbConnection();
    $pg = new PgConnection($db, $storeId);
    
	switch($action){
	    
		case "autocomplete-products":
		    
		    $term = mb_strtoupper($_REQUEST["term"]);
		    $type = $_REQUEST["type"];
		    
		    $arr = getFilterId($pg, $type, $term, 10);
			
		    echo $callback . '(' . json_encode($arr) . ')';
			
			break;
		case "import_account_stores":
// 		    require_once ABSPATH .'/Models/Store/StoreModel.php';
		    
		    $informations = getStoreInformations($pg);
			foreach($informations as $key => $info){
			    $info['account_id'] = $accountId;
			    $query = $db->insert('stores', $info);
			}
				
			break;
			
		case "update_available_products":
		    
		    $updateAP = new UpdateAvailableProducts($db, $pg, $storeId);
		    
		    $syncId =  logSyncStart($db, $storeId, "Seta", $action, "Importação de produtos tmp", $request);
		    
		    $imported = $updateAP->importAvailableProductsTmp();

		    logSyncEnd($db, $syncId, $imported);

		    $syncId =  logSyncStart($db, $storeId, "Seta", $action, "Atualização de produtos disponiveís", $request);
		    
		    $updated = $updateAP->updateAvailableProducts();
		     
		    logSyncEnd($db, $syncId, $updated);
		    
		    break;
		    
		case "update_brands":
		    $sql = "SELECT distinct brand FROM available_products WHERE brand not in (select brand from brands where brands.store_id = {$storeId}) AND store_id = {$storeId}";
		    $query = $db->query($sql);
		    $brands = $query->fetchAll(PDO::FETCH_ASSOC);
		    foreach($brands as $key => $brand){
		        $brandText = friendlyText($brand['brand']);
		        pre($key." - ".$brandText);
		        
		        $query = $db->insert('brands', array(
		            'store_id' => $storeId,
		            'brand' => $brandText,
		            'description' => $brandText
		          )
		        );
// 		        die;
		        
		    }
		    break;
		    
		    
		case "update_colors":
		    $sql = "SELECT distinct color FROM available_products WHERE color not in (
            SELECT color FROM colors WHERE colors.store_id = {$storeId}) AND store_id = {$storeId}";
		    $query = $db->query($sql);
		    $colors = $query->fetchAll(PDO::FETCH_ASSOC);
		    foreach($colors as $key => $color){
		        $colorText = friendlyText($color['color']);
		        pre($key." - ".$colorText);
		        
		        $query = $db->insert('colors', array(
		            'store_id' => $storeId,
		            'color' => $colorText,
		            'description' => $colorText
		        )
		            );
// 		        		        die;
		        
		    }
		    break;
		    
		    
	}

}
