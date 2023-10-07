<?php 


class ShipmentsModel extends MainModel
{
    
    
    
    public $api;
    
    public $records = 20;
    

    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
        
            $moduleConfig = getModuleConfig($this->db, $this->store_id, 9);
        
            /** @var \SkyHub\Api $api */
            $this->api = new SkyHub\Api($moduleConfig['email'], $moduleConfig['api_key'], $moduleConfig['account_key'], $moduleConfig['base_uri']);
        
        }
    }
    
   
    public function list_plps(){
        /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
        $entityInterface = $this->api->plp()->entityInterface();
        /**
         * GET A LIST OF PLP's.
         * @var SkyHub\Api\Handler\Response\HandlerInterface $response
         */
        $response = $entityInterface->plps();
        
        if( method_exists( $response, 'body' ) ){
            
            $body = $response->body();
            
            $bodyJson = json_decode($body);
            
            return $bodyJson->plp;
            
            
        }else{
            echo "error|".$response->message();
        }
    }
    
    public function list_order_ready_to_group_plp(){
        
        /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
        $entityInterface = $this->api->plp()->entityInterface();
        
        /**
         * GET A LIST OF ORDERS READY TO BE GROUPED IN A PLP.
         * @var SkyHub\Api\Handler\Response\HandlerInterface $response
         */
        $response = $entityInterface->ordersReadyToGroup();
        if( method_exists( $response, 'body' ) ){
            
            $body = $response->body();
            
            $bodyJson = json_decode($body);
            return $bodyJson->orders;
            
        }else{
            
            echo "error|".$response->message();
        }
    }
    
    public function list_order_ready_to_collect(){
        
        /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
        $entityInterface = $this->api->plp()->entityInterface();
        
        /**
         * GET A LIST OF ORDERS READY TO BE GROUPED IN A PLP.
         * @var SkyHub\Api\Handler\Response\HandlerInterface $response
         */
        $response = $entityInterface->ordersReadyToCollect();
        
        
        if( method_exists( $response, 'body' ) ){
            
            $body = $response->body();
            
            $bodyJson = json_decode($body);
            return $bodyJson->orders;
            
        }else{
            
            echo "error|".$response->message();
        }
    }
    
    

    

    
}
?>