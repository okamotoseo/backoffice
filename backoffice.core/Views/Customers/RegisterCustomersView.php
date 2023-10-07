<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="box box-primary">
		<div class="message alert"><?php if(!empty( $customerModel->form_msg)){ echo  $customerModel->form_msg;}?></div>
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Customers/RegisterCustomers" name="register-customers" >
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo $this->title?></h3>
				</div>
				<div class="box-body">
					<div class="row">
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
    								<input type="text" name="Telefone"  id='Nome' class="form-control input-sm" value="<?php echo $customerModel->Telefone; ?>">
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
    								<div class="input-append date" id="DataNascimento" data-date="" data-date-format="dd/mm/aaaa">
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
    								<label for="CPFCNPJ">CPF/CNPJ:</label>
    								<input type="text" name="CPFCNPJ"  id='CPFCNPJ' class="form-control input-sm" value="<?php echo $customerModel->CPFCNPJ; ?>">
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
    					<a href='<?php echo HOME_URI ?>/Customers/RegisterCustomers' class='btn btn-default btn-sm'><i class='fa fa-ban'></i> Limpar</a>
						<button type='submit' name='save-customer' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-check'></i> Salvar</button>
					</div>
				</div>
				<div class="overlay customers" style='display:none;'>
            		<i class="fa fa-refresh fa-spin"></i>
        		</div>
			</form>
		</div>
	</div>

	
</div>
