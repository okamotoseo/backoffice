<?php 

class ProductsModel extends MainModel
{

    public $id;
    
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
	
	public $seller_price;
	
	public $seller_color;
	
	public $seller_reference;
	
	public $seller_weight;
	
	public $seller_height;
	
	public $seller_length;
	
	public $seller_width;
	
	public $seller_description;
	
	public $created;
	
	public $updated;
	
	public $records = '50';


	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    if(isset($this->controller)){
	        
	        $this->parametros = $this->controller->parametros;
	        
	        $this->userdata = $this->controller->userdata;
	        
	        $this->seller_store_id = $this->controller->userdata['store_id'];
	        
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
	    
	    $query = $this->db->query("SELECT id FROM module_marketplace_products
            WHERE seller_store_id = {$this->seller_store_id} AND product_id = '{$this->product_id}' AND seller_store_id = {$this->seller_store_id}");
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    if ( ! empty( $res['id'] ) ) {
	    	
	    	$data = array(
	    			'seller_product_id' => $this->seller_product_id,
	    			'seller_sku' => $this->seller_sku,
	    			'seller_parent_id' => $this->seller_parent_id,
	    			'seller_title' => $this->seller_title,
	    			'seller_color' => $this->seller_color,
	    			'seller_brand' => $this->seller_brand,
	    			'seller_variation_type' => $this->seller_variation_type,
	    			'seller_variation' => $this->seller_variation,
	    			'seller_brand' => $this->seller_brand,
	    			'seller_reference' => $this->seller_reference,
	    			'seller_category' => $this->seller_category,
	    			'seller_quantity' => $this->seller_quantity,
	    			'seller_sale_price' => $this->seller_sale_price,
	    			'seller_weight' => $this->seller_weight,
	    			'seller_height' => $this->seller_height,
	    			'seller_width' => $this->seller_width,
	    			'seller_length' => $this->seller_length,
	    			'seller_ean' => $this->seller_ean,
	    			'seller_description' => $this->seller_description,
	    			'updated' => date('Y-m-d H:i:s')
	    	);
	    	
	        $query = $this->db->update('module_marketplace_products',
	            array('seller_store_id','id'),
	            array($this->seller_store_id, $res['id']),
	        		$data);
	        
	        if($query->rowCount()){
	            
	            $this->db->update('module_marketplace_products',
	                array('seller_store_id','id'),
	                array($this->seller_store_id, $res['id']),
	                array('updated' => date("Y-m-d H:i:s"))
	                );
	        }
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Pedido atualizado com sucesso.</div>';
	            $this->id = null;
	            return  $res['id'];
	            
	        }
	        
	    } else {
	    	
	    	$data = array(
    			'seller_product_id' => $this->seller_product_id,
    			'seller_sku' => $this->seller_sku,
    			'seller_parent_id' => $this->seller_parent_id,
    			'seller_title' => $this->seller_title,
    			'seller_color' => $this->seller_color,
    			'seller_brand' => $this->seller_brand,
    			'seller_variation_type' => $this->seller_variation_type,
    			'seller_variation' => $this->seller_variation,
    			'seller_brand' => $this->seller_brand,
    			'seller_reference' => $this->seller_reference,
    			'seller_category' => $this->seller_category,
    			'seller_quantity' => $this->seller_quantity,
    			'seller_sale_price' => $this->seller_sale_price,
    			'seller_weight' => $this->seller_weight,
    			'seller_height' => $this->seller_height,
    			'seller_width' => $this->seller_width,
    			'seller_length' => $this->seller_length,
    			'seller_ean' => $this->seller_ean,
    			'seller_description' => $this->seller_description,
	    		'created' => date('Y-m-d H:i:s'),
	    		'updated' => date('Y-m-d H:i:s')
	    	);
	        $query = $this->db->insert('module_marketplace_products', $data);
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            return;
	        } else {
	            
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Pedido cadastrado com sucesso.</div>';
	            return $this->db->last_id;
	        }
	        
	    }
	    
	    
	}
	
	public function TotalProducts(){
	    
	    
	    //         $sql = "SELECT count(*) as total FROM available_products WHERE available_products.variation != '' AND {$where_fields}";
	    
		$where_fields = $this->GetProductsFilter();
	    $sql = "SELECT count(*) as total FROM module_marketplace_products WHERE {$where_fields}";
	    
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	public function ListProducts(){
	    
        $query = $this->db->query("SELECT available_products.*, module_marketplace_products.*
    		FROM module_marketplace_products
    		LEFT JOIN available_products ON available_products.id = module_marketplace_products.seller_product_id
    		WHERE module_marketplace_products.seller_store_id= ?
    		ORDER BY module_marketplace_products.seller_title DESC
            LIMIT {$this->linha_inicial}, {$this->records}",
            array( $this->seller_store_id)
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
	        if(!empty($this->{$key})){
	            switch($key){
	                case 'seller_store_id': $where_fields .= "module_marketplace_products.{$key} = {$this->$key} AND ";break;
	                case 'seller_title': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'seller_product_id': $where_fields .= "module_marketplace_products.{$key} = {$this->$key} AND ";break;
	                case 'seller_sku': $where_fields .= "module_marketplace_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
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
	    
	    
	  	$sql = "SELECT available_products.*, module_marketplace_products.*
    		FROM module_marketplace_products
    		LEFT JOIN available_products ON available_products.id = module_marketplace_products.seller_product_id
    		WHERE {$where_fields} ORDER BY module_marketplace_products.seller_parent_id DESC
            LIMIT {$this->linha_inicial}, {$this->records}";
	    
	    //         pre($sql);die;
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
	        
        $query = $this->db->query('DELETE FROM module_marketplace_products
        WHERE seller_store_id = ? AND `id`= ?', array($this->seller_store_id, $this->id ) );
        if ( ! $query ) {
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
        }else{
        	$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
        }
	    
	}
	
}

?>