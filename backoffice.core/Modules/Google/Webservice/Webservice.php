<?php
set_time_limit ( 30000 );
$path = dirname(__FILE__);
ini_set ("display_errors", true);
ini_set ("libxml_disable_entity_loader", false);
header("Content-Type: text/html; charset=utf-8");

require_once $path .'/../../../Class/class-DbConnection.php';
require_once $path .'/../../../Class/class-MainModel.php';
require_once $path .'/../../../Functions/global-functions.php';
require_once $path .'/../Models/Products/AttributesValuesModel.php';
require_once $path .'/../../../Models/Products/ProductDescriptionModel.php';
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
    
    $moduleConfig = getModuleConfig($db, $storeId, 8);
    
    switch($action){
        
        /**
         * importa e atualiza  categoria titulo e descrição, cor, ean, peso
         * se não houver descrição em available products
         *
         */
        case "import_info_xml":
          
//             $xml = "/var/www/html/app_mvc/Modules/Google/xml/newXml/googleshopping-attr-1.xml";
        	$xml = "/var/www/html/app_mvc/Modules/Google/xml/newXml/googleshopping-attr-sku-1.xml";
            $rss = simplexml_load_file ($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            $attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
            $productsDescriptionModel = new ProductDescriptionModel($db);
            $i = $count = 0;
            foreach ($rss->channel->item as $key => $entry){
                
                $namespaces = $entry->getNameSpaces(true);
                
                $tag = $entry->children($namespaces['g']);
                $sku = trim($tag->id."");
                $parentId = substr($sku, 0, 6); 
                $setaSku = array();
                $sqlVerify = "SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$parentId}' ORDER BY variation asc LIMIT 1";
                $verifyQuery = $db->query($sqlVerify);
                $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
                if(!empty($verify['id'])){
                	
                    $color = strtoupper(str_replace(" ", "-", trim(removeAcentosNew(strtolower($tag->color)))));
                    $colors = explode('-', $color);
                    $color = isset($colors[1]) ? $colors[0]."-".$colors[1] : $colors[0]; 
                    
                    $titleParts = explode(" ", str_replace(strtoupper($verify['reference']), "", strtoupper($tag->title)));
                    $titleUnique = array_unique($titleParts, SORT_STRING); 
                    $title = implode(" ", $titleUnique);
                    $title = str_replace("SAPATO MASCULINO SOCIAL",                     "SAPATO SOCIAL MASCULINO", strtoupper($title));
                    $title = str_replace("CHINELO MASCULINO DE DEDO",                   "CHINELO MASCULINO", strtoupper($title));
                    $title = str_replace("MORMAII GRENDENE",                            "MORMAII", strtoupper($title));
                    $title = str_replace("TÊNIS MASCULINO MIZUNO",                      "TÊNIS MIZUNO MASCULINO", strtoupper($title));
                    $title = str_replace("TÊNIS MASCULINO ACADEMIA OLYMPIKUS",          "TÊNIS OLYMPIKUS MASCULINO", strtoupper($title));
                    $title = str_replace("TÊNIS UNISSEX ACADEMIA OLYMPIKUS",            "TÊNIS OLYMPIKUS MASCULINO", strtoupper($title));
                    $title = str_replace("TÊNIS MASCULINO OLYMPIKUS",                   "TÊNIS OLYMPIKUS MASCULINO", strtoupper($title));
                    $title = str_replace("TÊNIS MASCULINO RUNNING NIKE",                "TÊNIS NIKE RUNNING MASCULINO", strtoupper($title));
                    $title = str_replace("TÊNIS MASCULINO NEW BALANCE",                 "TÊNIS NEW BALANCE MASCULINO", strtoupper($title));
                    $title = str_replace("CHUTEIRA MASCULINA",                          "CHUTEIRA", strtoupper($title));
                    $title = str_replace("MASCULINO COURO FERRACINI",                   "COURO FERRACINI", strtoupper($title));
                    $title = str_replace("MASCULINO COURO NOBUCK FERRACINI",            "COURO FERRACINI", strtoupper($title));
                    $title = str_replace("MASCULINO COURO DEMOCRATA",                   "COURO DEMOCRATA", strtoupper($title));
                    $title = str_replace("DEMOCRATA MASCULINO URBAN",                   "MASCULINO DEMOCRATA URBAN", strtoupper($title));
                    $title = str_replace("BOTA MASCULINA DEMOCRATA",                    "BOTA DEMOCRATA", strtoupper($title));
                    $title = str_replace("BOTA MASCULINA MACBOOT",                      "BOTA MACBOOT MASCULINA", strtoupper($title));
                    $title = str_replace("BOTA MASCULINA MACBOOT",                      "BOTA MACBOOT MASCULINA", strtoupper($title));
                    $title = str_replace("BOTA MASCULINA MACBOOT",                      "BOTA MACBOOT MASCULINA", strtoupper($title));
                    $title = str_replace("SAND ", "SANDÁLIA", strtoupper($title));
                    $title = str_replace("UNISSEX ", "", strtoupper($title));
                    $title = str_replace("FEMININA ", "", strtoupper($title));
                    $title = str_replace("FEMININO ", "", strtoupper($title));
                    $title = str_replace("FEM ", "", strtoupper($title));
                    $title = str_replace("MASCULINO ", "", strtoupper($title));
                    $title = str_replace("MASCULINA ", "", strtoupper($title));
                    $title = str_replace("MASC ", "", strtoupper($title));
                    $title = str_replace("DE DEDO ", "", strtoupper($title));
                    $title = str_replace("23 AO 35", "", strtoupper($title));
                    $title = str_replace("23 AO 36", "", strtoupper($title));
                    $title = str_replace("23 AO 33", "", strtoupper($title));
                    $title = str_replace("30 AO 36", "", strtoupper($title));
                    $title = str_replace("28 AO 34", "", strtoupper($title));
                    $title = str_replace("25 AO 34", "", strtoupper($title));
                    $title = str_replace("28 AO 36", "", strtoupper($title));
                    $title = str_replace("17 AO 26", "", strtoupper($title));
                    $title = str_replace("19 AO 25", "", strtoupper($title));
                    $title = str_replace("20 AO 27", "", strtoupper($title));
                    $title = str_replace("29 AO 35", "", strtoupper($title));
                    $title = str_replace("16 AO 22", "", strtoupper($title));
                    $title = str_replace("17 AO 25", "", strtoupper($title));
                    $title = str_replace("24 AO 34", "", strtoupper($title));
                    $title = str_replace("27 AO 34", "", strtoupper($title));
                    $title = str_replace("23 AO 34", "", strtoupper($title));
                    $title = str_replace("25 AO 32", "", strtoupper($title));
                    $title = str_replace("17 AO 23", "", strtoupper($title));
                    $title = str_replace("18 AO 24", "", strtoupper($title));
                    $title = str_replace("I6 ", "", strtoupper($title));
                    $title = str_replace("I7 ", "", strtoupper($title));
                    $title = str_replace("I8 ", "", strtoupper($title));
                    $title = str_replace("I9 ", "", strtoupper($title));
                    $title = str_replace("V6 ", "", strtoupper($title));
                    $title = str_replace("V7 ", "", strtoupper($title));
                    $title = str_replace("V8 ", "", strtoupper($title));
                    $title = str_replace("V9 ", "", strtoupper($title));
                    
                    
                    $title = strtoupper(str_replace("  ", " ", $title));
                    $colorFriendly = str_replace("-", " ", $color);
                    
                    $colorParts = explode(" ", mb_strtoupper($colorFriendly, 'UTF-8'));
                    $colorUnique = array_unique($colorParts, SORT_STRING);
                    $colorFriendly = implode(" ", $colorUnique);
                    
                    $title = str_replace(" - ", " {$colorFriendly} ", strtoupper($title), $count);
                    if(!$count){
                        $title = trim($title." ".$colorFriendly);
                    }
                    $title = str_replace(".", "", strtoupper($title));
                    $title = str_replace("  ", " ", $title);
                    $title = trim($title);
                    
                    $category = "".$tag->product_type;
                    $gtin = "".$tag->gtin;
                    $gender = "".$tag->gender;
                    $ageGroup = "".$tag->age_group;
                    $weight = getNumbers("".$tag->shipping_weight);
                    $description = "".$tag->description;
   					
                    $categoryRoot = "";
                    switch($gender){
                        case "female":
                            if( $ageGroup == 'kids'){
                                $categoryRoot = "Infantil Menina";
                            }else{
                                $categoryRoot = "Femininos";
                            }
                            break;
                            
                        case "male":
                            if($ageGroup == 'kids'){
                                $categoryRoot = "Infantil Menino";
                            }else{
                                $categoryRoot = "Masculinos";
                            }
                            break;
                            
                         case "unisex":
                            if($ageGroup == 'kids'){
                            	$categoryRoot = "Infantil Menina";
                            }else{
                            	$categoryRoot = "Masculinos";
                            }
                            break;
                    }
//                     pre($gender);
//                     pre($ageGroup);
//                     pre($categoryRoot);
                        
					if(!empty($categoryRoot)){
                                
						$categories = explode(">", $category);
                        $endCategory = trim(end($categories));
                        $category = !empty($endCategory) ? "{$categoryRoot} > {$endCategory}" : $categoryRoot ; 
                                
					}else{
						$categories = explode(">", $category);
						$endCategory = trim(end($categories));
						$category = !empty($categoryRoot) ? "{$categoryRoot} > {$endCategory}" : $categoryRoot ;
					}
                            
                    switch($endCategory){
                    	case "Chinelo": $weight = '700';
                        case "Chinelos": $weight = '700';
                        case "Bota": $weight = '1200';
                        case "Botas": $weight = '1200';
                        case "Botina": $weight = '1200';
                        case "Botainas": $weight = '1200';
                        case "Coturno": $weight = '1200';
                        case "Coturnos": $weight = '1200';
					}
					$partsTitle = explode(trim($verify['reference']), $title);
					if(!isset($partsTitle[1])){
						$title = $title." ".trim($verify['reference']);
					}
// 					$resSub = substr($color, -1);
					$color = str_replace("/", " ", strtoupper(trim($color)));
					$color = str_replace("-", " ", strtoupper($color));
					$color = str_replace("  ", " ", strtoupper($color));
					$color = str_replace(" ", "-", strtoupper($color));
					
					$title = str_replace('/', ' ', $title);
					$title = str_replace('-', ' ', $title);
					$title = str_replace("  ", " ", $title);
					$title = ucwords(mb_strtolower(trim($title)));
// 					echo "<img src='{$tag->additional_image_link[0]}' width='200px' />";
					pre(array(
                   		'product_id' => $verify['id'],
                        'sku' => $verify['sku'],
                        'reference' => $verify['reference'],
						'brand' => $tag->brand."",
                        'title' => $tag->title."",
						'titl2' => $verify['title'],
                        'titl3' => $title,
						'description' => $description,
                        'color' => $color,
                        'category' => $category,
                        'weight' => !empty($weight) ? $weight : '1.000' 
                    	));
					
					$query = $db->update('available_products',
							array('store_id','parent_id'),
							array($storeId, $verify['parent_id']),
							array('title' => $title,
								'color' => trim($color),
								'description' => $description,
								'category' => $category,
								'xml' => 'T',
								'weight' => !empty($weight) ? $weight : '1.000' 
							));
					
					
                }
            }
            
            break;
 
            
        case "import_attributes_xml":
            $count = 1;
            $break = 0;
            $productsAttr = array();
            do{
            	$xml = "/var/www/html/app_mvc/Modules/Google/xml/newXml/googleshopping-attr-parent-{$count}.xml";
                $rss = simplexml_load_file ($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
                foreach ($rss->channel->item as $key => $entry){
                   
                    $namespaces = $entry->getNameSpaces(true);
                    $tag = $entry->children($namespaces['g']);
                    $productId = $tag->id;
                    $parentId = substr($productId, 0, 6); //"".$tag->id;
                    $sqlVerify = "SELECT * FROM available_products WHERE store_id = {$storeId} AND parent_id LIKE '{$productId}' ";
                    $verifyQuery = $db->query($sqlVerify);
                    $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
                    
                    if(isset($verify['id'])){
                    	
                    	
                        $category = "".$tag->product_type;
                        $gender = "".$tag->gender;
                        $ageGroup = "".$tag->age_group;
                        $type = explode(">", $category);
                        
                        $attr = array();
                        if($count == 1){
//                         	$attr['image'] = trim($tag->image_link."");
                            switch($ageGroup){
                                case "kids": $attr['faixa-etaria']  = 'Kids'; break;
                                case "adult": $attr['faixa-etaria']  = 'Adulto'; break;
                                default : $attr['faixa-etaria']  = 'Adulto'; break;
                                
                            }
                            switch($gender){
                                case "female": $attr['genero']  = 'Feminino'; break;
                                case "male": $attr['genero']  = 'Masculino'; break;
                                default : $attr['genero']  = ''; break;
                            }
                            $attr['tipo-de-calcado'] = trim(end($type));
                            if( $attr['tipo-de-calcado'] == 'Oxford'){
                            	$attr['type-age'] == 'Rêtro';
                            }
                            $collections = array(
                            		'V6','V7','V8','V9','V20', 'I6','I7','I8','I9','I20' 
                            );
                            foreach($collections as $k => $collection){
                            	$explode = $collection." ";
	                            $partsTitle = explode("{$explode}", trim("".$tag->title));
	                            if(isset($partsTitle[1])){
	                            	$collectionDescription = '' ;
	                            	switch(trim($explode)){
	                            		case 'V6': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Primavera Verão 2016"; break;
                            			case 'V7': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Primavera Verão 2017"; break;
                            			case 'V8': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Primavera Verão 2018"; break;
                            			case 'V9': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Primavera Verão 2019"; break;
                            			case 'V20': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Primavera Verão 2020"; break;
                            			case 'V21': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Primavera Verão 2021"; break;
	                            		case 'I6': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Outono Inverno 2016"; break;
	                            		case 'I7': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Outono Inverno 2017"; break;
	                            		case 'I8': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Outono Inverno 2018"; break;
	                            		case 'I9': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Outono Inverno 2019"; break;
	                            		case 'I20': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Outono Inverno 2020"; break;
	                            		case 'I21': $collectionDescription = "Coleção {$attr['tipo-de-calcado']} {$verify['brand']} Outono Inverno 2021"; break;
	                            	}
	                            	if(!empty($collectionDescription)){
	                            		$attr['collection-description'] = ucwords(strtolower($collectionDescription));
	                            	}
	                            	
	                            	$attr['collection-code'] = trim($explode);
	                            	break;
	                            }
                            }
                            if(!empty($tag->custom_label_0)){
                            	$alturaSalto = str_replace(',', ' ', "".$tag->custom_label_0);
                            	$alturaSalto = str_replace('  ', ' ', $alturaSalto);
                            	$parts  = explode(' ',  "".$tag->custom_label_0);
                            	$attr['tipo-salto'] = trim($parts[0]);
                            	$attr['altura-do-salto'] = !empty($parts[1]) ? trim($parts[1]) : $alturaSalto ;
                            }
                            
                            if(!empty($attr['tipo-de-calcado'])){
                            	switch(trim($attr['tipo-de-calcado'])){
                            		case 'Peep Toe':
                            			if(trim($attr['tipo-salto']) == 'Alto')
                            			$attr['type-age'] == 'Rêtro';
                            			$attr['type-description'] = "{$attr['tipo-de-calcado']} Rêtro Salto {$attr['tipo-salto']} {$verify['brand']}";
                            			$attr['confort'] = 'Possui tecnologia para aumentar o conforto nos pés';
                            			break;
                            			 
                            	}
                            
                            }
                            
                            if(!empty($tag->custom_label_1)){
                            	$estampa = str_replace(',', ' ', "".$tag->custom_label_1);
                            	$estampa = str_replace('  ', ' ', $estampa);
                            	$parts  = explode(' ', $estampa);
                            	$attr['estampa'] = trim($parts[0]);
                            	 
                            }
                            
                            if(!empty($tag->custom_label_2)){
                            	$modelo = str_replace(',', ' ', "".$tag->custom_label_2);
                            	$modelo = str_replace('  ', ' ', $modelo);
                            	$attr['modelo'] = $modelo;
                            }
                            
                           if(!empty($attr['modelo'])){
                            	$parts  = explode(' ',  $attr['modelo']);
                            	
                            	if(!empty($parts[0])){
                            		foreach($parts as $i => $part){
	                            		switch(trim($part)){
	                            			case 'Conforto': 
	                            				$attr['confort'] = 'Possui tecnologia para aumentar o conforto nos pés';
	                            				$attr['line'] = "{$attr['tipo-de-calcado']} Conforto {$verify['brand']}";
	                            				break;
	                            			case 'Numeração': 
	                            				$attr['especial-size'] = 'Desenvolvido com forma grande'; 
	                            				$attr['line'] = "{$attr['tipo-de-calcado']} Tamanho Grande {$verify['brand']}";
	                            				break;
	                            			case 'Joanetes':
	                            				$attr['joanetes'] = 'Ideal para diminuir a pressão sobre a joanete';
	                            				$attr['line'] = "{$attr['tipo-de-calcado']} Cuidados com Joanetes {$verify['brand']}";
	                            				break;
	                            			
	                            		}
                            		}
                            	
                            	}
                            }
                            
                            
                            if(!empty($tag->custom_label_3)){
                            	$material = str_replace(',', ' ', "".$tag->custom_label_3);
                            	$material = str_replace('  ', ' ', $material);
                            	$outrosMateriais = $material;
                            	$parts  = explode(' ', $material);
                            	$attr['material'] = trim($parts[0]);
                            	if(!empty($parts[1])){
                            		$outrosMateriais  = str_replace(trim($parts[0]), ' ', $outrosMateriais);
                            		$outrosMateriais  = str_replace('  ', ' ', $outrosMateriais);
                            		$attr['outros-materiais'] = trim($outrosMateriais);
                            	}
                            }
                            if(!empty($tag->custom_label_4)){
                            	$estilo = str_replace(',', ' ', "".$tag->custom_label_4);
                            	$estilo = str_replace('  ', ' ', $estilo);
                            	$parts  = explode(' ', $estilo);
                            	$attr['estilo'] = trim($parts[0]);
                            
                            }
                            
                        }
                        if($count == 2){
                        	
                        	if(!empty($tag->custom_label_0)){
                        		$formatoSalto = str_replace(',', ' ', "".$tag->custom_label_0);
                        		$formatoSalto = str_replace('  ', ' ', $formatoSalto);
                        		$attr['formato-do-salto'] = $formatoSalto;
                        	}
                        	
                            $attr['formato-do-bico'] = "".$tag->custom_label_1;
                            $attr['esporte'] = "".$tag->custom_label_2;
                            if(!empty($tag->custom_label_3)){
                            	$alturaCano = str_replace(',', ' ', "".$tag->custom_label_3);
                            	$alturaCano = str_replace('  ', ' ', $alturaCano);
                            	$attr['altura-do-cano'] = $alturaCano;
                            }
                            
                            if(!empty($tag->custom_label_4)){
	                            $detalhes = str_replace(',', ' ', "".$tag->custom_label_4);
	                            $detalhes = str_replace('  ', ' ', $detalhes);
	                            $attr['detalhe'] = $detalhes;
                            }
                            
                        }
                        if($count == 3){
                        	
                        	$attr['chuteiras'] = "".$tag->custom_label_1;
                        	$attr['personagem'] = "".$tag->custom_label_2;
                        	$attr['working'] = "".$tag->custom_label_3;
                        
                        }
                        
                        foreach($attr as $key => $value) {
                        	
                            if(!empty($value)){
                            	$name = trim($key);
                            	if($name == 'collection-description'){
                            		$name = 'Coleção';
                            	}
                            	$name = str_replace('_', ' ', $name);
                            	$name = str_replace('-', ' ', $name);
                            	$name = str_replace('  ', ' ', $name);
                            	$name = strtolower($name);
                            	$name = ucwords($name);
                            	
                                $productsAttr[$parentId][] = array(
                                    "attribute_id" => $key,
                                	"name" => $name,
                                    "value" => $value,
                                	"marketplace" => "Ecommerce"
                                		
                                );
                                
                            }
                            
                        }
                        
                    }
                    
                }  
                
                $count++;
                
            }while ($count <= 3) ;
            pre($productsAttr);die;
            $attributesValuesModel = new AttributesValuesModel($db, null, $storeId);
            
            foreach($productsAttr as $parentId => $attributes){
                pre($attributes);
              	$sqlAP = "SELECT id FROM available_products WHERE store_id = {$storeId} AND  parent_id LIKE '{$parentId}'";
                $queryAP = $db->query($sqlAP);
                $productsIds = $queryAP->fetchAll(PDO::FETCH_ASSOC);
                foreach($productsIds as $key => $id){
                    $attributesValuesModel->product_id = $id['id'];
                    $attributesValuesModel->attributesValues = $attributes;
                    $result = $attributesValuesModel->Save();
    
                }
                
            }
            
            break;
            
    }
    
}

