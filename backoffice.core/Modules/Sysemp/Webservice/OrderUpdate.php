<?php
header("Content-Type: text/html; charset=utf-8");
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
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
	    
		case "export_orders":
// 		    $db->query("UPDATE orders SET sent = 'F' WHERE store_id = 4 AND id != 461");
// 		    $pg->query('DELETE FROM nota_saida WHERE id_nota_saida != 2649');
// 		    $pg->query('DELETE FROM nota_saida_itens WHERE id_nota_saida != 2649');
// 		    $pg->query('DELETE FROM nota_saida_formapgto WHERE id_nota_saida != 2649');
// 		    die;
		    $syncId =  logSyncStart($db, $storeId, "Sysemp", $action, "Atualização de pedidos", $request);
		    $ordersModel = new OrdersModel($db);
		    $ordersModel->store_id = $storeId;
		    $ordersModel->id = isset($_REQUEST["order_id"]) && !empty($_REQUEST["order_id"]) ? intval($_REQUEST["order_id"]) : NULL ;
		    $orders = $ordersModel->ExportAllOrderDetails();
		    $imported = 0;
// 		    pre($orders);
		    foreach($orders as $key => $order){
// 		        echo "success";die;
// 		        pre($order);die;
		        
		        $sqlVerify = "SELECT id_nota_saida FROM nota_saida WHERE id_pedido_vda_importado = '{$order['id']}' 
                AND marketplace_pedido = '{$order['PedidoId']}'";
		        $queryVerify = $pg->query($sqlVerify);
		        $notaSaidaVerify = $queryVerify->fetch(PDO::FETCH_OBJ);
		        $idNotaSaida = $notaSaidaVerify->id_nota_saida;
		        
		        if($idNotaSaida > 0){
		            
    		        $customer = $order['customer'];
    		        
    		        $cpfAuxiliar = $customer['TipoPessoa'] == 1 ? $customer['CPFCNPJ'] : '' ;
    
    		        
    		        switch ($order['Marketplace']) {
    		            case 'Mercadolivre': $canal = 1;break;
    		            case 'Ecommerce': $canal = 3;break;
    		            default : $canal = 3;break;
    		        }
    		        
    		        
    		        $idTransportadora = 2;// correios coronel galdino
    		        $sellerId = 1; // Sysplace
//     		        $customerId = exportCustomer($db, $pg, $storeId, $customer, $sellerId);

    		        $PrecoTotalProd = $order['Subtotal'];
    		        $desconto = $order['ValorCupomDesconto'];
    		        $valorPedido = $order['ValorPedido'];
    		        
    		        $cep = $customer['CEP'];
    		        $cidade = empty($customer['Cidade']) ? pg_escape_string(getCityException($customer['Cidade'])) : pg_escape_string(getCityException($order['Cidade']));
    		        $estado = utf8_decode(strtoupper(removeAcentosNew($customer['Estado'])));
    		        $endereco = utf8_decode(str_replace("'", "", strtoupper(removeAcentosNew($customer['Endereco']))));
    		        $complemento = utf8_decode(strtoupper(removeAcentosNew($customer['Complemento'])));
    		        
    		        $bairro = !empty($customer['Bairro']) ? utf8_decode(strtoupper(removeAcentosNew($customer['Bairro']))) : 'Bairro';
    		        
    		        $nome = utf8_decode(strtoupper(removeAcentosNew($customer['Nome'])));
    		        $nomeRecebedor = utf8_decode(strtoupper(removeAcentosNew(substr($order['Nome'], 0, 30))));
    		        
    		        $apelido = utf8_decode(strtoupper(removeAcentosNew($customer['Apelido'])));
    		        $serie = 1;
    		        
    		        
    		        if(!empty($cidade)){
    		            
    		            $codigoIbge =  getCodigoIbge($db, $pg, $cidade);
    		            $idUf = getCodigoUf($pg, $codigoIbge);
        		        $idNatOperacao = 1;
        		        $idEmpresa = 1;
        		        
        		        if($idUf != 35){
        		           $idNatOperacao = '77';
        		           $idEmpresa = 1;
        		        }
        		        
        		        $pedidoDataHora = explode(" ", $order['DataPedido']);
        		        $pedidoData = $pedidoDataHora[0];
        		        $pedidoHora = $pedidoDataHora[1];
        		        
        		        $competencia = utf8_decode(removeAcentosNew(getMesAnoCompetencia($pedidoData)));
        		       
        		        $valor_frete = $order['ValorFrete'];
        		        
        		        $sqlInfo  = "SELECT id_nota_saida, id_nat_operacao, entrega_codigoibge  FROM nota_saida WHERE id_nota_saida = '{$idNotaSaida}'";
        		        $queryInfo = $pg->query($sqlInfo);
        		        $notaSaidaInfo = $queryInfo->fetch(PDO::FETCH_OBJ);
        		        echo "{$idUf} - {$idNatOperacao} - {$notaSaidaInfo->id_nat_operacao} - {$codigoIbge} - {$notaSaidaInfo->entrega_codigoibge}";
        		        
        		        if(empty($notaSaidaInfo->entrega_codigoibge)){
        		        
        		          echo  $sqlUpdate = "UPDATE nota_saida SET id_nat_operacao = '{$idNatOperacao}',
                            entrega_cidade = '{$cidade}', 
                            entrega_codigoibge = '{$codigoIbge}'
                            WHERE id_nota_saida = '{$idNotaSaida}'";
//         		          $pg->query($sqlUpdate);
        		        
        		        }
        		        
        		        echo "<br>";
        		        
        		        
        		        
        		        
        		        
        		        
        		      
    		        }
		        
		        }
		    }
		    logSyncEnd($db, $syncId, $imported);
		    break;
		    
	}

}

