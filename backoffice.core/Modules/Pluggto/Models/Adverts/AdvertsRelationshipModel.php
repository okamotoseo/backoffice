<?php 

class AdvertsRelationshipModel extends MainModel
{

    public $id;
    
    public $store_id;
    
    public $sku;
    
    public $product_id;
    
    public $variation_id;
    
    public $name;
    
    public $attribute;
    
    public $value;
    
    public $information;
    
    public $httpCode;
    
    public $status;
    
    public $flag;
    
    public $records = 150;
	
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
        }
        
        if(!defined('QTDE_REGISTROS')){
            
            define('QTDE_REGISTROS', 150);
            
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
// 	        echo 123;die;
	        $this->pagina_atual = 1;
	        $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
	    }
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        foreach ( $_POST as $property => $value ) {
	            if(!empty($value)){
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
	            
	            $this->Delete();
	            
	        }
	        
	        return;
	        
	    }
	    
	}
	
	
	public function ListIdAdverts()
	{
	    
	    $idAdverts = array();
	    
	    $query = $this->db->query('SELECT id, sku, permalink FROM `ml_products`  
            WHERE `store_id` = ?  ORDER BY id DESC',
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
	
	
	
	
	
	
	public function TotalAdverts(){
	    
	   $where_fields = $this->GetMlProductsFilter();
	        
       $sql = "SELECT count(DISTINCT ml_products.id) as total FROM  ml_products WHERE {$where_fields}";
	    
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	public function ListMlProducts(){
	 
// 	    $sql = "SELECT ml_products_attributes.product_id, ml_products.* FROM  ml_products_attributes
//                 LEFT JOIN ml_products ON ml_products.id = ml_products_attributes.product_id
//                 WHERE ml_products_attributes.store_id = ?
//                 GROUP BY ml_products_attributes.product_id
//                 LIMIT {$this->linha_inicial}, " . $this->records.";";
	    

	    $sql = "SELECT ml_products.*  FROM  ml_products WHERE ml_products.store_id = ? 
                 LIMIT {$this->linha_inicial}, " . $this->records.";";
	    
	    $query = $this->db->query( $sql ,array( $this->store_id));
	    
	    $res = $query->fetchAll(PDO::FETCH_ASSOC);
	    
	    foreach($res as $key => $row){
	    	
	    	$sqlVerify = "SELECT  id FROM `available_products` WHERE `store_id` = ? AND sku = ? ";
	    	$queryVerify = $this->db->query($sqlVerify,  array( $this->store_id, $row['sku']));
	    	$verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	    	$res[$key]['product_id'] = $verify['id'];
	    	
	       $sqlProductsVar= "SELECT  ml_products_attributes.* FROM ml_products_attributes
            WHERE ml_products_attributes.store_id = {$this->store_id} 
            AND ml_products_attributes.product_id = {$row['id']}
            ORDER BY ml_products_attributes.id";
	       $query = $this->db->query( $sqlProductsVar);
	       $variations =  $query->fetchAll(PDO::FETCH_ASSOC);
	       if(isset($variations[0])){
		       foreach($variations as $j => $variation){
		       
		       	
		           if($variation['name'] != 'picture_ids' AND $variation['name'] != 'sold_quantity'){
		           	
			           	$sqlVerify = "SELECT  quantity as available_qty FROM `available_products` WHERE `store_id` = ? AND sku = ? ";
			           	$queryVerify = $this->db->query($sqlVerify,  array( $this->store_id, $variation['sku']));
			           	$verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
			           	if(isset($verify['available_qty'])){
			           		$res[$key]['variations'][$variation['variation_id']]['Disponivel'] = $verify['available_qty'];
			           	}
		           	
		               if(!isset($res[$key]['variations'][$variation['variation_id']]['sku']) ){
		                   $res[$key]['variations'][$variation['variation_id']]['sku'] = $variation['sku'];
		               }
		               switch($variation['name']){
		               		case "available_quantity": $name = "Qtd."; break;
		                   	case "price": $name = "Preço"; break;
		                   	default : $name = $variation['name'];  break;
		               }
		               $res[$key]['variations'][$variation['variation_id']][$name] = !empty($variation['information']) ? $variation['information'] : $variation['value'] ; 
		               if(!isset($res[$key]['variations'][$variation['variation_id']]['status']) ){
		                   $res[$key]['variations'][$variation['variation_id']]['status'] = $variation['status'];
		               }
		           }
		       }
	       }
	       
	       
	       
	        
	    }
	    return $res;
	}
	
	
	
	public function GetMlProductsFilter()
	{
	    
	    $where_fields = "";
	    $values = array();
	    $class_vars = get_class_vars(get_class($this));
	    foreach($class_vars as $key => $value){
	        if(!empty($this->{$key})){
	            switch($key){
	                case 'store_id': $where_fields .= "ml_products.{$key} = {$this->$key} AND ";break;
	                case 'id': $where_fields .= "ml_products.{$key} = {$this->$key} AND ";break;
	                case 'sku': $where_fields .= "ml_products.{$key} LIKE '{$this->$key}' AND ";break;
	                
	            }
	        }
	        
	    }
	    
	    $where_fields = substr($where_fields, 0,-4);
	    
	    
	    return $where_fields;
	    
	}
	
	public function GetMlProducts(){
	    
	    $where_fields = $this->GetMlProductsFilter();
// 	   $sql = "SELECT ml_products_attributes.product_id, ml_products.* FROM  ml_products_attributes
//                 LEFT JOIN ml_products ON ml_products.id = ml_products_attributes.product_id
//                 WHERE {$where_fields}
//                 GROUP BY ml_products_attributes.product_id
//                 LIMIT {$this->linha_inicial}, " . $this->records.";";
	   
	   $sql = "SELECT ml_products.*  FROM  ml_products WHERE {$where_fields}
	   			LIMIT {$this->linha_inicial}, " . $this->records.";";
	    
	    $query = $this->db->query( $sql);
	    $res = $query->fetchAll(PDO::FETCH_ASSOC);
	    foreach($res as $key => $row){
	    	$sqlVerify = "SELECT  id FROM `available_products` WHERE `store_id` = ? AND sku = ? ";
	    	$queryVerify = $this->db->query($sqlVerify,  array( $this->store_id, $row['sku']));
	    	$verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	    	$res[$key]['product_id'] = $verify['id'];
	        
	        $sqlProductsVar= "SELECT  ml_products_attributes.* FROM ml_products_attributes
            WHERE ml_products_attributes.store_id = {$this->store_id}
            AND ml_products_attributes.product_id = {$row['id']}
            ORDER BY ml_products_attributes.id";
	        $query = $this->db->query( $sqlProductsVar);
	        $variations =  $query->fetchAll(PDO::FETCH_ASSOC);
	        
	        foreach($variations as $j => $variation){
	            // 	           pre($variation);die;
	            if($variation['name'] != 'picture_ids' AND $variation['name'] != 'sold_quantity'){
	            	
	            	$sqlVerify = "SELECT  quantity as available_qty FROM `available_products` WHERE `store_id` = ? AND sku = ? ";
	            	$queryVerify = $this->db->query($sqlVerify,  array( $this->store_id, $variation['sku']));
	            	$verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	            	if(isset($verify['available_qty'])){
	            		$res[$key]['variations'][$variation['variation_id']]['Disponivel'] = $verify['available_qty'];
	            	}
	            	
	            	
	                if(!isset($res[$key]['variations'][$variation['variation_id']]['sku']) ){
	                    $res[$key]['variations'][$variation['variation_id']]['sku'] = $variation['sku'];
	                }
	                switch($variation['name']){
	                    case "available_quantity": $name = "Qtd."; break;
	                    case "price": $name = "Preço"; break;
	                    default : $name = $variation['name'];  break;
	                }
	                $res[$key]['variations'][$variation['variation_id']][$name] = !empty($variation['information']) ? $variation['information'] : $variation['value'] ;
	                if(!isset($res[$key]['variations'][$variation['variation_id']]['status']) ){
	                    $res[$key]['variations'][$variation['variation_id']]['status'] = $variation['status'];
	                }
	            }
	        }
	        
	        
	        
	        
	    }
	    
	    return $res;
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