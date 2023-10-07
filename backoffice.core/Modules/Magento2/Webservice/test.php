<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
// require_once $path .'/../Class/class-Soap.php';
require_once $path .'/../Class/class-Magento2.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Catalog/ProductsModel.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$idProduct = isset($_REQUEST["id_product"]) && $_REQUEST["id_product"] != "" ? $_REQUEST["id_product"] : null ;
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
	  
		case "teste":
			
			$catalogProducts = new ProductsModel($db, null, $storeId);
			$res = $catalogProducts->ProductSku('WT09');
			
			pre($res);die;
			
			
			
			
			
			
			
			$userData = array("username" => "backoffice", "password" => "x20bo10kub");
			$ch = curl_init("http://shopping.sysplace.com.br/rest/V1/integration/admin/token");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
			
			$token = curl_exec($ch);
			
			$ch = curl_init("http://shopping.sysplace.com.br/rest/V1/products/WT09");
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
			
			$result = curl_exec($ch);
			
			var_dump($result);die;
			
			
// 			$opts = array(
// 					'http'=>array(
// 							'header' => 'Authorization: Bearer 36849300bca4fbff758d93a3379f1b8e'
// 					)
// 			);
// 			$wsdlUrl = 'https://shopping.sysplace.com.br/soap/default?wsdl=1&services=testModule1AllSoapAndRestV1';
// 			$serviceArgs = array("id"=>1);
			
// 			$context = stream_context_create($opts);
// 			$soapClient = new SoapClient($wsdlUrl, ['version' => SOAP_1_2, 'stream_context' => $context]);
			
// 			$soapResponse = $soapClient->testModule1AllSoapAndRestV1Item($serviceArgs);
// 			pre($soapResponse);die;
			
			
			 
			break;
	}
	
	
}





