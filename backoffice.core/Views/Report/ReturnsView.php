<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message"><?php if(!empty( $returnsModel->form_msg)){ echo  $returnsModel->form_msg;}?></div>
		
		<div class="box box-primary">
			<form  method="POST" action="" name="filter-order">
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar Pedidos</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Report/Returns/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body table-responsive">
					<div class="col-sm-2">
						<div class="form-group <?php echo $returnsModel->field_error['created']; ?>">
							<label>Data Inicial:</label> 
							<div class="input-append date" id="dp3" data-date="" data-date-format="dd/mm/yyyy">
								<input type="text"  name="created" id="data-1" class="form-control date-mask"  placeholder='00/00/0000' value="<?php echo $returnsModel->created;?>">
								<span class="add-on" ><i class="icon-th"></i></span>
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label>Data Final:</label>				
							<div class="input-append date" id="dp3" data-date="" data-date-format="dd/mm/yyyy">
								<input type="text"  name="createdAte" id="data-2" class="form-control date-mask"  placeholder='00/00/0000' value="<?php echo $returnsModel->createdAte;?>">
								<span class="add-on"><i class="icon-th"></i></span>
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label>Marketplace:</label>
							<select  name="Marketplace" class="form-control">
							<option value=''>Selecione</option>
							<?php 
							   if(isset($marketplaceOrder)){
    						 	  	foreach($marketplaceOrder as $k => $value){
    						        	$selected = $value['Marketplace'] == $returnsModel->Marketplace ? "selected" : '' ;
							       	 	echo "<option value='{$value['Marketplace']}' {$selected}>{$value['Marketplace']}</option>";
							   		}
										
								}
									
									?>
							</select>
						</div>
					</div>

    			</div>
    			<div class="overlay" style='display:none;'>
            		<i class="fa fa-refresh fa-spin"></i>
        		</div>
    			<div class="box-footer">
    				<button type='submit' id='report-product-sales' name='btn-filter-order' class='btn btn-primary btn-sm pull-right submit-load' value='btn-filter-order'>Gerar</button>
    			</div>
			</form>
		</div>
	</div>
