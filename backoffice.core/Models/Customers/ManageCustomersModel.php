<?php 


class ManageCustomersModel extends MainModel
{
    
    public $id;
    
    public $store_id;
    
    public $Codigo;
    
    public $TipoPessoa;
    
    public $Genero;
    
    public $Nome;
    
    public $Apelido;
    
    public $Email;
    
    public $CPFCNPJ;
    
    public $RGIE;
    
    public $Telefone;
    
    public $TelefoneAlternativo;
    
    public $TelefoneComercial;
    
    public $DataNascimento;
    
    public $Responsavel = 'Sysplace';
    
    public $DataCriacao;
    
    public $DataAtualizacao;
    
    public $Endereco;
    
    public $Numero;
    
    public $Complemento;
    
    public $Bairro;
    
    public $Cidade;
    
    public $Estado;
    
    public $CEP;
    
    public $Marketplace;
    
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
                    //                     $val = str_replace("_", " ", $val);
                    $this->{$param} = $val;
                    
                }
            }
            
            return true;
            
        }else{
            
            $this->pagina_atual = 1;
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
        }
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                    	
                    	switch($property){
                    		case 'CPFCNPJ': $value =  getNumbers($value); break;
                    	}
                    	
                        
                        $this->{$property} = !is_array($value) ? trim($value) : $value ;
                        
                    }
                }else{
                  
//                     pre($_POST);die;
                    $arr = array('CPFCNPJ', 'Email');
                    
                    if( isset ( $_POST['filter-customer'] ) ){
                        $arr = array();
                    }
                    if( in_array($property, $arr) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
            
            if ( in_array('edit', $this->parametros )) {
                
                $key = array_search('edit', $this->parametros);
                
                $customerId = get_next($this->parametros, $key);
                $this->id  = is_numeric($customerId) ? $customerId :  '';
                
                if(!empty($this->id)){
                    
                    $this->Load();
                    
                }
                
            }
            
            if ( in_array('del', $this->parametros )) {
                    
                $key = array_search('del', $this->parametros);
                
                $customerId = get_next($this->parametros, $key);
                $this->id  = is_numeric($customerId) ? $customerId :  '';
                
                if(!empty($this->id)){
                
                    $this->Delete();
                    
                }
            
                return;
            
            }
            
        }
        
    }
    
    public function Save(){
        
        
        $query = $this->db->query('SELECT id FROM `customers`  WHERE  `store_id` = ?
    				AND CPFCNPJ LIKE ? ORDER BY id DESC',
            array($this->store_id, $this->CPFCNPJ)
            );
        
        $res = $query->fetch(PDO::FETCH_ASSOC);
        if(!empty($res['id'])){
            
            $query = $this->db->update('customers', 'id', $res['id'], array(
                'store_id' => $this->store_id,
                'Codigo' => $this->Codigo,
                'TipoPessoa' => $this->TipoPessoa,
                'Genero' => $this->Genero,
                'Nome' => $this->Nome,
                'Apelido' => $this->Apelido,
                'Email' => $this->Email,
                'RGIE' => $this->RGIE,
                'Telefone' => $this->Telefone,
                'TelefoneAlternativo' => $this->TelefoneAlternativo,
                'TelefoneComercial' => $this->TelefoneComercial,
                'DataNascimento' => dbDate($this->DataNascimento),
                'Responsavel' => $this->Responsavel,
                'DataCriacao' => $this->DataCriacao,
                'DataAtualizacao' => $this->DataAtualizacao,
                'Endereco' => $this->Endereco,
                'Numero' => $this->Numero,
                'Complemento' => $this->Complemento,
                'Bairro' => $this->Bairro,
                'Cidade' => $this->Cidade,
                'Estado' => $this->Estado,
                'CEP' => $this->CEP,
                'Marketplace' => $this->Marketplace,
                'updated' => date('Y-m-d H:i:s'),
                
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
                $this->id = null;
                return $res['id'];
            }
        } else {

            $dataCriacao = isset($this->DataCriacao) ? $this->DataCriacao : date('Y-m-d H:i:s') ;
            $query = $this->db->insert('customers', array(
                'store_id' => $this->store_id,
                'Codigo' => $this->Codigo,
                'TipoPessoa' => $this->TipoPessoa,
                'Genero' => $this->Genero,
                'Nome' => $this->Nome,
                'Apelido' => $this->Apelido,
                'CPFCNPJ' => $this->CPFCNPJ,
                'Email' => $this->Email,
                'RGIE' => $this->RGIE,
                'Telefone' => $this->Telefone,
                'TelefoneAlternativo' => $this->TelefoneAlternativo,
                'TelefoneComercial' => $this->TelefoneComercial,
                'DataNascimento' => dbDate($this->DataNascimento),
                'Responsavel' => $this->Responsavel,
                'DataCriacao' => $dataCriacao,
                'DataAtualizacao' => $this->DataAtualizacao,
                'Endereco' => $this->Endereco,
                'Numero' => $this->Numero,
                'Complemento' => $this->Complemento,
                'Bairro' => $this->Bairro,
                'Cidade' => $this->Cidade,
                'Estado' => $this->Estado,
                'CEP' => $this->CEP,
                'Marketplace' => $this->Marketplace
                )
            );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
               echo $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
                return $this->db->last_id;
            }
                

            
        }
        
        
    }
    

    public function GetCustomerFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "customers.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "customers.{$key} = {$this->$key} AND ";break;
                    case 'Nome': $where_fields .= "customers.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Apelido': $where_fields .= "customers.{$key} LIKE UPPER('{$this->$key}') AND ";break;
                    case 'CPFCNPJ': $where_fields .= "customers.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Email': $where_fields .= "customers.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Genero': $where_fields .= "customers.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Codigo': $where_fields .= "customers.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Marketplace': $where_fields .= "customers.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'TipoPessoa': $where_fields .= "customers.{$key} = '{$this->$key}' AND ";break;
                    
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
    public function TotalGetCustomers(){
        
        $where_fields = $this->GetCustomerFilter();
        
        $query = $this->db->query("SELECT * FROM `customers`  WHERE {$where_fields}");
        if ( ! $query ) {
            return array();
        }
        return $query->rowCount();
        
    }
    
    public function GetCustomers()
    {
        
        $where_fields = $this->GetCustomerFilter();
        
        $sql = "SELECT * FROM `customers`  WHERE {$where_fields} ORDER BY DataCriacao DESC";
        
        if($this->records != 'no_limit'){
            $sql = $sql." LIMIT {$this->linha_inicial}, " . $this->records.";";
        }
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    public function TotalCustomers(){
        
        $sql = "SELECT count(*) as total FROM `customers`  WHERE `store_id` = ?";
        
        $query = $this->db->query( $sql ,array( $this->store_id));
        
        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }
    
    public function ListCustomers()
    {
        $query = $this->db->query("SELECT * FROM `customers`  WHERE `store_id` = ?  ORDER BY DataCriacao DESC
            LIMIT {$this->linha_inicial}, " . QTDE_REGISTROS.";",
            array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
        
        if(!isset($this->id)){
            return;
        }
            
        $query = $this->db->query('SELECT * FROM customers WHERE `id`= ?', array( $this->id ) );
        foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
        {
            $column_name = str_replace('-','_',$key);
            $this->{$column_name} = $value;
        }
            
        
    }
    
    public function Delete()
    {
        if(!isset($this->id)){
            return array();
        }
            
        $query = $this->db->query('DELETE FROM customers WHERE `id`= ?', array( $this->id ) );
        
        if ( ! $query ) {
            
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
            
        }
            
            
        
    }
    
}
?>