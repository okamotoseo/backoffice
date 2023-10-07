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
	    
	    case "verify_table":
	        
	       echo  $IdNtSaida = intval($_REQUEST['id_nota_saida']);
	       
	       $query = $pg->query("SELECT * FROM nota_saida_formapgto  WHERE  id_nota_saida  = ?", array($IdNtSaida));
	        
	        $row = $query->fetch(PDO::FETCH_ASSOC);
	        pre($row);

// 	        $query = $pg->describe('pessoas');
// 	        $res = $query->fetchAll(PDO::FETCH_ASSOC);
// 	        $columnsPessoas = array();
// 	        foreach($res as $k => $column){
// 	            $columnsPessoas[$column['column_name']] = $column;
	            
// 	        }
// 	        pre($columnsPessoas);
// 	        $query = $pg->describe('vendas');
// 	        $res = $query->fetchAll(PDO::FETCH_ASSOC);
// 	        $columnsVendas = array();
// 	        foreach($res as $k => $column){
// 	            $columnsVendas[$column['column_name']] = $column;
	            
// 	        }
// 	        pre($columnsVendas);
	        $query = $pg->describe('nota_saida_formapgto');
	        $res = $query->fetchAll(PDO::FETCH_ASSOC);
	        $columnsPagamento = array();
	        foreach($res as $k => $column){
	            $columnsPagamento[$column['column_name']] = $column;
	            
	        }
	        pre($columnsPagamento);die;
	        
	        break;
	
	    
		case "export_orders":
// 		    $db->query("UPDATE orders SET sent = 'F' WHERE store_id = 4 AND id != 461");
// 		    $pg->query('DELETE FROM nota_saida WHERE id_nota_saida != 2649');
// 		    $pg->query('DELETE FROM nota_saida_itens WHERE id_nota_saida != 2649');
// 		    $pg->query('DELETE FROM nota_saida_formapgto WHERE id_nota_saida != 2649');

// 		    die;
		    
		    $syncId =  logSyncStart($db, $storeId, "Sysemp", $action, "Exportação de pedidos", $request);
		    $ordersModel = new OrdersModel($db);
		    
		    $ordersModel->store_id = $storeId;
		    $ordersModel->id = isset($_REQUEST["order_id"])  ? intval($_REQUEST["order_id"]) : NULL ;
		    $orders = $ordersModel->ExportOrderDetails();
		    $imported = 0;
		    