</div>
<?php if(isset($list[0])){ ?>
<div class='row'>
	<div class="col-md-12">
		<div class='box'>
			<div class='box-header'>
		       	<h3 class='box-title'>Pedidos Devolvidos</h3>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<table id="search-default" class="table table-condensed table-hover  no-padding" style="width: 100%;">
			        <thead>
				        <tr>
				         	<th>Data</th>
				         	<th>Pedidos</th>
				         	<th>Markeplace</th>
				            <th title='Total Venda Produtos Sem Frete'>= Subtotal</th>
				            <th title='Frete Recebido'>+ Fretes</th>
				            <th title='Receita_Bruta = (Subtotal + Fretes_Recebidos) '>= R.Bruta</th>
				            <th title='Taxa do Marketplace'>- TaxaVenda</th>
				            <th title='Custo de Frete do Marketplace'>- FreteCusto</th>
				            <th title='Recebimento = ( Receita_Bruta - ( TaxaVenda + FreteCusto ) )'>= Recebimento</th>
				            <th>Tipo</th>
				            <th>Motivo</th>
				        </tr>
			        </thead>
		 		<tbody>
	             <?php
	             $sumImpostoReceita = $sumReceitaLiquida = $lucro = $margemBruta = $sumResult = $sumSubtotal = $sumFreteCusto = $costOrder =  $sumFrete = $sumCostTotal = $ordersCount = $sumDesc = $sumTax = $sumSale = $count = 0;
	             foreach ($list as $key => $fetch){
	                 $popup = "onclick=\"javascript:popup('".HOME_URI."/Orders/OrderDetail/id/{$fetch['id']}','1100','700');\"";
	             	$ordersCount++;
	                 $costOrder = $taxaVenda = 0;
	                 if(isset($fetch['items'])){
		                 foreach($fetch['items'] as $i => $item ){
		                 	$taxaVenda += $item['TaxaVenda'] * $item['Quantidade'];
		                    $costOrder += $item['cost'] * $item['Quantidade'];
		                 }
	                 }
	                 $taxaVenda = number_format($taxaVenda, 2, '.', '');
	                 $dataPedido = dateBr($fetch['DataPedido'], '/');
// 	                 $partsData = explode(' ', $fetch['DataPedido']);
// 	                 $dataPedido = $partsData[0];
	                 $receitaBruta = $fetch['ValorPedido'];
	                 $receitaBruta = number_format($receitaBruta, 2, '.', '');
	                 
	                 $sumSubtotal += $fetch['Subtotal'];
	                 $sumSubtotal = number_format($sumSubtotal, 2, '.', '');
	                 
	                 $sumSale += $fetch['ValorPedido'];
	                 $sumSale = number_format($sumSale, 2, '.', '');
	                 
	                 $sumFrete += $fetch['ValorFrete'];
	                 $sumFrete = number_format($sumFrete, 2, '.', '');
	                 
	                 $sumFreteCusto += $fetch['FreteCusto'];
	                 $sumFreteCusto = number_format($sumFreteCusto, 2, '.', '');
	                 
	                 $sumTax += $taxaVenda;
	                 $sumTax = number_format($sumTax, 2, '.', '');
	                 
	                 
	                 $sumCostTotal += $costOrder;
	                 $sumCostTotal = number_format($sumCostTotal, 2, '.', '');
	                 
	                 $sumDesc += $fetch['ValorCupomDesconto'];
	                 $sumDesc = number_format($sumDesc, 2, '.', '');
	                 
	                 $result = $receitaBruta - ($taxaVenda + $fetch['FreteCusto'] );
	                 $result = number_format($result, 2, '.', '');
	                 
	                 $lucro = $receitaBruta - ($taxaVenda + $fetch['FreteCusto'] + $costOrder);
	                 $lucro = number_format($lucro, 2, '.', '');
	                 
	                 $costOrder = $costOrder > 0 ? $costOrder : $sumSubtotal ;
	                 
	                 $sumResult += $result;
	                 $sumResult = number_format($sumResult, 2, '.', '');
	                 
	                 $freteCusto = $fetch['FreteCusto'] > 0 ? $fetch['FreteCusto'] : $fetch['ValorFrete'] ;
	                 $freteCusto = $freteCusto > 0 ? $freteCusto : 0 ;
	                 $freteCusto = number_format($freteCusto, 2, '.', '');
	                 
	                 $valorFrete = $fetch['ValorFrete'] > 0 ? $fetch['ValorFrete'] : 0 ;
	                 $valorFrete = number_format($valorFrete, 2, '.', '');
	                 switch($fetch['type_return']){
	                     case "return"; $typeReturn = 'Devolução'; break;
	                     case "exchange"; $typeReturn = 'Troca'; break;
	                     case "returning"; $typeReturn = 'Retornando'; break;
	                     default : $typeReturn = $fetch['type_return']; break;
	                 }
	                 
	             echo "
    	             <tr>
    	             	<td>{$dataPedido}</td>
                        <td {$popup}><span class='btn-link'>{$fetch['pedido_id']}</span></td>
                        <td>{$fetch['Marketplace']}</td>
                        <td class='align-middle' align='center' valign='center'>{$fetch['Subtotal']}</td>
                        <td class='align-middle' align='center' valign='center'>{$valorFrete}</td>
                        <td class='align-middle' align='center' valign='center'>{$fetch['ValorPedido']}</td>
                        <td class='align-middle' align='center' valign='center'>{$taxaVenda}</td>
                        <td class='align-middle' align='center' valign='center'>{$freteCusto}</td>
                        <td class='align-middle' align='center' valign='center'>{$result}</td>
                        <td class='align-middle' align='left' valign='left'>{$typeReturn}</td>
                        <td class='align-middle' align='left' valign='left'>{$fetch['reasons']}</td>
                        
    	             </tr>";
	             }
	             ?>
		 		</tbody>
		 		<tfoot>
		 		<tr class='align-middle' align='center' valign='center'>
		           <td class='align-middle' align='left' valign='center'><u><?php echo $ordersCount; ?></u></td>
		           <td class='align-middle' align='center' valign='center'></td>
		           <td class='align-middle' align='center' valign='center'></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumSubtotal; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumFrete; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumSale; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumTax; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumFreteCusto; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumResult; ?></u></td>
		           <td class='align-middle' align='center' valign='center'></td>
		           <td class='align-middle' align='center' valign='center'></td>
		        </tr>
		        
		 		</tfoot>
				</table>
			</div>
			<div class="overlay" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div>
	</div>
</div>

<?php } ?>
