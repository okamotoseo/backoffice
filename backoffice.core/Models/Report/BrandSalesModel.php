<?php 


class BrandSalesModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
    public $DataPedido;
    
    public $DataPedidoAte;
    
    public $sku;
    
    public $marketplace;
    
    public $title;
    
    public $brand;
    
    public $today;
    
    public $group_by;
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->today =  date("Y-m-d");
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
        }
    }
    
    
    public function ValidateForm() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['filter-report-brand-sales'] ) ) {
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        $this->{$property} = $value;
                    }
                }else{
                	$required = array('DataPedido');
                	if( in_array($property, $required) ){
                		$this->field_error[$property] = "has-error";
                		$this->form_msg = "<div class='alert alert-danger alert-dismissable'>Por favor informar uma data inicial...</div>";
                		return false;
                	}
                }
            }
            return true;
            
        } else {
            
            $this->pagina_atual = in_array('Page', $this->parametros ) ? get_next($this->parametros, array_search('Page', $this->parametros)) : 1 ;
            
            $this->linha_inicial = ($this->pagina_atual -1) * QTDE_REGISTROS;
            
            return;
            
        }
        
    }
    

    
    public function TotalOrders(){
        
        $sql = "SELECT count(*) as total FROM `orders`  WHERE `store_id` = ?";
        
        $query = $this->db->query( $sql ,array( $this->store_id));

        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }
    
    
    
    public function GetOrdersFilter(){
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
        	
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "orders.{$key} = {$this->$key} AND ";break;
                    case 'DataPedido':
                        $dataPedido = implode('-', array_reverse(explode('/', $this->$key)));
                        $dataPedidoAte = !empty($this->DataPedidoAte) ? implode('-', array_reverse(explode('/', $this->DataPedidoAte))) : date('Y-m-d') ;
                        $where_fields .= "orders.{$key} BETWEEN '{$dataPedido} 00:00:00' AND '{$dataPedidoAte} 23:59:59' AND ";
                        break;
                        
                    case 'DataPedidoAte':
                        if(empty($this->DataPedido)){
                            $dataPedido = implode('-', array_reverse(explode('/', $this->$key)));
                            $where_fields .= "orders.DataPedido BETWEEN '00-00-2000' AND '{$this->$key}' AND ";
                        }
                        
                        break;
                    case 'Marketplace': 
                    	 $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND "; 
                    	break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        return $where_fields;
    }
    
    public function GetOrdersItemsFilter(){
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'nome': $where_fields .= "order_items.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'store_id': $where_fields .= "order_items.{$key} = {$this->$key} AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
    }
    public function GetGroupBy(){
        
        $groupBy = '';
        
        if(empty($this->group_by)){
            return $groupBy;
        }
        
        switch($this->group_by){
            case "sku": $groupBy = " GROUP BY available_products.parent_id "; break;
            case "DataPedido": $groupBy = " GROUP BY available_products.reference "; break;
            case "PrecoUnitario": $groupBy = " GROUP BY available_products.color "; break;
            case "brand": $groupBy = " GROUP BY available_products.brand "; break;
            case "ean": $groupBy = " GROUP BY available_products.ean "; break;
            default: $groupBy = ""; break;
            
        }
        
        return $groupBy;
        
        
    }
    
    public function GetBrandGroupSales()
    {
    	$where_fields = $this->GetOrdersFilter();
    	 
    	$where_items_fields = $this->GetOrdersItemsFilter();
    	

    	$sqlBrands = "SELECT available_products.brand FROM available_products WHERE available_products.store_id = {$this->store_id} 
    	 GROUP BY available_products.brand ";
    	if(!empty($this->brand)){
    		$sqlBrands = "SELECT available_products.brand FROM available_products WHERE available_products.store_id = {$this->store_id}
    		AND available_products.brand LIKE '{$this->brand}' GROUP BY available_products.brand ";
    	}
    	$queryBrands = $this->db->query($sqlBrands);
    	$resBrands = $queryBrands->fetchAll(PDO::FETCH_ASSOC);
    	$report = array();
//     	pre($resBrands);die;
    	foreach($resBrands as $key => $value){
	    	if(!empty($value['brand'])){
	    			
	    		$brand = $value['brand'];
	    			 
	    			$brand = trim($brand);
	    			 
	    			$sql = "SELECT orders.id, orders.Status, orders.fiscal_key, orders.DataPedido as OrderDataPedido, order_items.* FROM orders
	    			RIGHT JOIN order_items ON order_items.order_id = orders.id AND order_items.SKU IN (
                            SELECT available_products.sku FROM available_products 
                            WHERE available_products.store_id = ? AND available_products.brand LIKE ?
                    )
	    			WHERE {$where_fields} AND orders.Status != 'cancelled' AND orders.Status != 'canceled' AND orders.Status != 'pending'";
	    			$query = $this->db->query($sql, array($this->store_id, $brand));
	    			$itemSku = $query->fetchAll(PDO::FETCH_ASSOC);
	    			if(count($itemSku) > 0){
	    			    $report[$value['brand']]['total'] = 0;
	    			   
		    			foreach($itemSku as $key => $item){
		    	
		    				$queryAPId = $this->db->query("SELECT id as product_id FROM available_products 
                                WHERE store_id = {$this->store_id} AND sku LIKE '{$item['SKU']}'");
		    				$productId = $queryAPId->fetch(PDO::FETCH_ASSOC); 
		    				
		    				$sqlRelational = "SELECT * FROM `product_relational` 
		    				WHERE store_id = {$this->store_id} AND product_id = {$productId['product_id']} ";
		    				$resRelational = $this->db->query($sqlRelational);
		    				$relational = $resRelational->fetchAll(PDO::FETCH_ASSOC);
		    				
		    				if(!isset($relational[0]['product_relational_id'])){
		    					$relational[] = array('product_relational_id' => $productId['product_id']);
		    				}
		    				
		    				foreach ($relational as $r => $relVal){
		    					
			    				$queryAP = $this->db->query("SELECT id as product_id, cost, title, variation, color, sale_price, sku, quantity, qty_erp
			    						FROM available_products  WHERE store_id = {$this->store_id} AND id = {$relVal['product_relational_id']}");
			    				$products = $queryAP->fetch(PDO::FETCH_ASSOC);
			    				
			    				
			    				
			    				$qtd = $relVal['qtd'] > 0 ? $relVal['qtd'] * $item['Quantidade'] : $item['Quantidade'] ;
			    				
			    				$precoVenda = floatval($item['PrecoVenda']) * $qtd;
			    				
// 			    				$qtd = $item['Quantidade'] ;
			    				
// 			    				if(!empty($relVal['fixed_unit_price'])){
			    					
// 			    					$precoVenda = floatval($relVal['fixed_unit_price']);
// 			    				}
// 			    				if(!empty($relVal['dynamic_price']) && $relVal['dynamic_price'] == 'T'){
// 			    					$precoVenda = floatval($products['sale_price']);
// 			    				}
			    				
			    				
			    				$preco =   $precoVenda;
			    				
			    				if(!isset($report[$value['brand']][$item['SKU']]['quantity'])){
			    					
									if(!empty($products['title'])){
										$report[$value['brand']][$item['SKU']]['sku'] = $products['sku'];
				    					$report[$value['brand']][$item['SKU']]['title'] = $products['title'];
				    					$report[$value['brand']][$item['SKU']]['variation'] = $products['variation'];
				    					$report[$value['brand']][$item['SKU']]['color'] = $products['color'];
				    					$report[$value['brand']][$item['SKU']]['sale_price'] = $products['sale_price'];
				    					$report[$value['brand']][$item['SKU']]['available_stock'] = $products['quantity'];
				    					$report[$value['brand']][$item['SKU']]['qty_erp'] = $products['qty_erp'];
									}
			    					$report[$value['brand']][$item['SKU']]['quantity'] = $qtd;
			    					$report[$value['brand']][$item['SKU']]['order_items'] = $item['Quantidade'];
			    					$report[$value['brand']][$item['SKU']]['total'] = $preco;
			    					
			    				}else{
			    					$report[$value['brand']][$item['SKU']]['quantity'] = $qtd;
			    					$report[$value['brand']][$item['SKU']]['order_items'] += $item['Quantidade'];
			    					$report[$value['brand']][$item['SKU']]['total'] += $preco;
			    				}
			    				
			    				$productCost = $products['cost'] * $qtd;
			    				$report[$value['brand']][$item['SKU']]['cost'] = $productCost;
			    				
			    				
// 			    				if(!isset($report[$value['brand']][$item['SKU']]['quantity'])){
			    				    
// 			    				    if(!empty($products['title'])){
// 			    				        $report[$value['brand']][$productId['product_id']]['item_sku'] = $item['SKU'];
// 			    				        $report[$value['brand']][$productId['product_id']]['sku'] = $products['sku'];
// 			    				        $report[$value['brand']][$productId['product_id']]['title'] = $products['title'];
// 			    				        $report[$value['brand']][$productId['product_id']]['variation'] = $products['variation'];
// 			    				        $report[$value['brand']][$productId['product_id']]['color'] = $products['color'];
// 			    				        $report[$value['brand']][$productId['product_id']]['sale_price'] = $products['sale_price'];
// 			    				        $report[$value['brand']][$productId['product_id']]['available_stock'] = $products['quantity'];
// 			    				        $report[$value['brand']][$productId['product_id']]['qty_erp'] = $products['qty_erp'];
// 			    				        $report[$value['brand']][$productId['product_id']]['cost'] = $productCost;
// 			    				    }
// 			    				    $report[$value['brand']][$productId['product_id']]['quantity'] = $qtd;
// 			    				    $report[$value['brand']][$productId['product_id']]['order_items'] = $item['Quantidade'];
// 			    				    $report[$value['brand']][$productId['product_id']]['total'] = $preco;
			    				    
// 			    				}else{
// 			    				    $report[$value['brand']][$productId['product_id']]['quantity'] += $qtd;
// 			    				    $report[$value['brand']][$productId['product_id']]['order_items'] += $item['Quantidade'];
// 			    				    $report[$value['brand']][$productId['product_id']]['total'] += $preco;
// 			    				}
			    				$report[$value['brand']]['total'] += $preco;
		    				}
		    				
		    			}
	    			
	    			}
	    			 
	    	
	    	}
	    	
    	}
    	
    	return $report;
    }
    
//     public function GetBrandSales()
//     {
//     	$where_fields = $this->GetOrdersFilter();
    	
//     	$where_items_fields = $this->GetOrdersItemsFilter();
    	
    	
// 		if(!empty($this->brand)){
			
// 			$brands = explode(',', $this->brand);
			
// 	    	foreach($brands as $brand){
	    		
// 	    		$brand = trim($brand);
	    		
// 	    		$sql = "SELECT orders.id, orders.Status, orders.fiscal_key, orders.DataPedido as OrderDataPedido, order_items.* FROM orders
// 	    		RIGHT JOIN order_items ON order_items.order_id = orders.id AND
// 	    		order_items.SKU IN (
// 	    			SELECT available_products.sku FROM available_products WHERE available_products.store_id = {$this->store_id} AND 
// 	    			available_products.brand LIKE '{$brand}'
// 	    		)
// 	    		WHERE {$where_fields} AND orders.status != 'cancelled'";
	    		
	    		
// 	    		$query = $this->db->query($sql);
// 	    		$itemSku = $query->fetchAll(PDO::FETCH_ASSOC);
	    		
// 	    		foreach($itemSku as $key => $value){
	    				
// 	    			$queryAP = $this->db->query("SELECT id as product_id, cost, brand FROM available_products WHERE store_id = {$this->store_id}
// 	    			AND sku LIKE '{$value['SKU']}'");
// 	    			$products = $queryAP->fetch(PDO::FETCH_ASSOC);
// 	    			$itemSku[$key]['product_id'] = $products['product_id'];
// 	    			$itemSku[$key]['cost'] = $products['cost'];
// 	    			$itemSku[$key]['brand'] = $products['brand'];
	    				
// 	    		}
	    		
// 	    		$itemsSku[] = $itemSku;
	    		
// 	    	}
	    	
// 		}else{
			
			
// 			$sql = "SELECT orders.id, orders.Status, orders.DataPedido as OrderDataPedido, order_items.* FROM order_items
// 			LEFT JOIN orders ON order_items.order_id = orders.id
// 			WHERE {$where_items_fields} AND order_items.order_id IN (
// 			SELECT orders.id as order_id FROM orders WHERE {$where_fields} AND orders.status != 'cancelled' AND  orders.status != 'canceled'
// 			)";
// 			$query = $this->db->query($sql);
// 			$itemSku = $query->fetchAll(PDO::FETCH_ASSOC);
			
// 			foreach($itemSku as $key => $value){
				 
// 				$queryAP = $this->db->query("SELECT id as product_id, cost, brand FROM available_products WHERE store_id = {$this->store_id}
// 				AND sku LIKE '{$value['SKU']}'");
// 				$products = $queryAP->fetch(PDO::FETCH_ASSOC);
// 				$itemSku[$key]['product_id'] = $products['product_id'];
// 				$itemSku[$key]['cost'] = $products['cost'];
// 				$itemSku[$key]['brand'] = $products['brand'];
				 
// 			}
			
// 			$itemsSku[] = $itemSku;
			
// 		}
		
//     	$response = array();
//     	foreach($itemsSku as $i => $items){
// 	    	foreach($items as $sku => $item){
	    
// 	    		$queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
// 	    				array($item['store_id'], $item['order_id'], $item['id'])
// 	    				);
// 	    		while($rowItemAttr = $queryItemAttributes->fetch(PDO::FETCH_ASSOC)){
// 	    			$itemsSku[$i][$sku]['attributes'][]  = array(
// 	    					'name' => $rowItemAttr['Nome'],
// 	    					'value' => $rowItemAttr['Valor']
// 	    			);
	    			
// 	    		}
// // 	    		pre($itemsSku[$i][$sku]);die;
// 	    		$response[] = $itemsSku[$i][$sku];
//     		}
    		
    
//     	}
//     	return $response;
    
//     }
    
    
    
}
?>