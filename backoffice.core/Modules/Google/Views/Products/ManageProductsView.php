<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsModel->form_msg)){ echo  $productsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Google/Products/ManageProducts" name="filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar produtos importados do XML</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Google/Products/ManageProducts/' class='btn btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-2">
								<div class="form-group">
									<label for="product_id">Id:</label> 
									<input type="text" name="product_id"  id='product_id' class="form-control input-sm" value="<?php echo $productsModel->product_id; ?>">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="item_group_id">Parent:</label> 
									<input type="text" name="item_group_id"  id='item_group_id' class="form-control input-sm" value="<?php echo $productsModel->item_group_id; ?>">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="npm">npm:</label> 
									<input type="text" name="npm"  id='npm' class="form-control input-sm" value="<?php echo $productsModel->npm; ?>">
								</div>
							</div>
				
							<div class="col-md-4">
								<div class="form-group">
									<label for="title">Título:</label> 
									<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $productsModel->title; ?>">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type='submit' name='google-products-filter' class='btn btn-primary btn-xs pull-right' ><i class='fa fa-search'></i> Procurar</button>
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
					<a id='copy_google_xml'  class='btn btn-default btn-xs'><i class='fa fa-cloud-download'></i>  Copiar XML</a>
					<a id='import_google_xml_products'  class='btn btn-default btn-xs'><i class='fa fa-database'></i>  Importar Produtos do XML</a>
				</div>
			</div>
			<div class="box-body no-padding" >
				<div class="col-sm-2">
        			<div class="form-group">
            		
        			</div>
    			</div>
				<table  class="table table-condensed table-hover display" id="search-default" width="100%" >
					<thead>
						<tr>
						<th><input type='checkbox' id='' class='flat-red select_all_google_products' /></th>
							
							<th>Foto</th>
							<th>Titulo/Categoria</th>
							<th>Id</th>
							<th>GroupId</th>
							<th>Variação/Cor</th>
							<th>Peso</th>
							<th>P.Venda</th>
							<th>Gtin</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($products as $fetch){
 					
    					echo "<tr id='{$fetch['id']}'>
                                <td><input type='checkbox' id='{$fetch['id']}'  item_group_id='{$fetch['item_group_id']}' class='flat-red select_one_google_products' /></td>
                                <td>
                                	<span class='badge small' style='vertical-align: top;margin-top: -10px;margin-left: -10px;' data-toggle='tooltip' title='Quantidade de fotos'>{$fetch['images']}</span>
                                	<img src='{$fetch['image_link']}' width='60px'/>
                                </td>
                                <td>
                                	<a href='{$fetch['link']}' target='_blank'>{$fetch['title']}</a><br>
                                    <small><strong>Gênero: </strong><u>{$fetch['gender']}</u><strong> Grupo: </strong><u>{$fetch['age_group']}</u></small><br>
                                    <small><strong>Category:</strong><u>{$fetch['product_type']}</u></small>
                                </td>
                                <td>{$fetch['id']}</td>
                                <td>{$fetch['item_group_id']}</td>
                                <td>{$fetch['size']}<br>{$fetch['color']}</td>
                                <td>{$fetch['shipping_weight']}</td>
                                <td align='center'>{$fetch['sale_price']}</td>
                                <td align='center'>{$fetch['gtin']}</td>
    						</tr>";
					}
					
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $productsModel->pagina_atual, HOME_URI."/Modules/Google/Products/ManageProducts/", array(
			    "id" => $productsModel->id,
			    "item_group_id" => str_replace("%", "_x_", $productsModel->item_group_id),
			    "title" => str_replace("%", "_x_", $productsModel->title),
			    "npm" => str_replace("%", "_x_", $productsModel->npm),
			    "gtin" => str_replace("%", "_x_", $productsModel->gtin),
			    "brand" => str_replace("%", "_x_", $productsModel->brand),
			    "color" => str_replace("%", "_x_", $productsModel->color),
			    "records" => $productsModel->records
			));?>
			</div><!-- /.box-body -->
			<div class="overlay google-products" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div><!-- /.box -->
	</div>
</div>

