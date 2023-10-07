<?php if ( ! defined('ABSPATH')) exit;?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message"><?php if(!empty( $questionsModel->form_msg)){ echo  $questionsModel->form_msg;}?></div>
		
		<div class="box box-primary">
			<form  method="POST" action="/Report/Questions/" name="filter-questions">
				<div class="box-header with-border">
					<h3 class="box-title">Perguntas por produtos</h3>
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
                        		<input type='text' name='sku' class='form-control'  value='<?php echo $questionsModel->sku;?>' />
                        	</div>
                        </div>
                        <div class="col-sm-2">
                        	<div class="form-group">
                        		<label>Titulo:</label>
                        		<input type='nome' name='nome' class='form-control'  value='<?php echo $questionsModel->nome;?>' />
                        	</div>
                        </div>
						<div class="col-sm-2">
							<div class="form-group <?php echo $questionsModel->field_error['DataPedido']; ?>">
							<label>Data Inicial:</label> 
							<div class="input-append" id="dp3" data-date="" data-date-format="dd/mm/aaaa">
								<input type="text"  name="DataPedido" id="data-1" class="form-control date date-mask"  placeholder='00/00/0000' value="<?php echo $questionsModel->DataPedido;?>">
								<span class="add-on" ><i class="icon-th"></i></span>
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label>Data Final:</label>				
							<div class="input-append" id="dp3" data-date="" data-date-format="dd/mm/aaaa">
								<input type="text"  name="DataPedidoAte" id="data-2" class="form-control date date-mask"  placeholder='00/00/0000' value="<?php echo $questionsModel->DataPedidoAte;?>">
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
					<button type='submit' id='report-questions' name='btn-questions' class='btn btn-primary btn-sm pull-right submit-load'>Gerar</button>
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
		       	<div class="row">
					<div class="col-sm-6"></div>
					<div class="col-sm-6"></div>
				</div>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive">
				<div class="col-md-12">
				<table id="s" class="table table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
			        <thead>
				        <tr>
				            <th>Sku</th>
				            <th>Título</th>
				            <th>Marketplace</th>
				            <th>Status</th>
				            <th>Criada</th>
				            <th>Respondida</th>
				        </tr>
			        </thead>
		 		<tbody>
		 
	             <?php
	             
	             foreach ($list as $key => $fetch){
// 	             	pre($fetch);
	             echo "
    	             <tr>
    	                 <td>{$fetch['sku']}</td>
                         <td><strong>{$fetch['title']}</strong><span class='bg bg-default pull-right'> {$fetch['qtd']}</span></td>
                         <td> {$fetch['marketplace']} </td>
    	                 <td> {$fetch['status']} </td>
    	                 <td> {$fetch['date_created']} </td>
                         <td> {$fetch['answer_date_created']} </td>
    	             </tr>";
		             foreach ($fetch['answers'] as $k => $answer){
		             	echo "
		             	<tr>
			             	<td></td>
			             	<td><strong>P:</strong> {$answer['question']} <br><strong>R:</strong> {$answer['answer']} </td>
			             	<td> {$answer['marketplace']} </td>
			             	<td> {$answer['status']} </td>
			             	<td> {$answer['date_created']} </td>
			             	<td> {$answer['answer_date_created']} </td>
		             	</tr>";
		             
		             }
	             
	             }
	             ?>
		 
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
