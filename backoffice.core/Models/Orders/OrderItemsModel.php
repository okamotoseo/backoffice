<?php 

class OrderItemsModel extends MainModel
{
    
    public $id;
    
    public $store_id;
    
    public $order_id;
    
    public $PedidoId;
    
    public $PedidoItemId;
    
    public $SKU;
     
    public $Nome;
    
    public $Quantidade;
    
    public $TipoAnuncio;
    
    public $PrecoUnitario;
     
    public $PrecoVenda;
     
    public $Desconto;
     
    public $TaxaVenda;
    
    public $CMV;
    
    public $UrlImagem;
    
    public $DataPedido;
    
    public $orderItems = array();
    
    public $Marketplace;
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
        }
    }
    
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST['item'] ) ) {
            
//             pre($_POST);
            foreach ( $_POST['item'] as $key => $value ) {
//                 pre($key);
//                 pre($value);
                $items = array(
                    "id" => $key
                );
                
                
                $sql ="SELECT id, sku, title, variation_type, variation, color, sale_price, image  
                FROM available_products WHERE store_id = {$this->store_id} AND id = {$key}";
                $query = $this->db->query($sql);
                $items = $query->fetch(PDO::FETCH_ASSOC);
                
                $variationType = isset($items['variation_type']) ? trim($items['variation_type']) : 'Variacao';
                
                if(!empty($items['variation'])){
                    $items['item_attributes'][$variationType] = $items['variation'];
                }
                if(!empty($items['color'])){
                    $items['item_attributes']['Cor'] = $items['color'];
                }
                
                
                unset($items['variation_type']);
                unset($items['variation']);
                unset($items['color']);
                
                if(isset($value['qty']) and !empty($value['qty'])){
                    $items['Quantidade'] = intval($value['qty']);
                }
                if(isset($value['price']) and !empty($value['price'])){
//                     $items['PrecoVenda'] = number_format(str_replace(",", ".", $value['price']), 2);

//                     $items['PrecoVenda'] = $value['price'];

                    $items['PrecoVenda'] = $value['price_unit'];
                    
                }
                
                $this->orderItems[] = $items;
                
                unset($items);
            }
            
            
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                }else{
                    $req = array();
                    
                    if( in_array($property, $req) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            return true;
            
        } else {
            
            
            return;
            
        }
        
    }
    
    public function Save(){
    	
    	
    	
    	if(empty(trim($this->SKU))){
    		$this->SKU = 'undefined-'.trim($this->PedidoItemId);
    	}
    	
        $query = $this->db->query('SELECT id FROM `order_items`  WHERE  `store_id` = ? AND order_id = ? AND SKU LIKE ?',
        		array($this->store_id,  $this->order_id, trim($this->SKU)));
        
        $res = $query->fetch(PDO::FETCH_ASSOC);
        
        if ( ! empty( $res['id'] ) ) {
        	
        	
        	if($this->PrecoVenda > 0){
	        	$data = array(
	                'PedidoId' => $this->PedidoId,
	                'PedidoItemId' => $this->PedidoItemId,
	                'SKU' => $this->SKU,
	                'Nome' => $this->Nome,
	                'Quantidade' => $this->Quantidade,
	                'TipoAnuncio' => $this->TipoAnuncio,
	                'PrecoUnitario' => $this->PrecoUnitario,
	                'PrecoVenda' => $this->PrecoVenda,
	                'Desconto' => $this->Desconto,
	                'TaxaVenda' => $this->TaxaVenda,
	        			'CMV' => $this->CMV,
	                'UrlImagem' => $this->UrlImagem,
	                'DataPedido' => $this->DataPedido,
	            	"Marketplace" => $this->Marketplace
            	);
        	}else{
        		
        		$data = array(
        				'PedidoId' => $this->PedidoId,
        				'PedidoItemId' => $this->PedidoItemId,
        				'SKU' => $this->SKU,
        				'Nome' => $this->Nome,
        				'Quantidade' => $this->Quantidade,
        				'TipoAnuncio' => $this->TipoAnuncio,
        				'Desconto' => $this->Desconto,
        				'CMV' => $this->CMV,
        				'UrlImagem' => $this->UrlImagem,
        				'DataPedido' => $this->DataPedido,
        				"Marketplace" => $this->Marketplace
        		);
        		
        	}
            $query = $this->db->update('order_items', array('store_id', 'id'), array($this->store_id, $res['id']), $data);
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Item do pedido atualizado com sucesso.</div>';
                $this->id = null;
                return $res['id'];
            }
            
        } else {
        	
        	if(empty( $this->order_id ) or empty($this->PedidoId)){
        		
        		$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent order payments.</div>';
        		return;
        	}
        	
            $query = $this->db->insert('order_items', array(
                'store_id' => $this->store_id,
                'order_id' => $this->order_id,
                'PedidoId' => $this->PedidoId,
                'PedidoItemId' => $this->PedidoItemId,
                'SKU' => $this->SKU,
                'Nome' => $this->Nome,
                'Quantidade' => $this->Quantidade,
                'TipoAnuncio' => $this->TipoAnuncio,
                'PrecoUnitario' => $this->PrecoUnitario,
                'PrecoVenda' => $this->PrecoVenda,
                'PrecoUnitario' => $this->PrecoUnitario,
                'Desconto' => $this->Desconto,
                'TaxaVenda' => $this->TaxaVenda,
            	'CMV' => $this->CMV,
                'UrlImagem' => $this->UrlImagem,
                'DataPedido' => $this->DataPedido,
            	"Marketplace" => $this->Marketplace
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
            	
            	$lastId = $this->db->last_id;
            	
            	$this->subStockItemSale($this->Quantidade, $this->SKU);
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Item do pedido cadastrado com sucesso.</div>';
                
                return $lastId;
            }

            
        }
        
    }
    
    public function subStockItemSale($qtdToSubstract, $sku){
    	
    	if(empty($sku)){
    		return ;
    	}
    
    	$qtdToSubstract = $qtdToSubstract > 0 ? $qtdToSubstract : 1 ;
    	$sqlVerify = "SELECT id, quantity FROM `available_products`  
    			WHERE  `store_id` = {$this->store_id} AND SKU LIKE '{$sku}'";
    	$queryVerify = $this->db->query($sqlVerify);
    	$resVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
    	 
    	if(isset($resVerify['id'])){
    		//verifica se existe produto relacional para atualizar o estoque dos relacionamentos
    		$sqlRel = "SELECT * FROM `product_relational`  WHERE  `store_id` = {$this->store_id} 
    				AND product_id = {$resVerify['id']}";
    		$queryRel = $this->db->query($sqlRel);
    		$resRel = $queryRel->fetchAll(PDO::FETCH_ASSOC);
    	
    		if(isset($resRel[0])){
    			//atualiza o produto pai ou kit
    			$this->db->update('available_products',
    					array('store_id','id'),
    					array($this->store_id, $resVerify['id']),
    					array('updated' =>  date("Y-m-d H:i:s")
    					));
    			 
    			foreach($resRel as $key => $relational){
    				$sqlRelational = "SELECT id, quantity FROM `available_products`  WHERE  `store_id` = {$this->store_id} 
    				AND id = '{$relational['product_relational_id']}'";
    				$queryVerifyRel = $this->db->query($sqlRelational);
    				$resVerifyRel = $queryVerifyRel->fetch(PDO::FETCH_ASSOC);
    	
    				if(isset($resVerifyRel['id'])){
    					//atualiza e subtrai a quantidade do kit
    					$sub = $relational['qtd'] * $qtdToSubstract;
    					$qty = $resVerifyRel['quantity'] - ($relational['qtd'] * $qtdToSubstract);
    					$qty = $qty > 0 ?  $qty : 0 ;
    					$this->db->update('available_products',
    							array('store_id','id'),
    							array($this->store_id, $relational['product_relational_id']),
    							array('quantity' => $qty, 'updated' =>  date("Y-m-d H:i:s")
    							));
    					
    					$dataLog['import_order_item'] = array('quantity' => $sub, 'PedidoId' => $this->PedidoId);
    					
    					$this->db->insert('products_log', array(
    							'store_id' => $this->store_id,
    							'product_id' => $relational['product_relational_id'],
    							'description' => "Baixa de Estoque {$sub} Pedido {$this->PedidoId}",
    							'user' => 'System',
    							'created' => date('Y-m-d H:i:s'),
    							'json_response' => json_encode($dataLog)
    					));
    				}
    				 
    			}
    			 
    		}else{
    			 // se nao houver produto relacional atualiza a quantidade do estoque
    			$qty = ($resVerify['quantity'] - $qtdToSubstract);
    			$qty = $qty > 0 ?  $qty : 0 ;
    			$this->db->update('available_products',
    					array('store_id','id'),
    					array($this->store_id, $resVerify['id']),
    					array('quantity' => $qty, 'updated' =>  date("Y-m-d H:i:s")
    					));
    			$dataLog['import_order_item'] = array('quantity' => $qtdToSubstract, 'PedidoId' => $this->PedidoId);
    				
    			$this->db->insert('products_log', array(
    					'store_id' => $this->store_id,
    					'product_id' => $resVerify['id'],
    					'description' => "Baixa de Estoque {$qtdToSubstract} Pedido {$this->PedidoId}",
    					'user' => 'System',
    					'created' => date('Y-m-d H:i:s'),
    					'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT)
    			));
    		}
    			 
    	}
    	
    }
    
    
    
    public function ListOrderItems()
    {
        $query = $this->db->query('SELECT * FROM `order_items`  WHERE `store_id` = ? AND order_id = ? ORDER BY id DESC',
            array($this->store_id, $this->order_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
        if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
            
            $id = chk_array( $this->parametros, 3 );
            
            $query = $this->db->query('SELECT * FROM order_items WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
            foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
            {
                $column_name = str_replace('-','_',$key);
                $this->{$column_name} = $value;
            }
            
        } else {
            
            return;
            
        }
        
    }
    
    public function Delete()
    {
        if ( chk_array( $this->parametros, 2 ) == 'del' ) {
            
            $id = chk_array( $this->parametros, 3 );
            
            $query = $this->db->query('DELETE FROM order_items WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
}
?>