<?php if ( ! defined('ABSPATH')) exit;?>
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
        						<div class="col-sm-12">
        							<label for="id">ProductId:</label> 
        							<input type="text" name="id"  id='id' class="form-control input-sm" value="<?php echo $availableProductModel->id; ?>">
        						</div>
        						<div class="col-sm-12">
        							<label for="sku">SKU:</label> 
        							<input type="text" name="sku"  id='sku' class="form-control input-sm" value="<?php echo $availableProductModel->sku; ?>">
        						</div>
        						<div class="col-sm-12">
        							<label for="parent_id">Parent:</label> 
        							<input type="text" name="parent_id"  id='parent_id' class="form-control input-sm" value="<?php echo $availableProductModel->parent_id; ?>">
        						</div>
        						<div class="col-sm-12">
        							<label for="ean">EAN:</label> 
        							<input type="text" name="ean"  id='ean' class="form-control input-sm" value="<?php echo $availableProductModel->ean; ?>">
        						</div>
        					</div>
    					</div>
    					<div class="col-md-4">
        					<div class="form-group">
        						<div class="col-sm-12">
        							<label for="reference">Referência:</label> 
        							<input type="text" name="reference"  id='reference' class="form-control input-sm" value="<?php echo $availableProductModel->reference; ?>">
        						</div>
        						<div class="col-sm-12">
        							<label for="title">Título:</label> 
        							<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $availableProductModel->title; ?>">
        						</div>
        						
        						<div class="col-sm-12">
        							<label for="category">Departamento:</label>
        							<select name="category" id='category' class="form-control input-sm">
            							<option value=''>Todas</option>
            							<option value='uncategorized'> Sem Departamento</option>
                                        <?php 
                                        foreach($listCategory as $key => $category){
                                        	$selected = $category['hierarchy'] == $availableProductModel->category ? "selected" : "" ;
                                            echo "<option value='{$category['hierarchy']}' {$selected}>{$category['hierarchy']}</option>";
                                        }
                                        ?>
        							</select>
        						</div>
        						<div class="col-sm-12">
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
        						<div class="col-sm-12">
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
        						<div class="col-sm-12">
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
        						<div class="col-sm-12">
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
        						<div class="col-sm-6">
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
        						<div class="col-sm-6">
        							<?php  $select5 = $select50 = $select100 = $select150 = $select200 = '';
        							switch($availableProductModel->records){
        							    case "5": $select5 = "selected"; break;
        							    case "50": $select50 = "selected"; break;
        							    case "100": $select100 = "selected"; break;
        							    case "150": $select150 = "selected"; break;
        							    case "200": $select200 = "selected"; break;
        							}?>
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
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
			<div class='message'></div>
				<div class="col-sm-2">
        			<div class="form-group">
        			<?php 
//         			pre($this->moduledata);die;
        			$ecommerce = isset($this->moduledata['Ecommerce'][0]) ? strtolower(trim($this->moduledata['Ecommerce'][0])) : 'ecommerce';
        			?>
            			<select id='select_action_available_products' class='form-control input-sm'>
            				<option id='selected' value='select' >Ações</option>
            				<option value='send_products_mercadolivre' >Enviar Mercadolivre</option>
            				<option value='send_products_skyhub' >Enviar Skyhub</option>
            				<option value='send_products_amazon' >Enviar Amazon</option>
            				<option value='block_products' >Bloquear Produto</option>
            				<option value='update_category' >Atualizar Categoria</option>
            				<option value='update_attribute' >Atualizar Atributos</option>
            				<option value='copy_available_products' >Duplicar produto</option>
