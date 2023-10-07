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
require_once $path .'/../../../Models/Products/PublicationsModel.php';
require_once $path .'/../Class/class-ConfigMws.php';
require_once $path .'/../Class/class-MWS.php';
require_once $path .'/../Models/API/SubmitFeedModel.php';
require_once $path .'/../Models/API/RecommendationsModel.php';
require_once $path .'/../Models/Products/GenerateProductDataXml.php';
// require_once $path .'/../Models/Products/DefaultGenerateProductDataXml.php';
require_once $path .'/../Models/Products/GenerateInventoryDataXml.php';
require_once $path .'/../Models/Products/GeneratePriceDataXml.php';
require_once $path .'/../Models/Map/AzAttributesModel.php';
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
function simpleXmlObjectToArray ( $xmlObject, $out = array () )
{
	foreach ( (array) $xmlObject as $index => $node )
		$out[$index] = ( is_object ( $node ) || is_array($node) )
		? simpleXmlObjectToArray ( $node )
		: $node;
		 
		return $out;
}
if(isset($storeId)){
    
    $db = new DbConnection();
    
    switch($action){
    	
        case "submit_feed_product":
           
            $submitFeedModel = new SubmitFeedModel($db, null, $storeId);
            
            $productDataXml = new GenerateProductDataXml($db, null, $storeId);
            
            $productDataXml->merchant_id = $submitFeedModel->site_id;
            
            $submitFeedModel->feed = $productDataXml->GetXml();
//             pre($submitFeedModel->feed);die;
            $responseProduct = $submitFeedModel->submitFeedProducts();
            
            echo "success|";
            
            break;
            
        case "submit_feed_inventory":
            
            $submitFeedModel = new SubmitFeedModel($db, null, $storeId);
            
            $inventoryDataXml = new GenerateInventoryDataXml($db, null, $storeId);
            
            $inventoryDataXml->merchant_id = $submitFeedModel->seller_id;
            
            $submitFeedModel->feed = $inventoryDataXml->GetXml();
            $responseInventory = $submitFeedModel->submitFeedInventory();
            
            echo "success|";
            
            break;
            
        case "submit_feed_price":
            
            $submitFeedModel = new SubmitFeedModel($db, null, $storeId);
            
            $priceDataXml = new GeneratePriceDataXml($db, null, $storeId);
            
            $priceDataXml->merchant_id = $submitFeedModel->seller_id;
            
            $submitFeedModel->feed = $priceDataXml->GetXml();
//             pre($submitFeedModel->feed);die;
            $responsePrice = $submitFeedModel->submitFeedPrice();
            
            echo "success|";
            
            break;
            
        case "results_feed_request":
            
            $submitFeedModel = new SubmitFeedModel($db, null, $storeId);
            
            
            $feedSubmissionId = isset($_REQUEST["feed_submission_id"]) && $_REQUEST["feed_submission_id"] != "" ? $_REQUEST["feed_submission_id"] : null ;
            
            if(isset($feedSubmissionId)){
                $feeds = array(
                    array("FeedSubmissionId" => $feedSubmissionId)
                    
                );
            }else{
                $feeds = $submitFeedModel->ListFeedSubmitted();
            }
            $error = array();
            foreach($feeds as $k => $id){
                
                $submitFeedModel->FeedSubmissionId = $id['FeedSubmissionId'];
                
                $res = $submitFeedModel->GetFeedSubmissionResultRequest();
                
                $status = isset($res->report->StatusCode) && !empty($res->report->StatusCode) ? $res->report->StatusCode : null ;
                
                if($status){
                	
                	foreach($res->report->Result as $key => $value){
                		$db->update('az_products_feed', 
                				array('store_id', 'sku'), 
                				array($storeId, trim($value->AdditionalInfo[0]->SKU[0])), 
                				array('result' => trim($value->ResultDescription)
                				));
                		
                		$error[trim($value->MessageID[0])] = array(
                				'product_id' => trim($value->MessageID[0]),
                				'sku' => trim($value->AdditionalInfo[0]->SKU[0]),
                				'message' =>json_encode($value->ResultDescription)
                		);
                	}
                }
                
            }
            break;
            
            
            
        case "submitted_feed":
            
            $submitFeedModel = new SubmitFeedModel($db, null, $storeId);
            
            $feedSubmissionInfoList = $submitFeedModel->getFeedSubmitted();
//             pre($feedSubmissionInfoList);
            foreach($feedSubmissionInfoList as $k => $feeds){
//             	pre($feeds);
                if(!empty($feeds)){
                    
                    foreach($feeds as $j => $feed){
//                     	pre($feed);
                        if(!empty($feed)){
                            $submitFeedModel->FeedSubmissionId = $feed->getFeedSubmissionId();
                            
                            $submitFeedModel->FeedType = $feed->getFeedType();
                           
                            $submitFeedModel->SubmittedDate = $feed->getSubmittedDate()->format("Y-m-d H:i:s");
                            
                            $submitFeedModel->FeedProcessingStatus = $feed->getFeedProcessingStatus();
                            
                            $submitFeedModel->StartedProcessingDate = $feed->getStartedProcessingDate()->format("Y-m-d H:i:s");
                            
                            $submitFeedModel->CompletedProcessingDate = $feed->getCompletedProcessingDate()->format("Y-m-d H:i:s");
                            
                            $submitFeedModel->Save();
                        }
                        
                    }
                }
            }
            
            echo "success|";
            
            break;
        
        case "Recommendations":
            
        	$recommendationsModel = new RecommendationsModel($db, null, $storeId);
            
          	$responseRecommendations = $recommendationsModel->ListRecommendationsRequest();
            
//           	pre($responseRecommendations);
           
           	echo "success|";
            
            
           	break;
            	 
        case "base":
           	$azBaseXsdModel = new AzBaseXsdModel($db, null, $storeId);
           	$azBaseXsdModel->LoadXsdBase();
           	$azBaseXsdModel->baseXsdComplexType();
//     		$azBaseXsdModel->baseXsdSimpleType();
            
           	break;
           	
        case "update_connection_product_feed":
        	
        	$connection = isset($_REQUEST['connection']) && !empty($_REQUEST['connection']) ? $_REQUEST['connection'] : null ;
        	
        	if(isset($connection)){
        	
	        	$productIds = is_array($productId) ? $productId : array($productId) ;
	        	 
	        	foreach($productIds as $ind => $id){
	        		 
	        		$parentId = getParentIdFromId($db, $storeId, $id);
	        		 
	        		if(!empty($parentId)){
	        	
	        			$sql = "SELECT id FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}'";
	        			$query = $db->query($sql);
	        			$products = $query->fetchAll(PDO::FETCH_ASSOC);
	        			foreach($products as $key => $product){
	        				
	        				$query = $db->update('az_products_feed', 
	        						array('store_id',  'product_id'),
	        						array($storeId, $product['id']),
	        						array('connection' =>  $connection,  'updated' => date('Y-m-d H:i:s')));
	        				
	        				if($query){
	        					
	        					$dataLog['update_connection_product_feed'] = array($connection);
	        					 
	        					$db->insert('products_log', array(
	        							'store_id' => $storeId,
	        							'product_id' => $product['id'],
	        							'description' => 'Atualização do Tipoe de Conexão do Produto no Feed de Exportação Amazon',
	        							'user' => $request,
	        							'created' => date('Y-m-d H:i:s'),
	        							'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	        					));
	        					
	        					echo "success|Conexão atualizada com sucesso!|{$connection}";
	        				}
	        				
	        			}
	        			
	        		}
	        		
	        	}
        	
        	}
        	
        	break;
        	
        	
            	 
        case "add_attribute_relationship":
           	$xsdName = $_REQUEST["xsd_name"];
           	$type = $_REQUEST["type"];
           	$choice = $_REQUEST["choice"];
           	$attributeId =$_REQUEST["attribute_id"];
           	$attribute =$_REQUEST["attribute"];
           	$azAttribute =$_REQUEST["az_attribute"];
           	$azAttributeType = $_REQUEST["az_attribute_type"];
            
           	$sqlVerify = "SELECT id FROM `az_attributes_relationship`
           	WHERE store_id = {$storeId}  AND az_attribute LIKE '{$azAttribute}'
           	AND xsdName LIKE '{$xsdName}' AND choice LIKE '{$choice}'";
           	$query = $db->query($sqlVerify);
           	$resVerify = $query->fetch(PDO::FETCH_ASSOC);
           	if(empty($resVerify['id'])){
           		$query = $db->insert('az_attributes_relationship', array(
           				'store_id' => $storeId,
           				'xsdName' => $xsdName,
           				'choice' => $choice,
           				'attribute_id' => $attributeId,
           				'attribute' => $attribute,
           				'az_attribute' => $azAttribute,
           				'az_attribute_type' => $azAttributeType,
           				'created' =>  date('Y-m-d H:i:s'),
           				'updated' => date('Y-m-d H:i:s')
            		));
           	}else{
            		 
           		if($type == 'remove'){
           			$sqlDelete = "DELETE FROM az_attributes_relationship WHERE store_id = {$storeId}  AND az_attribute LIKE '{$azAttribute}'
           			AND xsdName LIKE '{$xsdName}' AND choice LIKE '{$choice}'";
           			$queryDelete = $db->query($sqlDelete);
            				
           			if(!$queryDelete){
           				echo "error|Erro ao excluír relacionamento";
           			}else{
           				echo "success|Relacionamento removido com sucesso!";
           			}
           			exit;
           		}else{
            
           			$query = $db->update('az_attributes_relationship',
           					array('store_id', 'id'),
           					array($storeId, $resVerify['id']), array(
           							'attribute_id' => $attributeId,
           							'attribute' => $attribute,
           							'az_attribute' => $azAttribute,
           							'az_attribute_type' => $azAttributeType,
           							'updated' => date('Y-m-d H:i:s')
           					));
           			 
           		}
           	}
           	if($query){
           		echo "success|{$attribute}|Relacionamento cadastrado com successo!";
           	}
           
           
           	break;
           	
       	case "add_all_available_products":
       		
       		
       		
       		$sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND blocked = 'F'
       		AND parent_id IS NOT NULL AND parent_id > 1  AND EAN != '' AND EAN IS NOT NULL
       		AND id NOT IN (SELECT product_id as id FROM product_relational WHERE store_id = {$storeId})";
       		if($storeId == 6){
	       		$sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND blocked = 'F' AND quantity > 0
	       		AND parent_id IS NOT NULL AND parent_id > 1  AND EAN != '' AND EAN IS NOT NULL 
	       		AND id NOT IN (SELECT product_id as id FROM product_relational WHERE store_id = {$storeId})
	       		AND id NOT IN (SELECT id FROM available_products WHERE store_id = {$storeId} AND ean LIKE '1000000%')";
       		}
       		if($storeId == 4){
       			$sql = "SELECT * FROM `available_products` WHERE store_id = {$storeId} 
       			AND ean != '' and quantity > 2 AND parent_id IS NOT NULL
	       		AND id NOT IN (SELECT product_id as id FROM product_relational WHERE store_id = {$storeId})";
       		}
       		$query = $db->query($sql);
       		$products = $query->fetchAll(PDO::FETCH_ASSOC);
       		$updateError = $insertError = $updated = $inserted = 0;
       		foreach($products as $key => $product){
       			
       			$sql = "SELECT  az_category_relationship.*  FROM  az_category_relationship
       			WHERE az_category_relationship.store_id = {$storeId} AND az_category_relationship.category LIKE '{$product['category']}'";
       			 
       			$query = $db->query($sql);
       			$azCategoryRel = $query->fetch(PDO::FETCH_ASSOC);
       			 
       			$sqlVerify = "SELECT  az_products_feed.*  FROM  az_products_feed
       			WHERE az_products_feed.store_id = {$storeId} AND az_products_feed.product_id = {$product['id']}";
       			$queryVerify = $db->query($sqlVerify);
       			$azProductVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
       			 
       			if(!isset($azProductVerify['id'])){
       				$data = array(
       						'store_id' => $storeId,
       						'product_id' => $product['id'],
       						'parent_id' => $product['parent_id'],
       						'ean' =>$product['ean'],
       						'sku' => $product['sku'],
       						'title' => $product['title'],
       						'xsdName' => $azCategoryRel['xsd'],
       						'choice' => $azCategoryRel['choice'],
       						'set_attribute' => $azCategoryRel['set_attribute'],
       						'category_id' => $azCategoryRel['tree_id'],
       						'category' => $azCategoryRel['hierarchy'],
       						'created' => date("Y-m-d H:i:s"),
       						'updated' => date("Y-m-d H:i:s")
       			
       				);
       				$query = $db->insert('az_products_feed', $data);
       			
       				if($query){
       					$dataLog['add_products_feed_amazon'] = $data;
       			
//        					$db->insert('products_log', array(
//        							'store_id' => $storeId,
//        							'product_id' => $product['id'],
//        							'description' => 'Novo Produto Cadastrado no Feed de Exportação Amazon',
//        							'user' => $request,
//        							'created' => date('Y-m-d H:i:s'),
//        							'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
//        					));
       					$inserted++;
       				}else{
       					$insertError++;
       				}
       				 
       			}else{
       				$data = array(
       						'product_id' => $product['id'],
       						'parent_id' => $product['parent_id'],
       						'ean' =>$product['ean'],
       						'sku' => $product['sku'],
       						'title' => $product['title'],
       						'xsdName' => $azCategoryRel['xsd'],
       						'choice' => $azCategoryRel['choice'],
       						'set_attribute' => $azCategoryRel['set_attribute'],
       						'category_id' => $azCategoryRel['tree_id'],
       						'category' => $azCategoryRel['hierarchy'],
       						'updated' => date("Y-m-d H:i:s")
       			
       				);
       				$query = $db->update('az_products_feed', 'id', $azProductVerify['id'], $data);
       				if($query){
       					$dataLog['update_products_feed_amazon'] = array(
       							'before' => $azProductVerify,
       							'after' => $data
       					);
       					 
//        					$db->insert('products_log', array(
//        							'store_id' => $storeId,
//        							'product_id' => $product['id'],
//        							'description' => 'Atualização do Produto no Feed de Exportação Amazon',
//        							'user' => $request,
//        							'created' => date('Y-m-d H:i:s'),
//        							'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
//        					));
       					$updated++;
       				}else{
       					$updateError++;
       				}
       			
       			}
       			
       		}
       		
       		if(!$query){
       			$adicionarError = $insertError > 0 ? "Adicionar {$insertError}" : '';
       			$atualizarError = $updateError > 0 ? "Atualizar {$updateError}" : '';
       			echo "error|Não foi possivel {$adicionar} {$atualizar} produtos no Feed";
       		
       		}else{
       			$adicionar = $inserted > 0 ? "Adicionados {$inserted}" : '';
       			$atualizar = $updated > 0 ? "Atualizados {$updated}" : '';
       			echo "success|Produto {$adicionar} {$atualizar} com sucesso!";
       		}
       		
       		break;
       		
        case "add_products_feed":
        	
        	$productIds = is_array($productId) ? $productId : array($productId) ;
        	
        	foreach($productIds as $ind => $id){
        	
        		$parentId = getParentIdFromId($db, $storeId, $id);
        	
        		if(!empty($parentId)){
        			 
        			$sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}' AND EAN != ''";
        			$query = $db->query($sql);
        			$products = $query->fetchAll(PDO::FETCH_ASSOC);
        			foreach($products as $key => $product){
//         				$images = getUrlImageFromParentId($db, $storeId,$product['parent_id']);
//         				if(!empty($images[0])){
			           	$sql = "SELECT  az_category_relationship.*  FROM  az_category_relationship 
			           	WHERE az_category_relationship.store_id = {$storeId} AND az_category_relationship.category LIKE '{$product['category']}'";
			           	
			           	$query = $db->query($sql);
			           	$azCategoryRel = $query->fetch(PDO::FETCH_ASSOC);
			
			           	if(!isset($azCategoryRel)){
			           		echo "error|Não foi possivel publicar o produto, verifique o relacionamento de categorias no menu Amazon > Mapear Categoria";
			           		exit;
			           	}
			           	
			           	$sqlVerify = "SELECT  az_products_feed.*  FROM  az_products_feed
			           	WHERE az_products_feed.store_id = {$storeId} AND az_products_feed.product_id = {$product['id']}";
			           	$queryVerify = $db->query($sqlVerify);
			           	$azProductVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
			           	
			           	if(!isset($azProductVerify['id'])){
			           		$data = array(
			           				'store_id' => $storeId,
			           				'product_id' => $product['id'],
			           				'parent_id' => $product['parent_id'],
			           				'ean' =>$product['ean'],
			           				'sku' => $product['sku'],
			           				'title' => $product['title'],
			           				'xsdName' => $azCategoryRel['xsd'],
			           				'choice' => $azCategoryRel['choice'],
			           				'set_attribute' => $azCategoryRel['set_attribute'],
			           				'category_id' => $azCategoryRel['tree_id'],
       								'category' => $azCategoryRel['hierarchy'],
			           				'created' => date("Y-m-d H:i:s"),
			           				'updated' => date("Y-m-d H:i:s")
			           				
			           			);
			           		$query = $db->insert('az_products_feed', $data);
			           		
			           		if($query){
			           			$dataLog['add_products_feed_amazon'] = $data;
			           			 
			           			$db->insert('products_log', array(
			           					'store_id' => $storeId,
			           					'product_id' => $product['id'],
			           					'description' => 'Novo Produto Cadastrado no Feed de Exportação Amazon',
			           					'user' => $request,
			           					'created' => date('Y-m-d H:i:s'),
			           					'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
			           			));
			           		}
		           		
			           	}else{
			           		$data = array(
			           				'product_id' => $product['id'],
			           				'parent_id' => $product['parent_id'],
			           				'ean' =>$product['ean'],
			           				'sku' => $product['sku'],
			           				'title' => $product['title'],
			           				'xsdName' => $azCategoryRel['xsd'],
			           				'choice' => $azCategoryRel['choice'],
			           				'set_attribute' => $azCategoryRel['set_attribute'],
			           				'category_id' => $azCategoryRel['tree_id'],
       								'category' => $azCategoryRel['hierarchy'],
			           				'updated' => date("Y-m-d H:i:s")
			           		
			           		);
			           		$query = $db->update('az_products_feed', 'id', $azProductVerify['id'], $data);
			           		if($query){
				           		$dataLog['update_products_feed_amazon'] = array(
				           				'before' => $azProductVerify,
				           				'after' => $data
				           		);
				           		
				           		$db->insert('products_log', array(
				           				'store_id' => $storeId,
				           				'product_id' => $product['id'],
				           				'description' => 'Atualização do Produto no Feed de Exportação Amazon',
				           				'user' => $request,
				           				'created' => date('Y-m-d H:i:s'),
				           				'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
				           		));
			           		}
			           		
			           	}
			           
			           	
			           	if(!$query){
			           		echo "error|Não foi possivel publicar o produto, verifique o relacionamento de categorias no menu Amazon > Mapear Categoria";
			           		
			           	}else{
			           		echo "success|Produto atualizado com sucesso!";
			           	}
        				
        			
        			}
        			
        		}
        		
        	}
           
           	break;
           	
           	case "delete_amazon_product":
           		 
           		$productIds = is_array($productId) ? $productId : array($productId) ;
           		 
           		foreach($productIds as $ind => $id){
           			 
           			$parentId = getParentIdFromId($db, $storeId, $id);
           			 
           			if(!empty($parentId)){
           	
           				$sql = "SELECT id FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}' ";
           				$query = $db->query($sql);
           				$products = $query->fetchAll(PDO::FETCH_ASSOC);
           				foreach($products as $key => $product){
           			
           					$sqlDelete = "DELETE FROM  az_products_feed WHERE az_products_feed.store_id = {$storeId} AND az_products_feed.product_id = {$product['id']}";
           					$queryDelete = $db->query($sqlDelete);
           					if($queryDelete){
// 	           					$db->insert('products_log', array(
// 	           							'store_id' => $storeId,
// 	           							'product_id' => $product['id'],
// 	           							'description' => 'Removido Produto no Feed de Exportação Amazon',
// 	           							'user' => $request,
// 	           							'created' => date('Y-m-d H:i:s'),
// 	           					));
           					}
	           			}
	           			
	           			if(!$queryDelete){
	           				echo "error|Não foi removido o produto";
	           			
	           			}else{
	           				echo "success|Produto removido com sucesso!";
	           			}
	           			 
	           		}
	           	
	           	}
           	 
           	break;
    	}
}
 