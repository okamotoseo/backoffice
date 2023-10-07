<?php if ( ! defined('ABSPATH')) exit;?>

<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $advertsModel->form_msg)){ echo  $advertsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="/Modules/Mercadolivre/Adverts/Adverts/" name="filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar anúncios</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Mercadolivre/Adverts/Adverts/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label for="sku">MLB:</label> 
								<input type="text" name="id"  id='id' class="form-control input-sm" value="<?php echo $advertsModel->id; ?>">
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label for="sku">SKU:</label> 
								<input type="text" name="sku"  id='sku' class="form-control input-sm" value="<?php echo $advertsModel->sku; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="title">Título:</label> 
								<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $advertsModel->title; ?>">
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label for="reference">Referência:</label> 
								<input type="text" name="reference"  id='reference' class="form-control input-sm" value="<?php echo $advertsModel->reference; ?>">
							</div> 
						</div>
						<div class="col-md-2">
    						<div class="form-group">
    						<label for="status">Status:</label>
    								
    						<?php 
    						echo "<select id='status' name='status' class='form-control input-sm'>
									<option value='' selected >Todos</option>";
    						foreach($advertsStatus as $k => $status){
    							$selected = !empty($status) &&  $advertsModel->status == $status ? 'selected' : '' ;
    							echo "<option value='{$status}' {$selected}>{$status}</option>";
    						}
							?>
							</select>
    					</div>
    								
						</div>
						<div class="col-md-2">
    						<div class="form-group">
        						<?php 
        						$fulfillment = '';
    							switch($advertsModel->logistic_type){
    							    case "fulfillment": $fulfillment = "selected"; break;
    							}
    							
    							?>
								<label for="logistic_type">Logistica:</label>
								<select id="logistic_type" name="logistic_type" class="form-control input-sm">
								<option value=''>Todos</option>
								<option value='fulfillment' <?php echo $fulfillment; ?>>Fulfillment</option>
								</select>
							</div>
						</div>
						
						<div class="col-md-1">
							<div class="form-group">
							<?php 
							$select5 = $select50 = $select100 = $select150 = $select200 = '';
							switch($advertsModel->records){
							    case "5": $select5 = "selected"; break;
							    case "50": $select50 = "selected"; break;
							    case "100": $select100 = "selected"; break;
							    case "150": $select150 = "selected"; break;
							    case "200": $select200 = "selected"; break;
							}
							
							?>
								<label for="records">Registros:</label>
								<select id="records" name="records" class="form-control input-sm">
								<option value='5' <?php echo $select5; ?>>5</option>
								<option value='50' <?php echo $select50; ?>>50</option>
								<option value='100' <?php echo $select100; ?>>100</option>
								<option value='150' <?php echo $select150; ?>>150</option>
								<option value='200' <?php echo $select200; ?>>200</option>
								</select>
							</div>
						</div>


					</div>
				</div>
				<div class="box-footer">
					<button type='submit' name='available-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
				<div class="message-actions"></div>
			</div>
			<div class="box-body no-padding" >
			<div class="col-sm-2">
    			<div class="form-group">
        			<select id='select_action_meli_adverts' class='form-control input-sm'>
        				<option id='selected' value='select' >Ações</option>
        				<option value='update_stock_price' >Atualizar Preço e Estoque</option>
        				<option value='remove_meli_adverts' >Excluir Anúncios</option>
    				
    				</select>
    			</div>
			</div>
				<table  class="table table-condensed table-hover display" id="search-default" width="100%" >
					<thead>
						<tr>
							<th><input type='checkbox' id='' class='flat-red select_all_adverts' /></th>
							<th>Img</th>
							<th>Id</th>
							<th>Título</th>
							<th>Qtd.</th>
							<th>Preço</th>
							<th>Status</th>
							<th>Publicado</th>
							<th>Atualizado</th>
							<th>Ações</th>
							
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($list as $fetch): 
// 						pre($fetch);die;
					   $messageError = $fetch['message'] === 'success' || empty( $fetch['message']) ? '' : "<span class='badge bg-red'><i class='fa fa-warning' data-toggle='tooltip' title='{$fetch['message']}'></i></span>" ;
					   $logistic = $fetch['logistic_type'] != 'fulfillment' ? '' : "<span class='badge bg-green'><i>Fulfilment</i></span>" ;
					
    					echo "<tr id='{$fetch['id']}'>
                            <td><input type='checkbox' advert_id='{$fetch['id']}' class='flat-red select_one_advert' /></td>
                            <td><img src='{$fetch['thumbnail']}' /></td>
                            <td><a href='{$fetch['permalink']}' target='_blank' >MLB{$fetch['id']}</a><br>
                                {$messageError} {$logistic}
                            </td>
    						<td><a href='/Products/Product/{$fetch['product_id']}' target='_blank' >{$fetch['title']}</a><br><b>SKU:</b> {$fetch['sku']}</td>
    						<td>{$fetch['available_quantity']}</td>
                            <td>{$fetch['price']}</td>
                            <td>{$fetch['status']}</td>
                             <td>".dateTimeBrBreakLine($fetch['created'])."</td>
                            <td>".dateTimeBrBreakLine($fetch['updated'])."</td>
    						<td> 
                                <div class='dropdown'>
                                    <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                    <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                        <li role='presentation'><a class='action_ads' action='update_stock_price' ads_id='{$fetch['id']}' sku='{$fetch['sku']}' role='menuitem' tabindex='-1' title='Atualizar preço e estoque' ><i class='fa fa-refresh'></i>Preço e Estoque</a></li>
                                        <li role='presentation'><a  class='action_ads' action='remove_ads' ads_id='{$fetch['id']}' sku='{$fetch['sku']}' role='menuitem' tabindex='-1' title='Excluir anúncio'><i class='fa fa-trash'></i> Excluir</a></li>   
                     
                                    </ul>
                                </div>
                			</td>
    						</tr>";
		             endforeach;
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $advertsModel->pagina_atual, HOME_URI."/Modules/Mercadolivre/Adverts/Adverts", array(
			    "id" => $advertsModel->id,
			    "sku" => str_replace("%", "_x_", $advertsModel->sku),
			    "title" => str_replace("%", "_x_", $advertsModel->title),
			    "logistic_type" => $advertsModel->logistic_type,
			    "records" => $advertsModel->records
			));?>
			</div><!-- /.box-body -->
			<div class="overlay meli-adverts" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div><!-- /.box -->
		
	</div>
</div>

<!-- 
<a class='update_ads_product' id='{$fetch['id']}' sku='{$fetch['sku']}'>Atualizar</a>
<a class='remove_ads_product' id='{$fetch['id']}' sku='{$fetch['sku']}'>Finalizar</a>
 -->