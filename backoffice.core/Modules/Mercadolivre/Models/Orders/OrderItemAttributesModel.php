<?php 


class OrderItemAttributesModel extends MainModel
{
    
    public $id;
    
    public $store_id;
    
    public $order_id;
    
    public $item_id;
    
    public $PedidoId;
    
    public $PedidoItemId;
    
    public $Nome;
    
    public $Valor;
    
    
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
            
            $query = $this->db->update('order_item_attributes', 'id', $this->id, array(
                'PedidoId' => $this->PedidoId,
                'PedidoItemId' => $this->PedidoItemId,
                'Nome' => $this->Nome,
                'Valor' => $this->Valor
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Item do item atualizado com sucesso.</div>';
                $this->id = null;
                return;
            }
        } else {
                
            $query = $this->db->insert('order_item_attributes', array(
                'id' => $this->id,
                'store_id' => $this->store_id,
                'order_id' => $this->order_id,
                'item_id' => $this->item_id,
                'PedidoId' => $this->PedidoId,
                'PedidoItemId' => $this->PedidoItemId,
                'Nome' => $this->Nome,
                'Valor' => $this->Valor
            )
                );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Item do item cadastrado com sucesso.</div>';
                return;
            }

            
        }
        
        
    }
    
    public function ValidateOrderItemAttribute(){
            
            $query = $this->db->query('SELECT * FROM `order_item_attributes`  WHERE  `store_id` = ?
    				AND PedidoId LIKE ? AND PedidoItemId LIKE ?  AND  Nome = ? AND `Valor` = ?',
                array($this->store_id, $this->PedidoId, $this->PedidoItemId, $this->Nome, $this->Valor)
                );
            
//             echo $sql = "SELECT * FROM `order_item_attributes`  WHERE  `store_id` = {$this->store_id}
//     				AND PedidoId LIKE '{$this->PedidoId}' AND PedidoItemId LIKE '{$this->PedidoItemId}'  
//                     AND  Nome = '{$this->Nome}' AND `Valor` = '{$this->Valor}'";die;
            $res = $query->fetch(PDO::FETCH_ASSOC);
            if(!isset($res['Valor'])){
                
                $this->Save();
                return $this->db->last_id;
            }
            
            return $res['id'];
        }
    
    
    public function ListItemAttributes()
    {
        $query = $this->db->query('SELECT * FROM `order_item_attributes`  WHERE `store_id` = ? AND order_id = ? ORDER BY id DESC',
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
            
            $query = $this->db->query('SELECT * FROM order_item_attributes WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
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
            
            $query = $this->db->query('DELETE FROM order_item_attributes WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel excluir o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
}
?>