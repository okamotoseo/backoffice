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
    	
    	case 'rate_shipping':
    		$cepOrigem = '17526330';
    		
    		$cepDestino = isset($_REQUEST["cep_destino"]) && $_REQUEST["cep_destino"] != "" ? $_REQUEST["cep_destino"] : null ;
    		
//     		$returnType = isset($_REQUEST["return_type"]) && $_REQUEST["return_type"] != "" ? $_REQUEST["return_type"] : null ;
    		if(!isset($cepDestino)){
    			
    			return;
    		}
    		
    		$weight = isset($_REQUEST["weight"]) && $_REQUEST["weight"] != "" ? $_REQUEST["weight"] : null ;
    		if(!isset($weight)){
    			$weight = isset($storeConfig['Checkout']['weight_min']) && !empty($storeConfig['Checkout']['weight_min']) ? $storeConfig['Checkout']['weight_min'] : '0.3' ;
    		}
    		$height = isset($_REQUEST["height"]) && $_REQUEST["height"] != "" ? $_REQUEST["height"] : null ;
    		if(!isset($height)){
    			$height = isset($storeConfig['Checkout']['height']) && !empty($storeConfig['Checkout']['height_min']) ? $storeConfig['Checkout']['height_min'] : 16 ;
    		}
    		$width = isset($_REQUEST["width"]) && $_REQUEST["width"] != "" ? $_REQUEST["width"] : null ;
    		if(!isset($width)){
    			$width = isset($storeConfig['Checkout']['lenwidthgth']) && !empty($storeConfig['Checkout']['width_min']) ? $storeConfig['Checkout']['width_min'] : 2;
    		}
    		$length = isset($_REQUEST["length"]) && $_REQUEST["length"] != "" ? $_REQUEST["length"] : null ;
    		if(!isset($length)){
    			$length = isset($storeConfig['Checkout']['length_min']) && !empty($storeConfig['Checkout']['length_min']) ? $storeConfig['Checkout']['length_min'] : 11 ;
    		}
    		
    		
    		$total_peso = 0;
    		$total_cm_cubico = 0;
    		
    		/**
    		 * $produto = lista de produtos no carrinho de compras. Deve possuir,
    		 * obrigatoriamente, os campos largura, altura, comprimento e quantidade.
    		 */
    		$total_peso = number_format($weight, 2, '.', '');
    		$total_cm_cubico = ($height * $width * $length)/6000;
    		    
    		$raiz_cubica = round(pow($total_cm_cubico, 1/3), 2);
    		$resposta = array(
    		    'total_peso' => $total_peso,
    		    'raiz_cubica' => $raiz_cubica,
    		);
    		// Os valores 16, 2, 11 e 0.3 são os valores mínimos determinados pelo serviço dos Correios
    		$width =  $raiz_cubica < 16 ? 16 : $raiz_cubica;
    		$height = $raiz_cubica < 2 ? 2 : $raiz_cubica;
    		$length = $raiz_cubica < 11 ? 11 : $raiz_cubica;
    		$weight = $total_peso < 0.3 ? 0.3 : $total_peso;
    		$diametro = hypot($width, $length); // Calculando a hipotenusa pois minhas encomendas são retangulares
    		
