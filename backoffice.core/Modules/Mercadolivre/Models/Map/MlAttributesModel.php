<?php 

class MlAttributesModel extends MainModel
{
    
    public $id;

	public $store_id;
	
	public $category_id;
	
	public $attribute_id;
	
	public $attribute;
	
	public $ml_attributes_id;
	

	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->store_id = $this->controller->userdata['store_id'];
	    
	    
	}
	
	public function ValidateForm() {
	    
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        
	        foreach ( $_POST as $property => $value ) {
	            
	            if(property_exists($this,$property)){
	                
	                if( !empty( $value ) ){
	                    
	                    $this->{$property} = $value;
	                    
	                }else{
	                    $required = array('category_id', 'attribute_id', 'attribute', 'ml_attributes_id');
	                    
	                    if(in_array($property, $required)){
	                        
	                        $this->form_msg = "<div class='alert alert-danger alert-dismissable'> There are empty fields. Data has not been sent.</div>";
	                        
	                        return;
	                        
	                    }
	                    
	                }
	                
	            }
	            
	        }
	        
	        return true;
	        
	    } else {
	        
	        return;
	        
	    }
	    
	}
	
	public function Save(){
	    
	    if ( empty( $this->id ) ) {
	        
	        $query = $this->db->insert('ml_attributes_relationship', array(
	            'store_id' => $this->store_id,
	            'category_id' => $this->category_id,
	            'attribute_id' => $this->attribute_id,
	            'attribute' => $this->attribute,
	            'ml_attribute_id' => $this->ml_attribute_id
	        )
	            );
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            return;
	        } else {
	            
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Information successfully registered.</div>';
	            return;
	        }
	        
	    } else {
	        
	        $query = $this->db->update('ml_attributes_relationship', 'id', $this->id, array(
	            'category_id' => $this->category_id,
	            'attribute_id' => $this->attribute_id,
	            'attribute' => $this->attribute,
	            'ml_attribute_id' => $this->ml_attribute_id
	        ));
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Setup successfully updated.</div>';
	            
	            return;
	        }
	    }
	    
	}
	
	public function ListAttributesRequired()
	{
	    // Simplesmente seleciona os dados na base de dados
// 	    $query = $this->db->query('SELECT * FROM `ml_attributes_required` 
//             WHERE store_id = ? AND category_id = ?', 
// 	        array($this->store_id, $this->category_id)
// 	        );
	    $sql = "SELECT 
            ml_attributes_required.category_id, 
            ml_attributes_required.attribute_id as required_attribute_id, 
            ml_attributes_required.value_type, 
            ml_attributes_required.name, 
            ml_attributes_required.tag,
            ml_attributes_required.required,

            ml_attributes_relationship.attribute, 
            ml_attributes_relationship.attribute_id,
            ml_attributes_relationship.ml_category_id, 
            ml_attributes_relationship.ml_attribute_id

            FROM ml_attributes_required

            LEFT JOIN ml_attributes_relationship 

            ON ml_attributes_relationship.ml_category_id = ml_attributes_required.category_id
            AND ml_attributes_relationship.ml_attribute_id = ml_attributes_required.attribute_id

            WHERE ml_attributes_required.store_id = {$this->store_id} 
            AND ml_attributes_required.category_id = '{$this->category_id}' 

            GROUP BY ml_attributes_required.attribute_id";
	    $query = $this->db->query($sql);
	    
	    // Verifica se a consulta está OK
	    if ( ! $query ) {
	        return array();
	    }
	    // Preenche a tabela com os dados do usuário
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	
	public function LevelRelationshipAttributes(){
	    $sql = "SELECT count(*) FROM ml_attributes_required WHERE store_id = {$this->store_id} 
        AND category_id = '{$this->category_id}'";
	    $query = $this->db->query($sql);
	    
	    return $query->fetch(PDO::FETCH_ASSOC);
	    
	    
	    
	    
	}
	
	public function AttributesRelationship(){
	    
	    $query = $this->db->query('SELECT * FROM `ml_attributes_relationship` WHERE store_id = ?', 
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	    
	}
	
}

?>