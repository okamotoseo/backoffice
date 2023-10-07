<?php

class ProductsModel extends MainModel
{
    
    public $store_id;
    
    public $id;
    
    public $item_group_id;
    
    public $title;
    
    public $description;
    
    public $shipping_weight;
    
    public $product_type;
    
    public $availability;
    
    public $price;
    
    public $sale_price;
    
    public $brand;
    
    public $color;
    
    public $mpn;
    
    public $size;
    
    public $gtin;
    
    public $link;
    
    public $image_link;
    
    public $additional_image_link_0;
    
    public $additional_image_link_1;
    
    public $additional_image_link_2;
    
    public $additional_image_link_3;
    
    public $additional_image_link_4;
    
    public $additional_image_link_5;
    
    public $additional_image_link_6;
    
    public $records = '50';
    
    
    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
        }
        
        if(!defined('QTDE_REGISTROS')){
            
            define('QTDE_REGISTROS', 50);
            
        }
        
        
    }
    
    
    public function ValidateForm() {
        
        if(in_array('records', $this->parametros )){
            $records = get_next($this->parametros, array_search('records', $this->parametros));
            $this->records = isset($records) ? $records : QTDE_REGISTROS ;
        }
        
        if(in_array('Page', $this->parametros )){
            
            $this->pagina_atual =  get_next($this->parametros, array_search('Page', $this->parametros));
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
            
            foreach($this->parametros as $key => $param){
                if(property_exists($this,$param)){
                    $val = get_next($this->parametros, $key);
                    $val = str_replace("_x_", "%", $val);
                    $val = str_replace("_", " ", $val);
                    $this->{$param} = $val;
                    
                }
            }
            
            return true;
            
        }else{
            
            $this->pagina_atual = 1;
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
        }
        // 	    pre($_POST);die;
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
                if($value != ''){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                }else{
                    $req = array();
                    
                    if( in_array($property, $req) ){
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
                
                $key = array_search('del', $this->parametros);
                
                $this->id = get_next($this->parametros, $key);
                
                $this->Delete();
                
            }
            
            return;
            
        }
        
    }
    
    
    public function TotalProducts(){
        
        
        $sql = "SELECT count(*) as total FROM module_google_xml_products WHERE store_id = {$this->store_id}";
        
        $query = $this->db->query( $sql);
        $total =  $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
        
    }
    
    public function ListProducts(){
        
        
        $query = $this->db->query("SELECT module_google_xml_products.* FROM module_google_xml_products
             WHERE module_google_xml_products.store_id= ?
    		ORDER BY module_google_xml_products.id DESC
            LIMIT {$this->linha_inicial}, {$this->records}",
            array( $this->store_id)
        );
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    
    
    
    public function GetProductsFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            
            if($this->{$key} != ''){
                switch($key){
                    case 'store_id': $where_fields .= "module_google_xml_products.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "module_google_xml_products.{$key} LIKE '{$this->$key}'  AND ";break;
                    case 'item_group_id': $where_fields .= "module_google_xml_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'title': $where_fields .= "module_google_xml_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'gtin': $where_fields .= "module_google_xml_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'npm': $where_fields .= "module_google_xml_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'brand': $where_fields .= "module_google_xml_products.{$key} LIKE '{$this->$key}' AND ";break;
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
    public function TotalGetProducts(){
        
        $where_fields = $this->GetProductsFilter();
        
        $sql = "SELECT count(*) as total FROM module_google_xml_products WHERE {$where_fields}";
        $query = $this->db->query( $sql);
        $total =  $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
        
    }
    
    /**
     * Filtra produtos deisponiveis
     */
    public function GetProducts()
    {
        $where_fields = $this->GetProductsFilter();
        
        $sql = "SELECT module_google_xml_products.* FROM module_google_xml_products 
            WHERE {$where_fields} ORDER BY module_google_xml_products.id DESC
            LIMIT {$this->linha_inicial}, {$this->records}";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    /**
     * Filtra produtos deisponiveis sem limit
     */
    public function GetProductsNoLimits()
    {
        $where_fields = $this->GetProductsFilter();
        
        $sql = "SELECT module_google_xml_products.* FROM module_google_xml_products
        	WHERE {$where_fields} ORDER BY module_google_xml_products.id DESC";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    public function Delete()
    {
        
        if(empty($this->id)){
            return array();
        }
        
        $query = $this->db->query('DELETE FROM module_google_xml_products
        WHERE store_id = ? AND `id`= ?', array($this->store_id, $this->id ) );
        
        if ( ! $query ) {
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
            
        }else{
            
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
            
        }
        
    }
    
}
?>