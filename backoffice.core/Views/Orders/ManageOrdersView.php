<?php if ( ! defined('ABSPATH')) exit;?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty($sucess['message'])){ echo "<div class='callout callout-success'><h4>Tip!</h4><p>".$sucess['message']."</></div>";}?>
		</div>
		
		<div class="box box-primary">
			<form  method="POST" action="/Orders/Manage/" name="filter-print-order">
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar pedidos</h3>
					<div class='box-tools pull-right'>
    					<div class="form-group">
        	        		<a href='/Orders/Manage/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    				</div>
    			</div>
				<div class="box-body">
				
					<div class="row">
					
						<div class="col-md-4">
							<div class="form-group">
							
								<div class="col-md-12">
									<label>Pedido Id:</label> 
									<input type="text" name="PedidoId" id="PedidoId" placeholder='PedidoId' class="form-control input-sm"  value="<?php echo $ordersModel->PedidoId; ?>">
								</div>
								<div class="col-md-12">
									<label>Nome:</label> 
									<input type="text" name="Nome" id="Nome" placeholder='Nome' class="form-control input-sm" value="<?php echo $ordersModel->Nome; ?>">
								</div>
								<div class="col-md-12">
									<label>Email:</label> 
									<input type="text" name="Email" id="Email" placeholder='Email' class="form-control input-sm" value="<?php echo $ordersModel->Email; ?>">
								</div>
								<div class="col-md-8">
									<label>Cidade:</label> 
									<input type="text" name="Cidade" id="Cidade" placeholder='Cidade' class="form-control input-sm"  value="<?php echo $ordersModel->Cidade; ?>">
								</div>
								<div class="col-md-4">
									<label>UF:</label> 
									<select  name="Estado" class="form-control input-sm">
										<option value=''></option>
									<?php 
	    								if(isset($ufOrder)){
	    								    foreach($ufOrder as $k => $value){
										        $selected = $value['Estado'] == $ordersModel->Estado ? "selected" : '' ;
										        echo "<option value='{$value['Estado']}' {$selected}>{$value['Estado']}</option>";
										   }
										}?>
									</select>
								</div>
								
							</div>
	    				</div>
	    				<div class="col-md-4">
	    					<div class="form-group">
	    						<div class="col-md-12">
									<label>CPFCNPJ:</label> 
									<input type="text" name="CPFCNPJ" id="CPFCNPJ" placeholder='CPFCNPJ' class="form-control input-sm"  value="<?php echo $ordersModel->CPFCNPJ; ?>">
								</div>
	    					
		    					<div class="col-md-12">
									<label>Status:</label>
									<select  id="select_status" name="Status" class="form-control input-sm">
										<option value=''></option>
										<?php 
										if(isset($statusOrder)){
										    foreach($statusOrder as $k => $value){
										        $selected = $value['status'] == $ordersModel->Status ? "selected" : '' ;
										        echo "<option value='{$value['status']}' {$selected}>".  getSystemDefaultPaymentStatus($value['status'], 'label') ."</option>";
										   }
										}?>
									</select>
								</div>
								<div class="col-md-6">
									<label>Valor de:</label> 
									<input type="text" name="ValorPedido" id="ValorPedido" placeholder='R$' class="form-control input-sm"  value="<?php echo $ordersModel->ValorPedido; ?>">
								</div>
								<div class="col-md-6">
									<label>Valor até:</label> 
									<input type="text" name="ValorPedidoAte" id="ValorPedidoAte" placeholder='R$' class="form-control input-sm"  value="<?php echo $ordersModel->ValorPedidoAte; ?>">
								</div>
								<div class="col-md-12">
									<label>Pagamento:</label>
									<select  name="FormaPagamento" class="form-control input-sm">
									<option value=''></option>
									<?php 
	    								if(isset($paymentsOrder)){
	    								    foreach($paymentsOrder as $k => $value){
										        $selected = $value['FormaPagamento'] == $ordersModel->FormaPagamento ? "selected" : '' ;
										        echo "<option value='{$value['FormaPagamento']}' {$selected}>{$value['FormaPagamento']}</option>";
										   }
										}?>
									</select>
								</div>
								
							</div>
	    				</div>
	    				<div class="col-md-4">
	    					<div class="form-group">
	    					
		    					<div class="col-md-12">
									<label>Marketplace:</label>
									<select  name="Marketplace" class="form-control  input-sm">
									<option value=''></option>
									<?php 
									    if(isset($marketplaceOrder)){
	    								    foreach($marketplaceOrder as $k => $value){
	    								        $selected = $value['Marketplace'] == $ordersModel->Marketplace ? "selected" : '' ;
										        echo "<option value='{$value['Marketplace']}' {$selected}>{$value['Marketplace']}</option>";
										   }
										
										}?>
									</select>
								</div>
								<div class="col-md-6">
									<label>Data Inicial:</label> 
									<div class="input-append date" id="dp3" data-date="" data-date-format="dd/mm/aaaa">
										<input type="text"  name="DataPedido" id="data-1" class="form-control date-mask input-sm"  placeholder='dd/mm/aaaa' value="<?php echo $ordersModel->DataPedido;?>">
										<span class="add-on" ><i class="icon-th"></i></span>
									</div>
								</div>
								<div class="col-md-6">
									<label>Data Final:</label>				
									<div class="input-append date" id="dp3" data-date="" data-date-format="dd/mm/aaaa">
										<input type="text"  name="DataPedidoAte" id="data-2" class="form-control date-mask input-sm"  placeholder='dd/mm/aaaa' value="<?php echo $ordersModel->DataPedidoAte;?>">
										<span class="add-on"><i class="icon-th"></i></span>
									</div>
								</div>
								<div class="col-md-12">
									<?php 
										$printed = $notPrinted = '';
										switch($ordersModel->printed){
										    case "T": $printed = "selected"; break;
										    case "F": $notPrinted = "selected"; break;
										}?>
									<label for="printed">Impresso:</label>
									<select id="printed" name="printed" class="form-control input-sm">
										<option value=''></option>
										<option value='T' <?php echo $printed; ?>>Sim</option>
										<option value='F' <?php echo $notPrinted; ?>>Não</option>
									</select>
								</div>
								<div class="col-md-12">
									<?php 
										$select5 = $select50 = $select100 = $select150 = $select200 = '';
										switch($ordersModel->records){
										    case "5": $select5 = "selected"; break;
										    case "50": $select50 = "selected"; break;
										    case "100": $select100 = "selected"; break;
										    case "150": $select150 = "selected"; break;
										    case "200": $select200 = "selected"; break;
										}?>
									<label for="records">Registros:</label>
									<select id="records" name="records" class="form-control input-sm">
										<option value='5' <?php echo $select5; ?>>5</option>
										<option value='50' <?php echo $select50; ?>>50</option>
										<option value='100' <?php echo $select100; ?>>100</option>
										<option value='150' <?php echo $select150; ?>>150</option>
										<option value='200' <?php echo $select200; ?>>200</option>
									</select>
								</div>
							
							
							</div>
	    				</div>
	    				
	    			</div>
				</div>
				<div class="overlay" style='display:none;'>
            		<i class="fa fa-refresh fa-spin"></i>
        		</div>
				<div class="box-footer">
					<button type='submit' id='filter-order' name='filter-order' class='btn btn-primary btn-sm pull-right submit-load'><i class='fa fa-search'></i> Filtrar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
	
		<div class="box box-primary">
			<div class="box-header">
	        	<h3 class="box-title">Listagem de pedidos</h3>
	        	<div class="box-tools pull-right">
            			<div class="form-group">
                			<select id='select_action_orders' class='form-control input-sm'>
                				<option value='select' >Ações</option>
                				<option value='import_sysemp_orders_document' >Importar NFe Sysemp</option>
                				<option value='import_mercadolivre_orders' >Importar Pedidos Mercadolivre</option>
                				<?php if($storeId == 4){?>
                					<option value='send_mercadolivre_sysemp_orders_document' >Enviar NFe Mercadolivre</option>
                				<?php } ?>
                				<option value='send_tray_orders_document' >Enviar Nota Fiscal Tray</option>
            				</select>
            			</div>
				</div>
			</div><!-- /.box-header -->
			
			
		
			
			<div class="box-body table-responsive">
			
			
	<?php
    echo "<table class='table table-condensed  no-padding'>
    	<thead>
    		<tr>
                <th><input type='checkbox'  class='flat-red select_all_orders' /></th>
    			<th>PedidoId</th>
    			<th>Nome / Res</th>
                <th>Data</th>
    			<th>Status</th>
                <th>Frete</th>
    			<th>Total</th>
                <th>Ações</th>
    		</tr>
    	</thead>
    	<tbody>";
