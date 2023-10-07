<?php
$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../library/sdk-v2/CNovaApiLojistaV2.php';

require_once $path .'/../Models/Products/ProductsModel.php';
require_once $path .'/../Models/Products/ProductVariationsModel.php';
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 10);

    $vivarejoApi = new Viavarejo($moduleConfig);
    
    $api_client = $vivarejoApi->api_client;
    
    switch ($action){
    	
    	case "send_ticket":
    		
    		$tickets_api = new  \CNovaApiLojistaV2\TicketsApi($api_client);
    		
    		$new_ticket = new \CNovaApiLojistaV2\model\NewTicket();
    		$new_ticket->to = 'atendimento+OS_706000500000@mktp.extra.com.br';
    		$new_ticket->body = 'Corpo da mensagem do ticket';
    		
    		try {
    			
    			$tickets_api->postTicket($new_ticket);
    			
    		
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    			
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
    				echo ($e->getMessage());
    			}
    		}
    			
    			
    	break;
    	
    case "ticket_opened":
    	
    		$tickets_api = new  \CNovaApiLojistaV2\TicketsApi($api_client);
    		
    		try {
    		
    			$tickets = $tickets_api->getTickets('opened', '439211092852', null, null, null, 0, 5);
    			var_dump($tickets);
    			 
    			 
    	
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    			 
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
    				echo ($e->getMessage());
    			}
    		}
    		 
    		 
    		break;
    		
    		
    	case "update_status_ticket":
    		
    		$tickets_api = new  \CNovaApiLojistaV2\TicketsApi($api_client);
    		

    		try {
    		
    			$ticket_status = new \CNovaApiLojistaV2\model\TicketStatus();
    			$ticket_status->ticket_status = 'Em Acompanhamento';
    		
    			$tickets_api->putTicketStatus('123123', $ticket_status);
    		
    		
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    				 
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
    				echo ($e->getMessage());
    			}
    		}
    		 
    		break;
    	
    }
    
    
}
    	
    	
    	