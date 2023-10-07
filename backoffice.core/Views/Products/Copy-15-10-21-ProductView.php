<?php

if ( ! defined('ABSPATH')) exit;
// if($this->userdata['cpf'] != '30456130802'){
// 	echo "<h1>Módulo em Manutenção Até as 21:30</h1>";
// 	return;
// } 

foreach($tabs as $ind){
    if ( in_array($ind, $this->parametros )) {
        $tabs[$ind] = "active";
    }
}

if(isset($_GET['tab']) AND !empty($_GET['tab'])){
    foreach($tabs as $ind){
        if ( $ind == $_GET['tab'] ) {
            $tabs[$ind] = "active";
        }else{
            $tabs[$ind] = "";
        }
    }
}
if(isset($_POST['attributes'])){
	$tabs['attributes'] = 'active';
}
if(isset($_POST['product-relational'])){
	$tabs['product-relational'] = 'active';
}


if(!in_array("active", $tabs)){
    $tabs['available-products'] = "active";
}

$productId = isset($availableProductModel->id) ? $availableProductModel->id : '';

$formAction = "/Products/Product/{$productId}";

// pre($availableProductModel);
if(isset($availableProductModel->store_id) && !empty($availableProductModel->store_id)){
	$productInformation =  trim($availableProductModel->title." ".$availableProductModel->color." ".$availableProductModel->variation);
}
?>
<div class="row">
	<div class="col-md-12">
	
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
        
            <ul class="nav nav-tabs">
                <li class="<?php echo $tabs['available-products']; ?>"><a href="#tab_1" data-toggle='tab'>Produto</a></li>
                <li class="<?php echo $tabs['attributes']; ?>"><a href="#tab_2" data-toggle='tab'>Atributos</a></li>
                <li class="<?php echo $tabs['fotos']; ?>"><a href="#tab_3" data-toggle='tab'>Fotos</a></li>
                <li class="<?php echo $tabs['product-relational']; ?>"><a href="#tab_4" data-toggle='tab'>Kit</a></li>
 <!--                <li class="<?php // echo $tabs['product-description']; ?>"><a href="#tab_5" data-toggle='tab'>Descrições</a></li>  -->
                <li class="<?php echo $tabs['publications']; ?>"><a href="#tab_6" data-toggle='tab'>Publicações</a></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                      Ações <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu primary">
                        <?php 
                       $createProductOnbi = empty($ecPublications[0]) ? "create_product_magento" : "" ;
                        echo "<li role='presentation'>
                                <a  href='#' class='product_actions new_ads_product_new {$createProductOnbi}' 
                                    product_id='{$availableProductModel->id}' 
                                    sku='{$availableProductModel->sku}'  
                                    parent_id='{$availableProductModel->parent_id}' 
                                    title='Publicar Anúncio' role='menuitem' tabindex='-1' >
                                    <i class='fa fa-share-alt-square'></i> Anúnciar
                                </a>
                            </li>";

                        if($availableProductModel->store_id == 4){
                            echo "<li role='presentation'>
                                <a  href='#' class='product_actions' action='create_product_sysemp'
                                    product_id='{$availableProductModel->id}'
                                    sku='{$availableProductModel->sku}'
                                    parent_id='{$availableProductModel->parent_id}'
                                    title='Exportar produto' role='menuitem' tabindex='-1' >
                                    <i class='fa fa-share-alt-square'></i> Enviar Sysemp
                                </a>
                            </li>";
                        }
                        
                        if($availableProductModel->store_id == 5){
                            echo "<li role='presentation'>
                                <a  href='#' class='product_actions' action='import_products_media'
                                    product_id='{$availableProductModel->id}'
                                    sku='{$availableProductModel->sku}'
                                    parent_id='{$availableProductModel->parent_id}'
                                    title='Importar Media' role='menuitem' tabindex='-1' >
                                    <i class='fa fa-instagram'></i> Importar Imagens
                                </a>
                            </li>";
                        }
                        
                        ?>
                    </ul>
                </li>
                 <li class="pull-right <?php echo $tabs['log']; ?> text-muted"><a href="#tab_7" data-toggle='tab'><i class="fa fa-code"></i> Log</a></li>
            </ul>
			<div class="tab-content">
               	<div class="tab-pane <?php echo $tabs['available-products']; ?>" id="tab_1">
                    
                    <div class="message"><?php if(!empty( $availableProductModel->form_msg)){ echo  $availableProductModel->form_msg;}?></div>
                    <form method="POST" action="<?php echo  $formAction; ?>" name="available-products" enctype="multipart/form-data" >
                    
                    <input type='hidden' name='id'  id='id' value='<?php echo $productId; ?>' />
                  
                  
    				<div class='row'>
    				
                    	<div class='col-md-12'>
                      
                            <div class="box box-primary">
                    	
                            	<div class="box-header with-border">
                                	<h3 class="box-title">Informações do produto <small><?php echo empty($productInformation) ? "Conjunto de informações" : $productInformation ; ?></small></h3>
                                	<div class="box-tools pull-right">
                                		<small><i class='fa fa-clock-o'  data-toggle='tooltip' title='Produto Criado'></i> <?php echo isset($availableProductModel->created) ? dateTimeBr($availableProductModel->created, '/') : ''; ?> <i class='fa fa-clock' data-toggle='tooltip' title='Produto Atualizado'></i> <?php echo dateTimeBr($availableProductModel->updated, '/'); ?></small>
    			                    	<?php echo "<a href='/Products/Product/{$productId}/' class='btn btn-box-tool refresh-tab-product'><i class='fa fa-refresh'></i></a>";?>
                        			</div>
                                </div><!-- /.box-header -->
                                    <?php 
    	                                $pos = explode('copy', $availableProductModel->sku);
    	                                // Note o uso de ===.  Simples == não funcionaria como esperado
    	                                // por causa da posição de 'a' é 0 (primeiro) caractere.
    	                                if (count($pos) > 1) {
    											$readonly = '';	                                	 
    	                                }else{
    	                                	$readonly = 'readonly';
    	                                }
                                    ?>  
                                <div class="box-body">
                                    <div class="col-md-3">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['sku']; ?>" >
                                    		<label>SKU:</label>
                                    		<small  class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title="Código unico do produto, informe sem caracteres especiais e substitua espaços por hífen '-' Ex.: kit10-123-GG" ></i></small>
                                    		<input type='text' name='sku' class='form-control' id="inputError" placeholder='EX-001' 
                                    		value='<?php echo $availableProductModel->sku; ?>' <?php //echo $readonly; ?>  />
                                    	</div>
                                    </div>
                                    <div class="col-md-3">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['parent_id']; ?>">
                                    		<label>ParentSKU:</label>
                                    		<small  class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title='Adicione o mesmo SKU caso o produto não possua variações OU adicione o SKU da variação principal cadastrada.' ></i></small>
                                    		<input type='text' name='parent_id' class='form-control' id='parent_id' placeholder='EX-001' value='<?php echo $availableProductModel->parent_id;?>' />
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['ean']; ?>">
                                    		<label>Ean:</label>
                                    		<input type='text' name='ean' id='ean' class='form-control' placeholder='5901234123457' value='<?php echo $availableProductModel->ean; ?>' />
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['variation_type']; ?> ">
                                    	<?php
                                    	$voltagem = $tamanho = $volume = $unidade = '';
                                    	if(!empty($availableProductModel->variation_type)){
                                    	    
                                        	switch($availableProductModel->variation_type){
                                        	    case "voltagem": $voltagem = "selected"; break;  
                                        	    case "tamanho": $tamanho = "selected"; break;  
                                        	    case "volume": $volume = "selected"; break;  
                                        	    case "unidade": $unidade = "selected"; break;  
                                        	    default : $voltagem = 'selected'; break;
                                        	}
                                    	    
                                    	}
                                    	
                                    	?>
                                    		<label>Tipo de variação:</label>
                                    		<select class='form-control' id='variation_type' name="variation_type">
                                    		<option value=''> Selecione</option>
                                    		<option value='voltagem' <?php echo $voltagem; ?> > Voltagem</option>
                                    		<option value='tamanho' <?php echo $tamanho; ?> > Tamanho</option>
                                    		<option value='volume' <?php echo $volume; ?> > Volume</option>
                                    		<option value='unidade' <?php echo $unidade; ?> > Unidade</option>
                                   
                                    		</select>
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['variation']; ?>">
                                    		<label>Variação</label>
                                    		<div id='variation_input'>
                                    			<input type='text' name='variation' id='variation' class='form-control variation' placeholder='110V' value='<?php echo $availableProductModel->variation; ?>'  />
                                    		</div>
                                    	</div>
                                    </div>
                                    <div class="col-md-6">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['title']; ?>">
                                    		<label>Título:</label><span class='caracteres_count badge pull-right'><?php echo strlen($availableProductModel->title)?></span>
                                    		<input type='text' name='title' class='form-control' id='title' placeholder='Furadeira de Impacto Com Mandril de 1/2 Pol. Ford 2000W 110V' value='<?php echo friendlyText($availableProductModel->title); ?>' />
                                    	</div>
                                    </div>
                                    <div class="col-md-6">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['brand']; ?>">
                                    		<label>Marca:</label>
                                    		<input type="text"  name='brand'  class="form-control  autocomplete_product_attr" id='brand'    value='<?php echo $availableProductModel->brand; ?>'>
                                    	</div>
                                    </div>
                                    <div class="col-md-6">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['color']; ?>">
                                    		<label>Cor:</label>
                                    		<input type="text" name='color' class="form-control  autocomplete_product_attr" id='color'  value='<?php echo $availableProductModel->color; ?>'>
                                    	</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['reference']; ?>">
                                    		<label>Referência:</label>
                                    		<input type='text' name='reference' id='reference' class='form-control' value='<?php echo $availableProductModel->reference; ?>' />
                                    	</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['category-root']; ?>">
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
                                    <div class="col-md-6">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['category']; ?>">
                                    		<label>Categoria:</label>
                                    		<select class='form-control category_child' name="category" id='category'>
                                    		<option value=''> Selecione</option>
                                    		<?php 
                                    		foreach ($listCategoriesFromRoot as $key => $value){
                                    		    $selected = titleFriendly($availableProductModel->category) == titleFriendly($value['hierarchy']) ? 'selected' : '' ;
                                    			echo "<option value='{$value['hierarchy']}' {$selected}>{$value['hierarchy']}</option>";
                                    		}
                                    		?>
                                    		</select>
                                    	</div>
                                    </div>
                                    <?php 
    //                                   $description =  isset($productDescriptionModel->productDescriptions['default']['description']) ? $productDescriptionModel->productDescriptions['default']['description'] : '' ;
                                      $description = isset($availableProductModel->description) ? $availableProductModel->description : '' ;
                                      
                                      ?>
                                    <div class="col-md-12">	
                                    	<div class="form-group <?php echo $availableProductModel->field_error['description']; ?>">
                                    		<textarea name='description' class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo  $description; ?></textarea>
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['quantity']; ?>">
                                    		<label>Quantidate:</label>
                                    		<small class='text-muted pull-right'><?php echo $availableProductModel->qty_erp; ?> <i class='fa fa-info-circle' data-toggle='tooltip' title='Estoque ERP' ></i></small>
                                    		<input type='text' name='quantity' class='form-control quantity' value='<?php echo $availableProductModel->quantity; ?>' /> 
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['price']; ?>">
                                    		<label>Preço:</label>
                                    		<small  class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title='Preço Importado do ERP' ></i></small>
                                    		<input  type='text' name='price' class='form-control price'  value='<?php echo $availableProductModel->price; ?>'  />
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<label>CMV:</label>
                                    	<small  class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title='Custo Mercadoria Vendida Importado do ERP' ></i></small>
                                    	<input type='text' name='cost' class='form-control cost' value='<?php echo $availableProductModel->cost; ?>'   />
                                    </div>
                                    <div class="col-md-2">
                                    	<label>Frete:</label>
                                    	<small  class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title='Sugestão de Custo de Frete Grátis aplicado em produtos acima de R$79' ></i></small>
                                    	<input type='text' name='shipping_rate' class='form-control cost' value='<?php echo $shippingRate; ?>'  />
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['sale_price']; ?>">
                                    		<label>Preço Venda:</label>
                                    		<small  class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title='Preço de venda base' ></i></small>
                                    		<input  type='text' name='sale_price' class='form-control sale_price' value='<?php echo $availableProductModel->sale_price; ?>'  />
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['ncm']; ?>">
                                    		<label>NCM:</label>
                                    		<small  class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title='NCM' ></i></small>
                                    		<input type='text' name='ncm' class='form-control ncm' value='<?php echo $availableProductModel->ncm; ?>'  />
                                    	</div>
                                    </div>
                                    
                                    
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['weight']; ?>">
                                    		<label>Peso:</label><small class='text-muted pull-right'><?php echo validateKg($shippingReference->weight/1000)."g"; ?> <i class='fa fa-info-circle' data-toggle='tooltip' title='Sugestão de peso Mercadolivre' ></i></small>
                                    		<input  type='text' name='weight' class='form-control shipping_measures weight' maxlength="6"  id="mask-weight"  measure='weight' value='<?php echo $availableProductModel->weight; ?>'  />
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['height']; ?>">
                                    		<label>Alt. (cm):</label><small class='text-muted pull-right'><?php echo validateCm($shippingReference->height)."cm"; ?> <i class='fa fa-info-circle' data-toggle='tooltip' title='Sugestão de altura Mercadolivre' ></i></small>
                                    		<input  type='text' name='height' class='form-control shipping_measures centimeter' measure='height' value='<?php echo $availableProductModel->height; ?>'  />
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['width']; ?>">
                                    		<label>Larg. (cm):</label><small class='text-muted pull-right'><?php echo validateCm($shippingReference->width)."cm"; ?> <i class='fa fa-info-circle' data-toggle='tooltip' title='Sugestão de largura Mercadolivre' ></i></small>
                                    		<input type='text' name='width' class='form-control shipping_measures centimeter' measure='width' value='<?php echo $availableProductModel->width; ?>'  />
                                    	</div>
                                    </div>
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['length']; ?>">
                                    		<label>Compr. (cm):</label><small class='text-muted pull-right'><?php echo validateCm($shippingReference->length)."cm"; ?> <i class='fa fa-info-circle' data-toggle='tooltip' title='Sugestão de comprimento Mercadolivre' ></i></small>
                                    		<input type='text' name='length' class='form-control shipping_measures centimeter' measure='length' value='<?php echo $availableProductModel->length; ?>'  />
                                    	</div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                    	<div class="form-group <?php echo $availableProductModel->field_error['weight']; ?>">
                                    		<label>Peso³:</label><small class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title='Peso cúbico Correios AxLxC/6000' ></i></small>
                                    		<input  type='text' name='weight-cubic' class='form-control shipping_measures weight-cubic' measure='weight' value='<?php 
                                    			echo number_format(($availableProductModel->height * $availableProductModel->width * $availableProductModel->length) / 6000, 2); 
                                    		
                                    		?>' readonly />
                                    	</div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                    	<div class="form-group">
                                    		<label>Cross Docking:</label><small class='text-muted pull-right'><i class='fa fa-info-circle' data-toggle='tooltip' title='Tempo para envio do produto em dias' ></i></small>
                                    		<input  type='text' name='cross_docking' class='form-control cross_docking'  value='<?php echo $availableProductModel->cross_docking; ?>'  />
                                    	</div>
                                    </div>
            					</div>
            					<div class="box-footer">
            						<div class='col-md-3'>
    									<div class='form-group'>
    									<label class='col-md-12 control-label checkbox'>
    									<?php 
    										$selected = $availableProductModel->blocked == 'T' ? "checked" : '' ; 
    										echo "<input  name='blocked' type='checkbox' class='flat-red block_product' value='{$availableProductModel->blocked}' {$selected}> Bloqueado";
    										?>
    									</label>
    
    									</div>
    								</div>
    								
            						<button type="submit" class="btn btn-primary btn-sm pull-right" name="available-products"><i class='fa fa-check'></i> Salvar</button>
            					</div>
            				</div>
        				</div>
                    </div><!-- /.row -->
                </form>
                </div><!-- /.tab-pane -->
                  
                <div class="tab-pane <?php echo $tabs['attributes']; ?>" id="tab_2">
                	<div class='row'>
    					<div class="col-md-12">
                    		 <div class="box box-solid" style='margin-bottom: 5px; box-shadow: 0 0px 0px rgba(0,0,0,0);'>
			    				<div class="box-header">
			                    	<h3 class="box-title">Atributos do produto <small><?php echo empty($productInformation) ? "" : $productInformation ; ?></small></h3>
			                    	<div class="box-tools pull-right">
			                    	<?php echo "<a href='/Products/Product/{$productId}/' class='btn btn-box-tool refresh-tab-fotos'><i class='fa fa-refresh'></i></a>";?>
                    			</div>
			    				</div>
		    				</div>
		    				<div class='box-body no-padding'>
		    				</div>
	    				</div>
	    			</div>
                	<div class="message"><?php if(!empty( $attributesValuesModel->form_msg)){ echo  $attributesValuesModel->form_msg;}?></div>
                    <form method="POST" action="<?php echo  $formAction; ?>" name="attributes-values" enctype="multipart/form-data" >
                    <input type='hidden' name='product_id'  id='product_id' value='<?php echo $productId; ?>' />
                    <div class='row'>
                        
                       	<div class="col-md-12">
        					
                       		<?php if(!empty($listInputsAttrMkt)){ ?>
                            <div class="box box-primary ">
                            	<div class="box-header with-border">
                            	
                                	<h3 class="box-title">Atributos Mercadolivre: <small><?php echo $categoryInfo['hierarchy']; ?></small></h3>
                            	  	<div class="box-tools pull-right">
                    					<?php echo "<a class='fa fa-list-alt' href='".HOME_URI."/Modules/Mercadolivre/Map/Attributes/Category/{$listInputsAttrMkt[0]['category_id']}' title='Gerenciar atributos' target='_blank' ----></a>"; ?>
                    					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    				</div>
                            	</div>
                                      
                                <div class="box-body">
                                <?php 
                                    $alias = array();
                                    if(isset($listInputsAttr)){
                                        foreach($listInputsAttr as $prop => $value){
                                            $alias[] = $value['attribute_id'];
                                        }
                                    }
                                    $alias[] = 'EAN';
                                    $alias[] = 'GTIN';
                                    $alias[] = 'PACKAGE_HEIGHT';
                                    $alias[] = 'PACKAGE_LENGTH';
                                    $alias[] = 'PACKAGE_WIDTH';
                                    $alias[] = 'PACKAGE_WEIGHT';
                                    $alias[] = 'UPC';
                                    $alias[] = 'MPN';
                                    $alias[] = 'JAN';
                                    $alias[] = 'GTIN14';
                                    
                                    $attrAlias = array();
//                                     pre($listInputsAttrMkt);die;
            						foreach ($listInputsAttrMkt as $key => $attr){
            						    /**
            						     * verificar aki para não exibir mapeados
            						     * 
            						     */
//             						    if(!in_array($attr['attribute_id'], $alias)){

                						    $tags = json_decode($attr['tag']);
                						    
                						    $required = $attr['required'] ? "<font color='red'>*</font>" : "";
                						    
                						    $autoComplete = $attr['num_values'] > 1  ? "autocomplete-attributes" : "";
                						     
                						    $autoCompleteLabel = !empty($autoComplete) ?  "Selecione" : "" ;
                		                     
                							if(isset($attributesValues[0])){
                							    $attrValue = '';
                								foreach($attributesValues as $ind => $value){
                									if($value['attribute_id'] == $attr['attribute_id']){
                										$attrValue = $value['value'];
                									}
                									
                									if($value['ml_attribute_id'] == $attr['attribute_id']){
                									    $attrValue = $value['value'];
                									}
                									
                								}
                							}else{
                								$attrValue = '';
                							}
                							
                							switch($attr['attribute_id']){
                							    case "ITEM_CONDITION": $attrValue = !empty($attrValue) ? $attrValue : "Novo"; break;
                							    case "IS_KIT": $attrValue = !empty($attrValue) ? $attrValue : "Não"; break;
                							    case "IS_FLAMMABLE": $attrValue = !empty($attrValue) ? $attrValue : "Não"; break;
                							    case "IS_SUITABLE_FOR_SHIPMENT": $attrValue = !empty($attrValue) ? $attrValue : "Sim"; break;
                							    case "SELLER_SKU": $attrValue = !empty($attrValue) ? $attrValue : $availableProductModel->sku; break;
                							    
                							}
                							if($attr['attribute_id'] != "DESCRIPTIVE_TAGS"){
                    							echo "<div class='col-md-4'><div class='form-group'>
                    							<label>{$attr['name']} {$required}:</label>
                    							<input type='text' name='attr_values_ml[{$attr['attribute_id']}][{$attr['name']}]' class='form-control {$autoComplete}' 
                                                attribute_id='{$attr['attribute_id']}' category_id='{$attr['category_id']}'
                    							placeholder='{$attr['value']}' value='{$attrValue}' />
                    							</div></div>";
                							}
//             						    }
            						
            						}
            						?>
            					</div>
            				</div>
        				
        				
            			
        				<?php } ?>
        				
        				
                       	<?php if(isset($listInputsAttr) AND !empty($listInputsAttr)){ ?>	  
                            
                            <div class="box box-primary collapsed-box">
                           
                            	<div class="box-header with-border">
                                	<h3 class="box-title"><?php echo $listInputsAttr[0]['set_attribute'];?> <small><?php echo $availableProductModel->category  ?></small></h3>
                                    <div class="box-tools pull-right">
                    					<?php echo "<a class='fa fa-list-alt' href='/Products/SetAttributes/edit/{$listInputsAttr[0]['set_attribute_id']}' title='Gerenciar atributos' target='_blank' ></a>"; ?>
                    					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
                    				</div>
                                </div><!-- /.box-header -->
                                      
                                <div class="box-body">
                				<?php 
            						foreach ($listInputsAttr as $key => $attr){
            						    $attr['alias'] = isset($attr['alias']) ? $attr['alias'] : "" ;
            						    
            						    foreach($listInputsAttrMkt as $prop => $value){
            						        if(isset($value['alias'])){
//             						            pre($value);
            						            if($attr['attribute_id'] == $value['alias']){
            						                $attr['alias'] = $value['attribute_id'];
            						                
            						            }
//             						            else{
//             						                $attr['alias'] = $attr['attribute_id'];
//             						            }
            						        }
            						    }
//             						    pre($attributesValues);
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
            							
            							echo "<div class='col-md-4'><div class='form-group'>
            							<label>{$attr['attribute']}:</label>
            							<input type='text' name='attr_values[{$attr['attribute_id']}][{$attr['attribute']}]' class='form-control autocomplete-attributes'
                                        attribute_id='{$attr['alias']}' category_id='{$listInputsAttrMkt[0]['category_id']}'            							
                                        placeholder='{$attr['attribute']} ' value='{$attrValue}' />
            							</div></div>";
            						}
                				?>
            				 	</div>
            				</div>
            				
        					<?php } ?>
        			
        			</div>
        			<?php 
        			if(!empty($listAttributesRequired) AND !isset($listRelational)){
//         				pre($listAttributesRequired);die;
        				?>
        			<div class='col-md-12'>
        			<div class="box box-primary ">
                           
                        <div class="box-header with-border">
                        	<h3 class="box-title">Amazon Atributos: <small><?php echo $azCategoryModel->path_from_root; ?></small></h3>
                            <div class="box-tools pull-right">
                    					
                    			<?php echo "<a class='fa fa-list-alt' href='".HOME_URI."/Modules/Amazon/Map/Attributes/Xsd/{$azAttributesModel->xsdName}/$azAttributesModel->choice/' title='Gerenciar atributos' target='_blank' ></a>"; ?>
                    			<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    		</div>
                        </div><!-- /.box-header -->
                                      
                        <div class="box-body">
                		<?php 
            				foreach ($listAttributesRequired as $key => $attr){
            							
            					if(!empty($attr['attribute'])){
//             							pre($attributesValues);die;	
	            					$partsName = explode('-', $attr['name']);
	            					$name = trim($partsName[0]);
	            					$required = 
            						$placeholder = isset($attr['placeholder']) && !empty($attr['placeholder']) ? $attr['placeholder'] : '' ;
	            					$tooltip = !empty($attr['description']) ? "<i class='fa  fa-info-circle' data-toggle='tooltip' title='{$attr['description']}. {$attr['attribute']}'></i>" :  "<i class='fa  fa-info-circle' data-toggle='tooltip' title='{$name}. {$attr['attribute']}'></i>" ;
	            					$label = isset($attr['label']) && !empty($attr['label']) ? $attr['label'] :  $attr['attribute'];
	            					$required = $attr['required'] != 'Opcional' ? "<font color='red'>*</font>" : "";
	            					
	            					if(isset($attributesValues[0])){
	            						$attrValue = '';
	            						foreach($attributesValues as $ind => $value){
		            						if($value['attribute_id'] == $attr['attribute']){
		            							$attrValue = $value['value'];
		            						}
	            						}
            						}else{
            							$attrValue = '';
            						}
	            						
	            					echo "<div class='col-md-4'>
	            						<div class='form-group'>
	            						<label>{$label}{$required} {$tooltip} :</label>";
	            							
	            					if(empty($attr['values'])){
		            					echo "<input type='text' name='attr_values_az[{$attr['attribute']}][{$label}]' class='form-control autocomplete-attributes'
		                                      attribute_id='{$attr['alias']}' category_id='{$listInputsAttrMkt[0]['category_id']}' id='{$name}'           							
		                                      placeholder='{$placeholder} ' value='{$attrValue}' />";
	            					}else{
	            						echo "<select class='form-control' name='attr_values_az[{$attr['attribute']}][{$label}]' alias='{$attr['alias']}' >
	            							<option value=''> Selecione</option>";
		            						foreach ($attr['values'] as $i =>  $value){
		            							$selected = $value == $attrValue ? 'selected' : '' ;
		            							echo "<option value='{$value}' {$selected}>{$value}</option>";
		            						}
	            						echo "</select>";
	            						
	            						
	            					}
	            					
	            					
	            					echo "</div></div>";
            							
            					}
            				}
            						
            						
            						
            						

//             						if(isset($azAttributesRelationship[0])){
            							 
//             							$labelStyle = $classDiv = $classLabel = $attrrel = '';
//             							foreach($azAttributesRelationship as $i => $valueRel){
//             								if($valueRel['az_attribute'] == $attr['name']){
//             									$attrrel = $valueRel['attribute'];
//             									$classDiv = 'has-info';
//             									$labelStyle = "style='color: #3c8dbc'";
//             									$classLabel = "class='control-label' for='{$attr['name']}'";
//             								}
//             							}
            							 
//             						}else{
//             							$attrrel = '';
//             						}
            						
//             						if(isset($attributesValues[0])){
//             							$attrValue = '';
//             							foreach($attributesValues as $ind => $value){
//             								if($value['attribute_id'] == $attr['name']){
//             									$attrValue = $value['value'];
//             								}
//             							}
//             						}else{
//             							$attrValue = '';
//             						}
            						
//             						if(isset($attributesValues[0])){
//             							$placeholdRel = '';
//             							foreach($attributesValues as $ind => $value){
//             								if($value['attribute_id'] == $attrrel){
//             									$placeholdRel = $value['value'];
//             								}
//             							}
//             						}else{
//             							$placeholdRel = '';
//             						}
            						
            						
            						
            						
                				?>
            				 	</div>
            				</div>
            				</div>
            				<?php 
            				
	        			}?>
	        			<div class='col-md-12'>	
            				<div class="box-footer">
            					<button type='submit' class='btn btn-primary btn-sm pull-right' name='attributes-values'><i class='fa fa-check'></i> Salvar</button>
            				</div>
            			</div>
    			</div>
    			</form>
            </div><!-- /.tab-pane -->
                
                
                
            <!-- PRODUCT IMAGE LIST -->
            <div class="tab-pane <?php echo $tabs['fotos']; ?>" id="tab_3">
            <?php if(!empty($productId)){?>
                <div class='row'>
        			<div class="col-md-12">
                        <div class="box box-primary">
                	
                        	<div class="box-header with-border">
                            	<h3 class="box-title">Imagens do produto <small><?php echo empty($productInformation) ? "" : $productInformation ; ?></small></h3>
                            	<div class="box-tools pull-right">
			                    	<?php echo "<a href='/Products/Product/{$productId}/fotos' class='btn btn-box-tool refresh-tab-fotos'><i class='fa fa-refresh'></i></a>";?>
                    			</div>
                            </div><!-- /.box-header -->
                                  
                            <div class="box-body">
                            
	                            <div class="col-md-6">
	                            	<div id='drag-drop-area' class='DashboardContainer'>
	                            	
	                            	</div>
	                           	</div>
	                      
	            				<div class="col-md-6">
	            				<!-- Uploaded files list -->
									<div class="uploaded-files-server">
									<ul class="products-list product-list-in-box">
				                    <?php 
				                    	$count = 1;
	                					$pathShow = HOME_URI . "/Views/_uploads/store_id_{$availableProductModel->store_id}/products/{$productId}";
	                					$pathRead = ABSPATH . "/Views/_uploads/store_id_{$availableProductModel->store_id}/products/{$productId}";
	                					if(file_exists($pathRead)){
	                						$iterator = new DirectoryIterator($pathRead);
	                						$picturesArray = array();
	                						foreach ( $iterator as $key => $entry ) {
	                						    $file = $entry->getFilename();
	                						    if($file != '.' AND $file != '..'){
	                						    	$count++;
	                						        $fileSize = $entry->getSize();
	                						        $parts = explode("-", $file);
	                						        $array = array_slice($parts, -2);
	                						        
	                						        $picturesArray[$array[0]] = array(
	                						            'source' =>  $pathShow.'/'.$file, 
	                						            "file_size" => $fileSize,
	                						            "path_show" => $pathShow,
	                						            "file" => $file
	                						        );
	                						    }
	                						}
	                						ksort($picturesArray);
	                						
	                						foreach ($picturesArray as $key => $pics) {
	                							
	                							
	                							$fileType = mime_content_type(UP_ABSPATH."/store_id_{$this->storedata['id']}/products/{$productId}/{$pics['file']}");
	                							$partsType = explode("/", $fileType);
	                							$size = sizeFilter($pics['file_size']);
	                							if(trim($partsType[0]) == 'video'){
		                							echo "<li class='item'>
	                                    				<div class='product-img''>
				                							<video width='50' height='50' controls ><source src='{$pics['path_show']}/{$pics['file']}'  type='{$fileType}'></video>
			                							</div>
			                							<div class='product-info'>
			                								<a href='{$pics['path_show']}/{$pics['file']}'  class='product-title' target='_blank'>{$pics['file']}</a>
			                								<a type='button' onclick=\"javascript:removeProductImage(this, '".HOME_URI."', '{$productId}', '{$pics['file']}');\"   class='btn btn-xs pull-right' title='View Details'><i class='fa fa-trash'></i></a>&nbsp;
			                								<a type='button' href='{$pics['path_show']}/{$pics['file']}'  download  class='btn btn-xs pull-right' title='View Details'><i class='fa fa-download'></i></a>
				                							<span class='product-description small'>
				                								{$size}
				                							</span>
			                							</div>
		                							</li>";
	                							}else{
		                							list($width, $height, $type, $attr) = getimagesize(UP_ABSPATH."/store_id_{$this->storedata['id']}/products/{$productId}/{$pics['file']}");
		 
		                							
		                							echo "<li class='item position-image'>
										                      <div class='product-img'>
										                      		<img src='{$pics['path_show']}/{$pics['file']}'  alt='Product Image' >
										                      </div>
										                      <div class='product-info'>
											                      	<a href='{$pics['path_show']}/{$pics['file']}' class='product-title' target='_blank'>{$pics['file']}</a>
											                      	<a type='button' onclick=\"javascript:removeProductImage(this, '".HOME_URI."', '{$productId}', '{$pics['file']}');\"   class='btn btn-xs pull-right' title='View Details'><i class='fa fa-trash'></i></a>&nbsp;
											                        <a type='button' href='{$pics['path_show']}/{$pics['file']}'  download  class='btn btn-xs pull-right' title='View Details'><i class='fa fa-download'></i></a>
											                        <span class='product-description small'>
											                         	{$width} X {$height} - {$size}
											                        </span>
										                      </div>
									                    </li>";
	                							}
								                        
	                						}
	                						
	                					}
	                					
	                				
	                				?>
					                  </ul>
									  <input type='hidden' class='count-media' value='<?php echo $count; ?>' />
									  </div>
								  </div>
							</div>
    				        <div class="overlay fileinput-image-sort" style='display:none;'>
                        		<i class="fa fa-refresh fa-spin"></i>
                    		</div>
            			</div>
                	</div>
            	</div>
            	<?php } ?>
            </div>
            <!-- END PRODUCT IMAGE LIST -->
                  
                  
            <div class='tab-pane <?php echo $tabs['product-relational']; ?>' id='tab_4'>
            <div class="message"><?php if(!empty( $productRelationalModel->form_msg)){ echo  $productRelationalModel->form_msg;}?></div>
            <form method="POST" action="<?php echo  $formAction; ?>" name="product-relational" enctype="multipart/form-data" >
            <?php if(!empty($productId)){?>
            <input type='hidden' name='product_id' id='product_id_relational' value='<?php echo $productId; ?>' />
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                    	
                        	<div class="box-header with-border">
                            	<h3 class="box-title">Produtos relacionados <small><?php echo empty($productInformation) ? "" : $productInformation ; ?></small></h3>
                            </div><!-- /.box-header -->
                                  
                            <div class="box-body">
                            <div class='row'>
                            	<div class="col-md-3">
                            		<div class="form-group">
                            			<label>Persquisar por:</label>
                            			<select class='form-control' id="autocomplete-product-type" >
                            			<option value='sku'> Sku</option>
                            			<option value='title'> Título</option>
                            			<option value='reference'> Referência</option>
                            			</select>
                            		</div>
                            	</div>
                                <div class="col-md-9">
                            		<div class="form-group">
                            		<label>Código:</label>
                            		<input type="text" class="form-control input-sm" id='autocomplete_product_id'  product_id= '<?php echo $productId;?>' tabindex="5"  name='autocomplete-sku'  value=''>
                            		</div>
                            	</div>
                            </div>
                            <div class='row'>	
                                <div class="col-md-12">
                                
                                	<table  class='table table-condensed' id='log'>
                                	<tr>
                                		<th>Title</th>
                                		<th>Preço fixo unitário</th>
                                		<th>Dinâmico</th>
                                		<th>Desconto fixo unitário</th>
                                		<th>Desconto % unitário</th>
                                		<th>Quantidade</th>
                                		<th></th>
                                	</tr>
                                	
                                	<?php 
//                                 	pre($listRelational);die;
                                    if(isset($listRelational)){
                                		foreach($listRelational as $key => $value){
                                		    $checked = '';
                                		    $disabled = 'false';
                                		    if($value['dynamic_price'] == 'T'){
                                		        $checked = "checked";
//                                 		        $disabled = 'false';
                                		        
                                		    }
                                		    
                                		    echo "<tr id='{$value['product_relational_id']}'>
                                                <td>
                                                	<a href='/Products/Product/{$value['product_relational_id']}' target='_blank'>{$value['title']}</a><br>
                                                	<span class='product-description small'>
							                        	<b>SKU:</b> {$value['sku']} - <b>Qtd.:</b> {$value['quantity']} - <b>Preço:</b> {$value['sale_price']}
							                        </span>
							                    </td>
                                                
                                                <td>
                                                      <div class='form-group'>
                                                        <input type='text' name='fixed_unit_price[{$value['product_relational_id']}]' class='fixed_unit_price form-control input-sm'  value='{$value['fixed_unit_price']}'>
                                                      </div>
                                                </td>
                                                <td align='center'>  
                                                      <div class='form-group'>
                                                          <input  type='checkbox' name='dynamic_price[{$value['product_relational_id']}]'   class='dynamic_price' value='{$value['dynamic_price']}' {$checked}>
                                                      </div>
                                                </td>
                                                <td>
                                                      <div class='form-group'>
                                                        <input type='text' name='discount_fixed[{$value['product_relational_id']}]'  class='discount_fixed form-control input-sm'  value='{$value['discount_fixed']}'>
                                                      </div>
                                                </td>
                                                <td>
                                                      <div class='form-group'>
                                                        <input type='text' name='discount_percent[{$value['product_relational_id']}]' class='discount_percent form-control input-sm'  value='{$value['discount_percent']}'>
                                                      </div>
                                                </td>
                                                <td> 
                                                    <div class='form-group'>
                                                        <input type='text' name='products_relational[{$value['product_relational_id']}]' class='qtd_product_relational form-control input-sm'  value='{$value['qtd']}'>
                                                    </div>
                                                </td>
                                                <td><a class='fa fa-trash remove_product_relational' product_relational_id='{$value['product_relational_id']}' product_id='{$value['product_id']}' /></td>
                                              </tr>";
                                		}
                                    }
                                	?>
                                	</table>
                            	</div>
                            </div>
                            </div>
                          <div class="box-footer">	
        						<button type="submit" class="btn btn-primary btn-sm pull-right" name="product-relational"><i class='fa fa-check'></i> Salvar</button>
    					  </div>
                            
                    	</div>
                	</div>
                </div>
            <?php } ?>
			</form>
            </div>
                  
            <div class='tab-pane <?php echo $tabs['product-description']; ?>' id='tab_5'>
              
            	<div class="message"><?php if(!empty( $productDescriptionModel->form_msg)){ echo  $productDescriptionModel->form_msg;}?></div>
                    <form method="POST" action="<?php echo  $formAction; ?>" name="product-description" enctype="multipart/form-data" >
    				<?php if(!empty($productId)){?>
    				<input type='hidden' name='product_id'  id='id' value='<?php echo $productId; ?>' />
    				
    				<div class='row'>
    					<div class="col-md-6">
                    		 <div class="box box-primary">
                                  <div class="box-header with-border">
                                      <h3 class="box-title">Ecommerce</h3>
                                      <!-- tools box -->
                                      
                                  </div><!-- /.box-header -->
                                  
                                  <div class="box-body">
                                  <?php 
                                  $title = isset($productDescriptionModel->productDescriptions['Ecommerce']['title']) ?  $productDescriptionModel->productDescriptions['Ecommerce']['title'] : '';
                                  $description =  isset($productDescriptionModel->productDescriptions['Ecommerce']['description']) ? $productDescriptionModel->productDescriptions['Ecommerce']['description'] : '' ;
                                  
                                  ?>
                                  	  <div class="col-md-12">
                    					  <div class="form-group ">
                        					  <label>Title</label>
                        					  <input type='text' name='product_descriptions[Ecommerce][title]' class='form-control title' value='<?php echo $title; ?>'  />
                    					  </div>
                					  </div>
                						
                                  	  <div class="col-md-12">	
                        				  <div class="form-group ">
                        				  	<textarea class='textarea' name='product_descriptions[Ecommerce][description]' placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $description; ?></textarea>
                        				  </div>
                    				  </div>
                    				  
                        		  </div>
                    		  </div>
                          </div>
                          
                          <div class="col-md-6">
                              <div class="box  box-primary">
                                  <div class="box-header with-border">
                                      <h3 class="box-title">Mercadolivre </h3>
                                      <!-- tools box -->
                          
                                  </div><!-- /.box-header -->
                                  
                                  <div class="box-body">
                                  <?php 
                                  $title = isset($productDescriptionModel->productDescriptions['Ecommerce']['title']) ?  $productDescriptionModel->productDescriptions['Mercadolivre']['title'] : '';
                                  $description =  isset($productDescriptionModel->productDescriptions['Ecommerce']['description']) ? $productDescriptionModel->productDescriptions['Mercadolivre']['description'] : '' ;
                                  ?>
                                  	  <div class="col-md-12">
                    					  <div class="form-group ">
                        					  <label>Title</label><span class='caracteres badge pull-right'><?php echo strlen($title); ?></span>
                        					  <input type='text' id='char_limit' name='product_descriptions[Mercadolivre][title]' class='form-control title' value='<?php echo $title; ?>'  />
                    					  </div>
                					  </div>
                						
                                  	  <div class="col-md-12">	
                        				  <div class="form-group ">
                        				  	<textarea class='textarea' name='product_descriptions[Mercadolivre][description]'placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $description; ?></textarea>
                        				  </div>
                    				  </div>
                    				  
                        		  </div>
                    		  </div>
						</div>

                      </div>
                      <div class="box-footer">	
    						<button type="submit" class="btn btn-primary btn-sm pull-right" name="product-description"><i class='fa fa-check'></i> Salvar</button>
					  </div>
					  <?php } ?>
                	</form>
                </div>
                
                <div class='tab-pane <?php echo $tabs['publications']; ?>' id='tab_6'>
                	<div class='row'>
    					<div class="col-md-12">
                    		 <div class="box box-solid" style='margin-bottom: 0px; box-shadow: 0 0px 0px rgba(0,0,0,0);'>
			    				<div class="box-header">
			    				
			                    	<h3 class="box-title">Publicar produto <small><?php echo empty($productInformation) ? "" : $productInformation ; ?></small></h3>
			                    	<div class="box-tools pull-right">
			                    	<?php 
    			                     echo "<a href='/Products/Product/{$productId}/publications' class='btn btn-box-tool refresh-tab-publications'><i class='fa fa-refresh'></i></a>";
			                    	?>
                    				</div>
			    				</div>
		    				</div>
		    				<div class='box-body no-padding'>
		    				</div>
	    				</div>
	    			</div>
                    <form method="POST" action="<?php echo  $formAction; ?>" name="publications" enctype="multipart/form-data" >
    				<?php if(!empty($productId)){?>
    				<input type='hidden' name='product_id'  id='id' value='<?php echo $productId; ?>' />
    				<div class="row">
    				
                    	<div class="col-md-12">
                    		
                    		<div class="box box-primary">
                    			<div class="box-header with-border">
                    				<h3 class="box-title">Mercadolivre <small><?php 
                    				if(!empty($categoryInfo['hierarchy'])){
                    				    echo $categoryInfo['hierarchy'];
                    				}else{
                    				    echo "<a href='/Modules/Mercadolivre/Map/Category' class='btn btn-warning btn-xs' target='_blank'>É necessario atualizar o relacinamento da categoria!</a>";   
                    				}
                    				?></small></h3>
                    				<div class="message-actions"></div>
                    				<div class="box-tools pull-right">
                    					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    				</div>
                    			</div>
                    			<div class="box-body" >
                    				<?php if($availableProductModel->sku == $availableProductModel->parent_id){ ?>
                            			<div class="row">
                                			<div class="col-md-6">	
                                				<div class="form-group ">
                                    				<label>Substituir título por:</label><span class='caracteres_count badge pull-right'>0</span>
                                					<input type='text' id='alternative-title' class='input-sm form-control ' value=''>
                                				</div>
                                			</div>
                                			<div class="col-md-4">
                                    			<div class="form-group pull-right">
                                                      <label>Exposição</label>
                                                      <select id='listing_types' class="input-sm form-control" disabled="">
                                                          <option value='gold_special' >Clássico 13%</option>
                                                          <option value='gold_pro' >Premium 18%</option>
                                                      </select>
                                                </div>
                                			</div>	
                                			<div class="col-md-2">	
                                    			<?php 
                                                    echo "<a class='new_ads_product_new btn btn-app pull-right' 
                                                        product_id='{$availableProductModel->id}' 
                                                        sku='{$availableProductModel->sku}'  
                                                        parent_id='{$availableProductModel->parent_id}' 
                                                        title='Publicar Anúncio'><i class='fa fa-share-alt-square'></i> Mercadolivre
                                                    </a>";
                                                ?>
                                            </div>
                            			</div>
                        			
                        			<?php }?>
                        			<div class="row">
                            			<div class="col-md-12">	
                                			<?php 
                                			if(isset($mlPublications[0])){?>
                            				<table  class="table table-bordered  table-hover display">
                            					<thead>
                            						<tr>
                            							<th>#</th>
                            							<th>MLB/SKU</th>
                            							<th>Título</th>
                            							<th>Preço</th>
                            							<th>Criado</th>
                            							<th>Atualizado</th>
                            							<th>Ações</th>
                            						</tr>
                            					</thead>
                            					<tbody>
                            					<?php 
        //                     					pre($mlPublications);die;
                            					foreach ($mlPublications as $fetch): 
                            					$labelStatus = "label-info";
                            					$labelStatus = $fetch['status'] == 'active' ? "data-toggle='tooltip' title='Anúncio Ativo'  class='label label-success'" : "data-toggle='tooltip' title='Anúncio com erro de sincronização'  class='label label-danger' " ;
                            					$logisticType = $fetch['logistic_type'] == 'fulfillment' ? "<span class='label label-success' data-toggle='tooltip' title='Fulfiment' >{$fetch['logistic_type']}</span>" : "" ;
        
        //                         					$salePrice = str_replace('.',',',$fetch['sale_price']);
                                					$salePrice = $fetch['price'];
                                					
                                					
                                					$linkRemove = '';
                                					
                                					    
                            					    $linkRemove = "<li role='presentation'><a  class='action_ads' action='remove_ads' ads_id='{$fetch['id']}' sku='{$fetch['sku']}'><i class='fa fa-trash'></i> Excluir</a></li>";
                                					
                                					
                                					echo "<tr>
                                						<td><img src='{$availableProductModel->thumbnail}' width='60px'/></td>
                                                        <td>
                                                            <a href='{$fetch['permalink']}'target='_blank' title='Produto anunciado'><span {$labelStatus} >{$fetch['id']}</span></a><br>
                                                            <span class='label label-primary'>{$fetch['sku']}</span> {$logisticType}
                                                        </td>
                                						<td>{$fetch['ml_title']}</td>
                                                        <td>{$fetch['price']}</td>
                                                        <td>".dateTimeBr($fetch['created'], '/')."</td>
                                                        <td>".dateTimeBr($fetch['updated'], '/')."</td>
                                						<td align='center'>
                                                            <div class='dropdown'>
                                                                <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                                                <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                                                    <li role='presentation'><a class='action_ads' action='update_stock' ads_id='{$fetch['id']}' sku='{$fetch['sku']}' product_id='{$availableProductModel->id}' title='Atualizar preço e estoque' ><i class='fa fa-refresh'></i>Atualizar Estoque e Preço</a></li>
                                                                    <li role='presentation'><a class='action_ads' action='update_description' ads_id='{$fetch['id']}' sku='{$fetch['sku']}' product_id='{$availableProductModel->id}' title='Atualizar descrição' ><i class='fa fa-refresh'></i>Atualizar Descrição</a></li>
                                                                    <li role='presentation'><a class='action_ads' action='import_ads_variations' ads_id='{$fetch['id']}' sku='{$fetch['sku']}' title='Reimportar Anúncio' ><i class='fa fa-download'></i>Importar Variações</a></li>
                                					                {$linkRemove}
                                                                </ul>
                                                            </div>
                                            			</td>
                                						</tr>";
                                         
                            		             endforeach;
                            					?>	
                            					</tbody>
                            				</table>
                            				<?php }?>
                        				</div>
                    				</div>
                    			</div><!-- /.box-body -->
                    			<div class="overlay mercadolivre-publication-loading" style='display:none;'>
                                	<i class="fa fa-refresh fa-spin"></i>
                            	</div>
                    		</div><!-- /.box -->
                    	</div>
                    	</div>
                          
                        <div class='row'>
    					<div class="col-md-12">
                    		<div class="box box-primary">
                            	<div class="box-header with-border">
                                	<h3 class="box-title">Ecommerce <?php echo ucwords($ecommerce); ?><small><?php 
                                	if(isset($ecPublications[0]['category'])){
                                	   echo $ecPublications[0]['category']; 
                                	}
                                	?></small></h3>
                                	<div class="message-actions-ecommerce"></div>
                                    	<div class="box-tools pull-right">
                    						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    					</div>
                            	</div><!-- /.box-header -->
                                <div class="box-body">
                                <?php if(!isset($ecPublications[0])){?>
                                	<div class=" pull-right">
                            		   <?php
                            		   $class = '';
                            		   $disabled = 'disabled';
                            		   
                            		   if(empty($ecPublications[0])){
                            		   	
                            		   	$disabled = "";
                            		       
                            		   }
                            		   
                                        echo "<a  class='create_product_{$ecommerce}  btn btn-app' id='share-ecommerce'
                                                product_id='{$availableProductModel->id}'
                                                sku='{$availableProductModel->sku}'
                                                parent_id='{$availableProductModel->parent_id}'
                                                title='Publicar Anúncio' {$disabled} >
                                                <i class='fa fa-share-alt-square'></i> Ecommerce
                                            </a>";
                                        
                                        
                                        ?>
                        			</div>
                        			<?php }else{?>
                    				<table  class="table table-bordered  table-hover display">
                    					<thead>
                    						<tr>
                    							<th>#</th>
                    							<th>ID/SKU</th>
                    							<th>Título</th>
                    							<th>Preço</th>
                    							<th>Criado</th>
                    							<th>Atualizado</th>
                    							<th>Ações</th>
                    						</tr>
                    					</thead>
                    					<tbody>
                    					<?php 
                    					foreach ($ecPublications as $fetch){
                        					$linkRemove = '';
                        					$linkEcommerce = '';
                        					if($availableProductModel->store_id == 4){
                        					   $linkRemove = "<a class='remove_product_magento' product_id='{$fetch['product_id']}' sku='{$fetch['sku']}'><i class='fa fa-trash'></i> Excluir</a>";
                        					   $linkEcommerce = "https://www.fanlux.com.br/catalogsearch/result/?q={$fetch['sku']}";
                        					}
                        					
                        					$labelStatus = $fetch['status'] == '1' ? "label-success" : "label-danger" ;
                        					$salePrice = str_replace('.',',',$fetch['price']);
                        					
                        					
                        					if($ecommerce == 'Onbi'){
                        						$created = $fetch['created_at'];
                        						$updated = $fetch['updated_at'];
                        						$li = "<li role='presentation'><a  class='product_action_{$ecommerce}' action='export_stock' id='{$fetch['product_id']}' product_id='{$availableProductModel->id}' sku='{$availableProductModel->sku}' parent_id='{$availableProductModel->parent_id}' ><i class='fa fa-refresh'></i>Atualizar Estoque</a></li>
                        						<li role='presentation'><a class='create_product_relational_magento' id='{$availableProductModel->id}' product_id='{$fetch['product_id']}' sku='{$availableProductModel->sku}' parent_id='{$availableProductModel->parent_id}' {$disabled} ><i class='fa fa-plus'></i> Criar Produto Configurável </a></li>
                        						<li role='presentation'>{$linkRemove}</li>";
                        					}
                        					 
                        					if($ecommerce == 'Tray'){
                        					    $linkEcommerce = $fetch['url'];
                        						$labelStatus = $fetch['available'] == '1' ? "label-success" : "label-danger" ;
                        						$created = $fetch['created'];
                        						$updated = $fetch['updated'];
                        						$li = "<li role='presentation'><a class='action_tray_product' action='update_stock_price_product_tray' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Preço e Estoque</a> </li>
                        						<li role='presentation'><a class='action_tray_product' action='update_product_tray' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Atualizar Produtos</a> </li>
                        						<li role='presentation'><a class='action_tray_product' action='update_product_image_tray' product_id='{$fetch['product_id']}' id_product='{$fetch['id_product']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Atualizar Fotos</a> </li>
                        						<li role='presentation'><a class='action_tray_product' action='update_product_variations_tray' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Atualizar Variações</a> </li>
                        						<li role='presentation'><a class='action_tray_product' action='update_attributes_product_tray' product_id='{$fetch['product_id']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-refresh'></i> Atualizar Características</a> </li>
                        						<li role='presentation'><a class='action_tray_product' action='delete_tray_product' product_id='{$fetch['product_id']}' id_product='{$fetch['id_product']}' parent_id='{$fetch['parent_id']}'><i class='fa fa-trash'></i> Excluir</a> </li>";
                        					}
                        					
                        					echo "<tr id='{$fetch['product_id']}'>
                        						<td><img src='{$availableProductModel->thumbnail}'  width='60px' /></td>
                                                <td>
                                                    <a href='{$linkEcommerce}' target='_blank'><span class='label {$labelStatus}'>{$fetch['id_product']}</span></a><br>
                                                    <span class='label label-primary'>{$fetch['sku']}</span> 
                                                </td>
                        						<td>{$fetch['title']}</td>
                                                <td>{$salePrice}</td>
                                                <td>".dateTimeBr($created, '/')."</td>
                                                <td>".dateTimeBr($updated, '/')."</td>
                                                <td  align='center'>
                                                 <div class='dropdown'>
                                                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                                        <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                							{$li}
                                						</ul>
                                                    </div>
                                    			</td>
                        						</tr>";
                                 //<li role='presentation'><a   class='update_product_magento' id='{$fetch['product_id']}' product_id='{$availableProductModel->id}' sku='{$availableProductModel->sku}' parent_id='{$availableProductModel->parent_id}' ><i class='fa fa-refresh'></i>Atualizar Produto</a></li>
                        			     }
                    					?>	
                    					</tbody>
                    				</table>
                    				<?php }?>
                    		  	</div>
                    		  	<div class="overlay ecommerce-publication-loading" style='display:none;'>
                              		<i class="fa fa-refresh fa-spin"></i>
                             	</div>
                    		  </div>
                          </div>
                          </div>
                          
                          <?php if(!isset($listRelational)){ ?>
                          <div class='row'>
                          <div class="col-md-12">
                    		<div class="box box-primary">
                    		
                            	<div class="box-header with-border">
                                	<h3 class="box-title">Amazon <small>
                                	<?php 
                    				if(!empty($azCategoryModel->path_from_root)){
                    				    echo $azCategoryModel->path_from_root;
                    				}else{
                    				    echo "<a href='/Modules/Amazon/Map/Category' class='btn btn-warning btn-xs' target='_blank'>É necessario atualizar o relacinamento da categoria!</a>";   
                    				}
                    				?></small>
                    				</h3>
                                    	<div class="box-tools pull-right">
                    						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    					</div>
                                      <!-- tools box -->
                            	</div><!-- /.box-header -->
                            	
                                <div class="box-body">
                                <div class="message-amazon"></div>
                                	<?php if(!isset($azPublications[0])){?>
                                	<div class="row">
	                        			<div class="col-md-9">	
	                        			
	<!--                         				  <div class='callout callout-info'> -->
	<!-- 	                                		<h4>Publicar produtos Amazon</h4> -->
	<!-- 	                                		<p>Os produtos serão enviados </p> -->
	<!-- 	                                	</div> -->
	                        			</div>
	                        			<div class="col-md-3" >	
	                            		   <?php
	                                        echo "<a  class='btn btn-app action_amazon_product pull-right' action='send_products_amazon'
	                                                id='{$availableProductModel->id}' title='Incluir no feed para ser publicado'  >
	                                                <i class='fa fa-share-alt-square'></i> Amazon
	                                            </a>";
	                                        ?>
	                        			</div>
                        			</div>
                        			<?php }else{ ?>
                    				<table  class="table table-bordered  table-hover display">
                    					<thead>
                    						<tr>
                    							<th>#</th>
                    							<th>ID/SKU</th>
                    							<th>Criado</th>
                    							<th>Publicado</th>
                    							<th>Ações</th>
                    						</tr>
                    					</thead>
                    					<tbody>
                    					<?php 
                    					foreach ($azPublications as $fetch){
                    						$published = isset($fetch['published']) ? dateTimeBr($fetch['published'], '/') : 'Processando...' ; 
//                     						$published = empty($fetch['error']) ? $published : $fetch['error'] ;
                        					echo "<tr id='{$fetch['product_id']}'>
                        						<td><img src='{$availableProductModel->thumbnail}'  width='60px' /></td>
                                                <td>
                                                   <span class='label label-success'>{$fetch['product_id']}</span><br>
                                                   <span class='label label-primary'>{$fetch['sku']}</span>
                                                </td>
                                                <td>".dateTimeBr($fetch['created'], '/')."</td>
                                                <td>{$published}</td>
                                                <td  align='center'>
                                                 <div class='dropdown'>
                                                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                                        <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                                            <li role='presentation'><a class='action_amazon_product' action='update_products_amazon' id='{$fetch['product_id']}' ><i class='fa fa-refresh'></i>Atualizar Produto</a></li>
                                                            <li role='presentation'><a class='action_amazon_product'  action='delete_amazon_product' id='{$fetch['product_id']}' ><i class='fa fa-trash'></i> Excluir</a></li>
                                                        </ul>
                                                    </div>
                                    			</td>
                        						</tr>";
                        			     }
                    					?>	
                    					</tbody>
                    				</table>
                    				<?php }?>
                    		  	</div>
                    		  	<div class="overlay amazon-products" style='display:none;'>
                              		<i class="fa fa-refresh fa-spin"></i>
                             	</div>
                             	
                    		  </div>
                          </div>
                          </div>
                          <?php }?>
                          <div class='row'>
                          <div class="col-md-12">
                    		<div class="box box-primary">
                            	<div class="box-header with-border">
                                	<h3 class="box-title">Skyhub</h3>
<!--                                 	<div class='callout callout-info'> -->
<!--                                 		<h4>Publicar produtos Ecommerce</h4> -->
<!--                                 		<p>Produtos produtos produtos produtos produtos produtos -->
<!--                                 		 produtos produtos produtos produtos produtos produtos produtos</p> -->
<!--                                 	</div> -->
                                    	<div class="box-tools pull-right">
                    						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    					</div>
                                      <!-- tools box -->
                            	</div><!-- /.box-header -->
                                <div class="box-body"> 
                                <div class="message-skyhub"></div>
                                <?php if(!isset($skPublications[0])){?>
                                	<div class=" pull-right">
                            		   <?php
                            		   $class = '';
//                             		   $disabled = 'disabled';
                            		   $disabled = 'enabled';
                            		   
                            		   if(empty($ecPublications[0])){
                            		       $class =  "action_skyhub_product";
                            		       $disabled = "";
                            		       
                            		   }
                                        echo "<a  class='btn btn-app {$class} action_skyhub_product' action='send_products_skyhub'
                                                product_id='{$availableProductModel->id}'
                                                sku='{$availableProductModel->sku}'
                                                parent_id='{$availableProductModel->parent_id}'
                                                title='Enviar para skyhub' {$disabled} >
                                                <i class='fa fa-share-alt-square'></i> Skyhub
                                            </a>";
                                        ?>
                        			</div>
                        			<?php }else{ ?>
                    				<table  class="table table-bordered  table-hover display">
                    					<thead>
                    						<tr>
                    							<th></th>
                    							<th>ID/SKU</th>
                    							<th>Título</th>
                    							<th>Preço</th>
                    							<th>Criado</th>
                    							<th>Atualizado</th>
                    							<th>Ações</th>
                    						</tr>
                    					</thead>
                    					<tbody>
                    					<?php 
                    					foreach ($skPublications as $fetch){
                        					echo "<tr id='{$fetch['product_id']}'>
                        						<td><img src='{$availableProductModel->thumbnail}'  width='60px' /></td>
                                                <td>
                                                   <span class='label label-success'>{$fetch['id']}</span><br>
                                                   <span class='label label-primary'>{$fetch['sku']}</span>
                                                </td>
                                                <td>{$productInformation}</td>
                                                <td>{$fetch['price']}</td>
                                                <td>".dateTimeBr($fetch['created'], '/')."</td>
                                                <td>".dateTimeBr($fetch['updated'], '/')."</td>
                                                <td  align='center'>
                                                 <div class='dropdown'>
                                                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                                        <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                                            <li role='presentation'><a class='action_skyhub_product' action='update_products_skyhub' 
                                                                id='{$fetch['product_id']}' product_id='{$fetch['product_id']}' sku='{$fetch['sku']}' 
                                                                parent_id='{$fetch['parent_id']}' ><i class='fa fa-refresh'></i>Atualizar Produto</a>
                                                            </li>
                                                            <li role='presentation'>
                                                                    <a class='action_skyhub_product' action='disable_product_skyhub' 
                                                                    id='{$fetch['id']}' 
                                                                    product_id='{$fetch['product_id']}' 
                                                                    sku='{$fetch['sku']}' 
                                                                    parent_id='{$fetch['parent_id']}' >
                                                                    <i class='fa fa-ban'></i>Desabilitar
                                                                </a>
                                                            </li>
                                                            <li role='presentation'>
                                                                <a class='action_skyhub_product' action='remove_products_skyhub' 
                                                                    id='{$fetch['id']}' 
                                                                    product_id='{$fetch['product_id']}' 
                                                                    sku='{$fetch['sku']}' 
                                                                    parent_id='{$fetch['parent_id']}' >
                                                                    <i class='fa fa-trash'></i>Excluir
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                    			</td>
                        						</tr>";
                                 
                        			     }
                    					?>	
                    					</tbody>
                    				</table>
                    				<?php }?>
                    		  	</div>
                    		  	<div class="overlay skyhub-products" style='display:none;'>
                              		<i class="fa fa-refresh fa-spin"></i>
                             	</div>
                    		  </div>
                          </div>
					  </div>
					  <?php } ?>
                	</form>
                </div>
                <div class="tab-pane <?php echo $tabs['log']; ?>" id='tab_7'>
	                <div class='row'>
		                <div class="col-md-12">
			                <div class="box box-default">
				                <div class="box-header">
				                  <i class="fa  fa-info-circle"></i>
				                  <h3 class="box-title">Sincronização</h3>
				                </div>
				                <div class="box-body scroll500">
				                
				                <ul class='timeline'>
				                
				                <?php
				                
				                foreach($listLog as $key => $log){
				                	$logjson = json_decode($log['json_response']);
				                	$logrray = (array)$logjson;
				                	
				                	switch(key($logrray)){
				                		case "update_available_products": $fa = 'fa-pencil-square-o'; break;
				                		case "import_order_item": $fa = 'fa-cube'; break;
				                		case "update_stock_price_variations_mercadolivre" : $fa = 'fa-legal'; break;
				                		default: $fa = 'fa-database'; break;
				                	}
				                	$fa = strripos(key($logrray), 'mercadolivre') ? 'fa-legal' : $fa;
				                	$fa = strripos(key($logrray), 'skyhub') ? 'fa-cloud' : $fa;
				                	$fa = strripos(key($logrray), 'amazon') ? 'fa-amazon' : $fa;
				                	$fa = strripos(key($logrray), 'adj') ? 'fa-gg' : $fa;
				                	$fa = strripos(key($logrray), 'sysemp') ? 'fa-institution' : $fa;
				                	$fa = strripos(key($logrray), 'onbi') ? 'fa-shopping-cart' : $fa;
				                	$fa = strripos(key($logrray), 'magento') ? 'fa-shopping-cart' : $fa;
				                	$fa = strripos($log['description'], 'Scaquete') ? 'fa-strikethrough' : $fa;
				                	
				                	
				                	
				                	
				                	$json = str_replace('\n', '<br>', $log['json_response']);
				                	$time = getTimeFromTimestamp($log['created']);
				                	if(!isset($dateBr)){
				                		$dateBr = dateWeekFromTimeBr($log['created']);
				                		echo "<li class='time-label'>
				                		<span class='label label-primary'> {$dateBr}</span>
				                		</li>";
				                	}else{
				                		$newDateBr = dateWeekFromTimeBr($log['created']);
				                		if($dateBr != $newDateBr){
				                			$dateBr = $newDateBr;
				                			echo "<li class='time-label'>
				                			<span class='label label-primary'> {$dateBr}</span>
				                			</li>";
				                		}
				                	}
				                    echo "<li><i class='fa {$fa} bg-blue'></i>
		            						<div class='timeline-item'>
		            							<span class='time'><i class='fa fa-clock-o'></i> {$time} &nbsp;&nbsp;<i class='fa fa-user'></i> {$log['user']}</span>
		            							<h3 class='timeline-header'><a href='#'>{$log['description']}</a></h3>";
					                    		if(!empty($json)){
						            				echo "<div class='timeline-body scroll100'><pre><code>{$json}</code></pre></div>";
	                							}
		            					echo "</div>
		            				</li>";
				            				
				                }
					            ?>
				            </ul>
				                
				                
				                
				                
				                
				                
				                
					            </div>
			                </div>
		                </div>
	                </div>
                </div>
        	</div><!-- /.tab-content -->
    	</div><!-- nav-tabs-custom -->
	</div><!-- /.col -->
