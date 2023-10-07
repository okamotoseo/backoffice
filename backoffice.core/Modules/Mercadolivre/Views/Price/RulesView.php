<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class='col-md-12'>
		<div class="message"><?php if(!empty( $priceModel->form_msg)){ echo  $priceModel->form_msg;}?></div>
		<div class='box'>
			<div class='box-header'>
	        	<h3 class='box-title'><?php echo $this->title; ?></h3>
			</div><!-- /.box-header -->
			<form method="POST" action="<?php echo HOME_URI ?>/Modules/Mercadolivre/Price/Rules" name="form-brands">
			<input type="hidden" name="id" value="<?php echo $priceModel->id; ?>" />
			<div class='box-body'>
				<div class="row">
					<div class="col-xs-2">
						<div class="form-group">
							<label for="condition">Condição:</label>
								<select id="condition" name="condition" class="form-control">
    								<option value=''>Selecione</option>
<!--                 					<option value='sku'>SKU</option> -->
<!--                                     <option value='title'>Título</option> -->
<!--                 					<option value='color'>Cor</option> -->
<!--                 					<option value='variation'>Variação</option> -->
<!--                 					<option value='brand'>Marca</option> -->
<!--                 					<option value='reference'>Referencia</option> -->
<!--                 					<option value='category'>Categoria</option> -->
<!--                 					<option value='quantity'>Quantidade</option> -->
<!--                 					<option value='price'>Preço</option> -->
                					<option value='sale_price'>Se Preço de Venda</option>
<!--                 					<option value='promotion_price'>Preço Promocional</option> -->
								</select>
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="operator">Operador:</label>
								<select id="operator" name="operator" class="form-control">
    								<option value=''>Selecione</option>
                					<option value='igual'>É igual</option>
                                    <option value='diferente'>É diferente de</option>
                					<option value='maior'>É maior que</option>
                					<option value='menor'>É menor que</option>
								</select>
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="value_test">Valor</label> 
							<input type="text" name="value_test"  id='value_test' class="form-control" value="<?php echo $priceModel->value_test; ?>">
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="rule">Regra:</label>
								<select id="rule" name="rule" class="form-control">
									<option value=''>Selecione</option>
    								<option value='aumentar'>Aumentar</option>
                					<option value='diminuir'>Diminuir</option>
								</select>
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="fixed_rate">Tx. Fixa</label> 
							<input type="text" name="fixed_rate"  id='fixed_rate' class="form-control" value="<?php echo $priceModel->fixed_rate; ?>">
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="percentage_rate">Tx. %</label> 
							<input type="text" name="percentage_rate"  id='percentage_rate' class="form-control" value="<?php echo $priceModel->percentage_rate; ?>">
						</div>
					</div>


				</div>
			</div>
			
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary pull-right" id="btn" name="save">Salvar</button>
			</div>
			</form>
		</div>
	</div>
	
	
	<div class="col-md-12">
		<div class='box'>
			<div class='box-header'>
		       	<h3 class='box-title'>Listagem de regras de preço</h3>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive">
				<div class="col-md-12">
				<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
			        <thead>
				        <tr>
				            <th>ID</th>
				            <th>Condição</th>
				            <th>Operador</th>
				            <th>value_test</th>
				            <th>Regra</th>
				            <th>Tx. Fixa</th>
				            <th>Tx. %</th>
				            <th>Ação</th>
				        </tr>
			        </thead>
		 		<tbody>
		 
	             <?php foreach ($list as $fetch): ?>
	             
	             <tr>
	                 <td> <?php echo $fetch['id'] ?> </td>
	                 <td> <?php echo $fetch['condition'] ?> </td>
	                 <td> <?php echo $fetch['operator'] ?> </td>
	                 <td> <?php echo $fetch['value_test'] ?> </td>
	                 <td> <?php echo $fetch['rule'] ?> </td>
	                 <td> <?php echo $fetch['fixed_rate'] ?> </td>
	                 <td> <?php echo $fetch['percentage_rate'] ?> </td>
	                 <td align='right'> 
	                     <a href="<?php echo HOME_URI ?>/Modules/Mercadolivre/Price/Rules/edit/<?php echo $fetch['id'] ?>" class='fa fa-pencil-square-o' />&nbsp;&nbsp;
	                     <a href="<?php echo HOME_URI ?>/Modules/Mercadolivre/Price/Rules/del/<?php echo $fetch['id'] ?>" class='fa fa-trash delete' />
	                 </td>
	             </tr>
	             
	             <?php endforeach;?>
		 
		 		</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>