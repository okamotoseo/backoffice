<?php

class SetAttributesRelationshipModel extends MainModel
{

    public $store_id;
    
    public $set_attribute_id;
    
    public $set_attribute;
    
    public $onbi_attribute_set_id;
    
    public $onbi_name;
    
    
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
        
        if(!isset($this->set_attribute_id)){
            return array();
        }
        
        $query = $this->db->query('SELECT * FROM `onbi_attribute_set_relationship`  WHERE `store_id` = ?
        			AND set_attribute_id = ?',array($this->store_id, $this->set_attribute_id));
        $res = $query->fetch(PDO::FETCH_ASSOC);
        
        if(!isset($res['set_attribute_id'])){
            
            $query = $this->db->insert('onbi_attribute_set_relationship', array(
                'store_id' => $this->store_id,
                'set_attribute_id' => $this->set_attribute_id,
                'set_attribute' => $this->set_attribute,
                'onbi_attribute_set_id' => $this->onbi_attribute_set_id,
                'onbi_name' => $this->onbi_name
            ));
            
            
        }else{
            
            $query = $this->db->update('onbi_attribute_set_relationship',
                array("store_id", "set_attribute_id"),
                array($this->store_id, $this->set_attribute_id),
                array('set_attribute' => $this->set_attribute,
                    'onbi_attribute_set_id' => $this->onbi_attribute_set_id,
                    'onbi_name' => $this->onbi_name
                ));
            
        }
        
        return  $this->set_attribute_id;
        
        
    }
   
    
    public function ListAttributeSetRelationship()
    {
        $sql = "SELECT * FROM onbi_attribute_set_relationship WHERE `store_id` = ? ";
        
        $query = $this->db->query($sql, array($this->store_id));
        
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListSetAttributesRelationship()
    {
        
        $sql = "SELECT set_attributes.id, set_attributes.set_attribute, 
        onbi_attribute_set_relationship.onbi_attribute_set_id,
        onbi_attribute_set_relationship.variation_label  
        FROM `set_attributes` LEFT JOIN onbi_attribute_set_relationship 
        ON onbi_attribute_set_relationship.set_attribute_id = set_attributes.id
        AND set_attributes.store_id = onbi_attribute_set_relationship.store_id
        WHERE  set_attributes.store_id = ? ORDER BY set_attributes.id DESC";
        $query = $this->db->query($sql, array($this->store_id));
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function GetSetAttributeFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "onbi_attribute_set_relationship.{$key} = {$this->$key} AND ";break;
                    case 'set_attribute_id': $where_fields .= "onbi_attribute_set_relationship.{$key} = {$this->$key} AND ";break;
                    case 'set_attribute': $where_fields .= "onbi_attribute_set_relationship.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'onbi_attribute_set_id': $where_fields .= "onbi_attribute_set_relationship.{$key} = {$this->$key} AND ";break;
                    case 'onbi_name': $where_fields .= "onbi_attribute_set_relationship.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
    public function GetSetAttributeRelationship()
    {
        
        $where_fields = $this->GetSetAttributeFilter();
        
        $sql = "SELECT * FROM onbi_attribute_set_relationship WHERE {$where_fields}";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
} 