<?php
/**
 * Modelo para gerenciar devoluções
*
*/
class OccurrenceModel extends MainModel
{


	public $id;

	public $store_id;

	public $pedido_id;
	
	public $order_id;
	
	public $customer_id;

	public $created;
	
	public $user;
	
	public $records = 50;



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

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST['order-occurrence'] ) ) {
			foreach ( $_POST as $property => $value ) {
				if(!empty($value)){
					if(property_exists($this, $property)){
						$this->{$property} = $value;
					}
				}else{
					$arr = array();
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
				$this->id = get_next($this->parametros, $key);
				if(!empty($this->id)){
					$this->Load();
				}
			}
			return;

		}

	}

	public function Save(){
		
			$query = $this->db->query('SELECT * FROM `order_occurrence`  WHERE `store_id` = ?
    				AND order_id = ? ORDER BY created ASC',
					array($this->store_id, $this->order_id));

			$res = $query->fetch(PDO::FETCH_ASSOC);

			if(!isset($res['user'])){
				
				 $data = array(
						'store_id' => $this->store_id,
				 		'pedido_id' => $this->pedido_id,
				 		'customer_id' => $this->customer_id,
						'order_id' => $this->order_id,
				 		'occurrence' => $this->occurrence,
						'created' => date('Y-m-d H:i:s'),
						'user' => $this->user
				);
				 
				$query = $this->db->insert('order_occurrence', $data);
				
				if ( ! $query ) {
					$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
					return;
				} else {
					 
					$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
					return;
				}
				 
			}

	}

	
	public function TotalOrderOccurrence(){
		
		$sql = "SELECT count(*) as total FROM order_occurrence WHERE store_id = {$this->store_id}";
		
		$query = $this->db->query( $sql);
		$total =  $query->fetch(PDO::FETCH_ASSOC);
		return $total['total'];
		
	}
	
	
	public function ListOrderOccurrence()
	{
		$query = $this->db->query("SELECT * FROM `order_occurrence`  WHERE `store_id` = {$this->store_id} ORDER BY created DESC 
				LIMIT {$this->linha_inicial}, {$this->records}");

		if ( ! $query ) {
			return array();
		}
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		 

		return $res;

	}

	public function GetOrderOccurrenceFilter()
	{
	
		$where_fields = "";
		$values = array();
		$class_vars = get_class_vars(get_class($this));
		foreach($class_vars as $key => $value){
			if(!empty($this->{$key})){
				switch($key){
					case 'store_id': $where_fields .= "order_occurrence.{$key} = {$this->$key} AND ";break;
					case 'customer_id': $where_fields .= "order_occurrence.{$key} = {$this->$key} AND ";break;
					case 'order_id': $where_fields .= "order_occurrence.{$key} = {$this->$key} AND ";break;
					case 'pedido_id': $where_fields .= "order_occurrence.{$key} LIKE '".trim($this->$key)."' AND ";break;
				}
			}
		}
		
		$where_fields = substr($where_fields, 0,-4);
		
		return $where_fields;
	}
	
	public function GetOrderOccurrence()
	{
		$where_fields = $this->GetOrderOccurrenceFilter();
	
	
		$sql = "SELECT * FROM `order_occurrence` WHERE {$where_fields}
		ORDER BY order_occurrence.created DESC
		LIMIT {$this->linha_inicial}, " . $this->records.";";
	
		$query = $this->db->query($sql);
		if ( ! $query ) {
			return array();
		}
	
	
		return $query->fetchAll(PDO::FETCH_ASSOC);
	
	}

	public function Load()
	{
		if ( in_array('edit', $this->parametros )) {

			$key = array_search('edit', $this->parametros);

			$id = get_next($this->parametros, $key);

			$query = $this->db->query('SELECT * FROM order_occurrence WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );

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
		if ( in_array('del', $this->parametros )) {

			$key = array_search('del', $this->parametros);

			$id = get_next($this->parametros, $key);

			$query = $this->db->query('DELETE FROM order_occurrence WHERE store_id = ? AND  `id`= ?', array($this->store_id, $id ) );

			if ( ! $query ) {
				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
				return;
			}


		} else {

			return;

		}

	}

}