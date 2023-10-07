<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $productsModel->form_msg)){ echo  $productsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="/Modules/Marketplace/Products/Products/"name="seller-filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar Produtos</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Modules/Marketplace/Products/Products/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-1">
							<div class="form-group">
								<label for="prouct_id">#Id:</label> 
								<input type="text" name="seller_prouct_id"  id='prouct_id' class="form-control input-sm" value="<?php echo $productsModel->seller_product_id; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="ean">EAN:</label> 
								<input type="text" name="seller_ean"  id='ean' class="form-control input-sm" value="<?php echo $productsModel->seller_ean; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="sku">SKU:</label> 
								<input type="text"name="seller_sku"  id='sku' class="form-control input-sm" value="<?php echo $productsModel->seller_sku; ?>">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="title">Título:</label> 
								<input type="text"name="seller_title"  id='title' class="form-control input-sm" value="<?php echo $productsModel->seller_title; ?>">
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
					<button type='submit' name='marketplace-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
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
			</div>
			<div class="box-body" >
			<div class='row'>
				<div class="col-sm-2">
        			<div class="form-group">
            			<select id='select_action_marketplace_products' class='form-control input-sm'>
            				<option value='select' >Ações</option>
            				<option value='update_products_marketplace' >Atualizar Produto do Marketplace</option>&nbsp;
            				<option value='delete_marketplace_product' >Remover do Marketplace</option>
        				</select>
        			</div>
    			</div>
    			<div class='col-md-10'>
    				<div class='pull-right'>
    					<div class="form-group">
    						<button class="btn btn-default btn-xs" id='add_all_available_products' ><i class='fa fa-plus'></i> Adicionar Produtos Dísponiveis</button>
        	        	</div>
    				</div>
    			</div>
    		</div>
    			
				<table class="table table-condensed no-padding" id="search-default" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
					<thead>
						<tr>
							<th><input type='checkbox' id='' class='flat-red select_all_marketplace_manage_products' /></th>
							<th>EAN</th>
							<th>Título</th>
							<th>Cor</th>
							<th>Marca</th>
							<th>Qtd</th>
							<th>P.Venda</th>
							<th>Dimensões/Peso</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					if(isset($productsFeed)){
    					foreach ($productsFeed as $key => $productsVal){
    						
    						$created = dateTimeBr($productsVal['created']);
        					$updated = dateTimeBr($productsVal['updated']);
        					
        					echo "<tr>
                                <td><input type='checkbox' id='{$productsVal['id']}' class='flat-red select_one_marketplace_manage_products' /></td>
                                <td>{$productsVal['ean']}</td>
                                <td><a href='/Products/Product/{$productsVal['seller_product_id']}'>{$productsVal['title']}</a></td>
                                <td>{$productsVal['color']}</td>
                                <td>{$productsVal['brand']}</td>
                                <td align='center'>{$productsVal['quantity']}</td>
                                <td>{$productsVal['sale_price']}</td>
                                <td><b>Dimensões</b>: {$productsVal['height']} x {$productsVal['width']} x {$productsVal['length']} - <b>Peso</b>: {$productsVal['weight']}</td>";
    					  echo "<td align='center'> 
                                    <div class='dropdown'>
                                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                        <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                            <li role='presentation'><a class='action_marketplace_product' action='update_products_marketplace' id='{$productsVal['id']}'> Atualizar</a> </li>                                   
                                            <li role='presentation'><a class='action_marketplace_product' action='delete_marketplace_product' id='{$productsVal['id']}'> Excluir</a> </li>   
                         
                                        </ul>
                                    </div>
                    			</td>
        					</tr>";
    					}
					}
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $productsModel->pagina_atual, HOME_URI."/Modules/Marketplace/Products/Products", array(
			    "seller_product_id" => $productsModel->seller_product_id,
			    "sku" => str_replace("%", "_x_", $productsModel->sku),
				"title" => str_replace("%", "_x_", $productsModel->title),
			    "parent_id" => str_replace("%", "_x_", $productsModel->parent_id),
			    "records" => $productsModel->records
			));?>
			</div>
			<div class="overlay" style='display:none;'>
            	<i class="fa fa-refresh fa-spin"></i>
            </div>
		</div>
	</div>
</div>