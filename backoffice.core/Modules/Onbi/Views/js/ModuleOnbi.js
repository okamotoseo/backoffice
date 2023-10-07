
$(document).ready(function(){
	$("#dataUpdate").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
	
	$('.select_all_onbi_products').on('ifClicked', function(event){
		checked = $(".select_one_onbi_products:checked").length;
		if (checked == 0) {
			$('.select_one_onbi_products').iCheck('check');
		  } else {
			  $('.select_one_onbi_products').iCheck('uncheck');
		  }
	});	

	$('#select_action_onbi_products').change(function(){
		var Ids = [];
		var SKUs = [];
		var action = $('#select_action_onbi_products').val();
		
		if(action != 'select'){
			$(".overlay").css("display","inline");
			
			$('.select_one_onbi_products').each(function(){
				if( $(this).is(':checked') ){
					 Ids.push($(this).attr('id'));
					 SKUs.push($(this).attr('sku'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				case "update_products_onbi":
					 uri = home_uri+"/Modules/Onbi/Webservice/AddProducts.php";
					 jsonData = {action:"update_product_magento", store_id:storeId,  product_id:Ids, sku:SKUs};
					break;
					
				case "update_product_relational_onbi":
					 uri = home_uri+"/Modules/Onbi/Webservice/AddProducts.php";
					 jsonData = {action:"update_product_relational_magento", store_id:storeId, product_id:Ids, sku:SKUs};
					break;
			}
			$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					console.log(parts);
					$(".overlay").css("display","none");		
				}
			});
		}
		
	});
	
	
	
	
	$('.product_action_Onbi').each(function(){
		$(this).click(function(){
			$(".ecommerce-publication-loading").css("display","inline");	
			var sku = $(this).attr("sku");
			var productId = $(this).attr("product_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
				case "export_stock":
					 uri = home_uri+"/Modules/Onbi/Webservice/Products.php";
					 jsonData = {action:"export_stock", store_id:storeId,  product_id:productId, sku:sku, user:userName};
					break;
				
			}
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					console.log(data);
					parts = data.split("|");
					if(parts[0] == 'success'){
						$('.message-actions-ecommerce').html("<div class='callout callout-success'><h4>Anúncio atualizado com sucesso</h4></div>");
					}
					if(parts[0] == 'error'){
						$('.message-actions-ecommerce').html("<div class='callout callout-warning'><h4>"+parts[1]+"</h4></div>");
					}
					
					$(".ecommerce-publication-loading").css("display","none");	
				}
			});
		});
		
	});
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	$(".create_product_Onbi").each(function(){
		$(this).click(function(){
			$(".ecommerce-publication-loading").css("display","inline");
			sku = $(this).attr('sku');
			parentId = $(this).attr('parent_id');
			productId = $(this).attr('product_id');
			
			if(sku != ""){
	    		$.ajax({
	    			type: "POST",
	    			async: "true",
	    			url:home_uri+"/Modules/Onbi/Webservice/AddProducts.php",
	    			data: {
	    				action:"create_product_magento", 
	    				store_id:storeId, 
	    				sku:sku, 
	    				parent_id:parentId, 
	    				product_id:productId
	    				},
	    			success: function(data){
	    				console.log(data);
						parts = data.split("|");
						if(parts[0] == 'success'){
							$('.message-actions-ecommerce').html("<div class='callout callout-success'><h4>Anúncio atualizado com sucesso</h4></div>");
						}
						if(parts[0] == 'error'){
							$('.message-actions-ecommerce').html("<div class='callout callout-warning'><h4>"+parts[1]+"</h4></div>");
						}
						
						$(".ecommerce-publication-loading").css("display","none");	
						
	    			}
		   		});
			}
			
		});	
	});
	
	

