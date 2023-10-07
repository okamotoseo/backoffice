var storeId = $('#store_id').val();
var accountId = $('#account_id').val();
var home_uri = $('#home_uri').val();
var userName = $('#user_name').val();
var userId = $('#user_id').val();

$(document.body).on("keydown", "#title", function () {
    $(".caracteres_count").text(parseInt($(this).val().length));
});

$(document.body).on("keydown", "#char_count", function () {
    $(".caracteres").text(parseInt($(this).val().length));
});

$(document.body).ready(function(){

	$('.update_questions').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "true",
			url: home_uri+"/Modules/Mercadolivre/Webservice/Messages.php",
			data: {action:"import_questions", store_id:storeId, user_id:userId},
			success: function(data){
				$(".overlay").css("display","none");
				record = data.split("|");
				if(record[0] == "success"){
					window.location = home_uri+"/Sac/Questions/";
				}
			}
		});
	})
	
	
	$('#generate_ean').click(function(){
		var id = $(this).attr("product_id");
		
		$.ajax({
			type: "POST",
			async: "true",
			url: home_uri+"/Webservice/AppProducts.php",
			data: {action:"generate_ean", store_id:storeId, product_id:id, user_id:userId},
			success: function(data){
				parts = data.split("|");
				if(parts[0] == "success"){
					$('#ean').val(parts[1]);
					
				}
			}
			
		});
		
		
	})
	
	
	
	
	$( ".send-answer" ).each(function() {
		$(this).click(function(){
			var id = $(this).attr("id");
			var marketplace = $(this).attr("marketplace");
			var answer = $.trim($('#answer-'+id).val());
			if(answer != ''){
				switch(marketplace){
					case "Mercadolivre":
						 uri = home_uri+"/Modules/Mercadolivre/Webservice/Messages.php";
						 jsonData = {action:"send_answer", id:id, answer,answer, store_id:storeId, user:userName};
						break;
					case "B2W":
						 uri = home_uri+"/Modules/Skyhub/Webservice/Sac.php";
						 jsonData = {action:"send_answer", id:id, answer,answer, store_id:storeId, user:userName};
						break;
						
				}
				if( uri != ''){
					$.ajax({type:"POST",async:"true", url:uri,data: jsonData,success: function(data){
						console.log(data);
						parts = data.split("|");
						if(parts[0] == "success"){
							$('#body-'+id).append("<div class='direct-chat-msg right'>"+
		                  	"<div class='direct-chat-msg'><div class='direct-chat-info clearfix'>"+
		                    "<span class='direct-chat-name pull-right'>"+userName+"</span>"+
		                    "<span class='direct-chat-timestamp pull-left'>"+parts[1]+"</span></div>"+
		                    "<img class='direct-chat-img' src='/Views/_uploads/images/store/160x160/"+storeId+".png' alt='message user image'>"+
		                    "<div class='direct-chat-text'>"+answer+"</div></div>");
							$('#input-group-'+id).remove();
							TrHighline("tr-"+id);
							}
						}
						
					});
				}
			}
		
		})
		
	});
	
	
	$('#logout_user').click(function(){
		
		$.ajax({
			type: "POST",
			async: "true",
			url: home_uri+"/Webservice/App.php",
			data: {action:"logout", store_id:storeId, user_id:userId},
			success: function(data){
				
				console.log(data);
				record = data.split("|");
				if(record[0] == "success"){
					
					window.location = home_uri;
					
				}
			}
			
		});
		
		
		
	})
	
	
	 //SLIMSCROLL FOR CHAT WIDGET
	$('.scroll800').slimScroll({
		height: '800px'
	});
	$('.scroll').slimScroll({
		height: '500px'
	});
	$('.scroll300').slimScroll({
		height: '300px'
	});
	$('.scroll250').slimScroll({
		height: '250px'
	});
	$('.scroll100').slimScroll({
		height: '150px'
	});
	$('.scroll150').slimScroll({
		height: '150px'
	});
	
	$('.pickin-product-in').focus();
	$(".date-mask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
	
	setTimeout(function(){
		$(".alert").css("display","none");
	}, 15000);
	
	setTimeout(function(){
		$(".alert").css("display","none");
	}, 15000);
	
	$('.submit-load').click(function(){
		$(".overlay").css("display","inline");
	});
	
	$('.link-load').click(function(){
		$(".overlay").css("display","inline");
	})
	
	$(".textarea").wysihtml5();
		
	$('#export_csv').click(function(){
		
		$(".overlay").css("display","inline");
		
		if(confirm('Deseja realmente exportar o relatório ?')){
			var reportType = $(this).attr('report_type');
			
			if(reportType != ''){
				var uri = '';
				var jsonData = '';
				switch(reportType){
					case "customers": 
						 uri = home_uri+"/Webservice/ws_report.php",
						 jsonData = { action:reportType, store_id:storeId, id:$('#id').val(), Codigo:$('#codigo').val(), Marketplace:$('#Marketplace option:selected').val(),
								 Nome:$('#nome').val(), CPFCNPJ:$('#cpfcnpj').val(), Genero:$('#Genero option:selected').val()
								 };
						break;
					case "available_products":
						 uri = home_uri+"/Webservice/ws_report.php",
						 jsonData = { action:"export_available_products",  store_id:storeId, id:$('#id').val(),
								sku:$('#sku').val(), parent_id:$('#parent_id').val(), ean:$('#ean').val(),
								title:$('#title').val(), reference:$('#reference').val(), category:$('#category').val(),
								brand:$('#brand').val(), marketplace:$('#marketplace').val(), stock:$('#stock').val(),
								blocked:$('#blocked').val(), group_by:$('#group_by').val(),
							};
						break;
						
				}
				
				if( uri != ''){
					
					$.ajax({type:"POST",async:"false", url:uri, data:jsonData, success: function(data){
						
							console.log(data);
							parts = data.split("|");
							
							if(parts[0] == 'success'){
								window.location = home_uri+"/Views/_uploads/store_id_"+storeId+"/csv/"+parts[1];
							}
							
							if(parts[0] == 'error'){
								$('.message').html("<div class='alert alert-success'><h4>"+parts[1]+"</h4></div>");
							}
							
							$(".overlay").css("display","none");
						}
					
					});
						
				}else{
					$(".overlay").css("display","none");
				}
			}
		}else{
			$(".overlay").css("display","none");
		}
	});
/*	
	$( "#variation_type" ).change(function() {
	
		var type = $(this).val();
		
		switch(type){
			case 'voltagem': 
				$("#variation").remove();
		 		$("#variation_input").append('<select id="variation" type="text" value="1" class="form-control variation"></select>');
		 		$("#variation").append('<option value="110V">110V</option><option value="220V">220V</option><option value="110V/220V">110V/220V</option><option value="Bivolt">Bivolt</option>');
			break;
			default: 
				$("#variation").remove();
		 		$("#variation_input").append("<input type='text' name='variation' id='variation' class='form-control variation' placeholder='110V' value=''  />");
			
			break;
		}
	});*/
	
	$( ".barcode-pack-in" ).change(function() {
	
		var user = $(this).attr('user');
		var barcode = $(this).val();
		var company = $(this).attr('company');
		var shippingSendId = $(this).attr("shipping_send_id");
		var status = $(this).attr("status");
		
		$.ajax({
			type: "POST",
			async: "true",
			url: home_uri+"/Webservice/App.php",
			data: {action:"handling-pack-in", company:company, shippind_send_id:shippingSendId, barcode:barcode, store_id:storeId, user:user},
			success: function(data){
				console.log(data);
				record = data.split("|");
				
				if(record[0] == "success"){
					$('.table-hendling-in').after("<tr id='"+record[1]+"'><td>"+record[2]+"</td><td>"+record[4]+"</td><td><a class='fa fa-remove pull-right' onclick=\"javascript:removeShippingCode('"+home_uri+"',"+record[3]+", "+record[1]+", '"+record[2]+"' );\" ></a></td></tr>");
				}
				if(record[0] == "error"){
					alert(record[1]);
				}
			}
			
		});
		
		$(this).val("");
		
	});
	
	$( ".btn-remove-shipping-send" ).each(function() {
		
		$(this).click(function(){
			
			var shippingSendId = $(this).attr("shipping_send_id");
			
			if(confirm('Tem certeza que deseja remover a remessa de pacotes?')){
				
				$.ajax({
					type: "POST",
					async: "true",
					url: home_uri+"/Webservice/App.php",
					data: {action:"remove-shipping-send", shippind_send_id:shippingSendId, store_id:storeId},
					success: function(data){
						record = data.split("|");
						
						if(record[0] == "success"){
							location.href = home_uri+"/Shipping/Send";
						}
						if(record[0] == "error"){
							alert(record[1]);
						}
					}
					
				});
			
			}
		
		})
		
	});
	
	$( ".btn-remove-picking" ).each(function() {
	
		$(this).click(function(){
			
			var pickingId = $(this).attr("picking_id");
			
			if(confirm('Tem certeza que deseja remover a lista de separação e todos produtos e pedidos associados a ela?')){
				
				$.ajax({
					type: "POST",
					async: "true",
					url: home_uri+"/Webservice/AppOrders.php",
					data: {action:"remove-picking", picking_id:pickingId, store_id:storeId},
					success: function(data){
						record = data.split("|");
						console.log(record);
						if(record[0] == "success"){
							location.href = home_uri+"/Shipping/Picking";
						}
						if(record[0] == "error"){
							alert(record[1]);
						}
					}
					
				});
			
			}
		
		})
		
	});
	
	$( ".pickin-product-in" ).change(function() {
		
		$(".overlay").css("display","inline");
		
		var user = $(this).attr('user');
		var PedidoId = $(this).val();
		var picker = $(this).attr('picker');
		var pickingId = $(this).attr("picking_id");
		var user = $(this).attr("user");
		var obj = $(this);
		
		$.ajax({
			type: "POST",
			async: "true",
			url: home_uri+"/Webservice/AppOrders.php",
			data: {action:"handling-product-packing-in", picker:picker, picking_id:pickingId, pedido_id:PedidoId, store_id:storeId, user:user},
			success: function(data){
				
				obj.val("");
				obj.focus();
				
				record = data.split("|");
				
				if(record[0] == "success"){
					$('.table-hendling-in').after(record[1]);
					
				}
				if(record[0] == "reload"){
					window.location.href = home_uri+"/Shipping/picking/id/"+pickingId;
				}
				if(record[0] == "error"){
					alert(record[1]);
				}
				
				$(".overlay").css("display","none");
				
				
			}
			
		});
		
		obj.val("");
		obj.focus();
		
	});

	/***********************************************************************************************************/
	/************************************** Orders ********************************************************/
	/*********************************************************************************************************/
	
	$('#select_action_orders').change(function(){
		
		var orderId = [];
		
		var action = $('#select_action_orders').val();
		
		if(action != 'select'){
			$(".overlay").css("display","inline");
			
			$('.select_one_order').each(function(){
				if( $(this).is(':checked') ){
					 orderId.push($(this).attr('id'));
				}
				
			});
			
			var uri = '';
			
			var jsonData = '';
			
			switch(action){
				case "import_sysemp_orders_document":
					uri = home_uri+"/Modules/Sysemp/Webservice/OrdersDocument.php";
					jsonData = {action:"import_order_document", store_id:storeId};
					break;
				case "import_mercadolivre_orders": 
					uri = home_uri+"/Modules/Mercadolivre/Webservice/Orders.php";
					jsonData = {action:"list_orders", store_id:storeId};
					break;
					
				case "send_mercadolivre_sysemp_orders_document": 
					uri = home_uri+"/Modules/Mercadolivre/Webservice/OrdersDocument.php";
					jsonData = {action:"Sysemp", store_id:storeId, order_id:orderId};
					break;
					
				case "send_tray_orders_document": 
					uri = home_uri+"/Modules/Tray/Webservice/OrdersDocument.php";
					jsonData = {action:"export_document", store_id:storeId, order_id:orderId};
					break;
			}
			
			if( uri != ''){
				
				$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
					setTimeout(function(){
						$(".overlay").css("display","none");
						/*location.reload();*/
					}, 1000);
				
				} });
				
			}else{
				
				$(".overlay").css("display","none");
				
			}
		}
		
	});
	

	$('#list_mercadolivre_orders').click(function(){
		
		$(".overlay").css("display","inline");
		$.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Mercadolivre/Webservice/Orders.php",
			data: {action:"list_orders", store_id:storeId},
			success: function(data){
				setTimeout(function(){
					location.reload();
					$(".overlay").css("display","inline");
				}, 5000);
				
				
				console.log(data);
				
			}
		});
	})
	
	
	
	$('.fiscal_key_modal').each(function(){
		$(this).click(function(){
			
			var PedidoId = $(this).attr('pedido_id');
			var OrderId = $(this).attr('order_id');
			var ShippingId = $(this).attr('shipping_id');
			var IdNotaSaida = $(this).attr('id_nota_saida');
			var FiscalKey = $(this).attr('fiscal_key');
			var FreteCusto = $(this).attr('shipping_cost');
			$('#pedido_id').val(PedidoId);
			$('#shipping_id').val(ShippingId);
			$('#shipping_cost').val(FreteCusto);
			$('#OrderId').val(OrderId);
			$('#id_nota_saida').val(IdNotaSaida);
			$('#fiscal_key').val(FiscalKey);
		})
	});
	
	$('.link_payment_modal').each(function(){
		$(this).click(function(){
			
			var PedidoId = $(this).attr('pedido_id');
			var OrderId = $(this).attr('order_id');
//			$('#pedido_id').val(PedidoId);
//			$('#OrderId').val(OrderId);
			
			var link = "https://fanlux.appay.me/pagar/"+OrderId;
			$('#link_payment').val(link);
			$('#send-payment').attr('href', link);
			
			
		})
	});
	
	
	$( '#btnCopyToClipboard' ).click( function(){
	     var clipboardText = "";
	     clipboardText = $( '#txtKeyw' ).val(); 
	     copyToClipboard( clipboardText );
	     alert( "Copied to Clipboard" );
	 });
	
	$('.edit_order_modal').each(function(){
		$(this).click(function(){
			var PedidoId = $(this).attr('pedido_id');
			var OrderId = $(this).attr('order_id');
			var CustomerId = $(this).attr('customer_id');
			$('#OrderId_address').val(OrderId);
			if(OrderId){
				$.ajax({
					type:"POST",
					async:"true",
					url:home_uri+"/Webservice/AppOrders.php",
					data: {action:"get_order_detail", store_id:storeId, pedido_id:PedidoId, order_id:OrderId, customer_id:CustomerId, user:userName},
					success: function(data){
						var order = JSON.parse(data);
						populate($('#address'), order);
					}
				});
			}
		})
	});
	
	$('#update_address_order').click(function(){
		
		var OrderId = $('#OrderId_address').val();
		item = {}; 
		item['store_id'] = storeId;
		item['action'] = 'update_address_order';
	    $(".modal_address").each(function() {
	        var prop = $(this).attr("name");
	        item [prop] = $(this).val();
	    });
	    if(OrderId){
			$.ajax({
				type:"POST",
				async:"false",
				url:home_uri+"/Webservice/AppOrders.php",
				data: item,
				success: function(data){
					console.log(data);
					parts = data.split("|");
					if(parts[0] == 'success'){
						 $('#message_update_address_order').html("<div class='alert alert-success'><h4>"+parts[1]+"</h4></div>");
					}
					if(parts[0] == 'error'){
						$('#message_update_address_order').html("<div class='alert alert-danger'><h4>"+parts[1]+"</h4></div>");
					}
					
				}
			});
	    }
	});
	
	$('.occurrence_modal').each(function(){
		$(this).click(function(){
			var PedidoId = $(this).attr('pedido_id');
			var OrderId = $(this).attr('order_id');
			var CustomerId = $(this).attr('customer_id');
			$('#occurrence_pedido_id').val(PedidoId);
			$('#occurrence_customer_id').val(CustomerId);
			$('#occurrence_OrderId').val(OrderId);
			if(OrderId){
				$.ajax({
					type:"POST",
					async:"true",
					url:home_uri+"/Webservice/AppOrders.php",
					data: {action:"get_order_occurrences", store_id:storeId, pedido_id:PedidoId, order_id:OrderId, customer_id:CustomerId, user:userName},
					success: function(data){
						parts = data.split("|");
						if(parts[0] == 'success'){
							$('.occurrence_history').html(parts[1]);
						}
						if(parts[0] == 'error'){
							
							$('#message').html("<div class='alert alert-danger'><h4>Erro ao recuperar dados !</h4><p>"+parts[1]+"</p></div>");
						}
					
					}
				
				});
			}
			
		})
	});
	
	$('#register_order_occurrence').click(function(){
		var orderId = $('#occurrence_OrderId').val();
		var pedidoId = $('#occurrence_pedido_id').val();
		var customerId = $('#occurrence_customer_id').val();
		var occurrence = $('#occurrence').val();
		
		if(orderId){
			$.ajax({
				type:"POST",
				async:"false",
				url:home_uri+"/Webservice/AppOrders.php",
				data: {action:"register_order_occurrence", store_id:storeId, customer_id:customerId, pedido_id:pedidoId, 
					order_id:orderId, occurrences:occurrence, user:userName},
				success: function(data){
					
					parts = data.split("|");
					if(parts[0] == 'success'){
						$('.occurrence_history').append(parts[1]);
						$('.occurrence').val('');
//						$('#close-modal').click();
						
						
					}
					if(parts[0] == 'error'){
						
						$('#message').html("<div class='alert alert-danger'><h4>Erro ao registrar dados !</h4><p>"+parts[1]+"</p></div>");
					}
				
				}
			
			});
		}	
	});

	$('.returns_modal').each(function(){
		$(this).click(function(){
			
			var PedidoId = $(this).attr('pedido_id');
			var OrderId = $(this).attr('order_id');
			var ShippingId = $(this).attr('shipping_id');
			var IdNotaSaida = $(this).attr('id_nota_saida');
			var FiscalKey = $(this).attr('fiscal_key');
			var CustomerId = $(this).attr('customer_id');
			$('#return_pedido_id').html(PedidoId);
			$('#return_shipping_id').val(ShippingId);
			$('#return_OrderId').val(OrderId);
			$('#return_id_nota_saida').val(IdNotaSaida);
			$('#return_fiscal_key').val(FiscalKey);
			$('#return_customer_id').val(CustomerId);
			if(OrderId){
				$.ajax({
					type:"POST",
					async:"true",
					url:home_uri+"/Webservice/AppOrders.php",
					data: {action:"get_order_items", store_id:storeId, pedido_id:PedidoId, order_id:OrderId},
					success: function(data){
						console.log(data);
						parts = data.split("|");
						if(parts[0] == 'success'){
							
							$('.table-return-items').html(parts[1]);
							
							if(parts[2] != ''){
								$('#type-form-group > select > option').removeAttr("selected");
								$('#type-form-group > select > option[value="'+parts[2]+'"]').attr("selected", "selected");
							}
							if(parts[3] != ''){
								$('#reason-form-group > select > option').removeAttr("selected");
								if($('#reason-form-group > select > option[value="'+parts[3]+'"]').length > 0){
									$('#reason-form-group > select > option[value="'+parts[3]+'"]').attr("selected", "selected");
								}
								else{
									$('.reason-select').remove();
									$('.reason-input').show();
//									$('#reason').val(parts[3]);
									$('.reason-input').val(parts[3]);
								}
							}
							if(parts[4] != ''){
								$('#reverse_code').val(parts[3]);
							}
							if(parts[6] != ''){
								
								$('.created_information').html("<div class='callout callout-warning'><h4>Solicitação registrada por "+parts[6]+" em "+parts[5]+"</h4></div>");
							}else{
								$('.created_information').html("");
							}
							if(parts[7] != ''){
								$('#check-in-form-group > select > option').removeAttr("selected");
								$('#check-in-form-group > select > option[value="'+parts[7]+'"]').attr("selected", "selected");
							}
							if(parts[8] != ''){
								$('#status-form-group > select > option').removeAttr("selected");
								$('#status-form-group > select > option[value="'+parts[8]+'"]').attr("selected", "selected");
							}
							$.ajax({
								type:"POST",
								async:"true",
								url:home_uri+"/Webservice/AppOrders.php",
								data: {action:"get_order_occurrences", store_id:storeId, pedido_id:PedidoId, order_id:OrderId, customer_id:CustomerId, user:userName},
								success: function(data){
									parts = data.split("|");
									if(parts[0] == 'success'){
										$('.occurrence_history').html(parts[1]);
									}
								}
							});
						}
					}
				});
				
			}
		})
	});
	
	 $('.reason-input').hide();
	 $('#reason-select').html('Motivo:');

	  $('.reason-select').change(function() {
	    if ($('.reason-select').val() === "other_reasons") {
	    	$('.reason-select').remove();
	      $('.reason-input').show();
	      $('#reason-select').append(' Escreva o motivo');
	    }
	  })
	
	$('#register_order_return').click(function(){
		
		var valid = true;
		var items = [];
		var orderId = $('#return_OrderId').val();
		var pedidoId = $('#return_pedido_id').html();
		var shippingId = $('#return_shipping_id').val();
		var idNotaSaida = $('#return_id_nota_saida').val();
		var information = $.trim($('#return_information').val());
		var checkIn = $('#check_in').val();
		var typeReturn = $.trim($('#type_return').val());
		var validate = $('#validate').val();
		var reason = $.trim($('#reason').val());
		var reverseCode = $('#reverse_code').val();
		var fiscalKey = $('#return_fiscal_key').val();
		var customerId = $('#return_customer_id').val();
		var valid = true;
		var element;
		
		if (typeReturn  === '') {
			$("#type-form-group").addClass('has-error');
			element = document.getElementById("type-form-group");
			valid = false;
	    }else{
	    	$("#type-form-group").removeClass('has-error');
	    	
	    }
		
		
		if (reason  === '') {
			$("#reason-form-group").addClass('has-error');
			element = document.getElementById("reason-form-group");
			valid = false;
	    }else{
	    	$("#reason-form-group").removeClass('has-error');
	    }
		
		if(!valid){
			element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
			return false;
		}
		
		var sumQty = 0;
		
		$('.return-items').each(function(){
			
			var qty_ordered = parseInt($(this).attr('qty_ordered'), 10);
			var qty = parseInt($(this).val(), 10);
			sumQty = qty +sumQty ;
			if(qty > qty_ordered){
				$('.message-returns-products').html("<div class='alert alert-danger'><h4>Quantidade devolvida maior que quantidade comprada</h4></div>");
				
				valid  = false;
			}
			
			items.push({ relational:$(this).attr('relational'), product_id:$(this).attr('product_id'), item_id:$(this).attr('item_id'), qty:qty, qty_ordered:qty_ordered });
			
		});
		
		if(sumQty == 0){
			alert('Informe a quantidade devolvida de cada produto ou utilize zero para produtos não devolvidos ou remover produto da devolução.');
			valid = false;
			return false;
		}
		
		if (information  === '') {
			alert('Descreva mais informações sobre a devolução ou troca');
			valid = false;
	    }
		
		if(orderId && valid){
			$('.message-returns-products').html("");
			$('.message-returns').html("");
			
			$.ajax({
				type:"POST",
				async:"false",
				url:home_uri+"/Webservice/AppOrders.php",
				data: {action:"register_order_return", store_id:storeId, shipping_id:shippingId, pedido_id:pedidoId, 
					order_id:orderId, id_nota_saida:idNotaSaida, fiscal_key:fiscalKey, informations:information, customer_id:customerId,
					check_in:checkIn, type_return:typeReturn, validates:validate, reasons:reason, user:userName, item:items},
				success: function(data){
					console.log(data);
					parts = data.split("|");
					if(parts[0] == 'success'){
						$('.message').html("<div class='alert alert-success'><h4>Salvo!</h4></div>");
						$('#return_information').val('').empty();
						$('.close-modal').click();
//						$(".message")[0].scrollIntoView();
						
					}
					if(parts[0] == 'error'){
						
						$('.message-returns').html("<div class='alert alert-danger'><h4>Erro ao registrar dados !</h4><p>"+parts[1]+"</p></div>");
						element = document.getElementById(".message-returns");
						element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
					}
					
				}
			
			});
			
		}
		
	});
	
	$('.order_action').each(function(){
		
		$(this).click(function(){
			
			$(".overlay").css("display","inline");
			
			var orderId = $(this).attr("order_id");
			var pedidoId = $(this).attr("pedido_id");
			var marketplace = $(this).attr("marketplace");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
				case "delete_order":
					
					if(confirm('Essa ação é irreversível. Deseja realmente excluir o pedido')){
						uri = home_uri+"/Webservice/AppOrders.php",
						jsonData = {action:"delete_order", store_id:storeId, order_id:orderId, user:userName, user_id:userId, marketplace:marketplace};
					}
					break;	
				case "approved_order":
					
					if(confirm('Deseja realmente aprovar o pagamento do pedido')){
						uri = home_uri+"/Webservice/AppOrders.php",
						jsonData = {action:"approve_order", store_id:storeId, order_id:orderId, user:userName, user_id:userId, marketplace:marketplace};
					}
					break;		
					
				case "cancel_order":
					if(confirm('Deseja cancelar o pedido')){
						
						switch(marketplace){
						case 'televendas':
								uri = home_uri+"/Webservice/AppOrders.php",
								jsonData = {action:"cancel_order", store_id:storeId, order_id:orderId, user:userName, user_id:userId, marketplace:marketplace};
							break;
							default:
								uri = home_uri+"/Webservice/AppOrders.php",
								jsonData = {action:"cancel_order", store_id:storeId, order_id:orderId, user:userName, user_id:userId, marketplace:marketplace};
								break;
						
						}
						
					}
					break;
					
			}
			if(uri != ''){
				
				$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
					
						parts = data.split("|");
						
						if(parts[0] == 'success'){
							
							if(action == 'delete_order' ){
								$(".tr-order-"+parts[1]).remove();
							}else{
								TrHighline("#tr-order-"+parts[1]);
							}
							
							
						}else{
							
							defaultMessage(parts);
							
							console.log(data);						
						}
					
					}
			
				});
			}
			$(".overlay").css("display","none");
			
		});
		
	});

	
	$('#register_fiscal_data').click(function(){
		
		var orderId = $('#OrderId').val();
		var pedidoId = $('#pedido_id').val();
		var shippingId = $('#shipping_id').val();
		var shippingCost = $('#shipping_cost').val();
		var idNotaSaida = $('#id_nota_saida').val();
		var fiscalKey = $('#fiscal_key').val();
		
		if(orderId){
			$.ajax({
				type:"POST",
				async:"false",
				url:home_uri+"/Webservice/AppOrders.php",
				data: {action:"register_fiscal_data", store_id:storeId, shipping_id:shippingId, shipping_cost:shippingCost, pedido_id:pedidoId, order_id:orderId, id_nota_saida:idNotaSaida, fiscal_key:fiscalKey },
				success: function(data){
					
					parts = data.split("|");
					if(parts[0] == 'success'){
						
						$('#message').html("<div class='alert alert-success'><h4>Dados registrado com sucesso!</h4></div>");
						
						$('#close-modal').click();
						
						location.reload();
					}
					if(parts[0] == 'error'){
						
						$('#message').html("<div class='alert alert-danger'><h4>Erro ao registrar dados fiscais!</h4><p>"+parts[1]+"</p></div>");
					}
				
				}
			
			});
		}	
	});
	
	$('.send_fiscal_key').each(function(){
		$(this).click(function(){
			
			var marketplace = $(this).attr("marketplace");
			var captureFrom = $(this).attr("captureFrom");
			var orderId = $(this).attr('order_id');
			var pedidoId = $(this).attr('pedido_id');
			var idNotaSaida = $(this).attr('id_nota_saida');
			var fiscalKey = $(this).attr('fiscal_key');
			if(marketplace != ''){
				
				var uri = '';
				var jsonData = '';
				switch(marketplace){
				
					case "Mercadolivre": 
						switch(captureFrom){
							case "Sysemp":
							 	uri = home_uri+ "/Modules/Mercadolivre/Webservice/OrdersDocument.php"; 
							 	jsonData = {action:"Sysemp", store_id:storeId, order_id:orderId, fiscal_key:fiscalKey, id_nota_saida:idNotaSaida, pedido_id:pedidoId};
							 	
							 break;
						}
						
						break;
						
					case "B2W": 
						switch(captureFrom){
							case "Skyhub":
							 	uri = home_uri+ "/Modules/Skyhub/Webservice/Orders.php"; 
							 	jsonData = {action:"invoice", store_id:storeId, order_id:orderId, fiscal_key:fiscalKey, id_nota_saida:idNotaSaida, pedido_id:pedidoId};
							 	
							 break;
						}
						break;
				}
				$.ajax({type:"POST", async:"true", url:uri, data:jsonData, success:function(data){
					
						parts = data.split("|");
						if(parts[0] == 'success'){
							alert(parts[1]);
						}
						if(parts[0] == 'error'){
							alert(parts[1]);
						}
						if(parts[0] == 'reload'){
							location.reload();
							
						}
						
						console.log(data);
					}
				
				});
			
			}
		
		})
	})
	/***********************************************************************************************************/
	/************************************** End Orders ********************************************************/
	/*********************************************************************************************************/
	
	
	
	/***********************************************************************************************************/
	/************************************** Customer **********************************************************/
	/**********************************************************************************************************/
	$('#select_action_customers').change(function(){
		
		var gender = [];
		var customerId = [];
		var action = $('#select_action_customers').val();
		
		$(".attributes-available-products").css("display","none");
		
		if(action != 'select'){
			$(".overlay").css("display","inline");
			
			$('.select_one').each(function(){
				if( $(this).is(':checked') ){
					 customerId.push($(this).attr('id'));
				}
				
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				case "update_gender": 
					$(".attributes-customer").css("display","inline");
//					 uri = home_uri+"/Modules/Webservice/Customers.php";
//					 jsonData = {action:"update_gender", store_id:storeId, gender:gender, customer_id:customerId};
					break;
			}
			if( uri != ''){
				
				$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
					console.log(data);
					$(".overlay").css("display","none");
					}
				});
			}else{
				$(".overlay").css("display","none");
			}
		}
	});
	
	$('#btn_update_customer_gender').click(function(){
	
		
		var gender = $('.select-gender option:selected').val();
		
		if( gender != '' ){
			
			$(".overlay").css("display","inline");
			
			if(confirm('Deseja realmente atualizar o gênero do cliente')){
				
				var customerId = [];
				
				$('.select_one').each(function(){
					if( $(this).is(':checked') ){
						customerId.push($(this).attr('id'));
					}
					
				})
				if(customerId){
					$.ajax({
						type: "POST",
						async: "true",
						url: home_uri+"/Webservice/Customers.php",
						data: {action:'update_gender', store_id:storeId, customer_id:customerId, gender:gender},
						success: function(data){
							console.log(data);
							parts = data.split("|");
							if(parts[0] == 'success'){
								showMessage('success', parts[1]);
								
							}else{
								showMessage('error', parts[1]);
								console.log(data);
							}
							
							$(".overlay").css("display","none");
							
						}
					});
				}
			}
			
		}else{
			$('#gender-required').addClass("has-error");
		}
		
	});
	/***********************************************************************************************************/
	/************************************** Customer **********************************************************/
	/*********************************************************************************************************/
	
	
	/***********************************************************************************************************/
	/************************************** Available Products ************************************************/
	/*********************************************************************************************************/
	
	if(typeof $('#drag-drop-area').html() !== 'undefined'){
		var count = $('.count-media').val();
		
		if(!count){
			return;
		}
		var caption = $(this).attr('fileName');
	    var key = $(this).attr('key'); 
	    var productId = $('#product_id').val();
	    var title = $('#title').val();
		var uppy = Uppy.Core( {
				meta: {
						store_id: storeId,
			            title: title,
			            product_id: productId,
					},
				restrictions: {
			    maxFileSize: 200000000,
			    maxNumberOfFiles: 10,
			    minNumberOfFiles: 1,
			    allowedFileTypes: ['image/*', '.jpg', '.jpeg', '.png', '.gif', 'video/*', '.webm', '.mp4',, '.mp3', , '.wma']
			  }
		})
		.use(Uppy.Dashboard, {
			  id: 'Dashboard',
			  target: '#drag-drop-area',
//			  metaFields: [
//				    { id: 'position', name: 'Posição', placeholder: '0' }
//				  ],
			  trigger: '#drag-drop-area',
			  inline: true,
			  width: 900,
			  height: 350,
			  thumbnailWidth: 280,
			  showLinkToFileUploadResult: false,
			  showProgressDetails: true,
			  hideUploadButton: false,
			  hideRetryButton: false,
			  hidePauseResumeButton: false,
			  hideCancelButton: false,
			  hideProgressAfterFinish: false,
			  note: null,
			  closeModalOnClickOutside: false,
			  closeAfterFinish: false,
			  disableStatusBar: false,
			  disableInformer: false,
			  disableThumbnailGenerator: false,
			  disablePageScrollWhenModalOpen: true,
			  animateOpenClose: true,
			  proudlyDisplayPoweredByUppy: true,
			  onRequestCloseModal: () => this.closeModal(),
			  showSelectedFiles: true,
			  browserBackButtonClose: true,
			  theme: 'light'
			})
			.use(Uppy.Webcam, {
			  id:'Webcam',
			  target: Uppy.Dashboard,
			  onBeforeSnapshot: () => Promise.resolve(),
			  countdown: false,
			  modes: [
			    'video-audio',
			    'video-only',
			    'audio-only',
			    'picture'
			  ],
			  mirror: true,
			  facingMode: 'user',
			  showRecordingLength: false,
			  preferredVideoMimeType: null,
			  preferredImageMimeType: null,
			  locale: {}
			})
			.use(Uppy.XHRUpload, {
			  endpoint: home_uri+"/Views/_uploads/uploadsNew.php",
			  formData: true,
			  fieldName: 'file'
			 
			})
			.on('file-added', (file) => {
			  uppy.setFileMeta(file.id, {
			    position: count++
			  })
			}) 
			// And display uploaded files
			.on('upload-success', (file, response) => {
			  const url = response.body.filepath
			  const fileName = response.body.filename
			  const fileSize = response.body.filesize
			  const item = response.body.item
			  document.querySelector('.uploaded-files-server ul').innerHTML += item;
			  $(".count-media").val(parseInt($(".count-media").val()) + 1);
			});
			
	}
	
	$('.block_product').on('ifClicked', function(event){
		checked = $(".block_product:checked").length;
		console.log(checked);
		if (checked == 0) {
			$('.block_product').val('T');
		  } else {
			  $('.block_product').val('F');
		  }
	});
	
	$('.select_all').on('ifClicked', function(event){
		checked = $(".select_one:checked").length;
		if (checked == 0) {
			$('.select_one').iCheck('check');
		  } else {
			  $('.select_one').iCheck('uncheck');
		  }
	});	
	
	$('#select_action_available_products').change(function(){
		
		var sku = [];
		var parentId = [];
		var productId = [];
		var action = $('#select_action_available_products').val();
		
		$(".categories-available-products").css("display","none");
		
		if(action != 'select'){
			
			$(".overlay").css("display","inline");
			
			$('.select_one').each(function(){
				if( $(this).is(':checked') ){
					 productId.push($(this).attr('id'));
				}
				
			});
			
			var uri = '';
			var jsonData = '';
			
			if(confirm("Confirme a ação "+action)){
				switch(action){
					case "send_products_mercadolivre": 
						 uri = home_uri+"/Modules/Mercadolivre/Webservice/ProductsNew.php";
						 jsonData = {action:"new_ads_product", store_id:storeId, type:'multiple', product_id:productId, user:userName};
						break;
					case "send_products_mg2":
						 uri = home_uri+"/Modules/Magento2/Webservice/AddProducts.php";
						 jsonData = {action:"create_product_magento2", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "send_products_onbi":
						 uri = home_uri+"/Modules/Onbi/Webservice/AddProducts.php";
						 jsonData = {action:"create_product_magento", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "create_product_Onbi":
						 uri = home_uri+"/Modules/Onbi/Webservice/AddProducts.php";
						 jsonData = {action:"create_product_magento", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "update_products_onbi":
						 uri = home_uri+"/Modules/Onbi/Webservice/AddProducts.php";
						 jsonData = {action:"update_product_magento", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "update_product_relational_onbi":
						 uri = home_uri+"/Modules/Onbi/Webservice/AddProducts.php";
						 jsonData = {action:"update_product_relational_magento", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "send_products_tray":
						 uri = home_uri+"/Modules/Tray/Webservice/AddProducts.php";
						 jsonData = {action:"create_product_tray", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "send_products_skyhub":
						 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php";
						 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, user:userName, type:'create'};
						break;
					case "update_products_skyhub":
						 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php";
						 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, user:userName, type:'update'};
						break;
					case "send_products_viavarejo":
						 uri = home_uri+"/Modules/Viavarejo/Webservice/AddProducts.php";
						 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "send_products_amazon":
						 uri = home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
						 jsonData = {action:"add_products_feed", store_id:storeId,  type:"insert", product_id:productId, user:userName};
						break;
					case "block_products":
						 uri = home_uri+"/Webservice/App.php";
						 jsonData = {action:"block_products", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "update_category":
						$(".categories-available-products").css("display","inline");
						break;
					case "update_attribute":
						$(".attributes-available-products").css("display","inline");
						break;
					case "copy_available_products":
						if(confirm("Deseja realmente criar uma cópia do produtos e seus atributos cadastrados? " +
								"ATENÇÃO! -> Essa função copia o produto para criar um outro produto do tipo KIT" +
								"Para isso é necessário criar um novo sku que não estará relacionado com nenhum produto da sua integração," +
								"portanto não será possivel atualizar estoque e preço caso não tenha nenhum produto relacionadona aba KIT")){
							uri =  home_uri+"/Webservice/AppProducts.php";
							jsonData = {action:"copy_available_products", product_id:productId, store_id:storeId, user:userName};
						}
						break;
					case "copy_available_products_all":
						if(confirm("Deseja realmente criar uma cópia do produtos e seus atributos cadastrados? " +
								"ATENÇÃO! -> Essa função copia o produto para criar um outro produto do tipo KIT" +
								"Para isso é necessário criar um novo sku que não estará relacionado com nenhum produto da sua integração," +
								"portanto não será possivel atualizar estoque e preço caso não tenha nenhum produto relacionadona aba KIT")){
							uri =  home_uri+"/Webservice/AppProducts.php";
							jsonData = {action:"copy_available_products_all", product_id:productId, store_id:storeId, user:userName};
						}
						break;
					case "remove_products_skyhub":
						 uri = home_uri+"/Modules/Skyhub/Webservice/Products.php";
						 jsonData = {action:"delete_products", store_id:storeId, parent_id:parentId, product_id:productId, user:userName};
						break;
					case "delete_available_product":
						if(confirm("Deseja realmente excluír os produtos e seus atributos cadastrados?")){
							uri =  home_uri+"/Webservice/AppProducts.php";
							jsonData = {action:"delete_product", store_id:storeId, product_id:productId, user:userName, user_id:userId};
						}
						break;
					case "send_products_marketplace":
						uri = home_uri+"/Modules/Marketplace/Webservice/AddProducts.php",
						jsonData = {action:"add_all_available_products", store_id:storeId, user:userName,  product_id:productId, user:userName};
						break;
						
				}
				if( uri != ''){
					
					$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
						
							parts = data.split("|");
							$(".overlay").css("display","none");
							switch(action){
								case "copy_available_products": 
									if(parts[0] == 'success'){
//										location.reload();
										window.location.href = home_uri+"/Products/Product/"+parts[2];
									}
									break;
								case "copy_available_products_all": 
									if(parts[0] == 'success'){
//										location.reload();
										window.location.href = home_uri+"/Products/Product/"+parts[2];
									}
									break;
							}
							if(parts[0] == 'error'){
								alert(parts[1]);
							}
							defaultMessage(parts);
						}
					});
					
				}else{
					$(".overlay").css("display","none");
				}
			
			}else{
				$(".overlay").css("display","none");
			}
			
		}
		
	});
	

	
	/***********************************************************************************************************/
	/********************************* End  Available Products*************************************************/
	/*********************************************************************************************************/
	
	/***********************************************************************************************************/
	/************************************** Category **********************************************************/
	/*********************************************************************************************************/
	$('.change_products_category_modal').each(function(){
		$(this).click(function(){
			
			var CategoryId = $(this).attr('category_id');
			var ParentId = $(this).attr('parent_id');
			var Hierarchy = $(this).attr('hierarchy');
			var QtyProducts = $(this).attr('qty');
			
			$('#category_id_from').val(CategoryId);
			$('#parent_id_from').val(ParentId);
			$('#category_from').val(Hierarchy);
			$('#qty_products').append(QtyProducts);
		})
	})
	
	$('#change_categories').click(function(){
			$(".overlay").css("display","inline");
			var categoryIdFrom = $('#category_id_from').val();
			var parentIdFrom = $('#parent_id_from').val();
			var categoryFrom = $('#category_from').val();
			var categoryToInfo = $('#category_to').val();
			
			parts = categoryToInfo.split('|');
			
				var parentIdTo = parts[0];
				var categoryIdTo = parts[1];
				var categoryTo = parts[2];
			
			if(parts[0] != 'select'){
				$.ajax({
					type:"POST",
					async:"false",
					url:home_uri+"/Webservice/AppCategories.php",
					data: {action:"change_products_category", store_id:storeId, 
						category_id_from:categoryIdFrom, 
						parent_id_from:parentIdFrom, 
						category_from:categoryFrom, 
						category_id_to:categoryIdTo, 
						parent_id_to:parentIdTo,
						category_to:categoryTo,
						user:userName,
						user_id:userId
						},
					success: function(data){
						console.log(data);
						parts = data.split("|");
						if(parts[0] == 'success'){
							$('#close-modal').click();
							$(".overlay").css("display","inline");
							window.location.href = home_uri+"/Products/Category/";
						}
						if(parts[0] == 'error'){
							defaultMessage(parts)
							$(".overlay").css("display","none");
						}
						
					}
				
				});
			}
		});
	
	$('#category-root').change(function(){
		hierarchy = $(this).val();
		
		$.ajax({
			type: "POST",
			async: "true",
			url: home_uri+"/Webservice/App.php",
			data: {action:'get_category_child', store_id:storeId, root:hierarchy},
			success: function(data){
				
				if(data != ''){
					$('.category_child').html(data);
				}else{
					$('.category_child').html("<option value=''> Categoria</option>");
				}
			}
		});
		
	});
	
	$('#btn_update_attributes').click(function(){
		
		var attributeId = $('.update-attribute-product option:selected').val();
		
		var attribute = $('#attribute-value').val();
		
		if(attribute != '' && attributeId != ''){
			
			$(".available-products").css("display","inline");
			
			if(confirm('Deseja realmente atualizar o attributo')){
				
				var productId = [];
				
				$('.select_one').each(function(){
					if( $(this).is(':checked') ){
						 productId.push($(this).attr('id'));
					}
					
				})
				if(productId){
					$.ajax({
						type: "POST",
						async: "true",
						url: home_uri+"/Webservice/App.php",
						data: {action:'update_attributes', store_id:storeId, attribute_id:attributeId, attribute:attribute, product_id:productId},
						success: function(data){
							console.log(data);
							parts = data.split("|");
							if(parts[0] == 'success'){
								showMessage('success', parts[1]);
								
							}else{
								showMessage('error', parts[1]);
								console.log(data);
							}
							
							$(".available-products").css("display","none");
							
						}
					});
				}
			}
			
		}else{
			$('#category-required').addClass("has-error");
		}
		
	});
	
	$('#btn_update_categories').click(function(){
		
		var category = $('.update-category option:selected').val();
		
		if(category != ''){
			
			$(".available-products").css("display","inline");
			
			if(confirm('Deseja realmente atualizar a categorias')){
				
				var productId = [];
				
				$('.select_one').each(function(){
					if( $(this).is(':checked') ){
						 productId.push($(this).attr('id'));
					}
					
				})
				if(productId){
					$.ajax({
						type: "POST",
						async: "true",
						url: home_uri+"/Webservice/App.php",
						data: {action:'update_categories', store_id:storeId, category:category, product_id:productId},
						success: function(data){
							
							parts = data.split("|");
							if(parts[0] == 'success'){
								showMessage('success', parts[1]);
								
							}else{
								showMessage('error', parts[1]);
								console.log(data);
							}
							
							$(".available-products").css("display","none");
							
						}
					});
					
				}
				
			}
			
		}else{
			$('#category-required').addClass("has-error");
			
		}
		
	});
			
	$('#category').change(function(){
		hierarchy = $(this).val();
		
		$.ajax({
			type: "POST",
			async: "true",
			url: home_uri+"/Webservice/App.php",
			data: {action:'category_attributes', store_id:storeId, category:hierarchy},
			
			success: function(data){
				if(typeof data !== 'undefined'){
					$('#tab_2').html(data);
				}else{
					$('#tab_2').html("<option value=''> Categoria</option>");
				}
			}
		});
		
	});
	
	
	
	/***********************************************************************************************************/
	/************************************** End Categories **********************************************************/
	/*********************************************************************************************************/
	
	$('.product_actions').each(function(){
		$(this).click(function(){
			
			var sku = $(this).attr("sku");
			var parentId = $(this).attr("parent_id");
			var productId = $(this).attr("product_id");
			var action = $(this).attr("action");
			
			if(action != ''){
				
				var uri = '';
				var jsonData = '';
				switch(action){
					case "create_product_sysemp": 
						 uri = home_uri+"/Modules/Sysemp/Webservice/Product.php";
						 jsonData = {action:"export_available_products", store_id:storeId, parent_id:parentId, product_id:productId, sku:sku, update:0};
						break;
					case "import_products_media": 
						 uri = home_uri+"/Modules/Onbi/Webservice/Media.php";
						 jsonData = {action:"import_products_media", store_id:storeId, product_id:productId};
						break;
				}
				
				$.ajax({type:"POST", async:"true", url:uri, data:jsonData, success:function(data){
					
						parts = data.split("|");
						if(parts[0] == 'success'){
							alert(parts[1]);
						}
						if(parts[0] == 'error'){
							alert(parts[1]);
						}
						if(parts[0] == 'reload'){
							location.reload();
						}
						
						console.log(data);
					}
				
				});
			
			}
		
		})
	})
	
	
	
	$( '#autocomplete_product_id' ).autocomplete({		 
		source: function( request, response ) {
			var type = $('#autocomplete-product-type option:selected').val();
			var context = $(this);
			var productId = $("#product_id_relational").val();
			console.log(productId);
			$.ajax({
				url: home_uri+"/Webservice/App.php",
				dataType: "jsonp",
				data: {
					product_id:productId,
					term: request.term,
					action: "autocomplete_product_id",
					store_id: storeId,
					type: type
				},
				success: function( data ) {
//						console.log(data);
					response( data );
				}
			} );
		},
		minLength: 1,
		select: function( event, ui ) {
			if(typeof($('log')) !== 'undefined'){
				var cheked = ui.item.dynamic_price == 'T' ? 'checked' : '' ;
				$("#log").append("<tr id='"+ ui.item.id+"'><td><a href='/Products/Product/"+ ui.item.id+"' target='_blank'>"+ ui.item.value + "</a><br>"+
				"<span class='product-description small'><b>SKU:</b> "+ ui.item.sku + " - <b>Qtd.:</b> "+ ui.item.quantity + " - <b>Preço:</b> "+ ui.item.sale_price +"</span></td>"+
				"<td><div class='form-group'><input type='text' name='fixed_unit_price["+ui.item.id+"]'  class='fixed_unit_price form-control input-sm'  value=''></div></td>"+
				"<td align='center'><div class='form-group'><input type='checkbox' name='dynamic_price["+ui.item.id+"]' class='dynamic_price flat-red'></div></td>"+
				"<td><div class='form-group'><input type='text' name='discount_fixed["+ui.item.id+"]'  class='discount_fixed form-control input-sm'  value=''></div></td>"+
				"<td><div class='form-group'><input type='text' name='discount_percent["+ui.item.id+"]'  class='discount_percent form-control input-sm'  value=''></div></td>"+
				"<td><div class='form-group'><input type='text' name='products_relational["+ui.item.id+"]' class='qtd_product_relational form-control input-sm '  value='1'></div></td>" +
				"<td><a  class='fa fa-trash ' onclick='removeProductRelational(this)'  product_id='"+ui.item.product_id+"' /></td></tr>");
			}
			if(typeof($('items-order')) !== 'undefined'){
				$("#items-order").append("<tr id='"+ ui.item.id+"'><td colspan='3' >"+ ui.item.value + "<br> SKU: "+ ui.item.sku + " - "+ ui.item.variation+"</td><td><div class='form-group col-sm-4 pull-right'><input type='text' name='item["+ui.item.id+"][price_unit]'name='item["+ui.item.id+"][price]' class='form-control input-sm' id='"+ ui.item.id+"Price' onchange=\"updateOrderPrice(this, '"+ui.item.id+"')\" value='"+ ui.item.sale_price +"' /></div></td>"+
				"<td style='width:100px'><div class='form-group'><input type='text' name='item["+ui.item.id+"][qty]' class='qtd form-control input-sm' id='"+ui.item.id+"Qty' onchange=\"updateOrderQty(this, '"+ui.item.id+"')\" value='1'></div></td>"+
				"<td ><div class='form-group'><input type='text' name='item["+ui.item.id+"][price]'  class='price form-control input-sm'  id='"+ui.item.id+"'  value='"+ ui.item.sale_price + "'></div></td>"+
				"<td><div class='form-group'><a class='"+ui.item.product_id+" fa fa-trash' onclick='removeProductOrder(this)'  product_id='"+ui.item.product_id+"'></a>" +
				"<input type='hidden' name='PrecoUnitario' value='"+ ui.item.sale_price + "'></div></td></tr>");
				
				
				var subtotal = parseFloat($('#subtotal').val());
				var frete = parseFloat($('#frete').val());
				var total = parseFloat($('#total').val());
				var salePrice = parseFloat(ui.item.sale_price);
				subtotal = subtotal + salePrice;
				$('#subtotal').val(subtotal.toFixed(2));
//				$('#frete').val(frete + salePrice);
				total = total + salePrice;
				$('#total').val(total.toFixed(2));
				
				
			}
			return false;
		}
	});
	

	
	$('#frete, #discount').change(function (){
		
		var subtotal = parseFloat($('#subtotal').val());
		
		var frete = parseFloat($("#frete").val());
		
		var discount = parseFloat($("#discount").val());
			
		$('#total').val((subtotal - discount) + frete);
		
	});
	
	$( '#autocomplete_cpfcnpj' ).autocomplete({		 
		source: function( request, response ) {
			var type = 'cpfcnpj';
			var context = $(this);
			var cpfcnpj = $(this).attr('cpfcnpj');
			
			$.ajax({
				url: home_uri+"/Webservice/App.php",
				dataType: "jsonp",
				data: {
					cpfcnpj:cpfcnpj,
					term: request.term,
					action: "autocomplete_cpfcnpj",
					store_id: storeId,
					type: type
				},
				success: function( data ) {
						console.log(data);
					response( data );
				}
			} );
		},
		minLength: 3,
		select: function( event, ui ) {
			
			if(typeof($('customer-order')) !== 'undefined'){
				$('#autocomplete_cpfcnpj').val(ui.item.CPFCNPJ);
				$('#Nome').val(ui.item.Nome);
				$('#Email').val(ui.item.Email);
				$('#RGIE').val(ui.item.RGIE);
				$('#Telefone').val(ui.item.Telefone);
				$('#TelefoneAlternativo').val(ui.item.TelefoneAlternativo);
				$('#DataNascimento').val(ui.item.DataNascimento);
				$('#Apelido').val(ui.item.Apelido);
				$('#CEP').val(ui.item.CEP);
				$('#Bairro').val(ui.item.Bairro);
				$('#Endereco').val(ui.item.Endereco);
				$('#Numero').val(ui.item.Numero);
				$('#Complemento').val(ui.item.Complemento);
				$('#Cidade').val(ui.item.Cidade);
				$('#Estado').val(ui.item.Estado);
				$("#Genero option[value="+ui.item.Genero+"]").attr("selected", 'selected');
				$("#TipoPessoa option[value="+ui.item.TipoPessoa+"]").attr("selected", 'selected');
				
				
				
			}
			return false;
		}
	});
	
	
	
	$('.autocomplete_product_attr').each(function(){
		var context = $(this);
		$( this ).autocomplete({		 
			source: function( request, response ) {
				$.ajax({
					url: home_uri+"/Webservice/App.php",
					dataType: "jsonp",
					data: {
						product_id:productId,
						term: request.term,
						action: "autocomplete_product_attr",
						store_id: storeId,
						type: context.attr("id")
					},
					success: function( data ) {
//						console.log(data);
						response( data );
					}
				} );
			},
			minLength: 2,
			select: function( event, ui ) {

				context.val(ui.item.value);

				return false;
			}
		  
		} );
		
	})
	
	
		$('.autocomplete-attributes').each(function(){
		var context = $(this);
		$( this ).autocomplete({		 
			source: function( request, response ) {
				var attributeId = context.attr('attribute_id');
				var categoryId = context.attr('category_id');
				$.ajax({
					url: home_uri+"/Webservice/App.php",
					dataType: "jsonp",
					data: {
						term: request.term,
						action: "autocomplete_attributes",
						store_id: storeId,
						attribute_id: attributeId,
						category_id: categoryId
					},
					success: function( data ) {
//						console.log(data);
						response( data );
					}
				} );
			},
			minLength: 1,
			select: function( event, ui ) {

				context.val(ui.item.value);

				return false;
			}
		  
		} );
		
	})

	$('.remove_product_relational').each(function(){
		$(this).click(function(){
			var productId = $(this).attr('product_id');
			var productRelationalId = $(this).attr('product_relational_id');
			if(confirm('Tem certeza que deseja remover o produto relacionado?')){
				$.ajax({
					type: "POST",
					async: "true",
					url: home_uri+"/Webservice/App.php",
					data: {action:"remove_product_relational", product_relational_id:productRelationalId, store_id:storeId, product_id:productId},
					success: function(data){
						parts = data.split('|');
						if(parts[0] == 'success'){
							$('#'+productRelationalId).remove();
						}
						if(parts[0] == 'error'){
							alert(parts[1]);
						}
					}
				})
				
			}
			
		})
	});
	
	
	$('.openOrder').each(function(){
		$(this).on('click', function(){
			
			
	        var ifr = $('<iframe/>', {
	            id:	"MainPopupIframe",
	            src: "./orders_datail.php?pedido_id="+$(this).attr('id'),
	            style:'display:none;width:100%;height:1000px;',
	            load:function(){
	                $(this).show();

	            }
	        });
	        $('.iframeOrder').html(ifr);
	    });
	});
	
	$.fn.inlineEditParent = function(replaceWith) {
	    $(this).hover(function() {
	        $(this).addClass('hover');
	    }, function() {
	        $(this).removeClass('hover');
	    });
	    $(this).click(function() {
	    	var sku = $(this).attr('id');
	    	var parentId = $(this).attr('parent_id');
	    	var productId = $(this).attr('product_id');
	    	var color = $(this).attr('color');
	        var elem = $(this);
	        elem.hide();
	        elem.after(replaceWith);
	        replaceWith.focus();
	        replaceWith.blur(function() {
	            if ($(this).val() != "") {
	            	newId = $(this).val();
	            	$(this).val("");
	        		$.ajax({
	        			type: "POST",
	        			async: "true",
	        			url: home_uri+"/Webservice/App.php",
	        			data: {action:"update_parent_id", product_id:productId, 
	        				sku:sku, parent_id:parentId, store_id:storeId, newId:newId, color:color},
	        			success: function(data){
	        				elem.text(newId);
	        			}
	        		});
	            }
	            $(this).remove();
	            elem.show();
	        });
	    });
	};
	var replaceWith = $("<input type='text' name='parent_id'  class='input-sm form-control parent_id' style='width:80px;' value=''>");
	$(".inlineEditParent").inlineEditParent(replaceWith);
	
	$('.add_product_images').each(function(){
		$(this).click(function(){
			parentId = $(this).attr('parent_id');
			id = $(this).attr('id');
				acao = 'https://backoffice.sysplace.com.br/Views/_uploads/browse.php?store_id='+storeId+'&id='+id+'&parent_id='+parentId;
				popup(acao,'800','600');
		})
		
	})
	
	
	
//    var jsonObjPreview = [];
//    var jsonObjConfig = [];
//    
//    $(".imgs-path").each(function() {
//
//        var pathImg = $(this).val();
//
//        jsonObjPreview.push(pathImg);
//        
//        item = {}
//        item ["url"] = home_uri+'/Views/_uploads/remove_upload.php?action=remove_image_product&store_id='+storeId+'&product_id='+$(this).attr('product_id');
//        item ["caption"] = $(this).attr('fileName');
//        item ["key"] = $(this).attr('key');
//        item ["width"] = $(this).attr('width');
//        item ["size"] = $(this).attr('size');
//        jsonObjConfig.push(item);
//    });
//    
//    
//    
//    var productId = $('#id').val();
//    var title = $('#title').val();
//	$("#input-ke-1").fileinput({
//		theme: "explorer",
//	    uploadAsync: false,
//	    uploadExtraData: {store_id:storeId, product_id:productId, title:title},
//	    uploadUrl: home_uri+'/Views/_uploads/upload.php',
//		autoReplace: false,
//	    allowedFileExtensions: ['jpeg','jpg', 'png', 'gif'],
//	    overwriteInitial: false,
//	    previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
//	    initialPreviewAsData: true,
//	    initialPreview: jsonObjPreview,
//	    initialPreviewConfig: jsonObjConfig,
//	    initialPreviewDownloadUrl: home_uri+'/Views/_uploads/store_id_'+storeId+'/products/'+productId+'/{key}' // the key will be dynamically replaced  
//	}).on('filesorted', function(e, params){
////		$(".fileinput-image-sort").css("display","inline");
//		console.log(params);
////		console.log(params.newIndex);
//		
//		$.ajax({
//			type: "POST",
//			async: "true",
//			url: home_uri+'/Views/_uploads/manageFiles.php',
//			data: {action:"sort_image", store_id:storeId, product_id:productId, new_index:params.newIndex, old_index:params.oldIndex},
//			success: function(data){
//				console.log(data);
////				location.reload();
//			}
//		});
//		
//		
////		$(".fileinput-image-sort").css("display","none");
//	});
		

	
	
	$().tooltip();
	$(".select2").select2();
	$(".select_store_session").change(function() {
	     $('#store_session').submit();
	});
	
	
	$('.delete').click(function(){
		return confirm("Confirma a exclusão do registro?");
	})
	$('.duplicate-product').each(function(){
		$(this).click(function(){
			return confirm("Confirma duplicar o registro?");
			
		})
	})
	
	//iCheck for checkbox and radio inputs
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
	   checkboxClass: 'icheckbox_minimal-blue',
	   radioClass: 'iradio_minimal-blue'
	});
	//Red color scheme for iCheck
	$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
	   checkboxClass: 'icheckbox_minimal-red',
		radioClass: 'iradio_minimal-red'
	});
	 //Flat red color scheme for iCheck
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
	   checkboxClass: 'icheckbox_flat-blue',
	   radioClass: 'iradio_flat-blue'
	});

	
	$(".records").change(function() {
	     this.form.submit();
	});
	

	
	$('.select-all').click(function(){
		$('.check-select').prop('checked', true);
	});
	
	
