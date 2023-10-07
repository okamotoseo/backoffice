<?php 
class ProductEntity
{
    
    public $id;
    
    public $categories = array();
    
    public $websites = array(1);
    
    public $associated_skus = array();
    
    public $name;
    
    public $description;
    
    public $short_description;
    
    public $weight = '1.0000';
    
    public $volume_altura ;
    
    public $volume_comprimento;
    
    public $volume_largura;
    
    public $status = '1';
    
    public $url_key;
    
    public $url_path;
    
    public $visibility = '4';
    
    public $category_ids;
    
    public $website_ids;
    
    public $has_options = 0;
    
    public $gift_message_available;
    
    public $price;
    
    public $special_price;
    
    public $special_from_date;
    
    public $special_to_date;
    
    public $tax_class_id = 2;
    
    public $tier_price;
    
    public $meta_title;
    
    public $meta_keyword;
    
    public $meta_description;
    
    public $custom_design;
    
    public $custom_layout_update;
    
    public $options_container;
    
    public $additional_attributes;
    
    public $stock_data;
    
    public $set_attribute;
    
    public $type = 'simple';
    

    
    public function __construct( $data = array() ) 
    {
        
        
        foreach($data as $key => $value)
        {
            $column_name = str_replace('-','_',$key);
            $this->{$column_name} = $value;
        }
        
    }
    
    
    public function setEntityFromAvailableProducts( $data = array() )
    {
//     pre($data);die;
        foreach ( $data as $property => $value ) {
            
            if(!empty($value)){
                
                switch($property){
                    
                    case 'title':
                        $this->name = $value;
                        $this->meta_title = $value;
                        
                        break;
                        
                    case 'description': 
                        $this->description = $value;
                        $this->meta_description = mb_substr($value, 0, 200, 'utf8');
                        break;
                        
                    case 'sale_price': $this->price = $value; break;
                    case 'category': $this->categories = $value; break;
                    case 'sku': $this->sku = $value; break;
                    

                    
                }
                

            }else{
                $required = array();
                
                if( in_array($property, $required) ){
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                    return;
                }
                
            }
            
        }
        
    }
    
    
}

?>