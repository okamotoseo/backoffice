<?php if ( ! defined('ABSPATH')) exit; ?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
			<?php if(!empty( $storeModel->form_msg)){ echo  $storeModel->form_msg;}?>
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
			</div><!-- /.box-body -->
			<div class="box-footer">
				<button type="submit" class="btn btn-info pull-right" id="save" name="save">Salvar</button>
			</div><!-- /.box-footer-->
			</form>
		</div><!-- /.box -->
	</div>
</div>