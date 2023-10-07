<?php 

class AdvertsModel extends MainModel
{

    public $id;
    
    public $store_id;
    
    public $sku;
    
    public $title;
    
    public $price;
    
    public $available_quantity;
    
    public $sold_quantity;
    
    public $listing_type_id;
    
    public $condition_type;
    
    public $permalink;
    
    public $thumbnail;
    
    public $shipping;
    
    public $logistic_type;
    
    public $original_price;
    
    public $category_id;
    
    public $status;
    
    public $complete;
    
    public $created;
    
    public $updated;
    
    public $flag_import_variations;
    
    public $flag;
    
    public $parent_id;
    
    public $reference;
    
    public $category;
    
    public $brand;
    
    public $message;

    public $records = 50;
	
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
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
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        foreach ( $_POST as $property => $value ) {
	            if(!empty($value)){
	                if(property_exists($this,$property)){
	                    $value = $property == 'id' ? getNumbers($value) : $value ;
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
	            $this->Delete();
	        }
	        return;
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
	                case 'store_id': $where_fields .= "ml_products.{$key} = {$this->$key} AND ";break;
	                case 'id': $where_fields .= "ml_products.{$key} = '{$this->$key}' AND ";break;
	                case 'sku': $where_fields .= "ml_products.{$key} LIKE '{$this->$key}' AND ";break;
	                case 'title': $where_fields .= "ml_products.{$key} LIKE UPPER('{$this->$key}') AND ";break;
	                case 'status': $where_fields .= "ml_products.{$key} LIKE '{$this->$key}' AND ";break;
	                case 'logistic_type': $where_fields .= "ml_products.{$key} LIKE '{$this->$key}' AND ";break;
	               
	                
	            }
	        }
	    }
	    
	    $where_fields = substr($where_fields, 0,-4);
	    return $where_fields;
	}
	
	public function GetJoinAvailableProductsFilter()
	{
	    
	    $where_fields_join = '';
	    $values = array();
	    $class_vars = get_class_vars(get_class($this));
	    foreach($class_vars as $key => $value){
	        if(!empty($this->{$key})){
	            switch($key){
	                case 'parent_id': $where_fields_join .= " AND available_products.{$key} LIKE '{$this->$key}'";break;
	                case 'reference': $where_fields_join .= " AND available_products.{$key} LIKE '{$this->$key}'";break;
	                case 'category': $where_fields_join .= " AND available_products.{$key} LIKE '{$this->$key}%'";break;
	                case 'brand': $where_fields_join .= " AND available_products.{$key} LIKE '{$this->$key}'";break;
	                
	            }
	        }
	        
	    }
	    
	    
	    return $where_fields_join;
	    
	}
	
	
	public function TotalAdverts(){
	    
	    $where_fields = $this->GetAvailableProductsFilter();
	    
	    $where_fields_join = $this->GetJoinAvailableProductsFilter();
	    
	    if(!empty($where_fields_join)){
	        
	        $sql = "SELECT count(*) as total FROM ml_products
        		RIGHT JOIN available_products ON ml_products.sku = available_products.sku
                AND ml_products.store_id = available_products.store_id {$where_fields_join}
        		WHERE {$where_fields}";
	        
	    }else{
	        
	       $sql = "SELECT count(*) as total FROM ml_products WHERE {$where_fields} ;";
	    }
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	public function ListAdverts()
	{
		 
		 
		$sql = "SELECT ml_products.*, available_products.id as product_id FROM ml_products
		LEFT JOIN available_products ON ml_products.sku = available_products.sku
		AND available_products.store_id = ml_products.store_id
		WHERE ml_products.store_id= ? AND ml_products.sku != ''
		ORDER BY available_products.quantity DESC, ml_products.created DESC
		LIMIT {$this->linha_inicial}, " . $this->records.";";
		 
		$query = $this->db->query( $sql ,array( $this->store_id));
		if ( ! $query ) {
			return array();
		}
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		 
		return $result;
	
		 
	}
	
	public function GetAdverts()
	{
		 
		$where_fields = $this->GetAvailableProductsFilter();
		$where_fields_join = $this->GetJoinAvailableProductsFilter();
		$sql = "SELECT ml_products.*, available_products.id as product_id
		FROM ml_products
		RIGHT JOIN available_products ON ml_products.sku = available_products.sku
		AND ml_products.store_id = available_products.store_id {$where_fields_join}
		WHERE {$where_fields}
		ORDER BY ml_products.created DESC
		LIMIT {$this->linha_inicial}, " . $this->records.";";
		 
		$query = $this->db->query($sql);
		if ( ! $query ) {
			return array();
		}
		return $query->fetchAll(PDO::FETCH_ASSOC);
		 
	}
	
	
	
	
	
	
	
	
	
	public function ListIdAdverts()
	{
		 
		$idAdverts = array();
		 
		$query = $this->db->query('SELECT id, sku, permalink FROM `ml_products`
            WHERE `store_id` = ? ORDER BY created DESC',
				array( $this->store_id)
				);
		 
		if ( ! $query ) {
			return array();
		}
		 
		while($row = $query->fetch(PDO::FETCH_ASSOC)){
			 
			$query2 = $this->db->query("SELECT parent_id FROM `available_products`
            WHERE `store_id` = ? AND sku = ? ORDER BY sku DESC",
					array( $this->store_id, $row['sku'])
					);
			$parent = $query2->fetch(PDO::FETCH_ASSOC);
			 
			$idAdverts[ $parent['parent_id'] ] = $row;
		}
		 
		return $idAdverts;
		 
	}
	
	
	public function getAdvertsStatus(){
		$status = array();
	    $sql = "SELECT distinct status FROM ml_products WHERE store_id = {$this->store_id}";
	    $query = $this->db->query( $sql);
	    $result =  $query->fetchAll(PDO::FETCH_ASSOC);
	    
	    foreach($result as $key => $res){
	    	$status[] = $res['status'];
	    	
	    }
	    return $status;
	}
	
	
	
	public function ListAdvertsAvailableProducts()
	{
	    
	    $sql = "SELECT available_products.id as product_id,
                available_products.store_id,
			    available_products.sku,
			    available_products.parent_id,
			    available_products.title,
			    available_products.color,
			    available_products.variation,
			    available_products.brand,
			    available_products.reference,
			    available_products.category,
			    available_products.quantity,
			    available_products.price,
			    available_products.sale_price,
			    available_products.promotion_price,
			    available_products.cost,
			    available_products.weight,
			    available_products.height,
			    available_products.width,
			    available_products.length,
			    available_products.ean,
			    available_products.updated,
                ml_products.id,
        		ml_products.title as ml_title,
        		ml_products.thumbnail,
                ml_products.permalink,
                ml_products.status,
                ml_products.listing_type_id,
                ml_products.price,
                ml_products.created,
                ml_products.updated,
        		FROM ml_products
        		LEFT JOIN available_products ON  ml_products.sku = available_products.sku
                AND ml_products.store_id = available_products.store_id
        		WHERE ml_products.store_id= ?
                ORDER BY ml_products.created DESC
                LIMIT {$this->linha_inicial}, " . $this->records.";";
	    
	    $query = $this->db->query( $sql ,array( $this->store_id));
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	public function GetAdvertsAvailableProducts()
	{
	    
	    $where_fields = $this->GetAvailableProductsFilter();
	    $where_fields_join = $this->GetJoinAvailableProductsFilter();
	    $sql = "SELECT available_products.id as product_id,
                available_products.store_id,
			    available_products.sku,
			    available_products.parent_id,
			    available_products.title,
			    available_products.color,
			    available_products.variation,
			    available_products.brand,
			    available_products.reference,
			    available_products.category,
			    available_products.quantity,
			    available_products.price,
			    available_products.sale_price,
			    available_products.promotion_price,
			    available_products.cost,
			    available_products.weight,
			    available_products.height,
			    available_products.width,
			    available_products.length,
			    available_products.ean,
                ml_products.id,
        		ml_products.title as ml_title,
        		ml_products.thumbnail,
                ml_products.permalink,
                ml_products.status,
                ml_products.listing_type_id,
                ml_products.created,
                ml_products.updated,
                ml_products.price
        		FROM ml_products
        		RIGHT JOIN available_products ON ml_products.sku = available_products.sku
                AND ml_products.store_id = available_products.store_id {$where_fields_join}
        		WHERE {$where_fields}
                ORDER BY ml_products.updated DESC
                LIMIT {$this->linha_inicial}, " . $this->records.";";
	    
	    $query = $this->db->query($sql);
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	public function GetParentsAdverts()
	{
	    
	    if(!isset($this->sku)){
	        return array();
	    }
	    $sql = "SELECT available_products.id as product_id,
                available_products.store_id,
			    available_products.sku,
			    available_products.parent_id,
			    available_products.title,
			    available_products.color,
			    available_products.variation,
			    available_products.brand,
			    available_products.reference,
			    available_products.category,
			    available_products.quantity,
			    available_products.price,
			    available_products.sale_price,
			    available_products.promotion_price,
                ml_products.id,
        		ml_products.title as ml_title,
        		ml_products.thumbnail,
                ml_products.logistic_type,
                ml_products.permalink,
                ml_products.status,
                ml_products.listing_type_id,
                ml_products.created,
                ml_products.updated,
                ml_products.price
        		FROM available_products
        		RIGHT JOIN ml_products ON  ml_products.sku = available_products.sku
                AND ml_products.store_id = available_products.store_id
        		WHERE  available_products.store_id = {$this->store_id} AND available_products.parent_id LIKE '{$this->sku}'
                ORDER BY ml_products.updated DESC
                LIMIT {$this->linha_inicial}, " . $this->records.";";
	    
	    $query = $this->db->query($sql);
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	
	
	
	
	
	public function Load()
	{
	    if ( in_array('edit', $this->parametros )) {
	        
	        $key = array_search('edit', $this->parametros);
	        
	        $id = get_next($this->parametros, $key);
	        
	        $query = $this->db->query('SELECT * FROM ml_products WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
	        
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
	        
	        $query = $this->db->query('DELETE FROM ml_products_attributes 
            WHERE store_id = ? AND `product_id`= ?', array($this->store_id, $id ) );
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
	            return;
	            
	        }else{
	        
    	        $query = $this->db->query('DELETE FROM ml_products 
                WHERE store_id = ? AND  `id`= ?', array($this->store_id, $id ) );
    	        
    	        if ( ! $query ) {
    	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
    	            return;
    	        }
	        
	        }
	        
	        
	    } else {
	        
	        return;
	        
	    }
	    
	}

	
}

?>