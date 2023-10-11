<?php

class Magento2 {

	/**
	 * @version 1.1.0
	 */
    const VERSION  = "1.1.0";
    
    private  $consumer_key = '';
    
    private  $consumer_secret = '';
    
    protected $access_token;
    
    protected $access_token_secret;
    
    protected $Store;
    
    protected $username;
    
    protected $password;
    
    protected $token;
    
    protected $api_host;
    
    /**
     * Array with fieldName, fieldValue and conditionType
     * @var array()
     */
    public $filters;
    
    /**
     * return Result
     * @var array();
     */
    public $searchCriteria;
    
    /**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );
    
    /**
     * Constructor method. Set all variables to connect in Meli
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $access_token
     * @param string $refresh_token
     */
    public function __construct($api_host, $username = null, $password = null) {
        $this->api_host = $api_host;
        $this->username = $username;
        $this->password = $password;
    }

    

    public function refreshAccessToken(){
    	$path = '/rest/V1/integration/admin/token';
    	$params = array("username" => "{$this->username}", "password" => "{$this->password}");
    	$opts = array(
    			CURLOPT_POST => true,
    			CURLOPT_CUSTOMREQUEST => "POST",
    			CURLOPT_POSTFIELDS => json_encode($params),
    			CURLOPT_HTTPHEADER => array(
    					"Content-Type: application/json",
    					"cache-control: no-cache", 
    					"Content-Lenght: " . strlen(json_encode($params))
    			),
    	);
    	return $this->execute($path, $opts, $params);
    	

    }
    
    
    /**
     *	CONDITION	NOTES
     *	eq			Equals.
     *	finset		A value within a set of values
     *	from		The beginning of a range. Must be used with to
     *	gt			Greater than
     *	gteq		Greater than or equal
     *	in			In. The value can contain a comma-separated list of values.
     *	like		Like. The value can contain the SQL wildcard characters when like is specified.
     *	lt			Less than
     *	lteq		Less than or equal
     *	moreq		More or equal
     *	neq			Not equal
     *	nfinset		A value that is not within a set of values
     *	nin			Not in. The value can contain a comma-separated list of values.
     *	notnull		Not null
     *	null		Null
     *	to			The end of a range. Must be used with from
     */
    public function MakeCriteria(){
    
    	if(!isset($this->filters)){
    		return $this->searchCriteria =  array();
    	}
    
    	foreach($this->filters as $i => $criteria){
    
    		$filters[] = array('field' => $criteria['field'] , 'value' => $criteria['value'], 'condition_type' => $criteria['condition_type']);
    
    	}
    	unset($this->searchCriteria);
    	$this->searchCriteria['search_criteria']['filter_groups'][] = array('filters' => $filters);
    
    	return $this->searchCriteria;
    
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
    public function post($path, $params = array()) {
        $body = json_encode($params);
        $opts = array(
        	CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
            	"Authorization: Bearer {$this->token}",
                'Content-Type: application/json',
                'Content-Length: ' . strlen($body)),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body
        );
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }
    
    public function postImages($path, $params = array()) {
    	$body = json_encode($params);
    	$opts = array(
    			CURLOPT_CUSTOMREQUEST => "POST",
    			CURLOPT_HTTPHEADER => array(
    					"Authorization: Bearer {$this->token}",
    					'Content-Type: multipart/form-data',
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
        	CURLOPT_HTTPHEADER => array(
        		"Authorization: Bearer {$this->token}",
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
    			CURLOPT_HTTPHEADER => array(
    			"Authorization: Bearer {$this->token}",
    			'Content-Type: application/json'),
    			CURLOPT_CUSTOMREQUEST => "DELETE"
    	);
        
        return  $this->execute($path, $opts, $params);
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
