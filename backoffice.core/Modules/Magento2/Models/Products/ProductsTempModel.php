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
    
    public $attribute_set_id;
    
    public $type_id;
    
    public $categories_ids;
    
    public $websites;
    
    public $created_at; 
    
    public $updated_at; 
    
    public $visibility; 
    
    public $status; 
    
    public $products_sku;
    
    public $ids = array();
    
    public $records = '50';
    

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
    	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['magento-products-filter'] ) ) {
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
    
    public function Save(){
        
        
        if ( empty( $this->product_id ) ) {
            return array();   
        }
        
        $sql = 'SELECT product_id FROM mg2_products_tmp WHERE store_id = ? AND product_id = ?';
        $query = $this->db->query($sql, array($this->store_id, $this->product_id));
        $res = $query->fetch(PDO::FETCH_ASSOC);
        $price = isset($this->price) ? $this->price : $this->sale_price ;
        if ( isset($res['product_id'])) {
            
            $query = $this->db->update("mg2_products_tmp", 
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
                	'qty' => $this->qty,
                	'category' => $this->category,
                    'image' => $this->image,
                    'ncm' => $this->ncm,
                    'brand' => $this->brand,
                    'cost' => $this->cost,
                	'attribute_set_id' => $this->attribute_set_id,
                    'description' => $this->description,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                	'type_id' => $this->type_id,
                    'visibility' => $this->visibility,
                    'status' => $this->status
                ));
        }else{
            
            $query = $this->db->insert("mg2_products_tmp", array(
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
            		'qty' => $this->qty,
            		'category' => $this->category,
                    'image' => $this->image,
                    'ncm' => $this->ncm,
                    'brand' => $this->brand,
                    'cost' => $this->cost,
            		'attribute_set_id' => $this->attribute_set_id,
                    'description' => $this->description,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                    'type_id' => $this->type_id,
            		'visibility' => $this->visibility,
                    'status' => $this->status
                ));
            
        }
        
        if ( ! $query ) {
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
            return;
        } else {
            
            $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
            return;
        }
            
        
        
    }
    
    
    
    public function TotalProductsTemp()
    {
    	$where_fields = $this->GetProductsTempFilter();
    	
        $sql = "SELECT count(*) as total FROM mg2_products_tmp WHERE  {$where_fields}";
        $query = $this->db->query( $sql);
        $total =  $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
        
    }
    
    
    public function GetProductsTempFilter()
    {
    	 
    	$where_fields = "";
    	$values = array();
    	$class_vars = get_class_vars(get_class($this));
    	foreach($class_vars as $key => $value){
    
    		if($this->{$key} != ''){
    			switch($key){
    				case 'store_id': $where_fields .= "mg2_products_tmp.{$key} = {$this->$key} AND ";break;
    				case 'product_id': $where_fields .= "mg2_products_tmp.{$key} = ".trim($this->$key)." AND ";break;
    				case 'sku': $where_fields .= "mg2_products_tmp.{$key} LIKE '".trim($this->$key)."' AND ";break;
    				case 'parent_id': $where_fields .= "mg2_products_tmp.{$key} LIKE '".trim($this->$key)."' AND ";break;
    				case 'title': $where_fields .= "mg2_products_tmp.{$key} LIKE '".trim($this->$key)."' AND ";break;
    				case 'ean': $where_fields .= "mg2_products_tmp.{$key} LIKE '".trim($this->$key)."' AND ";break;
    				case 'reference': $where_fields .= "mg2_products_tmp.{$key} LIKE '".trim($this->$key)."' AND ";break;
    				case 'category': $where_fields .= "mg2_products_tmp.{$key} LIKE '".trim($this->$key)."' AND ";break;
    				case 'brand': $where_fields .= "mg2_products_tmp.{$key} = '".trim($this->$key)."' AND ";break;
    				case 'image': $where_fields .= "mg2_products_tmp.{$key} LIKE '".trim($this->$key)."' AND ";break;
    				case 'qty': $where_fields .= "mg2_products_tmp.{$key} >= ".trim($this->$key)." AND ";break;
    				case 'status': $where_fields .= "mg2_products_tmp.{$key} = '".trim($this->$key)."' AND ";break;
    			}
    		}
    		 
    	}
    	$where_fields = substr($where_fields, 0,-4);
    	 
    	return $where_fields;
    	 
    }
    
    /**
     * Filtra produtos deisponiveis
     */
    public function GetProductsTemp()
    {
    	$where_fields = $this->GetProductsTempFilter();
    	 
    	$sql = "SELECT * FROM mg2_products_tmp
    	WHERE  {$where_fields} ORDER BY updated_at DESC
    	LIMIT {$this->linha_inicial}, {$this->records}";
    	
    	$query = $this->db->query($sql);
    	if ( ! $query ) {
    		return array();
    	}
    
    
    	return $query->fetchAll(PDO::FETCH_ASSOC);
    	 
    }
    
    public function ListProductsTemp()
    {
        $sql = "SELECT * FROM mg2_products_tmp 
        WHERE `store_id` = ? ORDER BY updated_at DESC 
        LIMIT {$this->linha_inicial}, {$this->records};";
        
        $query = $this->db->query($sql, array($this->store_id));
       
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListProductsSku($offset, $limit){
        echo $sql = "SELECT sku FROM mg2_products_tmp WHERE store_id = ? LIMIT {$offset}, {$limit} ";
        echo "<br>";
        $query = $this->db->query( $sql, array($this->store_id));
        if ( ! $query ) {
            return array();
        }
        unset($this->products_sku);
        foreach($query->fetchAll(PDO::FETCH_ASSOC) as $key => $value)
        {
            $this->products_sku[] = $value['sku'];
            
        }
        
        return $this->products_sku;
        
    }
    
    
    public function ListProductsIds($offset, $limit){
        $sql = "SELECT product_id FROM mg2_products_tmp WHERE store_id = ? LIMIT {$offset}, {$limit} ";
        $query = $this->db->query( $sql, array($this->store_id));
        if ( ! $query ) {
            return array();
        }
        unset($this->ids);
        foreach($query->fetchAll(PDO::FETCH_ASSOC) as $key => $value)
        {
            $this->ids[] = $value['product_id'];
            
        }
        
        return $this->ids;
        
    }
    
    
    public function Load()
    {
        if ( in_array('edit', $this->parametros )) {
            
            $key = array_search('edit', $this->parametros);
            
            $id = get_next($this->parametros, $key);
            
            $query = $this->db->query('SELECT * FROM mg2_products_tmp WHERE store_id = ? AND `product_id`= ?', array($this->store_id, $id ) );
            
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
            
            $query = $this->db->query('DELETE FROM mg2_products_tmp WHERE store_id = ? AND  `product_id`= ?', array($this->store_id, $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
} 