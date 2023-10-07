<?php 

class ModulesModel extends MainModel
{
	/**
	 * @var int
	 * Class Unique ID
	 */
	public $id;

	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 */
	public $method;
	
	/**
	 * @var int
	 * Class Unique ID
	 */
	public $status = 1;


	
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
	                $arr = array('name', 'type');
	                
	                if( in_array($property, $arr) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
	                }
	                
	            }
	            
	        }
	        
	        
	        
	        return true;
	        
	    } else {
	        
	        if ( in_array('edit', $this->parametros )) {
	            
	            $this->Load();
	            
	        }
	        
	        if ( in_array('del', $this->parametros )) {
	            
	            $this->Delete();
	            
	        }
	        
	        return;
	        
	    }
	    
	}
	
	public function Save(){
	    
	    $db_check_store = $this->db->query (
	        'SELECT * FROM `modules` WHERE name LIKE ?',
	           array($this->name)
	        );
	    
	    if ( ! $db_check_store ) {
	        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
	        return;
	    }
	    
	    $fetch_module = $db_check_store->fetch();
	    
	    $this->id = $fetch_module['id'];
	    
	    
	    if ( ! empty( $this->id ) ) {
	        $query = $this->db->update('modules', 'id', $this->id, array(
	            'name' => $this->name,
	            'type' => $this->type,
	            'method' => $this->method,
	            'descripion' => $this->description
	            
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully updated.</div>';
	            
	            return;
	        }
	    } else {
	        
	        $query = $this->db->insert('modules', array(
	            'name' => $this->name,
	            'type' => $this->type,
	            'method' => $this->method,
	            'description' => $this->description
	            
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
	
	public function ListModules()
	{
		$query = $this->db->query('SELECT * FROM `modules`  WHERE `status`= ? ',
		          array($this->status)
				);
		if ( ! $query ) {
			return array();
		}
		return $query->fetchAll(PDO::FETCH_ASSOC);
	
	}
	
	
	
	public function Load()
	{

	        
        $query = $this->db->query('SELECT * FROM modules WHERE `id`= ?', array( $id ) );
        
        foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
        {
            $column_name = str_replace('-','_',$key);
            $this->{$column_name} = $value;
            
        }
	        
	}
	
}

?>