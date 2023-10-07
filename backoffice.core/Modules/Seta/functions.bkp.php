<?php


// /**
//  * Remove acentos
//  * @param string $brand
//  * @return boolean
//  */
// function RemoveAcentos($string){
// 	return preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $string ) );
// }
// function removeAcentos($s)
// {
//     $arr1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
//     $arr2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
//     return str_replace($arr1, $arr2, $s);
// }


function getPedidoSeta($connPg, $date){
    $orderId = array();
    $sql ="SELECT codigo, obs FROM vendas WHERE vendedor = '000261' AND data >= '{$date}'";
    $res = pg_query($connPg, $sql);
    
    while($value = pg_fetch_assoc($res)){
        $result = everything_in_tags($value['obs'], "VENDA");
        if(!empty($result)){
            $orderId[] = $result;
        }
    }
    return $orderId;
}
function getEstoqueEmpresa($connPg, $produtoId){
    
    $produtoId = strlen($produtoId) == 8 ? $produtoId : "0".$produtoId;
    
    $estoqueEmpresa = array();
    $sql ="SELECT * FROM vestoques WHERE Produto = '{$produtoId}' ORDER BY empresa ASC";
    $res = pg_query($connPg, $sql);
    while($value = pg_fetch_array($res)){
        switch($value['empresa']){
            case 1 : $emp =  "M" ; break;
            case 2 : $emp =  "G" ; break;
            case 3 : $emp =  "T" ; break;
        }
        $estoqueEmpresa[$emp] = $value['quantidade'];
    }
    return $estoqueEmpresa;
}

function getEstoqueEmpresaTotal($connPg, $produtoId){
    
    $estoqueEmpresa = array();
    $sql ="SELECT empresa, quantidade FROM vestoques WHERE Produto LIKE '{$produtoId}%' ORDER BY empresa ASC";
    $res = pg_query($connPg, $sql);
    while($value = pg_fetch_array($res)){
        switch($value['empresa']){
            case 1 : $estoqueEmpresa["M"] += $value['quantidade']; break;
            case 2 : $estoqueEmpresa["G"] += $value['quantidade']; break;
            case 3 : $estoqueEmpresa["T"] += $value['quantidade']; break;
        }
    }
    return $estoqueEmpresa;
}

