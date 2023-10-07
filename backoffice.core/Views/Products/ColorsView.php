<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class='col-md-6'>
		<div class="message"><?php if(!empty( $colorsModel->form_msg)){ echo  $colorsModel->form_msg;}?></div>
		<div class='box box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'>Cadastrar cores</h3>
	        	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='<?php echo HOME_URI ?>/Products/Colors' class='btn btn-block btn-default btn-xs'><i class='fa fa-plus'></i> Novo</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<form method="POST" action="<?php echo HOME_URI ?>/Products/Colors" name="form-colors">
			<input type="hidden" name="id" value="<?php echo $colorsModel->id; ?>" />
			<div class='box-body'>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Cor:</label> 
							<input type="text" name="color" id="color" class="form-control color"  value="<?php echo $colorsModel->color; ?>" />
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label>Descrição:</label> 
							<textarea type="text" rows='10' name="description" id="description" class="form-control description" placeholder="Descrição"><?php echo $colorsModel->description; ?></textarea>
						</div>
					</div>
				</div>
			</div>
			
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary pull-right btn-sm" id="btn" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
		</div>
	</div>
	
	
	<div class="col-md-6">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Listagem de Cores</h3>
		       	<div class="row">
					<div class="col-md-6"></div>
					<div class="col-md-6"></div>
				</div>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive">
				<div class="col-md-12">
				<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
			        <thead>
				        <tr>
				            <th>ID</th>
				            <th>Cor</th>
				            <th>Descrição</th>
				            <th>Edição</th>
				        </tr>
			        </thead>
		 		<tbody>
		 
	             <?php foreach ($list as $fetch): ?>
	             
	             <tr>
	                 <td> <?php echo $fetch['id'] ?> </td>
	                 <td> <?php echo $fetch['color'] ?> </td>
	                 <td> <?php echo substr($fetch['description'], 0, 15) ?> </td>
	                 <td align='right'> 
	                     <a href="<?php echo HOME_URI ?>/Products/Colors/edit/<?php echo $fetch['id'] ?>" class='fa fa-pencil-square-o' />&nbsp;&nbsp;
	                     <a href="<?php echo HOME_URI ?>/Products/Colors/del/<?php echo $fetch['id'] ?>" class='fa fa-trash delete' />
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