<?php if ( ! defined('ABSPATH')) exit; ?>

<div class="row">
	<div class="col-md-12">
		<div class="message">
			<?php 
			if(!empty( $mlAttributesModel->form_msg)){ echo $mlAttributesModel->form_msg;}?>
		</div>
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class="box-title"><?php echo $this->title; ?></h3>
		       	<div class="row">
					<div class="col-sm-6">
						Vizualizar atributos da categoria: <a href='https://api.mercadolibre.com/categories/<?php echo $mlAttributesModel->category_id; ?>/attributes' target='_blank'><?php echo $mlAttributesModel->category_id; ?></a>
					</div>
					<div class="col-sm-6"></div>
				</div>
			</div><!-- /.box-header -->
			
				<div class="box-body table-responsive">
				<div class="col-md-12">
<!-- 				<table id="example2" class="table table-bordered table-hover"> -->
<!-- 				<table id='search-advanced' class="table table-bordered  table-hover" > -->
<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
			        <thead>
				        <tr>
				            <th>Id/Types</th>
				            <th>Nome</th>
				            <th>Relacionamento</th>
				            <th>Atributos</th>
				        </tr>
			        </thead>
		 		<tbody>
		 		
	             <?php 
	             
	             foreach ($listAttributesRequired as $fetch){
	                 $required = $fetch['required'] ? "<strong>(Obrigatório)</strong>" : "";
					echo "<tr>
                        <td>{$fetch['required_attribute_id']} - {$fetch['value_type']}<br>{$fetch['tag']}</td>
						<td>{$fetch['name']}<br>{$required}</td>
                        <td>{$fetch['attribute']}</td>
						<td>
							<select class='form-control ml_attribute_relationship'
                                ml_category_id='{$fetch['category_id']}' 
                                ml_attribute_id='{$fetch['required_attribute_id']}' >
                                <option value='select'>Selecione</option>
            					<option value='0|sku'>SKU</option>
                                <option value='0|title'>Título</option>
            					<option value='0|color'>Cor</option>
            					<option value='0|variation'>Variação</option>
            					<option value='0|brand'>Marca</option>
            					<option value='0|reference'>Referencia</option>
            					<option value='0|weight'>Peso da embalagem</option>
            					<option value='0|height'>Altura da embalagem</option>
            					<option value='0|width'>Largura da embalagem</option>
            					<option value='0|length'>Comprimento da embalagem.</option>
            					<option value='0|ean'>EAN</option>
            					<option value='0|description'>Descrição</option>";
            					foreach($listAttributes as $key => $attribute){
            					    $selected = $attribute['id'] == $fetch['attribute_id'] ? 'selected' : '';
            					    echo "<option value='{$attribute['id']}|{$attribute['alias']}' {$selected} >{$attribute['attribute']}</option>";
            					    
            					}
            					echo "</select>
						</td>
					</tr>";
	             } 
	             
	             ?>
		 		</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>