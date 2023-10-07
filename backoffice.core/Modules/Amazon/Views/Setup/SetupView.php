<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $setupModel->form_msg)){ echo $setupModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="" name="form-setup-mws">
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
							<label for="seller_id">ID do vendedor:</label> 
							<input type="text" name="seller_id" id="seller_id" class="form-control seller_id" value="<?php echo $setupModel->seller_id; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="site_id">ID do site:</label> 
							<input type="text" name="site_id" id="site_id" class="form-control site_id"  value="<?php echo $setupModel->site_id; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="token">Token de autorização do MWS:</label> 
							<input type="text" name="token" id="token" class="form-control token_mws" value="<?php echo $setupModel->token; ?>" />
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
				<button type="submit" class="btn btn-primary btn-xs pull-right" id="save" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>