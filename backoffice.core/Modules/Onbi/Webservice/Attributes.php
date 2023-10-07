<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-Soap.php';
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
	    
	    case "remove_attribute_magento": 
	        $attributeId = $_REQUEST['attribute_id'];
	        
	        $attributeModel = new AttributesModel($db, null, $storeId);
	        
	        
	        $sqlVerify = "SELECT * FROM onbi_attributes_relationship WHERE store_id = {$storeId} AND attribute_id = '{$attributeId}' ";
	        $queryVerify = $db->query($sqlVerify);
	        $attrVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	        
	        if(!empty($attrVerify['attribute_id'])){
	           
	            $attributeModel->attribute_id = $attrVerify['attribute_id'];
	            
    	        if($attributeModel->catalogProductAttributeRemove()){
    	            
    	            $sql = "DELETE FROM onbi_attributes_relationship WHERE store_id = {$storeId} AND attribute_id = {$attributeId}";
    	            $query = $db->query($sql);
    	            
    	        }
    	        
	        }
	        if(!$query){
	            
	            echo  "error";die;
	            
	        }
	        echo "success";
	        break;
	    
            /**
             * Importa os atributos do ecommerce 
             * para tabela de relacionamento attributes onbi
             */
	    case "import_product_attributes":
	        
	        $attributesModel = new AttributesModel($db, null, $storeId);
	        $attributesRelationshipModel = new AttributesRelationshipModel($db, null, $storeId);
	        $attributes = $attributesModel->ListCatalogProductAttributeInfo();
