<?php

function totalQuestionStatus($db, $storeId, $status){
	 
	$sql = "SELECT count(*) as total FROM questions
	WHERE store_id  = {$storeId} AND status LIKE '{$status}'";
	$query = $db->query($sql);
	 
	if ( ! $query ) {

		return array();
	}
	$res = $query->fetch(PDO::FETCH_ASSOC);
	return $res['total'];
	 
	 
}
/***************************************************************************************************/
/***************************** Registro de Log do Sistema ******************************************/
/***************************************************************************************************/
function logSyncStart($db, $storeId, $webservie, $action, $message, $request){
    
    $query = $db->insert('log_sync', array(
        'store_id' => $storeId,
        'webservice' => $webservie,
        'action' => $action,
        'message' => $message,
        'request' => $request
    )
        );

    return $db->last_id;
}
function logSyncEnd($db, $syncId, $result){
    
    $query = $db->update('log_sync', 'id', $syncId, array(
        'end' => date('Y-m-d H:i:s'),
        'result' => $result
    ));
}

function productLog($db, $storeId, $module, $section, $action, $key, $id, $type,  $information, $jsonResponse = null, $userId = null){
    
    $query = $db->insert('product_log', array(
        'store_id' => $storeId,
        'module' => $module,
        'section' => $section,
        'action' => $action,
        'key' => $key,
        'id' => $id,
        'type' => $type,
        'information' => $information,
        'json_response' => $jsonResponse,
    	'user_id' => $userId,
    ));
}



/***************************************************************************************************/
/***************************** Funções Ajuda Sistema ***********************************************/
/***************************************************************************************************/
/**
 * Envia email de notificação administrador
 * @param string $message
 */
function notifyAdmin($message){
    
    $message = utf8_encode($message);
    $to      = "dev.masterxml@gmail.com, willians.seo@gmail.com";
    $subject = 'Notificação de Sistema '.date("d.m.y H:i:s");
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html;  charset=UTF-8' . "\r\n";
    $headers .= 'From: system@backoffice.sysplace.com.br' . "\r\n" .
        'Reply-To: no-reply@backoffice.sysplace.com.br' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);
}

/**
 * Envia email de notificação administrador
 * @param string $message
 */
function notifyUsers($to = null, $message){

	$message = utf8_encode($message);
	$to      = "willians.seo@gmail.com".$to;
	$subject = 'Notificação de Sistema '.date("d.m.y H:i:s");
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html;  charset=UTF-8' . "\r\n";
	$headers .= 'From: system@backoffice.sysplace.com.br' . "\r\n" .
			'Reply-To: no-reply@backoffice.sysplace.com.br' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);
}

function get_next($array, $key) {
    $currentKey = key($array);
    while ($currentKey !== null && $currentKey != $key) {
        next($array);
        $currentKey = key($array);
    }
    return next($array);
}
/**
 * Verifica chaves de arrays
 *
 * Verifica se a chave existe no array e se ela tem algum valor.
 * Obs.: Essa funçãoo está no escopo global, pois, vamos precisar muito da mesma.
 *
 * @param array  $array O array
 * @param string $key   A chave do array
 * @return string|null  O valor da chave do array ou nulo
 */
function chk_array ( $array, $key ) {
    
    if ( isset( $array[ $key ] ) && ! empty( $array[ $key ] ) ) {
        return $array[ $key ];
    }
    
    return null;
}


function pre($array){
    
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    
}
/**
 * @param unknown $var
 * @return boolean
 * is_number("12"); // true
 * is_number(NULL); // false
 * 
 */
function is_number($var)
{
	if ($var == (string) (float) $var) {
		return (bool) is_numeric($var);
	}
	if ($var >= 0 && is_string($var) && !is_float($var)) {
		return (bool) ctype_digit($var);
	}
	return (bool) is_numeric($var);
}

