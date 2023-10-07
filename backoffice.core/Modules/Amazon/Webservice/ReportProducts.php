<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';

require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SYSPlace | Sistema de Integrações com Marketplaces</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/bootstrap/css/bootstrap.min.css" >
    <!-- Font Awesome -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css"/>
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/dist/css/AdminLTE.min.css" >
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/dist/css/skins/_all-skins.min.css" >
    <!-- iCheck -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/iCheck/flat/blue.css" >
    <!-- Morris chart -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/morris/morris.css" >
    <!-- jvectormap -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-1.2.2.css" >
    <!-- Date Picker -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/datepicker/datepicker3.css" >
    <!-- Daterange picker -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/daterangepicker/daterangepicker-bs3.css" >
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="/library/themes/AdminLTE-2.3.0/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" >

    <script src="/library/themes/AdminLTE-2.3.0/plugins/jQuery/jQuery-2.1.4.min.js" ></script>
</head>
<body >
<div id="wrap">
	
<?php 
if(isset($storeId)){
    
    $db = new DbConnection();
    
    switch($action){
    	
    	case 'list_products_prices':
    		echo "<table class='table table-condensed' id='search-default' width='100%'>
    				<thead>
    				<tr>
    					<th>Sku</th>
    					<th>Foto</th>
						<th>Titulo</th>
	    				<th>Marca</th>
    					<th>Estoque</th>
    					<th>Múltiplos</th>
	    				<th>CustoJR</th>
						<th>Margem_JR%</th>
						<th>PrecoVendaJR</th>
						<th>Fraction</th>
						<th>Margen_Fan%</th>
						<th>Fan=JR+20+5</th>
						<th>PrecoAtual</th>
						<th>PrecoFinal</th>
						<th>NewPrice</th>
						<th></th>
    				</tr></thead><tbody>";
    		$sql = "SELECT * FROM az_products_feed WHERE store_id = {$storeId} AND connection LIKE 'match'";
    		$query = $db->query($sql);
    		$fetch = $query->fetchAll(PDO::FETCH_ASSOC);
    		$ids = array();
    		foreach($fetch as $k => $product){
    			
    			$publications = getPublicationsBySku($db, $storeId, $product['sku']);
    			if(empty($publications)){
	    			$sqlVerify = "SELECT id, thumbnail, title, cost, price, sale_price, quantity, extra_information FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$product['sku']}'";
	    			$verifyQuery = $db->query($sqlVerify);
	    			$verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	    			
	    			$priceRegistered = $verify['sale_price'] > $verify['price'] ?  $verify['sale_price'] : $verify['price'] ;
	    			
	    			$cost =  $verify['cost'] == 0 ? 1 : $verify['cost'] ;
	    			$price = $verify['price'];
	    			$dif =  $price - $cost ;
	    			$fraction = explode('/', $verify['extra_information']);
	    			sort($fraction);
	    			$multiply = !empty($fraction[0]) ? $fraction[0] : 1 ;
	    			$price = $price * $multiply;
	    			$fractionQtd = $multiply;
	    			
	    			
	    			$percentUnit = $dif /  $cost;
	    			
	    			$salePrice = ($price * 1.2);
	    			if($salePrice < 94){
	    				$salePrice += 5.00;
	    			}
	    			$salePrice = ceil($salePrice) -0.01;
	    			
	    			$priceFraction = ($price/$multiply) * 1.2;
	    			if($priceFraction < 94){
	    				$priceFraction += 5.00;
	    				
	    			}
	    			if($fractionQtd > 1 OR $fractionQtd == 0){
	    				$fractionQtd = 1;
    				}
	    			$priceFraction = ceil($priceFraction) -0.01;
	    			
	    			$difFan =  $priceFraction - ($price/$multiply);
	    			$percentUnitFan = $difFan /  $priceFraction;
	    			
	    			$finalPrice = $priceFraction > $priceRegistered ? $priceFraction : $priceRegistered ;
	    			
	    			$icon = '';
// 	    			if($finalPrice == $product['az_Amount']){
// 	    				$icon = "<i class='fa fa-check-circle'></i>";
// 	    			}else{
// 		    			if($finalPrice > $product['az_Amount']){
// 		    				$icon = "<i class='fa fa-arrow-circle-up text-red'></i>";
// 		    			}else{
// 		    				$icon = "<i class='fa fa-arrow-circle-down text-green'></i>";
// 		    			}
// 	    			}
	    			
	    			
	    			
	    			$test = '';
// 	    			if($priceFraction == $priceRegistered){
// 	    				$test = "<i class='fa fa-check-circle'></i>";
// 	    			}else{
// 		    			if($priceFraction > $priceRegistered){
// 		    				$test = "<i class='fa fa-arrow-circle-up text-green'></i>";
		    				
// 		    			}else{
// 		    				$test = "<i class='fa fa-arrow-circle-down text-red'></i>";
		    				
// 		    			}
// 	    			}
	    			
	    			
	    			$background = $multiply > 1 ? "style='background-color:#fff0f0;' " : '' ;
	    			echo "<tr id='{$product['product_id']}' {$background}>
	    					<td><a href='/Products/Product/{$verify['id']}/'>{$product['sku']}</a></td>
	    					<td><img src='{$product['az_SmallImage']}' width='80px' height='50px'></td>
	    					<td width='50%'><a href='https://www.amazon.com.br/dp/{$product['az_ASIN']}' target='_blank'>{$product['title']}</a><br>";
	    						$selected = $new = $match = $notMatch  = '';
									switch($product['connection']){
									    case "new": $new = "selected"; break;
									    case "match": $match = "selected"; break;
									    case "not_match": $notMatch = "selected"; break;
									    default : $selected = 'selected'; break;
									}
								
	    						echo "<select id='connection' name='connection' class='select2 connection' store_id='{$product['store_id']}' product_id='{$product['product_id']}' >
										<option value=''  {$selected} >Todos</option>
										<option value='new'  {$new} >New</option>
										<option value='match'  {$match} >Match</option>
										<option value='not_match'  {$notMatch} >Not Match</option>
								</select>";
								
							
	    					echo "<br>{$publications}</td>
	    					<td>{$product['az_Brand']}</td>
	    					<td>{$verify['quantity']}</td>
	    					<td>".json_encode($fraction)."</td>
	    					<td>{$cost}</td>
	    					<td>".number_format($percentUnit * 100, 2)."% </td>
	    					<td>{$price}</td>
	    					<td>{$fractionQtd}</td>
	    					<td>".number_format($percentUnitFan * 100, 2)."% </td>
	    					<td>{$priceFraction} {$test}</td>
	    					<td>{$priceRegistered}</td>
	    					<td><span data-toggle='title' title='Preço Final Calculado'>FL:{$finalPrice} {$icon}</span><br><span data-toggle='title' title='Preço Amazon'>Az:{$product['az_Amount']}</span></td>
	    					<td><input type='text' class='new_price product-id-{$product['product_id']}' id='product-id-{$product['product_id']}' value='0.00'></td>
	    					<td><button class='btn btn-xs save-new-price' product_id='{$product['product_id']}' store_id='{$product['store_id']}' >Salvar</button></td>
	    					</tr>";
	    					
// 					$queryAp = $db->update('available_products', 
// 							array('store_id', 'id'), 
// 							array($storeId, $verify['id']),
// 							array('sale_price' => $finalPrice)
// 						);
// 					pre($queryAp);die;
    			}
    			 
    			
    		}
    		echo "</tbody></table>";
    		break;
    }
    
    
}

