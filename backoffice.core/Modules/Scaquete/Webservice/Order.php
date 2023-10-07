<?php
// header("Content-Type: text/html; charset=utf-8");
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Orders/OrdersModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
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
    $host = "casebre.jelastic.saveincloud.net/11345:/opt/firebird/data/SPDDADOS.FDB";
    // $host = "191.243.199.81/11109:/opt/firebird/data/SPDDADOS.FDB";
    $username = "SYSDBA";
    $password = "07903113801";
     
    $dbh = ibase_connect($host, $username, $password);
    
	switch($action){


		case 'list_orders_exported':
			
// 			if(isset($_REQUEST['list'])){
				$sql = "SELECT * FROM PEDWEB ORDER BY PDATA DESC";
				$sth = ibase_query($dbh, $sql);
				while($orderExported = ibase_fetch_assoc($sth)){
			
					$sqlItem = "SELECT * FROM PEDWEBI WHERE IDVENDAWEB = {$orderExported['IDVENDAWEB']}";
					$sthPEDWEBI = ibase_query($dbh, $sqlItem);
					while($item = ibase_fetch_assoc($sthPEDWEBI)){
						$orderExported['items'][] = $item;
					}
					pre($orderExported);
				}
			
// 			}
			die;
			
			$sql = "SELECT PEDWEB.* , PEDWEBI. * FROM PEDWEB LEFT JOIN PEDWEBI ON PEDWEBI.IDVENDAWEB = PEDWEB.IDVENDAWEB";
			$sth = ibase_query($dbh, $sql);
			while($row = ibase_fetch_assoc($sth)){
				pre($row);
			}
			die;
			// 		[STATUSRETORNO] => 01-OK- em 03/08/2020 -  10:54:26
// 			Scaquete - Exportação de pedidos - 2 - System 12:40:01 ~ 12:40:02
// 			[STATUSRETORNO] => 01-OK- em 03/08/2020 -  13:58:33
			// 	    	$sql = "DELETE FROM PEDWEBI";
			// 	    	$sth = ibase_query($dbh, $sql);
			// 	    	$sql = "DELETE FROM PEDWEB";
			// 	    	$sth = ibase_query($dbh, $sql);
			// // 	    	die;
			break;
	    case "export_orders":
	    	
	    	
	    
	    	$syncId =  logSyncStart($db, $storeId, "Scaquete", $action, "Exportação de pedidos", $request);
	    	$ordersModel = new OrdersModel($db);
	    	
	    	$ordersModel->store_id = $storeId;
	    	$ordersModel->id = !empty($_REQUEST["order_id"])  ? $_REQUEST["order_id"] : NULL ;
	    	$orders = $ordersModel->ExportOrderDetails();
	    	$imported = 0;
	    	$customer = array();
// 	    	pre($orders);die;
	    	foreach($orders as $key => $order){
// 	    		pre($order);
	    		$sqlVerifyOrder = "SELECT * FROM PEDWEB WHERE IDVENDAWEB = {$order['id']} ";
	    		$resVerify = ibase_query($dbh, $sqlVerifyOrder);
	    		$orderVerify = ibase_fetch_assoc($resVerify);
		    	
		    	if(!isset($orderVerify['ID'])){
		    	
					$DOCUMENTO = getNumbers($order['customer']['CPFCNPJ']);
					$NOME = strtoupper(RemoveAcentos($order['Nome']));
					$NOME = str_replace("'", "", $NOME);
					$NOME = str_replace("’", "", $NOME);
					$NOME = str_replace("`", "", $NOME);
					$NOME = str_replace("'", "", $NOME);
					$EMAIL = trim($order['Email']);
					$FONE = trim($order['Telefone']);
					$BAIRRO = !empty($order['Bairro']) ? mb_strtoupper(RemoveAcentos($order['Bairro'], 'UTF-8')) : 'CENTRO';
					$CEP = trim($order['Cep']);
					$CIDADE = $order['Cidade'];
					$order['Complemento'];
					$PDATA = date('Y-m-d', strtotime($order['DataPedido']));
					$PHORA = date('H:i:s', strtotime($order['DataPedido']));
					$ENDERECO = mb_strtoupper(RemoveAcentos($order['Endereco']), 'UTF-8');
					$ENDERECO = str_replace("'", "",$ENDERECO);
					$NR = trim($order['Numero']);
					$Subtotal = $order['Subtotal'];
					$ValorFrete = isset($order['ValorFrete']) && !empty($order['ValorFrete']) ? $order['ValorFrete'] : '0.00';
					$pedidoId = $order['PedidoId'];
					$ValorPedido = $order['ValorPedido'];
					$ValorCupomDesconto = isset($order['ValorCupomDesconto']) && !empty($order['ValorCupomDesconto']) ? $order['ValorCupomDesconto'] : '0.00';
					$marketplace = $order['Marketplace'];
					$city = RemoveAcentos($CIDADE);
					$city = strtoupper(strtolower(trim($city)));
					$sqlIbge = "SELECT * FROM CIDADEIBGE WHERE NOME LIKE '{$city}'";
					$queryIbge = ibase_query($dbh, $sqlIbge);
					$ibge = ibase_fetch_assoc($queryIbge);
					
					$obsOrder = json_encode(
							array(
								"PedidoId" => $pedidoId,
								"Marketplace" => $marketplace,
								"order_id" => $order['id'],
								"sent" => date('Y-m-d H:i:s')
							)
					);
					
					switch($marketplace){
						case 'Ecommerce': $vendedor = 90; break;
						case 'Mercadolivre': $vendedor = 91; break;
						case 'Amazon': $vendedor = 92; break;
						case 'Submarino': $vendedor = 93; break;
						case 'Lojas Americanas': $vendedor = 94; break;
						case 'Shoptime': $vendedor = 95; break;
						default : $vendedor = 90; break;
					}
					
					if(isset($ibge['CODIGO'])){
						$NOME = trim(substr($NOME,0, 50));
						$cidade = trim(substr($ibge['NOME'], 0, 20));
						$uf = trim($ibge['UF']);
						$ibgeCod = trim($ibge['CODIGO']);
							
				    	$sqlOrder = "INSERT INTO PEDWEB (DOCUMENTO, INSCEST, NOME, ENDERECO, NR, BAIRRO, UF, CIDADE, CODMUNIBGE, CEP,
					    			FONE, EMAIL, ENOME, EENDERECO, ENR, EBAIRRO, EUF, ECIDADE, ECEP, EFONE, PDATA, PHORA, PDATAPENTR,
					    			PVENDEDOR, TVENDABRUTA, TDESCONTO, TACRES, FVFRETE, VLIQUIDO, PAGAMENTO, CODPGTOPAR, FBANDEIRA,
					    			VPRENTRA, OBSERVACAO1, OBSERVACAO2, IDVENDAWEB, STATUSRETORNO, NRNFE) VALUES (
					    			'{$DOCUMENTO}', NULL, '{$NOME}', '{$ENDERECO}', '{$NR}', '{$BAIRRO}', '{$uf}', '{$cidade}', {$ibgeCod}, '{$CEP}',
					    			'{$FONE}', '{$EMAIL}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{$PDATA}', '{$PHORA}','{$PDATA}', {$vendedor}, 
					    			{$ValorPedido}, {$ValorCupomDesconto}, 0, {$ValorFrete}, {$Subtotal}, 'CCC', 1, 'MEPA', {$ValorPedido}, '{$order['PedidoId']}',
					    			'{$obsOrder}', {$order['id']}, NULL, NULL);";
				    	$queryOrder = ibase_query($dbh, $sqlOrder);
				    	if(!$queryOrder){
				    		pre($order);
				    		pre($ibge);
				    		pre($sqlOrder);
				    		pre($queryOrder);
				    	}
				    	$vlrItem   = ($ValorPedido / count($order['items'] ));
				    	
			    		$itemCount = 1;
			    		
			    		foreach($order['items'] as $key2 => $item){
			    			
			    			$sku = getNumbers(trim($item['SKU']));
			    			
			    			$sql = "SELECT * FROM module_scaquete_products_tmp WHERE store_id = {$storeId} AND IDEST = {$sku}";
			    			$query = $db->query($sql);
			    			$resVerify = $query->fetch(PDO::FETCH_ASSOC);
			    			if(isset($resVerify['CODIGOEAN'])){
			    				$itemNome = mb_strtoupper(RemoveAcentos($item['Nome']), 'UTF-8');
			    				$itemNome = str_replace("'", "", $itemNome);
				    		  	$qty = $item['Quantidade'] > 0 ? $item['Quantidade'] : 1;
				    		  	$precoUnit = number_format($vlrItem / $qty, 3);
						    	$sqlItem = "INSERT INTO PEDWEBI (IDVENDAWEB, DOCCLI, PECODI, PEDESC, PEUNID, PEQUAN, PEVUNI, NRI) VALUES (
						    		{$order['id']}, '{$DOCUMENTO}', '{$resVerify['CODIGOEAN']}', '{$itemNome}', 'PC', {$qty}, {$precoUnit}, {$itemCount});";
						    	$queryItem = ibase_query($dbh, $sqlItem);
						    	if(!$queryItem){
						    		
						    		pre($item);
						    		pre($sqlItem);
						    		pre($queryItemyOrder);
						    	}
						    	$itemCount++;
			    			}else{
								$sqlUpdate = "UPDATE orders SET  error = 'PRODUTO {$order['SKU']} NAO LOCALIZADO ERP SCAQUETE' WHERE store_id = {$order['store_id']} AND id = {$order['id']}";
			 					$queryOrder = $db->query($sqlUpdate);
							}
							
			    		}
			    		
			    		$imported++;
			    		if($queryOrder && $queryItem){
				    		$sqlUpdate = "UPDATE orders SET sent = 'T' WHERE store_id = {$order['store_id']} AND id = {$order['id']}";
				    		$queryOrder = $db->query($sqlUpdate);
			    		}
			    		
			    	}else{
			    		$sqlUpdate = "UPDATE orders SET  error = 'ERRO AO EXPORTAR ERP SCAQUETE IBGE NAO LOCALIZADO PARA {$city}' WHERE store_id = {$order['store_id']} AND id = {$order['id']}";
			    		$queryOrder = $db->query($sqlUpdate);
			    	}
			    	
		    	}
			    	
		    }
		    
		    if(isset($_REQUEST['list'])){
		    	$sql = "SELECT * FROM PEDWEB";
		    	$sth = ibase_query($dbh, $sql);
		    	while($orderExported = ibase_fetch_assoc($sth)){
		    		
		    		$sqlItem = "SELECT * FROM PEDWEBI WHERE IDVENDAWEB = {$orderExported['IDVENDAWEB']}";
		    		$sthPEDWEBI = ibase_query($dbh, $sqlItem);
		    		while($item = ibase_fetch_assoc($sthPEDWEBI)){
		    			$orderExported['items'][] = $item;
		    		}
		    		pre($orderExported);
		    	}
	    	
		    }
		    
	    	logSyncEnd($db, $syncId, $imported);
	        
	        break;
	}
}
	        
