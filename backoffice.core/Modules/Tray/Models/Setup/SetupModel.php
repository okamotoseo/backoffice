<?php 

class SetupModel 
{

	public $store_id;
	
	public $access_token = null;

	public $refresh_token = null;

	public $code = null;
	
	public $date_expiration_access_token;
	
	public $date_expiration_refresh_token;
	
	public $date_activated;
	
	public $api_host;
	
	public $store;
	
	protected $uri = "https://backoffice.sysplace.com.br/Modules/Configuration/Tray/Setup";
	
	public $url;
	
	public $tax;
	
	public $tray;
	
	
	
	

	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->store_id = $this->controller->userdata['store_id'];
	    
	    
	    
	}
	
	
	public function ValidateForm() {
		
		
	    if ( isset( $_GET['code'] ) AND isset($_GET['api_address']) ) {
		
			$this->code = $_GET['code'];
			
			$this->api_host = $_GET['api_address'];
			
			$this->tray = new Tray($this->api_host);
			
			$res = $this->tray->authorize($this->code);
			
			
			if(isset($res['body']->date_expiration_access_token)){
			    
			    $this->date_expiration_access_token = $res['body']->date_expiration_access_token;
			    
			    $this->date_expiration_refresh_token = $res['body']->date_expiration_refresh_token;
			    
			    $this->date_activated = $res['body']->date_activated;
    			
    			$this->access_token = $res['body']->access_token;
    			
    			$this->refresh_token = $res['body']->refresh_token;
    			
    			$this->api_host = $res['body']->api_host;
    			
    			$this->store = $res['body']->store_id;
    			
    			return true;
    			
			}else{
			    return false;
			}
			 
		}
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        
	        foreach ( $_POST as $property => $value ) {
	            
	            if(property_exists($this,$property)){
    	                
    	            if( !empty( $value ) ){
    	            	
    	                $this->{$property} = $value;
    	                
    	            }else{
    	                $required = array('app_id', 'secret_key');
    	                
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
	        'SELECT * FROM `module_tray` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $db_check_setup ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_setup = $db_check_setup->fetch();
	    $storeId = $fetch_setup['store_id'];
	    
	    if ( ! empty( $storeId ) ) {
	        $query = $this->db->update('module_tray', 'store_id', $this->store_id, array(
	            'code' => $this->code,
	            'access_token' => $this->access_token,
	            'refresh_token' => $this->refresh_token,
	            'date_expiration_access_token' => $this->date_expiration_access_token,
	            'date_expiration_refresh_token' => $this->date_expiration_refresh_token,
	            'date_activated' => $this->date_activated,
	            'api_host' => $this->api_host,
	            'store' => $this->store
	        ));
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Configurações salva com sucesso.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('module_tray', array(
	            'store_id' => $this->store_id,
	            'code' => $this->code,
	            'access_token' => $this->access_token,
	            'refresh_token' => $this->refresh_token,
	            'date_expiration_access_token' => $this->date_expiration_access_token,
	            'date_expiration_refresh_token' => $this->date_expiration_refresh_token,
	            'date_activated' => $this->date_activated,
	            'api_host' => $this->api_host,
	            'store' => $this->store
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
            
            $query = $this->db->query('SELECT * FROM module_tray WHERE `store_id`= ?', array( $this->store_id ) );
            
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