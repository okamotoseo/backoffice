<?php 

class PricesModel  extends MainModel
{
    
    public $id;

	public $store_id;
	
	public $sku;

	public $price;

	public $sale_price;
	
	public $promotion_price;
	
	public $records = 50;
	


	
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
	
	
	public function getMlSalePrice(){
	    
	    $selectPrice = "SELECT price, sale_price, promotion_price FROM `available_products`
	       WHERE store_id = {$this->store_id} AND `sku` LIKE '{$this->sku}'";
	    $query = $this->db->query($selectPrice);
	    $resStockPrice = $query->fetch(PDO::FETCH_ASSOC);
	    $this->sale_price = $resStockPrice['sale_price'];
	    
	    if($resStockPrice['promotion_price'] > 0 AND  $resStockPrice['promotion_price'] < $resStockPrice['sale_price']){
	        $this->sale_price = $resStockPrice['promotion_price'];
	    }
	    
	    $selectTax = "SELECT * FROM `ml_price_rules` WHERE store_id = {$this->store_id} ";
	    $query = $this->db->query($selectTax);
	    while($row = $query->fetch(PDO::FETCH_ASSOC)){
	        
	        $row['value_test'] = number_format((float)  $row['value_test'], 2, '.', '');
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
	                            if($row['rule'] == 'dominuir'){
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
	                            if($row['rule'] == 'dominuir'){
	                                $this->sale_price = $resStockPrice[$row['condition']];
	                                $this->sale_price -= $row['fixed_rate'];
	                                $this->sale_price -= ($this->sale_price * ($row['percentage_rate'] / 100));
	                            }
	                            
	                        }
	                        
	                        
	                        break;
	                }
	                
	                break;
	                
	        }
	    }
// 	    $this->sale_price = number_format(ceil($this->sale_price)-0.10, 2, '.', '');

	    $this->sale_price = number_format($this->sale_price, 2, '.', '');

// 	    $this->sale_price = number_format($this->sale_price, 2);

// 	    $this->sale_price = str_replace(",", ".", $this->sale_price);
// 	    pre($this->sale_price);die;
	    return $this->sale_price;
	}
	
}

?>