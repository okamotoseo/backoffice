<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
		       	<div class='box-tools pull-right'>
		       	
		       	<?php 
		       	if($this->moduledata['type'] == 'import' OR $this->moduledata['type'] == 'import_export'){ 
    	        	echo "<button class='btn btn-xs btn-default import_attribute_set_mg2'><i class='fa fa-download'></i> Importar Conjunto de Atributos</button>&nbsp;";
		       	}
		       	if($this->moduledata['type'] == 'export' OR $this->moduledata['type'] == 'import_export'){
    	        	echo "<button class='btn btn-xs btn-default export_attribute_mg2'><i class='fa fa-upload'></i> Exportar Conjunto de Atributos</button>";
				}
    	        ?>
	        	</div>
			</div><!-- /.box-header -->
			
			<div class="box-body box-responsive">
				<div class="col-md-12">
                    <table  class="table table-striped">
                    	<thead>
                    	<tr>
                    		<th>Id</th>
							<th>Conjuntos de Atributos</th>
                    		<th>Conjunto de Atribuos Magento2</th>
                    		<th>Tipo de Variação</th>
                    		<th></th>
                    	</tr>
                    	</thead>
                    	<tbody>
                            <?php
                            if(!empty($setAttributesMg2['body'])){
//                                 pre($setAttributesMg2);die;
                                foreach($setAttributes as $key => $setAttribute){
                                	$linkAttrSet = "";
                                	if(!empty($setAttribute['mg2_attribute_set_id'])){
                                		$linkAttrSet = "<a href='/Modules/Magento2/Products/AttributesRelationship/SetId/{$setAttribute['id']}/AttributeSetId/{$setAttribute['mg2_attribute_set_id']}' >Mapear Aributos</a>";
                                	}
                                	
                                	$unidade = $variation = $tamanho = $voltagem = '';
                                    switch($setAttribute['variation_label']){
                                        case "Variação": 
                                            $variation = "selected";
                                            break;
                                        case "Tamanho": 
                                            $tamanho = "selected";
                                            break;
                                        case "Voltagem": 
                                            $voltagem = "selected";
                                            break;
                                        case "Unidade":
                                           	$unidade = "selected";
                                           	break;
                                    }
                                    
                                    $exist = false;
                                    echo "<tr id='{$setAttribute['id']}'>
                                    		<td>{$setAttribute['id']}</td>
                                            <td>{$setAttribute['set_attribute']}</td>
                                            <td>
                                    <select class='form-control set_attr_relationship_ecommerce' set_attribute_id='{$setAttribute['id']}'  >
                                    <option value='select'> >> Selecione</option>";
                                    foreach($setAttributesMg2['body']->items as $key => $setAttributeMg2){
//                                     	pre($setAttributeMg2);
                                        $selected = '';
                                        if($setAttribute['mg2_attribute_set_id'] == $setAttributeMg2->attribute_set_id){
                                            $selected =  'selected';
                                            $exist = true;
                                        }
                                        echo "<option value='{$setAttributeMg2->attribute_set_id}' {$selected} >{$setAttributeMg2->attribute_set_name}</option>";
                                    }
                                    if(!$exist){
                                        echo "<option value='export_set_attr_ecommerce' >Exportar ecommerce</option>";
                                    }
                                    echo "</select></td>
                                    <td>";
                                    $display = $exist ? "" : "disabled";
                                        echo "<select id='select-variation-{$setAttribute['id']}' {$display} class='form-control set_variation_label_relationship_ecommerce' set_attribute_id='{$setAttribute['id']}'  >
                                            <option value='select'> >> Selecione</option>
                                            
                                            <option value='Voltagem' variation_type='voltagem' {$voltagem}>Voltagem (110V / 220V)</option>
                                            <option value='Tamanho' variation_type='tamanho'  {$tamanho}>Tamanho (P, GG, 35,41...)</option>
                                            <option value='Unidade' variation_type='unidade'  {$unidade}>Unidade (PC, JG, UN...)</option>
                                        </select>";
                                    echo "</td>
                                        <td align='right' id='td-link-{$setAttribute['id']}'>{$linkAttrSet}</td>       
                                        </tr>";
                                    
                                }
                            }
                            
                            ?>
                        </tbody>
                    </table>
                    </div>
			</div>
            <div class='overlay' style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
		</div>
	</div>
</div>