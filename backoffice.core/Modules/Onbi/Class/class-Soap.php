<?php 


class Soap{
    
    public $db;
    
    public $id;
    
    public $store_id;
    
    public $wsdl;
    
    public $user;
    
    public $password;
    
    public $session_id;
    
    public $soapClient;
    
    
    
    public function __construct( $db = false, $storeId = null ) {

        $this->db = $db;
        
        $this->store_id = $storeId;
        
        $this->Load();
        $this->getWsdlStore();
        
        $this->soapClient = new SoapClient("{$this->wsdl}/api/v2_soap?wsdl", array(
            'keep_alive' => true,
            'trace' => 1,
            'exceptions' => 0,
            'cache_wsdl' => 0
            
        ));
        $this->verifySession();
        
        
    }
    
    public function getWsdlStore()
    {
        
        switch($this->store_id){
            case "4": $this->wsdl = "http://www.fanlux.com.br";  break;
            case "5": $this->wsdl = "https://www.miromi.com.br";  break;
        }
        
        return $this->wsdl;
        
        
    }
    
    public function verifySession()
    {
        
        
        if(!empty($this->session_id)){
            
            $storeInfo =  $this->soapClient->storeInfo($this->session_id, 1);
           
            if(!isset($storeInfo->store_id)){
                $this->Login();
                
            }
            
        }else{
            
            $this->Login();
        }
        
        return;
        
    }

    
    public function Login()
    {
        $this->session_id = $this->soapClient->login($this->user,  $this->password);
        if(!empty($this->session_id)){
            
            $query = $this->db->update('module_onbi', 'store_id', $this->store_id, array(
                'session_id' => $this->session_id
            ));
            
        }

        return $this->session_id;
        
        
        
    }
    
    
    public function Load()
    {
        
        if(!empty($this->store_id) ){
            
            $query = $this->db->query('SELECT * FROM module_onbi WHERE store_id = ?',
                array($this->store_id ) );
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($fetch)){
                foreach($fetch as $key => $value)
                {
                    $column_name = str_replace('-','_',$key);
                    $this->{$column_name} = $value;
                }
            }else{
                return;
            }
            
        }else{
            
            return;
            
        }
        
        
    }
    
    
    
    
    
//     public function getOrderInfo(){
        
//         $response = $this->soapClient->salesOrderInfo(array('sessionId' => $this->session_id, 'orderIncrementId' => '100000001'));
        
//         return $response;
        
//     }
    
    
    
    
}