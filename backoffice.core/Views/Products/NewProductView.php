
<?php
if ( ! defined('ABSPATH')) exit;

$tabs = array(
    "available-products",
    "attributes-values",
    "fotos",
    "product-description",
    "product-relational",
    "publications"
    
);


foreach($tabs as $ind){
    if ( in_array($ind, $this->parametros )) {
        $tabs[$ind] = "active";
    }else{
        $tabs[$ind] = "";
    }
}
// pre($tabs);die;


if(isset($_GET['tab']) AND !empty($_GET['tab'])){
    foreach($tabs as $ind){
        if ( $ind == $_GET['tab'] ) {
            $tabs[$ind] = "active";
        }
    }
}
// $tabs['attributes-values'] = isset($_REQUEST['attributes-values']) ? 'active' : '' ;
// $tabs['product-relational'] = isset($_REQUEST['product-relational']) ? 'active' : '' ;


if(!in_array("active", $tabs)){
    $tabs['available-products'] = "active";
}
$productId = isset($availableProductModel->id) ? $availableProductModel->id : '';

$formAction = HOME_URI."/Products/Product/{$productId}";

// pre($availableProductModel);
?>
<div class="row">
	<div class="col-md-12">
	
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
        
            <ul class="nav nav-tabs">
                <li class="<?php echo $tabs['available-products']; ?>"><a href="#tab_1" data-toggle='tab'>Produto</a></li>
                <li class="<?php echo $tabs['attributes-values']; ?>"><a href="#tab_2" data-toggle='tab'>Atributos</a></li>
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

                        //$availableProductModel->parent_id
                        
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
                        
                        if($this->userdata['email'] == 'willians.seo@gmail.com' OR $this->userdata['email'] == 'mutiwillians@gmail.com' OR $this->userdata['email'] == 'willians@fanlux.com.br' OR $this->userdata['email'] == 'willians@miromi.com.br'){
                            
                            echo "<li role='presentation'>
                                <a  href='#' class='product_actions create_product_magento'
                                    product_id='{$availableProductModel->id}'
                                    sku='{$availableProductModel->sku}'
                                    parent_id='{$availableProductModel->parent_id}'
                                    title='Publicar Anúncio' role='menuitem' tabindex='-1' >
                                    <i class='fa fa-share-alt-square'></i> Teste
                                </a>
                            </li>";
                            
                            
                        }
                       
                        ?>
                    </ul>
                </li>
                 <li class="pull-right <?php echo $tabs['product-log']; ?> text-muted"><a href="#tab_7" data-toggle='tab'><i class="fa fa-code"></i> Log</a></li>
            </ul>
            
			<div class="tab-content">
               	<div class="tab-pane <?php echo $tabs['available-products']; ?>" id="tab_1">
                    
                    <div class="message"><?php if(!empty( $availableProductModel->form_msg)){ echo  $availableProductModel->form_msg;}?></div>
                    <form method="POST" action="<?php echo  $formAction; ?>" name="available-products" enctype="multipart/form-data" >
                    
                    <input type='hidden' name='id'  id='id' value='<?php echo $productId; ?>' />
                  
                  
    				<div class='row'>
                    	<div class='col-sm-12'>
                      
                        <div class="box  box-primary">
                	
                        	<div class="box-header with-border">
                            	<h3 class="box-title">Informações do produto <small>Conjunto de informações</small></h3><small class='pull-right'>Criado: <?php echo dateTimeBr($availableProductModel->created, '/'); ?> | Atualizado: <?php echo dateTimeBr($availableProductModel->updated, '/'); ?></small>
                            </div><!-- /.box-header -->
                                  
                            <div class="box-body">
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['sku']; ?>" >
                                		<label>SKU:</label>
                                		<input type='text' name='sku' class='form-control' id="inputError"  value='<?php echo $availableProductModel->sku;?>' />
                                	</div>
                                </div>
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['parent_id']; ?>">
                                		<label>Pai:</label>
                                		<input type='text' name='parent_id' class='form-control' id='parent_id'  value='<?php echo $availableProductModel->parent_id;?>' />
                                	</div>
                                </div>
                                <div class="col-sm-2">
                                	<div class="form-group <?php echo $availableProductModel->field_error['ean']; ?>">
                                		<label>Ean:</label>
                                		<input type='text' name='ean' id='ean' class='form-control' value='<?php echo $availableProductModel->ean; ?>' />
                                	</div>
                                </div>
                                <div class="col-sm-2">
                                	<div class="form-group <?php echo $availableProductModel->field_error['variation_type']; ?> ">
                                	<?php
                                	$voltagem = $tamanho = $volume = $unidade = '';
                                	if(!empty($availableProductModel->variation_type)){
                                	    
                                    	switch($availableProductModel->variation_type){
                                    	    case "voltagem": $voltagem = "selected"; break;  
                                    	    case "tamanho": $tamanho = "selected"; break;  
                                    	    case "volume": $volume = "selected"; break;  
                                    	    case "unidade": $unidade = "selected"; break;  
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
                                <div class="col-sm-2">
                                	<div class="form-group <?php echo $availableProductModel->field_error['variation']; ?>">
                                		<label>Variação</label>
                                		<input type='text' name='variation' class='form-control variation' value='<?php echo $availableProductModel->variation; ?>'  />
                                	</div>
                                </div>
                                <div class="col-sm-6">
                                	<div class="form-group <?php echo $availableProductModel->field_error['title']; ?>">
                                		<label>Título:</label><span class='caracteres_count badge pull-right'><?php echo strlen($availableProductModel->title)?></span>
                                		<input type='text' name='title' class='form-control' id='title' placeholder='Título' value='<?php echo friendlyText($availableProductModel->title); ?>' />
                                	</div>
                                </div>
                                <div class="col-sm-6">
                                	<div class="form-group <?php echo $availableProductModel->field_error['brand']; ?>">
                                		<label>Marca:</label>
                                		<input type="text"  name='brand'  class="form-control  autocomplete_product_attr" id='brand'    value='<?php echo $availableProductModel->brand; ?>'>
                                	</div>
                                </div>
                                <div class="col-sm-6">
                                	<div class="form-group <?php echo $availableProductModel->field_error['color']; ?>">
                                		<label>Cor:</label>
                                		<input type="text" name='color' class="form-control  autocomplete_product_attr" id='color'  value='<?php echo $availableProductModel->color; ?>'>
                                	</div>
                                </div>
                                
                                <div class="col-sm-6">
                                	<div class="form-group <?php echo $availableProductModel->field_error['reference']; ?>">
                                		<label>Referência:</label>
                                		<input type='text' name='reference' id='reference' class='form-control' value='<?php echo $availableProductModel->reference; ?>' />
                                	</div>
                                </div>
                                
                                <div class="col-sm-6">
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
                                <div class="col-sm-6">
                                	<div class="form-group <?php echo $availableProductModel->field_error['category']; ?>">
                                		<label>Categoria:</label>
                                		<select class='form-control' name="category" id='category'>
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
                                <div class="col-sm-12">	
                                	<div class="form-group <?php echo $availableProductModel->field_error['description']; ?>">
                                		<textarea name='description' class="textarea" placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo  $description; ?></textarea>
                                	</div>
                                </div>
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['quantity']; ?>">
                                		<label>Quantidate.</label>
                                		<input type='text' name='quantity' class='form-control quantity' value='<?php echo $availableProductModel->quantity; ?>'  />
                                	</div>
                                </div>
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['price']; ?>">
                                		<label>Preço.:</label>
                                		<input  type='text' name='price' class='form-control price'  value='<?php echo $availableProductModel->price; ?>'  />
                                	</div>
                                </div>
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['sale_price']; ?>">
                                		<label>Preço Venda.:</label>
                                		<input  type='text' name='sale_price' class='form-control sale_price' value='<?php echo $availableProductModel->sale_price; ?>'  />
                                	</div>
                                </div>
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['promotion_price']; ?>">
                                		<label>Preço Promo.:</label>
                                		<input type='text' name='promotion_price' class='form-control promotion_price' value='<?php echo $availableProductModel->promotion_price; ?>'  />
                                	</div>
                                </div>
                                
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['weight']; ?>">
                                		<label>Peso (kg):</label>
                                		<input  type='text' name='weight' class='form-control shipping_measures' measure='weight' value='<?php echo $availableProductModel->weight; ?>'  />
                                	</div>
                                </div>
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['height']; ?>">
                                		<label>Altura (cm):</label>
                                		<input  type='text' name='height' class='form-control shipping_measures' measure='height' value='<?php echo $availableProductModel->height; ?>'  />
                                	</div>
                                </div>
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['width']; ?>">
                                		<label>Largura (cm):</label>
                                		<input type='text' name='width' class='form-control shipping_measures' measure='width' value='<?php echo $availableProductModel->width; ?>'  />
                                	</div>
                                </div>
                                <div class="col-sm-3">
                                	<div class="form-group <?php echo $availableProductModel->field_error['length']; ?>">
                                		<label>Comprimento (cm):</label>
                                		<input type='text' name='length' class='form-control shipping_measures' measure='length' value='<?php echo $availableProductModel->length; ?>'  />
                                	</div>
                                </div>
        					</div>
        					<div class="box-footer">
        					<div class='col-sm-3'>
									<div class='form-group'>
									<label class='col-sm-12 control-label checkbox'>
									<?php 
// 									pre($availableProductModel);
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
    					
                    </div>
                </form>
                </div><!-- /.tab-pane -->
                  
                <div class="tab-pane <?php echo $tabs['attributes-values']; ?>" id="tab_2">
                
                	<div class="message"><?php if(!empty( $attributesValuesModel->form_msg)){ echo  $attributesValuesModel->form_msg;}?></div>
                    
                    <form method="POST" action="<?php echo  $formAction; ?>" name="attributes-values" enctype="multipart/form-data" >
                    <input type='hidden' name='product_id'  id='product_id' value='<?php echo $productId; ?>' />
                    
                    <div class='row'>
                    
                        <?php if(!empty($listInputsAttrMkt)){ ?>
                        
                       	<div class="col-md-12">
                       	
                       	<?php if(isset($listInputsAttr) AND !empty($listInputsAttr)){ ?>	  
                            
                            <div class="box box-primary">
                           
                            	<div class="box-header with-border">
                                	<h3 class="box-title"><?php echo $listInputsAttr[0]['set_attribute'];?> <small>Conjunto de atributos</small></h3>
                                    <div class="box-tools pull-right">
                    					<?php echo "<a class='fa fa-list-alt' href='".HOME_URI."/Products/SetAttributes/edit/{$listInputsAttr[0]['set_attribute_id']}' title='Gerenciar atributos' target='_blank' ></a>"; ?>
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
            							
            							echo "<div class='col-sm-4'><div class='form-group'>
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
        					
                       	
                            <div class="box box-primary">
                            	<div class="box-header with-border">
                                	<h3 class="box-title">Mercadolivre Atributos: <small><?php echo $categoryInfo['hierarchy']; ?></small></h3>
                            	  	<div class="box-tools pull-right">
                    					<?php echo "<a class='fa fa-list-alt' href='".HOME_URI."/Modules/Mercadolivre/Map/Attributes/Category/{$listInputsAttrMkt[0]['category_id']}' title='Gerenciar atributos' target='_blank' ----></a>"; ?>
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
                                    
//                                     pre($listInputsAttrMkt);
                                    $attrAlias = array();
            						foreach ($listInputsAttrMkt as $key => $attr){
            						    
            						    if(!in_array($attr['attribute_id'], $alias)){

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
                    							echo "<div class='col-sm-4'><div class='form-group'>
                    							<label>{$attr['name']} {$required}:</label>
                    							<input type='text' name='attr_values_ml[{$attr['attribute_id']}][{$attr['name']}]' class='form-control {$autoComplete}' 
                                                attribute_id='{$attr['attribute_id']}' category_id='{$attr['category_id']}'
                    							placeholder='{$attr['value']}' value='{$attrValue}' />
                    							</div></div>";
                							}
            						    }
            						
            						}
            						?>
            					</div>
            				</div>
                            
                            
        				
        				</div>
        				
            			
            			
        			<?php }
        			if(!empty($listAttributesRequired)){?>
        			<div class='col-sm-12'>
        			<div class="box box-primary">
                           
                        <div class="box-header with-border">
                        	<h3 class="box-title">Amazon Atributos: <small><?php echo $azAttributesModel->hierarchy; ?></small></h3>
                            <div class="box-tools pull-right">
                    			<?php echo "<a class='fa fa-list-alt' href='".HOME_URI."/Modules/Amazon/Map/Attributes/Xsd/{$azAttributesModel->xsdName}/$azAttributesModel->choice/' title='Gerenciar atributos' target='_blank' ----></a>"; ?>
                    		</div>
                        </div><!-- /.box-header -->
                                      
                            	<div class="box-body">
                				<?php 
            						foreach ($listAttributesRequired as $key => $attr){
            							if(!empty($attr['name'])){
	            							
	            							if(isset($attributesValues[0])){
	            								$attrValue = '';
	            								foreach($attributesValues as $ind => $value){
	            									if($value['attribute_id'] == $attr['name']){
	            										$attrValue = $value['value'];
	            									}
	            								}
	            							}else{
	            								$attrValue = '';
	            							}
	            							$tooltip = !empty($attr['type']) ? "<i class='fa  fa-info-circle' data-toggle='tooltip' title='Tipo de valor esperado: {$attr['type']}'></i>" : '' ;
	            							echo "<div class='col-sm-4'><div class='form-group'>
	            							<label>{$attr['name']} {$tooltip} :</label>
	            							<input type='text' name='attr_values[{$attr['name']}][{$attr['attribute']}]' class='form-control autocomplete-attributes'
	                                        attribute_id='{$attr['alias']}' category_id='{$listInputsAttrMkt[0]['category_id']}'            							
	                                        placeholder='{$attr['attribute']} ' value='{$attrValue}' />
	            							</div></div>";
            							
            							}
            						}
                				?>
            				 	</div>
            				</div>
            				</div>
            				<?php 
            				
	        			}?>
	        			
	        			<div class='col-sm-12'>	
            				<div class="box-footer">
            					<button type='submit' class='btn btn-primary btn-sm pull-right' name='attributes-values'><i class='fa fa-check'></i> Salvar</button>
            				</div>
            			</div>
        			
    			</div>
    			</form>
            </div><!-- /.tab-pane -->
                
                
                
            <!-- PRODUCT IMAGE LIST -->
            <div class="tab-pane <?php echo $tabs['publications']; ?>" id="tab_3">
            <?php if(!empty($productId)){?>
                <div class='row'>
        			<div class="col-sm-12">
                        <div class="box box-primary">
                	
                        	<div class="box-header with-border">
                            	<h3 class="box-title">Imagens do produto <small>Cadastro de fotos</small></h3>
                            </div><!-- /.box-header -->
                                  
                            <div class="box-body">
                            
	                            <div class="col-sm-6">
	                            	<div id='drag-drop-area' class='DashboardContainer'>
	                            	
	                            	</div>
	                           	</div>
	                      
	            				<div class="col-sm-6">
	            				<!-- Uploaded files list -->
									<div class="uploaded-files-server">
									<ul class="products-list product-list-in-box">
				                    <?php 
	                					$pathShow = HOME_URI . "/Views/_uploads/store_id_{$availableProductModel->store_id}/products/{$productId}";
	                					$pathRead = ABSPATH . "/Views/_uploads/store_id_{$availableProductModel->store_id}/products/{$productId}";
	                					if(file_exists($pathRead)){
	                						$iterator = new DirectoryIterator($pathRead);
	                						$picturesArray = array();
	                						foreach ( $iterator as $key => $entry ) {
	                						    $file = $entry->getFilename();
	                						    if($file != '.' AND $file != '..'){
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
	                							
	                							if(trim($partsType[0]) == 'video'){
		                							echo "<li class='item'>
                                    				<div class='product-img''>
		                							<video width='50' height='50'  controls>
								                        <source src='{$pics['path_show']}/{$pics['file']}' type='{$fileType}'>
		                								</video>
		                								</div>
		                							<div class='product-info'>
		                							<a href='{$pics['path_show']}/{$pics['file']}' class='product-title'>{$pics['file']}</a>
		                							 
		                							<a type='button' onclick=\"javascript:removeProductImage(this, '".HOME_URI."', '{$productId}', '{$pics['file']}');\"   class='btn btn-sm btn-default pull-right' title='View Details'><i class='glyphicon glyphicon-trash '></i></a>
		                							&nbsp;
		                							 
		                							<a type='button' href='{$pics['path_show']}/{$pics['file']}' download  class='btn btn-sm btn-default pull-right' title='View Details'><i class='glyphicon glyphicon-download '></i></a>
		                							<span class='product-description'>
		                							{$width} X {$height} - {$size}
		                							</span>
		                							</div>
		                							</li>";
	                							}else{
	                							
	                							
		                							list($width, $height, $type, $attr) = getimagesize(UP_ABSPATH."/store_id_{$this->storedata['id']}/products/{$productId}/{$pics['file']}");
		 
		                							$size = sizeFilter($pics['file_size']);
		                							echo "<li class='item'>
									                      <div class='product-img'>
									                        <img src='{$pics['path_show']}/{$pics['file']}' alt='Product Image' >
									                      </div>
									                      <div class='product-info'>
									                      	<a href='{$pics['path_show']}/{$pics['file']}' class='product-title' target='_blank'>{$pics['file']}</a>
									                      	
									                      	<a type='button' onclick=\"javascript:removeProductImage(this, '".HOME_URI."', '{$productId}', '{$pics['file']}');\"   class='btn btn-sm btn-default pull-right' title='View Details'><i class='glyphicon glyphicon-trash '></i></a>
									                        &nbsp;
									             
									                        <a type='button' href='{$pics['path_show']}/{$pics['file']}' download  class='btn btn-sm btn-default pull-right' title='View Details'><i class='glyphicon glyphicon-download '></i></a>
									                        <span class='product-description'>
									                          {$width} X {$height} - {$size}
									                        </span>
									                      </div>
									                    </li>";
	                							}
								                        
	                						}
	                						
	                					}
	                					
	                				
	                				?>
					                  </ul>
									  
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
            <input type='hidden' name='product_id'  value='<?php echo $productId; ?>' />
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                    	
                        	<div class="box-header with-border">
                            	<h3 class="box-title">Produtos relacionados <small>Cadastro de relacionamento</small></h3>
                            </div><!-- /.box-header -->
                                  
                            <div class="box-body">
                            <div class='row'>
                            	<div class="col-sm-3">
                            		<div class="form-group">
                            			<label>Persquisar por:</label>
                            			<select class='form-control' id="autocomplete-product-type" >
                            			<option value='sku'> Sku</option>
                            			<option value='title'> Título</option>
                            			<option value='reference'> Referência</option>
                            			</select>
                            		</div>
                            	</div>
                                <div class="col-sm-9">
                            		<div class="form-group">
                            		<label>Código:</label>
                            		<input type="text" class="form-control input-sm" id='autocomplete_product_id'  product_id= '<?php echo $productId;?>' tabindex="5"  name='autocomplete-sku'  value=''>
                            		</div>
                            	</div>
                            </div>
                            <div class='row'>	
                                <div class="col-sm-12">
                                
                                	<table  class='table table-condensed' id='log'>
                                	<tr>
                                		<th>ID</th>
                                		<th>Sku</th>
                                		<th>Title</th>
                                		<th>Preço Original</th>
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
                                                <td>{$value['product_relational_id']}</td>
                                                <td>{$value['sku']}</td>
                                                <td><a href='/Products/Product/{$value['product_relational_id']}' target='_blank'>{$value['title']} <i class='fa fa-external-link'></i></a></td>
                                                <td>{$value['sale_price']}</td>
                                                
                                                <td>
                                                    <div class='col-sm-6'>
                                                      <div class='form-group'>
                                                        <input type='text' name='fixed_unit_price[{$value['product_relational_id']}]' class='fixed_unit_price form-control input-sm'  value='{$value['fixed_unit_price']}'>
                                                      </div>
                                                    </div>
                                                </td>
                                                <td>  
                                                    <div class='col-sm-6'>
                                                      <div class='form-group'>
                                                          <input  type='checkbox' name='dynamic_price[{$value['product_relational_id']}]'   class='dynamic_price' value='{$value['dynamic_price']}' {$checked}>
                                                      </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-sm-6'>
                                                      <div class='form-group'>
                                                        <input type='text' name='discount_fixed[{$value['product_relational_id']}]'  class='discount_fixed form-control input-sm'  value='{$value['discount_fixed']}'>
                                                      </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='col-sm-6'>
                                                      <div class='form-group'>
                                                        <input type='text' name='discount_percent[{$value['product_relational_id']}]' class='discount_percent form-control input-sm'  value='{$value['discount_percent']}'>
                                                      </div>
                                                    </div>
                                                </td>
                                                <td> 
                                                    <div class='col-sm-6'>
                                                    <div class='form-group'>
                                                        <input type='text' name='products_relational[{$value['product_relational_id']}]' class='qtd_product_relational form-control input-sm'  value='{$value['qtd']}'>
                                                    </div>
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
                                  	  <div class="col-sm-12">
                    					  <div class="form-group ">
                        					  <label>Title</label>
                        					  <input type='text' name='product_descriptions[Ecommerce][title]' class='form-control title' value='<?php echo $title; ?>'  />
                    					  </div>
                					  </div>
                						
                                  	  <div class="col-sm-12">	
                        				  <div class="form-group ">
                        				  	<textarea class='textarea' name='product_descriptions[Ecommerce][description]'placeholder="Place some text here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $description; ?></textarea>
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
                                  	  <div class="col-sm-12">
                    					  <div class="form-group ">
                        					  <label>Title</label><span class='caracteres badge pull-right'><?php echo strlen($title); ?></span>
                        					  <input type='text' id='char_limit' name='product_descriptions[Mercadolivre][title]' class='form-control title' value='<?php echo $title; ?>'  />
                    					  </div>
                					  </div>
                						
                                  	  <div class="col-sm-12">	
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
            	<div class="message"><?php if(!empty( $productDescriptionModel->form_msg)){ echo  $productDescriptionModel->form_msg;}?></div>
                    <form method="POST" action="<?php echo  $formAction; ?>" name="publications" enctype="multipart/form-data" >
    				<?php if(!empty($productId)){?>
    				<input type='hidden' name='product_id'  id='id' value='<?php echo $productId; ?>' />
    				<div class="row">
                    	<!-- Default box -->
                    	<div class="col-md-6">
                    	
                    		<div class="message">
                    		
                    		</div>
                    		
                    		<div class="box box-primary">
                    			<div class="box-header with-border">
                    				<h3 class="box-title">Mercadolivre <small><?php 
                    				
                    				if(!empty($categoryInfo['hierarchy'])){
                    				    echo $categoryInfo['hierarchy'];
                    				}else{
                    				    echo "<a href='".HOME_URI."/Modules/Mercadolivre/Map/Category' class='btn btn-warning btn-xs' target='_blank'>É necessario atualizar o relacinamento da categoria!</a>";   
                    				}
                    				
                    				
                    				?></small></h3>
                    				<div class="message-actions"></div>
                    				<div class="box-tools pull-right">
                    					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                    				</div>
                    			</div>
                    			<div class="box-body" >
                        			<div class="row">
                        			
                        			<div class="col-sm-9">	
                        				  <div class="form-group ">
                            			<label>Substituir título por:</label><span class='caracteres_count badge pull-right'>0</span>
                        			<input type='text' id='alternative-title' class='input-sm form-control ' value=''>
                        			</div>
                        			</div>
                        			<div class="col-sm-3" >	
                        			<?php 
                                    echo "<a  href='#' class='new_ads_product_new btn btn-app pull-right' 
                                        product_id='{$availableProductModel->id}' 
                                        sku='{$availableProductModel->sku}'  
                                        parent_id='{$availableProductModel->parent_id}' 
                                        title='Publicar Anúncio'><i class='fa fa-share-alt-square'></i> Mercadolivre
                                    </a>";
                                    ?>
                                    </div>
                        			</div>
                        			<div class="row">
                        			<div class="col-sm-12">	
                        			<?php 
                        			if(isset($mlPublications[0])){?>
                    				<table  class="table table-bordered  table-hover display">
                    					<thead>
                    						<tr>
                    							<th>Foto</th>
                    							<th>MLB</th>
                    							<th>Título</th>
                    							<th>Preço</th>
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

                        					$salePrice = str_replace('.',',',$fetch['sale_price']);
                        					
                        					
                        					$linkRemove = '';
                        					
                        					    
                    					    $linkRemove = "<li role='presentation'><a  class='action_ads' action='remove_ads' ads_id='{$fetch['id']}' sku='{$fetch['sku']}'><i class='fa fa-trash'></i> Excluir</a></li>";
                        					
                        					
                        					echo "<tr>
                                                <td><img src='{$fetch['thumbnail']}' /></td>
                                                <td>
                                                    <a href='{$fetch['permalink']}'target='_blank' title='Produto anunciado'><span {$labelStatus} >{$fetch['id']}</span></a>
                                                    <span class='label label-primary'>{$fetch['sku']}</span> {$logisticType}
                                                </td>
                        						<td>{$fetch['ml_title']}</td>
                                                <td>{$salePrice}</td>
                                                <td>".dateTimeBr($fetch['updated'], '/')."</td>
                        						<td align='center'>
                                                    <div class='dropdown'>
                                                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                                        <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                                            <li role='presentation'><a class='action_ads' action='update_stock_price' ads_id='{$fetch['id']}' sku='{$fetch['sku']}' title='Atualizar preço e estoque' ><i class='fa fa-refresh'></i>Preço e Estoque</a></li>
                                                            <li role='presentation'><a class='action_ads' action='import_ads' ads_id='{$fetch['id']}' sku='{$fetch['sku']}' title='Reimportar Anúncio' ><i class='fa fa-refresh'></i>Importar anúncio variações</a></li>
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
    					<div class="col-md-6">
                    		<div class="box box-primary">
                            	<div class="box-header with-border">
                                	<h3 class="box-title">Ecommerce <small><?php 
                                	if(isset($ecPublications[0]['category'])){
                                	   echo $ecPublications[0]['category']; 
                                	}
                                	?></small></h3>
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
                                	<div class=" pull-right">
                            		   <?php
                            		   $class = '';
                            		   $disabled = 'disabled';
                            		   
                            		   if(empty($ecPublications[0])){
                            		       $class =  "create_product_magento";
                            		       $disabled = "";
                            		       
                            		   }
                                        echo "<a  class='{$class}  btn btn-app' id='share-ecommerce'
                                                product_id='{$availableProductModel->id}'
                                                sku='{$availableProductModel->sku}'
                                                parent_id='{$availableProductModel->parent_id}'
                                                title='Publicar Anúncio' {$disabled} >
                                                <i class='fa fa-share-alt-square'></i> Ecommerce
                                            </a>";
                                        
                                        
                                        ?>
                        			</div>
                        			<?php 
                        			if(isset($ecPublications[0])){?>
                    				<table  class="table table-bordered  table-hover display">
                    					<thead>
                    						<tr>
                    							<th>ID/SKU</th>
                    							<th>Título</th>
                    							<th>Preço</th>
                    							<th>Criado</th>
                    							<th>Ações</th>
                    						</tr>
                    					</thead>
                    					<tbody>
                    					<?php 
                    					foreach ($ecPublications as $fetch){
                    					
                        					$linkRemove = '';
                        					
                        					if($availableProductModel->store_id == 4){
                        					   $linkRemove = "<a class='remove_product_magento' product_id='{$fetch['product_id']}' sku='{$fetch['sku']}'><i class='fa fa-trash'></i> Excluir</a>";
                        					}
                        					
                        					$labelStatus = $fetch['status'] == '1' ? "label-success" : "label-danger" ;

                        					$salePrice = str_replace('.',',',$fetch['price']);
                        					echo "<tr id='{$fetch['product_id']}'>
                                                <td>
                                                    <span class='label {$labelStatus}'>{$fetch['product_id']}</span><br>
                                                    <span class='label label-primary'>{$fetch['sku']}</span>
                                                </td>
                        						<td>{$fetch['title']}</td>
                                                <td>{$salePrice}</td>
                                                <td>".dateTimeBr($fetch['created_at'], '/')."</td>
                                                <td  align='center'>
                                                 <div class='dropdown'>
                                                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                                        <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                                            <li role='presentation'><a  href='#' class='upate_product_magento' id='{$fetch['product_id']}' product_id='{$availableProductModel->id}' sku='{$availableProductModel->sku}' parent_id='{$availableProductModel->parent_id}' ><i class='fa fa-refresh'></i>Atualizar Produto</a></li>
                                                            <li role='presentation'><a  href='#' class='create_product_relational_magento' id='{$availableProductModel->id}' product_id='{$fetch['product_id']}' sku='{$availableProductModel->sku}' parent_id='{$availableProductModel->parent_id}' {$disabled} ><i class='fa fa-plus'></i> Criar Produto Configurável </a></li>
                                                            <li role='presentation'>{$linkRemove}</li>
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
                    		  	<div class="overlay ecommerce-publication-loading" style='display:none;'>
                              		<i class="fa fa-refresh fa-spin"></i>
                             	</div>
                    		  </div>
                          </div>
                          <div class="col-md-6">
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
                        			<?php 
                        			if(isset($skPublications[0])){?>
                    				<table  class="table table-bordered  table-hover display">
                    					<thead>
                    						<tr>
                    							<th>ID/SKU</th>
                    							<th>Criado</th>
                    							<th>Atualizado</th>
                    							<th>Ações</th>
                    						</tr>
                    					</thead>
                    					<tbody>
                    					<?php 
                    					foreach ($skPublications as $fetch){
                        					
                        					echo "<tr id='{$fetch['product_id']}'>
                                                <td>
                                                   <span class='label label-success'>{$fetch['product_id']}</span><br>
                                                   <span class='label label-primary'>{$fetch['sku']}</span>
                                                </td>
                                                <td>".dateTimeBr($fetch['created'], '/')."</td>
                                                <td>".dateTimeBr($fetch['updated'], '/')."</td>
                                                <td  align='center'>
                                                 <div class='dropdown'>
                                                        <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                                        <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                                            <li role='presentation'><a  href='' class='action_skyhub_product' action='update_products_skyhub' id='{$fetch['product_id']}' product_id='{$availableProductModel->id}' sku='{$availableProductModel->sku}' parent_id='{$availableProductModel->parent_id}' ><i class='fa fa-refresh'></i>Atualizar Produto</a></li>
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
                <div class="tab-pane <?php echo $tabs['product-log']; ?>" id='tab_7'>
                <div class='row'>
                <div class="col-md-12">
                <pre><?php 
                    foreach($listLog as $key => $log){
                        echo "{$log['name']} - {$log['information']} - {$log['title']} - ".dateTimeBr($log['created']);
                        echo "<br>";
                    }
                
                ?></pre>
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
    	<div class="box box-primary">	
    		<div class="box-body table-responsive no-padding">
    			<div class="col-sm-12">
    				<div class="form-group">
    					<label>Parents:</label>
        				<table class="table table-sort">
        					<thead>
        						<tr>
        							<th>SKU</th>
        							<th>Ean</th>
        							<th>Título</th>
        							<th>Cor</th>
        							<th>Variação</th>
        							<th>Qtd.</th>
        							<th>Venda</th>
        							<th>Peso</th>
        							<th>AxLxC</th>
        							<th>Fotos</th>
        							<th></th>
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
        							<td><a href='".HOME_URI."/Products/Product/{$child['id']}'  title='Editar descrições' target='_blank' >{$child['title']}</a></td>
        							<td>{$child['color']}</td>
        							<td>{$child['variation']}</td>
        							<td>{$child['quantity']}</td>
        							<td>{$child['sale_price']}</td>
                                    <td>{$child['weight']}</td>
                                    <td>{$height} x {$width} x {$length}</td>
                                    <td>{$images}</td>
                                    <td>
                                        <a  href='".HOME_URI."/Products/Product/{$child['id']}'  title='Editar descrições' ><i class='fa fa-pencil-square-o'></i></a>
                                    </td>
        							</tr>";
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
<?php 
}
?>