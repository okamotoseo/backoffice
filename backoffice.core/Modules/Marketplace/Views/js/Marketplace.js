$('document').ready(function(){
	
	$('.select_all_marketplace_manage_products').on('ifClicked', function(event){
		checked = $(".select_one_marketplace_manage_products:checked").length;
		if (checked == 0) {
			$('.select_one_marketplace_manage_products').iCheck('check');
		  } else {
			  $('.select_one_marketplace_manage_products').iCheck('uncheck');
		  }
	});	
	
	$('#add_all_available_products').click(function(){
		$(".amazon-products").css("display","inline");
		$.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Marketplace/Webservice/AddProducts.php",
			data: {action:"add_all_available_products", store_id:storeId, user:userName },
			success: function(data){
				parts = data.split("|");
				if(parts[0] == "success"){
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					$(".overlay").css("display","none");
				}else{
					console.log(data);
					$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
					$(".overlay").css("display","none");
				}
				
			}
		});
		
	});
	
	$('#select_action_marketplace_manage_products').change(function(){
		
		var productId = [];
		var action = $('#select_action_marketplace_manage_products').val();
		
		if(action != 'select'){
			$(".overlay").css("display","inline");
			
			$('.select_one_marketplace_manage_products').each(function(){
				if( $(this).is(':checked') ){
					 productId.push($(this).attr('id'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				
				case "add_available_products":
					uri = home_uri+"/Modules/Marketplace/Webservice/AddAvailableProducts.php",
					jsonData = {action:"add_available_product", store_id:storeId, product_id:productId, user:userName};
					break;
				case "remove_seller_products":
					uri = home_uri+"/Modules/Marketplace/Webservice/Products.php",
					jsonData = {action:"remove_available_product", store_id:storeId, product_id:productId, user:userName};
					
					break;
					
			
			}
			if(uri != ''){
				$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
							console.log(data);
						parts = data.split("|");
						if(parts[0] == "success"){
							$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
						}else{
							console.log(data);
							$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
						}
						$(".overlay").css("display","none");
					}
				});
			}
		}
		
	});
	$(".action_marketplace_product").each(function(){
		
		$(this).click(function(){
			
			var idVariation = $(this).attr('id');
			
			var action = $(this).attr('action');
			
			if(action != ''){
				$(".overlay").css("display","inline");
				var uri = '';
				var jsonData = '';
				switch(action){
					
					case "add_available_products":
						uri = home_uri+"/Modules/Marketplace/Webservice/AddAvailableProducts.php",
						jsonData = {action:"add_available_product", store_id:storeId, product_id:idVariation, user:userName};
						break;
					case "update_products_marketplace":
						uri = home_uri+"/Modules/Marketplace/Webservice/AddAvailableProducts.php",
						jsonData = {action:"update_products_marketplace", store_id:storeId, product_id:idVariation, user:userName};
						break;
					case "reject_available_products":
						uri = home_uri+"/Modules/Marketplace/Webservice/AddAvailableProducts.php",
						jsonData = {action:"reject_available_products", store_id:storeId, product_id:idVariation, user:userName};
						break;
						
					case "delete_marketplace_product":
						uri = home_uri+"/Modules/Marketplace/Webservice/Products.php",
						jsonData = {action:"remove_available_product", store_id:storeId, product_id:idVariation, user:userName};
						break;
				}
				
				$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
							console.log(data);
						parts = data.split("|");
						if(parts[0] == "success"){
							$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
						}else{
							console.log(data);
							$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
						}
						$(".overlay").css("display","none");  
					}
				});
			}
			
		});
		
	});
	
	$('#select_action_marketplace_products').change(function(){
		
		var productId = [];
		var action = $('#select_action_marketplace_products').val();
		
		if(action != 'select'){
			
			$(".overlay").css("display","inline");
			
			$('.select_one_marketplace_manage_products').each(function(){
				if( $(this).is(':checked') ){
					 productId.push($(this).attr('id'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				
				case "update_products_marketplace":
						uri = home_uri+"/Modules/Marketplace/Webservice/AddProducts.php",
						jsonData = {action:"update_available_product", store_id:storeId, product_id:productId, user:userName};
					break;
					
				case "delete_marketplace_product":
					if(confirm("Deseja realmente remover do Marketplacer Sysplace os produtos selecionados?")){
						uri = home_uri+"/Modules/Marketplace/Webservice/Products.php",
						jsonData = {action:"remove_available_product", store_id:storeId, product_id:productId, user:userName};
					}
					break;
			}
			if(uri != ''){
				$.ajax({type:"POST", async:"false", url:uri, data:jsonData, success: function(data){
						console.log(data);
						parts = data.split("|");
						if(parts[0] == "success"){
							location.reload();
						}else{
							console.log(data);
							$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
						}
						$(".overlay").css("display","none");
					}
				});
			}
		}
		
	});
})