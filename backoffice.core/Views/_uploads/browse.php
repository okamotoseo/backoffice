<?php 
$uri = 'https://'.$_SERVER['HTTP_HOST'];
?>
<!DOCTYPE html>
<!-- release v4.5.2, copyright 2014 - 2018 Kartik Visweswaran -->
<!--suppress JSUnresolvedLibraryURL -->
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Sysplace File Upload</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://backoffice.sysplace.com.br/library/bootstrap-fileinput-master/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="https://backoffice.sysplace.com.br/library/bootstrap-fileinput-master/themes/explorer-fa/theme.css" media="all" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://backoffice.sysplace.com.br/library/themes/AdminLTE-2.3.0/bootstrap/css/bootstrap.min.css">
    
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://backoffice.sysplace.com.br/library/bootstrap-fileinput-master/js/plugins/sortable.js" type="text/javascript"></script>
    <script src="https://backoffice.sysplace.com.br/library/bootstrap-fileinput-master/js/fileinput.js" type="text/javascript"></script>
    <script src="https://backoffice.sysplace.com.br/library/bootstrap-fileinput-master/js/locales/fr.js" type="text/javascript"></script>
    <script src="https://backoffice.sysplace.com.br/library/bootstrap-fileinput-master/js/locales/es.js" type="text/javascript"></script>
    <script src="https://backoffice.sysplace.com.br/library/bootstrap-fileinput-master/themes/explorer-fa/theme.js" type="text/javascript"></script>
    <script src="https://backoffice.sysplace.com.br/library/bootstrap-fileinput-master/themes/fa/theme.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" type="text/javascript"></script>
</head>
<body>
<div class="container kv-main">
    <form enctype="multipart/form-data">
		<div class="col-xs-12">
			<div class="file-loading">
			    <input id="input-ke-1" name="file" type="file" multiple>
			</div>
		</div>
    
	    <!-- PRODUCT IMAGE LIST -->
		<div class="col-xs-6">
			<?php 
			$storeId = $_REQUEST['store_id'];
			$parentId = $_REQUEST['parentId'];
			$id = $_REQUEST['id'];
			$pathShow = $uri . "/Views/_uploads/store_id_{$storeId}/products/{$id}";
			$pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$id}";
				if(file_exists($pathRead)){
					$iterator = new DirectoryIterator($pathRead);
					foreach ( $iterator as $key => $entry ) {
						$file = $entry->getFilename();
						if($file != '.' AND $file != '..'){
							 $fileSize = $entry->getSize();
							 echo "<input type='hidden' product_id='{$id}' value='{$pathShow}/{$file}' url='{$pathShow}/'  fileName='{$file}' key='{$file}' width='120px' size='{$fileSize}' class='imgs-path' >";
						}
						    
					}
				}
			?>
			<input type='hidden' id='product_id' store_id='<?php echo $storeId; ?>' value='<?php echo $id; ?>'>
		</div>
	</form>
<hr>
</div>
</body>
<script>
var storeId =  $('#product_id').attr('store_id');
var home_uri = "https://backoffice.sysplace.com.br";
var productId = $('#product_id').val();
var jsonObjPreview = [];
var jsonObjConfig = [];
$(".imgs-path").each(function() {

    var pathImg = $(this).val();

    jsonObjPreview.push(pathImg);
    
    item = {}
    item ["url"] = home_uri+'/Views/_uploads/remove_upload.php?action=remove_image_product&store_id='+storeId+'&product_id='+$(this).attr('product_id');
    item ["caption"] = $(this).attr('fileName');
    item ["key"] = $(this).attr('key');
    item ["width"] = $(this).attr('width');
    item ["size"] = $(this).attr('size');
    jsonObjConfig.push(item);
});

// console.log(item);
// console.log(jsonObjConfig);
// var productId = '3041';
$("#input-ke-1").fileinput({
    theme: "explorer",
    uploadExtraData: {store_id:storeId, product_id:productId},
    uploadUrl: home_uri+'/Views/_uploads/upload.php',
	autoReplace: false,
    allowedFileExtensions: ['jpeg', 'jpg', 'png', 'gif'],
    overwriteInitial: false,
    previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
    initialPreviewAsData: true,
    initialPreview: jsonObjPreview,
    initialPreviewConfig: jsonObjConfig,
    initialPreviewDownloadUrl: home_uri+'/Views/_uploads/store_id_'+storeId+'/products/'+productId+'/{key}' // the key will be dynamically replaced  
}).on('filesorted', function(e, params){
	console.log(params);
	
});
     
</script>
</html>