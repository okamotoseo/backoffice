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
				            <li role='presentation'><a href='/Modules/Shopee/Products/Categories/'><i class="fa fa-repeat"></i> Atualizar</a></li>
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
                    		<th>Categorias Raiz</th>
                    		<th>Categorias Final</th>
                    	</tr>
                    	</thead>
                    	<tbody>
                            <?php 
                            if(isset($categoriesRoot)){
                               
                            	foreach($listCategories as $key => $category){
                            	 
                            		$styleParent = '';
                            		$childOption = '';
                            		
                            		$iconAngle = "&nbsp;&nbsp;&nbsp;<i class='fa fa-angle-right'></i>";
                            		if($category['parent_id'] == 0){
                            			$styleParent = "style='background-color:#e4e4e4'" ;
                            		
                            			$iconAngle = "<i class='fa fa-angle-double-right'></i>";
                            		}
                            		
                                    echo "<tr {$styleParent}  id='{$category['id']}'>
                                            <td>{$iconAngle} {$category['hierarchy']}</td>
                                            <td>{$category['type']}</td>
                                            <td>						
                                                <select class='form-control shopee_root_categories'   id='{$category['id']}'>
                                                <option value='select' >Selecione</option>";
                                        		foreach($categoriesRoot as $key => $categoryShopee){
                                        		    $selected = '';
                                        		    if(isset($categoriesRelationshipShopee[$category['id']])){
                                        		        
                                        		        if($categoriesRelationshipShopee[$category['id']]['shopee_root'] == $categoryShopee['root']){
                                        		            $selected = 'selected';
                                        		            $childOption = "<option value='{$categoriesRelationshipShopee[$category['id']]['id_category']}' selected>{$categoriesRelationshipShopee[$category['id']]['shopee_hierarchy']}</option>";
                                        		        }

                                        		    }
                                        		    echo "<option value='{$categoryShopee['root']}' {$selected}>{$categoryShopee['root']}</option>";
                                                }
                                                echo "</select>
                                            </td>
                                            <td>
                                                <select class='form-control select2 categories_child' id='child-{$category['id']}' category_id='{$category['id']}'>{$childOption}</select>
                                            </td>";
//                                     echo "<td align='right'>
//                                             <div class='dropdown'>
//             		                              <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
//             		                              <ul class='dropdown-menu pull-right' >
//             		                                  <li role='presentation'>
//                                                         <a class='update_attributes_categories' role='menuitem' tabindex='-1' id='update_attributes_categories' category_id='{$category['id']}' >
//                                                             <i class='fa fa-refresh'></i>Atualizar Características
//                                                         </a>
//                                                     </li>
//                                                 </ul>
//                                             </div>
//                                         </td>
//                                  </tr>";
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