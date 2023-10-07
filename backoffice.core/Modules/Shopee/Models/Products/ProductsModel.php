<?php 

class ProductsModel extends MainModel
{

    public $id;
    
	public $store_id;
	
	public $sku;
	
	public $parent_id;
	
	public $product_id;
	
	public $id_category;
	
	public $created;
	
	public $published;
	
	public $input_data;
	
	public $skus;
	
	public $records = '50';


	
	public function __construct($db = false, $controller = null)
	{
	    $this->db = $db;
	    
	    $this->controller = $controller;
	    
	    if(isset($this->controller)){
	        
	        $this->parametros = $this->controller->parametros;
	        
	        $this->userdata = $this->controller->userdata;
	        
	        $this->store_id = $this->controller->userdata['store_id'];
	        
	    }
	    
	    if(!defined('QTDE_REGISTROS')){
	        
	        define('QTDE_REGISTROS', 50);
	        
	    }
	    
	    
	}
	
	
	public function ValidateForm() {
	    
	    if(in_array('records', $this->parametros )){
	        $records = get_next($this->parametros, array_search('records', $this->parametros));
	        $this->records = isset($records) ? $records : QTDE_REGISTROS ;
	    }
	    
	    if(in_array('Page', $this->parametros )){
	        
	        $this->pagina_atual =  get_next($this->parametros, array_search('Page', $this->parametros));
	        $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
	        
	        foreach($this->parametros as $key => $param){
	            if(property_exists($this,$param)){
	                $val = get_next($this->parametros, $key);
	                $val = str_replace("_x_", "%", $val);
	                $val = str_replace("_", " ", $val);
	                $this->{$param} = $val;
	                
	            }
	        }
	        
	        return true;
	        
	    }else{
	        
	        $this->pagina_atual = 1;
	        $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
	    }
// 	    pre($_POST);die;
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
	        foreach ( $_POST as $property => $value ) {
	            if($value != ''){
	                if(property_exists($this,$property)){
	                    
	                    $this->{$property} = $value;
	                    
	                }
	            }else{
	                $req = array();
	                
	                if( in_array($property, $req) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
	                }
	                
	            }
	            
	        }
	        return true;
	        
	    } else {
	        
	        if ( in_array('edit', $this->parametros )) {
	            
	            $this->Load();
	            
	        }
	        
	        if ( in_array('del', $this->parametros )) {
	                
                $key = array_search('del', $this->parametros);
	                
                $this->id = get_next($this->parametros, $key);
                
	            $this->Delete();
	            
	        }
	        
	        return;
	        
	    }
	    
	}
	
	public function Save(){
	    
	    if(!isset($this->skus)){
	        return;
	    }
	    $skus = explode(PHP_EOL, $this->skus);

	    foreach($skus as $k => $sku){
	        
	        $sku = trim($sku);
	        
	        if(!empty($sku)){
	            
	            
	            if($this->input_data == 'default'){
	            
	                $sql = "SELECT available_products.sku, 
                            available_products.parent_id, 
                            available_products.thumbnail, 
                            available_products.weight, 
                            available_products.category, 
                            available_products.quantity,
                            available_products.sale_price,  
                            available_products.title, 
                            available_products.color, 
                            available_products.variation, 
                            module_shopee_products.*
    		                FROM module_shopee_products
    		                LEFT JOIN available_products ON available_products.sku = module_shopee_products.sku
    		                WHERE module_shopee_products.store_id = {$this->store_id} AND module_shopee_products.sku LIKE '{$sku}' 
                            ORDER BY module_shopee_products.created DESC
                            LIMIT 1000";
	            }
	            
	            
	            if($this->input_data == 'google_xml'){
	                
	                $sql = "SELECT module_google_xml_products.id as sku,
                                module_google_xml_products.item_group_id as parent_id,
                                module_google_xml_products.image_link as thumbnail,
                                module_google_xml_products.shipping_weight as weight,
                                module_google_xml_products.product_type as category,
                                module_google_xml_products.sale_price,
                                module_google_xml_products.title as title,
                                module_google_xml_products.color,
                                module_google_xml_products.brand,
                                module_google_xml_products.size as variation,
                                IF(module_google_xml_products.availability = 'in stock', 1, 0) as quantity,
                                module_shopee_products.*
                        		FROM module_shopee_products
                            	LEFT JOIN module_google_xml_products ON module_google_xml_products.id = module_shopee_products.sku
                            	WHERE module_shopee_products.store_id = {$this->store_id} AND module_shopee_products.sku LIKE '{$sku}'
                                ORDER BY module_shopee_products.parent_id DESC
                                LIMIT 500";
	                
	                if($this->store_id == 3){
	                    
    	                $sql = "SELECT module_google_xml_products.id as sku,
                                module_google_xml_products.item_group_id as parent_id,
                                module_google_xml_products.image_link as thumbnail,
                                module_google_xml_products.shipping_weight as weight,
                                module_google_xml_products.product_type as category,
                                module_google_xml_products.sale_price,
                                module_google_xml_products.title as title,
                                module_google_xml_products.color,
                                module_google_xml_products.brand,
                                module_google_xml_products.size as variation,
                                IF(module_google_xml_products.availability = 'in stock', 1, 0) as quantity,
                                module_shopee_products.*
                        		FROM module_shopee_products
                            	LEFT JOIN module_google_xml_products ON module_google_xml_products.id = module_shopee_products.sku
                            	WHERE module_shopee_products.store_id = {$this->store_id} AND module_shopee_products.sku LIKE '%{$sku}'
                                ORDER BY module_shopee_products.parent_id DESC
                                LIMIT 500";
	                }
	                
	            }
	            
	            $queryVer = $this->db->query($sql);
	            
	            $updatedRes = $queryVer->fetch(PDO::FETCH_ASSOC);
	            
	           
	            if($this->store_id == 3){
    	            $sql = "UPDATE module_shopee_products SET published = 'T' WHERE store_id = {$this->store_id} AND sku LIKE '%{$sku}'";
    	            $this->db->query($sql);
	            }else{
    	            $this->db->update('module_shopee_products',
    	                array('store_id', 'sku'),
    	                array($this->store_id, $sku),
    	                array('published' => 'T')
    	                );
	            }
	            $updated[] = $updatedRes;
	            
	        }else{
	         
	            $updated[] = array('quantity' => 0, 'sale_price' => 0.00);
	        }
	    }
	    
	    return $updated;
	}
	
	public function ExportProductsXml(){
	    
	    $salePriceModel = $this->controller->load_model('Prices/SalePriceModel');
	    $salePriceModel->store_id = $this->store_id;
	    $salePriceModel->marketplace = "Shopee";
	    $total = $j = 0;
	    $parents = array();
	    $categoryEnabled = array();
	    $sql = "SELECT * FROM `module_google_xml_products` WHERE store_id = {$this->store_id}
            AND product_type IN (SELECT hierarchy as product_type FROM module_shopee_categories_xml_relationship WHERE store_id = {$this->store_id})
            AND id NOT IN (SELECT sku as id FROM module_shopee_products WHERE store_id = {$this->store_id} AND published = 'T') LIMIT 10";
	    $query = $this->db->query($sql);
	    $products = $query->fetchAll(PDO::FETCH_ASSOC);
	    if(!empty($products[0])){
	        
	        foreach($products as $key => $product){
	            $sql = "SELECT  module_shopee_categories_xml_relationship.*  FROM  module_shopee_categories_xml_relationship
           			WHERE module_shopee_categories_xml_relationship.store_id = {$this->store_id} AND
                    module_shopee_categories_xml_relationship.hierarchy LIKE '{$product['product_type']}'";
	            $query = $this->db->query($sql);
	            $categoryRel = $query->fetch(PDO::FETCH_ASSOC);
	            
	            if(isset($categoryRel['id_category'])){
	                
	                if(empty($categoryEnabled) OR in_array($categoryRel['id_category'], $categoryEnabled)){
	                    
	                    if(!empty($product['image_link'])){
	                        
	                        $maxImage = 8;
	                        
	                        $imagePrincipal = $product['image_link'];
	                        
	                        $imagesText = $product['image_link']."\t".$product['image_link'] ;
	                        for ($i = 0; $i < $maxImage; $i++) {
	                            $imagesText .= isset($product["additional_image_link_{$i}"]) && !empty($product["additional_image_link_{$i}"]) ? "{$product["additional_image_link_{$i}"]}\t" : "\t" ;
	                            
	                        }
	                        $j++;
	                        
	                        $qtd = 1;
	                        
	                        $salePrice = $product['price'];
	                        
	                        if($qtd > 0){
	                            
	                            if(!isset($parents[$product['item_group_id']])){
	                                $parents[$product['item_group_id']] = 1;
	                                $total++;
	                            }
	                            
	                            $peso = isset($product['shipping_weight']) ? $product['shipping_weight']/1000 : 1 ;
	                            $h = isset( $product['height']) ? ceil( $product['height']) : 20;
	                            $w = isset( $product['width']) ? ceil( $product['width']) : 20 ;
	                            $l = isset( $product['length']) ? ceil( $product['length']) : 20 ;
	                            
	                            $description = strip_tags($product['description']);
	                            $description = str_replace(';', ' ',$description);
	                            $description = str_replace('*',  ' ',$description);
	                            $description = str_replace('•', ' -',$description);
	                            $description = str_replace('\n', ' ',$description);
	                            $description = str_replace('\R', ' ',$description);
	                            $description = str_replace('\r', ' ',$description);
	                            $description = str_replace('\t', ' ',$description);
	                            $description = str_replace('&nbsp', ' ', $description);
	                            $description = str_replace('  ',  ' ',$description);
	                            $description = trim(preg_replace('/\s\s+/', ' ', $description));
	                            
	                            $description = substr($description, 0, 5000);
	                            
	                            $title = substr($product['title'], 0, 255);
	                            
	                            $csvRow = "{$categoryRel['id_category']}\t{$title}\t{$description}\t{$product['item_group_id']}\t{$product['item_group_id']}\tCor\t{$product['color']}\t{$imagePrincipal}\tTamanho\t{$product['size']}\t{$salePrice}\t{$qtd}\t{$product['id']}\t{$imagesText}{$peso}\t{$w}\t{$l}\t{$h}\tAtivar\t";
	                            $rowProduct .= $csvRow.PHP_EOL;
	                            $sqlVerify = "SELECT * FROM module_shopee_products WHERE store_id = {$this->store_id} AND sku LIKE '{$product['id']}'";
	                            $queryVerify = $this->db->query($sqlVerify);
	                            $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
	                            if(!isset($resVerify['id'])){
                                    $queryRes = $this->db->insert('module_shopee_products', array(
                                        'store_id' => $this->store_id,
                                        'product_id' => $product['id'],
                                        'sku' => $product['id'],
                                        'parent_id' => $product['item_group_id'],
                                        'id_category' => $categoryRel['id_category'],
                                        'created' => date("Y-m-d H:i:s")
                                    ));
	                            }
    	                          
	                        }
	                    }
	                }
	            }
	        }
	        
	        return $rowProduct;
	    }
	}
	
	
	public function ExportProducts(){
	    
	    $salePriceModel = $this->controller->load_model('Prices/SalePriceModel');
	    $salePriceModel->store_id = $this->store_id;
	    $salePriceModel->marketplace = "Shopee";
	    
	    $j = $total = 0 ;
	    
	    $parents = array();
	    
	    $categoryEnabled = array();
	    
// 	    $sql = "SELECT * FROM `available_products` WHERE store_id = {$this->store_id}
//    			AND ean != '' and quantity > 3 AND parent_id IS NOT NULL AND variation != ''
//             AND id NOT IN (SELECT product_id as id FROM module_shopee_products WHERE store_id = {$this->store_id} AND published = 'T')
//        		 ORDER BY id DESC";
	     $sql = "SELECT * FROM `available_products` WHERE store_id = {$this->store_id} 
   			AND ean != '' and quantity > 1 AND parent_id IS NOT NULL AND variation != ''
            AND id NOT IN (SELECT product_id as id FROM module_shopee_products WHERE store_id = {$this->store_id} AND published = 'T') 
            ORDER BY id DESC LIMIT 100";
	    $query = $this->db->query($sql);
	    
	    $products = $query->fetchAll(PDO::FETCH_ASSOC);
	    
// 	    pre($products);
	    
	    if(!empty($products[0])){
	        
	        foreach($products as $key => $product){
	            $sql = "SELECT  module_shopee_categories_relationship.*  FROM  module_shopee_categories_relationship
           			WHERE module_shopee_categories_relationship.store_id = {$this->store_id} AND
                    module_shopee_categories_relationship.hierarchy LIKE '{$product['category']}'";
	            $query = $this->db->query($sql);
	            
	            $categoryRel = $query->fetch(PDO::FETCH_ASSOC);
	            
	            if(isset($categoryRel['id_category'])){
	                
	                if(empty($categoryEnabled) OR in_array($categoryRel['id_category'], $categoryEnabled)){
	                    
	                    $parentImages = getUrlImageFromParentId($this->db, $this->store_id, $product['parent_id']);
	                    
	                    if(!empty($parentImages[0])){
	                        
// 	                        $images = getUrlImageFromId($this->db, $this->store_id, $product['id']);
	                        
	                        $images = $this->getProductImageShopee($this->db, $this->store_id, $product['id']);
	                        $maxImage = 8;
// 	                        pre($images);
	                        $imagePrincipal = isset($images[0]) && !empty($images[0]) ? "{$images[0]}" : " " ;
	                        
	                        $imagesText = isset($images[0]) && !empty($images[0]) ? "{$images[0]}\t"  : " \t" ;
	                        
	                        for ($i = 0; $i < $maxImage; $i++) {
	                
// 	                            $imagesText .= !empty($images[$i]) ? "{$images[$i]}\t" : "{$images[0]}\t" ;
	                            if(isset($images[$i])){
	                               $imagesText .= !empty($images[$i]) ? "{$images[$i]}\t" : "\t" ;
	                            }else{
	                                
	                                $imagesText .="\t";
	                            }
	                        }
	                        
	                        
	                        $j++;
	                        
	                        $qtd = $product['quantity'] > 0 ? $product['quantity'] : 0 ;
	                        
	                        $salePriceModel->sku = trim($product['sku']);
	                        
	                        $salePriceModel->product_id = $product['id'];
	                        
	                        $salePrice = $salePriceModel->getSalePrice();
	                        
	                        $stockPriceRel = $salePriceModel->getStockPriceRelacional();
	                        
	                        $salePrice = empty($stockPriceRel['price']) ? $salePrice : $stockPriceRel['price'] ;
	                        
	                        $salePrice = ceil($salePrice) - 0.10;
	                        
	                        $qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
	                        
	                        if ($product['blocked'] == "T"){
	                            $qtd = 0;
	                        }
	                        
	                        if($qtd > 0){
	                            
	                            if(!isset($parents[$product['parent_id']])){
	                                $parents[$product['parent_id']] = 1;
	                                $total++;
	                            }
	                            
	                            $peso = ceil($product['weight']);
	                            $h = ceil( $product['height']);
	                            $w = ceil( $product['width']);
	                            $l = ceil( $product['length']);
	                            
	                            $description = strip_tags($product['description']);
	                            $description = str_replace(';', ' ',$description);
	                            $description = str_replace('*',  ' ',$description);
	                            $description = str_replace('•', ' -',$description);
	                            $description = str_replace('\n', ' ',$description);
	                            $description = str_replace('\R', ' ',$description);
	                            $description = str_replace('\r', ' ',$description);
	                            $description = str_replace('\t', ' ',$description);
	                            $description = str_replace('&nbsp', ' ', $description);
	                            $description = str_replace('  ',  ' ',$description);
	                            $description = trim(preg_replace('/\s\s+/', ' ', $description));
	                            
	                            $description = substr($description, 0, 5000);
	                            $title = substr($product['title'], 0, 255);
	                            $variationType = ucfirst(strtolower($product['variation_type']));
	                            
	                            $csvRow = "{$categoryRel['id_category']}\t{$title}\t{$description}\t{$product['parent_id']}\t{$product['parent_id']}\tCor\t{$product['color']}\t{$imagePrincipal}\t{$variationType}\t{$product['variation']}\t{$salePrice}\t{$qtd}\t{$product['sku']}\t{$imagesText}{$peso}\t{$w}\t{$l}\t{$h}\tAtivar\t\t";
	                            
	                            $rowProduct .= $csvRow.PHP_EOL;
	                            
	                            $sqlVerify = "SELECT * FROM module_shopee_products WHERE store_id = {$this->store_id} AND sku LIKE '{$product['sku']}'";
	                            
                                $queryVerify = $this->db->query($sqlVerify);
                                $resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
                                if(!isset($resVerify['id'])){
                                    
                                    
                                    $queryRes = $this->db->insert('module_shopee_products', array(
                                        'store_id' => $this->store_id,
                                        'product_id' => $product['id'],
                                        'sku' => $product['sku'],
                                        'parent_id' => $product['parent_id'],
                                        'id_category' => $categoryRel['id_category'],
                                        'created' => date("Y-m-d H:i:s")
                                    ));
                                    
                                }
                                    
	                        }
	                        
	                    }
	                    
	                }
	            
	           }
	           
	       }
	    
	   return $rowProduct;
	    
	   }
	   
    }
	
	
	
    public function getProductImageShopee($db, $storeId, $productId){
        
        
        $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$productId} ORDER BY id ASC";
        $query = $db->query($sql);
        $product = $query->fetch(PDO::FETCH_ASSOC);
        
        $images = getPathImageFromSku($db, $storeId, $product['sku']);
        
        $quality = 80;
        $i= 0;
        $picture_source = array();
        $pathSave = "/var/www/html/app_mvc/Modules/Shopee/img/";
        $pathShow = "https://backoffice.sysplace.com.br/Modules/Shopee/img/";
        foreach($images as $key => $filePath){
            
            $imageOutput = $filePath;
            
            $sizeOrig = floatval(filesize( $filePath ));
//             pre($sizeOrig);
            if($sizeOrig > 200000){
                $dif = ceil((($sizeOrig - 200000) / $sizeOrig) * 100);
                $type = exif_imagetype($filePath);
//                 pre($type);
                $partsFileName = explode('/', $filePath);
                $partsName = explode(".", end($partsFileName));
                $imageOutput = $pathShow.$product['id'].'-'.$i.'.'.end($partsName);
                $imageInput = $pathSave.$product['id'].'-'.$i.'.'.end($partsName);
                
                switch($type){
                    case 2: $image = imagecreatefromjpeg($filePath); break;
                    case 3: $image = imagecreatefrompng($filePath); break;
                    case 1: $image = imagecreatefromgif($filePath); break;
                }
                
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagealphablending($bg, TRUE);
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                imagejpeg($bg, $imageInput, $quality);
                imagedestroy($bg);
                $newsize = floatval(filesize($imageInput));
//                 pre($newsize);
                if($newsize > 200000){
//                     echo "error|A imagem é maior que o permitido -> {$newsize}";
                }
                
            }
            
            $imageOutput = str_replace("/var/www/html/app_mvc/", "https://backoffice.sysplace.com.br/", $imageOutput);
            
            $picture_source[$i] = trim($imageOutput);
            $i++;
            $imageOutput = '';
        }
        
        return $picture_source;
    }
    
	
	
	
	
	
	
	public function TotalProducts(){
	    
	    $sql = "SELECT count(*) as total FROM module_shopee_products WHERE store_id = {$this->store_id}";
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	public function ListProducts(){
	    
        $query = $this->db->query("SELECT available_products.*, module_shopee_products.*
    		FROM module_shopee_products
    		LEFT JOIN available_products ON available_products.id = module_shopee_products.product_id
    		WHERE module_shopee_products.store_id= ?
    		ORDER BY module_shopee_products.created DESC
            LIMIT {$this->linha_inicial}, {$this->records}",
            array( $this->store_id)
        );
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function ListProductsXml(){
	    
	    $query = $this->db->query("SELECT module_google_xml_products.id as sku, 
                module_google_xml_products.item_group_id as parent_id,
                module_google_xml_products.image_link as thumbnail,
                module_google_xml_products.shipping_weight as weight,
                module_google_xml_products.product_type as category,
                module_google_xml_products.sale_price,
                module_google_xml_products.title,
                module_google_xml_products.color,
                module_google_xml_products.brand,
                module_google_xml_products.size as variation,
             module_shopee_products.*
    		FROM module_shopee_products
    		LEFT JOIN module_google_xml_products ON module_google_xml_products.id = module_shopee_products.sku
    		WHERE module_shopee_products.store_id= ?
    		ORDER BY module_shopee_products.created ASC
            LIMIT {$this->linha_inicial}, {$this->records}",
            array( $this->store_id)
	    );
	    
	    if ( ! $query ) {
	        return array();
	    }
	    
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	
	
	
	public function GetProductsFilter()
	{
	    
	    $where_fields = "";
	    $values = array();
	    $class_vars = get_class_vars(get_class($this));
	    foreach($class_vars as $key => $value){
	     
	        if($this->{$key} != ''){
	            switch($key){
	                case 'store_id': $where_fields .= "module_shopee_products.{$key} = {$this->$key} AND ";break;
	                case 'id': $where_fields .= "module_shopee_products.{$key} = {$this->$key} AND ";break;
	                case 'product_id': $where_fields .= "module_shopee_products.{$key} = {$this->$key} AND ";break;
	                case 'id_product': $where_fields .= "module_shopee_products.{$key} = {$this->$key} AND ";break;
	                case 'parent_id': $where_fields .= "module_shopee_products.{$key} LIKE '{$this->$key}' AND ";break;
	                case 'title': $where_fields .= "module_shopee_products.{$key} LIKE '{$this->$key}' AND ";break;
	                case 'ean': $where_fields .= "module_shopee_products.{$key} LIKE '{$this->$key}' AND ";break;
	                case 'reference': $where_fields .= "module_shopee_products.{$key} LIKE '{$this->$key}' AND ";break;
	                case 'brand': $where_fields .= "module_shopee_products.{$key} LIKE '{$this->$key}' AND ";break;
	                case 'images': $where_fields .= "module_shopee_products.{$key} = '{$this->$key}' AND ";break;
	                case 'stock': $where_fields .= "module_shopee_products.{$key} >= {$this->$key} AND ";break;
	                case 'available': $where_fields .= "module_shopee_products.{$key} = '{$this->$key}' AND ";break;
	            }
	        }
	        
	    }
	   $where_fields = substr($where_fields, 0,-4);
	    
	    return $where_fields;
	    
	}
	
	public function TotalGetProducts(){
	    
	    $where_fields = $this->GetProductsFilter();
	    
	    $sql = "SELECT count(*) as total FROM module_shopee_products WHERE {$where_fields}";
	    $query = $this->db->query( $sql);
	    $total =  $query->fetch(PDO::FETCH_ASSOC);
	    return $total['total'];
	    
	}
	
	
	public function GetProducts()
	{
	    $where_fields = $this->GetProductsFilter();
	    
        $sql = "SELECT available_products.*, module_shopee_products.*
    		FROM module_shopee_products
        	LEFT JOIN available_products ON available_products.id = module_shopee_products.product_id
        	WHERE {$where_fields} ORDER BY module_shopee_products.parent_id DESC
            LIMIT {$this->linha_inicial}, {$this->records}";
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	
	public function GetProductsXml()
	{
	    $where_fields = $this->GetProductsFilter();
	    
	    $sql = "SELECT module_google_xml_products.id as sku, 
                module_google_xml_products.item_group_id as parent_id,
                module_google_xml_products.image_link as thumbnail,
                module_google_xml_products.shipping_weight as weight,
                module_google_xml_products.product_type as category,
                module_google_xml_products.sale_price,
                module_google_xml_products.title as title,
                module_google_xml_products.color,
                module_google_xml_products.brand,
                module_google_xml_products.size as variation,
            module_shopee_products.*
    		FROM module_shopee_products
        	LEFT JOIN module_google_xml_products ON module_google_xml_products.id = module_shopee_products.sku
        	WHERE {$where_fields} ORDER BY module_shopee_products.parent_id DESC
            LIMIT {$this->linha_inicial}, {$this->records}";
	    $query = $this->db->query($sql);
	    if ( ! $query ) {
	        return array();
	    }
	    
	    return $query->fetchAll(PDO::FETCH_ASSOC);
	    
	}
	
	public function Delete()
	{
	    
	    if(empty($this->id)){
	        return array();
	    }
	        
        $query = $this->db->query('DELETE FROM module_shopee_products
        WHERE store_id = ? AND `id`= ?', array($this->store_id, $this->id ) );
        
        if ( !$query ) {
            
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            
            return;
            
        }
        
        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
	    
	}
	
}

?>