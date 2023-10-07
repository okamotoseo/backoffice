<?php if ( ! defined('ABSPATH')) exit;?>

<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message"><?php if(!empty( $availableProductModel->form_msg)){ echo  $availableProductModel->form_msg;}?></div>
		
		<div class="box box-primary">	
			<div class="box-header with-border">
				<h3 class="box-title">Gerar descrição</h3>
			</div>
			<form method="POST" action="" name="form-register-product" enctype="multipart/form-data" >
			<input type='hidden' name='id'  id='id' value='<?php echo $availableProductModel->id;?>' />
			<div class="box-body">
				<div class="row">
					<div class="col-xs-6">
						<div class="row">
							<div class="col-xs-3">
								<div class="form-group">
									<label>SKU:</label>
									<input type='text' name='sku' class='form-control' id='sku'  value='<?php echo $availableProductModel->sku;?>' />
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label>Pai:</label>
									<input type='text' name='parent_id' class='form-control' id='parent_id'  value='<?php echo $availableProductModel->parent_id;?>' />
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label>Ean:</label>
									<input type='text' name='ean' id='ean' class='form-control' value='<?php echo $availableProductModel->ean; ?>' />
								</div>
							</div>
						</div>	
						<div class="form-group">
							<label>Título:</label>
							<input type='text' name='title' class='form-control span7' id='title' placeholder='Título' value='<?php echo friendlyText($availableProductModel->title); ?>' />
						</div>
						<div class="row">
							<div class="col-xs-6">
								<div class="form-group">
									<label for="brand">Marca:</label>
									<select id="brand" name="brand" class="form-control">
									<option value=''>Selecione</option>
									<?php 
		                            foreach($listBrands as $key => $brand){
										$selected = "";
										if(isset($availableProductModel->brand) && !empty($availableProductModel->brand)){
											$selected = $brand['brand'] == $availableProductModel->brand ? "selected" : "" ;
										}else{
											if(strpos( friendlyText($availableProductModel->title), $brand['brand'] )){
												$selected = 'Selected';
											}
										}
		                                echo "<option value='{$brand['brand']}' {$selected}>{$brand['brand']}</option>";
		                            }
		                            ?>
									</select>
								</div>
							</div>	
							<div class="col-xs-6">
								<div class="form-group">
									<label>Cor:</label>
									<select id="color" name="color" class="form-control">
									<option value=''>Selecione</option>
									<?php 
		                            foreach($listColors as $key => $color){
										$selected = "";
										if(isset($availableProductModel->color) && !empty($availableProductModel->color)){
											$selected = $color['color'] == $availableProductModel->color ? "selected" : "" ;
										}else{
											if(strpos( friendlyText($availableProductModel->title), $color['color'] )){
												$selected = 'Selected';
											}
										}
		                                echo "<option value='{$color['color']}' {$selected}>{$color['color']}</option>";
		                            }
		                            ?>
		                            </select>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label>Referência:</label>
									<input type='text' name='reference' id='reference' class='form-control' value='<?php echo $availableProductModel->reference; ?>' />
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label>Variação</label>
									<input type='text' name='variation' class='form-control variation' value='<?php echo $availableProductModel->variation; ?>'  />
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									<label>Categoria Raiz:</label>
									<select class='form-control' id='category-root' name="category-root">
									<option value=''> Selecione</option>
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
							<div class="col-xs-12">
								<div class="form-group">
									<label>Categoria:</label>
									<select class='form-control' name="category" id='category'>
									<option value=''> Selecione</option>
									<?php 
									foreach ($listCategoryRoot as $key => $value){
										$selected = $availableProductModel->category == $value['hierarchy'] ? 'selected' : '' ;
										echo "<option value='{$value['hierarchy']}' {$selected}>{$value['hierarchy']}</option>";
									}
									?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<textarea name='description' class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo  $availableProductModel->description; ?></textarea>
						</div>
						<div class="row">
							<div class="col-xs-3">
								<div class="form-group">
									<label>Quantidate.</label>
									<input type='text' name='quantity' class='form-control quantity' value='<?php echo $availableProductModel->quantity; ?>'  />
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label>Preço.:</label>
									<input  type='text' name='price' class='form-control price'  value='<?php echo $availableProductModel->price; ?>'  />
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label>Preço Venda.:</label>
									<input  type='text' name='sale_price' class='form-control sale_price' value='<?php echo $availableProductModel->sale_price; ?>'  />
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label>Preço Promo.:</label>
									<input type='text' name='promotion_price' class='form-control promotion_price' value='<?php echo $availableProductModel->promotion_price; ?>'  />
								</div>
							</div>
							
							<div class="col-xs-3">
								<div class="form-group">
									<label>Peso:</label>
									<input  type='text' name='weight' class='form-control shipping_measures' measure='weight' value='<?php echo $availableProductModel->weight; ?>'  />
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label>Altura:</label>
									<input  type='text' name='height' class='form-control shipping_measures' measure='height' value='<?php echo $availableProductModel->height; ?>'  />
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label>Largura:</label>
									<input type='text' name='width' class='form-control shipping_measures' measure='width' value='<?php echo $availableProductModel->width; ?>'  />
								</div>
							</div>
							<div class="col-xs-3">
								<div class="form-group">
									<label>Comprimento:</label>
									<input type='text' name='length' class='form-control shipping_measures' measure='length' value='<?php echo $availableProductModel->length; ?>'  />
								</div>
							</div>
							
    					<!-- PRODUCT IMAGE LIST -->
    					<div class="col-xs-6">
        					<?php 
        						$pathShow = HOME_URI . "/Views/_uploads/store_id_{$availableProductModel->store_id}/products/{$availableProductModel->id}";
        						$pathRead = ABSPATH . "/Views/_uploads/store_id_{$availableProductModel->store_id}/products/{$availableProductModel->id}";
        						if(file_exists($pathRead)){
        							$iterator = new DirectoryIterator($pathRead);
        							foreach ( $iterator as $key => $entry ) {
        								$file = $entry->getFilename();
        								if($file != '.' AND $file != '..'){
        									 $fileSize = $entry->getSize();
        									 echo "<input type='hidden' product_id='{$availableProductModel->id}' value='{$pathShow}/{$file}' url='{$pathShow}/'  fileName='{$file}' key='{$file}' width='120px' size='{$fileSize}' class='imgs-path' >";
        								}
        								    
        							}
        						}
        					?>
    					</div>
						<div class="col-xs-12">
    						<div class="file-loading">
    						    <input id="input-ke-1" name="file" type="file" multiple>
    						</div>
						</div>
							
						</div>
					</div>
					
					
					<div class="col-xs-6">	
						<?php 
						  
							foreach ($listInputsAttr as $key => $attr){
								if(isset($attributesValues[0])){
								    $attrValue = '';
									foreach($attributesValues as $ind => $value){
										if($value['attribute_id'] == $attr['attribute_id']){
											$attrValue = $value['value'];
										}
									}
								}else{
									$attrValue = '';
								}
								echo "<div class='form-group'>
								<label>{$attr['attribute']}:</label>
								<input type='text' name='attr_values[{$attr['attribute_id']}][{$attr['alias']}]' class='form-control span5' 
								placeholder='{$attr['attribute']}' value='{$attrValue}' />
								</div>";
							
							}
						?>
						

					</div>
					
					
	
	
				</div>
			</div>
			
			<div class="box-footer">
				<button type="submit" class="btn btn-primary pull-right" name="generate-description">Gerar Descrição</button>
				<?php 
					if(isset($resPD['id'])){
						echo "<a  class='btn btn-default add-product-channel  pull-right'  style='margin-right:5px;'  parent_id = '{$parentId}' name='generate-description'><i class='fa fa-share-alt'></i> Publicar</a>";
						echo "<img src='./images/ajax-loader-transparent.gif'  style='margin-right:5px; display:none;' id='add-product-loader' class='pull-right'>";
					}
				?>
				
				
			</div>
				
			</form>
		</div>	
		
		
	</div>
