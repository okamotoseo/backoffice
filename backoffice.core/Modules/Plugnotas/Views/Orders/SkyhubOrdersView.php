<?php if ( ! defined('ABSPATH')) exit;?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty($sucess['message'])){ echo "<div class='callout callout-success'><h4>Tip!</h4><p>".$sucess['message']."</></div>";}?>
		</div>
		
		<div class="box box-primary">
			<form  method="POST" action="" name="filter-print-order">
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar pedidos</h3>
					<div class='box-tools pull-right'>
    					<div class="form-group">
        	        		<a href='<?php echo HOME_URI ?>/Modules/Skyhub/Orders/SkyhubOrders' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    				</div>
    			</div>
				<div class="box-body table-responsive">
						<div class="col-xs-2">
							<div class="form-group">
								<label>Nome:</label> 
								<input type="text" name="Nome" id="Nome" placeholder='Nome' class="form-control" value="<?php echo $ordersModel->Nome; ?>">
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Pedido Id:</label> 
								<input type="text" name="PedidoId" id="PedidoId" placeholder='PedidoId' class="form-control"  value="<?php echo $ordersModel->PedidoId; ?>">
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Status:</label>
								<select  id="select_status" name="status" class="form-control">
									<option value=''>Status</option>
								</select>
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Valor de:</label> 
								<input type="text" name="ValorPedido" id="ValorPedido" placeholder='R$' class="form-control"  value="<?php echo $ordersModel->ValorPedido; ?>">
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Valor até:</label> 
								<input type="text" name="ValorPedidoAte" id="ValorPedidoAte" placeholder='R$' class="form-control"  value="<?php echo $ordersModel->ValorPedidoAte; ?>">
							</div>
						</div>
						<div class="col-xs-1">
							<div class="form-group">
								<label>UF:</label> 
								<select  name="state" class="form-control">
									<option value=''>UF</option>
								</select>
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Email:</label> 
								<input type="text" name="Email" id="Email" placeholder='Email' class="form-control" value="<?php echo $ordersModel->Email; ?>">
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Cidade:</label> 
								<input type="text" name="Cidade" id="Cidade" placeholder='Cidade' class="form-control"  value="<?php echo $ordersModel->Cidade; ?>">
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Pagamento:</label>
								<select  id="payment" name="payment" class="form-control">
								<option value=''>Pagamento</option>
								</select>
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Data Inicial:</label> 
								<div class="input-append date" id="dp3" data-date="" data-date-format="dd/mm/yyyy">
									<input type="text"  name="DataPedido" id="data-1" class="form-control"  placeholder='00/00/0000' value="<?php echo $ordersModel->DataPedido;?>">
									<span class="add-on" ><i class="icon-th"></i></span>
								</div>
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label>Data Final:</label>				
								<div class="input-append date" id="dp3" data-date="" data-date-format="dd/mm/yyyy">
									<input type="text"  name="DataPedidoAte" id="data-2" class="form-control"  placeholder='00/00/0000' value="<?php echo $ordersModel->DataPedidoAte;?>">
									<span class="add-on"><i class="icon-th"></i></span>
								</div>
							</div>
						</div>

				</div>
				<div class="box-footer">
					<button type='submit' id='filter-order' name='filter-order' class='btn btn-primary btn-sm pull-right'><i class='fa fa-search'></i> Filtrar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
	        	<h3 class="box-title">Listagem de pedidos</h3>
	        	<div class="box-tools pull-right">
					<button class="btn btn-warning btn-xs" id='update_order_queue' >Atualizar fila de pedidos</button>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
<?php
echo "<table class='table table-condensed  no-padding'>
	<thead>
		<tr>
			<th>PedidoId</th>
			<th>Nome / Res</th>
			<th>Email / Tel</th>
			<th>Frete</th>
			<th>Total</th>
			<th>Pagamento</th>
			<th>Status</th>
            <th>Canal</th>
			<th>Marketplace</th>
			<th>Data</th>
            <th>Ações</th>
		</tr>
	</thead>
	<tbody>";

