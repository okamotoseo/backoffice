<?php
class UpdateAvailableProducts{

    /**
     * $db
     *
     * O objeto da nossa conex√£o MYSQL PDO
     *
     * @access public
     */
    public $db;
    
    /**
     * $pg
     *
     * O objeto da nossa conex√£o POSTGRES PDO
     *
     * @access public
     */
    public $pg;
    
    public $store_id;

    public $account_id = 6;
    
    public $store_code;
    
    
    public function __construct($db = false, $pg = false, $storeId)
    {
        
        $this->db = $db;
        
        $this->pg = $pg;
        
        $this->store_id = $storeId;
        
    }
    
    
    
//     public function importProducts(){
        
//         $this->importAvailableProductsTmp();
//         $this->updateAvailableProducts();
//     }
    
    public function getStoreInformation(){
    	
    	$condition = "";
    	
    	$storeInformation = array();
    	
    	if(!empty($this->store_code)){
    		
    		$condition = "AND Codigo LIKE '{$this->store_code}'";
    	}
    	
    	$sql = "SELECT  codigo as code, apelido as store, email as email_sac,
    	email as email_send, telefone1 as phone, nome as company,
    	cpfcnpj as cnpj, endereco as address, cep as postalcode, bairro as neighborhood, cidade as city, uf as state
    	FROM pessoas WHERE filial {$condition} ORDER BY apelido";
    
    	$pgQuery = $this->pg->query($sql);
    	
    	while ($row = $pgQuery->fetch(PDO::FETCH_ASSOC)){
    	  
    		$storeInformation[] = $row;
    	  
    	}
    	
    	return $storeInformation;
    
    }
    
