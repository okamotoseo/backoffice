<?php

set_time_limit ( 30000 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Products/ProductDescriptionModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

if (empty ( $action ) and empty ( $storeId ) ) {
    
    if(isset($_SERVER ['argv'] [1])){
        $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
        $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    }
    
    if(isset($_SERVER ['argv'] [2])){
        $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
        $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
    }
    
    $request = "System";
    
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
    $moduleConfig = getModuleConfig($db, $storeId, 8);
    
    switch($action){
        
        /**
         * importa e atualiza  categoria titulo e descrição, cor, ean, peso
         * se não houver descrição em availability products
         *
         */
        case "import_xml":
          
//         	$xml = "/var/www/html/app_mvc/Modules/Google/xml/googleshopping-sku.xml";
            $xml = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/xml/googleshopping.xml";
        	
            $rss = simplexml_load_file ($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if(!empty($rss->channel->item)){
                $sql = "DELETE FROM module_google_xml_products WHERE store_id = ?";
                $queryDel = $db->query($sql, array($storeId));
            }
            
            $i = $count = 0;
            foreach ($rss->channel->item as $key => $entry){
                
                $namespaces = $entry->getNameSpaces(true);
                
                $tag = $entry->children($namespaces['g']);
                
                $availability = "".$tag->availability;
                
                if($availability == "in stock"){
//                     pre($tag);die;
                    $id = "".$tag->id;
                    $size = "".$tag->size;
                    $item_group_id = "".$tag->item_group_id;
                    if($storeId == '3'){
                        $item_group_id = substr($id, 0, 6);
//                         $id = $id.$size;
                    }
                    $title = "".$tag->title;
                    $description = "".$tag->description;
                    $product_type = "".$tag->product_type;
                    $gtin = "".$tag->gtin;
                    $shipping_weight = getNumbers("".$tag->shipping_weight);
                    $availability = "".$tag->availability;
                    $priceParts = explode(' ', "".$tag->price);
                    $price = trim($priceParts[0]);
                    $sale_priceParts = explode(' ', "".$tag->sale_price);
                    $sale_price = trim($sale_priceParts[0]);
                    $brand = "".$tag->brand;
                    $color = "".$tag->color;
                    $mpn = "".$tag->mpn;
                    $gender = "".$tag->gender;
                    
                    $link = "".$tag->link;
                    $image_link = "".$tag->image_link;
                    
                    $additional_image_link_0 = "".$tag->additional_image_link[0];
                    $additional_image_link_1 = "".$tag->additional_image_link[1];
                    $additional_image_link_2 = "".$tag->additional_image_link[2];
                    $additional_image_link_3 = "".$tag->additional_image_link[3];
                    $additional_image_link_4 = "".$tag->additional_image_link[4];
                    $additional_image_link_5 = "".$tag->additional_image_link[5];
                    $additional_image_link_6 = "".$tag->additional_image_link[6];
                    
                    
                    $custom_label_0 = isset($tag->custom_label_0) ? "".$tag->custom_label_0 : null ;
                    $custom_label_1 = isset($tag->custom_label_1) ? "".$tag->custom_label_1 : null ;
                    $custom_label_2 = isset($tag->custom_label_2) ? "".$tag->custom_label_2 : null ;
                    $custom_label_3 = isset($tag->custom_label_3) ? "".$tag->custom_label_3 : null ;
                    $custom_label_4 = isset($tag->custom_label_4) ? "".$tag->custom_label_4 : null ;
                    
                    $gender = "".$tag->gender;
                    
                    $ageGroup = "".$tag->age_group;
                    
                   
                    
                    $category = generateCategoriesGenderAge($gender, $ageGroup, $product_type);
                   
                    $queryRes = $db->insert('module_google_xml_products', array(
                        'store_id' => $storeId,
    				    'id' => $id,
    				    'item_group_id' => $item_group_id,
    				    'title' =>$title,
    				    'description' => $description,
    				    'shipping_weight' => $shipping_weight,
    				    'availability' => $availability,
    				    'price' => $price,
                        'sale_price' => $sale_price,
    				    'brand' => $brand,
    				    'color' => $color,
    				    'mpn' => $mpn,
                        'size' => $size,
                        'gtin' => $gtin,
                        'link' => $link,
                        'image_link' => $image_link,
                        'additional_image_link_0' => $additional_image_link_0,
                        'additional_image_link_1' => $additional_image_link_1,
                        'additional_image_link_2' => $additional_image_link_2,
                        'additional_image_link_3' => $additional_image_link_3,
                        'additional_image_link_4' => $additional_image_link_4,
                        'additional_image_link_5' => $additional_image_link_5,
                        'additional_image_link_6' => $additional_image_link_6,
                        'custom_label_0' => $custom_label_0,
                        'custom_label_1' => $custom_label_1,
                        'custom_label_2' => $custom_label_2,
                        'custom_label_3' => $custom_label_3,
                        'custom_label_4' => $custom_label_4,
                        'product_type' => $category,
                        'gender' => $gender,
                        'age_group' => $ageGroup,
                        'availability' => $availability
        	       ));
                }
            }
            
            break;
            
            
    }
    
    
}
