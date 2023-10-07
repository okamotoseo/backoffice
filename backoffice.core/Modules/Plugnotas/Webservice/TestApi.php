<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
// ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../library/sdk-v2/CNovaApiLojistaV2.php';
require_once $path .'/../Class/class-Viavarejo.php';

// require_once $path .'/../Models/Products/ProductsModel.php';
// require_once $path .'/../Models/Products/ProductVariationsModel.php';
// require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$pedidoId = isset($_REQUEST["pedido_id"]) && $_REQUEST["pedido_id"] != "" ? $_REQUEST["pedido_id"] : null ;
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 10);
    $vivarejoApi = new Viavarejo($moduleConfig);
    $api_client = $vivarejoApi->api_client;

    switch ($action){
    	case 'approve_order':
    		 
    		$orders_api = new  \CNovaApiLojistaV2\OrdersApi($api_client);
    		 
    		try {
    			
    			$res = $orders_api->putSellerItemsStatusApproved('1194295901');
    			pre($res);
    			echo 123;
    			
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
    				$res = $e->getMessage();
    				pre($res);
    			}
    		}
    	
    		break;
    	case 'export_order':
    	
    		$orders_api = new  \CNovaApiLojistaV2\OrdersApi($api_client);
    		 
    		try {
    			$orderTest = '{
					  "site": "EX",
					  "items": [
					    {
					      "skuSellerId": "998",
					      "name": "0000005213085",
					      "salePrice": 119.00,
					      "quantity": 1
					    }
					  ],
					  "customer": {
					    "name": "Gustavo Rubin",
					    "gender": "Male",
					    "documentNumber": "30230230215",
					    "type": "PF",
					    "email": "gustavo@gustavo00.com",
					    "bornAt": "1983-07-07",
					    "billing": {
					      "address": "Rua xyz",
					      "number": "231",
					      "complement": "casa",
					      "quarter": "1",
					      "reference": "lala",
					      "city": "Sao Paulo",
					      "state": "SP",
					      "countryId": "BR",
					      "zipCode": "13022234",
					      "recipientName": "Maria"
					    },
					    "phones": {
					      "mobile": "11202039283",
					      "home": "11092308934",
					      "office": "1128373823"
					    }
					  }
					}';
    			$orderTest = json_encode(json_decode($orderTest), JSON_PRETTY_PRINT);
    			 pre($orderTest);
//     			$order = new \CNovaApiLojistaV2\model\Order();
//     			//     			$order->id = $data["id"];
//     			//     			$order->order_site_id = $data["order_site_id"];
//     			$order->site = 'PF';
//     			//     			$order->payment_type = $data["payment_type"];
//     			//     			$order->purchased_at = $data["purchased_at"];
//     			//     			$order->approved_at = $data["approved_at"];
//     			//     			$order->updated_at = $data["updated_at"];
//     			//     			$order->status = $data["status"];
//     			//     			$order->total_amount = $data["total_amount"];
//     			//     			$order->total_discount_amount = $data["total_discount_amount"];
//     			//     			$order->freight = $data["freight"];
//     			//     			$order->shipping = $data["shipping"];
//     			//     			$order->trackings = $data["trackings"];
//     			//     			$order->seller = $data["seller"];
    			 
//     			$orderCustomer = new \CNovaApiLojistaV2\model\Customer();
//     			// 	    			$orderCustomer->id = $data["id"];
//     			$orderCustomer->name = 'Willians Okamoto';
//     			$orderCustomer->document_number = '30269241809';
//     			$orderCustomer->type = 'PF';
//     			$orderCustomer->created_at = $data["created_at"];
//     			$orderCustomer->email = 'dev.sysplace123@gmail.com';
//     			$orderCustomer->birth_date = '1984-01-17';
    	
//     			$orderCustomerPhone = new \CNovaApiLojistaV2\model\Phone();
//     			$orderCustomerPhone->number = '05514996393560';
//     			$orderCustomerPhone->type = 'mobile';
//     			$orderCustomer->phones = $orderCustomerPhone;
    	
//     			$orderCustomerBilling = new \CNovaApiLojistaV2\model\BillingAddress();
//     			$orderCustomerBilling->address = 'Rua de endereço';
//     			$orderCustomerBilling->number = '111';
//     			$orderCustomerBilling->complement = 'Casa';
//     			$orderCustomerBilling->quarter = 'Segundo';
//     			$orderCustomerBilling->reference = 'Unimar';
//     			$orderCustomerBilling->city = 'Marilia';
//     			$orderCustomerBilling->state = 'SP';
//     			$orderCustomerBilling->country_id = 'BR';
//     			$orderCustomerBilling->zip_code = '17526330';
//     			$orderCustomer->billing = $orderCustomerBilling;
    			 