function gotoPage($controller, $method) {
    // Verifica se a URL da HOME está configurada
    if ( defined( 'HOME_URI' ) ) {
        // Configura a URL de login
        $uri  = HOME_URI . "/{$controller}/{$method}";
        
        
        // Redireciona
        echo '<meta http-equiv="Refresh" content="0; url=' . $uri . '">';
        echo '<script type="text/javascript">window.location.href = "' . $uri . '";</script>';
        // header('location: ' . $login_uri);
    }
    
    return;
}
function pagination($totalReg, $pagina_atual, $uri, $params = array()){
//     pre($totalReg);
//     pre($pagina_atual);
//     pre($uri);
//     pre($params);
    $uriParam = "";
    if(isset($params)){
        foreach($params as $key => $param){
            
            if($key != 'path' AND !empty($param)){
                $uriParam .= "{$key}/$param/";
            }
            
        }
        
    }
    $records = isset($params['records']) ? $params['records'] : QTDE_REGISTROS ;
    
    /* Idêntifica a primeira página */
    $primeira_pagina = 1;
    
    /* Cálcula qual será a última página */
    $ultima_pagina  = ceil($totalReg / $records);
    
    /* Cálcula qual será a página anterior em relação a página atual em exibição */
    $pagina_anterior = ($pagina_atual > 1) ? $pagina_atual -1 : 1 ;
    
    /* Cálcula qual será a pŕoxima página em relação a página atual em exibição */
    $proxima_pagina = ($pagina_atual < $ultima_pagina) ? $pagina_atual +1 : 1 ;
    
    /* Cálcula qual será a página inicial do nosso range */
    $range_inicial  = (($pagina_atual - RANGE_PAGINAS) >= 1) ? $pagina_atual - RANGE_PAGINAS : 1 ;

    /* Cálcula qual será a página final do nosso range */
    $range_final   = (($pagina_atual + RANGE_PAGINAS) <= $ultima_pagina ) ? $pagina_atual + RANGE_PAGINAS : $ultima_pagina ;
   
    /* Verifica se vai exibir o botão "Primeiro" e "Pŕoximo" */
    $exibir_botao_inicio = ($range_inicial < $pagina_atual) ? 'visibility: visible;' : 'visibility: hidden;';
    
    /* Verifica se vai exibir o botão "Anterior" e "Último" */
    $exibir_botao_final = ($range_final > $pagina_atual) ? 'visibility: visible;' : 'visibility: hidden;';
    
    
    
    echo "<div class='box-footer clearfix'>

     {$totalReg} Registros | Página $pagina_atual de {$ultima_pagina}

     	<ul class='pagination pagination-sm no-margin pull-right'>

            <li><a class='box-navegacao' style='{$exibir_botao_inicio}' href='{$uri}/Page/{$primeira_pagina}/{$uriParam}' title='Primeira Página'>Primeira</a></li>
         
            <li><a class='box-navegacao' style='{$exibir_botao_inicio}' href='{$uri}/Page/{$pagina_anterior}/{$uriParam}' title='Página Anterior'>Anterior</a></li>";    
          
            for ($i = $range_inicial; $i <= $range_final; $i++){
          
                $destaque = ($i == $pagina_atual) ? 'active' : '' ;  
              
                echo "<li class='{$destaque}'><a class='box-numero>' href='{$uri}/Page/{$i}/{$uriParam}'>{$i}</a></li>";
          
            }  
          
            echo "<li><a class='box-navegacao' style='{$exibir_botao_final}' href='{$uri}/Page/{$proxima_pagina}/{$uriParam}' title='Próxima Página'>Próxima</a></li>
            
            <li><a class='box-navegacao' style='{$exibir_botao_final}' href='{$uri}/Page/{$ultima_pagina}/{$uriParam}' title='Última Página'>Última</a></li>  
        </ul>

     </div>";
    
    
}
function getStoreConfig($db, $storeId, $module = null){
	
	
	$sql = "SELECT * FROM store_config WHERE store_id = {$storeId}";
	if(isset($module)){
		$sql = "SELECT * FROM store_config WHERE store_id = {$storeId} AND module LIKE '{$module}'";
	}
	$configs = array();
	$res = $db->query($sql);
	$resStoreConfig  = $res->fetchAll(PDO::FETCH_ASSOC);
	foreach($resStoreConfig as $k => $config){
		$configs[$config['module']][$config['name']] = $config['value'];
	}
	
	return $configs;
	
}

function getModuleConfig($db, $storeId, $moduleId){
    
    $sql = "SELECT method FROM modules WHERE id = {$moduleId}";
    $res = $db->query($sql);
    $resMethod  = $res->fetch(PDO::FETCH_ASSOC);
    
    if(!isset($resMethod['method'])){
        return array();
    }
    $method = strtolower($resMethod['method']);
    $sql = "SELECT * FROM module_{$method} WHERE store_id = {$storeId}";
    $query = $db->query($sql);
    $configModule  = $query->fetch(PDO::FETCH_ASSOC);
    if(isset($configModule['password'])){
        unset($configModule['password']);
    }
    if(isset($configModule['user'])){
        unset($configModule['user']);
    }
    if(isset($configModule['session_id'])){
        unset($configModule['session_id']);
    }
//     if(isset($configModule['wsdl'])){
//         unset($configModule['wsdl']);
//     }
    if(isset($configModule['dbname'])){
        unset($configModule['dbname']);
    }
    if(isset($configModule['port'])){
        unset($configModule['port']);
    }
    if(isset($configModule['host'])){
        unset($configModule['host']);
    }
    
    $sqlStore = "SELECT * FROM stores WHERE id = {$storeId}";
    $resStores = $db->query($sqlStore);
    $configModule['store_info']  = $resStores->fetch(PDO::FETCH_ASSOC);
    
    return $configModule;
    
}