//     		'nVlPeso'        => $peso,
//     		'nVlComprimento' => $comprimento,
//     		'nVlAltura'      => $altura,
//     		'nVlLargura'     => $largura,
//     		'nVlDiametro'    => $diametro, // não obrigatório
    		
    		$VlValorDeclarado = isset($_REQUEST["VlValorDeclarado"]) && $_REQUEST["VlValorDeclarado"] != "" ? $_REQUEST["VlValorDeclarado"] : null ;
    		if(!isset($VlValorDeclarado)){
    			$VlValorDeclarado = isset($storeConfig['Checkout']['VlValorDeclarado_min']) && !empty($storeConfig['Checkout']['VlValorDeclarado_min']) ? $storeConfig['Checkout']['VlValorDeclarado_min'] : 50 ;
    		}
    		
    		$response = array();
    		$services = array('41106', '40010');
    		
    		foreach($services as $service){
		    $parametros = array();
		    // Código e senha da empresa, se você tiver contrato com os correios, se não tiver deixe vazio.
		    $parametros['nCdEmpresa'] = '';
		    $parametros['sDsSenha'] = '';
		    // CEP de origem e destino. Esse parametro precisa ser numérico, sem "-" (hífen) espaços ou algo diferente de um número.
		    $parametros['sCepOrigem'] = $cepOrigem;
		    $parametros['sCepDestino'] = $cepDestino;
		    // O peso do produto deverá ser enviado em quilogramas, leve em consideração que isso deverá incluir o peso da embalagem.
		    $parametros['nVlPeso'] = $weight ; 
		    // O formato tem apenas duas opções: 1 para caixa / pacote e 2 para rolo/prisma.
		    $parametros['nCdFormato'] = '1';
		    // O comprimento, altura, largura e diametro deverá ser informado em centímetros e somente números
		    $parametros['nVlComprimento'] = $width;
		    $parametros['nVlAltura'] = $height;
		    $parametros['nVlLargura'] = $length;
// 		    $parametros['nVlDiametro'] = $diametro;
		    // Aqui você informa se quer que a encomenda deva ser entregue somente para uma determinada pessoa após confirmação por RG. Use "s" e "n".
		    $parametros['sCdMaoPropria'] = 's';
		    // O valor declarado serve para o caso de sua encomenda extraviar, então você poderá recuperar o valor dela. Vale lembrar que o valor da encomenda interfere no valor do frete. Se não quiser declarar pode passar 0 (zero).
		    $parametros['nVlValorDeclarado'] = $VlValorDeclarado > 50 ? $VlValorDeclarado : 0 ;
		    // Se você quer ser avisado sobre a entrega da encomenda. Para não avisar use "n", para avisar use "s".
		    $parametros['sCdAvisoRecebimento'] = 'n';
		    // Formato no qual a consulta será retornada, podendo ser: Popup – mostra uma janela pop-up | URL – envia os dados via post para a URL informada | XML – Retorna a resposta em XML
		    $parametros['StrRetorno'] = 'xml';
		    // Código do Serviço, pode ser apenas um ou mais. Para mais de um apenas separe por virgula.
			// $parametros['nCdServico'] = '40010';
// 		    $parametros['nCdServico'] = '41106';

		        
		        $parametros['nCdServico'] = $service;
		    
    		    $parametros = http_build_query($parametros);
//     		    print_r($parametros);
    		    $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';
    		    $curl = curl_init($url.'?'.$parametros);
    		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    		    $dados = curl_exec($curl);
    		    $dados = simplexml_load_string($dados);
    		    $exit = false;
    		    foreach($dados->cServico as $linhas) {
    		    	if($linhas->Erro == 0) {
    		    		
    					$codigo = trim($linhas->Codigo."");
    					switch($codigo){
    						case'41106': $service ='PAC'; break;
    						case'40010': $service = 'Sedex'; break;
    						default: $service = 'Correios'; break;
    					}
    		    		
    		    		$response[] = array(
    		    				'Codigo' => $codigo,
    		    				'Servico' => $service,
    		    				'Valor' => $linhas->Valor."",
    		    				'PrazoEntrega' => $linhas->PrazoEntrega."",
    		    		        'medidas' => $resposta
    		    		);
    		    		
    		    	}else {
    		    		$response[] = array('Message' => $linhas->MsgErro."");
    		    		$exit = true;
    		    		break;
    		    	}
    		    }
    		    
    		    if($exit){
    		        break;
    		    }
		    
		    }
		    
		    echo $callback . '(' . json_encode($response) . ')';
		    
		    break;
    
    }
    
}