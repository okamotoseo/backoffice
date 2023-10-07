<?php if ( ! defined('ABSPATH')) exit; ?>

<div class="row">
	<div class="col-md-12">
		<div class="message">
			<?php if(!empty( $colorsModel->form_msg)){ echo $colorsModel->form_msg;}?>
		</div>
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class="box-title"><?php echo $this->title; ?></h3>
		       	<div class="row">
					<div class="col-sm-6"></div>
					<div class="col-sm-6"></div>
				</div>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive">
				<div class="col-md-12">
				<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
			        <thead>
				        <tr>
				            <th>ID</th>
				            <th>Cor</th>
				            <th>Cor Primária</th>
				            <th>Cor Secundária</th>
				        </tr>
			        </thead>
		 		<tbody>
	             <?php 
	             foreach ($listColors as $fetch): ?>
	             
	             <tr>
	                 <td> <?php echo $fetch['id']; ?> </td>
	                 <td> <?php echo $fetch['color']; ?> </td>
	                 <td   valign='center'> 
	                 	<select name='information_1' id='<?php echo $fetch['id']; ?>' color='<?php echo $fetch['color']; ?>' class='ml_color_relationship form-control'>
	                 	<option value='select'>-- Selecione --</option>
    	                 <?php 
    	                 foreach($listAllowedCollors as $key => $value){
//     	                     if($value['attribute_id'] == '33000'){
        	                     $selected="";
        	                     foreach($listColorsRelationship as $k => $relationship){
        	                         if($relationship['color'] == $fetch['color']){
        	                           $selected = $relationship['information_1'] == $value['value'] ? "selected" : "" ;
        	                         }
        	                     }
        	                     echo "<option value='{$value['value']}' {$selected}>{$value['value']}</option>";
//     	                     }
    	                 }
    	                 ?>
    	                 </select>
					</td>
					
					<td   valign='center'> 
	                 	<select name='information_2' id='<?php echo $fetch['id']; ?>'  color='<?php echo $fetch['color']; ?>'  class='ml_color_relationship_2 form-control'>
	                 	<option value='select'>-- Selecione --</option>
    	                 <?php 
    	                 foreach($listAllowedCollors as $key => $value){
//     	                     if($value['attribute_id'] == '43000'){
        	                     $selected="";
        	                     foreach($listColorsRelationship as $k => $relationship){
        	                         if($relationship['color'] == $fetch['color']){
        	                           $selected = $relationship['information_2'] == $value['value'] ? "selected" : "" ;
        	                         }
        	                     }
        	                     echo "<option value='{$value['value']}' {$selected}>{$value['value']}</option>";
//         	                  }
    	                 }
    	                 
    	                 ?>
    	                 </select>
					</td>
	             </tr>
	             <?php endforeach;?>
		 		</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>