//     			$order->customer = $orderCustomer;
    			 
//     			$orderItem = new \CNovaApiLojistaV2\model\OrderItem();
//     			$orderItem->sku_seller_id = '123456';
//     			$orderItem->name = 'Teste';
//     			$orderItem->sale_price = 99.90;
    	
//     			$order->items = $orderItem;
    			$res = $orders_api->postOrder($orderTest);
    			 
    		} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    			$errors = deserializeErrors($e->getResponseBody(), $api_client);
    			if ($errors != null) {
    				foreach ($errors->errors as $error) {
    					echo ($error->code . ' - ' . $error->message . "\n");
    				}
    			} else {
    				$res = $e->getMessage();
    				pre($res);
    			}
    		}
    	
    		break;
    		
    		
        case "add":
            pre($api_client);
            
            $salePriceModel = new SalePriceModel($db, null, $storeId);
            
            $loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
            
            $sqlAP = "SELECT ap.*, pd.html_description, pd.set_attribute_id FROM `available_products` ap
				JOIN product_description pd ON ap.parent_id = pd.id
				WHERE quantity > 0 AND xml = 'T' AND blocked = 'F' LIMIT 15";
            
            $sqlAP = "SELECT * FROM available_products WHERE store_id = {$storeId} AND quantity > 0 AND blocked = 'F' LIMIT 15";
            
            $query = $db->query($sqlAP);
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
//                 pre($row);die;
                $product = new \CNovaApiLojistaV2\model\Product();
                
                $product->sku_seller_id = $row['sku'];
                $product->product_seller_id = $row['parent_id'];
                $product->title = $row['title'];
                $product->description = $row['description'];
                $product->brand = $row['brand'];
                $product->categories = array ('Teste>API');
                
                $files = array();
                $images = getUrlImageFromParentId($db, $storeId, $row['parent_id']);
                
                foreach ($images as $key => $file){
                    if(!empty($file)){
                        $files[] = $file;
                    }
                }
                $product->images = $files;
                
                $salePriceModel->sku = $row['sku'];
                $salePriceModel->marketplace = "Viavarejo";
                $salePrice = $salePriceModel->getSalePrice();
                
                $product->price = $salePrice;
                
                $stockQuantity = 20;
                
                $stock = new \CNovaApiLojistaV2\model\ProductLoadStock();
                $stock->quantity = $stockQuantity;
                $stock->cross_docking_time = 1;
                $product->stock = $stock;
                
                $dimensions = new \CNovaApiLojistaV2\model\Dimensions();
                //                 $measures = getProductMeasures($storeId, $row['sku']);
                $dimensions->weight = $row['weight'];
                $dimensions->length = $row['length'];
                $dimensions->width = $row['width'];
                $dimensions->height = $row['height'];
                $product->dimensions = $dimensions;
                $product_attr_color = array();
                if(isset($row['color']) && !empty($row['color'])){
                    $product_attr_color = new \CNovaApiLojistaV2\model\ProductAttribute();
                    $product_attr_color->name = 'Cor';
                    $product_attr_color->value = $row['color'];
                }
                $product_attr_size = array();
                if(isset($row['variation']) && !empty($row['variation'])){
                    if(isset($row['variation_type']) && !empty($row['variation_type'])){
                        $product_attr_size = new \CNovaApiLojistaV2\model\ProductAttribute();
                        $product_attr_size->name = ucfirst($row['variation_type']);
                        $product_attr_size->value = $row['variation'];
                    }
                }
                
                $product->attributes =  array($product_attr_color, $product_attr_size);
                // 			$product->attributes =  array($product_attr);
                
//                 // Adiciona o novo produto na lista a ser enviada
                $products[] = $product; 
                // 		var_dump($products);
            }

            
//             echo "<pre>";
//             print_r($products);
//             echo "</pre>";
            
            try {
                $res = $loads->postProducts($products);
                echo 123;
                pre(json_decode($res));
                
            } catch (\CNovaApiLojistaV2\client\ApiException $e) {
                $errors = deserializeErrors($e->getResponseBody(), $api_client);
                if ($errors != null) {
                    foreach ($errors->errors as $error) {
                        echo ($error->code . ' - ' . $error->message . "\n");
                    }
                } else {
                    
                    $res = $e->getMessage();
                    pre($res);
                }
            }
            
            break; 
    }
    
}
            
