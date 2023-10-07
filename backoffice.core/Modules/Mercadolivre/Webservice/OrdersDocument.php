<?php
// die;
// echo phpinfo();die;
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
header("Content-Type: text/html; charset=utf-8");

$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Meli.php';
require_once $path .'/../Class/class-REST.php';
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
    $moduleConfig = getModuleConfig($db, $storeId, 2);
    require_once $path .'/verifyToken.php';
	switch($action){
	    
	    
	   case 'invoice':  
	       
	       $sql = "SELECT * FROM xml_nota_saida WHERE store_id = {$storeId}";
	       $query = $db->query($sql);
	       
	       $res = $query->fetch(PDO::FETCH_ASSOC);
	       
	       $xml = json_decode($res['xml']);
	       
	       
	       pre($xml);
	       
	       
	       break;
	    
	   case "Sysemp": 
	       
	       $syncId =  logSyncStart($db, $storeId, "Mercadolivre", $action, "Exportação XML Nota Fiscal.", $request);
	       $exported = 0;
	       
	       require_once $path .'/../../Sysemp/Class/class-PgConnection.php';
	       
	       $pg = new PgConnection($db, $storeId);
	       
	       $orderIds = isset($_REQUEST["order_id"])  ? $_REQUEST["order_id"] : NULL ;
	       
	       if(!isset($orderIds)){
	           $orderIds = array('all');
	       }else{
	           $orderIds = is_array($orderIds) ? $orderIds : array($orderIds) ;
	       }
	       
	       $dateFrom =  date("Y-m-d H:i:s", strtotime("-3 days", strtotime("now")));
	       if($request == 'Manual'){
	           $dateFrom =  date("Y-m-d H:i:s", strtotime("-1 days", strtotime("now")));
	       }
	       
	       foreach($orderIds as $i => $orderId){
	           
               $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND Status NOT IN ('pending', 'shipped', 'delivered', 'canceled') AND DataPedido >= '{$dateFrom}' AND Marketplace LIKE 'Mercadolivre'  ";
               
               if($orderId != 'all'){
	               if(!empty($orderId)){
	                   $sql = "SELECT * FROM orders WHERE store_id = {$storeId} AND id = {$orderId}";
	               }
	           }
    	       
    	       $query = $db->query($sql);
    	       $orders = $query->fetchAll(PDO::FETCH_ASSOC);
    	       foreach($orders as $key => $order){
    
        	       	if(isset($order['id_nota_saida'])){
        	           $sql = "SELECT * FROM nota_saida WHERE id_nota_saida = {$order['id_nota_saida']}";
        	           $query = $pg->query($sql);
        	           $rowFatura = $query->fetch(PDO::FETCH_ASSOC);
       
        	           $idNotaSaidaFatura = $rowFatura['id_nota_saida_fatura'] > 0 ? $rowFatura['id_nota_saida_fatura'] : $order['id_nota_saida'] ;
        	           if(isset($idNotaSaidaFatura)){
        	               
        	               $sql = "SELECT encode(arquivo,  'escape') as arquivo FROM nota_saida_xml WHERE id_nota_saida = '{$idNotaSaidaFatura}'";
                	       $query = $pg->query($sql);
                	       $row = $query->fetch(PDO::FETCH_ASSOC);
                	       if(isset($row['arquivo'])){
                	           
                	           if(!empty($order['shipping_id'])){
                	           	
                	               $result = $meli->postXml ( "/shipments/{$order['shipping_id']}/invoice_data", $row['arquivo'], 
                	               array (
                    	               'access_token' => $resMlConfig ['access_token'],
                	                   'siteId' => 'MLB'
                    	           ) );
                	               if($result['httpCode'] == 201){
                	                   $exported++;
                	                   $sqlUpdate = "UPDATE orders SET status = 'invoiced' WHERE store_id = {$storeId} AND id = {$order['id']}";
                	                   $db->query($sqlUpdate);
                	               }
                	               
                	               $domXml = simplexml_load_string($row['arquivo']);
                	               
                	               $nfe = trim($domXml->NFe->infNFe["Id"]."");
                	               
                	               if(!empty($nfe)){
                	                   
                	                   $nfe = str_replace('NFe', '', $nfe);
                	                   
                	                   $jsonNfe = json_encode($domXml->NFe);
                	                   
                	                   $serie = $domXml->NFe->infNFe->ide->serie."";
                	                   
                	                   $nNF = $domXml->NFe->infNFe->ide->nNF."";
                	                   
                	                   $tipo = $domXml->NFe->infNFe->ide->tpNF."";
                	                   
                	                   $dhEmissao = $domXml->NFe->infNFe->ide->dhEmi."";
                	                   
                	                   $dhEmissao = explode("T", $dhEmissao);
                	                   
                	                   $emissao = $dhEmissao[0];
                	                   
                	                   $parts = explode('-', $dhEmissao[1]);
                	                   
                	                   $hora_emissao =$parts[0];
                	                   
                	                   $valor_total = $domXml->NFe->infNFe->total->ICMSTot->vNF."";
                	                   
                	                   $infoFisco = $domXml->NFe->infNFe->infAdic->infAdFisco."";
                	                   
                	                   $queryUpdate = $db->update('orders',
                	                       array('store_id', 'id'),
                	                       array($storeId, $order['id']),
                	                       array('fiscal_key' => $nfe,
                	                           'nf_serie' => $serie,
                	                           'nota_numero' => $nNF,
                	                           'nf_tipo' => $tipo,
                	                           'nf_emissao' => $emissao,
                	                           'nf_hora_emissao' => $hora_emissao,
                	                           'nf_total' => $valor_total,
                	                           'nf_info_fisco' => $infoFisco
                	                       ));
                	                   
                	                   //         	                   		echo "{$order['id_nota_saida']} - OrderId: {$order['id']} -  PedidoId: {$order['PedidoId']} - DataPedido: {$order['DataPedido']} - Nome: {$order['Nome']} - {$order['Status']}<br>";
                	                   $queryNota = $db->insert('xml_nota_saida', array(
                	                       'store_id' => $storeId,
                	                       'order_id' => $order['id'],
                	                       'chave' => $nfe,
                	                       'id_nota_saida' => $order['id_nota_saida'],
                	                       'nota_numero' => $nNF,
                	                       'xml' => $jsonNfe,
                	                       'serie' => $serie,
                	                       'tipo' => $tipo,
                	                       'emissao' => $emissao,
                	                       'hora_emissao' => $parts[0],
                	                       'valor_total' => $valor_total
                	                   ));
                	               }
                	               
           
                	               echo "error|{$result['body']->message}";
                	               
                	               
                	           }else{
                	               
                	           	   echo "error|Cdigo do envio não localizado {$order['shipping_id']}";
                	           	
                	           }
                	           
                	       }else{
                	           echo "error|Nota fiscal não localizada para o pedido {$order['id_nota_saida']} - OrderId: {$order['id']} -  PedidoId: {$order['PedidoId']} - DataPedido: {$order['DataPedido']} - Nome: {$order['Nome']} - {$order['Status']}<br>";
                	       }
        	           }else{
        	               echo "error|Fatura não existe {$order['id_nota_saida']}";
        	           }
    	           }
    	       }
	       }
	       
	       logSyncEnd($db, $syncId, $exported);
	       
	   break;
		    
	}
}
    	

