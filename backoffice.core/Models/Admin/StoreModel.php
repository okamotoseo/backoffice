<?php 

class StoreModel extends MainModel
{
	/**
	 * @var int
	 * Class Unique ID
	 */
	public $id;
	
	/**
	 * @var int
	 * Class Unique ID
	 */
	public $account_id;
	/**
	 * @var string
	 */
	public $store;
	
	/**
	 * @var string
	 */
	public $modules;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $email_sac;

	/**
	 * @var string
	 */
	public $email_send;

	/**
	 * @var string
	 */
	public $phone;

	/**
	 * @var string
	 */
	public $company;

	/**
	 * @var string
	 */
	public $cnpj;

	/**
	 * @var string
	 */
	public $address;

	/**
	 * @var string
	 */
	public $postalcode;

	/**
	 * @var string
	 */
	public $neighborhood;

	/**
	 * @var string
	 */
	public $number;

	/**
	 * @var string
	 */
	public $city;

	/**
	 * @var string
	 */
	public $state;

	
	public function __construct( $db = false, $controller = null ) {
	    
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    $this->parametros = $this->controller->parametros;
	    
	    $this->userdata = $this->controller->userdata;
	    
	    $this->account_id =  $this->controller->userdata['account_id'];
	}
	
	
	public function ValidateForm() {
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        
	        
	        foreach ( $_POST as $property => $value ) {
	            if(!empty($value)){
	                if(property_exists($this,$property)){
	                    
	                    $this->{$property} = $value;
	                    
	                }
	            }else{
	                $arr = array();
	                
	                if( in_array($property, $arr) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
	                }
	                
	            }
	            
	        }
	        
	        return true;
	        
	    } else {
	        
	        if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
	            $this->Load();
	            
	        }
	        
	        return;
	        
	    }
	    
	}
	
	public function Save(){
	    
	    $storeId = chk_array( $this->parametros, 3 );
	    
	    $modules = serialize( $this->modules );
	    
	    
	    if ( ! empty( $storeId ) ) {
	        $query = $this->db->update('stores', 'id', $storeId, array(
	            'modules' => $modules,
	            'company' => $this->company,
	            'url' => $this->url,
	            'email_sac' => $this->email_sac,
	            'email_send' => $this->email_send,
	            'phone' => $this->phone,
	            'cnpj' => $this->cnpj,
	            'address' => $this->address,
	            'postalcode' => $this->postalcode,
	            'neighborhood' => $this->neighborhood,
	            'number' => $this->number,
	            'city' => $this->city,
	            'state' => $this->state
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully updated.</div>';
	            
	            return;
	        }
	    } else {
	        
	        if ( chk_array( $this->parametros, 4 ) == 'AccountId' ) {
	            
	            $accountId = chk_array( $this->parametros, 5 );
	        }
	        $accountId = isset($accountId) ? $accountId : $this->account_id ;
	        $query = $this->db->insert('stores', array(
	            'account_id' =>  $accountId,
	            'store' => $this->store,
	            'modules' => $modules,
	            'company' => $this->company,
	            'url' => $this->url,
	            'email_sac' => $this->email_sac,
	            'email_send' => $this->email_send,
	            'phone' => $this->phone,
	            'cnpj' => $this->cnpj,
	            'address' => $this->address,
	            'postalcode' => $this->postalcode,
	            'neighborhood' => $this->neighborhood,
	            'number' => $this->number,
	            'city' => $this->city,
	            'state' => $this->state
	           )
	         );
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            return;
	        } else {
	            
	            $this->id = $this->db->last_id;
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully registered.</div>';
	            return;
	        }
	    }
	    
	}
	
	public function ListStores()
	{
		$query = $this->db->query('SELECT * FROM `stores`  WHERE `account_id`= ? ',
		          array($this->account_id)
				);
	
		if ( ! $query ) {
			return array();
		}
		return $query->fetchAll(PDO::FETCH_ASSOC);
	
	}
	
	
	public function Load()
	{
	    if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
	        
	        $id = chk_array( $this->parametros, 3 );
	        
	        
	    }else {
	        
	        if(!empty($this->id)){
	            $id = $this->id;
	            
	            
	        }else{
	           
	           return;
	           
	        }
	        
	    }
	    $query = $this->db->query('SELECT * FROM stores WHERE `id`= ?', array( $id ) );
	    foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
	    {
	        $column_name = str_replace('-','_',$key);
	        $this->{$column_name} = $value;
	        
	    }
	    $this->modules = unserialize($this->modules);
	    
	}

	public function getStoreAccount(){
		$query = $this->db->query("SELECT * FROM accounts WHERE `id`= ?",
				array($this->account_id)
				);
		if ( ! $query ) {
			return array();
		}
		return $query->fetchAll(PDO::FETCH_ASSOC);
		
	}
	public function storeExists()
	{

		$query = $this->db->query("SELECT id FROM store_informations WHERE `account_id`= ? AND `cnpj`= ?",
									array($this->account_id, $this->cnpj)
								);
		if ( ! $query ) {
			return array();
		}
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
}

?>