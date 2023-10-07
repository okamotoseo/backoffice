<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class ProductsLogModel extends MainModel
{

    
	public $id;
	
    public $store_id;
    
    public $product_id;
    
    public $created;
    
    public $description;
    
    public $user;
    
    public $json_response;
    

    
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
    	 
    	 
    }
    
    
    public function Save(){
    			
    	if(empty($this->product_id)){
    		return ;
    	}
    	$query = $this->db->insert('products_log', array(
    		'store_id' => $this->store_id,
    		'product_id' => $this->product_id,
    		'created' => date('Y-m-d H:i:s'),
    		'description' => $this->description,
    		'json_response' => json_encode($this->json_response),
    		'user' => $this->user
    		
    		)
   		);
    }
    
    public function ListProductsLog()
    {
        $query = $this->db->query('SELECT * FROM `products_log`  WHERE `store_id` = ? ORDER BY created DESC',
            	array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function GetProductsLog()
    {
    	$query = $this->db->query("SELECT * FROM `products_log`  WHERE `store_id` = ? AND product_id = ? ORDER BY created DESC LIMIT 100",
    			array($this->store_id, $this->product_id)
    			);
    
    	if ( ! $query ) {
    		return array();
    	}
    	return $query->fetchAll(PDO::FETCH_ASSOC);
    
    }
    
    
} 