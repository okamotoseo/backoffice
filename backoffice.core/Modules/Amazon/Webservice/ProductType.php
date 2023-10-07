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
	$xsd = $listXsd = '';
	$choice = isset($_REQUEST['choice']) && !empty($_REQUEST['choice']) ? $_REQUEST['choice'] : null ;
	$attr['Choice'] = $choice;
	if(isset($_REQUEST['xsd'])){
		$xsd = $_REQUEST['xsd'];
	}
	$xsdstring ="https://images-na.ssl-images-amazon.com/images/G/01/rainier/help/xsd/release_4_1/Product.xsd";
	$xsdInfo = array();
	foreach($productType as $key => $xsds){
		if($xsd == $xsds['name']){
			$xsdstring = "https://backoffice.sysplace.com.br/Modules/Amazon/Xsd/".trim($xsds['file']);
			$xsdInfo = $xsds;
		}

		$listXsd .= "<li><a href='https://backoffice.sysplace.com.br/Modules/Amazon/Webservice/ProductType.php?store_id={$storeId}&action=list_attr_xsd&xsd={$xsds['name']}' target='_blank'>{$xsds['label']}</a> -  
		<a href='https://backoffice.sysplace.com.br/Modules/Amazon/Webservice/ProductType.php?store_id={$storeId}&action=list_choice&xsd={$xsds['name']}' target='_blank'>Choice</a> - 
		<a href='{$xsds['xsd']}' target='_blank'>XSD</a> = {$xsds['set_attribute']}</li>";
	
	}
	if(isset($_REQUEST['menu'])){
		if(!empty($listXsd)){
			echo "<ul>{$listXsd}</ul>";
		}
	}
	
