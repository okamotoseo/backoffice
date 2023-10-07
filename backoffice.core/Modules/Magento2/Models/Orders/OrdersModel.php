<?php 

class OrdersModel extends MainModel
{


    public $id;
    
    public $store_id;
    
    public $customer_id;
    
    public $PedidoId;
    
    Public $Nome;
    
    Public $Email;
    
    Public $Telefone;
    
    public $Bairro;
    
    public $Cep;
    
    public $Cidade;
    
    public $Complemento;
    
    public $DataPedido;
    
    public $DataPedidoAte;
    
    public $Endereco;
    
    public $Estado;
    
    public $Ip;
    
    public $NomeDestino;
    
    public $Numero;
    
    public $FormaPagamento;
    
    public $Parcelas;
    
    public $PrazoEnvio;
    
    public $RG;
    
    public $Status;
    
    public $Subtotal;
    
    public $ValorFrete;
    
    public $ValorParcelas;
    
    public $ValorPedido;
    
    public $ValorPedidoAte;
    
    public $ValorCupomDesconto;
    
    public $AnaliseFraude;
    
    public $Marketplace;
    
    public $shipping_id;




	
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
	
	
	public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
            	if(!empty($value)){
	                if(property_exists($this,$property)){
	                   $this->{$property} = $value;
	                }
            	}
                
            }
            
            return true;
            
        } else {
        	
            return;
            
        }
        
	    
	}
	
	
	public function ExportOrderDetails()
	{
	    $sql = "SELECT * FROM `orders`  WHERE `store_id` = ?  AND sent = 'F' AND status != 'pending' 
        AND status != 'cancelled'  AND status != 'canceled' ORDER BY id DESC LIMIT 1;";
	    
	    if (isset($this->id) AND !empty($this->id)){
	        $sql = "SELECT * FROM `orders`  WHERE `store_id` = ?  AND id = {$this->id} ORDER BY id DESC;";
	    }
// 	    pre($sql);die;
	    $query = $this->db->query($sql, array($this->store_id));
	    
	    if ( ! $query ) {
	        return array();
	    }
	    $orderDetail = array();
	    while($row = $query->fetch(PDO::FETCH_ASSOC)){
	        if($row['Canal'] != 'Amazon'){
	            
    	        $queryCustomer = $this->db->query( "SELECT * FROM customers WHERE store_id = ? AND id = ?", array($row['store_id'], $row['customer_id']));
    	        $row['customer'] = $queryCustomer->fetch(PDO::FETCH_ASSOC);
    	        
    	        
    	        $queryItems = $this->db->query( "SELECT * FROM order_items WHERE store_id = ? AND order_id = ?", array($row['store_id'], $row['id']));
    	        while($rowItems = $queryItems->fetch(PDO::FETCH_ASSOC)){
    	            
    	            
    	            $sqlAP = "SELECT id, kit FROM available_products WHERE store_id = {$row['store_id']} AND sku LIKE '{$rowItems['SKU']}'";
    	            $queryAP = $this->db->query($sqlAP);
    	            $availableProduct = $queryAP->fetch(PDO::FETCH_ASSOC);
    	            
    	            if($availableProduct['kit'] == 'T'){
    	                
    	                $sqlPR = "SELECT available_products.*, product_relational.product_relational_id, product_relational.qtd
                                         FROM product_relational
                                         LEFT JOIN available_products ON available_products.id = product_relational.product_relational_id
                                         WHERE product_relational.store_id = {$row['store_id']} AND product_relational.product_id = {$availableProduct['id']}";
    	                $queryPR = $this->db->query($sqlPR);
    	                $productsRelational = $queryPR->fetchAll(PDO::FETCH_ASSOC);
    	                $numRel = count($productsRelational);
    	                
    	                $PrecoUnitario = $rowItems['PrecoUnitario'] > 0 ? $rowItems['PrecoUnitario'] / $numRel : '0.00';
    	                $PrecoVenda = $rowItems['PrecoVenda'] > 0 ? $rowItems['PrecoVenda'] / $numRel : '0.00';
    	                $taxaVenda =  $rowItems['TaxaVenda']  > 0 ? $rowItems['TaxaVenda'] / $numRel : '0.00';
    	                
    	                foreach($productsRelational as $k => $productRelational){
    	                    
    	                    $kitItem = array();
    	                    
    	                    $qtd = $productRelational['qtd'] > 0 ? $productRelational['qtd'] : 1 ;
    	                    
    	                    $PrecoUnitarioRatio = $PrecoUnitario > 0 ? $PrecoUnitario / $qtd : '0.00';
    	                    $PrecoVendaRatio = $PrecoVenda > 0 ? $PrecoVenda / $qtd : '0.00';
    	                    $taxaVendaRatio =  $taxaVenda  > 0 ?$taxaVenda / $qtd : '0.00';
    	                    
    	                    $kitItem['store_id'] = $rowItems['store_id'];
    	                    $kitItem['order_id'] = $rowItems['order_id'];
    	                    $kitItem['PedidoId'] = $rowItems['PedidoId'];
    	                    $kitItem['PedidoItemId'] = $rowItems['PedidoItemId'];
    	                    $kitItem['SKU'] = $productRelational['sku'];
    	                    $kitItem['Nome'] = $productRelational['title'];
    	                    $kitItem['Quantidade'] = $qtd;
    	                    $kitItem['PrecoUnitario'] = $PrecoUnitarioRatio;
    	                    $kitItem['PrecoVenda'] = $PrecoVendaRatio;
    	                    $kitItem['TaxaVenda'] = $taxaVendaRatio;
    	                    
    	                    $kitItemAttr = array();
    	                    if(!empty($productRelational['color'])){
    	                       $kitItemAttr[] = array("Nome" => "color", "Valor" => $productRelational['color']);
    	                    }
    	                    if(!empty($productRelational['variation'])){
    	                       $kitItemAttr[] = array("Nome" => "variation", "Valor" => $productRelational['variation']);
    	                    }
    	                    
    	                    $kitItem['item_attributes'] = $kitItemAttr;
    	                    
    	                    $row['items'][] = $kitItem;
    	                    
    	                }
    	                
    	                
    	            }else{
    	            
        	            $queryItemAttributes = $this->db->query( "SELECT * FROM order_item_attributes WHERE store_id = ? AND order_id = ? AND item_id = ?",
        	                array($row['store_id'], $row['id'], $rowItems['id'])
        	                );
        	            $rowItems['item_attributes'] =  $queryItemAttributes->fetchAll(PDO::FETCH_ASSOC);
        	            
        	            $row['items'][] = $rowItems;
        	            
    	            }
    	            
    	        }
    	        
    	        $queryPayments = $this->db->query( "SELECT * FROM order_payments
                WHERE store_id = ? AND order_id = ? AND Situacao LIKE ?", array($row['store_id'], $row['id'], 'approved'));
    	        $row['payments'] = $queryPayments->fetchAll(PDO::FETCH_ASSOC);
    	        
    	        
    	        $orderDetail[] = $row;
    	    }
	    }
	    
	    return $orderDetail;
	    
	}
	
	
}

?>