?>
</div>
 <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.5 -->
    <script src="/library/themes/AdminLTE-2.3.0/bootstrap/js/bootstrap.min.js" ></script>
    <!-- Morris.js charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="/library/themes/AdminLTE-2.3.0/plugins/morris/morris.min.js" ></script>
    <!-- Sparkline -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/sparkline/jquery.sparkline.min.js" ></script>
    <!-- jvectormap -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" ></script>
    <script src="/library/themes/AdminLTE-2.3.0/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" ></script>
    <!-- jQuery Knob Chart -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/knob/jquery.knob.js" ></script>
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="/library/themes/AdminLTE-2.3.0/plugins/daterangepicker/daterangepicker.js" ></script>
    <!-- datepicker -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/datepicker/bootstrap-datepicker.js" ></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" ></script>
    <!-- DataTables -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/datatables/jquery.dataTables.min.js" ></script>
    <script src="/library/themes/AdminLTE-2.3.0/plugins/datatables/dataTables.bootstrap.min.js" ></script>
     <!-- Select2 -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/select2/select2.full.min.js"></script>
    <!-- Slimscroll -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/slimScroll/jquery.slimscroll.min.js" ></script>
    <!-- FastClick -->
    <script src="/library/themes/AdminLTE-2.3.0/plugins/fastclick/fastclick.min.js" ></script>
    <!-- AdminLTE App -->
    <script src="/library/themes/AdminLTE-2.3.0/dist/js/app.min.js" ></script>
    
    <script>
    var home_uri = 'https://backoffice.sysplace.com.br';
    var storeId = '4';
    var userName = 'System';
$('.connection').each(function(){
		
		$(this).change(function(){
			
			$(".amazon-products").css("display","inline");
			var storeId = $(this).attr("store_id");
			var productId = $(this).attr("product_id");
			var connection = $(this).val();
			
			$.ajax({
				type:"POST",
				async:"true",
				url: "/Modules/Amazon/Webservice/AddProducts.php",
				data: {action:"update_connection_product_feed", connection:connection, store_id:storeId, product_id:productId, user:userName},
				success: function(data){
					parts = data.split("|");
					
					if(parts[0] == "success"){
						
						TrHighlineReport(productId)
					}
					
				}
			
			});
			
		});
		
	});
    var table = $("#search-default").DataTable({

        order: [[4, 'desc']],
        paging: false,
        info: true,
        searching: true,
        "displayLength": 2000,
		"scrollX": false,
        "language": {
            "lengthMenu": "Visualizar _MENU_ registros",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro encontrado",
            "infoFiltered": "(Localizou _MAX_ registros)",
            "loadingRecords": "Carregando...",
            "processing":     "Processando...",
            "search":         "Procurar:",
            "paginate": {
                "first":      "Primeira",
                "last":       "Última",
                "next":       "Próxima",
                "previous":   "Anterior"
            },
        }
       
    });

	$( ".save-new-price" ).each(function() {
		$(this).click(function(){

			var productId = $(this).attr("product_id");
			var storeId = $(this).attr("store_id");
			var newPrice = $('#product-id-'+productId).val();
			
// 			if(newPrice > 0){
				$.ajax({
					type: "POST",
					async: "true",
					url: "/Webservice/AppProducts.php",
					data: {action:"update_price", product_id:productId, store_id:storeId, new_price:newPrice},
					success: function(data){
						record = data.split("|");
						if(record[0] == "success"){
							TrHighlineReport(productId);
						}
					}
					
				});
// 			}
			
		
		})
		
	});

	function TrHighlineReport(idTr){
		$("#"+idTr).css({ backgroundColor: "c4c4c4" }).show().fadeIn();
		 setTimeout(function() {
	         $( "#"+idTr ).removeAttr( "style" ).hide().fadeIn();
	       }, 100 );
	}
    </script>
    <script src='../Views/js/Amazon.js' language='javascript'></script>
  </body>
</html>