// 		    pre($orders);
		  
		    $customer = array();
		    foreach($orders as $key => $order){
		        
		        $disableExport = array('Pending', 'pending_payment', 'waiting_payment', 'em monitoramento');
		        if(!in_array($order['Status'], $disableExport)){
		       
    		        if($order['logistic_type'] != 'fulfillment'){
    		            
        		        $customer = $order['customer'];
        		        
        		        if(empty($customer['CPFCNPJ'])){
        		        	echo $error = 'Cliente sem CPF....';
        		        	continue;
        		        }
        		        
        		        $cpfAuxiliar = $customer['TipoPessoa'] == 1 ? $customer['CPFCNPJ'] : '' ;
        
        		        $idTransportadora = 2;// correios coronel galdino
        		        
        		        switch ($order['Marketplace']) {
        		            case 'Mercadolivre': 
        		                $canal = 1;
        		                $idTransportadora =  34207;
        		              break;
        		            case 'Ecommerce': $canal = 3;break;
        		            case 'Skyhub': $canal = 5;break;
        		            default : $canal = 3;break;
        		        }
        		      
        		        
        		        $sellerId = 1; // Sysplace
        		        
        		        $customerId = exportCustomer($db, $pg, $storeId, $customer, $sellerId);
        		        
        		        $PrecoTotalProd = $order['Subtotal'];
        		        
        		        $desconto = $order['ValorCupomDesconto'];
        		        
        		        $valorPedido = $order['ValorPedido'];
        		        
        		        $valor_frete = $order['ValorFrete'] > 0 ? $order['ValorFrete'] : 0 ;
        		        
        		        $valorPedido = ($valorPedido - $valor_frete);
        		        
        		        $valor_frete = 0;
        		        
        		        $cep = $customer['CEP'];
        		        $cidade = empty($customer['Cidade']) ? pg_escape_string(getCityException($customer['Cidade'])) : pg_escape_string(getCityException($order['Cidade']));
        		        if(empty($cidade)){
        		            notifyAdmin("Cidade não encontrada {$order['PedidoId']} Sysemp Order");
        		        }
    //     		        $cidade = "FOZ DO IGUACU";  e));
        		        $estado = utf8_decode(strtoupper(removeAcentosNew($customer['Estado'])));
        		        $endereco = utf8_decode(str_replace("'", "", strtoupper(removeAcentosNew(substr($customer['Endereco'], 0, 60)))));
        		        $complemento = utf8_decode(strtoupper(removeAcentosNew(substr($customer['Complemento'], 0, 30))));
        		        $bairro = !empty($customer['Bairro']) ? utf8_decode(strtoupper(removeAcentosNew(substr($customer['Bairro'], 0, 30)))) : 'Bairro';
        		        $nome = utf8_decode(strtoupper(removeAcentosNew(substr($customer['Nome'], 0, 60))));
        		        $nomeRecebedor = utf8_decode(strtoupper(removeAcentosNew(substr($order['Nome'], 0, 30))));
        		        $apelido = utf8_decode(strtoupper(removeAcentosNew($customer['Apelido'])));
        		        $serie = 1;
        		        
        		        
            		      if(!empty($customerId)){
            		            
            		          $codigoIbge =  getCodigoIbge($db, $pg, $cidade);
            		            $idUf = getCodigoUf($pg, $codigoIbge);
            		            if(isset($order['items'])){
                		            foreach($order['items'] as $key2 => $item){
                		                $sql = "SELECT id_produto, ncm FROM produto WHERE codigo_auxiliar LIKE '{$item['SKU']}'";
                		                $queryVerify = $pg->query($sql);
                		                $productVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                		                if(!empty($productVerify['id_produto'])){
                		                    $ncm_codigo = $productVerify['ncm'];
                		                }
                		            }
            		            }
                		        $idEmpresa = 1;
            		            if($customer['TipoPessoa'] == 2){  
            		                
        //             		        // Sem substituição
        //             		        if($idUf != 35){
        //             		            //fora do estado
        //             		            $idNatOperacao = '93';
        //             		        }
        //             		        if($idUf == 35){
        //             		            //dentro do estado
        //             		            $idNatOperacao = '73';
        //             		        }
                    		        
                    		        
                    		        // Com substituição
                    		        if($idUf != 35){
                    		            //fora do estado
                    		           $idNatOperacao = '75';
                    		        }
                    		        if($idUf == 35){
                    		            //dentro do estado
                    		            $idNatOperacao = '73';
                    		        }
                		        
            		            }
            		            
            		            if($customer['TipoPessoa'] == 1){
        //     		                // Sem substituição
        //     		                if($idUf != 35){
        //     		                    //fora do estado
        //     		                    $idNatOperacao = '93';
        //     		                }
        //     		                if($idUf == 35){
        //     		                    //dentro do estado
        //     		                    $idNatOperacao = '73';
        //     		                }
            		                
            		                
            		                // Com substituição
            		                if($idUf != 35){
            		                    //fora do estado
        //     		                    $idNatOperacao = '93';
            		                    $idNatOperacao = '77';
            		                }
            		                if($idUf == 35){
            		                    //dentro do estado
            		                    $idNatOperacao = '73';
            		                }
            		                
            		            }
            		        
            		    
            		        
            		        $pedidoDataHora = explode(" ", $order['DataPedido']);
            		        $pedidoData = $pedidoDataHora[0];
            		        $pedidoHora = $pedidoDataHora[1];
            		        
            		        $competencia = utf8_decode(removeAcentosNew(getMesAnoCompetencia($pedidoData)));
            		       
            		        
            		        
            		        $query = $pg->query("SELECT id_nota_saida, id_tp_pedido, nf_impressa, nfe_cstat FROM nota_saida  WHERE id_cliente = ?
            				AND marketplace_pedido = ? AND id_pedido_vda_importado = ?",
            		            array($customerId, $order['PedidoId'], $order['id'])
            		            );
            		        $row = $query->fetch(PDO::FETCH_ASSOC);
            		        //se o pedido tiver cancelado e existir no erp sysemp irá excluir o pedido se nao tiver emitido nota
            		        if(!empty($row['id_nota_saida']) AND $order['Status'] == 'cancelled'){
            		            if($row['nf_impressa'] == 'F' AND $row['nfe_cstat'] != 100){
                		            $pg->query("DELETE FROM nota_saida_formapgto WHERE id_nota_saida = {$row['id_nota_saida']}");
                		            $pg->query("DELETE FROM nota_saida_itens WHERE id_nota_saida = {$row['id_nota_saida']}");
                		            $pg->query("DELETE FROM nota_saida WHERE id_nota_saida = {$row['id_nota_saida']}");
            		            }
            		            
            		        }else{
                		        
            		            if(empty($row['id_nota_saida']) AND $order['Status'] != 'cancelled' ){
            		            	
//             		            	pre(array(
//             		            			'prod_total_vr' => $PrecoTotalProd,
//             		            			'total_nota_fiscal' => $valorPedido,
//             		            			'total_nota_fiscal_liq' => $valorPedido,
//             		            			'valor_frete' => $valor_frete,
//             		            			'razsocial' => $nome,
//             		            			'valor_financeiro' => $valorPedido,
//             		            			'dt_competencia' => $pedidoData,
//             		            			'marketplace_pedido' => $order['PedidoId']
            		            	
//             		            	));
                		            $queryGen = $pg->query("SELECT nextval('sysemp.gen_nota_saida')");
                		            $maxId = $queryGen->fetch(PDO::FETCH_OBJ);
                		            $idNotaSaida = $maxId->nextval;
                		            $number = substr($customer['Numero'], 0, 10);
                    		        $data = array(
                    		            'id_nota_saida' => $idNotaSaida,
                    		            'id_nr_nf' => 0,
                    		            'serie' => $serie,
                    		            'id_empresa' => $idEmpresa,
                    		            'id_empresa_danfe' => 1,
                    		            'serie_danfe' => 1,
                    		            'id_cliente' => $customerId,
                    		            'data_pedido' => $pedidoData,
                    		            'data_emissao' => Null,
                    		            'hora_emissao' => Null,
                    		            'data_saida' => Null,
                    		            'hora_saida' => Null,
                    		            'id_nat_operacao' => $idNatOperacao,
                    		            'id_vendedor' => $sellerId,
                    		            'dt_cadastro' => $pedidoData,
                    		            'us_cadastro' => 'SYSPLACE',
                    		            'hr_cadastro' => $pedidoHora,
                    		            'dt_alteracao' => Null,
                    		            'us_alteracao' => Null,
                    		            'entrega_rua' => $endereco,
                    		            'entrega_nr' => $number,
                    		            'entrega_cep' => $cep,
                    		            'entrega_cidade' => $cidade,
                    		            'entrega_uf' => $estado,
                    		            'entrega_bairro' => $bairro,
                    		            'entrega_nome_receber' => $nomeRecebedor,
                    		            'entrega_data' => Null,
                    		            'entrega_hora' => Null,
                    		            'entrega_observacao' => '',
                    		            'entrega_codigoibge' => $codigoIbge,
                    		            'prod_total_vr' => $PrecoTotalProd,
                    		            'prod_ipi_vr' => '0.00',
                    		            'prod_acrescimo_vr' => '0.00',
                    		            'desconto_vr' => $desconto,
                    		            'total_nota_fiscal' => $valorPedido,
                    		            'total_nota_fiscal_liq' => $valorPedido,
                    		            'valor_financeiro_servico' => 0,
                    		            'troco' => '0.00',
                    		            'base_icms' => '0.00',
                    		            'valor_icms' => '0.00',
                    		            'valor_frete' => $valor_frete,
                    		            'valor_seguro' => '0.00',
                    		            'valor_ipi' => '0.00',
                    		            'total_servico' => '0.00',
                    		            'nf_impressa' => 'F',
                    		            'nf_cancelada' => 'F',
                    		            'id_transportadora' => $idTransportadora,
                    		            'volume_qtde' => '1',
                    		            'conta_frete' => '1',
                    		            'observacao_nf' => 'Observacao_Fiscal',
                    		            'dt_registro' => Null,
                    		            'razsocial' => $nome,
                    		            'chavenfe' => Null,
                    		            'dataenvionfe' => Null,
                    		            'protocolonfe' => 'PROTOCOLO_AUTORIZACAO',
                    		            'placa_transp' => '',
                    		            'uf_placa_transp' => '',
                    		            'justificativacontingencia' => '',
                    		            'redespacho_id_transp' => Null,
                    		            'base_pis' => '0',
                    		            'base_ipi' => '0',
                    		            'valor_pis' => '0',
                    		            'valor_cofins' => '0',
                    		            'base_cofins' => '0',
                    		            'tipo_docto_fiscal' => 'NFS',
                    		            'total_tributo_aproximado' => '0',
                    		            'tipo_documento' => 'PD',
                    		            'valor_financeiro' => $valorPedido,
                    		            'valor_pis_retido' => '0.00',
                    		            'valor_cofins_retido' => '0.00',
                    		            'valor_csll_retido' => '0.00',
                    		            'valor_ir_retido' => '0.00',
                    		            'vrcomissao' => '0.00',
                    		            'servico_cofins_retido' => '0.00',
                    		            'servico_pis_retido' => '0.00',
                    		            'quilometragem' => '0',
                    		            'numero_serie' => $serie,
                    		            'cpf_auxiliar' => $cpfAuxiliar,
                    		            'id_fabrica' => $customerId,
                    		            'dt_competencia' => $pedidoData,
                    		            'competencia' => $competencia,
                    		            'nfe' => '1',
                    		            'id_terminal' => '33',
                    		            'id_tp_pedido' => 1,
                    		            'ref_tipo' => 0,
                    		            'ref_id_uf_emitente' => 0,
                    		            'ref_serie_docfiscal' => 0,
                    		            'ref_numero_docfiscal' => 0,
                    		            'ref_numero_ecf' => 0,
                    		            'ref_numero_coo' => 0,
                    		            'ref_modelo_nf_produtor' => 0,
                    		            'nrcaixa' => 0,
                    		            'id_tabela_preco' => 1,
                    		            'id_tabela_preco_coluna' => 1,
                    		            'servico_situacao' => 'TP',
                    		            'status' => 1,
                    		            'id_finalidade' => 1,
                    		            'id_ind_presenca' => $canal,
                    		            'bloqueada' => 'F',
                    		            'status_separacao' => 'N',
                    		            'id_atendimento' => 1, 
                    		            'ped_impresso' => 'F',
                    		            'sep_impresso' => 'F',
                    		            'etiq_impresso' => 'F',
                    		            'marketplace_pedido' => $order['PedidoId'],
                    		            'contato' => $nome,
                    		            'email_contato' => $customer['Email'],
                    		            'motivo_desoneracao' => '00',
                    		            'id_pedido_vda_importado' => $order['id'],
                    		            'total_tributo_estadual' => '0',
                    		            'total_tributo_municipal' => '0'
                    		            
                    		        ); 
                    		        $query = $pg->insert('nota_saida', $data);
                    		        if(!$query){ 
                    		            pre($query);
                    		        }
        
                		        }else{
                		            
                		            $idNotaSaida =  $row['id_nota_saida'];
                		            $tipoPedido = $row['id_tp_pedido'];
                		            // Verifica se é tipo pedido e ainda não foi faturado
                		            if($tipoPedido == 1){
                		                $number = substr($customer['Numero'], 0, 10);
                    		            $data =  array(
                    		                'serie' => $serie,
                    		                'id_empresa' => $idEmpresa,
                    		                'id_cliente' => $customerId,
                    		                'data_pedido' => $pedidoData,
                    		                'id_nat_operacao' => $idNatOperacao,
                    		                'id_vendedor' => $sellerId,
                    		                'dt_cadastro' => $pedidoData,
                    		                'hr_cadastro' => $pedidoHora,
                    		                'us_alteracao' => 'SYSPLACE',
                    		                'entrega_rua' => $endereco,
                    		                'entrega_nr' => $number,
                    		                'entrega_cep' => $cep,
                    		                'entrega_cidade' => $cidade,
                    		                'entrega_uf' => $estado,
                    		                'entrega_bairro' => $bairro,
                    		                'entrega_nome_receber' => $nomeRecebedor,
                    		                'entrega_codigoibge' => $codigoIbge,
                    		                'prod_total_vr' => $PrecoTotalProd,
                    		                'desconto_vr' => $desconto,
                    		                'total_nota_fiscal' => $valorPedido,
                    		                'total_nota_fiscal_liq' => $valorPedido,
                    		                'valor_frete' => $valor_frete,
                    		                'id_transportadora' => $idTransportadora,
                    		                'razsocial' => $nome,
                    		                'valor_financeiro' => $valorPedido,
                    		                'numero_serie' => $serie,
                    		                'cpf_auxiliar' => $cpfAuxiliar,
                    		                'id_fabrica' => $customerId,
                    		                'dt_competencia' => $pedidoData,
                    		                'competencia' => $competencia,
                    		                'marketplace_pedido' => $order['PedidoId'],
                    		                'contato' => $nome,
                    		                'email_contato' => $customer['Email'],
                    		                'id_pedido_vda_importado' => $order['id']
                    		                
                    		            ); 
                    		            $query = $pg->update('nota_saida', array('id_nota_saida'), array($row['id_nota_saida']), $data);
                    		            
                		            }
                		            
                		        }
                		       
                		        if(!empty($idNotaSaida)){
                		            
                		            $vr_frete_item  = $valor_frete > 0 ? $valor_frete / count($order['items'] ) : 0 ;
                		            $itemCount = 1;
                		            foreach($order['items'] as $key2 => $item){
                		                
                		                $sql = "SELECT id_produto, ncm FROM produto WHERE codigo_auxiliar LIKE '{$item['SKU']}'";
                		                $queryVerify = $pg->query($sql);
                		                $productVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                		                if(!empty($productVerify['id_produto'])){
                		                    
                		                    $id_produto =  $productVerify['id_produto'];
                    		                $custoUnit = $item['PrecoVenda'] * 0.50; 
                    		                $precoLiquido = $item['PrecoVenda'] - $item['TaxaVenda'];
                    		                $precoLiquidoTotal = ($item['PrecoVenda'] - $item['TaxaVenda']) * $item['Quantidade'];
                    		                $descricao = utf8_decode(strtoupper(removeAcentosNew($item['Nome'])));
                    		                $percentualcomissaoml = !empty($item['TaxaVenda']) ? (float) ( $item['TaxaVenda'] / $item['PrecoVenda'] ) * 100 : 0.00 ;
                    		                $vr_frete_item_qtd = $vr_frete_item > 0 ? $vr_frete_item / $item['Quantidade'] : 0 ;
                    		                
                    		                $ncm_codigo = $productVerify['ncm'];
                    		                $sku = $item['SKU'];
                    		                
                    		                $cst = 1;
                    		                $cst_cson = '102';
                    		                $id_tes = 1;
                    		                $codigo_cst = '00';
                    		                
                    		                if($ncm_codigo == '84145990' AND $idNatOperacao != '77'){
    //                 		                    $cst = null;
                    		                    $cst_cson = '500';
                    		                    $id_tes = 4;
                    		                    $codigo_cst = '60';
                    		                    
                    		                }
                    		                $tipoAnuncio = '';
//                     		                $total = ($item['PrecoVenda'] + $vr_frete_item_qtd) * $item['Quantidade'];
                    		                $total = ($item['PrecoVenda'] * $item['Quantidade']) ;
                    		                
                    		                $query = $pg->query("SELECT id_nota_saida, id_produto FROM nota_saida_itens
                                            WHERE id_nota_saida = ? AND id_produto = ?", array($idNotaSaida, $id_produto));
                    		                $rowItem = $query->fetch(PDO::FETCH_ASSOC);
                    		                
                    		                if(empty($rowItem['id_nota_saida'])){
        //             		                    echo "insert nota saida itens"; die;
        

//                     		                	pre(array('data_emissao' => $pedidoData,
//                     		                			'id_produto' => $id_produto,
//                     		                			'item' => $itemCount,
//                     		                			'qtde' => $item['Quantidade'],
//                     		                			'valor_bruto' => $item['PrecoVenda'],
//                     		                			'valor_liquido' => $item['PrecoVenda'],
//                     		                			'valor_total_liquido' => $total,
//                     		                			'vr_frete' => number_format($vr_frete_item, 4, '.', '')
                    		                			 
//                     		                	));
                    		                	
                                                $query = $pg->insert('nota_saida_itens', array( 
                                                    'id_nr_nf' => 0,
                                                    'serie' => '1',
                                                    'id_empresa' => $idEmpresa,
                                                    'id_cliente' => $customerId,
                                                    'id_vendedor' => $sellerId,
                                                    'data_emissao' => $pedidoData,
                                                    'id_produto' => $id_produto,
                                                    'item' => $itemCount,
                                                    'aliquota_icms' => '0',
                                                    'qtde' => $item['Quantidade'],
                                                    'valor_bruto' => $item['PrecoVenda'],
                                                    'valor_liquido' => $item['PrecoVenda'],
                                                    'valor_total_liquido' => $total,
                                                	'vr_frete' => number_format($vr_frete_item, 4, '.', ''),
                                                    'dt_registro' => Null,
                                                    'reducao' => '0',
                                                    'movimenta_estoque' => 'S',
                                                    'gera_financeiro' => 'S',
                                                    'icms' => 'D',
                                                    'ipi' => 'N',
                                                    'descr_reduzida' => substr($descricao, 0, 100),
                                                    'custo_produto' => $custoUnit,
                                                    'CUSTO_SEMIMP' => '0',
                                                    'CUSTO_DIFICMS' => '0',
                                                    'CUSTO_ICMS' => '0',
                                                    'CUSTO_IPI' => '0',
                                                    'CUSTO_SUBST' => '0',
                                                    'CUSTO_FRETE' => '0',
                                                    'CUSTO_OUTROS' => '0',
                                                    'CUSTO_PISCOFINS' => '0',
                                                    'id_nat_operacao_item' => $idNatOperacao,
                                                    'valor_desconto' => '0',
                                                    'perc_desconto' => '0',
                                                    'id_nota_saida' => $idNotaSaida,
                                                    'valor_bruto_old' => $total,
                                                    'vr_tabela' => $item['PrecoVenda'],
                                                    'percentualcomissaoml' => number_format($percentualcomissaoml, 2, '.', ''),
                                                    'tipoanuncioml' => $tipoAnuncio,
                                                    'sku' => $sku,
                                                    'ncm_codigo' => $ncm_codigo,
                                                    'cst' => $cst,
                                                    'cst_cson' => $cst_cson,
                                                    'codigo_cst' => $codigo_cst,
                                                    'id_tes' => $id_tes,
                                                    'cstipi' =>  '99',
                                                    'ipi_valor' =>  '0',
                                                    'ipi_total' =>  '0',
                                                    'icms_total' =>  '0',
                                                    'cstpis_saida' =>  '0',
                                                    'cstpis_saida_porc' =>  '0',
                                                    'cstpis_saida_valor' =>  '0',
                                                    'cstcofins_saida' =>  '0',
                                                    'dev_entr_nf' =>  '0',
                                                    'dev_entr_itens' =>  '0',
                                                    'perc_diferimento_icms' =>  '0',
                                                    'valor_diferimento_icms' =>  '0',
                                                    'desconto_total_item' =>  '0',
                                                    'vr_seguro' =>  '0',
                                                    'vr_outros' =>  '0',
                                                    'vr_desconto_total' =>  '0',
                                                    'vr_acrescimo' =>  '0',
                                                    'vr_financeiro' =>  '0',
                                                    'desoneracao_icms' =>  'N',
                                                    'vr_desconto_desoneracao' =>  '0',
                                                    'id_tp_pedido' =>  '1',
                                                    'valor_pis_retido' =>  '0',
                                                    'valor_cofins_retido' =>  '0',
                                                    'valor_csll_retido' =>  '0',
                                                    'aliquota_creditosn' =>  '0',
                                                    'id_tes_digitada' =>  '0',
                                                    'total_impostos_estadual' =>  '0',
                                                    'total_impostos_municipal' =>  '0',
                                                    'ncm_estadual' =>  '0',
                                                    'ncm_municipal' =>  '0',
                                                    'base_dificms' =>  '0',
                                                    'vr_dificms' =>  '0',
                                                    'qtde_faturada' =>  '0',
                                                    'qtde_devolvida' =>  '0',
                                                    'id_nota_saida_original' =>  '0',
                                                    'qtde_original' =>  '0',
                                                    'id_tp_pedido_original' =>  '1',
                                                    'vbcufdest' =>  '0',
                                                    'picmsufdest' =>  '0',
                                                    'picmsinter' =>  '0',
                                                    'picmsinterpart' =>  '0',
                                                    'vfcpufdest' =>  '0',
                                                    'vicmsufdest' =>  '0',
                                                    'vicmsufremet' =>  '0',
                                                    'pfcpmgdest' =>  '0',
                                                    'vfcpmgdest' =>  '0',
                                                    'ii_vbc' =>  '0',
                                                    'ii_vdespadu' =>  '0',
                                                    'ii_vii' =>  '0',
                                                    'ii_viof' =>  '0',
                                                    'di_vafrmm' =>  '0',
                                                    'adi_nadicao' =>  '0',
                                                    'adi_nseqadic' =>  '0',
                                                    'adi_cfabricante' =>  '0',
                                                    'adi_vdescdi' =>  '0',
                                                    'adi_ndraw' =>  '0',
                                                    'ii_vrfrete' =>  '0',
                                                    'ii_vrseguro' =>  '0',
                                                    'ii_vroutros' =>  '0',
                                                    'ii_aliquota' =>  '0',
                                                    'produto_embalagem' =>  '0',
                                                    'pedido_compra_item' =>  '0',
                                                    'custo_cofins' =>  '0',
                                                    'custo_outros_acr' =>  '0',
                                                    'despesa_fixa' =>  '0',
                                                    'vr_reserva' =>  '0',
                                                    'controle_entrega' =>  '0',
                                                    'qtde_entregar' =>  '0',
                                                    'indicador_escala' =>  'S',
                                                    'enquadramento_ipi' =>  '0',
                                                    'pauta_icms' =>  'F',
                                                    'pauta_icmsst' =>  'F',
                                                    'pauta_ipi' =>  'F',
                                                    'pauta_piscofins' =>  'F',
                                                    'aliq_icms_st' =>  '0',
                                                    'iva' =>  '0',
                                                    'pfcpst' =>  '0',
                                                    'vbcfcpst' =>  '0',
                                                    'vfcpst' =>  '0'
                                                 ));
                                       
                                        		
                                        		$itemCount++;
                                    		
                                    		
                    		              }else{
                    		                  if($tipoPedido == 1){
                    		                      $query = $pg->update('nota_saida_itens', 
                		                              array('id_nota_saida', 'id_produto'), 
                		                              array($idNotaSaida, $id_produto), 
                		                              array('id_empresa' => $idEmpresa,
                    		                              'id_cliente' => $customerId,
                    		                              'id_vendedor' => $sellerId,
                    		                              'data_emissao' => $pedidoData,
                    		                              'id_produto' => $id_produto,
                    		                              'qtde' => $item['Quantidade'],
                    		                              'valor_bruto' => $item['PrecoVenda'],
                    		                              'valor_liquido' => $item['PrecoVenda'],
                    		                              'valor_total_liquido' => $total,
                    		                              'descr_reduzida' => substr($descricao, 0, 100),
                    		                              'custo_produto' => $custoUnit,
                    		                              'id_nat_operacao_item' => $idNatOperacao,
                    		                              'id_nota_saida' => $idNotaSaida,
                    		                              'valor_bruto_old' => $total,
                    		                              'vr_tabela' => $item['PrecoVenda'],
                    		                              'percentualcomissaoml' => number_format($percentualcomissaoml, 2, '.', ''),
                    		                              'tipoanuncioml' => $tipoAnuncio,
                    		                              'sku' => $sku,
                    		                              'ncm_codigo' => $ncm_codigo,
                    		                              'vr_frete' => number_format($vr_frete_item, 4, '.', ''),
                    		                              'cst' => $cst,
                    		                              'cst_cson' => $cst_cson,
                    		                              'codigo_cst' => $codigo_cst,
                    		                              'id_tes' => $id_tes
                    		                               )
                    		                          );
                    		                  }
                    		              }
                    		              
            		                  }
                		                  
                		            }
                		            
                		        }
                		        
                		        if(isset($idNotaSaida)){
                		            
                		            $parcela = 1;
                		            
                		            $freteCobrado = $order['ValorFrete'] > 0 ? $order['ValorFrete'] : 0 ;
                		            
                		            foreach($order['payments'] as $key3 => $payment){
                		                
                		                $valor_taxa_cartao = $payment['MarketplaceTaxa'];
                		                
                		                $nsu = 0;
                		                
                		                switch ($payment['Marketplace']) {
                		                    
                		                    case 'Mercadolivre':
                		                        $idCartao = 13;//Mercadolivre
                		                        $id_bandeira = 1;
                		                        $id_operadora = 2;
                		                        $porc_taxa_cartao = 16;
                                                break;                		                
                		                    case "Lojas Americanas":
                		                        $idCartao = '14';
                		                        $id_bandeira = 3;
                		                        $id_operadora = 4;
                		                        $porc_taxa_cartao = 16;
                		                        break;
                		                    case "Submarino":
                		                        $idCartao = '14';
                		                        $id_bandeira = 3;
                		                        $id_operadora = 4;
                		                        $porc_taxa_cartao = 16;
                		                        break;
                		                    case "Shoptime":
                		                        $idCartao = '14';
                		                        $id_bandeira = 3;
                		                        $id_operadora = 4;
                		                        $porc_taxa_cartao = 16;
                		                        break;
                		                    case "Amazon":
                		                        $id_operadora = 5;
                		                        $idCartao = '40';
                		                        $id_bandeira = 4;
                		                        $porc_taxa_cartao = 11;
                		                        break;
                		                    case "Tray":
                		                        $id_operadora = 7;
                		                        $idCartao = '46';
                		                        $id_bandeira = 6;
                		                        $porc_taxa_cartao = 7;
                		                        break;
                		                    case "Shopee":
                		                        $id_operadora = 9;
                		                        $idCartao = '48';
                		                        $id_bandeira = 7;
                		                        $porc_taxa_cartao = 6;
                		                        break;
                		                    case "Televendas":
                		                        $id_operadora = 8;
                		                        $idCartao = '44';
                		                        $id_bandeira = 9;
                		                        $porc_taxa_cartao = 2;
                		                        break;
                		                }
                		                
                		                switch ($payment['FormaPagamento']) {
                		                    case 'credit_card':  $tipoCobranca = 2; break;
                		                    case 'Mastercard':  $tipoCobranca = 2; break;
                		                    case 'account_money':  $tipoCobranca = 2; break;
                		                    case 'ticket':  $tipoCobranca = 2; break;
                		                    case 'digital_currency':  $tipoCobranca = 2; break;
                		                    default :  $tipoCobranca = 2; break;
                		                }
                		                $nsu = isset($payment['NSU']) ? $payment['NSU'] : 0 ;
                		                $query = $pg->query("SELECT id_nota_saida, parcela FROM nota_saida_formapgto
                                                WHERE id_nota_saida = ? AND parcela = ?", array($idNotaSaida, $parcela));
                		                $rowPayment = $query->fetch(PDO::FETCH_ASSOC);
                		                
                		                if(empty($rowPayment['id_nota_saida'])){
                		                    $query = $pg->insert('nota_saida_formapgto', array(
                                                'id_nr_nf' => 0,
                                                'serie' => 1,
                                                'id_empresa' => $idEmpresa,
                                                'id_cliente' => $customerId,
                		                        'data_emissao' => $pedidoData,
                                                'parcela' => $parcela,
                                                'id_tipo_cobranca' => $tipoCobranca,
                                                'id_cartao' => $idCartao,
                		                        'valor' => $valorPedido, // ($payment['ValorTotal'] - $freteCobrado),
                                                'dt_registro' => $payment['DataAutorizacao'],
                                                'id_nota_saida' => $idNotaSaida,
                		                        'valor_produtos' => $valorPedido, //($payment['ValorTotal'] - $freteCobrado),
                                                'porc_taxa_cartao' => $porc_taxa_cartao,
                                                'dt_vencto' => date('Y-m-d'),
                                                'valor_servicos' => '0',
                                                'nr_controle_cartao' => '0',
                                                'id_status_cr' => '0',
                                                'nsu' => $nsu,
                                                'id_tp_cheque' => '0',
                                                'id_banco_conta' => '0',
                                                'id_bandeira' => $id_bandeira,
                                                'id_operadora' => $id_operadora,
                                                'valor_retido' => '0'
                		                        
                		                    ));
                		                    
                		                }else{
                		                    
                		                    
                		                    if($tipoPedido == 1){
//                 		                        pre( array('id_empresa' => $idEmpresa,
//                 		                            'id_cliente' => $customerId,
//                 		                            'data_emissao' => $pedidoData,
//                 		                            'parcela' => $parcela,
//                 		                            'id_tipo_cobranca' => $tipoCobranca,
//                 		                            'id_cartao' => $idCartao,
//                 		                            'valor' => $valorPedido, //($payment['ValorTotal'] - $freteCobrado),
//                 		                            'dt_registro' => $payment['DataAutorizacao'],
//                 		                            'id_nota_saida' => $idNotaSaida,
//                 		                            'valor_produtos' =>  $valorPedido, //($payment['ValorTotal'] - $freteCobrado),
//                 		                            'porc_taxa_cartao' => $porc_taxa_cartao,
//                 		                            'nsu' => $nsu,
//                 		                            'id_bandeira' => $id_bandeira,
//                 		                            'id_operadora' => $id_operadora,
//                 		                        ));
                		                        
                		                        $query = $pg->update('nota_saida_formapgto',
                		                            array('id_nota_saida', 'parcela'),
                		                            array($idNotaSaida, $rowPayment['parcela']), 
                		                            array('id_empresa' => $idEmpresa,
                    		                            'id_cliente' => $customerId,
                		                                'data_emissao' => $pedidoData,
                    		                            'parcela' => $parcela,
                    		                            'id_tipo_cobranca' => $tipoCobranca,
                    		                            'id_cartao' => $idCartao,
                		                                'valor' => $valorPedido, //($payment['ValorTotal'] - $freteCobrado),
                    		                            'dt_registro' => $payment['DataAutorizacao'],
                    		                            'id_nota_saida' => $idNotaSaida,
                		                                'valor_produtos' =>  $valorPedido, //($payment['ValorTotal'] - $freteCobrado),
                    		                            'porc_taxa_cartao' => $porc_taxa_cartao,
                    		                            'nsu' => $nsu,
                    		                            'id_bandeira' => $id_bandeira,
                    		                            'id_operadora' => $id_operadora,
                		                              )
                		                            );
                		                        
                		                      }
                		                      
                		                  }
                    		              $parcela++;
                		              }
                		            
                    		      }
                    		      
        		              }
        		              
        		              if(isset($idNotaSaida)){ 
        		                  $sqlUpdate = "UPDATE orders SET sent = 'T', id_nota_saida = {$idNotaSaida}
                                  WHERE store_id = {$order['store_id']} AND id = {$order['id']}";
            		              $imported++;
            		              $db->query($sqlUpdate);
            		              unset($idNotaSaida);
        		              }
            		      
        		        } 
        		        
        		    }
    		        
    		    }
		    
		    }
		    
		    logSyncEnd($db, $syncId, $imported);
		    break;
		    
		    
		case "xml_nota_saida":
		    
		    $ordersModel = new OrdersModel($db);
		    
		    $ordersModel->store_id = $storeId;
		    $ordersModel->id = isset($_REQUEST["order_id"])  ? intval($_REQUEST["order_id"]) : NULL ;
		    $orders = $ordersModel->ExportOrderDetails();
		    $sql = "SELECT encode(arquivo, 'escape') as arquivo FROM nota_saida_xml WHERE id_nota_saida = '{$orders[0]['id_nota_saida']}'";
		    $query = $pg->query($sql);
		    $row = $query->fetch(PDO::FETCH_ASSOC);
// 		    pre($row);die;
		    break;
		    
		    

	}

}

