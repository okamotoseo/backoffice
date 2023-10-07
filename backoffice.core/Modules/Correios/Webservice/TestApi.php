<?php
set_time_limit ( 300 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$orderId = isset($_REQUEST["order_id"]) && $_REQUEST["order_id"] != "" ? $_REQUEST["order_id"] : null ;
$pedidoId = isset($_REQUEST["pedido_id"]) && $_REQUEST["pedido_id"] != "" ? $_REQUEST["pedido_id"] : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

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
    
    $parametros = array();
    
    // Código e senha da empresa, se você tiver contrato com os correios, se não tiver deixe vazio.
    $parametros['nCdEmpresa'] = '';
    $parametros['sDsSenha'] = '';
    
    // CEP de origem e destino. Esse parametro precisa ser numérico, sem "-" (hífen) espaços ou algo diferente de um número.
    $parametros['sCepOrigem'] = '96010140';
    $parametros['sCepDestino'] = '02460000';
    
    // O peso do produto deverá ser enviado em quilogramas, leve em consideração que isso deverá incluir o peso da embalagem.
    $parametros['nVlPeso'] = '1';
    
    // O formato tem apenas duas opções: 1 para caixa / pacote e 2 para rolo/prisma.
    $parametros['nCdFormato'] = '1';
    
    // O comprimento, altura, largura e diametro deverá ser informado em centímetros e somente números
    $parametros['nVlComprimento'] = '16';
    $parametros['nVlAltura'] = '5';
    $parametros['nVlLargura'] = '15';
    $parametros['nVlDiametro'] = '0';
    
    // Aqui você informa se quer que a encomenda deva ser entregue somente para uma determinada pessoa após confirmação por RG. Use "s" e "n".
    $parametros['sCdMaoPropria'] = 's';
    
    // O valor declarado serve para o caso de sua encomenda extraviar, então você poderá recuperar o valor dela. Vale lembrar que o valor da encomenda interfere no valor do frete. Se não quiser declarar pode passar 0 (zero).
    $parametros['nVlValorDeclarado'] = '200';
    
    // Se você quer ser avisado sobre a entrega da encomenda. Para não avisar use "n", para avisar use "s".
    $parametros['sCdAvisoRecebimento'] = 'n';
    
    // Formato no qual a consulta será retornada, podendo ser: Popup – mostra uma janela pop-up | URL – envia os dados via post para a URL informada | XML – Retorna a resposta em XML
    $parametros['StrRetorno'] = 'xml';
    
    // Código do Serviço, pode ser apenas um ou mais. Para mais de um apenas separe por virgula.
	// $parametros['nCdServico'] = '40010';
    $parametros['nCdServico'] = '41106';
    
    
    $parametros = http_build_query($parametros);
    $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';
    $curl = curl_init($url.'?'.$parametros);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $dados = curl_exec($curl);
    $dados = simplexml_load_string($dados);
    
    foreach($dados->cServico as $linhas) {
    	if($linhas->Erro == 0) {
    		echo $linhas->Codigo.'</br>';
    		echo $linhas->Valor .'</br>';
    		echo $linhas->PrazoEntrega.' Dias </br>';
    	}else {
    		echo $linhas->MsgErro;
    	}
    }
    
}