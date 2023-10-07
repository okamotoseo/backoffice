<?php

class AttributesValuesModel extends MainModel
{
    public $id;
    
    public $store_id;
    
    public $product_id;
    
    public $attribute_id;
    
    public $attribute_code;
    
    public $value_id;
    
    public $attributesValues = array();
    

    public function __construct($db = false,  $controller = null, $storeId = null)
    {
        $this->db = $db;
        
        $this->store_id = $storeId;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
        }

        
    }
    
    
    public function Save(){

        foreach ($this->attributesValues as $attribute => $value){

            $sql = 'SELECT * FROM attributes_values WHERE store_id = ? AND product_id = ? AND attribute_id = ?';
            $query = $this->db->query($sql, array($this->store_id, $this->product_id, $value['attribute_id']));
            $res = $query->fetch(PDO::FETCH_ASSOC);
            
            if ( !isset($res['product_id']) AND !empty($value['value'])) {
                
                $query = $this->db->insert('attributes_values', array(
                    'store_id' => $this->store_id,
                    'product_id' => $this->product_id,
                    'attribute_id' => $value['attribute_id'],
                    'value' => strip_tags($value['value']),
                    'marketplace' => 'Ecommerce'
                ));
                
            } else {
                
//                 if(!empty($value['value']) AND isset($res['product_id'])){
//                     $sqlUpdate = "UPDATE attributes_values SET  value = '".strip_tags($value['value'])."', marketplace = 'Ecommerce' WHERE store_id = {$this->store_id}
//                     AND product_id = {$this->product_id} AND attribute_id = '{$value['attribute_id']}'";
//                     $query = $this->db->query($sqlUpdate);
//                 }
                
//                 if(empty($value['value']) AND isset($res['product_id'])){
//                     $sqlDelete = "DELETE FROM attributes_values  WHERE store_id = {$this->store_id}
//                     AND product_id = {$this->product_id} AND attribute_id = '{$value['attribute_id']}'";
//                     $query = $this->db->query($sqlDelete);
//                 }
                

            }
        }
        
        
    }
    
    
    public function GetProductAttributesValues(){
        
        if(!isset($this->product_id)){
            return array();
        }
        
        $sql = "SELECT * FROM attributes_values WHERE store_id = {$this->store_id} AND product_id = {$this->product_id}";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function GetAttributesValuesOptions()
    {
        
        if(!isset($this->attribute_code)){
            
            return array();
        }
        
        $sql = "SELECT options FROM onbi_attributes_relationship WHERE store_id = {$this->store_id} AND attribute_code LIKE '{$this->attribute_code}'";
        
        $query = $this->db->query($sql);
        
        $attrValues = $query->fetch(PDO::FETCH_ASSOC);
        
        $options = isset($attrValues['options']) ?  json_decode($attrValues['options']) : array() ;
        
        return $options;
        
        
        
    }
    
    public function GetAttributesRelationshipValue()
    {
        
        if(!isset($this->value_id)){
            
            return array();
        }
        
        $value = '';
        
        $options = $this->GetAttributesValuesOptions();
        if(!empty($options)){
            foreach($options as $key => $option){
                
                if($option->value == $this->value_id){
                    $value = $option->label;
                }
                
            }
        }
        $value = !empty($value) ? $value : $this->value_id ;
        return $value;
        
        
        
    }
    
    
    
    
    
} 