foreach($list as  $key => $row){
    $style = $ordersModel->ShowItems == 1 ? "style='border-top:none !important'" : "";
    $class = $ordersModel->ShowItems == 1 ? "warning" : "";
    $frete = $row['ValorFrete'] == 0 ? "Frete Grátis" : number_format($row['ValorFrete'], 2, ',', '.');
	$popup = "onclick=\"javascript:popup('".HOME_URI."/Orders/OrderDetail/id/{$row['id']}','1100','700');\"";
	$contentDeclaration = "onclick=\"javascript:popup('".HOME_URI."/Orders/OrderContentDeclaration/id/{$row['id']}','1100','700');\"";
// 	$printed = $row['printed'] == 'F' ? "<i class='fa fa-print'></i>" : "" ;
	$printed =  $row['printed'] == 'T' ? '' : "<span class='badge'><i class='fa fa-print' data-toggle='tooltip' title='O pedido não foi impresso'></i></span>" ;
	$logisticType = $row['logistic_type'] == 'fulfillment' ?  "<span class='badge bg-green'><i class='fa fa-flash' data-toggle='tooltip' title='Fulfillment'></i></span>" : '' ;
	$error = is_null($row['error']) ? '' : "<span class='badge bg-red'><i class='fa fa-warning' data-toggle='tooltip' title='{$row['error']}'></i></span>" ;
// 	$color = !isset($userPrint['user_print']) ? '#ff0000' : '' ;
	$color = $row['sent'] == 'T' ?  '#009900' : '#ff0000' ;
	
	
// 	$popup ="class='openOrder' id='{$row['PedidoId']}'";

	$canal  = '';
	switch($row['Marketplace']){
	    case "Onbi": $marketplace = "<small class='badge label-success' data-toggle='tooltip' title='Ecommerce'>Onbi</small>";break;   
	    case "Tray": 
	        
	        $marketplace = "<small class='badge label-primary' data-toggle='tooltip' title='Integração Tray'>Tray</small>";
	    								        
	        if($storeId == 4){
	            $canal =$row['Canal'];
    	        switch($row['Canal']){
    	            case 'LEROY MERLIN': $marketplace = "<small class='badge bg-green' data-toggle='tooltip' title='Integração LEROY MERLIN'>Tray<br>{$canal}</small>"; break;
    	            case 'MAGAZINE LUIZA': $marketplace = "<small class='badge bg-blue' data-toggle='tooltip' title='Integração MAGALU'>Tray<br>{$canal}</small>"; break;
    	        }
	        }
	        
	        break;
	    
	    case "Dafiti": $marketplace = "<small class='badge label-primary' data-toggle='tooltip' title='Dafiti'>Dafiti</small>";break;
	    case "Shopee": $marketplace = "<small class='badge bg-orange' data-toggle='tooltip' title='Shopee'>Shopee</small>";break;
	    case "Mercadolivre": 
	        if($storeId == 3){
	            $canal ="<br>{$row['Canal']}";
	        }
	        $marketplace = "<small class='badge bg-yellow' data-toggle='tooltip' title='Mercadolivre'>Mercadolivre {$canal}</small>";
    	    
	       break;
	    case "Lojas Americanas": $marketplace = "<small class='badge label-danger' data-toggle='tooltip' title='lojas Americanas'>Americanas</small>";break;
	    case "Submarino": $marketplace = "<small class='badge label-info' data-toggle='tooltip' title='Submarino'>Submarino</small>";break;
	    case "Amazon": $marketplace = "<small class='badge bg-blue' data-toggle='tooltip' title='Amazon'>Amazon</small>";break;
	    default : $marketplace = $row['Marketplace']; break;
    }
    
    $sendFiscalKey = false;
    $codeStatus = 0;
    $paymentStatus = getSystemDefaultPaymentStatus($row['Status']);
    switch($paymentStatus['label']){
        case "Pago": $bgColor='#CAFAD1'; $sendFiscalKey = true; $codeStatus =1; break;
        case "Aprovado": $bgColor='#CAFAD1';  $sendFiscalKey = true; $codeStatus =1; break;
        case "Cancelado": $bgColor='#FF7878'; $codeStatus =0; break;
        case "Faturado": $bgColor='#F9FACA'; $sendFiscalKey = true; $codeStatus =1; break;
        case "Aguardando Envio": $bgColor='#F9FACA'; $sendFiscalKey = true; $codeStatus =1; break;
        case "Preparando": $bgColor='#FAEFCA'; $sendFiscalKey = true; $codeStatus =1; break;
        case "Não enviado": $bgColor='#FAEFCA'; $sendFiscalKey = true; $codeStatus =1; break;
        case "Processando": $bgColor='#CAEEFA'; $sendFiscalKey = true;  $codeStatus =1; break;
        case "Pagamento Pendente": $bgColor='#FADBCA';$codeStatus =0;  break;
        case "Pendente": $bgColor='#FADBCA'; $codeStatus =0; break;
        case "Enviado": $bgColor='#E1E1E1'; $codeStatus =1; break;
        default : $bgColor='';break;
    }
	echo "<tr class='{$class} tr-order-{$row['id']}'>
    <td><input type='checkbox' id='{$row['id']}' PedidoId='{$row['PedidoId']}' class='flat-red select_one_order' /></td>
	<td><font color='{$color}' title=''>{$row['PedidoId']}</font> <br>{$marketplace} {$logisticType} {$printed} {$error}</td>
	<td {$popup}>{$row['Nome']}<br>".formatarCpfCnpj($row['CPFCNPJ'])."  {$row['Cidade']} -{$row['Estado']}</td>	
	<td {$popup}>".dateTimeBrBreakLine($row['DataPedido'], "/")."</td>
	<td {$popup} bgcolor='{$bgColor}' id='status-order-{$row['id']}'><div class='form-group has-{$paymentStatus['class']}' style='margin-bottom:0px;'><label class='control-label'>{$paymentStatus['label']}</label><br>{$row['FormaPagamento']}</div></td>
    <td {$popup}>{$frete}<br></td>
	<td {$popup}>".number_format($row['ValorPedido'], 2, ',', '.')."</td>
    <td>
        <div class='dropdown'>
            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
            <ul class='dropdown-menu pull-right' style='min-width:100px'>";
					
// 					if($this->userdata['cpf'] == '30456130802' && $row['sent'] != 'T'){
 					if($this->userdata['cpf'] == '30456130802' && $codeStatus == 0){
 						echo "<li role='presentation'><a class='link_payment_modal' data-toggle='modal' data-target='#payment-link' 
 						order_id='{$row['id']}' 
 						pedido_id='{$row['PedidoId']}'>Link de Pagamento</a> </li>";
 						
					}
					
					echo "<li role='presentation'><a {$contentDeclaration} >Declaração de conteúdo</a></li>";
					
					
					echo "<li role='presentation'><a class='edit_order_modal' data-toggle='modal' data-target='#order_edit'
						order_id='{$row['id']}'
						pedido_id='{$row['PedidoId']}'
						customer_id='{$row['customer_id']}'>Editar Comprador</a> </li>";

					echo "<li role='presentation'><a class='occurrence_modal' data-toggle='modal' data-target='#order_occurrence_modal'
						order_id='{$row['id']}'
						pedido_id='{$row['PedidoId']}'
						customer_id='{$row['customer_id']}'>Registrar Ocorrências</a> </li>";
					
					echo "<li role='presentation'><a class='returns_modal' data-toggle='modal' data-target='#order_returns_modal'
						order_id='{$row['id']}'
						pedido_id='{$row['PedidoId']}'
						id_nota_saida='{$row['id_nota_saida']}'
						shipping_id='{$row['shipping_id']}'
						fiscal_key='{$row['fiscal_key']}'
						customer_id='{$row['customer_id']}'>Criar Troca ou Devolução</a></li>";
					
                    echo "<li role='presentation'><a class='fiscal_key_modal' data-toggle='modal' data-target='#fiscal_key_order_modal'  
                        order_id='{$row['id']}' 
                        pedido_id='{$row['PedidoId']}'
                        id_nota_saida='{$row['id_nota_saida']}'
                        shipping_id='{$row['shipping_id']}'
                        shipping_cost='{$row['FreteCusto']}'
                        fiscal_key='{$row['fiscal_key']}'> Dados Fiscais e Rastreamento</a> </li>";
                    
                    
                    if(!empty($row['id_nota_saida']) && $sendFiscalKey === true ){
                        
                        //TODO: Substituir por informações do painel de configuração do sistema
                        switch($row['store_id']){
                            
                            case "4": $captureFrom = "Sysemp"; break;
                            case "5": $captureFrom = false; break;
                            case "6": $captureFrom = "Scaquete"; break;
                            default : $captureFrom = "Sysplace"; break;
                        }
                        if($captureFrom){
                            echo "<li role='presentation'><a class='send_fiscal_key' action='register_fiscal_key' marketplace='{$row['Marketplace']}'
                                order_id='{$row['id']}' pedido_id='{$row['PedidoId']}' captureFrom='{$captureFrom}'
                                id_nota_saida='{$row['id_nota_saida']}'shipping_id='{$row['shipping_id']}'
                                fiscal_key='{$row['fiscal_key']}'> Enviar Nota Fiscal</a> </li>";
                        }
                    }
             echo "<li role='presentation'>
                    <a class='order_action text-green'  marketplace='{$row['Marketplace']}' action='approved_order' 
                    order_id='{$row['id']}' pedido_id='{$row['PedidoId']}'>Aprovar pagamento</a>
                   </li>";
             
              if(empty($row['fiscal_key'])){   
            		echo "<li role='presentation'>
            			<a class='order_action text-red' action='cancel_order' order_id='{$row['id']}' pedido_id='{$row['PedidoId']}'>Cancelar Pedido</a>
            			<a class='order_action text-red' action='delete_order' order_id='{$row['id']}' pedido_id='{$row['PedidoId']}'>Excluír Pedido</a>
            		</li>";
              }
			echo "</ul>
        </div>

    </td>
	</tr>";
	if($ordersModel->ShowItems != 1){
// 	    if(isset($ordersModel->ShowItems)){
	    echo "<tr class='{$class} tr-order-{$row['id']}'><td {$style} colspan='8' >
		<table  class='table table-condensed  no-padding' style='margin-bottom: 5px;'>";
		foreach($row['items'] as $keyItem => $rowItem){
			
		    $colorVerifyAds =  '' ;
		    $itemPedidoId = $rowItem['PedidoItemId'];
			$verifyAds = true;
			switch($row['Marketplace']){
				case "Onbi": break;
				case "Tray": break;
				case "Mercadolivre":
				    if($storeId != 3){
				    $verifyAds = verifyAds($this->db, $storeId, $row['Marketplace'], str_replace('MLB', '', $rowItem['PedidoItemId']));
				    $itemPedidoId = "<a href='{$verifyAds['permalink']}' target='_blank' >{$rowItem['PedidoItemId']}</a>";
				    }
				    break;
				case "Lojas Americanas": break;
				case "Submarino": break;
				case "Amazon": break;
			
			}
			
			
			if($verifyAds == false){
			    $colorVerifyAds = '#ff0000';
			    $itemPedidoId = $rowItem['PedidoItemId'];
			}
			$thumbnail = getThumbnailSku($this->db, $storeId, $rowItem['SKU']);
			$imgProduct = isset($thumbnail['thumbnail']) && !empty($thumbnail['thumbnail']) ? $thumbnail['thumbnail'] :  $rowItem['UrlImagem'] ;
			echo "
				<tr class='tr-order-{$row['id']}' bgcolor='#fff'>
					<td width='50px' ><img src='{$imgProduct}' width='50' /></td>
					<td width='100px'><font color='{$colorVerifyAds}'>ID:{$itemPedidoId}</font><br>SKU: <a href='/Products/Product/{$rowItem['product_id']}' target='_blank' >{$rowItem['SKU']}</a></td>
					<td colspan='3'  width='750px'>{$rowItem['Nome']}<br>";
			            foreach($rowItem['item_attributes'] as $keyItemAttr =>$attr){
							echo "{$attr['Nome']}  - {$attr['Valor']} ";
						}
				
					echo "
					</td>
					<td width='30px' align='center' ><strong>{$rowItem['Quantidade']}</strong></td>
					<td width='80px'>".number_format($rowItem['PrecoVenda'], 2, ',', '.')."</td>
				</tr>";
					
					$formModalReturns .= '';

		}
		echo " 	</table>
			</td>
		</tr>
		<tr class='tr-order-{$row['id']}' ><td {$style} colspan='8' ></td></tr>";
	}
}
echo "</tbody></table>";

