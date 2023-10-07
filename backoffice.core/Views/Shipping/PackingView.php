<?php if ( ! defined('ABSPATH')) exit;?>
<style type="text/css" media="print">

.added{
    background-color: #00a65a; color: #b9b9b9;
}
.waiting-pickup{
    background-color: #ececec; color: #b9b9b9;
}
@page{
    size: 3in 4in;
    margin: 0;
    max-height: 13cm;
}

@media print {
  .noPrint{
    display:none;
  }
   .page { 
     margin: 0; 
     border: initial; 
     border-radius: initial; 
     width: initial; 
     min-height: initial; 
     box-shadow: initial; 
     background: initial; 
   } 
}
td {
    padding:1px !important;
}
.table-danfe-etiqueta{
    font-size: 12px;
}

 html, body {
        height: 93%;   
    }
</style>
<div class='row noPrint'>
	<div class="message noPrint">
		<div class="message noPrint"><?php if(!empty( $pickingModel->form_msg)){ echo  $pickingModel->form_msg;}?></div>
	</div>
</div>
<div class='row'>
	<div class="col-md-5 noPrint">
		<div class="box box-solid noPrint">
        	<div class="box-header with-border noPrint">
          		<h3 class="box-title noPrint">Separação</h3>
           		<div class="box-tools pull-right noPrint">
          			<a href="/Shipping/Packing/id/<?php echo $pickingModel->id; ?>/" class='btn btn-primary btn-xs noPrint' id='limpar' ><b>F5</b> - Limpar</a>
          		</div>
        	</div>
        	
        	<div class="box-body no-padding noPrint">
          		<ul class="nav nav-pills nav-stacked noPrint">
          			<li class="active noPrint">
						<input type="hidden" name="picking_id" class="form-control input-sm noPrint" id='picking_id' value='<?php echo $pickingModel->id; ?>' disabled/>
                    	<a href="#"><i class="fa fa-cube noPrint"></i>
                    		<label for='code'>PedidoId</label>
                            <input type="text" class="form-control code-pedido-id-in noPrint" id='code-pedido-id' autofocus="autofocus"  value='' />
        				</a>
        			</li>
					<li class="noPrint">
						<input type="hidden" name="picking_id" class="form-control input-sm noPrint" id='picking_id' value='<?php echo $pickingModel->id; ?>' disabled/>
                    	<a href="#"><i class="fa fa-barcode noPrint"></i> 
                    		<label for='code'>Produto</label>
                            <input type="text" class="form-control code-product-package-in noPrint" id='code' autofocus="autofocus"  value='' />
        				</a>
        			</li>
          		</ul>
			</div>
			<div class="overlay noPrint" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
    	</div>
        <div class="box box-solid noPrint" id='box-package' >
			<div class="box-header with-border noPrint">
                <h3 class="box-title"><i class='fa fa-dropbox'></i> Pacote</h3>
                <div class="box-tools pull-right" >
          			<b>#<span id='PedidoId'></span></b>
          		</div>
            </div>
			<div class="box-body table-responsive no-padding noPrint" >
            	<table class="table noPrint">
            		<thead>
                		<th style='text-align:center;'><i class='fa fa-camera'></i></th>
                		<th>Sku</th>
                		<th>Produto</th>
                		<th style='text-align:center;'>Total</th>
                		<th style='text-align:center;'>Qtd</th>
            		</thead>
            		<tbody class="package noPrint" id='package'></tbody>
            	</table>
            </div>
            <div class="overlay noPrint" style='display:none;'>
        		<i class="fa fa-refresh fa-spin"></i>
    		</div>
		</div>
    </div>
	<div class="col-md-7">
   		<div class="box box-solid">
        	<div class="box-header with-border noPrint">
          		<h3 class="box-title noPrint">Documento Fiscal</h3>
           		<div class="box-tools pull-right noPrint">
          			<button class='btn btn-primary btn-xs' id='btn-print-document'><b>F6</b> - <i class='fa fa-print'></i></button>
          		</div>
        	</div>
            <div class="box-body no-padding">
    	          <div class="box-body page order-info" id='order-info'></div>
            </div>
        	<div class="overlay noPrint" style='display:none;'>
    			<i class="fa fa-refresh fa-spin"></i>
			</div>
    	</div>
	</div>
</div>






<div class='row noPrint'>
<div class="col-md-12 table-responsive noPrint">
 <div class="box box-solid noPrint">
        <div class="box-heade noPrintr">
          <h3 class="box-title noPrint">Produtos da Separação</h3>
        </div>
        <div class="box-body noPrint">
		<table class='table table-condensed noPrint'>
    		<tr class='table-hendling-in noPrint'>
    			<th>Sku/EAN</th>
    			<th>Título/Marca</th>
    			<th>Cor/Variação/Ref.</th>
    			<th>Peso/Medidas</th>
    			<th>QTD</th>
    			
    		</tr>
    		<?php 
    		$count = 0;
    		foreach($listPickingProductOrders as $k => $pickingProduct){
    		    
    		     $count += $pickingProduct['quantity'];
        			    
        		    echo "<tr id='{$pickingProduct['id']}' class='{$pickingProduct['id']} noPrint' bgcolor='#f4f4f4'>
                            <td>{$pickingProduct['sku']}<br>{$pickingProduct['ean']}</td>
                            <td>{$pickingProduct['information']['title']}<br>{$pickingProduct['information']['brand']}</td>
                            <td>{$pickingProduct['information']['color']}/{$pickingProduct['information']['variation']}<br>{$pickingProduct['information']['reference']}</td>
                            <td>Peso: {$pickingProduct['information']['weight']}<br>Medidas: {$pickingProduct['information']['height']} x {$pickingProduct['information']['width']} x {$pickingProduct['information']['length']}</td>
                            <td id='qty-{$pickingProduct['id']}' valign='middle'><strong>{$pickingProduct['quantity']}</strong></td>
                    </tr>";
        		    echo "<tr><td></td><td colspan='4'><table class='table {$pickingProduct['id']} noPrint' >";
        		    
        		    foreach($pickingProduct['orders'] as $i => $order){
        		        
    		          echo "<tr>
                        <td></td>
        		        <td>{$order['PedidoId']}</td>
                        <td>{$order['short_description']}</td>
                        <td>{$order['marketplace']}</td>
                        <td>{$order['quantity']}</td>
                        <td>{$order['status']}</td>
                        </tr>";
        		        
        		        
        		    }
        		    echo "</table></td></tr>";
        		    
    			}
             ?>
		</table>
		</div>
	</div>
</div>
</div>

