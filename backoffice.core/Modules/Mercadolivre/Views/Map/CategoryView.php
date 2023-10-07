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
				            <li role='presentation'><a href='/Modules/Mercadolivre/Map/Category/'><i class="fa fa-repeat"></i> Atualizar</a></li>
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
				            <th># Meli</th>
				            <th>Hierarquia Meli</th>
				            <th>Categorias Meli</th>
				            <th></th>
				        </tr>
			        </thead>
		 		<tbody>
	             <?php 
	             $option = "<option value='select'>Selecione</option>";
	             foreach($listDefaultCategoriesMl as $key => $category){
	                 $option .="<option value='{$category->id}'>{$category->name}</option>";
	                 
	             }
	             $count=0;
	             foreach ($listCategories as $fetch){
// 	                 pre();die;
	
	             		$sqlCategory = "SELECT category_id, path_from_root 
                        FROM ml_category_relationship WHERE store_id = ? AND category LIKE ? LIMIT 1";
	             		$query = $this->db->query($sqlCategory, array($this->userdata['store_id'], trim($fetch['hierarchy']) ));
						$categoryId = $query->fetch(PDO::FETCH_ASSOC);
						
						
						$styleParent = '';
						$iconAngle = "&nbsp;&nbsp;&nbsp;<i class='fa fa-angle-right'></i>";
						if($fetch['parent_id'] == 0){
							$styleParent = "style='background-color:#e4e4e4'" ;
						
							$iconAngle = "<i class='fa fa-angle-double-right'></i>";
						}
						
						
						
						echo "<tr {$styleParent} >
							<td>{$iconAngle} {$fetch['hierarchy']}</td>
							<td><a href='https://api.mercadolibre.com/categories/{$categoryId['category_id']}' target='_blank'>{$categoryId['category_id']}</a></td>
							<td name='{$count}' ind='{$count}' style='min-width:400px;'>{$categoryId['path_from_root']}</td>
							<td>
								<select class='category form-control' category='{$fetch['hierarchy']}' id='{$count}' style='min-width:200px;'>
									{$option}
								</select>
							</td>
                            <td><a class='fa fa-file-o' href='".HOME_URI."/Modules/Mercadolivre/Map/Attributes/Category/{$categoryId['category_id']}'></a></td>
						</tr>";
						$count++;
	             } ?>
		 		</tbody>
				</table>
			</div>
		</div>
	</div>
</div>