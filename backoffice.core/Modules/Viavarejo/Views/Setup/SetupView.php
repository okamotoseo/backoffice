<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $setupModel->form_msg)){ echo $setupModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="/Modules/Configuration/Viavarejo/Setup/" name="form-module-setup">
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
							<label for="client_id:">client_id:</label> 
							<input type="text" name="client_id" id="client_id" class="form-control client_id" value="<?php echo $setupModel->client_id; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="client_secret">client_secret:</label> 
							<input type="text" name="client_secret" id="client_secret" class="form-control client_secret" value="<?php echo $setupModel->client_secret; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="token">token:</label> 
							<input type="text" name="token" id="token" class="form-control token" value="<?php echo $setupModel->token; ?>" />
						</div>
					</div>
					
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right" id="save" name="save-module-setup">Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>