
$(document).ready(function(){
	$("#dataUpdate").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
	
	$('#copy_google_xml').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Google/Webservice/CopyXml.php",
			data: {action:'copy_xml', store_id:storeId},
			success: function(data){
				console.log(data);
				$(".overlay").css("display","none");
//				location.reload();
			}
		});
		
	});
	
	
	$('#import_google_xml_products').click(function(){
		$(".overlay").css("display","inline");
		$.ajax({
			type: "POST",
			async: "false",
			url:home_uri+"/Modules/Google/Webservice/ImportXml.php",
			data: {action:'import_xml', store_id:storeId},
			success: function(data){
				console.log(data);
				$(".overlay").css("display","none");
//				location.reload();
			}
		});
		
	});
	
	
	
	    
	
})