//         case "list_errors":
//             $loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
//             try {
//                 $sku = $_REQUEST['sku'];
//                 $get_products_response = $loads->getProduct($sku);
//                 echo "<pre>";
//                 print_r($get_products_response);
//                 echo "</pre>";
                
//             } catch (\CNovaApiLojistaV2\client\ApiException $e) {
                
//                 $errors = deserializeErrors($e->getResponseBody(), $api_client);
                
//                 if ($errors != null) {
//                     foreach ($errors->errors as $error) {
//                         echo ($error->code . ' - ' . $error->message . "\n");
//                     }
//                 } else {
//                     echo ($e->getMessage());
//                 }
                
//             }
//             break;
//         case "list":
//             $loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
//             try {
                
//                 $get_products_response = $loads->getProducts(null, null, 0, 100);
                
//                 echo "<pre>";
//                 print_r($get_products_response);
//                 echo "</pre>";
                
//             } catch (\CNovaApiLojistaV2\client\ApiException $e) {
                
//                 $errors = deserializeErrors($e->getResponseBody(), $api_client);
                
//                 if ($errors != null) {
//                     foreach ($errors->errors as $error) {
//                         echo ($error->code . ' - ' . $error->message . "\n");
//                     }
//                 } else {
//                     echo ($e->getMessage());
//                 }
                
//             }
//             break;
//         case "delete":
//             $loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
//             try {
                
//                 // 			$get_products_response = $loads->getProducts(null, null, 0, 100);
//                 $get_products_response = $loads->getProduct("00766434");
//                 // 			var_dump($get_products_response);
//                 echo "<pre>";
//                 print_r($get_products_response);
//                 echo "</pre>";
                
//             } catch (\CNovaApiLojistaV2\client\ApiException $e) {
                
//                 $errors = deserializeErrors($e->getResponseBody(), $api_client);
                
//                 if ($errors != null) {
//                     foreach ($errors->errors as $error) {
//                         echo ($error->code . ' - ' . $error->message . "\n");
//                     }
//                 } else {
//                     echo ($e->getMessage());
//                 }
                
//             }
//             break;
            
//         case "add_old":
//             $loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
//             $product = new \CNovaApiLojistaV2\model\Product();
//             $product->sku_seller_id = 'CEL_LGG4';
//             $product->title = 'Produto de testes LG G4';
//             $product->description = '<h2>O novo produto de testes</h2>, LG G4';
//             $product->brand = 'LG';
//             $product->categories = array (
//                 'Teste>API'
//             );
//             $product->images = array (
//                 'http://img.g.org/img1.jpeg'
//             );
            
//             $price = new \CNovaApiLojistaV2\model\ProductLoadPrices();
//             $price->default = 1999.0;
//             $price->offer = 1799.0;
            
//             $product->price = $price;
            
//             $stock = new \CNovaApiLojistaV2\model\ProductLoadStock();
            
//             $stock->quantity = 100;
//             $stock->cross_docking_time = 0;
            
//             $product->stock = $stock;
            
//             $dimensions = new \CNovaApiLojistaV2\model\Dimensions();
//             $dimensions->weight = 10;
//             $dimensions->length = 10;
//             $dimensions->width = 10;
//             $dimensions->height = 10;
            
//             $product->dimensions = $dimensions;
            
//             $product_attr = new \CNovaApiLojistaV2\model\ProductAttribute();
//             $product_attr->name = 'cor';
//             $product_attr->value = 'Verde';
            
//             $product->attributes =  array($product_attr);
            
//             // Adiciona o novo produto na lista a ser enviada
//             $products = array($product);
//             // 		var_dump($products);
//             try {
//                 // Envia a carga de produtos
//                 $res = $loads->postProducts($products);
//                 echo "<pre>";
//                 print_r($res);
//                 echo "</pre>";
                
//             } catch (\CNovaApiLojistaV2\client\ApiException $e) {
//                 $errors = deserializeErrors($e->getResponseBody(), $api_client);
//                 if ($errors != null) {
//                     foreach ($errors->errors as $error) {
//                         echo ($error->code . ' - ' . $error->message . "\n");
//                     }
//                 } else {
//                     echo ($e->getMessage());
//                 }
//             }
//             break;
            
            
//     }
    
    function deserializeErrors($errorsJson, $apiClient) {
        
        $errors = null;
        
        try {
            $errors = $apiClient->deserialize(json_decode($errorsJson), 'Errors');
        } catch (\Exception $e) {}
        
        return $errors;
        
    }
            
