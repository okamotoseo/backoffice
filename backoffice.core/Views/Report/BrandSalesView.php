<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message"><?php if(!empty( $brandSalesModel->form_msg)){ echo  $brandSalesModel->form_msg;}?></div>
		
		<div class="box box-primary">
			<form  method="POST" action="/Report/BrandSales/" name="report-sale-brand">
				<div class="box-header with-border">
					<h3 class="box-title">Produtos vendidos por marca</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Report/BrandSales/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body table-responsive">
						<div class="col-sm-2">
							<div class="form-group <?php echo $brandSalesModel->field_error['DataPedido']; ?>">
								<label>Data Inicial:</label> 
								<div class="input-append" id="dp3" data-date="" data-date-format="dd/mm/aaaa">
									<input type="text"  name="DataPedido" id="data-1" class="form-control date date-mask"  placeholder='00/00/0000' value="<?php echo $brandSalesModel->DataPedido;?>">
									<span class="add-on" ><i class="icon-th"></i></span>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Data Final:</label>				
								<div class="input-append" id="dp3" data-date="" data-date-format="dd/mm/aaaa">
									<input type="text"  name="DataPedidoAte" id="data-2" class="form-control date date-mask"  placeholder='00/00/0000' value="<?php echo $brandSalesModel->DataPedidoAte;?>">
									<span class="add-on"><i class="icon-th"></i></span>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label>Marcas:</label>
								<select  name="brand" class="form-control select2">
								<option value=''>Todas</option>
								<?php 
								if(isset($brands)){
								    foreach($brands as $k => $value){
						 			        $selected = $value['brand'] == $brandSalesModel->brand ? "selected" : '' ;
									        echo "<option value='{$value['brand']}' {$selected}>{$value['brand']}</option>";
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
					<button type='submit' id='report-brand-sales' name='filter-report-brand-sales' class='btn btn-primary btn-sm pull-right submit-load'>Gerar</button>
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
				<table class='table table-condensed display'  style="width:100%">
		        <thead>
			        <tr>
			            <th>Marca</th>
			            <th style='text-align:center' >Qtd</th>
			            <th>SKU - Produto</th>
			            <th></th>
			            <th style='text-align:center' >Variação</th>
			            <th style='text-align:center' >Cor</th>
			            <th style='text-align:center' >CMV</th>
			            <th style='text-align:center' >CMVTotal</th>
			            <th style='text-align:center' >Faturado</th>
			            <th style='text-align:center' >CurvaABC</th>
			            <th style='text-align:center' >Disponível</th>
			            <th style='text-align:center' >ERP</th>
			        </tr>
		        </thead>
		 		<tbody>
		 
	             <?php
	             
	             
				if(isset($list)){
				    $grandTotal = $sumQtdTotal = $sumTotal =  $count = 0;
		              
		             foreach ($list as $key => $brand){
		                 $grandTotal += $brand['total'];
		             }
// 		             pre($grandTotal);
		             $sumBrandAbcPercent = 0;
		             $brandAbcPercents = array();
		             foreach ($list as $key => $brand){
// 		                 pre($brand); 
// 		                 $brand = array_orderby($brand, 'total', SORT_DESC);
// 		                 pre($brand); 
                       
                        $brandAbcPercent = $brand['total'] * 100 / $grandTotal;
                        
                        $brandAbcPercent = number_format($brandAbcPercent, 2, '.', '');
                        
                        $brandAbcPercents[] = $brandAbcPercent;
                        
                        $sumBrandAbcPercent += $brandAbcPercent;
                        
		                $itemAbcPercent = 0 ;
		             	echo "<tr style='background-color:#e4e4e4'>
		             		<td colspan='3'>{$key} &nbsp;&nbsp;<strong>{$brandAbcPercent}%</strong></td>  <td></td> <td></td> <td></td>  <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>
		             	</tr>";
		             	$sumASERP = $sumItemAbcPercent = $costTotalSum = $cost = $costTotal = $sumAS = $sumQtd = $totalPrice = $totalQtd = 0 ;
		             	
		             	foreach ($brand as $i => $fetch){
		             	    
		             	    if($i != 'total'){
		             	        
		             	        $itemAbcPercent = $fetch['total'] * 100 / $brand['total'];
		             	        $sumItemAbcPercent += $itemAbcPercent;
		             	        $sumItemAbcPercent = number_format($sumItemAbcPercent, 2, '.', '');
		             	        $itemAbcPercent = number_format($itemAbcPercent, 2, '.', '');
    		             		$cost = $fetch['cost'];
    		             		$costTotal = $cost * $fetch['order_items'];
    		             		echo "<tr>
    				             		<td ></td>
    				             		<td align='center'> {$fetch['quantity']} </td>
    				             		<td colspan='2'>{$fetch['sku']} - {$fetch['title']}</td>
    				             		<td align='center'> {$fetch['variation']}</td>
    				             		<td align='center'> {$fetch['color']}</td>
    				             		<td align='center'> {$cost}</td>
    				             		<td align='center'> {$costTotal}</td>
    				             		<td align='center'> {$fetch['total']}</td>
                                        <td align='center'> {$itemAbcPercent}%</td>
    				             		<td align='center'> {$fetch['available_stock']}</td>
                                        <td align='center'> {$fetch['qty_erp']}</td>
    			             		</tr>";
    		             	
    		             		if($totalPrice > 0){
    		             			$totalPrice += $fetch['total'];
    		             			$totalQtd += $fetch['quantity'];
    		             		}else{
    		             			$totalPrice = $fetch['total'];
    		             			$totalQtd = $fetch['quantity'];
    		             		}
    		             		if($costTotalSum > 0){
    		             			$costTotalSum += $costTotal;
    		             		}else{
    		             			$costTotalSum = $costTotal;
    		             		}
    		             		$sumCostTotal += $costTotal;
    		             		$sumAS += $fetch['available_stock'];
    		             		$sumASERP += $fetch['qty_erp'];
    		             		$sumQtd += $fetch['quantity'];
    		             		$sumTotal += $fetch['total'];
		             	    }
		             		
		             	
		             	}
		             	
		             	$sumQtdTotal +=$sumQtd;
		             	
		             	echo "<tr>
		             		<td></td>
                            <td align='center' ><strong>{$sumQtd}</strong></td> 
                            <td></td> 
                            <td></td> 
                            <td></td> 
                            <td></td> 
                            <td></td>
		             		<td align='center' ><strong>{$costTotalSum}</strong></td> 
                            <td align='center' ><strong>{$totalPrice}</strong></td> 
                            <td align='center' ><strong>{$sumItemAbcPercent}%</strong></td> 
                            <td align='center' ><strong>{$sumAS}</strong></td>
                            <td align='center' ><strong>{$sumASERP}</strong></td>
		             	</tr>"; 
		             	
		             }

				}
	             ?>
		 
		 		</tbody>
		 		<tfoot>
		 			<tr>
		 				<th></th>
			            <th style='text-align:center;'><?php echo $sumQtdTotal; ?></th>
			            <th></th>
			            <th></th>
			            <th></th>
			            <th></th>
			            <th></th>
			           	<th style='text-align:center;'><?php echo $sumCostTotal; ?></th>
			            <th style='text-align:center;'><?php echo $sumTotal; ?></th>
			            <th style='text-align:center;'><?php echo $sumBrandAbcPercent; ?>%</th>
			            <th style='text-align:center;'><?php echo $sumBrandAbcPercent; ?>%</th>
			            <th></th>
			            <th></th>
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