function getSystemDefaultPaymentStatus($status, $key = null){
    
    $statusSystem = array();
    $status = trim(strtolower($status));
    switch($status){
        case "new": $statusSystem = array("code" => "new", "label" => "Pendente", "class" => "warning"  );break;
        case "processing": $statusSystem = array("code" => "processing", "label" => "Processando", "class" => "info"  );break;
        case "paid": $statusSystem = array("code" => "paid", "label" => "Pago", "class" => "success"  );break;
        case "approved": $statusSystem = array("code" => "approved", "label" => "Aprovado", "class" => "success" );break;
        case "pending": $statusSystem = array("code" => "pending", "label" => "Pendente", "class" => "warning" );break;
        case "pending_payment": $statusSystem = array("code" => "pending", "label" => "Pagamento Pendente", "class" => "warning" );break;
        case "waiting_payment": $statusSystem = array("code" => "pending", "label" => "Pagamento Pendente", "class" => "warning" );break;
        case "invoiced": $statusSystem = array("code" => "invoiced", "label" => "Faturado", "class" => "gray" );break;
        case "ready_to_ship": $statusSystem = array("code" => "ready_to_ship", "label" => "Preparando", "class" => "info" );break;
        case "shipped": $statusSystem = array("code" => "shipped", "label" => "Enviado", "class" => "purple" );break;
        case "order_shipped": $statusSystem = array("code" => "order_shipped", "label" => "Enviado", "class" => "purple" );break;
        case "delivered": $statusSystem = array("code" => "delivered", "label" => "Entregue", "class" => "primary" );break;
        case "complete": $statusSystem = array("code" => "complete", "label" => "Entregue", "class" => "primary" );break;
        case "not_delivered": $statusSystem = array("code" => "not_delivered", "label" => "Não entregue", "class" => "primary" );break;
        case "unshipped": $statusSystem = array("code" => "unshipped", "label" => "Aguardando Envio", "class" => "warning" );break;
        case "canceled": $statusSystem = array("code" => "cancelled", "label" => "Cancelado", "class" => "primary" );break;
        case "cancelled": $statusSystem = array("code" => "cancelled", "label" => "Cancelado", "class" => "primary" );break;
        case "shipment_exception": $statusSystem = array("code" => "shipment_excption", "label" => "Problemas no transporte", "class" => "warning" );break;
        case "payment_overdue": $statusSystem = array("code" => "payment_overdue", "label" => "Pagamento atrasado", "class" => "success" );break;
        default :
            $status = strtolower($status);
            $statusSystem = array("code" => "{$status}", "label" => "{$status}", "class" => "info"  );
            break;
            
    }
    
    if(isset($key)){
        
        return $statusSystem[$key];
    }else{
        return $statusSystem;
    }
}
function sizeFilter( $bytes )
{
	$label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
	for( $i = 0; $bytes >= 1024 && $i < ( count( $label ) -1 ); $bytes /= 1024, $i++ );
	return( round( $bytes, 2 ) . " " . $label[$i] );
}
function listSystemStatus(){
    
    return array(
        'new' => "Novo",
        'paid' => "Pago",
        'approved' => "Aprovado",
        'cancelled' => "Cancelado",
        'pending' => "Pendente",
        'ready_to_ship' => "Pronto para envio",
        'shipped' => "Enviado",
        'delivered' => "Entregue",
        'invoiced' => "Faturado"
    );
    
}

function verifyAds($db, $storeId, $marketplace, $id){
		
	$res = array();

	switch($marketplace){
	    
		case 'Mercadolivre':
			$sql = "SELECT * FROM ml_products WHERE store_id = {$storeId} AND id = {$id}";
			$query = $db->query($sql);
			$res = $query->fetch(PDO::FETCH_ASSOC);
			
			break;

	}
	
	if(!empty($res['id'])){
	    return $res;
	}
	
	return false;
}

function getPublicationsBySku($db, $storeId, $sku){

	$publications = '';

	$sqlMkt = "SELECT count(marketplace) as qtd, marketplace, url FROM publications
        				WHERE store_id = ? AND sku LIKE ? GROUP BY marketplace";
	$queryMkt = $db->query($sqlMkt,  array($storeId, $sku));
	$publicationsSku =  $queryMkt->fetchAll(PDO::FETCH_ASSOC);
	
	if(isset($publicationsSku[0]['marketplace'])){
		foreach($publicationsSku as $p => $publication){
				
				
			switch($publication['marketplace']){
				case 'Mercadolivre':
					$publications .= !empty($publications) ? " " : "" ;
					$publications .= "<a href='{$publication['url']}' class='label label-warning' title='{$publication['qtd']} Mercadolivre' target='_blank' ><i class='fa fa-legal'></i></a>";
					break;
				case 'B2W':
					$publications .= !empty($publications) ? " " : "" ;
					$publications .= "<a href='{$publication['url']}' class='label label-info' title='B2W' target='_blank' ><i class='fa fa-cloud'></i></a>";
					break;
				case 'Ecommerce':
					$publications .= !empty($publications) ? " " : "" ;
					$publications .= "<a href='{$publication['url']}' class='label label-success' title='Ecommerce' target='_blank' ><i class='fa  fa-shopping-cart'></i></a>";
					break;
				case 'Amazon':
					$publications .= !empty($publications) ? " " : "" ;
					$publications .= "<a href='{$publication['url']}' class='label label-primary' title='Amazon' target='_blank' ><i class='fa  fa-amazon'></i></a>";
					break;
				case 'Marketplace':
					$publications .= !empty($publications) ? " " : "" ;
					$publications .= "<a href='{$publication['url']}' class='label label-depato' title='Marketplace Sysplace' target='_blank' ><i class='fa fa-map-marker'></i></a>";
					break;
				case 'Tray':
					$publications .= !empty($publications) ? " " : "" ;
					$publications .= "<a href='{$publication['url']}' class='label label-primary' title='Ecommerce Tray target='_blank' ><i class='fa  fa-shopping-cart'></i></a>";
					break;
				case 'Magento2':
					$publications .= !empty($publications) ? " " : "" ;
					$publications .= "<a href='{$publication['url']}' class='label label-magento' title='Marketplace Sysplace' target='_blank' ><i class='fab fa-magento'></i></a>";
					break;
			}
				
				
		}
	}

	return $publications;
		
}

/***************************************************************************************************/
/*********************************** Recupera Arquivo Imagens **************************************/
/***************************************************************************************************/

function getThumbnailSku($db, $storeId, $sku){
    $query = $db->query("SELECT thumbnail FROM available_products WHERE store_id = ? AND sku LIKE ?", array($storeId, $sku));
    $row = $query->fetch(PDO::FETCH_ASSOC);
    
    return $row;
}


function getUrlImageFromParentId($db, $storeId, $parendtId){
    $query = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = ? AND parent_id LIKE ?", 
        array($storeId, $parendtId));
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
    $urlImage = array();
    foreach($products as $key => $product){
        
        $pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$product['id']}";
        $pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$product['id']}";
        
        if(file_exists($pathRead)){
            
            $iterator = new DirectoryIterator($pathRead);
            foreach ( $iterator as $key => $entry ) {
                $file = $entry->getFilename();
                if($file != '.' AND $file != '..'){
                    $urlImage[] = $pathShow."/".$file;
                }
            }
        }
        
        if(isset($urlImage)){
            sort($urlImage);
            continue;
        }
        
    }
    
    return $urlImage;
}


