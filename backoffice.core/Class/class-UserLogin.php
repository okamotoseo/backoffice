<?php
/**
 * UserLogin - Manipula os dados de usuários
 *
 * Manipula os dados de usuários, faz login e logout, verifica permissões e 
 * redireciona página para usuários logados.
 *
 */
class UserLogin
{
	/**
	 * usuário logado ou não
	 *
	 * Verdadeiro se ele estiver logado.
	 *
	 * @public
	 * @access public
	 * @var bol
	 */
	public $logged_in;
	
	/**
	 * Dados do usuário
	 *
	 * @public
	 * @access public
	 * @var array
	 */
	public $userdata;
	
	
	/**
	 * Dados da loja
	 *
	 * @public
	 * @access public
	 * @var array
	 */
	public $storedata = array();
	
	/**
	 * Dados do modulo
	 *
	 * @public
	 * @access public
	 * @var array
	 */
	public $moduledata = array();
	
	/**
	 * Mensagem de erro para o formulário de login
	 *
	 * @public
	 * @access public
	 * @var string
	 */
	public $login_error;
	
	
	
	public function store_session(){
	    
	    if(isset($_POST['store_session'])
	        && isset($_SESSION['userdata'])
	        && $_SESSION['userdata']['store_id'] != intval($_POST['store_session'])
	        ){
	            $storeId = intval($_POST['store_session']);
                $queryStore = $this->db->query(
                    'SELECT * FROM stores WHERE id = ? LIMIT 1',
                    array( $storeId )
                    );
                
                if(!$queryStore){
                    return;
                }
                
                
                $fetchStore = $queryStore->fetch(PDO::FETCH_ASSOC);
                
                
                $query = $this->db->query(
                    'UPDATE users SET store_id = ? WHERE id = ?',
                    array( $fetchStore['id'], $_SESSION['userdata']['id'] )
                    );
                
                $_SESSION['userdata']['store_id'] = $fetchStore['id'];
                
//                 unset($_POST);
	        
	        
	    }
	    
	    
	}
	/**
	 * Verifica o login
	 *
	 * Configura as propriedades $logged_in e $login_error. Também
	 * configura o array do usuário em $userdata
	 */
	public function check_userlogin () {
		// Verifica se existe uma sessão com a chave userdata
		// Tem que ser um array e não pode ser HTTP POST
		if ( isset( $_SESSION['userdata'] )
			 && ! empty( $_SESSION['userdata'] )
			 && is_array( $_SESSION['userdata'] ) 
			 && ! isset( $_POST['userdata'] )
			) {
			// Configura os dados do usuário
			$userdata = $_SESSION['userdata'];
			
// 			pre($userdata);die;
			// Garante que não é HTTP POST
			$userdata['post'] = false;
		}
// 		pre($_SESSION);
		// Verifica se existe um $_POST com a chave userdata
		// Tem que ser um array
		if ( isset( $_POST['userdata'] )
			 && ! empty( $_POST['userdata'] )
			 && is_array( $_POST['userdata'] ) 
			) {
			// Configura os dados do usuário
			$userdata = $_POST['userdata'];
// 			echo 456;die;
			// Garante que é HTTP POST
			$userdata['post'] = true;
		}
		
// 		pre($userdata);
// 		die;
 		// Verifica se existe algum dado de usuário para conferir
		if ( ! isset( $userdata ) || ! is_array( $userdata ) ) {
			// Desconfigura qualquer sessão que possa existir sobre o usuário
			$this->logout();
// 			$this->Login();
			return;
		}
		
		
		// Passa os dados do post para uma variável
		if ( $userdata['post'] === true ) {
			$post = true;
		} else {
			$post = false;
		}
		
		// Remove a chave post do array userdata
		unset( $userdata['post'] );
		
		// Verifica se existe algo a conferir
		if ( empty( $userdata ) ) {
			$this->logged_in = false;
			$this->login_error = null;
		
			// Desconfigura qualquer sessão que possa existir sobre o usuário
			$this->logout();
		
			return;
		}
		
		
		// Extrai variáveis dos dados do usuário
		extract( $userdata );
		
		// Verifica se existe um usuário e senha
		if ( ! isset( $email ) || ! isset( $password ) ) {
			$this->logged_in = false;
			$this->login_error = null;
			
			// Desconfigura qualquer sessão que possa existir sobre o usuário
			$this->logout();
		
			return;
		}
		
// 		Verifica se o usuário existe na base de dados
		$query = $this->db->query( 
			'SELECT  * FROM users WHERE email = ? LIMIT 1', 
			array( $email ) 
		);
		
		// Verifica a consulta
		if ( ! $query ) {
			$this->logged_in = false;
			$this->login_error = 'Internal error.';
		
			// Desconfigura qualquer sessão que possa existir sobre o usuário
			$this->logout();
		
			return;
		}
		
		$fetch = $query->fetch(PDO::FETCH_ASSOC);
		$id = (int) $fetch['id'];
		if ( empty( $id ) ){
			$this->logged_in = false;
			$this->login_error = 'User do not exists.';
			$this->logout();
		
			return;
		}
		
		// Confere se a senha enviada pelo usuário bate com o hash do BD
		if ( $this->phpass->CheckPassword( $password, $fetch['password'] ) ) {
			
			if ( empty($fetch['session_id']) && ! $post ) {
				$this->logged_in = false;
				$this->logout();
					
				return;
			}
		    
			// Se for uma sessão, verifica se a sessão bate com a sessão do BD
			if ( session_id() != $fetch['session_id'] && ! $post ) { 
				$this->logged_in = false;
				$this->login_error = 'Wrong session ID.';
				// Desconfigura qualquer sessão que possa existir sobre o usuário
				$this->logout();
			
				return;
			}
			
			// Se for um post
			if ( $post ) {
// 				echo 123;die;
				// Recria o ID da sessão
// 				session_regenerate_id();
// 				session_start();
				$session_id = session_id();
				unset($fetch['password']);
				// Envia os dados de usuário para a sessão
				$_SESSION['userdata'] = $fetch;
				
				// Atualiza a senha
				$_SESSION['userdata']['password'] = $password;
				
				// Atualiza o ID da sessão
				$_SESSION['userdata']['session_id'] = $session_id;
				// Atualiza o ID da sessão na base de dados
				$query = $this->db->query(
					'UPDATE users SET session_id = ? WHERE id = ?',
					array( $session_id, $id )
				);
				
			}
			$stores = array();
			$storeList =  unserialize( $fetch['stores'] );
			foreach($storeList as $key => $idStore){
			    $queryStore = $this->db->query(
			        'SELECT * FROM stores WHERE id = ? LIMIT 1',
			        array( $idStore )
			        );
			    $fetchStore = $queryStore->fetch(PDO::FETCH_ASSOC);
			    
			    $fetchStore['modules'] = unserialize($fetchStore['modules']);
			    
			    $modules = array();
			    
			    foreach($fetchStore['modules'] as $key => $id){
			        
			        if(!empty($id)){
			            
			            $sql = "SELECT id, type, method FROM modules WHERE id = {$id}";
			            $queryModule = $this->db->query($sql);
			            $fetchModule = $queryModule->fetch(PDO::FETCH_ASSOC);
			             
// 			            if (isset($modules[$fetchModule['type']])){
			            $modules[$fetchModule['type']][] = $fetchModule['method'];
// 			            }else{
// 			             $modules[$fetchModule['type']] = $fetchModule['method'];
// 			            }
			            
			        }
			        
			    }
			    
			    if($fetch['store_id'] == $idStore){
			    	$this->storedata =  $fetchStore;
			    	$this->moduledata =  $modules;
			    }
			    
			    $stores[$idStore] = $fetchStore['store'];
			    
			}
			$_SESSION['userdata']['stores'] = $stores;
			
			$_SESSION['userdata']['permissions'] = unserialize( $fetch['permissions'] );
// 			pre($this);die;
			$sqlGPermission = "SELECT group_permissions.module, group_permissions.p_view, group_permissions.p_create, 
			group_permissions.p_update, group_permissions.p_delete 
			FROM `permissions`
			LEFT JOIN group_permissions ON group_permissions.p_group = permissions.id 
				AND group_permissions.store_id = {$this->storedata['id']} 
			WHERE permissions.permission LIKE 'any'";
			$queryGPermission = $this->db->query($sqlGPermission);
			$groupPermission = $queryGPermission->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($groupPermission as $k => $group){
				$_SESSION['userdata']['g_permissions'][$group['module']] = array(
						'p_view' => $group['p_view'], 
						'p_create' =>$group['p_create'], 
						'p_update' =>$group['p_update'], 
						'p_delete' =>$group['p_delete']
				);
			}
			
			$this->logged_in = true;
			$this->userdata = $_SESSION['userdata'];
// 			$_SESSION['goto_url'] = HOME_URI.'/Home/Dashboard';
			// Verifica se existe uma URL para redirecionar o usuário
			if ( isset( $_SESSION['goto_url'] ) ) {
				// Passa a URL para uma varável
				$goto_url = urldecode( $_SESSION['goto_url'] );
				
				// Remove a sessão com a URL
				unset( $_SESSION['goto_url'] );
				
				// Redireciona para a página
				echo '<meta http-equiv="Refresh" content="0; url=' . $goto_url . '">';
				echo '<script type="text/javascript">window.location.href = "' . $goto_url . '";</script>';
				header( 'location: ' . $goto_url );
			}
			return;
			
		} else {
// 			echo 123;die;
			// O usuário não está logado
			$this->logged_in = false;
			
			// A senha não bateu
			$this->login_error = 'Password does not match.';
		
// 			// Remove tudo
			$this->logout();
		
			return;
		}
	}

	
	/**
	 * Logout
	 *
	 * Desconfigura tudo do usuário.
	 *
	 * @param bool $redirect Se verdadeiro, redireciona para a página de login
	 * @final
	 */
	protected function logout( $redirect = false ) {
	    
		// Remove all data from $_SESSION['userdata']
		$_SESSION['userdata'] = array();
		
		// Only to make sure (it isn't really needed)
		unset( $_SESSION['userdata'] );
		
		// Regenerates the session ID
// 		if (session_status() !== PHP_SESSION_ACTIVE) {
// 			session_unset();
// 			session_destroy();
// 			session_regenerate_id(true);
// 		}
// 		session_destroy();

// 		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_regenerate_id();
// 			$this->logged_in = false;
			if ( $redirect === true ) {
				// Send the user to the login page
				$this->goto_login();
			}
// 		}
	}
	
