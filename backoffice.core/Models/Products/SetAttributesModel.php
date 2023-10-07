<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class SetAttributesModel extends MainModel
{
    /**
     * @var int
     */
	public $id;
	
    public $store_id;
    
    public $set_attribute;
    
    public $root_category;
    
    public $attribute_id_list = array();
    
    public $ind = array();
    
    public $defaultAttr = array();
    
    public $category;
    

    
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
            		$required = array('set_attribute', 'root_category');
            		
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
    	 

    	if(count($this->attribute_id_list) > 0){
    		if ( ! empty( $this->id ) ) {
//     		    $rootCategory = explode(">", $this->category);
    		    $rootCategory[] = $this->root_category;
    			$query = $this->db->update('set_attributes', 'id', $this->id, array(
    			    'set_attribute' => $this->set_attribute,
    			    'root_category' => trim($rootCategory[0]),
    			    'category' => $this->category
    			));
    			
    			if(isset($this->category) AND !empty($this->category)){
    			    $query = $this->db->update('category', 
    			        array('store_id', 'hierarchy'), 
    			        array($this->store_id, $this->category),
    			        array('set_attribute_id' => $this->id)
    			        );
    			}
    			$this->db->delete( "set_attributes_relationship", "set_attribute_id", $this->id );
    			foreach($this->attribute_id_list as $check){
    				if(!empty($check)){
    					$this->db->insert('set_attributes_relationship', array(
    							'store_id' => $this->store_id,
    							'attribute_id' => $check,
    							'set_attribute_id' => $this->id,
    							'ind' => 0
    							)
    						);
    				}
    			
    			}
    			if ( ! $query ) {
    				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    				return;
    			
    			} else {
    				$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
    				$this->id = null;
    				return;
    			}
    			 
    
    		} else {
//     		    $rootCategory = explode(">", $this->category);
    		    $rootCategory[] = $this->root_category;
	    		$query = $this->db->query("SELECT id FROM set_attributes  WHERE store_id = ? AND set_attribute = ?",
	    		array($this->store_id, $this->set_attribute));
	    		$res = $query->fetch(PDO::FETCH_ASSOC);
	    		if(empty($res['id'])){
	    
	    			$query = $this->db->insert('set_attributes', array(
	    					'store_id' => $this->store_id,
	    					'set_attribute' => $this->set_attribute,
	    			        'root_category' => trim($rootCategory[0]),
	    			        'category' => $this->category
	    						)
	    					);
	    			$this->id = $this->db->last_id;
	    			
	    			if(isset($this->category) AND !empty($this->category)){
	    			    $query = $this->db->update('category',
	    			        array('store_id', 'hierarchy'),
	    			        array($this->store_id, $this->category),
	    			        array('set_attribute_id' => $this->id)
	    			        );
	    			}
	    			
	    			
	    			$this->db->delete( "set_attributes_relationship", "set_attribute_id", $this->id );
	    			foreach($this->attribute_id_list as $check){
	    				if(!empty($check)){
	    					$this->db->insert('set_attributes_relationship', array(
	    							'store_id' => $this->store_id,
	    							'attribute_id' => $check,
	    							'set_attribute_id' => $this->id,
	    							'ind' => 0
	    					));
	    				}
	    			
	    			}
		    			
	    			
	    		}else{
	    			$this->form_msg = "<div class='alert alert-danger alert-dismissable'>Já existe o conjunto de atributos {$this->set_attribute}</div>";
	    			return;
	    		}
	    		
    		}
    		
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			return;
    			 
    		} else {
    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
    			$this->id = null;
    			return;
    		}
    		
    	}else{
    		$this->form_msg = "<div class='alert alert-danger alert-dismissable'>É necessario adicione um ou mais atributos.</div>";
    		return;
    	}
    		
    	 
    }
    
    public function ListSetAttributes()
    {
        $query = $this->db->query('SELECT * FROM `set_attributes`  WHERE  `store_id` = ? ORDER BY set_attribute ASC',
            	array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListSetAttributesHierarchy()
    {
        
        $sql = "SELECT set_attributes.*,  category.hierarchy FROM set_attributes 
        LEFT JOIN category ON category.hierarchy = set_attributes.category 
        WHERE set_attributes.store_id = {$this->store_id} ORDER BY id DESC";
        
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
    	if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
    
    		$id = chk_array( $this->parametros, 3 );
    
    		$query = $this->db->query('SELECT * FROM set_attributes WHERE `id`= ?', array( $id ) );
    
    		foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
    		{
    			$column_name = str_replace('-','_',$key);
    			$this->{$column_name} = $value;
    		}
    		$query = $this->db->query('SELECT attribute_id FROM set_attributes_relationship 
            WHERE  `store_id` = ? AND`set_attribute_id`= ?', 
    		    array($this->store_id, $id ) );
    		$arr = array();
    		while($row = $query->fetch(PDO::FETCH_ASSOC)){
//     			pre($row);die;
    			array_push($arr, $row['attribute_id']);
    		}
    		$this->attribute_id_list =$arr;
    	} else {
    
    		return;
    
    	}
    
    }
    
    public function Delete()
    {
    	if ( chk_array( $this->parametros, 2 ) == 'del' ) {
    
    		$id = chk_array( $this->parametros, 3 );
    
    		$query = $this->db->query('DELETE FROM set_attributes 
            WHERE  `store_id` = ? AND `id`= ?', 
    		     array( $this->store_id, $id ) 
    		);
    		
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
    			return;
    		}else{
    			$this->db->delete( "set_attributes_relationship", "set_attribute_id", $id );
    			
    		}
    
    
    	} else {
    
    		return;
    
    	}
    
    }
    
    public function GetSetAttributes()
    {
    	if(empty($this->id)){
    		return array();	
    	}
    		 
    	$sqlAttributes = "SELECT
    		set_attributes_relationship.set_attribute_id,
    		attributes.attribute,
    		attributes.alias as attribute_id,
    		attributes.description,
    		set_attributes.set_attribute
    		FROM set_attributes_relationship
    		JOIN attributes ON set_attributes_relationship.attribute_id = attributes.id
    		LEFT JOIN set_attributes ON set_attributes.id = set_attributes_relationship.set_attribute_id
    		WHERE set_attributes_relationship.set_attribute_id = {$this->id}
    		AND set_attributes_relationship.store_id = {$this->store_id}
    		ORDER BY set_attributes_relationship.ind ASC";
    	$query = $this->db->query($sqlAttributes);
    
    	if ( ! $query ) {
    		return array();
    	}
    	return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function GetSetAttributesLikeAttribute()
    {
    	
    	if(empty($this->id)){
    		return array();
    	}
    	 
    	$sqlAttributes = "SELECT 
    	attributes.id,
    	attributes.store_id,
    	attributes.attribute,
    	attributes.description,
    	attributes.alias,
    	set_attributes_relationship.set_attribute_id
    	FROM set_attributes_relationship
    	JOIN attributes ON set_attributes_relationship.attribute_id = attributes.id
    	LEFT JOIN set_attributes ON set_attributes.id = set_attributes_relationship.set_attribute_id
    	WHERE set_attributes_relationship.set_attribute_id = {$this->id}
    	AND set_attributes_relationship.store_id = {$this->store_id}";
    	$query = $this->db->query($sqlAttributes);
    
    	if ( ! $query ) {
    		return array();
    	}
    	return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function ListInputsAttr()
    {
    	
    	if(!empty($this->id)){
    		
    	
	    	$sqlAttributes = "SELECT 
            set_attributes_relationship.set_attribute_id,
            attributes.attribute,
	    	attributes.alias as attribute_id,
	    	attributes.description,
            set_attributes.set_attribute
	    	FROM set_attributes_relationship
	    	JOIN attributes ON set_attributes_relationship.attribute_id = attributes.id
            LEFT JOIN set_attributes ON set_attributes.id = set_attributes_relationship.set_attribute_id
	    	WHERE set_attributes_relationship.set_attribute_id = {$this->id}
            AND set_attributes_relationship.store_id = {$this->store_id}
	    	ORDER BY set_attributes_relationship.ind ASC";
	    	$query = $this->db->query($sqlAttributes);
	    	
	    	if ( ! $query ) {
	    		return array();
	    	}
	    	
	    	return $query->fetchAll(PDO::FETCH_ASSOC);
    	
    	} else {
    	
    		return;
    	
    	}
    	
    }
    
    public function ListInputsMarketplace()
    {
            
            $sqlAttributes = "SELECT ml_attributes_required.category_id, 
            ml_attributes_required.attribute_id, 
            ml_attributes_required.name, 
            ml_attributes_required.tag, 
            ml_attributes_required.value_id, 
            ml_attributes_required.value, 
            ml_attributes_required.value_type, 
            ml_attributes_required.required, 
            ml_attributes_relationship.attribute as alias 
            FROM ml_attributes_required
            LEFT JOIN ml_attributes_relationship ON ml_attributes_relationship.ml_attribute_id =  ml_attributes_required.attribute_id
            AND  ml_attributes_relationship.ml_category_id = ml_attributes_required.category_id
	    	WHERE ml_attributes_required.category_id LIKE ?
            AND ml_attributes_required.store_id = ? 
            GROUP BY ml_attributes_required.attribute_id
	    	ORDER BY ml_attributes_required.attribute_id ASC";
            $query = $this->db->query($sqlAttributes, array($this->category, $this->store_id));
            
            if ( ! $query ) {
                return array();
            }
            
            $attributes = $query->fetchAll(PDO::FETCH_ASSOC);
//             pre($attributes);die;
            $attributesRes = array();
            
            foreach ($attributes as $key => $value) {
                if($value['attribute_id'] != 'BRAND'){
                    $tags = json_decode($value['tag']);
                    
//                     if(!isset($tags->variation_attribute) AND !isset($tags->allow_variations)){
                        if( !isset($tags->allow_variations)){
                        
                        $sqlCount = "SELECT count(value) as num_values FROM ml_attributes_required
                        WHERE  ml_attributes_required.store_id = ? 
                        AND ml_attributes_required.category_id LIKE ?
                        AND ml_attributes_required.attribute_id = ? "; 
                        $queryCount = $this->db->query($sqlCount, array($this->store_id, $value['category_id'], $value['attribute_id']));
                        $attributesCount = $queryCount->fetch(PDO::FETCH_ASSOC);
                        
                        
                       
                        $value['num_values'] = $attributesCount['num_values'];
                        $attributesRes[] = $value;
    //                     pre($attributesRes);die;
                    }
                }
                
            }

//             pre($attributesRes);die;
 
            
            return $attributesRes;
            

        
        
        
    }
    
} 