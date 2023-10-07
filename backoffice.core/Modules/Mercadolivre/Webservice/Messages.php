<?php
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Sac/QuestionsModel.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Api/QuestionsRestModel.php';
require_once $path .'/../Models/Api/NotificationsRestModel.php';
require_once $path .'/../Models/Notifications/NotificationsModel.php';
require_once('../Library/php-sdk-master-new/vendor/autoload.php');
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
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
   
    require_once $path .'/verifyToken.php';
    
	switch($action){
		
		case 'get_notifications':
			
			$config = new Meli\Configuration();
			$servers = $config->getHostSettings();
			// Auth URLs Options by country
			
			// 1:  "https://auth.mercadolibre.com.ar"
			// 2:  "https://auth.mercadolivre.com.br"
			// 3:  "https://auth.mercadolibre.com.co"
			// 4:  "https://auth.mercadolibre.com.mx"
			// 5:  "https://auth.mercadolibre.com.uy"
			// 6:  "https://auth.mercadolibre.cl"
			// 7:  "https://auth.mercadolibre.com.cr"
			// 8:  "https://auth.mercadolibre.com.ec"
			// 9:  "https://auth.mercadolibre.com.ve"
			// 10: "https://auth.mercadolibre.com.pa"
			// 11: "https://auth.mercadolibre.com.pe"
			// 12: "https://auth.mercadolibre.com.do"
			// 13: "https://auth.mercadolibre.com.bo"
			// 14: "https://auth.mercadolibre.com.py"
			
			// Use the correct auth URL
			$config->setHost($servers[2]["url"]);
			
			// Or Print all URLs
			
			$notificationsRestModel = new NotificationsRestModel($db, null, $storeId);
			
			$notificationsRestModel->app_id = 7612099390077583;
			$notificationsRes = $notificationsRestModel->getNotifications();
			
			pre($notificationsRes);
			
			break;
		
		case "send_answer";
			$id = $_REQUEST['id'];
			$answer = $_REQUEST['answer'];
			$created = date("Y-m-d H:i:s");
			$sqlQuestion = "SELECT question_id FROM questions WHERE store_id = {$storeId} AND id = {$id} LIMIT 1";
			$query = $db->query($sqlQuestion);
			$question = $query->fetch(PDO::FETCH_ASSOC);
			$questionsRestModel = new QuestionsRestModel($db, null, $storeId);
			$questionsRestModel->question_id = $question['question_id'];
			$questionsRestModel->answer = trim($answer);
			$answerRes = $questionsRestModel->postAnswers();
			if(isset($answerRes['body'])){
				$answerRes = $answerRes['body'];
			}
			if($answerRes->status == 'ANSWERED'){
				$advertsId = str_replace("MLB", '', $answerRes->item_id);
				$sql = "SELECT ml_products.sku, ml_products.title, available_products.id as product_id FROM ml_products 
				LEFT JOIN available_products ON ml_products.sku = available_products.sku
				AND available_products.store_id = ml_products.store_id
				WHERE ml_products.store_id = {$storeId} AND ml_products.id =  {$advertsId}";
				$query = $db->query($sql);
				$result = $query->fetch(PDO::FETCH_ASSOC);
				if(!empty($result['product_id'])){
					$questionsModel = new QuestionsModel($db, null);
					$questionsModel->id = $id;
					$questionsModel->store_id = $storeId;
					$questionsModel->product_id = $result['product_id'];
					$questionsModel->sku = $result['sku'];
					$questionsModel->title = trim($result['title']);
					$questionsModel->customer = 'Mercadolivre';
					$questionsModel->question_id = $answerRes->id;
					$questionsModel->seller_id = $answerRes->seller_id;
					$questionsModel->question = $answerRes->text;
					$questionsModel->status = $answerRes->status;
					$questionsModel->item_id = $answerRes->item_id;
					$questionsModel->date_created = date("Y-m-d H:i:s", strtotime($answerRes->date_created));
					$questionsModel->hold = $answerRes->hold;
					$questionsModel->deleted_from_listing = $answerRes->deleted_from_listing;
					$questionsModel->answer = $answerRes->answer->text;
					$questionsModel->answer_status = $answerRes->answer->status;
					$questionsModel->answer_date_created = date("Y-m-d H:i:s", strtotime($answerRes->answer->date_created));
					$questionsModel->from_id = $answerRes->from->id;
					$questionsModel->from_answered_questions = $answerRes->from->answered_questions;
					$questionsModel->marketplace = 'Mercadolivre';
					$questionsModel->user = $request;
					$questionsModel->Save();
					echo "success|{$answerRes->answer->date_created}|{$answerRes->id}";
					
				}
			}else{
				echo "error|";
				pre($answerRes);
			}
		break;
	    
	    case "import_questions":

	    	$syncId =  logSyncStart($db, $storeId, "Mercadolivre", $action, "Importação de perguntas.", $request);
	    	$imported = 0;
	        $notificationsModel = new NotificationsModel($db, null, $storeId);
	        $questionsRestModel = new QuestionsRestModel($db, null, $storeId);
	        
	        $received =  date("Y-m-d H:i:s",  strtotime("-5 day") ); //date("Y-m-d H:i:s", strtotime("-24 hour", strtotime("now")));
	        $received_to = date("Y-m-d H:i:s");
	        $sql = "SELECT * FROM `ml_notifications` WHERE store_id = {$storeId} 
	        AND topic LIKE 'questions' AND received BETWEEN  '{$received}' AND '{$received_to}'";
	        $query = $db->query($sql);
	        $notifications = $query->fetchAll(PDO::FETCH_ASSOC);

	        foreach($notifications as $key => $notification){
	            $parts = explode("/", $notification['resource']);
	            $questionId = end($parts);
	            $questionsRestModel->question_id = $questionId;
	            $questionRes = $questionsRestModel->getQuestions();
	            if(!isset($questionRes['body']->message)){
		            $questionRes = $questionRes['body'];
		            $advertsId = str_replace("MLB", '', $questionRes->item_id);
		            $sql = "SELECT ml_products.sku, ml_products.title, available_products.id as product_id FROM ml_products
		            LEFT JOIN available_products ON ml_products.sku = available_products.sku
		            AND available_products.store_id = ml_products.store_id
		            WHERE ml_products.store_id = {$storeId} AND ml_products.id =  {$advertsId}";
		            $query = $db->query($sql);
		            $result = $query->fetch(PDO::FETCH_ASSOC);
		            if(!empty($result['product_id'])){
		            	$questionsModel = new QuestionsModel($db, null);
		            	$questionsModel->store_id = $storeId;
			            $questionsModel->product_id = $result['product_id'];
			            $questionsModel->customer = 'Mercadolivre';
			            $questionsModel->sku = $result['sku'];
			            $questionsModel->title = trim($result['title']);
			            $questionsModel->seller_id = $questionRes->seller_id;
			            $questionsModel->question = $questionRes->text;
			            $questionsModel->question_id = $questionRes->id;
			            $questionsModel->status = $questionRes->status;
			            $questionsModel->item_id = $questionRes->item_id;
			            $questionsModel->date_created = date("Y-m-d H:i:s", strtotime($questionRes->date_created));
			            $questionsModel->hold = $questionRes->hold;
			            $questionsModel->deleted_from_listing = $questionRes->deleted_from_listing;
			            $questionsModel->answer = $questionRes->answer->text;
			            $questionsModel->answer_status = $questionRes->answer->status;
			            $questionsModel->answer_date_created = date("Y-m-d H:i:s", strtotime($questionRes->answer->date_created));
			            $questionsModel->from_id = $questionRes->from->id;
			            $questionsModel->from_answered_questions = $questionRes->from->answered_questions;
			            $questionsModel->marketplace = 'Mercadolivre';
			            $questionsModel->user = $request;
			            $idQuestion = $questionsModel->Save();
			            if($idQuestion){
			            	if($questionRes->answer->status == 'ANSWERED'){
					            $queryUpdate = $db->update('ml_notifications',
					            		array('store_id', 'id'),
					            		array($storeId, $notification['id']),
					            		array('status' => 'imported',
					            				'information' => $questionRes->text
					            		));
			            	}else{
			            		$imported++;
			            	}
				            
				            echo "success|{$questionRes->answer->date_created}|{$questionRes->id}";
			            }else{
			            	echo "error|{$idQuestion}";
			            }
		            }
	            }else{
	            	
	            	$queryUpdate = $db->update('questions',
	            			array('store_id', 'question_id'),
	            			array($storeId, $questionId),
	            			array('status' => 'DELETED', 
	            				'answer_date_created' => date('Y-m-d H:i:s'),
	            				'deleted_from_listing' => trim($questionRes['body']->message)
	            			));
	            	
            		$queryUpdate = $db->update('ml_notifications',
            				array('store_id', 'id'),
            				array($storeId, $notification['id']),
            				array('status' => 'imported',
            					'information' => $questionRes['body']->message
            				));
            		 
            		if($queryUpdate){
            			echo "success|{$notification['id']}|{$questionRes['body']->message}";
            		}
	            		 
	            }
	        }
	        
	        logSyncEnd($db, $syncId, $imported);
	        
	        break;
	        
        /**
         * Send Message for status paid
         */   
	    case "send_sale_messages" :
	        
	        $sqlOrder = "SELECT * FROM orders WHERE store_id = {$storeId}  AND logistic_type != 'fulfillment'  AND marketplace LIKE 'Mercadolivre' AND sale_message  = 'F'  AND Status LIKE 'paid'";
	        
	        if(isset($_REQUEST['order_id'])){
	            $orderId = $_REQUEST['order_id'];
    	        $sqlOrder = "SELECT * FROM orders WHERE store_id = {$storeId}  AND logistic_type != 'fulfillment'  AND PedidoId = {$orderId} AND marketplace LIKE 'Mercadolivre' LIMIT 1";
	        }
	        $query = $db->query($sqlOrder);
	        $orders = $query->fetchAll(PDO::FETCH_ASSOC);
	        if(!empty($orders)){
    	        foreach($orders as $key => $order){
        	        $sqlCustomer = "SELECT codigo, Nome FROM customers WHERE store_id = {$storeId} AND id = {$order['customer_id']} LIMIT 1";
        	        $query = $db->query($sqlCustomer);
        	        $customer = $query->fetch(PDO::FETCH_ASSOC);
        	        
        	        $sqlMessage = "SELECT * FROM ml_messages WHERE store_id = {$storeId} AND status LIKE 'paid' LIMIT 1";
        	        $query = $db->query($sqlMessage);
        	        $message = $query->fetch(PDO::FETCH_ASSOC);
        	        
        	        $message = str_replace("[CUSTOMER_NAME]", $customer['Nome'], $message['message']);
        	        
        	        $obj = new StdClass;
        	        $obj->from = array('user_id' => $resMlConfig['seller_id'], 'email' => $resMlConfig['seller_email']);
        	        $obj->to = array(array('user_id' => $customer['codigo'], 'resource' =>'orders', 'resource_id' => $order['PedidoId'], 'site_id' =>'MLB'));
        	        $obj->text = $message;
        	        
        	        $result = $meli->post ( "/messages/packs/{$order['PedidoId']}/sellers/{$resMlConfig['seller_id']}", $obj, array (
        	            'access_token' => $resMlConfig ['access_token']
        	        ) );
        	        
        	        echo $sqlUpdate  = "UPDATE orders SET sale_message = 'T' WHERE store_id = {$storeId} AND id = {$order['id']}";
        	        $db->query($sqlUpdate);
        	        
    	        }
	        }
	        break;
	        
	    /**
	     * Send Message for status delivered
	     */    
	    case "send_after_sale_messages" :
	        
	        $sqlOrder = "SELECT * FROM orders WHERE store_id = {$storeId} AND marketplace LIKE 'Mercadolivre' 
            AND after_sale_message  = 'F' AND Status LIKE 'delivered' AND logistic_type != 'fulfillment'";
	        
	        if(isset($_REQUEST['order_id'])){
	            $orderId = $_REQUEST['order_id'];
	            $sqlOrder = "SELECT * FROM orders WHERE store_id = {$storeId} AND PedidoId = {$orderId} AND marketplace LIKE 'Mercadolivre' LIMIT 1";
	        }
	        $query = $db->query($sqlOrder);
	        $orders = $query->fetchAll(PDO::FETCH_ASSOC);
	        if(!empty($orders)){
	            foreach($orders as $key => $order){
	                $sqlCustomer = "SELECT codigo, Nome FROM customers WHERE store_id = {$storeId} AND id = {$order['customer_id']} LIMIT 1";
	                $query = $db->query($sqlCustomer);
	                $customer = $query->fetch(PDO::FETCH_ASSOC);
	                
	                $sqlMessage = "SELECT * FROM ml_messages WHERE store_id = {$storeId} AND status LIKE 'delivered' LIMIT 1";
	                $query = $db->query($sqlMessage);
	                $message = $query->fetch(PDO::FETCH_ASSOC);
	                
	                $message = str_replace("[CUSTOMER_NAME]", $customer['Nome'], $message['message']);

	                $obj = new StdClass;
	                $obj->from = array('user_id' => $resMlConfig['seller_id'], 'email' => $resMlConfig['seller_email']);
	                $obj->to = array(array('user_id' => $customer['codigo'], 'resource' =>'orders', 'resource_id' => $order['PedidoId'], 'site_id' =>'MLB'));
	                $obj->text = $message;
	                
	                $result = $meli->post ( "/messages/packs/{$order['PedidoId']}/sellers/{$resMlConfig['seller_id']}", $obj, array (
	                    'access_token' => $resMlConfig ['access_token']
	                ) );
	                
	                $sqlUpdate  = "UPDATE orders SET after_sale_message = 'T' WHERE store_id = {$storeId} AND id = {$order['id']}";
	                $db->query($sqlUpdate);
// 	                if ($result['httpCode'] == 201) {
	                    
// 	                    $sqlUpdate  = "UPDATE orders SET after_sale_message = 'T' WHERE store_id = {$storeId} AND PedidoId = {$order['PedidoId']}";
//     	                $db->query($sqlUpdate);
	                    
// 	                }else{
// 	                    $errorJson = json_encode($result['body']);
// 	                    $error = "Erro ao enviar mensagem automática pos venda entregues: \n {$errorJson}";
// // 	                    setMlLog($db, $storeId,  'order', $order['PedidoId'], "error", "messages", $error);
// // 	                    notifyAdmin(json_encode($result['body']->cause), true);
//                         echo "error|{$error}";  
// 	                }
	                
	                
	                
	            }
	        }
	        break;
	        
		    
	}
	
}

