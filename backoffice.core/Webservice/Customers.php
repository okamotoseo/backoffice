<?php
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
// header("Access-Control-Allow-Origin: *");
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/functions.php';


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
	    
	 
            
        case "update_gender" :
            
            $customerId = isset($_REQUEST["customer_id"]) && $_REQUEST["customer_id"] != "" ? $_REQUEST["customer_id"] : null ;
            
            $gender = isset($_REQUEST["gender"]) && $_REQUEST["gender"] != "" ? strtoupper($_REQUEST["gender"]) : null ;
            
            $customerIds = is_array($customerId) ? $customerId : array($customerId) ;
            
            if($gender){
                
                foreach($customerIds as $ind => $id){
                    
                    $query = $db->update('customers',
                        array('store_id', 'id'),
                        array($storeId, $id),
                        array('Genero'  => trim($gender), 'updated'  => date("Y-m-d H:i:s"))
                        );
                }
                if($query){
                    echo "success|Atributo atualizadas com sucesso!";
                }else{
                    echo "error|Erro ao atualiar atributo!";
                }
            }
            break;
                
	    
	        
	}
	
	
}