//	$(".create_product_magento").each(function(){
//		$(this).click(function(){
//			$(".ecommerce-publication-loading").css("display","inline");
//			sku = $(this).attr('sku');
//			parentId = $(this).attr('parent_id');
//			productId = $(this).attr('product_id');
//			
//			if(sku != ""){
//	    		$.ajax({
//	    			type: "POST",
//	    			async: "true",
//	    			url:home_uri+"/Modules/Onbi/Webservice/AddProducts.php",
//	    			data: {
//	    				action:"create_product_magento", 
//	    				store_id:storeId, 
//	    				sku:sku, 
//	    				parent_id:parentId, 
//	    				product_id:productId
//	    				},
//	    			success: function(data){
//						parts = data.split("|");
//						if(parts[0] == "success"){
//							var windowLoca =  window.location.href.replace("#", "");
//							var partsLocation = windowLoca.split('publications');
//							
//							if(partsLocation.length > 1){
//								var url = windowLoca;
//							}else{
//								var url = partsLocation[0] + '/publications';
//							}
//							window.location.href = url;
//						}else{
//							alert(parts[1]);
//						}
//						
//						$(".ecommerce-publication-loading").css("display","none");
//						
//	    			}
//		   		});
//			}
//			
//		});	
//	});
	$(".update_product_magento").each(function(){
		$(this).click(function(){
			$(".ecommerce-publication-loading").css("display","inline");
			sku = $(this).attr('sku');
			parentId = $(this).attr('parent_id');
			productId = $(this).attr('product_id');
			idProduct = $(this).attr('id');
			
			if(sku != ""){
	    		$.ajax({
	    			type: "POST",
	    			async: "true",
	    			url:home_uri+"/Modules/Onbi/Webservice/AddProducts.php",
	    			data: {
	    				action:"update_product_magento", 
	    				store_id:storeId, 
	    				sku:sku, 
	    				parent_id:parentId, 
	    				product_id:productId
	    				},
	    			success: function(data){
						parts = data.split("|");
						if(parts[0] == "success"){
//							location.reload();
							var windowLoca =  window.location.href.replace("#", "");
							var partsLocation = windowLoca.split('publications');
							
							if(partsLocation.length > 1){
								var url = windowLoca;
							}else{
								var url = partsLocation[0] + '/publications';
							}
							window.location.href = url;
						}else{
							alert(parts[1]);
						}
						
						$(".ecommerce-publication-loading").css("display","none");
						
	    			}
		   		});
			}
			
		});	
	});
	
	$(".create_product_relational_magento").each(function(){
		$(this).click(function(){
			$(".ecommerce-publication-loading").css("display","inline");
			sku = $(this).attr('sku');
			parentId = $(this).attr('parent_id');
			productId = $(this).attr('product_id');
			idProduct = $(this).attr('id');
			
			if(sku != ""){
	    		$.ajax({
	    			type: "POST",
	    			async: "true",
	    			url:home_uri+"/Modules/Onbi/Webservice/AddProducts.php",
	    			data: {
	    				action:"create_product_relational_magento", 
	    				store_id:storeId, 
	    				sku:sku, 
	    				parent_id:parentId, 
	    				product_id:productId,
	    				id_product:idProduct
	    				},
	    			success: function(data){
						parts = data.split("|");
						if(parts[0] == "success"){
							var windowLoca =  window.location.href.replace("#", "");
							var partsLocation = windowLoca.split('publications');
							
							if(partsLocation.length > 1){
								var url = windowLoca;
							}else{
								var url = partsLocation[0] + '/publications';
							}
							window.location.href = url;
						}else{
							alert(parts[1]);
						}
						
						$(".ecommerce-publication-loading").css("display","none");
						
	    			}
		   		});
			}
			
		});	
	});
	$('.remove_product_magento').each(function(){
		$(this).click(function(){
			$(".ecommerce-publication-loading").css("display","inline");
			var sku = $(this).attr('sku');
			var productId = $(this).attr('product_id');
			
			if(productId != ""){
				if(confirm("Confirma a exclusão do produto: "+productId)){
		    		$.ajax({
		    			type: "POST",
		    			async: "false",
		    			url: home_uri+"/Modules/Onbi/Webservice/Products.php",
		    			data: {action:'remove_product_magento', product_id:productId, store_id:storeId, sku:sku},
		    			success: function(data){
		    				if(data == 'success'){
		    					$("#"+productId).remove();
		    					$("#share-ecommerce").attr('disabled', false);
		    					alert("Anúncio finalizado com sucesso!");
		    					var windowLoca =  window.location.href.replace("#", "");
								var partsLocation = windowLoca.split('publications');
								
								if(partsLocation.length > 1){
									var url = windowLoca;
								}else{
									var url = partsLocation[0] + '/publications';
								}
								window.location.href = url;
		    				}else{
		    					alert("Erro ao finalizar anúncio!"+data);
		    				}
		    				$(".ecommerce-publication-loading").css("display","none");
		    			}
		    		})
				}
			}else{
				alert("Produto sem SKU");
			}
			
		})
	})
