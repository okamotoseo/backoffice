$(document).ready(function(){
	
	$('.select_all_tray_products').on('ifClicked', function(event){
		checked = $(".select_one_tray_products:checked").length;
		if (checked == 0) {
			$('.select_one_tray_products').iCheck('check');
		  } else {
			  $('.select_one_tray_products').iCheck('uncheck');
		  }
	});	

	$('#select_action_tray_products').change(function(){
		var sku = [];
		var parentId = [];
		var productId = [];
		var product_id = [];
		var action = $('#select_action_tray_products').val();
//		var action = $(this).attr('action');
		
		if(action != 'select'){
			$(".tray-products").css("display","inline");
			
			$('.select_one_tray_products').each(function(){
				if( $(this).is(':checked') ){
					 productId.push($(this).attr('id'));
					 product_id.push($(this).attr('product_id'));
				}
				
			})
			
			var uri = '';
			var jsonData = '';
			switch(action){ 
				
				case "update_products_tray":
					alert('verificar update_attributes_product_tray update_attributes');
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, type:"update",  status:"enabled"};
					break;
				case "disabled_products_tray":
					alert('verificar update_attributes_product_tray update_attributes');
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"export_products", store_id:storeId, parent_id:parentId, product_id:productId, type:"update",  status:"disabled"};
					break;	
				case "remove_products_tray":
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"delete_products", store_id:storeId, parent_id:parentId, product_id:product_id};
					break;
				case "update_attributes_product_tray":
					alert('verificar update_attributes_product_tray update_attributes');
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"update_attributes", store_id:storeId, product_id:product_id};
					break;
					
				case "update_variations_product_tray":
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"update_product_variations", store_id:storeId, product_id:product_id};
					
					break;
			}
			$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					console.log(parts);
//					for( var i = 0; i < parts.length; i++){
//						switch(action){
//							case "remove_products_tray": $('#'+parts[i]).remove(); break;
//						}
//					}
					
					$(".tray-products").css("display","none");		
			}
