<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Sac/QuestionsModel.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 9);
    $email   = $moduleConfig['email'];
    $apiKey  = $moduleConfig['api_key'];
    $xAccountKey = $moduleConfig['account_key'];
    $baseUri = $moduleConfig['base_uri'];
    $api = new REST($email, $apiKey, $xAccountKey, $baseUri);
    
    switch($action){
    	
    	case "get_sac":
    		
    		$params = array('status' => 'UNREAD');
    		$response = $api->get("/sac", $params);
    		
    		foreach($response['body']->sacs as $key => $sac){
    			
    			$responseChat = $api->get("/sac/{$sac->code}/chats/", array());
    			pre($responseChat['body']->chats);
    		}
    		break;
    	
    	
        case "get_questions":
        	$params = array();
        	$response = $api->get("/questions", $params);
        	if($response['httpCode'] == 200){
        		$questions = $response['body'];
// 	        	if($questions->qty > 0){
	        		
	        		$response = json_decode('{
							"questions": [{
									"status": "UNANSWERED",
									"product_sku": "4637",
									"platform_sku": "4637",
									"platform": "B2W",
									"customer": {
										"name": "Rodrigo"
									},
									"created_at": "2019-10-10T16:58:07.297Z",
									"code": "B2W-5d9f631fec233600327e3205",
									"body": "Ola boa tarde...\nFiz o pagamento dos autofalantes ontem ainda e ate agora nao foi despachado! Vocês não tem eles a pronta entrega?",
									"answers": [],
									"answer": {}
								},
								{
									"status": "UNANSWERED",
									"product_sku": "6804",
									"platform_sku": "6804",
									"platform": "B2W",
									"customer": {
										"name": "Ronaldo"
									},
									"created_at": "2019-10-10T12:06:52.758Z",
									"code": "B2W-5d9f1edca8bad6003227cbe5",
									"body": "Nao poderia retirar em alguma loja de vcs ???  Preciso muito rapido",
									"answers": [],
									"answer": {}
						
								}
							],
							"qty": 50,
							"cursor": "eyJvZmZzZXQiOjUxLCJsaW1pdCI6NTB9"
						}');
	        		$questions->questions = $response->questions;
	        		foreach($questions->questions as $key => $question){
	        			pre($question);
	        			$advertsId = str_replace("MLB", '', $questionRes['item_id']);
	        			$sql = "SELECT available_products.id as product_id FROM available_products 
	        			WHERE available_products.store_id = {$storeId} AND available_products.sku LIKE '".trim($question->product_sku)."'";
	        			$query = $db->query($sql);
	        			$result = $query->fetch(PDO::FETCH_ASSOC);
	        			 
// 	        			if(!empty($result['product_id'])){
	        				$questionsModel = new QuestionsModel($db, null);
	        				$questionsModel->store_id = $storeId;
	        				$questionsModel->product_id = $result['product_id'];
	        				$questionsModel->sku = $question->product_sku;
	        				$questionsModel->customer = $question->customer->name;
	        				$questionsModel->question_id = $question->code;
	        				$questionsModel->question = $question->body;
	        				$questionsModel->status = $question->status;
	        				$questionsModel->item_id = $question->platform_sku;
	        				$questionsModel->date_created =  date("Y-m-d H:i:s", strtotime($question->created_at));
	        				$questionsModel->answer_status = "ACTIVE";
	        				$questionsModel->from_id = $question->code;
	        				$questionsModel->from_answered_questions = count($question->answers);
	        				$questionsModel->marketplace = $question->platform;
	        				$questionsModel->user = $request;
	        				pre($questionsModel);
	        				$res = $questionsModel->Save();
	        				pre($res);die;
// 	        			}
	        			
	        			
	        		}
	        		
// 	        	}
        	}else{
        		pre($response->message);
        	}
            break;
            
    }
    
}