//	$('.dynamic_price').on('ifClicked', function(event){
//		if($(this).is(":checked")) {
//			$('.discount_fixed').attr("disabled", true);
//			$('.discount_percent').attr("disabled", true);
//		}else{
//			$('.discount_fixed').attr("disabled", false);
//			$('.discount_percent').attr("disabled", false);
//			
//		}
//	});	

	$("#postalcode").blur(function(){
	    if($.trim($("#postalcode").val()) != ""){
	        $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+$("#postalcode").val(), function(){
	            if(resultadoCEP["resultado"] != 0){
	                $("#address").val(unescape(resultadoCEP["tipo_logradouro"])+": "+unescape(resultadoCEP["logradouro"]));
	                $("#neighborhood").val(unescape(resultadoCEP["bairro"]));
	                $("#city").val(unescape(resultadoCEP["cidade"]));
	                $("#state").val(unescape(resultadoCEP["uf"]));
	            }else{
	                alert("Endereço não encontrado");
	            }
	        });				
	    }	
	})
	
	$("#account_postalcode").blur(function(){
	    if($.trim($("#account_postalcode").val()) != ""){
	        $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+$("#account_postalcode").val(), function(){
	            if(resultadoCEP["resultado"] != 0){
	                $("#account_address").val(unescape(resultadoCEP["tipo_logradouro"])+": "+unescape(resultadoCEP["logradouro"]));
	                $("#account_neighborhood").val(unescape(resultadoCEP["bairro"]));
	                $("#account_city").val(unescape(resultadoCEP["cidade"]));
	                $("#account_state").val(unescape(resultadoCEP["uf"]));
	            }else{
	                alert("Endereço não encontrado");
	            }
	        });				
	    }	
	})
	
	
	
