<?php

class MediaModel extends MainModel
{

	public $id;
	
    public $store_id;
    
    public $brand;
    
    public $description;
    

    
    public function __construct( $db = false, $controller = null ) {
        
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
    
    
    
    public function getUrlImageFromSku($db, $storeId, $sku){
        
        $query = $db->query("SELECT id FROM available_products WHERE store_id = ? AND sku LIKE ?", array($storeId, $sku));
        $row = $query->fetch(PDO::FETCH_ASSOC);
        
        $pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$row['id']}";
        $pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$row['id']}";
        $iterator = new DirectoryIterator($pathRead);
        foreach ( $iterator as $key => $entry ) {
            $file = $entry->getFilename();
            if($file != '.' AND $file != '..'){
                $urlImage[] = $pathShow."/".$file;
            }
            
        }
        sort($urlImage);
        
        return $urlImage;
    }
  
    
} 