<?php
/**
 * Modelo para gerenciar categorias
 *
 */
class CategoryModel extends MainModel
{
    /**
     * @var int
     */
	public $id;
	
    public $store_id;
    
    public $category;
    
    public $root_category;
    
    public $description;
    
    public $parent_id;
    
    public $set_attribute_id;
    
    public $hierarchy;
    
    public $children;
    
    public $readonly = '';
   

    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db; 
        
        $this->controller = $controller;
        
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
        }
        
        if(!defined('QTDE_REGISTROS')){
            
            define('QTDE_REGISTROS', 50);
            
        }
        
        
    }
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['save'] ) ) {
        	
        	
            foreach ( $_POST as $property => $value ) {
            	if(!empty($value)){
	                if(property_exists($this,$property)){
	                	switch($property){
	                		case 'category': $value = trim($value); break;
	                	}
	                    
	                    $this->{$property} = $value;
	                    
	                }
	                
            	}else{
            		
            		if($property == "parent_id"){
            			$this->parent_id =  0 ;
            		}
            		
            		
            		$arr = array('category');
            		
            		if( in_array($property, $arr) ){
	                    $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
	                    return;
            		}
                    
                }
                
            }
            
            return true;
            
        } else {
        	
        	if ( in_array('edit', $this->parametros )) {
        		$this->readonly = 'readonly="readonly" tabindex="-1" aria-disabled="true"';
        	        $this->Load();
        	
        	}
        	 
        	if ( in_array('del', $this->parametros )) {
        		$this->readonly = 'readonly="readonly" tabindex="-1" aria-disabled="true"';
        		$this->Delete();
        	
        	}
        	
            return;
            
        }
        
    }
    
    public function Save(){
    	
    	if($this->parent_id == 0){
    	    $this->hierarchy = friendlyText($this->category);
    	}else{
    	
    		$query = $this->db->query('SELECT hierarchy FROM category WHERE `id`= ? AND store_id = ?',
    				array( $this->parent_id, $this->store_id ) );
    		$row = $query->fetch(PDO::FETCH_OBJ);
    		$this->hierarchy = $row->hierarchy.' > '. friendlyText($this->category);
    	}
    	
    	$parts = explode(">", $this->hierarchy);
    	$numParts = count($parts);
    	$children = '';
    	for($i = 0 ; $i < $numParts -1 ; $i++){
    	    
    	    $children .= trim($parts[$i])." > ";
    	}
    	
    	$this->children = trim(substr($children, 0,-3));
    	 
    	if ( ! empty( $this->id ) ) {
    		
    		$query = $this->db->update('category', 'id', $this->id, array(
    				'description' => $this->description,
    		        'set_attribute_id' => $this->set_attribute_id
    		));
    		 
    		if ( ! $query ) {
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
    			 
    			return;
    		} else {
    			
    			$this->unsetForm();
    			
    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
    			return;
    		}
    	} else {
    		
    		$query = $this->db->query('SELECT * FROM `category`  WHERE `store_id` = ?
    				AND category LIKE ? AND parent_id = ?',
    				array( $this->store_id, friendlyText($this->category), $this->parent_id)
    		);
    		
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		if(!isset($res['category'])){
    		
	    		$query = $this->db->insert('category', array(
	    				'store_id' => $this->store_id,
	    				'category' => friendlyText($this->category),
	    				'description' => $this->description,
	    				'parent_id' => $this->parent_id,
	    		        'hierarchy' => $this->hierarchy,
	    		        'set_attribute_id' => $this->set_attribute_id
	    				)
	    			);
	    		
	    		if ( ! $query ) {
	    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
	    			return;
	    		} else {
	    			
	    			$this->unsetForm();
	    			 
	    			$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
	    			return;
	    		}
	    		
    		}else {
    			 
    			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Já existe uma categoria com a mesma hierarquia.</div>';
    			return;
    		}
    		
    	}
    	 
    }
    
    public function unsetForm(){
    	
    	unset($this->id);
    	unset($this->category);
    	unset($this->description);
    	unset($this->parent_id);
    	unset($this->children);
    	unset($this->hierarchy);
    	unset($this->set_attribute_id);
    }
    
    public function ListCategory()
    {
        
        $sql = "SELECT category.* FROM category WHERE category.store_id = ? ORDER BY category.hierarchy ASC";
        $query = $this->db->query($sql, array( $this->store_id));
        if ( ! $query ) {
            return array();
        }
        $res = $query->fetchAll(PDO::FETCH_ASSOC);

        
        return $res;
        
    }
    
    public function ListCategoryGoogleXml()
    {
//         $sql = "SELECT distinct product_type as hierarchy, gender, age_group FROM module_google_xml_products 
//                 WHERE store_id = ? AND product_type != '' ORDER BY product_type ASC";
        
        $sql = "SELECT distinct module_google_xml_products.product_type as hierarchy, module_google_xml_products.gender, 
                module_google_xml_products.age_group  FROM module_google_xml_products WHERE module_google_xml_products.store_id = ?";
        $query = $this->db->query($sql, array( $this->store_id));
        if ( ! $query ) {
            return array();
        }
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $res;
    }
    
    public function ListCategoryItems()
    {

    	$sql = "SELECT category.* FROM category WHERE category.store_id = ? ORDER BY category.hierarchy ASC";
    	$query = $this->db->query($sql, array( $this->store_id));
    	if ( ! $query ) {
    		return array();
    	}
    	$res = $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($res as $key => $category){

			$cond = $category['parent_id'] == 0 ? '%' : '' ;            
            $sql = "SELECT count(id) as qtd FROM available_products WHERE store_id = ? AND category LIKE ?";
            $queryAP = $this->db->query($sql, array($this->store_id, $category['hierarchy'].$cond));
            $row = $queryAP->fetch(PDO::FETCH_ASSOC);
            $res[$key]['items'] = isset($row['qtd']) ? $row['qtd'] : 0 ;
        
        }
        
        return $res;
        
    }
    
    public function ListCategoriesRoot()
    {
        $query = $this->db->query('SELECT * FROM `category`  WHERE parent_id = 0 AND `store_id` = ?',
            array( $this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function GetRootCategoryFromId()
    {
    	
    	if ( ! isset($this->id) ) {
    		return array();
    	}
    	
    	$query = $this->db->query("SELECT hierarchy FROM `category`  WHERE `store_id` = ? AND id = ? ",
    			array( $this->store_id, $this->id)
    			);
    
    	if ( ! $query ) {
    		return array();
    	}
    	$categories =  $query->fetch(PDO::FETCH_ASSOC);
    	
		$parts = explode(">", $categories['hierarchy']);
    	
    	return trim($parts[0]);
    	
    	
    }
    
    public function ListCategoryChild()
    {
        $query = $this->db->query('SELECT * FROM `category`  WHERE parent_id != 0 AND `store_id` = ? ORDER BY hierarchy DESC',
            array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
//         return $query->fetchAll(PDO::FETCH_ASSOC);
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($res as $key => $category){
            
            
            $sql = "SELECT count(id) as qtd FROM available_products WHERE store_id = ? AND category = ?";
            $queryAP = $this->db->query($sql, array($this->store_id, $category['hierarchy']));
            $row = $queryAP->fetch(PDO::FETCH_ASSOC);
            
            $res[$key]['items'] = isset($row['qtd']) ? $row['qtd'] : 0 ;
            
        }
        
        return $res;
        
    }
    
    
    public function ListCategoryFromRoot($root = null)
    {
        if(isset($root)){
            $query = $this->db->query("SELECT * FROM `category`
                    WHERE hierarchy LIKE '{$root}%' AND `store_id` = ? AND parent_id != 0",
                    array( $this->store_id)
            );
        }else{
            $query = $this->db->query("SELECT * FROM `category`
                    WHERE  `store_id` = ? AND parent_id != 0",
                    array( $this->store_id)
            );
            
        }
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function GetCategoryFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "category.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "category.{$key} = {$this->$key} AND ";break;
                    case 'parent_id': $where_fields .= "category.{$key} = {$this->$key} AND ";break;
                    case 'category': $where_fields .= "category.{$key} LIKE '{$this->$key}' AND ";break;
                    case 'hierarchy': $where_fields .= "category.{$key} LIKE '{$this->$key}' AND ";break;
                    
                }
            }
            
        }
        
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
    public function GetCategory()
    {
        
        $where_fields = $this->GetCategoryFilter();
        
        $sql = "SELECT * FROM category WHERE {$where_fields}";
        
        $query = $this->db->query($sql);
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function GetSetAttributeFromCategory()
    {
        
        if(!isset($this->hierarchy)){
            return array();
        }
        $sql = "SELECT set_attribute_id FROM category 
        WHERE store_id = {$this->store_id} AND hierarchy LIKE '{$this->hierarchy}'";
        $query = $this->db->query($sql);
        $res = $query->fetch(PDO::FETCH_ASSOC);
        if(empty($res['set_attribute_id'])){
        	if(empty($this->root_category)){
        		$parts = explode('>', $this->hierarchy);
        		$this->root_category = trim($parts[0]);
        		$res = $this->GetSetAttributeFromRootCategory();
        	}
        }
        
        return $res; 
    }
    
    public function GetSetAttributeFromRootCategory()
    {
    
    	if(!isset($this->root_category)){
    		return array();
    	}
    
    	$sql = "SELECT set_attribute_id FROM category
    	WHERE store_id = {$this->store_id} AND category LIKE '{$this->root_category}' AND parent_id = 0";
    
    	$query = $this->db->query($sql);
    	if ( ! $query ) {
    		return array();
    	}
    	return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    public function GetCategoriesIds()
    {
        
        if(!isset($this->hierarchy)){
	        return array();
	    }
        
	    $parts = explode(">", $this->hierarchy);
	    pre($parts);
        $categories_ids = array();
	    foreach($parts as $key => $category){
	       
	        $sql = "SELECT id, parent_id FROM category WHERE store_id = ? AND category LIKE ?";
	        $query = $this->db->query($sql, array($this->store_id, trim($category)));
	        $res = $query->fetch(PDO::FETCH_ASSOC);
	        pre($res);
	        $categories_ids[] = array('id' => $res['id'], 'parent_id' =>  $res['parent_id']);

	    }
	    
	    return $categories_ids;
    }
    
    public function GetCategoriesId()
    {
    
    	if(!isset($this->hierarchy)){
    		return array();
    	}
    
    
    		$sql = "SELECT id, parent_id FROM category WHERE store_id = ? AND hierarchy LIKE ?";
    		$query = $this->db->query($sql, array($this->store_id, $this->hierarchy));
    		return $query->fetch(PDO::FETCH_ASSOC);
    	 
    }
    
    
    public function GetCategoriesName()
    {
    
    	if(!isset($this->hierarchy)){
    		return array();
    	}
    
    	$parts = explode(">", $this->hierarchy);
    	$categories_ids = array();
    	$categoryParentId = 0;
    	foreach($parts as $key => $category){
//     		pre($category);
//     		echo $sql = "SELECT id, parent_id, category FROM category WHERE store_id = {$this->store_id} AND category LIKE '{$category}' AND parent_id = {$categoryParentId} ";
    		$sql = "SELECT id, parent_id, category FROM category WHERE store_id = ? AND category LIKE ? AND parent_id = ? ";
    		$query = $this->db->query($sql, array($this->store_id, trim($category), $categoryParentId));
    		$res = $query->fetch(PDO::FETCH_ASSOC);
    		$categories_ids[] = array('id' => $res['id'], 'parent_id' =>  $res['parent_id'], 'name' =>  $res['category']);
    		$categoryParentId = $res['id'];
    	}
    	 
    	return $categories_ids;
    }
    
    
    
    public function Load()
    {
        if ( in_array('edit', $this->parametros )) {
            
            $key = array_search('edit', $this->parametros);
            
            $id = get_next($this->parametros, $key);
    
    		$query = $this->db->query('SELECT * FROM category WHERE `id`= ? AND `store_id` = ?', array( $id, $this->store_id ) );
    
    		foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
    		{
    		    
    			$column_name = str_replace('-','_',$key);
    			$this->{$column_name} = $value;
    			
    			if($column_name =='hierarchy' ){
    			    
    			    $parts = explode(">", $value);
                    $numParts = count($parts);
                    $children = '';
                    for($i = 0 ; $i < $numParts -1 ; $i++){
                        
                        $children .= trim($parts[$i])." > ";
                    }
                    
                    $this->children = trim(substr($children, 0,-3));
    			}
    		}
    
    	} else {
    
    		return;
    
    	}
    
    }
    
    public function Delete()
    {
        if ( in_array('del', $this->parametros )) {
            
            $key = array_search('del', $this->parametros);
            
            $this->id = get_next($this->parametros, $key);
            
            $category = $this->GetCategory();
            
//             pre($category[0]['hierarchy']);die;
            $sql = "SELECT id FROM available_products WHERE store_id  = ? AND category LIKE ? ";
            
            $query = $this->db->query($sql, array($this->store_id, $category[0]['hierarchy']));
            $avaulableProducts = $query->fetch(PDO::FETCH_ASSOC);
            
            if(!isset($avaulableProducts['id'])){
                $query = $this->db->query('DELETE FROM category WHERE `id`= ? AND `store_id` = ?', array( $this->id , $this->store_id  ) );
    		
        		if ( ! $query ) {
        			$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
        			return;
        			
        		}else{
        		    
        		    $this->form_msg = '<div class="alert alert-success alert-dismissable">Categoria excluida com sucesso.</div>';
        		    return;
        		    
        		}
        		
            }else{
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Exclusão negada!. Existem produtos relacionados a essa categoria.</div>';
                return;
                
            }
    
    
    	} else {
    
    		return;
    
    	}
    
    }
    
} 