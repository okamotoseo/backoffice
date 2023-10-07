
$(document).ready(function(){
	$("#dataUpdate").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
	
	$('.select_all_mg2_products').on('ifClicked', function(event){
		checked = $(".select_one_mg2_products:checked").length;
		if (checked == 0) {
			$('.select_one_mg2_products').iCheck('check');
		  } else {
			  $('.select_one_mg2_products').iCheck('uncheck');
		  }
	});	
	
	
	$('#select_action_mg2_products').change(function(){
		var Ids = [];
		var SKUs = [];
		var action = $('#select_action_mg2_products').val();
		
		if(action != 'select'){
			$(".overlay").css("display","inline");
			
			$('.select_one_mg2_products').each(function(){
				if( $(this).is(':checked') ){
					 Ids.push($(this).attr('product_id'));
					 SKUs.push($(this).attr('sku'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				case "update_products_mg2":
					 uri = home_uri+"/Modules/Onbi/Webservice/AddProducts.php";
					 jsonData = {action:"update_product_mg2", store_id:storeId,  product_id:Ids, sku:SKUs};
					break;
					
				case "remove_products_mg2":
					 uri = home_uri+"/Modules/Magento2/Webservice/Products.php";
					 jsonData = {action:"remove_products_mg2", store_id:storeId, product_id:Ids, user_id:userId, user:userName};
					break;
			}
			$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
					parts = data.split("|");
					if(parts[0] == 'success'){
						location.reload();
					}
					console.log(parts);
					$(".overlay").css("display","none");
					
				}
			});
		}
		
	});
	
	$(".create_product_magento").each(function(){
		$(this).click(function(){
			$(".ecommerce-publication-loading").css("display","inline");
			sku = $(this).attr('sku');
			parentId = $(this).attr('parent_id');
			productId = $(this).attr('product_id');
			
			if(sku != ""){
	    		$.ajax({
	    			type: "POST",
	    			async: "true",
	    			url:home_uri+"/Modules/Magento2/Webservice/AddProducts.php",
	    			data: {
	    				action:"create_product_magento", 
	    				store_id:storeId, 
	    				sku:sku, 
	    				parent_id:parentId, 
	    				product_id:productId
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
	$(".upate_product_magento").each(function(){
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
	    			url:home_uri+"/Modules/Magento2/Webservice/AddProducts.php",
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
	    			url:home_uri+"/Modules/Magento2/Webservice/AddProducts.php",
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
		    			url: home_uri+"/Modules/Magento2/Webservice/Products.php",
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
	
	$('.mg2_attribute_relationship').each(function(){
		$(this).change(function(){
			var select = $(this);
			var attributeId = select.attr('attribute_id');
			var attributeCode = select.attr('attribute_code');
			var attributeSetId = select.attr('attribute_set_id');
			var attributeRelationship = select.val();
			var attributeType = select.find('option:selected').attr('type');

			$.ajax({
				type: "POST",
				async: "true",
				url: home_uri+"/Modules/Magento2/Webservice/Attributes.php",
				data: {action:'update_attribute_relationship', attribute_set_id:attributeSetId, attribute_id:attributeId, attribute_code:attributeCode, store_id:storeId, attribute_relationship:attributeRelationship, attribute_type:attributeType},
				success: function(data){
					parts = data.split("|");
					if(parts[0] == 'success'){
						TrHighline(attributeId);
					}

				}
			});
			
		})
	})
	$('.mg2_import_values').each(function(){
		$(this).on('ifChanged', function(event){
			
			var attributeId = $(this).attr('attribute_id');
			
			var importValue = '';
			if($(this).is(":checked")){
				importValue = 1;
			}else{
				importValue = 0;
			}
			$.ajax({
				type: "POST",
				async: "true",
				url: home_uri+"/Modules/Magento2/Webservice/Attributes.php",
				data: {action:'update_import_value', attribute_id:attributeId, store_id:storeId, import_value:importValue},
				success: function(data){
					console.log(data);

				}
			});
			
		})
		
	})
	
	$('.mg2_attribute_spotlight').each(function(){
		
		var spotlightVal='';
		
		var attributeId = $(this).attr('attribute_id');
		
		$(this).click(function(event){
			
			if($(this).hasClass( "fa-star-o" )){
				
				$(this).removeClass( "fa-star-o" )
				$(this).addClass( "fa-star" );
				spotlightVal = 1;
				
			}else{
				
				$(this).removeClass( "fa-star" )
				$(this).addClass( "fa-star-o" );
				spotlightVal = 0;
			}
			
			
			if(spotlightVal != ''){
				$.ajax({
					type: "POST",
					async: "true",
					url: home_uri+"/Modules/Magento2/Webservice/Attributes.php",
					data: {action:'add_attribute_spotlight', attribute_id:attributeId, store_id:storeId, spotlight:spotlightVal},
					success: function(data){
						parts = data.split("|");
						if(parts[0] == 'success'){
							TrHighline(attributeId);
						}
	
					}
				});
			}
			
		})
		
	})
	
	$('.mg2_categories_relationhsip').each(function(){
		$(this).change(function(){
			
			var mg2CategoryId = $(this).val();
			var categoryIds = $(this).attr('category_id');
			
			if( categoryIds != "select"){
				
				var categoryIds = categoryIds.split("|");
				var mg2CategoryId = mg2CategoryId.split("|");
				
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Magento2/Webservice/Categories.php",
					data: {action:'add_category_relationship', 
						parent_id:categoryIds[0], 
						category_id:categoryIds[1], 
						mg2_parent_id:mg2CategoryId[0], 
						mg2_category_id:mg2CategoryId[1], 
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
	
	$('.import_attribute_set_mg2').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Magento2/Webservice/Attributes.php",
			data: {action:'import_attribute_set_mg2', store_id:storeId},
			success: function(data){
				if(data == 'success'){
//					location.reload();
				}else{
					console.log(data);
				}
				$(".overlay").css("display","none");
			}
		});
		
	});
	
	$('#reset').click(function(){
		$(".overlay").css("display","inline");
		if(confirm("Ao confirmar esta ação você irá excluir permanentemente todos os dados referente ao mapeamento dos conjuntos de atributos, " +
				"mapeamento de atributos e os mapeamentos das categorias do Magento2.... " +
				"Entretanto não serão excluidos, produtos importados e seus atributos e valores cadastrados na Sysplace, " +
				"para isto você deve utilizar a ação de exclusão no seção de produtos disponíveis"+
				"Deseja realmente continuar com o RESET ?")){
			$.ajax({
				type: "POST",
				async: "false",
				url:home_uri+"/Modules/Magento2/Webservice/Products.php",
				data: {action:'reset_import_data_magento2', store_id:storeId, user:userName},
				success: function(data){
					parts = data.split("|");
					console.log(data);
					defaultMessage(parts)
				}
			});
		}else{
			$(".overlay").css("display","none");
			return;
		}
		
	});
	
	
	$('.export_attribute_mg2').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Magento2/Webservice/Attributes.php",
			data: {action:'export_attribute_mg2', store_id:storeId},
			success: function(data){
				if(data == 'success'){
					location.reload();
				}else{
					console.log(data);
				}
				$(".overlay").css("display","none");
				
			}
		});
		
	});
	
	$('.add_update_attribute_mg2').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Magento2/Webservice/Attributes.php",
			data: {action:'add_update_attributes_mg2', store_id:storeId},
			success: function(data){
				if(data == 'success'){
					location.reload();
				}else{
					$(".overlay").css("display","none");
					console.log(data);
				}
				
			}
		});
		
	});
	$('.import_product_attributes').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Magento2/Webservice/Attributes.php",
			data: {action:'import_product_attributes', store_id:storeId},
			success: function(data){
				if(data == 'success'){
					location.reload();
				}else{
					$(".overlay").css("display","none");
					console.log(data);
				}
				
			}
		});
		
	});
	
	$('.set_attr_relationship_ecommerce').each(function(){
		$(this).change(function(){
			var select = $(this);
			var setAttributeIdMg2 = select.val();
			var setAttributeId = select.attr("set_attribute_id");
			var setAttributeName = select.find('option:selected').text();
			
			if( setAttributeIdMg2 != "select"){
				$(".overlay").css("display","inline");
				
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Magento2/Webservice/Attributes.php",
					data: {action:'set_attr_relationship_ecommerce', set_attribute_id_mg2:setAttributeIdMg2, set_attribute_id:setAttributeId, set_attribute_name:setAttributeName, store_id:storeId},
					success: function(data){
						
						parts = data.split("|");
						
						if(parts[0] == 'success'){
							location.reload();
						}else{
							alert(parts[1]);
							$(".overlay").css("display","none");
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
			var variationType = select.find('option:selected').attr("variation_type");
			var setAttributeId = select.attr("set_attribute_id");
			
			if( variationLabel != "select"){
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Magento2/Webservice/Attributes.php",
					data: {action:'set_variation_label_relationship_ecommerce', variation_type:variationType, variation_label:variationLabel, set_attribute_id:setAttributeId, store_id:storeId},
					success: function(data){
						parts = data.split("|");
						if(parts[0] == 'success'){
							TrHighline(parts[1]);
						}else{
							alert(parts[1]);
						}
						
					}
				});
			}
		});
	});
	
	$('.import_categories_ecommerce').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Magento2/Webservice/Categories.php",
			data: {action:'import_categories_hierarchy', store_id:storeId},
			success: function(data){
				console.log(data);
				$(".overlay").css("display","none");
				location.reload();
			}
		});
		
	});
	
	$('.remove_attribute_magento').each(function(){
		$(this).click(function(){
			$(".overlay").css("display","inline");
			var attributeId = $(this).attr('attribute_id');
			
			if(attributeId != ""){
				if(confirm("Confirma a exclusão do attributo: "+attributeId)){
		    		$.ajax({
		    			type: "POST",
		    			async: "false",
		    			url: home_uri+"/Modules/Magento2/Webservice/Attributes.php",
		    			data: {action:'remove_attribute_magento', attribute_id:attributeId, store_id:storeId},
		    			success: function(data){
		    				console.log(data);
		    				if(data == 'success'){
		    					$("#"+attributeId).remove();
		    				}else{
		    					alert("Erro ao remover attributo!"+data);
		    				}
		    				$(".overlay").css("display","none");
		    			}
		    		})
				}
			}else{
				alert("Attributo sem Id");
			}
			
		})
	})
});