foreach($list as  $key => $row){
    $orderCode = $row['Marketplace']."-".$row['PedidoId'];
//     $orderCode = $row['PedidoId'];
    $style = $ordersModel->ShowItems == 1 ? "style='border-top:none !important'" : "";
    $class = $ordersModel->ShowItems == 1 ? "class='warning'" : "";
    $frete = $row['ValorFrete'] == 0 ? "Frete Grátis" : number_format($row['ValorFrete'], 2, ',', '.');
	$popup = "onclick=\"javascript:popup('".HOME_URI."/Orders/OrderDetail/id/{$row['id']}','1100','700');\"";
	
// 	$color = !isset($userPrint['user_print']) ? '#ff0000' : '' ;
	$color = $row['sent'] == 'T' ?  '#009900' : '#ff0000' ;
// 	$popup ="class='openOrder' id='{$row['PedidoId']}'";
	switch($row['Marketplace']){
	    case "Onbi": $marketplace = "<small class='label label-success' title='Ecommerce'>Onbi</small>";break;
	    case "Mercadolivre": $marketplace = "<small class='label label-warning' title='Mercadolivre'>Meli</small>";break;
	    default : $marketplace = $row['Marketplace']; break;
    
    }
	echo "
	<tr {$class}>
	<td><font color='{$color}' title=''>{$row['PedidoId']}</font></td>
	<td {$popup}>{$row['Nome']}<br>{$row['Cidade']} -{$row['Estado']}</td>	
	<td {$popup}>{$row['Email']}<br>{$row['Telefone']}</td>
	<td {$popup}>{$frete}<br></td>
	<td {$popup}>".number_format($row['ValorPedido'], 2, ',', '.')."</td>
 	<td {$popup}>{$row['FormaPagamento']}</td>
	<td {$popup}>{$row['Status']}</td>
    <td {$popup}>{$row['Canal']}</td>
    <td {$popup}>{$marketplace}</td>
    <td {$popup}>".dateBr($row['DataPedido'], "/")."</td>
    <td> 
        <div class='dropdown'>
            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
            <ul class='dropdown-menu pull-right' style='min-width:100px'>";
            	if($row['Status'] == 'invoiced'){
            	    echo "<li role='presentation'><a class='shipping_modal' data-toggle='modal' data-target='#skyhub_order_modal'  order_id='{$row['id']}' pedido_id='{$orderCode}'> Cadastrar rastreamento</a> </li>";
            	}
            	
	            if($row['Status'] == 'shipped' ){ 
                    echo "<li role='presentation'><a class='action_skyhub_oders' action='delivered_skyhub_order' order_id='{$row['id']}' pedido_id='{$orderCode}'> Registrar entrega</a> </li>";
                }
                if($row['Status'] == 'paid' || $row['Status'] == 'approved'){
                    echo "<li role='presentation'><a class='action_skyhub_oders' action='invoice_skyhub_order' order_id='{$row['id']}' pedido_id='{$row['PedidoId']}'>Faturar pedido</a> </li>";
                }
                if($row['Status'] == 'paid' || $row['Status'] == 'new'){
                    echo "<li role='presentation'><a class='action_skyhub_oders' action='cancel_skyhub_order' order_id='{$row['id']}' pedido_id='{$orderCode}'> <font color='red'>Cancelar pedido</font></a> </li>";
                }
                if($row['Status'] == 'shipped'){
                    echo "<li role='presentation'><a class='shipping_exception_modal' data-toggle='modal' data-target='#skyhub_order_shipped_exception_modal'  order_id='{$row['id']}' pedido_id='{$orderCode}'> Cadastrar exceção na entrega</a> </li>";
                }
            echo "</ul>
        </div>
        <img class='ajaxload-{$row['id']}' src='".HOME_URI."/Views/_uploads/images/facebook-ajax-loader.gif' style='display:none;'>                               
	</td>
</tr>";
	if($ordersModel->ShowItems != 1){
// 	    if(isset($ordersModel->ShowItems)){

	    echo "<tr {$class}><td {$style} colspan='10' >
		<table  class='table table-condensed  no-padding'>";
		foreach($row['items'] as $keyItem => $rowItem){
// 		    pre($rowItem);die;
			echo "<tr bgcolor='#fff'>
					<td width='50px' ><img src='{$rowItem['UrlImagem']}' width='50'/></td>
					<td width='100px' >ID:{$rowItem['PedidoItemId']}<br>SKU:{$rowItem['SKU']}</td>
					<td colspan='7'  width='750px'>{$rowItem['Nome']}<br>";
			            foreach($rowItem['item_attributes'] as $keyItemAttr =>$attr){
							echo "{$attr['Nome']} - {$attr['Valor']} ";
						}
					echo "
					</td>
					<td width='30px' align='center' ><strong>{$rowItem['Quantidade']}</strong></td>
					<td width='80px'>".number_format($rowItem['PrecoUnitario'], 2, ',', '.')."</td>
				</tr>";

		}
		echo " 	</table>
			</td>
		</tr>
		<tr><td {$style} colspan='10' ></td></tr>";
	}
}
echo "</tbody></table>";
// $teste = array(
//     "PedidoId" => $ordersModel->id,
//     "Nome" => str_replace("%", "_x_", $ordersModel->sku),
//     "status" => str_replace("%", "_x_", $ordersModel->title),
//     "ValorPedido" => str_replace("%", "_x_", $ordersModel->parent_id),
//     "ValorPedidoAte" => str_replace("%", "_x_", $ordersModel->reference),
//     "state" => str_replace("%", "_x_", $ordersModel->category),
//     "Email" => str_replace("%", "_x_", $ordersModel->brand),
//     "Cidade" => str_replace("%", "_x_", $ordersModel->brand),
//     "payment" => str_replace("%", "_x_", $ordersModel->brand),
//     "DataPedido" => str_replace("%", "_x_", $ordersModel->brand),
//     "DataPedidoAte" => str_replace("%", "_x_", $ordersModel->brand),
//     "records" => $ordersModel->records
// );
pagination($totalReg, $ordersModel->pagina_atual, HOME_URI."/Orders/Manage");
 
