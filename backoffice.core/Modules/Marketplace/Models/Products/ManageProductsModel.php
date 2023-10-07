<?php 

class ManageProductsModel extends MainModel
{

    public $id;
    
    public $store_id;
    
	public $seller_store_id;
	
	public $seller_product_id;
	
	public $seller_ean;
	
	public $seller_sku;
	
	public $seller_parent_id;
	
	public $seller_title;
	 	public $seller_crossdocking;
	 	public $seller_choice;
	 	public $seller_set_attribute;
	 	public $seller_variation_type;
	 	public $seller_variation;
	 	public $seller_category;
	
	public $seller_collection;
	
	public $seller_price;
	
	public $seller_color;
	
	public $seller_reference;
	
	public $seller_weight;
	
	public $seller_height;
	
	public $seller_length;
	
	public $seller_width;
	
	public $seller_description;
	
	public $product_id_relationship;
	
	public $created;
	
	public $updated;
	
	public $status;
	
	public $records = '25';


	
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
	        
	        define('QTDE_REGISTROS', 25);
	        
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
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        foreach ( $_POST as $property => $value ) {
	            if(!empty($value)){
	                if(property_exists($this,$property)){
	                    
	                    $this->{$property} = $value;
	                    
	                }
	            }
	            
	        }
	        