function getIdImageDirFromParentId($db, $storeId, $parendtId){
	$query = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = ? AND parent_id LIKE ?",
			array($storeId, $parendtId));
	$products = $query->fetchAll(PDO::FETCH_ASSOC);
	foreach($products as $key => $product){

		$pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$product['id']}";

		if(file_exists($pathRead)){

			$iterator = new DirectoryIterator($pathRead);
			foreach ( $iterator as $key => $entry ) {
				$file = $entry->getFilename();
				if($file != '.' AND $file != '..'){
					return $product['id'];
				}
			}
		}
	}

	return false;
}


function getUrlImageFromSku($db, $storeId, $sku){
    $query = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = ? AND sku LIKE ?", array($storeId, $sku));
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$row['id']}";
    $pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$row['id']}";
    
    $urlImage = array();
    
    if(file_exists($pathRead)){
        
        $iterator = new DirectoryIterator($pathRead);
        foreach ( $iterator as $key => $entry ) {
            $file = $entry->getFilename();
            if($file != '.' AND $file != '..'){
                $urlImage[] = $pathShow."/".$file;
            }
            
        }
    }
    sort($urlImage);
    
    return $urlImage;
}
function getUrlImageFromId($db, $storeId, $productId){
	$pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$productId}";
	$pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$productId}";

	$urlImage = array();

	if(file_exists($pathRead)){

		$iterator = new DirectoryIterator($pathRead);
		foreach ( $iterator as $key => $entry ) {
			$file = $entry->getFilename();
			if($file != '.' AND $file != '..'){
				$urlImage[] = $pathShow."/".$file;
			}

		}
	}
	sort($urlImage);

	return $urlImage;
}


function getPathImageFromSku($db, $storeId, $sku){
	$query = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = ? AND sku LIKE ?", array($storeId, $sku));
	$row = $query->fetch(PDO::FETCH_ASSOC);
	$pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$row['id']}";
	$pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$row['id']}";

	$urlImage = array();

	if(file_exists($pathRead)){

		$iterator = new DirectoryIterator($pathRead);
		foreach ( $iterator as $key => $entry ) {
			$file = $entry->getFilename();
			if($file != '.' AND $file != '..'){
				$urlImage[] = $pathRead."/".$file;
			}

		}
	}
	sort($urlImage);

	return $urlImage;
}
function getPathImageFromParentId($db, $storeId, $parendtId){
	$query = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = ? AND parent_id LIKE ?",
			array($storeId, $parendtId));
	$products = $query->fetchAll(PDO::FETCH_ASSOC);
	$urlImage = array();
	foreach($products as $key => $product){

		$pathShow = "https://backoffice.sysplace.com.br/Views/_uploads/store_id_{$storeId}/products/{$product['id']}";
		$pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$product['id']}";

		if(file_exists($pathRead)){

			$iterator = new DirectoryIterator($pathRead);
			foreach ( $iterator as $key => $entry ) {
				$file = $entry->getFilename();
				if($file != '.' AND $file != '..'){
					$urlImage[] = $pathRead."/".$file;
				}
			}
		}

		if(isset($urlImage)){
			sort($urlImage);
			continue;
		}

	}

	return $urlImage;
}

function getTotalImages($storeId, $productId){
    $pathDir = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$productId}";
    if(file_exists($pathDir)){
        $dir = scandir($pathDir);
        $images = count($dir) > 2 ? count($dir) -2 : 0 ;
    }else{
        $images = 0;
    }
    return $images;
}
function getTotalImagesFromParent($db, $storeId, $parendtId){
	$query = $db->query("SELECT id, parent_id FROM available_products WHERE store_id = ? AND parent_id LIKE ?",
			array($storeId, $parendtId));
	$products = $query->fetchAll(PDO::FETCH_ASSOC);
	$images = 0 ;
	foreach($products as $key => $product){
		$pathDir = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$product['id']}";
		if(file_exists($pathDir)){
			$dir = scandir($pathDir);
			$images += count($dir) > 2 ? count($dir) -2 : 0 ;
		}else{
			$images += 0;
		}
	}
	return $images;
}
/**
 * Copy a file, or recursively copy a folder and its contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @param       int      $permissions New folder creation permissions
 * @return      bool     Returns true on success, false on failure
 */
function xcopy($source, $dest, $permissions = 0777)
{
	$sourceHash = hashDirectory($source);
	// Check for symlinks
	if (is_link($source)) {
		return symlink(readlink($source), $dest);
	}

	// Simple copy for a file
	if (is_file($source)) {
		return copy($source, $dest);
	}

	// Make destination directory
	if (!is_dir($dest)) {
		mkdir($dest, $permissions);
	}

	// Loop through the folder
	$dir = dir($source);
	while (false !== $entry = $dir->read()) {
		// Skip pointers
		if ($entry == '.' || $entry == '..') {
			continue;
		}

		// Deep copy directories
		if($sourceHash != hashDirectory($source."/".$entry)){
			xcopy("$source/$entry", "$dest/$entry", $permissions);
		}
	}

	// Clean up
	$dir->close();
	return true;
}
function xcopyImageProductId($source, $dest, $fromProductId, $toProductId, $permissions = 0777)
{
	$sourceHash = hashDirectory($source);
	// Check for symlinks
	if (is_link($source)) {
		return symlink(readlink($source), $dest);
	}

	// Simple copy for a file
	if (is_file($source)) {
		return copy($source, $dest);
	}

	// Make destination directory
	if (!is_dir($dest)) {
		mkdir($dest, $permissions);
	}

	// Loop through the folder
	$dir = dir($source);
	while (false !== $entry = $dir->read()) {
		// Skip pointers
		if ($entry == '.' || $entry == '..') {
			continue;
		}
		
		$entryDest = str_replace($fromProductId, $toProductId, $entry);

		// Deep copy directories
		if($sourceHash != hashDirectory($source."/".$entry)){
			xcopyImageProductId("$source/$entry", "$dest/$entryDest",$fromProductId, $toProductId, $permissions);
		}
	}

	// Clean up
	$dir->close();
	return true;
}
// In case of coping a directory inside itself, there is a need to hash check the directory otherwise and infinite loop of coping is generated

