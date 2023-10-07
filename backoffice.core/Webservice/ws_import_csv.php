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
require_once $path .'/../Models/Customer/ManageCustomersModel.php';
require_once $path .'/functions.php';


$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;

if (empty ( $action ) and empty ( $storeId )) {
    $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
    $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
    $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
}
if(isset($storeId)){
    
    $db = new DbConnection();
    
	switch($action){
	    
	    case "import_customer" :
	        
	        
	        $delimitador = ';';
	        $cerca = '"';
	        $filePath ="../Views/_uploads/csv/prod4novo.csv";
	        
	        $file = fopen($filePath, "r");
	        $count = 0;
	        
	        if ($file) {
	            
	            $head = '';
	            
	            while (!feof($file)) {
	                
	                $linha = fgetcsv($file, 0, $delimitador, $cerca);
	                
	                if (!$linha) {
	                    continue;
	                }
	                
	                
	                if(empty($head)){
	                    $head = $linha;
	                }else{
	                    $customer = array_combine( $head, $linha );
	                
    	                if(!empty(trim($customer['email']))){
    	                    
    	                    if(trim($customer['cliente']) == 't' && trim($customer['funcionario']) == 'f'){
    	                        
    	                        
        	                    if(strpos(strtolower($customer['nome']), "func ") !== false){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['nome']), "gasparini") !== false){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['email']), "amazon") !== false){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['email']), "skyhub") !== false){
        	                       continue; 
        	                    }
        	                    if(strpos(strtolower($customer['email']), "mercadol") !== false){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['email']), "mercadopago") !== false){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['email']), "gasparini") !== false){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['email']), "const") !== false){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['email']), "fort") === true){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['email']), "@") === true){
        	                        continue;
        	                    }
        	                    if(strpos(strtolower($customer['cidade']), "marilia ") !== false){
        	                        continue;
        	                    }
    	                    
    	                    
            	                $customerModel = new  ManageCustomersModel($db);
            	                $customerModel->store_id = $storeId;
            	                $customerModel->Codigo = '';
            	                $customerModel->TipoPessoa = trim($customer['pessoa']);
            	                $customerModel->Nome = ucwords(mb_strtolower(trim($customer['nome'])));
            	                $customerModel->Apelido = ucwords(mb_strtolower(trim($customer['apelido'])));
            	                $customerModel->Email = mb_strtolower(trim($customer['email']));
            	                $customerModel->CPFCNPJ = getNumbers(trim($customer['cpfcnpj']));
    	                        $customerModel->RGIE = getNumbers(trim($customer['rgie']));
    	                        $customerModel->Telefone = !empty(trim($customer['telefone1'])) ? getNumbers(trim($customer['telefone1'])) : getNumbers(trim($customer['telefone2'])) ;
            	                $customerModel->TelefoneAlternativo = getNumbers(trim($customer['telefone2']));
            	                $customerModel->DataCriacao = trim($customer['cadastro']);
            	                $customerModel->Genero = trim($customer['sexo']);
            	                $customerModel->DataNascimento = dbDate(trim($customer['nascimento']));
            	                   $enderecoNumber = explode(",", $customer['endereco']);
        	                    $customerModel->Endereco = ucwords(mb_strtolower(trim($enderecoNumber[0])));
            	                $customerModel->Numero = isset($enderecoNumber[1]) ? trim($enderecoNumber[1]) : 'SN';
            	                $customerModel->Complemento = mb_strtolower(trim($customer['complemento']));
            	                $customerModel->Cidade = ucwords(mb_strtolower(trim($customer['cidade'])));
            	                $customerModel->Bairro = ucwords(mb_strtolower(trim($customer['bairro'])));
            	                $customerModel->Estado = trim($customer['uf']);
            	                $customerModel->CEP =  getNumbers(trim($customer['cep']));
            	                $customerModel->Responsavel = 'Willians';
            	                $customerModel->Marketplace = "Sysplace";
            	                $customerId = $customerModel->Save();
            	                echo "-------Customer-----------<br>";
            	                pre($customerId);
            	                echo $count++;
            	                echo "<br>";
//             	                if($count == 200){
//             	                    die;
//             	                }

            	                
    	                    }
    	                }
//     	                die;
                    }
	                
	            }
	           
	            fclose($file);
	        }
	        
	        break;
	        
	        
	}
	
	
}