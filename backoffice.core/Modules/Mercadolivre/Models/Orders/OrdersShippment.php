<?php 

class OrdersShippment extends Meli
{

	/**
	 * @var string
	 * Class Unique ID
	 */

	public $meli;
	
	public $set_id;
	
	public $attributes;
	
	public $categories;
	
	public $categoriesInfo = array();
	
	public $categoriesHierarchy;




	
	public function __construct($db = false,  $controller = null, $storeId = null)
	{
	    $this->db = $db;
	    
	    $this->store_id = $storeId;
	    
	    $this->controller = $controller;
	    
	    if(isset($this->controller)){
	        
    	    $this->parametros = $this->controller->parametros;
    	    
    	    $this->userdata = $this->controller->userdata;
    	    
    	    $this->store_id = $this->controller->userdata['store_id'];
    	    
	    }

	    
	}
	
	public function verifySession(){
	    
    	$sql = "SELECT * FROM `module_mercadolivre` WHERE `store_id` = {$storeId}";
    	
    	$query = $this->db->query($sql);
    	
    	$resMlConfig = $query->fetch(PDO::FETCH_ASSOC);
    	
    	$meli = new Meli($resMlConfig['app_id'], $resMlConfig['secret_key'], $resMlConfig['access_token'], $resMlConfig['refresh_token']);
    	
    	if($resMlConfig['expires_in'] < time()) {
    	    try {
    	        $refresh = $meli->refreshAccessToken();
    	        if($refresh['body']->access_token) {
    	            $expires_in = time() + $refresh['body']->expires_in;
    	            $sql = "UPDATE `module_mercadolivre` SET `access_token`='{$refresh['body']->access_token}',`expires_in`='{$expires_in}',
    			`refresh_token`='{$refresh['body']->refresh_token}' WHERE store_id = {$storeId}";
    	            $db->query($sql);
    	            $resMlConfig['access_token'] = $refresh['body']->access_token;
    	            $resMlConfig['refresh_token'] = $refresh['body']->refresh_token;
    	            $resMlConfig['expires_in'] = $refresh['body']->expires_in;
    	        }else{
    	            notifyAdmin($refresh['body']->message);
    	        }
    	    } catch (Exception $e) {
    	        echo $error =  "Exception: ",  $e->getMessage(), "\n";
    	        notifyAdmin($error);
    	    }
    	}
    	
    	$user = $meli->get('/users/me', array('access_token' => $resMlConfig['access_token']));
    	if($user['httpCode'] != "200"){
    	    notifyAdmin("access token invalido"+time());
    	    return;
    	}
	}
	
	public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
            	if(!empty($value)){
	                if(property_exists($this,$property)){
	                   $this->{$property} = $value;
	                }
            	}
                
            }
            
            return true;
            
        } else {
            
        	
            return;
            
        }
        
	    
	}
	
	
	
	function closeAdsProduct($meli, $storeId, $productId, $access_token) {
	    $information = array(
	        "status" => 'closed'
	    );
	    $condition = "/items/MLB{$productId}";
	    $result = $meli->put($condition, $information, array('access_token' => $access_token));
	    return $result;
	    
	    
	}
    
}
?>