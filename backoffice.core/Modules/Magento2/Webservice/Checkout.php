<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/CategoryModel.php';
// require_once $path .'/../../../Models/Orders/OrdersModel.php';

require_once $path .'/../Class/class-Soap.php';
require_once $path .'/../Models/Orders/OrdersModel.php';
require_once $path .'/../Models/Catalog/ProductsModel.php';
require_once $path .'/../Models/Customers/CustomerEntity.php';
require_once $path .'/../Models/Customers/CustomerModel.php';
require_once $path .'/../Models/Customers/AddressEntity.php';
require_once $path .'/../Models/Customers/AddressModel.php';
require_once $path .'/../Models/Catalog/DirectoryModel.php';
require_once $path .'/../Models/Checkout/CartModel.php';


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
    if(isset($_SERVER ['argv'] [3])){
        $paramAccountId = explode ( "=", $_SERVER ['argv'] [3] );
        $accountId = $paramAccountId [0] == "account_id" ? $paramAccountId [1] : null;
    }
    
    $request = "System";
    
}

if(isset($storeId)){
    $db = new DbConnection();
    
    $moduleConfig = getModuleConfig($db, $storeId, 5);

	switch($action){
	    
	    case "export_order":
	        $imported = 0;
	        $syncId =  logSyncStart($db, $storeId, "Ecommerce", $action, "Exportação de pedidos", $request);
	        
	        $ordersModel = new OrdersModel($db, null, $storeId);
	        $customerModel = new CustomerModel($db, null, $storeId);
	        $addressModel = new AddressModel($db, null, $storeId);
	        $directoryModel = new DirectoryModel($db, null, $storeId);
	        $cartModel = new CartModel($db, null, $storeId);
	        $productsModel = new ProductsModel($db, null, $storeId);
	        
	        $ordersModel->id = isset($_REQUEST["order_id"])  ? intval($_REQUEST["order_id"]) : null ;//1870756999 //2034436459
	        
	        $orders = $ordersModel->ExportOrderDetails();
	        
	        foreach($orders as $key => $order){
	            $order['Marketplace'] = 'Mercadolivre';
	            $cartModel->storeView = strtolower(strtolower($order['Marketplace']));
// 	            $cartModel->storeView = 'store_default';
// 	            $cartModel->storeView = 'store_mercadolivre';
	            $cartId = $cartModel->shoppingCartCreate();
// 	            pre($cartId);die;
	            $directoryModel->uf = $order['customer']['Estado'];
	            $region = $directoryModel->GetRegionIdFromUf();
	            $cpfCnpj =  formatarCpfCnpj($order['customer']['CPFCNPJ']);
	            $nameParts = explode(" ", $order['customer']['Nome']);
// 	            $customerModel->getFilterCustomer(array("taxvat" => $cpfCnpj));
// // 	            $customerModel->getFilterCustomer(array("taxvat" => '304.561.308-02'));
// 	            $customerEntity = $customerModel->customerCustomerList();
// 	            $customerEntity = isset($customerEntity[0]) ? $customerEntity[0] : $customerEntity ;
// 	            if(!empty($customerEntity->taxvat)){
// // 	               $customerEntity->taxvat = getNumbers($customerEntity->taxvat);
// 	               $customerEntity->mode = "customer";
// 	               $cartModel->shoppingCartCustomerSet();
// 	            }
	            
	            if(empty($customerEntity)){
	                
                    $customerEntity = new CustomerEntity();
                    $customerEntity->email = $order['customer']['Email'];
                    $customerEntity->firstname = $nameParts[0];
                    $customerEntity->lastname = end($nameParts);
                    $customerEntity->taxvat = $order['customer']['CPFCNPJ'];
                    $customerEntity->password = $order['customer']['CPFCNPJ'];
                    
                }
                if(isset($moduleConfig['create_customer'])){
                    $customerEntity->mode = "customer";
                    $customeId = $customerModel->customerCustomerCreate($customerEntity);
                    
                }
                
                $cartModel->customer = (array)$customerEntity;
                
                foreach($order['items'] as $key => $item){
                    
                    $productsModel->filters['sku'] = $item['SKU'];
                    $products = $productsModel->catalogProductList();
                    if(isset($products[0])){
                        
                        $product = (array)$products[0];
                        
//                         $productsModel->storeView = strtolower($order['Marketplace']);

                        $productsModel->storeView = strtolower($order['Marketplace']);
    //                         $productsModel->product_id = $product['product_id'];

                        $productsModel->product_id = $product['sku'];
                        
                        $products = $productsModel->catalogProductUpdate(array('website' => array(1), "price" => $item['PrecoVenda']));
                        
                        $product['qty'] = $item['Quantidade'];
                        
                        $cartModel->product = $product;
                        
                        $resAddCart = $cartModel->shoppingCartProductAdd();
//                         pre($resAddCart);
                    }
                    
                }
               
                $bairro = !empty($order['customer']['Bairro']) ? $order['customer']['Bairro'] : "Centro" ;
                $complemento = !empty($order['customer']['Complemento']) ? $order['customer']['Complemento'] : "" ;
                $phone = formatPhone($order['customer']['Telefone']);
                $cep = !empty($order['customer']['CEP']) ? formataCep($order['customer']['CEP']) : '000000000';
                $city =  !empty($order['customer']['Cidade']) ? $order['customer']['Cidade'] : 'nao informado';
                $rua = !empty($order['customer']['Endereco']) ? $order['customer']['Endereco'] : 'nao informado';
                $number = !empty($order['customer']['Numero']) ? $order['customer']['Numero'] : 'nao informado';
                
                $street = utf8_encode($rua."\n".$number."\n".substr($complemento, 0, 25)."\n".$bairro);
                
                
                $address = array(
                   array(
                       'mode' => 'shipping',
                       'firstname' => $nameParts[0],
                       'lastname' => end($nameParts),
                       'street' => $street,
                       'city' => $city,
                       'region_id' => $region->region_id,
                       'region' => $region->name, 
                       'telephone' => $phone,
                       'postcode' => $cep,
                       'country_id' => 'BR',
                       'is_default_shipping' => 1,
                       'is_default_billing' => 0
                   ),
                   array(
                       'mode' => 'billing',
                       'firstname' => $nameParts[0],
                       'lastname' => end($nameParts),
                       'street' => $street,
                       'city' => $city,
                       'region_id' => $region->region_id,
                       'region' => $region->name,
                       'telephone' => $phone,
                       'postcode' => $cep,
                       'country_id' => 'BR',
                       'is_default_shipping' => 0,
                       'is_default_billing' => 1
                   )
                );
                
//                 pre($address)
                $cartModel->marketplace = strtolower(strtolower($order['Marketplace']));
//                 $cartModel->marketplace = 'store_mercadolivre';
                
                $res = $cartModel->shoppingCartCustomerAddresses($address);
                
                
                $shippingMethod = $cartModel->GetShippingMethodCodeFromMarketplace();
                
                $cartModel->shoppingCartShippingMethod();
                
                $paymentMethod = $cartModel->GetPaymentMethodCodeFromMarketplace();
//                 pre($paymentMethod);
                $cartModel->payment =  array(
                   'po_number' => null,
                   'method' => $paymentMethod->code,
                   'cc_cid' => null,
                   'cc_owner' => null,
                   'cc_number' => null,
                   'cc_type' => null,
                   'cc_exp_year' => null,
                   'cc_exp_month' => null
                );
                
                $cartModel->shoppingCartPaymentMethod();
                $orderId = $cartModel->shoppingCartOrder();
                pre($orderId);
                if(isset($orderId->faultstring)){
                    
                    $db->update("orders",
                        array('store_id', 'id'),
                        array($storeId, $order['id']),
                        array('sent' => 'T', 'error' => date("d/m/Y  H:i:s")." | Erro ao exportar pedido para Onbi Loja {$storeId} ".$orderId->faultstring)
                        );
                    
                }else{
                    
                    $db->update("orders",
                        array('store_id', 'id'),
                        array($storeId, $order['id']),
                        array('sent' => 'T', 'error' => null)
                        );
                    
                    $imported++;
                    
                }
                
	        
	        }
	        
	        logSyncEnd($db, $syncId, $imported);
	        
	        break;
	    
	    
	}
	    
	
}

