<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class BrandsModel extends MainModel
{

    
	public $id;
	
    public $store_id;
    
    public $brand;
    
    public $description;
    

    
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
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
            	if(!empty($value)){
	                if(property_exists($this, $property)){
	                	
	                    switch($property){
	                    	case 'brand': $value = trim($value); break;
	                    }
	                    
	                    $this->{$property} = $value;
	                    
	                }
            	}else{
            		$arr = array('brand');
            		
            		if( in_array($property, $arr) ){
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
    
    public function Save(){
    	 
    	 
    	if ( ! empty( $this->id ) ) {
    		
    		$query = $this->db->update('brands', 'id', $this->id, array(
    				'brand' => friendlyText($this->brand),
    				'description' => $this->description
    		));
    		 
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			 
    			return;
    		} else {
    			
    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
    			$this->id = null;
    			return;
    		}
    	} else {
    		$query = $this->db->query('SELECT * FROM `brands`  WHERE `store_id` = ?
    				AND brand LIKE ? ORDER BY id DESC',
    				array($this->store_id, friendlyText($this->brand))
    		);
    		
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		
    		if(!isset($res['brand'])){
    			
	    		$query = $this->db->insert('brands', array(
	    				'store_id' => $this->store_id,
	    				'brand' => friendlyText($this->brand),
	    				'description' => $this->description,
	    				)
	    			);
	    		 
	    		if ( ! $query ) {
	    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	    			return;
	    		} else {
	    			 
	    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
	    			return;
	    		}
	    		
    		}else {
    			 
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Marca já cadastrada</div>';
    			return;
    		}
    		
    	}
    		
    	 
    }
    
    public function ListBrands()
    {
        $query = $this->db->query('SELECT * FROM `brands`  WHERE `store_id` = ? ORDER BY brand ASC',
            	array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListProductsBrands()
    {
    	$query = $this->db->query('SELECT * FROM `brands`  WHERE `store_id` = ? ORDER BY brand ASC',
    			array($this->store_id)
    			);
    
    	if ( ! $query ) {
    		return array();
    	}
    	$res = $query->fetchAll(PDO::FETCH_ASSOC);
    	

    	foreach($res as $key => $brand){
    	
    	
    		$sql = "SELECT count(id) as qtd, 
    				sum(sale_price) as total, 
    				sum(quantity) as stock,
    				 COUNT(IF(quantity > 0, 1, NULL)) 'variations'
    				FROM available_products WHERE store_id = ? AND brand = ? AND id NOT IN(
    					SELECT product_id as id FROM product_relational WHERE store_id = {$this->store_id}
    				)";
    		$queryAP = $this->db->query($sql, array($this->store_id, $brand['brand']));
    		
    		$row = $queryAP->fetch(PDO::FETCH_ASSOC);
    		
    		$res[$key]['stock'] = !empty($row['stock']) ? $row['stock'] : 0 ;
    		$res[$key]['variations'] = !empty($row['variations']) ? $row['variations'] : 0 ;
    		$ticket = 0.00;
    		
    		$res[$key]['items'] = !empty($row['qtd']) ? $row['qtd'] : 0 ;
    		
    		if( $row['total'] > 0 && $row['qtd'] > 0 ){
    			
    			$ticket = $row['total'] / $row['qtd'] ;
    		}
    		
    		$res[$key]['ticket'] = number_format($ticket, 2);
    	
    	}
    	
    	return $res;
    
    }
    
    
    
    public function Load()
    {
        if ( in_array('edit', $this->parametros )) {
            
            $key = array_search('edit', $this->parametros);
            
            $id = get_next($this->parametros, $key);
    
    		$query = $this->db->query('SELECT * FROM brands WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
    
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
    
            $query = $this->db->query('DELETE FROM brands WHERE store_id = ? AND  `id`= ?', array($this->store_id, $id ) );
    		
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
    			return;
    		}
    
    
    	} else {
    
    		return;
    
    	}
    
    }
    
} 