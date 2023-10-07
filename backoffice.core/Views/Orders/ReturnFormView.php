<div class="modal fade order_returns_modal" id='order_returns_modal' role='dialog'>

	<div class="modal-dialog">
	
    	<div class="modal-content">
    	
		    <div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		    	<h4 class="modal-title">Troca ou Devolução do Pedido <span id='return_pedido_id'></span></h4>
		    </div>
		    
	    	<div class="modal-body">
			    <div class='created_information'></div>
			    <div class='message-returns' id='message-returns'></div>
			    <div class='row'>
			    	
			      	<input type="hidden" name="OrderId" id='return_OrderId' disabled class="form-control input-sm"  value="">
			      	
			        <input type="hidden" name="customer_id" id='return_customer_id' disabled class="form-control input-sm"  value="">
			        
					<div class="col-sm-3">
						<div id="type-form-group" class="form-group">
							<label for="type_return">Tipo:</label>
							<select id="type_return" name="type_return" class="form-control input-sm">
								<option value='' >Selecione</option>
								<option value='exchange'>Troca</option>
								<option value='return'>Devolução</option>
								<option value='refused'>Recusado</option>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div id="status-form-group" class="form-group">
							<label for="status">Status:</label>
							<select id="status" name="status" class="form-control input-sm">
								<option value='' >Selecione</option>
								<option value='new'>Novo</option>
								<option value='returning'>Retornando</option>
								<option value='waiting'>Aguardando</option>
								<option value='received'>Recebido</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div id="reason-form-group" class="form-group">
							<label id='reason-select'>Motivo:</label> 
							<select id="reason" name="reason" class="form-control input-sm reason-select">
								<option value='' >Selecione</option>
								<option value='Recebeu Produto Danificado'>Recebeu Produto Danificado</option>
								<option value='Aparência Diferente do Publicado'>Aparência Diferente do Publicado</option>
								<option value='Recebeu Produto Errado'>Recebeu Produto Errado</option>
								<option value='Apresentou Defeito Durante Uso'>Apresentou Defeito Durante Uso</option>
								<option value='Arrependido'>Arrependimento</option>
								<option value='Insucesso na Entrega'>Insucesso na Entrega</option>
								<option value='other_reasons'>Outras razões</option>
							</select>
							<input type="text" name="reason" id="reason"  class="form-control input-sm reason-input"  value="">
						</div> 
					</div>
					<div class="col-sm-12 items">
						<div class='message-returns-products'></div>
						<div class='table-return-items'></div>
					</div>
					
				</div>
				
				<div class='row'>
				<div class="col-sm-7">
						<div class='box-body no-padding scroll300 '>
							<div class="form-group">
								<label for='timeline'>Timeline de Ocorrências</label>
					   			<ul class='timeline occurrence_history'></ul>
					   		</div>
				   		</div>
				    </div>
					<div class="col-sm-5">
					<?php if(isset($returnsModel->store_id)){ ?>
<!-- 						<div id="check-in-form-group" class="form-group"> -->
<!-- 							<label for="status">Status:</label> -->
<!-- 							<select id="check_in" name="check_in" class="form-control input-sm"> -->
<!-- 								<option value='' >Selecione</option> -->
<!-- 								<option value='stock_available'>Voltar no estoque para venda</option> -->
<!-- 								<option value='stock_damaged'>Enviar para estoque de defeito</option> -->
<!-- 							</select> -->
<!-- 						</div> -->
						<?php }?>
						
						
						<div id="reason-form-group" class="form-group">
							<label>Anotações e Divergências:</label> 
							<textarea type="text" name="information" id="return_information"  rows="5" cols="12" class="form-control input-sm return_information"></textarea>
						</div>
						<div class="form-group">
							<label>Rastreamento Envio:</label> 
							<input type="text" name="shipping_id" id='return_shipping_id' readonly class="form-control input-sm"  value="">
						</div>
						<div class="form-group">
							<label>Logística Reversa:</label> 
							<input type="text" name="reverse_code" id='reverse_code' class="form-control  input-sm"  value="">
						</div>
					</div>
					
				</div>
				
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default  btn-sm pull-left close-modal" id='close-modal' data-dismiss="modal">Fechar</button>
		        	<button type="button" class="btn btn-primary btn-sm" id='register_order_return'>Registrar</button>
		      	</div>
		      	
			</div><!-- /.modal-content -->
			
		</div><!-- /.modal-dialog -->
		
	</div><!-- /.modal -->
	
</div>