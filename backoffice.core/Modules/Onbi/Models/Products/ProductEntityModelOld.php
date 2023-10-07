<?php 
class ProductEntityModel extends MainModel
{
    
    public $id;
    
    public $store_id;
    
    public $product_id;
    
    public $sku;
    
    public $productEntity = array();
    
    
    
    
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
    
    public function Save(){
        
        
        if ( ! empty( $this->id ) ) {
            
            
            $query = $this->db->insert('onbi_product_entity', array(
                'id' => $this->id,
                'store_id' => $this->store_id,
                'product_id' => $this->product_id,
                'sku' => $this->sku,
                'websites' => $this->productEntity->websites[0],
                'name' => $this->productEntity->name,
                'description' =>$this->productEntity->description,
                'short_description' =>$this->productEntity->short_description,
                'categories' => $this->productEntity->categories,
                'weight' => $this->productEntity->weight,
                'status' => $this->productEntity->status,
                'url_key' => $this->productEntity->url_key,
                'url_path' => $this->productEntity->url_path,
                'visibility' => $this->productEntity->visibility,
                'category_ids' => json_encode($this->productEntity->category_ids),
                'website_ids' => json_encode($this->productEntity->website_ids),
                'price' => $this->productEntity->price,
                'special_price' => $this->productEntity->special_price,
                'special_from_date' => $this->productEntity->special_from_date,
                'special_to_date' => $this->productEntity->special_to_date,
                'tax_class_id' => $this->productEntity->tax_class_id,
                'tier_price' => $this->productEntity->tier_price,
                'additional_attributes' => $this->productEntity->additional_attributes,
                'stock_data' => $this->productEntity->stock_data

            )
                );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
                return;
            }
            
            
        }
        
        
    }
    
}

?>