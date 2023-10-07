<?php

class CategoriesModel extends MainModel
{

    public $store_id;
    
    public $category;
    
    public $categories;
    
    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->parametros = $this->controller->parametros;
        
        $this->userdata = $this->controller->userdata;
        
        $this->store_id = $this->controller->userdata['store_id'];
    }

    
    public function ListCategoriesRoot()
    {
        $root = array();
        $sql = "SELECT distinct root FROM module_shopee_categories_hierarchy Order BY root ASC";
        $query = $this->db->query($sql);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as $i => $res){
            $root[] = array('id' => $res['id'], 'root' => $res['root'], 'hierarchy' =>  $res['hierarchy'], 'id_category' => $res['id_category']);
        }
        return $root;
    }
    
    public function ListCategoriesHierarchy()
    {
            
        $sql = "SELECT * FROM module_shopee_categories_hierarchy Order BY hierarchy ASC";
        $query = $this->db->query($sql);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as $i => $res){
            $hierarchy[] = array('id' => $res['id'], 'root' => $res['root'], 'hierarchy' =>  $res['hierarchy'], 'id_category' => $res['id_category']);
        }
        
//         pre($hierarchy);die; 
        return $hierarchy;
    }
    
    public function ListCategoriesRelationship()
    {
        if(!isset($this->store_id)){
            return array();
        }
            
        $sql = "SELECT * FROM module_shopee_categories_relationship WHERE store_id = {$this->store_id} ";
        $query = $this->db->query($sql);
        $relationship = $query->fetchAll(PDO::FETCH_ASSOC); 
        return $relationship;
    }
    
    public function ListCategoiesIdsRelationship()
    {
        if(!isset($this->store_id)){
            return array();
        }
        $ategoriesIdsRelationship = array();
        $relationship = $this->ListCategoriesRelationship();
        
        foreach($relationship as $k => $val){
            $ategoriesIdsRelationship[$val['category_id']] = $val;
        }
        return $ategoriesIdsRelationship;
    }
    
    public function ListCategoriesXmlRelationship()
    {
        if(!isset($this->store_id)){
            return array();
        }
        
        $sql = "SELECT * FROM module_shopee_categories_xml_relationship WHERE store_id = {$this->store_id} ";
        $query = $this->db->query($sql);
        $relationship = $query->fetchAll(PDO::FETCH_ASSOC);
        return $relationship;
    }
    public function ListHierarchyRelationship()
    {
        if(!isset($this->store_id)){
            return array();
        }
        $ategoriesIdsRelationship = array();
        $relationship = $this->ListCategoriesXmlRelationship();
        foreach($relationship as $k => $val){
            $ategoriesIdsRelationship[$val['hierarchy']] = $val;
        }
        return $ategoriesIdsRelationship;
    }
    
} 