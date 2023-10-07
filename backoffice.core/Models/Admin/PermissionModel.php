<?php 

class PermissionModel extends MainModel
{
	/**
	 * @var int
	 * Class Unique ID
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $permission;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $level;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 */
	public $store_id = 0;


	
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
	    
	    $db_check_store = $this->db->query (
	        'SELECT * FROM `permissions` WHERE permission = ?',
	           array($this->permission)
	        );
	    
	    if ( ! $db_check_store ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_permission = $db_check_store->fetch();
	    
	    $this->id = $fetch_permission['id'];
	    
	    
	    if ( ! empty( $this->id ) ) {
	        $query = $this->db->update('permission', 'id', $this->id, array(
	            'permission' => $this->permission,
	            'name' => $this->name,
	            'level' => $this->level,
	            'descripion' => $this->description,
	            'store_id' => $this->store_id
	            
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully updated.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('permissions', array(
	            'permission' => $this->permission,
	            'name' => $this->name,
	            'level' => $this->level,
	            'descripion' => $this->description,
	            'store_id' => $this->store_id
	            
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
	
	public function ListPermissions()
	{
		$query = $this->db->query('SELECT * FROM `permissions`  WHERE `store_id`= ? ',
		          array($this->store_id)
				);
		if ( ! $query ) {
			return array();
		}
		return $query->fetchAll(PDO::FETCH_ASSOC);
	
	}
	
	
	public function Load()
	{
	    if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
	        
	        $id = chk_array( $this->parametros, 1 );
	        
	        $query = $this->db->query('SELECT * FROM permissions WHERE `id`= ?', array( $id ) );
	        
	        foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
	        {
	            $column_name = str_replace('-','_',$key);
	            $this->{$column_name} = $value;
	            
	        }
	        
	    }else {
	        
	        return;
	        
	    }
	}
	
}

?>