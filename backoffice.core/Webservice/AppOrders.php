<?php
header("Content-Type: text/html; charset=utf-8");

if(isset($_SERVER['HTTP_HOST'])){
    define( 'HOME_URI', 'https://'.$_SERVER['HTTP_HOST']);
}else{
    define( 'HOME_URI', 'https://backoffice.sysplace.com.br/');
}
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Models/Shipping/PickingModel.php';
require_once $path .'/../Models/Orders/ReturnsModel.php';
require_once $path .'/../Models/Orders/ManageOrdersModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? $_REQUEST["store_id"] : null ;
$orderId = isset($_REQUEST["order_id"]) && $_REQUEST["order_id"] != "" ? $_REQUEST["order_id"] : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;


if (empty ( $action ) and empty ( $storeId )) {
	if(isset($_SERVER ['argv'] [1])){
		$paramAction = explode ( "=", $_SERVER ['argv'] [1] );
		$action = $paramAction [0] == "action" ? $paramAction [1] : null;
	}
	if(isset($_SERVER ['argv'] [2])){
		$paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
		$storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
	}
	if(isset($_SERVER ['argv'] [3])){
		$paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
		$accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
	}

	$request = "System";
}
if(isset($storeId)){
    
    $db = new DbConnection();
 
    switch($action){
        
        case "expire_xml_document":
            
          echo $sql = "DELETE FROM `xml_nota_saida` WHERE emissao <  '".date("Y-m-d H:i:s", strtotime("-100 day"))."'";
          $query = $db->query($sql);
          break;
    	
    	case 'update_address_order':
    		
    		$fields = array('Nome', 'Cep', 'Endereco', 'Numero', 'Bairro', 'Complemento', 'Cidade', 'Estado', 'Telefone', 'TipoPessoa');
    		
    		foreach($_REQUEST as $k => $val){
    			if(in_array($k, $fields) && !empty($val)){
    				$data[$k] = trim($val);
    			}
    			
    		}
    		if(!empty($data)){
    			
	    		$query = $db->update('orders', 
	    				array('store_id', 'id'),
	    				array($storeId, $orderId),
	    				$data);
	  
	    		if(!$query){
	    			echo "error|Erro ao atualizar informações do pedido";
	    			pre($query);
	    		}else{
	    			echo "success|Pedido Atualizado com sucesso...";
	    		}
	    		echo json_encode($data, JSON_PRETTY_PRINT);
    		}
    		
    		break;
    		
    	case 'get_order_detail':
    		$ordersModel = new ManageOrdersModel($db);
    		$ordersModel->id = $_REQUEST['order_id'];
    		$orders = $ordersModel->GetOrderDetails();
    		$order = $orders[0];
    		echo json_encode($order);
    		break;
    		
    	
    	case'register_order_occurrence':
    	
    		$occurrence['store_id'] 	= $storeId;
    		$occurrence['order_id'] 	= isset($_REQUEST["order_id"]) 		&& $_REQUEST["order_id"] 	!= "" ? $_REQUEST["order_id"] 		: '' ;
    		$occurrence['pedido_id'] 	= isset($_REQUEST["pedido_id"])		&& $_REQUEST["pedido_id"] 	!= "" ? $_REQUEST["pedido_id"] 		: '' ;
    		$occurrence['customer_id'] 	= isset($_REQUEST["customer_id"]) 	&& $_REQUEST["customer_id"] != "" ? $_REQUEST["customer_id"] 	: '' ;
    		$occurrence['occurrence'] 	= isset($_REQUEST["occurrences"]) 	&& $_REQUEST["occurrences"] != "" ? $_REQUEST["occurrences"] 	: '' ;
    		$occurrence['type'] 		= 'default';
    		$occurrence['user'] 		= isset($_REQUEST["user"]) 			&& $_REQUEST["user"] 		!= "" ? $_REQUEST["user"] 			: '' ;
    		$occurrence['created'] 		= date('Y-m-d H:i:s');
    		$query = $db->insert('order_occurrence', $occurrence);
    		$timeline = '';
    		
    		switch($occurrence['user']){
    			case "System": $fa = 'fa-pencil-square-o'; break;
    			case "Manual": $fa = 'fa-pencil-square-o'; break;
    			default: $fa = 'fa-user'; break;
    		}
    		
    		$time = getTimeFromTimestamp($occurrence['created']);
    		
    		$timeline .= "<li><i class='fa {$fa} bg-blue'></i>
    		<div class='timeline-item'>
    		<span class='time'><i class='fa fa-clock-o'></i> {$time}</span>
    		<h3 class='timeline-header'>{$occurrence['user']} registrou...</h3>
    		<div class='timeline-body'>{$occurrence['occurrence']}</div>";
    		$timeline .= "</div></li>";
    		
    		echo "success|".$timeline;
    	
    	
    		break;
    	
    	case 'get_order_occurrences':
    		
    		$response = '';
    		$sql = "SELECT * FROM order_occurrence WHERE store_id = {$storeId} AND order_id = {$orderId} ORDER BY created DESC";
    		$res = $db->query($sql);
    		$result = $res->fetchAll(PDO::FETCH_ASSOC);
    		$timeline = '';
    		
    		
    		if(isset($result[0]['user'])){
	    		foreach($result as $key => $occurrence){
	    			 
	    			switch($occurrence['user']){
	    				case "System": $fa = 'fa-pencil-square-o'; break;
	    				case "Manual": $fa = 'fa-pencil-square-o'; break;
	    				default: $fa = 'fa-user'; break;
	    			}
	    			
	    			$time = getTimeFromTimestamp($occurrence['created']);
	    			if(!isset($dateBr)){
	    				$dateBr = dateWeekFromTimeBr($occurrence['created']);
	    				$timeline .= "<li class='time-label'>
	    				<span class='label label-primary'> {$dateBr}</span>
	    				</li>";
	    			}else{
	    				$newDateBr = dateWeekFromTimeBr($occurrence['created']);
	    				if($dateBr != $newDateBr){
	    					$dateBr = $newDateBr;
	    					$timeline .= "<li class='time-label'>
	    					<span class='label label-primary'> {$dateBr}</span>
	    					</li>";
	    				}
	    			}
	    			$timeline .= "<li><i class='fa {$fa} bg-blue'></i>
	    			<div class='timeline-item'>
	    			<span class='time'><i class='fa fa-clock-o'></i> {$time}</span>
	    			<h3 class='timeline-header'>{$occurrence['user']} registrou...</h3>
	    			<div class='timeline-body'>{$occurrence['occurrence']}</div>";
	    			$timeline .= "</div></li>";
	    		
	    		}
    		}else{
    			$timeline = "<li><i class='fa {$fa} bg-blue'></i>
	    			<div class='timeline-item'><h3 class='timeline-header'>Sem ocorrências...</h3></div></li>"; 
    		}
    		echo "success|".$timeline;
    		
    		break;
    		
    	case 'get_order_items':
    		
    		$checkInValid = false;
    		$checkInTh = $checkIndTd  = $checkInTd1 = $checkInTr = $readonly = '';
    		
    		if(isset($_REQUEST['return_stock'])){
    			$checkInValid = true;
    			$readonly =  '';
    			$checkInTd1 = "<td></td>";
    			$checkInTh = "<th style='text-align:center;'>Estoques</th>";
    		}
    		
    		$count = 1 ;
    		$response = "<table class='table table-condensed'>
					<tr>
    					<th>#</th>
						<th>SKU</th>
						<th>Produto</th>
						<th style='text-align:center;'>Comprado</th>
						<th style='text-align:center;'>Devolvido</th>
    					{$checkInTh}
					</tr>";
    		$sql = "SELECT order_items.*, available_products.id as product_id FROM order_items 
    		LEFT JOIN available_products ON available_products.sku = order_items.sku
    		AND order_items.store_id = available_products.store_id
    		WHERE order_items.store_id = {$storeId} AND order_items.order_id = {$orderId}";
    		$res = $db->query($sql);
    		$result = $res->fetchAll(PDO::FETCH_ASSOC);
    		
    		foreach($result as $key => $rowItem){
    			$returnSplitKit = json_decode($rowItem['return_split_kit_json']);
    			if(empty($rowItem['product_id'])){
    				$errorLog = ['Sku não relacionado...'];
    				continue;
    			}
    			
    			
    			$queryItemAttributes = $db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
    					array($storeId, $orderId, $rowItems['id'])
    					);
    			$itemAttributes =  $queryItemAttributes->fetchAll(PDO::FETCH_ASSOC);
    			$htmlItemsAttributes = "<br>";
    			foreach($itemAttributes as $keyItemAttr =>$attr){
    				if(!empty($attr['Valor'])){
    					$htmlItemsAttributes .= "<small>{$attr['Nome']} {$attr['Valor']}</small>";
    				}
    			}
    			
    			if($checkInValid){
    				
    				$checkIndTd = "<td align='center'><div class='form-group check-in-form-group'>
									<select id='check_in' name='check_in' class='form-control input-sm check_in select2'>
										<option value=''></option>
										<option value='stock_available'>VENDAVEL -> Voltar no estoque para venda</option>
										<option value='stock_damaged'>AVARIADO -> Enviar para estoque de defeito</option>
									</select>
								</div></td>";
    			}
    			
    			
    			$sqlRelational = "SELECT product_relational.*, available_products.sku, 
            	available_products.title, available_products.color, available_products.variation
	            FROM `product_relational` 
	            LEFT JOIN available_products ON available_products.id = product_relational.product_relational_id 
    			AND product_relational.store_id = available_products.store_id
    			WHERE product_relational.store_id = {$storeId} AND product_relational.product_id = {$rowItem['product_id']} ";
    			$resRelational = $db->query($sqlRelational);
    			$relational = $resRelational->fetchAll(PDO::FETCH_ASSOC);
    			
    			if(!empty($relational[0]['product_id'])){
    				$rel = '';
    				$response .= "
    				<tr style='background-color:#ffeeba' id='{$rowItem['order_id']}'>
    					<td>{$count}</td>
	    				<td>{$rowItem['SKU']}</td>
	    				<td>{$rowItem['Nome']} {$htmlItemsAttributes}</td>
	    				<td align='center' >{$rowItem['Quantidade']}</td>
	    				<td align='center'></td>
	    				{$checkInTd1}
    				</tr>";
    				
    				foreach($relational as $i => $value){
    					$checkInReturned = '';
    					$qtyReturned = 0;
    					if(!empty($rowItem['return_split_kit_json'])){
    						$returnSplitKit = json_decode($rowItem['return_split_kit_json'], true);
    						$qtyReturned = $returnSplitKit[$value['product_relational_id']]['qty'];
    						$checkInReturned = $rowItem['return_stock'];
    					}
    			
    					
    					$checkIndTd = $selectedDamaged = $selectedAvailable = $selected = '';
    					if($checkInValid){
    							
    						
    						switch($checkInReturned){
    							case 'stock_available':
    								$selectedAvailable = 'selected';
    								break;
    							case 'stock_damaged':
    								$selectedDamaged = 'selected';
    								break;
    						}
    					
	    					$checkIndTd = "<td align='center'><div class='form-group check-in-form-group'>
	    						<select id='check_in' name='check_in' class='form-control input-sm check_in select2'>
	    						<option value=''></option>
	    						<option value='stock_available' {$selectedAvailable}>VENDAVEL -> Voltar no estoque para venda</option>
	    						<option value='stock_damaged' {$selectedDamaged}>AVARIADO -> Enviar para estoque de defeito</option>
	    						</select>
	    						</div>
	    					</td>";
    					}
    					
    					
    					$qtyOrdered = $value['qtd'] * $rowItem['Quantidade'];
    					$response .= "
    					<tr>
    						<td></td>
	    					<td>{$value['sku']}</td>
	    					<td>{$value['title']}<br><small>{$value['color']} {$value['variation']}</small></td>
	    					<td align='center' >{$qtyOrdered}</td>
	    					<td>
		    					<div class='form-group qty-form-group'>
		    						<input name='return-items' type='text'  class='return-items return-items-{$orderId} form-control input-sm' relational='T' {$readonly}
		    						item_id='{$rowItem['id']}' qty_ordered='{$qtyOrdered}' product_id='{$value['product_relational_id']}' value='{$qtyReturned}'>
		    					</div>
	    					</td>
	    					{$checkIndTd}
    					</tr>";
    				}
    				
    				
    			}else{
    				$checkIndTd = ''; 
    				if($checkInValid){
    					
	    				$selectedDamaged = $selectedAvailable = $selected = '';
	    				switch($rowItem['return_stock']){
	    					case 'stock_available':
	    							$selectedAvailable = 'selected';
	    						break;
	    					case 'stock_damaged':
	    							$selectedDamaged = 'selected';
	    						break;
	    				}
    				
	    				$checkIndTd = "<td align='center'><div class='form-group check-in-form-group'>
		    				<select id='check_in' name='check_in' class='form-control input-sm check_in select2'>
		    				<option value=''></option>
		    				<option value='stock_available' {$selectedAvailable}>VENDAVEL -> Voltar no estoque para venda</option>
		    				<option value='stock_damaged' {$selectedDamaged}>AVARIADO -> Enviar para estoque de defeito</option>
		    				</select>
		    				</div></td>";
    				}
    				
	    			$response .= "
	    			<tr id='{$orderId}'>
	    				<td>{$count}</td>
	    				<td>{$rowItem['SKU']}</td>
	    				<td>{$rowItem['Nome']} {$htmlItemsAttributes}</td>
	    				<td align='center' >{$rowItem['Quantidade']}</td>
	    				<td align='center'>
	    					<div class='form-group  qty-form-group'>
	    						<input name='return-items' type='text'  class='return-items return-items-{$orderId} form-control input-sm' relational='F' {$readonly}
	    						item_id='{$rowItem['id']}' product_id='{$rowItem['product_id']}' qty_ordered='{$rowItem['Quantidade']}' value='{$rowItem['return_qty']}'>
	    					</div>
	    				</td>
	    				{$checkIndTd}
	    			</tr>";
    			
    			}
    			$count++;
    			
	    			
    		}
    		
    		$sql = "SELECT * FROM order_returns WHERE store_id = {$storeId} AND order_id = {$orderId}";
    		$res = $db->query($sql);
    		$result = $res->fetch(PDO::FETCH_ASSOC);
    		
    		if($checkInValid){
    			$response .= "<tr><td colspan='4'></td><td colspan='2'>
    					<button type='button' class='btn btn-block  btn-primary btn-sm pull-right register_returncheck_in' 
    					id='register_returncheck_in' return_id='{$result['id']}' pedido_id='{$result['pedido_id']}' order_id='{$result['order_id']}'
    					customer_id='{$result['customer_id']}'>Efetuar Entrada de Devolução</button>
    					</td></tr>";
    		}
    		  
    		$response .= "</table>"; 
    		

    		echo "success|{$response}|{$result['type_return']}|{$result['reasons']}|{$result['reverse_code']}|".dateBr($result['created'], '/')."|{$result['user']}|{$result['check_in']}|{$result['status']}";
    		
    		break;
    	
    	case 'check_in_item_order':
    		
    			$returnId = isset($_REQUEST['return_id']) && !empty($_REQUEST['return_id']) ? intval($_REQUEST['return_id']) : null ;
    			
    			$items = $_REQUEST['item'];
    			$relCount = 0;
    			foreach($items as $k => $item){
    		
    		
    				if($item['relational'] == 'T'){
    					 
    					$relational[$item['item_id']][] = $item;
    					 
    				}else{
    		
    					$queryUpdate = $db->update('order_items',
    							array('store_id', 'id'),
    							array($storeId, $item['item_id']),
    							array('return_id' => $returnId,
    									'return_qty' => $item['qty'], 
    									'return_stock' => $item['check_in']
    							));
    					
    					$relCount++;
    				}
    		
    			}
    			 
    			if(isset($relational)){
    		
    				foreach($relational as $itemId => $relValue){
    					$data = array();
    					$qtyReturnSum = $qtyOrderedSum = 0;
    					foreach ($relValue as $j => $valItem){
    						$qtyReturnSum += $valItem['qty'];
    						$qtyOrderedSum += $valItem['qty_ordered'];
    						$data[$valItem['product_id']] =  $valItem;
    						$queryUpdate = $db->update('order_items',
    							array('store_id', 'id'),
    							array($storeId, $itemId),
    							array('return_id' => $returnId,
    									'return_qty' => $qtyReturnSum,
    									'return_stock' => $valItem['return_stock'],
    									'return_split_kit_json' => json_encode($data, JSON_PRETTY_PRINT)
    							));
    						$relCount++;
    					
    					}
    					 
    				}
    		
    			}
    			if($relCount > 0 && !empty($returnId)){
	    			$db->update('order_returns',
	    					array('store_id', 'id'),
	    					array($storeId, $returnId),
	    					array('user_check_in' => $request,
	    						'checked' => date('Y-m-d H:s:i'),
	    						'status' => 'received'
	    					));
	    			 
	    			echo "success|Check in registrado com sucesso!";
	    			
    			}else{
    				echo "error|Erro ao registrar check in...";
    			}
    		
    		break;
    		
    	case'register_order_return':
    		
    		$returnsModel = new ReturnsModel($db);
    		
    		$returnsModel->store_id = $storeId;
    		$returnsModel->status = 'new';
    		$returnsModel->created =  date('Y-m-d H:i:s');
    		
    		foreach($_REQUEST as $property => $param){
    			
    			if(!empty($param)){
    				if(property_exists($returnsModel, $property)){
    					$returnsModel->{$property} = $param;
    				}
    			}
    			
    		}
    		if(!empty($returnsModel->order_id)){
    			$returnsModel->Save();
    		}
    		if(!empty($returnsModel->id)){
    		
	    		$occurrence['store_id'] 	= $storeId;
	    		$occurrence['order_id'] 	= $returnsModel->order_id;
	    		$occurrence['pedido_id'] 	= $returnsModel->pedido_id;
	    		$occurrence['customer_id'] 	= $_REQUEST['customer_id'];
	    		$occurrence['occurrence'] 	= "Motivo: {$returnsModel->reasons} <br> {$_REQUEST['informations']}";
	    		$occurrence['user'] 		= $returnsModel->user;
	    		$occurrence['created'] 		= $returnsModel->created;
	    		$occurrence['type'] 		= $returnsModel->type_return;
	    		$query = $db->insert('order_occurrence', $occurrence);
	    		$items = $_REQUEST['item'];
	    		foreach($items as $k => $item){
	    			
	    			
	    			if($item['relational'] == 'T'){
	    				
	    				$relational[$item['item_id']][] = $item;
	    				
	    			}else{
	    			
		    			$queryUpdate = $db->update('order_items', 
		    					array('store_id', 'id'), 
		    					array($storeId, $item['item_id']), 
		    					array('return_id' => $returnsModel->id,'return_qty' => $item['qty'])
		    				);
	    			}
	    			
	    		}
	    		
	    		if(isset($relational)){
	    			
	    			foreach($relational as $itemId => $relValue){
	    				$data = array();
	    				$qtyReturnSum = $qtyOrderedSum = 0;
	    				foreach ($relValue as $j => $valItem){
	    					$qtyReturnSum += $valItem['qty'];
	    					$qtyOrderedSum += $valItem['qty_ordered'];
	    					$data[$valItem['product_id']] =  $valItem;
	    				}
	    				
		    			$queryUpdate = $db->update('order_items',
	    					array('store_id', 'id'),
	    					array($storeId, $itemId),
	    					array('return_id' => $returnsModel->id,
	    						'return_qty' => $qtyReturnSum, 
	    						'return_split_kit_json' => json_encode($data, JSON_PRETTY_PRINT)
	    					));
		    			
	    			}
	    			
	    		}
	    		
	    		echo "success|Registro criado com sucesso!";
	    		
	    		pre($relational);
    		}
    		
    		break;
    	

    	case "delete_order":
    	
    		$userId = isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null ;
    	
    		if(!isset($userId)){
    			echo "error|Você não tem permissão para excluír o produto...";
    			exit;
    		}
    		if(verifyActionPermisssion($db, $storeId, 'orders', 'delete', $userId)){
    			$orderIds = is_array($orderId) ? $orderId : array($orderId) ;
    			if(!empty ( $orderIds )){
    				foreach($orderIds as $i => $orderId){
    					$sql = "DELETE FROM `orders` WHERE store_id = {$storeId} AND id = {$orderId}";
    					$db->query($sql);
    					$sql = "DELETE FROM `order_items` WHERE store_id = {$storeId} AND order_id = {$orderId}";
    					$db->query($sql);
    					$sql = "DELETE FROM `order_item_attributes` WHERE store_id = {$storeId} AND order_id = {$orderId}";
    					$db->query($sql);
    					$sql = "DELETE FROM `order_payments` WHERE store_id = {$storeId} AND order_id = {$orderId}";
    					$db->query($sql);
    					echo "success|{$orderId}";
    				}
    			}else{
    				echo "warning|Você não tem permissão para excluír o pedido...";
    				exit;
    			}
    		}
    		break;
    		
    	case "cancel_order":
    			 
    		$userId = isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null ;
    			 
    		if(!isset($userId)){
    			echo "error|Você não tem permissão para cancelar o produto...";
    			exit;
    		}
    		if(verifyActionPermisssion($db, $storeId, 'orders', 'update', $userId)){
    			$orderIds = is_array($orderId) ? $orderId : array($orderId) ;
    			if(!empty ( $orderIds )){
    				foreach($orderIds as $i => $orderId){
    					$sql = "UPDATE `orders` SET Status = 'canceled' WHERE store_id = {$storeId} AND id = {$orderId}";
    					$db->query($sql);
    					$sql = "UPDATE `order_payments` SET Situacao = 'canceled' WHERE store_id = {$storeId} AND order_id = {$orderId}";
    					$db->query($sql);
    					echo "success|{$orderId}";
    				}
    			}else{
    				echo "warning|Você não tem permissão para cancelar o pedido...";
    				exit;
    			}
    		}
    		break;
        
    	case "approve_order":
    	    
    	    $userId = isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null ;
    	    
    	    if(!isset($userId)){
    	        echo "error|Você não tem permissão para cancelar o produto...";
    	        exit;
    	    }
//     	    if(verifyActionPermisssion($db, $storeId, 'orders', 'update', $userId)){
    	        $orderIds = is_array($orderId) ? $orderId : array($orderId) ;
    	        if(!empty ( $orderIds )){
    	            foreach($orderIds as $i => $orderId){
    	               echo  $sql = "UPDATE `orders` SET Status = 'paid' WHERE store_id = {$storeId} AND id = {$orderId}";
    	                $db->query($sql);
    	             echo    $sql = "UPDATE `order_payments` SET Situacao = 'paid' WHERE store_id = {$storeId} AND order_id = {$orderId}";
    	                $db->query($sql);
    	                echo "success|{$orderId}";
    	            }
    	        }else{
    	            echo "warning|Você não tem permissão para aprovar o pedido...";
    	            exit;
    	        }
//     	    }
    	    break;
            
        case "update_order_print":
            
            $orderId = $_POST['order_id'];
            
            if(!empty($orderId)){
                
                $sqlVerify = "SELECT id FROM orders WHERE store_id = {$storeId} AND id = {$orderId}";
                $queryVerify = $db->query($sqlVerify);
                $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                if(!empty($resVerify['id'])){
                    $query = $db->update('orders', 'id', $resVerify['id'], array('printed'  => "T"));
                }
                
                echo "success|{$resVerify['id']}";
                
            }
            
            break;
            
        case "close-picking":
            
            $pickingId = $_POST['picking_id'];
            
            if(!empty($pickingId)){
                
                
                $sqlVerify = "SELECT * FROM picking WHERE store_id = {$storeId} AND id = {$pickingId}";
                $queryVerify = $db->query($sqlVerify);
                $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                if($resVerify['status'] != 'closed'){
                    if(!empty($resVerify['id'])){
                        $created = date("Y-d-m H:i:s");
                        $query = $db->update('picking', 'id', $resVerify['id'], array(
                            'status'  => "closed",
                            'closed'  => date("Y-m-d H:i:s")
                        ));
                        
                    }
                }
                
                echo "success|{$resVerify['id']}";
            }
            
            break;
            
        case "remove-picking" :
            $user = $_POST['user'];
            $pickingId = $_POST['picking_id'];
            
            $sqlStatus = "SELECT status FROM `picking`  WHERE store_id = {$storeId} AND id = {$pickingId}";
            $queryStatus = $db->query( $sqlStatus );
            $resStatus = $queryStatus->fetch(PDO::FETCH_ASSOC);
//             pre($resStatus);die;
            $status = $resStatus['status'];
            
            if($status != 'closed'){
                
                if(!empty($pickingId)){
                    
                    $sqlDeleteP = "DELETE FROM picking WHERE store_id = {$storeId} AND id = {$pickingId} ";
                    $queryDeleteP = $db->query($sqlDeleteP);
                    
                    $sqlDeletePP = "DELETE FROM `picking_products`  WHERE store_id = {$storeId} AND picking_id = {$pickingId}";
                    $queryStatusPP = $db->query( $sqlDeletePP );
                    
                    $sqlDeletePPO = "DELETE FROM `picking_product_orders`  WHERE store_id = {$storeId} AND picking_id = {$pickingId}";
                    $queryStatusPPO = $db->query( $sqlDeletePPO );
                    
                    echo "success|{$pickingId}";
                    
                }
                
            }else{
                echo "error| Lista de separação fechada!";
            }
            break;
        
        case "remove-picking-product-order" :
            
            $pickingProductId = $_POST['picking_product_id'];
            $orderId = $_POST['order_id'];
            $pickingId = $_POST['picking_id'];
   
                
                $sqlVerifyOrder = "SELECT * FROM picking_product_orders WHERE store_id = {$storeId} AND
                    picking_id = {$pickingId} AND order_id = {$orderId} AND picking_product_id = {$pickingProductId}";
                $queryVerifyOrder = $db->query($sqlVerifyOrder);
                $resVerifyOrder = $queryVerifyOrder->fetch(PDO::FETCH_ASSOC);
                if(!empty($resVerifyOrder['id'])){
                    
                
                    $sqlDelete = "DELETE FROM picking_product_orders WHERE store_id = {$storeId} AND picking_id = {$resVerifyOrder['picking_id']} 
                    AND order_id = {$resVerifyOrder['order_id']} AND picking_product_id = {$resVerifyOrder['picking_product_id']}";
                    $queryDelete = $db->query($sqlDelete);
                    
                    if($queryDelete){
                        
                        $sqlVerify = "SELECT id, quantity FROM picking_products WHERE store_id = {$storeId} AND 
                        picking_id = {$resVerifyOrder['picking_id']} AND id = {$resVerifyOrder['picking_product_id']}";
                        $queryVerify = $db->query($sqlVerify);
                        $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                        
                        if($resVerify['quantity'] > $resVerifyOrder['quantity']){
                            
                            $newQty = $resVerify['quantity'] - $resVerifyOrder['quantity'];
                            if( $newQty > 0){
                                $queryProduct = $db->update('picking_products',
                                    array('store_id', 'id'),
                                    array($storeId, $resVerify['id']),
                                    array('quantity'  => $newQty,
                                        'updated'  => date("Y-m-d H:i:s")
                                    ));
                            }else{
                                $remove = true;   
                            }
                            
                            
                        }
                        
                        if($resVerify['quantity'] == $resVerifyOrder['quantity'] OR isset($remove)){
                            
                            $sqlDeleteProduct = "DELETE FROM picking_products WHERE store_id = {$storeId} AND id = {$resVerifyOrder['picking_product_id']}";
                            $queryDeleteProduct = $db->query($sqlDeleteProduct);
                            if($queryDeleteProduct){
                                echo "reload|";
                            }
                            
                        }else{
                            echo "success|{$newQty}";
                        }
                    }
                    
                }
                
                
            break;
        
        case "handling-product-packing-in":
            
            $PedidoId = trim($_REQUEST['pedido_id']);
            $pickingId = trim($_REQUEST['picking_id']);
            $picker = trim($_REQUEST['picker']);
            $user = trim($_REQUEST['user']);
            $return = true;
            
            $alowedStatus = array('invoiced');
            
            $sqlVerify = "SELECT orders.*, order_items.order_id , order_items.PedidoId, order_items.SKU, order_items.Nome, order_items.Quantidade  FROM orders
            LEFT JOIN order_items ON orders.id  = order_items.order_id  WHERE orders.store_id = {$storeId} AND orders.PedidoId LIKE '{$PedidoId}'";
            $queryOrderItems = $db->query($sqlVerify);
            $orderItems = $queryOrderItems->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($orderItems as $key => $item){
                
                
                if( $item['logistic_type'] == 'fulfillment'){
                    
                    
                    echo "error|A logistica fulfillment não permite coleta";die;
                    
                    
                }
//                 if(in_array( $item['Status'], $alowedStatus)){
                if( $item['logistic_type'] == 'invoiced'){
                    
                    echo "error|A situação do pedido não permite coleta {$item['Status']}";die;
                    
                    
                }
                
                $selectQtd = "SELECT id, sku, ean,title, color, variation_type, variation, weight, height, width, length
                                FROM `available_products` WHERE store_id = {$storeId} AND `sku` LIKE '{$item['SKU']}'";
                $queryQtd = $db->query($selectQtd);
                $resStockPrice = $queryQtd->fetch(PDO::FETCH_ASSOC);
                
                
                if($pickingId == 'new'){
                    $return = false;
                    
                    $sqlVerifyPicking = "SELECT * FROM picking WHERE store_id = {$storeId} AND status LIKE 'new' AND picker LIKE '{$picker}'";
                    $queryVerifyPicking = $db->query($sqlVerifyPicking);
                    $resVerifyPicking = $queryVerifyPicking->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($resVerifyPicking['id'])){
                        
                        $pickingId = $resVerifyPicking['id'];
                    }else{
                        $queryInsertPicking = $db->insert('picking', array(
                            'store_id' => $storeId,
                            'picker' => $picker,
                            'status' => 'new',
                            'created' => date("Y-m-d H:i:s"),
                            'user' => $user
                            
                        ));
                        if($queryInsertPicking){
                            
                            $pickingId = $db->last_id;
                            
                        }else{
                            echo "erro|";
                        }
                        
                    }
                    
                    
                }
                
                
  
                
                
                $sqlVerify = "SELECT * FROM picking_products WHERE store_id = {$storeId} AND picking_id = {$pickingId} AND sku LIKE '{$item['SKU']}'";
                $queryVerify = $db->query($sqlVerify);
                $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                
                $updated = $created = date("Y-m-d H:i:s");
                
                if(empty($resVerify['id'])){
                    
                    
                    $sqlVerifyOrdersPickings = "SELECT picking_product_orders.picking_id FROM picking_product_orders
                    WHERE store_id = {$storeId} AND sku LIKE '{$item['SKU']}' AND order_id = {$item['order_id']}";
                    $queryVerifyOrdersPickings = $db->query($sqlVerifyOrdersPickings);
                    $resVerifyOrdersPickings = $queryVerifyOrdersPickings->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($resVerifyOrdersPickings['picking_id'])){
                        
                        echo "error|Produto do pedido ja conta em uma seraparação código {$resVerifyOrdersPickings['picking_id']}";die;
                        
                    }
                    
                    
                    
                    $queryProduct = $db->insert('picking_products', array(
                        'store_id'  => $storeId,
                        'picking_id' => $pickingId,
                        'product_id'  => $resStockPrice['id'],
                        'sku'  => $item['SKU'],
                        'ean'  => $resStockPrice['ean'],
                        'quantity'  => $item['Quantidade'],
                        'picker'  => $picker,
                        'created'  => $created,
                        'updated'  => $updated
                    ));
                    
                    $pickingProductId = $db->last_id;
                    
                    if(!empty($pickingProductId)){
                        
                        $queryOrder = $db->insert('picking_product_orders', array(
                            'store_id'  => $storeId,
                            'picking_id' => $pickingId,
                            'picking_product_id' => $pickingProductId,
                            'order_id' => $item['order_id'],
                            'PedidoId' => $PedidoId,
                            'product_id'  => $resStockPrice['id'],
                            'sku'  => $item['SKU'],
                            'short_description'  => $item['Nome'],
                            'marketplace'  => $item['Marketplace'],
                            'quantity'  => $item['Quantidade'],
                            'created'  => date("Y-m-d H:i:s"),
                            'status'  => 'picking_up'
                            
                        ));
                        
                        
                    }
                    if($queryOrder){
                        
                        
                        if(!isset($response)){
                            $response = "success|";
                        }
                        
                  
                        
                        $response .= "<tr id='{$pickingProductId}' class='{$pickingProductId}' bgcolor='#f4f4f4'>
                                        <td>{$item['SKU']}<br>{$resStockPrice['ean']}</td>
                                        <td>{$resStockPrice['title']}</td>
                                        <td>{$resStockPrice['brand']}</td>
                                        <td>{$resStockPrice['color']}/{$resStockPrice['variation']}</td>
                                        <td>Peso: {$resStockPrice['weight']}<br>Medidas: {$resStockPrice['height']} x {$resStockPrice['width']} x {$resStockPrice['length']}</td>
                                        <td id='qty-{$pickingProductId}'><strong>{$item['Quantidade']}</strong></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan='4'>
                                            <table class='table {$pickingProductId}'>
                                                <tr>
                                                    <td></td>
                                    		        <td>{$PedidoId}</td>
                                                    <td>{$item['Nome']}</td>
                                                    <td>{$item['Marketplace']}</td>
                                                    <td>{$item['Quantidade']}</td>
                                                    <td>picking_up</td>
                                                    <td>
                                                        <a class=' remove-product pull-right danger'
                                                            onclick=\"javascript:removePickingProduct(this, '".HOME_URI."', {$pickingId}, '{$pickingProductId}', '{$item['order_id']}', '{$PedidoId}' );\" >
                                                            <i class='fa fa-remove'></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td colspan='2'></td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan='7'></td>
                                    </tr>";
                        
                        
                        
                    }else{
                        pre($queryOrder);
                    }
                    
                }
                
                if(!empty($resVerify['id'])){
                    
                    $sqlVerifyOrder = "SELECT * FROM picking_product_orders WHERE store_id = {$storeId} AND
                        picking_id = {$pickingId} AND order_id = {$item['order_id']} AND sku LIKE '{$item['SKU']}'";
                    $queryVerifyOrder = $db->query($sqlVerifyOrder);
                    $resVerifyOrder = $queryVerifyOrder->fetch(PDO::FETCH_ASSOC);
                    if(empty($resVerifyOrder['id'])){
                        $qtyPicking = $resVerify['quantity'] > 0 ? $resVerify['quantity'] + $item['Quantidade'] : 1 ;
                        $queryProduct = $db->update('picking_products',
                            array('store_id', 'id'),
                            array($storeId, $resVerify['id']),
                            array('quantity'  => $qtyPicking,
                                'updated'  => $updated
                            ));
                        if($queryProduct){
                            
                            $queryUpdateOrder = $db->insert('picking_product_orders', array(
                                'store_id'  => $storeId,
                                'picking_id' => $pickingId,
                                'picking_product_id' => $resVerify['id'],
                                'order_id' => $item['order_id'],
                                'PedidoId' => $PedidoId,
                                'product_id'  => $resStockPrice['id'],
                                'sku'  => $item['SKU'],
                                'short_description'  => $item['Nome'],
                                'marketplace'  => $item['Marketplace'],
                                'quantity'  => $item['Quantidade'],
                                'created'  => $created,
                                'status'  => 'picking_up'
                            ));
                            
                            if($queryUpdateOrder){
                                $responseUpdate = "reload|";
                            }else{
                                pre($queryUpdateOrder);
                            }
                            
                        }
                        
                    }
                    
                }
                
                
            }
            
            if($return){
                if(isset($responseUpdate)){
                    echo $responseUpdate;die;
                }
                
                if(!empty($response)){
                    echo $response;
                }
            }
            break;
        
        case "register_fiscal_data":
            
            if(isset($orderId)){
            
                $data = array();
                
                $PedidoId = isset($_REQUEST["pedido_id"]) && $_REQUEST["pedido_id"] != "" ? $_REQUEST["pedido_id"] : '' ;
                $data['shipping_id'] = isset($_REQUEST["shipping_id"]) && $_REQUEST["shipping_id"] != "" ? $_REQUEST["shipping_id"] : '' ;
                $data['shipping_cost'] = isset($_REQUEST["shipping_cost"]) && $_REQUEST["shipping_cost"] != "" ? $_REQUEST["shipping_cost"] : '' ;
                $data['id_nota_saida'] = isset($_REQUEST["id_nota_saida"]) && $_REQUEST["id_nota_saida"] != "" ? $_REQUEST["id_nota_saida"] : '' ;
                $data['fiscal_key'] = isset($_REQUEST["fiscal_key"]) && $_REQUEST["fiscal_key"] != "" ? $_REQUEST["fiscal_key"] : '' ;
                $set = '';
                foreach($data as $key => $value){
                    if(!empty($value)){
                        switch($key){
                            case "shipping_id": $set .= " {$key} = '".trim($value)."',"; break;
                            case "shipping_cost": $set .= " FreteCusto = '".number_format(trim($value), 2, '.', '')."',"; break;
                            case "id_nota_saida": $set .= " {$key} = '".trim($value)."',"; break;
                            case "fiscal_key": $set .= " {$key} = '".trim($value)."', status = 'invoiced',"; break;
                        }
                    }
                }
                $set = substr($set, 0,-1);
                if(!empty($set)){
                    $sql = "UPDATE orders SET $set WHERE store_id = {$storeId} AND id = {$orderId}";
                    $query = $db->query($sql);
                    if($query){
                        echo "success|error|Erro ao atualiza dados do pedido {$PedidoId}";
                    }else{
                        echo "error|Erro ao atualiza dados do pedido {$PedidoId}";
                    }
                }else{
                    return "erro|There are empty fields. Data has not been sent.";
                }
            
            }
            
            break;
            
    }
    
    
}