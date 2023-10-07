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
                    		<th>Categorias do XMl</th>
                    		<th>Origem</th>
                    		<th>Categorias Raiz</th>
                    		<th>Categorias Final</th>
                    	</tr>
                    	</thead>
                    	<tbody>
                            <?php 
                            if(isset($categoriesRoot)){
                                sort($listCategoriesXml, false);
                                $i = 20000;
                            	foreach($listCategoriesXml as $key => $category){
                            	    $i++;
                            	    
                            	    $category['id'] = isset($category['id']) && !empty($category['id']) ? $category['id'] : $i ;
                            		$styleParent = '';
                            		$childOption = '';
                            		$catParts = explode('>',$category['hierarchy']);
                            		$numCat = count($catParts);
                            		$iconAngle = "&nbsp;&nbsp;&nbsp;<i class='fa fa-angle-right'></i>";
                            		if($numCat == 1){
                            			$styleParent = "style='background-color:#e4e4e4'" ;
                            			$iconAngle = "<i class='fa fa-angle-double-right'></i>";
                            		}
                            		
                                    echo "<tr {$styleParent}  id='{$category['id']}'>
                                            <td width='30%'>{$iconAngle} {$category['hierarchy']}</td>
                                            <td width='10%' >GoogleXml</td>
                                            <td width='20%'>						
                                                <select class='form-control shopee_root_categories' id='{$category['id']}' >
                                                <option value='select' >Selecione</option>";
                                        		foreach($categoriesRoot as $key => $categoryShopee){
                                        		    $selected = '';
                                        		    if(isset($categoriesRelationshipShopee[$category['hierarchy']])){
                                        		        
                                        		        if($categoriesRelationshipShopee[$category['hierarchy']]['shopee_root'] == $categoryShopee['root']){
                                        		            $selected = 'selected';
                                        		            $childOption = "<option value='{$categoriesRelationshipShopee[$category['hierarchy']]['id_category']}' selected>{$categoriesRelationshipShopee[$category['hierarchy']]['shopee_hierarchy']}</option>";
                                        		        }

                                        		    }
                                        		    echo "<option value='{$categoryShopee['root']}' {$selected}>{$categoryShopee['root']}</option>";
                                                }
                                                echo "</select>
                                            </td>
                                            <td width='40%'>
                                                <select class='form-control select2 categories_xml_child' id='child-{$category['id']}' category_id='{$category['id']}'  hierarchy='{$category['hierarchy']}'  style='width:100%'>{$childOption}</select>
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