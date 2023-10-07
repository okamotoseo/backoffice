<?php
function exportOrder($connPgSeta, $empresa, $vendedor, $orderId){
	$count = 0;

	$sqlSelect = "SELECT orders.id, orders.PedidoId, orders.Bairro, orders.Cep, orders.Cidade, orders.Complemento, orders.DataPedido, orders.Endereco, orders.channel_id,
	orders.Estado, orders.Ip, orders.NomeDestino, orders.Numero, orders.Parcelas, orders.PrazoEnvio, orders.RG, orders.Status, orders.Subtotal, orders.UsuarioId, orders.ValorFrete, orders.ValorFreteEmpresa, orders.ValorJuros, orders.ValorParcelas, orders.ValorPedido, orders.ValorCupomDesconto, orders.TransportadoraFrete, orders.ParceiroId, orders.AnaliseFraude,
	clients.UsuarioId,clients.EcommerceId,clients.TipoPessoaId,clients.OrigemContatoId,clients.EnderecoResidencialId,clients.EnderecoEntregaId,clients.TipoSexoId,clients.FormaPagamentoId,clients.TipoPreferenciaUsuarioId,clients.Nome,clients.CPF,clients.Email,clients.RG,clients.TelefoneResidencial,clients.TelefoneCelular,clients.TelefoneComercial,clients.DataNascimento,clients.RazaoSocial,clients.CNPJ,clients.InscricaoEstadual,clients.Responsavel,clients.DataCriacao,clients.DataAtualizacao,clients.Endereco,clients.Numero,clients.Complemento,clients.Bairro,clients.Cidade,clients.Estado,clients.CEP,clients.ParceiroID,
	payments.PedidoPagamentoId, payments.PedidoId, payments.FormaPagamentoId, payments.NumeroParcelas, payments.ValorParcela, payments.ValorDesconto, payments.ValorJuros, payments.ValorTotal, payments.NSU, payments.NumeroAutorizacaoPagamento, payments.DataAutorizacaoPagamento,
	channels.name as channel_name
	FROM orders
	JOIN channels ON orders.channel_id = channels.id
	JOIN clients ON orders.client_id = clients.id
	JOIN payments ON orders.id = payments.order_id
	WHERE orders.id = {$orderId}";

	$pedido = mysql_fetch_assoc(mysql_query($sqlSelect));
        pre($pedido);die;

	switch($pedido['TipoPessoaId']){
		case 1:
			$pessoafj = 1;
			$nome = strtoupper(prepareString($pedido['Nome']));
			$cpfCnpj = trim($pedido['CPF']);
			$rgie = trim($pedido['RG']);
			break;

		case 2:
			$pessoafj = 2;
			$nome = strtoupper(prepareString($pedido['RazaoSocial']));
			$cpfCnpj = trim($pedido['CNPJ']);
			$rgie = trim($pedido['InscricaoEstadual']);
			break;

	}

	$idCliente = existCpfCnpj($connPgSeta, $cpfCnpj);
	if(empty($idCliente)){
		$numero = isset($pedido['Numero']) ? prepareString(substr($pedido['Numero'], 0, 9)) : "NULL" ;
		$endereco = strtoupper(prepareString($pedido['Endereco']));
		$endereco .= ", ".$numero;
		$bairro = strtoupper(prepareString(substr($pedido['Bairro'], 0, 30)));
		$cidade = strtoupper(prepareString($pedido['Cidade']));
		$telefone1 = strlen($pedido['TelefoneResidencial'])> 15 ? substr(preg_replace("/[^0-9]/", "", $pedido['TelefoneResidencial']), 0, 15) : $pedido['TelefoneResidencial'] ;
		$telefone2 = strlen($pedido['TelefoneCelular'])> 15 ? substr(preg_replace("/[^0-9]/", "", $pedido['TelefoneCelular']), 0, 15) : $pedido['TelefoneCelular'] ;
		$email = substr($pedido['Email'], 0, 50);
		$sexo = $pedido['TipoSexoId'] == 1 ? 'M' : 'F';
		$cep = formataCEP($pedido['CEP']);
		$uf = $pedido['Estado'];
		$codCidade = getCodigoCidade($connPgSeta, $cep);
			
		if(empty($pedido['DataNascimento'])){
			$dtNascimento = "NULL";
		}else{
			$dateNascimento = explode("T", $pedido['DataNascimento']);
			$dtNascimento = "'{$dateNascimento[0]}'";
		}
		$sqlInsert = "INSERT INTO pessoas (
			nome,
			apelido,
			cpfcnpj,
			pessoa,
			rgie,
			endereco,
			cidade,
			bairro,
			uf,
			cep,
			codcidade,
			telefone1,
			telefone2,
			email,
			sexo,
			nascimento,
			cliente,
			cadastro,
			status,
			contribuinte
		) VALUES (
			UPPER('" . removeAcentos(utf8_encode($nome)) . "'),
			UPPER('" . substr(removeAcentos(utf8_encode($nome)), 0, 30) . "'),
			'" . $cpfCnpj . "',
			" . $pessoafj . ",
			'" . $rgie . "',
			UPPER('" . substr(removeAcentos(utf8_encode($endereco)), 0, 50) . "'),
			UPPER('" . substr(removeAcentos(utf8_encode($cidade)), 0, 30) . "'),
			UPPER('" . substr(removeAcentos(utf8_encode($bairro)), 0, 30) . "'),
			'" . $uf . "',
			'" . $cep . "',
			'" . $codCidade . "',
			'" . $telefone1 . "',
			'" . $telefone2 . "',
			'" . $email . "',
			'" . $sexo . "',
			" . $dtNascimento . ",
			TRUE,
			'" . date('Y-m-d') . "',
			'A',
			1
		) RETURNING codigo;";


		$resInsert = pg_query($connPgSeta, $sqlInsert);
		$resCliente = pg_fetch_assoc($resInsert);
		$idCliente = $resCliente['codigo'];

		if(!empty($idCliente)){
			$count++;
		}else{
			echo $sqlInsert;
		}
		unset($sqlInsert);
	}