function hashDirectory($directory){
	if (! is_dir($directory)){ return false; }

	$files = array();
	$dir = dir($directory);

	while (false !== ($file = $dir->read())){
		if ($file != '.' and $file != '..') {
			if (is_dir($directory . '/' . $file)) { $files[] = hashDirectory($directory . '/' . $file); }
			else { $files[] = md5_file($directory . '/' . $file); }
		}
	}

	$dir->close();

	return md5(implode('', $files));
}

function updateImageThumbnail($db, $storeId, $produtId){

	include_once '/var/www/html/app_mvc/Views/_uploads/images.php';

	$sql = "SELECT * FROM available_products WHERE store_id = {$storeId} AND id = {$produtId}";

	$query = $db->query($sql);

	while($res =  $query->fetch(PDO::FETCH_ASSOC)){

		$count = 1;
		$pathShow = "/Views/_uploads/store_id_{$storeId}/products/{$res['id']}";
		$pathShowThumb = "/Views/_uploads/store_id_{$storeId}/thumbnail/{$res['id']}";
		$pathRead = "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/products/{$res['id']}";
			
		if(file_exists($pathRead)){
			$picturesArray = array();
			$iterator = new DirectoryIterator($pathRead);
			foreach ( $iterator as $key => $entry ) {
				$file = $entry->getFilename();
				if($file != '.' AND $file != '..'){
					$count++;
					$fileSize = $entry->getSize();
					$parts = explode("-", $file);
					$array = array_slice($parts, -2);

					$picturesArray[$array[0]] = array(
							'source' =>  $pathShow.'/'.$file,
							"file_size" => $fileSize,
							"path_show" => $pathShow,
							"file" => $file
					);
				}
			}
				
			ksort($picturesArray);
				
			foreach ($picturesArray as $key => $pics) {
				if(!empty($pics['file'])){
					$width = '160';
					$ext = explode(".", $pics['file']);
					$fileNameThumbnail = "thumbnail_{$width}_".$res['id'].'.'.end($ext);
					$target_dir_thumb =  "/var/www/html/app_mvc/Views/_uploads/store_id_{$storeId}/thumbnail/{$res['id']}/";
					$filePathThumbnail = $target_dir_thumb . basename($fileNameThumbnail);
					if (!file_exists($target_dir_thumb)) {
						@mkdir($target_dir_thumb);
					}
					$filePath = $pathRead."/" . basename($pics['file']);

					$thumbnailImage = createThumbnail($filePath, $filePathThumbnail, $width);

					$queryAP = $db->update('available_products',
							array('store_id', 'id'),
							array($storeId, $res['id']),
							array('thumbnail' => "{$pathShowThumb}/{$fileNameThumbnail}", 'image' => $pics['source']
							));

					if(!$queryAP){
						pre($queryAP);
					}

					return "<img src='{$pathShowThumb}/{$fileNameThumbnail}' alt='Product Image' >";
				}

			}

		}

		return;
			
	}

}
/***************************************************************************************************/
/*************************** Funções para Validar Medidas ******************************************/
/***************************************************************************************************/
function validateKg($weight){
	
	if(empty($weight)){
		return ;
	}
	
	$weight =  number_format(filter_var(str_replace(',', '.',$weight), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION), 3, '.', '');
	$weight = $weight > 1000 ? $weight / 1000  : $weight ;
	return $weight;
	
}

function validateCm($value){
	if(empty($value)){
		return ;
	}
	$value =    filter_var(str_replace(',', '.',$value), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$value = $value < 1 ? $value * 100  : $value ;
	return $value;
}

function validatePrice($price){
	if(empty($price)){
		return ;
	}
	$price = (float) str_replace(",", ".", $price);
	$price =  number_format((double)filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION), 2, '.', '');
	return $price;
}
/***************************************************************************************************/
/***************************** Funções para Tratar Textos ******************************************/
/***************************************************************************************************/

/**
 * Funcao para deixar somente os numeros de uma string
 * @param string $str
 * return string
 */
function getNumbers($str){
    
    return preg_replace("/[^0-9]/", "", $str);
    
}


