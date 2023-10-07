<?php
//https://api.mercadolibre.com/sites/MLB/categories
// ini_set('max_execution_time', 86400);
// ini_set ("display_errors", true);
header("Access-Control-Allow-Origin: *");
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/../Models/Products/AvailableProductsModel.php';
require_once $path .'/../Models/Customers/ManageCustomersModel.php';
require_once $path .'/functions.php';


$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$marketplace = isset($_REQUEST["Marketplace"]) && $_REQUEST["Marketplace"] != "" ? intval($_REQUEST["Marketplace"]) : null ;

if (empty ( $action ) and empty ( $storeId )) {
    $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
    $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
    $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
}
if(isset($storeId)){
    
    $db = new DbConnection();
    
    switch($action){
    
    
        case "export_available_products" :
            
            $availableProductModel = new AvailableProductsModel($db);
            $availableProductModel->store_id = $storeId;
            $availableProductModel->records = 'no_limit';
            if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
                foreach ( $_POST as $property => $value ) {
                    if($value != ''){
                        if(property_exists($availableProductModel,$property)){
                            $availableProductModel->{$property} = $value;
                            
                        }
                    }
                    
                }
            }
//             pre($availableProductModel);die;
            $products = $availableProductModel->GetAvailableProductsMarketplaces();
            
//             else{
//                 $products = $availableProductModel->ListAvailableProductsMarketplaces();
//             }
            
            $fileName = "export_available_products.csv";
            $fp = fopen("../Views/_uploads/store_id_{$storeId}/csv/{$fileName}", 'a+');
            ftruncate($fp, 0);
            fwrite($fp, "Id;SKU;Parent;Marca;Título;Ref;Departamento;Status;EAN;Qtd;PVenda;Peso;PCubico;Altura;Largura;Comprimento;Criado;Atualizado;\n");
            
            foreach($products as $k => $fetch){
                $created = dateTimeBr($fetch['created']);
                
                $updated = dateTimeBr($fetch['updated']);

                $peso = str_replace(",", '.', $fetch['weight']);
                $h = str_replace(",", '.', $fetch['height']);
                $w = str_replace(",", '.', $fetch['width']);
                $l = str_replace(",", '.', $fetch['length']);
                $pesocubico = ($h*$w*$l)/6000;
                $pesocubico = number_format($pesocubico, 2);
                $csvRow = "{$fetch['id']};{$fetch['sku']};{$fetch['parent_id']};{$fetch['brand']};{$fetch['title']};{$fetch['reference']};{$fetch['category']};{$fetch['blocked']};{$fetch['ean']};{$fetch['quantity']};{$fetch['sale_price']};{$peso};{$pesocubico};{$fetch['height']};{$fetch['width']};{$fetch['length']};{$created};{$updated}\n";
                fwrite($fp, $csvRow);
            }
            if(fclose($fp)){
                echo "success|{$fileName}";
            }else{
                echo "error|Erro ao gerar arquivo csv do relatório";
            }
            break;
            
		case "list_available_products" :
            
            	$availableProductModel = new AvailableProductsModel($db);
            	$availableProductModel->store_id = $storeId;
            	$availableProductModel->records = 'no_limit';
            	if ( $_SERVER['REQUEST_METHOD'] && ! empty ( $_REQUEST ) ) {
            		foreach ( $_REQUEST as $property => $value ) {
            			if($value != ''){
            				if(property_exists($availableProductModel,$property)){
            					$availableProductModel->{$property} = $value;
            
            				}
            			}
            
            		}
            	}
            	//             pre($availableProductModel);die;
            	$products = $availableProductModel->GetAvailableProductsMarketplaces();
            
            	//             else{
            	//                 $products = $availableProductModel->ListAvailableProductsMarketplaces();
            	//             }
            echo "<table border=1 >
            		<tr>
            		<th>id</th>
            		<th>SKU</th>
            		<th>Title</th>
            		<th>Altura</th>
            		<th>Largura</th>
            		<th>Comprimento</th>
            		<th>Peso</th>
            		<th>Cubico Correios / 6000</th>
            		<th>Use</th>
            		
            		
            		</tr>";
            	foreach($products as $k => $fetch){
            		$created = dateTimeBr($fetch['created']);
            
            		$updated = dateTimeBr($fetch['updated']);
            
            		$peso = str_replace(",", '.', $fetch['weight']);
            		$h = str_replace(",", '.', $fetch['height']);
            		$w = str_replace(",", '.', $fetch['width']);
            		$l = str_replace(",", '.', $fetch['length']);
            		$pesocubico = ($h*$w*$l)/6000;
            		$pesocubico = number_format($pesocubico, 2);
            		
            		$use = $peso;
            		
            		$use = $pesocubico > $peso ? $pesocubico : $peso ;
            		
            		$dif = $peso - $pesocubico;
            		$dif = $dif < 0 ? $dif : '' ;
            		echo  "<tr align=center>
	            		<td>{$fetch['id']}</td>
	            		<td>{$fetch['sku']}</td>
	            		<td>{$fetch['title']}</td>
	            		<td>{$fetch['height']}</td>
	            		<td>{$fetch['width']}</td>
	            		<td>{$fetch['length']}</td>
	            		<td>{$peso}</td>
	            		<td>{$pesocubico}</td>
	            		<td>{$use}</td>
            		</tr>";
            	}
            	
            	echo "</table>";
            break;
            
    case "customers" :
        
        $customersModel = new ManageCustomersModel($db);
        $customersModel->store_id = $storeId;
        $customersModel->TipoPessoa = 1;;
        $customersModel->Marketplace = $marketplace;
        $customersModel->records = '14800';
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
                if($value != ''){
                    if(property_exists($customersModel,$property)){
                        $customersModel->{$property} = $value;
                        
                    }
                }
                
            }
        }
//         pre($customersModel);die;
        $customers = $customersModel->GetCustomers();
        
        
        $fileName = "customers.csv";
        
        $fp = fopen("../Views/_uploads/store_id_{$storeId}/csv/{$fileName}", 'a+');
        ftruncate($fp, 0);
//         fwrite($fp, "Id;TipoPessoa;Genero;Nome;Apelido;Email;CPFCNPJ;RGIE;Telefone;TelefoneAlternativo;DataPeso;PCubico;Altura;Largura;Comprimento;Criado;Atualizado;\n");
        fwrite($fp, "Nome;Email;Aniversario\n");
        
        foreach($customers as $k => $fetch){
            
//             $DataNascimento = dateTimeBr($fetch['DataNascimento']);
//             $created = dateTimeBr($fetch['Criado']);
            
//             $csvRow = "{$fetch['id']};{$fetch['sku']};{$fetch['parent_id']};{$fetch['brand']};{$fetch['title']};{$fetch['reference']};{$fetch['category']};{$fetch['blocked']};{$fetch['ean']};{$fetch['quantity']};{$peso};{$pesocubico};{$fetch['height']};{$fetch['width']};{$fetch['length']};{$created};{$updated}\n";
            $csvRow = "{$fetch['Nome']};{$fetch['Email']};".dateFromTimeBr($fetch['DataNascimento'], '/').";\n";
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