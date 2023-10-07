<?php

class QuestionsModel extends MainModel
{

    public $id;
    
    public $store_id;
    
    public $question_id;
    
    public $seller_id;
    
    public $question;
    
    public $status;
    
    public $item_id;
    
    public $date_created;
    
    public $hold;
    
    public $deleted_from_listing;
    
    public $answer;
    
    public $answer_status;
    
    public $answer_date_created;
    
    public $from_id;
    
    public $from_answered_questions;
    

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
        
        $this->received =  date("Y-m-d", strtotime("-2 day", strtotime("now")))." 00:00:00";
        $this->received_to = date("Y-m-d")." 23:59:59";
        
    }
    
    public function Save(){
        
        $sqlVerify = "SELECT id FROM `ml_questions` WHERE store_id = {$this->store_id}
        AND question_id = {$this->question_id}";
        
        $queryVerify = $this->db->query($sqlVerify);
        $verify = $queryVerify->fetch(PDO::FETCH_ASSOC);
//         echo 123;
//         pre($verify);
        if(!isset($verify['id'])){
           
            $query = $this->db->insert('ml_questions', array(
                "store_id" => $this->store_id,
                "question_id" => $this->question_id,
                "seller_id" => $this->seller_id,
                "question" => $this->question,
                "status" => $this->status,
                "item_id" => $this->item_id,
                "date_created" => $this->date_created,
                "hold" => $this->hold,
                "deleted_from_listing" => $this->deleted_from_listing,
                "answer" => $this->answer,
                "answer_status" => $this->answer_status,
                "answer_date_created" => $this->answer_date_created,
                "from_id" => $this->from_id,
                "from_answered_questions" => $this->from_answered_questions
            ));
            
            
        }else{
            
            $query = $this->db->update('ml_questions', 
                array("store_id", "id"), 
                array($this->store_id, $verify['id']),
                array("status" => $this->status,
                "answer" => $this->answer,
                "answer_status" => $this->answer_status,
                "answer_date_created" => $this->answer_date_created,
                "from_id" => $this->from_id,
                "from_answered_questions" => $this->from_answered_questions
            ));
            
            
        }
        if(!$query){
        	pre($query);
        }
        
    }
    
    
    
    

} 