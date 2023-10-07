<?php 


class Vtex extends Rest{
    
    public $db;
    
    public $store_id;
    
    private $app_key;
    
    private $token;
    
    private $account;
    
    private $environment;
    
    public $rest;
    
    
    
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
        
        
        try {
            
            $this->rest = new Rest($this->app_key, $this->token, $this->account, $this->environment);
            
        } catch (Exception $e) {
            echo $error =  "Exception: ",  $e->getMessage(), "\n";
//             notifyAdmin($error);

            print_r($error);
        }
        
        return $this->rest;
        
    }
    
    
    public function Load()
    {
        
        if(!empty($this->store_id) ){
            
            $query = $this->db->query('SELECT * FROM module_vtex WHERE store_id = ?',
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