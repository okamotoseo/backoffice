<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class="col-md-12">
		<div class="message"><?php if(!empty( $advertsModel->form_msg)){ echo  $advertsModel->form_msg;}?></div>
		<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Filtrar anúncios</h3>
    				<div class='box-tools pull-right'>
        	        	<div class="form-group">
        	        		<a href='<?php echo HOME_URI ?>/Modules/Mercadolivre/Adverts/ImportAdverts' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
        	        	</div>
    	        	</div>
				</div>
				<div class="box-body">
					<div class="row">
					
						<div class="col-md-3">
							<div class="form-group">
								<label for="sku">Relacionar com o SKU:</label> 
								<input type="text" name="sku" id='sku' class="form-control input-xs" value="">
							</div>
						</div>
					
					
					
						<div class="col-md-4">
						<label for="ads_id">MLB:</label> 
							<div class="input-group input-group-xs">
							
							<input type="text" class="form-control" id='ads_id' value=''>
		                    <div class="input-group-btn">
		                      <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Ação <span class="fa fa-caret-down"></span></button>
		                      <ul class="dropdown-menu">
		                        <li><a class='action_import_ads' action='import_ads'>Importar Anúncio</a></li>
		                        <li><a class='action_import_ads' action='import_ads_variation'>Importar Variações</a></li>
		                      </ul>
		                    </div><!-- /btn-group -->
		                    
		                  </div>
	                  </div>

					</div>
				</div>
				<div class="overlay " style='display:none;'>
                	<i class="fa fa-refresh fa-spin"></i>
                </div>
		</div>
	</div>
</div>

<?php 
if(isset($list)){
?>

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

<?php 
}

?>
