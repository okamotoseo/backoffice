<?php 


class OrderPaymentsModel extends MainModel
{
    
    public $id;
    
    public $store_id;
    
    public $order_id;
    
    public $PedidoId;
    
    public $PagamentoId;
    
    public $Status;
    
    public $FormaPagamento;
    
    public $Metodo;
    
    public $NumeroParcelas;
    
    public $ValorParcela;
    
    public $ValorDesconto;
    
    public $ValorTotal;
    
    public $NSU;
    
    public $NumeroAutorizacao;
    
    public $DataAutorizacao;
    
    public $Situacao;
    
    public $MarketplaceTaxa;
    
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
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
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
            
            if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
                $this->Load();
                
            }
            
            if ( chk_array( $this->parametros, 2 ) == 'del' ) {
                
                $this->Delete();
                
            }
            
            return;
            
        }
        
    }
    
    public function Save(){
        
        
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('order_payments', 'id', $this->id, array(
                'store_id' => $this->store_id,
                'order_id' => $this->order_id,
                'PedidoId' => $this->PedidoId,
                'PagamentoId' => $this->PagamentoId,
                'FormaPagamento' => $this->FormaPagamento,
                'Metodo' => $this->Metodo,
                'NumeroParcelas' => $this->NumeroParcelas,
                'ValorParcela' => $this->ValorParcela,
                'ValorDesconto' => $this->ValorDesconto,
                'ValorTotal' => $this->ValorTotal,
                'NSU' => $this->NSU,
                'NumeroAutorizacao' => $this->NumeroAutorizacao,
                'DataAutorizacao' => $this->DataAutorizacao,
                'Situacao' => $this->Situacao,
                'MarketplaceTaxa' => $this->MarketplaceTaxa,
                'Marketplace' => $this->Marketplace
                
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Pagamento do pedido atualizado com sucesso.</div>';
                $this->id = null;
                return;
            }
        } else {
                
            $query = $this->db->insert('order_payments', array(
                'store_id' => $this->store_id,
                'order_id' => $this->order_id,
                'PedidoId' => $this->PedidoId,
                'PagamentoId' => $this->PagamentoId,
                'FormaPagamento' => $this->FormaPagamento,
                'Metodo' => $this->Metodo,
                'NumeroParcelas' => $this->NumeroParcelas,
                'ValorParcela' => $this->ValorParcela,
                'ValorDesconto' => $this->ValorDesconto,
                'ValorTotal' => $this->ValorTotal,
                'NSU' => $this->NSU,
                'NumeroAutorizacao' => $this->NumeroAutorizacao,
                'DataAutorizacao' => $this->DataAutorizacao,
                'Situacao' => $this->Situacao,
                'MarketplaceTaxa' => $this->MarketplaceTaxa,
                'Marketplace' => $this->Marketplace
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Pagamento do pedido cadastrado com sucesso.</div>';
                return;
            }
                
            
        }
        
        
    }
    
    public function ValidateOrderPayments(){
        
        $query = $this->db->query('SELECT * FROM `order_payments`  WHERE  `store_id` = ?
    				AND PedidoId LIKE ? AND PagamentoId LIKE ? ORDER BY id DESC',
            array($this->store_id, $this->PedidoId, $this->PagamentoId)
            );
        
        $res = $query->fetch(PDO::FETCH_ASSOC);
        if(!isset($res['PedidoId'])){
            
            $this->Save();
            return $this->db->last_id;
        }
        
        return $res['id'];
    }
    
    public function ListOrderPayments()
    {
        $query = $this->db->query('SELECT * FROM `order_payments`  WHERE `store_id` = ? AND order_id = ? ORDER BY id DESC',
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
            
            $query = $this->db->query('SELECT * FROM order_payments WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
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
            
            $query = $this->db->query('DELETE FROM order_payments WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
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