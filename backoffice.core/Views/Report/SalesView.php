<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message"><?php if(!empty( $salesModel->form_msg)){ echo  $salesModel->form_msg;}?></div>
		
		<div class="box box-primary">
			<form  method="POST" action="" name="filter-order">
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar Pedidos</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Report/Sales/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body table-responsive">
					
					<div class="col-sm-2">
						<div class="form-group <?php echo $salesModel->field_error['DataPedido']; ?>">
							<label>Data Inicial:</label> 
							<div class="input-append date" id="dp3" data-date="" data-date-format="dd/mm/yyyy">
								<input type="text"  name="DataPedido" id="data-1" class="form-control date-mask"  placeholder='00/00/0000' value="<?php echo $salesModel->DataPedido;?>">
								<span class="add-on" ><i class="icon-th"></i></span>
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label>Data Final:</label>				
							<div class="input-append date" id="dp3" data-date="" data-date-format="dd/mm/yyyy">
								<input type="text"  name="DataPedidoAte" id="data-2" class="form-control date-mask"  placeholder='00/00/0000' value="<?php echo $salesModel->DataPedidoAte;?>">
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
    						        	$selected = $value['Marketplace'] == $salesModel->Marketplace ? "selected" : '' ;
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
<?php if(isset($list[0])){?>
<div class='row'>
<div class="col-md-12">
		<div class='box'>
			<div class='box-header'>
		       	<h3 class='box-title'>Relatório de vendas</h3>
			</div><!-- /.box-header -->
			<ul>
			</ul>
			<div class="box-body table-responsive">
				<table id="search-default" class="table table-condensed table-hover  no-padding" style="width: 100%;">
			        <thead>
				        <tr>
				         	<th>Pedidos</th>
				            <th title='Total Venda Produtos Sem Frete'>= Subtotal</th>
				            <th title='Frete Recebido'>+ Fretes</th>
				            <th title='Receita_Bruta = (Subtotal + Fretes_Recebidos) '>= R.Bruta</th>
				            <th title='Receita_Liquida = ( Receita_Bruta - Imposto ) '>= R.Liquida</th>
				            <th title='Taxa do Marketplace'>- TaxaVenda</th>
				            <th title='Custo de Frete do Marketplace'>- FreteCusto</th>
				            <th title='Recebimento = ( Receita_Bruta - ( TaxaVenda + FreteCusto ) )'>= Recebimento</th>
				            <th title='Custo das Mercadorias Vendidas'>- CMV</th>
				            <th title='Lucro_Bruto = ( Receita_Bruta - ( TaxaVenda + FreteCusto + CMV ) )' >= L.Liquido</th>
				            <th title='Margem_Bruta = ( Lucro_Bruto / Receita_Bruta )'>% M.Liquida</th>
				            <th title='Margem_Contribuicao = ( Lucro_Liquido / Lucro_Total )' style='text-align:center;'>% M.Contribuição</th>
				        </tr>
			        </thead>
		 		<tbody>
	             <?php
	             $lucroTotal = 0;
	             foreach ($list as $key => $fetch){
	                 $costOrder = $taxaVenda = 0;
	                 if(isset($fetch['items'])){
	                     foreach($fetch['items'] as $i => $item ){
	                         $taxaVenda += $item['TaxaVenda'] * $item['Quantidade'];
	                         $costOrder += $item['cost'] * $item['Quantidade'];
	                     }
	                 }
	                 $receitaBruta = $fetch['ValorPedido'];
	                 $receitaBruta = number_format($receitaBruta, 2, '.', '');
	                 
	                 $lucro = $receitaBruta - ($taxaVenda + $fetch['FreteCusto'] + $costOrder);
	                 $lucroTotal += number_format($lucro, 2, '.', '');
	             }
	             
	             $totalMargemContribuicao = $margemContribuicao = $sumImpostoReceita = $sumReceitaLiquida = $lucro = $margemBruta = $sumResult = $sumSubtotal = $sumFreteCusto = $costOrder =  $sumFrete = $sumCostTotal = $ordersCount = $sumDesc = $sumTax = $sumSale = $count = 0;
	             foreach ($list as $key => $fetch){
	             	$ordersCount++;
	                 $costOrder = $taxaVenda = 0;
	                 if(isset($fetch['items'])){
		                 foreach($fetch['items'] as $i => $item ){
		                 	$taxaVenda += $item['TaxaVenda'] * $item['Quantidade'];
		                    $costOrder += $item['cost'] * $item['Quantidade'];
		                 }
	                 }
	                 
	                 $dataPedido = dateBr($fetch['DataPedido'], '/');
	                 $salesModel->imposto = str_replace('%', '', $salesModel->imposto);
	                 
	                 $receitaBruta = $fetch['ValorPedido'];
	                 $receitaBruta = number_format($receitaBruta, 2, '.', '');
// 	                 pre($receitaBruta);
	                 $impostoReceita =  ($receitaBruta /100) * $salesModel->imposto;
	                 $impostoReceita = number_format($impostoReceita, 2, '.', '');
	                
	                 $receitaLiquida = $receitaBruta - $impostoReceita;
	                 $receitaLiquida = number_format($receitaLiquida, 2, '.', '');
	                 
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
	                 
	                 $margemContribuicao = ($lucro/$lucroTotal) *100;
	                 $margemContribuicao = number_format($margemContribuicao, 2, '.', '');
	                 if($receitaBruta < 1){
	                     $receitaBruta = 1 ;   
	                 }
	                 $margemBruta = ($lucro/$receitaBruta) *100;
	                 $margemBruta = number_format($margemBruta, 2, '.', '');
	                 
	                 $costOrder = $costOrder > 0 ? $costOrder : $sumSubtotal ;
	                 $margem= ($lucro/$costOrder) *100;
	                 $margem = number_format($margem, 2, '.', '');
	                 
	                 $sumResult += $result;
	                 $sumResult = number_format($sumResult, 2, '.', '');
	                 
	                 $sumReceitaLiquida += $receitaLiquida;
	                 $sumReceitaLiquida = number_format($sumReceitaLiquida, 2, '.', '');
	                 
	                 $sumImpostoReceita += $impostoReceita;
	                 $sumImpostoReceita = number_format($sumImpostoReceita, 2, '.', '');
	                 
	                 $sumLucroBruto += $lucro;
	                 $sumLucroBruto = number_format($sumLucroBruto, 2, '.', '');
	                 $totalMargemContribuicao += $margemContribuicao;
	                 
	             echo "
    	             <tr>
    	             	<td>{$dataPedido} <br>
                        <small>{$fetch['PedidoId']}</small>
    	             	</td>
                        <td class='align-middle' align='center' valign='center'>{$fetch['Subtotal']}</td>
                        <td class='align-middle' align='center' valign='center'>{$fetch['ValorFrete']}</td>
                        <td class='align-middle' align='center' valign='center'>{$fetch['ValorPedido']}</td>
                        <td class='align-middle' align='center' valign='center'>{$receitaLiquida}</td>
                        <td class='align-middle' align='center' valign='center'>{$taxaVenda}</td>
                        <td class='align-middle' align='center' valign='center'>{$fetch['FreteCusto']}</td>
                        <td class='align-middle' align='center' valign='center'>{$result}</td>
                        <td class='align-middle' align='center' valign='center'>{$costOrder}</td>
                        <td class='align-middle' align='center' valign='center'>{$lucro}</td>
                        <td class='align-middle' align='center' valign='center'>{$margemBruta} %</td>
                        <td class='align-middle' align='center' valign='center'>{$margemContribuicao} %</td>
    	             </tr>";
	             }
	             ?>
		 
		 		</tbody>
		 		<tfoot>
		 		<tr>
		         	<th>Pedidos</th>
		            <th title='Total Venda Produtos Sem Frete' style='text-align:center;'>= Subtotal</th>
		            <th title='Frete Recebido'  style='text-align:center;'>+ Fretes</th>
		            <th title='Receita_Bruta = (Subtotal + Fretes_Recebidos) ' style='text-align:center;'>= R.Bruta</th>
		            <th title='Receita_Liquida = ( Receita_Bruta - Imposto ) ' style='text-align:center;'>= R.Liquida</th>
		            <th title='Taxa do Marketplace' style='text-align:center;'>- TaxaVenda</th>
		            <th title='Custo de Frete do Marketplace' style='text-align:center;'>- FreteCusto</th>
		            <th title='Recebimento = ( Receita_Bruta - ( TaxaVenda + FreteCusto ) )' style='text-align:center;'>= Recebimento</th>
		            <th title='Custo das Mercadorias Vendidas' style='text-align:center;'>- CMV</th>
		            <th title='Lucro_Bruto = ( Receita_Bruta - ( TaxaVenda + FreteCusto + CMV ) )'  style='text-align:center;'>= L.Liquido</th>
		            <th title='Margem_Bruta = ( Lucro_Bruto / Receita_Bruta )' style='text-align:center;'>% M.Liquida</th>
		            <th title='Margem_Contribuicao = ( Lucro_Liquido / Lucro_Total )' style='text-align:center;'>% M.Contribuição</th>
		        </tr>
		 		<tr class='align-middle' align='center' valign='center'>
		           <td class='align-middle' align='left' valign='center'><u><?php echo $ordersCount; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumSubtotal; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumFrete; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumSale; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumReceitaLiquida; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumTax; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumFreteCusto; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumResult; ?></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumCostTotal; ?></u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumLucroBruto; ?></u></td>
		           <?php 
		           $sumMargemBruta = ($sumLucroBruto / $sumSale) * 100;
		           $sumMargemBruta = number_format($sumMargemBruta, 2, '.', '');
		           ?>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $sumMargemBruta; ?> %</u></td>
		           <td class='align-middle' align='center' valign='center'><u><?php echo $totalMargemContribuicao; ?> %</u></td>
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
<?php }?>
