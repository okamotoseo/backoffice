<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
		       	<div class='box-tools pull-right'>
    	        		<button class='btn btn-xs btn-default import_attribute_set_onbi'><i class='fa fa-plus'></i> Importar Conjuntos de Atributos</button>
    	        		<button class='btn btn-xs btn-default export_attribute_onbi'><i class='fa fa-plus'></i> Exportar Atributos para Ecommerce</button>
	        	</div>
			</div><!-- /.box-header -->
			
			<div class="box-body">
				<div class="col-md-12">
                    <table  class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
                    	<thead>
                    
                    	<tr>
							<th>Conjuntos </th>
                    		<th>Conjunto de Attribuos Ecommerce</th>
                    		<th>Etiqueta da Variação</th>
                    	</tr>
                    	</thead>
                    	<tbody>
                    
                    
                            <?php
                            
                            if(isset($setAttributesOnbi)){
                                
                                foreach($setAttributes as $key => $setAttribute){
                                    $variation = $tamanho = $voltagem = '';
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
                                    }
                                    
                                    $exist = false;
                                    echo "<tr>
                                                <td>{$setAttribute['set_attribute']}</td>
                                                <td>
                                    <select class='form-control set_attr_relationship_ecommerce' set_attribute_id='{$setAttribute['id']}'  >
                                    <option value='select'> >> Selecione</option>";
                                    foreach($setAttributesOnbi as $key => $setAttributeOnbi){
                                        $selected = '';
                                        if($setAttribute['onbi_attribute_set_id'] == $setAttributeOnbi->set_id){
                                            $selected =  'selected';
                                            $exist = true;
                                        }
                                        echo "<option value='{$setAttributeOnbi->set_id}' {$selected} >{$setAttributeOnbi->name}</option>";
                                    }
                                    if(!$exist){
                                        echo "<option value='export_set_attr_ecommerce' >Exportar ecommerce</option>";
                                    }
                                    echo "</select></td>
                                    <td>";
                                    $display = $exist ? "" : "disabled";
                                        echo "<select id='{$setAttribute['id']}' {$display} class='form-control set_variation_label_relationship_ecommerce' set_attribute_id='{$setAttribute['id']}'  >
                                            <option value='select'> >> Selecione</option>
                                            <option value='Variação' {$variation}>Variação</option>
                                            <option value='Voltagem' {$voltagem}>Voltagem</option>
                                            <option value='Tamanho' {$tamanho}>Tamanho</option>
                                        </select>";
                                    echo "</td>
                                                    
                                        </tr>";
                                    
                                }
                            }
                            
                            ?>
                        </tbody>
                    </table>
                    </div>
			</div>
            <div class="overlay attributes-set-relationship" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
		</div>
	</div>
</div>