<?php

class AttributesValuesModel extends MainModel
{
    public $id;
    
    public $store_id;
    
    public $product_id;
    
    public $attribute_id;
    
    public $attribute_code;
    
    public $value_id;
    
    public $attributesValues = array();
    

    public function __construct($db = false,  $controller = null, $storeId = null)
    {
        $this->db = $db;
        
        $this->store_id = $storeId;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
        }

        
    }
    
    
    public function Save(){

        foreach ($this->attributesValues as $attribute => $value){
        	
        	

        	$query = $this->db->query('SELECT * FROM `attributes`  WHERE `store_id` = ? AND alias LIKE ?',
        				array($this->store_id, $value['attribute_id'])
        			);
        	$res = $query->fetch(PDO::FETCH_ASSOC);
        	if(!isset($res['attribute'])){
        		$query = $this->db->insert('attributes', array(
        				'store_id' => $this->store_id,
        				'attribute' => $value['name'],
        				'alias' => $value['attribute_id'],
        				'marketplace' => $value['marketplace']
        		));
        	
        		
        	}
        	
            $sql = 'SELECT * FROM attributes_values WHERE store_id = ? AND product_id = ? AND attribute_id LIKE ?';
            $query = $this->db->query($sql, array($this->store_id, $this->product_id, $value['attribute_id']));
            $res = $query->fetch(PDO::FETCH_ASSOC);
            pre($res);
            if ( !isset($res['product_id']) AND !empty($value['value'])) {
                
                $query = $this->db->insert('attributes_values', array(
                    'store_id' => $this->store_id,
                    'product_id' => $this->product_id,
                    'attribute_id' => $value['attribute_id'],
                	'name' => $value['name'],
                    'value' => strip_tags($value['value']),
                    'marketplace' => $value['marketplace']
                ));
                

            } else {
            	
				echo "existe<br>";
				
				if(!empty($value['value']) AND isset($res['product_id'])){
					
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
        
        
    }
    
    
    public function GetProductAttributesValues(){
        
        if(!isset($this->product_id)){
            return array();
        }
        
        $sql = "SELECT * FROM attributes_values WHERE store_id = {$this->store_id} AND product_id = {$this->product_id}";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
   
    
    
    
    
    
} 