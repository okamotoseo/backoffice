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
	public $email;

	/**
	 * @var string
	 */
	public $api_key;


	/**
	 * @var string
	 */
	public $account_key;

	/**
	 * @var string
	 */
	public $base_uri;



	
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
	        'SELECT * FROM `module_skyhub` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $db_check_setup ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_setup = $db_check_setup->fetch();
	    $storeId = $fetch_setup['store_id'];
	    
	    
	       if ( ! empty( $storeId ) ) {
	           $query = $this->db->update('module_skyhub', 'store_id', $storeId, array(
	           'email' => $this->email,
	           'api_key' => $this->api_key,
               'account_key' => $this->account_key,
               'base_uri' => $this->base_uri
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Setup successfully updated.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('module_skyhub', array(
	            'store_id' => $this->store_id,
	            'email' => $this->email,
	            'api_key' => $this->api_key,
	            'account_key' => $this->account_key,
	            'base_uri' => $this->base_uri
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
            
            $query = $this->db->query('SELECT * FROM module_skyhub WHERE `store_id`= ?', array( $this->store_id ) );
            
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