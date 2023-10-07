<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class ColorsModel extends MainModel
{
    /**
     * @var int
     */
	public $id;
	
    public $store_id;
    
    public $color;
    
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
	                if(property_exists($this,$property)){
	                	switch($property){
	                		case 'color': $value = trim($value); break;
	                	}
	                    
	                    $this->{$property} = $value;
	                    
	                }
            	}else{
            		$arr = array('color');
            		
            		if( in_array($property, $arr) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
            		}
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
        	
        	if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
        		$this->Load();
        	
        	}
        	 
        	if ( chk_array( $this->parametros, 2 ) == 'del' ) {
        	
        		$this->Delete();
        	
        	}
        	
            return;
            
        }
        
    }
    
    public function Save(){
    	 
    	
    	if ( ! empty( $this->id ) ) {
    		$query = $this->db->update('colors', 'id', $this->id, array(
    				'color' => friendlyText($this->color),
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
    		
    		$query = $this->db->query('SELECT * FROM `colors`  WHERE `store_id` = ? 
    				AND color LIKE ? ORDER BY id DESC',
    				array($this->store_id, friendlyText($this->color))
    		);
    		
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		if(!isset($res['color'])){
    			
	    		$query = $this->db->insert('colors', array(
	    				'store_id' => $this->store_id,
	    				'color' => friendlyText($this->color),
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
	    			 
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Cor já cadastrada</div>';
    			return;
    		}
    		
    	}
    	 
    }
    
    public function ListColors()
    {
        $query = $this->db->query('SELECT * FROM `colors`  WHERE`store_id` = ? ORDER BY id DESC',
            	array( $this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
    	if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
    
    		$id = chk_array( $this->parametros, 3 );
    
    		$query = $this->db->query('SELECT * FROM colors WHERE `id`= ?', array( $id ) );
    
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
    	if ( chk_array( $this->parametros, 2 ) == 'del' ) {
    
    		$id = chk_array( $this->parametros, 3 );
    
    		$query = $this->db->query('DELETE FROM colors WHERE `id`= ?', array( $id ) );
    		
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
    			return;
    		}
    
    
    	} else {
    
    		return;
    
    	}
    
    }
    
} 