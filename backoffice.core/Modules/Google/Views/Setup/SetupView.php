<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $setupModel->form_msg)){ echo $setupModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
			<form role="form" method="POST" action="" name="form-google">
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
							<input type="text" name="url_xml" id="url_xml" class="form-control" value="<?php echo $setupModel->url_xml; ?>" />
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
                            	<input type='checkbox' name='import_images_xml' class='flat-red import_images' value='<?php echo $setupModel->import_images; ?>' <?php echo $setupModel->import_images ? "checked" : ""; ?> >
                            	Importa Imagens &nbsp;&nbsp;
                            	<a class="btn btn-primary btn-xs pull-right" id="import_images" name="import_images"><i class='fa fa-arrow-down'></i> Importar</a>
                            
                            </label>
                        </div>
                    	<div class='form-group '>
							<label>
                            	<input type='checkbox' name='export_products_xml'class='flat-red export_products_xml'  value='<?php echo $setupModel->export_products_xml; ?>' <?php echo $setupModel->export_products_xml ? "checked" : ""; ?> >
                            	Export Produtos XML
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