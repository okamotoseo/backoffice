<?php
/**
 * Modelo para gerenciar produtos
 *
 * @package
 * @since 0.1
 */
class ManageFeedModel extends MainModel
{
    
    public $id;
    
    public $store_id;
    
    public $layout;
    
    public $name;
    
    public $term;
    
    public $brand;
    
    public $category;
    
    public $min_qty = 1;
    
    public $min_variations;
    
    public $min_price;
    
    public $created;
    
    public $records = 50;
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
            $this->account_id = $this->controller->userdata['account_id'];
            
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
//                     $val = str_replace("_", " ", $val);
                    $this->{$param} = $val;
                    
                }
            }
            
            return true;
            
        }else{
            
            $this->pagina_atual = 1;
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
        }
        
        if(isset ( $_POST['feed-filter-products'] ) OR isset ( $_POST['feed-filter-products'] )){
            
            foreach ( $_POST as $property => $value ) {
                
                if(!empty($value)){
                    
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                    
                }else{
                    
                    if( isset ( $_POST['feed-filter-products'] ) ){
                        
                        $required = array(
                            'layout',
                            'name',
                        );
                        
                        if( in_array($property, $required) ){
                            
                            $this->form_msg = "<div class='alert alert-danger alert-dismissable'>There are empty field. Data has not been sent.</div>";
                        }
                        
                    }
                }
            }
            
            if(!empty($this->form_msg)){
                
                return false;
            }
            
            return true;
            
        } else {
            
            if ( in_array('edit', $this->parametros )) {
                
                $key = array_search('edit', $this->parametros);
                
                $feedId = get_next($this->parametros, $key);
                $this->id  = is_numeric($feedId) ? $feedId :  '';
                if(!empty($this->id)){
                    
                    $this->Load();
                    
                }
                
            }
            
            if ( in_array('del', $this->parametros )) {
                
                $this->Delete();
            }
            
            return;
            
        }
        
    }
    
    public function Save(){
        
        if ( ! empty( $this->id ) ) {
            $query = $this->db->update('feed_filter_products', 'id', $this->id, array(
                'name' => $this->name,
                'term' => $this->term,
                'brand' => $this->brand,
                'category' => $this->category,
                'min_qty' => $this->min_qty,
                'min_variations' => $this->min_variations,
                'min_price' => $this->min_price,
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Produto atualizado com sucesso.</div>';
                return;
            }
        } else {
            
            $query = $this->db->query('SELECT layout FROM feed_filter_products WHERE `layout`= ? AND store_id = ?',
                array(  $this->layout, $this->store_id ) );
            $verify = $query->fetch(PDO::FETCH_ASSOC);
            if(!isset($verify['layout'])){
                $query = $this->db->insert('feed_filter_products', array(
                    'store_id' => $this->store_id,
                    'layout' => $this->layout,
                    'name' => $this->name,
                    'term' => $this->term,
                    'brand' => $this->brand,
                    'category' => $this->category,
                    'min_qty' => $this->min_qty,
                    'min_variations' => $this->min_variations,
                    'min_price' => $this->min_price,
                    'created' => date("Y-m-d H:i:s")
                )
                    );
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    return;
                } else {
                    $this->id = $this->db->last_id;
                    $this->form_msg = '<div class="alert alert-success alert-dismissable">Produto cadastrado com sucesso.</div>';
                    return;
                }
                
                
            }else{
                
                
                $this->form_msg = "<div class='alert alert-danger alert-dismissable'>Já existe um feed para esse layout: {$this->layout}</div>";
                
                return;
            }
            
        }
        
    }
    
    
    public function GetAvailableProductsFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "available_products.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "available_products.{$key} = {$this->$key} AND ";break;
                    case 'sku': $where_fields .= "available_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'parent_id': $where_fields .= "available_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'reference': $where_fields .= "available_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'title': $where_fields .= "available_products.{$key} LIKE UPPER('{$this->$key}') AND ";break;
                    case 'category': $where_fields .= "available_products.{$key} LIKE '{$this->$key}%' AND ";break;
                    case 'brand': $where_fields .= "available_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'stock': 
                        if($this->$key == 'withStock'){
                            $where_fields .= "available_products.quantity > 0 AND ";
                        }else{
                            $where_fields .= "available_products.quantity <= 0 AND ";
                        }
                        break;
                }
            }
            
        }
        
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
   
    
    /**
     * Lista produtos deisponiveis
     */
    public function ListFeed()
    {
        $query = $this->db->query("SELECT * FROM feed_filter_products WHERE store_id= ? ",
                array( $this->store_id)
        );
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    /**
     * Filtra produtos deisponiveis
     */
    public function GetAvailableProducts()
    {
        $where_fields = $this->GetAvailableProductsFilter();
        
        
        $sql = "SELECT  available_products.*,
        		product_descriptions.set_attribute_id,
        		product_descriptions.description
        		FROM available_products
        		LEFT JOIN product_descriptions ON available_products.id = product_descriptions.product_id
                AND product_descriptions.marketplace = 'default'
        		WHERE {$where_fields}
        		ORDER BY available_products.sku DESC
                LIMIT {$this->linha_inicial}, " . $this->records.";";
        
//         pre($sql);die;
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
   
    
    public function Load()
    {
        
        if(!empty($this->id) ){
            
            $query = $this->db->query('SELECT * FROM feed_filter_products WHERE `id`= ? AND store_id = ?',
                array( $this->id, $this->store_id ) );
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($fetch)){
                foreach($fetch as $key => $value)
                {
                    $column_name = str_replace('-','_',$key);
                    $this->{$column_name} = $value;
                }
            }else{
                return;
            }
            
        }else{
            
            return;
            
        }
        
    }
    
    
    public function Delete()
    {
        
        //TODO: Fazer verificação se existe venda do produto
        $key = array_search('del', $this->parametros);
        if(!empty($key)){
            $id = get_next($this->parametros, $key);
        }
        
        if(!empty($id)){
            
            $query = $this->db->query('DELETE FROM feed_filter_products WHERE store_id = ? AND `id`= ?',  array( $this->store_id, $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
} 