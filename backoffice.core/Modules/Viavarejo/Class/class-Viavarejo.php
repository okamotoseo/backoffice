<?php
use \CNovaApiLojistaV2\client\ApiClient;
use \CNovaApiLojistaV2\client\Configuration;
use \CNovaApiLojistaV2\client\ApiException;

class Viavarejo {

    
    
    public $uri;

    public $client_id;
    
    public $access_token;
    
    public $api_client;
    
    public $token;
    
    public $sites = array();
    
    
    
    public function __construct( $moduleConfig = null, $uri = null ) {
    	
    	$this->client_id = $moduleConfig['client_id'];
    	
    	$this->token = $moduleConfig['token'];
    	
        $this->uri = isset($uri) && !empty($uri) ? $uri : 'sandbox-mktplace.viavarejo.com.br/api/v2';
        
        $this->Load();
        
    }
    
    
    public function Load(){
    	
    	Configuration::$apiKey['client_id'] = $this->client_id;
    	
    	Configuration::$apiKey['access_token'] = $this->token;
    	
    	$this->api_client = new ApiClient($this->uri);
    	
    }
    
    
    public function GetSites(){
    	
    		 
    		 
    		$sitesApi = new \CNovaApiLojistaV2\SitesApi($this->api_client);
    	
    		 
    		try {
    	
    			$result = $sitesApi->getSites();
    			
    			if(!empty($result->sites)){
    				foreach ($result->sites as $k => $site){
    					$this->sites[$site['id']] = array(
    							'id' => $site['id'],
    							'name' => $site['name'],
    							'mnemonic' => $site['mnemonic'],
    							'url' => $site['url']
    					);
    				}
    				
    				
    				return $this->sites;
    			}
    	
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
    				 
    				$res = $e->getMessage();
//     				pre($res);
    			}
    			
    		}
    	
    	
    }
    
    
    
    
}