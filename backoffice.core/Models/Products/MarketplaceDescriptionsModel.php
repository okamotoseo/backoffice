<?php
/**
 * Modelo para gerenciar descrições de produtos dos marketplaces
 *
 */
class MarketplaceDescriptionsModel extends MainModel
{

    public $store_id;
    
    public $product_id;
    
    public $parent_id;
    
    public $marketplace;
    
    public $title;
    
    public $html_description = '';
    
    public $description;

    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        $this->parametros = $this->controller->parametros;
        
        $this->userdata = $this->controller->userdata;
        
        $this->store_id = $this->controller->userdata['store_id'];
        
    }
    
    public function ValidateForm() {
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST ) ) {
            
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = $value;
                        
                    }
                }else{
                    $arr = array('title');
                    
                    if( in_array($property, $arr) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
            
            if ( in_array('edit', $this->parametros )) {
                
                $this->Load();
                
            }
            
            if ( in_array('del', $this->parametros )) {
                
                $this->Delete();
            }
            
            return;
            
        }
        
    }
    
    public function Save(){
        
        
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('marketplace_descriptions', 'id', $this->id, array(
                'title' => friendlyText($this->title),
                'description' => $this->description
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
                $this->id = null;
                return;
            }
        } else {

                
            $query = $this->db->insert('marketplace_descriptions', array(
                'store_id' => $this->store_id,
                'marketplace' => $this->marketplace,
                'title' => friendlyText($this->title),
                'description' => $this->description
                
            ));
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
                return;
            }
                
            
        }
        
        
    }
    
    public function ListMarketplaceDescriptions()
    {
        
        $query = $this->db->query('SELECT * FROM `marketplace_descriptions`  WHERE `store_id` = ? ORDER BY id DESC',
            
            array($this->store_id)
            
            );
        
        if ( ! $query ) {
            
            return array();
            
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
        $key = array_search('edit', $this->parametros);
        
        if(!empty($key)){
            
            $id = get_next($this->parametros, $key);
            
        }
        
        if(!empty($id)){
            
            $query = $this->db->query('SELECT * FROM marketplace_descriptions WHERE `id`= ?', array( $id ) );
            
            foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
            {
                $column_name = str_replace('-','_',$key);
                $this->{$column_name} = $value;
            }
            
        } else {
            
            return;
            
        }
        
    }
    
    public function Delete()
    {
        $key = array_search('del', $this->parametros);
        
        if(!empty($key)){
            
            $id = get_next($this->parametros, $key);
        
        }
        
        if(!empty($id)){
            
            $query = $this->db->query('DELETE FROM marketplace_descriptions WHERE `id`= ?', array( $id ) );
            
            if ( ! $query ) {
                
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
    
    
} 