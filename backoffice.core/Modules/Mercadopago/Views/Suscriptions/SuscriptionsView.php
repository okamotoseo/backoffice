<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $suscriptionsModel->form_msg)){ echo $suscriptionsModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="" name="suscriptions-form">
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
							<label for="public_key">public_key:</label> 
							<input type="text" name="public_key" id="public_key" class="form-control"  value="<?php echo $suscriptionsModel->public_key; ?>" />
						</div>
					</div>
					<div class="col-md-6">		
						<div class="form-group">
							<label for="token">Token:</label> 
							<input type="text" name="token" id="token" class="form-control" value="<?php echo $suscriptionsModel->token; ?>" />
						</div>
					</div>
					<div class="col-md-6">		
						<div class="form-group">
							<label for="created">Criado:</label> 
							<input type="text" name="seller_id" id="created" class="form-control" value="<?php echo $suscriptionsModel->created; ?>"  disabled />
						</div>
					</div>
					<div class="col-md-6">		
						<div class="form-group">
							<label for="updated">Atualizado:</label> 
							<input type="text" name="updated" id="updated" class="form-control" value="<?php echo $suscriptionsModel->updated; ?>" disabled />
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-primary pull-right" id="save" name="save">Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>