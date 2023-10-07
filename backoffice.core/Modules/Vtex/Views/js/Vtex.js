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
	
	$(".category" ).each(function(){
		
		$(this).change(function() {
			
			idCategory = $(this).val();
			
			category = $(this).attr("category");
			
			idTr = $(this).attr("rowLine");
			
			categoryVtex =  $("option:selected", this).text();
			
			var select = $(this);
			
			if(idCategory != 'select'){
				$.ajax({
					type: "POST",
					async: "true",
					url:home_uri+"/Modules/Vtex/Webservice/Webservice.php",
					data: {action:'add_category_relationship', store_id:storeId, hierarchy:category, id_category:idCategory, category_vtex:categoryVtex},
					success: function(data){
						parts = data.split("|");
						if(parts[0] == 'success'){
							TrHighline(idTr);
						}
	
					}
				});
			}
		});
	});


});