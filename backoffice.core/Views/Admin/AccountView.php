<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $accountModel->form_msg)){ echo  $accountModel->form_msg;}?>
		</div>
		
		<div class="box box-primary">
			<form method="POST" action="" name="form-account">
			<div class="box-header with-border">
				<h3 class="box-title">Cadastro de conta</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			
			<div class="box-body">
				<div class="row">
    				<div class="col-xs-12">
    					<h3 class="body-header"> 	
                    		<i class="fa fa-child"></i> Cadastro Responsável
                  		</h3>
                  	</div>
              	</div>
              	<div class="row">
					<div class="col-xs-6">		
						<div class="form-group">
							<label>Nome / Razão Social:</label> 
							<input type="text" name="account_name" id="account_name" class="form-control account_name" placeholder="" value="<?php echo $accountModel->account_name; ?>" />
						</div>
					</div>
					
					<div class="col-xs-6">		
						<div class="form-group">
							<label>Email:</label> 
							<input type="text" name="account_email" id="account_email" class="form-control account_email" placeholder="" value="<?php echo $accountModel->account_email; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label>Telefone:</label> 
							<input type="text" name="account_phone" id="account_phone" class="form-control account_phone" placeholder="" value="<?php echo $accountModel->account_phone; ?>" />
						</div>
					</div>
		
					<div class="col-xs-6">		
						<div class="form-group">
							<label>Celular:</label> 
							<input type="text" name="account_mobile" id="account_mobile" class="form-control account_mobile" placeholder="" value="<?php echo $accountModel->account_mobile; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label>RG/IE:</label> 
							<input type="text" name="account_rg" id="account_rg" class="form-control account_rg" placeholder="" value="<?php echo $accountModel->account_rg; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label>CPF/CNPJ:</label> 
							<input type="text" name="account_document" id="account_document" class="form-control account_document" placeholder="" value="<?php echo $accountModel->account_document; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label>CEP:</label> 
							<input type="text" name="account_postalcode" id="account_postalcode" class="form-control account_postalcode" placeholder="CEP" value="<?php echo $accountModel->account_postalcode; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label>Endereço:</label> 
							<input type="text" name="account_address" id="account_address" class="form-control account_address" placeholder="Endereço" value="<?php echo $accountModel->account_address; ?>" />
						</div>
					</div>
					<div class="col-xs-2">		
						<div class="form-group">
							<label>Nº:</label> 
							<input type="text" name="account_number" id="account_number" class="form-control account_number" placeholder="Número" value="<?php echo $accountModel->account_number; ?>" />
						</div>
					</div>
					<div class="col-xs-4">		
						<div class="form-group">
							<label>Bairro:</label> 
							<input type="text" name="account_neighborhood" id="account_neighborhood" class="form-control account_neighborhood" placeholder="Bairro" value="<?php echo $accountModel->account_neighborhood; ?>" />
						</div>
					</div>
					<div class="col-xs-2">		
						<div class="form-group">
							<label>Estado:</label> 
							<input  type="text" name="account_state" id="account_state" class="form-control account_state" placeholder="Estado" value="<?php echo $accountModel->account_state; ?>" />
						</div>
					</div>
					<div class="col-xs-4">		
						<div class="form-group">
							<label>Cidade:</label> 
							<input type="text" name="account_city" id="account_city" class="form-control account_city" placeholder="Cidade" value="<?php echo $accountModel->account_city; ?>" />
						</div>
					</div>
				</div>
				<div class="row">
    				<div class="col-xs-12">
    					<h3 class="body-header"> 	
                    		<i class="fa  fa-building"></i> Cadastro da Loja
                  		</h3>
                  	</div>
              	</div>
				<div class="row">
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="company">Razão Social:</label> 
							<input type="text" name="company" id="company" class="form-control company" value="<?php echo $storeModel->company; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="store">Nome Fantasia:</label> 
							<input type="text" name="store" id="store" class="form-control store"  value="<?php echo $storeModel->store; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="cnpj">CNPJ:</label> 
							<input type="text" name="cnpj" id="cnpj" class="form-control cnpj" value="<?php echo $storeModel->cnpj; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="url">Site:</label> 
							<input type="text" name="url" id="url" class="form-control url" value="<?php echo $storeModel->url; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="email_sac">Email Atendimento:</label> 
							<input type="text" name="email_sac" id="email_sac" class="form-control email_sac" value="<?php echo $storeModel->email_sac; ?>" />
						</div>
					</div>
					<div class="col-xs-6">		
						<div class="form-group">
							<label for="email_send">Email Admin:</label> 
							<input type="text" name="email_send" id="email_send" class="form-control email_send" value="<?php echo $storeModel->email_send; ?>" />
						</div>
					</div>
					
					<div class="col-xs-3">
						<div class="form-group">
							<label for="name">Telefone:</label> 
							<input type="text" name="phone" id="phone" class="form-control phone" placeholder="(00)00000-0000" value="<?php echo $storeModel->phone; ?>" />
						</div>
					</div>
					
					<div class="col-xs-3">
						<div class="form-group">
							<label for="postalcode">CEP:</label> 
							<input type="text" name="postalcode" id="postalcode" class="form-control postalcode" placeholder="CEP" value="<?php echo $storeModel->postalcode; ?>" />
						</div>
					</div>
					<div class="col-xs-6">
						<div class="form-group">
							<label for="address">Endereço:</label> 
							<input type="text" name="address" id="address" class="form-control address" placeholder="Endereço" value="<?php echo $storeModel->address; ?>" />
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="number">Nº:</label> 
							<input type="text" name="number" id="number" class="form-control nnumberame" placeholder="Número" value="<?php echo $storeModel->number; ?>" />
						</div>
					</div>
					<div class="col-xs-4">
						<div class="form-group">
							<label for="neighborhood">Bairro:</label> 
							<input type="text" name="neighborhood" id="neighborhood" class="form-control neighborhood" placeholder="Bairro" value="<?php echo $storeModel->neighborhood; ?>" />
						</div>
					</div>
					<div class="col-xs-2">
						<div class="form-group">
							<label for="state">Estado:</label> 
							<input type="text" name="state" id="state" class="form-control state" placeholder="Estado" value="<?php echo $storeModel->state; ?>" />
						</div>
					</div>
					<div class="col-xs-4">
						<div class="form-group">
							<label for="city">Cidade:</label> 
							<input  type="text" name="city" id="city" class="form-control city" placeholder="Cidade" value="<?php echo $storeModel->city; ?>" />
						</div>
					</div>
					<div class="col-xs-12">
    					<h3 class="body-header"> 	
                    		<i class="fa fa-code-fork"></i> Módulos Disponíveis:
                  		</h3>
                  	</div>
					<div class="col-xs-12">
						<div class="form-group">
                            <label for='modules'>Módulos: </label>
                            <select class="form-control select2 modules" id='modules' multiple="multiple"  name='modules[]'>
                            <?php 
                                foreach($modules as $key => $module){
                                    $selected = in_array($module['id'], $storeModel->modules) ? "selected" : "";
                                    echo "<option value='{$module['id']}' {$selected}>{$module['name']}</option>";
                                }
                            ?>
                            </select>
             			</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary pull-right" id="btn" >Salvar</button>
			</div>
			</form>
		</div><!-- /.box -->
	</div>
</div>
