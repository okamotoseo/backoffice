<?php
/**
 * Modelo para gerenciar produtos
 *
 * @package
 * @since 0.1
 */
class AvailableProductsModel extends MainModel
{
    
    public $id;
    
    public $account_id;
    
    public $store_id;
    
    public $sku;
    
    public $parent_id;
    
    public $title;
    
    public $color;
    
    public $variation_type;
    
    public $variation;
    
    public $brand;
    
    public $reference;
    
    public $collection;
    
    public $category;
    
    public $quantity = 0;
    
    public $price;
    
    public $sale_price;
    
    public $promotion_price;
    
    public $cost;
    
    public $weight;
    
    public $height;
    
    public $width;
    
    public $length;
    
    public $ean;
    
    public $ncm;
    
    public $description;
    
    public $updated;
    
    public $xml;
    
    public $flag;
    
    public $blocked;
    
    public $marketplace;
    
    public $stock;
    
    public $thumbnail;
    
    public $group_by;
    
    public $order_by;
    
    public $records = 50;
    
    public $request;
    
    public $codes;
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            $this->parametros = $this->controller->parametros;
            $this->userdata = $this->controller->userdata;
            $this->store_id = $this->controller->userdata['store_id'];
            $this->account_id = $this->controller->userdata['account_id'];
            $this->request = $this->controller->userdata['name'];
            $this->moduledata = $this->controller->moduledata;
        }
        
        if(!defined('QTDE_REGISTROS')){
            
            define('QTDE_REGISTROS', 50);
            
        }
        
        
    }
    
    public function ValidateForm() {
        $this->records = isset($_POST['records']) ? $_POST['records'] : QTDE_REGISTROS ;
        if(in_array('records', $this->parametros )){
            $records = get_next($this->parametros, array_search('records', $this->parametros));
            $this->records = isset($records) ? $records : QTDE_REGISTROS ;
        }
        if(in_array('Page', $this->parametros )){
            $this->pagina_atual =  get_next($this->parametros, array_search('Page', $this->parametros));
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
            foreach($this->parametros as $key => $param){
                if(property_exists($this,$param)){
                    $val = get_next($this->parametros, $key);
                    $val = str_replace("_x_", "%", $val);
                    $this->{$param} = $val;
                }
            }
            return true;
        }else{
            $this->pagina_atual = 1;
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
        }
        if(isset ( $_POST['available-products'] ) OR isset ( $_POST['available-products-filter'] )){
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                    	switch($property){
                    		case 'sku': $value = trim($value); break;
                    		case 'parent_id': $value = trim($value); break;
                    		case 'variation': $value = trim($value); break;
                    		case 'brand': $value = trim($value); break;
                    		case 'color': $value = trim($value); break;
                    		case 'category': $value = trim($value); break;
                    		case 'weight': $value = validateKg($value); break;
                    		case 'width': $value = validateCm($value); break;
                    		case 'height': $value = validateCm($value); break;
                    		case 'length': $value = validateCm($value); break;
                    		case 'sale_price': $value = validatePrice($value); break;
                    	}
                        $this->{$property} = $value;
                    }
                }else{
                    if( isset ( $_POST['available-products'] ) ){
                        $required = array(
                            'account_id',
                            'store_id',
                            'sku',
                            'parent_id',
                            'title',
                            'brand',
                            'category',
                            'price',
                            'sale_price',
                            'description',
                            'weight');
                        if( in_array($property, $required) ){
                            $this->field_error[$property] = "has-error";
                            $this->form_msg = "<div class='alert alert-danger alert-dismissable'>There are empty field. Data has not been sent.</div>";
                        }
                    }
                }
            }
            if(!empty($this->form_msg)){
                return false;
            }
            
            return true;
           
           
        }else{
            if ( in_array('Product', $this->parametros )) {
                $key = array_search('Product', $this->parametros);
                $productId = get_next($this->parametros, $key);
                $this->id  = is_numeric($productId) ? $productId :  '';
                if(!empty($this->id)){
                    $this->Load();
                }
            }
            
            return;
        }
        
        
        
    }
    
    public function Save(){
        
        if ( ! empty( $this->id ) ) {
        	
        	$query = $this->db->query('SELECT * FROM available_products WHERE `id`= ? AND store_id = ?',
        			array(  $this->id, $this->store_id ) );
        	$verify = $query->fetch(PDO::FETCH_ASSOC);
        	if(isset($verify['sku'])){
        	
	        	$data = array(
	                'sku' => $this->sku,
	                'parent_id' => $this->parent_id,
	                'title' => $this->title,
	                'color' => $this->color,
	                'variation_type' => $this->variation_type,
	                'variation' => $this->variation,
	                'brand' => $this->brand,
	                'reference' => $this->reference,
	                'category' => $this->category,
	                'quantity' => $this->quantity,
	                'price' => $this->price,
	                'sale_price' => $this->sale_price,
	                'ncm' => $this->ncm,
	                'cost' => $this->cost,
	                'weight' => $this->weight,
	                'height' => $this->height,
	                'width' => $this->width,
	                'length' => $this->length,
	                'ean' => $this->ean,
	        	    'blocked' => isset($this->blocked) && !empty($this->blocked) ? $this->blocked : 'F',
	                'description' => $this->description
	            );
	            $query = $this->db->update('available_products', 'id', $this->id, $data);
	            
	            if ( ! $query ) {
	                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	                $this->logSystem($this->db, $this->userdata['id'], $this->id, 'error', 'Products', 'Product', 'Update', $this->form_msg);
	                return;
	            } else {
	                
	                if($query->rowCount()){
	                    
	                    $this->db->update('available_products',
	                        array('store_id','id'),
	                        array($this->store_id, $this->id),
	                        array('flag' => 1, 'updated' => date("Y-m-d H:i:s"))
	                        );
	                    $this->db->update('ml_products',
	                        array('store_id','sku'),
	                        array($this->store_id, $this->sku),
	                        array('flag' => 1)
	                        );
	                    
	                    unset($data['description']);
	                    unset($verify['description']);
	                    $dataLog['update_available_products'] = array(
	                    		'before' => $verify,
	                    		'after' => $data
	                    );
	                    $this->db->insert('products_log', array(
	                    		'store_id' => $this->store_id,
	                    		'product_id' => $this->id,
	                    		'description' => "Atualização De Informação do Produto #Id {$this->id}",
	                    		'user' => $this->request,
	                    		'created' => date('Y-m-d H:i:s'),
	                    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
	                    ));
	                    
	                }
	                
	                $this->form_msg = '<div class="alert alert-success alert-dismissable">Produto atualizado com sucesso.</div>';
	                $this->logSystem($this->db, $this->userdata['id'],  $this->id, 'success', 'Products', 'Product', 'Update', $this->form_msg);
	                return;
	            }
            
        	}
        	
        } else {
            
            $query = $this->db->query('SELECT sku FROM available_products WHERE `sku`= ? AND store_id = ?',
                array(  $this->sku, $this->store_id ) );
            $verify = $query->fetch(PDO::FETCH_ASSOC);
            if(!isset($verify['sku'])){
            	
            	$data = array(
                    'account_id' => $this->account_id,
                    'store_id' => $this->store_id,
                    'sku' => $this->sku,
                    'parent_id' => $this->parent_id,
                    'title' => friendlyText($this->title),
                    'color' => $this->color,
                    'variation_type' => $this->variation_type,
                    'variation' => $this->variation,
                    'brand' => $this->brand,
                    'reference' => $this->reference,
                    'category' => $this->category,
                    'quantity' => $this->quantity,
                    'price' => $this->price,
                    'sale_price' => $this->sale_price,
                    'ncm' => $this->ncm,
                    'cost' => $this->cost,
                    'weight' => $this->weight,
                    'height' => $this->height,
                    'width' => $this->width,
                    'length' => $this->length,
                    'ean' => $this->ean,
                    'created' =>  date("Y-m-d H:i:s"),
                    'updated' =>  date("Y-m-d H:i:s"),
                    'description' => $this->description,
            	    'blocked' => isset($this->blocked) && !empty($this->blocked) ? $this->blocked : 'F' 
                );
                $query = $this->db->insert('available_products', $data);
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    $this->logSystem($this->db, $this->userdata['id'], $this->id, 'error', 'Products', 'Product', 'Insert', $this->form_msg);
                    return;
                } else {
                    $this->id = $this->db->last_id;
                    $this->form_msg = '<div class="alert alert-success alert-dismissable">Produto cadastrado com sucesso.</div>';
                   	unset($data['description']);
                    $dataLog['insert_available_products'] = $data;
                    
                    $this->db->insert('products_log', array(
                    		'store_id' => $this->store_id,
                    		'product_id' => $this->id,
                    		'description' => "Novo Produto Cadastrado Sysplace #Id {$this->id}",
                    		'user' => $this->request,
                    		'created' => date('Y-m-d H:i:s'),
                    		'json_response' => json_encode($dataLog, JSON_PRETTY_PRINT),
                    ));
                    
                    return;
                }
                
                
            }else{
                
                $this->field_error['sku'] = "has-error";
                
                $this->form_msg = "<div class='alert alert-danger alert-dismissable'>Já existe um produto com o mesmo SKU: {$this->sku}</div>";
                
                return;
            }
            
        }
        
    }
    
    
    public function GetAvailableProductsFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "available_products.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "available_products.{$key} = ".intval($this->$key)." AND ";break;
                    case 'sku': $where_fields .= "available_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
                    case 'parent_id': $where_fields .= "available_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
                    case 'reference': $where_fields .= "available_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'collection': $where_fields .= "available_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'title': $where_fields .= "available_products.{$key} LIKE UPPER('".trim($this->$key)."') AND ";break;
                    case 'category': 
                        if($this->$key == 'uncategorized'){
                            $where_fields .= "(available_products.{$key} LIKE '' OR available_products.{$key} IS NULL)  AND ";
                        }else{
                            $where_fields .= "available_products.{$key} LIKE '{$this->$key}%' AND ";
                        }
                        break;
                    case 'brand': $where_fields .= "available_products.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'ean': $where_fields .= "available_products.{$key} LIKE '".trim($this->$key)."' AND ";break;
                    case 'blocked': 
                        if(strtoupper(trim($this->$key)) == 'T'){
                            $where_fields .= "available_products.{$key} like 'T' AND ";
                        }else{
                            $where_fields .= "available_products.{$key}  != 'T' AND "; 
                        }
                        break;
                    case 'stock': 
                        if($this->$key == 'withStock'){
                            $where_fields .= "available_products.quantity > 0 AND ";
                        }else{
                            $where_fields .= "available_products.quantity <= 0 AND ";
                        }
                        break;
                    case 'thumbnail':
                       	if($this->$key == 'withImage'){
                       		$where_fields .= "available_products.thumbnail != '' AND ";
                       	}else{
                       		$where_fields .= "available_products.thumbnail IS NULL AND ";
                       	}
                       	break;
                }
            }
            
        }
        
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
   
    
    public function TotalAvailableProducts(){
        
        $sql = "SELECT count(*) as total FROM available_products WHERE store_id = {$this->store_id}";
        
        $query = $this->db->query( $sql);
        $total =  $query->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
        
    }
    
    
    public function TotalGetAvailableProductsMarketplace(){
        
        
        $sql = $this->getSqlProductMarketplaces();
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->rowCount();
        
    }
   
    public function TotalAvailableProductsDescription(){
        
        $where_fields = $this->GetAvailableProductsFilter();
        
        $sql = "SELECT available_products.id,
        		product_descriptions.set_attribute_id,
        		product_descriptions.description
        		FROM available_products
        		RIGHT JOIN product_descriptions ON available_products.id = product_descriptions.product_id
                AND available_products.store_id = product_descriptions.store_id
        		WHERE {$where_fields}
                GROUP BY available_products.parent_id";
        
        $query = $this->db->query( $sql);
        if ( ! $query ) {
            return array();
        }
        return $query->rowCount();
        
    }
    
    /**************************************************************************************************/
    /**************************** List Available Products Default *************************************/
    /**************************************************************************************************/
    
    /**
     * Lista produtos deisponiveis
     */
    public function ListAvailableProducts()
    {
        $query = $this->db->query("
        		SELECT available_products.*,
        		product_descriptions.set_attribute_id,
        		product_descriptions.description
        		FROM available_products
        		LEFT JOIN product_descriptions ON available_products.id = product_descriptions.product_id
                AND product_descriptions.marketplace = 'default'
        		WHERE  available_products.variation != ''
                AND available_products.store_id= ?
        		ORDER BY available_products.parent_id DESC
                LIMIT {$this->linha_inicial}, {$this->records}",
                array( $this->store_id)
        );
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    /**
     * Filtra produtos deisponiveis
     */
    public function GetAvailableProducts()
    {
        $where_fields = $this->GetAvailableProductsFilter();
        
        
        $sql = "SELECT  available_products.*,
        		product_descriptions.set_attribute_id,
        		product_descriptions.description
        		FROM available_products
        		LEFT JOIN product_descriptions ON available_products.id = product_descriptions.product_id
                AND product_descriptions.marketplace = 'default'
        		WHERE {$where_fields}
        		ORDER BY available_products.parent_id DESC
                LIMIT {$this->linha_inicial}, " . $this->records.";";
        
//         pre($sql);die;
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    public function GetGroupBy(){
        
        $groupBy = '';
        
        if(empty($this->group_by)){
            return $groupBy;
        }
        
        switch($this->group_by){
            case "parent_id": $groupBy = " GROUP BY available_products.parent_id "; break;
            case "reference": $groupBy = " GROUP BY available_products.reference "; break;
            case "color": $groupBy = " GROUP BY available_products.color "; break;
            case "brand": $groupBy = " GROUP BY available_products.brand "; break;
            case "ean": $groupBy = " GROUP BY available_products.ean "; break;
            default: $groupBy = ""; break;
            
        }
        
        return $groupBy;
        
        
    }
    
    function getSqlProductMarketplaces(){
        
        $where_fields = $this->GetAvailableProductsFilter();
        $group_by = $this->GetGroupBy();
        
        switch($this->marketplace){
            case "ecommerce": $marketplace = strtolower($this->moduledata['Ecommerce'][0]); break;
            case "not_published_ecommerce": $marketplace = strtolower("not_published_{$this->moduledata['Ecommerce'][0]}"); break;
            default : $marketplace = strtolower($this->marketplace);
        }
        switch($marketplace){
            case "all":
                
              $sql = "SELECT available_products.*
                		FROM available_products
                		WHERE {$where_fields} {$group_by}
                		ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                
                
                break;
                
            case "mercadolivre":
                $sql = "SELECT available_products.*,
                        ml_products.id as ml_product_id
                		FROM available_products
                        RIGHT JOIN ml_products ON available_products.sku = ml_products.sku
                        AND available_products.store_id = ml_products.store_id
                		WHERE {$where_fields} 
                		{$group_by}
                		ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                		//para verificar se não é kit
                		//AND available_products.id NOT IN (SELECT product_id FROM product_relational WHERE store_id = {$this->store_id})
                break;
                
            case "onbi":
                $sql = "SELECT available_products.*,
                        ml_products.id as ml_product_id,
                        module_onbi_products_tmp.product_id as onbi_ecommerce_id
                		FROM available_products
                        LEFT JOIN ml_products ON available_products.sku = ml_products.sku
                        RIGHT JOIN module_onbi_products_tmp ON available_products.sku = module_onbi_products_tmp.sku
                		WHERE {$where_fields} {$group_by}
                		ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                break;
               case "skyhub":
                	$sql = "SELECT available_products.*, module_skyhub_products.product_id as skyhub_id
                	FROM available_products
                	RIGHT JOIN module_skyhub_products ON available_products.sku = module_skyhub_products.sku
                	WHERE {$where_fields} {$group_by}
                	ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
               	break;
            case "tray":
                
               $sql = "SELECT available_products.*,
                        module_tray_products.id_product as tray_ecommerce_id
                		FROM available_products
                        RIGHT JOIN module_tray_products ON available_products.parent_id = module_tray_products.parent_id
                		WHERE {$where_fields} {$group_by}
                		ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                break;
            case "not_published_tray":
                $sql = "SELECT available_products.*
                		FROM available_products
                		WHERE {$where_fields} AND available_products.parent_id NOT IN (
                                SELECT parent_id FROM module_tray_products 
                                            WHERE store_id = {$this->store_id}
                                            )
                        AND available_products.id NOT IN (SELECT product_id FROM product_relational WHERE store_id = {$this->store_id})
                		{$group_by}
                        ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                break;
                
			case "mg2":
                
                	$sql = "SELECT available_products.* FROM available_products
                	RIGHT JOIN mg2_products_tmp ON available_products.sku = mg2_products_tmp.sku
                	WHERE {$where_fields} {$group_by}
                	ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                	
                	break;
                	
           	case "not_published_mg2":
           		
                	$sql = "SELECT available_products.* FROM available_products
                	WHERE {$where_fields} AND available_products.sku NOT IN (
                		SELECT sku FROM mg2_products_tmp WHERE store_id = {$this->store_id}
                	)
                	{$group_by}
                	ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                	
                	break;
                
            case "not_published":
                $sql = "SELECT available_products.*
                		FROM available_products
                		WHERE {$where_fields} AND available_products.sku NOT IN (SELECT sku FROM ml_products WHERE store_id = {$this->store_id})
                        AND available_products.sku NOT IN (SELECT sku FROM module_onbi_products_tmp WHERE store_id = {$this->store_id})
                		{$group_by} ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                break;
                
            case "not_published_meli":
                $sql = "SELECT available_products.*
                		FROM available_products
                		WHERE {$where_fields} AND available_products.sku NOT IN (SELECT sku FROM ml_products WHERE store_id = {$this->store_id})
                		{$group_by} ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                break;
                
            case "not_published_onbi":
                $sql = "SELECT available_products.*
                		FROM available_products
                		WHERE {$where_fields} AND available_products.sku NOT IN (SELECT sku FROM module_onbi_products_tmp WHERE store_id = {$this->store_id})
                		{$group_by} ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                break;
            case "not_published_skyhub":
                	$sql = "SELECT available_products.* FROM available_products
                	WHERE {$where_fields}  {$group_by} 
                	ORDER BY available_products.parent_id DESC, available_products.quantity DESC";
                	//AND available_products.parent_id NOT IN (SELECT parent_id FROM module_skyhub_products WHERE store_id = {$this->store_id})
                break;
            default: 
                $sql = "SELECT available_products.*
                        FROM available_products
                		WHERE {$where_fields} {$group_by}
                		ORDER BY available_products.parent_id DESC";
                
                break;
                
                
        }
//         pre($sql);die;
        return $sql;
        
        
        
    }
    
    /**************************************************************************************************/
    /********************************* List With Marketplaces *****************************************/
    /**************************************************************************************************/
    
    
    /**
     * Lista produtos com id publicado marketplace
     */
    public function ListAvailableProductsMarketplaces()
    {
        $sql = "SELECT available_products.* FROM available_products
        		WHERE available_products.store_id = ? 
                    AND available_products.thumbnail != '' 
                    AND available_products.blocked != 'T' 
                ORDER BY available_products.id DESC
                LIMIT {$this->linha_inicial}, {$this->records}";
        
        $query = $this->db->query($sql,
                array( $this->store_id)
        );
        if ( ! $query ) {
            return array();
        }
        
        $products =  $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $k => $product){
        	
        	$sqlMkt = "SELECT count(marketplace) as qtd, marketplace, url FROM publications 
        	WHERE store_id = ? AND sku LIKE ? GROUP BY marketplace";
        	$queryMkt = $this->db->query($sqlMkt,  array($this->store_id, $product['sku']));
        	$publications =  $queryMkt->fetchAll(PDO::FETCH_ASSOC);
        	
        	foreach ($publications as $i => $publication) {
        	    switch($publication['marketplace']){
        	        case 'Mercadolivre': 
                	    $sqlSold = "SELECT sold_quantity FROM ml_products WHERE store_id = {$this->store_id} AND  sku LIKE ? ";
                	    $querySold = $this->db->query($sqlSold,  array($product['sku']));
                	    $soldQty =  $querySold->fetch(PDO::FETCH_ASSOC);
                	    $publications[$i]['Mercadolivre'] = $soldQty['sold_quantity'] > 0 ? $soldQty['sold_quantity'] : 0 ;
            	    break;
        	    }
        	}
        	$products[$k]['publications'] =  $publications;
        	
        }
        
        
        return $products;
        
    }
    
    public function GetAvailableProductsMarketplaces()
    {
        
        $sql = $this->getSqlProductMarketplaces();
        
        if($this->records != 'no_limit'){
           $sql = $sql." LIMIT {$this->linha_inicial}, " . $this->records.";";
        }
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        
        
        $products =  $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $k => $product){
        	 
        	$sqlMkt = "SELECT count(marketplace) as qtd, marketplace, url FROM publications
        	WHERE store_id = ? AND sku LIKE ?  GROUP BY marketplace";
        	$queryMkt = $this->db->query($sqlMkt, array($this->store_id, $product['sku']));
        	$publications =  $queryMkt->fetchAll(PDO::FETCH_ASSOC);
        	
        	foreach ($publications as $i => $publication) {
        	    switch($publication['marketplace']){
        	        case 'Mercadolivre':
        	            $sqlSold = "SELECT sold_quantity FROM ml_products WHERE store_id = {$this->store_id} AND  sku LIKE ?";
        	            $querySold = $this->db->query($sqlSold,  array($product['sku']));
        	            $soldQty =  $querySold->fetch(PDO::FETCH_ASSOC);
        	            $publications[$i]['Mercadolivre'] = $soldQty['sold_quantity'] > 0 ? $soldQty['sold_quantity'] : 0 ;
        	            break;
        	    }
        	}
        	$products[$k]['publications'] =  $publications;
        	 
        	 
        }
        
        
        return $products;
        
    }
    
    /**************************************************************************************************/
    /************************************* List Parents ***********************************************/
    /**************************************************************************************************/
    
    public function ListParentProducts()
    {
        $query = $this->db->query('
        		SELECT  available_products.*,
        		product_descriptions.set_attribute_id,
        		product_descriptions.description
        		FROM available_products
        		LEFT JOIN product_descriptions ON available_products.id = product_descriptions.product_id
        		WHERE  available_products.store_id= ?
                GROUP BY available_products.parent_id
                ORDER BY available_products.parent_id DESC LIMIT 1000',
            array( $this->store_id)
            );
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    
    public function GetParentProducts()
    {
        $where_fields = $this->GetAvailableProductsFilter();
        
        $sql = "SELECT  available_products.*,
        		product_descriptions.set_attribute_id,
        		product_descriptions.description
        		FROM available_products
        		LEFT JOIN product_descriptions ON available_products.id = product_descriptions.product_id
                AND product_descriptions.marketplace = 'default'
        		WHERE {$where_fields}
                GROUP BY available_products.parent_id
        		ORDER BY available_products.sku DESC
                LIMIT {$this->linha_inicial}, {$this->records}";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    /**************************************************************************************************/
    /**************************************************************************************************/
    /**************************************************************************************************/
    
    public function ListAvailableProductsDescription()
    {
        
        //         echo $this->TotalAvailableProductsDescription();die;
        $sql = "SELECT available_products.id,
			    available_products.store_id,
			    available_products.sku,
			    available_products.parent_id,
			    available_products.title,
			    available_products.color,
			    available_products.variation,
			    available_products.brand,
			    available_products.reference,
			    available_products.category,
			    sum(available_products.quantity) as quantity,
			    available_products.price,
			    available_products.sale_price,
			    available_products.ncm,
			    available_products.cost,
			    available_products.weight,
			    available_products.height,
			    available_products.width,
			    available_products.length,
			    available_products.ean,
			    available_products.description,
			    available_products.updated,
			    available_products.xml,
			    available_products.flag,
			    available_products.blocked,
        		product_descriptions.set_attribute_id,
        		product_descriptions.description
        		FROM available_products
        		RIGHT JOIN product_descriptions ON available_products.id = product_descriptions.product_id
                AND available_products.store_id = product_descriptions.store_id
        		WHERE available_products.store_id= ?
                GROUP BY available_products.parent_id
                ORDER BY available_products.sku DESC
                LIMIT {$this->linha_inicial}, " . $this->records.";";
        
        $query = $this->db->query( $sql ,array( $this->store_id));
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function GetAvailableProductsDescriptions()
    {
        
        $where_fields = $this->GetAvailableProductsFilter();
        
        $sql = "SELECT  available_products.id,
			    available_products.store_id,
			    available_products.sku,
			    available_products.parent_id,
			    available_products.title,
			    available_products.color,
			    available_products.variation,
			    available_products.brand,
			    available_products.reference,
			    available_products.category,
			    sum(available_products.quantity) as quantity,
			    available_products.price,
			    available_products.sale_price,
			    available_products.ncm,
			    available_products.cost,
			    available_products.weight,
			    available_products.height,
			    available_products.width,
			    available_products.length,
			    available_products.ean,
			    available_products.description,
			    available_products.updated,
			    available_products.xml,
			    available_products.flag,
			    available_products.blocked,
        		product_descriptions.set_attribute_id,
        		product_descriptions.description
        		FROM available_products
        		RIGHT JOIN product_descriptions ON available_products.id = product_descriptions.product_id
                AND available_products.store_id = product_descriptions.store_id
        		WHERE {$where_fields}
                GROUP BY available_products.parent_id
                ORDER BY available_products.sku DESC";
        
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
        
        if(!empty($this->id) ){
            
            $query = $this->db->query('SELECT * FROM available_products WHERE `id`= ? AND store_id = ?',
                array( $this->id, $this->store_id ) );
            
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            if(!empty($fetch)){
                foreach($fetch as $key => $value)
                {
                    $column_name = str_replace('-','_',$key);
                    $this->{$column_name} = $value;
                }
            }else{
                return;
            }
            
        }else{
            
            return;
            
        }
        
    }
    
    public function getCategoryRelationship(){
        
        if(!empty($this->category) ){
            $query = $this->db->query('SELECT category_id FROM ml_category_relationship
                WHERE `category` LIKE ? AND store_id = ?',
                array( trim($this->category), $this->store_id ) );
            
            $categoryId = $query->fetch(PDO::FETCH_ASSOC);
            return $categoryId['category_id'];
            
        }else{
            
            return;
            
        }
        
        
    }
    public function getCategoryRelationshipInformations(){
        
        if(empty($this->category) ){
            
            return array();
            
        }
        $query = $this->db->query('SELECT * FROM ml_category_relationship
            WHERE `category` LIKE ? AND store_id = ?',
            array( trim($this->category), $this->store_id ) );
        
        return $query->fetch(PDO::FETCH_ASSOC);
        
        
    }
    
    public function getSetAttributeRelationship(){
        
        if(!empty($this->category) ){
            $query = $this->db->query('SELECT set_attribute_id FROM category
                WHERE `hierarchy` LIKE ? AND store_id = ?',
                array( $this->category, $this->store_id ) );
            
            $setAttributeId = $query->fetch(PDO::FETCH_ASSOC);
            if(empty($setAttributeId['set_attribute_id'])){
                $parts = explode(">",$this->category);
                
                $sql = "SELECT set_attribute_id FROM category
                WHERE store_id = ? AND category LIKE ? AND parent_id = 0";
                
//                $sql = "SELECT id as set_attribute_id FROM set_attributes
//                 WHERE store_id = ? AND `root_category`  LIKE ?";
                $query = $this->db->query($sql, array($this->store_id, trim($parts[0])));
                
                $setAttributeId = $query->fetch(PDO::FETCH_ASSOC);
                
            }
            $setAttributeId = !empty($setAttributeId['set_attribute_id']) ? $setAttributeId['set_attribute_id'] : 0 ;
            
            return $setAttributeId;
            
        }else{
            
            return 0;
            
        }
        
        
    }
    
    
    public function LoadParent()
    {
        
        $key = array_search('Product', $this->parametros);
        if(!empty($key)){
            $productId = get_next($this->parametros, $key);
        }
        if(!empty($productId)){
            
            $query = $this->db->query('SELECT parent_id FROM available_products WHERE `id`= ? AND store_id = ?'
                , array( $productId, $this->store_id ) );
            $parent = $query->fetch(PDO::FETCH_ASSOC);
            
            if(isset($parent['parent_id'])){
                
                $query = $this->db->query('SELECT * FROM available_products WHERE `parent_id`= ? AND store_id = ?'
                    , array( $parent['parent_id'], $this->store_id ) );
                
                return $query->fetchAll(PDO::FETCH_ASSOC);
                
                
            }
            
        }else{
            
            return;
            
        }
        
    }
    
    /**
     * Get seller categories
     */
    
    public function ListCategoriesByProducts(){
    
    	$sql = "SELECT distinct category FROM available_products WHERE store_id = ?";
    		
    	$query = $this->db->query($sql, array( $this->store_id ));
    		
    	$categories =  $query->fetchAll(PDO::FETCH_ASSOC);
    	
    	
    	$query = $this->db->query('SELECT category FROM `category`  WHERE parent_id = 0 AND `store_id` = ?',
    	    array( $this->store_id)
    	    );
    	
    	$categoriesRoot  = $query->fetchAll(PDO::FETCH_ASSOC);
    	
    	$categoriesMerged = array_merge($categories, $categoriesRoot);
    	 
    	 $categories = array();
    	 foreach($categoriesMerged as $cat => $val){
    	     
    	     $categories[] = $val['category'];
    	     
    	 }
    	 asort($categories);
    	 
    	 return $categories;
    
    }
    
    public function Delete()
    {
        
        //TODO: Fazer verificação se existe venda do produto
        $key = array_search('del', $this->parametros);
        if(!empty($key)){
            $id = get_next($this->parametros, $key);
        }
        
        if(!empty($id)){
            
            $query = $this->db->query('DELETE FROM available_products WHERE store_id = ? AND `id`= ?',  array( $this->store_id, $id ) );
            $this->db->query('DELETE FROM attributes_values WHERE store_id = ? AND `product_id`= ?',  array( $this->store_id, $id ) );
            $this->db->query('DELETE FROM product_descriptions WHERE store_id = ? AND `product_id`= ?',  array( $this->store_id, $id ) );
            $this->db->query('DELETE FROM product_relational WHERE store_id = ? AND `product_id`= ?',  array( $this->store_id, $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
} 