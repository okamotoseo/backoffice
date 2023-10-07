<?php if ( ! defined('ABSPATH')) exit;?>
<div class='row'>
	<div class='col-md-12'>
		<div class="message"><?php if(!empty( $mlMessagesModel->form_msg)){ echo  $mlMessagesModel->form_msg;}?></div>
		<div class='box  box-primary'>
			<div class='box-header'>
	        	<h3 class='box-title'><?php echo $this->title; ?></h3>
	        	     	<div class='box-tools pull-right'>
    	        	<div class="form-group">
    	        		<a href='<?php echo HOME_URI ?>/Modules/Mercadolivre/Messages/SalesMessages' class='btn btn-block btn-default btn-xs'><i class='fa fa-ban'></i> Limpar</a>
    	        	</div>
	        	</div>
			</div><!-- /.box-header -->
			<form method="POST" action="<?php echo HOME_URI ?>/Modules/Mercadolivre/Messages/SalesMessages" name="form-messages">
			<input type="hidden" name="id" value="<?php echo $mlMessagesModel->id; ?>" />
			<div class='box-body'>
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<?php 
							$paid = $delivered = '';
							switch($mlMessagesModel->status){
							    case "paid": $paid = "selected"; break;
							    case "delivered": $delivered = "delivered"; break;
							    default : $all = "selected"; break;
							}
							?>
							<label for="marketplace">Status:</label>
							<select id="status" name="status" class="form-control input-sm">
							<option value='all' <?php echo $all; ?>>Todos</option>
							<option value='paid' <?php echo $paid; ?>>Pedidos Pagos</option>
							<option value='delivered' <?php echo $delivered; ?>>Pedidos Entregues</option>
							</select>
						</div>
					</div>
					<div class="col-md-10">
						<div class="form-group">
							<label>Assunto:</label> 
							<input type="text" name="subject" id="subject" class="form-control"  value="<?php echo $mlMessagesModel->subject; ?>" />
						</div>
					</div>
					

					<div class="col-md-12">
						<div class="form-group">
							<label>Mensagem:</label> 
							<textarea type="text" rows='10' name="message" id="message" class="form-control textarea" placeholder="Place some text here" ><?php echo $mlMessagesModel->message; ?></textarea>
						</div>
					</div>
				</div>
			</div>
			
			<div class="box-footer">	
				<button type="submit" class="btn btn-primary btn-sm pull-right" id="btn" name="save"><i class='fa fa-check'></i> Salvar</button>
			</div>
			</form>
		</div>
	</div>
</div>


<div class='row'>
	<div class="col-md-12">
		<div class='box box-primary'>
			<div class='box-header'>
		       	<h3 class='box-title'>Listagem de mensagens</h3>
			</div><!-- /.box-header -->
			
			<div class="box-body table-responsive">
				<div class="col-md-12">
    				<table id="search-default" class="table table-bordered  table-hover display" cellspacing="0" width="100%" role="grid" aria-describedby="example_info" style="width: 100%;">
    			        <thead>
    				        <tr>
    				            <th>Status</th>
    				            <th>Assunto</th>
    				            <th>Mensagem</th>
    				            <th>Ação</th>
    				        </tr>
    			        </thead>
    		 		<tbody>
    		 
    	             <?php foreach ($listMessages as $fetch): ?>
    	             
    	             <tr>
    	                 <td> <?php echo $fetch['status'] ?> </td>
    	                 <td> <?php echo $fetch['subject'] ?> </td>
    	                 <td> <?php echo substr($fetch['message'], 0, 90); ?> </td>
    	                 <td align='right'> 
    	                     <a href="<?php echo HOME_URI ?>/Modules/Mercadolivre/Messages/SalesMessages/edit/<?php echo $fetch['id'] ?>" class='fa fa-pencil-square-o' />&nbsp;&nbsp;
    	                     <a href="<?php echo HOME_URI ?>/Modules/Mercadolivre/Messages/SalesMessages/del/<?php echo $fetch['id'] ?>" class='fa fa-trash delete' />
    	                 </td>
    	             </tr>
    	             
    	             <?php endforeach;?>
    		 
    		 		</tbody>
    				</table>
				</div>
			</div>
		</div>
	</div>

</div>