/*************** SMOBILE ****************************/	
    
	$(".open-store-sales").each(function(){
		$(this).click(function(){
			id = $(this).attr("cod"); 
			$("."+id ).dialog({
				width: 360,
				show: {
					effect: "blind",
			        duration: 300
			    },
			    hide: {
			    	effect: "explode",
			        duration: 300
			    }
		    });
			
		})
	});
	
	
	

	$(".import_account_stores").each(function(){
		$(this).click(function(){
			id = $(this).attr("id");
			$.ajax({
				type: "POST",
				async: "true",
				url: home_uri+"/Seta/webservice.php",
				data: "action=import_account_stores&account_id="+id,
				success: function(data){
					console.log(data);
//					location.reload();
				}
			});
			
			
		})
	});
	
	 $.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss' );    //Formatação com Hora
	 $.fn.dataTable.moment('DD/MM/YYYY');    //Formatação sem Hora
	  
	var table = $("#search-default").DataTable({
        order: [[0, 'desc']],
		"columnDefs": [ {
			          "targets": 'no-sort',
			          "orderable": false,
			    } ],
        paging: false,
        info: false,
        searching: false,
        "displayLength": 100,
		"scrollX": false,
        "language": {
            "lengthMenu": "Visualizar _MENU_ registros",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro encontrado",
            "infoFiltered": "(Localizou _MAX_ registros)",
            "loadingRecords": "Carregando...",
            "processing":     "Processando...",
            "search":         "Procurar:",
            "paginate": {
                "first":      "Primeira",
                "last":       "Última",
                "next":       "Próxima",
                "previous":   "Anterior"
            },
        }
       
    });

	var table = $("#search-categories").DataTable({

        order: [[1, 'asc']],
		"columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false,
	    } ],
        paging: false,
        info: false,
        "displayLength": 100,
		"scrollX": true,
        "language": {
            "lengthMenu": "Visualizar _MENU_ registros",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro encontrado",
            "infoFiltered": "(Localizou _MAX_ registros)",
            "loadingRecords": "Carregando...",
            "processing":     "Processando...",
            "search":         "Procurar:",
            "paginate": {
                "first":      "Primeira",
                "last":       "Última",
                "next":       "Próxima",
                "previous":   "Anterior"
            },
        }
       
    });

	
	
	// Add event listener for opening and closing details
    $('#search-advanced tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }else {
        	var data = row.data();
        	if(data[1]){
				$.ajax({
					type:"POST",
					async:"true",
					url:home_uri+"/Webservice/AppOrders.php",
					data: {action:"get_order_items", store_id:storeId, order_id:data[1], pedido_id:data[2], return_stock:true},
					success: function(data){
						console.log(data);
						parts = data.split("|");
						if(parts[0] == 'success'){
							
							 row.child( parts[1] ).show();
							 
							 if(parts[1]){
							 	$('.register_returncheck_in').each(function(){
								 
									$(this).click(function(){
										
										var items = [];
										var orderId = $(this).attr('order_id');
										var returnId = $(this).attr('return_id');
										var pedidoId = $(this).attr('pedido_id');
										var customerId = $(this).attr('customer_id');
										var valid = true;
										var sumQty = 0;
										var validQty = true;
										var validCheckin = true;
										var element;
										
										$('.return-items-'+orderId).each(function(){
											
											var NextTdElement = $(this).closest('td').next('td');
											var TdElement = $(this).closest('td');
											
											var checkIn =  NextTdElement.find('.check_in option:selected').val();	
											var qty_ordered = parseInt($(this).attr('qty_ordered'), 10);
											var qty = parseInt($(this).val(), 10);
											
											sumQty = qty +sumQty ;
											
											if(qty > qty_ordered){
												TdElement.find('.qty-form-group').addClass('has-error');
												validQty = false;
												valid  = false;
											}else{
												TdElement.find('.qty-form-group').removeClass('has-error');
											}
											
											if(qty > 0 && $.trim(checkIn) == ''){
												NextTdElement.find('.check-in-form-group').addClass('has-error');
												validCheckin = false;
												valid = false;
											}else{
												NextTdElement.find('.check-in-form-group').removeClass('has-error');
											}
											
											items.push({ return_stock:checkIn, relational:$(this).attr('relational'), product_id:$(this).attr('product_id'), item_id:$(this).attr('item_id'), qty:qty, qty_ordered:qty_ordered });
											
										});
										
										if(!validQty){
											alert('Quantidade devolvida maior que a quantidade comprada...');
											valid = false;
											return false;
										}
										
										if(!validCheckin){
											alert('Informe para qual estoque será enviado...');
											valid = false;
											return false;
										}
										if(sumQty == 0){
											alert('Informe a quantidade devolvida de cada produto ou utilize zero para produtos não devolvidos ou remover produto da devolução.');
											valid = false;
											return false;
										}
										
										if(orderId && valid){
											
											$('.message-returns-products').html("");
											
											$('.message-returns').html("");
											
											$.ajax({
												type:"POST",
												async:"false",
												url:home_uri+"/Webservice/AppOrders.php",
												data: {action:"check_in_item_order", store_id:storeId, pedido_id:pedidoId, order_id:orderId,  return_id:returnId, customer_id:customerId, user:userName, item:items},
												success: function(data){
													console.log(data);
													parts = data.split("|");
													if(parts[0] == 'success'){
//														
//														$('.message-returns').html("<div class='alert alert-success'><h4>Salvo!</h4></div>");
														TrHighlineSuccess(orderId);
//														row.child.hide();
//														tr.removeClass('shown');
														
													}
													if(parts[0] == 'error'){
														
														$('.message-returns').html("<div class='alert alert-danger'><h4>Erro ao registrar dados !</h4><p>"+parts[1]+"</p></div>");
													}
//													element = document.getElementById("message-returns");
//													element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
												}
											
											});
											
										}
										
									})
									
								});
							}
							
						}
					}
				})
        	}
            // Open this row
           
            tr.addClass('shown');
        }
        
    } );
	
	
	
	$('#btn-update-price').click(function(){
		
		var Fixed = $('#fixed').val();
		var Percent = $('#percent').val();
		var Brand = $('#brand option:selected').val();
		var Action = $('#action option:selected').val();
		if(confirm('Tem certeza que deseja atualizar o preço dos produtos?')){
			$.ajax({
				type: "POST",
				async: "true",
				url: home_uri+"/Webservice/AppProducts.php",
				data: {action:"update_price_manager", fixed:Fixed, percent:Percent, brand:Brand, action_price:Action, store_id:storeId, user:userName},
				success: function(data){
					
					console.log(data);
					
					parts = data.split('|');
					
					if(parts[0] == 'success'){
					
					}
					
					if(parts[0] == 'error'){
						alert(parts[1]);
					}
					
					
				}
				
			});
			
		}
		
	})
	
	$('.update_feed_products').each(function(){
		$(this).click(function(){
			
			var feedId = $(this).attr("id");
			
			$.ajax({
				
				type: "POST",
				async: "true",
				url: home_uri+"/Webservice/Feed.php",
				data: {action:"feed_products", feed_id:feedId, store_id:storeId},
				success: function(data){
					console.log(data);
				}
			
			});
			
		})
	});
		
});


