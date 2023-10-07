function ConfirmDialog(message) {
  $('<div></div>').appendTo('body')
    .html('<div><h6>' + message + '?</h6></div>')
    .dialog({
      modal: true,
      title: 'Delete message',
      zIndex: 10000,
      autoOpen: true,
      width: 'auto',
      resizable: false,
      buttons: {
        Yes: function() {
          // $(obj).removeAttr('onclick');                                
          // $(obj).parents('.Parent').remove();

          /*$('body').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');*/

          //$(this).dialog("close");
			return 1;
        },
        No: function() {
          /*$('body').append('<h1>Confirm Dialog Result: <i>No</i></h1>');*/

          //$(this).dialog("close");
			return 0;
        }
      },
      close: function(event, ui) {
        //$(this).remove();
		return 0;
      }
    });
};

$(document).ready(function(){
	
	$(window).keydown(function (e){
	    /*if (e.ctrlKey) alert("control");*/

	  	/*if(e.which === 112 && e.ctrlKey) {
	      
	    }*/

		if(e.which === 116) {
			window.location = document.getElementById('limpar').href
	    }
		if(e.which === 117) {
			$('#btn-print-document').click();
	    }
		if(e.which === 118) {
			$('#change-picking').click();
	    }
		
	});
	/*
	$(window).keypress(function(){
  		$("span").text(i += 1);
	});*/
	$( ".code-pedido-id-in" ).change(function() {
		
		$(".overlay").css("display","inline");
		
		var obj = $(this);
		
		var PedidoId = obj.val();
		
		var codeProduct = $('#code').val();
		
		var pickingId = $('#picking_id').val();
		
		var count = 0;
		
		var totalItens = 1;
		
		var rows = 1;
		
		if(pickingId != '' && PedidoId != ''){
			
			//if( $('.package').html() == '' || $("#tr-"+codeProduct).html() == 'undefined'){
				$.ajax({
					type: "POST",
					async: "false",
					url: home_uri+"/Webservice/AppShipping.php",
					data: {action:"add_order_document", picking_id:pickingId, pedido_id:PedidoId, store_id:storeId},
					success: function(data){
						obj.val("");
						obj.focus();
						packing = data.split("|");
						if(packing[0] == "success"){
							count++;
							$('#PedidoId').html(packing[1]);
							$('.order-info').html(packing[2]);
							$('.simple-danfe-content').html(packing[2]);
							if(packing[3] == 1 && packing[5] == 1 ){
								$('#box-package').addClass("box-success");
								$('.package').html(packing[4]);
								if(confirm('Deseja Imprimir 1 ?')){
									setTimeout(function(){
									  window.print();
									}, 500);
								}
							}else{
								$('#box-package').addClass("box-warning");
								$('.package').html(packing[4]);
								totalItens = packing[5];
							}
						}
						
						if(packing[0] == "error"){
							alert(packing[1]);
						}
						$(".overlay").css("display","none");
					}
				});
				
			//}
			
			$(".overlay").css("display","none");
			
		}else{
			
			alert('Separação não localizada...');
		}
		
		
	});
	
	
	$( ".code-product-package-in" ).change(function() {
		
		$(".overlay").css("display","inline");
		
		var obj = $(this);
		
		var codeProduct = $(this).val();
		
		var pickingId = $('#picking_id').val();
		
		var PedidoId = $(this).attr("pedido_id");
		
		var OrderId = $(this).attr("order_id");
		
		var count = 0;
		
		var totalItens = 1;
		
		var rows = 1;
		
		if(pickingId != '' && codeProduct != ''){
			
			if( $('.package').html() == '' || $("#tr-"+codeProduct).html() == 'undefined'){
				$.ajax({
					type: "POST",
					async: "false",
					url: home_uri+"/Webservice/AppShipping.php",
					data: {action:"add_product_package", code:codeProduct, picking_id:pickingId, pedido_id:PedidoId, store_id:storeId},
					success: function(data){
						obj.val("");
						obj.focus();
						packing = data.split("|");
						if(packing[0] == "success"){
							count++;
							$('#PedidoId').html(packing[1]);
							$('.order-info').html(packing[2]);
							$('.simple-danfe-content').html(packing[2]);
							if(packing[3] == 1 && packing[5] == 1 ){
								$('#box-package').addClass("box-success");
								$('.package').html(packing[4]);
								if(confirm('Deseja Imprimir 1 ?')){
									setTimeout(function(){
									  window.print();
									}, 500);
								}
							}else{
								$('#box-package').addClass("box-warning");
								$('.package').html(packing[4]);
								totalItens = packing[5];
							}
						}
						
						if(packing[0] == "error"){
							alert(packing[1]);
						}
						$(".overlay").css("display","none");
					}
				});
				
			}else{
				 
				$('.tr-order').each(function(){
					if($(this).attr('ean') == codeProduct || $(this).attr('sku') == codeProduct){
						
						var totalQty = parseInt($(this).find('#'+codeProduct+'-qtd_total').html());
						
						var qtyAdded = parseInt($(this).find('#'+codeProduct+'-qtd_added').html());
						
						if(qtyAdded < totalQty){
							$(this).find('#'+codeProduct+'-qtd_added').html(parseInt($(this).find('#'+codeProduct+'-qtd_added').html(), 10)+1);
							
							$(this).find('#'+codeProduct+'-qtd_added').removeClass("waiting-pickup");
							
							if(parseInt($(this).find('#'+codeProduct+'-qtd_added').html()) == totalQty){
								
								$(this).find('#'+codeProduct+'-qtd_added').removeClass("waiting-pickup");
								$(this).find('#'+codeProduct+'-qtd_added').removeClass("btn-warning");
								$(this).find('#'+codeProduct+'-qtd_added').addClass("bg-olive");
								
								$(this).find('#'+codeProduct+'-qtd_total').removeClass("btn-warning");
								$(this).find('#'+codeProduct+'-qtd_total').addClass("bg-olive");
							}else{
								$(this).find('#'+codeProduct+'-qtd_added').removeClass("bg-olive");
								$(this).find('#'+codeProduct+'-qtd_added').addClass("btn-warning");
							}
						}
						
						if(parseInt($(this).find('#'+codeProduct+'-qtd_added').html()) == totalQty){
							
							$(this).find('#'+codeProduct+'-qtd_added').removeClass("waiting-pickup");
							$(this).find('#'+codeProduct+'-qtd_added').removeClass("btn-warning");
							$(this).find('#'+codeProduct+'-qtd_added').addClass("bg-olive");
							
							$(this).find('#'+codeProduct+'-qtd_total').removeClass("btn-warning");
							$(this).find('#'+codeProduct+'-qtd_total').addClass("bg-olive");
							
						}
					
					}
					
				});
			
				var exist = false;
				
				$('.tr-order').each(function(){
					if( parseInt( $(this).find('.qtd_added').html() ) != parseInt( $(this).find('.qtd_total').html() ) ){
						exist = true;
					}
						
				});
				
				if(!exist){
					$('#box-package').removeClass("box-warning");
					$('#box-package').addClass("box-success");
					if(confirm('Deseja Imprimir ?')){
						window.print();
					}
				}
			
			}
	
			$(".overlay").css("display","none");
			
			/*obj.val("");
			obj.focus();*/
			
		}else{
			/*obj.focus();*/
				alert('Separação não localizada...');
		}
		
		
	});
	
	
	$('#btn-print-document').click(function(){
		
		window.print();
		
		/*var orderId = $(this).attr("order_id");
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
			
		});*/
		
	})
	
	
});