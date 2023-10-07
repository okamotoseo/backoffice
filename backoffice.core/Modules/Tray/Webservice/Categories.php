<?php

set_time_limit ( 300 );
setlocale(LC_ALL, "pt_BR.utf8");
$path = dirname(__FILE__);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/ProductDescriptionModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../Class/class-Tray.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Api/CategoriesRestModel.php';
require_once $path .'/../Models/Api/CaracteristicasRestModel.php';

require_once $path .'/../Models/Api/ItemsRestModel.php';
// require_once $path .'/../Models/Adverts/ItemsModel.php';
// require_once $path .'/../Models/Map/MlCategoryModel.php';
// require_once $path .'/../Models/Price/PriceModel.php';
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
    
    
    switch($action){
    	
    	case "remove_categories_tray":
    		
    		$sql = "DELETE FROM `module_tray_categories` WHERE store_id = {$storeId}";
    		$query = $db->query($sql);
    		if($query){
    			echo "success|";
    		}
    		
    		break;
    	
    	case "add_category_relationship":
    	
    		$trayParentId = $_REQUEST['tray_parent_id'];
    		$trayCategoryId = $_REQUEST['tray_category_id'];
    		$categoryId = $_REQUEST['category_id'];
    		$parentId = $_REQUEST['parent_id'];
    		 
    		if(!empty($trayCategoryId)){
    			$sql = "SELECT hierarchy FROM `category` WHERE store_id = {$storeId}
    			AND id = {$categoryId} and parent_id = {$parentId}";
    			$query = $db->query($sql);
    			$category = $query->fetch(PDO::FETCH_ASSOC);
    			 
    			if(!empty($category['hierarchy'])){
    			
	    			$sqlCategory = "SELECT * FROM `module_tray_categories` WHERE store_id = {$storeId}
	    			AND parent_id = '{$parentId}' AND category_id = '{$categoryId}' AND hierarchy LIKE '{$category['hierarchy']}'";
	    			$queryCategory = $db->query($sqlCategory);
	    			$categories = $queryCategory->fetch(PDO::FETCH_ASSOC);
	    	
	    			if(isset($categories['category_id']) && !empty($categories['category_id'])){
	    			
	    				/**
	    				 * if exists clean ohters relationships to add new
	    				 * @var unknown $query
	    				 */
	    				$query = $db->update('module_tray_categories',
	    						array('store_id', 'id'),
	    						array($storeId, $categories['id']),
	    						array('parent_id' => '', 'category_id' => '', 'hierarchy' => '')
	    						);
	    					 
	    			}else{
	    					 
	    				$sqltray = "SELECT * FROM `module_tray_categories` WHERE store_id = {$storeId}
	    				AND id_category = {$trayCategoryId} and id_parent = {$trayParentId}";
	    				$queryTray = $db->query($sqltray);
	    				$categoryTray = $queryTray->fetch(PDO::FETCH_ASSOC);
	    				if(empty($categoryTray['category_id'])){
	    					 
	    					$query = $db->update('module_tray_categories',
	    							array('store_id', 'id'),
	    							array($storeId, $categoryTray['id']),
	    							array(
	    									'category_id' => $categoryId,
	    									'parent_id' => $parentId,
	    									'hierarchy' => $category['hierarchy']
	    							));
	    	    						 
	    				}else{
	    	
	    					if($categoryTray['category_id'] != $categoryId){
	    						//if exist magento category relationship add another one
	    						$query = $db->insert('module_tray_categories', array(
	    								'store_id' => $storeId,
	    								'category_id' => $categoryId,
	    								'parent_id' => $parentId,
	    								'hierarchy' => $category['hierarchy'],
	    								'id_category' => $categoryTray['id_category'],
	    								'id_parent' => $categoryTray['id_parent'],
	    								'tray_hierarchy' => $categoryTray['tray_hierarchy'],
	    								'tray_category' => $categoryTray['tray_category'],
	    						));
	    						 
	    					}
	    	    		}
	    				 
	    			}
	    			if($query){
	    				echo "success|Relacionamento cadastrado com successo!";
	    			}else{
	    				echo "error|Erro ao relacionar categoria";
	    			}
    			}
    		}
    		 
    		break;
    	
    	case "import_categories_hierarchy":
    		
    		$categoriesRestModel = new CategoriesRestModel($db, null, $storeId);
	    	
    		$categories = $categoriesRestModel->getCategoriesList();
    		
    		$trayParentId = 0;
    		
    		foreach($categories as $key => $category){
    			
    			$categoryId = $parentId = null;
    			$hierarchy = '';
    			
    			echo $category['parent_id']."/".$category['id']." - ".$category['name']."<br>";
    			
    			$trayCategoryId = $category['id'];
    			$trayParentId = !empty($category['parent_id']) ? $category['parent_id'] : 0 ;
    			$trayCategoryName = $category['name'];
    			
    			if($trayParentId == 0){
    			
    				$parentId = 0;
    				$trayHierarchy = friendlyText($trayCategoryName);
    				$hierarchy = friendlyText($trayCategoryName);
    			
    			}
    			
    			if(!isset($parentId)){
    				
    				$sqlParent = "SELECT * FROM module_tray_categories WHERE store_id = {$storeId} AND id_category = '{$trayParentId}'";
    				$query = $db->query($sqlParent);
    				$resParent = $query->fetch(PDO::FETCH_ASSOC);
    				$parentId = isset($resParent['category_id']) && !empty($resParent['category_id']) ? $resParent['category_id'] : null ;
    				$trayHierarchy = !empty($resParent['hierarchy']) ? $resParent['hierarchy'].' > '. friendlyText($trayCategoryName) : '' ;
    				
    			}
    			
    			
    			if(isset($parentId)){
    				 
    				//Busca nas configurações do modulo se vai importar as categorias
    				$sql = "SELECT hierarchy FROM category WHERE `id`= {$parentId} AND store_id = {$storeId}";
    				$query = $db->query($sql);
    				$row = $query->fetch(PDO::FETCH_OBJ);
    				$hierarchy = !empty($row->hierarchy) ? $row->hierarchy.' > '. friendlyText($trayCategoryName) :  friendlyText($trayCategoryName);
    				 
    				 
    				// verifica se existe
    				$verify = ucwords(mb_strtolower($trayCategoryName));
    				$sql = "SELECT * FROM `category` WHERE `store_id` = {$storeId}
    				AND category LIKE '".addslashes($verify)."' AND parent_id = {$parentId}";
    				$query = $db->query($sql);
    				$res = $query->fetch(PDO::FETCH_ASSOC);
    				if(!isset($res['category'])){
    					/**
    					 * add here script to import categories 
    					 */
    				}else{
    					$categoryId = $res['id'];
    				}
    				 
    			}
    			
    			$sqlRel = "SELECT * FROM module_tray_categories
    			WHERE store_id = {$storeId} AND id_category = '{$trayCategoryId}'";
    			$queryRel = $db->query($sqlRel);
    			$resRel = $queryRel->fetch(PDO::FETCH_ASSOC);
    			
    			if(empty($resRel['id_category'])){
    				 
    				if(!isset($categoryId) OR empty($categoryId)){
    					$hierarchy = '';
    				}
    				echo 'insert';
    				pre(array(
    						'store_id' => $storeId,
    						'category_id' => $categoryId,
    						'parent_id' => $parentId,
    						'hierarchy' => $hierarchy,
    						'id_category' => $trayCategoryId,
    						'id_parent' => $trayParentId,
    						'tray_hierarchy' => $trayHierarchy,
    						'tray_category' => $trayCategoryName
    				));
    				
    				$query = $db->insert('module_tray_categories', array(
    						'store_id' => $storeId,
    						'category_id' => $categoryId,
    						'parent_id' => $parentId,
    						'hierarchy' => $hierarchy,
    						'id_category' => $trayCategoryId,
    						'id_parent' => $trayParentId,
    						'tray_hierarchy' => $trayHierarchy,
    						'tray_category' => $trayCategoryName
    				));
    				 
    			}
    			
    			else{
    				echo 'update';
    				pre(array('hierarchy' => $trayHierarchy));
    				$query = $db->update('module_tray_categories',
    						array('store_id', 'id'),
    						array($storeId, $resRel['id']),
    						array('tray_hierarchy' => $trayHierarchy,
    							 'tray_category' => $trayCategoryName
    						));
    				
    			}
    			
    		}
    		
    		if($query){
    			echo "success|Categorias importadas com successo!";
    		}else{
    			echo "error|Erro ao importar categoria";
    		}
    		
    		
    		break;
        
    	case "export_categories_hierarchy":
    		 
    		$categoriesRestModel = new CategoriesRestModel($db, null, $storeId);
    	
    		$query = $db->query("SELECT * FROM category WHERE store_id = {$storeId} AND type LIKE 'default' AND
            id NOT IN (SELECT category_id FROM module_tray_categories WHERE store_id = {$storeId}) 
            ORDER BY id ASC");
//     		AND
//     		hierarchy IN (SELECT category FROM available_products WHERE store_id = {$storeId} AND category != '' ) 
//     		$query = $db->query("SELECT * FROM category WHERE store_id = {$storeId}
//     		AND type LIKE 'default' AND hierarchy like 'Elétrica%'");
    		$categories = $query->fetchAll(PDO::FETCH_ASSOC);
    		foreach($categories as $key => $category){
    			$property = array();
    	 
    			$attributes =  array();
     	
    			if(!empty($category['set_attribute_id'])){
    				$sql = "SELECT attributes.attribute FROM set_attributes_relationship
    				LEFT JOIN attributes ON attributes.id = set_attributes_relationship.attribute_id
    				WHERE set_attributes_relationship.store_id = {$storeId} AND set_attributes_relationship.set_attribute_id = {$category['set_attribute_id']}";
    				$queryAttr = $db->query($sql);
    				$attributes = $queryAttr->fetchAll(PDO::FETCH_ASSOC);
    			}
    	
    	
    			/**
    			 * add here default attributes.
    			 */
    			$attributes[]['attribute'] = 'Cor';
    			if($storeId == 4){
    			     $attributes[]['attribute'] = 'Voltagem';
    			}
    			
    			$attributes[]['attribute'] = 'Referencia';
    			$attributes[]['attribute'] = 'Modelo';
    			 
    			$sql = "SELECT  distinct name as attribute, attribute_id FROM attributes_values WHERE store_id = {$storeId} AND product_id IN (
    			SELECT id FROM available_products WHERE store_id = {$storeId} AND category LIKE '{$category['hierarchy']}'
    			) group by name";
    			$queryAttr = $db->query($sql);
    			$attributesFromValues = $queryAttr->fetchAll(PDO::FETCH_ASSOC);
//     			pre($attributesFromValues);
    			if(empty($attributes[0])){
    				$attributes = $attributesFromValues;
    			}else{
    				if(!empty($attributesFromValues[0])){
    					foreach($attributesFromValues as $k => $attributeFromValue){
    						$exist = false;
    						foreach($attributes as $j => $attribute){
    							if(strtolower(trim($attributeFromValue['attribute'])) == strtolower(trim($attribute['attribute']))){
    								$exist = true;
    							}
    						}
    						if(!$exist){
    							$attributes[]['attribute'] = $attributeFromValue['attribute'];
    						}
    					}
    				}
    			}
//     			pre($attributes);
    			if(!empty($attributes[0])){
    				foreach($attributes as $i => $attr){
//     				    $attr['attribute'] = substr($attr['attribute'], 0, 15); 
    				    $attr['attribute'] = ucfirst(mb_strtolower($attr['attribute'], 'UTF-8'));
    					$remove = false;
    					if($remove == false && strlen($attr['attribute']) > 15){
    					    $remove = true; 
    					}
//     					if($remove === false && count($property) <=  25){
//     					if(!$remove){
//     					    if(trim($attr['attribute']) != ''){
//     					        $property[] = trim($attr['attribute']);
//     					    }
//     					}
    					
    					if(!$remove){
//     				    if(count($property) <=  20){
    					    if(trim($attr['attribute']) != ''){
    					        $property[] = trim($attr['attribute']);
    					    }
    					}
    					
    				}
    			}
//     			pre($property);
    			
    			$sqlParent = "SELECT id_category FROM module_tray_categories WHERE store_id = {$storeId}
    			AND category_id = {$category['parent_id']}";
    			$queryParent = $db->query($sqlParent);
    			$categoryParent = $queryParent->fetch(PDO::FETCH_ASSOC); 

    			$idParent = isset($categoryParent['id_category']) ? $categoryParent['id_category'] : '' ;
//     			$idParent = $categoryParent['id_category']; 
    			
    			$data["Category"]["name"] = $category['category'];
//     			$data["Category"]["description"] = !empty($category['description']) ? substr($category['description'], 0, 30) : substr($category['category'], 0, 30) ;
    			$data["Category"]["description"] = $category['category'];
    			$data["Category"]["title"] = $category['category'];
    			$data["Category"]["parent_id"] = $idParent;
    			$properties = array();
    			foreach($property as $k => $prop){
    			    $properties[] = ucfirst(mb_strtolower($prop, 'UTF-8'));
    			}
    			$data["Category"]["property"] = $properties;
    			$categoriesRestModel->categoryData = $data;
    			pre($data);
    			
//     			pre($properties);  
//     			pre($categoriesRestModel);
    			$result = $categoriesRestModel->postCategory();
    			pre($result);
    			if(isset($result['body']['id'])){
    				$idCategory = $result['body']['id'];
    				$parentId = $category['parent_id'] == 0 ? $category['id'] : $category['parent_id'] ;
    				$dataInsert = array(
    				    'store_id' => $storeId,
    				    'category_id' => $category['id'],
    				    'parent_id' =>$parentId,
    				    'hierarchy' => $category['hierarchy'],
    				    'id_category' => $idCategory,
    				    'id_parent' => $idParent,
    				    'tray_hierarchy' =>  $category['hierarchy'],
    				    'tray_category' => $category['category'],
    				    'code' => $result['body']['code'],
    				    'message' => $result['body']['message']
    				);
//     				pre($dataInsert);
    				$res = $db->insert('module_tray_categories', $dataInsert);
    				
    	
    			}else{
    				 
//     				pre($result);
    				 
    			}
    	
    		}
    	
    	
    		break;
    		
    		
    		case "update_attributes_categories":
    		    
    		    $categoryId = isset($_REQUEST["category_id"]) && $_REQUEST["category_id"] != "" ? $_REQUEST["category_id"] : null ;
    		    
    			$categoriesRestModel = new CategoriesRestModel($db, null, $storeId);
    			
    			$sql = "SELECT category.*, module_tray_categories.id_category FROM category 
    			RIGHT JOIN module_tray_categories ON category.id = module_tray_categories.category_id 
    			WHERE category.store_id = {$storeId} AND category.type LIKE 'default'   ORDER BY category.parent_id ASC";
    			//AND category.hierarchy LIKE 'Mesa Posta > Pratos E Sousplat'
    			if(!empty($categoryId)){
//     				$sql = "SELECT * FROM category WHERE store_id = {$storeId} AND type LIKE 'default' AND id  = {$categoryId}";
    				$sql = "SELECT category.*, module_tray_categories.id_category FROM category
        			RIGHT JOIN module_tray_categories ON category.id = module_tray_categories.category_id
        			WHERE category.store_id = {$storeId} AND category.id = {$categoryId}";
    			}
    			
    			$log = array();
    			$query = $db->query($sql);
    			$categories = $query->fetchAll(PDO::FETCH_ASSOC);
//     			pre($categories);
    			foreach($categories as $key => $category){
    			    
    				if(empty($category['id_category'])){
    					echo 'error|Categoria não localizada';
//     					continue;
    				}
    				 
    				$property = array();
    				$attributes =  array();
    				$removeAttrsRoot = array();
    				if(!empty($category['set_attribute_id'])){
    					$sql = "SELECT attributes.attribute FROM set_attributes_relationship
    					LEFT JOIN attributes ON attributes.id = set_attributes_relationship.attribute_id
    					WHERE set_attributes_relationship.store_id = {$storeId} 
    					AND set_attributes_relationship.set_attribute_id = {$category['set_attribute_id']}";
    					$queryAttr = $db->query($sql);
    					$attributes = $queryAttr->fetchAll(PDO::FETCH_ASSOC);
    				} 
    				
//     				pre($attributes);   
    				
//     				if(!empty($category['hierarchy'])){
//     				    $sqlMl = "SELECT distinct ml_attributes_required.name as attribute FROM ml_category_relationship
//     					LEFT JOIN ml_attributes_required 
//                         ON ml_category_relationship.category_id = ml_attributes_required.category_id
//     					WHERE ml_category_relationship.store_id = {$storeId}
//     					AND ml_category_relationship.category LIKE '{$category['hierarchy']}'";
//     				    $queryAttrMl = $db->query($sqlMl);
//     				    $attributesMl = $queryAttrMl->fetchAll(PDO::FETCH_ASSOC);
//     				}
    				
//     				$result = array_merge($attributes, $attributesMl);
    				
//     				pre($result);die;
    				/**
    				 * add here default attributes.
    				 * so pode adicionar se remover da erro 
    				 */
    				
	    			$attributes[]['attribute'] = 'Cor';
	    			if($storeId == 4){
	    			    $attributes[]['attribute'] = 'Voltagem';
	    			}
	    			$attributes[]['attribute'] = 'Referencia';
	    			$attributes[]['attribute'] = 'Modelo';
	    			$attributes[]['attribute'] = 'SKU';
// 	    			$attributes[]['attribute'] = 'Coleção';
	    			
	    			if($storeId != 6){
		    			if($category['parent_id'] != 0){
		    				$removeAttrsRoot = $attributes;
		    				$attributes =  array();
	    				}
	    			}
	    			
	    			
    				$sql = "SELECT  name as attribute FROM attributes_values WHERE store_id = {$storeId}  AND product_id IN (
    				SELECT id as product_id FROM available_products WHERE store_id = {$storeId} AND category LIKE '{$category['hierarchy']}'
    				) group by attribute"; 
    				
    				$queryAttr = $db->query($sql);
    				$attributesFromValues = $queryAttr->fetchAll(PDO::FETCH_ASSOC);
    				if(empty($attributes[0]['attribute'])){
    					if(!empty($removeAttrsRoot[0]['attribute'])){
	    					if(!empty($attributesFromValues[0])){
	    						foreach($attributesFromValues as $k => $attributeFromValue){
	    							foreach($removeAttrsRoot as $j => $removeAttrRoot){
	    								if(mb_strtolower(trim($attributeFromValue['attribute'])) == mb_strtolower(trim($removeAttrRoot['attribute']))){
// 	    										unset($attributesFromValues[$k]);
	    								}
	    							}
	    						}
	    					}
    					}
    					$attributes = $attributesFromValues;
    					
    				}else{
    					if(!empty($attributesFromValues[0])){
    						 
    						foreach($attributesFromValues as $k => $attributeFromValue){
//     						    pre($attributeFromValue);
    							$exist = false;
    							foreach($attributes as $j => $attribute){
    							    if(mb_strtolower(trim($attributeFromValue['attribute'])) == mb_strtolower(trim($attribute['attribute']))){
    									$exist = true;
    								}
    							}
    							if(!$exist){
    								$attributes[]['attribute'] = $attributeFromValue['attribute'];
    							}
    						}
    					}
    				}
    				
//     				pre($attributes);
    				$countProp = 0;
    				$property = array();
    				if(!empty($attributes[0])){
    				    
    				    foreach($attributes as $i => $attr){
    			            if(trim($attr['attribute']) != ''){
    			                
    			                $attr['attribute'] = ucfirst(mb_strtolower(trim($attr['attribute']), 'UTF-8'));
    			                $remove = false;
//     			                if($remove == false && strlen($attr['attribute']) > 30){
//     			                    $remove = true;
//     			                }

//     			                if($countProp > 1){
//     			                     $remove = true;
//     			                }
//     			                if(!$remove){
    			                    
//     			                    $attr['attribute'] = str_replace('/', ' ', $attr['attribute']);
//     			                    $propertyUTF8 = ucfirst(mb_strtolower(trim($attr['attribute']), 'UTF-8'));
//     			                    $property[] = iconv("utf-8", "ascii//TRANSLIT", $propertyUTF8);
    			                    
    			                    $property[] = ucfirst(mb_strtolower(trim($attr['attribute']), 'UTF-8'));
//     			                    $countProp ++;
//     			                }
    			            }
    				    }
    				}
//     				unset($property[1]);
    				$property = array_unique($property);
    				pre($property);
    				 
    				$sqlParent = "SELECT id_category FROM module_tray_categories WHERE store_id = {$storeId}
    				AND parent_id = {$category['parent_id']}";
    				$queryParent = $db->query($sqlParent);
    				$categoryParent = $queryParent->fetch(PDO::FETCH_ASSOC);
    				 
    				$idParent = isset($categoryParent['id_category']) ? $categoryParent['id_category'] : '' ;
    				 
    				
    				
    				/**
    				 *Adicionar açnao para atualizar outras informações 
    				 */
//     				$data["Category"]["name"] = $category['category'];
//     				$data["Category"]["description"] = $category['description'];
//     				$data["Category"]["title"] = $category['category'];
//     				$data["Category"]["parent_id"] = $idParent;
				    $properties = array();
				    foreach($property as $k => $prop){
				        $properties[] = ucfirst(mb_strtolower($prop, 'UTF-8'));
				    }
				    $data["Category"]["property"] = $properties;
    				$categoriesRestModel->categoryData = $data;
    				$categoriesRestModel->category_id = $category['id_category'];
//     				pre($categoriesRestModel);

    				$result = $categoriesRestModel->putCategory();
    				pre($result);
//         				if($result['httpCode'] != 200){
//         					$log[] = array('send' => $categoriesRestModel,'result' => $result);
//         				}
//         				die;
    			}
    			
    			
    			if(empty($log[0])){
    				echo "success|Características da categoria atualizado com sucesso";
    			}else{
    				echo "error|".json_encode($log, JSON_PRETTY_PRINT);
    			}
    			 
    			 
    			break;
    	
    	
        
        
        	
    }
    
}