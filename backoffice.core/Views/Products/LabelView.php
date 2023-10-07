<?php 

$path = dirname(__FILE__);
// echo $path .'/../../library/BarcodeD/src/BarcodeGenerator.php';

include($path .'/../../library/BarcodeD/src/BarcodeGenerator.php');

include($path .'/../../library/BarcodeD/src/BarcodeGeneratorPNG.php');

$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();

?>
<div class="row">
		<div class="box box-primary">
			
			<div class="box-body">
			
				<style type="text/css">
				 @media print{
					#report, #headerreport{display: block !important;}
					.main-sidebar, .left-side, .sidebar,
					.navbar, .filter-report, 
					.date, .noprint, 
					.datepicker, 
					.dropdown-menu, .footer, .logo, .top-actions, .credit, .alert
					#btn-print, #form-order, #footer, .alert-warning, .breadcrumb, 
					.main-footer, #myModal, .printed, .view, .order {display: none;}
					.content{padding:0px !important;}
					
				}
				</style>
				<div class='row'>
				<div class="col-md-12">
                  <div class="box box-widget widget-user-2">
                    <div class="widget-user-header">
                      <div class="widget-user-image">
                        <img class="img-rounded" src="<?php  echo $availableProductModel->thumbnail; ?>" alt="Foto do Produto">
                      </div>
                      <h3 class="widget-user-username"><?php  echo $availableProductModel->title; ?></h3>
                    </div>
                    <div class="box-footer no-padding">
                      <ul class="nav nav-stacked">
                      	<li><a href="#">Variação <span class="pull-right"><?php  echo $availableProductModel->variation; ?></span></a></li>      
                        <li><a href="#">Cor <span class="pull-right"><?php  echo $availableProductModel->color; ?></span></a></li>
                        <li><a href="#">Referência <span class="pull-right"><?php  echo $availableProductModel->reference; ?></span></a></li>
                        <li><a href="#">Marca <span class="pull-right"><?php  echo $availableProductModel->brand; ?></span></a></li>
                        <li><a href="#">SKU <span class="pull-right"><?php  echo $availableProductModel->sku; ?></span></a></li>
                        <li><a href="#">EAN <span class="pull-right"><?php  echo $availableProductModel->ean; ?></span></a></li>
                        <li><a href="#">Barras <span class="pull-right"><?php echo '<img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode($availableProductModel->ean, $generatorPNG::TYPE_CODE_128, 1, 20)) . '"> '; ?></span></a></li>
                      </ul>
                    </div>
                  </div><!-- /.widget-user -->
                </div>
				
				</div> 
			</div>
		</div>
</div>