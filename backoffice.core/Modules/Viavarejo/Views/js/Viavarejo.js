$(document).ready(function(){
	
	$('.select_all_viavarejo_products').on('ifClicked', function(event){
		checked = $(".select_one_viavarejo_products:checked").length;
		if (checked == 0) {
			$('.select_one_viavarejo_products').iCheck('check');
		  } else {
			  $('.select_one_viavarejo_products').iCheck('uncheck');
		  }
	});	

	$('#select_action_viavarejo_products').change(function(){
		var sku = [];
		var parentId = [];
		var productId = [];
		var action = $('#select_action_viavarejo_products').val();
		if(action != 'select'){
			$(".viavarejo-products").css("display","inline");
			
			$('.select_one_viavarejo_products').each(function(){
				if( $(this).is(':checked') ){
					 productId.push($(this).attr('id'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				
				case "update_products_viavarejo":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, type:"update",  status:"enabled"};
					break;
				case "disabled_products_viavarejo":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, type:"update",  status:"disabled"};
					break;	
				case "remove_products_viavarejo":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Products.php",
					 jsonData = {action:"delete_products", store_id:storeId, parent_id:parentId, product_id:productId};
					break;
			}
			
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					
					if(parts[0] == 'success'){
						$('#message-orders-to-group').html("<div class='callout callout-success'><h4>Codigo registrado com sucesso!</h4></div>");
					}
					if(parts[0] == 'error'){
						$('#message-orders-to-group').html("<div class='callout callout-danger'><h4>Erro ao agrupar pedidos!</h4><p>"+parts[1]+"</p></div>");
					}
					$(".viavarejo-products").css("display","none");		
					
				}
			});
		}
		
	});
	
	$('.select_all_viavarejo_shipments').on('ifClicked', function(event){
		checked = $(".select_one_viavarejo_shipment:checked").length;
		if (checked == 0) {
			$('.select_one_viavarejo_shipment').iCheck('check');
		  } else {
			  $('.select_one_viavarejo_shipment').iCheck('uncheck');
		  }
	});	
	$('#select_action_viavarejo_shipments').change(function(){
		var orderCodes = [];
		var action = $('#select_action_viavarejo_shipments').val();
		if(action != 'select'){
			$(".viavarejo-shipments").css("display","inline");
			
			$('.select_one_viavarejo_shipment').each(function(){
				if( $(this).is(':checked') ){
					orderCodes.push($(this).attr('order_code'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				case "group_viavarejo_plp":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Shipments.php",
					 jsonData = {action:"group_viavarejo_plp", store_id:storeId, order_code:orderCodes};
					break;
			}
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					if(parts[0] == 'success'){
						$('#message-orders-to-group').html("<div class='callout callout-success'><h4>Codigo registrado com sucesso!</h4></div>");
					}
					if(parts[0] == 'error'){
						$('#message-orders-to-group').html("<div class='callout callout-danger'><h4>Erro ao agrupar pedidos!</h4><p>"+parts[1]+"</p></div>");
					}
					
					$(".viavarejo-shipments").css("display","none");		
					
				}
			});
			
		}
	})
	
	
	
	$('.select_all_viavarejo_plps').on('ifClicked', function(event){
		checked = $(".select_one_viavarejo_plp:checked").length;
		if (checked == 0) {
			$('.select_one_viavarejo_plp').iCheck('check');
		  } else {
			  $('.select_one_viavarejo_plp').iCheck('uncheck');
		  }
	});	
	$('#select_action_viavarejo_plp').change(function(){
		var plpIds = [];
		var action = $('#select_action_viavarejo_plp').val();
		if(action != 'select'){
			$(".viavarejo-plp").css("display","inline");
			$('.select_one_viavarejo_plp').each(function(){
				if( $(this).is(':checked') ){
					plpIds.push($(this).attr('plp_id'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){

				case "ungroup_viavarejo_plp":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Shipments.php",
					 jsonData = {action:"ungroup_viavarejo_plp", store_id:storeId, plp_id:plpIds};
					break;
					
			}
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					if(parts[0] == 'success'){
						location.reload();
					}

					if(parts[0] == 'error'){
						$('#message-plp').html("<div class='callout callout-danger'><h4>Erro ao desagrupar PLP!</h4><p>"+parts[1]+"</p></div>");
					}
					$(".viavarejo-plp").css("display","none");		
					
				}
			});
			
		}
	});
	
	$('.select_all_viavarejo_shipment_collect').on('ifClicked', function(event){
		checked = $(".select_one_viavarejo_shipment_collect:checked").length;
		if (checked == 0) {
			$('.select_one_viavarejo_shipment_collect').iCheck('check');
		  } else {
			  $('.select_one_viavarejo_shipment_collect').iCheck('uncheck');
		  }
	});	
	$('#select_action_viavarejo_shipment_collect').change(function(){
		var orderCodes = [];
		var action = $('#select_action_viavarejo_shipment_collect').val();
		if(action != 'select'){
			$(".viavarejo-collect").css("display","inline");
			$('.select_one_viavarejo_shipment_collect').each(function(){
				if( $(this).is(':checked') ){
					orderCodes.push($(this).attr('order_code'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){

				case "confirm_viavarejo_collect":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Shipments.php",
					 jsonData = {action:"confirm_collect", store_id:storeId, order_code:orderCodes};
					break;
					
			}
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					if(parts[0] == 'success'){
						location.reload();
					}
					if(parts[0] == 'error'){
						$('#message-collect').html("<div class='callout callout-danger'><h4>Erro ao solicitar coleta!</h4><p>"+parts[1]+"</p></div>");
					}
					$(".viavarejo-colect").css("display","none");		
				}
			});
			
		}
	});
	
	
	
	$('.action_viavarejo_product').each(function(){
		
		$(this).click(function(){
			
			$(".viavarejo-products").css("display","inline");
			var id = $(this).attr("id");
			var product_id = $(this).attr("product_id");
			var sku = $(this).attr("sku");
			var parent_id = $(this).attr("parent_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
					
				case "delete_viavarejo_product":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Products.php",
					 jsonData = {action:"delete_products", store_id:storeId, sku:sku, product_id:id};
					break;
				case "update_products_viavarejo":
					 uri = home_uri+"/Modules/viavarejo/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, type:"update", status:"enabled", product_id:product_id};
					break;

				case "disable_product_viavarejo":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Products.php",
					 jsonData = {action:"update_status_disabled_product", store_id:storeId, type:"update", status:"disabled", product_id:product_id};
					break;
				case "enable_product_viavarejo":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Products.php",
					 jsonData = {action:"update_status_enabled_product", store_id:storeId, type:"update", status:"enabled", product_id:product_id};
					break;
					
				case "send_products_viavarejo":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, sku:sku, parent_id:parent_id, product_id:product_id};
					break;
					
				case "get_seller_items_by_sku":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Products.php",
					 jsonData = {action:"get_seller_items_by_sku", store_id:storeId, sku:sku, parent_id:parent_id, product_id:product_id};
					break;
					
					
			}
			
			$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
				console.log(data);
					parts = data.split("|");
					if(parts[0] == 'success'){
						$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					}
					
					if(parts[0] == 'error'){
						$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
					}
					$(".viavarejo-products").css("display","none");
					
				}
			
			});
			$(".viavarejo-products").css("display","none");
			
			
		});
		
	});
	$('#get_loads_products').click(function(){
		
		$(".viavarejo-products").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Viavarejo/Webservice/Products.php",
			data: {action:"get_load_products", store_id:storeId},
			success: function(data){
				parts = data.split("|");
				if(parts[0] == 'success'){
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
				}
				
				if(parts[0] == 'error'){
					$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
				}
				$(".viavarejo-products").css("display","none");
			
			}
		
		});
	});
	$('#update_seller_items').click(function(){
		
		$(".viavarejo-products").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Viavarejo/Webservice/Products.php",
			data: {action:"get_seller_items", store_id:storeId},
			success: function(data){
				
				parts = data.split("|");
				
				if(parts[0] == 'success'){
					
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					
				}
				
				if(parts[0] == 'error'){
					
					$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
					
					
				}
			
				
				$(".viavarejo-products").css("display","none");
			
			}
		
		});
	});
	$('#update_stock_price').click(function(){
		
		$(".viavarejo-products").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Viavarejo/Webservice/Products.php",
			data: {action:"update_stock_price", store_id:storeId},
			success: function(data){
				
				parts = data.split("|");
				
				if(parts[0] == 'success'){
					
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					
					location.reload();
				}
				
				if(parts[0] == 'error'){
					
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					
					
				}
			
				
				$(".viavarejo-products").css("display","none");
			
			}
		
		});
	});
	$('#update_order_queue').click(function(){
		
		$(".viavarejo-orders").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Viavarejo/Webservice/Orders.php",
			data: {action:"get_orders_queue", store_id:storeId},
			success: function(data){
				
				parts = data.split("|");
				
				if(parts[0] == 'success'){
					
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					
					location.reload();
				}
				
				if(parts[0] == 'error'){
					
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					
					
				}
				location.reload();
				
				$(".viavarejo-orders").css("display","none");
			
			}
		
		});
	});
	$('.shipping_modal').each(function(){
		$(this).click(function(){
			var PedidoId = $(this).attr('pedido_id');
			var OrderId = $(this).attr('order_id');
			$('#ShippingPedidoId').val(PedidoId);
			$('#OrderId').val(OrderId);
		})
	})
	$('.shipping_exception_modal').each(function(){
		$(this).click(function(){
			var PedidoId = $(this).attr('pedido_id');
			var OrderId = $(this).attr('order_id');
			$('#ShippingExceptionPedidoId').val(PedidoId);
			$('#OrderIdShippingException').val(OrderId);
		})
	})
	$('#add_shipping_code').click(function(){
		var orderId = $('#OrderId').val();
		var pedidoId = $('#ShippingPedidoId').val();
		var shippingCode = $('#shipping_code').val();
		var shippingType = $('#shipping_type').val();
		var shippingMethod = $('#shipping_method').val();
		if(shippingCode){
			$.ajax({
				type:"POST",
				async:"false",
				url:home_uri+"/Modules/Viavarejo/Webservice/Orders.php",
				data: {action:"add_shipping", store_id:storeId, shipping_code:shippingCode, shipping_type:shippingType, shipping_method:shippingMethod, pedido_id:pedidoId, order_id:orderId },
				success: function(data){
					
					parts = data.split("|");
					if(parts[0] == 'success'){
						
						$('#message').html("<div class='callout callout-success'><h4>Codigo registrado com sucesso!</h4></div>");
						
//						$('#close-modal').click();
					}
					if(parts[0] == 'error'){
						
						$('#message').html("<div class='callout callout-danger'><h4>Erro ao registrar codigo!</h4><p>"+parts[1]+"</p></div>");
					}
				
				}
			
			});
		}	
	});
	
	
	$('#add_shipping_code_delivered').click(function(){
		var orderId = $('#OrderId').val();
		var pedidoId = $('#ShippingPedidoId').val();
		var shippingCode = $('#shipping_code').val();
		var shippingType = $('#shipping_type').val();
		var shippingMethod = $('#shipping_method').val();
		if(shippingCode){
			$.ajax({
				type:"POST",
				async:"false",
				url:home_uri+"/Modules/Viavarejo/Webservice/Orders.php",
				data: {action:"add_shipping", store_id:storeId, shipping_code:shippingCode, shipping_type:shippingType, shipping_method:shippingMethod, pedido_id:pedidoId, order_id:orderId, shipping_delivered:'shipping_delivered' },
				success: function(data){
					
					parts = data.split("|");
					if(parts[0] == 'success'){
						
						$('#message').html("<div class='callout callout-success'><h4>Codigo registrado com sucesso!</h4></div>");
						
//						$('#close-modal').click();
					}
					if(parts[0] == 'error'){
						
						$('#message').html("<div class='callout callout-danger'><h4>Erro ao registrar codigo!</h4><p>"+parts[1]+"</p></div>");
					}
				
				}
			
			});
		}	
	});
	
	
	$('#add_shipping_exception').click(function(){
		var orderId = $('#OrderIdShippingException').val();
		var pedidoId = $('#ShippingExceptionPedidoId').val();
		var shipping_exception = $('#shipping_exception').val();
		if(shipping_exception){
			$.ajax({
				type:"POST",
				async:"false",
				url:home_uri+"/Modules/Viavarejo/Webservice/Orders.php",
				data: {action:"add_shipping_exception", store_id:storeId, shipping_exception:shipping_exception, pedido_id:pedidoId, order_id:orderId },
				success: function(data){
					
					parts = data.split("|");
					if(parts[0] == 'success'){
						
						$('#message').html("<div class='callout callout-success'><h4>Codigo registrado com sucesso!</h4></div>");
						
						$('#close-modal').click();
					}
					if(parts[0] == 'error'){
						
						$('#message').html("<div class='callout callout-danger'><h4>Erro ao registrar codigo!</h4><p>"+parts[1]+"</p></div>");
					}
				
				}
			
			});
		}	
	});
	$('.action_viavarejo_oders').each(function(){
		
		$(this).click(function(){
			
			$(".viavarejo-oders").css("display","inline");
			
			var orderId = $(this).attr("order_id");
			var pedidoId = $(this).attr("pedido_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
					
				case "cancel_viavarejo_order":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Orders.php",
					 jsonData = {action:"cancel_order", store_id:storeId, pedido_id:pedidoId, order_id:orderId};
					break;
				case "delivered_viavarejo_order":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Orders.php",
					 jsonData = {action:"delivery_order", store_id:storeId, pedido_id:pedidoId, order_id:orderId};
					break;	
				case "invoice_viavarejo_order":
					 uri = home_uri+"/Modules/Viavarejo/Webservice/Orders.php",
					 jsonData = {action:"invoice", store_id:storeId, pedido_id:pedidoId, order_id:orderId};
					break;	
					
					
					
			}
			
			$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
				
					parts = data.split("|");
					console.log(data);
					
				}
			
			});
			$(".viavarejo-orders").css("display","none");
			
			
		});
		
	});
	    
	
})