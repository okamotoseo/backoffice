<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsModel->form_msg)){ echo  $productsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Tray/Products/ManageProducts" name="filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar produtos</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Tray/Products/ManageProducts' class='btn btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-6">
								<div class="form-group">
									<label for="product_id">Id:</label> 
									<input type="text" name="product_id"  id='product_id' class="form-control input-sm" value="<?php echo $productsModel->product_id; ?>">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="id_product">Id Tray:</label> 
									<input type="text" name="id_product"  id='id_product' class="form-control input-sm" value="<?php echo $productsModel->id_product; ?>">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="parent_id">Parent:</label> 
									<input type="text" name="parent_id"  id='parent_id' class="form-control input-sm" value="<?php echo $productsModel->parent_id; ?>">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="reference">Referencia:</label> 
									<input type="text" name="reference"  id='reference' class="form-control input-sm" value="<?php echo $productsModel->reference; ?>">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-8">
								<div class="form-group">
									<label for="title">Título:</label> 
									<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $productsModel->title; ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<?php 
								$enabled = $disabled =  '';
								switch($productsModel->available){
								    case "0": $disabled = "selected"; break;
								    case "1": $enabled = "selected"; break;
								}
								
								?>
									<label for="available">Status:</label>
									<select id="available" name="available" class="form-control input-sm">
									<option value=''>Todos</option>
									<option value='0' <?php echo $disabled;?>>Pendente</option>
									<option value='1' <?php echo $enabled; ?>>Publicado</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="stock">Estoque >=</label> 
									<input type="text" name="stock"  id='stock' class="form-control input-sm" value="<?php echo $productsModel->stock; ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<?php 
								$yes = $no =  '';
								switch($productsModel->images){
								    case "0": $yes = "selected"; break;
								    case "1": $no = "selected"; break;
								}
								
								?>
									<label for="images">Fotos:</label>
									<select id="images" name="images" class="form-control input-sm">
									<option value=''>Todos</option>
									<option value='0' <?php echo $yes; ?>>Não</option>
									<option value='1' <?php echo $no; ?>>Sim</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
								<?php 
								$select5 = $select50 = $select100 = $select150 = $select200 = '';
								switch($productsModel->records){
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
				</div>
				<div class="box-footer">
					<a id='generate_products_tray_csv' class='btn btn-default btn-xs' >Download CSV <i class='fa fa-file-o'></i></a>
					<button type='submit' name='tray-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message">
		
		</div>
		
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class='box-tools pull-right'>
					<a id='update_product_information_tray'  class='btn btn-default btn-xs'><i class='fa fa-info'></i>  Atualizar Informações</a>
	        		<a id='update_stock_price_tray'  class='btn btn-default btn-xs'><i class='fa fa-refresh'></i>  Atualiza Estoque e Preço</a>
				</div>
			</div>
			<div class="box-body no-padding" >
				<div class="col-sm-2">
        			<div class="form-group">
            			<select id='select_action_tray_products' class='form-control input-sm'>
            				<option value='select' >Ações</option>
            				<option value='update_products_tray' >Atualizar Produtos</option>
            				<option value='update_attributes_product_tray' >Atualizar Características</option>
            				<option value='update_variations_product_tray' >Atualizar Variações</option>
            				<option value='disabled_products_tray' >Desativar Produtos</option>
            				<option value='remove_products_tray' >Excluir Produtos</option>
        				
        				</select>
        			</div>
    			</div>
				<table  class="table table-condensed table-hover display" id="search-default" width="100%" >
					<thead>
						<tr>
						<th><input type='checkbox' id='' class='flat-red select_all_tray_products' /></th>
							<th>IdTray/Id</th>
							<th>Foto</th>
							<th>Produto</th>
							<th>Status</th>
							<th>Qtd.</th>
							<th>Criado/Atualizado</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($products as $fetch): 
// 					
                        
					
					   $status = $fetch['available'] > 0 ? "<strong class='text-green'>Publicado</strong>" : "<strong class='text-red'>Pendente</strong>";
    				    $created = dateTimeBr($fetch['created']);
    					$updated = dateTimeBr($fetch['updated']);
    					$titleLink = $fetch['title'];
    					echo "<tr id='{$fetch['id']}'>
                            <td><input type='checkbox' id='{$fetch['id']}' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}' class='flat-red select_one_tray_products' /></td>
                            <td>{$fetch['id_product']}<br>{$fetch['product_id']}
                            </td>
                            <td >
                            	<span class='badge small' style='vertical-align: top;margin-top: -10px;margin-left: -10px;' data-toggle='tooltip' title='Quantidade de fotos'>{$fetch['images']}</span>
                            	<img src='{$fetch['thumbs']}' width='60px'/>
                            </td>
                            <td>
                            	<a href='{$fetch['url']}' target='_blank'>{$fetch['title']}</a><br>
	                            <small><strong>SKU:</strong><u>{$fetch['sku']}</u> <strong>Parent:</strong><u>{$fetch['parent_id']}</u></small><br>
	                            <small>P:{$fetch['weight']} A:{$fetch['height']} L:{$fetch['width']} C:{$fetch['length']}</small>
                            </td>
                            <td>{$status}</td>
                            <td align='center'>{$fetch['stock']}</td>
                            <td><small>{$created}</small><br><small>{$updated}</small></td>
    						<td> 
                                <div class='dropdown'>
                                    <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                    <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                        <li role='presentation'><a href='/Products/Product/{$fetch['product_id']}' target='_blank' ><i class='fa fa-external-link'></i>Editar</a></li>
                                        <li role='presentation'><a class='action_tray_product' action='update_stock_price_product_tray' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Preço e Estoque</a> </li>                                   
                                        <li role='presentation'><a class='action_tray_product' action='update_product_tray' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Atualizar Produtos</a></li>
                                        <li role='presentation'><a class='action_tray_product' action='update_product_image_tray' product_id='{$fetch['product_id']}' id_product='{$fetch['id_product']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Atualizar Fotos</a></li>
                                        <li role='presentation'><a class='action_tray_product' action='update_product_variations_tray' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Atualizar Variações</a> </li>
                                        <li role='presentation'><a class='action_tray_product' action='update_attributes_product_tray' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Atualizar Características</a> </li>
                                        <li role='presentation'><a class='action_tray_product' action='delete_tray_product' product_id='{$fetch['product_id']}' id_product='{$fetch['id_product']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-trash'></i> Excluir</a> </li>   
                     
                                    </ul>
                                </div>
                                <img class='ajaxload-{$fetch['id']}' src='".HOME_URI."/Views/_uploads/images/facebook-ajax-loader.gif' style='display:none;'>                               
                			</td>
    						</tr>";
             
		             endforeach;
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $productsModel->pagina_atual, HOME_URI."/Modules/Tray/Products/ManageProducts/", array(
			    "id" => $productsModel->id,
			    "parent_id" => str_replace("%", "_x_", $productsModel->parent_id),
			    "product_id" => str_replace("%", "_x_", $productsModel->product_id),
			    "id_product" => str_replace("%", "_x_", $productsModel->id_product),
			    "images" => str_replace("%", "_x_", $productsModel->images),
			    "available" => str_replace("%", "_x_", $productsModel->available),
			    "title" => str_replace("%", "_x_", $productsModel->title),
			    "stock" => str_replace("%", "_x_", $productsModel->stock),
			    "ean" => str_replace("%", "_x_", $productsModel->ean),
			    "brand" => str_replace("%", "_x_", $productsModel->brand),
			    "collection" => str_replace("%", "_x_", $productsModel->collection),
			    "records" => $productsModel->records
			));?>
			</div><!-- /.box-body -->
			<div class="overlay tray-products" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div><!-- /.box -->
	</div>
</div>

