<?php if ( ! defined('ABSPATH')) exit;?>


<div class='row'>
<div class="col-md-6">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'><?php echo $this->title; ?></h3>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<div class="col-md-12">
    				<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
    			        <thead>
    				        <tr>
        				        <th>Data</th>
        				        <th>Tipo</th>
    				            <th>Key</th>
    				            <th>Id</th>
    				            <th>Secção</th>
    				            <th>Informação</th>
    				        </tr>
    			        </thead>
    		 		<tbody>
    	             <?php foreach ($logList as $fetch): ?>
    	             <tr>
        	             <td> <?php echo $fetch['created'] ?> </td>
        	             <td> <?php echo $fetch['type'] ?> </td>
    	                 <td> <?php echo $fetch['key'] ?> </td>
    	                 <td> <?php echo $fetch['id'] ?> </td>
    	                 <td> <?php echo $fetch['section'] ?> </td>
    	                 <td> <?php echo $fetch['information'] ?> </td>
    	             </tr>
    	             <?php endforeach;?>
    		 		</tbody>
    				</table>
    				<?php pagination($totalReg, $mlLogModel->pagina_atual, HOME_URI."/Modules/Mercadolivre/Setup/Log");?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Tarefas</h3>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
			<div class="box-body">
                  <table class="table table-bordered">
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Tarefa</th>
                      <th>Parâmentro</th>
                      <th style="width: 40px">Ação</th>
                    </tr>
                    <tr>
                      <td>1.</td>
                      <td>Importar Pedidos Mercadolivre</td>
                      <td><input type='text' id='import_order' param='order_id' class='form-control input-sm' value=''></td>
                      <td  width='90px' text-align='center'>
                      	<a  id='import_order' ws='Orders' class="btn ml-task" target='_blank'><i class='glyphicon glyphicon-export'></i></a>
                      	<img class='ajaxload-import_order' src='<?php echo HOME_URI."/Views/_uploads/images/facebook-ajax-loader.gif"; ?>' style='display:none;'>  
                      </td>
                    </tr>
                    <tr>
                      <td>2.</td>
                      <td>Enviar Mensagens Pós Venda</td>
                      <td><input type='text' id='send_sale_messages' param='order_id' class='form-control input-sm' value=''></td>
                      <td width='90px' text-align='center'>
                      	<a  id='send_sale_messages' ws='Orders' class="btn ml-task" target='_blank'><i class='glyphicon glyphicon-export'></i></a>
                      	<img class='ajaxload-send_sale_messages' src='<?php echo HOME_URI."/Views/_uploads/images/facebook-ajax-loader.gif"; ?>' style='display:none;'> 
                      </td>
                    </tr>
                  </table>
                </div><!-- /.box-body -->
			<?php // echo HOME_URI."/Modules/Mercadolivre/Webservice/Messages.php?store_id={$this->userdata['store_id']}&action=send_sale_messages"; ?>
			<?php // echo HOME_URI."/Modules/Mercadolivre/Webservice/Orders.php?store_id={$this->userdata['store_id']}&action=import_order"; ?>
			</div>
		</div>
	</div>
	
	

</div>