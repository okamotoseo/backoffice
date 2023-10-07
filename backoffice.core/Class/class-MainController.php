<?php
/**
 * MainController - Todos os controllers deverão estender essa classe
 *
 */
class MainController extends UserLogin
{
	/**
	 * $db
	 *
	 * Nossa conexão com a base de dados. Manterá o objeto PDO
	 *
	 * @access public
	 */
	public $db;
	/**
	 * $phpass
	 *
	 * Classe phpass 
	 *
	 * @see http://www.openwall.com/phpass/
	 * @access public
	 */
	public $phpass;
	
	/**
	 * $panel
	 *
	 * Título da seção
	 *
	 * @access public
	 */
	public $panel;
	
	
	/**
	 * $title
	 *
	 * Título das páginas 
	 *
	 * @access public
	 */
	public $title;
	
	/**
	 * $menu
	 *
	 * Controla o menu ativo
	 *
	 * @access public
	 */
	public $menu = array();
	
	
	public $control_panel = 'Control Panel';
	
	
	/**
	 * Widgets
	 *
	 * Controla o recolhimento e modelo do Widgets
	 *
	 * @access public
	 */
	public $widgets = array(
	    "expandable" => array(
    	    "model" => "box-info collapsed-box",
	        "icon" => "fa-plus"
	    ),
	    "collapsable" => array(
	        "model" => "box-info",
	        "icon" => "fa-minus"
	    )
	    
	);
	
	/**
	 * $box
	 *
	 * Configura o modelo do container
	 *
	 * @access public
	 */
	public $box = array("form", "table");
	
	/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = false;
	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required = 'any';
	/**
	 * $parametros
	 *
	 * @access public
	 */
	public $parametros = array();
	
	/**
	 * $includes css, js ...
	 *
	 * @access public
	 */
	public $includes = array();
	
	/**
	 * Construtor da classe
	 *
	 * Configura as propriedades e métodos da classe.
	 *
	 * @since 0.1
	 * @access public
	 */
	
	public $manutencao = false;
	
	public function __construct ( $parametros = array() ) {
	    if($this->manutencao){
	        
	        require ABSPATH . '/Views/_includes/manutencao.php';
	        
	        exit;
	        
	    }
	    
	    $this->box['form'] = $this->widgets['collapsable'];
	    
	    $this->box['table'] = $this->widgets['collapsable'];
	    
		$this->db = new DbConnection();
		
		$this->phpass = new PasswordHash(8, false);
		
		$this->parametros = $parametros;
// 		pre($parametros);die;
		$this->store_session();
		
		$this->check_userlogin();
		
// 		pre($this);die;
		if($this->login_required){
			
			if ( ! $this->logged_in ) {
// 				pre($_POST);die;
				$this->logout();
				
// 				$this->goto_login();

				require ABSPATH . '/Views/Login/LoginView.php';
				
				exit;
			
			}

		
    		if (!$this->check_permissions($this->permission_required, $this->userdata['permissions'])) {
    		    
    		    require ABSPATH . '/Views/_includes/header.php';
    		    
    		    require ABSPATH . '/Views/_includes/denied.php';
    		    
    		    require ABSPATH . '/Views/_includes/footer.php';
    
    			exit;
    		}
    		
    		
		}
		
	}
	
	/**
	 * Load Model
	 *
	 * Carrega os modelos presentes na pasta /Models/.
	 *
	 * @access public
	 */
	public function load_model( $model_name = false ) {
	    
		// Um arquivo deverá ser enviado
		if ( ! $model_name ) return;
		
		// Inclui o arquivo
		$model_path = ABSPATH . '/Models/' . $model_name . '.php';
		
		// Verifica se o arquivo existe
		if ( file_exists( $model_path ) ) {
		
			// Inclui o arquivo
			require_once $model_path;
			
			// Remove os caminhos do arquivo (se tiver algum)
			$model_name = explode('/', $model_name);
			
			// Pega só o nome final do caminho
			$model_name = end( $model_name );
			
			// Remove caracteres inválidos do nome do arquivo
			$model_name = preg_replace( '/[^a-zA-Z0-9]/is', '', $model_name );
			
			// Verifica se a classe existe
			if ( class_exists( $model_name ) ) {
			    
				// Retorna um objeto da classe
				return new $model_name( $this->db, $this );
			
			}
			
			return;
			
		} 
		
	}
	
	/**
	 * Load Module
	 *
	 * Carrega os modulos presentes na pasta /Modules/.
	 *
	 * @access public
	 */
	public function load_module_controller( $module_name = false ) {
	    
	    // Um arquivo deverá ser enviado
	    if ( ! $module_name ) return;
	    
	    // Inclui o arquivo
	    $module_path = ABSPATH . '/Modules/' . $module_name . '.php';
	    // Verifica se o arquivo existe
	    if ( file_exists( $module_path ) ) {
	        
	        // Inclui o arquivo
	        require_once $module_path;
	        
	        // Remove os caminhos do arquivo (se tiver algum)
	        $module_name = explode('/', $module_name);
	        
	        // Pega só o nome final do caminho
	        $module_name = end( $module_name );
	        
	        // Remove caracteres inválidos do nome do arquivo
	        $module_name = preg_replace( '/[^a-zA-Z0-9]/is', '', $module_name );
	        
	        // Verifica se a classe existe
	        if ( class_exists( $module_name ) ) {
	            
	            // Retorna um objeto da classe
	            return new $module_name( $this->db, $this );
	            
	        }
	        
	        return;
	        
	    }
	    
	}
	
	/**
	 * Load Module Model
	 *
	 * Carrega os modelos presentes na pasta /Models/.
	 *
	 * @access public
	 */
	public function load_module_model( $model_name = false ) {
	    
	    // Um arquivo deverá ser enviado
	    if ( ! $model_name ) return;
	    
	    // Inclui o arquivo
	    $model_path = ABSPATH . '/Modules/' . $model_name . '.php';
	    
	    // Verifica se o arquivo existe
	    if ( file_exists( $model_path ) ) {
	        
	        // Inclui o arquivo
	        require_once $model_path;
	        
	        // Remove os caminhos do arquivo (se tiver algum)
	        $model_name = explode('/', $model_name);
	        
	        // Pega só o nome final do caminho
	        $model_name = end( $model_name );
	        
	        // Remove caracteres inválidos do nome do arquivo
	        $model_name = preg_replace( '/[^a-zA-Z0-9]/is', '', $model_name );
	        
	        // Verifica se a classe existe
	        if ( class_exists( $model_name ) ) {
	            
	            // Retorna um objeto da classe
	            return new $model_name( $this->db, $this );
	            
	        }
	        
	        return;
	        
	    }
	    
	}
	
} 