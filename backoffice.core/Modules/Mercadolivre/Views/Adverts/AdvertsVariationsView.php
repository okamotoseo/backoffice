<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $advertsModel->form_msg)){ echo  $advertsModel->form_msg;}?></div>
		<div class="box box-primary">
			<form role="form" method="POST" action="<?php echo HOME_URI ?>/Modules/Mercadolivre/Adverts/AdvertsRelationship" name="filter-product" >
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar anúncios</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='<?php echo HOME_URI ?>/Modules/Mercadolivre/Adverts/AdvertsRelationship' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-ms-2">
							<div class="form-group">
								<label for="sku">MLB:</label> 
								<input type="text" name="id"  id='id' class="form-control input-sm" value="<?php echo $advertsModel->id; ?>">
							</div>
						</div>
						<div class="col-ms-1">
							<div class="form-group">
								<label for="sku">SKU:</label> 
								<input type="text" name="sku"  id='sku' class="form-control input-sm" value="<?php echo $advertsModel->sku; ?>">
							</div>
						</div>
						<div class="col-ms-2">
							<div class="form-group">
								<label for="title">Título:</label> 
								<input type="text" name="title"  id='title' class="form-control input-sm" value="<?php echo $advertsModel->title; ?>">
							</div>
						</div>
						<div class="col-ms-1">
							<div class="form-group">
								<label for="reference">Referência:</label> 
								<input type="text" name="reference"  id='reference' class="form-control input-sm" value="<?php echo $advertsModel->reference; ?>">
							</div> 
						</div>
						<div class="col-ms-1">
    						<div class="form-group">
    						<?php 
							$status = $status1 = '';
							switch($advertsModel->status){
							    case "active": $status = "selected"; break;
							    case "paused": $status1 = "selected"; break;
							}
							
							?>
    								<label for="status">Status:</label>
    								<select id="status" name="status" class="form-control input-sm">
    								<option value=''>Todos</option>
    								<option value='active' <?php echo $status; ?>>Ativo</option>
    								<option value='paused' <?php echo $status1; ?>>Pausado</option>
    								</select>
    							</div>
						</div>
						
		
						<div class="col-ms-1">
							<div class="form-group">
							<?php 
							$select5 = $select50 = $select100 = $select150 = $select200 = '';
							switch($advertsModel->records){
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
					<button type='submit' name='available-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row">
	<!-- Default box -->
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
			<div class="box-body" >
				<table  class="table table-striped  table-hover display" id="search-default--" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
					<thead>
						<tr>
							<th>Img</th>
							<th>ID</th>
							<th>Sku</th>
							<th>Título</th>
							<th>Qtd</th>
							<th>Preço</th>
							<th>Atualizado</th>
							<th>Status</th>
							<th>Ações</th>
							
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($list as $fetch): 
// 					pre($fetch);die;
    					$salePrice = str_replace('.',',',$fetch['price']);
    					$updated = dateTimeBr($fetch['updated']);
    					echo "<tr>
                            <td><img src='{$fetch['thumbnail']}' /></td>
                            <td>{$fetch['id']}</td>
    						<td>{$fetch['sku']}</td>
    						<td>{$fetch['title']}</td>
    						<td>{$fetch['available_quantity']}</td>
                            <td>{$salePrice}</td>
                            <td>{$updated}</td>
                            <td>{$fetch['status']}</td>
                            <td>
                                <div class='dropdown'>
                                    <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                    <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                        <li role='presentation'><a class='action_meli_relationship_variation' action='delete_products_variation_meli' ads_id='{$fetch['id']}'> Deletar</a> </li>   
                     
                                    </ul>
                                </div>
                            </td>
    						</tr>";
    					
    					echo "<tr><td></td><td colspan='7'><table  class='table table-bordered  no-padding'>";
    					
//                                 echo "<tr>";
//                 					foreach($fetch['variations'] as $k => $variation){
//                 					   echo "<th>{$variation['name']}</th>";
//                 					}
//             				echo "</tr>";
            				$variationIdAnterior = '';
            				foreach($fetch['variations'] as $variationId =>  $variations){
            				    
            				    
            				    echo "<tr id='{$variationId}'>
                                        <td>
                                        <div class='dropdown'>
                                            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                            <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                                <li role='presentation'><a class='action_meli_relationship_variation' action='delete_products_variation_meli' ads_id='{$fetch['id']}' variation_id='{$variationId}'> Deletar</a> </li>   
                             
                                            </ul>
                                        </div>
                                        </td>";
            				    foreach($variations as $attr => $value){
            				        
            				        if( $attr == 'sku'){
            				            $edit = !empty($value) ? $value : "Editar";
            				            echo "<td><strong>{$attr}</strong</td><td><a class='inlineEditSkuMeliVar'  ads_id='{$fetch['id']}' variation_id='{$variationId}' >{$edit}</a></td>";
            				            
            				        }else{
            				            echo "<td><strong>{$attr}</strong</td><td>{$value}</td>";
            				        }
            				    }
            				    echo "</tr>";
            				  
            				}
            				
            				echo "</tr>";
            				echo "</table>
                                </td></tr>";
		             endforeach;
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $advertsModel->pagina_atual, HOME_URI."/Modules/Mercadolivre/Adverts/Adverts", array(
			    "id" => $advertsModel->id,
			    "sku" => str_replace("%", "_x_", $advertsModel->sku),
			    "title" => str_replace("%", "_x_", $advertsModel->title),
			    "parent_id" => str_replace("%", "_x_", $advertsModel->parent_id),
			    "reference" => str_replace("%", "_x_", $advertsModel->reference),
			    "records" => $advertsModel->records
			));?>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>

<!-- 
			    if($variation2['variation_id'] != $variationIdAnterior){
                				        $variationIdAnterior = $variation2['variation_id'];
                				        echo "<tr>";
                				    }
                				    
                				    $value = !empty($variation2['information']) ? $variation2['information'] : $variation2['value'] ;
                				    echo "<td>{$value}</td>";
                				    
                				    if($variation2['variation_id'] != $variationIdAnterior){
                				        echo "</tr>";
                				        
                				        
                				    }
foreach($fetch['variations'] as $k => $variation){
                				   echo "<tr>
                                            <td>{$variation['name']}</td>
                        					<td>{$variation['value']}</td>
                        					<td>{$variation['information']}</td>
                        					<td>{$variation['id']}</td>
                        					<td>{$variation['sku']}</td>
                        					
                                        </tr>";
                                    }
<a class='update_ads_product' id='{$fetch['id']}' sku='{$fetch['sku']}'>Atualizar</a>
<a class='remove_ads_product' id='{$fetch['id']}' sku='{$fetch['sku']}'>Finalizar</a>
 -->