//	$('input[type="checkbox"].flat-red').each(function(){
//		$(this).iCheck('check', function(){
//	
//		  alert('Well done, Sir');
//		});
//	})
	
	$('.onbi_attribute_relationship').each(function(){
		$(this).change(function(){
			
			var attributeId = $(this).attr('attribute_id');
			var attributeRelationship = $(this).val();

			$.ajax({
				type: "POST",
				async: "true",
				url: home_uri+"/Modules/Onbi/Webservice/Attributes.php",
				data: {action:'update_attribute_relationship', attribute_id:attributeId, store_id:storeId, attribute_relationship:attributeRelationship},
				success: function(data){
					console.log(data);

				}
			});
			
		})
	})
	$('.onbi_import_values').each(function(){
		$(this).on('ifChanged', function(event){
			
			var attributeId = $(this).attr('attribute_id');
			
			var importValue = '';
			if($(this).is(":checked")){
				importValue = 1;
				//importa values
			}else{
				importValue = 0;
				//nao importa values
			}
//			
			$.ajax({
				type: "POST",
				async: "true",
				url: home_uri+"/Modules/Onbi/Webservice/Attributes.php",
				data: {action:'update_import_value', attribute_id:attributeId, store_id:storeId, import_value:importValue},
				success: function(data){
					console.log(data);

				}
			});
			
		})
		
	})
	
	$('.onbi_categories_relationhsip').each(function(){
		$(this).change(function(){
			
			categoryIds = $(this).val();
			onbiCategoryId = $(this).attr('onbi_category_id');
			
			if( categoryIds != "select"){
				
				categoryIds = categoryIds.split("|");
				
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Onbi/Webservice/Categories.php",
					data: {action:'add_category_relationship', parent_id:categoryIds[0], category_id:categoryIds[1], onbi_category_id:onbiCategoryId, store_id:storeId},
					success: function(data){
						
						console.log(data);
						
					}
				});
			}
		});
	});
	
	$('.import_attribute_set_onbi').click(function(){
		$(".attributes-set-relationship").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Onbi/Webservice/Attributes.php",
			data: {action:'import_attribute_set_onbi', store_id:storeId},
			success: function(data){
				if(data == 'success'){
//					location.reload();
				}else{
					$(".attributes-set-relationship").css("display","none");
					console.log(data);
				}
				
			}
		});
		
	});
	
	$('.export_attribute_onbi').click(function(){
		$(".attributes-set-relationship").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Onbi/Webservice/Attributes.php",
			data: {action:'export_attribute_onbi', store_id:storeId},
			success: function(data){
				if(data == 'success'){
					location.reload();
				}else{
					$(".attributes-set-relationship").css("display","none");
					console.log(data);
				}
				
			}
		});
		
	});
	
	$('.add_update_attribute_onbi').click(function(){
		$(".attributes-onbi").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Onbi/Webservice/Attributes.php",
			data: {action:'add_update_attributes_onbi', store_id:storeId},
			success: function(data){
				if(data == 'success'){
					location.reload();
				}else{
					$(".attributes-onbi").css("display","none");
					console.log(data);
				}
				
			}
		});
		
	});
	$('.import_product_attributes').click(function(){
		$(".attributes-onbi").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Onbi/Webservice/Attributes.php",
			data: {action:'import_product_attributes', store_id:storeId},
			success: function(data){
				if(data == 'success'){
					location.reload();
				}else{
					$(".attributes-onbi").css("display","none");
					console.log(data);
				}
				
			}
		});
		
	});
	
	$('.set_attr_relationship_ecommerce').each(function(){
		$(this).change(function(){
			var select = $(this);
			var setAttributeIdOnbi = select.val();
			var setAttributeId = select.attr("set_attribute_id");
			var setAttributeName = select.find('option:selected').text();
			
			if( setAttributeIdOnbi != "select"){
				$(".attributes-set-relationship").css("display","inline");
				
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Onbi/Webservice/Attributes.php",
					data: {action:'set_attr_relationship_ecommerce', set_attribute_id_onbi:setAttributeIdOnbi, set_attribute_id:setAttributeId, set_attribute_name:setAttributeName, store_id:storeId},
					success: function(data){
						
						parts = data.split("|");
						
						if(parts[0] == 'success'){
							console.log(parts);
							select.html("<option value='"+parts[1]+"' selected>"+parts[2]+"</option>");
							$(".attributes-set-relationship").css("display","none");
							$("#"+setAttributeId).prop("disabled", false);
							
						}
						
					}
				});
			}
		});
	});
	
	$('.set_variation_label_relationship_ecommerce').each(function(){
		$(this).change(function(){
			var select = $(this);
			var variationLabel = select.val();
			var setAttributeId = select.attr("set_attribute_id");
			
			if( variationLabel != "select"){
				$(".attributes-set-relationship").css("display","inline");
				
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Onbi/Webservice/Attributes.php",
					data: {action:'set_variation_label_relationship_ecommerce', variation_label:variationLabel, set_attribute_id:setAttributeId, store_id:storeId},
					success: function(data){
						
						parts = data.split("|");
						
						if(parts[0] == 'success'){
							
							
						
						}
						$(".attributes-set-relationship").css("display","none");
						
					}
				});
			}
		});
	});
	
	$('.import_categories_ecommerce').click(function(){
		$(".categories-onbi").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Onbi/Webservice/Categories.php",
			data: {action:'import_categories_hierarchy', store_id:storeId},
			success: function(data){
				console.log(data);
				$(".categories-onbi").css("display","none");
				location.reload();
			}
		});
		
	});
	
	$('.remove_attribute_magento').each(function(){
		$(this).click(function(){
			$(".attributes-onbi").css("display","inline");
			var attributeId = $(this).attr('attribute_id');
			
			if(attributeId != ""){
				if(confirm("Confirma a exclusão do attributo: "+attributeId)){
		    		$.ajax({
		    			type: "POST",
		    			async: "false",
		    			url: home_uri+"/Modules/Onbi/Webservice/Attributes.php",
		    			data: {action:'remove_attribute_magento', attribute_id:attributeId, store_id:storeId},
		    			success: function(data){
		    				console.log(data);
		    				if(data == 'success'){
		    					$("#"+attributeId).remove();
		    				}else{
		    					alert("Erro ao remover attributo!"+data);
		    				}
		    				$(".attributes-onbi").css("display","none");
		    			}
		    		})
				}
			}else{
				alert("Attributo sem Id");
			}
			
		})
	})
	
	
})