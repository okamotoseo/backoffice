<?php 

function exportBrands($db, $pg, $storeId, $brand = null){
    
    $condition = isset($brand) ? "AND brand LIKE '{$brand}'" : '' ;
    
    $sql = "SELECT * FROM brands WHERE store_id = {$storeId} {$condition}";
    $query = $db->query($sql);
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        
        $sqlVerify = "SELECT id_marca FROM marca WHERE descricao LIKE '".utf8_decode(strtoupper(removeAcentosNew($row['brand'])))."'";
        $queryMarca = $pg->query($sqlVerify);
        $verifyMarca = $queryMarca->fetch(PDO::FETCH_ASSOC);
        $idMarca = $verifyMarca['id_marca'];
        
        if(!isset($verifyMarca['id_marca'])){
            
            
            $sqlInsert = "INSERT INTO marca (id_marca,descricao) VALUES ({$row['id']}, '".utf8_decode(strtoupper(removeAcentosNew($row['brand'])))."');";
            $queryInsert = $pg->query($sqlInsert);

            $idMarca = $row['id'];

            
        }
        
        
    }
    
    return $idMarca;
    
}
function exportColors($db, $pg, $storeId, $color = null){
    
    $condition = isset($color) ? "AND color LIKE '{$color}'" : '' ;
    
    $sql = "SELECT * FROM colors WHERE store_id = {$storeId} {$condition}";
    $query = $db->query($sql);
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        
        $sqlVerify = "SELECT id_cor FROM cores WHERE descricao LIKE '".utf8_decode(strtoupper(removeAcentosNew($row['color'])))."'";
        $queryColor = $pg->query($sqlVerify);
        $verifyColor = $queryColor->fetch(PDO::FETCH_ASSOC);
        $idColor = $verifyColor['id_cor'];
        
        if(!isset($verifyColor['id_cor'])){
            
            $queryGen = $pg->query("SELECT nextval('sysemp.gen_cores')");
            $maxId = $queryGen->fetch(PDO::FETCH_OBJ);
            $idColor = $maxId->nextval;
            $sqlInsert = "INSERT INTO cores (id_cor,descricao) VALUES ({$idColor}, '".utf8_decode(strtoupper(removeAcentosNew($row['color'])))."');";
            $queryInsert = $pg->query($sqlInsert);
            
            
        }
        
        
    }
    
    return $idColor;
    
}

function exportGroup($db, $pg, $storeId, $category = null){
    $condition='';
    if(isset($category)){
        $parts = explode('>', $category);
        $condition = "AND category LIKE '".trim($parts[0])."'";
    }
    
   $sql = "SELECT * FROM `category`  WHERE  store_id = {$storeId} AND parent_id = 0 {$condition}";
    $query = $db->query($sql);
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $sqlVerify = "SELECT id_grupo FROM grupo WHERE descricao LIKE '".utf8_decode(strtoupper(removeAcentosNew($row['category'])))."'";
        $queryGrupo = $pg->query($sqlVerify);
        $verifyGrupo = $queryGrupo->fetch(PDO::FETCH_ASSOC);
        $idGrupo = $verifyGrupo['id_grupo'];
        
        if(!isset($verifyGrupo['id_grupo'])){
            
//             $queryGen = $pg->query("SELECT nextval('sysemp.gen_grupo')");
//             $maxId = $queryGen->fetch(PDO::FETCH_OBJ);
//             $idGrupo = $maxId->nextval;
            
            $sqlVerify = "SELECT max(id_grupo) id FROM grupo ";
            $queryGrupo = $pg->query($sqlVerify);
            $maxId = $queryGrupo->fetch(PDO::FETCH_ASSOC);
            $idGrupo = $maxId['id']+1;
            $sqlInsert = "INSERT INTO grupo (id_grupo,descricao) VALUES ({$idGrupo},  '".utf8_decode(strtoupper(removeAcentosNew($row['category'])))."');";
            $queryInsert = $pg->query($sqlInsert);
            
            
        }
        
        
    }
    
    return $idGrupo;
    
}

