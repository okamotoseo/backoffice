<?php

ini_set ("display_errors", false);
set_time_limit ( 300 );
header("Content-Type: text/html; charset=utf-8");

echo http_response_code('200');

$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Functions/global-functions.php';
$content =  file_get_contents('php://input');
$content = json_decode($content);
$message = 'notificacao mercadolivre';


if(!empty($content->application_id)){
    
	$db = new DbConnection();
	$message .= $sqlStore = "SELECT store_id FROM `module_mercadolivre` WHERE app_id = {$content->application_id}";
	$query = $db->query($sqlStore);
	$store = $query->fetch(PDO::FETCH_ASSOC);
	
	if(isset($store['store_id']) and !empty($store['store_id'])){
	    
	    $sqlVerify = "SELECT id FROM `ml_notifications` WHERE store_id = {$store['store_id']} 
	    AND application_id = {$content->application_id} AND resource LIKE '{$content->resource}'
	    AND user_id LIKE '{$content->user_id}' AND topic LIKE '{$content->topic}'";
	    $queryVerify = $db->query($sqlVerify);
	    $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	    if(!isset($verify['id'])){
	        
	        $sent = isset($content->sent) ? date("Y-m-d H:i:s", strtotime($content->sent) ) :  date("Y-m-d H:i:s") ;
	        
	        $received = isset($content->received) ? date("Y-m-d H:i:s", strtotime($content->received) ) :  date("Y-m-d H:i:s") ;
	        
	        $query = $db->insert('ml_notifications', array(
	            "store_id" => $store['store_id'],
	            "resource" => $content->resource,
	            "user_id" => $content->user_id,
	            "topic" => $content->topic,
	            "application_id" => $content->application_id,
	            "attempts" => $content->attempts,
	            "sent" => $sent,
	            "received" => $received
	        ));
	        if($query){
	            echo http_response_code('200');
	        }
	    
	    }
	}
}
$message .= json_encode($content);
notifyAdmin($message);
?>