	/**
	 * Vai para a página de login
	 */
	protected function goto_login() {
	    
		// Verifica se a URL da HOME está configurada
		if ( defined( 'HOME_URI' ) ) { 
			// Configura a URL de login
			$login_uri  = HOME_URI . '/Login';
			
			// A página em que o usuário estava
			$_SESSION['goto_url'] = urlencode( $_SERVER['REQUEST_URI'] );
			
			$_SESSION['goto_url'] = $_SESSION['goto_url']  != '%2FUserLogout%2Fout%2F' ? $_SESSION['goto_url'] : HOME_URI ;
			
			// Redireciona
			echo '<meta http-equiv="Refresh" content="0; url=' . $login_uri . '">';
			echo '<script type="text/javascript">window.location.href = "' . $login_uri . '";</script>';
// 			header('location: ' . $login_uri);
		}
		
		return;
	}
	
	/**
	 * Envia para uma página qualquer
	 *
	 * @final
	 */
	final protected function goto_page( $page_uri = null ) {
		if ( isset( $_GET['url'] ) && ! empty( $_GET['url'] ) && ! $page_uri ) {
			// Configura a URL
			$page_uri  = urldecode( $_GET['url'] );
		}
		
		if ( $page_uri ) { 
			// Redireciona
			echo '<meta http-equiv="Refresh" content="0; url=' . $page_uri . '">';
			echo '<script type="text/javascript">window.location.href = "' . $page_uri . '";</script>';
			header('location: ' . $page_uri);
			return;
		}
	}
	
	/**
	 * Verifica permissões
	 *
	 * @param string $required A permissão requerida
	 * @param array $permissions As permissões do usuário
	 * @final
	 */
	final protected function check_permissions( 
		$required = 'any', 
		$permissions = array('any')
	) {
		
		if ( ! is_array( $permissions ) ) {
			return;
		}
		// Se o usuário não tiver permissão
		if ( ! in_array( $required, $permissions ) ) {
			// Retorna falso
			return false;
		} else {
			return true;
		}
	}
}

