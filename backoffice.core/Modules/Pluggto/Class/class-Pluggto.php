<?php 


class Pluggto {
    
    public $db;
    
    public $store_id;
    
    protected $access_token;
    
    protected $expires_in;
    
    protected $refresh_token;
    
    protected $api_host;
    
    protected $client_id;
    
    protected $client_secret;
    
    protected $api_user;
    
    protected $api_secret;
    
    protected $token_type;
    
    protected $type = 'PLUGIN';
    
    protected  $rest;
    
    
    
    public function __construct( $db = false, $storeId = null ) {

        $this->db = $db;
        
        $this->store_id = $storeId;
        
        if(isset($this->db) AND isset($this->store_id)){
            
            $this->Load();
            
            $this->VerifyToken();
            
        }
        
    }
    
    
    public function VerifyToken(){
        
            
        try {
            
            if(!isset($this->refresh_token)){
                
                $refresh = $this->getAccessToken();
                
            }else{
                
                if($this->expires_in - 3600 < time()){
                    
                    $refresh = $this->getAccessTokenByRefreshToken($this->refresh_token);
                    
                }else{
                    return;
                }
                
            }
            
            if(isset($refresh->refresh_token)) {
                
                $this->expires_in = time() + $refresh->expires_in;
                
                $data = array(
                    'access_token' => $refresh->access_token,
                    'refresh_token' => $refresh->refresh_token,
                    'token_type' => $refresh->token_type,
                    'expires_in' => $this->expires_in
                );
                
                $query = $this->db->update('module_pluggto', 'store_id', $this->store_id, $data);
                
                $this->Load();
                
                            
            }
            
        } catch (Exception $e) {
            
            echo $error =  "Exception: ",  $e->getMessage(), "\n";
            
        }
        
    }
    
    public function getAccessToken($code=null, $returnRefreshToken=true)
    {
        $url = $this->api_host.'/oauth/token';
        
        if ($this->type == 'APP')
        {
            $params = [
                "grant_type"    => "authorization_code",
                "client_id"     => $this->client_id,
                "client_secret" => $this->client_secret,
                "code"          => $code
            ];
            
            $response = $this->sendRequest("post", $url, $params, "auth");
            
            if (!isset($response->access_token))
                return false;
        }
        
        if ($this->type == 'PLUGIN')
        {
            $params = [
                "grant_type"    => "password",
                "client_id"     => $this->client_id,
                "client_secret" => $this->client_secret,
                "username"      => $this->api_user,
                "password"      => $this->api_secret
            ];
            
            $response = $this->sendRequest("post", $url, $params, "auth");
            
            if (!isset($response->access_token))
                return false;
        }
        
        if ($returnRefreshToken) {
            return $response;
        }
        return $response->access_token;
    }
    
    public function getAccessTokenByRefreshToken($refreshToken, $returnAllTokens = true)
    {
        $url = $this->api_host.'/oauth/token';
        
        $params = [
            "grant_type"    => "refresh_token",
            "client_id"     => $this->client_id,
            "client_secret" => $this->client_secret,
            "refresh_token" => $refreshToken
        ];
        
        $response = $this->sendRequest("post", $url, $params, "auth");
        
        if (!isset($response->access_token))
            return false;
            
            if($returnAllTokens)
            {
                return $response;
            }
            
            return $response->access_token;
    }
    
    public function sendRequest($method, $url, $params=[], $type="") {
        $ch = curl_init();
        
        if (strtolower ( $method ) == "get")  {
            $i =0;
            
            foreach ($params as $key => $value) {
                
                if ($i == 0) {
                    $value = "?".$key."=".$value;
                } else {
                    $value = "&".$key."=".$value;
                }
                
                $i++;
                
                $url = $url . $value;
            }
            
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url
            ));
            
        } elseif (strtolower ( $method ) == "post") {
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            
            $data_string = json_encode($params);
            
            if ($type == "auth") {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'))
                ));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
                    );
            }
        } elseif (strtolower ( $method ) == "put") {
            
            $data_string = json_encode($params);
            
            curl_setopt($ch, CURLOPT_URL, $url);
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
                );
            
        } else if (strtolower ( $method ) == "delete") {
            curl_setopt($ch, CURLOPT_URL, $url);
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            )
                );
        }
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 1000);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $result = curl_exec($ch);
        
        // get the curl status
        $status = curl_getinfo($ch);
        
        if (empty($status['http_code'])) {
            if ($this->tries < 10) {
                $this->tries++;
                return $this->sendRequest($method, $url, $params, $type);
            }
        }
        
        return json_decode($result);
    }
    
    public function Load()
    {
        
        if(!empty($this->store_id) ){
            $query = $this->db->query('SELECT * FROM module_pluggto WHERE store_id = ?',array($this->store_id ) );
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($fetch)){
                foreach($fetch as $key => $value)
                {
                    $column_name = str_replace('-','_',$key);
                    $this->{$column_name} = $value;
                }
            }
        }
        
        return;
        
    }

    
}