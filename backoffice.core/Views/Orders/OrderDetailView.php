<?php 

$path = dirname(__FILE__);
// echo $path .'/../../library/BarcodeD/src/BarcodeGenerator.php';

include($path .'/../../library/BarcodeD/src/BarcodeGenerator.php');

include($path .'/../../library/BarcodeD/src/BarcodeGeneratorPNG.php');

$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();

?>
<div class="row order_row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header order">
	        	<h3 class="box-title">Informações Detalhadas do Pedido</h3>
	        	<div class='pull-right'>
	        	<?php
// 	        	pre($order);die;
    	        	//TODO: Substituir por informações do painel de configuração do sistema
    	        	switch($storeModel->id){
    	        	    case "4": $exportTo = "Sysemp"; break;
    	        	    case "5": $exportTo = "Magento"; break;
    	        	    case "6": $exportTo = "Scaquete"; break;
    	        	    default : $exportTo = "Sysplace"; break;
    	        	}
    	        	if(isset($exportTo)){
    	        	    
    	        	    $exported = $order['sent'] == 'T' ? "Reexportar" : "Exportar" ;
    	        	    
    	        	    echo "<button id='btn-export-order' order_id='{$order['id']}' store_id='{$order['store_id']}' export_to='{$exportTo}' class='btn btn-primary btn-sm' type='button' value='{$exported}'><i class='fa fa-download'></i> {$exported}</button>";
        	        	
        	        	
//         	        	if($order['sent'] == 'T'){
//         	        	    echo "<button  class='btn btn-default btn-sm' type='button' value='Exportado' disabled><i class='fa fa-check'></i> Enviado</button>";
        	        	    
//         	        	}else{
//         	        	    echo "<button id='btn-export-order' order_id='{$order['id']}' store_id='{$order['store_id']}' export_to='{$exportTo}' class='btn btn-primary btn-sm' type='button' value='Exportar'><i class='fa fa-download'></i> Exportar</button>";
//         	        	}
    	        	}
    	        	
    	        	
    	        	if(isset($order['shipping_id'])){
    	        	    $partesData = explode(" ",$order['DataPedido']);
    	        	    echo "<button shipping_id='{$order['shipping_id']}' data_pedido='{$partesData[0]}' store_id='{$order['store_id']}'  class='btn btn-default  btn-sm' id='shippment_label_pdf' type='button'><i class='fa fa-truck'></i> Etiqueta</button>";
    	        	}
    	        	$printed = $order['printed'] == 'F' ? "Imprimir" : "Reimprimir" ;
	        		
	        		echo "<button id='btn-print-order'  
                            class='btn btn-default btn-sm btn-print-order'  
                            order_id='{$order['id']}' 
                            pedido_id='{$order['PedidoId']}'
                            store_id='{$order['store_id']}' 
                            type='button' 
                            value='Imprimir' 
                            picker='{$this->storedata['store']}' 
                            user='{$this->userdata['name']}' 
                            picking_id='new'
                            ><i class='fa fa-print'></i> {$printed}</button>";
	        		?>
	        	</div>
			</div><!-- /.box-header -->
			
			<div class="box-body">
			
				<style type="text/css">
				 @media print{
					#report, #headerreport{display: block !important;}
					.main-sidebar, .left-side, .sidebar,
					.navbar, .filter-report, 
					.date, .noprint, 
					.datepicker, 
					.dropdown-menu, .footer, .logo, .top-actions, .credit, .alert
					#btn-print, #form-order, #footer, .alert-warning, .breadcrumb, 
					.main-footer, #myModal, .printed, .view, .order {display: none;}
					.content{padding:0px !important;}
					
				}
				</style>
				<div class="message">
				<?php if(!empty( $ordersModel->form_msg)){ echo  $ordersModel->form_msg;}?>
				<?php 
// 					if(!empty($sucess['message'])){ 
// 						echo "<div class='alert alert-success'>".$sucess['message']."</div>";
// 					}
// 					if(!empty($alert)){
// 						echo "<div class='alert alert-warning'>".$alert."</div>";
// 					}
// 					if(!empty($error)){
// 						$errorMessage = "<div class='alert alert-danger'>";
// 						foreach($error as $campo => $valor){
// 							$errorMessage .= "<h4>".$valor."</h4>";
// 						}
// 						echo $errorMessage .= "</div>";
// 					}
				?>
				<?php 
// 				if(isset($pedido->SelectResult)){
// 					 $devolution = verifyClient($pedido->SelectResult);
				
