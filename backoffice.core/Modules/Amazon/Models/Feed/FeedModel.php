<?php 

class FeedModel extends MainModel
{

	public $id;
    
    public $store_id;
    
    public $FeedSubmissionId;
    
    public $FeedType;
    
    public $SubmittedDate;
    
    public $FeedProcessingStatus;
    
    public $StartedProcessingDate;
    
    public $CompletedProcessingDate;
    
    public $RequestId;
    
    public $feed;
    
    public $records = '50';
    




	
    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
          
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
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
	        
	        $this->pagina_atual = 1;
	        $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
	    }
	    
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
	        
	    } else {
	        
	        if ( in_array('edit', $this->parametros )) {
	            
	            $this->Load();
	            
	        }
	        
	        if ( in_array('del', $this->parametros )) {
	            
	            $key = array_search('del', $this->parametros);
	            
	            $this->id = get_next($this->parametros, $key);
	            
	            $this->Delete();
	            
	        }
	        
	        return;
	        
	    }
	    
	}
    
	public function TotalProducts(){
		 
		 
		$sql = "SELECT count(*) as total FROM module_amazon_feed WHERE store_id = {$this->store_id}";
		 
		$query = $this->db->query( $sql);
		$total =  $query->fetch(PDO::FETCH_ASSOC);
		return $total['total'];
		 
	}
    
	public function ListFeedSubmitted()
	{
	    $query = $this->db->query("SELECT * FROM `module_amazon_feed`  WHERE `store_id` = ? ORDER BY id DESC  
	    	LIMIT {$this->linha_inicial}, {$this->records}", array($this->store_id) );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	
	
}

?>