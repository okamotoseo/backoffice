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

    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
        }
    }
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['product-description'] ) ) {
            
            foreach ( $_POST['product_descriptions'] as $marketplace => $value ) {
                
                $this->productDescriptions[$marketplace] = array(
                    "title" => $value['title'],
                    "description" => $value['description'],
                    "marketplace" => $marketplace
                );
                
            }
            
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
                
                if(!empty($this->product_id)){
                    
                    $this->Load();
                    
                }
                
            }
            
            return;
            
        }
        
    }
    
    public function Generate(){

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
    			$query = $this->db->update('product_descriptions', 'id', $row['id'], array(
    					'sku' => $this->sku,
    			        'parent_id' => $this->parent_id,
    					'set_attribute_id' => $this->set_attribute_id,
        			    'title' => $attr['title'],
        			    'description' => $attr['description']
    			));
    			
    			if ( ! $query ) {
    			    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			}
    			
    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Descrição atualizada com sucesso.</div>';

    			
    		}
    		
    		$this->Load();
    		
    		
		
        }
    	
    }
    
    public function GetProductInfo(){
    	 
    	 
    	$query = $this->db->query('SELECT title, description FROM available_products WHERE `id`= ? AND store_id = ?',
    			array( $this->product_id, $this->store_id) );
    	$resAP = $query->fetch(PDO::FETCH_OBJ);
    	
    	$sqlAttributes = "SELECT attribute_id, attribute, value  FROM `attributes_values` WHERE store_id = ? AND product_id = ?";
    	$queryAV = $this->db->query($sqlAttributes, array($this->store_id, $this->product_id));
    	$resAV = $queryAV->fetchAll(PDO::FETCH_OBJ);
    	 
    	$resAP->attributes = $resAV;
    	
    	return $resAP;
    	 
    	 
    }
    

    
    public function ListDescription()
    {
    	$query = $this->db->query('SELECT * FROM `product_descriptions`  
    			WHERE `store_id` = ? ORDER BY id DESC',
    			array( $this->store_id)
    	);
    
    	if ( ! $query ) {
    		return array();
    	}
    	return $query->fetchAll(PDO::FETCH_ASSOC);
    
    }
    
    public function GetParentProductDescription()
    {
        if(!isset($this->parent_id)){
            
            return array();
            
        }
        $query = $this->db->query('SELECT * FROM `product_descriptions`
    			WHERE `store_id` = ? AND parent_id = ? AND marketplace = ? LIMIT 1',
            array( $this->store_id, $this->parent_id, $this->marketplace)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetch(PDO::FETCH_ASSOC);
        
    }
    
    
    
    
    public function Load()
    {
        
        if(!empty($this->product_id)){
            
        	$query = $this->db->query('SELECT * FROM `product_descriptions`
        			WHERE `store_id`= ? AND `product_id` = ?  ORDER BY id DESC',
        			array($this->store_id, $this->product_id)
        	);
        
        	if ( ! $query ) {
        		return array();
        	}
        	$description = $query->fetchAll(PDO::FETCH_ASSOC);
        	if(isset($description[0])){
            	$this->id = $description[0]['id'];
            	$this->store_id = $description[0]['store_id'];
            	$this->parent_id = $description[0]['parent_id'];
            	$this->product_id = $description[0]['product_id'];
            	$this->id = $description[0]['set_attribute_id'];
            	$this->sku = $description[0]['sku'];
            	
            	foreach ($description as $key => $value)
            	{
            	    $this->productDescriptions[$value['marketplace']]['title'] = $value['title'];
            	    $this->productDescriptions[$value['marketplace']]['description'] = $value['description'];
            	    $this->productDescriptions[$value['marketplace']]['marketplace'] = $value['marketplace'];
            	    
            	    
            	}
        	}
//         	pre($this);die;
    	
        } else {
            
            return;
            
        }
    
    }
    
    
    
} 