function var_dump_ret($mixed = null) {
    ob_start();
    var_dump($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function var_dump_pre($mixed = null) {
    echo '<pre>';
    var_dump($mixed);
    echo '</pre>';
    return null;
}




function friendlyText($string){
    return trim(ucwords(strtolower($string)));
}

/**
 * Torna titulo amigavel SEO
 * @param string $brand
 * @return boolean
 */
function titleFriendly($title){
    $title =  utf8_encode(removeAcentosNew(trim($title)));
 	$titleFriendly = strtolower($title);
    $titleFriendly = str_replace(' – '," - ", $titleFriendly);
    $titleFriendly = str_replace('º',"", $titleFriendly);
    $titleFriendly = str_replace('"',"", $titleFriendly);
    $titleFriendly = str_replace("'","", $titleFriendly);
    $titleFriendly = str_replace("  "," ", $titleFriendly);
    $titleFriendly = str_replace(" "," ", $titleFriendly);
    $titleFriendly = str_replace(" ","-", $titleFriendly);
    $titleFriendly = str_replace("/","", $titleFriendly);
    $titleFriendly = str_replace(".","-", $titleFriendly);
    $titleFriendly = str_replace(",","-", $titleFriendly);
    $titleFriendly = str_replace("---","-", $titleFriendly);
    $titleFriendly = str_replace("--","-", $titleFriendly);
//     $titleFriendly = strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $titleFriendly));
    
    return trim($titleFriendly);
}

function imageFileNameFriendly($title){
    $title =  utf8_encode(RemoveAcentos($title));
    $titleFriendly = strtolower($title);
    $titleFriendly = str_replace('"',"", $titleFriendly);
    $titleFriendly = str_replace("'","", $titleFriendly);
    $titleFriendly = str_replace("  "," ", $titleFriendly);
    $titleFriendly = str_replace(" "," ", $titleFriendly);
    $titleFriendly = str_replace(" ","-", $titleFriendly);
    $titleFriendly = str_replace("/","", $titleFriendly);
    $titleFriendly = str_replace("---","-", $titleFriendly);
    $titleFriendly = str_replace("--","-", $titleFriendly);
    $titleFriendly = str_replace(".","", $titleFriendly);
    return $titleFriendly;
}

function RemoveAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$string);
}
function removeAcentosNew($s)
{
    $arr1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
    $arr2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
    return str_replace($arr1, $arr2, $s); 
}

function mb_ucfirst($string, $encoding = 'UTF-8'){
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}
/***************************************************************************************************/
/*********************************** Formata Data, Documento, Telefone  ****************************/
/***************************************************************************************************/
function dbDate($dateBr){
    
    $date = str_replace('/', '-', $dateBr);
    $dbDate =  date('Y-m-d', strtotime($date));
    return $dbDate;
    
}

function dateTimeBr($dbDate, $separator = null){
    
    if(!isset($dbDate)){
        return null;
    }
    
    $dateTime = explode(" ", $dbDate);
    
    $dateTimeBr =  date('d-m-Y', strtotime($dateTime[0]));
    if(isset($separator)){
        $dateTimeBr = str_replace('-', $separator, $dateTimeBr);
    }
    $dateTimeBr .= isset($dateTime[1]) ? " ".$dateTime[1] : "" ;
    
    return $dateTimeBr;
    
}
function dateTimeBrBreakLine($dbDate, $separator = null){
    
    if(!isset($dbDate)){
        return null;
    }
    
    $dateTime = explode(" ", $dbDate);
    
    $dateTimeBr =  date('d-m-Y', strtotime($dateTime[0]));
    if(isset($separator)){
        $dateTimeBr = str_replace('-', $separator, $dateTimeBr);
    }
    $dateTimeBr .= isset($dateTime[1]) ? "<br>".$dateTime[1] : "" ;
    
    return $dateTimeBr;
    
}
function dateFromTimeBr($dbDate, $separator = null){
    
    if(!isset($dbDate)){
        return null;
    }
    $dateBr = '';
    $dateTime = explode(" ", $dbDate);
    $dateTimeBr =  date('d-m-Y', strtotime($dateTime[0]));
    if(isset($separator)){
        $dateTimeBr = str_replace('-', $separator, $dateTimeBr);
    }
    
    return $dateTimeBr;
    
}

function dateWeekFromTimeBr($dbDate, $separator = null){

	if(!isset($dbDate)){
		return null;
	}
	$dateBr = '';
	$dateTime = explode(" ", $dbDate);
	$dateTimeBr =  date('d-m-Y', strtotime($dateTime[0]));
	if(isset($separator)){
		$dateTimeBr = str_replace('-', $separator, $dateTimeBr);
	}

	return $dateTimeBr;

}

function dateBr($dateBr, $separator = null){
    
    
    $dateBr =  date('d-m-Y', strtotime($dateBr));
    if(isset($separator)){
        $dateBr = str_replace('-', $separator, $dateBr);
    }
    
    
    return $dateBr;
    
}

function getTimeFromTimestamp($timestamp){


	$datetime = explode(" ",$timestamp);
	$date = $datetime[0];
	$time = $datetime[1];

	return $time;

}

function getDateFromTimestamp($timestamp){


	$datetime = explode(" ",$timestamp);
	$date = $datetime[0];
	$time = $datetime[1];

	return $date;

}
function formatarCpfCnpj($cnpj_cpf)
{
    if (strlen(preg_replace("/\D/", '', $cnpj_cpf)) === 11) {
        $response = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
    } else {
        $response = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }
    
    return $response;
}

function formataCep($cep){
    
    return  preg_replace("/^(\d{5})(\d{3})$/", "\\1-\\2", $cep);
    
}
function digits($num){
    
    return (int) (log($num, 10)+1);
    
}
function formatPhone($phone)
{
    
    if(!isset($phone)){
        return null;
    }
    $formatedPhone = preg_replace('/[^0-9]/', '', trim($phone));
    $formatedPhone = str_replace(' ', '', $formatedPhone);
    $formatedPhone = intval($formatedPhone);
    switch(digits($formatedPhone)){
        case 1: $formatedPhone.="000000000";break;
        case 2: $formatedPhone.="00000000";break;
        case 3: $formatedPhone.="0000000";break;
        case 4: $formatedPhone.="000000";break;
        case 5: $formatedPhone.="00000";break;
        case 6: $formatedPhone.="0000";break;
        case 7: $formatedPhone.="000";break;
        case 8: $formatedPhone.="00";break;
        case 9: $formatedPhone.="0";break;
    }
    
    preg_match('/^([0-9]{2})([0-9]{4,5})([0-9]{4})$/', $formatedPhone, $matches);
  
    if ($matches) {
        return '('.$matches[1].') '.$matches[2].'-'.$matches[3];
    }
    return $formatedPhone; // return number without format
}