pagination($totalReg, $ordersModel->pagina_atual, HOME_URI."/Orders/Manage", array(
    "PedidoId" => $ordersModel->PedidoId,
    "Nome" => str_replace("%", "_x_", $ordersModel->Nome),
    "Status" => str_replace("%", "_x_", $ordersModel->Status),
    "ValorPedido" => str_replace("%", "_x_", $ordersModel->ValorPedido),
    "ValorPedidoAte" => str_replace("%", "_x_", $ordersModel->ValorPedidoAte),
    "Estado" => str_replace("%", "_x_", $ordersModel->Estado),
    "Email" => str_replace("%", "_x_", $ordersModel->Email),
	"CPFCNPJ" => str_replace("%", "_x_", $ordersModel->CPFCNPJ),
    "Cidade" => str_replace("%", "_x_", $ordersModel->Cidade),
    "FormaPagamento" => str_replace("%", "_x_", $ordersModel->FormaPagamento),
    "DataPedido" => str_replace("%", "_x_", str_replace('/', '-', $ordersModel->DataPedido)),
    "DataPedidoAte" => str_replace("%", "_x_", str_replace('/', '-', $ordersModel->DataPedidoAte)),
    "Marketplace" => str_replace("%", "_x_", $ordersModel->Marketplace),
	"printed" => $ordersModel->printed,
    "records" => $ordersModel->records
));
 