function exportSubgroup($db, $pg, $storeId, $category = null){
    $condition='';
    if(isset($category)){
        $parts = explode('>', $category);
        $condition = "AND category LIKE '".trim(end($parts))."'";
    }
    
    $sql = "SELECT * FROM `category`  WHERE  store_id = {$storeId} AND parent_id > 0 {$condition}";
    $query = $db->query($sql);
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $sqlVerify = "SELECT id_subgrupo FROM subgrupo WHERE descricao LIKE '".utf8_decode(strtoupper(removeAcentosNew($row['category'])))."'";
        $querySubgrupo = $pg->query($sqlVerify);
        $verifySubgrupo = $querySubgrupo->fetch(PDO::FETCH_ASSOC);
        $idSubgrupo = $verifySubgrupo['id_subgrupo'];
        
        if(!isset($verifySubgrupo['id_subgrupo'])){
            
            $queryGen = $pg->query("SELECT nextval('sysemp.gen_subgrupo')");
            $maxId = $queryGen->fetch(PDO::FETCH_OBJ);
            $idSubgrupo = $maxId->nextval;
            
            $sqlInsert = "INSERT INTO subgrupo (id_subgrupo,descricao) VALUES ({$idSubgrupo},  '".utf8_decode(strtoupper(removeAcentosNew($row['category'])))."');";
            $queryInsert = $pg->query($sqlInsert);
            
            
        }
        
        
    }
    
    return $idSubgrupo;
    
}

