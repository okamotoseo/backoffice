<?php

class ItemsRestModel extends REST
{
    
    public $db;
    
    public $store_id;
    
    public $category_id;
    
    public $item_id;
    
    public $id_product;
    
    public $productData = array();
    
    public $productVariantData = array();
    
    public $dataFilter = array();
    
    public $variation_id;
    
    
    

    
    
    
    
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
        if(isset($this->store_id)){
            
            parent::__construct($this->db, $this->store_id);
            
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
    
    
    
    function getProducts(){
        
        if(empty($this->dataFilter)){
            return;
        }else{
            $this->dataFilter['access_token'] = $this->access_token;
        }
        
        $result = $this->tray->get ( '/products', $this->dataFilter);
        
        return $result;
        
    }
    function getProduct(){
        
   
        $this->dataFilter['access_token'] = $this->access_token;
        
        $result = $this->tray->get ( "/products/{$this->id_product}", $this->dataFilter);
        
        return $result;
        
    }
    
    function postProduct(){
        
        if(!isset($this->productData)){
            return array();
        }
        
        $result = $this->tray->post ( '/products', $this->productData, array ('access_token' => $this->access_token));
        
        return $result;
        
    }
    
    
    function postProductVariation(){
        
        if(!isset($this->productVariantData)){
            return array();
        }
        
        $result = $this->tray->post ( '/products/variants/', $this->productVariantData, array ('access_token' => $this->access_token));
        
        return $result;
        
    }
    
    public function putProduct(){
        
        if(!isset($this->id_product)){
            return array();
        }
        
        $result = $this->tray->put("/products/{$this->id_product}", $this->productData, array ('access_token' => $this->access_token));
        
        return $result;
    }
    
    public function putProductVariation(){
        
        if(!isset($this->variation_id)){
            return array();
        }
        
        $result = $this->tray->put("/products/variants/{$this->variation_id}", $this->productVariantData, array ('access_token' => $this->access_token));
        
        return $result;
    }
    
    public function deleteProduct(){
        
        if(!isset($this->id_product)){
            return array();
        }
        
        //         delete($path, $params)
        $result = $this->tray->delete("/products/{$this->id_product}", array ('access_token' => $this->access_token));
        
        return $result;
    }
    public function deleteVariations(){
        
        if(!isset($this->variation_id)){
            return array();
        }
        
        //         delete($path, $params)
        $result = $this->tray->delete("/products/variants/{$this->variation_id}", array ('access_token' => $this->access_token));
        
        return $result;
    }
    
    
    
    
    
    
    public function putImageProduct(){
    
    	if(!isset($this->id_product)){
    		return array();
    	}
    	
    	$sqlVerify = "SELECT product_id, id_product FROM module_tray_products WHERE store_id = {$this->store_id} AND id_product LIKE '{$this->id_product}'";
    	$queryVerify = $this->db->query($sqlVerify);
    	$verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
    	$this->productData = array();
    	if(isset($verify['id_product'])){
    	
    		$sql = "SELECT * FROM available_products WHERE store_id = {$this->store_id} AND id = {$verify['product_id']} ORDER BY id ASC";
    		$query = $this->db->query($sql);
    		$product = $query->fetch(PDO::FETCH_ASSOC);
    		
    		if($this->store_id == 6){
//     		  $images = getPathImageFromParentId($this->db, $this->store_id, $product['parent_id']);
    		    $images = getUrlImageFromParentId($this->db, $this->store_id, $product['parent_id']);
    		}else{
//     		    $images = getPathImageFromSku($this->db, $this->store_id, $product['sku']);
    		    $images = getUrlImageFromSku($this->db, $this->store_id, $product['sku']);
    		    
    		}
    		$quality = 80; 
    		$i= 1;
    		$pathSave = "/var/www/html/app_mvc/Modules/Tray/Webservice/images/";
    		$pathShow = "https://backoffice.sysplace.com.br/Modules/Tray/Webservice/images/";
    		foreach($images as $key => $filePath){
    			$imageOutput = $filePath;
//     			$sizeOrig = floatval(filesize( $filePath ));
    	
//     			if($sizeOrig > 350000){ 
//     				$dif = ceil((($sizeOrig - 350000) / $sizeOrig) * 100);
//     				$type = exif_imagetype($filePath);
//     				$partsFileName = explode('/', $filePath);
//     				$partsName = explode(".", end($partsFileName));
//     				$imageOutput = $pathShow.$partsName[0]. ".jpg";
//     				$imageInput = $pathSave.$partsName[0]. ".jpg";
    				 
//     				switch($type){
//     					case 2: $image = imagecreatefromjpeg($filePath); break;
//     					case 3: $image = imagecreatefrompng($filePath); break;
//     					case 1: $image = imagecreatefromgif($filePath); break;
//     				}
    				 
//     				$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
//     				imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
//     				imagealphablending($bg, TRUE);
//     				imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
//     				imagedestroy($image);
// //     				$quality = 110 - $dif; // 0 = worst / smaller file, 100 = better / bigger file
//     				imagejpeg($bg, $imageInput, $quality);
//     				imagedestroy($bg);
    				
//     				$newsize = floatval(filesize($imageInput));
//     				if($newsize > 350000){
//     					echo "error|A imagem é maior que o permitido -> {$newsize}";
//     				}
    	
//     			}
    	
    			$imageOutput = str_replace("/var/www/html/app_mvc/", "https://backoffice.sysplace.com.br/", $imageOutput);
    			$this->productData["Product"]["picture_source_{$i}"] = $imageOutput;
    			$i++;
    		}
    	}
//     	pre($this->productData);
    	$result = $this->tray->put("/products/{$this->id_product}", $this->productData, array ('access_token' => $this->access_token));
    	return $result;
    }
    
}

?>