echo "
<div id='myModal'  style='width: 1024px; min-height:700px;height:auto;position: absolute;top:10px;left:420px; opacity: 1; overflow: visible;' class='modal hide fade' tabindex='-1' role='dialog'>
	<div class='modal-body' >
	<div class='iframeOrder' style='height:1024px !important;'></div>
		
	</div>
</div>";
?>
			</div>
			<div class="overlay" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div>
	</div>
</div>

<div class="modal fade" id='fiscal_key_order_modal' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Cadastrar dados fiscais</h4>
      </div>
      <div class="modal-body">
      <div id='message'></div>
        <div class='row'>
        	<input type="hidden" name="OrderId" id='OrderId' disabled class="form-control"  value="">
			<div class="col-md-4">
				<div class="form-group">
					<label>PedidoId:</label> 
					<input type="text" name="PedidoId" id='pedido_id' disabled class="form-control"  value="">
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>Rastreamento:</label> 
					<input type="text" name="shipping_id" id='shipping_id' class="form-control"  value="">
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label>FreteCusto:</label> 
					<input type="text" name="shipping_cost" id='shipping_cost' class="form-control"  value="">
				</div>
			</div>
			</div>
		<div class='row'>
			<div class="col-md-4">
				<div class="form-group">
					<label>Numero do Pedido:</label> 
					<input type="text" name="id_nota_saida" id="id_nota_saida"  class="form-control"  value="">
				</div>
			</div>
			<div class="col-md-8">
				<div class="form-group">
					<label>Chave da Nota Fiscal:</label> 
					<input type="text" name="fiscal_key" id="fiscal_key"  class="form-control"  value="">
				</div>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default  btn-sm pull-left" id='close-modal' data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" id='register_fiscal_data'>Registrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>


