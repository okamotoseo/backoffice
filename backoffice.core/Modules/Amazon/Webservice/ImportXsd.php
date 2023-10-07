<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-ConfigMws.php';
require_once $path .'/../Class/class-MWS.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
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

	

	$db = new DbConnection();
	
	$productType = getMenuXsd($db);
	
	$listXsd = '';
	
	$choice = isset($_REQUEST['choice']) && !empty($_REQUEST['choice']) ? $_REQUEST['choice'] : null ;
	
	$attr['Choice'] = $choice;
	
	if(isset($_REQUEST['xsd'])){
		$xsd = $_REQUEST['xsd'];
	}
	
	$xsdstring ="https://images-na.ssl-images-amazon.com/images/G/01/rainier/help/xsd/release_4_1/Product.xsd";
	$xsdInfo = array();
	foreach($productType as $key => $xsds){
		if($xsd == $xsds['name']){
			$xsdstring = $xsds['xsd'];
			$xsdInfo = $xsds;
		}

		$listXsd .= "<li><a href='https://backoffice.sysplace.com.br/Modules/Amazon/Webservice/ImportXsd.php?action=list_attr_xsd&xsd={$xsds['name']}&menu=true' target='_blank'>{$xsds['label']}</a> -  
		{$xsds['set_attribute']} - <a href='{$xsds['xsd']}' target='_blank'>XSD</a></li>";
	
	}
	
    
	if(isset($_REQUEST['menu'])){
		if(!empty($listXsd)){
			echo "<ul>{$listXsd}</ul>";
		}
	}
   
    $xml_file = getSSLFile($xsdstring);
    
    $doc = new DOMDocument();
    
    $doc->preserveWhiteSpace = false;
    
//     $doc->loadXML(mb_convert_encoding($xml_file, 'utf-8', mb_detect_encoding($xsdstring)));
    $doc->loadXML($xml_file);
    $parts = explode('/', $xsdstring);
    $file = end($parts);
    $doc->Save("/var/www/html/app_mvc/Modules/Amazon/Xsd/".$file);
    
    $xpath = new DOMXPath($doc);
    
    $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
    
    $xpath->registerPHPFunctions();
    
    
    function echoElements($indent, $elementDef) {
    	global $doc, $xpath;
    
    	$name = !empty($elementDef->getAttribute('name')) ? $elementDef->getAttribute('name') : $elementDef->getAttribute('ref') ;
    	echo "<div>" .$indent."&nbsp;&nbsp;&nbsp;&nbsp;".$name."</div>\n";
    	$elementDefs = $xpath->evaluate("xs:complexType/xs:sequence/xs:element", $elementDef);
    	foreach($elementDefs as $elementDef) {
    		echoElements($indent . "&nbsp;&nbsp;&nbsp;&nbsp;", $elementDef);
    	}
    
    }
     
    $elementDefs = $xpath->evaluate("/xs:schema/xs:element");
    foreach($elementDefs as $elementDef) {
    	echoElements("", $elementDef);
    }
    
    
    
