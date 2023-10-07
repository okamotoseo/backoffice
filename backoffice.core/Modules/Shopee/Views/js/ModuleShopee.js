//function putCategory(id_category, hierarchy) {
//	id = $("#"+idPath).parent().attr('ind');
//	categoryEcommerce = $("#"+idPath).attr('category');
//	$.ajax({
//		type: "POST",
//		async: "true",
//		url:home_uri+"/Modules/Mercadolivre/Webservice/Products.php",
//		data: {action:'get_categories', category:categoryEcommerce, category_id:category_id, store_id:storeId},
//		success: function(data){
//			parts = data.split("|");
//			res = parts[0].trim();
//			if(res == "next"){
//				$("[name='"+id+"']").html(parts[1]).focusout();
//				$("#"+id).empty().append(parts[2]).focusout();
//			}
//			if(res == "end"){
//				$("[name='"+id+"']").html(parts[1]).focusout();
//				$("#"+id).empty().append(parts[2]).focusout();
//			}
//		}
//	});
//}

$(document).ready(function(){
	
	$("#copy-quantity").click(function(){
	    $("#quantity").select();
	    document.execCommand('copy');
	});
	$("#copy-sale_price").click(function(){
	    $("#sale_price").select();
	    document.execCommand('copy');
	});
	
	$("#copy-products").click(function(){
	    $("#export_products").select();
	    document.execCommand('copy');
	});
	$(".shopee_root_categories" ).each(function(){
		$(this).change(function() {
//			if($(".shopee_root_categories" ).is(':focus')){
				rootCategory = $(this).val();
				id = $(this).attr("id");
				if(rootCategory != 'select'){
					$.ajax({
						type: "POST",
						async: "true",
						url:home_uri+"/Modules/Shopee/Webservice/Categories.php",
						data: {action:'get_categories_child', store_id:storeId, id:id, root_category:rootCategory},
						success: function(data){
							parts = data.split("|");
							console.log(parts);
							
							if(parts[0] == 'success'){
								$('#child-'+parts[1]).html(parts[2]);
							}
							
						}
					});
				}
//			}

		});
	});
	
	$(".categories_child" ).each(function(){
		
		$(this).change(function() {
			
			id = $(this).attr("id");
			
			catId = $(this).attr("category_id");
			
			var id_category = $(this).val(); 
			
			var childCategoryText =  $("option:selected", this).html();
			
			if(id_category != 'select'){
				
				$.ajax({
					type: "POST",
					async: "true",
					url:home_uri+"/Modules/Shopee/Webservice/Categories.php",
					data: {
						action:'save_category_relationship', 
						store_id:storeId, 
						category_id:catId,
						id_category:id_category, 
						child_category:childCategoryText
					},
					success: function(data){
						console.log(data);
						parts = data.split("|");
						// console.log(parts);
						TrHighline(parts[1]);
					}
					
				});
				
			}

		});
	});
	
	$(".categories_xml_child" ).each(function(){
		
		$(this).change(function() {
			
			id = $(this).attr("id");
			
			hierarchy = $(this).attr("hierarchy");
			
			catId = $(this).attr("category_id");
			
			var id_category = $(this).val(); 
			
			var childCategoryText =  $("option:selected", this).html();
			
			if(id_category != 'select'){
				
				$.ajax({
					type: "POST",
					async: "true",
					url:home_uri+"/Modules/Shopee/Webservice/Categories.php",
					data: {
						action:'save_category_xml_relationship', 
						store_id:storeId, 
						category_id:catId,
						id_category:id_category, 
						id_hierarchy:hierarchy, 
						child_category:childCategoryText
					},
					success: function(data){
						console.log(data);
						parts = data.split("|");
						// console.log(parts);
						TrHighline(parts[1]);
					}
					
				});
				
			}

		});
	});
	

	
	$('.shopee_categories_relationhsip').each(function(){
		$(this).change(function(){
			
			var shopeeCategoryId = $(this).val();
			var categoryIds = $(this).attr('category_id');
			
			if( categoryIds != "select"){
				
				var categoryIds = categoryIds.split("|");
				var shopeeCategoryId = shopeeCategoryId.split("|");
				
				$.ajax({
					type: "POST",
					async: "false",
					url:home_uri+"/Modules/Shopee/Webservice/Categories.php",
					data: {action:'add_category_relationship', 
						parent_id:categoryIds[0], 
						category_id:categoryIds[1], 
						shopee_parent_id:shopeeCategoryId[0], 
						shopee_category_id:shopeeCategoryId[1], 
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
	

	
})