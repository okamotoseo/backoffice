<?php
// header("Access-Control-Allow-Origin: *");
// ini_set('max_execution_time', 86400);
ini_set ("display_errors", true);
set_time_limit ( 300 );
header("Content-Type: text/html; charset=utf-8");

$path = dirname(__FILE__);
require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/functions.php';

$action = isset($_REQUEST["action"]) && $_REQUEST["action"] != "" ? $_REQUEST["action"] : null ;
$storeId = isset($_REQUEST["store_id"]) && $_REQUEST["store_id"] != "" ? intval($_REQUEST["store_id"]) : null ;
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
	    $host = "casebre.jelastic.saveincloud.net/11345:/opt/firebird/data/SPDDADOS.FDB";
	    // $host = "191.243.199.81/11109:/opt/firebird/data/SPDDADOS.FDB";
	    $username = "SYSDBA";
	    $password = "07903113801";
	    
	    $dbh = ibase_connect($host, $username, $password);
	    
    switch($action){
    
    	case "import_products_scaquete" :
	    
	    $stmt = 'select tabest.*, tabgpro.descricao as grupox from tabest left join tabgpro on tabgpro.codigo = tabest.grupo';
	    
	    $sth = ibase_query($dbh, $stmt);
	    while($row = ibase_fetch_assoc($sth)){
	    	pre(date('Y-m-d H:i:s'));
	        pre($row);
	        if($row){
	            
	            $IDEST = $row['IDEST'];
	            $CODIGOEAN = $row['CODIGOEAN'];
	            $GRUPO = $row['GRUPOX'];
	            $REFER = $row['REFER'];
	            $NCM = $row['NCM'];
	            $STCOD = $row['STCOD'];
	            $FORNECEDOR = $row['FORNECEDOR'];
	            $APLICACAO = $row['APLICACAO'];
	            $UNIDADE = $row['UNIDADE'];
	            $VALCUSTO = $row['VALCUSTO'];
	            $VALVAREJO = $row['VALVAREJO'];
	            $VALATACADO = $row['VALATACADO'];
	            $SDISPO = $row['SDISPO'];
	            $DENTRADA = $row['DENTRADA'];
	            $ATIVO = $row['ATIVO'];
	            $OBS1 = $row['OBS1'];
	            
	            $sql = "SELECT IDEST FROM module_scaquete_products_tmp WHERE store_id = {$storeId} AND IDEST LIKE '{$IDEST}'";
	            $query = $db->query($sql);
	            $res = $query->fetch(PDO::FETCH_ASSOC);
	            
	            if(empty($res['IDEST'])){
	            	$data = array(
		            		'IDEST' => $IDEST,
		            		'CODIGOEAN' => $CODIGOEAN,
		            		'GRUPO' => $GRUPO,
		            		'REFER' => $REFER,
		            		'NCM' => $NCM,
		            		'STCOD' => $STCOD,
		            		'FORNECEDOR' => $FORNECEDOR,
		            		'APLICACAO' => $APLICACAO,
		            		'UNIDADE' => $UNIDADE,
		            		'VALCUSTO' => $VALCUSTO,
		            		'VALVAREJO' => $VALVAREJO,
		            		'VALATACADO' => $VALATACADO,
		            		'SDISPO' => $SDISPO,
		            		'DENTRADA' => $DENTRADA,
		            		'ATIVO' => $ATIVO,
		            		'OBS1' => $OBS1,
		            		'store_id' => $storeId
		            	);
		            $query = $db->insert('module_scaquete_products_tmp', $data);
		            
		            if(!$query){
		            	pre($query);
		            	pre($data);
		            }else{
		            	echo $db->last_id."Novo <br>";
		            }
		            
	            }else{
	            	$data = array(
            			'CODIGOEAN' => $CODIGOEAN,
            			'GRUPO' => $GRUPO,
            			'REFER' => $REFER,
            			'NCM' => $NCM,
            			'STCOD' => $STCOD,
            			'FORNECEDOR' => $FORNECEDOR,
            			'APLICACAO' => $APLICACAO,
            			'UNIDADE' => $UNIDADE,
            			'VALCUSTO' => $VALCUSTO,
            			'VALVAREJO' => $VALVAREJO,
            			'VALATACADO' => $VALATACADO,
            			'SDISPO' => $SDISPO,
            			'DENTRADA' => $DENTRADA,
            			'ATIVO' => $ATIVO,
            			'OBS1' => $OBS1
            			);
	            	$query = $db->update('module_scaquete_products_tmp', 
	            			array('store_id', 'IDEST'), 
	            			array($storeId, $IDEST),  $data);
	            	
	            }
	            
	        }
	    
	    }
	    $count = 0;
	    $sql = "SELECT * FROM module_scaquete_products_tmp WHERE store_id = {$storeId}";
	    
	    $query = $db->query($sql);
	    
	    if($query){
	        
	        while($row = $query->fetch(PDO::FETCH_ASSOC)){
	            
	            $sqlVerify = "SELECT id, quantity, sku, ean, price, sale_price, cost FROM available_products 
	            WHERE store_id = {$storeId} AND sku LIKE '{$row['IDEST']}' ";
	            $verifyQuery = $db->query($sqlVerify);
	            $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	            if(!isset($verify['id'])){
	                $data = array(
	                	'account_id' =>'18',
	                    'store_id' =>  $storeId,
	                    'sku' =>  trim($row['IDEST']),
	                    'ean' => trim($row['CODIGOEAN']),
	                    'parent_id' =>  trim($row['IDEST']),
	                    'title' =>  ucfirst(trim(strtolower($row['APLICACAO']))),
	                    'description' =>  ucfirst(trim(strtolower($row['APLICACAO']." ".$row['FORNECEDOR']))),
	                    'variation_type' =>  'unidade',
	                    'variation' => !empty($row['UNIDADE']) ? trim($row['UNIDADE']) : 'PC' ,
	                    'brand' => ucfirst(strtolower(trim(strtolower($row['FORNECEDOR'])))),
	                    'reference' => trim($row['REFER']),
	                    'quantity' => $row['SDISPO'] > 0 ? $row['SDISPO'] : 0,
	                    'price' => trim($row['VALVAREJO']),
	                    'sale_price' => trim($row['VALVAREJO']),
	                    'cost' => trim($row['VALCUSTO']),
	                    'created' =>  date("Y-m-d H:i:s"),
	                    'updated' =>  date("Y-m-d H:i:s"),
	                    'flag' => 2
	                );
	                
	                $queryInsert = $db->insert('available_products', $data);
	                
	                if($queryInsert){	
	                    $count++;
	                    $id = $db->last_id;
	                    if(!empty($id)){
	                    	 
	                    	$dataLog['insert_import_available_products_scaquete'] = $data;
	                    	 
	                    	$db->insert('products_log', array(
	                    			'store_id' => $storeId,
	                    			'product_id' => $id,
	                    			'description' => 'Novo Produto Importado ERP Scaquete',
	                    			'user' => $request,
	                    			'created' => date('Y-m-d H:i:s'),
	                    			'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	                    	));
	                    }
	                }
	            }
	            
	            if(isset($verify['id'])){
	            	
	                $data = array(
	                	'account_id' =>'18',
	                    'quantity' => $row['SDISPO'] > 0 ? $row['SDISPO'] : 0,
	                    'price' => trim($row['VALVAREJO']),
	                    'cost' => trim($row['VALCUSTO'])
	                );
	                $queryUpdate = $db->update('available_products', 'id', $verify['id'], $data);
	                
	                if($queryUpdate){
	                
		                if($queryUpdate->rowCount()){
		                    
		                    $db->update('available_products',
		                        array('store_id','id'),
		                        array($storeId, $verify['id']),
		                        array('flag' => 2, 'updated' =>  date("Y-m-d H:i:s"))
		                        );
		                    $count++;
		                    
		                    $dataLog['update_import_available_products_scaquete'] = array(
		                    		'before' => $verify,
		                    		'after' => $data
		                    );
		                    $db->insert('products_log', array(
		                    		'store_id' => $storeId,
		                    		'product_id' => $verify['id'],
		                    		'description' => 'Atualização do Produto Importado ERP Scaquete',
		                    		'user' => $request,
		                    		'created' => date('Y-m-d H:i:s'),
		                    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
		                    ));
		                    
		                }
		                
	                }
	                
	            }
	            
	        }
	        
	        echo  $count;
	        
	    }
	    
	    break;
	}

}

//     $stmt = 'SELECT RDB$RELATION_NAME FROM RDB$RELATIONS WHERE ((RDB$SYSTEM_FLAG = 0) OR (RDB$SYSTEM_FLAG IS NULL)) AND (RDB$VIEW_SOURCE IS NULL)';
 
//     $stmt = 'Select f.rdb$relation_name, f.rdb$field_name
//     from rdb$relation_fields f
//     join rdb$relations r on f.rdb$relation_name = r.rdb$relation_name
//     and r.rdb$view_blr is null
//     and (r.rdb$system_flag is null or r.rdb$system_flag = 0)
//     order by 1, f.rdb$field_position;';
 
//     $sth = ibase_query($dbh, $stmt);
//     while($row = ibase_fetch_assoc($sth)){
//         echo "<pre>";
	//         print_r($row);
	//         echo "</pre>";
	 
	 
	//     }die;
	 
	//     $stmt = 'select * from tabest';
	//     $stmt = 'select * from tabgpro';
// $row['IDEST'] => 1
// $row['CODIGOEAN'] => 7890000020001
// $row['GRUPO'] => 1
// $row['REFER'] =>
// $row['CART'] =>
// $row['NCM'] => 4061090
// $row['STCOD'] => 102
// $row['ORIGEM'] =>
// $row['IPI'] =>
// $row['ICMSALIQ'] => 18.00
// $row['FORNECEDOR'] => MAKRO
// $row['APLICACAO'] => REQUEIJAO BISNAGA 1,800KG
// $row['APLICACAOECF'] => REQUEIJAO BISNAGA 1,800KG
// $row['UNIDADE'] => UN
// $row['VALCUSTO'] => 12.890
// $row['VALVAREJO'] => 12.890
// $row['VALATACADO'] => 12.890
// $row['SDISPO'] => 0.000
// $row['SDISPOMEDIO'] => 0.000
// $row['DENTRADA'] => 2013-11-27
// $row['DVALIDADE'] =>
// $row['LOTE'] =>
// $row['NFEUC'] => 50336
// $row['ATIVO'] => S
// $row['CONTROLE'] =>
// $row['PROMOCAO'] =>
// $row['OBS1'] => 1000000015003
// $row['OBS2'] =>
// $row['D1'] =>
// $row['D2'] =>
// $row['V1'] => 0.000
// $row['V2'] => 32.09
// $row['TXC'] =>
// $row['CMRE'] =>
// $row['CMDO'] =>
// $row['CM1'] =>
// $row['CM2'] =>
// $row['CEST'] =>
// $row['CEST1'] =>
// $row['VCM1'] =>
// $row['VCM2'] =>
// $row['VCT1'] =>
// $row['VCA1'] =>
// $row['NIC1'] =>
// $row['PIS'] =>
// $row['CONFIS'] =>
// $row['XL1'] =>
// $row['XL2'] =>
// $row['XL3'] =>
// $row['XTL1'] =>
// $row['XMEDIDA'] =>
// $row['XCOR'] =>
// $row['XUSO'] =>
// $row['XLIVRE'] =>
// $row['UNIDREF'] =>
// $row['GTIN'] =>
// $row['FCI'] =>
// $row['FCP'] =>
// $row['TEXTO1'] =>
// $row['TEXTO2'] =>
// $row['TEXTO3'] =>
// $row['TEXTO4'] =>
// $row['TEXTO5'] =>
// $row['TEXTO6'] =>
// $row['TEXTO7'] =>
// $row['TEXTO8'] =>
// $row['TEXTO9'] =>
// $row['TEXTO10'] =>
    
    
    
    
    
    
//     [IDEST] => 1
//     [CODIGOEAN] => 7890000020001
//     [GRUPO] => 1
//     [REFER] =>
//     [CART] =>
//     [NCM] => 4061090
//     [STCOD] => 102
//     [ORIGEM] =>
//     [IPI] =>
//     [ICMSALIQ] => 18.00
//     [FORNECEDOR] => MAKRO
//     [APLICACAO] => REQUEIJAO BISNAGA 1,800KG
//     [APLICACAOECF] => REQUEIJAO BISNAGA 1,800KG
//     [UNIDADE] => UN
//     [VALCUSTO] => 12.890
//     [VALVAREJO] => 12.890
//     [VALATACADO] => 12.890
//     [SDISPO] => 0.000
//     [SDISPOMEDIO] => 0.000
//     [DENTRADA] => 2013-11-27
//     [DVALIDADE] =>
//     [LOTE] =>
//     [NFEUC] => 50336
//     [ATIVO] => S
//     [CONTROLE] =>
//     [PROMOCAO] =>
//     [OBS1] => 1000000015003
//     [OBS2] =>
//     [D1] =>
//     [D2] =>
//     [V1] => 0.000
//     [V2] => 32.09
//     [TXC] =>
//     [CMRE] =>
//     [CMDO] =>
//     [CM1] =>
//     [CM2] =>
//     [CEST] =>
//     [CEST1] =>
//     [VCM1] =>
//     [VCM2] =>
//     [VCT1] =>
//     [VCA1] =>
//     [NIC1] =>
//     [PIS] =>
//     [CONFIS] =>
//     [XL1] =>
//     [XL2] =>
//     [XL3] =>
//     [XTL1] =>
//     [XMEDIDA] =>
//     [XCOR] =>
//     [XUSO] =>
//     [XLIVRE] =>
//     [UNIDREF] =>
//     [GTIN] =>
//     [FCI] =>
//     [FCP] =>
//     [TEXTO1] =>
//     [TEXTO2] =>
//     [TEXTO3] =>
//     [TEXTO4] =>
//     [TEXTO5] =>
//     [TEXTO6] =>
//     [TEXTO7] =>
//     [TEXTO8] =>
//     [TEXTO9] =>
//     [TEXTO10] =>
    

