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
							<label for="input_data">Tipo de Dados de Entrada:</label> 
							<input type="text" name="input_data" id="input_data" class="form-control input_data" value="<?php echo $setupModel->input_data; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="created">Ativação:</label> 
							<input type="text" name="created" id="created" class="form-control created" readonly value="<?php echo $setupModel->created; ?>" />
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