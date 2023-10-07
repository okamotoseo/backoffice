$(document).ready(function(){
	
	$('.select_all_skyhub_products').on('ifClicked', function(event){
		checked = $(".select_one_skyhub_products:checked").length;
		if (checked == 0) {
			$('.select_one_skyhub_products').iCheck('check');
		  } else {
			  $('.select_one_skyhub_products').iCheck('uncheck');
		  }
	});	

	$('#select_action_skyhub_products').change(function(){
		var sku = [];
		var parentId = [];
		var productId = [];
		var action = $('#select_action_skyhub_products').val();
		if(action != 'select'){
			$(".skyhub-products").css("display","inline");
			
			$('.select_one_skyhub_products').each(function(){
				if( $(this).is(':checked') ){
					 productId.push($(this).attr('product_id'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				
				case "update_products_skyhub":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, type:"update",  status:"enabled", user:userName};
					break;
				case "disabled_products_skyhub":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, type:"update",  status:"disabled", user:userName};
					break;	
				case "remove_products_skyhub":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"delete_products", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
					break;
			}
			
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					for( var i = 0; i < parts.length; i++){
						switch(action){
							case "remove_products_skyhub": $('#'+parts[i]).remove(); break;
						}
					}
					$(".skyhub-products").css("display","none");		
					
				}
			});
		}
		
	});
	
	$('.select_all_skyhub_shipments').on('ifClicked', function(event){
		checked = $(".select_one_skyhub_shipment:checked").length;
		if (checked == 0) {
			$('.select_one_skyhub_shipment').iCheck('check');
		  } else {
			  $('.select_one_skyhub_shipment').iCheck('uncheck');
		  }
	});	
	$('#select_action_skyhub_shipments').change(function(){
		var orderCodes = [];
		var action = $('#select_action_skyhub_shipments').val();
		if(action != 'select'){
			$(".skyhub-shipments").css("display","inline");
			
			$('.select_one_skyhub_shipment').each(function(){
				if( $(this).is(':checked') ){
					orderCodes.push($(this).attr('order_code'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				case "group_skyhub_plp":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Shipments.php",
					 jsonData = {action:"group_skyhub_plp", store_id:storeId, order_code:orderCodes};
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
					
					$(".skyhub-shipments").css("display","none");		
					
				}
			});
			
		}
	})
	
	
	
	$('.select_all_skyhub_plps').on('ifClicked', function(event){
		checked = $(".select_one_skyhub_plp:checked").length;
		if (checked == 0) {
			$('.select_one_skyhub_plp').iCheck('check');
		  } else {
			  $('.select_one_skyhub_plp').iCheck('uncheck');
		  }
	});	
	$('#select_action_skyhub_plp').change(function(){
		var plpIds = [];
		var action = $('#select_action_skyhub_plp').val();
		if(action != 'select'){
			$(".skyhub-plp").css("display","inline");
			$('.select_one_skyhub_plp').each(function(){
				if( $(this).is(':checked') ){
					plpIds.push($(this).attr('plp_id'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){

				case "ungroup_skyhub_plp":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Shipments.php",
					 jsonData = {action:"ungroup_skyhub_plp", store_id:storeId, plp_id:plpIds};
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
					$(".skyhub-plp").css("display","none");		
					
				}
			});
			
		}
	});
	
	$('.select_all_skyhub_shipment_collect').on('ifClicked', function(event){
		checked = $(".select_one_skyhub_shipment_collect:checked").length;
		if (checked == 0) {
			$('.select_one_skyhub_shipment_collect').iCheck('check');
		  } else {
			  $('.select_one_skyhub_shipment_collect').iCheck('uncheck');
		  }
	});	
	$('#select_action_skyhub_shipment_collect').change(function(){
		var orderCodes = [];
		var action = $('#select_action_skyhub_shipment_collect').val();
		if(action != 'select'){
			$(".skyhub-collect").css("display","inline");
			$('.select_one_skyhub_shipment_collect').each(function(){
				if( $(this).is(':checked') ){
					orderCodes.push($(this).attr('order_code'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){

				case "confirm_skyhub_collect":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Shipments.php",
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
					$(".skyhub-colect").css("display","none");		
				}
			});
			
		}
	});
	
	
	
	$('.action_skyhub_product').each(function(){
		
		$(this).click(function(){
			
			$(".skyhub-products").css("display","inline");
			var product_id = $(this).attr("product_id");
			var sku = $(this).attr("sku");
			var parent_id = $(this).attr("parent_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
					
				case "delete_skyhub_product":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"delete_products", store_id:storeId, sku:sku, product_id:product_id, user:userName};
					break;
				case "update_products_skyhub":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, type:"update", status:"enabled", product_id:product_id, user:userName, type:'update'};
					break;

				case "disable_product_skyhub":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"update_status_disabled_product", store_id:storeId, type:"update", status:"disabled", product_id:product_id, user:userName};
					break;
				case "enable_product_skyhub":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"update_status_enabled_product", store_id:storeId, type:"update", status:"enabled", product_id:product_id, user:userName};
					break;
					
				case "send_products_skyhub":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, sku:sku, parent_id:parent_id, product_id:product_id, user:userName, type:'create'};
					break;
					
				case "remove_products_skyhub":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php",
					 jsonData = {action:"delete_products", store_id:storeId, parent_id:parent_id, product_id:product_id, user:userName};
					break;
					
					
					
			}
			
			$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
				
					parts = data.split("|");
					console.log(parts);
					switch(action){
						case "remove_products_skyhub": 
//							var windowLoca =  window.location.href.replace("#", "");
//							var partsLocation = windowLoca.split('publications');
//							
//							if(partsLocation.length > 1){
//								window.location.href = windowLoca;
//							}else{
//								window.location.href = partsLocation[0] + '/publications';
//							}
							break;
						case "delete_skyhub_product": $('#'+parts[i]).remove(); break;
						case "send_products_skyhub": 
							
							if(parts[0] == 'error'){
								alert(parts[1]);
							}else{
								console.log(parts);
								var windowLoca =  window.location.href.replace("#", "");
								windowLoca = windowLoca.replace("/fotos", "");
								var partsLocation = windowLoca.split('publications');
								
								if(partsLocation.length > 1){
									window.location.href = windowLoca;
								}else{
									window.location.href = partsLocation[0] + '/publications';
								}
							}
							break;
						default:  
							if(parts[0] == 'error'){
								alert(parts[1]);
								console.log(data);
							}else{
								$('.message-skyhub').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>"); 
							}
						
						
						break;
					}
					$(".skyhub-products").css("display","none");
					
				}
			
			});
			$(".skyhub-products").css("display","none");
			
			
		});
		
	});
	
	
	$('#update_stock_price').click(function(){
		
		$(".skyhub-products").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Skyhub/Webservice/Products.php",
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
			
				
				$(".skyhub-products").css("display","none");
			
			}
		
		});
	});
	$('#update_order_queue').click(function(){
		
		$(".skyhub-orders").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Skyhub/Webservice/Orders.php",
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
				
				$(".skyhub-orders").css("display","none");
			
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
				url:home_uri+"/Modules/Skyhub/Webservice/Orders.php",
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
				url:home_uri+"/Modules/Skyhub/Webservice/Orders.php",
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
				url:home_uri+"/Modules/Skyhub/Webservice/Orders.php",
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
	$('.action_skyhub_oders').each(function(){
		
		$(this).click(function(){
			
			$(".skyhub-oders").css("display","inline");
			
			var orderId = $(this).attr("order_id");
			var pedidoId = $(this).attr("pedido_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
					
				case "cancel_skyhub_order":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Orders.php",
					 jsonData = {action:"cancel_order", store_id:storeId, pedido_id:pedidoId, order_id:orderId};
					break;
				case "delivered_skyhub_order":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Orders.php",
					 jsonData = {action:"delivery_order", store_id:storeId, pedido_id:pedidoId, order_id:orderId};
					break;	
				case "invoice_skyhub_order":
					 uri = home_uri+"/Modules/Skyhub/Webservice/Orders.php",
					 jsonData = {action:"invoice", store_id:storeId, pedido_id:pedidoId, order_id:orderId};
					break;	
					
					
					
			}
			
			$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
				
					parts = data.split("|");
					console.log(data);
					
				}
			
			});
			$(".skyhub-orders").css("display","none");
			
			
		});
		
	});
	    
	
})