<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class TranslatesModel extends MainModel
{
    /**
     * @var int
     */
	public $id;
	
	/**
	 * 0 representa todas as lojas
	 * @var integer
	 */
	public $store_id = 0; 
	
    public $word;
    
    public $attribute_group = 'Default';
    
    public $alias;
    
    public $required = 'Opicional';
    
    public $translate;
    
	public $description;
	
	public $exemple;
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
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
            		$arr = array('word', 'translate', 'description');
            		
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
    		$query = $this->db->update('translates', 'id', $this->id, array(
    				'store_id' => $this->store_id,
    				'word' => $this->word,
    				'attribute_group' => $this->attribute_group,
	    			'translate' => $this->translate,
    				'description' => $this->description,
    				'exemple' => $this->exemple,
    				'alias' => titleFriendly($this->word),
    				'required' => $this->required,
    				'updated' => date("Y-m-d H:i:s")
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
    		
    		$query = $this->db->query('SELECT * FROM `translates`  WHERE `store_id` = ? 
    				AND word LIKE ? AND attribute_group LIKE ?',
    				array($this->store_id, $this->word, $this->attribute_group)
    		);
    		
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		if(!isset($res['word'])){
    			
	    		$query = $this->db->insert('translates', array(
	    				'store_id' => $this->store_id,
	    				'word' => $this->word,
	    				'attribute_group' => $this->attribute_group,
		    			'translate' => $this->translate,
	    				'description' => $this->description,
	    				'exemple' => $this->exemple,
	    				'alias' => titleFriendly($this->word),
	    				'required' => $this->required,
	    				'created' => date("Y-m-d H:i:s"),
	    				'updated' => date("Y-m-d H:i:s")
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
	    			 
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Registro já cadastrada</div>';
    			return;
    		}
    		
    	}
    	 
    }
    
    public function ListTranslates()
    {
        $query = $this->db->query('SELECT * FROM `translates`  WHERE`store_id` = ? ORDER BY id DESC', array($this->store_id));
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
    	if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
    
    		$id = chk_array( $this->parametros, 3 );
    
    		$query = $this->db->query('SELECT * FROM translates WHERE `id`= ?', array( $id ) );
    
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
    
    		$query = $this->db->query('DELETE FROM translates WHERE `id`= ?', array( $id ) );
    		
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel excluir o registro.</div>';
    			return;
    		}
    
    
    	} else {
    
    		return;
    
    	}
    
    }
    
} 