function populate($form, data)
{
    //console.log("PopulateForm, All form data: " + JSON.stringify(data));

    $.each(data, function(key, value)   // all json fields ordered by name
    {
        //console.log("Data Element: " + key + " value: " + value );
        var $ctrls = $form.find('[name='+key+']');  //all form elements for a name. Multiple checkboxes can have the same name, but different values

        //console.log("Number found elements: " + $ctrls.length );

        if ($ctrls.is('select')) //special form types
        {
            $('option', $ctrls).each(function() {
                if (this.value == value)
                    this.selected = true;
            });
        } 
        else if ($ctrls.is('textarea')) 
        {
            $ctrls.val(value);
        } 
        else 
        {
            switch($ctrls.attr("type"))   //input type
            {
                case "text":
                case "hidden":
                    $ctrls.val(value);   
                    break;
                case "radio":
                    if ($ctrls.length >= 1) 
                    {   
                        //console.log("$ctrls.length: " + $ctrls.length + " value.length: " + value.length);
                        $.each($ctrls,function(index)
                        {  // every individual element
                            var elemValue = $(this).attr("value");
                            var elemValueInData = singleVal = value;
                            if(elemValue===value){
                                $(this).prop('checked', true);
                            }
                            else{
                                $(this).prop('checked', false);
                            }
                        });
                    }
                    break;
                case "checkbox":
                    if ($ctrls.length > 1) 
                    {   
                        //console.log("$ctrls.length: " + $ctrls.length + " value.length: " + value.length);
                        $.each($ctrls,function(index) // every individual element
                        {  
                            var elemValue = $(this).attr("value");
                            var elemValueInData = undefined;
                            var singleVal;
                            for (var i=0; i<value.length; i++){
                                singleVal = value[i];
                                console.log("singleVal : " + singleVal + " value[i][1]" +  value[i][1] );
                                if (singleVal === elemValue){elemValueInData = singleVal};
                            }

                            if(elemValueInData){
                                //console.log("TRUE elemValue: " + elemValue + " value: " + value);
                                $(this).prop('checked', true);
                                //$(this).prop('value', true);
                            }
                            else{
                                //console.log("FALSE elemValue: " + elemValue + " value: " + value);
                                $(this).prop('checked', false);
                                //$(this).prop('value', false);
                            }
                        });
                    }
                    else if($ctrls.length == 1)
                    {
                        $ctrl = $ctrls;
                        if(value) {$ctrl.prop('checked', true);}
                        else {$ctrl.prop('checked', false);}

                    }
                    break;
            }  //switch input type
        }
    }) // all json fields
}  // populate form
function popup(acao,larg,alt) {
	window.open(acao,larg,"scrollbars=yes,resizable=yes, width="+larg+",height="+alt);
}

