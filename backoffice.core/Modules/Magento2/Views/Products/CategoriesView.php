<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
		       	<div class='box-tools pull-right'>
    	        		<button  class='btn  btn-xs btn-default import_categories_ecommerce'><i class='fa fa-refresh'></i> Atualizar Categorias Magento</button>
	        	</div>
			</div><!-- /.box-header -->
			
			<div class="box-body no-padding">
				<table  class="table table-condensed">
				<thead>
                    	<tr>
                    		<th>Categorias Sysplace</th>
                    		<th>Origem</th>
                    		<th>Categorias Magento</th>
                    	</tr>
                    	</thead>
                    	<tbody>
                            <?php 
                            if(isset($categoriesMg2)){
//                             	pre($listCategories);
//                             	pre($categoriesMg2);die;
                            	foreach($listCategories as $key => $category){
                            		$styleParent = ''; 
                            		$iconAngle = "&nbsp;&nbsp;&nbsp;<i class='fa fa-angle-right'></i>";
                            		if($category['parent_id'] == 0){
                            			$styleParent = "style='background-color:#e4e4e4'" ;
                            		
                            			$iconAngle = "<i class='fa fa-angle-double-right'></i>";
                            		}
                            		
                                    echo "<tr {$styleParent} id='{$category['id']}'>
                                                <td>{$iconAngle} {$category['hierarchy']}</td>
                                                <td>{$category['type']}</td>
                                                <td>						
                                    <select class='form-control mg2_categories_relationhsip' category_id='{$category['parent_id']}|{$category['id']}'  >
                                    <option value='select' >Selecione</option>";
                                    foreach($categoriesMg2 as $key => $categoryMg2){
                                    	$selected = '';
                                    	if(!empty($categoryMg2['category_id'])){
	                                        if($category['id'] == $categoryMg2['category_id'] && $category['parent_id'] == $categoryMg2['parent_id']){
	                                        	$selected = 'selected';
	                                        	
	                                        }
                                    	}
                                        echo "<option value='{$categoryMg2['mg2_parent_id']}|{$categoryMg2['mg2_category_id']}' {$selected} >{$categoryMg2['mg2_hierarchy']}</option>";
                                    }
                                    echo "</select></td>
                                        </tr>";
                                }
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
</div>