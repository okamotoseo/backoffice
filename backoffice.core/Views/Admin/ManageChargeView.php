<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-6">
	
		<div class="message"><?php if(!empty( $manageChargeModel->form_msg)){ echo  $manageChargeModel->form_msg;}?></div>
		
		<div class="box box-primary">
			<form  method="POST" action="" name="filter-order">
				<div class="box-header with-border">
					<h3 class="box-title">Gerar Faturamento das contas</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Admin/ManageCharge/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body table-responsive">
	
					<div class="col-md-12">
						<div class="form-group">
							<label>Período:</label>
							<select  name="month" class="form-control">
							<option value=''>Selecione</option>
								<?php 
								$start = $month = strtotime('2020-01-01');
								$end = strtotime(date('Y-m-d'));
								while($month < $end)
								{
								    
								    $selected = isset( $salesModel->month) && $salesModel->month.' '.$salesModel->year == date('F Y', $month) ? 'selected' : '' ;
								    echo "<option value='".date('F Y', $month)."' {$selected}>".date('F Y', $month)."</option>";
								    $month = strtotime("+1 month", $month);
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
    <div class="col-md-6">
      <div class="box box-solid">
        <div class="box-header with-border">
          <i class="fa fa-text-width" aria-hidden="true"></i>
          <h3 class="box-title">Faturamento <?php echo $salesModel->month.' '.$salesModel->year; ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table class="table table-condensed no-padding" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
    			<thead>
    				<tr>
    					<th>Ano</th>
    					<th>Mes</th>
    					<th>Faturado</th>
    					<th>Comissão</th>
    					<th></th>
    				</tr>
    			</thead>
    			<tbody>
    				<tr>
    					<td><?php echo $salesModel->year; ?></td>
    					<td><?php echo $salesModel->month; ?></td>
    					<td><?php echo $total; ?></td>
    					<td><?php echo number_format($total * 0.01, 2); ?></td>
    					<td></td>
    				</tr>
    			</tbody>
		</table>
					
					
        </div><!-- /.box-body -->
        <div class="box-footer">
    		<button  id='report-product-sales' name='btn-filter-order' class='btn btn-warning btn-xs pull-right submit-load' value='btn-filter-order'>Gerar Titulo</button>
    	</div>
      </div><!-- /.box -->
    </div>
   
   
</div>
<?php 



?>
