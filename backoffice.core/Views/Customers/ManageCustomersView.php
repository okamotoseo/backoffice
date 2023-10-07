<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $customerModel->form_msg)){ echo  $customerModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Customers/ManageCustomers/" name="filter-customers" >
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo $this->title?></h3>
					<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='<?php echo HOME_URI ?>/Customers/ManageCustomers/' class='btn btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
    	        	</div>
	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-sm-1">
							<div class="form-group">
								<label for="id">id</label> 
								<input type="text" name="id"  id='id' class="form-control" value="<?php echo $customerModel->id; ?>">
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<label for="CPFCNPJ">CPF/CNPJ:</label> 
								<input type="text" name="CPFCNPJ"  id='CPFCNPJ' class="form-control" value="<?php echo $customerModel->CPFCNPJ; ?>">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="Nome">Nome:</label> 
								<input type="text" name="Nome"  id='Nome' class="form-control" value="<?php echo $customerModel->Nome; ?>">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label for="Email">Email:</label> 
								<input type="text" name="Email"  id='Email' class="form-control" value="<?php echo $customerModel->Email; ?>">
							</div>
						</div>
						
						
						<div class="col-sm-2">
    						<?php  $meli = $ecommerce = $skyhub = $amazon = '';
    						switch($customerModel->Marketplace){
    						    case "Mercadolivre": $meli = "selected"; break;
    						    case "Ecommerce": $ecommerce = "selected"; break;
    						    case "Skyhub": $skyhub = "selected"; break;
    						    case "Amazon": $amazon = "selected"; break;
    						    case "Tray": $tray = "selected"; break;
    						}?>
							<label>Marketplace:</label>
							<select  id="Marketplaces" name="Marketplace" class="form-control input-sm">
								<option value=''>Todos</option>
								<option value='Mercadolivre' <?php echo $meli; ?>>Mercadolivre</option>
								<option value='Ecommerce' <?php echo $ecommerce; ?>>Ecommerce</option>
								<option value='Skyhub' <?php echo $skyhub; ?>>Skyhub</option>
								<option value='Amazon' <?php echo $amazon; ?>>Amazon</option>
								<option value='Tray' <?php echo $tray; ?>>Tray Ecommerce</option>
							</select>
						</div>
						
						<div class="col-sm-1">
							<?php  $select5 = $select50 = $select100 = $select150 = $select200 = $select1000= '';
							switch($customerModel->records){
							    case "5": $select5 = "selected"; break;
							    case "50": $select50 = "selected"; break;
							    case "100": $select100 = "selected"; break;
							    case "150": $select150 = "selected"; break;
							    case "200": $select200 = "selected"; break;
							    case "1000": $select1000 = "selected"; break;
							}?>
							<label for="records">Registros:</label>
							<select id="records" name="records" class="form-control input-sm">
    							<option value='5' <?php echo $select5; ?>>5</option>
    							<option value='50' <?php echo $select50; ?>>50</option>
    							<option value='100' <?php echo $select100; ?>>100</option>
    							<option value='150' <?php echo $select150; ?>>150</option>
    							<option value='200' <?php echo $select200; ?>>200</option>
    							<option value='1000' <?php echo $select1000; ?>>1K </option>
    							
							</select>
						</div>
						
					</div>
				</div>
				<div class="box-footer">
    				<div class="form-group">
    					<a id='export_csv' report_type='customers' class='btn btn-default btn-xs' title='Download CSV'><i class='fa fa-download'></i> Download</a>
						<button type='submit' name='filter-customer' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Filtrar</button>
					</div>
				</div>
				<div class="overlay customers" style='display:none;'>
            		<i class="fa fa-refresh fa-spin"></i>
        		</div>
			</form>
		</div>
	</div>

	<div class="col-md-12">
	
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="col-sm-2">
        			<div class="form-group">
        			<?php 
        			$ecommerce = isset($this->moduledata['Ecommerce'][0]) ? strtolower(trim($this->moduledata['Ecommerce'][0])) : 'ecommerce';
        			?>
            			<select id='select_action_customers' class='form-control input-sm'>
            				<option id='selected' value='select' >Ações</option>
            				<option value='update_gender' >Atualizar Gênero</option>
        				
        				</select>
        			</div>
    			</div>
    			<div class="attributes-customer" style='display:none;'>
        			
                    <div class="col-sm-2">
                    	<div class="form-group" id='gender-required'>
                    		<select class='form-control input-sm select-gender' id='gender'>
                    		<option value='M'> Masculino</option>
                    		<option value='F'> Feminino</option>
                    		
                    		</select>
                    	</div>
                    </div>
                    <div class='col-sm-4'>
                    	<div class='form-group'>
                    		<a class='btn btn-primary btn-sm' id='btn_update_customer_gender' ><i class='fa fa-check'></i></a>
                    	</div>
                    </div>
                </div>
				<table class="table table-condensed no-padding" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
					<thead>
						<tr>
							<th><input type='checkbox' id='' class='flat-red select_all' /></th>
							<th>Cliente</th>
							<th>Endereço</th>
							<th>Mkt / Cadastro</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					
					
					foreach ($cutomers as $key => $fetch):
// 					pre($fetch);die;
					$tipo =  $fetch['TipoPessoa'] == 1 ? 'Fisica' : 'jurídica' ;
					$gender = '';
					switch(strtoupper($fetch['Genero'])){
					    case 'M': $gender = 'Masculino';break;
					    case 'F': $gender = 'Feminino' ;break;
					    
					}
					echo "<tr>
                        <td><input type='checkbox' id='{$fetch['id']}' genero='{$fetch['Genero']}' class='flat-red select_one' /><br>{$fetch['id']}</td>
						<td>{$fetch['Nome']}<br>
                            <small><b>CPFCNPJ: </b>".formatarCpfCnpj($fetch['CPFCNPJ'])."</small><br>
                            <small><b>Email: </b>{$fetch['Email']}</small><br>
                            <small><b>Tel1.: </b>".formatPhone($fetch['Telefone'])."</small>
                            <small><b>Tel2.: </b>".formatPhone($fetch['TelefoneAlternativo'])."</small>
                        </td>
                            <td>{$fetch['Cidade']} / {$fetch['Estado']} <br>
                            <small><b>Endereço: </b>{$fetch['Endereco']}, {$fetch['Numero']}</small><br>
                            <small><b>CEP: </b>".formataCep($fetch['CEP'])."</small><small> <b>Bairro: </b>{$fetch['Bairro']}</small>
                            
                        </td>
                        <td>{$fetch['Marketplace']}</br>".dateFromTimeBr($fetch['DataCriacao'], '/')."</td>

                        <td><a class='delete' href='".HOME_URI."/Customers/ManageCustomers/del/{$fetch['id']}/' role='menuitem' tabindex='-1' /><i class='fa fa-trash'></i></a></td>";
// 					echo "<td align='center'>
//                             <div class='dropdown'>
//  		                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
//              		            <ul class='dropdown-menu pull-right' style='min-width:100px'>
//                   		          <li role='presentation'><a class='' href='".HOME_URI."/Customers/Customer/edit/{$fetch['id']}/' role='menuitem' tabindex='-1' title='Produto com descrição' ><i class='fa fa-pencil-square-o'></i>Editar</a></li>
//                   		          <li role='presentation'><a class='delete' href='".HOME_URI."/Customers/ManageCustomers/del/{$fetch['id']}/' role='menuitem' tabindex='-1' /><i class='fa fa-trash'></i> Excluir</a></li>
// 					            </ul>
// 					           </div>
//                             </td>";
						echo "</tr>";
             
		             endforeach;
					?>	
					</tbody>
				</table>
				<?php 
				pagination($totalReg, $customerModel->pagina_atual, HOME_URI."/Customers/ManageCustomers", array(
				    "id" => $customerModel->id,
				    "Codigo" => str_replace("%", "_x_", $customerModel->Codigo),
				    "Nome" => str_replace("%", "_x_", $customerModel->Nome),
				    "Email" => str_replace("%", "_x_", $customerModel->Email),
				    "CPFCNPJ" => str_replace(" ", "_", $customerModel->CPFCNPJ),
				    "Genero" => str_replace(" ", "_", $customerModel->Genero),
				    "marketplace" => str_replace(" ", "_", $customerModel->Marketplace),
				    "records" => $customerModel->records
				    ));
				?>
			</div><!-- /.box-body -->
			<div class="overlay customers" style='display:none;'>
            		<i class="fa fa-refresh fa-spin"></i>
        		</div>
		</div><!-- /.box -->
	</div>
</div>
