<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class='col-sm-8'>
		<div class="message"><?php if(!empty( $documentationsModel->form_msg)){ echo  $documentationsModel->form_msg;}?></div>
		<div class='box box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'>Cadastrar Documento</h3>
	        	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='<?php echo HOME_URI ?>/Admin/Documentations' class='btn btn-block btn-default btn-xs'><i class='fa fa-refresh'></i> Limpar</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<form method="POST" action="<?php echo HOME_URI ?>/Admin/Documentations/" name="form-documentations">
			<input type="hidden" name="id" value="<?php echo $documentationsModel->id; ?>" />
			<div class='box-body'>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
                            <label for='module'>Módulos: </label>
                            <select class="form-control select2  select-hidden-accessible input-sm" name='module'>
                            <?php 
                                foreach($modules as $key => $module){
                                    $selected = $module['name'] == $documentationsModel->module ? "selected" : "";
                                    echo "<option value='{$module['name']}' {$selected}>{$module['name']}</option>";
                                }
                            ?>
                            </select>
             			</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label>Tipo:</label> 
							<input type="text" name="type" id="title" class="form-control  input-sm"  value="<?php echo $documentationsModel->type; ?>" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
                            <label for='visibility'>Visibilidade: </label>
                            <select class="form-control select2 input-sm" name='visibility'>
                            <?php 
                            $visibility = array("Site" , "Backoffice" ,'Todos' , 'Nenhum');
                            foreach($visibility as $key){
                                    $selected = $key == $documentationsModel->visibility ? "selected" : "";
                                    echo "<option value='{$key}' {$selected}>{$key}</option>";
                                }
                            ?>
                            </select>
             			</div>
					</div>
					<div class="col-sm-8">
						<div class="form-group">
							<label>Título:</label> 
							<input type="text" name="title" id="title" class="form-control  input-sm"  value="<?php echo $documentationsModel->title; ?>" />
						</div>
					</div>
					
					<div class="col-sm-4">
						<div class="form-group">
                            <label for='status'>Situação: </label>
                            <select class="form-control select2 input-sm" name='status'>
                            <?php 
                            $status = array("Ativo" => 'success', "Desenvolvimento" => 'primary', 'Analise' => 'warning' );
                            foreach($status as $key => $val){
                                    $selected = $key == $documentationsModel->status ? "selected" : "";
                                    echo "<option value='{$key}' {$selected}>{$key}</option>";
                                }
                            ?>
                            </select>
             			</div>
					</div>
			
					
					<div class="col-sm-12">
						<div class="form-group">
							<label>URL Post:</label> 
							<input type="text" name="url_post" id="url_post" class="form-control  input-sm"  value="<?php echo $documentationsModel->url_post; ?>" />
						</div>
					</div>
					
					<div class="col-sm-12">
						<div class="form-group">
							<label>Descrição:</label> 
							<textarea type="text" id="textarea"   rows='5' name="description"  class="form-control textarea" placeholder="Descrição"><?php echo $documentationsModel->description; ?></textarea>
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
	
	
	<div class="col-sm-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Documentos</h3>
		       	<div class="row">
					<div class="col-sm-6"></div>
					<div class="col-sm-6"></div>
				</div>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive">
				<div class="col-sm-12">
				<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
			        <thead>
				        <tr>
				            <th>ID</th>
				            <th>Modulo</th>
				            <th>Tipo</th>
				            <th>Título</th>
				            <th>Descrição</th>
				            <th>Criado</th>
				            <th>Situação</th>
				            <th></th>
				        </tr>
			        </thead>
		 		<tbody>
		 
	             <?php foreach ($list as $fetch): ?>
	             
	             <tr>
	                 <td> <?php echo $fetch['id'] ?> </td>
	                 <td> <?php echo $fetch['module'] ?> </td>
	                 <td> <?php echo $fetch['type'] ?> </td>
	                 <td> <?php 
	                 
	                 echo !empty($fetch['url_post']) ? "<a href='{$fetch['url_post']}' target='_blank' >{$fetch['title']}</a>" : $fetch['title'] ; 
	                 
	                 
	                 ?> 
	                 </td>
	                 <td> <?php echo $fetch['description']; ?> </td>
	                 <td> <?php echo dateBr($fetch['created'], '/'); ?> </td>
	                 <td> <?php echo $fetch['status'] ?> </td>
	                 <td> <?php echo $fetch['visibility'] ?> </td>
	                 <td> 
	                     <div class='dropdown'>
                            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                            <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                <li role='presentation'><a href="<?php echo HOME_URI ?>/Admin/Documentations/edit/<?php echo $fetch['id'] ?>" class='fa fa-pencil-square-o' /> Editar</a></li>
                                <li role='presentation'><a href="<?php echo HOME_URI ?>/Admin/Documentations/del/<?php echo $fetch['id'] ?>" class='fa fa-trash delete' /> Excluir</a> </li>
             
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
</div>