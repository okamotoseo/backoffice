<?php if ( ! defined('ABSPATH')) exit; ?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $availableProductModel->form_msg)){ echo  $availableProductModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="/Products/AvailableProducts/" name="filter-product" >
			
			<input type="hidden" name="marketplace"  id='marketplace' value="mercadolivre">
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar produtos</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='/Products/AvailableProducts/' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">

    					<div class="col-md-4">
        					<div class="form-group">
        						<div class="col-md-12">
        							<label for="id">ProductId:</label> 
        							<input type="text" name="id"  id='id' class="form-control input-sm" value="<?php echo $availableProductModel->id; ?>">
        						</div>
        						<div class="col-md-12">
        							<label for="sku">SKU:</label> 
        							<input type="text" name="sku"  id='sku' class="form-control input-sm" value="<?php echo $availableProductModel->sku; ?>">
        						</div>
        						<div class="col-md-12">
        							<label for="parent_id">Parent:</label> 
        							<input type="text" name="parent_id"  id='parent_id' class="form-control input-sm" value="<?php echo $availableProductModel->parent_id; ?>">
        						</div>
        						<div class="col-md-12">
        							<label for="ean">EAN:</label> 
        							<input type="text" name="ean"  id='ean' class="form-control input-sm" value="<?php echo $availableProductModel->ean; ?>">
        						</div>
        					</div>
    					</div>
    					<div class="col-md-4">
        					<div class="form-group">
        						<div class="col-md-6">
        							<label for="reference">Referência:</label> 
        							<input type="text" name="reference"  id='reference' class="form-control input-sm" value="<?php echo $availableProductModel->reference; ?>">
        						</div>
        						<div class="col-md-6">
        							<label for="collection">Coleção:</label> 
        							<input type="text" name="collection"  id='collection' class="form-control input-sm" value="<?php echo $availableProductModel->collection; ?>">
        						</div>
        						<div class="col-md-12">
        							<label for="title">Título:</label> 
        							<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $availableProductModel->title; ?>">
        						</div>
        						
        						<div class="col-md-12">
        							<label for="category">Departamento:</label>
        							<select name="category" id='category' class="form-control input-sm">
        								<option value='' selected>Todas</option>
                                        <?php 
                                        foreach($listCategoriesByProducts as $key => $category){
                                            $selected = $category == $availableProductModel->category ? "selected" : "" ;
                                            echo "<option value='{$category}' {$selected}>{$category}</option>";
                                        }
                                        
                                        ?>
        							</select>
        						</div>
        						<div class="col-md-12">
        							<label for="brand">Marca:</label>
        							<select id="brand" name="brand" class="form-control input-sm">
            							<option value=''>Todas</option>
            							<?php 
                                        foreach($listBrands as $key => $brand){
                                        		$selected = $brand['brand'] == $availableProductModel->brand ? "selected" : "" ;
                                                echo "<option value='{$brand['brand']}' {$selected}>{$brand['brand']}</option>";
                                        }  ?>
        							</select>
        						</div>
    						</div>
    					</div>
    					<div class="col-md-4">
        					<div class="form-group">
        						<div class="col-md-12">
        							<?php $notPublishedSkyhub = $skyhub = $ecommerce = $notPublishedEcommerce = $notPublishedMeli = $mercadolivre = $onbi = $all = $notPublished = '';
        							switch($availableProductModel->marketplace){
        							    case "mercadolivre": $mercadolivre = "selected"; break;
        							    case "onbi": $ecommerce = "selected"; break;
        							    case "ecommerce": $ecommerce = "selected"; break;
        							    case "skyhub": $skyhub = "selected"; break;
        							    case "all": $all = "selected"; break;
        							    case "not_published": $notPublished = "selected"; break;
        							    case "not_published_meli": $notPublishedMeli = "selected"; break;
        							    case "not_published_ecommerce": $notPublishedEcommerce = "selected"; break;
        							    case "not_published_skyhub": $notPublishedSkyhub = "selected"; break;
        							    default : $all = "selected"; break;
        							}?>
        							<label for="marketplace">Marketplaces:</label>
        							<select id="marketplace" name="marketplace" class="form-control input-sm">
            							<option value='all' <?php echo $all; ?>>Todos</option>
            							<option value='not_published' <?php echo $notPublished; ?>>Não Publicados</option>
            							<option value='mercadolivre' <?php echo $mercadolivre; ?>>Mercadolivre</option>
            							<option value='not_published_meli' <?php echo $notPublishedMeli; ?>>Não Publicados Mercadolivre</option>
            							<option value='ecommerce' <?php echo $ecommerce; ?>>Ecommerce</option>
            							<option value='not_published_ecommerce' <?php echo $notPublishedEcommerce; ?>>Não Publicados Ecommerce</option>
            							<option value='skyhub' <?php echo $skyhub; ?>>B2W</option>
            							<option value='not_published_skyhub' <?php echo $notPublishedSkyhub; ?>>Não Publicados B2W</option>
        							</select>
        						</div>
        						<div class="col-md-6">
        							<?php  $withStock = $withouStock = '';
        							switch($availableProductModel->stock){
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
        							<?php  $withImage = $withouImage = '';
        							switch($availableProductModel->thumbnail){
        							    case "withImage": $withImage = "selected"; break;
        							    case "withouImage": $withouImage = "selected"; break;
        							    default : $all = "selected"; break;
        							}
        							?>
        							<label for="thumbnail">Foto:</label>
        							<select id="thumbnail" name="thumbnail" class="form-control input-sm">
            							<option value='' <?php echo $all; ?>>Todos</option>
            							<option value='withImage' <?php echo $withImage; ?>>Com foto</option>
            							<option value='withouImage' <?php echo $withouImage; ?>>Sem foto</option>
        							</select>
        						</div>
        						<div class="col-md-12">
        							<?php $yes = $no  = '';
        							switch($availableProductModel->blocked){
        							    case "t": $yes = "selected"; break;
        							    case "f": $no = "selected"; break;
        							    default : $all = "selected"; break;
        							} ?>
        							<label for="blocked">Bloqueado:</label>
        							<select id="blocked" name="blocked" class="form-control input-sm">
            							<option value='' <?php echo $all; ?>>Todos</option>
            							<option value='t' <?php echo $yes; ?>>Sim</option>
            							<option value='f' <?php echo $no; ?>>Não</option>
        							</select>
        						</div>
        						<div class="col-md-6">
        							<?php  $groupEan = $groupBrand = $groupColor = $groupReference = $groupParentId = '';
        							switch($availableProductModel->group_by){
        							    case "parent_id": $groupParentId = "selected"; break;
        							    case "reference": $groupReference = "selected"; break;
        							    case "color": $groupColor = "selected"; break;
        							    case "brand": $groupBrand = "selected"; break;
        							    case "ean": $groupEan = "selected"; break;
        							    
        							}?>
        							<label for="group_by">Agrupar por:</label>
        							<select id="group_by" name="group_by" class="form-control input-sm">
            							<option value=''> </option>
            							<option value='parent_id' <?php echo $groupParentId; ?>>Parent</option>
            							<option value='reference' <?php echo $groupReference; ?>>Referência</option>
            							<option value='color' <?php echo $groupColor; ?>>Cor</option>
            							<option value='brand' <?php echo $groupBrand; ?>>Marca</option>
            							<option value='ean' <?php echo $groupEan; ?>>Ean</option>
        							</select>
        						</div>
        						<div class="col-md-6">
        							<?php  $select5 = $select50 = $select100 = $select150 = $select200 = $select2000 = '';
        							switch($availableProductModel->records){
        							    case "5": $select5 = "selected"; break;
        							    case "50": $select50 = "selected"; break;
        							    case "100": $select100 = "selected"; break;
        							    case "150": $select150 = "selected"; break;
        							    case "200": $select200 = "selected"; break;
        							    case "2000": $select2000 = "selected"; break;
        							}?>
        							<label for="records">Registros:</label>
        							<select id="records" name="records" class="form-control input-sm">
            							<option value='5' <?php echo $select5; ?>>5</option>
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
    				<div class="form-group">
            	        <a id='export_csv' report_type='available_products' class='btn btn-default btn-xs' >Download CSV <i class='fa fa-file-o'></i></a>
    					<button type='submit' name='available-products-filter' class='btn btn-primary pull-right btn-sm submit-load'><i class='fa fa-search'></i> Filtrar</button>
    				</div>
				</div>
				<div class="overlay available-products" style='display:none;'>
            		<i class="fa fa-refresh fa-spin"></i>
        		</div>
			</form>
		</div>
	</div>
</div>
<div class='row'>
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body table-responsive">
				<div class='message'></div>
				<div class="col-md-2">
        			<div class="form-group">
        			<?php 
//         		 	<option value='update_products_{$ecommerce}' >Atualizar produtos Ecommerce</option>
//         			pre($this->moduledata);die;
        			$ecommerce = isset($this->moduledata['Ecommerce'][0]) ? strtolower(trim($this->moduledata['Ecommerce'][0])) : 'ecommerce';
        			$delete = '';
        			if($developer OR $this->userdata['email'] == 'design.fanlux@gmail.com'){
        				$delete = "<option value='remove_products_skyhub' >Excluir Skyhub</option>
						<option value='delete_available_product'> Excluir Produto Sysplace</option>";
        			}
        			 
        			?>
            			<select id='select_action_available_products' class='form-control input-sm'>
            				<option id='selected' value='select' selected>Ações</option>
            				<option value='send_products_mercadolivre' >Enviar Mercadolivre</option>
            				<option value='send_products_skyhub' >Enviar Skyhub</option>
            				<option value='send_products_viavarejo' >Enviar Viavarejo</option>
            				<option value='send_products_amazon' >Enviar Amazon</option>
            				<?php if($this->userdata['store_id'] != 7){ ?>
            				<option value='send_products_marketplace' >Enviar Marketplace</option>
            				<?php }?>
            				<option value='send_products_<?= $ecommerce; ?>' >Enviar Ecommerce</option>
            				<option value='update_category' >Atualizar Categoria</option>
            				<option value='update_attribute' >Atualizar Atributos</option>
            				<option value='copy_available_products' >Duplicar Produto Simples</option>
            				<option value='copy_available_products_all' >Duplicar Produto Completo</option>
            				<option value='block_products' >Bloquear Produto</option>
        					<?php echo $delete; ?>
        				</select>
        			</div>
    			</div>
    			<div class="attributes-available-products" style='display:none;'>
        			<div class="col-md-2">
                    	<div class="form-group">
                    	
                    		<select class='form-control input-sm update-attribute-product' id='attribute-product'>
                    		<option value=''> Atributo</option>
                    		<option value='weight'> Peso</option>
                    		<option value='length'> Comprimento</option>
                    		<option value='width'> Largura</option>
                    		<option value='height'> Altura</option>
                    		<option value='color'> Cor</option>
                    		<option value='brand'> Marca</option>
                    		<option value='sale_price'> Preço de Venda</option>
                    		<option value='reference'> Referência</option>
                    		</select>
                    	</div>
                    </div>
                    <div class="col-md-4">
                    	<div class="form-group" id='attribute-required'>
                    		<input type="text" id='attribute-value' class="form-control input-sm" value="">
                    	</div>
                    </div>
                    <div class='col-md-4'>
                    	<div class='form-group'>
                    		<a class='btn btn-primary btn-sm' id='btn_update_attributes' ><i class='fa fa-check'></i></a>
                    	</div>
                    </div>
                </div>
    			<div class="categories-available-products" style='display:none;'>
        			<div class="col-md-2">
                    	<div class="form-group">
                    		<select class='form-control input-sm update-category-root' id='category-root'>
                    		<option value=''> Categoria Raiz</option>
                    		<?php 
                    		
                    		$rootCategory = trim($categories[0]);
                    		foreach ($listCategoriesRoot as $key => $value){
                    		    $selected = $rootCategory == $value['hierarchy'] ? 'selected' : '' ;
                    			echo "<option value='{$value['hierarchy']}' {$selected}>{$value['hierarchy']}</option>";
                    		}
                    		?>
                    		</select>
                    	</div>
                    </div>
                    <div class="col-md-4">
                    	<div class="form-group" id='category-required'>
                    		<select class='form-control input-sm update-category category_child' id='category'>
                    		<option value=''> Categoria</option>
                    		<?php 
                    		foreach ($listCategoriesFromRoot as $key => $value){
                    			$selected = $availableProductModel->category == $value['hierarchy'] ? 'selected' : '' ;
                    			echo "<option value='{$value['hierarchy']}' {$selected}>{$value['hierarchy']}</option>";
                    		}
                    		?>
                    		</select>
                    	</div>
                    </div>
                    <div class='col-md-4'>
                    	<div class='form-group'>
                    		<a class='btn btn-primary btn-sm' id='btn_update_categories' ><i class='fa fa-check'></i></a>
                    	</div>
                    </div>
                </div>
               	<table  class="table table-condensed no-padding" id="search-default" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
					<thead>
						<tr>
							<th ><input type='checkbox' class='flat-red select_all' /></th>
							<th>Produto</th>
							<th style='text-align:center'>Variação</th>
							<th style='text-align:center'>Qtd.</th>
							<th>Preço</th>
<!-- 							<th>Criado</th> -->
							<th>Atualizado</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php
					
					
// 					pre($list);die;
					foreach ($list as $fetch):
					
						$publications = '';
						if(isset($fetch['publications'][0]['marketplace']) && !empty($fetch['publications'][0]['marketplace'])){
							
							foreach($fetch['publications'] as $p => $publication){
								switch($publication['marketplace']){
									case 'Mercadolivre': 
										$publications .= !empty($publications) ? " " : "" ;
										$publications .= "<a href='{$publication['url']}' class='label label-warning' title='Quantidade Vendida Mercadolivre' target='_blank' ><i class='fa fa-legal'></i> {$publication['Mercadolivre']}</a>"; 
										break;
									case 'B2W': 
										$publications .= !empty($publications) ? " " : "" ;
										$publications .= "<a href='{$publication['url']}' class='label label-info' title='B2W' target='_blank' ><i class='fa fa-cloud'></i></a>"; 
										break;
									case 'Ecommerce': 
										if($availableProductModel->store_id == 4){
											$publication['url'] = "https://www.fanlux.com.br/catalogsearch/result/?q={$fetch['sku']}";
										}
										$publications .= !empty($publications) ? " " : "" ;
										$publications .= "<a href='{$publication['url']}' class='label label-success' title='Ecommerce' target='_blank' ><i class='fa  fa-shopping-cart'></i></a>";
										break;
									case 'Amazon':
										$publications .= !empty($publications) ? " " : "" ;
										$publications .= "<a href='{$publication['url']}' class='label label-primary' title='Amazon' target='_blank' ><i class='fa  fa-amazon'></i></a>";
										break;
									case 'Marketplace':
// 										$publications .= !empty($publications) ? " " : "" ;
// 										$publications .= "<a href='{$publication['url']}' class='label label-depato' title='Marketplace Sysplace' target='_blank' ><i class='fa fa-map-marker'></i></a>";
										break;
									case 'Tray':
										$publications .= !empty($publications) ? " " : "" ;
										$publications .= "<a href='{$publication['url']}' class='label label-primary' title='Ecommerce Tray target='_blank' ><i class='fa  fa-shopping-cart'></i></a>";
										break;
									case 'Magento2':
										$publications .= !empty($publications) ? " " : "" ;
										$publications .= "<a href='{$publication['url']}' class='label label-magento' title='Marketplace Sysplace' target='_blank' ><i class='fab fa-magento'></i></a>";
										break;
								}
							}
						}
						$pathDir = ABSPATH . "/Views/_uploads/store_id_{$storeId}/products/{$fetch['id']}";
						
						$images = getTotalImages($this->storedata['id'], $fetch['id']);
						
						$salePrice = str_replace('.',',',$fetch['sale_price']);
						$reference = '';
// 						if($this->userdata['store_id'] == 3 OR $this->userdata['store_id'] == 7){
						if($this->userdata['store_id'] == 3 ){
							$reference = " <b>Ref.:</b> {$fetch['reference']} <b>Coleção:</b> {$fetch['collection']}";
						}
						
						$badge = $fetch['blocked'] == 'T' ? 'bg-red' : '' ;
						
						$onbi = isset($fetch['onbi_ecommerce_id']) ? "<small class='label label-success' title='Ecommerce'>Onbi</small>" : "" ;
						$onbi = isset($fetch['tray_ecommerce_id']) ? "<small class='label label-success' title='Ecommerce'>Tray</small>" : "" ;
						$meli = isset($fetch['ml_product_id']) ? "<small class='label label-warning' title='Mercadolivre'>Meli</small>" : "" ;
						$qtyErp = $fetch['store_id'] == 4  ? "<small title='Estoque ERP'>/{$fetch['qty_erp']}</small>" :  '' ;
						$fetch['thumbnail'] = isset($fetch['thumbnail']) && !empty(trim($fetch['thumbnail'])) ? $fetch['thumbnail'] : HOME_URI."/Views/_uploads/images/semfoto.jpg";
						echo "<tr>
                            
                            <td width='10%' >
                                <input type='checkbox' id='{$fetch['id']}' sku='{$fetch['sku']}' parent_id='{$fetch['parent_id']}' class='flat-red select_one' />
                                <small>{$fetch['id']}</small>
                            	<img src='{$fetch['thumbnail']}' width='60px' height='60px' style='margin-top:5px !important' />
                            </td>
							<td width='60%'>
								<a href='/Products/Product/{$fetch['id']}/' title='Produto com descrição' >{$fetch['title']}</a><br>
								<small class='grey'><b>SKU:</b> <u>{$fetch['sku']}</u> <b>ParentId:</b> <u>{$fetch['parent_id']}</u></small><br>
								<small class='grey'><b>EAN:</b> {$fetch['ean']} <b>Ref.:</b> {$fetch['reference']} <b>Marca:</b> {$fetch['brand']} {$reference}</small><br>";
// 							echo "<small class='grey'><b>Categoria:</b> {$fetch['category']}</small><br>";
							echo "<small>{$publications}</small>
							</td>
							<td width='10%' align='center'><small>{$fetch['variation']}</small><br><small>{$fetch['color']}</small></td>
							<td width='5%' align='center'><small>{$fetch['quantity']}</small>{$qtyErp}</td>
							<td><small>{$fetch['sale_price']}</small></td>";
//         					echo "<td width='5%'><small>".dateTimeBrBreakLine($fetch['created'], '/')."</small></td>";
        					echo "<td width='5%'><small>".dateTimeBrBreakLine($fetch['updated'], '/')."</small></td>";
        					
//                             echo "<td width='5%'><small><a class='btn btn-app' data-toggle='modal' data-target='#product-options' id='{$fetch['id']}'><span class='glyphicon glyphicon-th'></span></a></small></td>";
							
             
		            
		             echo "<td align='center'><div class='dropdown'>
		                      <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
            		             <ul class='dropdown-menu pull-right' style='min-width:100px'>
             		                <li role='presentation'><a class='' href='".HOME_URI."/Products/Product/{$fetch['id']}/' role='menuitem' tabindex='-1' title='Produto com descrição' ><i class='fa fa-pencil-square-o'></i>Editar</a></li>
                                    <li role='presentation'><a class='' onclick=\"javascript:popup('".HOME_URI."/Products/Label/id/{$fetch['id']}','600','500');\" role='menuitem' tabindex='-1' title='Informações do Produto' ><i class='fa fa-pencil-square-o'></i>Etiqueta Padrão</a></li>
                                </ul>
                                </div>
                            </td>";
		             echo "</tr>";
		             endforeach;
		             
// 		             <li role='presentation'><a class='' href='".HOME_URI."/Products/Product/{$fetch['id']}/' role='menuitem' tabindex='-1' title='Produto com descrição' ><i class='fa  fa-qrcode'></i>QrCode</a></li>
// 		             <li role='presentation'><a class='delete' href='".HOME_URI."/Products/AvailableProducts/del/{$fetch['id']}/' role='menuitem' tabindex='-1' /><i class='fa fa-trash'></i>Excluir</li>
// 		             <li role='presentation'><a class='add_product_images' id='{$fetch['id']}' parent_id='{$fetch['parent_id']}'  url='{$pathDir}'  role='menuitem' tabindex='-1' title='Foto do Produto'><i class='fa fa-instagram'></i>Fotos</a></li>
// 		             <li role='presentation'><a class=duplicate-product' href='".HOME_URI."/Products/RegisterProduct/CopyProduct/{$fetch['id']}/' role='menuitem' tabindex='-1' title='Duplicar Produto'><i class='fa fa-copy'></i>Duplicar</a></li>
// 		                         <li role='presentation'><a class='link_payment_modal' data-toggle='modal' data-target='#product-options' id='{$fetch['id']}'><i class='fa fa-credit-card'></i> Link Pagamento</a> </li>
                    ?>	
					</tbody>
				</table>
				<?php 
				pagination($totalReg, $availableProductModel->pagina_atual, HOME_URI."/Products/AvailableProducts", array(
				    "id" => $availableProductModel->id,
				    "sku" => str_replace("%", "_x_", $availableProductModel->sku),
				    "title" => str_replace("%", "_x_", $availableProductModel->title),
				    "parent_id" => str_replace("%", "_x_", $availableProductModel->parent_id),
				    "reference" => str_replace("%", "_x_", $availableProductModel->reference),
				    "category" => str_replace(" ", "_", $availableProductModel->category),
				    "brand" => str_replace(" ", "_", $availableProductModel->brand),
				    "ean" => str_replace(" ", "_", $availableProductModel->ean),
				    "blocked" => str_replace(" ", "_", $availableProductModel->blocked),
				    "marketplace" => str_replace(" ", "_", $availableProductModel->marketplace),
				    "stock" => str_replace(" ", "_", $availableProductModel->stock),
					"collection" => str_replace(" ", "_", $availableProductModel->collection),
					"thumbnail" => str_replace(" ", "_", $availableProductModel->thumbnail),
				    "records" => $availableProductModel->records
				    ));
				?>
			
			</div><!-- /.box-body -->
    		<div class="overlay available-products" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
			
		</div><!-- /.box -->
	</div>
</div>







<div class="modal fade" id='product-options' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
              <a class="btn btn-app">
                <i class="fa fa-edit"></i> Edit
              </a>
              <a class="btn btn-app">
                <i class="fa fa-qrcode"></i> QrCode
              </a>
              <a class="btn btn-app">
                <i class="fa fa-credit-card"></i> Link Pagamento
              </a>
    	  </div>
          <div class="modal-footer">
          </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>

