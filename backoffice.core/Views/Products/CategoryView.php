<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-8">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Listagem de categorias</h3>
				<div class="box-tools"></div>
			</div><!-- /.box-header -->
			<div class="box-body no-padding">
				<table  class="table table-condensed">
			        <thead>
				        <tr>
				            <th>Hierarquia</th>
				            <th>Categoria</th>
				            <th style='text-align:center;'>Produtos</th> 
				            <th></th>
				        </tr>
			        </thead>
		 		<tbody>
		 
	             <?php  
	             $choose = $list;
	             $optionsToChange = "<option value='select||'>Selecione</option>";
	             foreach ($list as $fetch){
	             	$styleParent = '';
	             	$iconAngle = "&nbsp;&nbsp;&nbsp;<i class='fa fa-angle-right'></i>";
	             	if($fetch['parent_id'] == 0){
	             		$styleParent = "style='background-color:#e4e4e4'" ;
	             		
	             		$iconAngle = "<i class='fa fa-angle-double-right'></i>";
	             	}
	             	
	             	$linkProductsCategory =  str_replace(" ", "_", removeAcentosNew($fetch['hierarchy']));
	             	
	             	$optionsToChange .= "<option value='{$fetch['parent_id']}|{$fetch['id']}|{$fetch['hierarchy']}'>{$fetch['hierarchy']}</option>";
	             	
		             echo "<tr {$styleParent} >
			                <td>{$iconAngle} {$fetch['hierarchy']}</td>
			                <td><span title='{$fetch['parent_id']} / {$fetch['id']} / {$fetch['type']}'>{$fetch['category']}</span></td>
			                <td align='center'><a href='/Products/AvailableProducts/Page/1/category/{$linkProductsCategory}' title='Listar produtos associados nesta categoria.' target='_blank'>{$fetch['items']}</a></td>
			                <td align='right'>
			                	<div class='dropdown'>
					            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
						            <ul class='dropdown-menu pull-right' style='min-width:100px'>
							            <li role='presentation'><a href='/Products/Category/edit/{$fetch['id']}' class='submit-load' /><i class='fa fa-pencil-square-o'></i>Editar</a></li>
							            <li role='presentation'><a class='change_products_category_modal text-yellow' data-toggle='modal' data-target='#change_products_category'  
				                        category_id='{$fetch['id']}' parent_id='{$fetch['parent_id']}' hierarchy='{$fetch['hierarchy']}'  qty='{$fetch['items']}' ><i class='fa fa-exchange'></i>Substituir Categoria</a></li>
					                    <li role='presentation'><a href='/Products/Category/del/{$fetch['id']}' class='submit-load delete text-red' /><i class='fa fa-trash'></i>Excluir</a></li>
				                    </ul>
			                    </div>
			                </td>
			             </tr>";
	             
	             }
	             ?>
		 
		 		</tbody>
				</table>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
            </div>
		</div>
	</div>
	<div class='col-md-4'>
		<div class="message"><?php if(!empty( $categoryModel->form_msg)){ echo  $categoryModel->form_msg;}?></div>
		<div class='box box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'>Cadastrar categoria</h3>
	        	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='/Products/Category/' class='btn btn-block btn-default btn-xs'>Limpar</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<div class='box-body'>
				<form method="POST" action="/Products/Category/" name="form-category">
				<input type="hidden" name="id" value="<?php echo $categoryModel->id; ?>" />
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Hierarquia:</label> 
							<?php 
							echo "<select class='form-control' name='parent_id'  {$categoryModel->readonly} >";
							if(empty($categoryModel->readonly)){
								
								echo "<option value='0'> Categoria Raiz</option>";
								foreach($list as $key => $category){
								    $selected = $category['hierarchy'] == $categoryModel->children ? "selected" : "" ;
	    								echo "<option value='{$category['id']}' {$selected}>{$category['hierarchy']}</option>";
								}
								
							}else{
								
								$option = "<option value='0'> Categoria Raiz</option>";
								foreach($list as $key => $category){
									if($category['hierarchy'] == $categoryModel->children){
										$option = "<option value='{$category['id']}' selected >{$category['hierarchy']}</option>";
									}
								}
								echo $option;
								
							}
							echo "</select>";
							?>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label>Categoria:</label> 
							<input type="text" name="category" id="category" class="form-control category"  value="<?php echo $categoryModel->category; ?>" <?php echo $categoryModel->readonly; ?> />
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label>Descrição:</label> 
							<textarea type="text" rows='4' name="description" id="description" class="form-control description" placeholder="Descrição"><?php echo $categoryModel->description; ?></textarea>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
						<label>Conjunto de atributos:</label> 
							<select class='form-control' name='set_attribute_id'>
							<option value='' selected>Selecione</option>
        					<?php 
            					if(isset($listSetAttributes)){
            					    foreach($listSetAttributes as $key => $value){
            					        $selected = $value['id'] == $categoryModel->set_attribute_id ? "selected" : "" ;
            					        $attrOption = mb_strtoupper($value['set_attribute'], 'UTF-8');
            					        echo "<option value='{$value[id]}' {$selected}>{$attrOption}</option>";
            					   }
            					}
        					?>
        					</select>
						</div>
					</div>
				</div>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
            </div>
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary pull-right btn-sm submit-load" id="btn" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
		</div>
	</div>
	
</div>

<div class="modal fade" id='change_products_category' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Substituir Categorias de Produtos</h4>
      </div>
      <div class="modal-body">
      	<div id='message'></div>
        <div class='row'>
	        <input type="hidden" name="category_id_from" id='category_id_from' disabled class="form-control"  value="">
	        <input type="hidden" name="parent_id_from" id='parent_id_from' disabled class="form-control"  value="">
			<div class="col-md-12">
				<div class="form-group">
					<label>Existem <span id='qty_products'></span> Produtos Associados a Categoria :</label> 
					<input type="text" name="category_from" id='category_from' disabled class="form-control"  value="">
				</div>
			</div>
			<div class="col-md-12">
				<div class="form-group">
					<label for='category_to'>Substituir por:</label> 
					<select name='category_to' class='form-control' id='category_to'><?php echo $optionsToChange; ?></select>
				</div>
			</div>
			</div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default  btn-sm pull-left" id='close-modal' data-dismiss="modal">Cancelar</button>
        <a class="btn btn-warning btn-sm" id='change_categories'>Substituir em Todos Produtos e Mapeamentos</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>