function printShippingCodes(uri, shippingSendId){
	
	if(confirm("Deseja fechar e imprimir a remessa "+shippingSendId)){
		$.ajax({
			
			type: "POST",
			async: "true",
			url: uri+"/Webservice/App.php",
			data: {action:"close-print-shipping", shippind_send_id:shippingSendId, store_id:storeId},
			success: function(data){
				
				record = data.split("|");
				
				if(record[0] == "success"){
//					location.href = uri+"/Shipping/Send";
					var popupUri = uri+"/Shipping/ShippingSendDetail/id/"+record[1];
					popup(popupUri,'700','700');
				}
			}
		
		});
		
		
	}
	
}

function printPickingProducts(uri, pickingId){
	
	if(confirm("Deseja fechar e imprimir a coleta "+pickingId)){
		$.ajax({
			
			type: "POST",
			async: "true",
			url: uri+"/Webservice/AppOrders.php",
			data: {action:"close-picking", picking_id:pickingId, store_id:storeId},
			success: function(data){
				
				record = data.split("|");
				
				if(record[0] == "success"){
//					location.href = uri+"/Shipping/Send";
					var popupUri = uri+"/Shipping/PickingDetail/id/"+record[1];
					popup(popupUri,'700','700');
				}
			}
		
		});
		
		
	}
	
}

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
function showMessage(type, message){
	if(type == 'success'){
		$('.message').html("<div class='alert alert-success'><h4>"+message+"</h4></div>")
	}
	if(type == 'warning'){
		$('.message').html("<div class='alert alert-warning'><h4>"+message+"</h4></div>")
	}
	if(type == 'error'){
		$('.message').html("<div class='alert alert-danger'><h4>"+message+"</h4></div>")
	}
	setTimeout(function(){
		$(".alert").css("display","none");
	}, 10000);
} 

	
function removeProductRelational(o){
	if(confirm('Tem certeza que deseja remover o produto relacionado?')){
		var p=o.parentNode.parentNode;
		p.parentNode.removeChild(p);
	}
}
function removeProductOrder(o){
	var p=o.parentNode.parentNode.parentNode;
	p.parentNode.removeChild(p);
}


