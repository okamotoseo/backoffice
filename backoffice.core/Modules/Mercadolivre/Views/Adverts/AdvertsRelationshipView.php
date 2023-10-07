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
						<div class="col-md-2">
							<div class="form-group">
								<label for="sku">MLB:</label> 
								<input type="text" name="id"  class="form-control input-sm" value="<?php echo $advertsModel->id; ?>">
							</div>
						</div>

						
		
						<div class="col-md-1">
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
					<button type='submit' name='ml-products-filter' class='btn btn-primary btn-sm pull-right' ><i class='fa fa-search'></i> Procurar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row">
	<!-- Default box -->
	<div class="col-md-12">
	
		<div class="message-actions">
		
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
					if(!empty($list)){
					foreach ($list as $fetch): 
    					$salePrice = str_replace('.',',',$fetch['price']);
    					$updated = dateTimeBr($fetch['updated']);
    					echo "<tr>
                            <td><img src='{$fetch['thumbnail']}' /></td>
                            <td><a href='{$fetch['permalink']}' target='_blank' >MLB{$fetch['id']}</a></td>
    						<td>{$fetch['sku']}</td>
    						<td><a href='/Products/Product/{$fetch['product_id']}/' target='_blank'> {$fetch['title']}</a></td>
    						<td>{$fetch['available_quantity']}</td>
                            <td>{$salePrice}</td>
                            <td>{$updated}</td>
                            <td>{$fetch['status']}</td>
                            <td>
                                <div class='dropdown'>
                                    <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
                                    <ul class='dropdown-menu pull-right' style='min-width:100px'>
                                        <li role='presentation'><a class='action_ads' action='update_stock_price' ads_id='{$fetch['id']}' sku='{$fetch['sku']}' title='Atualizar preço e estoque' ><i class='fa fa-refresh'></i>Preço e Estoque</a></li>                                        
                                        <li role='presentation'><a class='action_meli_relationship_variation' action='delete_products_variation_meli' ads_id='{$fetch['id']}'><i class='fa fa-trash'></i>Excluir</a> </li>   
                                    </ul>
                                </div>
                            </td>
    						</tr>";
    					
    					echo "<tr><td></td><td colspan='7'><table  class='table table-bordered table-condensed no-padding'><thead><tr><th>#</th>";
    						if(isset($fetch['variations'])){
	            				$variationIdAnterior = '';
	            				foreach($fetch['variations'] as $variationId =>  $variations){
	            					foreach ($variations as $i => $val){
	            						$heads[$i] = 'th';
	            					}
	            						
	            				}
	            				foreach ($heads as $head => $th){
	            					echo "<{$th}>{$head}</{$th}>";
	            				}
	            				echo "</tr></thead><tbody>";
	            				
	            				foreach($fetch['variations'] as $variationId =>  $variations){
	            				    echo "<tr id='{$variationId}'>
	                                        <td>
	                                        <div class='dropdown'>
	                                            <a class='btn dropdown-toggle' data-toggle='dropdown' href='# ariel-expanded='true''><span class='fa fa-ellipsis-v'></span></a>
	                                            <ul class='dropdown-menu pull-right' style='min-width:100px'>
	                                                <li role='presentation'><a class='action_meli_relationship_variation' action='delete_products_variation_meli' ads_id='{$fetch['id']}' variation_id='{$variationId}'> Excluir</a> </li>   
	                             
	                                            </ul>
	                                        </div>
	                                        </td>";
	            				    foreach($variations as $attr => $value){
	            				    	
	            				        if( $attr == 'sku'){
	            				            $edit = !empty($value) ? $value : "Editar";
// 	            				            echo "<td><strong>{$attr}</strong</td><td><a class='inlineEditSkuMeliVar'  ads_id='{$fetch['id']}' variation_id='{$variationId}' >{$edit}</a></td>";
	            				            echo "<td><a class='inlineEditSkuMeliVar'  ads_id='{$fetch['id']}' variation_id='{$variationId}' >{$edit}</a></td>";
	            				            
	            				        }else{
// 	            				            	echo "<td><strong>{$attr}</strong</td><td>{$value}</td>";
	            				            	echo "<td>{$value}</td>";
	            				        }
	            				    }
	            				    echo "</tr>";
	            				  
	            				}
    						}
            				
            				echo "</tr></tbody>";
            				echo "</table>
                                </td></tr>";
		             endforeach;
					}
					?>	
					</tbody>
				</table>
			<?php pagination($totalReg, $advertsModel->pagina_atual, HOME_URI."/Modules/Mercadolivre/Adverts/AdvertsRelationship/", array(
			    "id" => $advertsModel->id,
			    "sku" => str_replace("%", "_x_", $advertsModel->sku),
			    "product_id" => str_replace("%", "_x_", $advertsModel->product_id),
			    "records" => $advertsModel->records
			));?>
			</div><!-- /.box-body -->
			<div class="overlay meli-adverts" style='display:none;'>
          		<i class="fa fa-refresh fa-spin"></i>
         	</div>
		</div><!-- /.box -->
	</div>
</div>