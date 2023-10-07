<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsModel->form_msg)){ echo  $productsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Shopee/Products/ManageProducts" name="filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar produtos</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Shopee/Products/ManageProducts/' class='btn btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
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
									<label for="parent_id">Parent:</label> 
									<input type="text" name="parent_id"  id='parent_id' class="form-control input-sm" value="<?php echo $productsModel->parent_id; ?>">
								</div>
							</div>
						</div>
						<div class="col-md-6">
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
					<a id='generate_products_shopee_csv' class='btn btn-default btn-xs' >Download CSV <i class='fa fa-file-o'></i></a>
					<button type='submit' name='shopee-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
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
	        		<a href='/Modules/Shopee/Products/ExportProducts/'  id='export_products'  class='btn btn-default btn-xs'><i class='fa fa-file-o'></i>  Exportar Produtos </a>
	        		<a href='/Modules/Shopee/Products/UpdateProducts/'  class='btn btn-default btn-xs'><i class='fa fa-refresh'></i>  Atualizar Informações</a>
				</div>
			</div>
			<div class="box-body no-padding" >
				<table  class="table table-condensed table-hover display" id="search-default" width="100%" >
					<thead>
						<tr>
						<th><input type='checkbox' id='' class='flat-red select_all_shopee_products' /></th>
							
							<th>Foto</th>
							<th>Produto</th>
							<th>Sku</th>
							<th>ParentId</th>
							<th>Variação/Cor</th>
							<th>Status</th>
							<th>Preço</th>
							<th>Qtd.</th>
							<th>Criado</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					if(isset($products)){
    					foreach ($products as $fetch): 
    					   $status = $fetch['published'] == 'T' ? "<strong class='text-green'>Publicado</strong>" : "<strong class='text-red'>Pendente</strong>";
        				    $created = dateTimeBr($fetch['created']);
        					$updated = dateTimeBr($fetch['updated']);
        					$titleLink = $fetch['title'];
        					echo "<tr id='{$fetch['id']}'>
                                <td><input type='checkbox' id='{$fetch['id']}' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}' class='flat-red select_one_shopee_products' /></td>
                                <td>
                                	<span class='badge small' style='vertical-align: top;margin-top: -10px;margin-left: -10px;' data-toggle='tooltip' title='Quantidade de fotos'>{$fetch['images']}</span>
                                	<img src='{$fetch['thumbnail']}' width='60px'/>
                                </td>
                                <td>
                                	<a href='{$fetch['url']}' target='_blank'>{$fetch['title']}</a><br>
    	                            <small><strong>Categoria:</strong><u>{$fetch['category']}</u></small><br>
    	                            <small><strong>P:</strong>{$fetch['weight']} <strong>A:</strong>{$fetch['height']} <strong>L:</strong>{$fetch['width']} <strong>C:</strong>{$fetch['length']}</small>
                                </td>
                                <td>{$fetch['sku']}</td>
                                <td>{$fetch['parent_id']}</td>
                                <td>{$fetch['variation']}<br>{$fetch['color']}</td>
                                <td>{$status}</td>
                                <td align='center'>{$fetch['sale_price']}</td>
                                <td align='center'>{$fetch['quantity']}</td>
                                <td><small>{$created}</small></td>
        						</tr>";
                 
    		             endforeach;
					}
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $productsModel->pagina_atual, HOME_URI."/Modules/Shopee/Products/ManageProducts/", array(
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
			<div class="overlay shopee-products" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div><!-- /.box -->
	</div>
</div>