function updateOrderQty(o, productId){
	
	var qty  = $(o).val();
	
	var orginalPrice = parseFloat($("#"+productId+"Price").val());
	
	var priceUnitTotal = parseFloat($("#"+productId+" .price ").val());
	
	var totalProduct = (orginalPrice  * qty);
	
	var subtotal = parseFloat($('#subtotal').val());
	
	
	
	$("#"+productId+" .price ").val(totalProduct.toFixed(2));

	subtotal = (subtotal - priceUnitTotal) + totalProduct;
	
	$('#subtotal').val(subtotal.toFixed(2));
	
	var frete = parseFloat($("#frete").val());
	
	var discount = parseFloat($("#discount").val());
		
	var total = (subtotal - discount) + frete;
	$('#total').val(total.toFixed(2));
}

function updateOrderPrice(o, productId){
	
	var qty = parseFloat($("#"+productId+"Qty").val());
	
	var orginalPrice = parseFloat($(o).val());
	
	var priceUnitTotal = parseFloat($("#"+productId+" .price ").val());
	
	var totalProduct = (orginalPrice  * qty);
	
	var subtotal = parseFloat($('#subtotal').val());
	
	
	
	$("#"+productId+" .price ").val(totalProduct.toFixed(2));

	subtotal = (subtotal - priceUnitTotal) + totalProduct;
	
	$('#subtotal').val(subtotal.toFixed(2));
	
	var frete = parseFloat($("#frete").val());
	
	var discount = parseFloat($("#discount").val());
		
	var total = (subtotal - discount) + frete;
	$('#total').val(total.toFixed(2));
}


