<?php

set_time_limit ( 300 );

$path = dirname(__FILE__);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Rest.php';
require_once $path .'/../Class/class-Vtex.php';
require_once $path .'/functions.php';

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$type = isset($_REQUEST["type"]) && $_REQUEST["type"] != "" ? $_REQUEST["type"] : 'single' ;
$sku = isset($_REQUEST["sku"]) && $_REQUEST["sku"] != "" ? $_REQUEST["sku"] : null ;
$parentId = isset($_REQUEST["parent_id"]) && $_REQUEST["parent_id"] != "" ? $_REQUEST["parent_id"] : null ;

$request = "Manual";
if (empty ( $action ) and empty ( $storeId )) {
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 17);
    
	switch($action){
	        
	    case "Product_Specifications":
	        $Id = isset($_REQUEST['id']) && !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : null ;
	        $vtex = new Vtex($db, $storeId);
	        if(isset($Id)){
	            $sql = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId} AND Id = {$Id}";
	        }else{
	            $sql = "SELECT * FROM module_vtex_products WHERE store_id = {$storeId} 
            AND specificationReturnCode != 200";
	        }
	        $dataLabels = array();
	        $specifications = array();
	        $query = $db->query($sql);
	        $resProduct = $query->fetchAll(PDO::FETCH_ASSOC);
	        foreach($resProduct as $k => $product){
	            
	            $sqlXml = "SELECT * FROM module_google_xml_products WHERE store_id = {$storeId}
                            AND item_group_id LIKE '{$product['RefId']}'";
	            $queryXml = $db->query($sqlXml);
	            $dataProduct = $queryXml->fetch(PDO::FETCH_ASSOC);
	            
	            if(!empty($dataProduct['custom_label_0'])){
	                $values = explode(',', $dataProduct['custom_label_0']);
	                $values[0] = trim($values[0]);
	                $dataLabels[$values[0]] = isset($dataLabels[$values[0]]) ? $dataLabels[$values[0]]+1 : 1 ;
	            }
	            if(!empty($dataProduct['custom_label_1'])){
	                $values = explode(',', $dataProduct['custom_label_1']);
	                $values[0] = trim($values[0]);
	                $dataLabels[$values[0]] = isset($dataLabels[$values[0]]) ? $dataLabels[$values[0]]+1 : 1 ;
	            }
	            if(!empty($dataProduct['custom_label_2'])){
	                $values = explode(',', $dataProduct['custom_label_2']);
	                $values[0] = trim($values[0]);
	                $dataLabels[$values[0]] = isset($dataLabels[$values[0]]) ? $dataLabels[$values[0]]+1 : 1 ;
	            }
	            if(!empty($dataProduct['custom_label_3'])){
	                $values = explode(',', $dataProduct['custom_label_3']);
	                $values[0] = trim($values[0]);
	                $dataLabels[$values[0]] = isset($dataLabels[$values[0]]) ? $dataLabels[$values[0]]+1 : 1 ;
	            }
	            if(!empty($dataProduct['custom_label_4'])){
	                $values = explode(',', $dataProduct['custom_label_4']);
	                $values[0] = trim($values[0]);
	                $dataLabels[$values[0]] = isset($dataLabels[$values[0]]) ? $dataLabels[$values[0]]+1 : 1 ;
	            }
	            
	            
	            /**********************************************************************************************************/
	            /********************************* Altura do Salto ******************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_0']) && !empty($dataProduct['custom_label_0'])){
	                $FieldValueId = '';
	                $alturaSalto = array(
	                    'Rasteiro até 2cm' => 24968,
	                    'Baixo 2-5cm' => 24969,
	                    'Médio 5-9cm' => 24970,
	                    'Alto +9cm' => 24971
	                );
	                $values = explode(',', $dataProduct['custom_label_0']);
	                $valueName = trim(RemoveAcentos(trim($values[0])));
	                foreach($alturaSalto as $text => $idVal){
	                    
	                    $valueText = trim(RemoveAcentos($text));
	                    if($valueName == $valueText){
	                        $FieldValueId = $idVal;
	                        break;
	                    }
	                }
	                if(!empty($FieldValueId)){
    	                $data = array('FieldId' => 30, 'FieldValueId' => $FieldValueId, 'Text' => 'Altura do Salto');
    	                pre($data);
    	                $product['specifications'][] = $data;
    	                $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
    	                pre($resPrincipal);
    	                if($resPrincipal['httpCode'] == 200){
    	                    $queryUpdate = $db->update('module_vtex_products',
    	                        array('store_id', 'Id'),
    	                        array($storeId, $product['Id']),
    	                        array('specificationReturnCode' => $resPrincipal['httpCode']));
    	                }
	                }
	            }
	            /**********************************************************************************************************/
	            /********************************* Altura do Salto ******************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_0']) && !empty($dataProduct['custom_label_0'])){
	                $FieldValueId = '';
	                $alturaSalto = array(
	                    'Rasteiro até 2cm' => 25028,
	                    'Baixo 2-5cm' => 25029
	                );
	                $values = explode(',', $dataProduct['custom_label_0']);
	                $valueName = trim(RemoveAcentos(trim($values[0])));
	                foreach($alturaSalto as $text => $idVal){
	                    
	                    $valueText = trim(RemoveAcentos($text));
	                    if($valueName == $valueText){
	                        $FieldValueId = $idVal;
	                        break;
	                    }
	                }
	                if(!empty($FieldValueId)){
	                    $data = array('FieldId' => 48, 'FieldValueId' => $FieldValueId, 'Text' => 'Altura do Salto');
	                    pre($data);
	                    $product['specifications'][] = $data;
	                    $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
	                    pre($resPrincipal);
	                    if($resPrincipal['httpCode'] == 200){
	                        $queryUpdate = $db->update('module_vtex_products',
	                            array('store_id', 'Id'),
	                            array($storeId, $product['Id']),
	                            array('specificationReturnCode' => $resPrincipal['httpCode']));
	                    }
	                }
	            }
	            /**********************************************************************************************************/
	            /********************************* Altura do Cano *********************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_1']) && !empty($dataProduct['custom_label_1'])){
	                $FieldValueId = '';

    	            $alturaCano = array(
    	                'Cano Médio' => 24959,
    	                'Cano Baixo' => 24960,
    	                'Cano Alto'=> 24961,
    	                'Médio' => 24959,
    	                'Baixo' => 24960,
    	                'Alto'=> 24961
    	            );
    	            $values = explode(',', $dataProduct['custom_label_1']);
    	            $valueName = trim(RemoveAcentos(trim($values[0])));
    	            foreach($alturaCano as $text => $idVal){
    	                
    	                $valueText = trim(RemoveAcentos($text));
    	                if($valueName == $valueText){
    	                    $FieldValueId = $idVal;
    	                    break;
    	                }
    	            }
    	            if(!empty($FieldValueId)){
        	            $data = array('FieldId' => 27, 'FieldValueId' => $FieldValueId, 'Text' => 'Altura do Cano');
        	            pre($data);
        	            $product['specifications'][] = $data;
        	            $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
        	            pre($resPrincipal);
        	            if($resPrincipal['httpCode'] == 200){
        	                $queryUpdate = $db->update('module_vtex_products',
        	                    array('store_id', 'Id'),
        	                    array($storeId, $product['Id']),
        	                    array('specificationReturnCode' => $resPrincipal['httpCode']));
        	            }
	               }
	            }
	            
	            /**********************************************************************************************************/
	            /********************************* Altura do Cano *********************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_1']) && !empty($dataProduct['custom_label_1'])){
	                $FieldValueId = '';
	                $alturaCano = array(
	                    'Cano Médio' => 25004,
	                    'Cano Baixo' => 25002,
	                    'Cano Alto'=> 25003,
	                    'Médio' => 25004,
	                    'Baixo' => 25002,
	                    'Alto'=> 25003
	                );
	                $values = explode(',', $dataProduct['custom_label_1']);
	                $valueName = trim(RemoveAcentos(trim($values[0])));
	                foreach($alturaCano as $text => $idVal){
	                    
	                    $valueText = trim(RemoveAcentos($text));
	                    if($valueName == $valueText){
	                        $FieldValueId = $idVal;
	                        break;
	                    }
	                }
	                if(!empty($FieldValueId)){
	                    $data = array('FieldId' => 38, 'FieldValueId' => $FieldValueId, 'Text' => 'Altura do Cano');
	                    pre($data);
	                    $product['specifications'][] = $data;
	                    $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
	                    pre($resPrincipal);
	                    if($resPrincipal['httpCode'] == 200){
	                        $queryUpdate = $db->update('module_vtex_products',
	                            array('store_id', 'Id'),
	                            array($storeId, $product['Id']),
	                            array('specificationReturnCode' => $resPrincipal['httpCode']));
	                    }
	                }
	            }
	            
	            /**********************************************************************************************************/
	            /********************************* Altura do Cano *********************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_1']) && !empty($dataProduct['custom_label_1'])){
	                $FieldValueId = '';
	                $alturaCano = array(
	                    'Cano Médio' => 25022,
	                    'Cano Baixo' => 25023,
	                    'Cano Alto'=> 25024,
	                    'Médio' => 25022,
	                    'Baixo' => 25023,
	                    'Alto'=> 25024
	                );
	                $values = explode(',', $dataProduct['custom_label_1']);
	                $valueName = trim(RemoveAcentos(trim($values[0])));
	                foreach($alturaCano as $text => $idVal){
	                    
	                    $valueText = trim(RemoveAcentos($text));
	                    if($valueName == $valueText){
	                        $FieldValueId = $idVal;
	                        break;
	                    }
	                }
	                if(!empty($FieldValueId)){
	                    $data = array('FieldId' => 46, 'FieldValueId' => $FieldValueId, 'Text' => 'Altura do Cano');
	                    pre($data);
	                    $product['specifications'][] = $data;
	                    $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
	                    pre($resPrincipal);
	                    if($resPrincipal['httpCode'] == 200){
	                        $queryUpdate = $db->update('module_vtex_products',
	                            array('store_id', 'Id'),
	                            array($storeId, $product['Id']),
	                            array('specificationReturnCode' => $resPrincipal['httpCode']));
	                    }
	                }
	            }
	            
	            /**********************************************************************************************************/
	            /*********************************** Material *************************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_2']) && !empty($dataProduct['custom_label_2'])){
	                $data = array();
	                $parts = explode(',', $dataProduct['custom_label_2']);
	                foreach($parts as $i => $part){
    	                $FieldValueId = '';
    	  
    	                $material = array(
    	                    'Couro' => 24936,
    	                    'Couro pelica' => 24936,
    	                    'EVA' => 24937,
    	                    'Camurça' => 24938,
    	                    'Nobuck' => 24939,
    	                    'Lona' => 24941,
    	                    'Sintético' => 24942,
    	                    'Verniz' => 24943,
    	                    'Elastano' => 24944,
    	                    'Algodão' => 24945,
    	                    'Poliéster' => 24946,
    	                    'Mesh' => 24947,
    	                    'Poliamida' => 24948,
    	                    'Borracha' => 24949,
    	                    'Têxtil' => 25095,
    	                    'Tecido' => 25095,
    	                    'Jeans' => 25095,
    	                    'Juta' => 25095,
    	                    'PVC' => 25096,
    	                    'Multimaterial' => 25260,
    	                    'Nylon' => 25261
    	                );
    	                $valueName = trim(RemoveAcentos(trim($part)));
    	                foreach($material as $text => $idVal){
    	                    
    	                    $valueText = trim(RemoveAcentos($text));
    	                    if($valueName == $valueText){
    	                        $FieldValueId = $idVal;
    	                        break;
    	                    }
    	                }
    	                if(!empty($FieldValueId)){
    	                    $data = array('FieldId' => 24, 'FieldValueId' => $FieldValueId, 'Text' => 'Material');
        	                pre($data);
        	                $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
        	                pre($resPrincipal);
        	                if($resPrincipal['httpCode'] == 200){
        	                    $queryUpdate = $db->update('module_vtex_products',
        	                        array('store_id', 'Id'),
        	                        array($storeId, $product['Id']),
        	                        array('specificationReturnCode' => $resPrincipal['httpCode']));
        	                }
    	               }
    	               
	               
	                }
	                $product['specifications'][] = $data;
	                
	            }
	            
	            
	            /**********************************************************************************************************/
 	            /********************************** Fomato do Bico ********************************************************/
 	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_3']) && !empty($dataProduct['custom_label_3'])){
	                $FieldValueId = '';
    	            $formatoBico = array(
    	                'Bico Fino' => 24965,
    	                'Fino' => 24965,
    	                'Bico Redondo' => 24966,
    	                'Redondo' => 24966,
    	                'Bico Quadrado' => 24967,
    	                'Quadrado' => 24967
    	            );
	                $values = explode(',', $dataProduct['custom_label_3']);
	                $valueName = trim(RemoveAcentos(trim($values[0])));
	                foreach($formatoBico as $text => $idVal){
	                    $valueText = trim(RemoveAcentos($text));
	                    if($valueName == $valueText){
	                        $FieldValueId = $idVal;
	                        break;
	                    }
	                }
	                if(!empty($FieldValueId)){
    	                $data = array('FieldId' => 29, 'FieldValueId' => $FieldValueId, 'Text' => 'Fomato do Bico');
    	                pre($data);
    	                $product['specifications'][] = $data;
    	                $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
    	                pre($resPrincipal);
    	                if($resPrincipal['httpCode'] == 200){
    	                    $queryUpdate = $db->update('module_vtex_products',
    	                        array('store_id', 'Id'),
    	                        array($storeId, $product['Id']),
    	                        array('specificationReturnCode' => $resPrincipal['httpCode']));
    	                }
	                }
	            }
	            
	            /**********************************************************************************************************/
	            /********************************** Fomato do Bico ********************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_3']) && !empty($dataProduct['custom_label_3'])){
	                $FieldValueId = '';

	                $formatoBico = array(
	                    'Bico Fino' => 24999,
	                    'Fino' => 24999,
	                    'Bico Redondo' => 25000,
	                    'Redondo' => 25000,
	                    'Bico Quadrado' => 25001,
	                    'Quadrado' => 25001
	                );
	                

	                $values = explode(',', $dataProduct['custom_label_3']);
	                $valueName = trim(RemoveAcentos(trim($values[0])));
	                foreach($formatoBico as $text => $idVal){
	                    $valueText = trim(RemoveAcentos($text));
	                    if($valueName == $valueText){
	                        $FieldValueId = $idVal;
	                        break;
	                    }
	                }
	                if(!empty($FieldValueId)){
	                    $data = array('FieldId' => 37, 'FieldValueId' => $FieldValueId, 'Text' => 'Fomato do Bico');
	                    pre($data);
	                    $product['specifications'][] = $data;
	                    $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
	                    pre($resPrincipal);
	                    if(!empty($resPrincipal['httpCode'])){
	                        $queryUpdate = $db->update('module_vtex_products',
	                            array('store_id', 'Id'),
	                            array($storeId, $product['Id']),
	                            array('specificationReturnCode' => $resPrincipal['httpCode']));
	                    }
	                }
	            }
	            
	            /**********************************************************************************************************/
	            /*********************************** Formato do Salto *****************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_4']) && !empty($dataProduct['custom_label_4'])){
	                $FieldValueId = '';
	                $formatoSalto = array(
	                    'Fino'=> 24972,
	                    'Agulha'=> 24972,
	                    'Grosso'=> 24973,
	                    'Robusto'=> 24973,
	                    'Plataforma'=> 24974,
	                    'Flatform'=> 24974,
	                    'Meia Pata'=> 24975,
	                    'Cone'=> 24976,
	                    'Anabela'=> 25030,
	                    'Quadrado'=> 25034,
	                    'Rasteiro'=> 25277
	                );
    	            $values = explode(',', $dataProduct['custom_label_4']);
    	            $valueName = trim(RemoveAcentos(trim($values[0])));
    	            foreach($formatoSalto as $text => $idVal){
    	                
    	                $valueText = trim(RemoveAcentos($text));
    	                if($valueName == $valueText){
    	                    $FieldValueId = $idVal;
    	                    break;
    	                }
    	            }
    	            if(!empty($FieldValueId)){
        	            $data = array('FieldId' => 31, 'FieldValueId' => $FieldValueId, 'Text' => 'Formato do Salto');
        	            pre($data);
        	            $product['specifications'][] = $data;
        	            $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
        	            pre($resPrincipal);
        	            if($resPrincipal['httpCode'] == 200){
        	                $queryUpdate = $db->update('module_vtex_products',
        	                    array('store_id', 'Id'),
        	                    array($storeId, $product['Id']),
        	                    array('specificationReturnCode' => $resPrincipal['httpCode']));
        	            }
    	            }
    	           
	            }
	            
	            /**********************************************************************************************************/
	            /*********************************** Formato do Salto *****************************************************/
	            /**********************************************************************************************************/
	            if(isset($dataProduct['custom_label_4']) && !empty($dataProduct['custom_label_4'])){
	                
	                $FieldValueId = '';
	                $formatoSalto = array(
	                    'Grosso'=> 25031,
	                    'Robusto'=> 25031,
	                    'Plataforma'=> 25032,
	                    'Flatform'=> 25032,
	                    'Anabela'=> 24977,
	                    'Quadrado'=> 25033,
	                    'Rasteiro'=> 25278
	                ); 
	                $values = explode(',', $dataProduct['custom_label_4']);
	                $valueName = trim(RemoveAcentos(trim($values[0])));
	                foreach($formatoSalto as $text => $idVal){
	                    
	                    $valueText = trim(RemoveAcentos($text));
	                    if($valueName == $valueText){
	                        $FieldValueId = $idVal;
	                        break;
	                    }
	                }
	                if(!empty($FieldValueId)){
	                    $data = array('FieldId' => 49, 'FieldValueId' => $FieldValueId, 'Text' => 'Formato do Salto');
	                    pre($data);
	                    $product['specifications'][] = $data;
	                    $resPrincipal = $vtex->rest->post("catalog/pvt/product/{$product['Id']}/specification", $data, $params = array());
	                    pre($resPrincipal);
	                    if($resPrincipal['httpCode'] == 200){
	                        $queryUpdate = $db->update('module_vtex_products',
	                            array('store_id', 'Id'),
	                            array($storeId, $product['Id']),
	                            array('specificationReturnCode' => $resPrincipal['httpCode']));
	                    }
	                }
	                
	            }
	            
                pre($product);
	       }
	       
	       ksort($dataLabels);
	       
	       pre($dataLabels);
	       
	       break;
	       
	       
	}
	
	
}