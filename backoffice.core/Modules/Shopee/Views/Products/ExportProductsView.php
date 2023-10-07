<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Shopee/Products/ExportProducts/" name="export-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Exportar Produtos Shopee</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        	
        	        		<button type='submit' name='export_products'  class='btn btn-primary btn-xs submit-load'><i class='fa fa-database'></i> Exportar</button>
        	        	
        	        		<a href='/Modules/Shopee/Products/ExportProducts/' class='btn btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
						
						<?php if(!empty($exportProducts)){?>
    						<div class="col-md-12">
    							<div class="form-group">
    								<label for="export_products">Produtos: </label>
            	        			<a class='btn btn-default btn-xs pull-right' id='copy-products'> <i class='fa fa-copy'></i> Copiar</a>
    								<textarea name="export_products"  readonly id='export_products'  class="form-control" wrap='off' rows="20" ><?php echo $exportProducts; ?></textarea>
    							</div>
    						</div>
    						<?php 
                                }
    						?>
						</div>
					</div>
				</div>
			</form>
		</div>
		
		<div class="overlay shopee-products" style='display:none;'>
        	<i class="fa fa-refresh fa-spin"></i>
    	</div>
		
	</div>
</div>