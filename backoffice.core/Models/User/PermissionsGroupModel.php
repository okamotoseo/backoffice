<?php
/**
 * Modelo para gerenciar valores dos atributos
 *
 */
class PermissionsGroupModel extends MainModel
{
    /**
     * @var int
     */
	public $id;
	
	public $store_id;
	
	public $p_group;
	
	public $module;
	
	public $p_view;
	
	public $p_createp;
	
	public $p_update;
	
	public $p_delete;
	
    public $created;
    
    public $updated;
    
    public $permissionsGroupValues = array();
    
    public $category;
    
    public $defaultModules = array(
    		'configurations' => 'Configurações',
    		'customers' => 'Clientes',
    		'orders' => 'Pedidos',
    		'prices' => 'Preços',
    		'products' => 'Produtos',
    		'report' => 'Relatórios',
    		'user' => 'Usuários'
							
    );
    
    public $defaultGroups = array(
    	
    		1 => 'Tudo',
    		2 => 'Adiministrador',
    		3 => 'Gerente',
    		4 => 'Faturista',
    		5 => 'Estoquista',
    		6 => 'Editor',
    		7 => 'Colaborador'
    );
    
    public $defaultGroupPermissions = array(
    		
    		array('Tudo' => array(
    				'configurations' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
    				'customers' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
    				'orders' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
    				'prices' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
    				'products' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
    				'report' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
    				'user' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T')
    		)),
    		array('Adiministrador' => array(
	    		'configurations' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'customers' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'orders' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'prices' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'products' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'report' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'user' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T')
    		)),
    		array('Gerente' => array(
	    		'configurations' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'customers' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'orders' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'prices' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'products' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'report' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'user' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T')
    		)),
    		array('Faturista' => array(
	    		'configurations' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'customers' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'orders' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'prices' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'products' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'report' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'user' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F')
    		)),
    		array('Estoquista' => array(
	    		'configurations' => array('p_view' => 'T','p_create' => 'F','p_update' => 'F','p_delete' => 'F'),
	    		'customers' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'orders' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'prices' => array('p_view' => 'T','p_create' => 'F','p_update' => 'F','p_delete' => 'F'),
	    		'products' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'report' => array('p_view' => 'T','p_create' => 'F','p_update' => 'F','p_delete' => 'F'),
	    		'user' => array('p_view' => 'T','p_create' => 'F','p_update' => 'F','p_delete' => 'F')
    		)),
    		array('Editor' => array(
	    		'configurations' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'customers' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'orders' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'prices' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'products' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'report' => array('p_view' => 'T','p_create' => 'F','p_update' => 'F','p_delete' => 'F'),
	    		'user' => array('p_view' => 'T','p_create' => 'F','p_update' => 'F','p_delete' => 'F')
    		)),
    		array('Colaborador' => array(
	    		'configurations' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'customers' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'orders' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'prices' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'F'),
	    		'products' => array('p_view' => 'T','p_create' => 'T','p_update' => 'T','p_delete' => 'T'),
	    		'report' => array('p_view' => 'T','p_create' => 'F','p_update' => 'F','p_delete' => 'F'),
	    		'user' => array('p_view' => 'T','p_create' => 'F','p_update' => 'F','p_delete' => 'F')
    		))
    );    
    

    
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
        
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
        }
    }
    
    public function ValidateForm() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] &&  isset ( $_POST['save-usergroups'] ) ) {
            
            if(isset($_POST['group-permissions'])){
            	
            	foreach ($this->defaultModules as $key => $val ){
            	
	            	foreach ( $_POST['group-permissions'] as $module => $value ) {
	            		
	            		
	            		$this->permissionsGroupValues[$module] = $value;
	            		
	            		
	            		
	            	}
            	
            	}
            }
            foreach ( $_POST as $property => $value ) {
            	if(!empty($value)){
            		
	            	if(property_exists($this,$property)){
	            		$this->{$property} = $value;
	            	}
            	}else{
            		$required = array('p_group');
            		if( in_array($property, $required) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
            		}
                }
            }
            return true;
            
        } else {
        	
//         	if ( in_array('Product', $this->parametros )) {
//         	    $key = array_search('Product', $this->parametros);
//         	    $this->product_id = get_next($this->parametros, $key);
//         		$this->Load();
        		 
//         	}
        	
//         	if ( in_array('p_group', $this->parametros )) {
        	    
//         	    $key = array_search('p_group', $this->parametros);
        	    
//         	    $groupId = get_next($this->parametros, $key);
//         	    $this->p_group  = is_numeric($groupId) ? $groupId :  null;
        	    
//         	    if(!empty($this->p_group)){
        	        
//         	        $this->Load();
        	        
//         	    }
        	    
//         	}
        	
//         	if ( in_array('CopyProduct', $this->parametros )) {
//         	    $this->CopyAttrValuesProduct();
//         	}
        	
            return;
        }
        
    }
    
    public function CreateDefaultStorePermissions(){
    	
    	
    	foreach($this->defaultGroupPermissions as $key => $group){
    		
    		if(isset($group[$this->defaultGroups[$this->p_group]])){
//     			pre($group);
//     			pre($this->defaultGroups[$this->p_group]);
//     			pre($group[$this->defaultGroups[$this->p_group]]);
    			
	    		$this->permissionsGroupValues = $group[$this->defaultGroups[$this->p_group]];
	    		continue;
	    		
	    		
    		}
    		
    		

    		
    	}
//     	pre($this->permissionsGroupValues)
//     	pre($this->permissionsGroupValues);die;
    	$this->Save();
    }
    
    
    
    public function Save(){
    	foreach ($this->permissionsGroupValues as $modules => $permission ){
//     	    pre($value);die;
    		$sql = 'SELECT * FROM group_permissions WHERE store_id = ? AND p_group = ?  AND module LIKE ?';
    		$query = $this->db->query($sql, array($this->store_id, $this->p_group, $modules));
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		
    		if ( !isset($res['module']) ) {
    		   
    			$query = $this->db->insert('group_permissions', array(
    			        'store_id' => $this->store_id,
    					'p_group' => $this->p_group,
    					'module' => $modules,
    					'p_view' => $permission['p_view'],
    					'p_create' => $permission['p_create'],
    					'p_update' => $permission['p_update'],
    					'p_delete' => $permission['p_delete'],
    					'created' => date('Y-m-d H:i:s'),
    					'updated' =>  date('Y-m-d H:i:s')
    			));
    			
    			
    			if ( ! $query ) {
    				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			}else{
    				$this->id = $this->db->last_id;
        			$this->form_msg = '<div class="alert alert-success alert-dismissable">Atributos cadastrado com sucesso.</div>';
    			}
    			
    		} else {
    		    
    			$query = $this->db->update('group_permissions', array('store_id', 'id'), array( $this->store_id, $res['id']), array(
    					'p_view' => $permission['p_view'],
    					'p_create' => $permission['p_create'],
    					'p_update' => $permission['p_update'],
    					'p_delete' => $permission['p_delete']
    			));
    		    
    			if($query->rowCount() > 0){
    				
    				$query = $this->db->update('group_permissions', array('store_id', 'id'), array( $this->store_id, $res['id']), array(
    						'updated' =>  date('Y-m-d H:i:s')
    				));
    				
    			}
    		    
    			if ( ! $query ) {
    				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			}else{
    			 
        			$this->form_msg = '<div class="alert alert-success alert-dismissable">Grupo atualizados com sucesso.</div>';
    			}
    		}
    	}
    	
    	 
    }
    
    public function Load()
    {
    
    	if(empty($this->p_group) ){
    		return array();	
    	}
    	    
    	    $query = $this->db->query('SELECT * FROM group_permissions WHERE  store_id = ? AND p_group LIKE ?', 
    	        array($this->store_id,  $this->p_group) 
    	        );
    
    		
    	    
    	    $this->permissionsGroupValues = $query->fetchAll(PDO::FETCH_ASSOC);
    		
    		return $this->permissionsGroupValues;


    
    }
    
    
    public function GetPermissionsGroupValues()
    {
        if(!isset($this->defaultModules)){
        	
            return array();
        }
        
        
        foreach($this->defaultModules as $module => $label){
        	if(isset($this->p_group)){
		       	$sql = "SELECT  * FROM group_permissions WHERE store_id = {$this->store_id} AND p_group = '{$this->p_group}' AND module LIKE '{$module}'";
		        $query = $this->db->query($sql);
		        $permission = $query->fetchAll(PDO::FETCH_ASSOC);
		        
		        
        	}
        	
        	if(!isset($permission)){
        		
        		$resPermissions = array(
        				'p_view' => isset($permission['p_view']) && !empty($permission['p_view']) ? $permission['p_view'] : 'T',
        				'p_create' => isset($permission['p_create']) && !empty($permission['p_create']) ? $permission['p_create'] : 'T',
        				'p_update' => isset($permission['p_update']) && !empty($permission['p_update']) ? $permission['p_update'] : 'T',
        				'p_delete' => isset($permission['p_delete']) && !empty($permission['p_delete']) ? $permission['p_delete'] : 'T'
        				);
        		
        	}else{
        		$resPermissions = $permission[0];
        	}
        	
	        $this->permissionsGroupValues[$module] = $resPermissions;
	        
	        
	        
	        
        
        }
//         pre($this->permissionsGroupValues);die;
        return $this->permissionsGroupValues;
        
    }
    
    
} 