function getVendasEmpresa($connPg, $produtoId, $data){
    $productId = strlen($produtoId) > 6 ? substr($produtoId, 0, 6) : $produtoId;
    
    if(!empty($data)){
        $condition = "AND movimento.data >= '{$data}'";
    }
    $vendasEmpresa = array();
    $sql ="SELECT movimento.codigo, movimento.auxiliar,
	movimento.empresa, movimento.produto,movimento.quantidade, vendas.vendedor
	FROM movimento JOIN vendas on vendas.codigo = substr(movimento.auxiliar, 3,8)
	WHERE movimento.produto like '{$produtoId}%' AND movimento.operacao = 'VE' {$condition} AND movimento.estoque = true";
    $res = pg_query($connPg, $sql);
    while ($row = pg_fetch_assoc($res)){
        switch($row['empresa']){
            case 1 :
                if($row['vendedor'] == '000261'){
                    $vendasEmpresa["E"] += $row['quantidade'];
                }else{
                    $vendasEmpresa["M"] += $row['quantidade'];
                }
                break;
            case 2 : $vendasEmpresa["G"] += $row['quantidade']; break;
            case 3 : $vendasEmpresa["T"] += $row['quantidade']; break;
        }
    }
    
    foreach ($vendasEmpresa as $key => $value){
        $totalEmpresa += $value;
    }
    $vendasEmpresa["EP"] = porcentagem_nx($vendasEmpresa["E"], $totalEmpresa);
    $vendasEmpresa["MP"] = porcentagem_nx($vendasEmpresa["M"], $totalEmpresa);
    $vendasEmpresa["GP"] = porcentagem_nx($vendasEmpresa["G"], $totalEmpresa);
    $vendasEmpresa["TP"] = porcentagem_nx($vendasEmpresa["T"], $totalEmpresa);
    
    return $vendasEmpresa;
}
function getDataUltimaCompraProduto($connPg, $produtoId){
    // 	$sqlCount ="SELECT count(codigo) as empresas FROM config";
    // 	$count = pg_fetch_assoc(pg_query($connPg, $sqlCount));
    $count['empresas'] = 3;
    for($i = 1; $i <= $count['empresas']; $i++ ){
        $sql ="SELECT max(data) FROM movimento WHERE produto LIKE '{$produtoId}%' AND empresa = '0{$i}' AND operacao = 'EN'";
        $res = pg_query($connPg, $sql);
        $value[$i] = pg_fetch_assoc($res);
    }
    $data = array();
    foreach($value as $key => $attr){
        if(!empty($attr['max'])){
            array_push($data, $attr['max']);
        }
    }
    return max($data);
}
function getEstoqueProduto($connPg, $produtoId){
    
    $sql ="SELECT sum(quantidade) as total FROM vestoques WHERE Produto = '{$produtoId}'";
    $res = pg_query($connPg, $sql);
    $value = pg_fetch_assoc($res);
    return $value['total'];
}
function getEstoqueProdutoGrade($connPg, $produtoId){
    
    $sql ="SELECT sum(quantidade) as total, produto FROM vestoques WHERE produto LIKE '{$produtoId}%' AND quantidade > 0 GROUP BY produto";
    $res = pg_query($connPg, $sql);
    while($value = pg_fetch_assoc($res)){
        
        $product["{$value['produto']}"] = $value['total'];
    }
    if(empty($product)){
        $product["{$value['produto']}"] = 0;
    }
    return $product;
}
function getSizeProduct($connPg, $produtoId){
    $tamanho = substr($produtoId, -2);
    $sku = substr($produtoId, 0, -2 );
    $sqlGrade ="SELECT grade FROM produtos WHERE codigo = '{$sku}'";
    $resGrade = pg_fetch_assoc(pg_query($connPg, $sqlGrade));
    
    $sqlTamanhos ="SELECT tamanhos FROM grades WHERE codigo = '{$resGrade['grade']}'";
    $resTamanhos = pg_fetch_assoc(pg_query($connPg, $sqlTamanhos));
    $tamanhos = explode(",",$resTamanhos['tamanhos']);
    $key = array_search($tamanho, $tamanhos)+1;
    $key =  strlen($key) == 1 ? "0".$key : $key ;
    $sqlLegenda =  "SELECT legenda{$key} as legenda FROM grades WHERE codigo = '{$resGrade['grade']}'";
    $resLegenda = pg_fetch_assoc(pg_query($connPg, $sqlLegenda));
    
    return $resLegenda['legenda'];
}
function getVendasProduto($connPg, $produtoId, $data){
    if(!empty($data)){
        $condition = "AND data >= '{$data}'";
    }
    $sql ="SELECT sum(quantidade) as vendas FROM movimento WHERE produto = '{$produtoId}' AND operacao = 'VE' {$condition} AND estoque = true";
    $res = pg_query($connPg, $sql);
    $value = pg_fetch_assoc($res);
    return $value;
}

function getCompraProduto($connPg, $produtoId, $data){
    if(!empty($data)){
        $condition = "AND data >= '{$data}'";
    }
    $sql ="SELECT sum(quantidade) as entradas FROM movimento WHERE produto = '{$produtoId}' AND operacao = 'EN'  {$condition} AND estoque = true";
    $res = pg_query($connPg, $sql);
    $value = pg_fetch_assoc($res);
    return $value;
}


