<?php
/**
 * Modelo para gerenciar descrições de produtos
 *
 */
class ProductRelationalModel extends MainModel
{

	public $id;
    
    public $store_id;
    
    public $product_id;
    
    public $productsRelational = array();
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->parametros = $this->controller->parametros;
        
        $this->store_id = $this->controller->userdata['store_id'];
        
    }
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['product-relational'] ) ) {
            
            foreach ( $_POST['products_relational'] as $key => $value ) {
                
                $productRelationa = array(
                    "product_relational_id" => $key,
                    "qtd" => $value
                );
                
                if(isset($_POST['dynamic_price'][$key]) and !empty($_POST['dynamic_price'][$key])){
                    $productRelationa['dynamic_price'] = 'T';
                }else{
                    $productRelationa['dynamic_price'] = 'F';
                }
                    
                if(isset($_POST['fixed_unit_price'][$key]) and !empty($_POST['fixed_unit_price'][$key])){
                    $productRelationa['fixed_unit_price'] = number_format(str_replace(",", ".", $_POST['fixed_unit_price'][$key]), 2);
                }else{
                    $productRelationa['fixed_unit_price'] = null;
                }
                
                if(isset($_POST['discount_fixed'][$key]) and !empty($_POST['discount_fixed'][$key])){
                    $productRelationa['discount_fixed'] = number_format(str_replace(",", ".", $_POST['discount_fixed'][$key]), 2);
                }else{
                    $productRelationa['discount_fixed'] = null;
                }
                if(isset($_POST['discount_percent'][$key]) and !empty($_POST['discount_percent'][$key])){
                    $productRelationa['discount_percent'] = number_format(str_replace(",", ".", $_POST['discount_percent'][$key]), 2);
                }else{
                    $productRelationa['discount_percent'] = null;
                }
            
                $this->productsRelational[] = $productRelationa;
                
                unset($productRelationa);
                
            }
            
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    
                    if(property_exists($this,$property)){
                        $this->{$property} = $value;
                    }
                }else{
                    $required = array();
                    if( in_array($property, $required) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                    }
                }
            }
            
            
            return true;
            
        }else{
            
            if ( in_array('Product', $this->parametros )) {
                
                $key = array_search('Product', $this->parametros);
                
                $productId = get_next($this->parametros, $key);
                $this->product_id  = is_numeric($productId) ? $productId :  '';
                
                if(!empty($this->product_id)){
                    
                    $this->Load();
                    
                }
                
            }
            
            return;
            
        }
        
    }
    
    public function Save(){
//         pre($this->productsRelational);die;
        foreach($this->productsRelational as $key => $value){
            $query = $this->db->query('SELECT * FROM `product_relational`  WHERE `store_id` = ?
    				AND product_id = ? AND product_relational_id = ?',
                array($this->store_id, $this->product_id, $value['product_relational_id'])
                );
            
            $res = $query->fetch(PDO::FETCH_ASSOC);
            
            if(!isset($res['id'])){
                
                $query = $this->db->insert('product_relational', array(
                    'store_id' => $this->store_id,
                    'product_id' => $this->product_id ,
                    'product_relational_id' => $value['product_relational_id'],
                    'qtd' => $value['qtd'],
                    'dynamic_price' => $value['dynamic_price'],
                    'fixed_unit_price' => $value['fixed_unit_price'],
                    'discount_fixed' => $value['discount_fixed'],
                    'discount_percent' => $value['discount_percent']
                ));
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    return;
             
                }else{
                    
                    $this->db->update('available_products',
                        array('store_id','id'),
                        array($this->store_id, $this->product_id),
                        array('kit' => 'T')
                        );
                }
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Produto relacionado com sucesso.</div>';
                
                
            }else {
                
//                 $sql = "UPDATE product_relational SET qtd = {$value['qtd']},
//                     dynamic_price = '{$value['dynamic_price']}',
//                     fixed_unit_price = '{$value['fixed_unit_price']}'
//                 WHERE store_id = {$this->store_id}
//                 AND product_id = {$this->product_id}
//                 AND product_relational_id = {$value['product_relational_id']}";
                
                
                $query = $this->db->update('product_relational',
                    array('store_id','product_id','product_relational_id'),
                    array($this->store_id, $this->product_id, $value['product_relational_id']),
                    array('qtd' => $value['qtd'],
                        'dynamic_price' => $value['dynamic_price'],
                        'fixed_unit_price' => $value['fixed_unit_price'],
                        'discount_fixed' => $value['discount_fixed'],
                        'discount_percent' => $value['discount_percent'])
                    );
                
//                 $query = $this->db->query($sql);
                
                if ( ! $query ) {
                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                    
                    return;
                }
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Produto atualizado com sucesso.</div>';
                
            }
        }
        
        
    }
    
    public function ListRelational()
    {
            $query = $this->db->query('SELECT product_relational.*, available_products.sku, 
            		available_products.title, available_products.sale_price, available_products.quantity
            FROM `product_relational` 
            LEFT JOIN available_products ON available_products.id = product_relational.product_relational_id 
            WHERE product_relational.store_id = ? AND product_relational.product_id = ? ORDER BY product_relational.id DESC',
            array($this->store_id, $this->product_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }

    
    public function Load()
    {
            
        $query = $this->db->query('SELECT * FROM product_relational WHERE store_id = ? AND `product_id`= ?', array($this->store_id, $this->product_id ) );
        $relationa = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($relationa)){
            $this->id = $relationa[0]['id'];
            $this->store_id = $relationa[0]['store_id'];
            $this->product_id = $relationa[0]['product_id'];
            
            
            foreach($relationa as $key => $value)
            {
                $this->productsRelational[] = array(
                    "product_relational_id" => $value['product_relational_id'], 
                    "qtd" => $value['qtd']
                    
                );
            }
        }
        
        
    }
    
    public function Delete()
    {
            
        $query = $this->db->query('DELETE FROM product_relational WHERE store_id = ? AND `id`= ?', array($this->store_id, $this->id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            

        
    }
    
    
    
} 