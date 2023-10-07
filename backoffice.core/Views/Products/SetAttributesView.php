<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class='col-md-12'>
		<div class="message"><?php if(!empty( $setAttributesModel->form_msg)){ echo  $setAttributesModel->form_msg;}?></div>
		<div class='box box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'><?php echo $this->title; ?></h3>
	        	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='<?php echo HOME_URI ?>/Products/SetAttributes' class='btn btn-block btn-default btn-xs'><i class='fa fa-plus'></i> Novo</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<form method="POST" action="" name="form-setattributes">
			<input type="hidden" name="id" value="<?php echo $setAttributesModel->id; ?>" />
			<div class='box-body'>
				<div class="row">

					<div class="col-md-4">
						<div class="form-group">
							<label>Nome do conjunto:</label> 
							<input type="text" name="set_attribute" id="set_attribute" class="form-control brand"  value="<?php echo $setAttributesModel->set_attribute; ?>" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Categoria Raiz:</label>
							<select class='form-control' id='root_category' name="root_category">
							<option value=''> Selecione</option>
							<?php 
							
							foreach ($listCategoriesRoot as $key => $value){
							    $selected = $setAttributesModel->root_category == $value['hierarchy'] ? 'selected' : '' ;
								echo "<option value='{$value['hierarchy']}' {$selected}>{$value['hierarchy']}</option>";
							}
							?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
                    	<div class="form-group">
                    		<label>Categoria padrão:</label>
                    		<select class='form-control' name="category" id='category'>
                    		<option value=''> Selecione</option>
                    		<?php 
                    		foreach ($listCategoriesFromRoot as $key => $value){
                    		    $selected = $setAttributesModel->category == $value['hierarchy'] ? 'selected' : '' ;
                    			echo "<option value='{$value['hierarchy']}' {$selected}>{$value['hierarchy']}</option>";
                    		}
                    		?>
                    		</select>
                    	</div>
                    </div>
					<div class="col-md-12">
						<?php 
						foreach($attributesList as $key => $attr){
// 							pre($setAttributesModel->attribute_id_list);die;
							$checked = in_array($attr['id'], $setAttributesModel->attribute_id_list) ? "checked" : "" ; 
							echo "<div class='col-md-3'>
									<div class='form-group'>
									<label class='col-md-12 control-label checkbox'>
										<input name='attribute_id_list[]' type='checkbox' class='flat-red' value='{$attr['id']}' {$checked}> {$attr['attribute']}
									</label>

									</div>
								</div>";
						}
						?>
    				</div>

				</div>
			</div>
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary pull-right btn-sm" id="btn" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
		</div>
	</div>
</div>	

<div class='row'>	
	<div class="col-md-12">
		<div class='box'>
			<div class='box-header'>
		       	<h3 class='box-title'>Listagem de Conjunto de atributos</h3>
			</div><!-- /.box-header -->
			<div class="box-body">
    			<div class="row">
        			<div class="col-md-12">
        				<table id="search-simple" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
            			    <thead>
                		        <tr>
                		            <th>ID</th>
                		            <th>Atributo</th>
                		            <th>Categoria Raiz</th>
                		            <th>Categoria</th>
                		            <th>Edição</th>
                		        </tr>
        			        </thead>
            		 		<tbody>
            		 
            	             <?php foreach ($listSetAttrHierarchy as $fetch): ?>
            	             
            	             <tr>
            	                 <td> <?php echo $fetch['id'] ?> </td>
            	                 <td> <?php echo $fetch['set_attribute'] ?> </td>
            	                 <td> <?php echo $fetch['root_category'] ?> </td>
            	                 <td> <?php echo $fetch['hierarchy'] ?> </td>
            	                 <td align='right'> 
            	                     <a href="<?php echo HOME_URI ?>/Products/SetAttributes/edit/<?php echo $fetch['id'] ?>" class='fa fa-pencil-square-o' />&nbsp;&nbsp;
            	                     <a href="<?php echo HOME_URI ?>/Products/SetAttributes/del/<?php echo $fetch['id'] ?>" class='fa fa-trash delete' />
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
</div>