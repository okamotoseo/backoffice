<?php 
if ( ! defined('ABSPATH')) exit;

$tabs = array(
    "general-configuration",
	'products-configuration',
	'checkout-configuration',
	'correios-configuration'
);

foreach($tabs as $ind){
    
    if(isset($_REQUEST[$ind])){
        $tabs[$ind] = "active";
    }else{
        $tabs[$ind] = "";
    }
}
if(!in_array("active", $tabs)){
    $tabs['general-configuration'] = "active";
    $tabs['products-configuration'] = "";
    $tabs['checkout-configuration'] = "";
}

?>
<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            
            <ul class="nav nav-tabs">
            	<li class="<?php echo $tabs['general-configuration']; ?>"><a href="#tab_1" data-toggle='tab'>Geral</a></li>
            	<li class="<?php echo $tabs['products-configuration']; ?>"><a href="#tab_2" data-toggle='tab'>Produtos</a></li>
            	<li class="<?php echo $tabs['checkout-configuration']; ?>"><a href="#tab_3" data-toggle='tab'>Checkout</a></li>
            	<li class="<?php echo $tabs['correios-configuration']; ?>"><a href="#tab_4" data-toggle='tab'>Correios</a></li>
            </ul>
            
            <div class="tab-content">
            
                <div class="tab-pane <?php echo $tabs['general-configuration']; ?>" id="tab_1">
                
                </div>
                
                <div class="tab-pane <?php echo $tabs['products-configuration']; ?>" id="tab_2">
                	<div class="message"><?php if(!empty( $configurationModel->form_msg)){ echo  $configurationModel->form_msg;}?></div>
                    <form method="POST" action="" name="products-configuration" enctype="multipart/form-data" class="form-horizontal" >
                    	<div class='row'>
                        	<div class='col-sm-6'>
                                <div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="col-sm-8 control-label">Alerta de estoque minimo:</label>
											<div class='col-sm-2'>
												<input type="text" name="configurations[Product][alert_stock_min]"  class="form-control input-sm"  value="<?php  echo $configurationsModel->configurations['Product']['alert_stock_min']; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="col-sm-8 control-label">Cross Doking:</label>
											<div class='col-sm-2'>
												<input type="text" name="configurations[Product][cross_docking_default]"  class="form-control input-sm"  value="<?php  echo $configurationsModel->configurations['Product']['cross_docking_default']; ?>">
											</div> 
										</div>
									</div>
								</div>
                            	<div class="box-footer">
                					<button type="submit" class="btn btn-primary btn-sm pull-right" name="products-configuration"><i class='fa fa-check'></i> Salvar</button>
                				</div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="tab-pane <?php echo $tabs['checkout-configuration']; ?>" id="tab_3">
                	<div class="message"><?php if(!empty( $configurationModel->form_msg)){ echo  $configurationModel->form_msg;}?></div>
                    <form method="POST" action="" name="checkout-configuration-form" enctype="multipart/form-data" class="form-horizontal" >
                    	<div class='row'>
                        	<div class='col-sm-6'>
                        		<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
						                	<div class="radio">
						                	
						                	<?php 
						                	$checkedShippingOn = '';
						                	$checkedShippingOff = 'checked';
						                	if( isset($configurationsModel->configurations['Checkout']['shipping']) && $configurationsModel->configurations['Checkout']['shipping'] == 'T'){
						                		$checkedShippingOn = 'checked' ;
						                		$checkedShippingOff = '';
						                	}
						                	
						                	?>
						                       <label class="col-sm-8 control-label"><strong>Sem Cálculo de Frete</strong>
						                        	<input type="radio" name='configurations[Checkout][shipping]' class="col-sm-6" value='F' <?php echo $checkedShippingOff; ?> > 
						                        </label>
						                        <label class="col-sm-8 control-label"><strong>Com Cálculo do Frete com Correios</strong>
						                        	<input type="radio" name='configurations[Checkout][shipping]' class="col-sm-6" value='T' <?php echo $checkedShippingOn; ?> >
						                        </label>
					                      	</div>
			                      		</div>
					               	</div>
					            </div>
					           <div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-5 control-label">Chave PIX:</label> 
											 <div class="col-sm-6">
											<input type="text" name="configurations[Checkout][pix_payment]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Checkout']['pix_payment']; ?>">
											</div>
										</div>
									</div>
								</div>
					             <div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
						                      <div class="radio">
						                        <label class="col-sm-8 control-label"><strong>MercadoPago</strong>
						                        	<input type="radio" name='configurations[Checkout][mercadopago_payment]' class="col-sm-6" value='T' checked>
						                        </label>
					                      	</div>
			                      		</div>
					               	</div>
					            </div>
					       		<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-8 control-label">ID Produto Padrão:</label> 
											 <div class="col-sm-2">
											<input type="text" name="configurations[Checkout][id_product_default]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Checkout']['id_product_default']; ?>">
											</div>
										</div>
									</div>
								</div>
                            	<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-8 control-label">Peso minimo para calculo do frete em Kg:</label> 
											<div class="col-sm-2">
												<input type="text" name="configurations[Checkout][weight_min]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Checkout']['weight_min']; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-8 control-label">Adicional fixo por pedido R$:</label> 
											 <div class="col-sm-2">
											<input type="text" name="configurations[Checkout][flat_rate]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Checkout']['flat_rate']; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-8 control-label">Adicional variável por pedido %:</label> 
											 <div class="col-sm-2">
											<input type="text" name="configurations[Checkout][percentage_rate]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Checkout']['percentage_rate']; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="col-sm-8 control-label">Adicionar mais Dias ao prazo de envio dos correios:</label> 
											 <div class="col-sm-2">
											<input type="text" name="configurations[Checkout][cross_docking]"   class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Checkout']['cross_docking']; ?>">
											</div>
										</div>
									</div>
								</div>
                                <div class="box-footer">
                					<button type="submit" class="btn btn-primary btn-sm pull-right" name="checkout-configuration" ><i class='fa fa-check'></i> Salvar</button>
                				</div>
                            </div>
                        </div>
                    </form>
                </div><!-- /.tab-pane -->
                
                
                
                
                
                
                
                
                
                
                <div class="tab-pane <?php echo $tabs['correios-configuration']; ?>" id="tab_4">
                	<div class="message"><?php if(!empty( $configurationModel->form_msg)){ echo  $configurationModel->form_msg;}?></div>
                    <form method="POST" action="" name="correios-configuration-form" enctype="multipart/form-data" class="form-horizontal" >
                    	<div class='row'>
                        	<div class='col-sm-6'>
                        		<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
						                	<div class="radio">
						                	
						                	<?php 
						                	$checkedShippingOn = '';
						                	$checkedShippingOff = 'checked';
						                	if( isset($configurationsModel->configurations['Correios']['shipping']) && $configurationsModel->configurations['Correios']['shipping'] == 'T'){
						                		$checkedShippingOn = 'checked' ;
						                		$checkedShippingOff = '';
						                	}
						                	
						                	?>
						                       <label class="col-sm-8 control-label"><strong>Sem Cálculo de Frete</strong>
						                        	<input type="radio" name='configurations[Correios][shipping]' class="col-sm-6" value='F' <?php echo $checkedShippingOff; ?> > 
						                        </label>
						                        <label class="col-sm-8 control-label"><strong>Com Cálculo do Frete com Correios</strong>
						                        	<input type="radio" name='configurations[Correios][shipping]' class="col-sm-6" value='T' <?php echo $checkedShippingOn; ?> >
						                        </label>
					                      	</div>
			                      		</div>
					               	</div>
					            </div>
					            <div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
						                      <div class="radio">
						                        <label class="col-sm-8 control-label"><strong>Mercado Pago</strong>
						                        <input type="radio" name='configurations[Correios][mercadopago_payment]' class="col-sm-6" value='T' checked></label>
					                      	</div>
			                      		</div>
					               	</div>
					            </div>
					       		<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-8 control-label">ID Produto Padrão:</label> 
											 <div class="col-sm-2">
											<input type="text" name="configurations[Correios][id_product_default]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Correios']['id_product_default']; ?>">
											</div>
										</div>
									</div>
								</div>
                            	<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-8 control-label">Peso minimo para calculo do frete em Kg:</label> 
											<div class="col-sm-2">
												<input type="text" name="configurations[Correios][weight_min]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Correios']['weight_min']; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-8 control-label">Adicional fixo por pedido R$:</label> 
											 <div class="col-sm-2">
											<input type="text" name="configurations[Correios][flat_rate]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Correios']['flat_rate']; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label  class="col-sm-8 control-label">Adicional variável por pedido %:</label> 
											 <div class="col-sm-2">
											<input type="text" name="configurations[Correios][percentage_rate]"  class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Correios']['percentage_rate']; ?>">
											</div>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="col-sm-8 control-label">Adicionar mais Dias ao prazo de envio dos correios:</label> 
											 <div class="col-sm-2">
											<input type="text" name="configurations[Correios][cross_docking]"   class="form-control input-sm"  value="<?php echo $configurationsModel->configurations['Correios']['cross_docking']; ?>">
											</div>
										</div>
									</div>
								</div>
                                <div class="box-footer">
                					<button type="submit" class="btn btn-primary btn-sm pull-right" name="correios-configuration" ><i class='fa fa-check'></i> Salvar</button>
                				</div>
                            </div>
                        </div>
                    </form>
                </div><!-- /.tab-pane -->
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
            </div><!-- /.tab-content -->
        </div><!-- nav-tabs-custom -->
    </div><!-- /.col -->
</div> <!-- /.row -->
<!-- END CUSTOM TABS -->