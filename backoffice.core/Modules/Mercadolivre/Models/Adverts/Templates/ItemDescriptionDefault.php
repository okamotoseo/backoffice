<?php

class ItemDescriptionModel extends MainModel
{
    
    public $store_id;
    
    public $sku;
    
    public $product_id;
    
    public $parent_id;
    
    public $category_id;
    
    public $title;
    
    public $description;
    
    public $textDescription = array();
    

    
    
    
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
    
    
    

    
    public function getTemplateDescription(){
        
        $textDescriptions = array();
        $description = '';
        
        $sqlAttr = "SELECT * FROM attributes_values WHERE attribute_id NOT IN (
        
            SELECT attribute FROM ml_attributes_relationship
            WHERE ml_attributes_relationship.store_id = {$this->store_id}
            AND ml_attributes_relationship.ml_category_id LIKE '{$this->category_id}'
            
    ) AND attribute_id NOT IN (
            SELECT attribute_id FROM ml_attributes_required
            WHERE ml_attributes_required.store_id = {$this->store_id}
            AND ml_attributes_required.category_id LIKE '{$this->category_id}'
            
    )
    AND attributes_values.store_id = {$this->store_id} AND attributes_values.product_id = {$this->product_id}
    AND attributes_values.value != ''";
        
        $query = $this->db->query($sqlAttr);
        $attrInf = $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($attrInf as $attribute => $value){
            $sql = "SELECT attribute FROM attributes WHERE store_id = {$this->store_id} AND alias = '{$value['attribute_id']}'";
            $queryAttr = $this->db->query($sql);
            $attributeName = $queryAttr->fetch(PDO::FETCH_ASSOC);
            if(!empty($attributeName['attribute']) AND !empty($value['value'])){
                
                $description .= $attributeName['attribute'].": ".$value['value'];
                $description .= "\n\n";
            }else{
                // atributos sem relacionamento com tabela attributes
            }
        }
        
        $sql = "SELECT sku, reference, weight, height, width, length FROM available_products
   WHERE store_id = {$this->store_id} AND id = '{$this->product_id}'";
        $query = $this->db->query($sql);
        $defaultAttr = $query->fetch(PDO::FETCH_ASSOC);
        foreach($defaultAttr as $key => $attr){
            switch($key){
                case "sku": $description .= "SKU: ".$attr."\n\n"; break;
                case "reference": $description .= "Referência: ".$attr."\n\n"; break;
                
                
            }
        }
        
        
        $textTitle = '';
        $textDescription = '';
        $sqlDescriptions = "SELECT title, description, marketplace FROM product_descriptions
   WHERE store_id = {$this->store_id} AND product_id = {$this->product_id} AND marketplace = 'Mercadolivre'";
        $queryMlDesc = $this->db->query($sqlDescriptions);
        $mlDescription =  $queryMlDesc->fetch(PDO::FETCH_ASSOC);
        
        if(isset($mlDescription['description']) AND !empty($mlDescription['description'])){
            $textDescription = $mlDescription['description'];
            
        }
        if(isset($mlDescription['title']) AND !empty($mlDescription['title'])){
            $textTitle = $mlDescription['title'];
        }
        
        if(empty($textDescription)){
            $sqlDescriptions = "SELECT title, description, marketplace FROM product_descriptions
       WHERE store_id = {$this->store_id} AND product_id = {$this->product_id} AND marketplace = 'default'";
            $queryDfDesc = $this->db->query($sqlDescriptions);
            $dfDescription =  $queryDfDesc->fetch(PDO::FETCH_ASSOC);
            if(isset($dfDescription['description']) AND !empty($dfDescription['description'])){
                $textDescription = $dfDescription['description'];
            }
            
        }
        
        if(empty($textTitle)){
            $sqlDescriptions = "SELECT title, marketplace FROM product_descriptions
       WHERE store_id = {$this->store_id} AND product_id = {$this->product_id} AND marketplace = 'default'";
            $queryDfDesc = $this->db->query($sqlDescriptions);
            $dfDescription =  $queryDfDesc->fetch(PDO::FETCH_ASSOC);
            if(isset($dfDescription['title']) AND !empty($dfDescription['title'])){
                $textTitle = $dfDescription['title'];
            }
            
        }
        
        if(empty($textDescription)){
            $sql = "SELECT description, title FROM available_products WHERE store_id = {$this->store_id} AND id = {$this->product_id}";
            $query = $this->db->query($sql);
            $apDescription =  $query->fetch(PDO::FETCH_ASSOC);
            $textDescription = !empty($apDescription['description']) ? $apDescription['description'] : '';
            
        }
        if(empty($textTitle)){
            $sql = "SELECT description, title FROM available_products WHERE store_id = {$this->store_id} AND id = {$this->product_id}";
            $query = $this->db->query($sql);
            $apDescription =  $query->fetch(PDO::FETCH_ASSOC);
            $textTitle = !empty($apDescription['title']) ? $apDescription['title'] : '';
            
        }
        
        $textDescription = str_replace("<br>", "\n", $textDescription);
        $textDescription = str_replace("</p>", "</p>\n", $textDescription);
        $textDescription = str_replace("</li>", "</li>\n", $textDescription);
        //    $description .= "\n\n";
        $description .= "\t".$textDescription;
        
        $this->title = substr ( $textTitle, 0, 60 );;
        $this->description = $description;
        
        
        return $textDescriptions;
        
    }
    
} 