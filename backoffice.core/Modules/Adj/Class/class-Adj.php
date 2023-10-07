<?php

class Adj {

    public $db;
    
    /**
     * Configuration for CURL
     */
//     public static $CURL_OPTS = array(
// //           CURLOPT_PORT => 2001,
// 		  CURLOPT_RETURNTRANSFER => true,
// 		  CURLOPT_ENCODING => "UTF-8",
// // 		  CURLOPT_MAXREDIRS => 10,
// 		  CURLOPT_TIMEOUT => 60,
// 		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
//     );
    
    public static $CURL_OPTS = array(
//         CURLOPT_PORT => 2001,
    	CURLOPT_PORT => '8080',
        CURLOPT_RETURNTRANSFER => true,
    	CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => "UTF-8",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
    );
    
//     public static $CURL_OPTS = array(
//         CURLOPT_SSL_VERIFYPEER => true,
//         CURLOPT_CONNECTTIMEOUT => 10,
//         CURLOPT_RETURNTRANSFER => 1,
//         CURLOPT_TIMEOUT => 60
//     );
    
    /**
     * @var $API_ROOT_URL is a main URL to access the Adj API's.
     * @var $AUTH_URL is a url to redirect the user for login.
//      */
    public static $API_ROOT_URL = 'http://187.32.13.196';
    
//     public static $API_ROOT_URL = 'https://cloud2.informo.com.br';
    
//     public static $API_ROOT_URL = 'http://cloud.informo.com.br';
    
//     public static $API_ROOT_URL = 'http://187.32.13.193';
    
//     public static $API_ROOT_URL = 'http://177.87.152.154';
    
    public $store_id;
    
    protected $port;
    
    protected $user;
    
    protected $pass;
    
    protected $token;
    
    public $formdata;
    
    /**
     * Constructor method. Set all variables to connect in Adj
     *
     * @param string $store_id
     * @param string $port
     * @param string $user
     * @param string $pass
     * @param string $token
     */
    public function __construct($db, $store_id) {
    	
    	$this->db = $db;
    	$this->store_id = $store_id;
    	
    	$sql = "SELECT * FROM module_adj WHERE store_id = ? ";
    	$query = $this->db->query($sql, array($this->store_id));
    	$res = $query->fetch(PDO::FETCH_ASSOC);
    	
    	if(isset($res['token']) && isset($res['password'])){
	    	$this->user = $res['user'];
	    	$this->port = $res['port'];
	    	$this->pass = $res['password'];
	    	$this->token = $res['token'];
	    	$this->Login();
    	}
    }
    
 
    
    public function Login(){
    	$path = '/adj-api/UsuarioApi/Login';
    	$params = json_encode(array("UserName" => "{$this->user}", "Password" => "{$this->pass}"));
//     	$params = "{\R\t'Username': 'willians.seo@gmail.com',\R\t'Password': '24973647000168'\R}";
//     	pre($params);die;
//     	echo $params;die;
    	$opts = array(
    	        CURLOPT_POST => true,
    			CURLOPT_CUSTOMREQUEST => "POST",
    			CURLOPT_POSTFIELDS => $params,
    			CURLOPT_HTTPHEADER => array(
			    	"Content-Type: application/json",
			    	"cache-control: no-cache"
    			),
    	);
//     	pre($params);die;
    	$response = $this->execute($path, $opts, $params);
//     	pre($response);die;
    	$this->token = $response['body']['Jwt'];
    	if(!empty($this->token)){
    	$query = $this->db->update('module_adj', 'store_id', $this->store_id, array(
	    				'token' => $this->token
	    			)
    			);
    	
    	}
    }
    
    public function Products($params){
    	
    	$path = '/adj-api/ProdutoApi/Produtos?$Expand=Estoque';
		$params = json_encode($params);
// 		array(
// 				"@xdata.type" => "XData.Default.DTOProduto",
// 				"descricao" => "LUMINARIA",
// 				"dataUpdate" => "2018-09-01"
// 		);
		$opts = array(
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $params,
				CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer {$this->token}",
						"Content-Type: application/json",
						"cache-control: no-cache"
								),
		);
		
		return $this->execute($path, $opts, $params, true);
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
    	
    	$uri = self::$API_ROOT_URL.$path;
    	
    	$ch = curl_init($uri);
    	curl_setopt_array($ch, self::$CURL_OPTS);
    
    	if(!empty($opts))
    		curl_setopt_array($ch, $opts);
    	$return["body"] = json_decode(curl_exec($ch), $assoc);
    	$return['error'] = curl_error($ch);
    	$return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    	curl_close($ch);
    	return $return;
    }
    
    
    
    
}