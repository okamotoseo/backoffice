<?php
/**
 * Gerencia Models, Controllers e Views
 *
 */
class App
{

	/**
	 * $controlador
	 *
	 * Receberá o valor do controlador (Vindo da URL).
	 * exemplo.com/controlador/
	 *
	 * @access private
	 */
	private $controlador;
	
	/**
	 * $acao
	 *
	 * Receberá o valor da ação (Também vem da URL):
	 * exemplo.com/controlador/acao
	 *
	 * @access private
	 */
	private $acao;
	
	/**
	 * $parametros
	 *
	 * Receberá um array dos parâmetros (Também vem da URL):
	 * exemplo.com/controlador/acao/param1/param2/param50
	 *
	 * @access private
	 */
	private $parametros;
	
	/**
	 * $not_found
	 *
	 * Caminho da página não encontrada
	 *
	 * @access private
	 */
	private $not_found = '/Views/_includes/404.php';
	
	/**
	 * Construtor para essa classe
	 *
	 * Obtém os valores do controlador, ação e parêmetros. Configura 
	 * o controlado e a ação (método).
	 */
	public function __construct () {
		
		$this->get_url_data();

		/**
		 * Verifica se o controlador existe. Caso contrário, adiciona o
		 * controlador padrão (Controllers/home-controller.php) e chama o método index().
		 */
		if ( ! $this->controlador ){
			require_once ABSPATH . '/Controllers/HomeController.php';
			$this->controlador = new HomeController();
			$this->controlador->Dashboard();
// 			$this->controlador->index();
			
			return;

		}
		
		// Se o arquivo do controlador não existir, não faremos nada
		if ( ! file_exists( ABSPATH . '/Controllers/' . $this->controlador . '.php' ) ) {
			
			// Página não encontrada
			require_once ABSPATH . $this->not_found;
			
			return;
		}
		// Inclui o arquivo do controlador
		require_once ABSPATH . '/Controllers/' . $this->controlador . '.php';
// 		die;
		// Remove caracteres inválidos do nome do controlador para gerar o nome
		// da classe. Se o arquivo chamar "news-controller.php", a classe deverá
		// se chamar NewsController.
		$this->controlador = preg_replace( '/[^a-zA-Z]/i', '', $this->controlador );
		// Se a classe do controlador indicado não existir, não faremos nada
		if ( ! class_exists( $this->controlador ) ) {
			// Página não encontrada
			
			require_once ABSPATH . $this->not_found;
			// FIM :)
			return;
		}
		
		// Cria o objeto da classe do controlador e envia os parâmetros
		$this->controlador = new $this->controlador( $this->parametros );
		// Remove caracteres inválidos do nome da ação (método)
		$this->acao = preg_replace( '/[^0-9a-zA-Z]/i', '', $this->acao );
		// Se o método indicado existir, executa o método e envia os parâmetros
		if ( method_exists( $this->controlador, $this->acao ) ) {
			
			$this->controlador->{$this->acao}( $this->parametros );
			
			// FIM :)
			return;
		} // method_exists

		// Sem ação, chamamos o método index
		if ( ! $this->acao && method_exists( $this->controlador, 'index' ) ) {
			
			$this->controlador->index( $this->parametros );	
			
			return;
		}
		
		// Página não encontrada
		require_once ABSPATH . $this->not_found;
		
		return;
	} 
	
	/**
	 * Obtém parâmetros de $_GET['path']
	 *
	 * Obtém os parâmetros de $_GET['path'] e configura as propriedades 
	 * $this->controlador, $this->acao e $this->parametros
	 *
	 * A URL deverá ter o seguinte formato:
	 * https://backoffice.sysplace.com.br/controlador/acao/parametro1/parametro2/etc...
	 */
	public function get_url_data () {
		// Verifica se o parâmetros path foi enviado
		
		if ( isset( $_GET['path'] ) ) {
			
			// Captura o valor de $_GET['path']
			$path = $_GET['path'];
			// Limpa os dados
            $path = rtrim($path, '/');
            $path = filter_var($path, FILTER_SANITIZE_URL);
			// Cria um array de parâmetros
			$path = explode('/', $path);
			// Configura as propriedades
			
// 			$path[0] = ucfirst($path[2]);
// 			$path[1] = ucfirst($path[3]);
// 			$path[2] = ucfirst($path[2]);
			
// 			for ($i = 0; $i < 4; $i++) {
// 			    $path[$i] = ucfirst($path[$i]);
// 			}
			
			$this->controlador  = chk_array( $path, 0 );
// 			$this->controlador = ucfirst($this->controlador);
			$this->controlador .= 'Controller';
			$this->acao         = chk_array( $path, 1 );
// 			$this->acao = ucfirst($this->acao);
			// Configura os parâmetros
// 			if ( chk_array( $path, 2 ) ) {
// 				unset( $path[0] );
// 				unset( $path[1] );
				
				// Os parâmetros sempre virão após a ação
// 				pre($path);die;

			
				$this->parametros = array_values( $path );
// 			}
// 				pre($this->parametros);die;
			// DEBUG
			//
// 			echo $this->controlador . '<br>';
// 			echo $this->acao        . '<br>';
// 			echo '<pre>';
// 			print_r( $this->parametros );
// 			echo '</pre>'; 
		}
	
	}
	
} 