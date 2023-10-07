<?php 
if ( ! defined('ABSPATH')) exit;

$tabs = array(
    "orders-to-group", 
    "plp-list", 
    "request-collect"
);
foreach($tabs as $ind){
    if ( in_array($ind, $this->parametros )) {
        $tabs[$ind] = "active";
    }else{
        $tabs[$ind] = "";
    }
}

if(!in_array("active", $tabs)){
    $tabs['orders-to-group'] = "active";
}
?>

<div class="row">
	<div class="col-md-12">
	
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
        
            <ul class="nav nav-tabs">
                <li class="<?php echo $tabs['orders-to-group']; ?>"><a href="#tab_1" data-toggle='tab'>Pedidos para Agrupar</a></li>
                <li class="<?php echo $tabs['plp-list']; ?>"><a href="#tab_2" data-toggle='tab'>Pré Lista de Postagem (PLP)</a></li>
                <li class="<?php echo $tabs['request-collect']; ?>"><a href="#tab_3" data-toggle='tab'>Solicitar Coleta</a></li>
                <li class="pull-right"><a href="/Modules/Skyhub/Shipments/Shipments/" ><i class="fa fa-refresh"></i></a></li>
             </ul>
            
			<div class="tab-content">
			
            	<div class="tab-pane <?php echo $tabs['orders-to-group']; ?>" id="tab_1">
                  	<div class="box box-solid">
                        <div class="box-header ">
                        	<div id='message-orders-to-group'></div>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class='row'>
                                <div class="col-sm-3">
                            		<div class="form-group">
                                        <select id='select_action_skyhub_shipments' class='form-control input-sm'>
                            				<option value='select' >Ações</option>
                            				<option value='group_skyhub_plp' >Agrupar PLPs</option>
                        				</select>
                    				</div>
                				</div>
            				</div>
            				<div class='row'>
                                <div class="col-sm-12">
                                   	<?php
                                        echo "<table class='table table-condensed  no-padding'>
                                        	<thead>
                                        		<tr>
                                                    <th><input type='checkbox' id='' class='flat-red select_all_skyhub_shipments' /></th>
                                        			<th>ShippindId</th>
                                        			<th>Expira</th>
                                        			<th>Impresso</th>
                                        			<th>Tipo</th>
                                        		</tr>
                                        	</thead>
                                        	<tbody>";
                                        
                                        if(isset($listOrdersReadyToGroupPlp)){
                                        foreach($listOrdersReadyToGroupPlp as  $k => $order){
                                            echo "<tr class='warning' >
                                                    <td><input type='checkbox' order_code='{$order->code}' class='flat-red select_one_skyhub_shipment' /></td>
                                        			<td>{$order->code}</td>
                                                    <td>{$order->customer}</td>
                                                    <td>{$order->value}</td>
                                                    <td>{$order->shipping}</td>
                                                </tr>";
                                            
                                        }
                                        }
                                        echo "</tbody></table>";
                                    ?>
                    			</div>
                    		</div>
                    	</div><!-- /.box-body -->
                        <div class="overlay skyhub-shipments" style='display:none;'>
                    		<i class="fa fa-refresh fa-spin"></i>
                		</div>
              		</div><!-- /.box -->
                </div><!-- /.tab-pane -->
                
                <div class="tab-pane <?php echo $tabs['plp-list']; ?>" id="tab_2">
                	<div class="box box-solid">
                        <div class="box-header ">
                        	<div id='message-plp'></div>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class='row'>
                                <div class="col-sm-3">
                        			<div class="form-group">
                            			<select id='select_action_skyhub_plp' class='form-control input-sm'>
                            				<option value='select' >Ações</option>
                            				<option value='download_pdf_shipments_plp' >Baixar PDF Etiqueta</option>
                            				<option value='ungroup_skyhub_plp' >Desagrupar PLP</option>
                        				
                        				</select>
                        			</div>
                				</div>
            				</div>
            				<div class='row'>
                                <div class="col-sm-12">
                                    <?php
                                    echo "<table class='table table-condensed  no-padding'>
                                    	<thead>
                                    		<tr>
                                                <th><input type='checkbox' id='' class='flat-red select_all_skyhub_plps' /></th>
                                    			<th>ShippindId</th>
                                    			<th>Expira</th>
                                    			<th>Impresso</th>
                                    			<th>Tipo</th>
                                    		</tr>
                                    	</thead>
                                    	<tbody>";
                                    foreach($listplps as  $key => $row){
                                        
//                                         pre($row);
                                        
                                        $print = $row->printed ? "Reimprimir" : "Gerar Etiqueta" ;
                                        $link = "<a href='".HOME_URI."/Modules/Skyhub/Shipments/PlpView/{$row->id}' target='_blank' >{$print}</a>";
                                        echo "<tr class='warning' >
                                                <th><input type='checkbox' plp_id='{$row->id}' class='flat-red select_one_skyhub_plp' /></th>
                                                <td>{$row->id}</td>
                                                <td>{$row->expiration_date}</td>
                                                <td>{$link}</td>
                                                <td>{$row->type}</td>
                                            </tr>";
                                        
                                        echo "<tr><td  colspan='5'>
                                    		<table  class='table table-condensed  no-padding'>";
                                        foreach($row->orders as $keyOrder => $order){
                                            // 		    pre($rowItem);die;
                                            echo "<tr bgcolor='#fff'>
                                    					<td>PedidoId: {$order->code}</td>
                                                        <td>Cliente: {$order->customer}</td>
                                                        <td>Valor: {$order->value}</td>
                                                        <td></td>
                                    				</tr>";
                                            
                                        }
                                        echo " 	</table>
                                                </td>
                                    		</tr>";
                                    
                                    }
                                    echo "</tbody></table>";
                                    ?>
                    			</div>
                    		</div>
                    	</div><!-- /.box-body -->
                        <div class="overlay skyhub-plp" style='display:none;'>
                    		<i class="fa fa-refresh fa-spin"></i>
                		</div>
              		</div><!-- /.box -->
                </div><!-- /.tab-pane -->
                
                <div class="tab-pane <?php echo $tabs['request-collect']; ?>" id="tab_3">
                	<div class="box box-solid">
                        <div class="box-header ">
                        	<div id='message-collect'></div>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class='row'>
                                <div class="col-sm-3">
                        			<div class="form-group">
                            			<select id='select_action_skyhub_shipment_collect' class='form-control input-sm'>
                            				<option value='select' >Ações</option>
                            				<option value='confirm_skyhub_collect' >Solicitar Coleta</option>
                        				
                        				</select>
                        			</div>
                				</div>
            				</div>
            				<div class='row'>
                                <div class="col-sm-12">
                
                                <?php
                                if(!empty($listOrdersReadyToCollect)){
                                    echo "<table class='table table-condensed  no-padding'>
                                    	<thead>
                                    		<tr>
                                                <th><input type='checkbox' id='' class='flat-red select_all_skyhub_shipment_collect' /></th>
                                    			<th>ShippindId</th>
                                    			<th>Cliente</th>
                                    			<th>Valor</th>
                                    		</tr>
                                    	</thead>
                                    	<tbody>";
                                    foreach($listOrdersReadyToCollect as  $j => $ordeReady){
                                        echo "<tr class='warning' >
                                                <td><input type='checkbox' order_code='{$ordeReady->code}' class='flat-red select_one_skyhub_shipment_collect' /></td>
                                    			<td>{$ordeReady->code}</td>
                                                <td>{$ordeReady->customer}</td>
                                                <td>{$ordeReady->value}</td>
                                            </tr>";
                                        
                                    }
                                    echo "</tbody></table>";
                                }
                                 ?>
                                 </div>
                    		</div>
                    	</div><!-- /.box-body -->
                        <div class="overlay skyhub-plp" style='display:none;'>
                    		<i class="fa fa-refresh fa-spin"></i>
                		</div>
              		</div><!-- /.box -->
                </div><!-- /.tab-pane -->
                
                
                
                
     		</div><!-- /.tab-content -->
    	</div><!-- nav-tabs-custom -->
	</div><!-- /.col -->
</div> <!-- /.row -->

<!-- END CUSTOM TABS -->


