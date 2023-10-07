<?php
$path = dirname(__FILE__);
ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Magento2.php';
require_once $path .'/../Class/class-REST.php';
require_once $path .'/../Models/Catalog/AttributesModel.php';
require_once $path .'/../Models/Catalog/AttributeSetModel.php';
require_once $path .'/../Models/Products/SetAttributesRelationshipModel.php';
require_once $path .'/../Models/Products/AttributesRelationshipModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$accountId = isset($_REQUEST["account_id"]) && $_REQUEST["account_id"] != "" ? intval($_REQUEST["account_id"]) : null ;
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
    if(isset($_SERVER ['argv'] [3])){
        $paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
        $accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
    }
    
    $request = "System";
    
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
	switch($action){
		case "get_group_id":
			$catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
// 			
// 			$catalogAttributeSetModel->filters[] = array('field' => 'attribute_group_name', 'value' => '', 'condition_type' => 'neq' );
			$catalogAttributeSetModel->filters[] = array('field' => 'attribute_set_id', 'value' => 16, 'condition_type' => 'eq' );
	        $catalogAttributeSetModel->filters[] = array('field' => 'attribute_group_name', 'value' => 'General', 'condition_type' => 'eq' );
	        $attributeGroup = $catalogAttributeSetModel->catalogSetAttributesGroups();
			pre($attributeGroup);
			if(isset($attributeGroup['body']->items)){
				echo $attributeGroupIdGeneral =  $attributeGroup['body']->items[0]->attribute_group_id;
			}die;
			
				
				break;
	    
// 	    case "remove_attribute_magento": 
// 	        $attributeId = $_REQUEST['attribute_id'];
	        
// 	        $attributeModel = new AttributesModel($db, null, $storeId);
	        
	        
// 	        $sqlVerify = "SELECT * FROM mg2_attributes_relationship WHERE store_id = {$storeId} AND attribute_id = '{$attributeId}' ";
// 	        $queryVerify = $db->query($sqlVerify);
// 	        $attrVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	        
// 	        if(!empty($attrVerify['attribute_id'])){
	           
// 	            $attributeModel->attribute_id = $attrVerify['attribute_id'];
	            
//     	        if($attributeModel->catalogProductAttributeRemove()){
    	            
//     	            $sql = "DELETE FROM mg2_attributes_relationship WHERE store_id = {$storeId} AND attribute_id = {$attributeId}";
//     	            $query = $db->query($sql);
    	            
//     	        }
    	        
// 	        }
// 	        if(!$query){
	            
// 	            echo  "error";die;
	            
// 	        }
// 	        echo "success";
// 	        break;
	    
            /**
             * Import attributes from Magento2 whithout creating attributes 
             * relationship in default sysplace set_attribute_relationship
             * Import to mg2_attributes_relationship
             */
	    case "import_product_attributes":
	        
	    	$catalogAttributesModel = new AttributesModel($db, null, $storeId);
	        $attributesRelationshipModel = new AttributesRelationshipModel($db, null, $storeId);
	        $setAttributesRelationshipModel = new SetAttributesRelationshipModel($db, null, $storeId);
	        $catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
	        $catalogAttributeSetModel->filters[] = array('field' => 'attribute_set_name', 'value' => '', 'condition_type' => 'neq' );
	        
// 	        $catalogAttributeSetModel->filters[] = array('field' => 'attribute_set_id', 'value' => '27', 'condition_type' => 'eq' );
	      
	        $attributeSet = $catalogAttributeSetModel->catalogProductAttributeSetList();
	        $attributesGroups = array();
	        foreach($attributeSet['body']->items as $key => $setAttr){
	        	$setAttributeIdMg2 = $setAttr->attribute_set_id;
	        	$setAttributeName = $setAttr->attribute_set_name;
	        	$setAttributesRelationshipModel->mg2_attribute_set_id = $setAttributeIdMg2;
	        	$setAttributesRelationshipModel->mg2_name = $setAttributeName;
	        	$setAttributesRelationshipModel->Save();
	        	
	        	$catalogAttributesModel->attribute_set_id = $setAttr->attribute_set_id;
	        	$attributesGroups =  $catalogAttributesModel->catalogProductAttributeList();
	        	
	        	if(empty($attributesGroups['body'])){
	        		continue;
	        	}
	        	foreach($attributesGroups['body'] as $key => $attr){
	        		if($attr->attribute_code == 'country_of_manufacture' or $attr->attribute_code == 'merchant_center_category'){
	        			continue;
	        		}
	        		$catalogAttributesModel->attribute_code = $attr->attribute_code;
		        	$productAttributes = $catalogAttributesModel->getProductsAttributes();
		        	$productAttributes = $productAttributes['body'];
	        		$continue = false;
	        		$isConfigurable = 0;
	        		if($attr->frontend_input == 'select'){
	        			$isConfigurable = 1;
	        		}
            		if($productAttributes->is_visible_on_front == 1 OR $productAttributes->is_html_allowed_on_front == 1){
            			$continue = true; 
            			
            		}
            		
            		if(!$continue){
	            		switch($attr->attribute_code){
	            			case "weight": $continue = true; break;
	            			case "apply_to":
	            				
	            				$applyTo = $productAttributes->apply_to;
	            				if(!empty($applyTo)){
		            				$k = array_search('configurable', $applyTo); 
		            				if(!empty($k)){
		            					$isConfigurable = 1 ;
		            				}
	            				}
	            				
	            				break;
	            		}
            		}
            		
		            if($continue){
		            	echo $attr->attribute_id."  /  ".$attr->attribute_code."  /  ".$attr->default_frontend_label."<br>";
// 		                switch($attr->attribute_code){
// 		                    case "color": if( isset($attr->options) ){ importColor($db, $storeId, $attr->options); }  break;
// 		                    case "manufacturer": if( isset($attr->options) ){ importBrands($db, $storeId, $attr->options); }  break;
// 		                }
			                
	                    $optionsJson = isset($attr->options) ? json_encode($attr->options) : '';
	                    $additionalFieldsJson = isset($attr->additional_fields) ? json_encode($attr->additional_fields) : '';
		                    
	                    $attributesRelationshipModel->attribute = trim($attr->default_frontend_label);
	                    $attributesRelationshipModel->attribute_id = $attr->attribute_id;
	                    $attributesRelationshipModel->attribute_code = $attr->attribute_code;
	                    $attributesRelationshipModel->options = $optionsJson;
	                    $attributesRelationshipModel->frontend_input = trim($attr->frontend_input);
	                    $attributesRelationshipModel->scope = json_encode($attr->scope);
	                    $attributesRelationshipModel->is_unique = $attr->is_unique;
	                    $attributesRelationshipModel->is_required = $attr->is_required;
	                    $attributesRelationshipModel->is_configurable = $isConfigurable;
	                    $attributesRelationshipModel->additional_fields = $additionalFieldsJson;
	                    $attributesRelationshipModel->frontend_label = trim($attr->default_frontend_label);
	                    $attributesRelationshipModel->Save();
		            }
	            }
// 	            die;
	        }
	        break;
	        
	        /**
	         * Cria e atualiza os atributos na tabela attributes
 	         */
	    case "add_update_attributes_mg2":
	       
	        $sql = "SELECT * FROM mg2_attributes_relationship WHERE store_id = {$storeId}  AND import_values = 1";
	        $query = $db->query($sql);
	        $attributes = $query->fetchAll(PDO::FETCH_ASSOC);
	        foreach($attributes as $key => $attribute){
	        	pre($attribute);
	            if(empty(trim($attribute['relationship']))){
	                
                $attrTitle = friendlyText($attribute['attribute']);
                $sqlAttr = "SELECT * FROM `attributes`  WHERE `store_id` = {$storeId}
    			AND alias LIKE '{$attribute['attribute_code']}' ORDER BY id DESC";
	            $queryAttr = $db->query($sqlAttr);
                $result = $queryAttr->fetch(PDO::FETCH_ASSOC);
                
                if(!isset($result['attribute'])){
                
                    $query = $db->insert('attributes',array(
                        'store_id' => $storeId,
                        'attribute' => $attrTitle,
                        'description' => $attribute['attribute'],
                        'alias' => titleFriendly($attribute['attribute_code']),
                    	'marketplace' => 'Magento2'
                    ));
                    
                    if ( ! $query ) {
                        pre($query);
                    }
                
                }else{
                    pre($result);
                    
                    $query = $db->update('attributes', array('store_id', 'id'), 
                        array($storeId, $result['id']), array('attribute' => $attrTitle)
                        );
                    
                    if($query->rowCount()){
                        
                        $sqlAttrValue = "UPDATE `attributes_values` SET name = '{$attrTitle}', 
                        marketplace = 'Magento2' WHERE `store_id` = {$storeId} AND attribute_id LIKE '{$result['alias']}'";
                        $queryAttrvalue = $db->query($sqlAttrValue);
                        pre($queryAttrvalue->rowCount());
                        
                        if ( ! $queryAttrvalue ) {
                            
                            pre($query);
                        }
                        
                    }else{
                       echo "sem atualizacao \n"; 
                       pre($result);
                    }
                  }
	           }
            }
	        break;
	        
// 	       /**
// 	        * 
// 	        * Cria os atributos configuraveis de variação
// 	        */ 
// 	    case "export_attribute_variations":
// 	        $catalogAttributesModel = new AttributesModel($db, null, $storeId);
// 	        $setAttributesRelationshipModel = new SetAttributesRelationshipModel($db, null, $storeId);
// 	        $attributesRelationshipModel = new AttributesRelationshipModel($db, null, $storeId);
// 	        $setAttributes = $setAttributesRelationshipModel->ListSetAttributesRelationship();
// 	        foreach($setAttributes as $key => $attribute){
// 	            if(!empty($attribute['variation_label'])){
// 	                pre($attribute['variation_label']);
// 	                $attributeCode = titleFriendly($attribute['variation_label']);
	                
// 	               $sqlVerify = "SELECT attribute_code, attribute_id FROM mg2_attributes_relationship
//                 WHERE store_id = {$storeId} AND attribute_code LIKE '{$attributeCode}'";
// 	                $query = $db->query($sqlVerify);
// 	                $queryVerify = $query->fetch(PDO::FETCH_ASSOC);
	                
// 	                if(!isset($queryVerify['attribute_code'])){
// // 	                    echo 123;die;
// 	                    $catalogAttributesModel->attribute_data = array(
	                        
// 	                        "attribute_code" => $attributeCode,
// 	                        "frontend_input" => 'select',
// 	                        "scope" => "global",
// 	                        "default_value" => '',
// 	                        "is_unique" => 0,
// 	                        "is_required" => 0,
// 	                        "apply_to" => array(),
// 	                        "is_configurable" => 1,
// 	                        "is_searchable" => 1,
// 	                        "is_visible_in_advanced_search" => 1,
// 	                        "is_comparable" => 1,
// 	                        "is_used_for_promo_rules" => 1,
// 	                        "is_visible_on_front" => 1,
// 	                        "used_in_product_listing" => 1,
// 	                        "additional_fields" => array(
// 	                            array('key' => 'is_filterable','value' => 1),
// 	                            array('key' => 'is_filterable_in_search','value' => 1),
// 	                            array('key' => 'position','value' => 1),
// 	                            array('key' => 'used_for_sort_by','value' => 1),
// 	                        ),
// 	                        "frontend_label" => array(array("store_id" => "0", "label" => $attribute['variation_label']))
// 	                    );
	                    
// // 	                    pre($catalogAttributesModel->attribute_data);
// 	                    $catalogAttributeId = $catalogAttributesModel->catalogProductAttributeCreate();
// // 	                    pre($catalogAttributeId);
	                   
	                    
// 	                }else{
// 	                    $catalogAttributeId = $queryVerify['attribute_id'];
// 	                }
	                
// 	                pre($catalogAttributeId);
// 	                if(!empty($catalogAttributeId)){
//     	                $catalogAttributesModel->attribute_id = $catalogAttributeId;
    	                
//     	                $attr = $catalogAttributesModel->catalogProductAttributeInfo();
//     	                pre($attr);
//     	                if(!empty($attr->attribute_id)){
    	                    
//     	                    $optionsJson = isset($attr->options) ? json_encode($attr->options) : '';
//     	                    $frontendLabelJson = isset($attr->frontend_label) ? json_encode($attr->frontend_label) : '';
//     	                    $additionalFieldsJson = isset($attr->additional_fields) ? json_encode($attr->additional_fields) : '';
    	                    
// //     	                    $scope = isset( $attr->scope) ?  $attr->scope : '';
//     	                    $scope = 'global';
//     	                    $attributesRelationshipModel->attribute = $attr->frontend_label[0]->label;
//     	                    $attributesRelationshipModel->attribute_id = $attr->attribute_id;
//     	                    $attributesRelationshipModel->attribute_code = $attr->attribute_code;
//     	                    $attributesRelationshipModel->options = $optionsJson;
//     	                    $attributesRelationshipModel->frontend_input = $attr->frontend_input;
//     	                    $attributesRelationshipModel->scope = $scope;
//     	                    $attributesRelationshipModel->is_unique = $attr->is_unique;
//     	                    $attributesRelationshipModel->is_required = $attr->is_required;
//     	                    $attributesRelationshipModel->additional_fields = $additionalFieldsJson;
//     	                    $attributesRelationshipModel->frontend_label = $frontendLabelJson;
//     	                    $attributesRelationshipModel->Save();
    	                    
//     	                    if(!$query){
//     	                        pre($query);
//     	                    }
    	                    
//     	                }
// 	                }
	                
// 	            }
	            
	            
// 	        }
	        
// // 	        pre($setAttributes);
// 	        break;
	        
	        /**
	         * Cria os atributos no ecommerce e salva o relacionamento
	         */
	    case "export_attribute_mg2":
	    	
	        $catalogAttributesModel = new AttributesModel($db, null, $storeId);
	        $catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
	        $attributesRelationshipModel = new AttributesRelationshipModel($db, null, $storeId);
// 	        $sqlAttr = "SELECT * FROM `attributes`  WHERE `store_id` = {$storeId} AND alias NOT IN (
//                 SELECT relationship FROM mg2_attributes_relationship 
//                 WHERE store_id = {$storeId} AND relationship != '')";
	        
	        $sqlAttr = "SELECT attributes_values.attribute_id as alias, attributes_values.name as attribute 
	        	FROM `attributes_values`  WHERE `store_id` = {$storeId} AND attribute_id NOT IN (
	        	SELECT relationship as attribute_id FROM mg2_attributes_relationship
	       	 	WHERE store_id = {$storeId} AND relationship != ''
	        )";
	        $queryAttr = $db->query($sqlAttr);
	        $attributes = $queryAttr->fetchAll(PDO::FETCH_ASSOC);
	        $attributes[] = array('attribute' => 'Tamanho', 'alias' => 'tamanho', 'frontend_input' => 'select', 'description' => 'Variação Tamanho','store_id' => $storeId, 'is_configurable' => 1);
	        $attributes[] = array('attribute' => 'Voltagem', 'alias' => 'voltagem', 'frontend_input' => 'select','description' => 'Variação Voltagem','store_id' => $storeId, 'is_configurable' => 1);
	        $attributes[] = array('attribute' => 'Unidade', 'alias' => 'unidade', 'frontend_input' => 'select','description' => 'Variação Unidade','store_id' => $storeId);
	        $attributes[] = array('attribute' => 'Cor', 'alias' => 'color', 'frontend_input' => 'select','description' => 'Variação Cor','store_id' => $storeId, 'is_configurable' => 1);
	        $attributes[] = array('attribute' => 'Marca', 'alias' => 'manufacturer', 'frontend_input' => 'select','description' => 'Variação Cor','store_id' => $storeId);
	        $attributes[] = array('attribute' => 'Referência', 'alias' => 'reference', 'frontend_input' => 'text','description' => 'Referência','store_id' => $storeId);
	        $attributes[] = array('attribute' => 'EAN', 'alias' => 'ean', 'frontend_input' => 'text','description' => 'EAN','store_id' => $storeId);
	        $attributes[] = array('attribute' => 'Coleção', 'alias' => 'colecao', 'frontend_input' => 'text','description' => 'Coleção','store_id' => $storeId);
	        
	        /**
	         * Envia o vendedor 
	         */
	        if($storeId == '7'){
	        	
	        		$attributes[] = array('attribute' => 'Lojas', 'alias' => 'lojas', 'frontend_input' => 'select','description' => 'Lojas','store_id' => $storeId);
	        	
	        }
	        
	        foreach($attributes as $key => $attribute){
	        	
	            $attributeCode = str_replace("-", "_", strtolower(titleFriendly($attribute['alias'])));
	            
                $sqlVerify = "SELECT attribute_code FROM mg2_attributes_relationship 
                WHERE store_id = {$storeId} AND attribute_code LIKE '{$attributeCode}'";
                $query = $db->query($sqlVerify);
                $queryVerify = $query->fetch(PDO::FETCH_ASSOC);
                
                if(!isset($queryVerify['attribute_code']) OR empty($queryVerify['attribute_code'])){
                    $frontendInput =   isset($attribute['frontend_input']) && !empty($attribute['frontend_input']) ? $attribute['frontend_input'] : 'text';
                    $is_filterable_in_search = $is_filterable_in_grid = $is_filterable = $frontendInput == 'select' ? 1 : 0 ;
                    
                    $attrName = !empty( $attribute['attribute']) ?  $attribute['attribute'] :  $attribute['alias'] ;
                    
                    $catalogAttributesModel->attribute_data = (object) array("attribute" => array(
                       "attribute_code" => "{$attributeCode}",
                       "entity_type_id" => 'catalog',
                       "is_unique" => 0,
                       "is_required" => 0,
                       "is_comparable" => 1,
                       "is_filterable" => 0, 
                       "is_used_for_promo_rules" => 0,
                       "is_visible_on_front" => 1,
                       "is_filterable_in_grid" => $is_filterable_in_grid,
                       "is_filterable_in_search" => $is_filterable_in_search,
                       "used_in_product_listing" => 1,
                       "frontend_input" => $frontendInput,
                       "frontend_labels" => array(array("label" => $attrName, "store_id" => "0"))
                    	)
                    );
                    pre(array('novo_attributo' =>$catalogAttributesModel->attribute_data));
                    $catalogAttributeId = $catalogAttributesModel->catalogProductAttributeCreate();
                    pre($catalogAttributeId);
                    $catalogAttributesModel->attribute_code = $attributeCode;
                    $attr = $catalogAttributesModel->catalogProductAttributeInfo();
//                     pre($attr);die;
	              	if(!isset($attr['body']->message)){
		            	$attr = $attr['body'];
	                    if(!empty($attr->attribute_id)){
	                        $optionsJson = isset($attr->options) ? json_encode($attr->options) : '';
	                        $frontendLabelJson = isset($attr->frontend_label) ? json_encode($attr->frontend_label) : '';
	                        $scope = 'global';
	                        $attributesRelationshipModel->attribute = !empty($attr->default_frontend_label) ? $attr->default_frontend_label : $attr->frontend_label[0]->label;
	                        $attributesRelationshipModel->attribute_id = $attr->attribute_id;
	                        $attributesRelationshipModel->attribute_code = $attr->attribute_code;
	                        $attributesRelationshipModel->options = $optionsJson;
	                        $attributesRelationshipModel->frontend_input = $attr->frontend_input;
	                        $attributesRelationshipModel->scope = $scope;
	                        $attributesRelationshipModel->is_unique = $attr->is_unique;
	                        $attributesRelationshipModel->is_required = $attr->is_required;
	                        $attributesRelationshipModel->is_configurable = isset($attribute['is_configurable']) && !empty($attribute['is_configurable']) ? $attribute['is_configurable'] : 0 ;
	                        $attributesRelationshipModel->frontend_label = $frontendLabelJson;
	                        $result = $attributesRelationshipModel->Save();
                            pre($result);
	                    
	                    }
	               	}else{
	                	echo "error|".$attr['body']->message;
	            	}
            	}
	        }
	        
	        $sql = "SELECT * FROM mg2_attribute_set_relationship WHERE store_id = {$storeId} and set_attribute_id != ''";
	        $query = $db->query($sql);
	        $sets = $query->fetchAll(PDO::FETCH_ASSOC);
	        foreach($sets as $key => $set){
// 	            pre($set);
	            $catalogAttributeSetModel->filters = array();
	            $catalogAttributeSetModel->filters[] = array('field' => 'attribute_set_id', 'value' => $set['mg2_attribute_set_id'], 'condition_type' => 'eq' );
	            $catalogAttributeSetModel->filters[] = array('field' => 'attribute_group_name', 'value' => 'General', 'condition_type' => 'eq' );
// 	            $catalogAttributeSetModel->filters[] = array('field' => 'attribute_group_name', 'value' => 'Sysplace', 'condition_type' => 'eq' );
	            $attributeGroup = $catalogAttributeSetModel->catalogSetAttributesGroups();
	            if(isset($attributeGroup['body']->items)){
	            	$attributeGroupIdGeneral =  $attributeGroup['body']->items[0]->attribute_group_id;
	            }else{
// 	            	$catalogAttributeSetModel->attribute_set_id = $set['attribute_set_id'];
// 	            	$attributeGroup = $catalogAttributeSetModel->catalogSetAttributesGroupsAdd();
// 	            	pre($attributeGroup);
// 	            	if($attributeGroup['httpCode'] == '200'){
// 	            		$attributeGroup = $catalogAttributeSetModel->catalogSetAttributesGroups();
// 	            		if(isset($attributeGroup['body']->items)){
// 	            			$attributeGroupIdGeneral =  $attributeGroup['body']->items[0]->attribute_group_id;
// 	            		}
// 	            	}else{
	            		echo "error|Erro ao importar grupo de attributos {$set['attribute_set_id']}";die;
	            		continue;
// 	            	}
	            }
// 	            die;
	            $query = $db->query('SELECT set_attributes_relationship.attribute_id, attributes.alias 
                FROM set_attributes_relationship 
                LEFT JOIN attributes ON set_attributes_relationship.attribute_id = attributes.id
                WHERE set_attributes_relationship.store_id = ? AND set_attributes_relationship.set_attribute_id = ?', 
	                array($storeId, $set['set_attribute_id']));
	            $attributes = $query->fetchAll(PDO::FETCH_ASSOC);
	            $attributes[] = array('alias' => 'color', 'attribute' => 'Cor');
	            $attributes[] = array('alias' => 'manufacturer', 'attribute' => 'Marca');
	            $attributes[] = array('alias' => 'reference', 'attribute' => 'Referência');
	            $attributes[] = array('alias' => 'ean', 'attribute' => 'EAN');
	            $attributes[] = array('alias' => 'colecao', 'attribute' => 'Coleção');
	            $attributes[] = array('alias' => 'lojas', 'attribute' => 'Lojas');
	            
// 	            pre("Conjunto de attributos adicionado");
// 	            pre($attributes);
	            
	            foreach($attributes as $ind => $attribute){
	                $attributeCode  = str_replace("-", "_", strtolower($attribute['alias']));
	                $sqlAttrRel = "SELECT * FROM mg2_attributes_relationship
                    WHERE store_id = {$storeId} AND attribute_code LIKE '{$attributeCode}'";
	                $queryAttrRel = $db->query($sqlAttrRel);
	                $queryAttrRel = $queryAttrRel->fetch(PDO::FETCH_ASSOC);
	                $catalogAttributeSetModel->attribute_set_id = $set['mg2_attribute_set_id'];
	                $catalogAttributeSetModel->attribute_code = isset($queryAttrRel['attribute_code']) && !empty($queryAttrRel['attribute_code']) ? strtolower($queryAttrRel['attribute_code']) : $attributeCode;
	                $catalogAttributeSetModel->attribute_group_id = $attributeGroupIdGeneral;
    	            $res = $catalogAttributeSetModel->catalogProductAttributeSetAttributeAdd();
//     	            pre($res);
	            }
	            
	            $sqlAttrRelVar = "SELECT * FROM mg2_attributes_relationship WHERE store_id = {$storeId} 
                AND attribute_code LIKE '{$set['variation_label']}'";
	            $queryAttrRelVar = $db->query($sqlAttrRelVar);
	            
	            while($queryAttrVar = $queryAttrRelVar->fetch(PDO::FETCH_ASSOC)){
	                
	                if(!empty($queryAttrVar)){
	                    $catalogAttributeSetModel->attribute_set_id = $set['mg2_attribute_set_id'];
	                    $catalogAttributeSetModel->attribute_code = strtolower($queryAttrVar['attribute_code']);
	                    $catalogAttributeSetModel->attribute_group_id = $attributeGroupIdGeneral;
	                    $res = $catalogAttributeSetModel->catalogProductAttributeSetAttributeAdd();
// 	                    pre($res);
	                }
	                
	            }
	       }
           
	       break;
	        
	        /**
	         * Atualiza relacionamento de attributos ajax
	         */
	    case "update_attribute_relationship":
	    		
	            $attributeId = $_REQUEST["attribute_id"];
	            $attributeCode = $_REQUEST["attribute_code"];
	            $attributeRelationship = $_REQUEST["attribute_relationship"];
	            $attributeType = $_REQUEST["attribute_type"];
	            $attributeSetId = $_REQUEST["attribute_set_id"];
	            
	            if(!isset($attributeId) AND !isset($attributeRelationship)){
	                return array();
	            }
	           if($attributeType == 'default'){
	            	$attributeRelationship = $attributeRelationship != 'select' ? $attributeRelationship  : "" ;
    	            $query = $db->update("mg2_attributes_relationship",
    	                array('store_id','attribute_id'),
    	                array($storeId, $attributeId),
    	                array("relationship" => $attributeRelationship));
    	            
    	            if ( $query ) {
    	                
    	                echo "success|";
    	            }
    	            
	           }else{
	           	
		           	$sqlVerify = "SELECT * FROM mg2_set_attribute_relationships WHERE store_id = {$storeId} 
		           	AND attribute_set_id = '{$attributeSetId}' AND attribute_id = '{$attributeId}' ";
		           	$queryVerify = $db->query($sqlVerify);
		           	$attrVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
		           	if(!isset($attrVerify['id'])){
		           		$query = $db->insert("mg2_set_attribute_relationships", array(  
		           				'store_id' => $storeId,
				           		'attribute_id' => $attributeId,
				           		'attribute_code' => $attributeCode,
				           		"attribute_set_id" =>$attributeSetId,
				           		"relationship" => $attributeRelationship
		           			));
		           		
		           		
		           	}else{
		           		
		           		$query = $db->update("mg2_set_attribute_relationships",
		           			array('store_id','attribute_id'),
		           			array($storeId, $attributeId),
		           			array("relationship" => $attributeRelationship));
		           	 
			           	if ( $query ) {
			           		 
			           		echo "success|";
			           	}
			           	
		           	}
		           	
		           	$query = $db->update("mg2_attributes_relationship",
		           			array('store_id','attribute_id'),
		           			array($storeId, $attributeId),
		           			array("relationship" => ''));
		           	
		           	
		        }
	            break;
	         /**
	          * Seta o atributo para importar valores
	          */   
	    case "update_import_value":
	        
	        $attributeId = $_REQUEST["attribute_id"];
	        $importValue = $_REQUEST["import_value"];
	        $query = $db->update("mg2_attributes_relationship",
	            array('store_id','attribute_id'),
	            array($storeId, $attributeId),
	            array("import_values" => $importValue));
	        if ( $query ) {
	            
	            echo "success";
	        }
	        
	        break;
	        
        case "add_attribute_spotlight":
        	 
        	$attributeId = $_REQUEST["attribute_id"];
        	$spotlight = $_REQUEST["spotlight"];
        	$query = $db->update("mg2_attributes_relationship",
        			array('store_id','attribute_id'),
        			array($storeId, $attributeId),
        			array("spotlight" => $spotlight));
        	if ( $query ) {
        		 
        		echo "success";
        	}
        	 
        	break;
	        
	       /**
	        * Integração tipo importação para publicação em marketplace
	        * Importa conjunto de atributos do ecommerce para integraçnoes 
	        */
	    case "import_attribute_set_mg2":
	        
	        $setAttributesRelationshipModel = new SetAttributesRelationshipModel($db, null, $storeId);
	        $catalogAttributesModel = new AttributesModel($db, null, $storeId);
	        $catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
	        $catalogAttributeSetModel->filters[] = array('field' => 'attribute_set_name', 'value' => '', 'condition_type' => 'neq' );
// 	        $catalogAttributeSetModel->filters[] = array('field' => 'attribute_set_id', 'value' => '27', 'condition_type' => 'eq' );
	        $setAttributesMg2 = $catalogAttributeSetModel->catalogProductAttributeSetList();
	        foreach($setAttributesMg2['body']->items as $key => $setAttribute){
	        	pre($setAttribute);
	            
	            $query = $db->query('SELECT * FROM `mg2_attribute_set_relationship`  WHERE `store_id` = ?
        			AND mg2_attribute_set_id = ? AND mg2_name LIKE ? ',array($storeId, $setAttribute->attribute_set_id, $setAttribute->attribute_set_name));
	            $res = $query->fetch(PDO::FETCH_ASSOC);
	            
	            if(!isset($res['set_attribute_id'])){
	                
	                $querySetAttr = $db->query('SELECT * FROM `set_attributes`  WHERE `store_id` = ?
        			AND set_attribute LIKE ?',array($storeId, $setAttribute->attribute_set_name));
	                $resSetAttr = $querySetAttr->fetch(PDO::FETCH_ASSOC);
	                
	                if(!empty($resSetAttr['set_attribute'])){
	                    
	                    $setAttributeId = $resSetAttr['id'];
	                }else{
	                    $query = $db->insert('set_attributes', array(
	                        'store_id' => $storeId,
	                        'set_attribute' => $setAttribute->attribute_set_name,
	                        'description' => $setAttribute->attribute_set_name
	                    ));
	                    
	                    $setAttributeId = $db->last_id;
	                }
	            }else{
	                $setAttributeId = $res['set_attribute_id'];
	            }
	           
               $setAttributeIdMg2 = $setAttribute->attribute_set_id;
               $setAttributeName = $setAttribute->attribute_set_name;
               $setAttributesRelationshipModel->set_attribute_id = $setAttributeId;
               $setAttributesRelationshipModel->set_attribute = $setAttributeName;
               $setAttributesRelationshipModel->mg2_attribute_set_id = $setAttributeIdMg2;
               $setAttributesRelationshipModel->mg2_name = $setAttributeName;
               $setAttributesRelationshipModel->Save();
               $catalogAttributesModel->attribute_set_id = $setAttributeIdMg2;
               $attributes = $catalogAttributesModel->catalogProductAttributeList();
               foreach($attributes['body'] as $key => $attribute){
                   $queryAttr = $db->query('SELECT * FROM `attributes`  WHERE `store_id` = ?
    			     AND alias LIKE ?',array($storeId, $attribute->attribute_code));
                   $resAttr = $queryAttr->fetch(PDO::FETCH_ASSOC);
                   if(!empty($resAttr['attribute'])){
                       $queryAttrRel = $db->query('SELECT * FROM `mg2_attributes_relationship`  WHERE `store_id` = ?
    			         AND attribute_code LIKE ?',array($storeId, $resAttr['alias']));
                       $resAttrRel = $queryAttrRel->fetch(PDO::FETCH_ASSOC);
                       
                       if(isset($resAttrRel['import_values']) AND empty($resAttrRel['relationship']) AND $resAttrRel['import_values'] == 1){
                           $querySetAttrRel = $db->query('SELECT * FROM `set_attributes_relationship`  WHERE `store_id` = ?
    			             AND attribute_id = ? AND set_attribute_id = ?',array($storeId, $resAttr['id'], $setAttributeId));
                           $resSetAttrRel = $querySetAttrRel->fetch(PDO::FETCH_ASSOC);
                           
                           if(!isset($resSetAttrRel['attribute_id'])){
                               $query = $db->insert('set_attributes_relationship', array(
                                   'store_id' => $storeId,
                                   'attribute_id' => $resAttr['id'],
                                   'set_attribute_id' => $setAttributeId
                               ));
                               
                           }else{
                               echo "ja existe relacionamento";
                           }
                       }
                   }else{
                   	pre($resAttr);
                   }
               }
	        }
	        echo "success";
	        
	        break;
	        
	        /**
	         * Atualiza o relacionamento de conjunto de atributos do ecommerce
	         * 
	         *             
	         */
	    case "set_attr_relationship_ecommerce":
	        
	        $setAttributeIdMg2Val = $_REQUEST["set_attribute_id_mg2"];
	        $setAttributeId = $_REQUEST["set_attribute_id"];
	        $setAttributeName = $_REQUEST["set_attribute_name"];
	        
	        $queryAttr = $db->query('SELECT * FROM `set_attributes`  WHERE `store_id` = ?
        			AND id = ?',array($storeId, $setAttributeId));
	        $resAttr = $queryAttr->fetch(PDO::FETCH_ASSOC);
	        if($setAttributeIdMg2Val == 'export_set_attr_ecommerce'){
	            $attributeSetModel = new AttributeSetModel($db, null, $storeId);
	            $attributeSetModel->attribute_set_name = $resAttr['set_attribute'];
	            $setAttributeIdMg2 = $attributeSetModel->catalogProductAttributeSetCreate();
	            if(isset($setAttributeIdMg2['body']->message)){
	            	echo  "error|{$setAttributeIdMg2['body']->message}";
	            	return ;
	            }
	            $setAttributeIdMg2 = $setAttributeIdMg2['body']->attribute_set_id;
	            $setAttributeName = $resAttr['set_attribute'];
	        }else{
	        	$setAttributeIdMg2 = $setAttributeIdMg2Val;
	        }
	        $query = $db->query('SELECT * FROM `mg2_attribute_set_relationship`  WHERE `store_id` = ?
        			AND set_attribute_id = ?',array($storeId, $setAttributeId));
	        $res = $query->fetch(PDO::FETCH_ASSOC);
	        if(empty($res['set_attribute_id'])){
	            
	            $query = $db->insert('mg2_attribute_set_relationship', array(
	                'store_id' => $storeId,
	                'set_attribute_id' => $setAttributeId,
	                'set_attribute' => $resAttr['set_attribute'],
	                'mg2_attribute_set_id' => $setAttributeIdMg2,
	                'mg2_name' => $setAttributeName
	            ));
	            
	        }else{
	            
	            $query = $db->update('mg2_attribute_set_relationship', 
	                array("store_id", "set_attribute_id"), 
	                array($storeId, $setAttributeId), 
	                array('set_attribute' => $resAttr['set_attribute'],
    	                'mg2_attribute_set_id' => $setAttributeIdMg2,
    	                'mg2_name' => $setAttributeName
    	            ));
	            
	        }
	        if ( $query ) {
	           echo "success|{$setAttributeIdMg2}|{$setAttributeName}";
	        }
	        
	        break;
	        
	    case "set_variation_label_relationship_ecommerce":
	        
	        $setAttributeId = $_REQUEST["set_attribute_id"];
	        $variationType = $_REQUEST["variation_type"];
	        $variationLabel = $_REQUEST["variation_label"];
	        
	        if(isset($setAttributeId)){
    	        $query = $db->update('mg2_attribute_set_relationship',
    	            array("store_id", "set_attribute_id"),
    	            array($storeId, $setAttributeId),
    	            array('variation_type' => $variationType,
    	            		'variation_label' => $variationLabel)
    	            );
    	        
    	        if ( $query ) {
    	            echo "success|{$setAttributeId}";
    	        }
    	        
	        }
	        
	        break;
	}
	
	
	
}

/**
 * Importa cor na importação de atributos
 */
function importColor($db, $storeId, $colors = array()){
   
    foreach($colors as $key => $color){
   
        if(!empty($color->label)){
           
            $query = $db->query('SELECT * FROM `colors`  WHERE `store_id` = ?
        			AND ecommerce_id = ?',array($storeId, $color->value));
            $res = $query->fetch(PDO::FETCH_ASSOC);
            pre($res);
            if(!isset($res['color'])){
                
                $query = $db->insert('colors', array(
                    'store_id' => $storeId,
                    'color' => $color->label,
                    'description' => $color->label,
                    'ecommerce_id' => $color->value
                ));
                
                if ( ! $query ) {
                    pre($query);
                } 
            
            }
        }
    }
        
}
/**
 * Importa marcas na importação de attributos 
 */
function importBrands($db, $storeId, $brands = array()){
    
    foreach($brands as $key => $brand){
        
        if(!empty($brand->label)){
            
            $query = $db->query('SELECT * FROM `brands`  WHERE `store_id` = ?
        			AND ecommerce_id = ?',array($storeId, $brand->value));
            $res = $query->fetch(PDO::FETCH_ASSOC);
            if(!isset($res['brand'])){
                
                $query = $db->insert('brands', array(
                    'store_id' => $storeId,
                    'brand' => $brand->label,
                    'description' => $brand->label,
                    'ecommerce_id' => $brand->value
                ));
                
                if ( ! $query ) {
                    pre($query);
                }
                
            }
        }
    }
    
}
