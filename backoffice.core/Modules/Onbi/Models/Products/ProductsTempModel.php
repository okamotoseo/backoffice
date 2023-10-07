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
                	'type' => $this->type,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                    'visibility' => $this->visibility,
                    'status' => $this->status,
                	'flag' => 'T'
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
            		'type' => $this->type,
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
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
    
    public function ListProductsSku($offset, $limit){
        $sql = "SELECT sku FROM module_onbi_products_tmp WHERE store_id = ? LIMIT {$offset}, {$limit} ";
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
        $sql = "SELECT product_id FROM module_onbi_products_tmp WHERE store_id = ? LIMIT {$offset}, {$limit} ";
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
    
    
    public function GetProductsFilter()
    {
    	 
    	$where_fields = "";
    	$values = array();
    	$class_vars = get_class_vars(get_class($this));
    	foreach($class_vars as $key => $value){
    
    		if($this->{$key} != ''){
    			switch($key){
    				case 'store_id': $where_fields .= "module_onbi_products_tmp.{$key} = {$this->$key} AND ";break;
    				case 'product_id': $where_fields .= "module_onbi_products_tmp.{$key} = {$this->$key} AND ";break;
    				case 'sku': $where_fields .= "module_onbi_products_tmp.{$key} LIKE '{$this->$key}' AND ";break;
    				case 'title': $where_fields .= "module_onbi_products_tmp.{$key} LIKE '{$this->$key}' AND ";break;
    				case 'ean': $where_fields .= "module_onbi_products_tmp.{$key} LIKE '{$this->$key}' AND ";break;
    				case 'qty': $where_fields .= "module_onbi_products_tmp.{$key} >= {$this->$key} AND ";break;
    				case 'type': $where_fields .= "module_onbi_products_tmp.{$key} LIKE '{$this->$key}' AND ";break;
    				case 'available': $where_fields .= "module_onbi_products_tmp.{$key} = '{$this->$key}' AND ";break;
    			}
    		}
    		 
    	}
    	$where_fields = substr($where_fields, 0,-4);
    	 
    	return $where_fields;
    	 
    }
    
    public function TotalGetProducts(){
    	 
    	 
    	$where_fields = $this->GetProductsFilter();
    	 
    	 
    	$sql = "SELECT count(*) as total FROM module_onbi_products_tmp WHERE {$where_fields}";
    	 
    	if(isset($this->collection)){
    		 
    		$sql = "SELECT count(*) as total FROM module_onbi_products_tmp WHERE {$where_fields}";
    		 
    	}
    	 
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
    	 
    	$sql = "SELECT  module_onbi_products_tmp.* FROM module_onbi_products_tmp 
    	WHERE {$where_fields} ORDER BY module_onbi_products_tmp.product_id DESC
    	LIMIT {$this->linha_inicial}, {$this->records}";
    
    
    
    	$query = $this->db->query($sql);
    	if ( ! $query ) {
    		return array();
    	}
    
    
    	return $query->fetchAll(PDO::FETCH_ASSOC);
    	 
    }
    
    /**
     * Filtra produtos deisponiveis
     */
    public function GetProductIdBySku()
    {
    	$where_fields = $this->GetProductsFilter();
    
    	$sql = "SELECT  module_onbi_products_tmp.* FROM module_onbi_products_tmp
    	WHERE {$where_fields} ORDER BY module_onbi_products_tmp.product_id DESC
    	LIMIT {$this->linha_inicial}, {$this->records}";
    	$query = $this->db->query($sql);
    	if ( ! $query ) {
    		return array();
    	}
    	$result = $query->fetchAll(PDO::FETCH_ASSOC);
    	
    	foreach($result as $key => $productTmp){
    		
    		$sku = str_replace('-x', '', $productTmp['sku']);
    		$sku = trim($sku);
    		
    		if(!empty($sku)){
    			$sqlAp = "SELECT id FROM available_products WHERE store_id = {$this->store_id} AND  sku LIKE '{$sku}' LIMIT 1";
    			$queryAP = $this->db->query( $sqlAp );
    			$resAp = $queryAP->fetch(PDO::FETCH_ASSOC);
    			if(!empty($resAp['id'])){
    				$result[$key]['id'] = $resAp['id'];
    			}
    		}
    		
    	}
    	
    	return $result;
    
    }
    
    public function ListProductIdBySku()
    {
    	$sql = "SELECT * FROM module_onbi_products_tmp
    	WHERE `store_id` = ? ORDER BY updated_at DESC
    	LIMIT {$this->linha_inicial},".QTDE_REGISTROS.";";
    
    	$query = $this->db->query($sql, array($this->store_id));
    	 
    	if ( ! $query ) {
    
    		return array();
    	}
    	$result = $query->fetchAll(PDO::FETCH_ASSOC);
    	
    	foreach($result as $key => $productTmp){
    	
    		$sku = str_replace('-x', '', $productTmp['sku']);
    		$sku = trim($sku);
    	
    		if(!empty($sku)){
    			$sqlAp = "SELECT id FROM available_products WHERE store_id = {$this->store_id} AND  sku LIKE '{$sku}' LIMIT 1";
    			$queryAP = $this->db->query( $sqlAp );
    			$resAp = $queryAP->fetch(PDO::FETCH_ASSOC);
    			if(!empty($resAp['id'])){
    				$result[$key]['id'] = $resAp['id'];
    			}
    		}
    	
    	}
    	 
    	return $result;
    
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