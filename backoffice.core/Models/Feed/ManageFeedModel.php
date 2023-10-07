<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class ManageFeedModel extends MainModel
{

    
	public $id;
	
    public $store_id;
    
    public $name;
    
    public $layout;
    
    public $description;
    
    public $url_store;
    
    public $parameters;
    
    public $created;
    
    public $updated;
    
    public $status;
    
    

    
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
	                if(property_exists($this,$property)){
	                    
	                    $this->{$property} = $value;
	                    
	                }
            	}else{
            		$arr = array('name', 'layout');
            		
            		if( in_array($property, $arr) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
            		}
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
        	
            
            if ( in_array('edit', $this->parametros )) {
                
                $key = array_search('edit', $this->parametros);
                
                $feedId = get_next($this->parametros, $key);
                $this->id  = is_numeric($feedId) ? $feedId :  '';
                if(!empty($this->id)){
                    
                    $this->Load();
                    
                }
                
            }
       
            
            if ( in_array('del', $this->parametros )) {
                
                $key = array_search('del', $this->parametros);
                
                $feedId = get_next($this->parametros, $key);
                $this->id  = is_numeric($feedId) ? $feedId :  '';
                if(!empty($this->id)){
                    
                    $this->Delete();
                    
                }
                
            }
        	
            return;
            
        }
        
    }
    
    public function Save(){
    	 
    	 
    	if ( ! empty( $this->id ) ) {
    		
    		$query = $this->db->update('feed', 'id', $this->id, array(
    		    'name' => $this->name,
    		    'layout' => $this->layout,
    		    'description' => $this->description,
    		    'url_store' => $this->url_store,
    		    'parameters' => $this->parameters,
    		    'updated'  => date("Y-d-m H:i:s"),
    		    'status' => $this->status
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
    			
    		$query = $this->db->insert('feed', array(
				'store_id' => $this->store_id,
				'name' => $this->name,
    		    'layout' => $this->layout,
				'description' => $this->description,
    		    'url_store' => $this->url_store,
    		    'parameters' => $this->parameters,
    		    'created'  => date("Y-d-m H:i:s"),
    		    'updated'  => date("Y-d-m H:i:s"),
    		    'status' => $this->status
    		));
    		 
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			return;
    		} else {
    			 
    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
    			return;
    		}
    		
    		
    	}
    		
    	 
    }
    
    public function ListFeed()
    {
        $query = $this->db->query('SELECT * FROM `feed`  WHERE `store_id` = ? ORDER BY id DESC',
            	array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    
    public function Load()
    {
        
        if(!empty($this->id) ){
            
            $query = $this->db->query('SELECT * FROM feed WHERE `id`= ? AND store_id = ?',
                array( $this->id, $this->store_id ) );
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($fetch)){
                foreach($fetch as $key => $value)
                {
                    $column_name = str_replace('-','_',$key);
                    $this->{$column_name} = $value;
                }
            }else{
                return;
            }
            
        }else{
            
            return;
            
        }
        
    }
    
    public function Delete()
    {
        if(!empty($this->id) ){
    
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