</div> <!-- /.row -->
<!-- END CUSTOM TABS -->
<?php 
if(isset($parentsProduct)){
?>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
    	<div class="box box-solid">	
    		<div class="box-header">
				<h3 class="box-title">Variações</h3>
			</div>
    		<div class="box-body no-padding">
				<table class="table table-condensed table-hover">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Ean</th>
							<th>Ref.</th>
							<th>Título</th>
							<th>Cor</th>
							<th>Variação</th>
							<th>Qtd.</th>
							<th>Venda</th>
							<th>Peso</th>
							<th>AxLxC</th>
							<th>Fotos</th>
						</tr>
					</thead>
					<tbody>
					<?php 
    					foreach($parentsProduct as $child){
    					    $length = !empty(trim($child['length']))? trim($child['length']) : 'C' ;
    					    $width = !empty(trim($child['width']))? trim($child['width']) : 'L' ;
    					    $height = !empty(trim($child['height'])) ? trim($child['height']) : 'A' ;
    					    $images = getTotalImages($this->storedata['id'], $child['id']);
    					    echo "<tr>
							<td>{$child['sku']}</td>
                            <td>{$child['ean']}</td>
                            <td>{$child['reference']}</td>
							<td><a href='/Products/Product/{$child['id']}/'  title='Editar descrições' target='_blank' >{$child['title']}</a></td>
							<td>{$child['color']}</td>
							<td>{$child['variation']}</td>
							<td>{$child['quantity']}</td>
							<td>{$child['sale_price']}</td>
                            <td>{$child['weight']}</td>
                            <td>{$height} x {$width} x {$length}</td>
                            <td>{$images}</td>
							</tr>";
    					}
					?>
					</tbody>
				</table>
    		</div>
    	</div>
	</div>
</div>
<?php } ?>
<script src="https://transloadit.edgly.net/releases/uppy/v1.14.1/uppy.min.js"></script>