    public function updateAvailableProducts(){
        
       $count = 0;
       
       $dateFrom =  date("Y-m-d H:i:s",  strtotime("-3 hour") );
       
       $sql = "SELECT * FROM module_seta_products_tmp WHERE store_id = {$this->store_id}  AND updated >= '{$dateFrom}'";  
       
       $query = $this->db->query($sql);
       
       if($query){
           
           while($row = $query->fetch(PDO::FETCH_ASSOC)){
               
	           	$variationType = 'tamanho';
	           	$variation = trim(strtoupper($row['size']));
	           	if($variation == 'UN'){
	           		$variationType = 'unidade';
	           	}
	           	
               $sqlVerify = "SELECT id, sku, ean FROM available_products WHERE store_id = {$this->store_id} AND sku LIKE '{$row['sku']}' ";
               $verifyQuery = $this->db->query($sqlVerify);
               $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
               
               if(!isset($verify['id'])){
               		
               		
                   $queryInsert = $this->db->insert('available_products', array(
                   		'account_id' =>  $this->account_id,
                        'store_id' =>  $this->store_id,
                        'sku' =>  trim($row['sku']),
                        'parent_id' =>  trim($row['parent_id']),
                        'title' =>  "".trim($row['title'])."",
                        'color' => trim($row['color']),
                        'variation_type' =>  $variationType,
                        'variation' =>  $variation,
                        'brand' => trim($row['brand']),
                        'reference' => trim($row['reference']),
                        'ean' => trim($row['ean']),
                        'collection' => trim($row['collection']),
                        'category' => trim($row['category']),
                        'quantity' => $row['quantity'] > 0 ? $row['quantity'] : 0,
                        'price' => trim($row['price']),
                        'sale_price' => trim($row['sale_price']),
                        'promotion_price' => trim($row['promotion_price']),
                        'cost' => trim($row['cost']),
                        'created' =>  date("Y-m-d H:i:s"),
                        'updated' =>  date("Y-m-d H:i:s"),
                        )
                    );
                   
                   $count++;
            
               }
               
               if(isset($verify['id'])){
                   
                   if(empty(trim($verify['ean']))){ 
                       $queryUpdate = $this->db->update('available_products', 'id', $verify['id'], array(
//                            'variation_type' =>  $variationType,
                           'quantity' => $row['quantity'] > 0 ? $row['quantity'] : 0,
                           'price' => trim($row['price']),
//                            'ean' =>  trim($row['ean']),
//                        	   'category' => trim($row['category']),
                           'sale_price' => trim($row['sale_price']),
                           'promotion_price' => trim($row['promotion_price']),
                           'cost' => trim($row['cost']),
//                            'collection' => trim($row['collection'])
                           )
                       );
                   }else{
                       $queryUpdate = $this->db->update('available_products', 'id', $verify['id'], array(
//                            'variation_type' =>  $variationType,
                           'quantity' => $row['quantity'] > 0 ? intval($row['quantity']) : 0,
                           'price' => trim($row['price']),
//                        	   'category' => trim($row['category']),
                           'sale_price' => trim($row['sale_price']),
                           'promotion_price' => trim($row['promotion_price']),
                           'cost' => trim($row['cost']),
//                            'collection' => trim($row['collection'])
                       ));
                   }
                   if($queryUpdate->rowCount()){
                       
                       $this->db->update('available_products',
                           array('store_id','id'),
                           array($this->store_id, $verify['id']),
                           array('updated' =>  date("Y-m-d H:i:s"))
                           );
                       $count++;
                   }
                   
               }
               
            }
            echo date('Y-m-d H:i:s');
            return $count;
            
        }
        
        
    }
    
    
    public function importAvailableProductsTmp(){
//     	$sqlGroups = "SELECT * from grupos WHERE desativar = 'f'";
//     	$queryGroups = $this->pg->query($sqlGroups);
//     	$groups = $queryGroups->fetchAll(PDO::FETCH_ASSOC);
//     	pre($groups);die;
        echo date('Y-m-d H:i:s');
      $sql = "SELECT p.codigo, ((p.codigo::text || g.tamanho::text))::character(8) AS variacao, 
                (( btrim(p.descricao::text) || ' '::text))::character(95) AS nome, 
                p.referencia, p.colecao, d.descricao AS departamento, m.descricao AS marca, 
                p.corx AS cor, p.unidade, p.custo, p.preco, p.preco2, 
                ds.promopreco, ds.promodatainicio, ds.promodatafim, p.atualizado, g.legenda, 
                g.tamanho, ve.quantidade, NOT p.desativar , gr.descricao AS grupo
               FROM produtos p
               JOIN departamentos d ON p.departamento = d.codigo
               JOIN grupos gr ON p.grupo = gr.codigo
               JOIN marcas m ON p.marca = m.codigo
               JOIN ( SELECT g.codigo, g.arr1[g.i]::character(2) AS tamanho, 
                g.arr2[g.i]::character(5) AS legenda
               FROM ( SELECT g.codigo, generate_series(1, array_upper(g.arr1, 1)) AS i, 
                        g.arr1, g.arr2
                       FROM ( SELECT grades.codigo, 
                                string_to_array(grades.tamanhos::text, ','::text) AS arr1, 
                                string_to_array(grades.legendas::text, ','::text) AS arr2
                               FROM grades) g) g
              WHERE g.arr2[g.i] <> ''::text) g ON p.grade = g.codigo
               LEFT JOIN ( SELECT p.codigo, 
                round(
                    CASE
                        WHEN pr.tipo = 'D'::bpchar THEN p.preco2 - p.preco2 * d.desconto / 100::numeric
                        WHEN pr.tipo = 'P'::bpchar THEN p.preco2 - d.desconto
                        ELSE p.preco2
                    END, 2) AS promopreco, 
                pr.inicio AS promodatainicio, pr.fim AS promodatafim
               FROM promocoes pr
               JOIN ( SELECT DISTINCT substr(descontos.codigo::text, 1, 4) AS codigo, 
                        descontos.desconto
                       FROM descontos) d ON pr.codigo::text = d.codigo
               JOIN ( SELECT produtos.codigo, produtos.preco, produtos.preco2, 
                   substr(produtos.promocoes::text, 1, 4) AS promocao
                  FROM produtos
                 WHERE produtos.promocoes <> ''::bpchar) p ON pr.codigo::text = p.promocao
              WHERE pr.filtro = 'A'::bpchar AND pr.status = 2::numeric OR pr.status = 3::numeric 
                AND 'now'::text::date >= pr.inicio AND 'now'::text::date <= pr.fim) ds ON p.codigo = ds.codigo
               LEFT JOIN ( SELECT vestoques.produto, 
                sum(vestoques.quantidade) AS quantidade
               FROM vestoques WHERE vestoques.empresa IN ('01', '02', '05', '06', '07', '09', '04','22', '33',  '55', '66')
              GROUP BY vestoques.produto) ve ON (p.codigo::text || g.tamanho::text) = ve.produto::text
              WHERE NOT p.desativar
              ORDER BY ((p.codigo::text || g.tamanho::text))::character(8) DESC";
      		//AND p.preco > '50' AND ve.quantidade > 0
        $query = $this->pg->query($sql);
        $categories = array();
        $categoriesPrint = array();
        $new = $updated = 0;
        $ean = '';
        $parentId = '';
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $collection = isset( $row['colecao']) ? trim($row['colecao']) : '' ;
            $allowCollections = array("65", "66","98", "100","101","104", "107", "109");
            if(in_array($collection, $allowCollections)){
            	
            	$brand = isset( $row['marca']) ? trim($row['marca']) : '' ;
            	$disallowBrands = array("LUPO");
            	if(!in_array($brand, $disallowBrands)){
            	
	            	$departament = isset( $row['departamento']) ? trim($row['departamento']) : '' ;
	            	$disallowDepartaments = array("ALMOXARIFADO", "CHINELOS", "SAO PAULO IMPORTADORAS", "IMPORTACAO", "");
	            	if(!in_array($departament, $disallowDepartaments)){
	            		
	            		$rowGrupo = isset($row['grupo']) ? trim($row['grupo']) : '' ;
	                	$disallowGroups = array("PROMOCAO","LONA C B", "LONA", "SAPATILHA", "CALCADOS MASCULINOS", "CINTOS", "IMPORTACAO", "");
	                    if(!in_array($rowGrupo, $disallowGroups)){
	                    	
	                        if($parentId != trim($row['codigo'])){
	                            $ean = '';
	                        }
	                        
	                        if(empty($ean)){
	                            $sqlBarras = "SELECT * FROM barras WHERE produto LIKE '{$row['codigo']}%'";
	                            $queryBarras = $this->pg->query($sqlBarras);
	                            $resBarra = $queryBarras->fetchAll(PDO::FETCH_ASSOC);
	                            if(!empty($resBarra)){
	                                foreach($resBarra as $k => $barcode){
	                                    if(strlen(trim($barcode['codigo'])) == 13){
	                                        $ean = trim($barcode['codigo']);
	                                    }
	                                    if(empty($ean)){
	                                        if(strlen(trim($barcode['codigo'])) == 12){
	                                            $ean = "0".trim($barcode['codigo']);
	                                        }
	                                    }
	                                    
	                                }
	                            }
	                        }
	                        $group = '';
	                        $parts = array();
	                        if(!empty($rowGrupo)){
	                            $parts = explode(" ", trim($rowGrupo));
	                            switch(trim($parts[0])){
	                                case 'COTURNO' : $parts[0] = 'Coturnos'; break;
	                                case 'CHUTEIRA' : $parts[0] = 'Chuteiras'; break;
	                                case 'TENIS' : $parts[0] = 'Tênis'; break;
	                            }
	                            $group = isset($parts[0]) ? ucfirst(strtolower(trim($parts[0]))) : '' ;
	                        }
	                        $reference = trim($row['referencia']);
	                        $variacao = trim($row['variacao']);
	                        $parentId = trim($row['codigo']);
	                        $title = trim($row['nome']);
	                        $color = trim($row['cor']);
	                        $size = trim($row['legenda']);
	                        $brand =trim($row['marca']);
	                        
	                        $category = !empty($departament) ? ucwords(strtolower($departament)) : '' ;
	                        if(!empty($category)){
	                        	$category = !empty($group) ? $category." > ".ucwords(strtolower($group)) : $category ;
	                        }else{
	                        	$category = !empty($group) ? ucwords(strtolower($group)) : '' ;
	                        }
	                        
	                        $cost = trim($row['custo']);
	                        $price = trim($row['preco']);
	                        $salePrice = trim($row['preco']);
	                        $promotionPrice = 0;
	                        $quantity = $row['quantidade'] > 0 ? $row['quantidade'] : 0 ;
	                        
	                        if(!isset($categories[$category])){
	                            $categories[$category]['total'] = 1;
	                            
	                            if(!isset($categories[$category][$rowGrupo])){
	                                $categories[$category][$rowGrupo] = 1;
	                            }else{
	                                $categories[$category][$rowGrupo] += 1;
	                            }
	                            
	                        }else{
	                            
	                            if(!isset($categories[$category][$rowGrupo])){
	                                $categories[$category][$rowGrupo] = 1;
	                            }else{
	                                $categories[$category][$rowGrupo] += 1;
	                            }
	                            
	                            $categories[$category]['total'] += 1;
	                        }
	                        
	                        $categoryUpdate = true;
	                        
	                        switch ($collection){
	                        	case '000065': $collection = 'I8'; break;
	                        	case '000066': $collection = 'V8'; break;
	                        	case '000098': $collection = 'I9'; break;
	                        	case '000100': $collection = 'CLASSICOS'; break;
	                        	case '000101': $collection = 'V9'; break;
	                        	case '000104': $collection = 'I20'; break;
	                        	case '000107': $collection = 'V20'; break;
	                        	case '000109': $collection = 'I21'; break;
	                        }
	                      
	                        $category = !empty($row['information']) && ucwords(strtolower($row['information'])) != ucwords(strtolower($group)) ? $category ." > ". ucwords(strtolower($row['information'])) : $category ;
	                        
	                        switch(trim($row['grupo'])){
	                        	case 'TENIS FEMININO': $category = "Femininos > Tênis"; break;
	                        	case 'TENIS MASCULINO': $category = "Masculinos > Tênis"; break;
	                        		 
	                        }
	                       
	                        switch($category){
	                        	
	                        	case 'Infantil > Chuteiras': $category = "Infantil Menino > Chuteiras"; break;
	                        	
	                        	case 'Tenis > Chuteiras': $category = "Masculinos > Chuteiras"; break;
	                        	
	                        	case 'Infantil > Masc': 
	                        		
	                        		if(!empty($parts[1])){
	                        			$sub = ucwords(mb_strtolower(trim($parts[1]), 'UTF-8'));
	                        			$category = "Infantil Menino > {$sub}";
	                        		}else{
	                        			$category = "Infantil Menino";
	                        			$categoryUpdate = false;
	                        		}
	                        		break;
	                        		
	                        	case 'Infantil > Fem': 
	                        		
	                        		if(!empty($parts[1])){
	                        			$sub = ucwords(mb_strtolower(trim($parts[1]), 'UTF-8'));
	                        			$category = "Infantil Menina > {$sub}";
	                        		}else{
	                        			$category = "Infantil Menina";
	                        			$categoryUpdate = false;
	                        		}
	                        		break;
	                        		
	                        	case 'Infantil > Geral': $categoryUpdate = false; break;
	                        	
	                        	case 'Feminino > CalÇados': 
	                        		
	                        		$partsName = explode(' ', $row['nome']);
	                        		$name = trim($partsName[0]);
	                        		if($name != trim($row['marca'])){
	                        			$sub = ucwords(mb_strtolower($name, 'UTF-8'));
	                        			$category = "Femininos > {$sub}";
	                        		}else{
	                        			$category = "Femininos";
	                        			$categoryUpdate = false;
	                        		}
	                        		break;
	                        }
	                        
	                        $category = str_replace('Feminino', 'Femininos', $category);
	                        $category = str_replace('Masculino', 'Masculinos', $category);
	                        $category = str_replace('ss ', 's ', $category);
	                        $category = str_replace('Botinas/coturnos', 'Botinas', $category);
	                        $category = str_replace('Babuche/crocs', 'Clogs', $category);
	                        $category = str_replace('Cart ', 'Carteiras', $category);
	                        
	                        
	                        if(!isset($categoriesPrint[$category])){
	                        	$categoriesPrint[$category] = $category;
	                        }
	                        
	                        
	                        $sqlVerify = "SELECT sku, parent_id FROM module_seta_products_tmp WHERE store_id = {$this->store_id} AND sku LIKE '{$variacao}' ";
	                        $verifyQuery = $this->db->query($sqlVerify);
	                        $verify = $verifyQuery->fetch(PDO::FETCH_ASSOC);
	                        if(!isset($verify['sku'])){
	                        	
	                        	if($row['legenda'] == 'UN' && $row['quantidade'] < 1){
	                        		break;
	                        	}
	                        
	                        	$data = array(
	                                'store_id' => $this->store_id,
	                                'sku' => $variacao,
	                                'parent_id' => $parentId,
	                                'title' => $title,
	                                'color' => $color,
	                                'size' => $size,
	                                'brand' => $brand,
	                                'reference' => $reference,
	                                'ean' => $ean,
	                                'collection' => $collection,
	                                'category' => $category,
	                                'quantity' => $quantity,
	                                'price' => $price,
	                                'sale_price' => $salePrice,
	                                'promotion_price' => $promotionPrice,
	                                'cost' => $cost,
	                                'information' => $rowGrupo,
	                                'updated' => date('Y-m-d H:i:s')
	                                );
// 	                        	pre($data);
	                            $queryInsert = $this->db->insert('module_seta_products_tmp', $data);
	                            if($queryInsert){
	                                $new++;
	                            }
	                            
	                        }
	                        if(isset($verify['sku'])){
	                        	
	                        	$data = array('parent_id' => $parentId,
	                                'title' => $title,
	                                'color' => $color,
	                                'size' => $size,
	                                'brand' => $brand,
	                                'reference' => $reference,
	                                'ean' => $ean,
	                                'collection' => $collection,
	                                'category' => $category,
	                                'quantity' => $quantity,
	                                'price' => $price,
	                                'sale_price' => $salePrice,
	                                'promotion_price' => $promotionPrice,
	                                'cost' => $cost,
	                                'information' => $rowGrupo
	                            );
	                            $queryUpdate = $this->db->update('module_seta_products_tmp', 
	                                array('store_id', 'sku'), 
	                                array($this->store_id, $verify['sku']), $data);
	                            
	                            if($queryUpdate->rowCount()){
	                                
	                                $this->db->update('module_seta_products_tmp',
	                                    array('store_id','sku'),
	                                    array($this->store_id, $verify['sku']),
	                                    array('updated' =>  date("Y-m-d H:i:s"))
	                                    );
	                                $updated++;
	                            }
	                        }
                    	}
            		}
            	}
            }
        }
        pre($categoriesPrint);
        $count = $new + $updated;
        
