<?php 
function getFilterId($pg, $type, $term, $limit){
	$term = strtoupper($term);
	switch ($type){
		case "brand":
			$sql = "SELECT codigo, descricao FROM marcas
			WHERE desativar = 'f' AND  descricao LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
			break;
		case "provider":
			$sql = "SELECT codigo, nome as descricao FROM pessoas
			WHERE pessoa = 1 AND nome LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
			break;
		case "department":
			$sql = "SELECT codigo, descricao FROM departamentos
			WHERE desativar = 'f' AND descricao LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
			break;
		case "grid":
			$sql = "SELECT codigo, descricao FROM grades
			WHERE desativar = 'f' AND descricao LIKE '%{$term}%' ORDER BY descricao LIMIT {$limit}";
			break;
		case "collection":
			$sql = "SELECT codigo, descricao FROM colecoes
			WHERE desativar = 'f' AND descricao LIKE '%{$term}%' ORDER BY codigo DESC LIMIT {$limit}";
			break;
		case "company":
			$sql = "SELECT apelido as descricao, substr(codigo,5,2)::char(2) as codigo FROM pessoas
			WHERE filial AND apelido LIKE '%{$term}%' LIMIT {$limit} ORDER BY codigo ASC";
			break;
		case "group":
			$sql = "SELECT codigo, descricao FROM grupos
			WHERE desativar = 'f' AND descricao LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
			break;
		case "subgroup":
			$sql = "SELECT codigo, descricao FROM subgrupos
			WHERE desativar = 'f' AND descricao LIKE '{$term}%' ORDER BY descricao LIMIT {$limit}";
			break;
	}
	if(!empty($sql)){
	    $pgQuery = $pg->query($sql);
		while ($row = $pgQuery->fetch(PDO::FETCH_ASSOC)){
			$arr[] = array(
					"id" => "{$row['codigo']}",
					"label" => trim("{$row['descricao']}"),
					"value" => trim("{$row['descricao']}")
			);
		}
		return $arr;
	}else{
		return false;
	}

}

/***************************************************************/
/******************** Funções de compra e Vendas ***************/
/***************************************************************/

function getStoreInformations($pg, $storeId = NULL){
	$condition = "";
	$storeInformation = array();
	if(isset($storeId)){
		$condition = "AND Codigo LIKE '{$storeId}'";
	}
	$sql = "SELECT  codigo as code, apelido as store, email as email_sac, 
            email as email_send, telefone1 as phone, nome as company, 
            cpfcnpj as cnpj, endereco as address, cep as postalcode, bairro as neighborhood, cidade as city, uf as state 
    FROM pessoas WHERE filial {$condition} ORDER BY apelido";
	
	$pgQuery = $pg->query($sql);
	while ($row = $pgQuery->fetch(PDO::FETCH_ASSOC)){
	    
	    $storeInformation[] = $row;
	    
	}
	return $storeInformation;

}

/***************************************************************/
/***************** Funções para exportar pedidos ***************/
/***************************************************************/
function existCpfCnpj($connPgSeta, $cpf)
{
    $sql = "
			SELECT
				codigo
			FROM
				pessoas
			WHERE
				REPLACE(REPLACE(REPLACE(cpfcnpj,'.',''),'-',''),'/','') = '" . preg_replace("/[^0-9]/","", $cpf) . "'";
    
    
    $query = $connPgSeta->query($sql);
    $res = $query->fetch(PDO::FETCH_ASSOC);
    
    if(isset($res['codigo'])){
        return $res['codigo'];
    }
    return ;
}
function getCodigoIbge($connPgSeta, $cep, $cidade, $uf)
{
    $out = '';
    $cep = formataCEP($cep);
    
    if(!empty($cep))
    {
        $sql = "
				SELECT
					cc.ibge
				FROM
					cepcidades AS cc
					INNER JOIN cep AS c ON (cc.codigo = c.cidade)
				WHERE
					c.codigo = TRIM('" . $cep . "')";
        
        $res = pg_query($connPgSeta, $sql);
        $result = pg_fetch_assoc($res);
        if(isset($result['ibge'])){
            return $result['ibge'];
        }
    }
}
function getCodigoCidade($pg, $cep)
{
    $out = '';
    $cep = formataCEP($cep);
    
    if(!empty($cep))
    {
        $sql = "
				SELECT
					cc.ibge, cc.codigo
				FROM
					cepcidades AS cc
					INNER JOIN cep AS c ON (cc.codigo = c.cidade)
				WHERE
					c.codigo = TRIM('" . $cep . "')";
        $res = $pg->query($sql);
        $result = $res->fetch(PDO::FETCH_ASSOC);
        
        if(isset($result['codigo'])){
            return $result['codigo'];
        }
    }
}


function formataCodigoERP($cod, $size = NULL)
{
    $size = isset($size) && is_integer($size) ? $size : 6;
    $cod = isset($cod) ? $cod : 0;
    $cod = intval(preg_replace('/[^0-9]/', '', $cod));
    return str_pad($cod, $size, '0', STR_PAD_LEFT);
}
function getPaymentCondition($type, $parcelas){
    if($type === 3){
        switch($parcelas)
        {
            case 1:
                $condicao_pagamento = '130';
                break;
            case 2:
                $condicao_pagamento = '131';
                break;
            case 3:
                $condicao_pagamento = '132';
                break;
            case 4:
                $condicao_pagamento = '133';
                break;
            case 5:
                $condicao_pagamento = '134';
                break;
            case 6;
            $condicao_pagamento = '135';
            break;
            case 7:
                $condicao_pagamento = '136';
                break;
            case 8:
                $condicao_pagamento = '137';
                break;
            case 9:
                $condicao_pagamento = '138';
                break;
            case 10:
                $condicao_pagamento = '139';
                break;
        }
    }else{
        $condicao_pagamento = '126';
    }
    return $condicao_pagamento;
}
function getERPCusto($pg, $id_produto)
{
    $sql = "
	SELECT
	p.custo
	FROM
	produtos AS p
	WHERE
	p.codigo = '{$id_produto}'";
    
    $res = $pg->query($sql);
    $resCusto = $res->fetch(PDO::FETCH_ASSOC);
    if(isset($resCusto['custo'])){
        return $resCusto['custo'];
    }
    
    return 0;
}
function prepareString($string) {
    return pg_escape_string(mb_convert_encoding(trim($string), "WINDOWS-1252", "UTF-8"));
}

