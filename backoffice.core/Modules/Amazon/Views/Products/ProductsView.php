<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsModel->form_msg)){ echo  $productsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="/Modules/Amazon/Products/ProductsFeed/" name="filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar Produtos do Feed</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Amazon/Products/ProductsFeed/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="prouct_id">#Id:</label> 
									<input type="text" name="prouct_id"  id='prouct_id' class="form-control input-sm" value="<?php echo $productsModel->product_id; ?>">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="sku">SKU:</label> 
									<input type="text" name="sku"  id='sku' class="form-control input-sm" value="<?php echo $productsModel->sku; ?>">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="ean">EAN:</label> 
									<input type="text" name="ean"  id='ean' class="form-control input-sm" value="<?php echo $productsModel->ean; ?>">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="az_ASIN">ASIN:</label> 
									<input type="text" name="az_ASIN"  id='az_ASIN' class="form-control input-sm" value="<?php echo $productsModel->az_ASIN; ?>">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="title">Título:</label> 
									<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $productsModel->title; ?>">
								</div>
							</div>
							
							<div class="col-md-6">
	    						<div class="form-group">
	    						<?php 
	    						$selected = $new = $match = $notMatch  = '';
									switch($productsModel->connection){
									    case "new": $new = "selected"; break;
									    case "match": $match = "selected"; break;
									    case "not_match": $notMatch = "selected"; break;
									    default : $selected = 'selected'; break;
									}
								
	    						echo "<label for='connection'>Relacionamento:</label>
	    						<select id='connection' name='connection' class='form-control input-sm connection'>
										<option value=''  {$selected} >Todos</option>
										<option value='new'  {$new} >New</option>
										<option value='match'  {$match} >Match</option>
										<option value='not_match'  {$notMatch} >Not Match</option>
								</select>";
								
								?>
								</div>
	    					</div>
	    					<div class="col-md-6">
	        					<?php  $withStock = $withouStock = '';
	        						switch($productsModel->stock){
	        						    case "withStock": $withStock = "selected"; break;
	        						    case "withouStock": $withouStock = "selected"; break;
	        						    default : $all = "selected"; break;
	        						}
	        					?>
	        					<label for="stock">Estoque:</label>
	        					<select id="stock" name="stock" class="form-control input-sm">
	            					<option value='' <?php echo $all; ?>>Todos</option>
	            					<option value='withStock' <?php echo $withStock; ?>>Com estoque</option>
	            					<option value='withouStock' <?php echo $withouStock; ?>>Sem estoque</option>
	        					</select>
	        				</div>
							<div class="col-md-6">
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
					<button type='submit' name='amazon-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="message"></div>
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class='box-tools pull-right'>
    				<div class="form-group">
    					<button class="btn btn-default btn-xs" id='match_products' ><i class='fa fa-amazon'></i> Combinar Produtos</button>&nbsp;
        	        		<button class="btn btn-default btn-xs" id='submit_products_feed' ><i class='fa fa-amazon'></i> Atualizar Feed de Produtos</button>&nbsp;
							<button class="btn btn-default btn-xs" id='submit_inventory_feed' ><i class='fa fa-amazon'></i> Atualizar Feed de Estoque</button>&nbsp;
							<button class="btn btn-default btn-xs" id='submit_price_feed' ><i class='fa fa-amazon'></i> Atualizar Feed de Preços</button>
        	       	</div>
    			</div>
			</div>
			<div class="box-body" >
			<div class='row'>
				<div class="col-sm-2">
        			<div class="form-group">
            			<select id='select_action_amazon_products' class='form-control input-sm'>
            				<option value='select' >Ações</option>
            				<option value='update_products_amazon' >Atualizar Produto do Feed</option>&nbsp;
            				<option value='delete_amazon_product' >Remover do Feed</option>
        				</select>
        			</div>
    			</div>
    			<div class='col-md-10'>
    				<div class='pull-right'>
    					<div class="form-group">
    					<button class="btn btn-default btn-xs" id='add_all_available_products' ><i class='fa fa-plus'></i> Adicionar Produtos Dísponiveis</button>
        	       		<button class="btn btn-default btn-xs" id='unmatch_products' ><i class='fa fa-minus'></i> Limpar Combinações</button>
        	       		<button class="btn btn-default btn-xs" id='unmatch_products_not_published' ><i class='fa fa-minus'></i> Limpar Combinações Não Publicadas</button>
    						
        	        	</div>
    				</div>
    			</div>
    			</div>
				<table  class="table table-bordered display" id="search-default" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
					<thead>
						<tr>
							<th><input type='checkbox' id='' class='flat-red select_all_amazon_products' /></th>
							<th>SKU/Conexão</th>
							<th>Produtos Disponíveis</th>
	                        <th>Match Amazon</ASIN</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					
					foreach ($productsFeed as $key => $productsVal){
						
						$publications = getPublicationsBySku($this->db, $this->userdata['store_id'], $productsVal['sku']);
						
						$option = $badge = $create = $update = $notMatch = $match = '';
    					switch($productsVal['connection']){
    						case 'match': $match = 'selected'; $badge = "<span class='text-green'><i class='fa fa-check pull-right'></i></span>"; break;
    						case 'not_match': $notMatch = 'selected'; $badge = "<span class='text-red'><i class='fa fa-ban pull-right'></i></span>"; break;
    						case 'update': $update = 'selected'; $badge = "<span class='text-yellow'><i class='fa fa-refresh pull-right'></i></span>"; break;
    						case 'create': $create = 'selected'; $badge = "<span class='text-blue'><i class='fa fa-plus pull-right'></i></span>"; break;
    						case 'new': $option = "<option value='new' selected>Novo</option>"; break;
    						
    					}
// 						$selectStatus = "<select  name='connection' class='btn-xs connection' product_id='{$productsVal['product_id']}'>
// 							{$option}
// 							<option value='match' {$match}>Combina</option>
// 							<option value='not_match' {$notMatch}>Não Combina</option>
// 							<option value='update' {$update}>Atualizar</option>
// 							<option value='create' {$create}>Criar</option>
// 						</select>";
    					$selectStatus = "<select  name='connection' class='btn-xs connection' product_id='{$productsVal['product_id']}'>
    					{$option}
	    					<option value='match' {$match}>Combina</option>
	    					<option value='not_match' {$notMatch}>Não Combina</option>
    					</select>";
    					
						$created = dateTimeBr($productsVal['created']);
    					$updated = dateTimeBr($productsVal['updated']);
    					$price = !empty($productsVal['sale_price']) ? $productsVal['sale_price'] : $productsVal['price'] ;
    					$cost = !empty($productsVal['cost']) ? $productsVal['cost'] : '' ;
    					
    					$thumbnail = !empty($productsVal['thumbnail']) ? "<img src='{$productsVal['thumbnail']} ' alt='Product Image' width='50px'>" : '' ; 
    					echo "<tr>
                            <td><input type='checkbox' id='{$productsVal['product_id']}' class='flat-red select_one_amazon_products' /></td>
                            <td width='10%'>{$productsVal['sku']} 
                            <div id='badge-{$productsVal['product_id']}'>{$badge}</div><br>
                            {$selectStatus}<br>{$publications}</td>
							<td width='40%'>
								<div class='product-img'>
									{$thumbnail}
	                      		</div>
								<div class='product-info'>
									<a href='/Products/Product/{$productsVal['product_id']}'>{$productsVal['title']}</a><br>
									<small><b>Custo</b>: {$cost} <b>Preço</b>: {$price} <b>Estoque</b>: {$productsVal['quantity']}</small><br>
									<small><b>Referência</b>: {$productsVal['reference']} <b>Cor</b>: {$productsVal['color']}</small><br>
									<small><b>Dimensões</b>: {$productsVal['height']} x {$productsVal['width']} x {$productsVal['length']} <b>Peso</b>: {$productsVal['weight']}</small><br>
									<small><b>EAN</b>: {$productsVal['ean']} <b>Extra</b>: {$productsVal['extra_information']}</small>
								</div>
							</td>
							<td width='40%'>";
							$asin = isset($productsVal['az_ASIN']) && !empty(trim($productsVal['az_ASIN'])) ? $productsVal['az_ASIN'] : null ;
							if(isset($asin)){
								$rank = !empty($productsVal['az_Rank']) ? $productsVal['az_Rank'] : '-';
								$amount = !empty($productsVal['az_Amount']) ? $productsVal['az_Amount'] : '-';
								$material = !empty($productsVal['az_MaterialType']) ? $productsVal['az_MaterialType'] : '-';
								$color = !empty($productsVal['az_Color']) ? $productsVal['az_Color'] : '-';
								$modelo = !empty($productsVal['az_Model']) ? $productsVal['az_Model'] : '-';
								
								echo "<ul class='products-list product-list-in-box'>
	                    			<li class='item'>
										<div class='product-img'>
	                        				<img src='{$productsVal['az_SmallImage']} ' alt='Product Image'>
	                      				</div>
	                      				<div class='product-info'>
											<a href='https://www.amazon.com.br/dp/{$productsVal['az_ASIN']}' target='_blank'>{$productsVal['az_Title']}</a><br>
											<small><b title='CurrencyCode' >Preço</b>: {$amount} - <b>Rank</b>: {$rank}</small><br>
											<small><b>Modelo</b>: {$modelo} - <b>Cor</b>: {$color} - <b>Material</b>: {$material}</small><br>
											<small><b>Dimensões</b>: {$productsVal['az_HeightPackage']} x {$productsVal['az_WidthPackage']} x {$productsVal['az_LengthPackage']} - <b>Peso</b>: {$productsVal['az_WeightPackage']}</small><br>
											<small><b>ASIN</b>: {$productsVal['az_ASIN']}</small>
										
										</div>
									</li>
								</ul>";
							}else{
									echo "<p data-toggle='tooltip' title='{$productsVal['error']}'>Not Match</p>";
							}
							
							
							echo "</td>
    						<td align='center'> 
                                <div class='dropdown'>
                                    <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                    <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                        <li role='presentation'><a class='action_amazon_product' action='disable_product_amazon' id='{$productsVal['product_id']}'> Desabilitar</a> </li>
                                        <li role='presentation'><a class='action_amazon_product' action='enable_product_amazon' id='{$productsVal['product_id']}'> Habilitar</a> </li>
                                        <li role='presentation'><a class='action_amazon_product' action='update_products_amazon' id='{$productsVal['product_id']}'> Atualizar</a> </li>                                   
                                        <li role='presentation'><a class='action_amazon_product' action='delete_amazon_product' id='{$productsVal['product_id']}'> Excluir</a> </li>   
                     
                                    </ul>
                                </div>
                			</td>
    					</tr>";
					}
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $productsModel->pagina_atual, HOME_URI."/Modules/Amazon/Products/ProductsFeed", array(
			    "product_id" => $productsModel->product_id,
			    "sku" => str_replace("%", "_x_", $productsModel->sku),
				"connection" => str_replace("%", "_x_", $productsModel->connection),
				"title" => str_replace("%", "_x_", $productsModel->title),
				"stock" => str_replace("%", "_x_", $productsModel->stock),
			    "parent_id" => str_replace("%", "_x_", $productsModel->parent_id),
			    "records" => $productsModel->records
			));?>
			</div>
			<div class="overlay amazon-products" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
            </div>
		</div>
	</div>
</div>