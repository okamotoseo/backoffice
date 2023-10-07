<?php if ( ! defined('ABSPATH')) exit;?>
<style type="text/css">
				 @media print{
					#report, #headerreport{display: block !important;}
					.main-sidebar, .left-side, .sidebar, .filter,
					.navbar, .filter-report, 
					.date, .noprint, 
					.datepicker, 
					.dropdown-menu, .footer, .logo, .top-actions, .credit, .alert
					#btn-print, #form-order, #footer, .alert-warning, .breadcrumb, 
					.main-footer, #myModal, .printed, .view, .order {display: none;}
					.content{padding:0px !important;}
					
				}
</style>
<div class="row filter">
	<!-- Default box -->
	<div class="col-md-6">
	
		<div class="message"><?php if(!empty( $brandSalesModel->form_msg)){ echo  $brandSalesModel->form_msg;}?></div>
		
		<div class="box box-primary">
			<form  method="POST" action="/Report/Inventory/" name="inventory-brand">
				<div class="box-header with-border">
					<h3 class="box-title">Iventario por marca</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Report/Inventory/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body table-responsive">
						
						<div class="col-sm-12">
							<div class="form-group">
								<label>Marcas:</label>
								<select  name="brand" class="form-control select2">
								<option value=''>Todas</option>
								<?php 
								if(isset($brands)){
								    foreach($brands as $k => $value){
								        $selected = $value['brand'] == $inventoryModel->brand ? "selected" : '' ;
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
					<button type='submit' id='report-inventiry-brand' name='filter-report-inventiry-brand' class='btn btn-primary btn-sm pull-right submit-load'>Gerar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class='row'>
	<div class="col-md-12">
		<div class='box'>
			<div class='box-header'>
		       	<h3 class='box-title'>Inventário</h3>
		       	<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<?php 
        	        			echo "<button type='button' class='btn btn-ptimary btn-xs' onclick='window.print();'><i class='fa fa-print'></i> Imprimir</button>";
        	        		?>
        	        	</div>
    	        	</div>
			</div><!-- /.box-header -->
		
		<div class="box-body no-padding">
			<div class="col-md-12">
			<table class="table table-condensed">
		        <thead>
			        <tr align='center'>
			            <th style='text-align: center;'>Sku</th>
			            <th>Foto</th>
			            <th>Produto</th>
			            <th style='text-align: center;'>Cor/Variação</th>
			            <th style='text-align: center;'>Qtd</th>
			            <th>Custo</th>
			            <th>Custo T.</th>
			        </tr>
		        </thead>
	 		<tbody>
	 
             <?php
             $sumQtd = $count = 0;
             
//              pre($list);
             foreach ($list as $key => $fetch){
                 $qtyErp = $fetch['quantity'];
                 $qtyErp = $fetch['store_id'] == 4  ? $fetch['qty_erp'] :  0 ;
                 
                 $sumQtd += $qtyErp;
                 $costItem = $qtyErp * $fetch['cost'];
                
	             echo "
    	             <tr>
                         <td align='center'> {$fetch['sku']} </td>
                         <td>
                            	<img src='{$fetch['thumbnail']}' width='60px' />
                         </td>
                         <td> {$fetch['title']} <br> 
	                         <span class='small'><strong>Marca:</strong> {$fetch['brand']}</span>
	                         <span class='small'><strong>Referência:</strong> {$fetch['reference']}</span>
	                         <span class='small'><strong>EAN:</strong> {$fetch['ean']}</span>
                         </td>
                         <td align='center'> {$fetch['color']} <br> {$fetch['variation']} </td>
    	                 <td align='center'><strong>{$qtyErp}</strong></td>
    	                 <td align='center'><strong>{$fetch['cost']}</strong></td>
                        <td align='center'><strong>{$costItem}</strong></td>
    	             </tr>";
	             $count++;
	             $sumCost += $costItem;
             
             }
             ?>
			 <tr>
			 	<td colspan='3'><?php echo $count." Itens"; ?></td>
			 	<td align='right'>Total</td>
			 	<td><?php echo $sumQtd; ?></td>
			 	<td></td>
                <td><?php echo $sumCost; ?></td>
			 	
			 </tr>
	 		</tbody>
			</table>
			</div>
		</div>
		<div class="overlay" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div>
	</div>
</div>
