<!-- Main content -->
<section class="invoice" >
  <!-- title row -->
  <div class="row">
    <div class="col-xs-12">
      <h2 class="page-header"><i class="fa fa-truck"></i> Remessa de pacotes
        <small class="pull-right">Data: <?php echo dateBr($shippingSendModel->sent, "/"); ?></small>
      </h2>
    </div><!-- /.col -->
  </div>
  
  <div class="row no-print">
    <div class="col-xs-12">
      <a href="#" onclick="window.print();" class="btn btn-default pull-right"><i class="fa fa-print"></i> Print</a>
    </div>
  </div>
  
  <!-- info row -->
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      Enviador
      <address>
        <strong><?php echo $storeModel->store; ?></strong><br>
        <?php echo $storeModel->address."".$storeModel->number; ?><br>
        <?php echo $storeModel->city." - ".$storeModel->state; ?><br>
        <?php echo "Telefone: ".$storeModel->phone; ?><br>
        <?php echo "Email: ".$storeModel->email_sac; ?>
      </address>
    </div><!-- /.col -->
    <div class="col-sm-4 invoice-col">
      Transportador
      <address>
        <strong><?php echo $shippingSendModel->company; ?></strong><br>
        
      </address>
    </div><!-- /.col -->
    <div class="col-sm-4 invoice-col">
      <b>Remessa #<?php echo $shippingSendModel->id; ?></b><br>
    </div><!-- /.col --> 
  </div><!-- /.row -->

  <!-- Table row -->
  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Qty</th>
            <th>PedidoId</th>
            <th>Código</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            		$count = 0;
            		foreach($listShippingSendCode as $k => $sendCode){
        			    
        			    $removeBtn = "onclick=\"javascript:removeShippingCode('".HOME_URI."',{$sendCode['shipping_send_id']}, {$sendCode['id']}, {$sendCode['code']} );\"";
        			    $count++;
        			    echo "<tr id='{$sendCode['id']}' >
                                <td>1</td>
                                <td>{$sendCode['PedidoId']}</td>
                                <td>{$sendCode['code']}</td>
                                </td>
                        </tr>";
        			}
        			?>
        </tbody>
      </table>
    </div><!-- /.col -->
  </div><!-- /.row -->

  <div class="row">
    <!-- accepted payments column -->
    <div class="col-xs-6">
      <p class="lead">Método de Envio:</p>
      <p><strong><?php echo $shippingSendModel->company; ?></strong></p>
<!--               <img src="../../dist/img/credit/visa.png" alt="Visa"> -->
<!--               <img src="../../dist/img/credit/mastercard.png" alt="Mastercard"> -->
<!--               <img src="../../dist/img/credit/american-express.png" alt="American Express"> -->
<!--               <img src="../../dist/img/credit/paypal2.png" alt="Paypal"> -->
      <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
       Antes de receber os pacotes, verifique se não estão violados ou danificados, em caso de perda ou avaria a responsabilidade será do transpotador.
      </p>
    </div><!-- /.col -->
    <div class="col-xs-6">
      <p class="lead">Protocolo de envio</p>
      <div class="table-responsive">
        <table class="table">
          <tr>
            <th style="width:50%">Numero de Pacotes:</th>
            <td><?php echo $count; ?></td>
          </tr>
          <tr>
            <th>Transportadora</th>
            <td><?php echo $shippingSendModel->company; ?></td>
          </tr>
          <tr>
            <th>Nome do coletador:</th>
            <td></td>
          </tr>
          <tr>
            <th>Documento coletador:</th>
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