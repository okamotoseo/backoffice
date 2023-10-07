<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsTempModel->form_msg)){ echo  $productsTempModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="/Modules/Magento2/Products/ProductsTemp/" name="magento-products-filter" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar produtos</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Magento2/Products/ProductsTemp/' class='btn btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-1">
							<div class="form-group">
								<label for="product_id">Id:</label> 
								<input type="text" name="product_id"  id='product_id' class="form-control input-sm" value="<?php echo $productsTempModel->product_id; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="sku">SKU</label> 
								<input type="text" name="sku"  id='sku' class="form-control input-sm" value="<?php echo $productsTempModel->sku; ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="title">Título:</label> 
								<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $productsTempModel->title; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
							<?php 
							$enabled = $disabled =  '';
							switch($productsTempModel->status){
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
			
						<div class="col-md-2">
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
				<div class="overlay" style='display:none;'>
	            	<i class="fa fa-refresh fa-spin"></i>
	        	</div>
				<div class="box-footer">
					<button type='submit' name='magento-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
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
		       	<div class='box-tools pull-right'>
		       	<div class='dropdown'>
		            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
		            <ul class='dropdown-menu pull-right' >
				            <li role='presentation'><a href='/Modules/Magento2/Products/ProductsTemp/'><i class="fa fa-repeat"></i> Atualizar</a></li>
	                    </ul>
                    </div>
	        	</div>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive no-padding">
				<div class="col-sm-2">
        			<div class="form-group">
            			<select id='select_action_mg2_products' class='form-control input-sm'>
            				<option value='select' >Ações</option>
            				<option value='update_products_mg2' >Atualizar produtos Ecommerce</option>
            				<option value='remove_products_mg2' >Excluir Produtos Selecionados</option>
        				
        				</select>
        			</div>
    			</div>
                    <table class="table table-hover ">
                    	<thead>
                    
                    	<tr>
                    		<th><input type='checkbox' id='' class='flat-red select_all_mg2_products' /></th>
                    		<th># / sku</th>
                    		<th>Título</th>
                    		<th>Preço</th>
                    		<th>Barras</th>
                    		<th>Qtd</th>
                    		<th>Publicado/Atualizado</th>
                    	</tr>
                    	</thead>
                    	<tbody>
                    
                        <?php 
                        if(isset($productsTemp)){
                            foreach($productsTemp as $key => $product){
                                
                                echo "<tr>
                                		<td><input type='checkbox' product_id='{$product['product_id']}' sku='{$product['sku']}' class='flat-red select_one_mg2_products' /></td>
                                        <td>{$product['product_id']} / {$product['sku']}</td>
                                        <td>{$product['title']}
                                        </td>
                                        <td>{$product['price']}</td>
                                        <td>{$product['ean']}</td>
                                        <td>{$product['qty']}</td>
                                        <td>{$product['created_at']}<br>
                                        {$product['updated_at']}</td>
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
                    	pagination($totalReg, $productsTempModel->pagina_atual, HOME_URI."/Modules/Magento2/Products/ProductsTempView", array(
                    		"id" => $productsTempModel->id,
                    		"sku" => str_replace("%", "_x_", $productsTempModel->sku),
                    		"title" => str_replace("%", "_x_", $productsTempModel->title),
                    		"records" => $productsTempModel->records
                    ));
                    ?>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
        	</div>
		</div>
	</div>
</div>