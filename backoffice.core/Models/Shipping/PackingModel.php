<?php 


class PackingModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
    public $picker;
    
    public $status = 'new';
    
    Public $created;
    
    Public $closed;
    
    Public $user;
    
    public $records = 50;
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
            
        }
        
        if(!defined('QTDE_REGISTROS')){
            
            define('QTDE_REGISTROS', 50);
            
        }
        

    }
    
    
    public function ValidateForm() {
        
        
        if(in_array('records', $this->parametros )){
            $records = get_next($this->parametros, array_search('records', $this->parametros));
            $this->records = isset($records) ? $records : QTDE_REGISTROS ;
        }
        
        if(in_array('Page', $this->parametros )){
            
            $this->pagina_atual =  get_next($this->parametros, array_search('Page', $this->parametros));
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
            
            foreach($this->parametros as $key => $param){
                if(property_exists($this,$param)){
                    $val = get_next($this->parametros, $key);
                    $val = str_replace("_x_", "%", $val);
                    $this->{$param} = $val;
                }
            }
            
            return true;
            
        }else{
            
            $this->pagina_atual = 1;
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
        }
//         pre($_POST);die;
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST['packing'] ) ) {
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                }else{
                    $req = array("");
                    
                    if( in_array($property, $req) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            if(!empty($this->form_msg)){
                
                return false;
            }
            
            return true;
            
            
        } else {
            
            
            if ( in_array('id', $this->parametros )) {
                
                $key = array_search('id', $this->parametros);
                
                $pickingId = get_next($this->parametros, $key);
                $this->id  = is_numeric($pickingId) ? $pickingId :  '';
                
                if(!empty($this->id)){
                    $this->Load();
                    
                }
                
            }
            
     
            if ( in_array('del', $this->parametros )) {
                $this->Delete();
            }
            
            return false;
            
        }
        
    }
    
    public function Save(){
       
        
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('picking', 'id', $this->id, array(
                'id'  => $this->id,
                'store_id'  => $this->store_id,
                'picker'  => $this->picker,
                'status'  => $this->status,
                'closed'  => date('Y-m-d H:i:s'),
                'user'  => $this->user
                
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Separação atualizado com sucesso.</div>';
                $this->id = null;
                return;
            }
        } else {
            
            $sqlVerifyPicking = "SELECT * FROM picking WHERE store_id = {$this->store_id} AND status LIKE 'new' AND picker LIKE '{$this->picker}'";
            $queryVerifyPicking = $this->db->query($sqlVerifyPicking);
            $resVerifyPicking = $queryVerifyPicking->fetch(PDO::FETCH_ASSOC);
            
            if(!isset($resVerifyPicking['id'])){
                
            
                $query = $this->db->insert('picking', array(
                    'store_id'  => $this->store_id,
                    'picker'  => $this->picker,
                    'status'  => $this->status,
                    'created'  => date("Y-m-d H:i:s"),
                    'user'  => $this->user
                ));
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    return;
                } else {
                    
                    $this->form_msg = '<div class="alert alert-success alert-dismissable">Separação cadastrado com sucesso.</div>';
                    $this->id = $this->db->last_id;
                    return;
                }
                
            }else{
                
                $this->form_msg = "<div class='alert alert-danger alert-dismissable'>Já existe uma Lista de coleta aberta para {$this->picker}.</div>";
                return;
                
            }
            
        }
        
        
    }
    
    
    public function TotalPicking(){
        
        $where_fields = $this->GetPickingFilter();
        
        $sql = "SELECT count(*) as total FROM `picking`  WHERE {$where_fields}";
        
        $query = $this->db->query( $sql);
        
        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }
    
    public function GetPickingFilter(){
        
        $where_fields = "";
        $values = array();
        
        $class_vars = get_class_vars(get_class($this));
       
        foreach($class_vars as $key => $value){
           
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "picking.{$key} = {$this->$key} AND ";break;
                    case 'picker': $where_fields .= "picking.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'id': $where_fields .= "picking.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'user': $where_fields .= "picking.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'created': $where_fields .= "picking.{$key} >= '".dbDate($this->$key)."' AND ";break;
                    case 'closed': $where_fields .= "picking.{$key} >= '".dbDate($this->$key)."' AND ";break;
                    case 'status': $where_fields .= "picking.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
    }
    
    
    
    public function ListPicking()
    {
        $sql = "SELECT * FROM `picking`  WHERE `store_id` = {$this->store_id} ORDER BY id DESC
        LIMIT {$this->linha_inicial}, " . $this->records.";";
        $query = $this->db->query($sql, array($this->store_id));
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListPickingPacks()
    {
        $sql = "SELECT * FROM `picking`  WHERE `store_id` = ? ORDER BY id DESC
        LIMIT {$this->linha_inicial}, " . $this->records.";";
        $query = $this->db->query($sql, array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        
        $picking =  $query->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($picking)){
            
            foreach($picking as $k => $pick){
                
                $sqlCount = "SELECT count(*) as total FROM `picking_products`  WHERE store_id = {$this->store_id} AND picking_id = {$pick['id']}";
                
                $query = $this->db->query( $sqlCount);
                
                $totalPacks = $query->fetch(PDO::FETCH_ASSOC);
                
                $picking[$k]['packs']  = $totalPacks['total'];
                
            }
            
            
        }else{
            return array();
        }
        
        return $picking;
        
    }
    
    public function ListPickingProductOrders()
    {
//         $sql = "SELECT * FROM `picking`  WHERE `store_id` = ? ORDER BY id DESC
//         LIMIT {$this->linha_inicial}, " . $this->records.";";
        $sql = "SELECT * FROM `picking_products`  WHERE `store_id` = ? AND picking_id = {$this->id} ORDER BY created DESC";
        $query = $this->db->query($sql, array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
         
        $picking =  $query->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($picking)){
            
            foreach($picking as $k => $pick){
                
                $selectQtd = "SELECT id, sku, title, brand, reference, ean, color, variation_type, variation, weight, height, width, length
                                FROM `available_products` WHERE store_id = {$this->store_id} AND `sku` LIKE '{$pick['sku']}'";
                $queryQtd = $this->db->query($selectQtd);
                
                $picking[$k]['information']  = $queryQtd->fetch(PDO::FETCH_ASSOC);
                
                $sqlCount = "SELECT * FROM `picking_product_orders`  WHERE store_id = {$this->store_id} AND picking_product_id = {$pick['id']}";
                
                $query = $this->db->query( $sqlCount);
                
                $picking[$k]['orders']  = $query->fetchAll(PDO::FETCH_ASSOC);
                
            }
            
            
        }else{
            return array();
        }
        
        return $picking;
        
    }
    
    public function ListPickingProducts()
    {
        $sql = "SELECT * FROM `picking_products`  WHERE `store_id` = ? AND picking_id = {$this->id} ORDER BY created DESC";
        $query = $this->db->query($sql, array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    public function GetPicking()
    {
        $where_fields = $this->GetPickingFilter();
//         pre($where_fields);die;
        $query = $this->db->query("SELECT * FROM `picking` WHERE {$where_fields} ORDER BY id DESC
        LIMIT {$this->linha_inicial}, " . $this->records.";");
        
        if ( ! $query ) {
            
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    
    public function Load()
    {
            
            if(empty($this->id)){
                return array();
            }
            
            $query = $this->db->query('SELECT * FROM picking WHERE `id`= ?', array( $this->id ) );
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($result)){
                
                foreach($result as $key => $value)
                {
                    $column_name = str_replace('-','_',$key);
                    $this->{$column_name} = $value;
                }
                
            }
        
    }
    
    public function Delete()
    {
        $key = array_search('del', $this->parametros);
        if(!empty($key)){
            $id = get_next($this->parametros, $key);
        }
        
        if(!empty($id)){
            
            $query = $this->db->query('DELETE FROM picking WHERE `id`= ?', array( $id ) );
            
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