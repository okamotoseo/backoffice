<?php

class AttributesRelationshipModel extends MainModel
{
    public $id;
    
    public $store_id;
    
    public $attribute;
    
    public $attribute_id;
    
    public $attribute_code;
    
    public $options;
    
    public $frontend_input;
    
    public $scope;
    
    public $is_unique;
    
    public $is_required;
    
    public $is_configurable;
    
    public $additional_fields;
    
    public $frontend_label;
    
    public $import_values;
    
    

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
    
    public function ValidateForm() {
        
        $this->pagina_atual = in_array('Page', $this->parametros ) ? get_next($this->parametros, array_search('Page', $this->parametros)) : 1 ;
        
        $this->linha_inicial = ($this->pagina_atual -1) * QTDE_REGISTROS;
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                }else{
                    $arr = array();
                    
                    if( in_array($property, $arr) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
            
            if ( in_array('edit', $this->parametros )) {
                
                $this->Load();
                
            }
            
            if ( in_array('del', $this->parametros )) {
                
                $this->Delete();
                
            }
            
            return;
            
        }
        
    }
    
    
    public function Save(){
        
        $sqlVerify = "SELECT * FROM `onbi_attributes_relationship` 
        WHERE `store_id` = {$this->store_id} AND attribute_id = {$this->attribute_id}";
        $query = $this->db->query($sqlVerify);
        $resVerify = $query->fetch(PDO::FETCH_ASSOC);
        
        if(!isset($resVerify['attribute_id'])){
            
            $query = $this->db->insert('onbi_attributes_relationship', array(
                'store_id' => $this->store_id,
                'attribute' => $this->attribute,
                'attribute_id' => $this->attribute_id,
                'attribute_code' => $this->attribute_code,
                'options' => $this->options,
                'frontend_input' => $this->frontend_input,
                'scope' => $this->scope,
                'is_unique' => $this->is_unique,
                'is_required' => $this->is_required,
                'is_configurable' => $this->is_configurable,
                'additional_fields' => $this->additional_fields,
                'frontend_label' => $this->frontend_label
            ));
            
            if(!$query){
                pre($query);
            }

            
        }else{
          
            $query = $this->db->update('onbi_attributes_relationship',
                array("store_id", "id"),
                array($this->store_id, $resVerify['id']),
                array('attribute' => $this->attribute,
                    'attribute_id' => $this->attribute_id,
                    'attribute_code' => $this->attribute_code,
                    'options' => $this->options,
                    'frontend_input' => $this->frontend_input,
                    'scope' => $this->scope,
                    'is_unique' => $this->is_unique,
                    'is_required' => $this->is_required,
                    'is_configurable' => $this->is_configurable,
                    'additional_fields' => $this->additional_fields,
                    'frontend_label' => $this->frontend_label
                ));
            
            if(!$query){
                pre($query);
            }
            
            
        }
        
    }
    
    
    
    public function TotalAttributes()
    {
        
        $sql = "SELECT count(*) as total FROM onbi_attributes_relationship WHERE store_id = ?";
        $query = $this->db->query( $sql, array($this->store_id));
        $total =  $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
        
    }
    
    public function ListAttributes()
    {
//         $sql = "SELECT * FROM onbi_attributes_relationship 
//         WHERE `store_id` = ? ORDER BY relationship DESC 
//         LIMIT {$this->linha_inicial},".QTDE_REGISTROS.";";
        
        $sql = "SELECT * FROM onbi_attributes_relationship
        WHERE `store_id` = ? ORDER BY relationship DESC;";
        
        $query = $this->db->query($sql, array($this->store_id));
       
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function GetAttributesToImport()
    {
        
        $additionalAttributes['additional_attributes'] = array();
        
        $sql = "SELECT * FROM onbi_attributes_relationship
        WHERE `store_id` = ? AND import_values = 1";

//         $sql = "SELECT * FROM onbi_attributes_relationship
//         WHERE `store_id` = ?";
        
        $query = $this->db->query($sql, array($this->store_id));
        
        if ( ! $query ) {
            
            return array();
        }
        $attributes =  $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($attributes as $key => $attr){
                
            $additionalAttributes['additional_attributes'][] = $attr['attribute_code'];
            
        }
        
        return $additionalAttributes;
        
    }
    
    
    public function GetAttributesRelationship()
    {
        
        
        $sql = "SELECT attribute_code, relationship FROM onbi_attributes_relationship
        WHERE `store_id` = ? AND relationship != ''";
        
        $query = $this->db->query($sql, array($this->store_id));
        
        if ( ! $query ) {
            
            return array();
        }
        $attributes =  $query->fetchAll(PDO::FETCH_ASSOC);
        
        $attributesRelationship = array();
        
        foreach($attributes as $key => $attr){
            
            $attributesRelationship[$attr['attribute_code']] = $attr['relationship'];
            
        }
        
        return $attributesRelationship;
        
    }

    
    public function Load()
    {
        if ( in_array('edit', $this->parametros )) {
            
            $key = array_search('edit', $this->parametros);
            
            $id = get_next($this->parametros, $key);
            
            $query = $this->db->query('SELECT * FROM onbi_attributes_relationship WHERE store_id = ? AND `product_id`= ?', array($this->store_id, $id ) );
            
            foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
            {
                $column_name = str_replace('-','_',$key);
                $this->{$column_name} = $value;
            }
            
        } else {
            
            return;
            
        }
        
    }
    
    public function Delete()
    {
        if ( in_array('del', $this->parametros )) {
            
            $key = array_search('del', $this->parametros);
            
            $id = get_next($this->parametros, $key);
            
            $query = $this->db->query('DELETE FROM onbi_attributes_relationship WHERE store_id = ? AND  `product_id`= ?', array($this->store_id, $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
} 