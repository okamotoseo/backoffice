<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
// ini_set ("libxml_disable_entity_loader", false);
// header("Content-Type: text/html; charset=utf-8");
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../../Models/Products/AvailableProductsModel.php';
require_once $path .'/../../../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Prices/SalePriceModel.php';
require_once $path .'/../library/sdk-v2/CNovaApiLojistaV2.php';

require_once $path .'/../Models/Products/ProductsModel.php';
require_once $path .'/../Models/Products/ProductVariationsModel.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
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
//     pre($moduleConfig);
    \CNovaApiLojistaV2\client\Configuration::$apiKey['client_id'] = $moduleConfig['client_id'];
    \CNovaApiLojistaV2\client\Configuration::$apiKey['access_token'] = $moduleConfig['token'];
    
    $api_client = new \CNovaApiLojistaV2\client\ApiClient('https://sandbox-mktplace.viavarejo.com.br/api/v2');
    
    switch ($action){
    	
    	case "update_stock_price";
    	
    		$loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
    		$seller_items_api = new \CNovaApiLojistaV2\SellerItemsApi($api_client);
	    	$syncId =  logSyncStart($db, $storeId, "Skyhub", $action, "Atualização do estoque e preço skyhub", $request);
	    	
	    	$availableProducts = new AvailableProductsModel($db);
	    	$availableProducts->store_id = $storeId;
	    	$salePriceModel = new SalePriceModel($db, null, $storeId);
	    	$productModel = new ProductsModel($db);
	    	$productModel->store_id = $storeId;
	    	$productVariationsModel = new ProductVariationsModel($db);
	    	$productVariationsModel->store_id = $storeId;
	    	
	    	$productId = isset($_REQUEST["product_id"])  ? intval($_REQUEST["product_id"]) : NULL ;
	    	
	    	$dateFrom =  date("Y-m-d H:i:s",  strtotime("-2 hour") );
	    	
	    	$sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId} AND updated >= '{$dateFrom}'";
	    	if(isset($productId)){
	    		$sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
	    	}
	    	if(isset($_REQUEST['all'])){
	    		$sqlProduct = "SELECT * FROM available_products WHERE store_id = {$storeId} LIMIT 100";
	    	}
	    	$totalUpdated = 0;
	    	$queryAP = $db->query($sqlProduct);
	    	while($rowProduct = $queryAP->fetch(PDO::FETCH_ASSOC)){
	    		$sqlProducts = "SELECT * FROM module_viavarejo_products
	    		WHERE store_id = {$storeId} AND product_id = '{$rowProduct['id']}'";
	    		$queryProducts = $db->query($sqlProducts);
	    		$products = $queryProducts->fetchAll(PDO::FETCH_ASSOC);
	    		if(!empty($products)){
	    			 
	    			foreach($products as $kp => $product){
	    				 
	    				/**
	    				 * TODO: Criar regra para verificar se houve alteração nos valores q serão enviados
	    				 * Para isso é necessário salvar a quantidade enviada em cada interação para criar um parametro
	    				 * de verificação de alteração.
	    				 * @var Ambiguous $qtd
	    				 */
	    				$qtd = $rowProduct['quantity'] > 0 ? $rowProduct['quantity'] : 0 ;
	    				$salePriceModel->sku = trim($product['sku']);
	    				$salePriceModel->product_id = $product['product_id'];
	    				$salePriceModel->marketplace = "Viavarejo";
	    				$salePrice = $salePriceModel->getSalePrice();
	    				$stockPriceRel = $salePriceModel->getStockPriceRelacional();
	    				$salePrice = empty($stockPriceRel['price']) ? $salePrice : number_format($stockPriceRel['price'], 2) ;
	    				
	    				$qtd = empty($stockPriceRel['qty']) ? $qtd : $stockPriceRel['qty'] ;
	    				if ($rowProduct['blocked'] == "T"){
	    					$qtd = 0;
	    					echo "error|Produto Bloqueado...";
	    				}
	    				
	    				
	    				try {
	    					$prices = new \CNovaApiLojistaV2\model\Prices();
	    					$prices->default = $salePrice;
	    					$prices->offer = $salePrice;
	    					$res = $seller_items_api->putSellerItemPrices($product['sku'], $prices);
	    					pre($res);
	    					 
	    					$stock = new \CNovaApiLojistaV2\model\Stock();
	    					$stock->quantity = $qtd;
	    					$stock->cross_docking_time = !empty($rowProduct['cross_docking']) ? $rowProduct['cross_docking'] : 1 ;
	    					$res = $seller_items_api->putSellerItemStock($product['sku'], $stock);
	    					pre($res);
	    					 
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
	    			}
	    		}
	    				
	    	
	    	}
	    	logSyncEnd($db, $syncId, $totalUpdated);
    	
    	break;
    	
    	
    	
    	case "get_seller_items":
    		
    		$productModel = new ProductsModel($db);
    		$productModel->store_id = $storeId;
    		
    		$sellerItemsApi = new \CNovaApiLojistaV2\SellerItemsApi($api_client);
    		$_limit = 10;
    		$_offset = 0;
    		$site = 'CB';
    		
    		try {
    			$result = $sellerItemsApi->getSellerItems($site, $_offset, $_limit);
    			
    			foreach ($result->seller_items as $k => $item){
    				
    				$sql = "SELECT id, sku, parent_id FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$item['sku_seller_id']}'";
    				$query = $db->query($sql);
    				$products = $query->fetch(PDO::FETCH_ASSOC);
    				
    				if(isset($products['id'])){
    				
		    			$productModel->sku = $item['sku_seller_id'];
		    			$productModel->product_id = $products['id'];
		    			$productModel->parent_id = $products['parent_id'];
		    			$productModel->status = 'sent';
		    			$idProduct = $productModel->save();
		    			
		    			if(empty($idProduct)){
		    				echo "error|";
			    			pre($result);
		    			}else{
		    				echo "success|";
		    				pre($idProduct);
		    			}
	    			
    				}
    			
    			}
    			
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
    	/**
    	 * homologar
    	 */
    	case "get_seller_items_by_sku":
    		
    		$sellerItemsApi = new \CNovaApiLojistaV2\SellerItemsApi($api_client);
    		$site = 'EX';
    		$sku = 10011;//$_REQUEST['sku'];
    		
    		try {
    			$result = $sellerItemsApi->getSellerItemBySkuSellerId($sku, $site);
    			
    			echo "error|";
    			pre($result);
    			
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
    			
    		case "get_sites":
    			
    			
    			$sitesApi = new \CNovaApiLojistaV2\SitesApi($api_client);
    		
    			
    			try {

    				$result = $sitesApi->getSites();
    				pre($result);die;
    				
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
    			
    			/**
    			 * Operação para consulta da atualização massiva de status de produtos enviado.
    			 */
    		case "get_seller_itens_status":
    			$loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
    			$_offset = 0; 
    			$_limit = 10;
    			try {
    					
    				$res = $loads->getSellerItemsStatusUpdatingStatus($_offset, $_limit);
    				
    				pre($res);
    					
    			} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    				$errors = deserializeErrors($e->getResponseBody(), $api_client);
    				if ($errors != null) {
    					foreach ($errors->errors as $error) {
    						echo ($error->code . ' - ' . $error->message . "\n");
    					}
    				} else {
    					pre($res);
    					$res = $e->getMessage();
    					pre($res);
    				}
    			}
    			
    			break;
    			
    			
    			
    			/**
    			 * Operação para consulta do status do produto
    			 */
    			case "get_load_products":
    				$productModel = new ProductsModel($db);
    				$productModel->store_id = $storeId;
    				$loads = new \CNovaApiLojistaV2\LoadsApi($api_client);
    				$createdAt = ''; 
    				$status = 'PENDING';
    				$_offset = 0;
    				$_limit = 30;
    				try {
    						
    					$result = $loads->getProducts(null, $status, $_offset, $_limit);
    					
    					foreach ($result->skus as $k => $item){
    						$sql = "SELECT id, sku, parent_id FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$item->sku_seller['id']}'";
    						$query = $db->query($sql);
    						$products = $query->fetch(PDO::FETCH_ASSOC);
    					
    						if(isset($products['id'])){
    					
    							$productModel->sku = $item['sku_seller_id'];
    							$productModel->product_id = $products['id'];
    							$productModel->parent_id = $products['parent_id'];
    							$productModel->status = "".$item->status;
    							$idProduct = $productModel->save();
    							 
    							if(empty($idProduct)){
    								echo "error|";
    								pre($result);
    							}else{
    								echo "success|";
    								pre($idProduct);
    							}
    					
    						}
    						 
    					}
    						
    				} catch (\CNovaApiLojistaV2\client\ApiException $e) {
    					$errors = deserializeErrors($e->getResponseBody(), $api_client);
    					if ($errors != null) {
    						foreach ($errors->errors as $error) {
    							echo ($error->code . ' - ' . $error->message . "\n");
    						}
    					} else {
    						pre($result);
    						$res = $e->getMessage();
    						pre($res);
    					}
    				}
    				 
    			break;
    			
            			
    }
    
}