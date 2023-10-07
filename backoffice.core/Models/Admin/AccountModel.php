<?php
/**
 * Modelo para gerenciar Contas
 *
 * @package 
 * @since 0.1
 */
class AccountModel extends MainModel
{
    
    /**
     * @var int
     * Class Unique ID
     */
    public $account_id;
    
    /**
     * @var string
     */
    public $account_name;
  
    /**
     * @var string
     */
    public $account_email;

    /**
     * @var string
     */
    public $account_phone;
    
    /**
     * @var string
     */
    public $account_mobile;
    
    /**
     * @var string
     */
    public $account_rg;
    
    /**
     * @var string
     */
    public $account_document;
    
    /**
     * @var string
     */
    public $account_address;
    
    /**
     * @var string
     */
    public $account_postalcode;
    
    /**
     * @var string
     */
    public $account_neighborhood;
    
    /**
     * @var string
     */
    public $account_number;
    
    /**
     * @var string
     */
    public $account_city;
    
    /**
     * @var string
     */
    public $account_state;
    
    /**
     * @var int
     */
    public $account_plan_id;
    
    /**
     * @var int
     */
    public $account_status = 1;
    
    
    public $reportSales;
    
    
    public $charge = array();
    
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->parametros = $this->controller->parametros;
        
        $this->userdata = $this->controller->userdata;
        
    }
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            
//             pre($_POST);die;
            foreach ( $_POST as $property => $value ) {
                
                if(property_exists($this,$property)){
                    
                    if(!empty($value)){
                        
                        if(property_exists($this,$property)){
                            
                            $this->{$property} = $value;
                            
                        }
                        
                    }else{
                        
                        if( isset ( $_POST['form-account'] ) ){
                            
                            $required = array();
                            
                            if( in_array($property, $required) ){
                                
                                $this->field_error[$property] = "has-error";
                                
                                $this->form_msg = "<div class='alert alert-danger alert-dismissable'>There are empty fields. Data has not been sent. {$property}</div>";;
                            }
                            
                        }
                    }
                }
                
            }
            
            return true;
            
        } else {
            
            return;
            
        }
        
    }
    
    public function Save(){
    	
        $db_check = $this->db->query (
            'SELECT * FROM `accounts` WHERE `email` = ?',
            array($this->account_email)
            );
        
        if ( ! $db_check ) {
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Email já cadastrado..</div>';
            return;
        }
        
        $fetch_account = $db_check->fetch();
        
        $this->account_id = $fetch_account['id'];
        
        
        if ( ! empty( $this->account_id ) ) {
            $query = $this->db->update('accounts', 'id', $this->account_id, array(
                'name' => $this->account_name,
                'email' => $this->account_email,
                'phone' => $this->account_phone,
                'mobile' => $this->account_phone,
                'rg' => $this->account_rg,
                'document' => $this->account_document,
                'address' => $this->account_address,
                'postalcode' => $this->account_postalcode,
                'neighborhood' => $this->account_neighborhood,
                'number' => $this->account_number,
                'city' => $this->account_city,
                'state' => $this->account_state,
                'plan_id' => $this->account_plan_id,
                'status' => $this->account_status
            ));
            
            if ( ! $query ) {
            	
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
                
            } else {
            	
                $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully updated.</div>';
                return;
            }
        } else {
            $query = $this->db->insert('accounts', array(
                'name' => $this->account_name,
                'email' => $this->account_email,
                'phone' => $this->account_phone,
                'mobile' => $this->account_phone,
                'rg' => $this->account_rg,
                'document' => $this->account_document,
                'address' => $this->account_address,
                'postalcode' => $this->account_postalcode,
                'neighborhood' => $this->account_neighborhood,
                'number' => $this->account_number,
                'city' => $this->account_city,
                'state' => $this->account_state,
                'plan_id' => $this->account_plan_id,
                'status' => $this->account_status
             ));
            
            
            if ( ! $query ) {
            	
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
                
            } else {
                
                $this->account_id = $this->db->last_id;
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully registered.</div>';
                return ;
                
            }
        }
        
    }
    
    public function ListAccounts()
    {
        // Simplesmente seleciona os dados na base de dados
        $query = $this->db->query("SELECT * FROM accounts ORDER BY id ASC");
        
        // Verifica se a consulta está OK
        if ( ! $query ) {
            return array();
        }
        // Preenche a tabela com os dados do usuário
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
    	
        if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
            
            $account_id = chk_array( $this->parametros, 1 );
            
            $query = $this->db->query('SELECT * FROM accounts WHERE `id`= ?', array( $account_id ) );
            
            foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
            {
                
                $column_name = "account_".str_replace('-','_',$key);
                $this->{$column_name} = $value;
                
                
            }
        }else {
            
            return;
            
        }
    }
    
    
    public function Charge(){
        
        
        $totalPedido = 0.00;
        $count++;
        foreach($this->reportSales as $k => $order){
            
            $totalPedido += $order['ValorPedido'];
            $count++;
            
        }
        $this->charge = array('total' => $totalPedido, 'total_pedidos' => $count);
        
        return $totalPedido;
        
        
    }
    
    
} 