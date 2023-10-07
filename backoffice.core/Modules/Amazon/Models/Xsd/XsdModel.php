<?php

class XsdModel extends MainModel
{
    
    public $id;
    
    public $name;
    
    public $label;
    
    public $alias;
    
    public $set_attribute;
    
    public $type;
    
    public $xsd;
    
    public $file;
    
    public $created;
    
    
    
    
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
        	 
        	$this->parametros = $this->controller->parametros;
        		
        	$this->userdata = $this->controller->userdata;
        		
        	$this->store_id = $this->controller->userdata['store_id'];
        		
        }
        
        
    }
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        if($property == 'label' ){
                        	$this->alias = titleFriendly($value);
                        }
                        if($property == 'xsd' ){
                        	$file = explode("/",$value);
                        	$this->file = end($file);
                        }
                        
                        
                    }
                }else{
                    $arr = array('type', 'name', 'label', 'set_attribute', 'xsd');
                    
                    if( in_array($property, $arr) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
            
            if ( chk_array( $this->parametros, 4 ) == 'edit' ) {
                $this->Load();
                
            }
            
            if ( chk_array( $this->parametros, 4 ) == 'del' ) {
                
                $this->Delete();
                
            }
            
            return;
            
        }
        
    }
    
    public function Save(){
        
        
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('az_category_xsd', 'id', $this->id, array(
            		'label' => $this->label,
            		'set_attribute' => $this->set_attribute,
            		'type' => $this->type,
            		'alias' => $this->alias,
            		'xsd' => $this->xsd,
            		'file' => $this->file
                
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
	            $query = $this->db->insert('az_category_xsd', array(
	                'name' => $this->name,
	                'label' => $this->label,
	                'alias' => $this->alias,
	            	'set_attribute' => $this->set_attribute,
	            	'type' => $this->type,
	            	'xsd' => $this->xsd,
	            	'file' => $this->file,
	            	'created' => $this->created
	            		
	            ));
	            
	            if ( ! $query ) {
	            	$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            	return;
	            } else {
	            
	            	$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
	            	$this->id = $this->db->last_id;
	            	return;
	            }
	            
            
        	}else {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">XSD já cadastrado</div>';
    			return;
    		}
            
            
            
            
        }
        
        
    }
    
    public function ListXsd()
    {
    	
    	$sql = 'SELECT * FROM `az_category_xsd` ORDER BY id DESC';
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
        if ( chk_array( $this->parametros, 4 ) == 'edit' ) {
            
            $id = chk_array( $this->parametros, 5 );
            
            $query = $this->db->query('SELECT * FROM az_category_xsd WHERE `id`= ?', array($id ) );
            
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
        if ( chk_array( $this->parametros, 4 ) == 'del' ) {
            
            $id = chk_array( $this->parametros, 5 );
            
            $query = $this->db->query('DELETE FROM az_category_xsd WHERE `id`= ?', array( $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
    
    
}

?>