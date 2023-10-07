<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $setupModel->form_msg)){ echo $setupModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="" name="form-store">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="host">Host:</label> 
							<input type="text" name="host" id="host" class="form-control host" value="<?php echo $setupModel->host; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="port">Porta:</label> 
							<input type="text" name="port" id="port" class="form-control port"  value="<?php echo $setupModel->port; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="dbname">Nome do banco de dados:</label> 
							<input type="text" name="dbname" id="dbname" class="form-control dbname" value="<?php echo $setupModel->dbname; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="user">Usuário:</label> 
							<input type="text" name="user" id="user" class="form-control user" value="<?php echo $setupModel->user; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="password">Senha:</label> 
							<input type="text" name="password" id="password" class="form-control password" value="<?php echo $setupModel->password; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="description">Descrição:</label> 
							<input type="text" name="description" id="description" class="form-control description" value="<?php echo $setupModel->description; ?>" />
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right" id="save" name="save">Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>