function dateTimeNow(){
    return date('Y-m-d H:i:s');
}
function getUf($estado){
    
    $estadosBrasileiros = array(
        'AC'=>'Acre',
        'AL'=>'Alagoas',
        'AP'=>'Amapá',
        'AM'=>'Amazonas',
        'BA'=>'Bahia',
        'CE'=>'Ceará',
        'DF'=>'Distrito Federal',
        'ES'=>'Espírito Santo',
        'GO'=>'Goiás',
        'MA'=>'Maranhão',
        'MT'=>'Mato Grosso',
        'MS'=>'Mato Grosso do Sul',
        'MG'=>'Minas Gerais',
        'PA'=>'Pará',
        'PB'=>'Paraíba',
        'PR'=>'Paraná',
    	'PR'=>'Parana',
        'PE'=>'Pernambuco',
        'PI'=>'Piauí',
        'RJ'=>'Rio de Janeiro',
        'RN'=>'Rio Grande do Norte',
        'RS'=>'Rio Grande do Sul',
        'RO'=>'Rondônia',
        'RR'=>'Roraima',
        'SC'=>'Santa Catarina',
        'SP'=>'São Paulo',
        'SE'=>'Sergipe',
        'TO'=>'Tocantins'
    );
    foreach($estadosBrasileiros as $uf => $estados){
        if(mb_strtoupper($estados, 'UTF-8') == mb_strtoupper($estado, 'UTF-8')){
            return $uf;
        }
    }
    
    return $estado;
}


function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

/**
 * Função para capturar valor da tag dentro de uma string
 * @param string $string
 * @param string $tagname
 * @return int
 */
function everything_in_tags($string, $tagname)
{
    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
    preg_match($pattern, $string, $matches);
    if(!empty($matches[1])){
        return $matches[1];
    }
}

/**
 * Formata moeda reais
 * @param string $valor1
 * @param string $valor2
 * @param string $operacao
 * @return string
 */
function formataReais($valor1, $valor2, $operacao)
{
    /*     function formataReais ($valor1, $valor2, $operacao)
     *
     *     $valor1 = Primeiro valor da operação
     *     $valor2 = Segundo valor da operação
     *     $operacao = Tipo de operações possíveis . Pode ser :
     *     "+" = adição,
     *     "-" = subtração,
     *     "*" = multiplicação
     *
     */
    
    
    // Antes de tudo , arrancamos os "," e os "." dos dois valores passados a função . Para isso , podemos usar str_replace :
    $valor1 = str_replace (",", "", $valor1);
    $valor1 = str_replace (".", "", $valor1);
    
    $valor2 = str_replace (",", "", $valor2);
    $valor2 = str_replace (".", "", $valor2);
    
    
    // Agora vamos usar um switch para determinar qual o tipo de operação que foi definida :
    switch ($operacao) {
        // Adição :
        case "+":
            $resultado = $valor1 + $valor2;
            break;
            
            // Subtração :
        case "-":
            $resultado = $valor1 - $valor2;
            break;
            
            // Multiplicação :
        case "*":
            $resultado = $valor1 * $valor2;
            break;
            
    } // Fim Switch
    
    
    // Calcula o tamanho do resultado com strlen
    $len = strlen ($resultado);
    
    
    // Novamente um switch , dessa vez de acordo com o tamanho do resultado da operação ($len) :
    // De acordo com o tamanho de $len , realizamos uma ação para dividir o resultado e colocar
    // as vírgulas e os pontos
    switch ($len) {
        // 2 : 0,99 centavos
        case "2":
            $retorna = "0,$resultado";
            break;
            
            // 3 : 9,99 reais
        case "3":
            $d1 = substr("$resultado",0,1);
            $d2 = substr("$resultado",-2,2);
            $retorna = "$d1,$d2";
            break;
            
            // 4 : 99,99 reais
        case "4":
            $d1 = substr("$resultado",0,2);
            $d2 = substr("$resultado",-2,2);
            $retorna = "$d1,$d2";
            break;
            
            // 5 : 999,99 reais
        case "5":
            $d1 = substr("$resultado",0,3);
            $d2 = substr("$resultado",-2,2);
            $retorna = "$d1,$d2";
            break;
            
            // 6 : 9.999,99 reais
        case "6":
            $d1 = substr("$resultado",1,3);
            $d2 = substr("$resultado",-2,2);
            $d3 = substr("$resultado",0,1);
            $retorna = "$d3.$d1,$d2";
            break;
            
            // 7 : 99.999,99 reais
        case "7":
            $d1 = substr("$resultado",2,3);
            $d2 = substr("$resultado",-2,2);
            $d3 = substr("$resultado",0,2);
            $retorna = "$d3.$d1,$d2";
            break;
            
            // 8 : 999.999,99 reais
        case "8":
            $d1 = substr("$resultado",3,3);
            $d2 = substr("$resultado",-2,2);
            $d3 = substr("$resultado",0,3);
            $retorna = "$d3.$d1,$d2";
            break;
            
    } // Fim Switch
    
    // Por fim , retorna o resultado já formatado
    return $retorna;
}


