<?php
/**
 * Modelo para gerenciar valores dos atributos
 *
 */
class AttributesValuesModel extends MainModel
{
    /**
     * @var int
     */
	public $id;
	
	public $store_id;
    
	public $product_id;
	
    public $attribute_id;
    
    public $alias;
    
    public $attributesValues = array();
    
    public $category;
    
    

    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
        
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
        }
        
    }
    
    public function ValidateForm() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] &&  isset ( $_POST['attributes-values'] ) ) {
            
            if(isset($_POST['attr_values'])){
            	foreach ( $_POST['attr_values'] as $attributeId => $value ) {
    
            		$this->attributesValues[] = array(
            				"attribute_id" => $attributeId,
            		        "name" => key($value),
            				"value" => $value[key($value)],
            		        "marketplace" => 'Sysplace'
            		);
            		
            	}
            }
            if(isset($_POST['attr_values_az'])){
            	foreach ( $_POST['attr_values_az'] as $attributeId => $value ) {
            
            		$this->attributesValues[] = array(
            				"attribute_id" => $attributeId,
            				"name" => key($value),
            				"value" => $value[key($value)],
            				"marketplace" => 'Amazon'
            		);
            
            	}
            }
            if(isset($_POST['attr_values_ml'])){
            	foreach ( $_POST['attr_values_ml'] as $attributeIdMl => $valueMl ) {
            	    
            	    $this->attributesValues[] = array(
            	        "attribute_id" => $attributeIdMl,
            	        "name" => key($valueMl),
            	        "value" => $valueMl[key($valueMl)],
            	        "marketplace" => 'Mercadolivre'
            	    );
            	    
            	}
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
            
        } else {
        	
//         	if ( in_array('Product', $this->parametros )) {
//         	    $key = array_search('Product', $this->parametros);
//         	    $this->product_id = get_next($this->parametros, $key);
//         		$this->Load();
        		 
//         	}
        	
        	if ( in_array('Product', $this->parametros )) {
        	    
        	    $key = array_search('Product', $this->parametros);
        	    
        	    $productId = get_next($this->parametros, $key);
        	    $this->product_id  = is_numeric($productId) ? $productId :  '';
        	    
        	    if(!empty($this->product_id)){
        	        
        	        $this->Load();
        	        
        	    }
        	    
        	}
        	
//         	if ( in_array('CopyProduct', $this->parametros )) {
//         	    $this->CopyAttrValuesProduct();
//         	}
        	
            return;
        }
        
    }
    
    public function Save(){
    	
    	$dataLog = array();
    	foreach ($this->attributesValues as $attribute => $value){
    		
    		$sql = 'SELECT * FROM attributes_values WHERE store_id = ? AND product_id = ? AND attribute_id = ?';
    		$query = $this->db->query($sql, array($this->store_id, $this->product_id, $value['attribute_id']));
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		
    		if ( !isset($res['product_id']) AND !empty($value['value'])) {
    			
    		   $data = array(
    			        'store_id' => $this->store_id,
    					'product_id' => $this->product_id,
    					'attribute_id' => $value['attribute_id'],
    					'name' => $value['name'],
    					'value' => $value['value'],
    			        'marketplace' => $value['marketplace']
    			);
    			$query = $this->db->insert('attributes_values', $data);
    			//TODO: salvar marketplace 
    			
    			
    			if ( ! $query ) {
    				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    				//TODO: substituir por log de erro para administrador apenas
    			}else{
    				$dataLog['attribute']['created'][] = $data; 
        			$this->form_msg = '<div class="alert alert-success alert-dismissable">Atributos cadastrado com sucesso.</div>';
    			}
    			
    		} else {
    		    
    		    if(!empty($value['value']) AND isset($res['product_id'])){
//         			$sqlUpdate = "UPDATE attributes_values SET  name = '{$value['name']}',  value = '{$value['value']}',  marketplace = '{$value['marketplace']}' 
//         			WHERE store_id = {$this->store_id} 
//                     AND product_id = {$this->product_id} AND attribute_id = '{$value['attribute_id']}'";
//     		    	$query = $this->db->query($sqlUpdate);
					if(isset($value['name'])){
						$data = array('name' => $value['name'],
								'value' => $value['value'],
								'name' => $value['name'],
								'marketplace' => $value['marketplace']
						);
					}else{
	                    $data = array('name' => $value['name'],  
	                    		'value' => $value['value'], 
	                    		'marketplace' => $value['marketplace']
	                    );
					}
        			$query = $this->db->update('attributes_values', 
        					array('store_id', 'product_id', 'attribute_id'),
        					array($this->store_id, $this->product_id, $value['attribute_id']),
        					$data);
        			
        			$dataLog['attribute']['updated'][] = $data;
    		    }
    		    if(empty($value['value']) AND isset($res['product_id'])){
    		        $sqlDelete = "DELETE FROM attributes_values  WHERE store_id = {$this->store_id} 
                    AND product_id = {$this->product_id} AND attribute_id = '{$value['attribute_id']}'";
    		        $query = $this->db->query($sqlDelete);
    		        $dataLog['attribute']['deleted'][] = array('attribute_id' => $value['attribute_id']);
    		    }
    		    
    			if ( ! $query ) {
    				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			}else{
        			$this->form_msg = '<div class="alert alert-success alert-dismissable">Atributos atualizados com sucesso.</div>';
    			}
    			
    		}
    	}
    	parent::productsLog($this->product_id, 'Atualização de atributos', $dataLog);
    	 
    }
    
    public function Load()
    {
    
    	if(!empty($this->product_id) ){
    	    
    	    $query = $this->db->query('SELECT * FROM attributes_values WHERE `product_id`= ? AND store_id = ?', 
    	        array( $this->product_id, $this->store_id ) 
    	        );
    
    		$this->attributesValues = $query->fetchAll(PDO::FETCH_ASSOC);
    		
    		return $this->attributesValues;


    	} else {
    
    		return;
    
    	}
    
    }
    
    
    public function GetAttributesValues()
    {
        if(empty($this->product_id)){
            return array();
        }
        
        
        $sql = "SELECT attributes_values.product_id, attributes_values.attribute_id,
        attributes_values.value, attributes_values.id_attribute, attributes.attribute, attributes_values.marketplace
        FROM attributes_values 
        LEFT JOIN attributes ON attributes.alias = attributes_values.attribute_id AND attributes.store_id = attributes_values.store_id 
        WHERE attributes_values.store_id = {$this->store_id} AND attributes_values.product_id = {$this->product_id}";
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function LoadAlias()
    {
        
        if(!empty($this->product_id) ){
            
            $query = $this->db->query('SELECT * FROM attributes_values WHERE `product_id`= ? AND store_id = ?',
                array( $this->product_id, $this->store_id )
                );
            
            $attributesValues = $query->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($attributesValues as $key => $value){
               $sql = "SELECT ml_attribute_id FROM ml_attributes_relationship WHERE store_id = {$this->store_id}
                AND attribute LIKE '{$value['attribute_id']}' AND ml_category_id LIKE '{$this->category}' LIMIT 1";
                
                $queryAlias = $this->db->query($sql);
                $alias = $queryAlias->fetch(PDO::FETCH_ASSOC);
                
//                 if(isset($alias['ml_attribute_id'])){
                    $attributesValues[$key]['ml_attribute_id'] = $alias['ml_attribute_id'];
//                 }
                
            }
            $this->attributesValues = $attributesValues;
            
            return $this->attributesValues;
            
            
        } else {
            
            return;
            
        }
        
    }
    
    public function CopyAttrValuesProduct()
    {
        $key = array_search('CopyProduct', $this->parametros);
        
        if(!empty($key)){
            $productId = get_next($this->parametros, $key);
            
            $query = $this->db->query('SELECT * FROM attributes_values WHERE store_id = ? AND product_id= ?', 
                array( $this->store_id, $productId) 
                );
            
            $this->attributesValues = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($this->attributesValues as $attribute => $value){

                
//                 $query = $this->db->insert('attributes_values', array(
//                     'product_id' => $this->product_id,
//                     'store_id' => $this->store_id,
//                     'attribute_id' => $value['attribute_id'],
//                     'attribute' => $value['attribute'],
//                     'value' => $value['value']
//                 ));
                
               $sql = "INSERT INTO `attributes_values`(`store_id`,`product_id`, `attribute_id`, `alias`, `value`) VALUES 
                ({$this->store_id}, {$this->product_id},'{$value['attribute_id']}','{$value['alias']}','{$value['value']}')";
                $this->db->query($sql);
                
            }
            return $this->attributesValues;
            
            
        } else {
            return;
            
        }
        
    }
    
    
    
} 