<?php
header("Content-Type: text/html; charset=utf-8");
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Class/class-PgConnection.php';
require_once $path .'/functions.php';
$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
$request = isset($_REQUEST["user"]) && !empty($_REQUEST["user"]) ? $_REQUEST["user"] : "Manual" ;

if (empty ( $action ) and empty ( $storeId )) {
    $paramAction = explode ( "=", $_SERVER ['argv'] [1] );
    $action = $paramAction [0] == "action" ? $paramAction [1] : null;
    $paramStoreId = explode ( "=", $_SERVER ['argv'] [2] );
    $storeId = $paramStoreId [0] == "store_id" ? $paramStoreId [1] : null;
    $request = "System";
}

if(isset($storeId)){
    $db = new DbConnection();
    $pg = new PgConnection($db, $storeId);
    
	switch($action){
			
		case "export_available_products":
		    
		    $productId = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : NULL;
		    $parentId = isset($_REQUEST['parent_id']) ? $_REQUEST['parent_id'] : NULL;
		    $sku = isset( $_REQUEST['sku']) ? $_REQUEST['sku'] : NULL;
		    $update = isset($_REQUEST['update']) ? $_REQUEST['update'] : NULL;
// 		    $pg->query('DELETE FROM cores');
// 		    $pg->query('DELETE FROM categoria');
// 		    $pg->query('DELETE FROM marca');
// 		    $pg->query('DELETE FROM grupo');
// 		    $pg->query('DELETE FROM subgrupo');
// 		    $pg->query('DELETE FROM tabela_preco_produto');
// 		    $pg->query('DELETE FROM tabela_condpagto_produto');
// 		    $pg->query('DELETE FROM produto_inventario');
// 		    $pg->query('DELETE FROM produto_estoque');
// 		    $pg->query('DELETE FROM produto');
// 		    die;
		    
		    $idTablePrice =  addTablePrice($db, $pg, $storeId);
		    $idCondPayment = addCondPayment($db, $pg, $storeId);
// 		    $syncId =  logSyncStart($db, $storeId, "Seta", $action, "Exportação de produtos para ERP Sysemp Fanlux", $request);
		    
		    if(!empty($productId)){
		        
    		    $sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND brand != '' AND id = {$productId}";
    		    $query = $db->query($sql);
    		    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    		        
        		        $idMarca = !empty($row['brand']) ? exportBrands($db, $pg, $storeId, $row['brand']) : '';
        		        
        		        $idColor = !empty($row['color']) ? exportColors($db, $pg, $storeId, $row['color']) : '';
        		        
        		        if(empty($idColor)){
        		            echo "error|Produto sem cor!";
        		            continue;
        		            
        		        }
        		        
        		        $idCategory = !empty($row['category']) ? exportCategory($db, $pg, $storeId, $row['category']) : '';
        		        $idGrupo = !empty($row['category']) ? exportGroup($db, $pg, $storeId, $row['category']) : '';
        		        $idSubgrupo = !empty($row['category']) ? exportSubgroup($db, $pg, $storeId, $row['category']) : '';
        		        $descricao = utf8_decode(strtoupper(removeAcentosNew($row['title'])));
        		        $custo = empty($row['cost']) ? str_replace(",", ".",  $row['sale_price'] * 0.50) : $row['cost'] ;
        		        $preco = str_replace(",", ".", $row['price']);
        		        $preco2 = str_replace(",", ".",  $row['sale_price']);
        		        
        		        if(!empty($row['ncm'])){
        		            $queryNcm = $pg->query("SELECT ncm FROM ncm WHERE ncm LIKE '{$row['ncm']}'");
        		            $idNcm = $queryNcm->fetch(PDO::FETCH_OBJ);
        		            $ncm = isset($idNcm->ncm) ? $idNcm->ncm : "";
        		            
        		        }
        		        if(empty($ncm)){
        		            $ncm = '84145990';
        		        }
        		        
        		        
        		        $controleGrade = empty($row['parent_id']) ? $row['id'] : $row['parent_id'] ; 
        		        
        		        $controleGrade = is_int($controleGrade) ? $controleGrade :  $row['id'] ;  
        		        $idTamanho = '';
        		        $unidade = 'UND';
        		        $idPesoMedida = 2;
        		        $referencia = substr($row['reference'], 0, 15);
        		        $sku =  $row['sku'];
        		        $margem = 0;
        		        $codBarras = empty($row['ean']) ? '' : $row['ean'] ;
        		        switch(trim($row['variation'])){
        		            case "UN": $idTamanho = 5; break;
        		            case "40": $idTamanho = 4; break;
        		            case "127V": $idTamanho = 1;break;
        		            case "110v": $idTamanho = 1;break;
        		            case "220v": $idTamanho = 2;break;
        		            case "220V": $idTamanho = 2;break;
        		            case "127V / 220": $idTamanho = 3;break;
        		            case "127v / 220": $idTamanho = 3;break;
        		            case "127/220V": $idTamanho = 3;break;
        		            case "110V / 220": $idTamanho = 3;break;
        		            default: $idTamanho = 5;break;
        		            
        		        }
        		        
        		        $width = str_replace(",", '.', $row['width']);
        		        $height = str_replace(",", '.', $row['height']);
        		        $length = str_replace(",", '.', $row['length']);
        		        $sql = "SELECT id_produto FROM produto WHERE codigo_auxiliar LIKE '{$row['sku']}'";
        		        $queryVerify = $pg->query($sql);
        		        $productVerify = $queryVerify->fetch(PDO::FETCH_ASSOC);
        		        
        		        if(!isset($productVerify['id_produto'])){
        		            
        		            $queryGen = $pg->query("SELECT nextval('sysemp.gen_produto')");
        		            $maxId = $queryGen->fetch(PDO::FETCH_OBJ);
        		            $id_produto = $maxId->nextval;
        		            
        		            if(isset($id_produto)){
        		            
            		            $sqlProduct = "INSERT INTO produto ( 
                                    id_produto,
                                    codigo_auxiliar, 
                                    descricao, 
                                    cod_barra, 
                                    cod_fabrica, 
                                    id_marca,
                                    id_grupo,
                                    id_subgrupo,
                                    unidade, 
                                    custo,
                					venda_vista, 
                                    venda_prazo, 
                                    id_cor, 
                                    id_categoria, 
                                    id_tamanho, 
                                    id_peso_medida,
                                    ncm,
                                    largura,
                                    altura,
                                    comprimento,
                                    controle_grade, 
                                    id_grade,
                                    us_cadastro)
                					VALUES ({$id_produto},
                                    '{$sku}', 
                                    '{$descricao}',
                                    '{$codBarras}', 
                                    '{$referencia}', 
                                    {$idMarca},
                                    {$idGrupo},
                                    {$idSubgrupo},
                                    '{$unidade}',
                					'{$custo}', 
                                    '{$preco2}', 
                                    '{$preco2}', 
                                    {$idColor}, 
                                    {$idCategory},
                					{$idTamanho},
                                    {$idPesoMedida},
                                    '{$ncm}', 
                                    '{$width}',
                                    '{$height}',
                                    '{$length}',
                                    {$controleGrade},  
                                    1,
                                    'SYSPLACE') RETURNING id_produto";
                                    
                                    $sqlTabelaPrecoProduto = "INSERT INTO tabela_preco_produto
                                    (id_tb_preco, id_produto, preco, dt_cadastro, us_cadastro)
                                    VALUES (1, {$id_produto},'{$preco2}',CURRENT_DATE,'SYSPLACE')"; 
                                    
                                    $sqlTabelaCondPagtoProduto = "INSERT INTO tabela_condpagto_produto (id_condpagto,id_tb_preco, 
                                    id_produto,   valor, dt_inicial_promocao, dt_final_promocao, dt_cadastro,  us_cadastro) 
                                    VALUES (1,1,{$id_produto}, '{$preco2}', Null, Null, CURRENT_DATE,'SYSPLACE')";
        		            }
                                
        		        }
        		        
        		        if(isset($productVerify['id_produto']) AND $update){
        		           
        		            $sqlProduct = "UPDATE produto SET 
                                descricao = '{$descricao}',
                                cod_barra = '{$codBarras}', 
                                cod_fabrica = '{$referencia}',
                                id_marca = {$idMarca},
                                id_grupo = {$idGrupo},
                                id_subgrupo = {$idSubgrupo},
                                unidade = '{$unidade}', 
                                custo = '{$custo}', 
            					venda_vista = '{$preco2}',  
                                venda_prazo = '{$preco2}',  
                                id_cor = {$idColor},
                                id_categoria = {$idCategory}, 
                                id_tamanho = {$idTamanho},  
                                id_peso_medida = '{$idPesoMedida}',
                                ncm ='{$ncm}',
                                largura = '{$width}',
                                altura = '{$height}',
                                comprimento = '{$length}',
                                controle_grade = {$controleGrade}
                                WHERE id_produto = {$productVerify['id_produto']} AND codigo_auxiliar LIKE '{$sku}'";
        		            
        		            $sqlTabelaPrecoProduto = "UPDATE tabela_preco_produto SET preco = '{$preco2}', 
                            dt_cadastro = CURRENT_DATE WHERE id_produto = {$productVerify['id_produto']} AND id_tb_preco = 1";
        		            
        		            $sqlTabelaCondPagtoProduto = "UPDATE tabela_condpagto_produto SET  valor = '{$preco2}',
                            dt_cadastro = CURRENT_DATE WHERE id_produto = {$productVerify['id_produto']} AND id_tb_preco = 1 AND id_condpagto = 1";
        		            
        		       } 
        		       
        		       if(!empty($sqlProduct)){
//             		       echo  $sqlProduct;
//             		       echo "<br>";
//             		       echo  $sqlTabelaPrecoProduto;
//             		       echo "<br>";
//             		       echo  $sqlTabelaCondPagtoProduto;
//             		       echo "<br>";
            		       
            		        $resInsert = $pg->query($sqlProduct);
            		        
            		        $insertProduct = $resInsert->fetch(PDO::FETCH_ASSOC);
            		        if(isset($insertProduct['id_produto'])){
            		            $sqlProdutoInventario = "UPDATE produto_estoque SET id_tes_saida = 1 WHERE id_produto = {$insertProduct['id_produto']}";
            		            $produtoInventario = $pg->query($sqlProdutoInventario);
            		        }
            		        $queryTabelaPrecoProduto = $pg->query($sqlTabelaPrecoProduto);
            		        $queryTabelaCondPagtoProduto = $pg->query($sqlTabelaCondPagtoProduto);
            		        echo "success|Produto cadastrado com sucesso!";
        		       }else{
        		           echo "error|Produto já cadastrado no ERP Sysemp Codigo: {$productVerify['id_produto']}";
        		       }
    		        
    	
    		    }
		    
		    
		    }
		    
// 		    logSyncEnd($db, $syncId, $updated);
		    break;
		case "price_table":
		   $idTablePrice =  addTablePrice($db, $pg, $storeId);
		   $idCondPayment = addCondPayment($db, $pg, $storeId);
		    break;
		    
		case "export_brands":
		    $idMarca = exportBrands($db, $pg, $storeId, $brand = null);
		    break;
		case "export_colors":
		    $idColor = exportColors($db, $pg, $storeId, $color = null);
		    break;
		case "export_category":
		    $idcategory = exportCategory($db, $pg, $storeId, $category = null);
		    break;
		    
	}

}
