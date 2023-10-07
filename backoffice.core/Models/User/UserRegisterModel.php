<?php

class UserRegisterModel  extends MainModel
{
    /**
     * @var int
     * Class Unique ID
     */
    public $id;
    
    /**
     * @var int
     */
    public $account_id;
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var string
     */
    public $email;
    
    /**
     * @var string
     */
    public $stores;
    
    /**
     * @var string
     */
    public $password;
    
    /**
     * @var string
     */
    public $session_id;
    
    /**
     * @var
     */
    public $permissions;
    
    /**
     * @var int
     */
    public $store_id;
    
    /**
     * @var string
     */
    public $address;
    
    /**
     * @var int
     */
    public $postalcode;
    
    /**
     * @var int
     */
    public $neighborhood;
    
    /**
     * @var int
     */
    public $number;
    
    /**
     * @var int
     */
    public $city;
    
    /**
     * @var string
     */
    public $state;
    
    /**
     * @var int
     */
    public $created;

    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->parametros = $this->controller->parametros;
        
        $this->userdata = $this->controller->userdata;
        
        $this->account_id = $this->controller->userdata['account_id'];
        
        $this->store_id =  $this->controller->userdata['store_id'];
    }
    
    
    
    public function ValidateForm() {
    	
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
        	
            foreach ( $_POST as $property => $value ) {
            	                
                if(property_exists($this,$property) AND !empty($value)){
                    
                    $this->{$property} = $value;
                    if($property == 'permissions'){
//                     	pre($this->permissions);die;
                    }
                    
                }else{
//                 	pre($property);die;
                	
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
//                     return;
                    
                }
                
                
            }
            
            return true;
            
        } else {
        	
            return;
            
        }
        
    }
    
    public function NewUserAccount($accountModel, $storeModel){
        
        $db_check_user = $this->db->query (
            'SELECT * FROM `users` WHERE `email` = ? AND `account_id` = ?',
            array($accountModel->account_email, $accountModel->account_id)
            );
        $result = $db_check_user->fetch(PDO::FETCH_ASSOC);
        if ( ! isset($result['id']) ) {
         
            $password_hash = new PasswordHash(8, FALSE);
            $password = $password_hash->HashPassword( $accountModel->account_document );
            $permissions = serialize( array("any") );
            $stores = serialize( $storeModel->id );
            
            $query = $this->db->insert('users', array(
                'account_id' =>  $accountModel->account_id,
                'email' =>  $accountModel->account_email,
                'stores' =>  $stores,
                'store_id' =>  $storeModel->id,
                'password' => $password,
                'name' =>  $accountModel->account_name,
                'session_id' => md5(time()),
                'permissions' => $permissions
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
                
            } else {
                $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully registered.</div>';
                return;
            }
        
        
        }else{
        	return;
        }
        
    }
    
    public function Save(){
        
        // Verifica se o usuário existe
        $db_check_user = $this->db->query (
            'SELECT * FROM `users` WHERE `email` = ?',
            array($this->email)
            );
        
        // Verifica se a consulta foi realizada com sucesso
        if ( ! $db_check_user ) {
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error.</div>';
            return;
        }
        
        // Obtém os dados da base de dados MySQL
        $fetch_user = $db_check_user->fetch();
        
        // Configura o ID do usuário
        $this->id = $fetch_user['id'];
        
        $this->session_id = $fetch_user['session_id'];
        
        // Precisaremos de uma instância da classe Phpass
        // veja http://www.openwall.com/phpass/
        $password_hash = new PasswordHash(8, FALSE);
        
        // Cria o hash da senha
        $password = $password_hash->HashPassword( $this->password );
        
        // Verifica se as permissões tem algum valor inválido:
        // 0 a 9, A a Z e , . - _
        if(isset($this->permissions)){
	        foreach ($this->permissions as $key => $permissions){
		        if ( preg_match( '/[^0-9A-Za-z,.-_s ]/is', $permissions ) ) {
		            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Use just letters, numbers and a comma for permissions.</div>';
		            return;
		        }
	        }
        }else{
        	$this->form_msg = '<div class="alert alert-danger alert-dismissable">Use just letters, numbers and a comma for permissions.</div>';
        	return;
        }
        
//         $stores = array_map('trim', $this->stores);
        
//         // Remove lojas duplicadas
//         $stores = array_unique( $this->stores);
        
//         // Remove valores em branco
//         $stores = array_filter( $this->stores);
        
        // Serializa as lojas
        $stores = serialize( $this->stores);
        
        // Faz um trim nas permissões
//         $permissions = array_map('trim', explode(',', $this->permissions));
        
        // Remove permissões duplicadas
//         $permissions = array_unique( $permissions );
        
//         // Remove valores em branco
//         $permissions = array_filter( $permissions );
        
        // Serializa as permissões
        $permissions = serialize( $this->permissions );
        
        
        // Se o ID do usuário não estiver vazio, atualiza os dados
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('users', 'id', $this->id, array(
                'password' => $password,
                'name' => $this->name,
                'session_id' => $this->session_id,
                'stores' => $stores,
                'store_id' => $this->store_id,
                'permissions' => $permissions
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully updated.</div>';
                return;
            }
            // Se o ID do usuário estiver vazio, insere os dados
        } else {
            
            // Executa a consulta
            $query = $this->db->insert('users', array(
                'account_id' =>  $this->account_id,
                'email' =>  $this->email,
                'password' => $password,
                'name' =>  $this->name,
                'session_id' => md5(time()),
                'stores' => $stores,
                'store_id' => $this->store_id,
                'permissions' => $permissions
            ));

            // Verifica se a consulta está OK e configura a mensagem
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                // Termina
                return;
            } else {
                $this->form_msg = '<div class="alert alert-success alert-dismissable">User successfully registered.</div>';
                
                // Termina
                return;
            }
        }
        
    }
    

    
    
    public function ListUsers()
    {   
        // Simplesmente seleciona os dados na base de dados
        $query = $this->db->query('SELECT * FROM `users`  WHERE `account_id`= ? ORDER BY id DESC', 
                array($this->account_id)
            );
        
        // Verifica se a consulta está OK
        if ( ! $query ) {
            return array();
        }
        // Preenche a tabela com os dados do usuário
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }

    public function Load()
    {
        if ( chk_array( $this->parametros, 2 ) == 'edit' ) {
            
            $id = chk_array( $this->parametros, 3 );
            
            $query = $this->db->query('SELECT * FROM users WHERE `id`= ?', array( $id ) );
            
            foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
            {
                $column_name = str_replace('-','_',$key);
                $this->{$column_name} = $value;
                
            }
            
            $this->stores = unserialize($this->stores);
            $this->permissions = unserialize($this->permissions);
//             $this->permissions = implode(',', $this->permissions);
//             $this->password = null;
            
        } else {
            
            return;
            
        }
        
    }
    
    
} 