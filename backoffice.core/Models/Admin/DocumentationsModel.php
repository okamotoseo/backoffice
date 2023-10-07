<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class DocumentationsModel extends MainModel
{

    
	public $id;
	
    public $module;
    
    public $type;
    
    public $title;
    
    public $url_post;
    
    public $description;
    
    public $status;
    
    public $visibility;
    
    public $created;
    
    public $updated;
    

    
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
            		$arr = array('title');
            		
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
    	 
//     	 pre($this);die;
    	if ( ! empty( $this->id ) ) {
    		
    		$query = $this->db->update('documentations', 'id', $this->id, array(
        		    'module' => $this->module,
        		    'type' => $this->type,
    				'title' => $this->title,
    				'url_post' => $this->url_post,
    				'description' => $this->description,
    		        'status' => $this->status,
    				'visibility' => $this->visibility,
    		        'updated' => date("Y-d-m H:i:s")
    		));
    		 
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			 
    			return;
    		} else {
    			
    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Documento atualizado com sucesso.</div>';
    			$this->id = null;
    			return;
    		}
    	} else {
    		$query = $this->db->query('SELECT * FROM `documentations`  WHERE title LIKE ? ORDER BY id DESC',
    				array(friendlyText($this->title))
    		);
    		
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		
    		if(!isset($res['title'])){
    			
	    		$query = $this->db->insert('documentations', array(
    	    		    'module' => $this->module,
    	    		    'type' => $this->type,
    	    		    'title' => $this->title,
	    				'url_post' => $this->url_post,
    	    		    'description' => $this->description,
    	    		    'status' => $this->status,
	    				'visibility' => $this->visibility,
	    		        'created' => date("Y-d-m H:i:s")
	    				)
	    			);
	    		 
	    		if ( ! $query ) {
	    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	    			return;
	    		} else {
	    			 
	    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Documento cadastrado com sucesso.</div>';
	    			return;
	    		}
	    		
    		}else {
    			 
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Documento já cadastrado</div>';
    			return;
    		}
    		
    	}
    		
    	 
    }
    
    public function ListDocuments()
    {
        $query = $this->db->query('SELECT * FROM `documentations`  ORDER BY id DESC');
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    function listLastDocuments()
    {
        $query = $this->db->query('SELECT * FROM `documentations`  ORDER BY id DESC LIMIT 10');
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    public function Load()
    {
        if ( in_array('edit', $this->parametros )) {
            
            $key = array_search('edit', $this->parametros);
            
            $id = get_next($this->parametros, $key);
    
    		$query = $this->db->query('SELECT * FROM documentations WHERE  `id`= ?', array($id ) );
    
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
    
            $query = $this->db->query('DELETE FROM documentations WHERE  `id`= ?', array($id ) );
    		
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o documento.</div>';
    			return;
    		}
    
    
    	} else {
    
    		return;
    
    	}
    
    }
    
} 