// 	pre($xsdstring);
	
	if(empty($xsdstring)){
		$xsdstring ="https://images-na.ssl-images-amazon.com/images/G/01/rainier/help/xsd/release_4_1/Product.xsd";
	}
   
    $xml_file = getSSLFile($xsdstring);
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->loadXML(mb_convert_encoding($xml_file, 'utf-8', mb_detect_encoding($xsdstring)));
    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
    $xpath->registerPHPFunctions();
    
    switch($action){
    	
    	case "list_product_type":
    		 
    		 $result = array();
//   			$query = "/xs:schema/xs:element[@name='{$xsd}']/xs:complexType/xs:sequence/xs:element[@name='ProductType']/xs:complexType/xs:sequence/xs:element";
    		 $query = "/xs:schema/xs:element[@name='{$xsd}']/xs:complexType/xs:sequence/xs:element";
    		$elementDefs = $xpath->query($query);
    		
//     		$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='ProductType']";
    		
//     		$elementDefs = $xpath->query($query);
    		
    		foreach($elementDefs as $elementDef) {
    			
    			
    			$name = !empty($elementDef->getAttribute('name')) ? $elementDef->getAttribute('name') : $elementDef->getAttribute('ref') ;
    			$type = !empty($elementDef->getAttribute('type')) ? $elementDef->getAttribute('type') : $elementDef->getAttribute('value') ;
    			$result[$name] =  $type;
    			
//     			ProductType
    		}
    		pre($result);
    		break;
    	
    	case "list_product":
    		 
    		 
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
    		
    	
    	case "list_choice":
    		
    		
    		$type = $_REQUEST['type'];
    		
    		$category = $_REQUEST['category'];
    		
    		$categoryId = $_REQUEST['category_id'];
    		
    		$choice = isset($_REQUEST['choice']) && !empty($_REQUEST['choice']) ? $_REQUEST['choice'] : null ;
    		
    		$setAttribute = isset($_REQUEST['set_attribute']) && !empty($_REQUEST['set_attribute']) ? $_REQUEST['set_attribute'] : null ;
    		
    		$choices = array();
    		if($type == 'complexType'){
    			if(!isset($choice)){
    				switch($setAttribute){
    					case 'ClassificationData':
    						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$setAttribute}']/xs:complexType/xs:sequence/xs:element";
    						break;
    					default:
    						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$setAttribute}']/xs:complexType/xs:choice/xs:element";
    						break;
    				}
    					
	    			
	    			$entries = $xpath->query($query);
	    			foreach ($entries as $entry) {
	    				$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('ref') ;
	    				$choices[] =  array('name' => $name);
	    			}
    			}else{
    				$query = "/xs:schema/xs:element[@name='{$choice}']/xs:complexType/xs:sequence/xs:element";
    				$entries = $xpath->query($query);
    				foreach ($entries as $entry) {
    					$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('ref') ;
    					$choices[] =  array('name' => $name);
    				}
    			}
    		}
    		
    		if($type == 'simpleType'){
    			if(!isset($choice)){
	    			switch($xsd){
		    			case 'ProductClothing':
			    			$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$setAttribute}']/xs:simpleType/xs:restriction/xs:enumeration";
		    				break;
		    			default:
		    				$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$setAttribute}']/xs:simpleType/xs:restriction/xs:enumeration";
		    				break;
	    			}
	    			$entries = $xpath->query($query);
	    			foreach ($entries as $entry) {
	    				$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('value') ;
	    				$choices[] =  array('name' => $name);
	    			}
    			}else{
    				switch($xsd){
    					case 'ClothingType':
    						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$setAttribute}']/xs:simpleType/xs:restriction/xs:enumeration";
    						break;
    					case 'ProductClothing':
    						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element/xs:complexType/xs:sequence/xs:element[@name='{$setAttribute}']/xs:simpleType/xs:restriction/xs:enumeration";
    						break;
    					default:
    						$query = "/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element";
    						break;
    				}
    				$entries = $xpath->query($query);
    				foreach ($entries as $entry) {
    					$name = !empty($entry->getAttribute('name')) ? $entry->getAttribute('name') : $entry->getAttribute('value') ;
    					$choices[] =  array('name' => $name);
    				}
    			}
    			
    		}
    		
    		$option = "<option value='select'> Selecionar Próxima >></option>";
    		if(!empty($choices[0])){
    			foreach($choices as $key => $name){
    				if(!empty($name['name'])){
    					$option .= "<option value='{$name['name']}' xsd ='{$xsd}' choice='{$name['name']}' set_attribute='{$setAttribute}' type='{$type}' >{$name['name']}</option>";
    				}
    			}
    		}
    		if(!isset($choice)){
    			echo "next| <a onclick=\"getCategoryXsd({$xsd}, '')\" id='' category='' >{$xsd}</a> > |{$option}| ";
    		}
    		if(isset($choice)){
    			$pathFromRoth ="<a onclick=\"getCategoryXsd({$xsd}, '')\" id='' category='' >{$xsd}</a> > <a onclick=\"getCategoryXsd({$xsd}, {$choice})\" id='' category='' >{$choice}</a>";
    			$hierarchy = "{$xsd} > {$choice}";
    			
    			$db->insert('az_category_relationship', array(
    					'store_id' => $storeId,
    					'category_id' => $categoryId,
    					'category' => $category,
    					'xsd' => $xsd,
    					'choice' => $choice,
    					'set_attribute' => $setAttribute,
    					'hierarchy' => $hierarchy,
    					'path_from_root' => $pathFromRoth
    			));
    			$relationshipId = $db->last_id;
    			$azCategoryModel = new AzCategoryModel($db, null, $storeId);
    			$listDefaultCategoriesAz = $azCategoryModel->defaultCategoriesAz();
    			
    			$option = "<option value='select'>Selecione</option>";
    			foreach($listDefaultCategoriesAz as $key => $category){
    				$option .="<option value='{$category['name']}' xsd ={$category['name']} choice='' set_attribute='{$category['set_attribute']}' type='{$category['type']}' >{$category['label']}</option>";
    			
    			}
    			$btn = "<button  class='btn btn-default btn-xs {$categoryId}'  onclick=\"removeCategeryRelationship({$relationshipId}, {$categoryId})\" ><i class='fa fa-undo' ></i> Refazer relacionamento</button>";
    			$linkAttr = "<a class='fa  fa-list-alt' href='".HOME_URI."/Modules/Amazon/Map/Attributes/Xsd/{$xsd}/{$choice}/Category/{$categoryId}' title='Relacionar Atributos' ></a>&nbsp;&nbsp;";
    			$linkXsd = "<a class='fa  fa-info-circle' href='{$xsdstring}' target='_blank' title='XSD' ></a>";
    			echo "end|{$pathFromRoth}|{$option}|{$btn}|{$linkAttr}|{$linkXsd}";
    		}
    		break;
    		
    	case "remove_relationhsip":
    		$id = $_REQUEST['relationship_id'];
    		$sql = "DELETE FROM az_category_relationship WHERE store_id =  {$storeId} AND id = {$id}";
    		$query = $db->query($sql);
    		if(!$query){
    			echo "error|Não foi possivel remover o relacionamento!";
    		}
    		echo "success|";
    		break;
            
    }
}
?>
