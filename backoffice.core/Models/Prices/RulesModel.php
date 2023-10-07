<?php 

class RulesModel extends MainModel
{

	/**
	 * @var string
	 * Class Unique ID
	 */
    public $id;
    
	public $store_id;
	
	public $marketplace;
	
    public $condition;
    
    public $operator;
    
    public $value_test;
    
    public $rule;
    
    public $fixed_rate = 0;
    
    public $percentage_rate = 0;
    
    public $records = 50;
	


	
    public function __construct( $db = false, $controller = null ) {
        
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
                    //                     $val = str_replace("_", " ", $val);
                    $this->{$param} = $val;
                    
                }
            }
            
            return true;
            
        }else{
            
            $this->pagina_atual = 1;
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
        }
        
        if(isset ( $_POST['save'] ) ){
            
            foreach ( $_POST as $property => $value ) {
                
                if(!empty($value)){
                    
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                    
                }else{
                    
                        
                    $arr = array('marketplace', 'condition', 'value_test', 'rule' );
                    
                    if( in_array($property, $arr) ){
                        
                        $this->field_error[$property] = "has-error";
                        
                        $this->form_msg = "<div class='alert alert-danger alert-dismissable'>There are empty field. Data has not been sent.</div>";
                    }
                        
                }
            }
            
            if(!empty($this->form_msg)){
                
                return false;
            }
            return true;
            
        } else {
            
            if ( in_array('edit', $this->parametros )) {
                
                $key = array_search('edit', $this->parametros);
                
                $id = get_next($this->parametros, $key);
                $this->id  = is_numeric($id) ? $id :  '';
                
                if(!empty($this->id)){
                    
                    $this->Load();
                    
                }
                
            }
            
            if ( in_array('del', $this->parametros )) {
                
                $key = array_search('del', $this->parametros);
                
                $id = get_next($this->parametros, $key);
                $this->id  = is_numeric($id) ? $id :  '';
                
                if(!empty($this->id)){
                    
                    $this->Delete();
                    
                }
            }
            
            return;
            
        }
        
    }
	
	public function Save(){
	    
	    if ( ! empty( $this->id ) ) {
	        
	        $query = $this->db->update('price_rules', 'id', $this->id, array(
	            'marketplace' => $this->marketplace,
	            'condition' => $this->condition,
	            'operator' => $this->operator,
	            'value_test' =>  number_format(str_replace(',', '.', $this->value_test), 2, ",", "."),
	            'rule' => $this->rule,
	            'fixed_rate' => number_format(str_replace(',', '.', $this->fixed_rate), 2, ",", "."),
	            'percentage_rate' => $this->percentage_rate
	            
	        ));
	        
	        if ( ! $query ) {
	            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	            
	            return;
	        } else {
	            
	            $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
	            $this->id = null;
	            return;
	        }
	    } else {

	           $sqlVerify = "SELECT id FROM price_rules WHERE store_id = {$this->store_id} AND marketplace LIKE '{$this->marketplace}'
                AND `condition` LIKE '{$this->condition}' AND operator LIKE '{$this->operator}'";
	           $queryVerify = $this->db->query($sqlVerify);
	           
	           $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	           
	           if(!isset($verify['id'])){
	        
    	            $query = $this->db->insert('price_rules', array(
    	                'store_id' => $this->store_id,
    	                'marketplace' => $this->marketplace,
    	                'condition' => $this->condition,
    	                'operator' => $this->operator,
    	                'value_test' => number_format(str_replace(',', '.', $this->value_test), 2, ".", ""),
    	                'rule' => $this->rule,
    	                'fixed_rate' => number_format(str_replace(',', '.', $this->fixed_rate), 2, ".", ""),
    	                'percentage_rate' => $this->percentage_rate
    	            )
    	                );
    	            
    	            if ( ! $query ) {
    	                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    	                return;
    	            } else {
    	                
    	                $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
    	                return;
    	            }
	            
	           }else{
	               
	               $this->form_msg = '<div class="alert alert-danger alert-dismissable">Já existe uma regra semelhante cadastrada!</div>';
	               return;
	               
	           }
	            
	        
	    }
	    
	    
	}
	
	public function ListPriceRules()
	{
	    $query = $this->db->query('SELECT * FROM `price_rules`  WHERE `store_id` = ? ORDER BY id DESC',
	        array($this->store_id)
	        );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	public function Load()
	{
	    
	    if(empty($this->id)){
	        
	        return array();
	        
	    }
        $query = $this->db->query('SELECT * FROM price_rules WHERE  store_id = ? AND `id`= ?', array($this->store_id, $this->id ) );
        
        foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
        {
            $column_name = str_replace('-','_',$key);
            $this->{$column_name} = $value;
        }
	        
	    
	}
	
	public function Delete()
	{
	    
	    if(empty($this->id)){
	        
	        return array();
	        
	    }
	    
        $query = $this->db->query('DELETE FROM price_rules WHERE store_id = ? AND `id`= ?', array($this->store_id, $this->id ) );
        
        if ( ! $query ) {
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
        }
        
        unset($this->id);
	    
	    
	}
	
	
}

?>