</div>


<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
    	<div class="message"><?php if(!empty( $availableProductModel->form_msg)){ echo  $availableProductModel->form_msg;}?></div>
    	<div class="box box-primary">	
    		<div class="box-body">
    			<div class="col-xs-12">
    				<div class="form-group">
    					<label>Variações disponíveis :</label>
        				<table class="table table-sort">
        					<thead>
        						<tr>
        							<th>SKU</th>
        							<th>Pai</th>
        							<th>Título</th>
        							<th>Cor</th>
        							<th>Variação</th>
        							<th>Qtd.</th>
        							<th>Venda</th>
        							<th>Peso</th>
        							<th>Altura</th>
        							<th>Largura</th>
        							<th>Comp.</th>
        						</tr>
        					</thead>
        					<tbody>
        					<?php 
        					if(isset($parentsProduct)){
            					foreach($parentsProduct as $child){
            					    echo "<tr>
        							<td>{$child['sku']}</td>
                                    <td>{$child['parent_id']}</td>
        							<td>{$child['title']}</td>
        							<td>{$child['color']}</td>
        							<td>{$child['variation']}</td>
        							<td>{$child['quantity']}</td>
        							<td>".number_format($child['sale_price'], 2, ',', '.')."</td>
                                    <td>{$child['weight']}</td>
                                    <td>{$child['height']}</td>
                                    <td>{$child['width']}</td>
                                    <td>{$child['length']}</td>
                                    <td><a class='fa fa-file-text-o' href='".HOME_URI."/Products/RegisterProduct/Product/{$child['id']}/SetAttribute/{$listInputsAttr[0]['set_attribute_id']}'></a><td>
        							</tr>";
            					    
            					}
        					}
        					?>
        					</tbody>
        				</table>
    				</div>
    			</div>
    		</div>
    	</div>
	</div>
</div>
