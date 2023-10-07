<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);

require_once $path .'/../Class/class-DbConnection.php';
require_once $path .'/../Class/class-MainModel.php';
require_once $path .'/../Functions/global-functions.php';
require_once $path .'/../Models/Admin/TranslatesModel.php';
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
	
	
	case "import_promotion_price" :

        $file = fopen('numeros.csv', 'r');
        while (($line = fgetcsv($file)) !== false)
        {
          $meuArray[] = $line;
        }
        fclose($file);
        print_r($meuArray);
        
        break;
        
        
	case 'import_csv_translate':
		
		$translateModel = new TranslatesModel($db, null);
		
		$file = fopen('Home_Definicao_Dados.csv', 'r');
		while (($line = fgetcsv($file)) !== false)
		{
			$translateModel->attribute_group = 'Home';
			$translateModel->store_id = 0;
			$translateModel->word = trim($line[0]);
			$translateModel->translate = trim($line[1]);
			$translateModel->description = trim($line[2]);
			$translateModel->exemple = trim($line[3]);
			$translateModel->required = trim($line[4]);
			$translateModel->Save();
			$meuArray[] = $line;
		}
		fclose($file);
		
		break;
        
    }
        
}