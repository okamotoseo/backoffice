<?php
ini_set ("display_errors", true);
set_time_limit ( 300 );
header("Content-Type: text/html; charset=utf-8");
$path = dirname(__FILE__);
require_once $path .'/Class/class-DbConnection.php';
require_once $path .'/Functions/global-functions.php'; 

$contents =  file_get_contents('php://input'); 

$contents = json_decode($contents);

notifyAdmin(json_encode($_REQUEST));

$message = '1 notificacao mercadolivre';

echo http_response_code('200');

foreach($contents->requests as $i => $request){
    
    $content = json_decode($request->rawModeData);
    
    if(!empty($content->application_id)){
        
    	$db = new DbConnection();
    	
    	$message .= $sqlStore = "SELECT store_id FROM `module_mercadolivre` WHERE app_id = {$content->application_id}";
    	$query = $db->query($sqlStore);
    	$store = $query->fetch(PDO::FETCH_ASSOC);
    	
    	if(isset($store['store_id']) and !empty($store['store_id'])){
    	    
    		$message .= $sqlVerify = "SELECT id FROM `ml_notifications` WHERE store_id = {$store['store_id']} 
    	    AND application_id = {$content->application_id} AND resource LIKE '{$content->resource}'
    	    AND user_id LIKE '{$content->user_id}' AND topic LIKE '{$content->topic}'";
    	    $queryVerify = $db->query($sqlVerify);
    	    $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
    	    if(!isset($verify['id'])){
    	        
    	        $sent = isset($content->sent) ? date("Y-m-d H:i:s", strtotime($content->sent) ) :  date("Y-m-d H:i:s") ;
    	        
    	        $received = isset($content->received) ? date("Y-m-d H:i:s", strtotime($content->received) ) :  date("Y-m-d H:i:s") ;
    	        $data = array(
    	            "store_id" => $store['store_id'],
    	            "resource" => $content->resource,
    	            "user_id" => $content->user_id,
    	            "topic" => $content->topic,
    	            "application_id" => $content->application_id,
    	            "attempts" => $content->attempts,
    	            "sent" => $sent,
    	            "received" => $received
    	        );
    	        $query = $db->insert('ml_notifications', $data);
    	        
    	        if(!$query){
    	            $message .= json_encode($data);
    	            notifyAdmin($message);
    	        }
    	        
    	    }
    	    
    	}
    
    }
   
}

?>