<?php

class MessagesModel
{
    
    /**
     * @var string
     * Class Unique ID
     */
    public $id;
    
    public $store_id;
    
    public $status;
    
    public $message;
    
    public $subject;
    

    
    
    
    
    public function __construct($db = false, $controller = null)
    {
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
                    $arr = array('store_id', 'status', 'subject', 'message' );
                    
                    if( in_array($property, $arr) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
            
            if ( chk_array( $this->parametros, 4 ) == 'edit' ) {
                $this->Load();
                
            }
            
            if ( chk_array( $this->parametros, 4 ) == 'del' ) {
                
                $this->Delete();
                
            }
            
            return;
            
        }
        
    }
    
    public function Save(){
        
        
        if ( ! empty( $this->id ) ) {
            
            $query = $this->db->update('ml_messages', 'id', $this->id, array(
                'status' => $this->status,
                'subject' => $this->subject,
                'message' => $this->message
                
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
            
            
            $query = $this->db->insert('ml_messages', array(
                'store_id' => $this->store_id,
                'status' => $this->status,
                'subject' => $this->subject,
                'message' => $this->message
            )
                );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
                return;
            }
            
            
        }
        
        
    }
    
    public function ListMessages()
    {
        $query = $this->db->query('SELECT * FROM `ml_messages`  WHERE `store_id` = ? ORDER BY id DESC',
            array($this->store_id)
            );
        
        if ( ! $query ) {
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    public function Load()
    {
        if ( chk_array( $this->parametros, 4 ) == 'edit' ) {
            
            $id = chk_array( $this->parametros, 5 );
            
            $query = $this->db->query('SELECT * FROM ml_messages WHERE  store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
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
        if ( chk_array( $this->parametros, 4 ) == 'del' ) {
            
            $id = chk_array( $this->parametros, 5 );
            
            $query = $this->db->query('DELETE FROM ml_messages WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
                return;
            }
            
            
        } else {
            
            return;
            
        }
        
    }
    
    
    
}

?>