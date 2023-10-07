<?php
header("Content-Type: text/html; charset=utf-8");
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", false);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/..//Class/class-PgConnection.php';
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
    $pg = new PgConnection($db, $storeId);
    
    switch($action){
	    
	   case "import_order_document": 
	       
	       $syncId =  logSyncStart($db, $storeId, "Sysemp", $action, "Importação XML Nota Fiscal.", $request);
	       $exported = 0;
	       
	       require_once $path .'/../../Sysemp/Class/class-PgConnection.php';
	       
	       $pg = new PgConnection($db, $storeId);
	       
	       $ordersModel = new OrdersModel($db);
	       $ordersModel->store_id = $storeId;
	       
	       $orderIds = isset($_REQUEST["order_id"])  ? $_REQUEST["order_id"] : NULL ;
	       
	       if(!isset($orderIds)){
	           $orderIds = array('all');
	       }else{
	           $orderIds = is_array($orderIds) ? $orderIds : array($orderIds) ;
	       }
	       
	       $dateFrom =  date("Y-m-d H:i:s", strtotime("-30 days", strtotime("now")));

	       if($request == 'Manual'){
	           $dateFrom =  date("Y-m-d H:i:s", strtotime("-15 days", strtotime("now")));
	       }
	       
	       foreach($orderIds as $i => $orderId){
	           
               $sql = "SELECT * FROM orders WHERE store_id = {$storeId}  AND DataPedido >= '{$dateFrom}'";
               
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
            	                   
            	                   if(empty($order['fiscal_key']) && !empty($nfe)){
//             	                       pre($order['Status']);
            	                       $enabledStatusEdit = $ordersModel->getEnabledStatusEditable($order['Status']);
//                 	                   pre($enabledStatusEdit);
            	                       if(in_array('invoiced', $enabledStatusEdit) ) {
//             	                           pre(array('fiscal_key' => $nfe,
//             	                               'Status' => 'invoiced',
//             	                               'nf_serie' => $serie,
//             	                               'nota_numero' => $nNF,
//             	                               'nf_tipo' => $tipo,
//             	                               'nf_emissao' => $emissao,
//             	                               'nf_hora_emissao' => $hora_emissao,
//             	                               'nf_total' => $valor_total,
//             	                               'nf_info_fisco' => $infoFisco
//             	                           ));
            	                           
                    	                   $queryUpdate = $db->update('orders',
                	                       array('store_id', 'id'),
                	                       array($storeId, $order['id']),
                	                       array('fiscal_key' => $nfe,
                	                           'Status' => 'invoiced',
                	                           'nf_serie' => $serie,
                	                           'nota_numero' => $nNF,
                	                           'nf_tipo' => $tipo,
                	                           'nf_emissao' => $emissao,
                	                           'nf_hora_emissao' => $hora_emissao,
                	                           'nf_total' => $valor_total,
                	                           'nf_info_fisco' => $infoFisco
                	                       ));
                	                   }
            	                   }
            	                   
            	                   if(!empty($nfe)){
                	                   $sqlVerify = "SELECT chave FROM xml_nota_saida WHERE order_id = {$order['id']}";
                	                   $queryVerify = $db->query($sqlVerify);
                	                   $rowVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                	                   if(empty($rowVerify['chave'])){
                    	                   $queryNota = $db->insert('xml_nota_saida', array(
                    	                       'store_id' => $storeId,
                    	                       'order_id' => $order['id'],
                    	                       'PedidoId' => $order['PedidoId'],
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
                    	                   $exported++;
                	                   }
            	                   }
            	               }
                	       }
        	           }
    	           }
    	       }
	       }
	       echo "success|{$exported}";
	       logSyncEnd($db, $syncId, $exported);
	       
	   break;
		    
	}
}
    	

