$(document).ready(function(){
	
	
	$('#btn-export-order').click(function(){
		$(this).attr("disabled", true);
		var orderId = $(this).attr("order_id");
		var storeId =  $(this).attr("store_id");
		var exportTo =  $(this).attr("export_to");
		var dataJson  = '';
		var uri = '';
		
		switch(exportTo){
			case "Sysemp": 
				dataJson = {action:'export_orders', store_id:storeId, order_id:orderId};
				uri = "https://backoffice.sysplace.com.br/Modules/Sysemp/Webservice/Order.php";
				break; 
			case "Magento": 
				dataJson = {action:'export_order', store_id:storeId, order_id:orderId};
				uri = "https://backoffice.sysplace.com.br/Modules/Onbi/Webservice/Checkout.php";
				break; 
		
		}
		
		if(uri != ''){
			$.ajax({
				type: "POST",
				async: "true",
				url:uri,
				data:dataJson,
				success: function(data){
					if(data == 'success'){
						alert('Pedido exportado com sucesso!');
						
					}
					console.log(data);
				}
			});
		}
		
	})
	
	
	
	$('#btn-print-order').click(function(){
		
		var orderId = $(this).attr("order_id");
		var storeId =  $(this).attr("store_id");
		var PedidoId = $(this).attr('pedido_id');
		var picker = $(this).attr('picker');
		var user = $(this).attr("user");
		
		$.ajax({
			type: "POST",
			async: "true",
			url:"https://backoffice.sysplace.com.br/Webservice/AppOrders.php",
			data: {action:'update_order_print', store_id:storeId, order_id:orderId},
			success: function(data){
				parts = data.split("|");
				if(parts[0] == 'success'){
					window.print();
				}else{
					alert("erro ao imprimir pedido");
				}
			}
		});
		
		
		$.ajax({
			type: "POST",
			async: "true",
			url: "https://backoffice.sysplace.com.br/Webservice/AppOrders.php",
			data: {action:"handling-product-packing-in", picker:picker, picking_id:'new', pedido_id:PedidoId, store_id:storeId, user:user},
			success: function(data){
				console.log(data);
				
			}
			
		});
		
	})
	
	$('#shippment_label_pdf').click(function(){
		$('.loading-xml').css("display","inline");
		storeId =  $(this).attr("store_id");
		orderId = $(this).attr('order_id');
		shippingId = $(this).attr('shipping_id');
		dataPedido = $(this).attr('data_pedido');
		path = $(this).attr('path');
		
			$('.loading').css("display","none");
			window.location = "http://backoffice.sysplace.com.br/Modules/Mercadolivre/Labels/store_id_"+storeId+"/"+dataPedido+"/"+shippingId+".pdf";

			

	});
	
});