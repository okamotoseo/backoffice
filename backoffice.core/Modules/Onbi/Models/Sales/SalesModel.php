<?php 

class SalesModel extends Soap
{

    public $order_increment_id;
    
    public $filters = array(
        "status" => "processing"
    );
    //processing, pending_payment, pending
    
    public $complexFilter;
    
	
	


	
	public function __construct($db = false,  $controller = null, $storeId = null)
	{
	    $this->db = $db;
	    
	    $this->store_id = $storeId;
	    
	    $this->controller = $controller;
	    
	    if(isset($this->controller)){
	        
    	    $this->parametros = $this->controller->parametros;
    	    
    	    $this->userdata = $this->controller->userdata;
    	    
    	    $this->store_id = $this->controller->userdata['store_id'];
    	    
	    }
	    if(isset($this->store_id)){
	    
	        parent::__construct($this->db, $this->store_id);
	    
	    }
	    
	}
	
	
	public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
            	if(!empty($value)){
	                if(property_exists($this,$property)){
	                   $this->{$property} = $value;
	                }
            	}
                
            }
            
            return true;
            
        } else {
            
        	
            return;
            
        }
        
	    
	}
	
    public function setStatus($status){

        switch($status){
            case "onbips_aguardando_pagamento": $this->status = 'pending_payment'; break;
            case "onbips_em_analise": $this->status = 'processing';break;
            case "onbips_paga": $this->status = 'paid'; break;
            case "pending": $this->status = 'pending'; break;
            case "paid": $this->status = 'paid'; break;
            case "canceled": $this->status = 'canceled'; break;
            case "approved": $this->status = 'approved'; break;
            case "complete": $this->status = 'shipped'; break;
            default: $this->status = $status ; break;
        }
        return $this->status;
    }
   
	public function getFilterSales(){
	    
	    $this->complexFilter = array();
	    
	    foreach($this->filters as $key => $value){
	        
	        switch($key){
	            
	            case "status" :
	                
// 	                $this->complexFilter['filter'][] = array(
//     	                'key' => 'status',
//     	                'value' => 'paid'
// 	                );
	                
// 	                $this->complexFilter['filter'][] = array(
// 	                    'key' => 'status',
// 	                    'value' => 'canceled'
// 	                );
	               
	                $this->complexFilter['complex_filter'][] = array(
	                    'key' => 'created_at',
	                    'value' => array('key' => 'gt', 'value' => date("Y-m-d", strtotime("-5 day", strtotime("now")))." 00:00:00")
	                );
	                
	                break;
	                
	            case "order_id" :
	                
	                $this->complexFilter['complex_filter'][] = array(
    	                'key' => $key,
    	                'value' => array('key' => 'in', 'value' => $value)
	                );
	                
	                break;
	                
	            case "protect_code" :
	                
	                $this->complexFilter['complex_filter'][] = array(
    	                'key' => $key,
    	                'value' => array('key' => 'eq', 'value' => $value)
	                );
	                
	                break;
	                
	                
	                
	        }
	        
	        
	}
	
	return $this->complexFilter;
	
	}
	
	
	public function salesOrderList(){
	    
	    
	    $this->getFilterSales();
	    pre( $this->complexFilter) ;
	    $response = $this->soapClient->salesOrderList($this->session_id, $this->complexFilter);
	    
	    return $response;
	    
	}
    
    
	public function salesOrderAddComment(){
        
	    if(!isset($this->order_increment_id)){
            
            return array();
        }
        
        $response = $this->soapClient->salesOrderAddComment($this->session_id, $this->order_increment_id, 'processing');
        
        return $response;
        
    }
    
    public function salesOrderInfo(){
        
        if(!isset($this->order_increment_id)){
            
            return array();
        }
        
        $response = $this->soapClient->salesOrderInfo($this->session_id, $this->order_increment_id);
        
        return $response;
        
    }
    /************************************************************************************************/
    /************************************** Custom **************************************************/
    /************************************************************************************************/
    
  
}
    
?>