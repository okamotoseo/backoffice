<?php if ( ! defined('ABSPATH')) exit;?>
<div class="row">
	<!-- Default box -->
	<div class="col-sm-12">
	
		<div class="message">
			<div class="message"><?php if(!empty( $pickingModel->form_msg)){ echo  $pickingModel->form_msg;}?></div>
		</div>
		
        <!-- Application buttons -->
        <div class="box">
            <div class="box-header">
            	<h3 class="box-title"><?php echo $this->title; ?></h3>
            </div>
            <div class="box-body">
                <div class="col-sm-12">
                <form  method="POST" action="<?php echo HOME_URI."/Shipping/Picking/"; ?>" name="add-picking">
                    <input type='hidden' name='user' value='<?php echo $this->userdata['name']; ?>' />
                    <div class='row'>
						<div class="form-group">
						<?php 
// 						pre($this);die;
							$others = $mercado_envios = $correios = '';
							switch($pickingModel->picker){
							    case "others": $others = "selected"; break;
							    default : $others = "selected"; break;
							}
							
							?>
							<label for="picker" class='col-sm-2 control-label'>Coletor:</label>
							<div class="col-sm-2">
								<select  name="picker" class="form-control">
								<option value='<?php echo $this->storedata['store']; ?>'><?php echo $this->storedata['store']; ?></option>
								</select>
							</div>
							<div class="col-sm-2">
                				<button type='submit' name='new-picking' class='btn btn-default pull-right' value='create' ><i class="fa fa-plus"></i> Nova Lista de Coleta</button>
                        	</div>
                        </div>
            		</div>
                </form>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
    
    <?php if(!empty($pickingModel->id)){ ?>
    <div class="col-sm-12">
    <!-- Application buttons -->
        <div class="box">
            <div class="box-header">
            	<h3 class="box-title">Leitura de Pedidos</h3>
            </div>
            <div class="box-body">
            	<div class="col-sm-12">
                    <div class='row'>
                   
                 <?php 
                 $printLink = "onclick=\"javascript:printPickingProducts('".HOME_URI."', {$pickingModel->id} );\"";
                 
                 ?>
                  <a class="btn btn-app pull-right" <?php echo $printLink; ?> >
                    <span class="glyphicon glyphicon-list-alt"></span> Fechar Lista de Coleta
                  </a>
                 
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class='row'>
						<div class="form-group">
						
							<label for="code" class='col-sm-2 control-label'>Código pedido</label>
							<div class="col-sm-2">
								<input name="PedidoId" class="form-control pickin-product-in" picker='<?php echo $pickingModel->picker; ?>' id='PedidoId' user='<?php echo $this->userdata['name']; ?>' picking_id='<?php echo $pickingModel->id; ?>' value=''/>
							</div>
                        </div>
            		</div>
            		
            		<div class='row'>
                		<div class="col-sm-12">
                			<table class='table table-condensed'>
                			<tr class='table-hendling-in'>
                    			<th>Sku/EAN</th>
                    			<th>Título/Marca</th>
                    			<th>Cor/Variação/Ref.</th>
                    			<th>Peso/Medidas</th>
                    			<th>QTD</th>
                    			<th></th>
                    			
                			</tr>
                    		<?php 
//                     		pre($listPickingProducts);die;
                    		foreach($listPickingProducts as $k => $pickingProduct){
                			   
                    		    
                			    
                    		    echo "<tr id='{$pickingProduct['id']}' class='{$pickingProduct['id']}' bgcolor='#f4f4f4'>
                                        <td>{$pickingProduct['sku']}<br>{$pickingProduct['ean']}</td>
                                        <td>{$pickingProduct['information']['title']}<br>{$pickingProduct['information']['brand']}</td>
                                        <td>{$pickingProduct['information']['color']}/{$pickingProduct['information']['variation']}<br>{$pickingProduct['information']['reference']}</td>
                                        <td>Peso: {$pickingProduct['information']['weight']}<br>Medidas: {$pickingProduct['information']['height']} x {$pickingProduct['information']['width']} x {$pickingProduct['information']['length']}</td>
                                        <td id='qty-{$pickingProduct['id']}'><strong>{$pickingProduct['quantity']}</strong></td>
                                        <td> </td>
                                </tr>";
                    		    echo "<tr><td></td><td colspan='4'><table class='table {$pickingProduct['id']}' >";
                    		    foreach($pickingProduct['orders'] as $i => $order){
                    		        
                    		        $removeBtn = "onclick=\"javascript:removePickingProduct(this, '".HOME_URI."', {$order['picking_id']}, '{$order['picking_product_id']}', '{$order['order_id']}', '{$order['PedidoId']}' );\"";
                    		        
                    		          echo "<tr>
                                        <td></td>
                        		        <td>{$order['PedidoId']}</td>
                                        <td>{$order['short_description']}</td>
                                        <td>{$order['marketplace']}</td>
                                        <td>{$order['quantity']}</td>
                                        <td>{$order['status']}</td>
                                        <td><a class=' remove-product pull-right danger' {$removeBtn} ><i class='fa fa-remove'></i></a></td>                    		          
                                        </tr>";
                    		        
                    		        
                    		    }
                    		    echo "</table></td><td colspan='2'></td></td></tr><tr><td colspan='7'></td></tr>";
                    		    
                			}
                			?>
                    		</table>
                		</div>
            		</div>
            		
                </div>
            </div><!-- /.box-body -->
            <div class="overlay " style='display:none;'>
            		<i class="fa fa-refresh fa-spin"></i>
        		</div>
        </div><!-- /.box -->
    </div>
    <?php } ?>
    
	<div class="col-sm-12">
		<div class="box box-primary">
			<div class="box-header">
	        	<h3 class="box-title">Listagem das Coletas</h3>
			</div><!-- /.box-header -->
			<div class="box-body no-padding">
			<div class="col-sm-12">
			<table class='table table-condensed'>
			<tr>
				<th>#</th>
    			<th>Criado</th>
    			<th>Coletor</th>
    			<th>Situação</th>
            	<th></th>
			</tr>
			
			<?php 
			if(isset($listPicking)){
    			foreach($listPicking as $k => $picking){
    			    
    			    $deletePrintLink = '';
    			    
    			    if($picking['status'] != 'closed'){
    			        $deletePrintLink =  "<a class='fa fa-remove pull-right btn-remove-picking' picking_id='{$picking['id']}' href='#' ></a>" ;
    			    }else{
    			        $deletePrintLink = "<a class='fa fa-print pull-right' href='#'  onclick=\"javascript:printPickingProducts('".HOME_URI."', {$picking['id']} );\"></a>";
    			    }
    			    
    			    $createdLink = dateTimeBr($picking['created'], '/');
    			    
    			    if($picking['status'] != 'closed'){
    			        $createdLink = "<a href='".HOME_URI."/Shipping/Picking/id/{$picking['id']}'>".dateTimeBr($picking['created'], '/')." <i class='fa fa-edit'></i></a>";
    			    }
    			    
    			    echo "<tr id='{$picking['id']}'>
                            <td>{$picking['id']}</td>
                            <td>{$createdLink}</td>
                            <td><a href='/Shipping/Packing/id/{$picking['id']}/'>{$picking['picker']}</a></td>
                            <td>{$picking['status']}</td>
                            <td>{$deletePrintLink}
                            </td>
                    </tr>";
    			}
			}
			?>
			</table>
			</div>
			</div>
		</div>
	</div>
</div>