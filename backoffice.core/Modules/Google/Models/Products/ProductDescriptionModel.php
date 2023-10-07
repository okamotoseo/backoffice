<?php
/**
 * Modelo para gerenciar descrições de produtos
 *
 */
class ProductDescriptionModel extends MainModel
{

	public $id;
	
    public $store_id;
    
    public $product_id;
    
    public $parent_id;
    
    public $set_attribute_id;
    
    public $sku;
    
    public $marketplace = 'default';
    
    public $productDescriptions = array();
    
    public $updated;

    
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
        pre($this->productDescriptions);
        foreach ($this->productDescriptions as $key => $attr){
            
            $marketplace = !empty($attr['marketplace']) ? $attr['marketplace'] : 'default' ;
    		$query = $this->db->query("SELECT id FROM `product_descriptions`  WHERE `store_id`= ? 
    				AND `product_id` = ?  AND marketplace = ?",
    		    array($this->store_id, $this->product_id, $marketplace)
    		);
    		$row = $query->fetch(PDO::FETCH_ASSOC);
    		
    		if(!isset($row['id'])){
    			$query = $this->db->insert('product_descriptions', array(
    					'store_id' => $this->store_id,
    					'product_id' => $this->product_id,
    					'parent_id' => $this->parent_id,
    					'set_attribute_id' => $this->set_attribute_id,
    					'sku' => $this->sku,
        			    'marketplace' => $attr['marketplace'],
        			    'title' => $attr['title'],
    					'description' => $attr['description']
    				)
    			);
    			
    			if ( ! $query ) {
    			    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			}
    			
    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Descrição cadastrado com sucesso.</div>';
    			
    			
    			
    		}else{
//     			$query = $this->db->update('product_descriptions', 'id', $row['id'], array(
//     					'sku' => $this->sku,
//     			        'parent_id' => $this->parent_id,
//     					'set_attribute_id' => $this->set_attribute_id,
//         			    'title' => $attr['title'],
//         			    'description' => $attr['description']
//     			));
    			
//     			if ( ! $query ) {
//     			    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
//     			}
    			
//     			$this->form_msg = '<div class="alert alert-success alert-dismissable">Descrição atualizada com sucesso.</div>';

    			
    		}
    		
    		
    		
		
        }
    	
    }
    
    
    
    
} 