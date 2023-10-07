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
require_once $path .'/../Models/Map/AzBaseXsdModel.php';
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
    
    switch($action){
    	
    	case "categories":
    		
    		$categoryTree = $_REQUEST['category'];
    		$categoryRelational= $_REQUEST['relational'];
    		$xsd = $_REQUEST['xsd'];
    		$choice = $_REQUEST['choice'];
    		$categoryId= isset($_REQUEST['category_id']) && !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : null ;
    		$hierarchy= isset($_REQUEST['hierarchy']) && !empty($_REQUEST['hierarchy']) ? $_REQUEST['hierarchy'] : null ;
    		
    		$sql = "SELECT * FROM az_category_tree WHERE name LIKE '{$hierarchy}%'";
    		$queryTree = $db->query($sql);
    		$categories = $queryTree->fetchAll(PDO::FETCH_ASSOC);
    		$verify = array();
    		$option = "<option value='select'>Selecione</option>";
    		foreach($categories as $key => $category){
    			
    			$categoryName = explode($hierarchy, $category['name'], 2);
    			
    			if(isset($categoryName[1])){
    				
    				$categoryParts = explode("/", $categoryName[1]);
    				if(isset($categoryParts[1])){
	    				if(!isset($verify[$categoryParts[1]])){
	    					
	    					
	    					$verify[$categoryParts[1]] = true;
	    					$option .="<option value='{$categoryParts[1]}' hierarchy='{$hierarchy}{$categoryParts[0]}/{$categoryParts[1]}' xsd='{$xsd}'  choice='{$choice}'  >{$categoryParts[1]}</option>";
	    				}
    				
    				}
    			}
    			 
    		}
    		
    		
    		$count = count($verify);
    		if($count > 0){
    			echo "next| <a onclick=\"returnCategory({$hierarchy}, {$categoryTree}, {$categoryRelational}, {$xsd}, {$choice}, {$categoryId})\">{$categoryTree}</a> > |{$option}|{$count} ";
    		}else{
    			
    			$resSplitCategories = splitCategory($hierarchy);
    			$resSplitCategories = array_reverse($resSplitCategories);
    			$pathFromRoth = '';
    			foreach($resSplitCategories as $k => $catVal){
    				
    				$pathFromRoth .="{$catVal['name']} > ";
    				
    			}
    			$pathFromRoth = substr($pathFromRoth, 0,-3);
    			
    			$hierarchy = $category['name'];
    			 
    			$db->insert('az_category_relationship', array(
    					'store_id' => $storeId,
    					'category_id' => $categoryId,
    					'tree_id' => $categories[0]['category_id'],
    					'category' => $categoryRelational,
    					'name' => $catVal['name'],
    					'xsd' => $xsd,
    					'choice' => $choice,
    					'set_attribute' => 'ProductType',
    					'hierarchy' => $hierarchy,
    					'path_from_root' => $pathFromRoth
    			));
    			$relationshipId = $db->last_id;
    			$azCategoryModel = new AzCategoryModel($db, null, $storeId);
    			$listDefaultCategoriesAz = $azCategoryModel->defaultCategoriesAz();
    			 
    			$option = "<option value='select'>Selecione</option>";
    			foreach($listDefaultCategoriesAz as $key => $category){
    				$option .="<option value='{$category['name']}' xsd='{$category['name']}'  choice='{$category['label']}' hierarchy='{$category['label']}'>{$category['label']}</option>";
    				 
    			}
    			$btn = "<button  class='btn btn-default btn-xs {$categoryId}'  onclick=\"removeCategeryRelationship({$relationshipId}, {$categoryId})\" ><i class='fa fa-undo' ></i> Refazer relacionamento</button>";
    			$linkAttr = "<a class='fa  fa-list-alt' href='/Modules/Amazon/Map/Attributes/Xsd/{$xsd}/{$choice}/Category/{$categoryId}' title='Relacionar Atributos' ></a>&nbsp;&nbsp;";
    			$linkXsd = "<a class='fa  fa-info-circle' href='{$xsdstring}' target='_blank' title='XSD' ></a>";
    			echo "end|{$pathFromRoth}|{$option}|{$btn}|{$linkAttr}|{$linkXsd}";
    			
    		}
    		
//     		pre($result);
    		
    		
    		break;
    }
    
}