<?php if ( ! defined('ABSPATH')) exit; ?>

<div class="row">
	<div class="col-md-12">
		<div class="message">
			<?php 
			if(!empty( $azAttributesModel->form_msg)){ echo $azAttributesModel->form_msg;}?>
		</div>
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class="box-title"><?php echo $this->title; ?></h3>
		       	<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='<?php echo HOME_URI ?>/Modules/Amazon/Map/Category/' class='btn btn-block btn-default btn-xs'><i class='fa fa-arrow-left'></i> Voltar</a>
        	        	</div>
    	        	</div>
		       	<div class="row">
					<div class="col-sm-6">
						Vizualizar atributos da categoria: <a href='<?php echo $azAttributesModel->xsd; ?>' target='_blank'><?php echo $azAttributesModel->xsdName." > ". $azAttributesModel->choice; ?></a>
					</div>
					
					<div class="col-sm-6"></div>
				</div>
			</div><!-- /.box-header -->
			
				<div class="box-body pad table-responsive">
                  <table class="table table-bordered table-hover">
			        
			        <thead>
				        <tr>
				            <th>Id</th>
				            <th style='text-align:center'>Types</th>
				            <th style='text-align:center'>Relacionamento</th>
				            <th>Atributos / (Conjunto)</th>
				        </tr>
			        </thead>
		 		<tbody>
		 		
	             <?php 
	             
	             foreach ($listAttributesRequired as $fetch){
// 	             	pre($azAttributesModel);die;
	             	$attrRelationshiped = '' ;
	             	foreach($azAttributesRelationship as $i => $attrInfo){
	             		if($attrInfo['az_attribute'] == $fetch['name']){
	             			$attrRelationshiped =  $attrInfo['attribute'];
	             		}
	             	}
	             	
	                $required = $fetch['required'] ? "<strong>(Obrigatório)</strong>" : "";
					echo "<tr>
                        <td width='40%'>{$fetch['name']}</td>
                        <td width='15%' style='text-align:center'>{$fetch['type']}{$fetch['values']}</td>
                        <td width='15%'>{$attrRelationshiped}</td>
						<td width='15%'>
							<select class='az_attribute_relationship btn-sm select2attrAmazon' id='select2attrAmazon'
                                az_attribute='{$fetch['name']}'
                                az_attribute_type='{$fetch['type']}'
                                xsd_name='{$azAttributesModel->xsdName}'
                                choice='{$azAttributesModel->choice}' >
                                <option value='select'>Selecione</option>
                                <option value='remove'>Remover</option>
            					 (default)<option value='0|sku'>SKU (default)</option>
                                 (default)<option value='0|title'>Título (default)</option>
            					 (default)<option value='0|color'>Cor (default)</option>
            					 (default)<option value='0|variation'>Variação (default)</option>
            					 (default)<option value='0|brand'>Marca (default)</option>
            					 (default)<option value='0|reference'>Referencia (default)</option>
            					 (default)<option value='0|weight'>Peso da embalagem (default)</option>
            					 (default)<option value='0|height'>Altura da embalagem (default)</option>
            					 (default)<option value='0|width'>Largura da embalagem (default)</option>
            					 (default)<option value='0|length'>Comprimento da embalagem. (default)</option>
            					 (default)<option value='0|ean'>EAN (default)</option>
            					 (default)<option value='0|description'>Descrição (default)</option>";
					
								if(isset($listAttributes)){
	            					foreach($listAttributes as $key => $attribute){
	            					    $selected = $attribute['id'] == $fetch['attribute_id'] ? 'selected' : '';
	            					    echo "<option value='{$attribute['id']}|{$attribute['alias']}' {$selected} >{$attribute['attribute']} ({$attribute['set_attribute']})</option>";
	            					    
	            					}
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