<div class="modal fade" id='order_edit' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Atualizar Informações do Pedido</h4>
      </div>
      <div class="modal-body">
	  	
	  
	  		<div class="nav-tabs-custom">
	  		
                <ul class="nav nav-tabs">
                  <li class="active"><a href="#address" data-toggle="tab" aria-expanded="true">Comprador</a></li>
                </ul>
                
                <div class="tab-content">
                
                	<div class="tab-pane active " id="address">
                	<div id='message_update_address_order'></div>
                	<div class="row">
							<input type="hidden" name="order_id" id='OrderId_address' class="form-control modal_address"  value="">
							<div class="col-sm-6">
    							<div class="form-group">
    								<label for="Nome">Nome:</label> 
    								<input type="text" name="Nome"  id='Nome_address' class="form-control input-sm  modal_address" value="">
    							</div>
    						</div>
    						<div class="col-sm-6">
    							<div class="form-group">
    								<label for="Email">Email:</label> 
    								<input type="text" name="Email"  id='Email_address' class="form-control input-sm modal_address" value="">
    							</div>
    						</div>
                    		<div class="col-sm-6">
    							<div class="form-group">
    								<label for="CPFCNPJ">CPF/CNPJ:</label>
    								<input type="text" name="CPFCNPJ"  id='CPFCNPJ_address' class="form-control input-sm modal_address" value="" >
    							</div>
    						</div>
							<div class="col-sm-6">
								<div class="form-group">
        							<label for="TipoPessoa">Tipo Pessoa:</label>
        							<select id="TipoPessoa_address" name="TipoPessoa" class="form-control input-sm modal_address">
            							<option value=''>Selecione</option>
            							<option value='1'>Física</option>
            							<option value='2'>Jurídica</option>
        							</select>
    							</div>
    						</div>
    						
					
    						<div class="col-sm-6">
    							<div class="form-group">
    								<label for="Telefone">Telefone:</label> 
    								<input type="text" name="Telefone"  id='Telefone_address' class="form-control input-sm modal_address" value="">
    							</div>
    						</div>
    						
    				
    						
    						<div class="col-sm-6">
    							<div class="form-group">
    								<label for="CEP">CEP:</label> 
    								<input type="text" name="Cep"  id='CEP_address' class="form-control input-sm" value="">
    							</div>
    						</div>
    						
    						<div class="col-sm-9">
    							<div class="form-group">
    								<label for="Endereco">Endereço:</label>
    								<input type="text" name="Endereco"  id='Endereco_address' class="form-control input-sm modal_address" value="">
    							</div>
    						</div>
    						<div class="col-sm-3">
    							<div class="form-group">
    								<label for="Numero">Numero:</label>
    								<input type="text" name="Numero"  id='Numero_address' class="form-control input-sm modal_address" value="">
    							</div>
    						</div>
    						<div class="col-sm-6">
    							<div class="form-group">
    								<label for="Bairro">Bairro:</label> 
    								<input type="text" name="Bairro"  id='Bairro_address' class="form-control input-sm modal_address" value="">
    							</div>
    						</div>
    						<div class="col-sm-6">
    							<div class="form-group">
    								<label for="Complemento">Complemento:</label> 
    								<input type="text" name="Complemento"  id='Complemento_address' class="form-control input-sm modal_address" value="">
    							</div>
    						</div>
    						
    						<div class="col-sm-9">
    							<div class="form-group">
    								<label for="Cidade">Cidade:</label> 
    								<input type="text" name="Cidade"  id='Cidade_address' class="form-control input-sm modal_address" value="">
    							</div>
    						</div>
					
    						<div class="col-sm-3">
    							<div class="form-group">
    								<label for="Estado">Estado:</label> 
    								<input type="text" name="Estado"  id='Estado_address' class="form-control input-sm modal_address" placeholder='UF' value="">
    							</div>
    						</div>
    					</div>
                	 
                		<div class="modal-footer">
					  		<button type="button" class="btn btn-default  btn-sm pull-left close-modal" id='close-modal' data-dismiss="modal">Close</button>
					    	<button type="button" class="btn btn-primary btn-sm" id='update_address_order'>Salvar</button>
				   		</div>
                	</div>
                </div>
            </div>
	  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>





<div class="modal fade" id='payment-link' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Link de Pagamento</h4>
      </div>
      <div class="modal-body">
      	<div class="col-sm-12">
    		<div class="form-group">
    		<label for="Nome">Link:</label> 
	    	<div class="input-group input-group-sm">
    			<input type="text" name="link"  id='link_payment' class="form-control" value="">
        	   	<span class="input-group-btn">
            		<button type="button" id='btnCopyToClipboard' class="btn btn-primary btn-flat"  ><i class='fa fa-copy'></i></button>
            	</span>
      		</div>
	  	</div>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default  btn-sm pull-left" id='close-modal' data-dismiss="modal">Close</button>
        <a href='' class="btn btn-warning btn-sm" id='send-payment' target='_blank'>Efetuar Pagamento</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>