// 	echo $idCliente;
// 	echo " - ";
	if(!empty($idCliente)){

		$dataHora = explode(" ", $pedido['DataPedido']);
		if(strlen($dataHora[1]) > 5){
			$difHora = 5 - strlen($dataHora[1]);
			$dataHora[1] = substr($dataHora[1], 0, $difHora);
		}

		$sqlVerify = "SELECT count(*) total FROM vendas WHERE cliente = '{$idCliente}' AND status = 'P'
		AND data = '{$dataHora[0]}' AND hora = '{$dataHora[1]}'";
		$resVerifySale = pg_fetch_assoc(pg_query($connPgSeta, $sqlVerify));
		if($resVerifySale['total'] == 0 ){
			$payment = getPaymentName($pedido['FormaPagamentoId']);
			$forma_pagamento = strtoupper($payment['method']);
			$parcelas =  $pedido['NumeroParcelas'];
			$condicao_pagamento = getPaymentCondition($payment['type'], $parcelas);
			$status = "P";// Pendente
			$ajustex = "0";
			$ajuste = "0";
			$vmanual = "0";
			$pedidoId = $pedido['PedidoId'];
			
			$sqlCountItems = "SELECT count(*) as total from `item`  WHERE order_id = {$pedido['id']}";
			$resCountItems = mysql_fetch_assoc(mysql_query($sqlCountItems));
					
			$frete = $pedido['ValorFrete'];
			$desconto = $pedido['ValorCupomDesconto'];

			$subTotal = $pedido['Subtotal'];
			$parcelas = $pedido['NumeroParcelas'];
			$valorTotal = $pedido['ValorTotal'];

			$desconto = $pedido['ValorDesconto'];
			$ajustex = $desconto > 0 ? 'D': '';//'A',
			$ajuste = $desconto;
			$vmanual = $desconto > 0 ? str_replace(",", ".", ($desconto * -1)): '0.00';
			$valorTotal = str_replace(",", ".", ($subTotal + $frete - $desconto));

			$obs = '<PARCEIRO>' . $pedido['channel_name'] . '</PARCEIRO>' .
					'<VENDA>' . $pedidoId . '</VENDA>' .
					'<TIPO>' . $forma_pagamento . '</TIPO>' .
					'<FORMA>' . ($parcelas === 1 ? '1 VEZ' : $parcelas . ' VEZES') . '</FORMA>';

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
				'01',
				'01',
				'" . $idCliente . "',
				'" . $vendedor . "',
				'" . $condicao_pagamento . "',
				'1',
				'" . $status . "',
				'" . $dataHora[0] . "',
				'" . $dataHora[1] . "',
				'" . $resCountItems['total'] . "',
				'" . $subTotal . "',
				'" . $ajustex . "',
				'" . $ajuste . "',
				'" . $vmanual . "',
				'" . $frete . "',
				'" . $valorTotal . "',
				'" . $valorTotal . "',
				'" . $obs . "',
				TRUE
			) RETURNING codigo;";

			$resInsertVenda = pg_query($connPgSeta, $sqlInsertVenda);
			$resVenda = pg_fetch_assoc($resInsertVenda);
			$idVenda = $resVenda['codigo'];

// 			echo $idVenda;
// 			echo " - ";
			if(!empty($idVenda)){
				$sqlItems = "SELECT * from `item`  WHERE order_id = {$pedido['id']}";
				$resItem = mysql_query($sqlItems);
				while($item = mysql_fetch_assoc($resItem)){
					$total = str_replace(",", ".", ($item['ProdutoQuantidade'] * $item['PrecoUnitario']));
					$custo = getERPCusto($connPgSeta, substr($item['SKU'], 0, -2));
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
						'" . $pedido['DataPedido'] . "',
						'01',
						'" . $item['SKU'] . "',
						'" . $item['ProdutoQuantidade'] . "',
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
						'" . $item['Preco_Venda'] . "'
					) RETURNING codigo;";
					$resInsertMovimento = pg_query($connPgSeta, $sqlInsertMovimento);
					$resMovimento = pg_fetch_assoc($resInsertMovimento);
					$idMovimento = $resMovimento['codigo'];
					
// 					echo $idMovimento;
// 					echo " - ";
				}
			}
		}else{
			$message = "warning|Venda ja exportada!";
		}

		if(!empty($idMovimento)){
			$message = "success|Exportação efetuada com sucesso!";
		}else if (empty($message)){
			$message = "error|Erro ao exportar venda!";
		}
	}
	return $message;
}















