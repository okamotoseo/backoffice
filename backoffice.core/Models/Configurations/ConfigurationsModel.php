<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class ConfigurationsModel extends MainModel
{
    /**
     * @var int
     */
	public $id;
	
    public $store_id;
    
    public $module;
    
    public $name;
	 
	public $value;
	 
	public $description;
	 
	public $method;
	 
	public $formula;
	
	public $configurations = array();
    

    

    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        if(isset($controller)){
        	$this->store_id = $controller->userdata['store_id'];
        }
        
    }
    
    
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
        	
//         	pre($_POST);die;
        	
            foreach ( $_POST as $property => $value ) {
            	
            	
            	if($value == 0 OR !empty($value)){
	                if(property_exists($this,$property)){
	                    $this->{$property} = $value;
	                }
            	}else{
            		$arr = array('');
            		if( in_array($property, $arr) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
            		}
                }
                
            }
            
          	if(isset($_POST['checkout-configuration'])){
          		
          		$this->module = 'Checkout';
          		
          		$this->method = 'Checkout';
          		
          	}
          	
          	if(isset($_POST['products-configuration'])){
          	
          		$this->module = 'Products';
          	
          		$this->method = 'Products';
          	
          	}
          	
          	
          	
            return true;
            
        } else {
        	
            return;
            
        }
        
    }
    
    
    
    
    public function Save(){
    	 
    	$dataLog = array();
    	
    	foreach ($this->configurations as $module => $config){
    		
    		foreach($config as $name => $value){
    			
 	    		$sql = 'SELECT * FROM store_config WHERE store_id = ?  AND module = ? AND name = ?';
	    		$query = $this->db->query($sql, array($this->store_id, $module, $name));
	    		$res = $query->fetch(PDO::FETCH_ASSOC);
	    		if ( !isset($res['name']) ) {
	    			 
	    			$data = array(
	    					'store_id' => $this->store_id,
	    					'name' => $name,
	    					'value' => $value,
	    					'description' => $name,
	    					'module' => $module,
	    					'method' => $this->method,
	    					'formula' => $this->formula,
	    					'created' => date('Y-m-d H:s:i'),
	    					'updated' => date('Y-m-d H:s:i')
	    			);
	    			
	    			$query = $this->db->insert('store_config', $data);
	    			 
// 	    			if ( ! $query ) {
// 	    				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
// 	    			}else{
// 	    				$dataLog['configurations']['created'][] = $data;
// 	    				$this->form_msg = '<div class="alert alert-success alert-dismissable">Atributos cadastrado com sucesso.</div>';
// 	    			}
	    			 
	    		} else {
	    			

	    			$data = array(
	    					'value' => $value,
	    					'description' => $name,
	    					'formula' => $this->formula,
	    					'updated' => date('Y-m-d H:s:i')
	    			);
	    			
	    			$query = $this->db->update('store_config', 
	    					array('store_id', 'id'), 
	    					array($this->store_id, $res['id']), 
	    					$data);
	    			 
// 	    			if ( ! $query ) {
// 	    				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
// 	    			}else{
// 	    				$dataLog['configurations']['updated'][] = $data;
// 	    				$this->form_msg = '<div class="alert alert-success alert-dismissable">Atributos cadastrado com sucesso.</div>';
// 	    			}
	    		 
	    		}
    		
    		}
    		
    	}
    
    }
    
    
    public function LoadConfigurations(){
    	
    	
//     	if(empty($this->module)){
    		
    		$sql = 'SELECT * FROM store_config WHERE store_id = ? ';
    		$query = $this->db->query($sql, array($this->store_id));
//     	}else{
    	
// 	    	$sql = 'SELECT * FROM store_config WHERE store_id = ?  AND module LIKE ?';
// 	    	$query = $this->db->query($sql, array($this->store_id, $this->module));
//     	}
    	
    	$res = $query->fetchAll(PDO::FETCH_ASSOC);
    	
    	foreach ($res as $k => $value){
    		$this->configurations[$value['module']][$value['name']] = $value['value'];  
    	}
    	
    	return $this->configurations;
    	
    	
    }
    
    
} 