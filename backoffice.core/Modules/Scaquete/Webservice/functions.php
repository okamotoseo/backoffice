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

    function getCodigoIbge($pg, $cidade){
        
        $sql = "SELECT  a.municipio FROM sysemp.ibge a  
        WHERE sysemp.fun_sem_acento( upper(a.municipio_nome) ) =  fun_sem_acento( upper('{$cidade}')) LIMIT 1";
        $query = $pg->query($sql);
        $res =  $query->fetch(PDO::FETCH_ASSOC);
        $codigoIbge = empty($res['municipio'])? '' : $res['municipio'] ;
       
        if(empty($codigoIbge)){
            $sql = "SELECT  a.municipio FROM sysemp.ibge a
            WHERE sysemp.fun_sem_acento( upper(a.distrito_nome) ) =  fun_sem_acento( upper('{$cidade}')) LIMIT 1";
            $query = $pg->query($sql);
            $res =  $query->fetch(PDO::FETCH_ASSOC);
            $codigoIbge = empty($res['municipio'])? '' : $res['municipio'] ;
            
        }
        
        if(empty($codigoIbge)){
            $message = "Cidade não encontrada: ".$cidade." Scaquete Functions Orders";
            notifyAdmin($message);
            
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
    
