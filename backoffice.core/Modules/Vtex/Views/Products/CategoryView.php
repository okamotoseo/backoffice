<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<div class="col-md-12">
		<div class="message">
			<?php if(!empty( $categoryModel->form_msg)){ echo $categoryModel->form_msg;}?>
		</div>
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class="box-title"><?php echo $this->title; ?></h3>
		      	<div class='box-tools pull-right'>
		       		<div class='dropdown'>
    		            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
    		            <ul class='dropdown-menu pull-right' >
				            <li role='presentation'><a href='/Modules/Vtex/Products/Category/'><i class="fa fa-repeat"></i> Atualizar</a></li>
	                    </ul>
                    </div>
    	        		
	        	</div>
	        	
	        	
		       	<div class="row">
					<div class="col-sm-6"></div>
					<div class="col-sm-6"></div>
				</div>
			</div><!-- /.box-header -->
			
			<div class="box-body no-padding">
				<table  class="table table-condensed">
				        <tr>
				            <th>Categoria</th>
				            <th>Categorias Vtex</th>
				        </tr>
			        </thead>
		 		<tbody>
	            <?php 
	             
	             $count = 0;
	             
	             foreach ($listCategories as $fetch){
	                 
						$categoryId = array();
						
						$styleParent = '';
						
						$iconAngle = "&nbsp;&nbsp;&nbsp;<i class='fa fa-angle-right'></i>";
						
						if($fetch['parent_id'] == 0){
						    
							$styleParent = "style='background-color:#e4e4e4'" ;
						
							$iconAngle = "<i class='fa fa-angle-double-right'></i>";
						}
						
						
						echo "<tr {$styleParent} id='{$fetch['id']}'>
							<td width='40%'>{$iconAngle} {$fetch['hierarchy']}</td>

							<td width='60%'>

								<select class='category select2 form-control' category='{$fetch['hierarchy']}' id='{$count}' category_id='{$fetch['id']}'style='min-width:200px;'>
									 <option value='select'>Selecione</option>";
						
                    	             foreach($listDefaultCategoriesVtex as $key => $category){
                    	                 $selected = '' ;
                    	                 foreach($listCategoryRelationship as $i => $rel){
                    	                     if($fetch['id'] == $rel['category_id'] && $rel['id_category'] == $category['id']){
                    	                         $selected = 'selected' ;
                    	                         break;
                    	                     }
                    	                 }
                    	                 echo "<option value='{$category['id']}' {$selected}>{$category['name']}</option>";
                    	                 
                    	             }


								echo "</select>
							</td>
						</tr>";
								
						$count++;
	             }
	             
	             ?>
		 		</tbody>
				</table>
			</div>
		</div>
	</div>
</div>