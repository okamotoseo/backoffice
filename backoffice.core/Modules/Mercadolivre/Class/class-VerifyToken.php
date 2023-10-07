<?php 
class VerifyToken{
    
    public $db;
    
    public function __construct( $db = false ) {
        
        $this->db = $db;
        
        
    }
   
    
    public function getConfig(){
        
        
        $sql = "SELECT * FROM `ml_config` WHERE `store_id` = {$storeId}";
        $resMlConfig = mysql_fetch_assoc(mysql_query($sql));
        
        
    }
    
    public function Verify(){
        
        $sql = "SELECT * FROM `ml_config` WHERE `store_id` = {$storeId}";
        $query = $this->db->query($sql);
        $resMlConfig = $query->fetch(PDO::FETCH_ASSOC);
        $meli = new Meli($resMlConfig['app_id'], $resMlConfig['secret_key'], $resMlConfig['access_token'], $resMlConfig['refresh_token']);
        if($resMlConfig['expires_in'] < time()) {
        	try {
        		$refresh = $meli->refreshAccessToken();
        		if($refresh['body']->access_token) {
        			$expires_in = time() + $refresh['body']->expires_in;
        			$sql = "UPDATE `ml_config` SET `access_token`='{$refresh['body']->access_token}',`expires_in`='{$expires_in}',
        			`refresh_token`='{$refresh['body']->refresh_token}' WHERE store_id = {$storeId}";
        			$this->db->query($sql);
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
    
}

?>