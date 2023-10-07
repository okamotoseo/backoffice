<?php

// ini_set('memory_limit', '256M');
ini_set('session.gc_maxlifetime', 648000); // 3 horas
ini_set('session.cookie_lifetime', 648000);
// ini_set('session.cache_expire', 648000);

// Evita que usuários acesse este arquivo diretamente
if ( ! defined('ABSPATH')) exit;

// $cache_expire = session_cache_expire();
// print_r($_SESSION);die;
if ( ! defined('DEBUG') || DEBUG === true) {

	ini_set("display_errors", true);
	error_reporting(~E_DEPRECATED & ~E_NOTICE & E_ALL);

} else {

	ini_set("display_errors", false);
	error_reporting(E_ALL);

}



// $cache_limiter = session_cache_limiter('private_no_expire');
// print_r($cache_limiter);

// session_cache_expire(64800);
session_cache_expire(60);

// if (session_status() !== PHP_SESSION_DISABLED) {
// 	echo 123;die;
// $requestPath = $_SERVER['REQUEST_URI'];
// if($requestPath != '/Login' AND $requestPath != '/UserLogout/out/'){

// 	print_r($requestPath);
	session_start();
	
// }
// }


// echo phpinfo();die;
// die;
/**
 * Função para carregar automaticamente todas as classes padrão
 * Ver: http://php.net/manual/pt_BR/function.autoload.php.
 * Nossas classes estão na pasta classes/.
 * O nome do arquivo deverá ser class-NomeDaClasse.php.
 */

spl_autoload_register(function ($class_name) {
    
    $file = ABSPATH . '/Class/class-' . $class_name . '.php';
    
    $t = debug_backtrace();
    $path = explode(ABSPATH, $t[1]['file']);
    $parts = explode("/", $path[1]);
    
    if($parts[1] == "Modules" AND $class_name != "MainModel" AND $class_name != "MainController"){
        $file = ABSPATH . "/{$parts[1]}/{$parts[2]}/Class/class-{$class_name}.php";
    }
    
    if ( ! file_exists( $file ) ) {
        require_once ABSPATH . '/Views/_includes/404.php';
        return;
    }
    
    require_once $file;
});

require_once ABSPATH . '/Functions/global-functions.php';
 

$app = new App();


?>