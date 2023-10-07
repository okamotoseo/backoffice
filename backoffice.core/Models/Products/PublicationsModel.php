<?php
class PublicationsModel extends MainModel
{
    
	public $id;
	
	public $sku;
	
	public $product_id;
	
	public $store_id;
	
	public $marketplace;
	
	public $publication_code;
	
	public $url;
	
	public $created;
	
	public $updated;
	
	public $user;
	
	
    
    
    public function __construct( $db = false, $controller = null ) {
        
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
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['publications'] ) ) {
            
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    
                    if(property_exists($this,$property)){
                        $this->{$property} = $value;
                    }
                }else{
                    $required = array();
                    if( in_array($property, $required) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                    }
                }
            }
            
            
            return true;
            
        }else{
            
            if ( in_array('Product', $this->parametros )) {
                
                $key = array_search('Product', $this->parametros);
                
                $productId = get_next($this->parametros, $key);
                $this->product_id  = is_numeric($productId) ? $productId :  '';
                
            }
            
            return;
            
        }
        
    }
    
    
    public function Save(){
    	
    	if(empty($this->publication_code)){
    		return;
    	}
    	
    	$query = $this->db->query('SELECT * FROM `publications`  WHERE `store_id` = ?
    		AND product_id = ? AND publication_code LIKE ?',
    		array($this->store_id, $this->product_id, $this->publication_code)
    	);
    
    	$res = $query->fetch(PDO::FETCH_ASSOC);
    	if(empty($res['id'])){
    			
    		$query = $this->db->insert('publications', array(
    				'store_id' => $this->store_id,
    				'publication_code' => $this->publication_code,
    				'product_id' =>  $this->product_id,
    				'sku' => $this->sku,
    				'marketplace' => $this->marketplace,
    				'url' => $this->url,
    				'created' => date('Y-m-d H:i:s'),
    				'updated' => date('Y-m-d H:i:s'),
    				'user' => $this->user
    				 
    		));
    
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			return;
    			 
    		}
    			
    		$this->form_msg = '<div class="alert alert-success alert-dismissable">Produto relacionado com sucesso.</div>';
    
    
    	}else {
    
    
//     		$query = $this->db->update('publications',
//     				array('store_id','id'),
//     				array($this->store_id, $res['id']),
//     				array('publication_code' => $this->publication_code,
//     					'product_id' =>  $this->product_id,
//     					'sku' => $this->sku,
//     					'marketplace' => $this->marketplace,
//     					'url' => $this->url,
//     					'updated' => date('Y-m-d H:i:s')
    					
//     				));
    
//     		if ( ! $query ) {
//     			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
//     			return;
//     		}
    
//     		$this->form_msg = '<div class="alert alert-success alert-dismissable">Produto atualizado com sucesso.</div>';
    
    	}
    	
//     	pre($this->form_msg);
    
    }
    
    public function getMlAdverts()
    {
        
        if(!isset($this->sku)){
            return array();
        }
        
        $sql = "SELECT  * FROM ml_products WHERE store_id = {$this->store_id} AND sku LIKE {$this->sku}";
        
        $query = $this->db->query($sql, array($this->store_id, $this->sku));
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function getEcAdverts($ecommerce)
    {
        
        if(!isset($this->sku)){
            return array();
        }
        	
        switch($ecommerce){
        	case 'Onbi': $sql = "SELECT  * FROM module_onbi_products_tmp WHERE store_id = {$this->store_id} AND sku LIKE '{$this->sku}'"; break;
        	case 'Magento2': $sql = "SELECT  * FROM mg2_products_tmp WHERE store_id = {$this->store_id} AND sku LIKE '{$this->sku}'"; break;
//         	case 'Tray': $sql = "SELECT  * FROM module_tray_products WHERE store_id = {$this->store_id} AND parent_id LIKE '{$this->sku}'"; break;
//         	case 'Tray': $sql = "SELECT  * FROM module_tray_products WHERE store_id = {$this->store_id} AND sku LIKE '{$this->sku}' OR  store_id = {$this->store_id} AND parent_id LIKE '{$this->sku}' "; break;
        	case 'Tray': $sql = "SELECT  * FROM module_tray_products WHERE store_id = {$this->store_id} AND sku LIKE '{$this->sku}'"; break;
        	default: $sql = "SELECT  * FROM module_onbi_products_tmp WHERE store_id = {$this->store_id} AND sku LIKE '{$this->sku}'"; break;
        }
        
        $query = $this->db->query($sql, array($this->store_id, $this->sku));
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function getSkAdverts()
    {
        
        if(!isset($this->parent_id)){
            return array();
        }
//         $sql = "SELECT  * FROM module_skyhub_products WHERE store_id = {$this->store_id} AND sku LIKE '{$this->sku}'";
        $sql = "SELECT  * FROM module_skyhub_products WHERE store_id = {$this->store_id} AND parent_id LIKE '{$this->parent_id}'";
        
        $query = $this->db->query($sql, array($this->store_id, $this->sku));
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function getAzAdverts()
    {
    
    	if(!isset($this->sku)){
    		return array();
    	}
    
    	$sql = "SELECT  * FROM az_products_feed WHERE store_id = {$this->store_id} AND sku LIKE '{$this->sku}'";
    
    	$query = $this->db->query($sql, array($this->store_id, $this->sku));
    	if ( ! $query ) {
    		return array();
    	}
    	return $query->fetchAll(PDO::FETCH_ASSOC);
    
    }
    

    
} 