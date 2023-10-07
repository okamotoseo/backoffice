<?php 


class TitulosModel extends MainModel
{
    
    
    public $id;
    
    public $store_id;
    
    public $customer_id;
    
	public $tipo_pessoa;
	
	public $nome;
	
	public $codigo;
	
	public $tipo;
	
	public $auxiliar;
	
	public $documento;
	
	/**
	 * @var timestamp
	 */
	public $lancamento;
	
	/**
	 * @var date
	 */	
	public $emissao;

	/**
	 * @var date
	 */
	public $vencimento;

	/**
	 * @var date
	 */
	public $aceite;
	
	public $lote;
	
	public $parcela = 1;
	
	public $status = 'new';
	
	public $valor = 0.00;
	
	public $multa = 0.00;
	
	public $juros = 0.00;
	
	public $ajuste = 0.00;
	
	public $desconto = 0.00;
	
	public $valor_pago= 0.00;
	/**
	 * @var date
	 */	
	public $pagamento;
	
	public $descricao;
	
	public $conta;
	
	public $metodo;
	
	public $instrucoes;
	
	public $item;
	
	public $autorizador;
	
	/**
	 * @var timestamp
	 */
	public $autorizado;

	/**
	 * @var timestamp
	 */
	public $updated;
    

	

    
    
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
        
        $query = $this->db->query("SELECT id, status FROM financeiro_titulos 
            WHERE store_id = {$this->store_id} 
            AND customer_id = '{$this->customer_id}' 
            AND documento LIKE '{$this->documento}'  
        	AND parcela = {$this->parcela} 
            AND tipo LIKE '{$this->tipo}'");
        $res = $query->fetch(PDO::FETCH_ASSOC);
        
        if ( ! empty( $res['id'] ) ) {
            
            $verifyStatus = isset($res['status']) ? strtolower($res['status']) : null ;
            $enabledStatusEdit = array("new");
            if(is_null($verifyStatus) OR in_array($verifyStatus, $enabledStatusEdit)) {
            	
                $query = $this->db->update('financeiro_titulos', 'id', $res['id'], array(
					'customer_id' => $this->customer_id,
					'tipo_pessoa' => $this->tipo_pessoa,
					'nome' => $this->nome,
					'codigo' => $this->codigo,
					'tipo' => $this->tipo,
					'auxiliar' => $this->auxiliar,
					'documento' => $this->documento,
					'lancamento' => date('Y-m-d H:i:s'),
					'emissao' => $this->emissao,
					'vencimento' => $this->vencimento,
					'aceite' => $this->aceite,
					'lote' => $this->lote,
					'parcela' => $this->parcela,
					'status' => $this->status,
					'valor' => $this->valor,
					'multa' => $this->multa,
					'juros' => $this->juros,
					'ajuste' => $this->ajuste,
					'desconto' => $this->desconto,
					'valor_pago' => $this->valor_pago,
					'pagamento' => $this->pagamento,
					'descricao' => $this->descricao,
					'conta' => $this->conta,
					'metodo' => $this->metodo,
					'instrucoes' => $this->instrucoes,
					'item' => $this->item,
					'autorizador' => $this->autorizador,
					'autorizado' => $this->autorizado
					
                    
                ));
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    
                    return;
                }else{
                    if($query->rowCount() > 0 ){
	                    $querySent = $this->db->update('financeiro_titulos', 'id', $res['id'], array('updated' => date('Y-m-d H:i:s')));
	                    if(!$querySent){
	                        pre($querySent);
	                    }

                        $this->form_msg = '<div class="alert alert-success alert-dismissable">Titulo atualizado com sucesso.</div>';
                    }
                }
                
            }
            
            return $res['id'];
            
        } else {
            
            if($this->status != 'closed'){
                
                
                    $query = $this->db->insert('financeiro_titulos', array(
                        'id' => $this->id,
						'store_id' => $this->store_id,
						'customer_id' => $this->customer_id,
						'tipo_pessoa' => $this->tipo_pessoa,
						'nome' => $this->tipo_pessoa,
						'codigo' => $this->codigo,
						'tipo' => $this->tipo,
						'auxiliar' => $this->auxiliar,
						'documento' => $this->documento,
						'lancamento' => date('Y-m-d H:i:s'),
						'emissao' => $this->emissao,
						'vencimento' => $this->vencimento,
						'aceite' => $this->aceite,
						'lote' => $this->lote,
						'parcela' => $this->parcela,
						'status' => $this->status,
						'valor' => $this->valor,
						'multa' => $this->multa,
						'juros' => $this->juros,
						'ajuste' => $this->ajuste,
						'desconto' => $this->desconto,
						'valor_pago' => $this->valor_pago,
						'pagamento' => $this->pagamento,
						'descricao' => $this->descricao,
						'conta' => $this->conta,
						'metodo' => $this->metodo,
						'instrucoes' => $this->instrucoes,
						'item' => $this->item,
						'autorizador' => $this->autorizador,
						'autorizado' => $this->autorizado,
						'updated' => date('Y-m-d H:i:s')
                    ));
                    
                    if ( ! $query ) {
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                        return;
                    } else {
                        $this->form_msg = '<div class="alert alert-success alert-dismissable">Titulo cadastrado com sucesso.</div>';
                        return $this->db->last_id;
                    }
            }
                
            
        }
        
        
    }

    
    public function GetTitulo()
    {
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "orders.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'nome': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'CPFCNPJ': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Email': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Cidade': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedido': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ValorPedidoAte': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'DataPedido': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'DataPedidoAte': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'FormaPagamento': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Canal': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'Marketplace': $where_fields .= "orders.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        $query = $this->db->query("SELECT * FROM `orders` WHERE {$where_fields} ORDER BY id DESC");
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function ListTitulos()
    {
        $query = $this->db->query('SELECT * FROM `orders`  WHERE `store_id` = ? ORDER BY id DESC',
            array($this->store_id)
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
            
            $query = $this->db->query('SELECT * FROM orders WHERE `id`= ?', array( $id ) );
            
            foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
            {
                $column_name = str_replace('-','_',$key);
                
                if($column_name != 'orders_pack'){
                    
                    $this->{$column_name} = $value;
                    
                }else{
                
                    $this->{$column_name} = json_decode($value, true);
                    
                }
            }
            
        } else {
            
            return;
            
        }
        
    }
    
    public function Delete()
    {
        if ( chk_array( $this->parametros, 2 ) == 'del' ) {
            
            $id = chk_array( $this->parametros, 3 );
            
            $query = $this->db->query('DELETE FROM orders WHERE `id`= ?', array( $id ) );
            
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