// 					if(!empty($devolution[0])){
// 						echo "<div class='alert alert-danger'>
// 								<strong>ATENÇÃO -> Cliente já efetuou devoluções por:</strong><br>";
// 						foreach($devolution as $value){
// 							echo $value['reason']." - ".$value['description_reason']." - <a onclick=\"javascript:popup('/xml-tool/fort_order_datail.php?pedido_id={$value['order_id']}','1100','700');\">".$value['order_id']."</a> - ".$value['date'];
							
// 						}
// 						echo "</div>";
// 					}
// 				}
				
				?>
				</div>
				<div class='order_view' style='font-family: Helvetica Neue,Helvetica,Arial,sans-serif;line-height: 20px;'>
					<div class="confirmacaoPedido">
						<div class="content">
							<div class="boxes usuario" >
								<div style='background: #f5f5f5; border-bottom: 1px solid #000;'>
									<h3 style='margin-bottom:0px;margin-left:10px;display:inline-block;'>Detalhes do Pedido</h3>
									<span style='text-align: right;float: right;font-size: 18px;font-weight: bold;margin: 10px;'><?php echo '<img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($order['PedidoId'], $generatorPNG::TYPE_CODE_128, 1, 20)) . '"> '.$order['PedidoId']; ?></span>
								</div>
								
								<div class="dadosUsuario">
									<p><strong>Número Pedido:</strong> <?php echo $order['PedidoId'];?></p>
									<p><strong>Data da compra:</strong> <?php echo dateTimeBr($order['DataPedido'], "/"); ?></p>
									<p><strong>Situação:</strong> <?php echo getSystemDefaultPaymentStatus($order['Status'], 'label'); ?></p>
									<?php if(!empty( $order['fiscal_key'])){ ?>
									<p><strong>Nota Fiscal:</strong> <?php echo $order['fiscal_key'] ?></p>
									<?php } ?>
								</div>
							
								<div style='background: #f5f5f5; border-bottom: 1px solid #000;'>
									<h3 style='margin-bottom:0px;margin-left:10px;'>Dados do Usuário</h3>
								</div>
								
								<table cellpadding="1" cellspacing="0"  width='100%'>
									<tbody>
										<tr>
											<td><strong>Nome:</strong></td>
											<td><?php echo $order['Nome']; ?></td>
											<td><strong>Email:</strong></td>
											<td><?php echo $order['Email']; ?></td>
										</tr>
										<tr>
											<td><strong>Endereço:</strong></td>
											<td><?php echo $order['Endereco'];  ?>, <?php echo $order['Numero']; ?> <?php echo $order['Complemento']; ?></td>
											<td><strong>Telefone Residencial: </strong></td>
											<td><?php echo $order['Telefone']; ?></td>
										</tr>
										<tr>
											<td><strong>Bairro:</strong></td>
								            <td><?php echo $order['Bairro']; ?></td>
								            <td><strong>Telefone Celular:</strong></td>
											<td><?php echo $order['Telefone']; ?></td>
								        </tr>
								        <tr>
											<td><strong>Cidade / Estado:</strong></td>
								            <td><?php echo $order['Cidade']; ?> - <?php echo $order['Estado']; ?></td>
								            <td><strong>Telefone Comercial: </strong></td>
											<td><?php echo $order['Telefone']; ?></td>
								        </tr>
								        <tr>
											<td><strong>CEP:</strong></td>
								            <td><?php echo $order['Cep']; ?></td>
										</tr>
										<tr>
											<td><strong>CPF/CNPJ:</strong></td>
											<td><?php echo $order['customer']['CPFCNPJ']; ?></td>
										</tr>
										
									</tbody>
								</table>
							</div>
				
				
							<div class="boxes frete" style="margin-bottom: 0;">
								<div style='background: #f5f5f5; border-bottom: 1px solid #000;'>
									<h3 style='margin-bottom:0px;margin-left:10px;'>Produtos</h3>
								</div>
								<table cellpadding="1" cellspacing="0" width='100%' align="center">
									<thead>
										<tr style='background:#f5f5f5;color:#000;height:25px;padding:5px;'>
											<th style='border-right: 1px solid #ddd;border-bottom: 1px solid #000;text-align:center' >Foto</th>
											<th style='border-right: 1px solid #ddd;border-bottom: 1px solid #000;text-align:center' >SKU</th>
											<th style='border-right: 1px solid #ddd;border-bottom: 1px solid #000;text-align:left; padding-left:10px;' >Nome</th>
											<th style='border-right: 1px solid #ddd;border-bottom: 1px solid #000;text-align:center' >Qtd</th>
											<th style='border-right: 1px solid #ddd;border-bottom: 1px solid #000;text-align:center' >Valor Unitário</th>
											<th style='border-right: 1px solid #ddd;border-bottom: 1px solid #000;text-align:center' >Valor</th>
										</tr>
									</thead>
									<tbody>
										<?php