function exportOrderFbits($connPgSeta, $empresa, $vendedor, $orderId){
	$count = 0;
	$order =  getPedido($orderId);
	
	switch($order->SelectResult->Usuario->TipoPessoaId){
		case 1:
			$pessoafj = 1;
			$nome = strtoupper(prepareString($order->SelectResult->Usuario->Nome));
			$cpfCnpj = trim($order->SelectResult->Usuario->CPF);
			$rgie = trim($order->SelectResult->Usuario->RG);
			break;
	
		case 2:
			$pessoafj = 2;
			$nome = strtoupper(prepareString($order->SelectResult->Usuario->RazaoSocial));
			$cpfCnpj = trim($order->SelectResult->Usuario->CNPJ);
			$rgie = trim($order->SelectResult->Usuario->InscricaoEstadual);
			break;
	
	}
	
	$idCliente = existCpfCnpj($connPgSeta, $cpfCnpj);
	if(empty($idCliente)){
		$numero = isset($order->SelectResult->Usuario->Numero) ? prepareString(substr($order->SelectResult->Usuario->Numero, 0, 9)) : "NULL" ;
		$endereco = strtoupper(prepareString($order->SelectResult->Usuario->Endereco));
		$endereco .= ", ".$numero;
		$bairro = strtoupper(prepareString(substr($order->SelectResult->Usuario->Bairro, 0, 30)));
		$cidade = strtoupper(prepareString($order->SelectResult->Usuario->Cidade));
		$telefone1 = strlen($order->SelectResult->Usuario->TelefoneResidencial)> 15 ? substr(preg_replace("/[^0-9]/", "", $order->SelectResult->Usuario->TelefoneResidencial), 0, 15) : $order->SelectResult->Usuario->TelefoneResidencial ;
		$telefone2 = strlen($order->SelectResult->Usuario->TelefoneCelular)> 15 ? substr(preg_replace("/[^0-9]/", "", $order->SelectResult->Usuario->TelefoneCelular), 0, 15) : $order->SelectResult->Usuario->TelefoneCelular ;
		$email = $order->SelectResult->Usuario->Email;
		$sexo = $order->SelectResult->Usuario->TipoSexoId == 1 ? 'M' : 'F';
		$cep = formataCEP($order->SelectResult->Usuario->CEP);
		$uf = $order->SelectResult->Usuario->Estado;
		$codCidade = getCodigoCidade($connPgSeta, $cep);
			
		if(empty($order->SelectResult->Usuario->DataNascimento)){
			$dtNascimento = "NULL";
		}else{
			$dateNascimento = explode("T", $order->SelectResult->Usuario->DataNascimento);
			$dtNascimento = "'{$dateNascimento[0]}'";
		}
		$sqlInsert = "INSERT INTO pessoas (
			nome,
			apelido,
			cpfcnpj,
			pessoa,
			rgie,
			endereco,
			cidade,
			bairro,
			uf,
			cep,
			codcidade,
			telefone1,
			telefone2,
			email,
			sexo,
			nascimento,
			cliente,
			cadastro,
			status,
			contribuinte
		) VALUES (
			UPPER('" . removeAcentos(utf8_encode($nome)) . "'),
			UPPER('" . substr(removeAcentos(utf8_encode($nome)), 0, 30) . "'),
			'" . $cpfCnpj . "',
			" . $pessoafj . ",
			'" . $rgie . "',
			UPPER('" . substr(removeAcentos(utf8_encode($endereco)), 0, 50) . "'),
			UPPER('" . substr(removeAcentos(utf8_encode($cidade)), 0, 30) . "'),
			UPPER('" . substr(removeAcentos(utf8_encode($bairro)), 0, 30) . "'),
			'" . $uf . "',
			'" . $cep . "',
			'" . $codCidade . "',
			'" . $telefone1 . "',
			'" . $telefone2 . "',
			UPPER('" . $email . "'),
			'" . $sexo . "',
			" . $dtNascimento . ",
			TRUE,
			'" . date('Y-m-d') . "',
			'A',
			1
		) RETURNING codigo;";
		
		
		$resInsert = pg_query($connPgSeta, $sqlInsert);
		$resCliente = pg_fetch_assoc($resInsert);
		$idCliente = $resCliente['codigo'];
		
		if(!empty($idCliente)){
			$count++;
		}else{
			echo $sqlInsert;
			echo "<br><br>";
		}
		unset($sqlInsert);
	}
