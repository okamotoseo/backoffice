<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<div class="col-sm-12">
		<div class="message">
			<?php if(!empty( $categoryModel->form_msg)){ echo $categoryModel->form_msg;}?>
		</div>
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class="box-title"><?php echo $this->title; ?></h3>
		       	<div class="row">
					<div class="col-sm-6"></div>
					<div class="col-sm-6"></div>
				</div>
			</div><!-- /.box-header -->
			
			<div class="box-body pad table-responsive">
                  <table class="table table-bordered table-hover">
			        <thead>
				        <tr>  
				            <th>Categoria</th>
				            <th  style='text-align:center'>Amazon</th>
				            <th>Amazon</th>
				            <th>Attributos</th>
				        </tr>
			        </thead>
		 		<tbody>
	             <?php 
	             $xsds = array();
	             $option = "<option value='select'>Selecione</option>";
	             foreach($listDefaultCategoriesAz as $key => $category){
	                 $option .="<option value='{$category['name']}' xsd='{$category['name']}' choice='{$category['label']}'  hierarchy='{$category['label']}'>{$category['label']}</option>";
	                 $xsds[$category['name']] = $category['xsd'];
	             }
	             $count=0;
	             foreach ($listCategories as $fetch){
	             	$sqlCategory = "SELECT * FROM az_category_relationship WHERE store_id = ? AND category LIKE ? LIMIT 1";
	             	$query = $this->db->query($sqlCategory, array($this->userdata['store_id'], trim($fetch['hierarchy']) ));
	             	$relationshipId = $query->fetch(PDO::FETCH_ASSOC);
	             	$styleBtn = "style='display:none'";
	             	$styleSelect = "style='display:inline; width:180px;'";
	             	if(isset($relationshipId['id'])){
	             		$styleBtn = "style='display:inline'";
	             		$styleSelect = "style='display:none;width:180px;'";
	             		
	             	}
	             	$linkXsd = $linkAttr = '';
	             	if(!empty($relationshipId['choice'])){
	             		$linkAttr = "<a class='fa  fa-list-alt' href='".HOME_URI."/Modules/Amazon/Map/Attributes/Xsd/{$relationshipId['xsd']}/{$relationshipId['choice']}/Category/{$fetch['id']}' title='Relacionar Atributos' ></a>&nbsp;&nbsp;";
	             		
	             		$linkXsd = "<a class='fa  fa-info-circle' href='{$xsds[$relationshipId['xsd']]}' target='_blank' title='XSD' ></a>";
	             	}
					echo "<tr>
						<td width='40%'>{$fetch['hierarchy']}</td>
						<td width='15%' style='text-align:center'>";
					
						echo  "<a class='btn btn-default btn-xs {$fetch['id']}' onclick=\"removeCategeryRelationship({$relationshipId['id']}, {$fetch['id']})\"  {$styleBtn}><i class='fa fa-undo' ></i> Refazer relacionamento</a>";
						echo "<select class='category input-sm' category='{$fetch['hierarchy']}' id='{$fetch['id']}' {$styleSelect}>
							{$option}
						</select>";
							
						echo "</td>
						<td name='{$count}' ind='{$count}' width='50%'>{$relationshipId['path_from_root']}</td>
						<td width='5%' style='text-align:center'>
							<span class='linkAttr'>{$linkAttr}</span>
							<span class='linkXsd'>{$linkXsd}</span>
						</td> 
					</tr>";
					$count++;
	             } ?>
		 		</tbody>
				</table>
			</div>
		</div>
	</div>
</div>