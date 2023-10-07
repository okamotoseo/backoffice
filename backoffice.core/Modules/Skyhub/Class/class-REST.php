<?php

class REST {

	/**
	 * @version 1.1.0
	 */
    const VERSION  = "1.1.0";
    
    public $email;
    
    public $apiKey;
    
    public $xAccountKey;
    
    public $base_uri;
    
    /**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );

    public function __construct($email, $apiKey, $xAccountKey, $baseUri) {
        $this->email   = $email;
        $this->apiKey  = $apiKey;
        $this->xAccountKey = $xAccountKey;
        $this->base_uri = $baseUri;
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
    public function execute($path, $opts = array(), $params = array(), $assoc = false) {
        $url = $this->base_uri."{$path}/?".http_build_query($params);
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
    public function post($path, $params = array()) {
        $body = json_encode($params);
        $opts = array(
        	CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
            	"X-User-Email: {$this->email}",
        		"X-Api-Key: {$this->apiKey}",
        		"X-Accountmanager-Key:{$this->xAccountKey}",
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
    	
    	$opts = array(
    			CURLOPT_CUSTOMREQUEST => "GET",
    			CURLOPT_HTTPHEADER => array(
    					"X-User-Email: {$this->email}",
    					"X-Api-Key: {$this->apiKey}",
    					"X-Accountmanager-Key:{$this->xAccountKey}",
    					'Content-Type: application/json'),
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
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array(
            		"X-User-Email: {$this->email}",
            		"X-Api-Key: {$this->apiKey}",
            		"X-Accountmanager-Key:{$this->xAccountKey}",
            		'Content-Type: application/json'),
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
            CURLOPT_CUSTOMREQUEST => "DELETE",
        	CURLOPT_HTTPHEADER => array(
        			"X-User-Email: {$this->email}",
        			"X-Api-Key: {$this->apiKey}",
        			"X-Accountmanager-Key:{$this->xAccountKey}",
        			'Content-Type: application/json'),
        );
        
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }


}
