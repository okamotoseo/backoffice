<?php

class Tray {

	/**
	 * @version 1.1.0
	 */
    const VERSION  = "1.1.0";

    
    /**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );

    private  $consumer_key = '0414c3cd359f374ca8745ba8ecf28cf23a1a30951d05e90cdb50a79bfcfaf18c';
    
    private  $consumer_secret = '2c32ed3eb51441f8b38ddc681a3c8aa1f0cde20139082103164fe96aaf809b17';
    
    protected $api_host;
    
    protected $access_token;
    
    private $refresh_token;
    

    /**
     * Constructor method. Set all variables to connect in Meli
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $access_token
     * @param string $refresh_token
     */
    public function __construct( $api_host, $access_token = null, $refresh_token = null) {
        $this->api_host = $api_host;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
    }


    /**
     * Executes a POST Request to authorize the application and take
     * an AccessToken.
     * 
     * @param string $code
     * @param string $redirect_uri
     * 
     */
    public function authorize($code) {
        $params["consumer_key"] = $this->consumer_key;
        $params["consumer_secret"] = $this->consumer_secret;
        $params["code"] = $code;
        $url = $this->api_host."/auth/";
        
        ob_start();
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_exec($ch);
        
        // JSON de retorno
        $result['body'] = json_decode(ob_get_contents());
        $result['HttpCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        ob_end_clean();
        curl_close($ch);
        
//         if($code == "201"){
//             //Tratamento dos dados de resposta da consulta.
//         }else{
//             //Tratamento das mensagens de erro
//         }
        
        return $result;
    }
    
    /**
     * Execute a POST Request to create a new AccessToken from a existent refresh_token
     *
     * @return string|mixed
     */
    public function refreshAccessToken() {
        
        if($this->refresh_token) {
            
          
            $params["refresh_token"] =  $this->refresh_token;
            
            
            $url = $this->api_host."/auth?".http_build_query($params);
            ob_start();
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_exec($ch);
            
            // JSON de retorno
            $result['body'] = json_decode(ob_get_contents());
            $result['HttpCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            ob_end_clean();
            curl_close($ch);
            
            return $result;
            
        } else {
            $result = array(
                'error' => 'Offline-Access is not allowed.',
                'httpCode'  => null
            );
            return $result;
        }
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
    public function execute($path, $opts = array(), $params = array(), $assoc = true) {
        $url = $this->api_host."{$path}/?".http_build_query($params);
        $ch = curl_init($url);
        curl_setopt_array($ch, self::$CURL_OPTS);
        
        if(!empty($opts))
            curl_setopt_array($ch, $opts);
            
            $return["body"] = json_decode(curl_exec($ch), $assoc);
            $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            curl_close($ch);
            
            return $return;
    }
    
    
    /**
     * Execute a POST Request
     *
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function post($path, $body = null, $params = array()) {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($body)),
            CURLOPT_POST => true,
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
        $exec = $this->execute($path, null, $params, $assoc);
        
        return $exec;
    }
    
    
    
   

    

    /**
     * Execute a POST Request 
     * XML
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function postXml($path, $body = null, $params = array()) {
//         $body = json_decode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/xml'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body
        );
        
        $exec = $this->execute($path, $opts, $params);
        
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
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
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
}
