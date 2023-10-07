<?php if ( ! defined('ABSPATH')) exit; ?>


<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $setupModel->form_msg)){ echo $setupModel->form_msg;}
			
			?>
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="" name="setup-form">
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
							<label for="store">Loja:</label> 
							<input type="text" name="store" id="store" class="form-control store" value="<?php echo $setupModel->store; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="secret_key">Api Host</label> 
							<input type="text" name="api_host" id="api_host" class="form-control api_host"  value="<?php echo $setupModel->api_host; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="date_expiration_access_token">Expira:</label> 
							<input type="text" name="date_expiration_access_token" id="date_expiration_access_token" class="form-control date_expiration_access_token" value="<?php echo $setupModel->date_expiration_access_token; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="date_activated">Ativação:</label> 
							<input type="text" name="date_activated" id="date_activated" class="form-control date_activated" value="<?php echo $setupModel->date_activated; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="code">Code:</label> 
							<input type="text" name="code" id="code" class="form-control code" value="<?php echo $setupModel->code; ?>" disabled />
						</div>
					</div>
				</div>
				<div class='row'>
					<div class="col-xs-3">		
						<div class="form-group">
							<label for="tax">Taxa:</label> 
							<input type="text" name="tax" id="tax" class="form-control tax" value="<?php echo $setupModel->tax; ?>"  />
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
			
			<div class="box-footer">
				<?php 
    					echo "<a class='btn btn-primary' href='{$setupModel->url}'>Autorizar Integração</a>";
    			?>
				<button type="submit" class="btn btn-info pull-right" id="save" name="save">Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>