
<?php
ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
// require_once $path .'/../../config.php';
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Modules/Adj/Class/class-Adj.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../../Sysemp/Class/class-PgConnection.php';
$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && !empty($_REQUEST["store_id"]) ? $_REQUEST["store_id"] : null ;
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
    
    $pg = new PgConnection($db, $storeId);
    
	switch($action){
	    
	    case 'update_qty_erp':
	        
	        $sqlVerify = "SELECT id, quantity, sku, parent_id, ean, price, sale_price, cost, qty_erp
                FROM available_products WHERE store_id = {$storeId}";
	        $verifyQuery = $db->query($sqlVerify);
	        
	        $products = $verifyQuery->fetchAll(PDO::FETCH_ASSOC);
	        foreach($products as $k => $product){
	            
	            if(isset($product['id'])){
    	            
        	        $sqlProductsSysemp = "SELECT produto.id_produto,  produto.custo, produto.codigo_auxiliar, produto_inventario.estoque,
        	                reserva.reserva, (produto_inventario.estoque - reserva.reserva) as saldo
        	                FROM produto LEFT JOIN produto_inventario ON produto.id_produto = produto_inventario.id_produto
        	                LEFT JOIN reserva ON produto.id_produto = reserva.id_produto
        	                WHERE produto.codigo_auxiliar LIKE '{$product['sku']}'";
        	        
        	        $queryProductSysemp = $pg->query($sqlProductsSysemp);
        	        
        	        $productSysemp = $queryProductSysemp->fetch(PDO::FETCH_ASSOC);
        	        
        	        if(isset($productSysemp['id_produto'])){
        	            
        	            $reserva = isset($productSysemp['reserva']) && $productSysemp['reserva']> 0 ? $productSysemp['reserva'] : 0 ;
        	            
        	            $sysempEstoque = isset($productSysemp['estoque']) && $productSysemp['estoque'] > 0 ? $productSysemp['estoque'] : 0 ;
        	            
        	            $sysempSaldo = isset($productSysemp['saldo']) &&  $productSysemp['saldo'] > 0 ? $productSysemp['saldo'] : $sysempEstoque - $reserva ;
        	            
        	            $logStock['available_products'] = $product;
        	            
        	            $logStock['sysemp'] = array(
        	                'saldo' => $sysempSaldo,
        	                'estoque' => $productSysemp['estoque'],
        	                'reserva' => $productSysemp['reserva']
        	            );
        	            
        	            $quantityErp =  $sysempSaldo > 0 ?  $sysempSaldo : 0 ;
        	            
        	            $data = array('qty_erp' => $quantityErp);
        	            
        	            $logStock['qty_update'] = $data;
        	            
        	            
        	            
//         	            if($quantityErp > 0 ){
//         	                echo 'Atualizado<br>';
        	               $queryUpdate = $db->update('available_products',
        	                   array('store_id', 'id'),
        	                   array($storeId, $product['id']),
        	                $data);
        	               
//         	            }else{
//         	                echo 'Não atualizado<br>';
//         	            }
        	            pre($logStock);
        	            
        	        }
        	        
    	        }
    	        
	        } 
	        break;
	        
	    /**
	     * This funcion increase stock of the products doesnt exists in adj erp relationship
	     * and exists in sysemp erp
	     * Choose function update_available_products to join stock each other
	     */
	    case 'update_available_products_fanlux_sysemp':
	        
	        $syncId =  logSyncStart($db, $storeId, "Sysemp", $action, "Atualização de produtos disponiveís", $request);
	        $imported=0;
	        
	        $sqlVerifyTmp = "SELECT id total FROM module_adj_products_tmp WHERE store_id = {$storeId} ";
	        $query = $db->query($sqlVerifyTmp);
	        $reg = $query->rowCount();
	        if($reg < 1){
	        	echo "Sem registros de importação na tabela";
	        	return ;
	        }
	        
	        $sql = "SELECT id, sku, cost, title, quantity FROM available_products WHERE store_id = {$storeId} AND sku NOT IN (
                SELECT produtoId as sku FROM module_adj_products_tmp WHERE store_id = {$storeId} 
            ) AND id NOT IN(
    			SELECT product_id as id FROM product_relational WHERE store_id = {$storeId}
    		)";
	        
