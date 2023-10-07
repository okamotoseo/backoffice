<?php
class ArrayValue implements JsonSerializable {
    public function __construct(array $array) {
        $this->array = $array;
    }
    
    public function jsonSerialize() {
        return $this->array;
    }
}
class Rest {
    
    /**
     * @version 1.1.0
     */
    const VERSION  = "1.0";
    
    private $app_key;
    
    private $token;
    
    private $account;
    
    private $environment;
    
    protected $api_host;
    
    /**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Content-Type: application/json"
        ],
        CURLOPT_TIMEOUT => 30
    );
    

    public function __construct( $app_key, $token,  $account, $environment) {
        
        $this->app_key = $app_key;
        
        $this->token = $token;
        
        $this->account = $account;
        
        $this->environment = $environment;
        
        $this->api_host = "https://{$account}.vtexcommercestable.com.br/api/";
    }
    
    
    /**
     * Execute a POST Request
     *
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function post($path, $body = null, $params = array()) {
        $body = json_encode($body, JSON_FORCE_OBJECT);
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body
        );
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }
    
    
    /**
     * Execute a GET Request
     *
     * @param string $path
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    public function get($path, $params = null, $assoc = false) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "GET"
        );
        $exec = $this->execute($path, $opts, $params, $assoc);
        return $exec;
    }
    
    /**
     * Execute a PUT Request
     *
     * @param string $path
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function put($path, $body = null, $params = array()) {
        
        $body = json_encode($body, JSON_FORCE_OBJECT);
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $body
        );
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }
    
    /**
     * Execute a DELETE Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function delete($path, $params) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "DELETE"
        );
        
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }
    
    /**
     * Execute a OPTION Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function options($path, $params = null) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
        );
        
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }
    
    
    
    /**
     * Check and construct an real URL to make request
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function make_path($path, $params = array()) {
        if (!preg_match("/^http/", $path)) {
            if (!preg_match("/^\//", $path)) {
                $path = '/'.$path;
            }
            $uri = $this->API_ROOT_URL.$path;
        } else {
            $uri = $path;
        }
        
        if(!empty($params)) {
            $paramsJoined = array();
            
            foreach($params as $param => $value) {
                $paramsJoined[] = "$param=$value";
            }
            $params = '?'.implode('&', $paramsJoined);
            $uri = $uri.$params;
        }
        
        return $uri;
    }
    
    /**
     * Execute all requests and returns the json body and headers
     *
     * @param string $path
     * @param array $opts
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    public function execute($path, $opts = array(), $params = null, $assoc = true) {
        if(isset($params[0])){
          $url = $this->api_host."{$path}/?".http_build_query($params);
        }else{
            $url = $this->api_host."{$path}";
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, self::$CURL_OPTS);
        curl_setopt_array($ch,  array(
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json",
                "X-VTEX-API-AppKey: {$this->app_key}",
                "X-VTEX-API-AppToken: {$this->token}"
                ]
            ));

        if(!empty($opts))
            curl_setopt_array($ch, $opts);
        
        $return["body"] = json_decode(curl_exec($ch), $assoc);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        return $return;
    }
}
