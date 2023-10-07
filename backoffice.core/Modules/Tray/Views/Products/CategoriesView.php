<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
		       	<div class='box-tools pull-right'>
		       	<div class='dropdown'>
		            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
		            <ul class='dropdown-menu pull-right' >
				            <li role='presentation'><a href='/Modules/Tray/Products/Categories/'><i class="fa fa-repeat"></i> Atualizar</a></li>
				            <li role='presentation'><a class='export_categories_tray'><i class='fa fa-upload'></i> Exportar Categorias Padrão para Tray</a></li>
				            <li role='presentation'><a class='update_attributes_categories_tray'><i class='fa fa-refresh'></i> Atualizar Características da Categorias Tray</a></li>
		                    <li role='presentation'><a class='import_categories_tray'><i class='fa fa-download'></i> Importa Categorias Tray</a></li>
		                    <li role='presentation'><a class='remove_categories_tray'><i class='fa fa-trash'></i> Excluir Mapeamentos</a></li>
	                    </ul>
                    </div>
    	        		
	        	</div>
			</div><!-- /.box-header -->
			<div class="box-body">
			<div class='message'></div>
				<table  class="table table-condensed">
                    	<thead>
                    	<tr>
                    		<th>Categorias Sysplace</th>
                    		<th>Origem</th>
                    		<th>Categorias Tray</th>
                    		<th></th>
                    	</tr>
                    	</thead>
                    	<tbody>
                            <?php 
                            if(isset($categoriesTray)){
//                             	pre($listCategories);
//                             	pre($categoriesMg2);die;
                            	foreach($listCategories as $key => $category){
                            		$styleParent = '';
                            		$iconAngle = "&nbsp;&nbsp;&nbsp;<i class='fa fa-angle-right'></i>";
                            		if($category['parent_id'] == 0){
                            			$styleParent = "style='background-color:#e4e4e4'" ;
                            		
                            			$iconAngle = "<i class='fa fa-angle-double-right'></i>";
                            		}
                            		
                                    echo "<tr {$styleParent}  id='{$category['id']}'>
                                                <td>{$iconAngle} {$category['hierarchy']}</td>
                                                <td>{$category['type']}</td>
                                                <td>						
                                    <select class='form-control tray_categories_relationhsip' category_id='{$category['parent_id']}|{$category['id']}'  >
                                    <option value='select' >Selecione</option>";
                                    foreach($categoriesTray as $key => $categoryTray){
                                    	$selected = '';
                                    	if(!empty($categoryTray['category_id'])){
	                                        if($category['id'] == $categoryTray['category_id'] && $category['parent_id'] == $categoryTray['parent_id']){
	                                        	$selected = 'selected';
	                                        	
	                                        }
                                    	}
                                        echo "<option value='{$categoryTray['id_parent']}|{$categoryTray['id_category']}' {$selected} >{$categoryTray['hierarchy']}</option>";
                                    }
                                    echo "</select>
                                        </td>
                                        <td align='right'>
                                            <div class='dropdown'>
            		                              <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
            		                              <ul class='dropdown-menu pull-right' >
            		                                  <li role='presentation'>
                                                        <a class='update_attributes_categories' role='menuitem' tabindex='-1' id='update_attributes_categories' category_id='{$category['id']}' >
                                                            <i class='fa fa-refresh'></i>Atualizar Características
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
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