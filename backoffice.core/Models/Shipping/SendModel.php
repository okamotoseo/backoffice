<?php 


class SendModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
    public $company;
    
    public $status = 'new';
    
    Public $created;
    
    Public $sent;
    
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
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
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
                
                $productId = get_next($this->parametros, $key);
                $this->id  = is_numeric($productId) ? $productId :  '';
                
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
            
            $query = $this->db->update('shipping_send', 'id', $this->id, array(
                'id'  => $this->id,
                'store_id'  => $this->store_id,
                'company'  => $this->company,
                'status'  => $this->status,
                'user'  => $this->user
                
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Remessa atualizado com sucesso.</div>';
                $this->id = null;
                return;
            }
        } else {
            $created = date("Y-m-d H:i:s");
                $query = $this->db->insert('shipping_send', array(
                    'id'  => $this->id,
                    'store_id'  => $this->store_id,
                    'company'  => $this->company,
                    'status'  => $this->status,
                    'created'  => $created,
                    'user'  => $this->user
                ));
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    return;
                } else {
                    
                    $this->form_msg = '<div class="alert alert-success alert-dismissable">Remessa cadastrado com sucesso.</div>';
                    $this->id = $this->db->last_id;
                    return;
                }

            
        }
        
        
    }
    
    
    public function TotalShipping(){
        
        $where_fields = $this->GetShippingFilter();
        
        $sql = "SELECT count(*) as total FROM `shipping_send`  WHERE {$where_fields}";
        
        $query = $this->db->query( $sql);
        
        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }
    
    public function GetShippingFilter(){
        
        $where_fields = "";
        $values = array();
        
        $class_vars = get_class_vars(get_class($this));
       
        foreach($class_vars as $key => $value){
           
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "shipping_send.{$key} = {$this->$key} AND ";break;
                    case 'company': $where_fields .= "shipping_send.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'id': $where_fields .= "shipping_send.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'user': $where_fields .= "shipping_send.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'created': $where_fields .= "shipping_send.{$key} >= '".dbDate($this->$key)."' AND ";break;
                    case 'status': $where_fields .= "shipping_send.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
    }
    
    
    
    public function ListShipping()
    {
        $sql = "SELECT * FROM `shipping_send`  WHERE `store_id` = ? ORDER BY id DESC
        LIMIT {$this->linha_inicial}, " . $this->records.";";
        $query = $this->db->query($sql, array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListShippingPacks()
    {
        $sql = "SELECT * FROM `shipping_send`  WHERE `store_id` = ? ORDER BY id DESC 
        LIMIT {$this->linha_inicial}, " . $this->records.";";
        $query = $this->db->query($sql, array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        
        $shippingSend =  $query->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($shippingSend)){
            
            foreach($shippingSend as $k => $shipping){
                
                $sqlCount = "SELECT count(*) as total FROM `shipping_send_code`  WHERE store_id = {$this->store_id} AND shipping_send_id = {$shipping['id']}";
                
                $query = $this->db->query( $sqlCount);
                
                $totalPacks = $query->fetch(PDO::FETCH_ASSOC);
                
                $shippingSend[$k]['packs']  = $totalPacks['total'];
                
            }
            
            
        }else{
            return array();
        }
        
        return $shippingSend;
        
    }
    
    public function ListShippingCode()
    {
        $sql = "SELECT * FROM `shipping_send_code`  WHERE `store_id` = ? AND shipping_send_id = {$this->id} ORDER BY created DESC";
        $query = $this->db->query($sql, array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    public function GetShipping()
    {
        $where_fields = $this->GetShippingFilter();
//         pre($where_fields);die;
        $query = $this->db->query("SELECT * FROM `shipping_send` WHERE {$where_fields} ORDER BY id DESC
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
            
            $query = $this->db->query('SELECT * FROM shipping_send WHERE `id`= ?', array( $this->id ) );
            
            $resShippingSend = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($resShippingSend)){
                
                foreach($resShippingSend as $key => $value)
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
            
            $query = $this->db->query('DELETE FROM shipping_send WHERE `id`= ?', array( $id ) );
            
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