// 	        $sql = "SELECT id, sku, cost, title, quantity FROM available_products WHERE store_id = {$storeId} AND sku LIKE '1578'";
	        $countTotal = 0;
	        
	        $query = $db->query($sql);
	        $availableProduct = $query->fetchAll(PDO::FETCH_ASSOC);
	        
	        foreach($availableProduct as $key => $value){
	        	
	        	$logStock = array();
	        	
	            $sku = trim($value['sku']);
	            
	            $quantity = $value['quantity'] > 0 ? $value['quantity'] : 0 ;
	            
	            $logStock['sysplace'] = array('sku' => $sku, 'quantity' => $quantity);
	            
	            if($storeId == 4){
	                
	                $sqlProductsSysemp = "SELECT produto.id_produto,  produto.custo, produto.codigo_auxiliar, produto_inventario.estoque,
	                reserva.reserva, (produto_inventario.estoque - reserva.reserva) as saldo
	                FROM produto LEFT JOIN produto_inventario ON produto.id_produto = produto_inventario.id_produto
	                LEFT JOIN reserva ON produto.id_produto = reserva.id_produto
	                WHERE produto.codigo_auxiliar LIKE '{$sku}'";
	                
	                $queryProductSysemp = $pg->query($sqlProductsSysemp);
	                
	                $productSysemp = $queryProductSysemp->fetch(PDO::FETCH_ASSOC);
	                 
	                if(isset($productSysemp['id_produto'])){ 
	                
	                	$reserva = isset($productSysemp['reserva']) && $productSysemp['reserva']> 0 ? $productSysemp['reserva'] : 0 ;
	                
	                	$sysempEstoque = isset($productSysemp['estoque']) && $productSysemp['estoque'] > 0 ? $productSysemp['estoque'] : 0 ;
	                
	                	$sysempSaldo = isset($productSysemp['saldo']) &&  $productSysemp['saldo'] > 0 ? $productSysemp['saldo'] : $sysempEstoque - $reserva ;
	                
// 	                	if($productSysemp['codigo_auxiliar'] == '1056' ){
// 	                	  $sysempSaldo = 500;
// 	                	}
	                	$logStock['sysemp'] = array(
	                			'saldo' => $sysempSaldo,
	                			'estoque' => $productSysemp['estoque'],
	                			'reserva' => $productSysemp['reserva']
	                	);
	                	 
	                	$quantity =  $sysempSaldo > 0 ?  $sysempSaldo : 0 ;
	                	
	                    $countTotal += $quantity;
	                   
	                    $cost =  $productSysemp['custo'] > 0 ? number_format($productSysemp['custo'], 2) : $value['cost'] ;
	                   
	                    $cost = $cost > 0 ? $cost : 0 ;
// 	                    $data = array('quantity' => $quantity, 'cost' => $cost);
	                    $data = array('quantity' => $quantity);
    			   	    
    			   	    $logStock['update_available_products'] = $data;
    			   	    
    			   	    
    			   	    $queryUpdate = $db->update('available_products', 
    			   	        array('store_id', 'id'), 
    			   	        array($storeId, $value['id']), 
    			   	        $data);
    			   	    
    			   	    
    			   	    if($queryUpdate->rowCount()){
    			   	       
    			   	       $queryUpdateAP = $db->update('available_products',
    			   	           array('store_id','id'),
    			   	           array($storeId, $value['id']),
    			   	           array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
    			   	           );
    			   	       $sqlRelational = "SELECT product_id FROM product_relational WHERE store_id = {$storeId} AND product_relational_id = {$value['id']} ";
    			   	       $queryRelational = $db->query($sqlRelational);
    			   	       
    			   	       while($productRelational =  $queryRelational->fetch(PDO::FETCH_ASSOC)){
    			   	           $queryUpdateAP =  $db->update('available_products',
    			   	           array('store_id','id'),
    			   	           array($storeId, $productRelational['product_id']),
    			   	           array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
    			   	           );
    			   	       }
    			   	       
    			   	       $imported++;
    			   	       
    			   	       $dataLog['update_available_products_fanlux_sysemp'] = $logStock;
    			   	       
//     			   	       pre($dataLog);
    			   	       
    			   	       $db->insert('products_log', array(
    			   	       		'store_id' => $storeId,
    			   	       		'product_id' => $value['id'],
    			   	       		'description' => 'Atualização do Estoque Importado Sysemp',
    			   	       		'user' => $request,
    			   	       		'created' => date('Y-m-d H:i:s'),
    			   	       		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
    			   	       ));
    			   	   }
	                    
	                }else{
	                	/**
	                	 * block and update to zero all products doesnt exists in both erp`s
	                	 */
	                	$sqlVerify = "SELECT id, quantity, sku, parent_id, ean, price, sale_price, cost FROM available_products 
	                	WHERE store_id = {$storeId} AND sku LIKE '{$sku}' ";
	                	$verifyQuery = $db->query($sqlVerify);
	                	$verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	                	if(isset($verify['id'])){
	                		
// 	                		$data = array('quantity' => 0, 'blocked' => 'T', 'last_message' => 'Blocked and updated to zero all Products doesnt exists in both ERP');
	                	    $data = array('quantity' => 0, 'last_message' => 'Blocked and updated to zero all Products doesnt exists in both ERP');
	                		$logStock['update_available_products'] = array('sku' => 'Produto não existe tmp', 'update_available_products' => $data);
// 	                		pre($verify);
// 	                		pre($logStock);
	                		$queryUpdate = $db->update('available_products',
	                				array('store_id', 'id'),
	                				array($storeId, $verify['id']),
	                				$data);
	                		 
	                		if($queryUpdate->rowCount()){
	                		
	                			$queryUpdateAP = $db->update('available_products',
	                					array('store_id','id'),
	                					array($storeId, $verify['id']),
	                					array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
	                					);
	                			$sqlRelational = "SELECT product_id FROM product_relational WHERE store_id = {$storeId} AND product_relational_id = {$verify['id']} ";
	                			$queryRelational = $db->query($sqlRelational);
	                		
	                			while($productRelational =  $queryRelational->fetch(PDO::FETCH_ASSOC)){
	                				$queryUpdateAP =  $db->update('available_products',
	                						array('store_id','id'),
	                						array($storeId, $productRelational['product_id']),
	                						array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
	                						);
	                			}
	                		
	                			$dataLog['update_available_products_fanlux_sysemp'] = $logStock;
// 	                			pre($dataLog);
	                			$db->insert('products_log', array(
	                					'store_id' => $storeId,
	                					'product_id' => $verify['id'],
	                					'description' => 'Atualização do Estoque Removido',
	                					'user' => $request,
	                					'created' => date('Y-m-d H:i:s'),
	                					'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	                			));
	                		}
	                		
	                	}
	                }
	                
	            }
	            
	        }
	        
	        logSyncEnd($db, $syncId, $countTotal);
	        
	        break;
	    
		case 'update_available_products':
		    
		   $syncId =  logSyncStart($db, $storeId, "Adj", $action, "Atualização de produtos disponiveís", $request);
		   $imported = 0;
		   $query = $db->query("SELECT * FROM module_adj_products_tmp");
		   foreach($query->fetchAll(PDO::FETCH_ASSOC) as $key => $value){
		   		
		   		$logStock = array();
		   		
			   	$sku = trim($value['produtoId']);
			   	
			   	$title = addslashes($value['descricao']);
			   	$logStock['sku'][] = $sku;
			   	$quantity = $value['estoque'] > 0 ? $value['estoque'] : 0 ;
			   	
			   	$logStock['adj'] = array(
			   			'estoque' => $quantity
			   	);
			   	
			   	 
			   	if($storeId == 4){
			   	
			   		$sqlProductsSysemp = "SELECT produto.id_produto, produto.codigo_auxiliar, produto_inventario.estoque,
			   		reserva.reserva, (produto_inventario.estoque - reserva.reserva) as saldo
			   		FROM produto
			   		LEFT JOIN produto_inventario ON produto.id_produto = produto_inventario.id_produto
			   		LEFT JOIN reserva ON produto.id_produto = reserva.id_produto
			   		WHERE produto.codigo_auxiliar LIKE '{$sku}'";
			   		$queryProductSysemp = $pg->query($sqlProductsSysemp);
			   		$productSysemp = $queryProductSysemp->fetch(PDO::FETCH_ASSOC);
			   	
			   		if(isset($productSysemp['id_produto'])){
			   			 
			   			$reserva = isset($productSysemp['reserva']) ? $productSysemp['reserva'] : 0 ;
			   			 
			   			$sysempEstoque = isset($productSysemp['estoque']) ? $productSysemp['estoque'] : 0 ;
			   			 
			   			$sysempSaldo = isset($productSysemp['saldo']) ? $productSysemp['saldo'] : 0 ;
// 			   			if($productSysemp['codigo_auxiliar'] == '1056' ){
// 			   			    $sysempEstoque = 500;
// 			   			}
			   			$qtySum = $quantity + $sysempEstoque;
			   			$qtySum = $qtySum - $reserva;
			   			$logStock['sysemp'] = array(
			   					'saldo' => $sysempSaldo,
			   					'estoque' => $productSysemp['estoque'],
			   					'reserva' => $productSysemp['reserva']
			   			);
			   			
			   			pre(array($sku, $logStock['sysemp']));
			   			 
			   		}
			   	}

			   	
			   	$addStock =  isset($qtySum) ? $qtySum : $quantity ;
			   	unset($qtySum);
			   	$quantity =  $addStock > 0 ? $addStock : 0 ;
			   	$espessura = isset($value["espessura"]) ? $value["espessura"] * 100 : '';
			   	$largura =  isset($value["largura"]) ? $value["largura"] * 100 : '';
			   	$comprimento =  isset($value["comprimento"]) ? $value["comprimento"] * 100 : '';
			   	
// 			   	$skuBlocked = array('13', '92', '144', '183', '186', '189', '198', '278', '279', '302', '303', '304', '305',
// 			   			'392', '393', '394', '395', '396', '397', '398', '399', '400', '401', '402', '403', '404', '405',
// 			   			'516', '530', '534', '567', '638', '643', '644', '645', '731', '767', '769', '775', '860', '861',
// 			   			'862', '863', '864', '865', '866', '1048', '1049', '1052', '1207', '1208', '1834', '1849', '1850',
// 			   			'2048', '2054', '2055', '2079', '2080', '2081', '2082', '2272', '2431', '3011', '3012', '3013', '3014',
// 			   			'3015', '3016', '3017', '3018', '3019', '3020', '3021', '3022', '3023', '3024', '3057', '3058', '3059',
// 			   			'3060', '3062', '3063', '3064', '3067', '3068', '3075', '3076', '3077', '3135', '3137', '3141', '3185',
// 			   			'3186', '3188', '3189', '3191', '3193', '3194', '3196', '3197', '3198', '3199', '3201', '3202', '3274',
// 			   			'3275', '3276', '3310', '3313', '3314', '3315', '3316', '3317', '3391', '3407', '3409', '3411', '3414',
// 			   			'3426', '3463', '3468', '3500', '3501', '3502', '3518', '3519', '3523', '3547', '3624', '3633', '3924',
// 			   			'4266', '4370', '4371', '4372', '4373', '4374', '4375', '4376', '4377', '4378', '4472', '4473', '4474',
// 			   			'4475', '4498', '4734', '4735', '4736', '10080', '10081', '10083', '10085', '5733', '5734', '5735', '5736',
// 			   			'3155', '3415', '3066', '4271', '5934', '102030', '203040', '304050', '304060', '304070'
// 			   	);
// 			   	if(in_array($sku, $skuBlocked)){
// 			   		pre(array('blocked' => $sku));
// 			   		$quantity = 0 ;
// 			   	}
			   	
			    $sqlVerify = "SELECT id, quantity, sku, parent_id, ean, price, sale_price, cost FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}' ";
			    $verifyQuery = $db->query($sqlVerify);
			   	$verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
			   
				if(!isset($verify['id'])){
				    
					$data = array(
			     			
			     			'account_id' => 4,
							'store_id' => $storeId,
							'sku' => $sku,
							'parent_id' => $sku,
							'title' => $title,
							'quantity' => $quantity,
// 					        'qty_erp' => isset($sysempSaldo) && $sysempSaldo > 0 ? $sysempSaldo : 0,
							'cost' => $value["vlrCustoVenda"],
							'price' => $value["vlrVenda"],
							'sale_price' => $value["vlrVenda"],
							'weight' => $value["pesoBruto"],
							'height' => $espessura,
							'width' => $largura,
							'length' => $comprimento,
							'ean' => $value["codigoEan13"],
							'created' => date("Y-m-d H:i:s"),
							'updated' => date("Y-m-d H:i:s"),
							'extra_information' => $value["embMultiplos"],
							'flag' => 1
			     			
			     	);
					$logStock['add_available_products'] = $data;
					$res = $db->insert('available_products', $data);
			     	
			     	if($res){
			     	    $imported++;
			     	    $id = $db->last_id;
			     	    if(!empty($id)){
		     	    		$dataLog['insert_import_available_products_adj'] = $logStock;
			     	    
			     	    	$db->insert('products_log', array(
			     	    			'store_id' => $storeId,
			     	    			'product_id' => $id,
			     	    			'description' => 'Novo Produto Importado do ERP ADJ',
			     	    			'user' => $request,
			     	    			'created' => date('Y-m-d H:i:s'),
			     	    			'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
			     	    	));
			     	    }
			     	}
			   	
				}
				
				if(isset($verify['id'])){
					
// 					if(empty($verify['parent_id'])){
// 						$data = array('parent_id' => $sku);
// 						$db->update('available_products', 'id', $verify['id'], $data);
						
// 					}
				    
				    $ean = trim($value["codigoEan13"]);
				    //se existir ean adj
					if(!empty($ean)){
					    //e não existir ean sysplace atualiza o ean
					    if(empty($verify['ean'])){
					    	$data = array(
        					    'quantity' => $quantity,
// 					    	    'qty_erp' => isset($sysempSaldo) && $sysempSaldo > 0 ? $sysempSaldo : 0,
        					    'price' => trim($value["vlrVenda"]),
        					    'cost' => trim($value["vlrCustoVenda"]),
        					    'ean' => trim($ean)
        					);
					    	$data['extra_information'] = $value["embMultiplos"];
        					$queryUpdate = $db->update('available_products', 'id', $verify['id'], $data);
        					
					    }else{
					    	$data = array(
					            'quantity' => $quantity,
// 					    	    'qty_erp' => isset($sysempSaldo) && $sysempSaldo > 0 ? $sysempSaldo : 0,
					    	    'cost' => trim($value["vlrCustoVenda"]),
					            'price' => trim($value["vlrVenda"])
					            
					        );
					    	$data['extra_information'] = $value["embMultiplos"];
					        $queryUpdate = $db->update('available_products', 'id', $verify['id'], $data);
					    }
    					
					}else{
					    
						$data = array(
					        'quantity' => $quantity,
// 						    'qty_erp' => isset($sysempSaldo) && $sysempSaldo > 0 ? $sysempSaldo : 0,
						    'cost' => trim($value["vlrCustoVenda"]),
					        'price' => trim($value["vlrVenda"])
					    );
						
						$data['extra_information'] = $value["embMultiplos"];
						$queryUpdate = $db->update('available_products', 'id', $verify['id'], $data);
					}
					
					$logStock['update_available_products'] = $data;
					
					if($queryUpdate->rowCount()){
					    
					    $db->update('available_products',
					        array('store_id','id'),
					        array($storeId, $verify['id']),
					        array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
					        );
					    
					    $imported++;
					   
					    $dataLog['update_import_available_products_adj'] = $logStock ; 
					    
					    $db->insert('products_log', array(
					    		'store_id' => $storeId,
					    		'product_id' => $verify['id'],
					    		'description' => 'Atualização do Produto Importado do ERP ADJ',
					    		'user' => $request,
					    		'created' => date('Y-m-d H:i:s'),
					    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
					    ));
					    
					    $sqlRelational = "SELECT product_id FROM product_relational WHERE store_id = {$storeId}
                                    AND product_relational_id = {$verify['id']} ";
					    $queryRelational = $db->query($sqlRelational);
					    while($productRelational =  $queryRelational->fetch(PDO::FETCH_ASSOC)){
					        $db->update('available_products',
					            array('store_id','id'),
					            array($storeId, $productRelational['product_id']),
					            array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
					            );
					        
					    }
					    
					}
					
				}
				pre($logStock);
			   	
		   	}
		   	logSyncEnd($db, $syncId, $imported);
		   	
	   		if($imported == 0){
	   		
	   			$message = "ALERTA!!! Atualização estoque JR Retornou Zero";
	   		
	   			notifyUsers($message);
	   		
	   		}
		   	
			break;
	    
		case 'import_products_adj':
			
		    $syncId =  logSyncStart($db, $storeId, "Adj", $action, "Importação de produtos ADJ tmp", $request);
		    
		    $updated = 0;
		    
		    $params = array_filter(array(
		        '@xdata.type' => 'XData.Default.DTOProduto',
// 					'descricao' => "LUMINARIA",
// 					'dataUpdate' => "2018-09-01"
		    ));
		    
			$adj = new Adj($db, $storeId);
			
			$products = $adj->Products($params);
			
			if(!empty($products['error'])){
				$message = "ALERTA!!! Sistema Ageu Offline {$products['error']}";
				notifyUsers(',atendimento@fanlux.com.br, tamyris_falcao@hotmail.com', $message);
			}
			
			$countBlocked = 0 ;
			
			if(isset($products['body']['value'])){
				
				$db->query("DELETE FROM module_adj_products_tmp WHERE store_id = {$storeId}");
				$estoqueTotal = 0;
				$skuBlocked = array(
						'6916', '6917', '6920', '6921', '6922', '6923', '6924', '6925', '6928', '6929', '6930', '6931', '6932', '6933', '6934', '6935', '6936', '6937', '6938', '6939', '6918', '6919', '6926', '6927', '7912', '7913', '7914', '7915', '7916', '7917', '7918', '7919',
						'6195', '5792', '5105', '5105',  '4739', '4637', '4634', '4632', '4630', '4628', '4627', '4622', '200815', '200811', '200809',  '1764', '1764', '1764', '1754',
						'13', '92', '144', '183', '186', '189', '198', '278', '279',  '393', '394', '395', '396', '397', '398', '399', '400', '401', '402', '403', '404', '405', '516', '530', '534', '567', '638', '643', '644', '645', '731', '767', '769', '775', '860', '861', '862', '863', '864', '865', '866', '1048', '1049', '1052', '1207', '1208', '1834', '1849', '1850', '2048', '2054', '2055', '2079', '2080', '2081', '2082', '2272', 
	    // 				    '2431', '302', '303', '304', '305', '392','10085', '3409',
				    '3011', '3012', '3013', '3014', '3015', '3016', '3017', '3018', '3019', '3020', '3021', '3022', '3023', '3024', '3057', '3058', '3059', '3060', '3062', '3063', '3064', '3067', '3068', '3075', '3076', '3077', '3135', '3137', '3141', '3185', '3186', '3188', '3189', '3191', '3193', '3194', '3196', '3197', '3198', '3199', '3201', '3202', '3274', '3275', '3276', '3310', '3313', '3314', '3315', '3316', '3317', '3391', '3407', '3411', '3414', '3426', '3463', '3468', '3500', '3501', '3502', '3518', '3519', '3523', '3547', '3624', '3633', '3924', '4266', '4370', '4371', '4372', '4373', '4374', '4375', '4376', '4377', '4378', '4472', '4473', '4474', '4475', '4498', '4734', '4735', '4736', '10080', '10081', '10083', '5733', '5734', '5735', '5736', '3155', '3415', '3066', '4271', '5934', '102030', '203040', '304050', '304060', '304070',
						'6268', '6269', '6266', '6265', '6264', '6246', '6243', '5173', '5759', '5761', '5762', '6245', '6267',
				        '894'
				);
				
				//Willians liberou
				//'498', Umidificador 
				//'1859', Ventisol 1 Metro
				
				foreach($products['body']['value'] as $key => $product){
					
						$product = (array) $product;
						
						if(isset($product['produtoId'])){
							$productId = $product['produtoId'];
							
						    $saldo = isset($product['Estoque']['vlrSaldo']) && $product['Estoque']['vlrSaldo'] >= 0 ? $product['Estoque']['vlrSaldo'] : 0 ;
					        
						    $reservado = isset($product['Estoque']['vlrReservado']) && $product['Estoque']['vlrReservado'] >= 0  ? $product['Estoque']['vlrReservado'] : 0 ;
					        
					        $saldoReserva = ($saldo - $reservado);
					        
					        $quantity = $saldoReserva >= 0 ? $saldoReserva : 0 ;
					        
					        if(in_array($productId, $skuBlocked)){
					        	$quantity = 0 ;
					        }
					        
					        $estoqueTotal += $quantity;
					        
					        pre($product);
					       
			 				$query = $db->insert('module_adj_products_tmp', array(
						 				'id' => $product['$id'],
						 				'produtoId' =>	$product['produtoId'],
						 				'codigoEan13' =>	$product['codigoEan13'],
						 				'descricao' =>	$product['descricao'],
						 				'miniDescricao' =>	$product['miniDescricao'],
			 				            'estoque' =>  !in_array($product['produtoId'], $skuBlocked) ? $quantity : 0 , 
						 				'qtdeEstoqueMinimo' =>	$product['qtdeEstoqueMinimo'], 
						 				'dataUltimaCompra' =>   $product['dataUltimaCompra'],
						 				'dataUltimaVenda' =>   $product['dataUltimaVenda'], 
			 				            'vlrCustoVenda' =>   $product['vlrCustoVenda'],
						 				'vlrVenda' =>   $product['vlrVenda'],
						 				'largura' =>   $product['largura'],
						 				'espessura' =>   $product['espessura'],
						 				'comprimento' =>   $product['comprimento'],
						 				'mt3' =>   $product['mt3'],
						 				'pesoBruto' =>   $product['pesoBruto'],
						 				'pesoLiquido' =>   $product['pesoLiquido'],
						 				'origemProduto' =>   $product['origemProduto'],
						 				'stAtual' =>   $product['stAtual'],
						 				'dataUpdate' =>   $product['dataUpdate'],
						 				'totalizarQtdePedido' =>   $product['totalizarQtdePedido'],
			 							'store_id' => $storeId,
			 							'embMultiplos' => $product['embMultiplos']
			 						)
			 				);
			 				if($query){
			 				    $updated++;
			 				}
			 				
						
// 		 				unset($product);
	 				
						}
	 				
				}
				
// 				$message = "Sistema Ageu Online !!! Foram atualizados {$updated} Produtos";
// 				notifyUsers(',atendimento@fanlux.com.br, tamyris_falcao@hotmail.com', $message);
			
			}
			pre($estoqueTotal);
			logSyncEnd($db, $syncId, $estoqueTotal);
			break;
		    
			
			
		case 'update_available_products_csv':
		    
		   $syncId =  logSyncStart($db, $storeId, "JR", $action, "Atualização de produtos disponiveís CSV", $request);
		   $imported = 0;
		   $sumGrandTotal = 0 ;
		   
		   $sql = "SELECT BEM_ID, VLRSALDO, VLRRESERVADO, (VLRSALDO - VLRRESERVADO) as estoque
		   FROM stock_jr WHERE BEM_ID IN (SELECT sku as BEM_ID FROM  available_products WHERE store_id = {$storeId}) AND BEM_ID = '1764'";
		   $query = $db->query($sql);
		   foreach($query->fetchAll(PDO::FETCH_ASSOC) as $key => $value){
		       
		   		$logStock = array();
		   		$sku = trim($value['BEM_ID']);
			   	$quantity = $value['estoque'] > 0 ? $value['estoque'] : 0 ;
			   	$logStock['adj'] = array(
			   			'SALDO' => $quantity,
			   			'VLRSALDO' => $value['VLRSALDO'],
			   			'VLRRESERVADO' => $value['VLRRESERVADO']
			   	);
			   	$qtySum = 0;
			   	if($storeId == 4){
    			   	$sqlProductsSysemp = "SELECT produto.id_produto, produto.codigo_auxiliar, produto_inventario.estoque, 
    			   	reserva.reserva, (produto_inventario.estoque - reserva.reserva) as saldo
    			   	FROM produto JOIN produto_inventario ON produto.id_produto = produto_inventario.id_produto
    			   	JOIN reserva ON produto.id_produto = reserva.id_produto
    			   	WHERE produto.codigo_auxiliar LIKE '{$sku}'";
    			   	$queryProductSysemp = $pg->query($sqlProductsSysemp);
    			   	$productSysemp = $queryProductSysemp->fetch(PDO::FETCH_ASSOC);
    			   
    			   	if(isset($productSysemp['estoque'])){
    			   	    
    			   	    $reserva = isset($productSysemp['reserva']) ? $productSysemp['reserva'] : 0 ;
    			   	    $sysempEstoque = isset($productSysemp['estoque']) ? $productSysemp['estoque'] : 0 ;
    			   	    $sysempSaldo = isset($productSysemp['saldo']) ? $productSysemp['saldo'] : 0 ;
    			   	    $qtySum = $quantity + $sysempEstoque;
    			   	    $qtySum = $qtySum - $reserva;
    			   	    $logStock['sysemp'] = array( 
    			   	    		'saldo' => $sysempSaldo, 
    			   	    		'estoque' => $productSysemp['estoque'], 
    			   	    		'reserva' => $productSysemp['reserva']
    			   	    );
    			   	    
    			   	}
			   	}
			   	
			   	$addStock =  isset($qtySum) ? $qtySum : $quantity ;
			   	
			   	$addStock =  $addStock > 0 ? $addStock : 0 ;
			   	
			    $sqlVerify = "SELECT id, quantity, sku, ean, price, sale_price, cost 
			    FROM available_products WHERE store_id = {$storeId} AND sku LIKE '{$sku}' LIMIT 1";
			    $verifyQuery = $db->query($sqlVerify);
			   	$verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
			   
				if(isset($verify['id'])){
					
				    $sumGrandTotal += $addStock;
				    
					$data = array('quantity' => $addStock);
					
					$logStock['add-stock'][] = $sku;
					$logStock['add-stock'] = $data;
					
					$logStock['stock-before'] = array('last-quantity' => $verify['quantity']);
					
// 					pre($logStock);
					
					$queryUpdate = $db->update('available_products', 'id', $verify['id'], $data);
						
					if($queryUpdate->rowCount()){
					    
					    $db->update('available_products',
					        array('store_id','id'),
					        array($storeId, $verify['id']),
					        array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
					        );
					    
					    $imported++;
					    	
					    $dataLog['update_import_available_products_adj_csv'] = $logStock;
					    
					    $db->insert('products_log', array(
					    		'store_id' => $storeId,
					    		'product_id' => $verify['id'],
					    		'description' => 'Atualização do Produto Importado do CSV JR ADJ',
					    		'user' => $request,
					    		'created' => date('Y-m-d H:i:s'),
					    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
					    ));
					    
					    $sqlRelational = "SELECT product_id FROM product_relational WHERE store_id = {$storeId}
                                    AND product_relational_id = {$verify['id']} ";
					    $queryRelational = $db->query($sqlRelational);
					    while($productRelational =  $queryRelational->fetch(PDO::FETCH_ASSOC)){
					        $db->update('available_products',
					            array('store_id','id'),
					            array($storeId, $productRelational['product_id']),
					            array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
					            );
					    }
					    
					}
					
				}
			   	
		   	}
		   	logSyncEnd($db, $syncId, $imported);
			break;
	}
	
	

}