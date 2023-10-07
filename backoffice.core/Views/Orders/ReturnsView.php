<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>

	
	
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Solicitações de Trocas e Devoluções Registradas</h3>
		       	<div class='box-tools pull-right'>
    				<div class="form-group">
        	       		<a href='/Orders/Returns/' class='btn btn-block btn-default btn-xs'><i class='fa fa-refresh'></i></a>
        	       	</div>
    			</div>
			</div><!-- /.box-header -->
			
			<div class="box-body">
			<div class='message' id='message-returns'></div>
			<table class='table table-condensed table-hover display' id="search-advanced"  style="width:100%">
				<thead>
			        <tr>
			        	<th></th>
			        	<th>Id</th>
			            <th>Pedido</th>
			            <th>Tipo</th>
			            <th>Status</th>
			            <th>ShippingId</th>
			            <th>Criado</th>
			            <th>Atualizado</th>
			            <th>Por</th>
			            <th></th>
			        </tr>
		        </thead>
			 		<tbody>
			 
		             <?php 
		             foreach ($list as $fetch){
		             	
		             	switch($fetch['status']){
		             		case 'new': $styleStatus = "primary"; break;
		             		case 'received': $styleStatus = "default"; break;
		             		case 'returning': $styleStatus = "danger"; break;
		             		case 'waiting': $styleStatus = "warning"; break;
		             		default: $styleStatus = 'default'; break;
		             	}
		             	$typeReturn = '';
		             	switch($fetch['type_return']){
		             		case 'exchange': $typeReturn = "Troca"; break;
		             		case 'return': $typeReturn = "Devolução"; break;
		             		case 'refused': $typeReturn = "Recusado"; break;
		             		default: $typeReturn = ''; break;
		             	}
		             	$openModal = "onclick=\"javascript:editReturnModal({$fetch['order_id']});\"";
		            	echo "<tr id='{$rowItem['order_id']}'>
		              	 <td class='details-control'></td>
		                 <td >{$fetch['order_id']}</td>
		                 <td {$openModal}>{$fetch['pedido_id']}</td>
		                 <td {$openModal}>{$typeReturn}</td>
		                 <td {$openModal}><small class='label label-{$styleStatus}'>{$fetch['status']}</small></td>
		                 <td {$openModal}>{$fetch['shipping_id']}</td>
		                 <td {$openModal}>{$fetch['created']}</td>
		                 <td {$openModal}>{$fetch['updated']}</td>
		                 <td {$openModal}>{$fetch['user']}</td>
		                 <td>
		                	<div class='dropdown pull-right'>
				            	<a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
					            <ul class='dropdown-menu '>
	                     			<li role='presentation'><a class='returns_modal {$fetch['order_id']}' data-toggle='modal' data-target='#order_returns_modal'
		                     			order_id='{$fetch['order_id']}' pedido_id='{$fetch['pedido_id']}'
		                     			id_nota_saida='{$fetch['id_nota_saida']}' shipping_id='{$fetch['shipping_id']}' fiscal_key='{$fetch['fiscal_key']}'
		                     			customer_id='{$row['customer_id']}'><i class='fa fa-pencil-square-o'></i> Editar</a>
	                     			</li>
	                     		</ul>
	                     	</div>
		                 </td>
		             </tr>";
		             
					}
					?>
			 
			 		</tbody>
				</table>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
            </div>
		</div>
	</div>
</div>