function exportCategory($db, $pg, $storeId, $category = null){
    
    
    $condition='';
    if(isset($category)){
        $parts = explode('>', $category);
        $condition = "AND category LIKE '".trim(end($parts))."'";
    }
    
    $sql = "SELECT * FROM `category`  WHERE  store_id = {$storeId} AND parent_id != 0 {$condition}";
    $query = $db->query($sql);
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $sqlVerify = "SELECT id_categoria FROM categoria WHERE descricao LIKE '".utf8_decode(strtoupper(removeAcentosNew($row['category'])))."'";
        $queryCategoria = $pg->query($sqlVerify);
        $verifyCategoria = $queryCategoria->fetch(PDO::FETCH_ASSOC);
        $idCategoria = $verifyCategoria['id_categoria'];
        
        if(!isset($verifyCategoria['id_categoria'])){
            
            $queryGen = $pg->query("SELECT nextval('sysemp.gen_subgrupo')");
            $maxId = $queryGen->fetch(PDO::FETCH_OBJ);
            $idCategoria = $maxId->nextval;
            
            $sqlInsert = "INSERT INTO categoria (id_categoria,descricao) VALUES ({$idCategoria},  '".utf8_decode(strtoupper(removeAcentosNew($row['category'])))."');";
            $queryInsert = $pg->query($sqlInsert);
            
            
        }
        
        
    }
    
    return $idCategoria;
    
}

    function addTablePrice($db, $pg, $storeId){
        
        
        $sql = "SELECT id_tb_preco FROM tabela_preco_cv WHERE id_tb_preco = 1 ";
        $query = $pg->query($sql);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        
        if(!isset($row['id_tb_preco'])){
            
            $sql = " INSERT INTO tabela_preco_cv 
            (id_tb_preco, 
            id_empresa,                
            descricao_tabela,          
            descricao_pgto,            
            compra_venda,              
            cadastro_automatico,       
            tabela_valida_de,          
            tabela_valida_ate,         
            dt_cadastro,               
            us_cadastro)               
            VALUES (
            '1',                           
            '1',                           
            'TABELA PADRAO',               
            'A VISTA',                     
            'V',                           
            'T' ,                          
            NULL,                                   
            NULL,                                   
            CURRENT_DATE,  
            'SYSPLACE') RETURNING id_tb_preco as id";
            
            $queryInsert = $pg->query($sql);
            $lastId = $queryInsert->fetch(PDO::FETCH_OBJ);
            
            return $lastId->id; 
        
        }
        
        return $row['id_tb_preco'];
    }
    
    
    function addCondPayment($db, $pg, $storeId){
        
        $sql = "SELECT id_condpagto FROM tabela_condpagto  WHERE id_condpagto = 1 ";
        $query = $pg->query($sql);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        
        if(!isset($row['id_condpagto'])){
            
            $sql = "INSERT INTO tabela_condpagto 
                     (id_condpagto,               
                     id_tb_preco,                
                     descricao_tabela,           
                     descricao_pgto,             
                     tipo,                       
                     percentual,                 
                     dt_inicial_promocao,        
                     dt_final_promocao,          
                     parcelas,                   
                     tipo_pagto,                 
                     valor_minimo,               
                     com_entrada,                
                     dia_fixo,                   
                     dias_entre_parcelas,        
                     prazo_medio,                
                     dt_cadastro,                
                     us_cadastro)                
                     VALUES (                    
                    1,                                        
                    1,                                        
                    'TABELA PADRAO',                      
                    '30/60/90/120/150/180',              
                    'C',                             
                    '0.000',                                            
                    Null,                                             
                    Null,                                            
                    6,                                        
                    ';0,1,2,3,4,5',                         
                    '0.00',                                             
                    'F',                                     
                    'F',                                    
                    30,                                     
                    0,                                     
                    CURRENT_DATE,                                   
                    'SYSPLACE') RETURNING id_condpagto as id";      
            $queryInsert = $pg->query($sql);
            $lastId = $queryInsert->fetch(PDO::FETCH_OBJ);
            
            return $lastId->id;
            
        }
        
        return $row['id_condpagto'];
        
        
    }
    
    function getCityException($city){
        
        switch ($city) {
            
            case 'Embu Guaçu': $city = utf8_decode(strtoupper('Embu-Guaçu')); break;
            case 'Embu Guaçu': $city = utf8_decode(strtoupper('Embu-Guaçu')); break;
            case "Pau'Dalho": $city = utf8_decode(strtoupper('Paudalho')); break;
            case "Santa Bárbara D'Oeste": $city = utf8_decode(strtoupper("Santa Bárbara d'Oeste")); break;
            case "Holambra II": $city = utf8_decode(strtoupper('Holambra')); break;
            case "Birigüi": $city = utf8_decode(strtoupper('Birigui')); break;
            case "Embu das Artes": $city = utf8_decode(strtoupper('Embu')); break;
            case "Mogi Mirim": $city = utf8_decode(strtoupper('Moji Mirim')); break;
            case "Jacaré": $city = utf8_decode(strtoupper('Jacareí')); break;
            case "Parati": $city = utf8_decode(strtoupper('Paraty')); break;
            case "Búzios": $city = utf8_decode(strtoupper('Armação dos Búzios')); break;
            
            default:
                
                $city = utf8_decode(strtoupper($city));
                
                break;
        }
        return $city;
        
    }
    
    function exportCustomer($db, $pg, $storeId, $customer = array(), $sellerId){
        
        
        switch($customer['TipoPessoa']){
            case 1:
                $tipoPessoa = "F";
                $cpf = $customer['CPFCNPJ'];
                $rg = $customer['RGIE'];
                $cnpj = "NULL";
                $ie = "NULL";
                $sqlVerify = "SELECT id_cliente FROM cliente WHERE cpf LIKE '{$cpf}'";
                break;
            case 2:
                $tipoPessoa = "J";
                $cpf = "NULL";
                $rg = "NULL";
                $cnpj = $customer['CPFCNPJ'];
                $ie = $customer['RGIE'];
                $sqlVerify = "SELECT id_cliente FROM cliente WHERE cnpj LIKE '{$cnpj}'";
                break;
            default:
                $tipoPessoa = "F";
                $cpf = $customer['CPFCNPJ'];
                $rg = $customer['RGIE'];
                $cnpj = "NULL";
                $ie = "NULL";
                $sqlVerify = "SELECT id_cliente FROM cliente WHERE cpf LIKE '{$cpf}'";
                break;
        }
        
        
        $cep = $customer['CEP'];
        
        $cidade =  pg_escape_string(getCityException($customer['Cidade']));
            
        $endereco = utf8_decode(str_replace("'", "", strtoupper(removeAcentosNew(substr($customer['Endereco'], 0, 100)))));
        $complemento = utf8_decode(strtoupper(removeAcentosNew(substr($customer['Complemento'], 0, 30))));
        $bairro = !empty($customer['Bairro']) ? utf8_decode(strtoupper(removeAcentosNew(substr($customer['Bairro'], 0, 30)))) : 'BAIRRO' ;
        $nome = utf8_decode(strtoupper(removeAcentosNew(substr($customer['Nome'], 0, 60))));
        $fantasia = utf8_decode(strtoupper(removeAcentosNew(substr($customer['Nome'], 0, 30))));
        $nomeContato = utf8_decode(strtoupper(removeAcentosNew(substr($customer['Nome'], 0, 30))));
        $apelido = utf8_decode(strtoupper(removeAcentosNew($customer['Apelido'])));
        $telefone = utf8_decode(substr(trim($customer['Telefone']), 0, 15));
        $telefoneAlternativo = utf8_decode(substr(trim($customer['TelefoneAlternativo']), 0, 15));
        $number = substr($customer['Numero'], 0, 10);
        
        
        if(empty($cidade)){
            $message = "Erro ao cadastrar cliente sem cidade {$customer['Cidade']}";
            notifyAdmin($message);
            
        }
            
        $codigoIbge =  getCodigoIbge($db, $pg, $cidade);
            $idUf = getCodigoUf($pg, $codigoIbge);
            
            $queryVerify = $pg->query($sqlVerify);
            $clienteVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
//             print_r($clienteVerify['id_cliente']);
            
            if(empty($clienteVerify['id_cliente'])){
                $queryGen = $pg->query("SELECT nextval('sysemp.gen_cliente')");
                $maxId = $queryGen->fetch(PDO::FETCH_OBJ);
                $id_cliente = $maxId->nextval;

                 $query = $pg->insert('cliente', array(
                    'id_cliente' => $id_cliente,
                    'id_empresa' => 1,
                    'id_tp_cliente' => 1,
                    'id_transportadora' => 1,
                    'razsocial' => $nome,
                    'fantasia' => $apelido,
                    'pessoafj' => $tipoPessoa,
                    'cnpj' => $cnpj,
                    'insc_estadual' => $ie,
                    'rg' => $rg,
                    'cpf' => $cpf,
                    'logradouro' => $endereco,
                    'logra_numero' => $number,
                    'logra_bairro' => $bairro,
                    'logra_cep' => $cep,
                    'logra_complemento' => $complemento,
                    'cidade' => $cidade,
                    'codigoibge' => $codigoIbge,
                    'email' => $customer['Email'],
                    'email_nfe' => $customer['Email'],
                    'id_uf' => $idUf,
                    'telefone' => $telefone,
                    'nome_contato' => $nomeContato,
                    'celular_contato' => $telefoneAlternativo,
                    'limite_credito' => 1,
                    'mae' => '',
                    'pai' => '',
                    'ativo' => 'T',
                    'data_nascimento' => Null,
                    'aut_dinheiro' => 'T',
                    'aut_cheque' => 'T',
                    'aut_cartao' => 'T',
                    'aut_duplicata' => 'T',
                    'aut_promissoria' => 'T',
                    'aut_vale' => 'T',
                    'aut_crediario' => 'T',
                    'codigo_pais' => 1058,
                    'cliente' => 'T',
                    'fornecedor' => 'F',
                    'funcionario_vend' => 'F',
                    'Vendedor' => 'F',
                    'dt_cadastro' => date('Y-m-d'),
                    'observacao' => $apelido,
                    'comissao_venda' => '0.00',
                    'faturamento' => 'T',
                    'id_vendedor' => $sellerId,
                ));
                 
                return $pg->last_id;

            }else{
            	$data = array(
                    'razsocial' => $nome,
                    'fantasia' => $apelido,
                    'pessoafj' => $tipoPessoa,
                    'cnpj' => $cnpj,
                    'insc_estadual' => $ie,
                    'rg' => $rg,
                    'cpf' => $cpf,
                    'logradouro' => $endereco,
                    'logra_numero' => $customer['Numero'],
                    'logra_bairro' => $bairro,
                    'logra_cep' => $cep,
                    'logra_complemento' => $complemento,
                    'cidade' => $cidade,
                    'codigoibge' => $codigoIbge,
                    'email' => $customer['Email'],
                    'email_nfe' => $customer['Email'],
                    'id_uf' => $idUf,
                    'telefone' => $telefone,
                    'nome_contato' => $nomeContato,
                    'celular_contato' => $customer['TelefoneAlternativo'],
                    'dt_cadastro' => date('Y-m-d'),
                    'observacao' => $apelido,
                    'id_vendedor' => $sellerId,
                );
                $query = $pg->update('cliente', array('id_cliente'), array($clienteVerify['id_cliente']), $data);
                
                return $clienteVerify['id_cliente'];
            
            }
            
        
//         }
        
    }
    

    function getCodigoIbge($db, $pg, $cidade){
        
        $cidade = trim($cidade);
        
        $sql = "SELECT  a.municipio FROM sysemp.ibge a  
        WHERE sysemp.fun_sem_acento( lower(a.municipio_nome) ) =  sysemp.fun_sem_acento(lower('{$cidade}')) LIMIT 1";
        $query = $pg->query($sql);
        $res =  $query->fetch(PDO::FETCH_ASSOC);
        $codigoIbge = empty($res['municipio'])? '' : $res['municipio'] ;
       
        if(empty($codigoIbge)){
            $sql = "SELECT  a.municipio FROM sysemp.ibge a
            WHERE sysemp.fun_sem_acento(lower(a.distrito_nome)) = sysemp.fun_sem_acento(lower('{$cidade}')) LIMIT 1";
            $query = $pg->query($sql);
            $res =  $query->fetch(PDO::FETCH_ASSOC);
            $codigoIbge = empty($res['municipio'])? '' : $res['municipio'] ;
            
        }
        
        if(empty($codigoIbge)){
//             $message = "Cidade não encontrada: ".$cidade." Sysemp Functions Orders {$sql}";
//             notifyAdmin($message);

            /**
             * 
             * TODO => capturar variação da escrita da cidade e cadastrar no ERP Sysemp
             */
            
//             $sqlVerifyLI = "SELECT id FROM module_sysemp_log_ibge WHERE cidade LIKE '{$cidade}'";
//             $queryLI = $db->query($sqlVerifyLI);
//             $verifyLI = $queryLI->fetch(PDO::FETCH_ASSOC);
            
            $sqlLogIbge = "INSERT INTO `module_sysemp_log_ibge`( `store_id`, `cidade`) VALUES (4, '{$cidade}')";
            $queryLI = $db->query($sqlLogIbge);
            
            return;
        }
        return $codigoIbge;
    }
    
    function getCodigoUf($pg, $codigoIbge){
        
        $sql = "SELECT id_uf FROM ibge WHERE municipio LIKE '{$codigoIbge}' LIMIT 1";
        $query = $pg->query($sql);
        $res =  $query->fetch(PDO::FETCH_ASSOC);
        $idUf = empty($res['id_uf'])? NULL : $res['id_uf'] ;
        
        return $idUf;
        
    }
    
    
    function getMesAnoCompetencia($data){
        
        $data = explode("-", $data);
        
        switch($data[1]){
            case 1; $competencia = "JANEIRO / {$data[0]}";break;
            case 2; $competencia = "FEVEREIRO / {$data[0]}";break;
            case 3; $competencia = "MARÇO / {$data[0]}";break;
            case 4; $competencia = "ABRIL / {$data[0]}";break;
            case 5; $competencia = "MAIO / {$data[0]}";break;
            case 6; $competencia = "JUNHO / {$data[0]}";break;
            case 7; $competencia = "JULHO / {$data[0]}";break;
            case 8; $competencia = "AGOSTO / {$data[0]}";break;
            case 9; $competencia = "SETEMBRO / {$data[0]}";break;
            case 10; $competencia = "OUTUBRO / {$data[0]}";break;
            case 11; $competencia = "NOVEMBRO / {$data[0]}";break;
            case 12; $competencia = "DEZEMBRO / {$data[0]}";break;
            
        }
        
        return $competencia;
        
    }
    
    


?>