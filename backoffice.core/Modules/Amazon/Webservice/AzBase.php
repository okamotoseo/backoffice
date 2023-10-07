<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
define( 'HOME_URI', 'https://'.$_SERVER['HTTP_HOST']);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../Class/class-ConfigMws.php';
require_once $path .'/../Class/class-MWS.php';
require_once $path .'/../Models/Map/AzCategoryModel.php';
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
	$productType = getMenuXsd($db);
	pre($productType);
// 	$xsdstring ="https://images-na.ssl-images-amazon.com/images/G/01/rainier/help/xsd/release_4_1/Product.xsd";
	$xsdstring ="https://images-na.ssl-images-amazon.com/images/G/01/rainier/help/xsd/release_4_1/amzn-base.xsd";
   
    $xml_file = getSSLFile($xsdstring);
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->loadXML(mb_convert_encoding($xml_file, 'utf-8', mb_detect_encoding($xsdstring)));
    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
    $xpath->registerPHPFunctions();
    
    switch($action){
    	
    	
    	case "list_base":
    		
    		
    		 
    		function echoElements($indent, $elementDef) {
    			global $doc, $xpath;
    	
    			$name = !empty($elementDef->getAttribute('name')) ? $elementDef->getAttribute('name') : $elementDef->getAttribute('ref') ;
    			$type = !empty($elementDef->getAttribute('type')) ? $elementDef->getAttribute('type') : $elementDef->getAttribute('value') ;
    	
    			echo "<div>" .$indent."&nbsp;&nbsp;&nbsp;&nbsp;".$name."  -> {$type}"."</div>\n";
    	
    			$elementDefs = $xpath->evaluate("xs:complexType/xs:sequence/xs:element", $elementDef);
    			foreach($elementDefs as $elementDef) {
    				echoElements($indent . "&nbsp;&nbsp;&nbsp;&nbsp;", $elementDef);
    			}
    	
    		}
    		 
    		$elementDefs = $xpath->evaluate("/xs:schema/xs:element");
    		foreach($elementDefs as $elementDef) {
    			echoElements("", $elementDef);
    		}
    		 
    		break;
    		
    	
            
    }
}
?>
