<?php

class NotificationsModel extends MainModel
{

    public $id;
    
    public $store_id;
    
    public $resource;
    
    public $user_id;
    
    public $topic;
    
    public $application_id;
    
    public $attempts;
    
    public $sent;
    
    public $received;

    public $received_to;
    

    public function __construct($db = false,  $controller = null, $storeId = null)
    {
        $this->db = $db;
        
        $this->store_id = $storeId;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id = $this->controller->userdata['store_id'];
            
        }
        
        $this->received =  date("Y-m-d H:i:s",  strtotime("-24 hour") ); //date("Y-m-d H:i:s", strtotime("-24 hour", strtotime("now")));
        $this->received_to = date("Y-m-d H:i:s");
        
    }
    
   
    
    public function GetNotifications(){
        
        if(empty($this->topic)){
            return array();
        }
        
       $sql = "SELECT * FROM `ml_notifications` WHERE store_id = {$this->store_id} 
            AND topic LIKE '{$this->topic}' AND received BETWEEN  '{$this->received}' AND '{$this->received_to}'";
        $query = $this->db->query($sql);
        $notifications = $query->fetchAll(PDO::FETCH_ASSOC);
        return $notifications;
    }
    
    
    

} 