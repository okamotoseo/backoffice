<?php if ( ! defined('ABSPATH')) exit; ?>

<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
		        <div class='box-tools pull-right'>
		        	<a href='/Modules/Magento2/Products/SetAttributes/'  class='btn  btn-xs btn-default'><i class='fa fa-arrow-circle-left'></i></a>
        	     	<?php 
			       	
					// All types integrations need import attributes from magento 2					
					echo "<button  class='btn  btn-xs btn-default import_product_attributes'><i class='fa fa-plus'></i> Importar Atributos</button>&nbsp;";
					
					if($this->moduledata['type'] == 'import' OR $this->moduledata['type'] == 'import_export'){
						echo "<button  class='btn  btn-xs btn-default add_update_attribute_mg2'><i class='fa fa-plus'></i> Importar Valores dos Atributos Magento2</button>";
					}
					
	    	        ?>
	    	        <a href='/Modules/Magento2/Products/AttributesRelationship/'  class='btn  btn-xs btn-default import_product_attributes'><i class='fa fa-refresh'></i></a>
	        	</div>
			</div><!-- /.box-header -->
			
			<div class="box-body ">
			
				<div class='row'>
					<div class='col-md-12'>
						<div class="callout callout-info">
	                    	<h4><i class='fa fa-star'></i> Destaque os atributos mais importantes!</h4>
	                    	<p>Serão listados até 5 características em destaque por conjunto de 
	                    	atributos na página do produto na posição do bloco Short Description.</p>
	                	</div>
	                </div>
	                <div class='col-md-12'>
	                	<div class="callout callout-warning">
	                    	<h4><i class='fa  fa-warning'></i> Destaque os atributos mais importantes!</h4>
	                    	<p>Serão listados até 5 características em destaque por conjunto de 
	                    	atributos na página do produto na posição do bloco Short Description.</p>
	                	</div>
					</div>
				</div>
				
			<div class='row'>
				<div class="col-md-12">
                    <table  class="table table-striped table-hover display" >
                    	<thead>
                    
                    	<tr>
                    		<th></th>
                    		<th>Atributo</th>
                    		<th>Mapear Atributos Sysplace</th>
                    		<!-- <th style='text-align:center'>Impotar Valores</th> -->
                    		<th></th>
                    		
                    	</tr>
                    	</thead>
                    	<tbody>
                        <?php 
                        
                         
                         if(isset($productAttributes)){
	                         foreach($productAttributes as $k => $attr){
	                         	if(!empty($attr['alias'])){
	                         		$attrDefault['attribute'][$attr['alias']] = "{$attr['attribute']} / {$attr['alias']}";
	                         	}
	                         }
                         }
                        if(isset($attributes)){
                            foreach($attributes as $key => $attribute){
                            	
                            	$required = $attribute['is_required'] == 0 ? "" : "<span class='label label-danger pull-right' title='Atributo Obrigatorio'>Obrigatório</span>";
                            	$spotlight = $attribute['spotlight'] == 0 ? "-o" : "";
                            	$configurable = $attribute['is_configurable'] == 0 ? "" : "<span class='label label-info pull-right' title='Variação'>Variação</span>";
                               	$checked = $attribute['import_values'] > 0  ? "checked" : "" ;
                               	$disabledSelect = '';
                               	if(isset($setAttrRel)){
                               		$disabledSelect = !empty($attribute['relationship']) ? 'disabled' : '' ;
                               	}
                                echo "<tr id='{$attribute['attribute_id']}' >
                                		<td  align='center'>
                                			<a class='star-on mg2_attribute_spotlight fa fa-star{$spotlight} text-grey star-attribute' status='' attr_code='{$attribute['attribute_code']}' attribute_id='{$attribute['attribute_id']}' ></a>
                                		</td>
                                        <td width='20%'>{$attribute['attribute']}{$required} <br><small class='grey'>#{$attribute['attribute_code']} / {$attribute['frontend_input']}</small> {$configurable}</td>
                                        <td>";
                                      echo "<select class='form-control mg2_attribute_relationship select2' 
                                            attribute_id='{$attribute['attribute_id']}' 
                                            attribute_code='{$attribute['attribute_code']}'
                                            attribute_set_id='{$attributeSetId}' 
                                            style='width:100%' {$disabledSelect}>
                                                <option value='select'>Selecione</option>";
                                      
                                				$selected = '';
                                				$exist = false;
                                                foreach($attrDefault['default'] as $attrId => $label ){
                                                	$selected = '';
                                                    if($attribute['relationship'] == $attrId){
                                                    	$selected = "selected";
                                                    	$exist = true;
                                                    }
                                                    echo "<option value='{$attrId}' type='default' {$selected}>{$label}</option>";
                                                }
                                                if(isset($attrDefault['attribute'])){
	                                                foreach($attrDefault['attribute'] as $attrId => $label ){
	                                                	$selected = '';
	                                                	if(!$exist){
		                                                	foreach($setAttrRel as $m => $attRelationship){
		                                                		if($attribute['attribute_id'] == $attRelationship['attribute_id'] && strtolower($attrId) == strtolower($attRelationship['relationship'])){
		                                                			$selected = "selected";
		                                                			$exist = true;
		                                                		}
		                                                	}
	                                                	}else{
	                                                		$selected = '';
	                                                	}
	                                                	echo "<option value='{$attrId}' type='attribute' {$selected}>{$label} - Atributo</option>";
	                                                	
	                                                }
                                                }
                                        echo " </select>";
                                        echo "</td>";
//                                         echo "<td align='center'>
//                                             <div class='form-group '><label>
//                                                 <input type='checkbox' attribute_id='{$attribute['attribute_id']}' class='flat-red mg2_import_values' {$checked} >
//                                             </label></div>
//                                         </td>";
                                        echo "<td align='center' valign='center'>
                                            <a class='remove_attribute_magento' attribute_id='{$attribute['attribute_id']}' role='menuitem' tabindex='-1' title='Excluir attributo ecommerce'><i class='fa fa-trash'></i></a>
                                        </td>
                                    </tr>";
                                
                            }
                        }
    
                        ?>
                        </tbody>
                    </table>
                    	<?php 
//                     	pagination($totalReg, $attributesModel->pagina_atual, HOME_URI."/Modules/Onbi/Products/Attributes"); ?>
                    </div>
            	</div>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
		</div>
	</div>
</div>