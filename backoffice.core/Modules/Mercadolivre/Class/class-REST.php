<?php 


class Rest extends Meli{
    
    public $db;
    
    public $store_id;
    
    public $app_id;
    
    public $secret_key;
    
    public $access_token;
    
    public $expires_in;
    
    public $refresh_token;
    
    public $code;
    
    public $nickname;
    
    public $seller_id;
    
    public $scope;
    
    public $tax;
    
    public $meli;
    
    
    
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
        
        
        $this->meli = new Meli($this->app_id, $this->secret_key, $this->access_token, $this->refresh_token);
        
        
        if($this->expires_in < time()) {
            try {
                $refresh = $this->meli->refreshAccessToken();
                if($refresh['body']->access_token) {
                    $this->expires_in = time() + $refresh['body']->expires_in;
                    
                    $sql = "UPDATE `module_mercadolivre` SET `access_token`='{$refresh['body']->access_token}',`expires_in`='{$this->expires_in}',
			`refresh_token`='{$refresh['body']->refresh_token}' WHERE store_id = {$this->store_id}";
                    $this->db->query($sql);
                    
                    $this->access_token = $refresh['body']->access_token;
                    $this->refresh_token = $refresh['body']->refresh_token;
                    $this->expires_in = $refresh['body']->expires_in;
                    
                }else{
                    notifyAdmin($refresh['body']->message);
                }
            } catch (Exception $e) {
                echo $error =  "Exception: ",  $e->getMessage(), "\n";
                notifyAdmin($error);
            }
        }
        
    }
    
    
    public function Load()
    {
        
        if(!empty($this->store_id) ){
            
            $query = $this->db->query('SELECT * FROM module_mercadolivre WHERE store_id = ?',
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