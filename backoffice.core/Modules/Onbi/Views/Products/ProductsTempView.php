<?php if ( ! defined('ABSPATH')) exit; ?>

<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsTempModel->form_msg)){ echo  $productsTempModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Onbi/Products/ProductsTemp/" name="filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar produtos</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
					<a href='<?php echo HOME_URI ?>/Modules/Onbi/Products/ProductsTemp/' class='btn btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
					</div>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-1">
							<div class="form-group">
								<label for="product_id">Onbi_Id:</label> 
								<input type="text" name="product_id"  id='product_id' class="form-control input-sm" value="<?php echo $productsTempModel->product_id; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="sku">SKU:</label> 
								<input type="text" name="sku"  id='sku' class="form-control input-sm" value="<?php echo $productsTempModel->sku; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="title">Título:</label> 
								<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $productsTempModel->title; ?>">
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label for="reference">Referencia:</label> 
								<input type="text" name="reference"  id='reference' class="form-control input-sm" value="<?php echo $productsTempModel->reference; ?>">
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label for="qty">Qtd.>=</label> 
								<input type="text" name="qty"  id='qty' class="form-control input-sm" value="<?php echo $productsTempModel->qty; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
							<?php 
							$configurable = $simple =  '';
							switch($productsTempModel->type){
							    case "simple": $simple = "selected"; break;
							    case "configurable": $configurable = "selected"; break;
							}
							
							?>
								<label for="type">Tipo:</label>
								<select id="type" name="type" class="form-control input-sm">
								<option value=''>Todos</option>
								<option value='simple' <?php echo $simple;?>>Simples</option>
								<option value='configurable' <?php echo $configurable; ?>>Configurável</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
							<?php 
							$enabled = $disabled =  '';
							switch($productsTempModel->available){
							    case "0": $disabled = "selected"; break;
							    case "1": $enabled = "selected"; break;
							}
							
							?>
								<label for="status">Status:</label>
								<select id="status" name="status" class="form-control input-sm">
								<option value=''>Todos</option>
								<option value='0' <?php echo $disabled;?>>Pendente</option>
								<option value='1' <?php echo $enabled; ?>>Publicado</option>
								</select>
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
							<?php 
							$select5 = $select50 = $select100 = $select150 = $select200 = '';
							switch($productsTempModel->records){
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
					<button type='submit' name='tray-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
				</div>
				<div class="overlay" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
			</form>
		</div>
	</div>
</div>
<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive no-padding">
				<div class="col-sm-2">
        			<div class="form-group">
            			<select id='select_action_onbi_products' class='form-control input-sm'>
            				<option value='select' >Ações</option>
            				<option value='update_products_onbi' >Atualizar produtos Ecommerce</option>
            				<option value='update_product_relational_onbi' >Atualizar Produtos Configuraveis Ecommerce</option>
        				
        				</select>
        			</div>
    			</div>
				<div class="col-md-12">
                    <table id='search-default' class="table table-hover ">
                    	<thead>
                    
                    	<tr>
                    		<th><input type='checkbox' id='' class='flat-red select_all_onbi_products' /></th>
                    		<th>Img</th>
                    		<th>Id/sku</th>
                    		<th>Título</th>
                    		<th>Marca</th>
                    		<th>Custo</th>
                    		<th>Preço</th>
<!--                     		<th>Preço Venda</th> -->
<!--                     		<th>Preço Promocional</th> -->
                    		<th>Barras</th>
                    		<th>status</th>
<!--                     		<th>Reference</th> -->
<!--                     		<th>Variations</th> -->
                    		<th>Qtd</th>
                    		<th>Medidas</th>
                    		<th>Atualizado</th>
                    	</tr>
                    	</thead>
                    	<tbody>
                    
                        <?php 
                        if(isset($productsTemp)){
                        	
                            foreach($productsTemp as $key => $product){
                                
                                echo "<tr>
                                		<td><input type='checkbox' id='{$product['id']}' sku='{$product['sku']}' class='flat-red select_one_onbi_products' /></td>
                                		<td><img src='{$product['image']}' style='width:50px' /></td>
                                        <td>{$product['product_id']}<br>{$product['sku']}</td>
                                        <td>{$product['title']}</td>
                                        <td>{$product['brand']}</td>
                                        <td>{$product['cost']}</td>
                                        <td>{$product['price']}</td>
                                        <td>{$product['ean']}</td>
                                        <td>{$product['status']}</td>
                                        <td>{$product['qty']}</td>
                                        <td>
                                            P.:{$product['weight']}<br>
                                            A.:{$product['height']}<br>
                                            L.:{$product['width']}<br>
                                            C.:{$product['length']}
                                        </td>
                                        <td>{$product['updated_at']}</td>
                                    </tr>";
                                
                            }
                        }
//                                         <td>{$product['sale_price']}</td>
//                                         <td>{$product['promotion_price']}</td>
//                         <td>{$product['ean']}</td>
//                         <td>{$product['hierarchy']}</td>
//                                         <td>{$product['reference']}</td>
//                                         <td>{$product['variation']}</td>
                        ?>
                        </tbody>
                    </table>
                    	<?php 
                    	
                    	pagination($totalReg, $productsTempModel->pagina_atual, HOME_URI."/Modules/Onbi/Products/ProductsTemp/", array(
                    			"product_id" => $productsTempModel->product_id,
                    			"sku" => str_replace("%", "_x_", $productsTempModel->sku),
                    			"title" => str_replace("%", "_x_", $productsTempModel->title),
                    			"reference" => str_replace("%", "_x_", $productsTempModel->reference),
                    			"type" => str_replace(" ", "_", $productsTempModel->type),
                    			"status" => str_replace(" ", "_", $productsTempModel->status),
                    			"qty" => str_replace(" ", "_", $productsTempModel->qty),
                    			"records" => $productsTempModel->records
                    	));
                    	?>
                    </div>
			</div>
			<div class="overlay" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div>
	</div>
</div>