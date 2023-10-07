<?php 

class ProductVariationsModel extends MainModel
{

    public $id;
    
	public $store_id;
	
	public $id_product;
	
	public $product_id;
	
	public $sku;
	
	public $parent_id;
	
	public $ean;
	
	public $color;
	
	public $variation_type;
	
	public $variation;
	
	public $created;
	
	public $updated;
	
	public $records = '20';


	
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
	        

	        
	        if ( in_array('del', $this->parametros )) {
	                
                $key = array_search('del', $this->parametros);
	                
                $this->id = get_next($this->parametros, $key);
                
	            $this->Delete();
	            
	        }
	        
	        return;
	        
	    }
	    
	}
	
	public function Save(){
	    
	    
	    $query = $this->db->query("SELECT id FROM module_skyhub_products_variations
            WHERE store_id = {$this->store_id} AND id_product = '{$this->id_product}'
         AND color LIKE '{$this->color}' AND variation LIKE '{$this->variation}'");
	    $res = $query->fetch(PDO::FETCH_ASSOC);
	    
	    if ( ! empty( $res['id'] ) ) {
	        
	        $query = $this->db->update('module_skyhub_products_variations',
	            array('store_id','id'),
	            array($this->store_id, $res['id']),
	            array('id_product' => $this->id_product,
	                'product_id' => $this->product_id, 
	                'sku' => $this->sku, 
	                'parent_id' => $this->parent_id,
	                'ean' => $this->ean,
	                'color' => $this->color,
	                'variation_type' => $this->variation_type,
	                'variation' => $this->variation
	            ));
	        
	        if($query->rowCount()){
	            
	            $this->db->update('module_skyhub_products_variations',
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
	            return;
	            
	        }
	        
	    } else {
	        
	        $query = $this->db->insert('module_skyhub_products_variations', array(
	            'store_id' => $this->store_id,
	            'id_product' => $this->id_product,
	            'product_id' => $this->product_id,
	            'sku' => $this->sku,
	            'parent_id' => $this->parent_id,
	            'ean' => $this->ean,
	            'color' => $this->color,
	            'variation_type' => $this->variation_type,
	            'variation' => $this->variation,
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
	
	
	
	public function Delete()
	{
	    
	    if(empty($this->id)){
	        return array();
	    }
	        
        $query = $this->db->query('DELETE FROM module_skyhub_products_variations
        WHERE store_id = ? AND `id`= ?', array($this->store_id, $this->id ) );
        
        if ( ! $query ) {
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
            
        }else{
            
//             $query = $this->db->query('DELETE FROM ml_products
//             WHERE store_id = ? AND  `id`= ?', array($this->store_id, $id ) );
            
//             if ( ! $query ) {
//                 $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
//                 return;
//             }
            
        }
	        
	        

	    
	}
	
}

?>