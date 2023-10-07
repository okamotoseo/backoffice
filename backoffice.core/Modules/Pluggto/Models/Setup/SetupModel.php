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
	public $app_id;

	/**
	 * @var string
	 */
	public $secret_key;

	/**
	 * @var string
	 */
	public $access_token = null;

	/**
	 * @var string
	 */
	public $expires_in = null;

	/**
	 * @var string
	 */
	public $refresh_token = null;

	/**
	 * @var string
	 */
	public $code = null;
	/**
	 * @var string
	 */
	public $nickname;
	
	/**
	 * @var string
	 */
	public $seller_id = null;
	
	/**
	 * @var string
	 */
	public $tax = 0;
	
	/**
	 * @var string
	 */
	public $scope;
	
	/**
	 * @var string
	 */
	public $meli;
	
	/**
	 * @var string
	 */
	public $uri = "https://backoffice.sysplace.com.br/Modules/Configuration/Mercadolivre/Setup";
// 	public $uri = "https://backoffice.sysplace.com.br/modules/configuration/mercadolivre/setup/";
	
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * @var string
	 */
	public $site_id = 'MLB';
	


	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->store_id = $this->controller->userdata['store_id'];
	    
	    
	    
	}
	
	
	public function ValidateForm() {
		
		if( isset($this->app_id) && isset($this->secret_key) ){
			 
			$this->meli = new Meli($this->app_id, $this->secret_key);
			 
			$this->url = $this->meli->getAuthUrl($this->uri, Meli::$AUTH_URL[$this->site_id]);
			 
			 
		}
		
		
		if ( isset( $_GET['code'] ) ) {
		
			$this->code = $_GET['code'];
			
			$res = $this->meli->authorize($this->code, $this->uri);
			
			if(isset($res['body']->expires_in)){
			    
    			$this->expires_in = time() + $res['body']->expires_in;
    			
    			$this->access_token = $res['body']->access_token;
    			
    			$this->refresh_token = $res['body']->refresh_token;
    			
    			$this->seller_id = $res['body']->user_id;
    			
    			$this->scope = $res['body']->scope;
    			
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
	        'SELECT * FROM `module_mercadolivre` WHERE store_id = ?',
	        array($this->store_id)
	        );
	    
	    if ( ! $db_check_setup ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_setup = $db_check_setup->fetch();
	    $storeId = $fetch_setup['store_id'];
	    
	    if ( ! empty( $storeId ) ) {
	        $query = $this->db->update('module_mercadolivre', 'store_id', $storeId, array(
	            'app_id' => $this->app_id,
	            'secret_key' => $this->secret_key,
	            'access_token' => $this->access_token,
	            'expires_in' => $this->expires_in,
	            'refresh_token' => $this->refresh_token,
	            'code' => $this->code,
        		'nickname' => $this->nickname,
        		'seller_id' => $this->seller_id,
        		'tax' => $this->tax,
	        	'scope' => $this->scope
	        ));
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Configurações salva com sucesso.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('module_mercadolivre', array(
	        	'store_id' => $this->store_id,
        		'app_id' => $this->app_id,
        		'secret_key' => $this->secret_key,
        		'access_token' => $this->access_token,
        		'expires_in' => $this->expires_in,
        		'refresh_token' => $this->refresh_token,
        		'code' => $this->code,
        		'nickname' => $this->nickname,
        		'seller_id' => $this->seller_id,
        		'tax' => $this->tax,
	        	'scope' => $this->scope
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
            
            $query = $this->db->query('SELECT * FROM module_mercadolivre WHERE `store_id`= ?', array( $this->store_id ) );
            
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