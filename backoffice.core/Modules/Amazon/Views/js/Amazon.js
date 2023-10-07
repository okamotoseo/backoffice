function getCategoriesAz(category_id, idPath) {
	id = $("#"+idPath).parent().attr('ind');
	categoryEcommerce = $("#"+idPath).attr('category');
	$.ajax({
		type: "POST",
		async: "true",
		url:home_uri+"/Modules/Amazon/Webservice/ProductType.php",
		data: {action:'list_choice', category:categoryEcommerce, xsd:category_id, store_id:storeId},
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

function removeCategeryRelationship(relationship, category){
	
	const relationshipId = relationship;
	const categoryId = category;
	$.ajax({
		type: "POST",
		async: "true",
		url:home_uri+"/Modules/Amazon/Webservice/ProductType.php",
		data: {action:'remove_relationhsip', relationship_id:relationshipId, category_id:categoryId, store_id:storeId},
		success: function(data){
			var parts = data.split("|");
			if(parts[0] == 'success'){
				$("#"+categoryId).css("display","inline");
				$("."+categoryId).css("display","none");
				$("#"+categoryId).parent().next().html('');
				$("#"+categoryId).parent().next().next().find('.linkAttr').html('');
				$("#"+categoryId).parent().next().next().find('.linkXsd').html('');
				
			}
			
			if(parts[0] == 'error'){
				$(".message").html(parts[1]);
			}

		}
	});
	
	
	
}
$(document).ready(function(){
	
	
	$('.select_all_amazon_products').on('ifClicked', function(event){
		checked = $(".select_one_amazon_products:checked").length;
		if (checked == 0) {
			$('.select_one_amazon_products').iCheck('check');
		  } else {
			  $('.select_one_amazon_products').iCheck('uncheck');
		  }
	});	
	
	$('.connection').each(function(){
		
		$(this).change(function(){
			
			$(".amazon-products").css("display","inline");
			
			var productId = $(this).attr("product_id");
			var connection = $(this).val();
			
			$.ajax({
				type:"POST",
				async:"true",
				url: home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
				data: {action:"update_connection_product_feed", connection:connection, store_id:storeId, product_id:productId, user:userName},
				success: function(data){
					parts = data.split("|");
					
					if(parts[0] == "success"){
						
						switch(parts[2]){
							case 'match': 
								$('#badge-'+productId).html("<span class='text-green'><i class='fa fa-check pull-right'></i></span>");
								break; 
							case 'not_match': 
								$('#badge-'+productId).html("<span class='text-red'><i class='fa fa-ban pull-right'></i></span>");
								break;
							default : 
								$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
								break;
						}
						
						
						$(".amazon-products").css("display","none");
					}else{
						console.log(data);
						$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
						$(".amazon-products").css("display","none");
					}
					
				}
			
			});
			
		});
		
	});
	

	$('#select_action_amazon_products').change(function(){
		var productId = [];
		var action = $('#select_action_amazon_products').val();
		if(action != 'select'){
			$(".amazon-products").css("display","inline");
			
			$('.select_one_amazon_products').each(function(){
				if( $(this).is(':checked') ){
					 productId.push($(this).attr('id'));
				}
			})
			var uri = '';
			var jsonData = '';
			switch(action){
				
				case "update_products_amazon":
					uri = home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
					 jsonData = {action:"add_products_feed", store_id:storeId, product_id:productId, user:userName};
					break;
				case "delete_amazon_product":
					 uri = home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
					 jsonData = {action:"delete_amazon_product", store_id:storeId,  product_id:productId, user:userName};
					break;
			}
			
			$.ajax({type:"POST",async:"false",url:uri,data: jsonData,success: function(data){
					$(".amazon-products").css("display","none");	
					parts = data.split("|");
					if(parts[0] == "success"){
//						location.reload();
						console.log(data);
					}else{
						$(".amazon-products").css("display","none");
					}
					
				}
			});
		}
		
	});
	
	$(".select2attrAmazon").select2({
		  theme: "classic"
	}).each(function(){
		
		$(this).on("select2:select", function (e) {
			
	    var selected_element = $(e.currentTarget);
	    var select_val = selected_element.val();
	    var xsdName = selected_element.attr('xsd_name');
	    var choice = selected_element.attr('choice');
	    var parts = select_val.split("|");
	    var azAttribute = selected_element.attr('az_attribute');
	    var azAttributeType = selected_element.attr('az_attribute_type');
	    var type = 'add-update';
	    if(parts[0] == 'remove'){
	    	type = 'remove';
	    }
	    
	    
	    $.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
			data: { action:"add_attribute_relationship", 
				store_id:storeId, 
				type:type, 
				xsd_name:xsdName, 
				choice:choice, 
				attribute_id:parts[0], 
				attribute:parts[1], 
				az_attribute:azAttribute,
				az_attribute_type:azAttributeType
			},
			success: function(data){
				
				console.log(data);
				
				parts = data.split("|");
				
				if(parts[0] == "success"){
					if(type == 'remove'){
						selected_element.parent().prev().html('');
					}else{
						selected_element.parent().prev().html(parts[1]);
					}
				}
				
				
			}
		});
	});
		
});
	
	
	
	
	$('.action_amazon_product').each(function(){
		
		$(this).click(function(){
			
			$(".amazon-products").css("display","inline");
			var product_id = $(this).attr("id");
			var parent_id = $(this).attr("parent_id");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
					
				case "delete_amazon_product":
					 uri = home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
					 jsonData = {action:"delete_amazon_product", store_id:storeId,  product_id:product_id, user:userName};
					break;
				case "update_products_amazon":
					 uri = home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
					 jsonData = {action:"add_products_feed", store_id:storeId, type:"update", status:"enabled", product_id:product_id, user:userName};
					break;
				case "disable_product_amazon":
					 uri = home_uri+"/Modules/Amazon/Webservice/Products.php",
					 jsonData = {action:"update_status_disabled_product", store_id:storeId, type:"update", status:"disabled", product_id:product_id, user:userName};
					break;
				case "enable_product_amazon":
					 uri = home_uri+"/Modules/Amazon/Webservice/Products.php",
					 jsonData = {action:"update_status_enabled_product", store_id:storeId, type:"update", status:"enabled", product_id:product_id, user:userName};
					break;
					
				case "send_products_amazon":
					 uri = home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
					 jsonData = {action:"add_products_feed", store_id:storeId,  type:"insert", product_id:product_id, user:userName};
					break;
			}
			
			$.ajax({type:"POST",async:"true",url:uri,data: jsonData,success: function(data){
				
					var parts = data.split("|");
//					console.log(parts);
					
						switch(action){
							case "delete_amazon_product":
								if(parts[0] == 'error'){
									alert(parts[1]);
									console.log(data);
								}else{
//									console.log(data);
									var windowLoca =  window.location.href.replace("#", "");
									var partsLocation = windowLoca.split('publications');
									
									if(partsLocation.length > 1){
										window.location.href = windowLoca;
									}else{
										window.location.href = partsLocation[0] + '/publications';
									}
								
								}
							
							break;
							
							case "send_products_amazon": 
								 
								if(parts[0] == 'error'){
									alert(parts[1]);
									console.log(data);
								}else{
									var windowLoca =  window.location.href.replace("#", "");
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
									$('.message-amazon').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>"); 
								}
							
							
							break;
								
						}
						
						
					$(".amazon-products").css("display","none");
					
				}
			
			});
			
			
			
		});
		
	});
	
	
	$(".category" ).each(function(){
		$(this).change(function() {
			if($(".category" ).is(':focus')){
				var category = $(this).val();
				var categoryId = $(this).attr("id");
				var optSelcted = $(this).find('option:selected');
				var hierarchy = optSelcted.attr("hierarchy")
				var name = optSelcted.html()
				var xsd = optSelcted.attr("xsd"); 
			    var choice = optSelcted.attr("choice"); 
			    var categoryEcommerce = $(this).attr("category");
				var select = $(this);
				if(category != 'select'){
					switch(optSelcted){
						default:
							uri = home_uri+"/Modules/Amazon/Webservice/Category.php";
							jsonData = {action:'categories', category:name, relational:categoryEcommerce, hierarchy:hierarchy, store_id:storeId, xsd:xsd, choice:choice, category_id:categoryId};
							break;
					}
					$.ajax({
						type: "POST",
						async: "true",
						url: uri,
						data: jsonData,
						success: function(data){
							console.log(data);
							parts = data.split("|");
							res = parts[0].trim();
							if(res == "next"){
								select.parent().next().append(parts[1]);
								select.empty().append(parts[2]).focusout();
								select.parent().prev().prev().html(parts[3]);
							}
							if(res == "end"){
								select.parent().next().html(parts[1]);
								select.empty().append(parts[2]);
								select.css("display","none");
								select.before(parts[3]);
								select.parent().next().next().find('.linkAttr').html(parts[4]);
								select.parent().next().next().find('.linkXsd').html(parts[5]);
							}
						}
					});
				}
			}
		});
	});
	
//	$(".category" ).each(function(){
//		$(this).change(function() {
//			if($(".category" ).is(':focus')){
//				var category = $(this).val();
//				var categoryId = $(this).attr("id");
//				var optSelcted = $(this).find('option:selected');
//			    var type = optSelcted.attr("type"); 
//			    var xsd = optSelcted.attr("xsd"); 
//			    var choice = optSelcted.attr("choice"); 
//			    var setAttribute = optSelcted.attr("set_attribute");
//				var categoryEcommerce = $(this).attr("category");
//				var select = $(this);
//				if(category != 'select'){
//					switch(type){
//						case 'complexType':
//							uri = home_uri+"/Modules/Amazon/Webservice/ProductType.php";
//							jsonData = {action:'list_choice', category:categoryEcommerce, choice:choice, store_id:storeId, set_attribute:setAttribute, type:type, xsd:xsd, category_id:categoryId} ;
//							break;
//						case "simpleType":
//							uri = home_uri+"/Modules/Amazon/Webservice/ProductType.php";
//							jsonData = {action:'list_choice', category:categoryEcommerce, choice:choice, store_id:storeId, set_attribute:setAttribute, type:type, xsd:xsd, category_id:categoryId} ;
//							break;
//					}
//					$.ajax({
//						type: "POST",
//						async: "true",
//						url: uri,
//						data: jsonData,
//						success: function(data){
//							parts = data.split("|");
//							res = parts[0].trim();
//							if(res == "next"){
//								select.parent().next().html(parts[1]);
//								select.empty().append(parts[2]).focusout();
//								select.parent().prev().prev().html(parts[3]);
//							}
//							if(res == "end"){
//								select.parent().next().html(parts[1]);
//								select.empty().append(parts[2]);
//								select.css("display","none");
//								select.before(parts[3]);
//								select.parent().next().next().find('.linkAttr').html(parts[4]);
//								select.parent().next().next().find('.linkXsd').html(parts[5]);
//							}
//						}
//					});
//				}
//			}
//		});
//	});
	
	$('.result_modal').each(function(){
		$(this).click(function(){
			var id = $(this).attr('id');
			var FeedSubmissionId = $(this).attr('FeedSubmissionId');
			var file = $(this).attr('file');
			$('#FeedSubmissionId').html(FeedSubmissionId);
			$('#id').html(id);
			$('#file').load(file);
		})
	})
	
	$('#add_all_available_products').click(function(){
		$(".amazon-products").css("display","inline");
		$.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
			data: {action:"add_all_available_products", store_id:storeId, user:userName },
			success: function(data){
				parts = data.split("|");
				if(parts[0] == "success"){
//					location.reload();
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					$(".amazon-products").css("display","none");
				}else{
					console.log(data);
					$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
					$(".amazon-products").css("display","none");
				}
				
			}
		});
		
	});
	
	$('#match_products').click(function(){
		$(".amazon-products").css("display","inline");
		$.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Amazon/Webservice/MatchProducts.php",
			data: {action:"get_matching_products", store_id:storeId, user:userName },
			success: function(data){
				parts = data.split("|");
				if(parts[0] == "success"){
//					location.reload();
					$('.message').html("<div class='callout callout-success'><h4>"+parts[1]+"</h4></div>");
					$(".amazon-products").css("display","none");
				}else{
					console.log(data);
					$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
					$(".amazon-products").css("display","none");
				}
				
			}
		});
		
	});
	$('#unmatch_products').click(function(){
		$(".amazon-products").css("display","inline");
		if(confirm('Deseja realmente limpar todas combinações?')){
			$.ajax({
				type:"POST",
				async:"true",
				url:home_uri+"/Modules/Amazon/Webservice/MatchProducts.php",
				data: {action:"unmatch_products", store_id:storeId, user:userName },
				success: function(data){
					parts = data.split("|");
					if(parts[0] == "success"){
						location.reload();
					}else{
						console.log(data);
						$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
						$(".amazon-products").css("display","none");
					}
					
				}
			});
		}else{
			$(".amazon-products").css("display","none");
		}
		
	});
	
	$('#unmatch_products_not_published').click(function(){
		$(".amazon-products").css("display","inline");
		if(confirm('Deseja realmente limpar todas combinações não publicadas em outros canais?')){
			$.ajax({
				type:"POST",
				async:"true",
				url:home_uri+"/Modules/Amazon/Webservice/MatchProducts.php",
				data: {action:"unmatch_products_not_published", store_id:storeId, user:userName },
				success: function(data){
					parts = data.split("|");
					if(parts[0] == "success"){
						location.reload();
					}else{
						console.log(data);
						$('.message').html("<div class='callout callout-danger'><h4>"+parts[1]+"</h4></div>");
						$(".amazon-products").css("display","none");
					}
					
				}
			});
		}else{
			$(".amazon-products").css("display","none");
		}
		
	});
	
	
	
	$('#submit_products_feed').click(function(){
		$(".amazon-feed-loading").css("display","inline");
		$.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
			data: {action:"submit_feed_product", store_id:storeId },
			success: function(data){
				parts = data.split("|");
				if(parts[0] == "success"){
					location.reload();
				}else{
					$(".amazon-feed-loading").css("display","none");
				}
				
			}
		});
		
	});
	$('#submit_inventory_feed').click(function(){
		$(".amazon-feed-loading").css("display","inline");
		$.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
			data: {action:"submit_feed_inventory", store_id:storeId },
			success: function(data){
				parts = data.split("|");
				if(parts[0] == "success"){
					location.reload();
				}else{
					$(".amazon-feed-loading").css("display","none");
				}
				
			}
		});
		
	});
	$('#submit_price_feed').click(function(){
		$(".amazon-feed-loading").css("display","inline");
		$.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
			data: {action:"submit_feed_price", store_id:storeId },
			success: function(data){
				parts = data.split("|");
				if(parts[0] == "success"){
					location.reload();
				}else{
					$(".amazon-feed-loading").css("display","none");
				}
				
			}
		});
		
	});
	$('#submitted_feed').click(function(){
		$(".amazon-feed-loading").css("display","inline");
		$.ajax({
			type:"POST",
			async:"true",
			url:home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
			data: {action:"submitted_feed", store_id:storeId },
			success: function(data){
				parts = data.split("|");
				if(parts[0] == "success"){
					location.reload();
				}else{
					$(".amazon-feed-loading").css("display","none");
				}
				
			}
		});
		
	});
	
	$('.amazon_feed_action').each(function(){
		
		$(this).click(function(){
			
			$(".amazon-feed-loading").css("display","inline");
			
			var id = $(this).attr("id");
			var FeedSubmissionId = $(this).attr("FeedSubmissionId");
			var action = $(this).attr('action');		
			var uri = '';
			var jsonData = '';
			switch(action){
					
				case "get_feed_result":
					 uri = home_uri+"/Modules/Amazon/Webservice/AddProducts.php",
					 jsonData = {action:"results_feed_request", store_id:storeId, id:id, feed_submission_id:FeedSubmissionId};
					break;
					
			}
			
			$.ajax({
				type:"POST",
				async:"false",
				url:uri,
				data: jsonData,
				success: function(data){
			
				$(".amazon-feed-loading").css("display","none");
					parts = data.split("|");
//					for( var i = 0; i < parts.length; i++){
//					
//						switch(action){
//							case "delete_amazon_product": $('#'+parts[i]).remove(); break;
//							case "update_products_amazon": $('#'+parts[i]).remove(); break;
//						}
//					}
					
				}
			
			});
			
		});
		$(".amazon-products").css("display","none");
		
	});
	
})

