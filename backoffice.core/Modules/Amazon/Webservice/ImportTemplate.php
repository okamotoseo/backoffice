<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../Class/class-ConfigMws.php';
require_once $path .'/../Class/class-MWS.php';
require_once $path .'/../Models/API/SubmitFeedModel.php';
require_once $path .'/../Models/API/RecommendationsModel.php';
require_once $path .'/../Models/Products/GenerateProductDataXml.php';
require_once $path .'/../Models/Products/GenerateInventoryDataXml.php';
require_once $path .'/../Models/Products/GeneratePriceDataXml.php';
require_once $path .'/../Models/Map/AzAttributesModel.php';
require_once $path .'/../Models/Map/AzFeedProductTypeModel.php';
require_once $path .'/../Models/Map/AzBaseXsdModel.php';

require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

if (empty ( $action ) and empty ( $storeId ) ) {

	if(isset($_SERVER ['argv'] [1])){
		$paramAction = explode ( "=", $_SERVER ['argv'] [1] );
		$action = $paramAction [0] == "action" ? $paramAction [1] : null;
	}

	if(isset($_SERVER ['argv'] [2])){
		$paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
		$storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
	}

	$request = "System";

}
if(isset($storeId)){
    
    $db = new DbConnection();
    
	switch($action){
		
		case "import_values" :
			
			$azFeedProductTypeModel = new AzFeedProductTypeModel($db, null, $storeId);
			$azFeedProductTypeModel->tree_id = '17124957011';
			
			
			
			$res = $azFeedProductTypeModel->GetFeedProductTypeValues();
			
			pre($res);die;
			
			
			
			
			
			die;
			$file = fopen("../Templates/ar_e_ventilacao.csv", 'r');
			$count = 0;
			$productType = array();
			while (($line = fgetcsv($file)) !== false)
			{
				
				
				if($count == 0){
					foreach($line as $i => $attr){
						$texto = explode('[', $attr);
						$texto = explode(']', $texto[1]);
						$attrId = trim($texto[0]);
						$productType[$i] = array("name" => $attr, "feed_product_type" => $attrId);
						
					}
				}
				if($count == 1){
					foreach($line as $i => $attr){
						$productType[$i]['attribute'] =  $attr;
				
					}
				}
				
				if($count > 1){
					foreach($line as $i => $attr){
						if(!empty($attr)){
							$productType[$i]['values'][] =  $attr;
						}
				
					}
				}
				
				$count++;
			}
			fclose($file);
			
			
			
// 			pre($productType);die;
			
			foreach($productType as $k => $values){
				if(!empty($values['feed_product_type'])){
					foreach($values['values'] as $i => $value){
						$data = array(
							'feed_product_type' => $values['feed_product_type'], 
							'attribute' => $values['attribute'], 
							'value' => $value,
							'name' => $values['name'],
							'created' => date('Y-m-d H:i:s')
						);
						pre($data);
						$query = $db->insert('az_feed_product_type', $data);
						if(!$query){
							pre($query);
						}else{
							echo $id = $db->last_id;
						}
						
					}
				}
				
				
				
			}
			
			
			
			break;
	    
	    case "import_values_2" :
	        
	        
	        $delimitador = ';';
	        $cerca = '"';
	        $filePath ="../Templates/home_values.csv";
	        
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
	                    $row = array_combine( $head, $linha );
	                    
	                    pre($row);
	                
                    }
	                
	            }
	           
	            fclose($file);
	        }
	        
	        break;
	        
	        
	}
	
	
}