// 	echo $idCliente;
// 	echo " - ";
	if(!empty($idCliente)){
		
		$dataPedido = explode("T", $order->SelectResult->DataPedido);
		$horaPedido = substr($dataPedido[1], 0, 5);
		
		$sqlVerify = "SELECT count(*) total FROM vendas WHERE cliente = '{$idCliente}' AND status = 'P' 
		AND data = '{$dataPedido[0]}' AND hora = '{$horaPedido}'";
		$resVerifySale = pg_fetch_assoc(pg_query($connPgSeta, $sqlVerify));
		if($resVerifySale['total'] == 0 ){
			$payment = getPaymentName($order->SelectResult->FormasPagamento->IntegracaoPedidoPagamentoInfo->FormaPagamentoId);
			$forma_pagamento = strtoupper($payment['method']);
			$parcelas =  $order->SelectResult->FormasPagamento->IntegracaoPedidoPagamentoInfo->NumeroParcelas;
			$condicao_pagamento = getPaymentCondition($payment['type'], $parcelas);
			$status = "P";// Pendente
			$ajustex = "0";
			$ajuste = "0";
			$vmanual = "0";
			$pedidoId = $order->SelectResult->PedidoId;
			$itens = count($order->SelectResult->Itens->IntegracaoPedidoProdutoInfo);
			$frete = $order->SelectResult->ValorFrete;
			$desconto = $order->SelectResult->ValorCupomDesconto;
			
			$subTotal = $order->SelectResult->Subtotal;
			$parcelas = $order->SelectResult->FormasPagamento->IntegracaoPedidoPagamentoInfo->NumeroParcelas;
			$valorTotal = $order->SelectResult->FormasPagamento->IntegracaoPedidoPagamentoInfo->ValorTotal;
			
			$desconto = $order->SelectResult->FormasPagamento->IntegracaoPedidoPagamentoInfo->ValorDesconto;
			$ajustex = $desconto > 0 ? 'D': '';//'A',
			$ajuste = $desconto;
			$vmanual = $desconto > 0 ? str_replace(",", ".", ($desconto * -1)): '0.00';
			$valorTotal = str_replace(",", ".", ($subTotal + $frete - $desconto));
			
			$obs = '<VENDA>' . $pedidoId . '</VENDA>' .
					'<TIPO>' . $forma_pagamento . '</TIPO>' .
					'<FORMA>' . ($parcelas === 1 ? '1 VEZ' : $parcelas . ' VEZES') . '</FORMA>';
			
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
				'01',
				'01',
				'" . $idCliente . "',
				'" . $vendedor . "',
				'" . $condicao_pagamento . "',
				'1',
				'" . $status . "',
				'" . $dataPedido[0] . "',
				'" . $horaPedido . "',
				'" . $itens . "',
				'" . $subTotal . "',
				'" . $ajustex . "',
				'" . $ajuste . "',
				'" . $vmanual . "',
				'" . $frete . "',
				'" . $valorTotal . "',
				'" . $valorTotal . "',
				'" . $obs . "',
				TRUE
			) RETURNING codigo;";
			
			$resInsertVenda = pg_query($connPgSeta, $sqlInsertVenda);
			$resVenda = pg_fetch_assoc($resInsertVenda);
			$idVenda = $resVenda['codigo'];
			
	// 		echo $idVenda;
	// 		echo " - ";
			if(!empty($idVenda)){
				$orderIntegration = count($order->SelectResult->Itens->IntegracaoPedidoProdutoInfo) > 1 ? $order->SelectResult->Itens->IntegracaoPedidoProdutoInfo : $order->SelectResult->Itens ;
			
				foreach ($orderIntegration as $key => $item){
					$total = str_replace(",", ".", ($item->ProdutoQuantidade * $item->PrecoUnitario)); 
					$custo = getERPCusto($connPgSeta, substr($item->SKU, 0, -2));
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
						'" . $dataPedido[0] . "',
						'01',
						'" . $item->SKU . "',
						'" . $item->ProdutoQuantidade . "',
						'" . $item->PrecoUnitario . "',
						'" . $total . "',
						'" . $custo . "',
						'" . $item->PrecoUnitario . "',
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
						'" . $item->Preco_Venda . "'
					) RETURNING codigo;";
					$resInsertMovimento = pg_query($connPgSeta, $sqlInsertMovimento);
					$resMovimento = pg_fetch_assoc($resInsertMovimento);
					$idMovimento = $resMovimento['codigo'];
				
				}
			}
		}else{
			$message = "warning|Venda ja exportada!";
		}
		
		if(!empty($idMovimento)){
			$message = "success|Exportação efetuada com sucesso!";
		}else if (empty($message)){
			$message = "error|Erro ao exportar venda!";
		}
	}
	return $message;
}

function debugArray($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}
?>