function updateOrderDiscount(o, productId){
	var discount  = $(o).val();
	var subtotal = $("#"+productId+" .price ").val();
	$("#"+productId+" .price ").val(subtotal - discount);
}



function removeProductImage(ob, uri, productId, fileName){
	
	var obj = ob;
	var productIdToRemove = productId;
	var fileNameToRemove = fileName;
	
	if(confirm("Deseja remover a imagem produto "+fileNameToRemove+" ?")){
		$.ajax({
			
			type: "POST",
			async: "true",
			url: uri+'/Views/_uploads/remove_upload.php',
			data: {action:"remove_image_product", product_id:productIdToRemove, key:fileNameToRemove, store_id:storeId, new_:'new'},
			success: function(data){
				console.log(data);
				record = data.split("|");
				
				if(record[0] == "success"){
					obj.closest('li').remove();
					if(parseInt($(".count-media").val()) > 1){
						$(".count-media").val(parseInt($(".count-media").val()) - 1);
					}else{
						$(".count-media").val(1);
					}
					window.location.href = uri+"/Products/Product/"+productIdToRemove+"/fotos";
				}
				if(record[0] == "error"){
					alert(record[1]);
				}
			}
		
		});
		
		
	}
	
}



function removePickingProduct(ob, uri, pickingId, pickingProductId, OrderId, PedidoId){
	
	var obj = ob;
	var pickingProductIdToRemove = pickingProductId;
	if(confirm("Deseja remover o produto "+PedidoId+" da coleta "+pickingProductId)){
		$.ajax({
			
			type: "POST",
			async: "true",
			url: uri+"/Webservice/AppOrders.php",
			data: {action:"remove-picking-product-order", picking_id:pickingId, order_id:OrderId, pedido_id:PedidoId, store_id:storeId, picking_product_id:pickingProductId},
			success: function(data){
				console.log(data);
				record = data.split("|");
				
				if(record[0] == "success"){
//					$('.table-hendling-in').after(record[1]);
					obj.closest('tr').remove();
					$('#qty-'+pickingProductIdToRemove).html('<strong>'+record[1]+'</strong>');
				}
				if(record[0] == "reload"){
//					window.location.href = home_uri+"/Shipping/picking/id/"+pickingId;
					obj.closest('tr').remove();
					$('.'+pickingProductIdToRemove).remove();
					
					
				}
				if(record[0] == "error"){
					alert(record[1]);
				}
			}
		
		});
		
		
	}
	
}

