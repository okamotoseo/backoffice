<?php 


class Soap{
    
    public $db;
    
    public $id;
    
    public $store_id;
    
    public $wsdl;
    
    private $user;
    
    private $password;
    
    private $session_id;
    
    public $soapClient;
    
    
    
    public function __construct( $db = false, $storeId = null ) {
        
        $this->db = $db;
        
        $this->store_id = $storeId;
        
        $this->Load();
        
        $this->soapClient = new SoapClient('http://fanlux-dev.onbi.com.br/api/v2_soap?wsdl', array(
            'trace' => 1,
            'exceptions' => 0,
            'cache_wsdl' => 0
            
        ));
        
        
        $this->verifySession();
        
        
    }
    
    public function verifySession(){
        
        
        $storeInfo =  $this->soapClient->storeInfo(array('sessionId' => $this->session_id, 'storeId' => '1'));
        
        if(!isset($storeInfo->result)){
            
            $this->Login();
            
        }
       
        return;
        
    }

    
    public function Login()
    {
        
        $sessionId = $this->soapClient->login(array('username' => $this->user, 'apiKey' => $this->password));
        
        if(!empty($sessionId->result)){
            $this->session_id = $sessionId->result;
            
            $query = $this->db->update('module_onbi', 'store_id', $this->store_id, array(
                'session_id' => $this->session_id
            ));
        }
        
        return $sessionId;
        
        
        
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
    
    
    
    
    public function getOrderInfo(){
        
        $response = $this->soapClient->salesOrderInfo(array('sessionId' => $this->session_id, 'orderIncrementId' => '100000001'));
        
        return $response;
        
    }
    
    
    
    
    
    
    
    
}