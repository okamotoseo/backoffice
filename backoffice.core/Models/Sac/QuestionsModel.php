<?php 
class QuestionsModel extends MainModel
{
    
	public $id;
	
	public $store_id;
	
	public $product_id;
	
	public $sku;
	
	public $title;
	
	public $customer;
	
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
	
	public $marketplace;
	
	public $user;
	
    public $records = 50;
    
    
    public function __construct( $db = false, $controller = null ) {
        
        $this->db = $db;
        
        $this->controller = $controller;
        
        if(isset($this->controller)){
            
            $this->parametros = $this->controller->parametros;
            
            $this->userdata = $this->controller->userdata;
            
            $this->store_id =  $this->controller->userdata['store_id'];
        
        }
        
        if(!defined('QTDE_REGISTROS')){
            
            define('QTDE_REGISTROS', 50);
            
        }
    }
    
    
    public function ValidateForm() {
        
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
                    //                     $val = str_replace("_", " ", $val);
                    $this->{$param} = $val;
                    
                }
            }
            
            return true;
            
        }else{
            
            $this->pagina_atual = 1;
            $this->linha_inicial = ($this->pagina_atual -1) * $this->records;
        }
        
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset ( $_POST['btn-questions'] ) ) {
            foreach ( $_POST as $property => $value ) {
                if(!empty($value)){
                    if(property_exists($this,$property)){
                        
                        $this->{$property} = !is_array($value) ? trim($value) : $value ;
                        
                    }
                }else{
                    $arr = array();
                    $arr = array('CPFCNPJ', 'Email');
                    
                    if( in_array($property, $arr) ){
                        $this->form_msg = '<div class="alert alert-danger alert-dismissable">There are empty fields. Data has not been sent.</div>';
                        return;
                    }
                    
                }
                
            }
            
            
            
            return true;
            
        } else {
            
            if ( in_array('edit', $this->parametros )) {
                
                $key = array_search('edit', $this->parametros);
                
                $customerId = get_next($this->parametros, $key);
                $this->id  = is_numeric($customerId) ? $customerId :  '';
                
                if(!empty($this->id)){
                    
                    $this->Load();
                    
                }
                
            }
            
            if ( in_array('del', $this->parametros )) {
                    
                $key = array_search('del', $this->parametros);
                
                $customerId = get_next($this->parametros, $key);
                $this->id  = is_numeric($customerId) ? $customerId :  '';
                
                if(!empty($this->id)){
                
                    $this->Delete();
                    
                }
            
                return;
            
            }
            
        }
        
    }
    
    public function Save(){
        
        $sqlVerify = "SELECT id FROM `questions`  WHERE  `store_id` = $this->store_id AND question_id = '{$this->question_id}'";
        $query = $this->db->query($sqlVerify);
        $res = $query->fetch(PDO::FETCH_ASSOC);
        if(!empty($res['id'])){
            $query = $this->db->update('questions',
            		array("store_id", "id"),
            		array($this->store_id, $res['id']),
            		array("status" => $this->status,
            				"answer" => $this->answer,
            				"answer_status" => $this->answer_status,
            				"answer_date_created" => $this->answer_date_created,
            				"from_id" => $this->from_id,
            				"from_answered_questions" => $this->from_answered_questions
            		));
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
                $this->id = null;
                return $res['id'];
            }
        } else {

            $query = $this->db->insert('questions', array(
            		'id' => $this->id,
            		'store_id' => $this->store_id,
            		'sku' => $this->sku,
            		"title" => $this->title,
            		'product_id' => $this->product_id,
            		'customer' => $this->customer,
            		'question_id' => $this->question_id,
            		'seller_id' => $this->seller_id,
            		'question' => $this->question,
            		'status' => $this->status,
            		'item_id' => $this->item_id,
            		'date_created' => $this->date_created,
            		'hold' => $this->hold,
            		'deleted_from_listing' => $this->deleted_from_listing,
            		'answer' => $this->answer,
            		'answer_status' => $this->answer_status,
            		'answer_date_created' => $this->answer_date_created,
            		'from_id' => $this->from_id,
            		'from_answered_questions' => $this->from_answered_questions,
            		'marketplace' => $this->marketplace
                )
            );
            
            if ( ! $query ) {
                $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
                return;
            } else {
                
                $this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
                return $this->db->last_id;
            }
                

            
        }
        
        
    }
    

    public function GetQuestionsFilter()
    {
        
        $where_fields = "";
        $values = array();
        $class_vars = get_class_vars(get_class($this));
        foreach($class_vars as $key => $value){
            if(!empty($this->{$key})){
                switch($key){
                    case 'store_id': $where_fields .= "questions.{$key} = {$this->$key} AND ";break;
                    case 'id': $where_fields .= "questions.{$key} = {$this->$key} AND ";break;
                    case 'product_id': $where_fields .= "questions.{$key} = {$this->$key} AND ";break;
                    case 'customer': $where_fields .= "questions.{$key} LIKE '".trim($this->$key)."' AND ";break;
                    case 'marketplace': $where_fields .= "questions.{$key} LIKE '".trim($this->$key)."' AND ";break;
                    case 'sku': $where_fields .= "questions.{$key} LIKE '".trim($this->$key)."' AND ";break;
                }
            }
            
        }
        $where_fields = substr($where_fields, 0,-4);
        
        return $where_fields;
        
    }
    
    public function TotalGetQuestions(){
        
        $where_fields = $this->GetQuestionsFilter();
        
        $query = $this->db->query("SELECT * FROM `questions`  WHERE {$where_fields}");
        if ( ! $query ) {
            return array();
        }
        return $query->rowCount();
        
    }
    
    public function GetQuestions()
    {
        
        $where_fields = $this->GetQuestionsFilter();
        
        $sql = "SELECT * FROM `questions`  WHERE {$where_fields} ORDER BY id DESC";
        
        if($this->records != 'no_limit'){
            $sql = $sql." LIMIT {$this->linha_inicial}, " . $this->records.";";
        }
//         pre($sql);die; 
        $query = $this->db->query($sql);
        
        if ( ! $query ) {
            return array();
        }
        
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    }
    
    
    public function TotalQuestions(){
        
        $sql = "SELECT count(*) as total FROM `questions`  WHERE `store_id` = ?";
        
        $query = $this->db->query( $sql ,array( $this->store_id));
        
        $total = $query->fetch(PDO::FETCH_ASSOC);
        
        return $total['total'];
        
    }
    
    public function ListQuestions()
    {
    	
    	if(!isset($this->status)){
    		
    		if(isset($this->parametros[2])){
    			switch($this->parametros[2]){
    				case "Answered": $this->status = 'ANSWERED'; break;
    				default: $this->status = 'UNANSWERED'; break;
    					
    			}
    		}else{
    			$this->status = 'UNANSWERED';
    		}
    	}
        $query = $this->db->query("SELECT * FROM `questions`  WHERE `store_id` = ?  AND status LIKE '{$this->status}' ORDER BY date_created DESC
            LIMIT {$this->linha_inicial}, " . QTDE_REGISTROS." ;",
            array($this->store_id)
            );
        
        $questions =  $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($questions as $key => $question){
        	
        	switch($question['marketplace']){
        		case "Mercadolivre":
        			$advertsId = str_replace("MLB", '', $question['item_id']);
        			$sql = "SELECT available_products.*,
        			available_products.id as ap_id,
        			available_products.sku  as ap_sku,
        			available_products.quantity  as ap_quantity,
        			available_products.sale_price as ap_sale_price,
        			ml_products.* FROM ml_products 
        			LEFT JOIN available_products ON available_products.sku = ml_products.sku
        			WHERE ml_products.store_id = {$this->store_id} AND ml_products.id =  {$advertsId}";
        			$query = $this->db->query($sql);
        			$result = $query->fetch(PDO::FETCH_ASSOC);
        			$questions[$key]['item'] = $result;
        			break;
        		case "B2W":
        			$sql = "SELECT available_products.*, module_skyhub_products.* FROM module_skyhub_products 
        			LEFT JOIN available_products ON available_products.id = module_skyhub_products.product_id
        			WHERE module_skyhub_products.store_id = {$this->store_id} AND module_skyhub_products.product_id =  {$question['product_id']}";
        			$query = $this->db->query($sql);
        			$result = $query->fetch(PDO::FETCH_ASSOC);
        			$images = getUrlImageFromId($this->db, $this->store_id, $question['product_id']);
        			if(!empty($images[0])){
        				$result['thumbnail'] = $images[0];
        			}
        			$questions[$key]['item'] = $result;
        			break;
        	
        		
        	}
        }
        return $questions;
        
    }
    
    public function ReportQuestions(){
//     	$sql = "SELECT COUNT(questions.sku) AS qtd, questions.product_id, questions.title, questions.customer, questions.question_id, questions.seller_id, questions.question, questions.
//     	status, questions.item_id, questions.date_created, questions.hold, questions.deleted_from_listing, questions.answer, questions.answer_status, questions.
//     	answer_date_created, questions.from_id, questions.from_answered_questions, questions.marketplace, questions.user  FROM questions
//     	WHERE questions.store_id = {$this->store_id} GROUP BY questions.product_id";
    
    	$sql = "SELECT COUNT(questions.sku) AS qtd, questions.product_id, questions.sku, questions.title FROM questions
    	WHERE questions.store_id = {$this->store_id} GROUP BY questions.product_id ORDER BY qtd DESC";
    	$query = $this->db->query($sql);
    	$questions =  $query->fetchAll(PDO::FETCH_ASSOC);
    	 
    	foreach ($questions as $k => $question){
    
    		$sql2 = "SELECT * FROM questions WHERE questions.store_id = {$this->store_id} AND product_id = {$question['product_id']}";
    		$query2 = $this->db->query($sql2);
    		$questions[$k]['answers'] =  $query2->fetchAll(PDO::FETCH_ASSOC);
    
    	}
    	return $questions;
    	 
    }
    
    
    public function Load()
    {
        
        if(!isset($this->id)){
            return;
        }
            
        $query = $this->db->query('SELECT * FROM questions WHERE `id`= ?', array( $this->id ) );
        foreach($query->fetch(PDO::FETCH_ASSOC) as $key => $value)
        {
            $column_name = str_replace('-','_',$key);
            $this->{$column_name} = $value;
        }
            
        
    }
    
    public function Delete()
    {
        if(!isset($this->id)){
            return array();
        }
            
        $query = $this->db->query('DELETE FROM questions WHERE `id`= ?', array( $this->id ) );
        
        if ( ! $query ) {
            
            $this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
            return;
            
        }
            
            
        
    }
    
}
?>