<!--             				<option value='remove_products_skyhub' >Excluir Skyhub</option> -->
							<option value='send_products_<?= $ecommerce; ?>' >Enviar Ecommerce</option>
            				<option value='update_products_<?= $ecommerce; ?>' >Atualizar produtos Ecommerce</option>
        				
        				</select>
        			</div>
    			</div>
    			<div class="attributes-available-products" style='display:none;'>
        			<div class="col-sm-2">
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
                    <div class="col-sm-4">
                    	<div class="form-group" id='attribute-required'>
                    		<input type="text" id='attribute-value' class="form-control input-sm" value="">
                    	</div>
                    </div>
                    <div class='col-sm-4'>
                    	<div class='form-group'>
                    		<a class='btn btn-primary btn-sm' id='btn_update_attributes' ><i class='fa fa-check'></i></a>
                    	</div>
                    </div>
                </div>
    			<div class="categories-available-products" style='display:none;'>
        			<div class="col-sm-2">
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
                    <div class="col-sm-4">
                    	<div class="form-group" id='category-required'>
                    		<select class='form-control input-sm update-category' id='category'>
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
                    <div class='col-sm-4'>
                    	<div class='form-group'>
                    		<a class='btn btn-primary btn-sm' id='btn_update_categories' ><i class='fa fa-check'></i></a>
                    	</div>
                    </div>
                </div>
				<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
					<thead>
						<tr>
							<th><input type='checkbox' id='' class='flat-red select_all' /></th>
							<th>Id</th>
							<th>SKU</th>
							<th>Parent</th>
							<th>Marca</th>
							<th>Título</th>
							<th>Cor</th>
							<th>Ref.</th>
							<th>Var.</th>
							<th>Qtd.</th>
							<th>Preço</th>
							<th>Fotos</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($list as $fetch): 
						if($developer){
							$delete = "<a  href='#'  class='delete_available_product' title='Excluir Produto' ><i class='fa fa-trash'></i></a>";
						}
						$pathDir = ABSPATH . "/Views/_uploads/store_id_{$storeId}/products/{$fetch['id']}";

						$images = getTotalImages($this->storedata['id'], $fetch['id']);
// 						$sparkbar = "<div class='sparkbar' data-color='#00a65a' data-height='20'>";
// 						if(empty($fetch['ml_product_id'])){
// 						    $sparkbar .="90,90,"; 
// 						}
// 						$sparkbar .= "</div>"; 
						$salePrice = str_replace('.',',',$fetch['sale_price']);
						
						$onbi = isset($fetch['onbi_ecommerce_id']) ? "<small class='label label-success' title='Ecommerce'>Onbi</small>" : "" ;
						$onbi = isset($fetch['tray_ecommerce_id']) ? "<small class='label label-success' title='Ecommerce'>Tray</small>" : "" ;
						$meli = isset($fetch['ml_product_id']) ? "<small class='label label-warning' title='Mercadolivre'>Meli</small>" : "" ;
						echo "<tr>
                            <td><input type='checkbox' id='{$fetch['id']}' sku='{$fetch['sku']}' parent_id='{$fetch['parent_id']}' class='flat-red select_one' /></td>
                            <td>{$fetch['id']}</td>
							<td>{$fetch['sku']}</td>
                            <td>{$fetch['parent_id']}</td>
							<td>{$fetch['brand']}</td>
							<td><a href='/Products/Product/{$fetch['id']}/' title='Produto com descrição' >{$fetch['title']}</a></td>
							<td>{$fetch['color']}</td>
                            <td>{$fetch['reference']}</td>
							<td>{$fetch['variation']}</td>
							<td>{$fetch['quantity']}</td>
							<td>{$salePrice}</td>
							<td>{$images}</td> 
                            <td width='30px'  align='center'>
                                <a class='' href='/Products/Product/{$fetch['id']}/' title='Produto com descrição' ><i class='fa fa-pencil-square-o'></i></a>
                                {$delete}
                            </td>
                            
							</tr>";
             
		             endforeach;
// 		             <div class='dropdown'>
// 		             <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
// 		             <ul class='dropdown-menu pull-right' style='min-width:100px'>
// 		             <li role='presentation'><a class='' href='".HOME_URI."/Products/Product/{$fetch['id']}' role='menuitem' tabindex='-1' title='Produto com descrição' ><i class='fa fa-pencil-square-o'></i>Editar</a></li>
// 		             <li role='presentation'><a class='delete' href='".HOME_URI."/Products/AvailableProducts/del/{$fetch['id']}' role='menuitem' tabindex='-1' /><i class='fa fa-trash'></i>Excluir</li>
// 		             <li role='presentation'><a class='add_product_images' id='{$fetch['id']}' parent_id='{$fetch['parent_id']}'  url='{$pathDir}'  role='menuitem' tabindex='-1' title='Foto do Produto'><i class='fa fa-instagram'></i>Fotos</a></li>
// 		             <li role='presentation'><a class=duplicate-product' href='".HOME_URI."/Products/RegisterProduct/CopyProduct/{$fetch['id']}' role='menuitem' tabindex='-1' title='Duplicar Produto'><i class='fa fa-copy'></i>Duplicar</a></li>
//                                     </ul>
//                                 </div>
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

<!--                                 <div class='sparkbar' data-color='#00a65a' data-height='20'> -->
<!-- <canvas width='34' height='20'> -->
<!--                                 </div> -->
