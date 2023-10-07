<?php 

class SetupModel extends MainModel
{

	public $store_id;

	public $wsdl;

	public $session_id;

	public $user;

	public $password;
	
	public $import_products;
	
	public $export_products;


	
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
	            if(!empty($value)){
	                if(property_exists($this,$property)){
	                    
	                    $this->{$property} = $value;
	                    
	                }
	            }else{
	                $req = array();
	                
	                if( in_array($property, $req) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
	                }
	                
	            }
	            
	        }
	        
	        
	        
	        return true;
	        
	    } 
	}
	
	
	public function Save(){
	    
	    $db_check_setup = $this->db->query (
	        'SELECT * FROM `module_onbi` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $db_check_setup ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_setup = $db_check_setup->fetch();
	    $storeId = $fetch_setup['store_id'];
	    
	    
	       if ( ! empty( $storeId ) ) {
	           $query = $this->db->update('module_onbi', 'store_id', $storeId, array(
               'wsdl' => $this->wsdl,
               'session_id' => $this->session_id,
	           'user' => $this->user,
	           'password' => $this->password,
	           'import_products' => $this->import_products,  
	           'export_products' => $this->export_products
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Setup successfully updated.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('module_onbi', array(
	            'store_id' => $this->store_id,
	            'wsdl' => $this->wsdl,
	            'session_id' => $this->session_id,
	            'user' => $this->user,
	            'password' => $this->password,
	            'import_products' => $this->import_products,
	            'export_products' => $this->export_products
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
            
            $query = $this->db->query('SELECT * FROM module_onbi WHERE `store_id`= ?', array( $this->store_id ) );
            
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