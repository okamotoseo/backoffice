<?php

class ProductsTempModel extends MainModel
{

    public $store_id;
    
    public $sku;
    
    public $product_id;
    
    public $title;
    
    public $color;
    
    public $variation;
    
    public $brand;
    
    public $reference;
    
    public $category; 
    
    public $qty; 
    
    public $price;
    
    public $sale_price;
    
    public $promotion_price;
    
    public $cost;
    
    public $weight; 
    
    public $height;
    
    public $width;
    
    public $length;
    
    public $ean;
    
    public $image;
    
    public $ncm;
    
    public $description; 
    
    public $set_attribute;
    
    public $type;
    
    public $categories_ids;
    
    public $websites;
    
    public $created_at; 
    
    public $updated_at; 
    
    public $visibility; 
    
    public $status; 
    
    public $products_sku = array();
    

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
        
        
        if ( empty( $this->product_id ) ) {
            return array();   
        }
        
        $sql = 'SELECT product_id FROM module_onbi_products_tmp WHERE store_id = ? AND product_id = ?';
        $query = $this->db->query($sql, array($this->store_id, $this->product_id));
        $res = $query->fetch(PDO::FETCH_ASSOC);
        $price = isset($this->price) ? $this->price : $this->sale_price ;
        if ( isset($res['product_id'])) {
            
            $query = $this->db->update("module_onbi_products_tmp", 
                array('store_id','product_id'), 
                array($this->store_id, $this->product_id), 
                array('sku' => $this->sku ,
                    'title' => $this->title,
                    'color' => $this->color,
                    'price' => $price,
                    'sale_price' => $this->sale_price,
                    'promotion_price' => $this->promotion_price,
                    'reference' => $this->product_id,
                    'variation' => $this->variation,
                    'weight' => $this->weight,
                    'height' => $this->height,
                    'width' => $this->width,
                    'length' => $this->length,
                    'ean' => $this->ean,
                    'image' => $this->image,
                    'ncm' => $this->ncm,
                    'brand' => $this->brand,
                    'cost' => $this->cost,
                    'description' => $this->description,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                    'visibility' => $this->visibility,
                    'status' => $this->status
                ));
        }else{
            
            $query = $this->db->insert("module_onbi_products_tmp", array(
                    'store_id' => $this->store_id,
                    'product_id' => $this->product_id,
                    'sku' => $this->sku ,
                    'title' => $this->title,
                    'color' => $this->color,
                    'price' => $price,
                    'sale_price' => $this->sale_price,
                    'promotion_price' => $this->promotion_price,
                    'reference' => $this->product_id,
                    'variation' => $this->variation,
                    'weight' => $this->weight,
                    'height' => $this->height,
                    'width' => $this->width,
                    'length' => $this->length,
                    'ean' => $this->ean,
                    'image' => $this->image,
                    'ncm' => $this->ncm,
                    'brand' => $this->brand,
                    'cost' => $this->cost,
                    'description' => $this->description,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                    'visibility' => $this->visibility,
                    'status' => $this->status
                ));
            
        }
        
        if ( ! $query ) {
//             pre($query);die;
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
            return;
        } else {
            
            $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
            return;
        }
            
        
        
    }
    
    public function TotalProductsTemp()
    {
        
        $sql = "SELECT count(*) as total FROM module_onbi_products_tmp WHERE store_id = ?";
        $query = $this->db->query( $sql, array($this->store_id));
        $total =  $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
        
    }
    
    public function ListProductsTemp()
    {
        $sql = "SELECT * FROM module_onbi_products_tmp 
        WHERE `store_id` = ? ORDER BY updated_at DESC 
        LIMIT {$this->linha_inicial},".QTDE_REGISTROS.";";
        
        $query = $this->db->query($sql, array($this->store_id));
       
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListProductsSku(){
        $sql = "SELECT sku FROM module_onbi_products_tmp WHERE store_id = ?";
        $query = $this->db->query( $sql, array($this->store_id));
        if ( ! $query ) {
            return array();
        }
        foreach($query->fetchAll(PDO::FETCH_ASSOC) as $key => $value)
        {
            $this->products_sku[] = $value['sku'];
            
        }
        
        return $this->products_sku;
        
    }
    
    
    public function Load()
    {
        if ( in_array('edit', $this->parametros )) {
            
            $key = array_search('edit', $this->parametros);
            
            $id = get_next($this->parametros, $key);
            
            $query = $this->db->query('SELECT * FROM module_onbi_products_tmp WHERE store_id = ? AND `product_id`= ?', array($this->store_id, $id ) );
            
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
            
            $query = $this->db->query('DELETE FROM module_onbi_products_tmp WHERE store_id = ? AND  `product_id`= ?', array($this->store_id, $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
} 