<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';

require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Interfaces/ItemBagInterface.php';
require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Interfaces/ItemInterface.php';
require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Interfaces/PessoaInterface.php';

require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Core/Controller.php';
require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Core/Entity.php';
require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Core/ItemBag.php';

require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Entities/Item.php';

require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Entities/Pessoa.php';

require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/DeclaracaoConteudo.php';


 

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$orderId = isset($_REQUEST["order_id"]) && $_REQUEST["order_id"] != "" ? $_REQUEST["order_id"] : null ;
$pedidoId = isset($_REQUEST["pedido_id"]) && $_REQUEST["pedido_id"] != "" ? $_REQUEST["pedido_id"] : null ;
$productId = isset($_REQUEST["product_id"]) && $_REQUEST["product_id"] != "" ? $_REQUEST["product_id"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;
$callback = isset($_REQUEST["callback"]) && $_REQUEST["callback"] != "" ? $_REQUEST["callback"] : null ;

if (empty ( $action ) and empty ( $storeId ) ) {
    
    if(isset($_SERVER ['argv'] [1])){
        $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
        $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    }
    
    if(isset($_SERVER ['argv'] [2])){
        $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
        $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
    }
    
    $request = "System";
    
}

if(isset($storeId)){
    
    $db = new DbConnection();
    
    $storeConfig = getStoreConfig($db, $storeId);
    

    switch ($action){
    	
    	case 'declara_conteudo':
    	    
    	    
//     	    echo 123;die;
    	    
    	    $remetente = new Click4Web\DeclaracaoConteudo\Entities\Pessoa([
            	    'nome' => 'Click 4 Web - Marketing',
            	    'doc' => '12.398.650/0001-16',
            	    'endereco' => 'Rua Mandaguari, 400 - Vila Curuça',
            	    'cidade' => 'Santo André',
            	    'estado' => 'SP',
            	    'cep' => '09290-660'
        	    ]);
    	    
//     	    pre($remetente);die;
    	    
    	    $destinatario = new Click4Web\DeclaracaoConteudo\Entities\Pessoa();
    	    $destinatario->setNome('TagCool')
        	    ->setDoc('21.814.544/0001-67')
        	    ->setEndereco('Rua Albuquerque Lins, 128 - Jardim Paulista')
        	    ->setCidade('Ribeirão Preto')
        	    ->setEstado('SP')
        	    ->setCep('14090-010');
    	    
    	    
    	    $itens = new \Click4Web\DeclaracaoConteudo\Core\ItemBag([
    	        [
    	            'descricao' => 'Livro - 8Ps do Marketing Digital',
    	            'quantidade' => 1,
    	            'peso' => 0.733
    	        ],
    	        [
    	            'descricao' => 'Livro - Super Apresentações',
    	            'quantidade' => 1,
    	            'peso' => 0.397
    	        ],
    	    ]);
    	    
    	    $declaracao = new \Click4Web\DeclaracaoConteudo\DeclaracaoConteudo(
    	        $remetente,
    	        $destinatario,
    	        $itens,
    	        219.98 // Valor Total (R$)
    	        );
    	    
    	    
    	    
    	    
    	    echo $declaracao->imprimirHtml();
    	    
    	    break;
    	    
    	    
    	    
    	    
    	    
    	    
    }
    
    
    
    
}
require_once $path .'/../../../library/mozgbrasil/declara_conteudo/src/Resources/views/declaracao-conteudo-bootstrap.php';