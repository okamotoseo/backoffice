<?php 

class LogModel 
{

	/**
	 * @var string
	 * Class Unique ID
	 */
	public $store_id;
	
	/**
	 * @var string
	 */
	public $key;

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $setion;

	/**
	 * @var string
	 */
	public $information;

	/**
	 * @var string
	 */
	public $created;
	
	
	public $records = 50;
	
	


	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    if(isset($this->controller)){
	        
	        $this->parametros = $this->controller->parametros;
	        
	        $this->userdata = $this->controller->userdata;
	        
	        $this->store_id =  $this->controller->userdata['store_id'];
	        
	    }
	    
	    if(!defined('QTDE_REGISTROS')){
	        
	        define('QTDE_REGISTROS', 50);
	        
	    }
	    
	    
	    
	}
	
	
	public function ValidateForm() {
		

	    if(in_array('records', $this->parametros )){
	        $records = get_next($this->parametros, array_search('records', $this->parametros));
	        $this->records = isset($records) ? $records : QTDE_REGISTROS ;
	    }
	    
	    if(in_array('Page', $this->parametros )){
	        
	        $this->pagina_atual =  get_next($this->parametros, array_search('Page', $this->parametros));
	        $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
	        
	        foreach($this->parametros as $key => $param){
	            if(property_exists($this,$param)){
	                $val = get_next($this->parametros, $key);
	                $val = str_replace("_x_", "%", $val);
	                $val = str_replace("_", " ", $val);
	                $this->{$param} = $val;
	                
	            }
	        }
	        
	        return true;
	        
	    }else{
	        // 	        echo 123;die;
	        $this->pagina_atual = 1;
	        $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
	    }
	    
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        
	        foreach ( $_POST as $property => $value ) {
	            
	            if(property_exists($this,$property)){
    	                
    	            if( !empty( $value ) ){
    	            	
    	                $this->{$property} = $value;
    	                
    	            }else{
    	                $required = array();
    	                
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
	
	
	public function TotalLogs(){
	        
	    $sql = "SELECT count(*) as total FROM ml_log WHERE store_id = {$this->store_id} ";
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	public function ListLogs()
	{
	    $query = $this->db->query("SELECT * FROM `ml_log`  WHERE `store_id` = ? ORDER BY created DESC
            LIMIT {$this->linha_inicial}, " . $this->records.";",
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
}

?>