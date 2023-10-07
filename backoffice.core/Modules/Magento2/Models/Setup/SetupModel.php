<?php 

class SetupModel extends MainModel
{

	public $store_id;
	
	public $Store;
	
	public $type;

	public $api_host;

	public $token;

	public $username;

	public $password;
	
	public $date_expiration_token;
	
	public $Consumer_Key;
	
	public $Consumer_Secret;
	
	public $Access_Token;
	
	public $Access_Token_Secret;
	
	
	
	
	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->store_id = $this->controller->userdata['store_id'];
	    
	    
	}
	
	
	public function ValidateForm() {
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['save'] ) ) {
	        foreach ( $_POST as $property => $value ) {
	            if(!empty($value)){
	                if(property_exists($this,$property)){
	                    
	                    $this->{$property} = $value;
	                    
	                }
	            }else{
	                $req = array('store','type','username','password','api_host','Consumer_Key','Consumer_Secret','Access_Token','Access_Token_Secret');
	                
	                if( in_array($property, $req) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
	                }
	                
	            }
	            
	        }
	        return true;
	    }else{
	    	return ;
	    }
	}
	
	
	public function Save(){
	    
	    $db_check_setup = $this->db->query (
	        'SELECT * FROM `module_mg2` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $db_check_setup ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_setup = $db_check_setup->fetch();
	    $storeId = $fetch_setup['store_id'];
	    
	    if ( ! empty( $storeId ) ) {
	    	
	    	$query = $this->db->update('module_mg2', 'store_id', $storeId, array(
	                'store' => $this->Store,
	        		'type' => $this->type,
		            'api_host' => $this->api_host,
		            'username' => $this->username,
		            'password' => $this->password,
		            'Consumer_Key' => $this->Consumer_Key,
	           		'Access_Token' => $this->Access_Token,
	           		'Access_Token_Secret' => $this->Access_Token_Secret
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Setup successfully updated.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('module_mg2', array(
		            'store_id' => $this->store_id,
	        		'store' => $this->Store,
	        		'type' => $this->type,
		            'api_host' => $this->api_host,
		            'username' => $this->username,
		            'password' => $this->password,
		            'Consumer_Key' => $this->Consumer_Key,
	           		'Access_Token' => $this->Access_Token,
	           		'Access_Token_Secret' => $this->Access_Token_Secret
	        ));
	        
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
            
            $query = $this->db->query('SELECT * FROM module_mg2 WHERE `store_id`= ?', array( $this->store_id ) );
            
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