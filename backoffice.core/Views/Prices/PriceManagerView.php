<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-6">
	
		<div class="message"><?php if(!empty( $priceManager->form_msg)){ echo  $priceManager->form_msg;}?></div>
		
		<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Atualização de Preços de Venda</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Prices/PriceManager/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body table-responsive">
					<div class="col-sm-2">
						<label>Fixo R$:</label> 
						<input type="text"  name="fixed" class="form-control  input-sm" id='fixed' placeholder='5.00' value="">
					</div>
					<div class="col-sm-2">
						<label>Percent%:</label> 
						<input type="text"  name="percent" class="form-control input-sm"  id='percent' placeholder='5' value="">
					</div>
					
					<div class="col-sm-4">
						<div class="form-group">
							<label>Marca:</label>
							<select  name="brand" class="form-control input-sm"  id='brand'>
							<option value=''>Todas</option>
							<?php 
							if(isset($brands)){
							    foreach($brands as $k => $value){
							        $selected = $value['brand'] == $priceManager->brand ? "selected" : '' ;
								        echo "<option value='{$value['brand']}' {$selected}>{$value['brand']}</option>";
								   }
								
							}
							?>
							</select>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label>Ação:</label>
							<select  name="action" class="form-control input-sm"  id='action'>
    							<option value='increase'>Somar</option>
    							<option value='decrease'>Subtrair</option>
							</select>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type='submit' id='btn-update-price' name='btn-update-price' class='btn btn-primary btn-sm pull-right submit-load'><i class='fa fa-check'></i> Atualizar Preços</button>
				</div>
		</div>
	</div>
</div>