function validateEmailsTypes(){
    
    if(strpos(strtolower($email), "@") === true){
        return false;
    }
    
    $invalidEmails = array("amazon", 'skyhub', "mercadol", "mercadopago");
    
    foreach($invalidEmails as $key => $type){
        
        if(strpos(strtolower($email), "amazon") !== false){
            return false;
        }
        
        
    }
    
    return true;
    
}
function getSSLFile($url) {
	 
	$options = array(
			CURLOPT_RETURNTRANSFER => true, // return web page
			CURLOPT_HEADER => false, // don’t return headers
			CURLOPT_FOLLOWLOCATION => true, // follow redirects
			CURLOPT_ENCODING => '', // handle all encodings
			CURLOPT_USERAGENT => 'spider', // who am i
			CURLOPT_AUTOREFERER => true, // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
			CURLOPT_TIMEOUT => 120, // timeout on response
			CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
			CURLOPT_SSL_VERIFYPEER => false, // Disabled SSL Cert checks
			CURLOPT_SSL_VERIFYHOST => 2 , // Disabled SSL Cert checks
			CURLOPT_CAINFO => getcwd() ."/etc/ssl/backoffice_sysplace_com_br.crt" // Disabled SSL Cert checks
	);
	 
	$ch = curl_init($url);
	 
	curl_setopt_array( $ch, $options );
	 
	$curl_response = curl_exec($ch);
	 
	curl_close($ch);
	 
	return $curl_response;
		
}
function getParentIdFromId($db, $storeId, $productId){

	$sql = "SELECT parent_id FROM available_products WHERE store_id = {$storeId} AND id = {$productId}";
	$query = $db->query($sql);
	$res = $query->fetch(PDO::FETCH_ASSOC);
	if($res){
		return $res['parent_id'];
	}
	return;

}

function translate($db, $word, $storeId){
	$sql = "SELECT translate, description FROM translates WHERE store_id = {$storeId} AND word LIKE '{$word}'";
	$query = $db->query($sql);
	$res = $query->fetch(PDO::FETCH_ASSOC);
	if($res){
		return $res;
	}else{
		$sql = "SELECT * FROM translates WHERE store_id = 0 AND word LIKE '{$word}'";
		$query = $db->query($sql);
		$res = $query->fetch(PDO::FETCH_ASSOC);
		if($res){
			return $res;
		}
	}
	return array();
}


function verifyActionPermisssion($db, $storeId, $module, $action, $userId){
	
	if(empty($userId) 
		|| empty($action) 
		||empty($module) 
		||empty($storeId) 
		||empty($db)){
		
		return false;
	}
	
	$query = $db->query(
			"SELECT  `id`, `account_id`, `cpf`, `name`, `email`, `stores`, 
			`session_id`, `permissions`, `store_id`, `active` 
			FROM users WHERE id = ? AND active = 'T' LIMIT 1",
			array( $userId )
			);
	$fetch = $query->fetch(PDO::FETCH_ASSOC);
	//if admin root allow access	
	if($fetch['cpf'] == '30456130802'){
		return true;
	}
	
	$permissions = unserialize( $fetch['permissions'] );
	
	if(!isset($permissions[0])){
		return false;
	}

	$sqlGPermission = "SELECT permissions.id, group_permissions.module, group_permissions.p_view, group_permissions.p_create,
	group_permissions.p_update, group_permissions.p_delete
	FROM `permissions`
		LEFT JOIN group_permissions ON group_permissions.p_group = permissions.id
		AND group_permissions.store_id = {$storeId} AND group_permissions.module = '{$module}'
		AND group_permissions.p_{$action} = 'T'
	WHERE permissions.permission LIKE '{$permissions[0]}'";
	$queryGPermission = $db->query($sqlGPermission);
	$groupPermission = $queryGPermission->fetch(PDO::FETCH_ASSOC);
	if(!empty($groupPermission["p_{$action}"])){
		return $groupPermission;
	}
	return false;
	
}

function generateEAN($number)
{
	$code = '200' . str_pad($number, 9, '0');
	$weightflag = true;
	$sum = 0;
	// Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit.
	// loop backwards to make the loop length-agnostic. The same basic functionality
	// will work for codes of different lengths.
	for ($i = strlen($code) - 1; $i >= 0; $i--)
	{
		$sum += (int)$code[$i] * ($weightflag?3:1);
		$weightflag = !$weightflag;
	}
	$code .= (10 - ($sum % 10)) % 10;
	return $code;
}
function defaultTextPattern($string){
	
	$string = strtolower(trim($string));
	$string = str_replace('-', ' ', $string);
	$string = str_replace('/', ' ', $string);
	$string = str_replace('_', ' ', $string);
	$string = str_replace('  ', ' ', $string);
	$string = str_replace('   ', ' ', $string);
	$string = ucwords($string);
	return trim($string);
	
}

function IncluiDigito($ean) {
    
    switch(strlen($ean)){
        case 1: $eanEan = '90900000000'.$ean;break;
        case 2: $eanEan = '9090000000'.$ean;break;
        case 3: $eanEan = '909000000'.$ean;break;
        case 4: $eanEan = '90900000'.$ean;break;
        case 5: $eanEan = '9090000'.$ean;break;
        case 6: $eanEan = '909000'.$ean;break;
        case 7: $eanEan = '90900'.$ean;break;
        case 8: $eanEan = '9090'.$ean;break;
    }
    
    
    $digitos = str_split($eanEan);
    $soma = 0;
    foreach ($digitos as $i => $digito) {
        if (($i % 2) === 0) {
            $soma += $digito * 1;
        } else {
            $soma += $digito * 3;
        }
    }
    $resultado = floor($soma / 10) + 1;
    $resultado *= 10;
    $resultado -= $soma;
    if (($resultado % 10) === 0) {
        $eanEan = $eanEan . '0';
    } else {
        $eanEan = $eanEan . $resultado;
    }
    return $eanEan;
}
