<?php
/**
 * Modelo para gerenciar dashboard
 *
 * @package 
 * @since 0.1
 */
class DashboardModel extends MainModel
{
	
	public $store_id;
	
    public $total_orders;
    
    public $total_revenues;
    
    public $total_customers;
    
    public $total_products;
    
    public $stores_sales = array();
    
    public $today;
    
    public function __construct( $db = false, $controller = null , $storeId = null) {
    	
        $this->db = $db;
        
        $this->controller = $controller;
        
        
        if(isset($this->controller)){
        
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
        }else{
            
            $this->store_id = isset($storeId) ? $storeId : null ;
            
        }
        $this->today =  date("Y-m-d");
        
        $this->total_orders = $this->getTotalOrdersDay();
        
        $this->total_revenues = $this->getTotalRevenues();
        
        $this->total_customers = $this->getTotalCutomers();
        
        $this->total_products = $this->GetTotalProducts();
        
    }
    
    
    
    public function addStoresSales($salesList){
        
        $this->stores_sales = $salesList;
        
        foreach($this->stores_sales as $key =>$storeSaleData){
            
            $this->total_orders += $storeSaleData['quantidade'];
            
            $this->total_revenues += $storeSaleData['total'];
            
        }
        
        return $this->stores_sales;
        
    }
    
    public function getLogSync(){
        $dateFrom =  date("Y-m-d H:i:s",  strtotime("-3 hour") );
        $today =  date("Y-m-d 00:00:00");
        $sql = "SELECT * FROM log_sync WHERE store_id = {$this->store_id} AND start >= '{$today}' ORDER BY id DESC LIMIT 50";
        $query = $this->db->query($sql);
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
//             pre($row);die;
            if(isset($row['result']) AND $row['result'] > 0){
                $parts = explode("/", $row['result']);
//                 if(end($parts) != 0){
                    $start = explode(" ", $row['start']);
                    $end = explode(" ", $row['end']);
                    
                    $end = isset($end[1]) ? $end[1] : "" ;
                    
                    echo "<div class='item'>
        			<p class='message'>
            				{$row['webservice']} - {$row['message']} - {$row['result']} - {$row['request']}
        					<small class='text-muted pull-right'><i class='fa fa-clock-o'></i> {$start[1]} ~ {$end}</small>
        					
        					
        			</p>
        		</div>";
//                 }
            }
        }
        
    }
    public function GetBestSellers($limit = null){
    	$limit = isset($limit) ? $limit : 5 ;
    	$sql = "SELECT SUM(order_items.Quantidade) AS qtd, SUM(order_items.PrecoVenda) AS fat, order_items.SKU, order_items.UrlImagem,
    	available_products.title, available_products.color, available_products.variation, available_products.id, available_products.thumbnail
    	FROM order_items 
    	LEFT JOIN available_products ON order_items.store_id = available_products.store_id AND available_products.sku  = order_items.SKU
    	WHERE order_items.store_id = {$this->store_id} GROUP BY SKU ORDER BY qtd DESC LIMIT 0 , {$limit}";
    	$query = $this->db->query($sql);
    	return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function GetBestSalesRecently($limit = null, $limitDays = null){
        $limit = isset($limit) ? $limit : 5 ;
        $limitDays = isset($limitDays) ? $limitDays : 90 ;
        
        
        $dateFrom =  date("Y-m-d H:i:s",  strtotime("-{$limitDays} day") ); //date("Y-m-d H:i:s", strtotime("-24 hour", strtotime("now")));
        $dateTo = date("Y-m-d H:i:s");
        
        $sql = "SELECT SUM(order_items.Quantidade) AS qtd, SUM(order_items.PrecoVenda) AS fat, order_items.SKU, order_items.UrlImagem,
    	available_products.title, available_products.color, available_products.variation, available_products.id, available_products.thumbnail
    	FROM order_items
    	LEFT JOIN available_products ON order_items.store_id = available_products.store_id AND available_products.sku  = order_items.SKU
    	WHERE order_items.store_id = {$this->store_id}   AND order_items.DataPedido BETWEEN  '{$dateFrom}' AND '{$dateTo}'
        GROUP BY SKU ORDER BY qtd DESC LIMIT 0 , {$limit}";
        $query = $this->db->query($sql);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function GetTotalProducts(){

    	$sql = 'SELECT count(*) as total FROM available_products WHERE  store_id = ?';
    	$query = $this->db->query($sql, array( $this->store_id));
    	$res = $query->fetch(PDO::FETCH_ASSOC);
    	return $res['total'];
    	
    }
    
    public function getTotalOrdersDay(){
        $sql = "SELECT COUNT(*) as total FROM orders WHERE store_id = {$this->store_id}  AND DataPedido >= '{$this->today}'";
        $query = $this->db->query($sql);
        $total = $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
    }
    
    public function getTotalCutomers(){
//         $sql = "SELECT COUNT(*) as total FROM customers WHERE store_id = {$this->store_id}  AND DataCriacao >= '{$this->today}'";
        $sql = "SELECT COUNT(*) as total FROM customers WHERE store_id = {$this->store_id}";
        $query = $this->db->query($sql);
        $total = $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
    }
    
    public function getTotalRevenues(){
        $sql = "SELECT sum(ValorPedido) revenues FROM orders WHERE store_id = {$this->store_id} AND DataPedido >= '{$this->today}'";
        $query = $this->db->query($sql);
        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        if(empty($total['revenues'])){
            return '0.00';
            
        }
        return $total['revenues'];
    }
    
    public function getOrdersDay(){
        $sql = "SELECT Marketplace, cast(DataPedido as date) as date, SUM(ValorPedido) as totalDay FROM orders
		WHERE store_id = {$this->store_id} AND DataPedido BETWEEN NOW() - INTERVAL 29 DAY AND NOW() 
		GROUP BY Marketplace, day(DataPedido) ORDER BY DataPedido DESC";
        $query = $this->db->query($sql);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getOrdersMonth(){
        $sql = "SELECT Marketplace, YEAR(DataPedido) as year, MONTHNAME(DataPedido) as monthname, SUM(ValorPedido) as totalpedido 
        FROM orders WHERE store_id = {$this->store_id} 
        GROUP BY YEAR(DataPedido), MONTH(DataPedido)";
        $query = $this->db->query($sql);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalOrdersMarketplaces(){
    	$sql = "SELECT count(id) as vendas, Marketplace from orders WHERE store_id = {$this->store_id}  GROUP BY Marketplace";
    	$query = $this->db->query($sql);
    	$resOrders =  $query->fetchAll(PDO::FETCH_ASSOC);
    	$result = array();
    	if(isset($resOrders)){
    		$total = 0;
    		
	    	foreach($resOrders as $key => $value){
	    		$total += $value['vendas'];
	    	}
	    	foreach($resOrders as $i => $value){
	    		$result["{$value['Marketplace']}"] = number_format(($value['vendas'] / $total) * 100, 2);
	    		 
	    	}
    	}
    	
    	return $result;
    }
    
    
//     public function getTicketMMarketplaces(){
//     	$sql = "SELECT count(id) as vendas, sum(ValorPedido) as total, Marketplace from orders WHERE store_id = {$this->store_id} GROUP BY Marketplace";
//     	$query = $this->db->query($sql);
//     	$resOrders =  $query->fetchAll(PDO::FETCH_ASSOC);
    	
//     	$result = array();
//     	if(isset($resOrders)){
//     		foreach($resOrders as $i => $value){
    			
//     			$result["{$value['Marketplace']}"] = number_format($value['total'] / $value['vendas'], 2);
    
//     		}
//     	}
//     	return $result;
//     }
} 