	        return true;
	        
	    } else {
	        
	        return;
	        
	    }
	    
	}
	
	
	public function TotalProducts(){
	    
	    
		$where_fields = $this->GetProductsFilter();
		
		if(!empty($where_fields)){
			$where_fields = 'WHERE '.$where_fields;
		}
		
	   	$sql = "SELECT count(distinct module_marketplace_products.seller_parent_id) as total FROM module_marketplace_products {$where_fields}";
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	public function ListProducts(){
        $query = $this->db->query("SELECT module_marketplace_products.*, available_products.id as product_id,
	  	 	available_products.title, available_products.ean, available_products.parent_id, available_products.created as published,
        		available_products.sku,
        		available_products.brand,
        		available_products.color,
        		available_products.height,
        		available_products.length,
        		available_products.width,
        		available_products.weight,
        		available_products.variation,
        		available_products.quantity,
        		available_products.category,
        		available_products.collection,
        		available_products.sale_price
    		FROM module_marketplace_products 
	        	LEFT JOIN available_products  ON available_products.ean = module_marketplace_products.seller_ean
	        	AND available_products.store_id = {$this->store_id} 
    		ORDER BY module_marketplace_products.seller_ean DESC
            LIMIT {$this->linha_inicial}, {$this->records}"
        );


        
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	public function ListProductsByParent(){

		$query = $this->db->query("SELECT module_marketplace_products.*
    		FROM module_marketplace_products 
    		GROUP BY module_marketplace_products.seller_parent_id
            LIMIT {$this->linha_inicial}, {$this->records}");
			 
		if ( ! $query ) {
			return array();
		}
		$products = $query->fetchAll(PDO::FETCH_ASSOC);
		foreach ($products as $k => $val){
				 
			$queryDefault = $this->db->query("SELECT available_products.id as product_id,
					available_products.title, 
					available_products.ean, 
					available_products.parent_id, 
					available_products.created as published,
					available_products.brand,
					 available_products.sku,
					available_products.color,
					available_products.height,
					available_products.length,
					available_products.width,
					available_products.weight,
					available_products.variation,
					available_products.quantity,
					available_products.category,
					available_products.collection,
					available_products.sale_price
			FROM available_products  WHERE store_id = {$this->store_id} AND ean LIKE '{$val['seller_ean']}'");
			$default = $queryDefault->fetch(PDO::FETCH_ASSOC);
			$products[ $k ]['default'] = !empty($default) ? $default : array() ;;
			
			$query2 = $this->db->query("SELECT module_marketplace_products.*
    		FROM module_marketplace_products  WHERE seller_store_id = {$val['seller_store_id']} AND seller_parent_id LIKE '{$val['seller_parent_id']}'");
			$parents = $query2->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($parents as $i => $parent){
				
				$queryDefault = $this->db->query("SELECT available_products.id as product_id,
					available_products.title, 
					available_products.ean, 
					available_products.parent_id, 
					available_products.created as published,
					available_products.brand,
					available_products.sku,
					available_products.color,
					available_products.height,
					available_products.length,
					available_products.width,
					available_products.weight,
					available_products.variation,
					available_products.quantity,
					available_products.category,
					available_products.collection,
					available_products.sale_price
				FROM available_products  WHERE store_id = {$this->store_id} AND ean LIKE '{$parent['seller_ean']}'
				AND variation LIKE '{$parent['seller_variation']}'");
				$default = $queryDefault->fetch(PDO::FETCH_ASSOC);
				$parents[ $i ]['default'] = !empty($default) ? $default : array() ;
				
			}
			
			$products[ $k ]['variations'] = $parents;
		}
		
		return $products;
			 
	}
	
	public function GetProductsFilter()
	{
	    
	    $where_fields = "";
	    $values = array();
	    $class_vars = get_class_vars(get_class($this));
	    foreach($class_vars as $key => $value){
	        if(!empty($this->{$key})){
	            switch($key){
	                case 'seller_store_id': $where_fields .= "module_marketplace_products.{$key} = {$this->$key} AND ";break;
	                case 'seller_title': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'seller_product_id': $where_fields .= "module_marketplace_products.{$key} = {$this->$key} AND ";break;
	                case 'seller_sku': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'seller_category': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'seller_collection': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'seller_reference': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'seller_ean': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'seller_parent_id': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
	               
	            }
	        }
	        
	    }
	    
	    $where_fields = substr($where_fields, 0,-4);
	    
	    return $where_fields;
	    
	}
	/**
	 * Filtra produtos deisponiveis
	 */
	public function GetProducts()
	{
	    $where_fields = $this->GetProductsFilter();
	    
	    if(!empty($where_fields)){
	    	$where_fields = 'WHERE '.$where_fields;
	    	$whereStatus  = ' AND ';
	    }else{
	    	$whereStatus  = ' WHERE ';
	    }
	    
    	if(!empty($this->status)){
    		if($this->status == 'active'){
    			$where_fields .= "{$whereStatus} module_marketplace_products.seller_ean IN (
    			SELECT ean as seller_ean FROM `available_products` WHERE store_id = {$this->store_id}
    			)";
    		}
    		 
    		if($this->status == 'pending'){
    			$where_fields .= "{$whereStatus} module_marketplace_products.seller_ean NOT IN (
    			SELECT ean as seller_ean FROM `available_products` WHERE store_id = {$this->store_id}
    			)";
    		}
    	}
	    
	    	$sql = "SELECT module_marketplace_products.*
    				FROM module_marketplace_products {$where_fields}
    				GROUP BY module_marketplace_products.seller_parent_id
    				LIMIT {$this->linha_inicial}, {$this->records}";
// 	    	pre($sql);die;
    		$query = $this->db->query($sql);
    		
    		if ( ! $query ) {
    			return array();
    		}
    		$products = $query->fetchAll(PDO::FETCH_ASSOC);
    		foreach ($products as $k => $val){
    				
    			$queryDefault = $this->db->query("SELECT available_products.id as product_id,
    					available_products.title,
    					available_products.ean,
    					available_products.parent_id,
    					available_products.created as published,
    					available_products.brand,
    					available_products.sku,
    					available_products.color,
    					available_products.height,
    					available_products.length,
    					available_products.width,
    					available_products.weight,
    					available_products.variation,
    					available_products.quantity,
    					available_products.category,
    					available_products.collection,
    					available_products.sale_price
    					FROM available_products  WHERE store_id = {$this->store_id} AND ean LIKE '{$val['seller_ean']}'");
    			$default = $queryDefault->fetch(PDO::FETCH_ASSOC);
    			$products[ $k ]['default'] = !empty($default) ? $default : array() ;
    			
    				
    			$query2 = $this->db->query("SELECT module_marketplace_products.*
    					FROM module_marketplace_products  WHERE seller_store_id = {$val['seller_store_id']} AND seller_parent_id LIKE '{$val['seller_parent_id']}'");
    			$parents = $query2->fetchAll(PDO::FETCH_ASSOC);
    				
    			foreach($parents as $i => $parent){
    		
    				$queryDefault = $this->db->query("SELECT available_products.id as product_id,
    						available_products.title,
    						available_products.ean,
    						available_products.parent_id,
    						available_products.created as published,
    						available_products.brand,
    						available_products.sku,
    						available_products.color,
    						available_products.height,
    						available_products.length,
    						available_products.width,
    						available_products.weight,
    						available_products.variation,
    						available_products.quantity,
    						available_products.category,
    						available_products.collection,
    						available_products.sale_price
    						FROM available_products  WHERE store_id = {$this->store_id} AND ean LIKE '{$parent['seller_ean']}'
    					AND variation LIKE '{$parent['seller_variation']}'");
    					$default = $queryDefault->fetch(PDO::FETCH_ASSOC);
    					
    					$parents[ $i ]['default'] = !empty($default) ? $default : array() ;
    		
    			}
    				
    			$products[ $k ]['variations'] = $parents;
    		}
    		
    		return $products;
	    
	}
	
	/**
	 * Get seller categories
	 */
	
	public function getSellerCategories(){
		
		$sql = "SELECT distinct seller_category FROM module_marketplace_products ORDER BY seller_category ASC";
			
		$query = $this->db->query($sql);
		if ( ! $query ) {
			return array();
		}
			
			
		return $query->fetchAll(PDO::FETCH_ASSOC);
		
		
	}
	
	/**
	 * Filtra produtos disponiveis
	 */
	public function getSellersByProducts()
	{
		
		 
		$sql = "SELECT module_marketplace_products.seller_store_id, stores.store 
		FROM module_marketplace_products LEFT JOIN stores
		ON module_marketplace_products.seller_store_id = stores.id
		GROUP BY module_marketplace_products.seller_store_id";
		 
		$query = $this->db->query($sql);
		if ( ! $query ) {
			return array();
		}
		 
		 
		return $query->fetchAll(PDO::FETCH_ASSOC);
		 
	}
	
	
}

?>