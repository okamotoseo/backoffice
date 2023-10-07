<?php if ( ! defined('ABSPATH')) exit;?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message"><?php if(!empty( $productSalesModel->form_msg)){ echo  $productSalesModel->form_msg;}?></div>
		
		<div class="box box-primary">
			<form  method="POST" action="/Report/ProductSales/" name="filter-report-order-sale">
				<div class="box-header with-border">
					<h3 class="box-title">Produtos vendidos</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Report/ProductSales/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body table-responsive">

					<div class="col-sm-2">
                    	<div class="form-group">
                    		<label>SKU:</label>
                    		<textarea  name='sku' class='form-control'  /><?php echo $productSalesModel->sku;?></textarea>
                    	</div>
                    </div>
                    <div class="col-sm-2">
                    	<div class="form-group">
                    		<label>Titulo:</label>
                    		<input type='nome' name='nome' class='form-control'  value='<?php echo $productSalesModel->nome;?>' />
                    	</div>
                    </div>
					<div class="col-sm-2">
						<div class="form-group <?php echo $productSalesModel->field_error['DataPedido']; ?>">
							<label>Data Inicial:</label> 
							<div class="input-append" id="dp3" data-date="" data-date-format="dd/mm/aaaa">
								<input type="text"  name="DataPedido" id="data-1" class="form-control date date-mask"  placeholder='00/00/0000' value="<?php echo $productSalesModel->DataPedido;?>">
								<span class="add-on" ><i class="icon-th"></i></span>
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label>Data Final:</label>				
							<div class="input-append" id="dp3" data-date="" data-date-format="dd/mm/aaaa">
								<input type="text"  name="DataPedidoAte" id="data-2" class="form-control date date-mask"  placeholder='00/00/0000' value="<?php echo $productSalesModel->DataPedidoAte;?>">
								<span class="add-on"><i class="icon-th"></i></span>
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label>Marcas:</label>
							<select  name="brand" class="form-control">
							<option value=''>Todas</option>
							<?php 
							if(isset($brands)){
							    foreach($brands as $k => $value){
					 			        $selected = $value['brand'] == $productSalesModel->brand ? "selected" : '' ;
								        echo "<option value='{$value['brand']}' {$selected}>{$value['brand']}</option>";
								   }
								
							}
							?>
							</select>
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
								        $selected = $value['Marketplace'] == $productSalesModel->Marketplace ? "selected" : '' ;
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
					<button type='submit' id='report-sales' name='generate-sales' class='btn btn-primary btn-sm pull-right submit-load'>Gerar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class='row'>
<div class="col-md-12">
		<div class='box'>
			<div class='box-header'>
		       	<h3 class='box-title'>Relatório de produtos vendidos</h3>
			</div><!-- /.box-header -->
			
			<div class="box-body ">
				<table id="search-default" class="table table-hover table-condensed">
			        <thead>
				        <tr>
				        	<th>PedidoId</th>
				            <th>SKU</th>
				            <th>Produto</th>
				            <th style='text-align:center;'>Qtd</th>
				            <th>CMV.Unit</th>
				            <th>CMV</th>
				            <th>PrecoVendaUnit.</th>
				            <th>PrecoVenda</th>
				            <th>TaxaVenda</th>
				        </tr>
			        </thead>
		 		<tbody>
		 
	             <?php
	             if(isset($list)){
	             	$sumTaxTotal = $sumOrders = $sumCost = $sumCostTotal = $ordersCount = $sumDesc = $sumTax = $sumQtd = $sumSaleUnit = $sumSale = $count = 0;
		             foreach ($list as $key => $fetch){
// 		             	pre($fetch['order_id']);
// 		             	pre($fetch['PedidoItemId']);
		                 $sumQtd += $fetch['Quantidade'];
		                 
		                 $sumTax += $fetch['TaxaVenda'];
	
		                 $sumCost += $fetch['cost'];
		                 $sumCostTotal += $fetch['cost_total'];
		                 $sumDesc += $fetch['Desconto'];
		                 $ordersCount++;
		                 $attrVal = '';
		                 if(isset($fetch['attributes'])){
			                 foreach ($fetch['attributes'] as $i => $attr){
			                 	$attrVal .= '<b>'.$attr['value']."</b> ";
			                 }
		                 }
		                 $sumSaleUnit += $fetch['PrecoVenda'];
		                 $costTotal = $fetch['cost'] * $fetch['Quantidade'];
		                 $totalVenda = $fetch['PrecoVenda'] * $fetch['Quantidade'];
		                 $totalTax = number_format($fetch['TaxaVenda'], 2, '.', '') * $fetch['Quantidade'];
// 		                 $totalTax = $fetch['TaxaVenda'];
		                 $sumCost += $costTotal;
		                 $sumSale += $totalVenda;
		                 $sumTaxTotal+= $totalTax;
		             echo "
	    	             <tr>
	    	             	 <td>{$fetch['PedidoId']}</td>
	    	                 <td style='width:40px !important'>{$fetch['SKU']}</td>
	    	                 <td> {$fetch['Nome']} <br> ".trim($attrVal)."</td>
	    	                 <td align='center'> {$fetch['Quantidade']} </td>
	    	                 
	    	                 <td align='center'> {$fetch['cost']} </td>
	    	                 <td align='center'> {$costTotal} </td>
	    	                 <td align='center'> {$fetch['PrecoVenda']} </td>
	                         <td align='center'> {$totalVenda} </td>
	    	                 <td align='center'> {$totalTax} </td>
	    	             </tr>";
		             	$sumOrders++;
		             }
	             
	             }
	             ?>
		 
		 		</tbody>
		 		<tfoot>
		 			<tr>
			            <th style='text-align:center;'><?php echo $sumOrders; ?></th>
			            <th></th>
			            <th></th>
			            <th style='text-align:center;'><?php echo $sumQtd; ?></th>
			            <th></th>
			            <th style='text-align:center;'><?php echo $sumCost; ?></th>
			            <th style='text-align:center;'><?php echo $sumSaleUnit; ?></th>
			            <th style='text-align:center;'><?php echo $sumSale; ?></th>
			            <th style='text-align:center;'><?php echo $sumTaxTotal; ?></th>
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
