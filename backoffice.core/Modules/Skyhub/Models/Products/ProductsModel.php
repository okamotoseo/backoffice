<?php 

class ProductsModel extends MainModel
{

    public $id;
    
	public $store_id;
	
	public $product_id;
	
	public $sku;
	
	public $parent_id;
	
	public $created;
	
	public $updated;
	
	public $price;
	
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
	    
	    $query = $this->db->query("SELECT id FROM module_skyhub_products
            WHERE store_id = {$this->store_id} AND product_id = '{$this->product_id}'");
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    if ( ! empty( $res['id'] ) ) {
	        $query = $this->db->update('module_skyhub_products',
	            array('store_id','id'),
	            array($this->store_id, $res['id']),
	            array('product_id' => $this->product_id, 
	                'sku' => $this->sku, 
	            	'price' => $this->price,
	                'parent_id' => $this->parent_id
	                
	            ));
	        
	        if($query->rowCount()){
	            
	            $this->db->update('module_skyhub_products',
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
	        
	        $query = $this->db->insert('module_skyhub_products', array(
	            'store_id' => $this->store_id,
	            'product_id' => $this->product_id,
	            'sku' => $this->sku,
	            'parent_id' => $this->parent_id,
	            'created' => date("Y-m-d H:i:s"),
	            'updated' => date("Y-m-d H:i:s")
	        )
	            );
	        
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
	    $sql = "SELECT count(*) as total FROM module_skyhub_products WHERE store_id = {$this->store_id}";
	    
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	public function ListProducts(){
	    
        $query = $this->db->query("
    		SELECT available_products.*, module_skyhub_products.id,module_skyhub_products.sku, 
            module_skyhub_products.parent_id, module_skyhub_products.product_id,  module_skyhub_products.price as b2wprice
    		FROM module_skyhub_products
    		LEFT JOIN available_products ON available_products.id = module_skyhub_products.product_id
    		WHERE module_skyhub_products.store_id= ?
    		ORDER BY module_skyhub_products.created DESC
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
	        if(!empty($this->{$key})){
	            switch($key){
	                case 'store_id': $where_fields .= "module_skyhub_products.{$key} = {$this->$key} AND ";break;
	                case 'id': $where_fields .= "module_skyhub_products.{$key} = {$this->$key} AND ";break;
	                case 'product_id': $where_fields .= "module_skyhub_products.{$key} = {$this->$key} AND ";break;
	                case 'sku': $where_fields .= "module_skyhub_products.{$key} LIKE '{$this->$key}' AND ";break;
	                case 'parent_id': $where_fields .= "module_skyhub_products.{$key} LIKE '{$this->$key}' AND ";break;
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
	    
	    
	  $sql = "SELECT available_products.*, module_skyhub_products.id,module_skyhub_products.sku, 
            module_skyhub_products.parent_id, module_skyhub_products.product_id
    		FROM module_skyhub_products
    		LEFT JOIN available_products ON available_products.id = module_skyhub_products.product_id
    		WHERE {$where_fields} ORDER BY module_skyhub_products.created DESC
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
	        
        $query = $this->db->query('DELETE FROM module_skyhub_products
        WHERE store_id = ? AND `id`= ?', array($this->store_id, $this->id ) );
        
        $sql = "DELETE FROM `publications` WHERE store_id = {$this->store_id} AND publication_code LIKE '{$this->id}'";
        $db->query($sql);
        
        if ( ! $query ) {
        	
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
            
        }else{
            
            $query = $this->db->query('DELETE FROM module_skyhub_products_variations
            WHERE store_id = ? AND  `id_product`= ?', array($this->store_id, $this->id ) );
            
            if ( ! $query ) {
            	
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
        }
	        
	        

	    
	}
	
}

?>