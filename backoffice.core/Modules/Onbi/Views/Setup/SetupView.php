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
							<label for="host">URL:</label> 
							<input type="text" name="wsdl" id="wsdl" class="form-control" value="<?php echo $setupModel->wsdl; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="port">Session_id:</label> 
							<input type="text" name="session_id" id="session_id" class="form-control"  value="<?php echo $setupModel->session_id; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="user">Usuário:</label> 
							<input type="text" name="user" id="user" class="form-control" value="<?php echo $setupModel->user; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="password">Senha:</label> 
							<input type="text" name="password" id="password" class="form-control" value="<?php echo $setupModel->password; ?>" />
						</div>
					</div>
					<div class="col-xs-6">
						<div class='form-group '>
							<label>
                            	<input type='checkbox' name='import_products' class='flat-red onbi_import_products' value='<?php echo $setupModel->import_products; ?>' <?php echo $setupModel->import_products ? "checked" : ""; ?> >
                            	Importa Produtos
                            </label>
                        </div>
                        
                        <div class='form-group '>
							<label>
                            	<input type='checkbox' name='import_categories' class='flat-red onbi_import_categories' value='<?php echo $setupModel->import_categories; ?>' <?php echo $setupModel->import_categories ? "checked" : ""; ?> >
                            	Importa Categorias
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-6">
                    	<div class='form-group '>
							<label>
                            	<input type='checkbox' name='export_products'class='flat-red onbi_export_products'  value='<?php echo $setupModel->export_products; ?>' <?php echo $setupModel->export_products ? "checked" : ""; ?> >
                            	Export Produtos
                            </label>
                        </div>
                    </div>

				</div>
			</div><!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-primary btn-sm pull-right" id="save" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>