// 	        echo x"INSERT INTO PEDWEB (ID, DOCUMENTO, INSCEST, NOME, ENDERECO, NR, BAIRRO, UF, CIDADE, CODMUNIBGE, CEP,
	// 		    			FONE, EMAIL, ENOME, EENDERECO, ENR, EBAIRRO, EUF, ECIDADE, ECEP, EFONE, PDATA, PHORA, PDATAPENTR,
	// 		    			PVENDEDOR, TVENDABRUTA, TDESCONTO, TACRES, FVFRETE, VLIQUIDO, PAGAMENTO, CODPGTOPAR, FBANDEIRA,
	// 		    			VPRENTRA, OBSERVACAO1, OBSERVACAO2, IDVENDAWEB, STATUSRETORNO, NRNFE) VALUES (
	        
	// 		    			1, '07903113801', NULL, 'MARIA DOS SANTOS', 'RUA X', '10', 'CENTRO', 'SP', 'GARCA', 3516705, '17400000',
	// 		    			'1434138684', 'maria@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '13:30:00',
	// 		    			'2020-06-01', 1, 1015.1, 15.1, 0, 20, 1020, 'CCC', 3, 'VISA', 340, 'OSERVACAO LIVRE 01',
	// 		    			'OBSERVACAO LIVRE 02', 1000, NULL, NULL);
	// 		    	COMMIT WORK;";
	        
	        // 		    	echo "INSERT INTO PEDWEBI (ID, IDVENDAWEB, DOCCLI, PECODI, PEDESC, PEUNID, PEQUAN, PEVUNI, NRI) VALUES (
	        // 		    			1, 1000, '07903113801', '7890000000121', 'VASO LISO', 'PC', 1, 500, 1);
	        // 				INSERT INTO PEDWEBI (ID, IDVENDAWEB, DOCCLI, PECODI, PEDESC, PEUNID, PEQUAN, PEVUNI, NRI) VALUES (
	        // 		    			2, 1000, '07903113801', '7890000021212', 'XICARA', 'JG', 2, 257.55, 2);
	        	
	        // 				COMMIT WORK;";

