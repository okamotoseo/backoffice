<div class="modal fade" id='order_occurrence_modal' role='dialog'>
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    	<h4 class="modal-title">Ocorrências pedido <input type="text" name="PedidoId" id='occurrence_pedido_id' readonly class="form-control input-sm"  value=""></h4>
    </div>
    <div class="modal-body">
    <div id='message'></div>
    <div class='row'>
       	<div class="col-md-12">
    	    <ul class='timeline occurrence_history'></ul>
        </div>
    </div>
    <div class='row'>
    	<input type="hidden" name="OrderId" id='occurrence_OrderId' disabled class="form-control input-sm"  value="">
    	<input type="hidden" name="customer_id" id='occurence_customer_id' disabled class="form-control input-sm"  value="">
		<div class="col-sm-12">
			<div class="form-group">
				<label>Ocorrência:</label> 
				<textarea type="text" name="occurrence" id="occurrence"  rows="5" cols="12" class="form-control input-sm occurrence"  value=""></textarea>
			</div>
		</div>
	</div>	
    <div class="modal-footer">
    	<button type="button" class="btn btn-default  btn-sm pull-left" id='close-modal' data-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary btn-sm" id='register_order_occurrence'>Registrar</button>
	</div>
    </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div>