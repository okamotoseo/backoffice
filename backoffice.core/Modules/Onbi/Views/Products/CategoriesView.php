<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
		       	<div class='box-tools pull-right'>
    	        		<button  class='btn  btn-xs btn-default import_categories_ecommerce'><i class='fa fa-plus'></i> Importa Categorias do Ecommerce para Sysplace</button>
	        	</div>
			</div><!-- /.box-header -->
			
			<div class="box-body">
				<div class="col-md-12">
                    <table  class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
                    	<thead>
                    
                    	<tr>

                    		<th>Categorias Ecommerce</th>
                    		<th>Categorias</th>
                    		
                    	</tr>
                    	</thead>
                    	<tbody>
                    
                    
                            <?php 
                            
                            if(isset($categoriesOnbi)){
                                foreach($categoriesOnbi as $key => $categoryOnbi){
                                    echo "<tr>
                                                <td>{$categoryOnbi['hierarchy']}</td>
                                                <td>						
                                    <select class='form-control onbi_categories_relationhsip' onbi_category_id='{$categoryOnbi['onbi_category_id']}'  >
                                    <option value='select' >Selecione</option>";
                                    foreach($listCategories as $key => $category){
                                        $selected = $category['id'] == $categoryOnbi['category_id'] ? 'selected' : '';
                                        echo "<option value='{$category['parent_id']}|{$category['id']}' {$selected} >{$category['hierarchy']}</option>";
                                        
                                    }
                                    echo "</select></td>
                            
                                        </tr>";
                                    
                                }
                            }
                            
                            ?>
                        </tbody>
                    </table>
                    </div>
			</div>
			<div class="overlay categories-onbi" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
		</div>
	</div>
</div>