<?php
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Models/Products/ProductsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;


if(isset($storeId)){
    
    $db = new DbConnection();
    
    switch($action){
       
        case "export_products_tray" :
            
            $productsModel = new ProductsModel($db, null, $storeId);
            
            if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
                foreach ( $_POST as $property => $value ) {
                    if($value != ''){
                        if(property_exists($productsModel,$property)){
                            $productsModel->{$property} = $value;
                            
                        }
                    }
                    
                }
            }
            $products = $productsModel->GetProductsNoLimits();
            $fileName = "export_products_tray.csv";
			$fp = fopen("../Views/Report/{$fileName}", 'a+');
			ftruncate($fp, 0);
			fwrite($fp, "Id;IdTray;Parent;Marca;Título;Ref;Coleção;Status;Fotos;Qtd;Criado;Atualizado;\n");
			
			foreach($products as $k => $fetch){
			    $status = $fetch['available'] > 0 ? "Publicado" : "Pendente";
			    $created = dateTimeBr($fetch['created']);
			    $updated = dateTimeBr($fetch['updated']);
			    $csvRow = "{$fetch['product_id']};{$fetch['id_product']};{$fetch['parent_id']};{$fetch['brand']};{$fetch['title']};{$fetch['reference']};{$fetch['collection']};{$status};{$fetch['images']};{$fetch['stock']};{$created};{$updated}\n";
			    fwrite($fp, $csvRow);
			}
			if(fclose($fp)){
			     echo "success|{$fileName}";
			}else{
			    echo "error|Erro ao gerar arquivo csv do relatório";
			}
			break;
            
    }
}