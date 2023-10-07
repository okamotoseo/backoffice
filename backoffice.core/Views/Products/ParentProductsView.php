<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $availableProductModel->form_msg)){ echo  $availableProductModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Products/ParentProducts" name="available-products-filter" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar produtos</h3>
					<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='<?php echo HOME_URI ?>/Products/ParentProducts' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-xs-2">
							<div class="form-group">
								<label for="sku">SKU:</label> 
								<input type="text" name="sku"  id='sku' class="form-control span1" value="<?php echo $availableProductModel->sku; ?>">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label for="title">Título:</label> 
								<input type="text" name="title"  id='title' class="form-control span3" value="<?php echo $availableProductModel->title; ?>">
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label for="reference">Referência:</label> 
								<input type="text" name="reference"  id='reference' class="form-control span1" value="<?php echo $availableProductModel->reference; ?>">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label for="category">Departamento:</label>
								<select id="category" name="category" class="form-control">
								<option value=''>Todas</option>
	                            <?php 
	                            foreach($listCategory as $key => $category){
	                            	$selected = $category['id'] == $availableProductModel->category ? "selected" : "" ;
                                    echo "<option value='{$category['id']}' {$selected}>{$category['category']}</option>";
                                }
	                            ?>
								</select>
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<label for="brand">Marca:</label>
								<select id="brand" name="brand" class="form-control">
								<option value=''>Todas</option>
								<?php 
	                            foreach($listBrands as $key => $brand){
	                            		$selected = $brand['id'] == $availableProductModel->brand ? "selected" : "" ;
	                                    echo "<option value='{$brand['brand']}' {$selected}>{$brand['brand']}</option>";
	                                }
	                            ?>

								</select>
							</div>
						</div>


					</div>
				</div>
				<div class="box-footer">
					<button type='submit' name='available-products-filter' class='btn btn-primary pull-right btn-sm' ><i class='fa fa-search'></i> Filtrar</button>
				</div>
			</form>
		</div>
	</div>
	<div class="col-md-12">
		<div class="message">
		
		</div>
		
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $this->title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="box-body">
			<div class='row'>
  
    			</div>
				<table id="search-default-old" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
					<thead>
						<tr>
							<th>Id</th>
							<th>SKU</th>
							<th>Pai</th>
							<th>Marca</th>
							<th>Título</th>
							<th>Cor</th>
							<th>Variação</th>
							<th>Qtd.</th>
							<th>Venda</th>
							<th>Fotos</th>
							<th>Ações</th>
							
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($list as $fetch):
					
					$pathDir = ABSPATH . "/Views/_uploads/store_id_{$storeId}/products/{$fetch['id']}";
					if(file_exists($pathDir)){
					    $dir = scandir($pathDir);
					    $images = count($dir) > 2 ? count($dir) -2 : 0 ;
					}else{
					    $images = 0;
					}
					
					$salePrice = str_replace('.',',',$fetch['sale_price']);
					echo "<tr>
                            <td>{$fetch['id']}</td>
							<td>{$fetch['sku']}</td>
                            <td><a class='inlineEditParent' type='parent_id' color='{$fetch['color']}' product_id='{$fetch['id']}' sku='{$fetch['sku']}'  parent_id='{$fetch['parent_id']}' >{$fetch['parent_id']}</a></td>
							<td>{$fetch['brand']}</td>
							<td>{$fetch['title']}</td>
							<td>{$fetch['color']}</td>
							<td>{$fetch['variation']}</td>
							<td>{$fetch['quantity']}</td>
							<td>{$salePrice}</td>
							<td>{$images}</td>
                            <td width='30px'  align='center'>
<a class='' href='".HOME_URI."/Products/Product/{$fetch['id']}' role='menuitem' tabindex='-1' title='Produto com descrição' ><i class='fa fa-pencil-square-o'></i></a>
             
                            </td>
							</tr>";
					
					endforeach;
					// <li role='presentation'><a class='add_product_images' id='{$fetch['id']}' parent_id='{$fetch['parent_id']}'  url='{$pathDir}'  role='menuitem' tabindex='-1' title='Foto do Produto'><i class='fa fa-instagram'></i>Fotos</a></li>
					
					?>	
					</tbody>
				</table>
				<?php pagination($totalReg, $availableProductModel->pagina_atual, HOME_URI."/Products/ParentProducts"); ?>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>