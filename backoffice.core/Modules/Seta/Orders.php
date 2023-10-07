<?php
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", false);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../../Class/class-DbConnection.php';
require_once $path .'/../../Class/class-MainModel.php';
require_once $path .'/../../Functions/global-functions.php';
require_once $path .'/../../Models/Orders/OrdersModel.php';
require_once $path .'/Class/class-PgConnection.php';
require_once $path .'/Class/class-UpdateAvailableProducts.php';
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
    $pg = new PgConnection($db, $storeId);
    
	switch($action){
	    
	    case 'verify_orders':
	        $sqlOrders = 'SELECT * FROM orders WHERE id_nota_saida IS NOT NULL AND store_id = 3';
	        $query = $db->query($sqlOrders);
	        $res = $query->fetchAll(PDO::FETCH_ASSOC);
	        foreach($res as $k => $order){
	            $dataHora = explode(" ", $order['DataPedido']);
	            if(strlen($dataHora[1]) > 5){
	                $difHora = 5 - strlen($dataHora[1]);
	                $dataHora[1] = substr($dataHora[1], 0, $difHora);
	            }
	            
	            $sqlCount = "SELECT * FROM vendas WHERE empresa = '97' AND data = '{$dataHora[0]}' AND hora = '{$dataHora[1]}'";
	            $queryCount = $pg->query($sqlCount);
	            $resCount = $queryCount->fetchAll(PDO::FETCH_ASSOC);
	            if(count($resCount) <= 1){
	                echo "continue <br><br>";
	                continue;
	            }
// 	            pre($resCount);
	            
	            
// 	            $sqlCount = "SELECT * FROM vendas WHERE empresa = '97' AND data = '{$dataHora[0]}' AND hora = '{$dataHora[1]}'";
// 	            $queryCount = $pg->query($sqlCount);
// 	            $resCount = $queryCount->fetchAll(PDO::FETCH_ASSOC);
	            
// 	            if(count($resCount) > 1){
// 	               pre($resCount);
// 	            }
	            
	        }
	        
	        break;
	   
	    case "update_orders":
	        $dateFrom =  date("Y-m-d", strtotime("-200 day", strtotime("now")));
// 	        $dateTo = date("Y-m-d");
// 	        $dateFrom = "2021-09-01";
	        $dateTo = "2021-10-04";
	        $dateTo = date("Y-m-d");
	        $sqlOrder = "SELECT * FROM orders WHERE store_id = '{$storeId}' AND sent = 'T' 
            AND Status IN ('invoiced','canceled', 'shipped', 'delivered') AND DataPedido BETWEEN '{$dateFrom}  00:00:00' AND '{$dateTo}  23:59:59'";
	        $queryOrder = $db->query($sqlOrder);
	        $resOrder = $queryOrder->fetchAll(PDO::FETCH_ASSOC);
	        foreach($resOrder as $k => $order){
	            if(!empty($order['id'])){
    	            
	                $codigo = $order['id_nota_saida'];
	                $numS = strlen($codigo);
	                switch($numS){
	                    case  1: $codigo = "0000000".$codigo; break;
	                    case  2: $codigo = "000000".$codigo; break;
	                    case  3: $codigo = "00000".$codigo; break;
	                    case  4: $codigo = "0000".$codigo; break;
	                    case  5: $codigo = "000".$codigo; break;
	                    case  6: $codigo = "00".$codigo; break;
	                    case  7: $codigo = "0".$codigo; break;
	                }
	                $sqlVendas = "SELECT * FROM VENDAS WHERE codigo = '{$codigo}'";
	                $queryVendas = $pg->query($sqlVendas);
	                $resVendas = $queryVendas->fetch(PDO::FETCH_ASSOC);
	                echo "<br>";
	                if(!empty($resVendas['codigo'])){
	                    
	                    $status = $order['Status'] == 'canceled' ? 'C' : 'S' ; 
    	                echo $updateOrder = "UPDATE vendas SET status = '{$status}' WHERE empresa = '97' AND codigo = '{$codigo}'";
    	                $queryOrder = $pg->query($updateOrder);
                        echo "<br>";
	                    if($queryOrder){
	                       echo  $sqlUpdateOrder = "UPDATE orders SET ERP = '{$resVendas['codigo']}' 
                                WHERE store_id = {$order['store_id']} AND id = {$order['id']}";
	                        $db->query($sqlUpdateOrder);
	                       echo "<br>";
	                    }
	                }
    	        }
	        }
	        break;
	        
		case "export_orders":
		    $syncId =  logSyncStart($db, $storeId, "Seta", $action, "Exportação de pedidos SETA ERP", $request);
		    $ordersModel = new OrdersModel($db);
		    $empresa = '97'; //ecommerce seta
		    $ordersModel->store_id = $storeId;
		    $ordersModel->id = isset($_REQUEST["order_id"])  ? intval($_REQUEST["order_id"]) : NULL ;
		    
		    $query = $pg->describe('pessoas');
		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
		    $columnsPessoas = array();
		    foreach($res as $k => $column){
		        $columnsPessoas[$column['column_name']] = $column;
		    }
		    $query = $pg->describe('vendas');
		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
		    $columnsVendas = array();
		    foreach($res as $k => $column){
		        $columnsVendas[$column['column_name']] = $column;
		    }
		    $query = $pg->describe('movimento');
		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
		    $columnsMovimento = array();
		    foreach($res as $k => $column){
		        $columnsMovimento[$column['column_name']] = $column;
		    }
		    
		    
		    $orders = $ordersModel->ExportOrderDetails();
		    $imported = 0;
		    $customer = array();
		    foreach($orders as $key => $order){
		        
		        if($order['Canal'] == 'Pedidodeteste'){
		            continue;
		        }
		        
// 		        pre($order);

		        if(!isset($order['id_nota_saida']) OR empty($order['id_nota_saida'])){
    		        $customer = $order['customer'];
    		        $items = $order['items'];
    		        
    		        $itemsSku = true;
    		        foreach($order['items'] as $j => $item){
    		            if(empty($item['SKU'])){
    		                $itemsSku = false;
    		            }
    		            $parts = explode('parent', $item['SKU']);
    		            if(isset($parts[1])){
    		                $itemsSku = false;
    		            }
    		        }
    		        if(!$itemsSku){
    		            continue;
    		        }
    		        
    		        $payments = $order['payments'];
    		        $nome = removeAcentos($customer['Nome']);
    		        $apelido = removeAcentosNew($customer['Apelido']);
    		        
    		        $pessoafj = 1;
    		        
//     		        pre($customer);
    		        
    		        switch(trim($customer['TipoPessoa'])){
    		            case 1:
    		                $pessoafj = 1;
    		                $nome = strtoupper(prepareString($nome));
    		                $cpfCnpj = trim($customer['CPFCNPJ']);
    		                $rgie = trim($customer['RGIE']);
    		                break;
    		                
    		            case 2:
    		                $pessoafj = 2;
    		                $nome = strtoupper(prepareString($nome));
    		                $cpfCnpj = trim($customer['CPFCNPJ']);
    		                $rgie = trim($customer['RGIE']);
    		                break;
    		        }
    		        
    		        $idCliente = existCpfCnpj($pg, $cpfCnpj);
                    if(empty($idCliente)){ 
                        $numero = isset($customer['Numero']) ? mb_strtoupper(substr($customer['Numero'], 0, 9)) : "" ;
                        $endereco = removeAcentos(mb_strtoupper($customer['Endereco'])); //strtoupper(prepareString($customer['Endereco']));
                        $endereco = substr($endereco.", ".$numero, 0, $columnsPessoas['endereco']['character_maximum_length']);
    //                     $bairro = strtoupper(prepareString(substr($customer['Bairro'], 0, $columnsPessoas['bairro']['character_maximum_length'])));
                        $bairro = mb_strtoupper(substr($customer['Bairro'], 0, $columnsPessoas['bairro']['character_maximum_length']));
                        $cidade = mb_strtoupper($customer['Cidade']);
                        $telefone1 = strlen($customer['Telefone']) > 20 ? substr(preg_replace("/[^0-9]/", "", $customer['Telefone']), 0, $columnsPessoas['telefone1']['character_maximum_length']) : $customer['Telefone'] ;
                        $telefone2 = strlen($customer['TelefoneAlternativo']) > 20 ? substr(preg_replace("/[^0-9]/", "", $customer['TelefoneAlternativo']), 0, $columnsPessoas['telefone1']['character_maximum_length']) : $customer['TelefoneAlternativo'] ;
                        $email = substr($customer['Email'], 0, $columnsPessoas['email']['character_maximum_length']);
                        $genero = isset($customer['Genero']) && !empty($customer['Genero']) ? $customer['Genero'] : '';
                        $uf = $customer['Estado'];
                        $cep = formataCEP($customer['CEP']);
                        $cep = substr_replace($cep, '.', 2,0);
                        $codCidade = getCodigoCidade($pg, $cep);
                        if(empty($customer['DataNascimento'])){
                            $dtNascimento = "NULL";
                        }else{
                            $dateNascimento = explode("T", $customer['DataNascimento']);
                            $dtNascimento = "'{$dateNascimento[0]}'";
                        }
                        $sqlInsert = "INSERT INTO pessoas (
                			nome, apelido,cpfcnpj,pessoa,rgie,endereco,cidade,bairro,
                			uf,cep,codcidade,telefone1,telefone2,email,sexo,nascimento,
                			cliente,cadastro,status,contribuinte,empresa
                		) VALUES (
                			UPPER('" . substr(str_replace("'", " ", removeAcentos($nome)), 0, $columnsPessoas['nome']['character_maximum_length']) . "'),
                			UPPER('" . substr(str_replace("'", " ", removeAcentos($apelido)), 0, $columnsPessoas['apelido']['character_maximum_length']) . "'),
                			'" . $cpfCnpj . "',
                			" . $pessoafj . ",
                			'" . $rgie . "',
                			UPPER('" . substr(str_replace("'", " ", removeAcentos($endereco)), 0, $columnsPessoas['endereco']['character_maximum_length']) . "'),
                			UPPER('" . substr(str_replace("'", " ", removeAcentos($cidade)), 0, $columnsPessoas['cidade']['character_maximum_length']) . "'),
                			UPPER('" . substr(str_replace("'", " ", removeAcentos($bairro)), 0, $columnsPessoas['bairro']['character_maximum_length']) . "'),
                			'" . $uf . "',
                			'" . $cep . "',
                			'" . $codCidade . "',
                			'" . $telefone1 . "',
                			'" . $telefone2 . "',
                			'" . $email . "',
                			'" . $genero . "',
                			" . $dtNascimento . ",
                			TRUE,
                			'" . date('Y-m-d') . "',
                			'A',
                			1,
                            '{$empresa}'
                		) RETURNING codigo;";
                        $resInsert = $pg->query($sqlInsert);
                        $resCliente = $resInsert->fetch(PDO::FETCH_ASSOC);
                        $idCliente = $resCliente['codigo'];
//                         pre(array('cliente' => $idCliente));
                        
                    }
                    
                    if(!empty($idCliente)){
                        
                        $dataHora = explode(" ", $order['DataPedido']);
                        if(strlen($dataHora[1]) > 5){
                            $difHora = 5 - strlen($dataHora[1]);
                            $dataHora[1] = substr($dataHora[1], 0, $difHora);
                        }
                        $sqlCount = "SELECT * FROM vendas WHERE empresa = '97' AND data = '{$dataHora[0]}' AND hora = '{$dataHora[1]}' AND cliente = '{$idCliente}'";
                        $queryCount = $pg->query($sqlCount);
                        $resCount = $queryCount->fetchAll(PDO::FETCH_ASSOC);
                        if(count($resCount) > 0){
                            continue;
                        }
                        $payment = $order['FormaPagamento'];
                        $parcelas =  empty($order['Parcelas']) ? 1 : $order['Parcelas'] ;
                        /**
                         * Método de Pagamento
                         * 
                         * 1 - Criar uma forma de Pagamento 
                         * para cada gateway ou operadora
                         * EX:
                             * 024 Mercado Pago
                             * 058 Credit Card Yapay
                         * 
                         * 2 - Associar para todas lojas 
                         * que irão faturar pedido
                         */
                        $forma_pagamento = '024';
                        /**
                         * Forma de Pagamento
                         * 
                         * 1- Criar método para cada formas de pagamento
                         * 
                         * EX:
                             * 1 X cartão Yapay 157
                             * 2 X cartão Yapay 158
                             * 3 X cartão Yapay 159
                             * 4 X cartão Yapay 160
                             * 5 X cartão Yapay 161
                             * 6 X cartão Yapay 162
                             * 7 X cartão Yapay 163
                             * 8 X cartão Yapay 164
                             * 9 X cartão Yapay 165
                             * 10 X cartão Yapay 166
                             * 11 X cartão Yapay 167
                             * 12 X cartão Yapay 168
                             * Boleto
                             * Depósito
                             * account_money
                          *
                          * ou Finaliza a forma de pagamento 
                          * manualemnte durante o faturamento
                          * EX: 
                            * Ecommerce Manual 171
                         */
                        $condicao_pagamento = '171';
                        
                        /**
                         * Vendedor
                         * 
                         * 1-  Criar vendedor referente canal de venda 
                         * e associar a loja de faturamento
                         * EX:
                             * ECOMMERCE TRAY 404736
                             * MERCADOLIVRE CIRANDINHA PLUGGTO 404731
                             * MERCADOLIVRE FM PLUGGTO 404734
                             * ou  MERCADOLIVRE PLUGGTO (default)
                         */
                        $vendedor = '';
                        
                        $status = "P";// Pendente
                        
                        if(!empty($order['fiscal_key'])){
                            $status = "S";// Faturado
                        }
                        $ajustex = "0";
                        $ajuste = "0";
                        $vmanual = "0";
                        $pedidoId = $order['PedidoId'];
    //                     $vendedor = '404736';
    //                     $vendedor = '00235555'; //MELI GODIVA
                        $canal = $order['Canal'];
    //                     pre($canal);
                        switch($canal){
    //                         case "LOJAS CIRANDINHA": $vendedor = '404731'; break;
    //                         case "FMCALADOSDEMARILIAEIRELIE": $vendedor = '404734'; break;
    //                         case "LOJAS VIRTUAL": $vendedor = '404736'; break;
    //                         case "MAGAZINCALCADOS": $vendedor = '404735'; break;
                            case "Cnova":
                                $condicao_pagamento = '403';
                                $vendedor = '00783757';
                                break;
                            case "BELLACALÇADOSDEMARÍLIA":
                                $condicao_pagamento = '357';
                                $vendedor = '00596495';
                                break;
                            case "GODIVA CALCADOS":
                                $condicao_pagamento = '357';
                                $vendedor = '00235555';
                                break;
                            case "BABY CALÇADOS":
                                $condicao_pagamento = '357';
                                $vendedor = '00745139';
                                break;
                            case "FORT CALCADOS":
                                $condicao_pagamento = '357';
                                $vendedor = '00703666';
                                break;
                            case "MercadoLivre":
                                $condicao_pagamento = '357';
                                $vendedor = '00703666';
                                break;
                            case "Shopee":
                                $condicao_pagamento = '386';
                                $vendedor = '00741699';
                                break;
                            case "Dafiti":
                                $condicao_pagamento = '385';
                                $vendedor = '00715829';
                                break;
                            case "Magazineluiza":
                                $condicao_pagamento = '372';
                                $vendedor = '00704170';
                                break;
                            case "B2w":
                                $condicao_pagamento = '374';
                                $vendedor = '00704169'; 
                                break;
                            case "vendas@godivacalcados.com.br":
                                $condicao_pagamento = '374';
                                $vendedor = '00704169'; 
                                break;
                            case "Netshoes":
                                $condicao_pagamento = '370';
                                $vendedor = '00702868';
                                break;
                            case "Amazon.com.br":
                                $condicao_pagamento = '369';
                                $vendedor = '00702864';
                                break;
                                
                            default: $vendedor = ''; break;
                        }
                        if(empty($vendedor)){
                            continue;
                        }
                        $countItems = count($items);
                        $frete = $order['ValorFrete'];
                        $desconto = $order['ValorCupomDesconto'];
                        
                        $subTotal = $order['Subtotal'];
                        $valorTotal = $order['ValorPedido'];
                        
                        $desconto = $order['ValorCupomDesconto'];
    //                     $ajustex = $desconto > 0 ? 'D': '';//'A',
                        $ajustex = 'D';//'A',
                        $ajuste = $desconto;
                        $vmanual = $desconto > 0 ? str_replace(",", ".", ($desconto * -1)): '0.00';
                        $valorTotal = str_replace(",", ".", ($subTotal + $frete - $desconto));
                        $obs =  $order['PedidoId'];
                        $sqlInsertVenda = "INSERT INTO vendas (
            				empresa,
            				tipo,
            				cliente,
            				vendedor,
            				condicoes,
            				impresso,
            				status,
            				data,
            				hora,
            				itens,
            				subtotal,
            				ajustex,
            				ajuste,
            				vmanual,
            				frete,
            				total,
            				avista,
            				obs,
            				ecommerce
            			) VALUES (
            				'{$empresa}',
            				'01',
            				'" . $idCliente . "',
            				'" . $vendedor . "',
            				'" . $condicao_pagamento . "',
            				'1',
            				'" . $status . "',
            				'" . $dataHora[0] . "',
            				'" . $dataHora[1] . "',
            				'" . $countItems. "',
            				'" . $subTotal . "',
            				'" . $ajustex . "',
            				'" . $ajuste . "',
            				'" . $vmanual . "',
            				'" . $frete . "',
            				'" . $valorTotal . "',
            				'" . $valorTotal . "',
            				'" . $obs . "',
            				true
            			) RETURNING codigo;";
                        
    //                     pre(array('sql_venda' => $sqlInsertVenda));
                        $resInsertVenda = $pg->query($sqlInsertVenda);
                        $resVenda = $resInsertVenda->fetch(PDO::FETCH_ASSOC);
                        $idVenda = $resVenda['codigo'];
                        pre(array('id_venda' => $idVenda));
                        if(!empty($idVenda)){
    
                            foreach($order['items'] as $j => $item){
                                
                                if(empty($item['SKU'])){
                                    
                                    $db->update("orders",
                                        array('store_id', 'id'),
                                        array($storeId, $order['id']),
                                        array('error' => date("d/m/Y  H:i:s")." | Erro ao exportar pedido para Seta item sem SKU {$order['PedidoId']} ")
                                    );
                                    continue;
                                    
                                }
                                $total = str_replace(",", ".", ($item['Quantidade'] * $item['PrecoUnitario']));
                                
                                $custo = getERPCusto($pg, substr($item['SKU'], 0, -2));
                                
                                $sqlInsertMovimento = "INSERT INTO movimento (
        						auxiliar,
        						operacao,
        						movimento,
        						data,
        						empresa,
        						produto,
        						quantidade,
        						unitario,
        						total,
        						custo,
        						base,
        						desconto,
        						ipi,
        						icms,
        						cstcf,
        						estoque,
        						obs,
        						promocao,
        						vendedorm,
        						csum,
        						presente,
        						conferido,
        						cfop,
        						reducao,
        						precovenda
        					) VALUES (
        						'VE" . $idVenda . "',
        						'VE',
        						'S',
        						'" . $dataHora[0] . "',
        						'{$empresa}',
        						'" . $item['SKU'] . "',
        						'" . $item['Quantidade'] . "',
        						'" . $item['PrecoUnitario'] . "',
        						'" . $total . "',
        						'" . $custo . "',
        						'" . $item['PrecoUnitario'] . "',
        						'0.00',
        						'0.00',
        						'0.00',
        						'',
        						TRUE,
        						'',
        						'',
        						'" . $vendedor . "',
        						'0',
        						FALSE,
        						FALSE,
        						'',
        						'0.00000',
        						'0.00'
        					   ) RETURNING codigo;";
    //                             pre(array('sql_mov' => $sqlInsertMovimento));
                                $resInsertMov = $pg->query($sqlInsertMovimento);
                                $resMov = $resInsertMov->fetch(PDO::FETCH_ASSOC);
                                $idmovimento = $resMov['codigo'];
//                                 pre(array('id_mov' => $idmovimento));
    
                            }
                            
                            
                            if(isset($idVenda)){
                                $sqlUpdateOrder = "UPDATE orders SET sent = 'T', id_nota_saida = {$idVenda}
                                WHERE store_id = {$order['store_id']} AND id = {$order['id']}";
                                $imported++;
                                $db->query($sqlUpdateOrder);
                                unset($idVenda);
                            }
                        }
                    }
                    
                   
	
    		    }else{
    		        echo "erro|falta cliente para gerar a vennda {$order['id']}";
    		    }
//     		    die;
	        }
		    logSyncEnd($db, $syncId, $imported);
			break;
			
		case "export_orders_test":
		    $ordersModel = new OrdersModel($db);
		    
		    $ordersModel->store_id = $storeId;
		    $ordersModel->id = isset($_REQUEST["order_id"])  ? intval($_REQUEST["order_id"]) : NULL ;
		    $orders = $ordersModel->ExportOrderDetails();
		    pre($orders);
		    $imported = 0;
		    $customer = array();
		    foreach($orders as $key => $order){
		        pre($order);die;
		        // 	            $message = exportOrder($connPgSeta, $empresa, $vendedor, $order['id']);
		        pre($message);die;
		        
		    }
		    
		    
		    break;
		    
		    
		case 'test_seta':
    		    $sqlCep ="SELECT * FROM cepcidades WHERE cep LIKE '{}'";
    		    $query = $pg->query($sqlCep);
    		    $resCep = $query->fetchAll(PDO::FETCH_ASSOC);
    		    pre($res);die;
    		    $query = $pg->describe('cepcidades');
    		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    		    pre($res);die;
    		    $sql ="SELECT table_schema,table_name
                FROM information_schema.tables
                ORDER BY table_schema,table_name;";
    		    $query = $pg->query($sql);
    		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    		    pre($res);die;
    		    
    		    
    		    $query = $pg->describe('cidades');
    		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    		    $columnsPessoas = array();
    		    foreach($res as $k => $column){
    		        $columnsPessoas[$column['column_name']] = $column;
    		        
    		    }
    		    pre($columnsPessoas);die;
    		    $query = $pg->describe('pessoas');
    		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    		    $columnsPessoas = array();
    		    foreach($res as $k => $column){
    		        $columnsPessoas[$column['column_name']] = $column;
    		        
    		    }
    		    pre($columnsPessoas);
    		    $query = $pg->describe('vendas');
    		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    		    $columnsVendas = array();
    		    foreach($res as $k => $column){
    		        $columnsVendas[$column['column_name']] = $column;
    		        
    		    }
    		    pre($columnsVendas);
    		    $query = $pg->describe('movimento');
    		    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    		    $columnsMovimento = array();
    		    foreach($res as $k => $column){
    		        $columnsMovimento[$column['column_name']] = $column;
    		        
    		    }
    		    pre($columnsMovimento);die;
    		    $order = array();
    		    // 	        $sql = "SELECT * FROM vendas WHERE empresa = '97' AND obs LIKE 'VENDA ECOMMERCE 46757' ORDER BY codigo DESC LIMIT 10 ";
    		    $sql = "SELECT * FROM vendas WHERE empresa = '97' AND obs LIKE 'VENDA ECOMMERCE 46757' ";
    		    $query = $pg->query($sql);
    		    $resVenda = $query->fetch(PDO::FETCH_ASSOC);
    		    $order['venda'] = $resVenda;
    		    
    		    $vendedor = $resVenda['vendedor'];
    		    $sql = "SELECT * FROM pessoas WHERE codigo LIKE '{$vendedor}'";
    		    $query = $pg->query($sql);
    		    $resVendedor = $query->fetch(PDO::FETCH_ASSOC);
    		    $order['vendedor'] = $resVendedor;
    		    
    		    $sql = "SELECT * FROM movimento WHERE empresa = '97' AND vendedorm = '{$vendedor}' ";
    		    $query = $pg->query($sql);
    		    $resMov = $query->fetch(PDO::FETCH_ASSOC);
    		    $order['movimento'] = $resMov;
    		    
    		    $cliente = $resVenda['cliente'];
    		    $sql = "SELECT * FROM pessoas WHERE codigo LIKE '{$cliente}'";
    		    $query = $pg->query($sql);
    		    $resCliente = $query->fetch(PDO::FETCH_ASSOC);
    		    $order['cliente'] = $resCliente;
    		    
    		    pre($order);die;
		    
		    break;
		    
	}

}
