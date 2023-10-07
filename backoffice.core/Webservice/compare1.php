<?php 
if(isset($orderPayments)){
	foreach ($orderPayments as $keyP => $orderPayment){
		pre($orderPayment);
		$orderPayments = new OrderPaymentsModel($db);
		$orderPayments->store_id = $storeId;
		$orderPayments->order_id = $orderId;
		$orderPayments->PedidoId = $pedidoId;
		$orderPayments->PagamentoId = !empty($orderPayment->sefaz->id_payment) ? $orderPayment->sefaz->id_payment : $keyP + 1 ;
		if($orderPayments->NumeroParcelas > 0 ){
			$orderPayments->NumeroParcelas = $orderPayments->parcels;
			$orderPayments->ValorParcela = $orderPayments->total_ordered / $orderPayments->parcels;
		}

		$orderPayments->ValorTotal = $orderPayment->value;
		$formaPagamento = $orderPayment->sefaz->name_card_issuer != 'null'  ?  $orderPayment->sefaz->name_card_issuer : $orderPayment->sefaz->name_payment ;
		$orderPayments->FormaPagamento =  !empty($formaPagamento) ? $formaPagamento : $orderPayment->method;
		$orderPayments->Metodo = $orderPayment->method;
		$orderPayments->NumeroAutorizacao = !empty($orderPayment->authorization_id) ? $orderPayment->authorization_id : '';
		$orderPayments->DataAutorizacao = date("Y-m-d H:i:s", strtotime($orderPayment->transaction_date));
		$status = !empty($orderPayment->status) ? getSystemDefaultPaymentStatus($orderPayment->status) :  getSystemDefaultPaymentStatus($order->status->type) ;
		$orderPayments->Situacao = $status['code'];
		$orderPayments->MarketplaceTaxa = !empty($ordersModel->MarketplaceTaxa) ? $ordersModel->MarketplaceTaxa : '0.00';
		$orderPayments->Marketplace = $order->channel;

		$orderPaymentsId = $orderPayments->Save();
		echo "-------Payments-----------<br>";
		pre($orderPaymentsId);
		pre($orderPayments);

	}
}