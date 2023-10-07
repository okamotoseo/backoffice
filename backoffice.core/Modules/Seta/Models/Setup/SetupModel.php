<?php 

class SetupModel extends MainModel
{

	/**
	 * @var string
	 * Class Unique ID
	 */
	public $store_id;
	
	/**
	 * @var string
	 */
	public $host;

	/**
	 * @var string
	 */
	public $port;

	/**
	 * @var string
	 */
	public $dbname;

	/**
	 * @var string
	 */
	public $user;

	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var string
	 */
	public $description;


	
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
	        'SELECT * FROM `module_seta` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $db_check_setup ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_setup = $db_check_setup->fetch();
	    $storeId = $fetch_setup['store_id'];
	    
	    
	       if ( ! empty( $storeId ) ) {
	           $query = $this->db->update('module_seta', 'store_id', $storeId, array(
	            'host' => $this->host,
	            'port' => $this->port,
	            'dbname' => $this->dbname,
	            'user' => $this->user,
	            'password' => $this->password,
	            'description' => $this->description
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Setup successfully updated.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('module_seta', array(
	            'store_id' => $this->store_id,
	            'host' => $this->host,
	            'port' => $this->port,
	            'dbname' => $this->dbname,
	            'user' => $this->user,
	            'password' => $this->password,
	            'description' => $this->description
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
            
            $query = $this->db->query('SELECT * FROM module_seta WHERE `store_id`= ?', array( $this->store_id ) );
            
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