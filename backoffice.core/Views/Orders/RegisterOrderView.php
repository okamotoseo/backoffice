<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
	<div class="message">
			<?php if(!empty($sucess['message'])){ echo "<div class='callout callout-success'><h4>Tip!</h4><p>".$sucess['message']."</></div>";}?>
		</div>
        
	    	<div class="box box-primary">
    			<div class="box-header with-border">
    				<h3 class="box-title">Informações</h3>
    				<div class='box-tools pull-right'>
    					<div class="form-group">
        	        		<a href='/Orders/RegisterOrder/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    				</div>
    			</div>
    			<div class="box-body">
					
					<form role="form" method="POST" action="/Orders/RegisterOrder/" name="register-orders" >	
    				
    				<div class='row'>
    				
    					<div class="col-sm-12">
    						<div class='row'>
                            	<div class="col-sm-2">
                            		<div class="form-group">
                            			<label>Persquisar por:</label>
                                		<select class='form-control' id="autocomplete-product-type" >
                                			<option value='sku'> Sku</option>
                                			<option value='title'> Título</option>
                                			<option value='reference'> Referência</option>
                            			</select>
                            		</div>
                            	</div>
                                <div class="col-sm-4">
                            		<div class="form-group">
                                		<label>Código:</label>
                                		<input type="text" class="form-control input-sm" id='autocomplete_product_id'  product_id= '<?php echo $productId;?>' tabindex="5"  name='autocomplete-sku'  value=''>
                            		</div>
                            	</div>
                            	<div class="col-sm-6">
                            	</div>
                            </div>
                            <div class='row'>
                        		<div class="col-sm-12">
                                	<div class="form-group">
                                    	<table  class='table table-condensed' >
                                    	<thead>
                                        	<tr>
                                        		<th>Produto</th>
                                        		<th></th>
                                        		<th></th>
                                        		<th style="text-align:right">Preço&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        		<th>Qtd.</th>
                                        		<th>Total</th>
                                        		<th></th>
                                        	</tr>
                                        	</thead>
                                        	<tbody id='items-order'>
                                        	
                                        	</tbody>
                                        	<tfoot>
                                        	<tr>
                                        		<th></th>
                                        		<th colspan='4' style='text-align:right'>Subtotal R$</th>
                                        		<th width='70px'><div class="form-group"><input type="text" name="Subtotal"  id='subtotal'  class="form-control input-sm" value="0"></div></th>
                                        		<th></th>
                                        	</tr>
                                        	<tr>
                                        		<th></th>
                                        		<th colspan='4' style='text-align:right'>Desconto R$</th>
                                        		<th width='70px'><div class="form-group"><input type="text" name="ValorCupomDesconto"  id='discount' class="form-control input-sm" value="0"></div></th>
                                        		<th></th>
                                        	</tr>
                                        	<tr>
                                        		<th></th>
                                        		<th colspan='4' style='text-align:right'>Frete R$</th>
                                        		<th width='70px'><div class="form-group"><input type="text" name="ValorFrete"  id='frete' class="form-control input-sm" value="0"></div></th>
                                        		<th></th>
                                        	</tr>
                                        	<tr bgcolor="#f4f4f4">
                                        		<th style='text-align:right'>Marketplace</th>
                                        		<th style='text-align:right'>
                                        			<div class="form-group">
                            							<select id="Marketplace" name="Marketplace" class="form-control input-sm">
                                							<option value='Televendas'>Televendas</option>
                                							<option value='Shopee'>Shopee</option>
                            							</select>
                        							</div>
                        						</th>
                                        		<th style='text-align:right'>Forma de Pagamento</th>
                                        		<th style='text-align:right'>
                                        			<div class="form-group">
                            							<?php 
                            							$transferencia = '';
                            							switch(trim($orderPaymentsModel->FormaPagamento)){
                            							    case "MercadoPago": $mercadopago = "selected"; break;
                            							    case "Transferencia": $transferencia = "selected"; break;
                            							    case "PIX": $pix = "selected"; break;
                            							    case "Dinheiro": $dinheiro = "selected"; break;
                            							    case "Pagseguro": $pagseguro = "selected"; break;
                            							    case "Shopee": $shopee = "selected"; break;
                            							    default : $mercadopago = "selected"; break;
                            							} ?>
                            							<select id="FormaPagamento" name="FormaPagamento" class="form-control input-sm">
                                							<option value='Transferencia' <?php echo $transferencia; ?>>Transferências</option>
                                							<option value='PIX' <?php echo $pix; ?>>PIX</option>
                                							<option value='Dinheiro' <?php echo $dinheiro; ?>>Dinheiro</option>
                                							<option value='Mercadopago' <?php echo $mercadopago; ?>>MercadoPago</option>
                                							<option value='Pagseguro' <?php echo $pagseguro; ?>>Pagseguro</option>
                                							<option value='Shopee' <?php echo $shopee; ?>>Shopee</option>
                            							</select>
                        							</div>
                                        		</th>
                                        		<th style='text-align:right'>Total R$</th>
                                        		<th  width='70px'>
                                        			<div class="form-group"><input type="text" name="ValorPedido"  id='total' class="form-control input-sm" value="0"></div>
                    							</th>
                    							<th></th>
                                        	</tr>
                                        	</tfoot>
                                    	</table>
                                	</div>
                            	</div>
                        	</div>
                    	</div>
                    </div>
                    <div class="row">
                    
                    	<div class="col-sm-4">
                    	
                    		<div class="col-sm-12">
    							<div class="form-group">
    								<label for="CPFCNPJ">CPF/CNPJ:</label>
    								<input type="text" name="CPFCNPJ"  id='autocomplete_cpfcnpj' class="form-control input-sm" value="<?php echo $customerModel->CPFCNPJ; ?>">
    							</div>
    						</div>
    						
							<div class="col-sm-12">
								<div class="form-group">
        							<?php 
        							$juridica = '';
        							switch(intval($customerModel->TipoPessoa)){
        							    case "1": $juridica = "selected"; break;
        							    case "2": $fisica = "selected"; break;
        							    default : $fisica = "selected"; break;
        							} ?>
        							<label for="TipoPessoa">Tipo Pessoa:</label>
        							<select id="TipoPessoa" name="TipoPessoa" class="form-control input-sm">
            							<option value='1' <?php echo $fisica; ?>>Física</option>
            							<option value='2' <?php echo $juridica; ?>>Jurídica</option>
        							</select>
    							</div>
    						</div>
    						
        					<div class="col-sm-12">
        						<div class="form-group">
        							<?php $all = $m = $f  = '';
        							switch(strtoupper(trim($customerModel->Genero))){
        							    case "M": $m = "selected"; break;
        							    case "F": $f = "selected"; break;
        							    default : $m = "selected"; break;
        							} ?>
        							<label for="Genero">Gênero:</label>
        							<select id="Genero" name="Genero" class="form-control input-sm">
            							<option value='M' <?php echo $m; ?>>Masculino</option>
            							<option value='F' <?php echo $f; ?>>Feminino</option>
        							</select>
    							</div>
    						</div>
    						
    						<div class="col-sm-12">
    							<div class="form-group">
    								<label for="RGIE">RG/IE:</label>
    								<input type="text" name="RGIE"  id='RGIE' class="form-control input-sm" value="<?php echo $customerModel->RGIE; ?>">
    							</div>
    						</div>
    						
    					</div>
    					
						<div class="col-sm-4">
						
    						<div class="col-sm-12">
    							<div class="form-group">
    								<label for="Nome">Nome:</label> 
    								<input type="text" name="Nome"  id='Nome' class="form-control input-sm" value="<?php echo $customerModel->Nome; ?>">
    							</div>
    						</div>
    						<div class="col-sm-12">
    							<div class="form-group">
    								<label for="Email">Email:</label> 
    								<input type="text" name="Email"  id='Email' class="form-control input-sm" value="<?php echo $customerModel->Email; ?>">
    							</div>
    						</div>
					
    						<div class="col-sm-6">
    							<div class="form-group">
    								<label for="Telefone">Telefone:</label> 
    								<input type="text" name="Telefone"  id='Telefone' class="form-control input-sm" value="<?php echo $customerModel->Telefone; ?>">
    							</div>
    						</div>
    						<div class="col-sm-6">
    							<div class="form-group">
    								<label for="TelefoneAlternativo">Telefone 2:</label> 
    								<input type="text" name="TelefoneAlternativo"  id='TelefoneAlternativo' class="form-control input-sm" value="<?php echo $customerModel->TelefoneAlternativo; ?>">
    							</div>
    						</div>
    				
    						<div class="col-sm-6">
    							<div class="form-group">
    								<label>Aniversário:</label> 
    								<div class="input-append date" data-date="" data-date-format="dd/mm/aaaa">
    									<input type="text"  name="DataNascimento" id="DataNascimento" class="form-control date-mask input-sm"  placeholder='DD/MM/AAAA' value="<?php echo $customerModel->DataNascimento;?>">
    									<span class="add-on" ><i class="icon-th"></i></span>
    								</div>
    							</div>
							</div>
							<div class="col-sm-6">
    							<div class="form-group">
    								<label for="Apelido">Apelido:</label> 
    								<input type="text" name="Apelido"  id='Apelido' class="form-control input-sm" value="<?php echo $customerModel->Apelido; ?>">
    							</div>
    						</div>
    						
    					</div>
						
    					
    					<div class="col-sm-4">
    					
    						<div class="col-sm-4">
    							<div class="form-group">
    								<label for="CEP">CEP:</label> 
    								<input type="text" name="CEP"  id='CEP' class="form-control input-sm" value="<?php echo $customerModel->CEP; ?>">
    							</div>
    						</div>
    						<div class="col-sm-8">
    							<div class="form-group">
    								<label for="Bairro">Bairro:</label> 
    								<input type="text" name="Bairro"  id='Bairro' class="form-control input-sm" value="<?php echo $customerModel->Bairro; ?>">
    							</div>
    						</div>
    						<div class="col-sm-9">
    							<div class="form-group">
    								<label for="Endereco">Endereço:</label>
    								<input type="text" name="Endereco"  id='Endereco' class="form-control input-sm" value="<?php echo $customerModel->Endereco; ?>">
    							</div>
    						</div>
    						<div class="col-sm-3">
    							<div class="form-group">
    								<label for="Numero">Numero:</label>
    								<input type="text" name="Numero"  id='Numero' class="form-control input-sm" value="<?php echo $customerModel->Numero; ?>">
    							</div>
    						</div>
    						<div class="col-sm-12">
    							<div class="form-group">
    								<label for="Complemento">Complemento:</label> 
    								<input type="text" name="Complemento"  id='Complemento' class="form-control input-sm" value="<?php echo $customerModel->Complemento; ?>">
    							</div>
    						</div>
    						<div class="col-sm-9">
    							<div class="form-group">
    								<label for="Cidade">Cidade:</label> 
    								<input type="text" name="Cidade"  id='Cidade' class="form-control input-sm" value="<?php echo $customerModel->Cidade; ?>">
    							</div>
    						</div>
					
    						<div class="col-sm-3">
    							<div class="form-group">
    								<label for="Estado">Estado:</label> 
    								<input type="text" name="Estado"  id='Estado' class="form-control input-sm" placeholder='UF' value="<?php echo $customerModel->Estado; ?>">
    							</div>
    						</div>
    						
    					</div>
				</div>
    		</div>
    		<div class="box-footer">
    			<div class="form-group">
    				<a href='/Orders/RegisterOrder/' class='btn btn-default btn-sm '><i class='fa fa-ban'></i> Limpar</a>
    				<button type='submit' name='save-order' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-check'></i> Salvar</button>
				</div>
    		</div>
    	</div>
    	<div class="overlay orders" style='display:none;'>
           	<i class="fa fa-refresh fa-spin"></i>
        </div>
	</div>
</div>
