<?php 

class SetupModel 
{

	/**
	 * @var string
	 * Class Unique ID
	 */
	public $store_id;
	
	/**
	 * @var string
	 */
	public $app_key;

	/**
	 * @var string
	 */
	public $account;

	/**
	 * @var string
	 */
	public $token;

	/**
	 * @var string
	 */
	public $environment;


	/**
	 * @var string
	 */
	public $input_data;
	
	


	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
// 	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->store_id = $this->controller->userdata['store_id'];
	    
	    
	    
	}
	
	
	public function ValidateForm() {

	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        
	        foreach ( $_POST as $property => $value ) {
	            
	            if(property_exists($this,$property)){
    	                
    	            if( !empty( $value ) ){
    	            	
    	                $this->{$property} = $value;
    	                
    	            }else{
    	                $required = array('app_key', 'token');
    	                
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
	    
	    $db_check_setup = $this->db->query (
	        'SELECT * FROM `module_vtex` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $db_check_setup ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_setup = $db_check_setup->fetch();
	    $storeId = $fetch_setup['store_id'];
	    
	    if ( ! empty( $storeId ) ) {
	        $query = $this->db->update('module_vtex', 'store_id', $storeId, array(
	            'app_key' => $this->app_key,
	            'token' => $this->token,
	            'environment' => $this->environment,
	            'account' => $this->account,
	            'input_data' => $this->input_data,
	        ));
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Configurações salva com sucesso.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('module_vtex', array(
	        	'store_id' => $this->store_id,
	            'app_key' => $this->app_key,
	            'token' => $this->token,
	            'environment' => $this->environment,
	            'account' => $this->account,
	            'input_data' => $this->input_data
	        )
	            );
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            return;
	        } else {
	            
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Configurações salva com sucesso.</div>';
	            return;
	        }
	    }
	    
	}
	
	
	
	
	
	public function Load()
	{
        if(!empty($this->store_id)){
            
            $query = $this->db->query("SELECT * FROM module_vtex WHERE `store_id`= {$this->store_id}" );
            
            $loaded = $query->fetch(PDO::FETCH_ASSOC);
            
            if(!$loaded){
                return array();
            }
            
            foreach($loaded as $key => $value)
            {
                $column_name = str_replace('-','_',$key);
                $this->{$column_name} = $value;
                
            }
            
            
        }else{
            
            return;
            
        }
	    
	}
	
	
	
	
}

?>