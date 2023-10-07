<?php 


class PlpViewModel extends MainModel
{
    
    
    public $plpId;
    
    public $api;
    
    public $apiPdf;
    
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
        
            /** @var SkyHub\Api\ $servicePdf */
            $servicePdf = new SkyHub\Api\Service\ServicePdf(null);
            /** @var \SkyHub\Api $api2 */
            $this->apiPdf = new SkyHub\Api($moduleConfig['email'], $moduleConfig['api_key'], null, null, $servicePdf);
            
            $this->plpId = end($this->parametros);
        }
    }
    
    
    public function PlpView(){
        
        
        $target_dir =  UP_ABSPATH."/store_id_{$this->store_id}/plp/";
        $urlShow = HOME_URI."/Views/_uploads/store_id_{$this->store_id}/plp/";
        
        
        
        if (!file_exists($target_dir)) {
            @mkdir($target_dir);
        }
        
        $target_file = $target_dir .  $this->plpId . ".pdf";
        $urlShow = $urlShow .  $this->plpId . ".pdf";
        
        if (!file_exists($target_file)) {
            
            
            /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $entityInterface */
            $entityInterfacePdf = $this->apiPdf->plp()->entityInterface();
            
            /**
             * GET PLP PDF.
             * @var SkyHub\Api\Handler\Response\HandlerInterface $response
             */
            $entityInterfacePdf->setId("{$this->plpId}");
            $response = $entityInterfacePdf->viewFile();
            
            if( method_exists( $response, 'body' ) ){
                
                $body = $response->body();
                file_put_contents("{$target_file}", $body);
                
                return $urlShow;
                
                
            }else{
                return $response->message();
            }
        
        }else{
            
            return $urlShow;
            
        }
    
    }
    

    

    
}
?>