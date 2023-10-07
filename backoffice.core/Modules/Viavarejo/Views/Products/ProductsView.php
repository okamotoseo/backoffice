<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsModel->form_msg)){ echo  $productsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="/Modules/Viavarejo/Products/ManageProducts/" name="filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar anúncios</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Viavarejo/Products/ManageProducts/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label for="id">id:</label> 
								<input type="text" name="id"  id='id' class="form-control input-sm" value="<?php echo $productsModel->id; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="product_id">Id Produto:</label> 
								<input type="text" name="product_id"  id='product_id' class="form-control input-sm" value="<?php echo $productsModel->product_id; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="sku">SKU:</label> 
								<input type="text" name="sku"  id='sku' class="form-control input-sm" value="<?php echo $productsModel->sku; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="prouct_id">ProductId:</label> 
								<input type="text" name="prouct_id"  id='prouct_id' class="form-control input-sm" value="<?php echo $productsModel->product_id; ?>">
							</div>
						</div>
						
						<div class="col-md-2">
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
				<div class="box-footer">
					<button type='submit' name='viavarejo-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
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
    					<div class="form-group">
    						<button id='get_loads_products' class="btn btn-default btn-xs" ><i class='fa fa-bug'></i>  Resultado Produtos Enviados</button>&nbsp;
    						<button id='update_seller_items' class="btn btn-default btn-xs" ><i class='fa fa-download'></i>  Importar Items</button>&nbsp;
        	        		<button id='update_stock_price' class="btn btn-default btn-xs"><i class='fa fa-refresh'></i>  Atualiza Estoque e Preço</button>
        	        	</div>
    				</div>
			</div>
			<div class="box-body" >
				<div class='row'>
				<div class="col-sm-2">
        			<div class="form-group">
            			<select id='select_action_viavarejo_products' class='form-control input-sm'>
            				<option value='select' >Ações</option>
            				<option value='update_products_viavarejo' >Atualizar Produtos</option>
            				<option value='disabled_products_viavarejo' >Desativar Produtos</option>
            				<option value='remove_products_viavarejo' >Excluir Produtos</option>
        				
        				</select>
        			</div>
    			</div>
    			</div>
				<table  class="table table-codensed table-hover display" id="search-default  width="100%">
					<thead>
						<tr>
						<th><input type='checkbox' id='' class='flat-red select_all_viavarejo_products' /></th>
							<th>SKU</th>
							<th>Produto</th>
							<th style='text-align:center;'>Variação</th>
							<th>Qtd.</th>
							<th>Criado</th>
							<th>Atualizado</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($products as $fetch): 
// 					
    					$created = dateTimeBr($fetch['created']);
    					$updated = dateTimeBr($fetch['updated']);
    					echo "<tr id='{$fetch['id']}'>
                            <td><input type='checkbox' id='{$fetch['id']}' product_id='{$fetch['product_id']}' sku='{$fetch['sku']}' class='flat-red select_one_viavarejo_products' /></td>
    						<td>{$fetch['sku']}</td>
    						<td>{$fetch['title']}<br><small>{$fetch['brand']}</small></td>
                            <td align='center'>{$fetch['variation']}<br>{$fetch['color']}</td>
                            <td>{$fetch['quantity']}</td>
                            <td>{$created}</td>
                            <td>{$updated}</td>
    						<td> 
                                <div class='dropdown'>
                                    <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                    <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                        <li role='presentation'><a class='action_viavarejo_product' action='disable_product_viavarejo' product_id='{$fetch['product_id']}' sku='{$fetch['sku']}'> Desabilitar</a> </li>
                                        <li role='presentation'><a class='action_viavarejo_product' action='enable_product_viavarejo' product_id='{$fetch['product_id']}' sku='{$fetch['sku']}'> Habilitar</a> </li>
                                        <li role='presentation'><a class='action_viavarejo_product' action='update_products_viavarejo' product_id='{$fetch['product_id']}' sku='{$fetch['sku']}'> Atualizar</a> </li>  
                                        <li role='presentation'><a class='action_viavarejo_product' action='get_seller_items_by_sku' product_id='{$fetch['product_id']}' sku='{$fetch['sku']}'> Detalhes</a> </li>
                                        <li role='presentation'><a class='action_viavarejo_product' action='delete_viavarejo_product' id='{$fetch['id']}' sku='{$fetch['sku']}'> Excluir</a> </li>   
                     
                                    </ul>
                                </div>
                			</td>
    						</tr>";
             
		             endforeach;
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $productsModel->pagina_atual, HOME_URI."/Modules/Viavarejo/Products/ManageProducts", array(
			    "id" => $productsModel->id,
			    "sku" => str_replace("%", "_x_", $productsModel->sku),
			    "parent_id" => str_replace("%", "_x_", $productsModel->parent_id),
			    "product_id" => str_replace("%", "_x_", $productsModel->product_id),
			    "records" => $productsModel->records
			));?>
			</div><!-- /.box-body -->
			<div class="overlay viavarejo-products" style='display:none;'>
                        		<i class="fa fa-refresh fa-spin"></i>
                    		</div>
		</div><!-- /.box -->
	</div>
</div>

