<?php

class SalePriceModel  extends MainModel
{
    
    public $id;
    
    public $store_id;
    
    public $sku;
    
    public $product_id;
    
    public $marketplace;
    
    public $priceType = 'sale_price';
    
    public $price;
    
    public $sale_price;
    
    public $promotion_price;
    
    public $request = 'System';
    
    
    public function __construct($db = false,  $controller = null, $storeId = null)
    {
        $this->db = $db;
        
        $this->store_id = $storeId;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
            $this->request = $this->controller->userdata['name'];
            
        }
        
        
    } 
    
    
    public function getSalePrice(){ 
        
        $selectPrice = "SELECT id, price, sale_price, promotion_price, category FROM `available_products`
	       WHERE store_id = {$this->store_id} AND `sku` LIKE '{$this->sku}'";
        $query = $this->db->query($selectPrice);
        $resStockPrice = $query->fetch(PDO::FETCH_ASSOC);
        

        
        /**
         * 
         * Chose de price will be processed 
         * @var SalePriceModel $sale_price
         */
        
        $this->sale_price = $resStockPrice['sale_price'];
        
        switch($this->priceType){
        	case 'price': $this->sale_price = !empty($resStockPrice['price']) ? $resStockPrice['price'] : $resStockPrice['sale_price']; break;
        	case 'sale_price': $this->sale_price = !empty($resStockPrice['sale_price']) ? $resStockPrice['sale_price'] : $resStockPrice['price']; break;
        }
        
//         $this->product_id = $resStockPrice['id'];
//         if($resStockPrice['promotion_price'] > 0 AND  $resStockPrice['promotion_price'] < $this->sale_price){
//         	$this->sale_price = $resStockPrice['promotion_price'];
//         }

        $marketplace = isset($this->marketplace) ? $this->marketplace : 'Todos' ;
        
        $selectTax = "SELECT * FROM `price_rules` WHERE store_id = {$this->store_id} AND marketplace LIKE '{$marketplace}' 
        OR store_id = {$this->store_id} AND marketplace LIKE 'Todos' ";
        $query = $this->db->query($selectTax);
        $prices = $query->fetchAll(PDO::FETCH_ASSOC);
        if(isset($prices[0])){
            
            foreach($prices as $k => $row){
                
                $row['value_test'] = number_format( $row['value_test'], 2, '.', '');
                switch($row['condition']){
                    
                    case 'sale_price':
                        switch($row['operator']){
                            case 'maior':
                                
                                if($resStockPrice[$row['condition']] > $row['value_test']){
                                    if($row['rule'] == 'aumentar'){
                                        $this->sale_price = $resStockPrice[$row['condition']];
                                        $this->sale_price += $row['fixed_rate'];
                                        $this->sale_price += ($this->sale_price * ($row['percentage_rate'] / 100));
                                    }
                                    if($row['rule'] == 'diminuir'){
                                        $this->sale_price = $resStockPrice[$row['condition']];
                                        $this->sale_price -= $row['fixed_rate'];
                                        $this->sale_price -= ($this->sale_price * ($row['percentage_rate'] / 100));
                                    }
                                    
                                }
                                
                                break;
                            case 'menor':
                                if($resStockPrice[$row['condition']] < $row['value_test']){
                                    if($row['rule'] == 'aumentar'){
                                        $this->sale_price = $resStockPrice[$row['condition']];
                                        $this->sale_price += $row['fixed_rate'];
                                        $this->sale_price += ($this->sale_price * ($row['percentage_rate'] / 100));
                                    }
                                    if($row['rule'] == 'diminuir'){
                                        $this->sale_price = $resStockPrice[$row['condition']];
                                        $this->sale_price -= $row['fixed_rate'];
                                        $this->sale_price -= ($this->sale_price * ($row['percentage_rate'] / 100));
                                    }
                                    
                                }
                                break;
                        }
                        
                        break;
                        
                    case 'category':
                    	
                    	switch($row['operator']){
                    		case 'maior':
                    	
                    			if($resStockPrice['category'] == $row['value_test']){
                    				if($row['rule'] == 'aumentar'){
                    					$this->sale_price = $this->sale_price;
                    					$this->sale_price += $row['fixed_rate'];
                    					$this->sale_price += ($this->sale_price * ($row['percentage_rate'] / 100));
                    				}
                    				if($row['rule'] == 'diminuir'){
                    					$this->sale_price = $this->sale_price;
                    					$this->sale_price -= $row['fixed_rate'];
                    					$this->sale_price -= ($this->sale_price * ($row['percentage_rate'] / 100));
                    				}
                    	
                    			}
                    	
                    			break;
                    		case 'menor':
                    	
                    			if($resStockPrice['category'] == $row['value_test']){
                    				if($row['rule'] == 'aumentar'){
                    					$this->sale_price = $this->sale_price;
                    					$this->sale_price += $row['fixed_rate'];
                    					$this->sale_price += ($this->sale_price * ($row['percentage_rate'] / 100));
                    				}
                    				if($row['rule'] == 'diminuir'){
                    					$this->sale_price = $this->sale_price;
                    					$this->sale_price -= $row['fixed_rate'];
                    					$this->sale_price -= ($this->sale_price * ($row['percentage_rate'] / 100));
                    				}
                    	
                    			}
                    	
                    	
                    			break;
                    	}
                    	
                    	break;
                }
            }
            
        }else{ 
            //TODO:: cpegar preos customizados 
        }
        $this->sale_price = (float) str_replace(",", ".", $this->sale_price);
        $this->sale_price = number_format($this->sale_price, 2, '.', '');
        $this->sale_price =  (double)filter_var($this->sale_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return $this->sale_price;
    }
    
    public function getStockPriceRelacional(){
        
        if(!isset($this->product_id)){
            return array();
        }
        $idsteste = array();
        $count = 0;
        $priceRel = 0.00;
        $sqlRelational = "SELECT * FROM product_relational WHERE store_id = {$this->store_id} AND product_id = {$this->product_id} ";
        $queryRelational = $this->db->query($sqlRelational);
        $rowCount = $queryRelational->rowCount();
        $idsteste[$this->product_id][] = array('row_count' => $rowCount);
        if($rowCount){
        	
	        while($productRelational =  $queryRelational->fetch(PDO::FETCH_ASSOC)){
	            
	            $selectQtdRel = "SELECT id, sku, title, quantity, sale_price, promotion_price, blocked
	                                            FROM `available_products` WHERE store_id = {$this->store_id} AND `id` = {$productRelational['product_relational_id']}";
	            $queryQtdRel = $this->db->query($selectQtdRel);
	            $resStockPriceRel = $queryQtdRel->fetch(PDO::FETCH_ASSOC);
	            $idsteste[$this->product_id][] = array('product_relational_id' => $productRelational['product_relational_id']);
	            if(isset($stock)){
	            	
	            	$productRelationalQtd = intval($productRelational['qtd']) > 0 ? intval($productRelational['qtd']) : 1 ;
	            	$possibleQty = $resStockPriceRel['quantity'] > 0 ? ($resStockPriceRel['quantity'] / $productRelationalQtd) : 0;
	            	$stock =  $stock > $possibleQty ? floor($possibleQty) : $stock  ;
	            	$idsteste[$this->product_id][] = array('stock_set' => $stock, 'possibleQty' => 
	            			$possibleQty, 'stock' => $stock, 'productRelational[qtd]' => $productRelational['qtd']);
	            	
	            }else{
	            	$productRelationalQtd = intval($productRelational['qtd']) > 0 ? intval($productRelational['qtd']) : 1 ;
	                $possibleQty = $resStockPriceRel['quantity'] > 0 ? ($resStockPriceRel['quantity'] /$productRelationalQtd) : 0;
	                $stock = $possibleQty > 0 ? floor($possibleQty) : 0;
	                $idsteste[$this->product_id][] = array('stock_notset' => $stock, 'possibleQty' => $possibleQty, 'stock' => $stock, 'productRelational[qtd]' => $productRelational['qtd']);
	            }
	            
	            $this->sku = $resStockPriceRel['sku'];
	            
	            if($productRelational['dynamic_price'] == 'T'){
	            	
	                $price = $this->getSalePrice();
	                
	                if(isset($productRelational['discount_fixed']) && !empty($productRelational['discount_fixed'])){
	                    $price = $productRelational['discount_fixed'] > 0 ? ($price - $productRelational['discount_fixed']) : $price;
	                }
	                if(isset($productRelational['discount_percent']) && !empty($productRelational['discount_percent'])){
	                    $price = $productRelational['discount_percent'] > 0 ? ($price -  ( $price * $productRelational['discount_percent'] ) / 100 ) : $price;
	                }
	                $priceRel += $price * intval($productRelational['qtd']) ;
	                
	            }
	            if($productRelational['dynamic_price'] == 'F'){
	            	
	                $price = $productRelational['fixed_unit_price'];
	                $priceRel += $price * intval($productRelational['qtd']) ;
	                
	            }
	            $count++;
	        }
	        
	        
	        
	        If($count > 0){
	        	if(isset($stock)){
		        	$data = array(
		        			'quantity' => $stock,
		        	);
		        	$queryUpdate = $this->db->update('available_products', 
		        			array('store_id', 'id'), 
		        			array($this->store_id, $this->product_id), 
		        			$data);
		        	$idsteste[$this->product_id][] = array('data' => $data);
		        	if($queryUpdate->rowCount()){
		        		$this->db->update('available_products',
		        				array('store_id', 'id'),
		        				array($this->store_id, $this->product_id),
		        				array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
		        				);
		        
		        		$dataLog['update_available_products_sale_price_relational'] = array(
		        				'after' => $idsteste,
		        				'before' => array(" {$stock} KIT {$this->product_id} {$count}")
		        		);
		        		$this->db->insert('products_log', array(
		        				'store_id' => $this->store_id,
		        				'product_id' => $this->product_id,
		        				'description' => "Atualização do Estoque do KIT {$this->product_id}",
		        				'user' => $this->request,
		        				'created' => date('Y-m-d H:i:s'),
		        				'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
		        		));
		        	}
	        	
	        	}
        	
	        }
        
        }
        
        $stock = isset($stock) && $stock > 0 ? $stock : 0;
        
        $priceRel = (float) str_replace(",", ".", $priceRel);
        
        $priceRel = number_format($priceRel, 2, '.', '');
        
        $priceRel = isset($priceRel) && $priceRel > 0 ? $priceRel : 0;
        
        $priceRel =  (double)filter_var($priceRel, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        return array("price" => $priceRel, "qty" => $stock, "itens" => $rowCount);
    }
    
}

?>