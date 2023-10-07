<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $setupModel->form_msg)){ echo $setupModel->form_msg;}?>
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
					<div class="col-md-6">		
						<div class="form-group">
							<label for="app_id">App ID:</label> 
							<input type="text" name="app_id" id="app_id" class="form-control app_id" value="<?php echo $setupModel->app_id; ?>" />
						</div>
					</div>
					<div class="col-md-6">		
						<div class="form-group">
							<label for="secret_key">Secret Key:</label> 
							<input type="text" name="secret_key" id="secret_key" class="form-control secret_key"  value="<?php echo $setupModel->secret_key; ?>" />
						</div>
					</div>
					<div class="col-md-6">		
						<div class="form-group">
							<label for="nickname">Apelido:</label> 
							<input type="text" name="nickname" id="nickname" class="form-control nickname" value="<?php echo $setupModel->nickname; ?>" />
						</div>
					</div>
					<div class="col-md-6">		
						<div class="form-group">
							<label for="seller_id">Códido do Vendedor:</label> 
							<input type="text" name="seller_id" id="seller_id" class="form-control seller_id" value="<?php echo $setupModel->seller_id; ?>" />
						</div>
					</div>
					<div class="col-md-6">		
						<div class="form-group">
							<label for="scope">Escopo:</label> 
							<input type="text" name="scope" id="scope" class="form-control scope" value="<?php echo $setupModel->scope; ?>" disabled />
						</div>
					</div>
				</div>
				<div class='row'>
					<div class="col-md-3">		
						<div class="form-group">
							<label for="tax">Taxa:</label> 
							<input type="text" name="tax" id="tax" class="form-control tax" value="<?php echo $setupModel->tax; ?>"  />
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
			<?php 
    				$disabled = isset($setupModel->uri) ? "" : "disabled" ;
    					echo "<a class='btn btn-primary' href='{$setupModel->url}'>Autorizar Integração</a>";
    			?>
				<button type="submit" class="btn btn-info pull-right" id="save" name="save">Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>