// 										pre($order['items']);die;
										
										foreach($order['items'] as $keyItem => $rowItem){
										    $reference = '';
										    $brand = '';
										    $query = $this->db->query("SELECT * FROM available_products WHERE store_id = {$order['store_id']} AND sku LIKE '{$rowItem['SKU']}'");
										    $queryAP = $query->fetch(PDO::FETCH_ASSOC);
										    if(isset($queryAP['id'])){
										        $reference = $queryAP['reference'];
										        $brand  = $queryAP['brand'];
										    }
										    
											$precoUnitario = number_format($rowItem['PrecoUnitario'], 2, ',', '.');
											$precoVenda = number_format($rowItem['PrecoVenda'] * $rowItem['Quantidade'], 2, ',', '.');
											echo "<tr style='border-bottom: 1px solid #DDD;font-size: 16px;'>
											<td align='center' style='padding:5px;border-right: 1px solid #DDD;border-bottom:1px solid #DDD;'><img src='{$rowItem['UrlImagem']}' width='70' /></td>
											<td align='center' style='padding:5px;border-right: 1px solid #DDD;border-bottom:1px solid #DDD;'>{$rowItem['SKU']}</td>
											<td align='left' width='50%' style='padding:5px;padding-left:10px;border-right: 1px solid #DDD;border-bottom:1px solid #DDD;color:#000;'>
												<b>{$rowItem['Nome']}</b> <br> 
												<span class='nameProduto'>";
											echo "<b>Referencia:</b> ".$reference."\t <br><b>Marca:</b> ".$brand;
    											echo "<br>";
											       foreach($rowItem['item_attributes'] as $keyItemAttr => $attr){
											           echo "<b>{$attr['Nome']}</b>";
														echo ": ";
														echo $attr['Valor'];
														echo "<br>";
													}
												 
												 echo "</span>
											</td>
											<td align='center' style='font-size:18px;padding:5px;border-right:1px solid #DDD;border-bottom:1px solid #DDD;'><strong>{$rowItem['Quantidade']}</strong></td>
											<td align='center' style='padding:5px;border-right: 1px solid #DDD;border-bottom:1px solid #DDD;'>R$ {$precoUnitario}</td>
											<td align='center' style='padding:5px;border-bottom:1px solid #DDD;'>R$ {$precoVenda}</td>
											</tr>";
											}
										?>
									</tbody>
									<tfoot>
									
										<tr style='border-bottom: 1px solid #DDD;font-size: 14px;'>
											<td colspan="5" style='text-align: right;padding: 10px;line-height: 18px;border-bottom:1px solid #DDD;'>SubTotal</td>
											<td style='border-left: 1px solid #DDD;border-bottom:1px solid #DDD;font-weight: bold;text-align: center;' width="100">R$ <?php echo number_format($order['Subtotal'], 2, ',', '.'); ?></td>
										</tr>
											<?php if($order['ValorCupomDesconto'] != 0){?>
											<tr style='border-bottom: 1px solid #DDD;font-size: 14px;'>
									          <td colspan="5" style='text-align: right;border-bottom:1px solid #DDD;padding: 10px;line-height: 18px;'>Valor desconto</td>
									          <td style='border-left: 1px solid #DDD;border-bottom:1px solid #DDD;font-weight: bold;text-align: center;' width="100">R$ <?php echo number_format($order['ValorCupomDesconto'], 2, ',', '.'); ?></td>
									        </tr>
								        <?php } ?>
										<tr style='border-bottom: 1px solid #DDD;font-size: 14px;'>
											<td colspan="5" style='text-align: right;border-bottom:1px solid #DDD;padding: 10px;line-height: 18px;'>Frete</td>
											<td style='border-left: 1px solid #DDD;border-bottom:1px solid #DDD;font-weight: bold;text-align: center;' width="100"><?php echo  $order['ValorFrete'] > 0 ? "R$ ".number_format($order['ValorFrete'], 2, ',', '.') : "Grátis"; ?></td>
										</tr>
									
										<tr style='border-bottom: 1px solid #DDD;font-size: 14px;'>
											<td colspan="5" style='text-align: right;border-bottom:1px solid #DDD;padding: 10px;line-height: 18px;'>Total:</td>
											<td style='border-left: 1px solid #DDD;border-bottom:1px solid #DDD;font-weight: bold;text-align: center;' width="120">R$ <?php echo  number_format($order['ValorPedido'], 2, ',', '.'); ?></td>
										</tr>
										
									</tfoot>
								</table>
							</div>
							
							<div class="boxes pagamentos" style="margin-bottom: 0;">
								<div style='background: #f5f5f5; border-bottom: 1px solid #000;'>
									<h3 style='margin-bottom:0px;margin-left:10px;'>Pagamento</h3>
								</div>
				
								<table cellpadding="1" cellspacing="0" >
									<tbody>
									<?php 
									   foreach($order['payments'] as $keyPay => $rowPayment){ ?>
									   	<tr>
											<td><strong>Situação:&nbsp;</strong></td>
											<td> <?php echo  getSystemDefaultPaymentStatus($rowPayment['Situacao'], "label"); ?> </td>
										</tr>
										<tr>
											<td><strong>Forma de pagamento: &nbsp;</strong></td>
											<td><?php echo  $rowPayment['FormaPagamento'];?></td>
										</tr>
										<?php if($rowPayment['NumeroParcelas'] > 1){ ?>
											<tr>
												<td><strong>Operadora Cartão: </strong></td>
												<td>	
												<?php 
												if(strlen($rowPayment['NSU']) > 6){
														echo "REDE";
													}else{
														echo "CIELO";
													}
												
												?>
												</td>
											</tr>
											<tr>
												<td><strong>Numero Parcelas: </strong></td>
												<td> <?php echo $rowPayment['NumeroParcelas']; ?></td>
											</tr>
										<?php }?>
										<?php if($rowPayment['ValorParcela'] > 0){?>
											<tr>
												<td><strong>Valor da Parcela: </strong></td>
												<td>R$ <?php echo  number_format($rowPayment['ValorParcela'], 2, ',', '.'); ?></td>
											</tr>
										<?php } ?>
										<?php if($rowPayment['ValorDesconto'] > 0){?>
											<tr>
												<td><strong>Valor do Desconto: </strong></td>
												<td>R$ <?php echo  number_format($rowPayment['ValorDesconto'], 2, ',', '.'); ?></td>
											</tr>
										<?php }?>
										<tr>
											<td><strong>Valor: </strong></td>
											<td>R$ <?php echo number_format($rowPayment['ValorTotal'], 2, ',', '.'); ?></td>
										</tr>
										
										<?php if($rowPayment['NumeroAutorizacao'] > 0){?>
											<tr>
											<td><strong>NSU: </strong></td>
												<td><?php echo  $rowPayment['NSU']; ?></td>
											</tr>
																		<tr>
											<td><strong>Autorização: </strong></td>
												<td><?php echo  $rowPayment['NumeroAutorizacao']; ?></td>
											</tr>
																		<tr>
											<td><strong>Data Autorização: </strong></td>
												<td><?php echo  dateTimeBr($rowPayment['DataAutorizacao'], "/"); ?></td>
											</tr>
										<?php }
										
										} ?>
				                    
										<!--/tbody-->
									</tbody>
								</table>
							</div>
								
							<div class="boxes entrega" style="margin-bottom: 0;">
								<div style='background: #f5f5f5; border-bottom: 1px solid #000;'>
									<h3 style='margin-bottom:0px;margin-left:10px;'>
										Detalhes da Entrega
									</h3>
								</div>
								<table class="detalhesPedido" style='line-height: 30px;font-size:22px;' width='100%' >
									<tbody>
										<tr valign='center'>
											<td width='43%' style='padding-right:20px;border-right:1px solid #ddd;'>
											
												Remetente:<br>
												<strong><?php echo $storeModel->store;?></strong><br>
												<?php  echo $storeModel->address.", ".$storeModel->number;?><br>
												<?php  echo $storeModel->city."-".$storeModel->state;?><br>
												CEP: <?php  echo $storeModel->postalcode; ?>
											</td>
											<td width='57%' style='padding-left:20px;padding-top:10px;'>
												Destinatário:<br>
												<strong><?php echo $order['NomeDestino']; ?></strong><br>
												<?php echo $order['Endereco'];?>, <?php echo $order['Numero']; ?> - <?php echo $order['Bairro']; ?> <br>
												<?php echo empty($order['Complemento']) ? "" : $order['Complemento']."<br>"; ?>
												<?php echo $order['Cidade']; ?> - <?php echo $order['Estado']; ?><br>
												CEP: <?php echo $order['Cep']; ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>