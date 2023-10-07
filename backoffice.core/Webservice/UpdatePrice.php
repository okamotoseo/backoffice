<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;

if (empty ( $action ) and empty ( $storeId )) {
    $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
    $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
    $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
}
if(isset($storeId)){
    
    $db = new DbConnection();
    
    switch($action){
	
	
	case "update_price" :
	    $parentIds = array(166827, 166505, 166491, 166490, 167000, 166999, 167002, 167249, 167247, 166400, 166507, 167257, 167258, 167251, 167255, 167256, 166996, 166997, 167598, 166736, 166735, 166746, 167594, 167600, 167187, 167185, 167186, 167180, 167178, 167176, 166438, 167540, 167172, 166440, 166439, 167171, 167542, 166582, 167173, 166579, 166474, 166606, 166478, 166604, 166624, 166605, 166469, 166468, 166622, 166620, 166617, 166464, 166614, 166619, 166475, 166625, 166565, 166428, 166432, 166560, 166437, 166564, 166563, 166433, 166430, 166431, 166566, 166713, 166714, 167477, 167610, 167617, 167611, 167609, 167615, 167537, 167686, 167421, 167498, 166543, 166422, 166598, 166798, 166417, 167286, 167294, 167295, 166414, 166407, 166404, 166977, 166410, 166513, 166539, 167263, 167268, 166530, 166531, 166535, 166534, 166791, 166770, 166771, 166772, 166769, 166788, 167387, 167384, 167385, 167386, 166380, 167017, 166766, 167010, 167009, 166688, 166696, 166686, 167583, 167582, 167580, 167581, 167579, 167576, 167503, 167502, 166332, 167191, 167192, 166339, 166330, 166322, 167500, 167577, 167578, 167188, 167194, 167156, 167138, 167137, 167139, 167142, 167131, 167130, 167106, 167108, 167113, 167111, 167124, 167145, 167116, 167121, 167161, 166955, 167279, 167281, 166992, 166993, 166991, 166990, 166988, 167277, 167278, 166984, 166995, 167282, 167276);
	    
	    foreach($parentIds as $k => $parentId){
	        $sqlAP = "SELECT sale_price FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}'"; 
	        $query = $db->query($sqlAP);
	        $res = $query->fetch(PDO::FETCH_ASSOC);
	        
	        if(!empty($res['sale_price'])){
	            
	            
	            $promotionPrice = $res['sale_price'] * 0.80;
	            $promotionPrice = number_format($promotionPrice, 2);
// 	            echo $res['sale_price'] ." = ". $promotionPrice ."<br>";
                $updated = date("Y-m-d H:m:s");
                $startPromotion = date("Y-m-d");
                $endPromotion = "2019-12-02";
                
	            $sqlUpdate = "UPDATE available_products SET 
                promotion_price = '{$promotionPrice}', 
                start_promotion = '{$startPromotion}', 
                end_promotion = '{$endPromotion}', 
                updated = '{$updated}' 
                WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}'";
	            $db->query($sqlUpdate);
	        }
	        
	        
	    }
        
        break;
    }
        
}