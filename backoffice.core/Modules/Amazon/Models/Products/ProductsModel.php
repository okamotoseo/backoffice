<?php 

class ProductsModel extends MainModel
{

    public $id;
    
	public $store_id;
	
	public $product_id;
	
	public $ean;
	
	public $sku;
	
	public $parent_id;
	
	public $title;
	 	public $xsdName;
	 	public $choice;
	 	public $set_attribute;
	 	public $published;
	 	public $error;
	 	public $category;
	
	public $category_id;
	
	public $connection;
	
	public $created;
	
	public $updated;
	
	public $stock;
	
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
	    
	    $query = $this->db->query("SELECT id FROM az_products_feed
            WHERE store_id = {$this->store_id} AND product_id = '{$this->product_id}'");
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    if ( ! empty( $res['id'] ) ) {
	        $query = $this->db->update('az_products_feed',
	            array('store_id','id'),
	            array($this->store_id, $res['id']),
	            array('product_id' => $this->product_id,
            		'ean' => $this->ean,
	                'sku' => $this->sku, 
	                'parent_id' => $this->parent_id
	                
	            ));
	        
	        if($query->rowCount()){
	            
	            $this->db->update('az_products_feed',
	                array('store_id','id'),
	                array($this->store_id, $res['id']),
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
	        
	        $query = $this->db->insert('az_products_feed', array(
	            'store_id' => $this->store_id,
	            'product_id' => $this->product_id,
        		'ean' => $this->ean,
	            'sku' => $this->sku,
	            'parent_id' => $this->parent_id,
	            'created' => date("Y-m-d H:i:s"),
	            'updated' => date("Y-m-d H:i:s")
	        ));
	        
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
	    $sql = "SELECT count(*) as total FROM az_products_feed WHERE {$where_fields}";
	    
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	public function ListProducts(){
	   
//         $query = $this->db->query("SELECT available_products.*, az_products_feed.*
//     		FROM az_products_feed
//     		LEFT JOIN available_products ON available_products.id = az_products_feed.product_id
//     		WHERE az_products_feed.store_id= ? AND connection = 'match'
//     		ORDER BY az_products_feed.az_Title DESC
//             LIMIT {$this->linha_inicial}, {$this->records}",
            
// 		$query = $this->db->query("SELECT available_products.*, az_products_feed.*, ml_products.id as ads_id,  
// 			ml_products_attributes.variation_id as ml_variation_id
//             FROM az_products_feed
//             LEFT JOIN available_products ON available_products.id = az_products_feed.product_id
//             LEFT JOIN ml_products ON ml_products.sku = available_products.sku
//             LEFT JOIN ml_products_attributes ON ml_products_attributes.sku =  available_products.sku
//             AND ml_products_attributes.name LIKE 'available_quantity'
//             WHERE az_products_feed.store_id= ? AND connection = 'match' ORDER BY az_products_feed.parent_id DESC
//             LIMIT {$this->linha_inicial}, {$this->records}",
//             array( $this->store_id)
//         );

		$query = $this->db->query("SELECT available_products.*, az_products_feed.*
				FROM az_products_feed
				LEFT JOIN available_products ON available_products.id = az_products_feed.product_id
				WHERE az_products_feed.store_id= ? AND az_products_feed.connection = 'match' ORDER BY az_products_feed.parent_id DESC
				LIMIT {$this->linha_inicial}, {$this->records}",
				array( $this->store_id)
				);


        
        
        if ( ! $query ) {
            return array();
        }
        
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $res;
	    
	}
	public function GetProductsFilter()
	{
	    
	    $where_fields = "";
	    $values = array();
	    $class_vars = get_class_vars(get_class($this));
	    foreach($class_vars as $key => $value){
	        if(!empty($this->{$key})){
	            switch($key){
	                case 'store_id': $where_fields .= "az_products_feed.{$key} = {$this->$key} AND ";break;
	                case 'title': $where_fields .= "az_products_feed.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'product_id': $where_fields .= "az_products_feed.{$key} = {$this->$key} AND ";break;
	                case 'sku': $where_fields .= "az_products_feed.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'ean': $where_fields .= "az_products_feed.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'parent_id': $where_fields .= "az_products_feed.{$key} LIKE '".trim($this->$key)."' AND ";break;
	                case 'connection': $where_fields .= "az_products_feed.{$key} LIKE '".trim($this->$key)."' AND ";break;
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
	    
	    $sql = "SELECT available_products.*, az_products_feed.*
	    FROM az_products_feed
	    LEFT JOIN available_products ON available_products.id = az_products_feed.product_id 
	    WHERE {$where_fields} AND az_Amount IS NOT NULL AND az_Rank IS NULL  ORDER BY az_products_feed.parent_id DESC
	    LIMIT {$this->linha_inicial}, {$this->records}";
	    
    	if(!empty($this->stock)){
    		
    		$where_ap = '';
    		
    		if($this->stock == 'withStock'){
    			$where_ap = " AND available_products.quantity > 0 ";
    		}else{
    			$where_ap = " AND available_products.quantity <= 0 ";
    		}
    		
    		$sql = "SELECT available_products.*, az_products_feed.* FROM az_products_feed
    		RIGHT JOIN available_products ON available_products.id = az_products_feed.product_id {$where_ap}
    		WHERE {$where_fields} AND az_Amount IS NOT NULL AND az_Rank IS NULL ORDER BY az_products_feed.parent_id DESC
    		LIMIT {$this->linha_inicial}, {$this->records}";
    		
    	}
	    $query = $this->db->query($sql);
	    if ( ! $query ) {
	        return array();
	    }
	    
	    
	    $res =  $query->fetchAll(PDO::FETCH_ASSOC);
	    
	 	return $res;
	    
	}
	
	public function Delete()
	{
	    
	    if(empty($this->id)){
	        return array();
	    }
	        
        $query = $this->db->query('DELETE FROM az_products_feed
        WHERE store_id = ? AND `id`= ?', array($this->store_id, $this->id ) );
        
        if ( ! $query ) {
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
            
        }else{
        	
        	$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            
        }
	        
	        

	    
	}
	
}

?>