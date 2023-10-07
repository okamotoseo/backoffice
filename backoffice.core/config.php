<?php
/**
 * Configução geral
 */
 
// Caminho para a raiz
define( 'ABSPATH', dirname( __FILE__ ) );
// Caminho para a pasta de uploads
define( 'UP_ABSPATH', ABSPATH . '/Views/_uploads' );
// URL da home
define( 'HOME_URI', 'https://'.$_SERVER['HTTP_HOST']);

define( 'HOSTNAME', '' );
// Nome do DB
define( 'DB_NAME', '' );
 
// Usuário do DB
define( 'DB_USER', '' );
 
// Senha do DB
define( 'DB_PASSWORD', '' );
 
// Charset da conexão PDO
define( 'DB_CHARSET', 'utf8' );
 
// Se você estiver desenvolvendo, modifique o valor para true
define( 'DEBUG', false );

define('QTDE_REGISTROS', 50);

define('RANGE_PAGINAS', 1);
 
?>