//					
			});
		}
		
	});
	
	
	$('.export_categories_tray').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Tray/Webservice/Categories.php",
			data: {action:'export_categories_hierarchy', store_id:storeId},
			success: function(data){
				console.log(data);
				$(".overlay").css("display","none");
//				location.reload();
			}
		});
		
	});
	

	$('.update_attributes_categories_tray').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Tray/Webservice/Categories.php",
			data: {action:'update_attributes_categories', store_id:storeId},
			success: function(data){
				parts = data.split('|');
				$(".overlay").css("display","none");
				if(parts[0] == 'success'){
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
				}
				if(parts[0] == 'error'){
					$('.message').html("<div class='callout callout-error'><h4>"+parts[1]+"</h4></div>");
				}
			}
		});
	});
	$('.update_attributes_categories').each(function(){
		$(this).click(function(){
			var categoryId = $(this).attr('category_id');
			$(".overlay").css("display","inline");
			$.ajax({
				type: "POST",
				async: "false",
				url:home_uri+"/Modules/Tray/Webservice/Categories.php",
				data: {action:'update_attributes_categories', store_id:storeId, category_id:categoryId}, 
				success: function(data){
					parts = data.split('|');
					$(".overlay").css("display","none");
					if(parts[0] == 'success'){
						$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					}
					if(parts[0] == 'error'){
						$('.message').html("<div class='callout callout-error'><h4>"+parts[1]+"</h4></div>");
					}
				}
			});
		});
	});
	
	$('.import_categories_tray').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Tray/Webservice/Categories.php",
			data: {action:'import_categories_hierarchy', store_id:storeId},
			success: function(data){
				console.log(data);
				$(".overlay").css("display","none");
//				location.reload();
			}
		});
		
	});
	$('.remove_categories_tray').click(function(){
		$(".overlay").css("display","inline");
		if(confirm("Esta ação é irreversível e irá excluir somento os relacionamentos e categorias importados, "+
				"nenhuma categoria será removida da sua loja Tray.")){
			$.ajax({
				type: "POST",
				async: "false",
				url:home_uri+"/Modules/Tray/Webservice/Categories.php",
				data: {action:'remove_categories_tray', store_id:storeId},
				success: function(data){
					console.log(data);
					$(".overlay").css("display","none");
					window.location.href
				}
			});
		}else{
			$(".overlay").css("display","none");
		}
	});
	$('.tray_categories_relationhsip').each(function(){
		$(this).change(function(){
			
			var trayCategoryId = $(this).val();
			var categoryIds = $(this).attr('category_id');
			
			if( categoryIds != "select"){
				
				var categoryIds = categoryIds.split("|");
				var trayCategoryId = trayCategoryId.split("|");
				
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Tray/Webservice/Categories.php",
					data: {action:'add_category_relationship', 
						parent_id:categoryIds[0], 
						category_id:categoryIds[1], 
						tray_parent_id:trayCategoryId[0], 
						tray_category_id:trayCategoryId[1], 
						store_id:storeId,
						user:userName
						},
					success: function(data){
						console.log(data);
						parts = data.split("|");
						if(parts[0] == 'success'){
							TrHighline(categoryIds[1]);
						}
						
					}
				});
			}
		});
	});
	
	
	$(".create_product_tray, .create_product_Tray").each(function(){
		$(this).click(function(){
			$(".ecommerce-publication-loading").css("display","inline");
			sku = $(this).attr('sku');
			parentId = $(this).attr('parent_id');
			productId = $(this).attr('product_id');
			
			if(sku != ""){
	    		$.ajax({
	    			type: "POST",
	    			async: "true",
	    			url:home_uri+"/Modules/Tray/Webservice/AddProducts.php",
	    			data: {
	    				action:"create_product_tray", 
	    				store_id:storeId, 
	    				sku:sku, 
	    				parent_id:parentId, 
	    				product_id:productId,
	    				user:userName
	    				},
	    			success: function(data){
						console.log(data);
						parts = data.split("|");
						if(parts[0].trim() == 'success'){
							
							$('.message, .message-actions').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
							$('.refresh-tab-publications').click();
							var windowLoca =  window.location.href.replace("#", "");
							var partsLocation = windowLoca.split('publications');
							
							if(partsLocation.length > 1){
								window.location.href = windowLoca;
							}else{
								window.location.href = partsLocation[0] + '/publications';
							}
							$(".tray-products, .ecommerce-publication-loading").css("display","none");
						}
						
						if(parts[0].trim() == 'error'){
							
							$('.message-actions, .message-actions-ecommerce').html("<div class='callout callout-error'><h4>"+parts[1]+"</h4></div>");
							$(".tray-products, .ecommerce-publication-loading").css("display","none");
						}
						
	    			}
		   		});
			}
			
		});	
	});
	
	$('.action_tray_product').each(function(){
		
		$(this).click(function(){
			
			$(".tray-products").css("display","inline");
			
			var id = $(this).attr("id");
			var product_id = $(this).attr("product_id");
			var id_product = $(this).attr("id_product");
			var parent_id = $(this).attr("parent_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
					
				case "delete_tray_product":
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"delete_products", store_id:storeId, id_product:id_product, product_id:product_id};
					break;
					
				case "update_stock_price_product_tray":
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"update_stock_price", store_id:storeId, product_id:product_id};
					break;
					
				case "update_product_tray":
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"update_product", store_id:storeId, product_id:product_id};
					break;
					
				case "update_product_image_tray":
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"update_product_image", store_id:storeId, product_id:product_id, id_product:id_product};
					break;
					
				case "update_attributes_product_tray":
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"update_attributes", store_id:storeId, product_id:product_id};
					break;
					
				case "update_product_variations_tray":
					 uri = home_uri+"/Modules/Tray/Webservice/Products.php",
					 jsonData = {action:"update_product_variations", store_id:storeId, product_id:product_id};
					break;
					
			}
			if(confirm('Deseja realmente efetuar a ação'+action)){
				$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
					
						parts = data.split("|");
						if(parts[0].trim() == 'success'){
							console.log(data);
							$('.message, .message-actions').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
							$('.refresh-tab-publications').click();
							var windowLoca =  window.location.href.replace("#", "");
							var partsLocation = windowLoca.split('publications');
							
							if(partsLocation.length > 1){
								window.location.href = windowLoca;
							}else{
								window.location.href = partsLocation[0] + '/publications';
							}
							$(".tray-products").css("display","none");
						}
						
						if(parts[0].trim() == 'error'){
							
							$('.message,  .message-actions').html("<div class='callout callout-error'><h4>"+parts[1]+"</h4></div>");
							$(".tray-products").css("display","none");
							
						}
						
						
					}
				
				});
			}
			
		});
		
	});
	
	
	$('#generate_products_tray_csv').click(function(){
		
		$(".tray-products").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Tray/Webservice/ExportCsv.php",
			data: {
				action:"export_products_tray", 
				store_id:storeId,
				id:$('#id').val(),
				product_id:$('#product_id').val(),
				parent_id:$('#parent_id').val(),
				id_product:$('#id_product').val(),
				ean:$('#ean').val(),
				title:$('#title').val(),
				reference:$('#reference').val(),
				collection:$('#collection').val(),
				stock:$('#stock').val(),
				available:$('#available').val(),
				images:$('#images').val(),
			
			},
			success: function(data){
				
				parts = data.split("|");
				
				if(parts[0] == 'success'){
					
					window.location = home_uri+"/Modules/Tray/Views/Report/export_products_tray.csv";
					
				}
				
				if(parts[0] == 'error'){
					
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					
				}
				
				$(".tray-products").css("display","none");
			
			}
		
		});
	});
	
	$('#update_product_information_tray').click(function(){
		
		$(".tray-products").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Tray/Webservice/Products.php",
			data: {action:"update_product_information_tray", store_id:storeId},
			success: function(data){
				
				parts = data.split("|");
				
				if(parts[0] == 'success'){
					
					location.reload();
				}
				
				if(parts[0] == 'error'){
					
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					
				}
				
				$(".tray-products").css("display","none");
			
			}
		
		});
	});

	$('#update_stock_price_tray').click(function(){
		
		$(".tray-products").css("display","inline");
		
		$.ajax({
			type:"POST",
			async:"false",
			url:home_uri+"/Modules/Tray/Webservice/Products.php",
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
			
				
				$(".tray-products").css("display","none");
			
			}
		
		});
	});
	
});