<!-- Main content -->
<section class="invoice" >
  <!-- title row -->
  <div class="row">
    <div class="col-sm-12">
      <h2 class="page-header"><i class="fa fa-cubes"></i> Coleta de produtos
        <small class="pull-right">Data: <?php  echo dateTimeBr($pickingModel->closed, "/"); ?></small>
      </h2>
    </div><!-- /.col -->
  </div>
  
  <div class="row no-print">
    <div class="col-sm-12">
      <a href="#" onclick="window.print();" class="btn btn-default pull-right"><i class="fa fa-print"></i> Print</a>
    </div>
  </div>
  
  <!-- info row -->
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      Coletor
      <address>
        <strong><?php echo $pickingModel->picker; ?></strong><br>
        
      </address>
    </div><!-- /.col -->
    <div class="col-sm-4 invoice-col">
      <b>Coleta #<?php echo $pickingModel->id; ?></b><br>
    </div><!-- /.col --> 
  </div><!-- /.row -->


  
  <div class='row'>
	<div class="col-sm-12 table-responsive">
		<table class='table table-condensed'>
		<tr class='table-hendling-in'>
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
    			    
    		    echo "<tr id='{$pickingProduct['id']}' class='{$pickingProduct['id']}' bgcolor='#f4f4f4'>
                        <td>{$pickingProduct['sku']}<br>{$pickingProduct['ean']}</td>
                        <td>{$pickingProduct['information']['title']}<br>{$pickingProduct['information']['brand']}</td>
                        <td>{$pickingProduct['information']['color']}/{$pickingProduct['information']['variation']}<br>{$pickingProduct['information']['reference']}</td>
                        <td>Peso: {$pickingProduct['information']['weight']}<br>Medidas: {$pickingProduct['information']['height']} x {$pickingProduct['information']['width']} x {$pickingProduct['information']['length']}</td>
                        <td id='qty-{$pickingProduct['id']}' valign='middle'><strong>{$pickingProduct['quantity']}</strong></td>
                </tr>";
    		    echo "<tr><td></td><td colspan='4'><table class='table {$pickingProduct['id']}' >";
    		    
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

  <div class="row">
    <!-- accepted payments column -->
    <div class="col-sm-6">
        <?php 
        $info = false;
        if($info){
        ?>
      <p class="lead">Informações para coleta:</p>
      <p><strong><?php echo $pickingModel->picker; ?></strong></p>
      <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
       Antes de receber os pacotes, verifique se não estão violados ou danificados, em caso de perda ou avaria a responsabilidade será do transpotador.
      </p>
      <?php } ?>
    </div><!-- /.col -->
    <div class="col-sm-6">
      <p class="lead">Protocolo da coleta</p>
      <div class="table-responsive">
        <table class="table">
          <tr>
            <th style="width:50%">Numero de Produtos:</th>
            <td><?php echo $count; ?></td>
          </tr>
          <tr>
            <th>Coleta</th>
            <td><?php echo $pickingModel->picker; ?></td>
          </tr>
          <tr>
            <th>Nome do coletador:</th>
            <td></td>
          </tr>
          <tr>
            <th>Produtos coletados:</th>
            <td></td>
          </tr>
          <tr>
            <th>Data da coleta:</th>
            <td></td>
          </tr>
        </table>
      </div>
    </div><!-- /.col -->
  </div><!-- /.row -->

</section><!-- /.content -->