// echo "
// <div id='myModal'  style='width: 1024px; min-height:700px;height:auto;position: absolute;top:10px;left:420px; opacity: 1; overflow: visible;' class='modal hide fade' tabindex='-1' role='dialog'>
// 	<div class='modal-body' >
// 	<div class='iframeOrder' style='height:1024px !important;'></div>
		
// 	</div>
// </div>";
?>

			</div>
			<div class="overlay skyhub-orders" style='display:none;'>
    		<i class="fa fa-refresh fa-spin"></i>
		</div>
		</div>
	</div>
</div>
<div class="modal fade" id='skyhub_order_modal' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Registrar entrega</h4>
        
      </div>
      <div class="modal-body">
      <div id='message'></div>
        <div class='row'>
        <input type="hidden" name="OrderId" id='OrderId' disabled class="form-control"  value="">
			<div class="col-xs-6">
				<div class="form-group">
					<label>PedidoId:</label> 
					<input type="text" name="ShippingPedidoId" id='ShippingPedidoId' disabled class="form-control"  value="">
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label>Transporte:</label>
					<select  id="shipping_type" name="transport" class="form-control">
						<option value='Correios'>Correios</option>
<!-- 						<option value='transportadora'>Transportadora</option> -->
					</select>
				</div>
			</div>
			</div>
		<div class='row'>
			<div class="col-xs-6">
				<div class="form-group">
					<label>Metodo:</label> 
					<input type="text" name="shipping_method" id="shipping_method" placeholder='PAC' class="form-control"  value="PAC">
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label>Codigo:</label> 
					<input type="text" name="shipping_code" id="shipping_code" placeholder='BR1321830198302DR' class="form-control"  value="BR1321830198302DR">
				</div>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default  btn-sm pull-left" id='close-modal' data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" id='add_shipping_code'>Informar Código de Rastreamento</button>
        <button type="button" class="btn btn-primary btn-sm" id='add_shipping_code_delivered'>Registrar e Entregar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>




<div class="modal fade" id='skyhub_order_shipped_exception_modal' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Registrar exceção na entrega</h4>
        
      </div>
      <div class="modal-body">
      <div id='message'></div>

		<div class='row'>
		<input type="hidden" name="OrderIdShippingException" id='OrderIdShippingException' disabled class="form-control"  value="">
			<div class="col-xs-6">
				<div class="form-group">
					<label>PedidoId:</label> 
					<input type="text" name="ShippingExceptionPedidoId" id='ShippingExceptionPedidoId' disabled class="form-control"  value="">
				</div>
			</div>

			<div class="col-xs-12">
				<div class="form-group">
					<label>Descrição da exceção:</label> 
					<input type="text" name="shipping_exception" id="shipping_exception" placeholder='Problemas na entrega' class="form-control"  value="Problemas na entrega">
				</div>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default  btn-sm pull-left" id='close-modal' data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" id='add_shipping_exception'>Registrar exceção na entrega</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>