        echo "Novos {$new} + Atualizados {$updated} = {$count}";
        
        return $count;
        
        
    }
    
   
}

// Array
// (
// 		[Masculinos > Tênis] => Masculinos > Tênis
// 		[Masculinos > Sapatenis] => Masculinos > Sapatenis
// 		[Femininos > Tamanco] => Femininos > Tamanco
// 		[Infantil Menina > Sapatilha] => Infantil Menina > Sapatilha
// 		[Infantil Menina > Sandalia] => Infantil Menina > Sandalia
// 		[Femininos > Tamancos] => Femininos > Tamancos
// 		[Femininos > Tênis] => Femininos > Tênis
// 		[Femininos > Sandalia] => Femininos > Sandalia
// 		[Femininos > Sandalias] => Femininos > Sandalias
// 		[Femininos > Sapatilhas] => Femininos > Sapatilhas
// 		[Infantil Menina > Coturno] => Infantil Menina > Coturno
// 		[Infantil Menino > Tenis] => Infantil Menino > Tenis
// 		[Acessorios > Bolsa] => Acessorios > Bolsa
// 		[Masculinos > Chuteiras] => Masculinos > Chuteiras
// 		[Infantil Menina > Tenis] => Infantil Menina > Tenis
// 		[Acessorios > Carteira] => Acessorios > Carteira
// 		[Acessorios > Bolsas] => Acessorios > Bolsas
// 		[Acessorios > Mochila] => Acessorios > Mochila
// 		[Acessorios > Pochete] => Acessorios > Pochete
// 		[Infantil Menina > Tamancos] => Infantil Menina > Tamancos
// 		[Infantil Menino > Sandalia] => Infantil Menino > Sandalia
// 		[Femininos > Sapatos] => Femininos > Sapatos
// 		[Masculinos > Coturnos] => Masculinos > Coturnos
// 		[Acessorios > Malas] => Acessorios > Malas
// 		[Acessorios > Suporte] => Acessorios > Suporte
// 		[Acessorios > Rede] => Acessorios > Rede
// 		[Acessorios > Mesa] => Acessorios > Mesa
// 		[Infantil Menino > Chuteiras] => Infantil Menino > Chuteiras
// 		[Acessorios > Bijuteria] => Acessorios > Bijuteria
// 		[Infantil > Babuche/crocs] => Infantil > Babuche/crocs
// 		[Infantil Menino > Sapatenis] => Infantil Menino > Sapatenis
// 		[Infantil Menina > Rasteiras] => Infantil Menina > Rasteiras
// 		[Masculinos > Sandalias] => Masculinos > Sandalias
// 		[Femininos > Mule] => Femininos > Mule
// 		[Acessorios > Luva] => Acessorios > Luva
// 		[Acessorios > Meias] => Acessorios > Meias
// 		[Acessorios > Pasta] => Acessorios > Pasta
// 		[Acessorios > Sacola] => Acessorios > Sacola
// 		[Acessorios > Estojo] => Acessorios > Estojo
// 		[Infantil > Geral] => Infantil > Geral
// 		[Femininos > Tenis] => Femininos > Tenis
// 		[Femininos > Chinelo] => Femininos > Chinelo
// 		[Acessorios > Acessorios] => Acessorios > Acessorios
// 		[Femininos > Clog] => Femininos > Clog
// 		[Femininos > Sapatilha] => Femininos > Sapatilha
// 		[Femininos > Crocband] => Femininos > Crocband
// 		[Femininos > Sapato] => Femininos > Sapato
// 		[Femininos > Alpargata] => Femininos > Alpargata
// 		[Femininos > Retro] => Femininos > Retro
// 		[Femininos > Bota] => Femininos > Bota
// 		[Femininos > Sapatenis] => Femininos > Sapatenis
// 		[Femininos > Scarpin] => Femininos > Scarpin
// 		[Femininos > Chanel] => Femininos > Chanel
// 		[Femininos > Rasteira] => Femininos > Rasteira
// 		[Femininos > Mocassim] => Femininos > Mocassim
// 		[Femininos > Wmns] => Femininos > Wmns
// 		[Femininos > Papete] => Femininos > Papete
// 		[Femininos > Tmanco] => Femininos > Tmanco
// 		[Femininos > Coturno] => Femininos > Coturno
// 		[Femininos > Sand.] => Femininos > Sand.
// 		[Femininos > Birken] => Femininos > Birken
// 		[Femininos > Pantufa] => Femininos > Pantufa
// 		[Femininos > Sand] => Femininos > Sand
// 		[Femininos > Sandália] => Femininos > Sandália
// 		[Femininos > Meia] => Femininos > Meia
// 		[Acessorios > Necessaire] => Acessorios > Necessaire
// 		[Masculinos > Sapatos] => Masculinos > Sapatos
// 		[Masculinos > Chinelos] => Masculinos > Chinelos
// 		[Acessorios > Tapete] => Acessorios > Tapete
// 		[Acessorios > Fita] => Acessorios > Fita
// 		[Acessorios > Carteiras] => Acessorios > Carteiras
// 		[Tenis > Tênis] => Tenis > Tênis
// 		[Acessorios > Oculos] => Acessorios > Oculos
// 		[Acessorios > Bone] => Acessorios > Bone
// 		[Acessorios > Bola] => Acessorios > Bola
// 		[Acessorios > Cinto] => Acessorios > Cinto
// 		[Acessorios > Joelheiras] => Acessorios > Joelheiras
// 		[Acessorios > Porta] => Acessorios > Porta
// 		[Masculinos > Botinas] => Masculinos > Botinas
// 		[Acessorios > Touca] => Acessorios > Touca
// 		[Acessorios > Bracadeira] => Acessorios > Bracadeira
// 		[Acessorios > Raquete] => Acessorios > Raquete
// 		[Acessorios > Kit] => Acessorios > Kit
// 		[Acessorios > Toalha] => Acessorios > Toalha
// 		[Acessorios > Squeeze] => Acessorios > Squeeze
// 		[Acessorios > Caneleiras] => Acessorios > Caneleiras
// 		[Femininos > Botas] => Femininos > Botas
// 		[Infantil Menino > Botinas] => Infantil Menino > Botinas
// 		[Acessorios > Tamancos] => Acessorios > Tamancos
// 		[Infantil Menina > Botas] => Infantil Menina > Botas
// 		[Femininoss] => Femininoss
// 		[Acessorios > Chaveiro] => Acessorios > Chaveiro
// 		[Acessorios > Calcanheira] => Acessorios > Calcanheira
// 		[Acessorios > Protetor] => Acessorios > Protetor
// 		[Acessorios > Desodorante] => Acessorios > Desodorante
// 		[Acessorios > Meiao] => Acessorios > Meiao
// 		[Acessorios > Cart] => Acessorios > Cart
// 		[Acessorios > Cadarco] => Acessorios > Cadarco
// 		[Acessorios > Bandagem] => Acessorios > Bandagem
// 		[Acessorios > Coxal] => Acessorios > Coxal
// 		[Acessorios > Cotoveleira] => Acessorios > Cotoveleira
// 		[Acessorios > Tornozeleira] => Acessorios > Tornozeleira
// 		[Acessorios > Faixa] => Acessorios > Faixa
// 		[Acessorios > Pe] => Acessorios > Pe
// 		[Acessorios > Narizeira] => Acessorios > Narizeira
// 		[Acessorios > Anti-embacante] => Acessorios > Anti-embacante
// 		[Acessorios > Boia] => Acessorios > Boia
// 		[Acessorios > Palmilhas] => Acessorios > Palmilhas
// 		[Acessorios > Limpador] => Acessorios > Limpador
// 		[Acessorios > Amaciante] => Acessorios > Amaciante
// 		[Acessorios > Renovador] => Acessorios > Renovador
// 		[Acessorios > Impermeabilizante] => Acessorios > Impermeabilizante
// 		[Acessorios > Anti-derrapante] => Acessorios > Anti-derrapante
// 		[Acessorios > Apoio] => Acessorios > Apoio
// 		[Acessorios > Bomba] => Acessorios > Bomba
// 		[Acessorios > Frasqueiras] => Acessorios > Frasqueiras
// 		[Infantil Menino > Sapatos] => Infantil Menino > Sapatos
// 		[Acessorios > Maquiagem] => Acessorios > Maquiagem
// 		[Acessorios > Balanca] => Acessorios > Balanca
// 		[Acessorios > Lancheira] => Acessorios > Lancheira
// 		[Acessorios > Antivibrador] => Acessorios > Antivibrador
// 		[Acessorios > Cordao] => Acessorios > Cordao
// 		[Acessorios > Viseira] => Acessorios > Viseira
// 		[Acessorios > Esc] => Acessorios > Esc
// 		[Acessorios > Case] => Acessorios > Case
// 		[Acessorios > Cartao] => Acessorios > Cartao
// 		[Acessorios > Apito] => Acessorios > Apito
// 		[Acessorios > Agulha] => Acessorios > Agulha
// 		[Acessorios > Manopla] => Acessorios > Manopla
// 		[Acessorios > Saco] => Acessorios > Saco
// 		[Acessorios > Munhequeira] => Acessorios > Munhequeira
// 		[Acessorios > Gorro] => Acessorios > Gorro
// 		[Acessorios > Manga] => Acessorios > Manga
// 		[Acessorios > Corda] => Acessorios > Corda
// 		[Acessorios > Calibrador] => Acessorios > Calibrador
// 		[Femininos > Via] => Femininos > Via
// 		[Infantil Menina > Mule] => Infantil Menina > Mule
// 		[Infantil > Tênis] => Infantil > Tênis
// 		[Femininos > Gata] => Femininos > Gata
// 		[Femininos > Gatamania] => Femininos > Gatamania
// 		[Femininos > San] => Femininos > San
// 		[Acessorios > ImportaÇÃo] => Acessorios > ImportaÇÃo
// 		[Femininos > Loucos] => Femininos > Loucos
// 		[Femininos > Jorge] => Femininos > Jorge
// 		[Femininos > Cravo] => Femininos > Cravo
// 		[Femininos > Gabriela] => Femininos > Gabriela
// 		[Femininos > Oliveira] => Femininos > Oliveira
// 		[Femininos > Comfortflex] => Femininos > Comfortflex
// 		[Femininos > Lia] => Femininos > Lia
// 		[Acessorios > Material] => Acessorios > Material
// 		)
// 		Novos 0 + Atualizados 0 = 0
?>