// 	        pre($attributes);die;
	        foreach($attributes as $key => $attr){
// 	            pre($attr);
	            if(isset($attr->frontend_label[0]->label)){
	                
	                switch($attr->attribute_code){
	                    case "color": if( isset($attr->options) ){ importColor($db, $storeId, $attr->options); }  break;
	                    case "manufacturer": if( isset($attr->options) ){ importBrands($db, $storeId, $attr->options); }  break;
	                }
	                
                    $optionsJson = isset($attr->options) ? json_encode($attr->options) : '';
                    $frontendLabelJson = isset($attr->frontend_label) ? json_encode($attr->frontend_label) : '';
                    $additionalFieldsJson = isset($attr->additional_fields) ? json_encode($attr->additional_fields) : '';
              
                    echo $attr->attribute_code."  /  ".$attr->frontend_label[0]->label."<br>";
//                     $scope = isset( $attr->scope) ?  $attr->scope : '';
                    $scope = 'global';
                    $attributesRelationshipModel->attribute = $attr->frontend_label[0]->label;
                    $attributesRelationshipModel->attribute_id = $attr->attribute_id;
                    $attributesRelationshipModel->attribute_code = $attr->attribute_code;
                    $attributesRelationshipModel->options = $optionsJson;
                    $attributesRelationshipModel->frontend_input = $attr->frontend_input;
                    $attributesRelationshipModel->scope = $scope;
                    $attributesRelationshipModel->is_unique = $attr->is_unique;
                    $attributesRelationshipModel->is_required = $attr->is_required;
                    $attributesRelationshipModel->is_configurable = $attr->is_configurable;
                    $attributesRelationshipModel->additional_fields = $additionalFieldsJson;
                    $attributesRelationshipModel->frontend_label = $frontendLabelJson;
                    $attributesRelationshipModel->Save();
                    
	            }    
	        }
	        
	        break;
	        
	        /**
	         * Cria e atualiza os atributos na tabela attributes
	         */
	    case "add_update_attributes_onbi":
	       
	        $sql = "SELECT * FROM onbi_attributes_relationship WHERE store_id = {$storeId}  AND import_values = 1";
	        $query = $db->query($sql);
	        $attributes = $query->fetchAll(PDO::FETCH_ASSOC);
	        foreach($attributes as $key => $attribute){
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
                        'alias' => titleFriendly($attribute['attribute_code'])
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
                        marketplace = 'Ecommerce' WHERE `store_id` = {$storeId} AND attribute_id LIKE '{$result['alias']}'";
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
	        
	       /**
	        * 
	        * Cria os atributos configuraveis de variação
	        */ 
	    case "export_attribute_variations":
	        $catalogAttributesModel = new AttributesModel($db, null, $storeId);
	        $setAttributesRelationshipModel = new SetAttributesRelationshipModel($db, null, $storeId);
	        $attributesRelationshipModel = new AttributesRelationshipModel($db, null, $storeId);
	        $setAttributes = $setAttributesRelationshipModel->ListSetAttributesRelationship();
	        foreach($setAttributes as $key => $attribute){
	            if(!empty($attribute['variation_label'])){
	                pre($attribute['variation_label']);
	                $attributeCode = titleFriendly($attribute['variation_label']);
	                
	               $sqlVerify = "SELECT attribute_code, attribute_id FROM onbi_attributes_relationship
                WHERE store_id = {$storeId} AND attribute_code LIKE '{$attributeCode}'";
	                $query = $db->query($sqlVerify);
	                $queryVerify = $query->fetch(PDO::FETCH_ASSOC);
	                
	                if(!isset($queryVerify['attribute_code'])){
// 	                    echo 123;die;
	                    $catalogAttributesModel->attribute_data = array(
	                        
	                        "attribute_code" => $attributeCode,
	                        "frontend_input" => 'select',
	                        "scope" => "global",
	                        "default_value" => '',
	                        "is_unique" => 0,
	                        "is_required" => 0,
	                        "apply_to" => array(),
	                        "is_configurable" => 1,
	                        "is_searchable" => 1,
	                        "is_visible_in_advanced_search" => 1,
	                        "is_comparable" => 1,
	                        "is_used_for_promo_rules" => 1,
	                        "is_visible_on_front" => 1,
	                        "used_in_product_listing" => 1,
	                        "additional_fields" => array(
	                            array('key' => 'is_filterable','value' => 1),
	                            array('key' => 'is_filterable_in_search','value' => 1),
	                            array('key' => 'position','value' => 1),
	                            array('key' => 'used_for_sort_by','value' => 1),
	                        ),
	                        "frontend_label" => array(array("store_id" => "0", "label" => $attribute['variation_label']))
	                    );
	                    
// 	                    pre($catalogAttributesModel->attribute_data);
	                    $catalogAttributeId = $catalogAttributesModel->catalogProductAttributeCreate();
// 	                    pre($catalogAttributeId);
	                   
	                    
	                }else{
	                    $catalogAttributeId = $queryVerify['attribute_id'];
	                }
	                
	                pre($catalogAttributeId);
	                if(!empty($catalogAttributeId)){
    	                $catalogAttributesModel->attribute_id = $catalogAttributeId;
    	                
    	                $attr = $catalogAttributesModel->catalogProductAttributeInfo();
    	                pre($attr);
    	                if(!empty($attr->attribute_id)){
    	                    
    	                    $optionsJson = isset($attr->options) ? json_encode($attr->options) : '';
    	                    $frontendLabelJson = isset($attr->frontend_label) ? json_encode($attr->frontend_label) : '';
    	                    $additionalFieldsJson = isset($attr->additional_fields) ? json_encode($attr->additional_fields) : '';
    	                    
//     	                    $scope = isset( $attr->scope) ?  $attr->scope : '';
    	                    $scope = 'global';
    	                    $attributesRelationshipModel->attribute = $attr->frontend_label[0]->label;
    	                    $attributesRelationshipModel->attribute_id = $attr->attribute_id;
    	                    $attributesRelationshipModel->attribute_code = $attr->attribute_code;
    	                    $attributesRelationshipModel->options = $optionsJson;
    	                    $attributesRelationshipModel->frontend_input = $attr->frontend_input;
    	                    $attributesRelationshipModel->scope = $scope;
    	                    $attributesRelationshipModel->is_unique = $attr->is_unique;
    	                    $attributesRelationshipModel->is_required = $attr->is_required;
    	                    $attributesRelationshipModel->additional_fields = $additionalFieldsJson;
    	                    $attributesRelationshipModel->frontend_label = $frontendLabelJson;
    	                    $attributesRelationshipModel->Save();
    	                    
    	                    if(!$query){
    	                        pre($query);
    	                    }
    	                    
    	                }
	                }
	                
	            }
	            
	            
	        }
	        
// 	        pre($setAttributes);
	        break;
	        
	        /**
	         * Cria os atributos no ecommerce e salva o relacionamento
	         */
	    case "export_attribute_onbi":
	        
	        $catalogAttributesModel = new AttributesModel($db, null, $storeId);
	        $catalogAttributeSetModel = new AttributeSetModel($db, null, $storeId);
	        
	        $attributesRelationshipModel = new AttributesRelationshipModel($db, null, $storeId);
	        $sqlAttr = "SELECT * FROM `attributes`  WHERE `store_id` = {$storeId} AND alias NOT IN (
                SELECT relationship FROM onbi_attributes_relationship 
                WHERE store_id = {$storeId} AND relationship != '')";
	        $queryAttr = $db->query($sqlAttr);
	        $attributes = $queryAttr->fetchAll(PDO::FETCH_ASSOC);
	        
	        foreach($attributes as $key => $attribute){
	            
	            $attributeCode = str_replace("-", "_", $attribute['alias']);
                $sqlVerify = "SELECT attribute_code FROM onbi_attributes_relationship 
                WHERE store_id = {$storeId} AND attribute_code LIKE '{$attributeCode}'";
                $query = $db->query($sqlVerify);
                $queryVerify = $query->fetch(PDO::FETCH_ASSOC);
                
                if(!isset($queryVerify['attribute_code'])){
                    
                    $catalogAttributesModel->attribute_data = array(
                        
                       "attribute_code" => $attributeCode,
                       "frontend_input" => 'text',
                       "scope" => "global",
                       "default_value" => '',
                       "is_unique" => 0,
                       "is_required" => 0,
                       "apply_to" => array(),
                       "is_configurable" => 0,
                       "is_searchable" => 1,
                       "is_visible_in_advanced_search" => 1,
                       "is_comparable" => 1,
                       "is_used_for_promo_rules" => 0,
                       "is_visible_on_front" => 1,
                       "used_in_product_listing" => 1,
                       "additional_fields" => array(),
                       "frontend_label" => array(array("store_id" => "0", "label" => $attribute['attribute']))
                    );
                    
                    $catalogAttributeId = $catalogAttributesModel->catalogProductAttributeCreate();
                    
                    $catalogAttributesModel->attribute_id = $catalogAttributeId;
                    
                    $attr = $catalogAttributesModel->catalogProductAttributeInfo();
                    
                    if(!empty($attr->attribute_id)){
                    
                        $optionsJson = isset($attr->options) ? json_encode($attr->options) : '';
                        $frontendLabelJson = isset($attr->frontend_label) ? json_encode($attr->frontend_label) : '';
                        $additionalFieldsJson = isset($attr->additional_fields) ? json_encode($attr->additional_fields) : '';
                        
//                         $scope = isset( $attr->scope) ?  $attr->scope : '';
                        $scope = 'global';
                        
                        $attributesRelationshipModel->attribute = $attr->frontend_label[0]->label;
                        $attributesRelationshipModel->attribute_id = $attr->attribute_id;
                        $attributesRelationshipModel->attribute_code = $attr->attribute_code;
                        $attributesRelationshipModel->options = $optionsJson;
                        $attributesRelationshipModel->frontend_input = $attr->frontend_input;
                        $attributesRelationshipModel->scope = $scope;
                        $attributesRelationshipModel->is_unique = $attr->is_unique;
                        $attributesRelationshipModel->is_required = $attr->is_required;
                        $attributesRelationshipModel->additional_fields = $additionalFieldsJson;
                        $attributesRelationshipModel->frontend_label = $frontendLabelJson;
                        $attributesRelationshipModel->Save();
                        
                        if(!$query){
                            pre($query);
                        }
                    
                    }

               }
           
               
	        }
	        
	        
	        
	        $sql = "SELECT * FROM onbi_attribute_set_relationship WHERE store_id = {$storeId}";
	        $query = $db->query($sql);
	        $sets = $query->fetchAll(PDO::FETCH_ASSOC);
	        
	        foreach($sets as $key => $set){
	            pre($set);
	            $query = $db->query('SELECT set_attributes_relationship.attribute_id, attributes.alias 
                FROM set_attributes_relationship 
                LEFT JOIN attributes ON set_attributes_relationship.attribute_id = attributes.id
                WHERE set_attributes_relationship.store_id = ? AND set_attributes_relationship.set_attribute_id = ?', 
	                array($storeId, $set['set_attribute_id']));
	            $attributes = $query->fetchAll(PDO::FETCH_ASSOC);
	            
	            pre($attributes);
	            foreach($attributes as $ind => $attribute){
	                $attributeCode  = str_replace("-", "_", $attribute['alias']);
	                $sqlAttrRel = "SELECT * FROM onbi_attributes_relationship
                    WHERE store_id = {$storeId} AND attribute_code LIKE '{$attributeCode}'";
	                $queryAttrRel = $db->query($sqlAttrRel);
	                $queryAttrRel = $queryAttrRel->fetch(PDO::FETCH_ASSOC);
	                if(!empty($queryAttrRel)){
	                    $catalogAttributeSetModel->attribute_set_id = $set['onbi_attribute_set_id'];
	                    $catalogAttributeSetModel->attribute_id = $queryAttrRel['attribute_id'];
    	                $res = $catalogAttributeSetModel->catalogProductAttributeSetAttributeAdd();
	                }
	            }
	            
	            $sqlAttrRelVar = "SELECT * FROM onbi_attributes_relationship WHERE store_id = {$storeId} 
                AND attribute_code LIKE '{$set['variation_label']}' OR  attribute_code LIKE 'color'";
	            $queryAttrRelVar = $db->query($sqlAttrRelVar);
	            while($queryAttrVar = $queryAttrRelVar->fetch(PDO::FETCH_ASSOC)){
	                
	                if(!empty($queryAttrVar)){
	                    $catalogAttributeSetModel->attribute_set_id = $set['onbi_attribute_set_id'];
	                    $catalogAttributeSetModel->attribute_id = $queryAttrVar['attribute_id'];
	                    $res = $catalogAttributeSetModel->catalogProductAttributeSetAttributeAdd();
	                }
	                
	            }
	            
	       }
           
	       break;
	        
	        /**
	         * Atualiza relacionamento de attributos ajax
	         */
	    case "update_attribute_relationship":
	        
	            $attributeId = $_REQUEST["attribute_id"];
	            $attributeRelationship = $_REQUEST["attribute_relationship"];
	            
	            if(!isset($attributeId) AND !isset($attributeRelationship)){
	                return array();
	            }
	            
	            $attributeRelationship = $attributeRelationship != 'select' ? $attributeRelationship  : "" ;
    	            $query = $db->update("onbi_attributes_relationship",
    	                array('store_id','attribute_id'),
    	                array($storeId, $attributeId),
    	                array("relationship" => $attributeRelationship));
    	            
    	            if ( $query ) {
    	                
    	                echo "success";
    	            }
	            break;
	         /**
	          * Seta o atributo para importar valores
	          */   
	    case "update_import_value":
	        
	        $attributeId = $_REQUEST["attribute_id"];
	        $importValue = $_REQUEST["import_value"];
	        $query = $db->update("onbi_attributes_relationship",
	            array('store_id','attribute_id'),
	            array($storeId, $attributeId),
	            array("import_values" => $importValue));
	        if ( $query ) {
	            
	            echo "success";
	        }
	        
	        break;
	        
	       /**
	        * Importa conjunto de atributos do ecommerce
	        */
	    case "import_attribute_set_onbi":
	        
	        $setAttributesOnbiModel = new AttributeSetModel($db, null, $storeId);
	        $setAttributesRelationshipModel = new SetAttributesRelationshipModel($db, null, $storeId);
	        $attributesModel = new AttributesModel($db, null, $storeId);
	        
	        $setAttributesOnbi = $setAttributesOnbiModel->catalogProductAttributeSetList();
	        foreach($setAttributesOnbi as $key => $setAttribute){

	            
	            $query = $db->query('SELECT * FROM `onbi_attribute_set_relationship`  WHERE `store_id` = ?
        			AND onbi_attribute_set_id = ? AND onbi_name LIKE ? ',array($storeId, $setAttribute->set_id, $setAttribute->name));
	            $res = $query->fetch(PDO::FETCH_ASSOC);
	            
	            if(!isset($res['set_attribute_id'])){
	                
	                $querySetAttr = $db->query('SELECT * FROM `set_attributes`  WHERE `store_id` = ?
        			AND set_attribute LIKE ?',array($storeId, $setAttribute->name));
	                $resSetAttr = $querySetAttr->fetch(PDO::FETCH_ASSOC);
	                
	                if(isset($resSetAttr['set_attribute'])){
	                    
	                    $setAttributeId = $resSetAttr['id'];
	                }else{
	                    $query = $db->insert('set_attributes', array(
	                        'store_id' => $storeId,
	                        'set_attribute' => $setAttribute->name,
	                        'description' => $setAttribute->name
	                    ));
	                    
	                    $setAttributeId = $db->last_id;
	                }
	            }else{
	                $setAttributeId = $res['set_attribute_id'];
	            }
	            
               $setAttributeIdOnbi = $setAttribute->set_id;
               $setAttributeName = $setAttribute->name;
               
               $setAttributesRelationshipModel->set_attribute_id = $setAttributeId;
               $setAttributesRelationshipModel->set_attribute = $setAttributeName;
               $setAttributesRelationshipModel->onbi_attribute_set_id = $setAttributeIdOnbi;
               $setAttributesRelationshipModel->onbi_name = $setAttributeName;
               $setAttributesRelationshipModel->Save();
        
               $attributesModel->set_id = $setAttribute->set_id;
               $attributes = $attributesModel->catalogProductAttributeList();
               foreach($attributes as $key => $attribute){
                   
                   $queryAttr = $db->query('SELECT * FROM `attributes`  WHERE `store_id` = ?
    			     AND alias LIKE ?',array($storeId, $attribute->code));
                   $resAttr = $queryAttr->fetch(PDO::FETCH_ASSOC);
                   
                   if(isset($resAttr['attribute'])){
                       
                       $queryAttrRel = $db->query('SELECT * FROM `onbi_attributes_relationship`  WHERE `store_id` = ?
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
	        
	        $setAttributeIdOnbi = $_REQUEST["set_attribute_id_onbi"];
	        $setAttributeId = $_REQUEST["set_attribute_id"];
	        $setAttributeName = $_REQUEST["set_attribute_name"];
	        
	        $queryAttr = $db->query('SELECT * FROM `set_attributes`  WHERE `store_id` = ?
        			AND id = ?',array($storeId, $setAttributeId));
	        $resAttr = $queryAttr->fetch(PDO::FETCH_ASSOC);
	        
	        if($setAttributeIdOnbi == 'export_set_attr_ecommerce'){
	            
	            $attributeSetModel = new AttributeSetModel($db, null, $storeId);
	            $attributeSetModel->attribute_set_name = $resAttr['set_attribute'];
	            $setAttributeIdOnbi = $attributeSetModel->catalogProductAttributeSetCreate();
	            $setAttributeName = $resAttr['set_attribute'];
	            
	        }
	        
	        $query = $db->query('SELECT * FROM `onbi_attribute_set_relationship`  WHERE `store_id` = ?
        			AND set_attribute_id = ?',array($storeId, $setAttributeId));
	        $res = $query->fetch(PDO::FETCH_ASSOC);
	        
	        if(!isset($res['set_attribute_id'])){
	            
	            $query = $db->insert('onbi_attribute_set_relationship', array(
	                'store_id' => $storeId,
	                'set_attribute_id' => $setAttributeId,
	                'set_attribute' => $resAttr['set_attribute'],
	                'onbi_attribute_set_id' => $setAttributeIdOnbi,
	                'onbi_name' => $setAttributeName
	            ));
	            
	        }else{
	            
	            $query = $db->update('onbi_attribute_set_relationship', 
	                array("store_id", "set_attribute_id"), 
	                array($storeId, $setAttributeId), 
	                array('set_attribute' => $resAttr['set_attribute'],
    	                'onbi_attribute_set_id' => $setAttributeIdOnbi,
    	                'onbi_name' => $setAttributeName
    	            ));
	            
	        }
	        if ( $query ) {
	           echo "success|{$setAttributeIdOnbi}|{$setAttributeName}";
	        }
	        
	        break;
	        
	    case "set_variation_label_relationship_ecommerce":
	        
	        $setAttributeId = $_REQUEST["set_attribute_id"];
	        $variationLabel = $_REQUEST["variation_label"];
	        
	        if(isset($setAttributeId)){
    	        $query = $db->update('onbi_attribute_set_relationship',
    	            array("store_id", "set_attribute_id"),
    	            array($storeId, $setAttributeId),
    	            array('variation_label' => $variationLabel)
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