function getSalePrice($connPg, $produtoId){
    $sql ="SELECT precoavista FROM eprecoestoque WHERE produto = '{$produtoId}'";
    $res = pg_query($connPg, $sql);
    $value = pg_fetch_array($res);
    
    return $value['precoavista'];
}
function getSalePriceVariation($connPg, $produtoId){
    $sql ="SELECT precoavista FROM eprecoestoque WHERE variante = '{$produtoId}'";
    $res = pg_query($connPg, $sql);
    $value = pg_fetch_array($res);
    
    return $value['precoavista'];
}

// Ao remover da função principal ativar aki
// function getTotalVariante($connPg, $produtoId){
//     $estoqueEmpresa = array();
//     $sql ="SELECT DISTINCT produto FROM vestoques WHERE produto LIKE '{$produtoId}%' AND quantidade > 0 GROUP BY produto";
//     $res = pg_query($connPg, $sql);
//     $count = 0;
//     while($totalVariante = pg_fetch_assoc($res)){
//         $count++;
//     }
//     return $count;
// }
function getProductInfo($connPg, $productId){
    $sql ="
	SELECT
	produtos.codigo, produtos.descricao AS nome, produtos.corx AS cor,
	produtos.referencia, produtos.cadastro, produtos.ecommerce,
	marcas.descricao AS marca, produtos.desativar
	
	FROM produtos
	
	LEFT JOIN marcas ON produtos.marca = marcas.codigo
	
	WHERE produtos.codigo = '{$productId}'
	";
    $res = pg_query($connPg, $sql);
    $value = pg_fetch_assoc($res);
    $sqlVestoques = "SELECT quantidade, ultimacompra FROM vprodutos WHERE codigo = '{$value['codigo']}'";
    $estoque = pg_fetch_assoc(pg_query($connPg, $sqlVestoques));
    $value['quantidade'] = intval($estoque['quantidade']);
    $value['ultimacompra'] = $estoque['ultimacompra'];
    
    return $value;
}


function getQuantityProductSeta($connPg){
    
    $sqlCount ="SELECT count(codigo) FROM produtos";
    $resCount = pg_query($connPg, $sqlCount);
    $count = pg_fetch_array($resCount, 0);
    
    return intval($count[0]);
}

function getHtmlStockStore($connPgSeta, $sku){
    $produtoEstoqueEmpresa = getEstoqueEmpresa($connPgSeta, $sku);
    $res = "";
    foreach($produtoEstoqueEmpresa as $empresa => $quantidade){
        $quantidade = trim(intval($quantidade));
        $res .= "{$empresa}: {$quantidade} <br>";
    }
    return $res;
}





/***************************** remover colocar no masterxml functions **********************/
function formataData($data)
{
    list($ano, $mes, $dia) = explode('-', $data);
    
    if(intval($ano . $mes . $dia) === 0)
        return '';
        
        return $dia . '/' . $mes . '/' . $ano;
}

function formataTelefone($numero)
{
    $numero = preg_replace('/[^0-9]/', '', $numero);
    
    if(strlen($numero) === 10)
    {
        return '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 4) . '-' . substr($numero, 6, 4);
    } else if(strlen($numero) === 11)
    {
        return '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 5) . '-' . substr($numero, 7, 4);
    }
    
    return '';
}

function formataCEP($cep)
{
    $cep = preg_replace('/[^0-9]/', '', $cep);
    
    if(strlen($cep) === 8)
        return substr($cep, 0, 2) . '.' . substr($cep, 2, 3) . '-' . substr($cep, 5, 3);
        
        return '';
}

function formataCpfCnpj($doc)
{
    $out = '';
    $doc = preg_replace('/[^0-9]/', '', $doc);
    
    if(strlen($doc) === 11)
        $out = substr($doc, 0, 3) . '.' . substr($doc, 3, 3) . '.' . substr($doc, 6, 3) . '-' . substr($doc, 9,2);
        else if(strlen($doc) === 14)
            $out = substr($doc, 0, 2) . '.' . substr($doc, 2, 3) . '.' . substr($doc, 5, 3) . '/' . substr($doc, 8, 4) . '-' . substr($doc, 11, 2);
            
            return $out;
}

function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}


?>