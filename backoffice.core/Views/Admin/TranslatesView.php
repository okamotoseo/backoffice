<?php if ( ! defined('ABSPATH')) exit;


?>
<div class='row'>
	<div class='col-md-6'>
		<div class="message"><?php if(!empty($translatesModel->form_msg)){ echo  $translatesModel->form_msg;}?></div>
		<div class='box box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'><?php echo $this->title; ?></h3>
	        	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='<?php echo HOME_URI ?>/Admin/Translates/' class='btn btn-block btn-default btn-xs'><i class='fa fa-plus'></i> Novo</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<form method="POST" action="<?php echo HOME_URI ?>/Admin/Translates/" name="form-colors">
			<input type="hidden" name="id" value="<?php echo $translatesModel->id; ?>" />
			<div class='box-body'>
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group">
							<label>Palavra:</label> 
							<input type="text" name="word" class="form-control"  value="<?php echo $translatesModel->word; ?>" />
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label>Tradução:</label> 
							<input type="text" name="translate" class="form-control"  value="<?php echo $translatesModel->translate; ?>" />
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label>StoreId:</label> 
							<input type="text" name="store_id"  class="form-control"  value="<?php echo $translatesModel->store_id; ?>" />
						</div>
					</div>

					<div class="col-xs-12">
						<div class="form-group">
							<label>Descrição:</label> 
							<textarea type="text" rows='5' name="description" class="form-control"><?php echo $translatesModel->description; ?></textarea>
						</div>
					</div>
				</div>
			</div>
			
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary pull-right btn-sm" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
		</div>
	</div>
	
</div>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Listagem de Cores</h3>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive no-padding">
				<table class="table table-bordered">
			        <thead>
				        <tr>
				        	<th>Grupo</th>
				            <th>Palavra</th>
				            <th>Tradução</th>
				            <th>Alias</th>
				            <th>Required</th>
				            <th>Descrição</th>
				            <th>Exemplo</th>
				            <th></th>
				        </tr>
			        </thead>
		 		<tbody>
	             <?php foreach ($list as $fetch): ?>
	             <tr>
	              	 <td> <?php echo $fetch['attribute_group'] ?> </td>
	                 <td> <?php echo $fetch['word'] ?> </td>
	                 <td> <?php echo $fetch['translate'] ?> </td>
	                 <td> <?php echo $fetch['alias'] ?> </td>
	                 <td> <?php echo $fetch['required'] ?> </td>
	                 <td> <?php echo $fetch['description']; ?> </td>
	                 <td> <?php echo $fetch['exemple']; ?> </td>
					<td align='center'>
                    <div class='dropdown'>
                       <a class='btn dropdown-toggle' data-toggle='dropdown' ariel-expanded='true'><span class='fa fa-ellipsis-v'></span></a>
                       	<ul class='dropdown-menu pull-right'>
                       		<li role='presentation'><a href="<?php echo HOME_URI ?>/Admin/Translates/edit/<?php echo $fetch['id'] ?>"><i  class='fa fa-pencil-square-o' ></i>Editar</a></li>
                       		<li role='presentation'><a href="<?php echo HOME_URI ?>/Admin/Translates/del/<?php echo $fetch['id'] ?>" ><i class='fa fa-trash delete' ></i>Excluir</a></li>
                    	</ul>
                 	</div>
                	</td>	                 
	            
	            </tr>
	             
	            <?php endforeach;?>
		 
		 		</tbody>
				</table>
			</div>
		</div>
	</div>
</div>