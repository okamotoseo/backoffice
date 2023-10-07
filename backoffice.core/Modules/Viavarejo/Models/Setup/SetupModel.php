<?php 

class SetupModel extends MainModel
{

	/**
	 * @var int
	 * Class Unique ID
	 */
	public $id;
	
	/**
	 * @var int
	 */
	public $store_id;
	
	/**
	 * @var string
	 */
	public $client_id;
	
	/**
	 * @var string
	 */
	public $client_secret;

	/**
	 * @var string
	 */
	public $token;


	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->store_id = $this->controller->userdata['store_id'];
	    
	    
	}
	
	
	public function ValidateForm() {
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        
	        
	        foreach ( $_POST as $property => $value ) {
	            if(property_exists($this,$property)){
    	                
    	            if(!empty($value)){
    	                $this->{$property} = $value;
    	            }else{
    	                $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
    	                return;
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
	        'SELECT * FROM `module_viavarejo` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $db_check_setup ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_setup = $db_check_setup->fetch();
	    $storeId = $fetch_setup['store_id'];
	    
	    
	       if ( ! empty( $storeId ) ) {
	           $query = $this->db->update('module_viavarejo', 'store_id', $storeId, array(
	           'client_id' => $this->client_id,
               'client_secret' => $this->client_secret,
               'token' => $this->token
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Setup successfully updated.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('module_viavarejo', array(
	            'store_id' => $this->store_id,
	            'client_id' => $this->client_id,
               	'client_secret' => $this->client_secret,
               	'token' => $this->token
	        )
	            );
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            return;
	        } else {
	            
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Setup successfully registered.</div>';
	            return;
	        }
	    }
	    
	}
	
	
	public function Load()
	{
	    
        if(!empty($this->store_id)){
            
            $query = $this->db->query('SELECT * FROM module_viavarejo WHERE `store_id`= ?', array( $this->store_id ) );
            
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