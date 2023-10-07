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
							<label for="Store">cpfCnpj:</label> 
							<input type="text" name="cpfCnpj"  class="form-control" value="<?php echo $setupModel->cpfCnpj; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="inscricaoMunicipal">inscricaoMunicipal:</label> 
							<input type="text" name="inscricaoMunicipal"  class="form-control" value="<?php echo $setupModel->inscricaoMunicipal; ?>" />
						</div>
					</div>
				
					<div class="col-md-4">		
						<div class="form-group">
							<label for="inscricaoEstadual">inscricaoEstadual:</label> 
							<input type="text" name="inscricaoEstadual"  class="form-control" value="<?php echo $setupModel->inscricaoEstadual; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="razaoSocial">razaoSocial:</label> 
							<input type="text" name="razaoSocial" id="razaoSocial" class="form-control" value="<?php echo $setupModel->razaoSocial; ?>" />
						</div>
					</div>
					
					<div class="col-md-4">		
						<div class="form-group">
							<label for="nomeFantasia">nomeFantasia:</label> 
							<input type="text" name="nomeFantasia" class="form-control"  value="<?php echo $setupModel->nomeFantasia; ?>" />
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="certificado">certificado:</label> 
							<input type="text" name="Consumer_Secret" class="form-control"  value="<?php echo $setupModel->certificado; ?>" />
						</div>
					</div>
					
					
					<div class="col-md-4">		
						<div class="form-group">
							<label for="simplesNacional">simplesNacional *:</label> 
							<?php 
							$selectedT = $selectedF = '';
							switch($setupModel->simplesNacional){
								case 'T': $selectedT = 'selected'; break;
								case 'F': $selectedF = 'selected'; break;
							}
							?>
							<select name="simplesNacional"  class="form-control">
								<option value='' >Selecione</option>
								<option value='T' <?php echo $selectedT; ?> >É optante</option>
								<option value='F' <?php echo $selectedF; ?>>Não é optante</option>
							</select>
						</div>
					</div>
					
					<div class="col-md-4">		
						<div class="form-group">
							<label for="regimeTributario">regimeTributario *:</label> 
							<?php 
							$selected0 = $selected1 = $selected2 = $selected3 = $selected4 = '';
							switch($setupModel->regimeTributario){
								case 0: $selected0 = 'selected'; break;
								case 1: $selected1 = 'selected'; break;
								case 2: $selected2 = 'selected'; break;
								case 3: $selected3 = 'selected'; break;
								case 4: $selected4 = 'selected'; break;
							}
							?>
							<select name="regimeTributario"  class="form-control">
								<option value='' >Selecione</option>
								<option value='0' <?php echo $selected0; ?>>Nenhum</option>
								<option value='1' <?php echo $selected1; ?>>Simples Nacional</option>
								<option value='2' <?php echo $selected2; ?>>Simples Nacional - Excesso</option>
								<option value='3' <?php echo $selected3; ?>>Normal - Lucro Presumido</option>
								<option value='4' <?php echo $selected4; ?>>Normal - Lucro Real</option>
							</select>
						</div>
					</div>
					
					
					<div class="col-md-4">		
						<div class="form-group">
							<label for="incentivoFiscal">incentivoFiscal:</label> 
							<?php 
							$selectedT = $selectedF = '';
							switch($setupModel->incentivoFiscal){
								case 'T': $selectedT = 'selected'; break;
								case 'F': $selectedF = 'selected'; break;
							}
							?>
							<select name="incentivoFiscal"  class="form-control">
								<option value='' >Selecione</option>
								<option value='T' <?php echo $selectedT; ?> >Sim</option>
								<option value='F' <?php echo $selectedF; ?>>Não</option>
							</select>
						</div>
					</div>
					
					<div class="col-md-4">		
						<div class="form-group">
							<label for="incentivadorCultural">incentivadorCultural:</label> 
							<?php 
							$selectedT = $selectedF = '';
							switch($setupModel->incentivadorCultural){
								case 'T': $selectedT = 'selected'; break;
								case 'F': $selectedF = 'selected'; break;
							}
							?>
							<select name="incentivadorCultural"  class="form-control">
								<option value='' >Selecione</option>
								<option value='T' <?php echo $selectedT; ?> >Sim</option>
								<option value='F' <?php echo $selectedF; ?>>Não</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="regimeTributarioEspecial">regimeTributarioEspecial *:</label> 
							<?php 
							$selected0 = $selected1 = $selected2 = $selected3 = $selected4 = $selected5 = $selected6 = '';
// 							switch($setupModel->regimeTributarioEspecial){
// 								case 0: $selected0 = 'selected'; break;
// 								case 1: $selected1 = 'selected'; break;
// 								case 2: $selected2 = 'selected'; break;
// 								case 3: $selected3 = 'selected'; break;
// 								case 4: $selected4 = 'selected'; break;
// 								case 5: $selected5 = 'selected'; break;
// 								case 6: $selected6 = 'selected'; break;
// 							}
							?>
							<select name="regimeTributarioEspecial"  class="form-control" >
								<option value=''  >Selecione</option>
								<option value='0' <?php echo $selected0; ?>>Sem Regime Tributário Especial</option>
								<option value='1' <?php echo $selected1; ?>>Micro Empresa Municipal</option>
								<option value='2' <?php echo $selected2; ?>>Estimativa</option>
								<option value='3' <?php echo $selected3; ?>>Sociedade de Profissionais</option>
								<option value='4' <?php echo $selected4; ?>>Cooperativa</option>
								<option value='5' <?php echo $selected5; ?>>Microempresário Individual - MEI</option>
								<option value='6' <?php echo $selected6; ?>>Microempresa ou Pequeno Porte - ME EPP</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">		
						<div class="form-group">
							<label for="endereco">endereco *:</label> 
							<input type="text" name="endereco" class="form-control" value="<?php echo $setupModel->endereco; ?>" />
						</div>
					</div>
				</div>
				<div class='row'>
					<div class="col-md-6">
		              <div class="box box-primary">
		                <div class="box-header with-border">
							<label>
                            	<input type='radio' name='type' class='flat-red' value='import' <?php echo $setupModel->type == 'import' ? "checked" : ""; ?> > &nbsp; NF-e Nota Fiscal Eletrônica
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
		            
		            <div class="col-md-6">
		              <div class="box box-primary">
		                <div class="box-header with-border">
							<label>
                            	<input type='radio' name='type' class='flat-red' value='export' <?php echo $setupModel->type == 'export' ? "checked" : ""; ?> > &nbsp; NFC-e Nota Fiscal Consumidor Eletrônica
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
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary btn-sm pull-right" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
		</div>
	</div>
</div>