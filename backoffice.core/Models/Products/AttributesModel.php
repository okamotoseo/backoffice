<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class AttributesModel extends MainModel
{
    /**
     * @var int
     */
	public $id;
	
    public $store_id;
    
    public $attribute;
    
    public $description;
    
    public $alias;
    
    public $category;
    
    public $marketplace = 'Sysplace';
    

    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->parametros = $this->controller->parametros;
        
        $this->userdata = $this->controller->userdata;
        
        $this->store_id = $this->controller->userdata['store_id'];
        
        
    }
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
            	if(!empty($value)){
	                if(property_exists($this,$property)){
	                    
	                    $this->{$property} = $value;
	                    
	                }
            	}else{
            		$required = array('attribute', 'description');
            		
            		if( in_array($property, $required) ){
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
    		$query = $this->db->update('attributes', 'id', $this->id, array(
    				'attribute' => friendlyText($this->attribute),
    				'description' => $this->description,
    				'alias' => titleFriendly($this->attribute)
    		));
    		 
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    		} else {
    			
    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
    		}
    	} else {
    		
    		$query = $this->db->query('SELECT * FROM `attributes`  WHERE `store_id` = ?
    				AND attribute LIKE ? ORDER BY id DESC',
    				array($this->store_id, friendlyText($this->attribute))
    		);
    		
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		if(!isset($res['attribute'])){
	    		$query = $this->db->insert('attributes', array(
	    				'store_id' => $this->store_id,
	    				'attribute' => friendlyText($this->attribute),
	    				'description' => $this->description,
	    				'alias' => titleFriendly($this->attribute),
	    				'marketplace' => $this->marketplace
	    			));
	    		 
	    		if ( ! $query ) {
	    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	    		} else {
	    			 
	    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
	    		}
	    		
	    	}else {
	    			 
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Atributo já cadastrado</div>';
    		}
    		
    	}
    }
    
    public function ListAttributes()
    {
        $query = $this->db->query('SELECT * FROM `attributes`  WHERE `store_id` = ? ORDER BY attribute ASC',
            	array( $this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function GetAttributes($mlCategoryId)
    {
        
            
            
        $query = $this->db->query('SELECT category FROM ml_category_relationship WHERE store_id = ? AND category_id LIKE ?',
            array( $this->store_id, $mlCategoryId)
            );
        $res = $query->fetch(PDO::FETCH_ASSOC);
        $categories = explode(">", $res['category']);
        $mlCategory = trim($categories[0]);
       $sql = "SELECT * FROM `attributes`  WHERE `store_id` = {$this->store_id} AND id IN (
            SELECT set_attributes_relationship.attribute_id FROM set_attributes LEFT JOIN  set_attributes_relationship 
            ON set_attributes.id = set_attributes_relationship.set_attribute_id
            WHERE set_attributes.store_id = {$this->store_id} AND set_attributes.root_category LIKE '{$mlCategory}'
        ) ";
        $queryAttr = $this->db->query($sql);
    
        if ( ! $queryAttr ) {
            return array();
        }
        
        return $queryAttr->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    public function GetAttributesFromRootCategory()
    {
    
    	if(!isset($this->category) || empty($this->category)){
    		return array();
    	}
    	
    	
    	$sql = "SELECT set_attributes.id, set_attributes.set_attribute, attributes.*
    	FROM set_attributes
    	LEFT JOIN set_attributes_relationship ON set_attributes_relationship.set_attribute_id = set_attributes.id
    	LEFT JOIN attributes ON attributes.id = set_attributes_relationship.attribute_id
    	WHERE set_attributes.store_id = {$this->store_id} AND set_attributes.category LIKE '{$this->category}' ";
    	$queryAttr = $this->db->query($sql);
    	 
    	if ( ! $queryAttr ) {
    		return array();
    	}
    	
    	if($queryAttr->rowCount() < 1 ){
    		
    		$parts = explode('>', $this->category);
    		 
    		$rootCategory = trim($parts[0]);
    	
    		$sql = "SELECT set_attributes.id, set_attributes.set_attribute, attributes.*
    		FROM set_attributes
    		LEFT JOIN set_attributes_relationship ON set_attributes_relationship.set_attribute_id = set_attributes.id
    		LEFT JOIN attributes ON attributes.id = set_attributes_relationship.attribute_id
    		WHERE set_attributes.store_id = {$this->store_id} AND set_attributes.root_category LIKE '{$rootCategory}'";
    		$queryAttr = $this->db->query($sql);
    	
    	}
    	
    	return $queryAttr->fetchAll(PDO::FETCH_ASSOC);
    
    }
    
    public function Load()
    {
    	if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
    
    		$id = chk_array( $this->parametros, 3 );
    
    		$query = $this->db->query('SELECT * FROM attributes WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
    
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
    
    		$query = $this->db->query('DELETE FROM attributes WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
    		
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
    			return;
    		}
    
    
    	} else {
    
    		return;
    
    	}
    
    }
    
} 