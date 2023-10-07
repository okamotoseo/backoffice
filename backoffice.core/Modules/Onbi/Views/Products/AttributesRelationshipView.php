<?php if ( ! defined('ABSPATH')) exit; ?>

<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
		        	<div class='box-tools pull-right'>
    	        		<button  class='btn  btn-xs btn-default add_update_attribute_onbi'><i class='fa fa-plus'></i> Criar e Atualizar Atributos</button>
    	        		<button  class='btn  btn-xs btn-default import_product_attributes'><i class='fa fa-plus'></i> Importar Atributos</button>
	        	</div>
			</div><!-- /.box-header -->
			
			<div class="box-body">
				<div class="col-md-12">
                    <table  class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
                    	<thead>
                    
                    	<tr>
                    		<th>attribute_code</th>
                    		<th>attribute</th>
                    		<th>frontend_input</th>
                    		<th>scope</th>
                    		<th>is_unique</th>
                    		<th>is_rerquired</th>
                    		<th>updated</th>
                    		<th>import_values</th>
                    		<th>Mapear</th>
                    		<th></th>
                    		
                    	</tr>
                    	</thead>
                    	<tbody>
                    
                    
                        <?php 
                        
                        $attrDefault = array();
                         $attrDefault['sku'] = 'SKU';
                         $attrDefault['parent_id'] = 'Parent Id';
                         $attrDefault['title'] = 'Título';
                         $attrDefault['color'] = 'Cor';
                         $attrDefault['variation'] = 'Variação';
                         $attrDefault['brand'] = 'Marca';
                         $attrDefault['reference'] = 'Referencia';
                         $attrDefault['weight'] = 'Peso da embalagem';
                         $attrDefault['height'] = 'Altura da embalagem';
                         $attrDefault['width'] = 'Largura da embalagem';
                         $attrDefault['length'] = 'Comprimento da embalagem.';
                         $attrDefault['ean'] = 'EAN';
                         $attrDefault['description'] = 'Descrição';
                         $attrDefault['price'] = 'Preço';
                         $attrDefault['sale_price'] = 'Preço de Venda';
                         $attrDefault['promotion_price'] = 'Preço Promocional';
                         $attrDefault['cost'] = 'Custo';
                         $attrDefault['ean'] = 'EAN - Codigo de Barras';
                         $attrDefault['ncm'] = 'NCM';
                        
                        if(isset($attributes)){
                            foreach($attributes as $key => $attribute){
                               $checked = $attribute['import_values'] > 0  ? "checked" : "" ;
                                echo "<tr id='{$attribute['attribute_id']}'>
                                        <td>{$attribute['attribute_code']}</td>
                                        <td>{$attribute['attribute']}</td>
                                        <td>{$attribute['frontend_input']}</td>
                                        <td>{$attribute['scope']}</td>
                                        <td>{$attribute['is_unique']}</td>
                                        <td>{$attribute['is_required']}</td>
                                        <td>{$attribute['updated']}</td>
                                        <td>
                                            <div class='form-group '><label>
                                                <input type='checkbox' attribute_id='{$attribute['attribute_id']}' class='flat-red onbi_import_values' {$checked} >
                                            </label></div>
                                        </td>
                                        <td>
                                            <select class='form-control onbi_attribute_relationship' attribute_id='{$attribute['attribute_id']}' >
                                                <option value='select'>Selecione</option>";
                                                foreach($attrDefault as $attrId => $label ){
                                                    $selected = $attribute['relationship'] == $attrId ? "selected" : "" ;
                                                    echo "<option value='{$attrId}' {$selected}>{$label}</option>";
                                                }
                                           echo " </select>
                                        </td>
                                        <td align='center' valign='center'>
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
			<div class="overlay attributes-onbi" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
		</div>
	</div>
</div>