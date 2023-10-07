<?php 


class Rest extends Magento2{
    
    public $db;
    
    public $store_id;
    
    public $Access_Token = null;
    
    public $Access_Token_Secret = null;
    
    public $Consumer_Key;
    
    public $Consumer_Secret;
    
    public $api_host;
    
    public $Store;
    
    public $username;
    
    public $password;
    
    public $token;
    
    public $date_expiration_token;
    
    public $magento2;
    
    public $storeView = 'default';
    
    // Consumer_Key u89w9s0k7nytv5pwg1e64dujyv4qbbuc
    // Consumer_Secret zn3zpnwpzx7dkosibqdzdyjyxylw4gvr
    // Access_Token  rtiml1skkdtp6kgihj76q02g0k90mf1e
    // Access_Token_Secret xoitxvmnk07zhnuiuzjzq1fmrbmge8mk
    
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
		if(date("Y-m-d H:i:s") >= $this->date_expiration_token OR empty($this->token)){
    			
        	$this->magento2 = new Magento2($this->api_host, $this->username, $this->password);
	        $response = $this->magento2->refreshAccessToken();
	                
        	if($response['httpCode'] == 200) {
                	
            	$this->token = $response['body'];
            	
               	if(!empty($this->token)){
               		$query = $this->db->update('module_mg2', 'store_id', $this->store_id, array(
              				'token' => $this->token,
              				'date_expiration_token' => date("Y-m-d H:i:s", strtotime("+4 hour", strtotime("now")))
               		));
               	}
	                   
	    	}
	    	
    	}
        
    }
    
    
    public function Load()
    {
        
        if(!empty($this->store_id) ){
            
            $query = $this->db->query('SELECT * FROM module_mg2 WHERE store_id = ?',
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