<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(isset( $products['total']) && !empty( $products['total'])){ echo  $products['total'];}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Shopee/Products/UpdateProducts/" name="update-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Atualizar Produtos Shopee</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Shopee/Products/UpdateProducts/' class='btn btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
						<div class="col-md-2">
							<div class="form-group">
								<label for="skus">SKUs: </label>
        	        			<button type='submit' name='update-shopee-products' class='btn btn-success btn-xs pull-right'> Importar</button>
								<textarea  name="skus"  id='skus'  class="form-control" rows="50" cols='5' ><?php echo $productsModel->skus; ?></textarea>
							</div>
						</div>
						</form>
    					<?php
    					$quantity = "";
    					
    					$salePrice = "";
//     					pre($products);die;
    					if(isset($products)){
    					    
    					    
    					    foreach($products as $k => $val){
//     					        pre($val);
        					        $quantity .= $val['quantity'].PHP_EOL;
        					        
        					        $salePrice .= $val['sale_price'].PHP_EOL;
    					    }
    					    ?>
						
							
						<div class="col-md-2">
							<div class="form-group">
								<label for="quantity">Qtd.:</label> 
        	        			<a class='btn btn-default btn-xs pull-right' id='copy-quantity'> <i class='fa fa-copy'></i> Copiar</a>
								<textarea  name="quantity"  id='quantity'  readonly class="form-control" rows="50" cols='5' ><?php echo rtrim($quantity); ?></textarea>
							</div>
						</div>
						
						<div class="col-md-2">
							<div class="form-group">
								<label for="sale_price">Preço:</label>
								<a class='btn btn-default btn-xs pull-right' id='copy-sale_price'> <i class='fa fa-copy'></i> Copiar</a>
								<textarea  name="sale_price"  id='sale_price'  readonly class="form-control" rows="50" cols='5' ><?php echo rtrim($salePrice); ?></textarea>
							</div>
						</div>
					<?php 
					}
					?>
					</div>
				</div>
				</div>
		</div>
	</div>
</div>