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
					<div class="col-md-4">		
						<div class="form-group">
							<label for="Store">Loja:</label> 
							<input type="text" name="Store"  class="form-control" value="<?php echo $setupModel->Store; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="host">URL:</label> 
							<input type="text" name="api_host"  class="form-control" value="<?php echo $setupModel->api_host; ?>" />
						</div>
					</div>
				
					<div class="col-md-4">		
						<div class="form-group">
							<label for="user">Usuário:</label> 
							<input type="text" name="username"  class="form-control" value="<?php echo $setupModel->username; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="password">Senha:</label> 
							<input type="text" name="password" id="password" class="form-control" value="<?php echo $setupModel->password; ?>" />
						</div>
					</div>
					
					<div class="col-md-4">		
						<div class="form-group">
							<label for="Consumer_Key">Consumer Key:</label> 
							<input type="text" name="Consumer_Key" class="form-control"  value="<?php echo $setupModel->Consumer_Key; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="Consumer_Secret">Consumer Secret:</label> 
							<input type="text" name="Consumer_Secret" class="form-control"  value="<?php echo $setupModel->Consumer_Secret; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="Access_Token">Access Token:</label> 
							<input type="text" name="Access_Token"  class="form-control" value="<?php echo $setupModel->Access_Token; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="Access_Token_Secret">Access Token Secret:</label> 
							<input type="text" name="Access_Token_Secret" class="form-control" value="<?php echo $setupModel->Access_Token_Secret; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="Token">Token:</label> 
							<input type="text" name="token" class="form-control" value="<?php echo $setupModel->token; ?>" disabled />
						</div>
					</div>
				</div>
				<div class='row'>
					<div class="col-md-4">
		              <div class="box box-primary">
		                <div class="box-header with-border">
							<label>
                            	<input type='radio' name='type' class='flat-red' value='import' <?php echo $setupModel->type == 'import' ? "checked" : ""; ?> > &nbsp; Integração de Importação
                            </label>
		                </div>
		                <div class="box-body">
		                  <ul>
		                    <li>Importa Produtos</li>
		                    <li>Importa Categorias</li>
		                    <li>Importa Attributos</li>
		                    <li>Importa Conjunto de Attributos</li>
		                    <li>Exporta Pedidos</li>
		                  </ul>
		                </div>
		              </div>
		            </div>
		            
		            <div class="col-md-4">
		              <div class="box box-primary">
		                <div class="box-header with-border">
							<label>
                            	<input type='radio' name='type' class='flat-red' value='export' <?php echo $setupModel->type == 'export' ? "checked" : ""; ?> > &nbsp; Integração de Exportação
                            </label>
		                </div>
		                <div class="box-body">
		                  <ul>
		                    <li>Exporta Produtos</li>
		                    <li>Importa Categorias</li>
		                    <li>Exporta Attributos</li>
		                    <li>Exporta Conjunto de Attributos</li>
		                    <li>Importa Pedidos</li>
		                  </ul>
		                </div>
		              </div>
		            </div>
		            
		            <div class="col-md-4">
		              <div class="box box-primary">
		                <div class="box-header with-border">
							<label>
                            	<input type='radio' name='type' class='flat-red' value='import_export' <?php echo $setupModel->type == 'import_export' ? "checked" : ""; ?> > &nbsp; Integração de Importação e Exportação
                            </label>
		                </div>
		                <div class="box-body">
		                  <ul>
		                    <li>Importa e Exporta Produtos</li>
		                    <li>Importa Categorias</li>
		                    <li>Importa e Exporta Attributos</li>
		                    <li>Importa e Exporta Conjunto de Attributos</li>
		                    <li>Importa ou Exporta Pedidos</li>
		                  </ul>
		                </div>
		              </div>
		            </div>
				</div>
			</div>
			<div class="box-footer">
				<a class="btn btn-danger btn-sm" id='reset' ><i class='fa fa-trash' ></i> Reset</a>
				<button type="submit" class="btn btn-primary btn-sm pull-right" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
		</div>
	</div>
</div>