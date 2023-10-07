<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsModel->form_msg)){ echo  $productsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="/Modules/Marketplace/Products/ManageProducts/"name="seller-filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar Produtos</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Marketplace/Products/ManageProducts/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
					<div class="col-md-6">
						<div class="col-md-4">
							<div class="form-group">
								<label for="prouct_id">#Id:</label> 
								<input type="text" name="seller_prouct_id"  class="form-control input-sm" value="<?php echo $productsModel->seller_product_id; ?>">
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<label for="ean">EAN:</label> 
								<input type="text" name="seller_ean"   class="form-control input-sm" value="<?php echo $productsModel->seller_ean; ?>">
							</div>
						</div>
					
						<div class="col-md-6">
							<div class="form-group">
								<label for="sku">SKU:</label> 
								<input type="text" name="seller_sku"  class="form-control input-sm" value="<?php echo $productsModel->seller_sku; ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="sku">Parent:</label> 
								<input type="text" name="seller_parent_id"  class="form-control input-sm" value="<?php echo $productsModel->seller_parent_id; ?>">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="title">Título:</label> 
								<input type="text" name="seller_title"  class="form-control input-sm" value="<?php echo $productsModel->seller_title; ?>">
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="col-md-4">
							<div class="form-group">
								<label for="seller_reference">Referência:</label> 
								<input type="text" name="seller_reference"  class="form-control input-sm" value="<?php echo $productsModel->seller_reference; ?>">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="seller_collection">Coleção:</label> 
								<input type="text" name="seller_collection"  class="form-control input-sm" value="<?php echo $productsModel->seller_collection; ?>">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
	                            <label>Vendedor:</label>
	                            <select class='form-control input-sm' name="seller_store_id">
	                            <option value=''> Selecione</option>
	                            <?php 
	                            	foreach ($sellers as $key => $seller){
	                               		$selected = $productsModel->seller_store_id == $seller['seller_store_id'] ? 'selected' : '' ;
	                               			echo "<option value='{$seller['seller_store_id']}' {$selected}>{$seller['store']}</option>";
	                               	}
	                               	?>
	                        	</select>
                        	</div>
                    	</div>
                    	
                    	<div class="col-md-12">
	                    	<div class="form-group">
	                            <label>Departamento:</label>
	                            <select class='form-control input-sm' name="seller_category">
	                            <option value=''> Selecione</option>
	                            <?php 
	                            	foreach ($sellerCategories as $key => $category){
	                               		$selected = $productsModel->seller_category == $category['seller_category'] ? 'selected' : '' ;
	                               			echo "<option value='{$category['seller_category']}' {$selected}>{$category['seller_category']}</option>";
	                               	}
	                               	?>
	                        	</select>
                        	</div>
                    	</div>
                    	<div class="col-md-6">
							<div class="form-group">
								<?php 
								$active = $pending = $all = '';
									switch($productsModel->status){
									    case "active": $active = "selected"; break;
									    case "pending": $pending = "selected"; break;
									    default: $all = 'selected'; break;
									}
								?>
	                            <label>Status:</label>
	                            <select class='form-control input-sm' name="status">
	                            <option value='' <?php echo $all;?> > Todos</option>
	                            <option value='active' <?php echo $active;?> > Aprovados</option>
	                            <option value='pending' <?php echo $pending;?> > Pendente</option>
	                        	</select>
                        	</div>
                    	</div>
                    	
                    	<div class="col-md-6">
							<div class="form-group">
							<?php 
								$select25 = $select50 = $select100 = $select150 = $select200 = $select2000 = '';
								switch($productsModel->records){
								    case "25": $select25 = "selected"; break;
								    case "50": $select50 = "selected"; break;
								    case "100": $select100 = "selected"; break;
								    case "150": $select150 = "selected"; break;
								    case "200": $select200 = "selected"; break;
								    case "2000": $select2000 = "selected"; break;
								}
							?>
								<label for="records">Registros:</label>
									<select id="records" name="records" class="form-control input-sm">
									<option value='25' <?php echo $select25; ?>>25</option>
									<option value='50' <?php echo $select50; ?>>50</option>
									<option value='100' <?php echo $select100; ?>>100</option>
									<option value='150' <?php echo $select150; ?>>150</option>
									<option value='200' <?php echo $select200; ?>>200</option>
									<option value='2000' <?php echo $select2000; ?>>2000</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				</div>
				<div class="box-footer">
					<button type='submit' name='marketplace-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
			</div>
			<div class="box-body" >
				<div class='row'>
					<div class="col-sm-2">
	        			<div class="form-group">
	            			<select id='select_action_marketplace_manage_products' class='form-control input-sm'>
	            				<option value='select' >Ações</option>
	            				<option value='add_available_products' >Dísponibilizar Produto</option>
	            				<option value='remove_seller_products' >Remover Produtos Selecionados</option>
	        				</select>
	        			</div>
	    			</div>
    			</div>
    			<div class='row'>
					<div class="col-md-12">
				<table  class="table table-condensed  table-striped">
					<thead>
						<tr>
							<td ><input type='checkbox' id='' class='flat-red select_all_marketplace_manage_products' /></td>
							<th>Foto</th>
							<th>Produto</th>
							<th style='text-align:center;'>Cor/Variação</th>
							<th style='text-align:center;'>Estoque</th>
							<th style='text-align:center;'>Preço</th>
						</tr>
					</thead>
					<tbody>
					<?php 
// 					pre($productsFeed);die;
					foreach ($productsFeed as $key => $productsVal){
						$created = dateTimeBr($productsVal['created']);
    					$updated = dateTimeBr($productsVal['updated']);
    					
    					$images = $urlImages = array();
    					
    					$default = $productsVal['default'];
    					
    					if(empty($default['product_id'])){
    						
	    					$urlImages = getUrlImageFromId($this->db, $productsVal['seller_store_id'], $productsVal['seller_product_id']);
	    					if(!isset($urlImages[0])){
	    						$urlImages = getUrlImageFromParentId($this->db, $productsVal['seller_store_id'], $productsVal['seller_parent_id']);
	    					}
	    					$images = count($urlImages);
	    					echo"<tr>
	                            <td colspan='2'>
	                            	<img src='{$urlImages[0]}' width='60px' />
	                            	<span class='badge small' style='vertical-align: top;margin-top: -10px;margin-left: -10px;' data-toggle='tooltip' title='Quantidade'>{$images}</span>
	                            </td>
	                            <td><a href='/Products/Product/{$productsVal['seller_product_id']}'>{$productsVal['seller_title']}</a><br>
	                            	<small class='grey'><b>Dimensões:</b> {$productsVal['seller_height']} x {$productsVal['seller_width']} x {$productsVal['seller_length']} <b>Peso</b>: {$productsVal['seller_weight']} - <b>Marca:</b> {$productsVal['seller_brand']}</small><br>
	                            	<small class='grey'><b>Loja:</b> {$productsVal['seller_store']} <b>EAN:</b> {$productsVal['seller_ean']}  <b>SKU:</b> {$productsVal['seller_sku']}</small><br>
	                            	<small class='grey'><b>Categoria:</b> {$productsVal['seller_category']}</small>
	                            </td>
	                            <td align='center'>{$productsVal['seller_color']}<br>{$productsVal['seller_variation']}</td>
	                            <td align='center'>{$productsVal['seller_quantity']}</td>
	                            <td align='center'>{$productsVal['seller_sale_price']} </td>
	                            ";
	    					
    					}else{
    						
    						$urlImages = getUrlImageFromId($this->db, $this->userdata['store_id'], $default['product_id']);
    						if(!isset($urlImages[0])){
    							$urlImages = getUrlImageFromParentId($this->db, $this->userdata['store_id'], $default['parent_id']);
    						}
    						$images = count($urlImages);
    						echo"<tr>
    						<td colspan='2'>
    						<img src='{$urlImages[0]}' width='60px' />
    						<span class='badge small' style='vertical-align: top;margin-top: -10px;margin-left: -10px;' data-toggle='tooltip' title='Quantidade'>{$images}</span>
    						</td>
    						<td><a href='/Products/Product/{$default['product_id']}'>{$default['title']}</a><br>
	    						<small class='grey'><b>Dimensões:</b> {$default['height']} x {$default['width']} x {$default['length']} <b>Peso</b>: {$default['weight']} - <b>Marca:</b> {$default['brand']}</small><br>
	    						<small class='grey'><b>Loja:</b> Marketplace <b>EAN:</b> {$default['ean']}  <b>SKU:</b> {$default['sku']}</small><br>
	    						<small class='grey'><b>Categoria:</b> {$default['category']} - <b>Coleção:</b> {$default['collection']}</small>
    						</td>
    						<td align='center'>{$default['color']}<br>{$default['variation']}</td>
    						<td align='center'>{$default['quantity']}</td>
    						<td align='center'>{$default['sale_price']} </td>
    						";
    						
    					}
    					
					  echo "<td align='center'> 
                                <div class='dropdown'>
                                    <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                    <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                        <li role='presentation'><a class='action_marketplace_product' action='update_products_marketplace' id='{$productsVal['seller_product_id']}'> Atualizar</a> </li>                                   
                                        <li role='presentation'><a class='action_marketplace_product' action='delete_marketplace_product' id='{$productsVal['seller_product_id']}'> Excluir</a> </li>   
                                    </ul>
                                </div>
                			</td>
    					</tr>";
					  
					  echo "<tr><td colspan='1'></td><td colspan='6'><table  class='table table-condensed table-hover'>";
						  if(isset($productsVal['variations'])){
						  	$variationIdAnterior = '';
						  	 
						  	foreach($productsVal['variations'] as $i =>  $variations){ 
						  		$styleStatus = 'text-yellow';
						  		$titleInfo = "Aguardando";
						  		$link= "<button class='btn btn-default btn-xs action_marketplace_product' action='add_available_products' id='{$variations['id']}'><i class='fa fa-share'></i> Cadastrar</button><br>";
						  		
						  		if(!empty($variations['default']['product_id'])){
						  			$styleStatus = 'text-green';
						  			$titleInfo = "Disponível para integração desde ".dateTimeBr($variations['published'], '/');
						  			$link = "<button class='btn btn-default btn-xs action_marketplace_product' action='update_products_marketplace' id='{$variations['id']}'><i class='fa fa-refresh'></i> Atualizar</button><br>";
						  		}
						  		
						  		echo"<tr>
						  			<td></td>
							  		<td align='center'>
		                            	<input type='checkbox' id='{$variations['id']}' class='flat-red select_one_marketplace_manage_products' />
		                            </td>
		                            <td>
		                            	<i class='fa fa-circle {$styleStatus} pull-right' data-toggle='tooltip' title='{$titleInfo}'></i>
		                            </td>
								<td>
	                            	<img src='{$urlImages[0]}' width='60px' />
	                            	<span class='badge small' style='vertical-align: top;margin-top: -10px;margin-left: -10px;' data-toggle='tooltip' title='Quantidade'>{$images}</span>
	                            </td>
						  		<td width='40%'><a href='/Products/Product/{$variations['seller_product_id']}'>{$variations['seller_title']}</a><br>
						  			<small class='grey'><b>Dimensões:</b> {$variations['seller_height']} x {$variations['seller_width']} x {$variations['seller_length']} <b>Peso</b>: {$variations['seller_weight']} - <b>Marca:</b> {$variations['seller_brand']}</small><br>
						  			<small class='grey'><b>Loja:</b> {$variations['seller_store']} <b>EAN:</b> {$variations['seller_ean']}  <b>SKU:</b> {$variations['seller_sku']}</small><br>
						  			<small class='grey'><b>Categoria:</b> {$variations['seller_category']} - <b>Coleção:</b> {$variations['seller_collection']}</small>
						  		</td>
						  		
						  		<td align='center' width='15%'>{$variations['seller_color']}<br>{$variations['seller_variation']}</td>
						  		<td align='center'>{$variations['seller_quantity']}<br>{$variations['seller_sale_price']} </td>
						  		<td align='center'>
									{$link}
							  		<button class='btn btn-default btn-xs action_marketplace_product' action='delete_marketplace_product' id='{$variations['id']}'><i class='fa fa-trash'></i> Excluir</button>
						  		</td>
						  		</tr>";
						  		 
						  	}
						  }
					  
					  echo "</table>
                    	</td></tr>";
					}
					?>	
					</tbody>
				</table>
				</div>
			</div>
			<?php 
			pagination($totalReg, $productsModel->pagina_atual, HOME_URI."/Modules/Marketplace/Products/ManageProducts", array(
			    "product_id" => $productsModel->product_id,
			    "sku" => str_replace("%", "_x_", $productsModel->sku),
				"title" => str_replace("%", "_x_", $productsModel->title),
				"category" => str_replace(" ", "_", $availableProductModel->category),
			    "parent_id" => str_replace("%", "_x_", $productsModel->parent_id),
				"seller_store_id" => str_replace("%", "_x_", $productsModel->seller_store_id),
			    "records" => $productsModel->records
			));?>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
            </div>
		</div>
	</div>
</div>