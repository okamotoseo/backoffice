function getCategories(category_id, idPath) {
	id = $("#"+idPath).parent().attr('ind');
	categoryEcommerce = $("#"+idPath).attr('category');
	$.ajax({
		type: "POST",
		async: "true",
		url:home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
		data: {action:'get_categories', category:categoryEcommerce, category_id:category_id, store_id:storeId},
		success: function(data){
			parts = data.split("|");
			res = parts[0].trim();
			if(res == "next"){
				$("[name='"+id+"']").html(parts[1]).focusout();
				$("#"+id).empty().append(parts[2]).focusout();
			}
			if(res == "end"){
				$("[name='"+id+"']").html(parts[1]).focusout();
				$("#"+id).empty().append(parts[2]).focusout();
			}
		}
	});
}

$(document.body).on("keydown", "#alternative-title", function () {
    $(".caracteres_count").text(parseInt($(this).val().length));
});

$(document).ready(function(){
	$.fn.inlineEditSkuMeliVar = function(replaceWith) {
	    $(this).hover(function() {
	        $(this).addClass('hover');
	    }, function() {
	        $(this).removeClass('hover');
	    });
	    $(this).click(function() {
	    	var adsId = $(this).attr('ads_id');
	    	var variationId = $(this).attr('variation_id');
	        var elem = $(this);
	        elem.hide();
	        elem.after(replaceWithInputSku);
	        replaceWith.focus();
	        replaceWith.blur(function() {
	            if ($(this).val() != "") {
	            	newId = $(this).val();
	            	$(this).val("");
	        		$.ajax({
	        			type: "POST",
	        			async: "true",
	        			url: home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
	        			data: {action:"updte_sku_products_variation_meli", store_id:storeId, ads_id:adsId, variation_id:variationId,  new_id:newId,},
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
	var replaceWithInputSku = $("<input type='text' name='sku'  class='input-sm form-control parent_id' style='width:80px;' value=''>");
	$(".inlineEditSkuMeliVar").inlineEditSkuMeliVar(replaceWithInputSku);
	
	$('.action_meli_relationship_variation').each(function(){
		
		$(this).click(function(){
			
			$(".meli-ads-relationship").css("display","inline");
			
			var id = $(this).attr("id");
			var adsId = $(this).attr("ads_id");
			var variationId = $(this).attr("variation_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
					
				case "delete_products_variation_meli":
					var confirmDelete = confirm("Confirma a exclusão da variação!");
					if(confirmDelete == true){
						 uri = home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
						 jsonData = {action:"delete_products_variation_meli", store_id:storeId, ads_id:adsId, variation_id:variationId};
					}else{
						return false;
					}
					break;
					
			}
			
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					switch(action){
						case "delete_products_variation_meli": 
							if(parts[0] == 'success'){
								$('#'+parts[1]).remove(); 
								location.reload();
							}
							
							break;
					}
					
				}
			
			});
			
			$(".meli-ads-relationship").css("display","none");
			
			
		});
		
	});

	$('.select_all_adverts').on('ifClicked', function(event){
		checked = $(".select_one_advert:checked").length;
		if (checked == 0) {
			$('.select_one_advert').iCheck('check');
		  } else {
			  $('.select_one_advert').iCheck('uncheck');
		  }
	});	
	$('#select_action_meli_adverts').change(function(){
		var advertId = [];
		var action = $('#select_action_meli_adverts').val();
		if(action != 'select'){
			$(".meli-adverts").css("display","inline");
			
			$('.select_one_advert').each(function(){
				if( $(this).is(':checked') ){
					advertId.push($(this).attr('advert_id'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				case "remove_meli_adverts":
					var confirmDelete = confirm("Confirma a exclusão de anúncios!");
					
					if(confirmDelete == true){
						 uri = home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
						 jsonData = {action:"remove_ads_product", store_id:storeId, ads_id:advertId};
					}else{
						return false;
					}
					break;
				case "update_stock_price":
					
					 uri = home_uri+"/Modules/Mercadolivre/Webservice/UpdateAds.php",
					 jsonData = {action:"update_stock_price", store_id:storeId, ads_id:advertId, user:userName};
				
					break;
			}
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					console.log(data);
					parts = data.split("|");
					
					if(parts[0] == 'success'){
						switch(action){
							case "remove_meli_adverts":
								if(parts[0] == 'success'){
									$('#message-adverts').html("<div class='callout callout-success'><h4>Anúncios removidos com sucesso!</h4></div>");
									$("#"+parts[1]).remove();
//									location.reload();
								}
							break;
							default: $('#message-adverts').html("<div class='callout callout-success'><h4>Solicitação efetuado com sucesso!</h4></div>"); break;
						}
					}
						
					if(parts[0] == 'error'){
						$('#message-adverts').html("<div class='callout callout-danger'><h4>Erro ao excluir anúncios!</h4><p>"+parts[1]+"</p></div>");
					}
					
					$('#selected').attr("selected", true);
					
					$(".meli-adverts").css("display","none");	
				
				}
			
			});
			
		}
			
	});

	
	$('.action_ads').each(function(){
		$(this).click(function(){
			$(".meli-adverts").css("display","inline");	
			var adsId = $(this).attr("ads_id");
			var sku = $(this).attr("sku");
			var productId = $(this).attr("product_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
				case "remove_ads":
					var confirmDelete = confirm("Confirma a exclusão do anuncio!");
					if(confirmDelete == true){
						 uri = home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
						 jsonData = {action:"remove_ads_product", store_id:storeId, ads_id:adsId, sku:sku};
					}else{
						return false;
					}
					break;
				case "update_stock":
					 uri = home_uri+"/Modules/Mercadolivre/Webservice/UpdateAds.php",
					 jsonData = {action:"update_stock", store_id:storeId, ads_id:adsId, sku:sku, product_id:productId, user:userName};
					break;
				case "update_stock_price":
					 uri = home_uri+"/Modules/Mercadolivre/Webservice/UpdateAds.php",
					 jsonData = {action:"update_stock_price", store_id:storeId, ads_id:adsId, sku:sku, user:userName};
					break;
					
				case "update_description":
					 uri = home_uri+"/Modules/Mercadolivre/Webservice/UpdateAds.php",
					 jsonData = {action:"update_description", store_id:storeId, ads_id:adsId, sku:sku, user:userName};
					break;
				case "import_ads_variations":
					 uri = home_uri+"/Modules/Mercadolivre/Webservice/ImportAds.php",
					 jsonData = {action:"import_ads_variations", store_id:storeId, ads_id:adsId};
					break;
				case "update_ads_pictures":
					 uri = home_uri+"/Modules/Mercadolivre/Webservice/UpdateAds.php",
					 jsonData = {action:"update_ads_pictures", store_id:storeId, ads_id:adsId};
					break;
			}
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
				console.log(data);
					parts = data.split("|");
					switch(action){
						case "remove_ads": 
							if(parts[0] == 'success'){
								$('.message-actions').html("<div class='callout callout-success'><h4>Anúncio removido com sucesso</h4></div>");
								$('#'+parts[1]).remove(); 
								var windowLoca =  window.location.href.replace("#", "");
								windowLoca =  window.location.href.replace("fotos", "");
								
								var partsLocation = windowLoca.split('publications');
								if(partsLocation.length > 1){
									var url = windowLoca;
								}else{
									var url = partsLocation[0] + '/publications';
								}
								window.location.href = url;
							}
							if(parts[0] == 'error'){
								$('.message-actions').html("<div class='callout callout-warning'><h4>"+parts[1]+"</h4></div>");
							}
							break;
						case "update_stock_price": 
							if(parts[0] == 'success'){
								$('.message-actions').html("<div class='callout callout-success'><h4>Anúncio Atualizado com sucesso</h4></div>");
//								var windowLoca =  window.location.href.replace("#", "");
//								var partsLocation = windowLoca.split('publications');
//								if(partsLocation.length > 1){
//									var url = windowLoca;
//								}else{
//									var url = partsLocation[0] + '/publications';
//								}
//								window.location.href = url;
							}
							if(parts[0] == 'error'){
								$('.message-actions').html("<div class='callout callout-warning'><h4>"+parts[1]+"</h4></div>");
							}
							break;
						default: 
							if(parts[0] == 'success'){
								$('.message-actions').html("<div class='callout callout-success'><h4>Anúncio atualizado com sucesso</h4></div>");
							}
							if(parts[0] == 'error'){
								$('.message-actions').html("<div class='callout callout-warning'><h4>"+parts[1]+"</h4></div>");
							}
							break;
					}
					$(".meli-adverts").css("display","none");	
				}
			});
		});
	});
	
	$('.action_import_ads').each(function(){
		$(this).click(function(){
			
			if($("#ads_id").val().length === 0 ){
				 return alert('Informe o código do anúncio!');
			}
			$(".overlay").css("display","inline");
			var adsId = $("#ads_id").val();
			
			var sku = $("#sku").val();
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
				case "import_ads":
					 uri = home_uri+"/Modules/Mercadolivre/Webservice/ImportAds.php",
					 jsonData = {action:action, store_id:storeId, ads_id:adsId, sku:sku};
					break;
				case "import_ads_variations":
					 uri = home_uri+"/Modules/Mercadolivre/Webservice/ImportAds.php",
					 jsonData = {action:action, store_id:storeId, ads_id:adsId, sku:sku};
					break;
			}
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					
					if(parts[0] == 'success'){
						$('.message').html("<div class='callout callout-success'><h4>Anúncio importado com sucesso</h4></div>");
					}
					if(parts[0] == 'error'){
						$('.message').html("<div class='callout callout-warning'><h4>"+parts[1]+"</h4></div>");
					}
					$(".overlay").css("display","none");	
				}
			});
		});
	});
	
	
	$('.ml-task').each(function(){
		$(this).click(function(){
			
			var ws = $(this).attr('ws');
			var action = $(this).attr('id');
			var param = $('#'+action).attr('param');
			var paramValue = $('#'+action).val();
			$(".ajaxload-"+action).css("display","inline");
			
			$.ajax({
				type: "POST",
				async: "true",
				url: home_uri+"/Modules/Mercadolivre/Webservice/"+ws+".php",
				data: {action:action, order_id:paramValue, store_id:storeId},
				success: function(data){
					console.log(data);
					$(".ajaxload-"+action).css("display","none");
	
				}
			})
			
		})
		
	});

	$(".new_ads_product_new").each(function(){
		$(this).click(function(){
			sku = $(this).attr('sku');
			parentId = $(this).attr('parent_id');
			productId = $(this).attr('product_id');
			alternativeTitle = $('#alternative-title').val();
			listingTypes = $('#listing_types').val();
			
			$(".mercadolivre-publication-loading").css("display","inline");
			$(".ajaxload-"+productId).css("display","inline");
			if(sku != ""){
	    		$.ajax({
	    			type: "POST",
	    			async: "true",
	    			url:home_uri+"/Modules/Mercadolivre/Webservice/ProductsNew.php",
	    			data: {action:"new_ads_product", store_id:storeId, sku:sku, parent_id:parentId, product_id:productId, title:alternativeTitle, listing_types:listingTypes, user:userName},
	    			success: function(data){
						parts = data.split("|");
						if(parts[0] == "success"){
							$('#'+parts[2]).remove();
							
							var windowLoca =  window.location.href.replace("#", "");
							windowLoca =  window.location.href.replace("fotos", "");
							var partsLocation = windowLoca.split('publications');
							
							if(partsLocation.length > 1){
								var url = windowLoca;
							}else{
								var url = partsLocation[0] + '/publications';
							}
							window.location.href = url;
							window.open(parts[3], '_blank');
							
						}else{
							alert(parts[1]);
						}
						$(".mercadolivre-publication-loading").css("display","none");
						$(".ajaxload-"+productId).css("display","none");

	    			}
		   		});
			}
		});	
	});
	
	$('.remove-ads').each(function(){
		$(this).click(function(){
			adsId = $(this).attr('ads_id');
			
			if(adsId != ""){
				if(confirm("Confirma a finalização do anúncio: "+adsId)){
		    		$.ajax({
		    			type: "POST",
		    			async: "true",
		    			url: home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
		    			data: {action:'remove_ads_product', ads_id:adsId, store_id:storeId},
		    			success: function(data){
		    				if(data == 'success'){
	//	    					alert("Anúncio finalizado com sucesso!");
		    					location.reload();
		    				}else{
//		    					alert("Erro ao finalizar anúncio!"+data);
		    				}
		    			}
		    		})
				}
			}else{
				alert("Produto sem SKU");
			}
			
		})
	});
	$('.update_ads').each(function(){
		$(this).click(function(){
			sku = $(this).attr('sku');
			parentId = $(this).attr('parent_id');
			productId = $(this).attr('product_id');
			adsId = $(this).attr('ads_id');
			$(".ajaxload-"+productId).css("display","inline");
			if(sku != ""){
	    		$.ajax({
	    			type: "POST",
	    			async: "true",
	    			url:home_uri+"/Modules/Mercadolivre/Webservice/UpdateAds.php",
	    			data: {action:"update_ads", store_id:storeId, sku:sku, parent_id:parentId, ads_id:adsId, product_id:productId},
	    			success: function(data){
						parts = data.split("|");
						if(parts[0] == 'success'){
							$('.message').html("<div class='callout callout-success'><h4>Anúncio atualizado com sucesso</h4></div>");
						}
						if(parts[0] == 'error'){
							$('.message').html("<div class='callout callout-warning'><h4>"+parts[1]+"</h4></div>");
						}
						$(".ajaxload-"+productId).css("display","none");

	    			}
		   		});
			}
			
			
		})
	})
	
	
	

	
	
	$('.ml_attribute_relationship').each(function(){
		$(this).change(function(){
			
			attribute = $(this).val();
			parts = attribute.split("|");
			attributeId = parts[0];
			alias = parts[1];
			
			mlCategoryId = $(this).attr('ml_category_id');
			mlAttributeId = $(this).attr('ml_attribute_id');
			var select = $(this);
			$.ajax({
				type: "POST",
				async: "true",
				url: home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
				data: {action:'add_attribute_relationship', ml_category_id:mlCategoryId, 
					attribute_id:attributeId, attribute_alias:alias, 
					ml_attribute_id:mlAttributeId, store_id:storeId},
				success: function(data){
					
					parts = data.split("|");
					res = parts[0].trim();
					if(res == "success"){
						select.parent().prev().html(parts[1]);
						
					}
				}
			});
			
		})
	})
	
	

	$(".category" ).each(function(){
		$(this).change(function() {
			if($(".category" ).is(':focus')){
				category = $(this).val();
				id = $(this).attr("id");
				categoryEcommerce = $(this).attr("category");
				var select = $(this);
				if(category != 'select'){
					$.ajax({
						type: "POST",
						async: "true",
						url:home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
						data: {action:'get_categories', category:categoryEcommerce, category_id:category, store_id:storeId},
						success: function(data){
							parts = data.split("|");
							res = parts[0].trim();
							if(res == "next"){
								select.parent().prev().html(parts[1]);
								select.empty().append(parts[2]).focusout();
								select.parent().prev().prev().html(parts[3]);
							}
							if(res == "end"){
								select.parent().prev().html(parts[1]);
								select.empty().append(parts[2]).focusout();
								select.parent().prev().prev().html(parts[3]);
							}
						}
					});
				}
			}
		});
	});
	
	$('.ml_color_relationship').each(function(){
		$(this).change(function(){
			mlColorId = $(this).val();
			color = $(this).attr('color');
			colorId = $(this).attr('id');
			if( mlColorId != "select"){
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
					data: {action:'add_color_relationship', ml_color_id:mlColorId,  color_id:colorId, color:color, store_id:storeId, number:1},
					success: function(data){
						
						console.log(data);
						
					}
				});
			}
		});
	});
	
	$('.ml_color_relationship_2').each(function(){
		$(this).change(function(){
			mlColorId = $(this).val();
			color = $(this).attr('color');
			colorId = $(this).attr('id');
			if( mlColorId != "select"){
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
					data: {action:'add_color_relationship', ml_color_id:mlColorId,  color_id:colorId, color:color, store_id:storeId, number:2},
					success: function(data){
						
						console.log(data);
						
					}
				});
			}
		});
	});


});