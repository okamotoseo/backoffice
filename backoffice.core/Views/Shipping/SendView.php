<?php if ( ! defined('ABSPATH')) exit;?>
<div class="row">
	<!-- Default box -->
	<div class="col-xs-12">
	
		<div class="message">
			<?php if(!empty($sucess['message'])){ echo "<div class='callout callout-success'><h4>Tip!</h4><p>".$sucess['message']."</></div>";}?>
		</div>
		
        <!-- Application buttons -->
        <div class="box">
            <div class="box-header">
            	<h3 class="box-title">Remessas</h3>
            </div>
            <div class="box-body">
                <div class="col-xs-12">
                <form  method="POST" action="<?php echo HOME_URI."/Shipping/Send/"; ?>" name="add-shipping-send">
                    <input type='hidden' name='user' value='<?php echo $this->userdata['name']; ?>' />
                    <div class='row'>
						<div class="form-group">
						<?php 
							$others = $mercado_envios = $correios = '';
							switch($shippingSendModel->company){
							    case "mercado_envios": $mercado_envios = "selected"; break;
							    case "correios": $correios = "selected"; break;
							    case "others": $others = "selected"; break;
							    default : $others = "selected"; break;
							}
							
							?>
							<label for="shipping_company" class='col-sm-2 control-label'>Transportador:</label>
							<div class="col-sm-2">
								<select  name="company" class="form-control">
								<option value='others' <?php echo $others; ?>>Outros</option>
								<option value='mercado_envios' <?php echo $mercado_envios; ?>>Mercado Envios</option>
								<option value='correios' <?php echo $correios; ?>>Correios</option>
								</select>
							</div>
							<div class="col-sm-2">
                				<button type='submit' name='new-shipping' class='btn btn-default pull-right' value='create' ><i class="fa fa-plus"></i> Nova Remessa</button>
                        	</div>
                        </div>
            		</div>
                </form>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
    
    <?php if(!empty($shippingSendModel->id)){ ?>
    <div class="col-xs-12">
    <!-- Application buttons -->
        <div class="box">
            <div class="box-header">
            	<h3 class="box-title">Leitura de Códigos de Barras</h3>
            </div>
            <div class="box-body">
            	<div class="col-xs-12">
                    <div class='row'>
                   
                 <?php 
                 $printLink = "onclick=\"javascript:printShippingCodes('".HOME_URI."', {$shippingSendModel->id} );\"";
                 
                 ?>
                  <a class="btn btn-app pull-right" <?php echo $printLink; ?> >
                    <i class="fa fa-cubes"></i> Fechar Remessa
                  </a>
                 
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class='row'>
						<div class="form-group">
						
							<label for="code" class='col-sm-2 control-label'>Código pacote</label>
							<div class="col-sm-2">
								<input name="code" class="form-control barcode-pack-in" company='<?php echo $shippingSendModel->company; ?>' id='barcode' user='<?php echo $this->userdata['name']; ?>' shipping_send_id='<?php echo $shippingSendModel->id; ?>' value=''/>
							</div>
                        </div>
            		</div>
            		
            		<div class='row'>
                		<div class="col-xs-12">
                			<table class='table table-condensed '>
                			<tr class='table-hendling-in'>
                    			<th>Código</th>
                    			<th>Criado</th>
                    			<th></th>
                			</tr>
                    		<?php 
                			foreach($listShippingSendCode as $k => $sendCode){
                			   
                			    $removeBtn = "onclick=\"javascript:removeShippingCode('".HOME_URI."',{$sendCode['shipping_send_id']}, {$sendCode['id']}, '".$sendCode['code']."' );\"";
                			    
                			    echo "<tr id='{$sendCode['id']}' >
                                        <td>{$sendCode['code']}</td>
                                        <td>".dateTimeBr($sendCode['created'], '/')."</td>
                                        <td>
                                            <a class=' remove-pack-barcode pull-right danger' {$removeBtn} ><i class='fa fa-remove'></i></a>
                                        </td>
                                </tr>";
                			}
                			?>
                    		</table>
                		</div>
            		</div>
            		
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
    <?php } ?>
    
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
	        	<h3 class="box-title">Listagem de Remessa</h3>
			</div><!-- /.box-header -->
			<div class="box-body no-padding">
			<div class="col-xs-12">
			<table class='table table-condensed'>
			<tr>
				<th>#</th>
    			<th>Criado</th>
    			<th>Transportador</th>
    			<th>Situação</th>
            	<th>Pacotes</th>    			
            	<th></th>
			</tr>
			
			<?php 
			
			foreach($listShippingSend as $k => $send){
			    
			    $deletePrintLink = '';
			    
			    if($send['status'] != 'closed'){
			        $deletePrintLink =  "<a class='fa fa-remove pull-right btn-remove-shipping-send' shipping_send_id='{$send['id']}' href='#' ></a>" ;
			    }else{
			        $deletePrintLink = "<a class='fa fa-print pull-right' href='#'  onclick=\"javascript:printShippingCodes('".HOME_URI."', {$send['id']} );\"></a>";
			    }
			    
			    $createdLink = dateTimeBr($send['created'], '/');
			    
			    if($send['status'] != 'closed'){
			         $createdLink = "<a href='".HOME_URI."/Shipping/Send/id/{$send['id']}'>".dateTimeBr($send['created'], '/')." <i class='fa fa-edit'></i></a>";
			    }
			    
			    echo "<tr id='{$send['id']}'>
                        <td>{$send['id']}</td>
                        <td>{$createdLink}</td>
                        <td>{$send['company']}</td>
                        <td>{$send['status']}</td>
                        <td>{$send['packs']}</td>
                        <td>{$deletePrintLink}
                        </td>
                </tr>";
			}
			
			?>
			</table>
			</div>
			</div>
		</div>
	</div>
</div>