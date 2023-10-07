<?php 


class Rest extends Tray{
    
    public $db;
    
    public $store_id;
    
    public $access_token = null;
    
    public $refresh_token = null;
    
    public $code = null;
    
    public $date_expiration_access_token;
    
    public $date_expiration_refresh_token;
    
    public $date_activated;
    
    public $api_host;
    
    public $store;
    
    protected $uri = "https://backoffice.sysplace.com.br/Modules/Configuration/Tray/Setup";
    
    public $url;
    
    public $tax;
    
    public $tray;
    
    
    
    public function __construct( $db = false, $storeId = null ) {

        $this->db = $db;
        
        $this->store_id = $storeId;
        
        if(isset($this->db) AND isset($this->store_id)){
            
            $this->Load();
            
            $this->verifyToken();
        }
        
    }
    
    
    public function VerifyToken()
    {
        
       
        $this->tray = new Tray( $this->api_host, $this->access_token, $this->refresh_token);
        if($this->date_expiration_access_token < date("Y-m-d H:i:s") ) {
            try {
               
                $refresh = $this->tray->refreshAccessToken();
                if(isset($refresh['body']->access_token)) {
                    
                    $query = $this->db->update('module_tray', 'store_id', $this->store_id, array(
                        'access_token' => $refresh['body']->access_token,
                        'refresh_token' => $refresh['body']->refresh_token,
                        'date_expiration_access_token' => $refresh['body']->date_expiration_access_token,
                        'date_expiration_refresh_token' => $refresh['body']->date_expiration_refresh_token,
                        'date_activated' => $refresh['body']->date_activated
                    ));
                    $this->access_token = $refresh['body']->access_token;
                    $this->refresh_token = $refresh['body']->refresh_token;
                    $this->date_expiration_access_token = $refresh['body']->date_expiration_access_token;
                }
                
            } catch (Exception $e) {
                echo $error =  "Exception: ",  $e->getMessage(), "\n";
            }
        }
        
    }
    
    
    public function Load()
    {
        
        if(!empty($this->store_id) ){
            
            $query = $this->db->query('SELECT * FROM module_tray WHERE store_id = ?',
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
    

    
}