function removeShippingCode(uri, shippingSendId, shippingSendCodeId, barcode){
	
	
	if(confirm("Deseja remover o pacote "+barcode+" da remessa "+shippingSendId)){
		$.ajax({
			
			type: "POST",
			async: "true",
			url: uri+"/Webservice/App.php",
			data: {action:"remove-pack-shipping", shippind_send_id:shippingSendId, barcode:barcode, store_id:storeId, shipping_send_code_id:shippingSendCodeId},
			success: function(data){
				
				record = data.split("|");
				
				if(record[0] == "success"){
//					$("#"+shippingSendCodeId).remove();
					location.reload();
					
				}
			}
		
		});
		
		
	}
	
}

function TrHighline(idTr){
	$("#"+idTr).css({ backgroundColor: "c4c4c4" }).show().fadeIn();
	 setTimeout(function() {
         $( "#"+idTr ).removeAttr( "style" ).hide().fadeIn();
       }, 200 );
}
function TrHighlineSuccess(idTr){
	$("#"+idTr).css({ backgroundColor: "#00a65a" }).show().fadeIn();
	 setTimeout(function() {
        $( "#"+idTr ).removeAttr( "style" ).hide().fadeIn();
      }, 500 );
}

function defaultMessage(parts){
	
	$(".overlay").css("display","none");
	
	if(parts[0] == "success"){
		$('.message').html("<div class='alert alert-success'><h4>"+parts[1]+"</h4></div>");
	}
	if(parts[0] == "error"){
		$('.message').html("<div class='alert alert-danger'><h4>"+parts[1]+"</h4></div>");
	}
	if(parts[0] == "waring"){
		$('.message').html("<div class='alert alert-warning'><h4>"+parts[1]+"</h4></div>");
	}
	
	setTimeout(function(){
		$(".alert").css("display","none");
	}, 20000);
	
	return ;
	
}

function editReturnModal(orderId){
	$('.'+orderId).click();
}

function copyToClipboard(text) {

   var textArea = document.createElement( "textarea" );
   textArea.value = text;
   document.body.appendChild( textArea );       
   textArea.select();

   try {
      var successful = document.execCommand( 'copy' );
      var msg = successful ? 'successful' : 'unsuccessful';
      console.log('Copying text command was ' + msg);
   } catch (err) {
      console.log('Oops, unable to copy',err);
   }    
   document.body.removeChild( textArea );
}
