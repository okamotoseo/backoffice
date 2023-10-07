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
    
    public $UrlImagem;
    
    public $DataPedido;
    
    public $orderItems = array();
    
    
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
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            
//             pre($_POST);
            foreach ( $_POST['item'] as $key => $value ) {
//                 pre($key);
//                 pre($value);
                $items = array(
                    "id" => $key
                );
                
                $sql ="SELECT id, sku, title, variation_type, variation, color, sale_price  FROM available_products WHERE store_id = {$this->store_id} AND id = {$key}";
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
                    $items['PrecoVenda'] = number_format(str_replace(",", ".", $value['price']), 2);
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
//         echo "SELECT id FROM `order_items`  WHERE  `store_id` = '{$this->store_id}'
//     				AND PedidoId = '{$this->PedidoId}' AND SKU LIKE '{$this->SKU}' ORDER BY id DESC";die;
        
//         $query = $this->db->query('SELECT id FROM `order_items`  WHERE  `store_id` = ? AND PedidoId = ? AND SKU LIKE ? ORDER BY id DESC',
//             array($this->store_id, $this->PedidoId, $this->SKU));
        $query = $this->db->query('SELECT id FROM `order_items`  WHERE  `store_id` = ? AND order_id = ? AND SKU LIKE ? ORDER BY id DESC',
        		array($this->store_id,  $this->order_id, $this->SKU));
        $res = $query->fetch(PDO::FETCH_ASSOC);
        if ( ! empty( $res['id'] ) ) {
            $query = $this->db->update('order_items', 'id', $res['id'], array(
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
                'UrlImagem' => $this->UrlImagem,
                'DataPedido' => $this->DataPedido
                
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Item do pedido atualizado com sucesso.</div>';
                $this->id = null;
                return $res['id'];
            }
        } else {
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
                'UrlImagem' => $this->UrlImagem,
                'DataPedido' => $this->DataPedido
            )
                );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Item do pedido cadastrado com sucesso.</div>';
                return $this->db->last_id;
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