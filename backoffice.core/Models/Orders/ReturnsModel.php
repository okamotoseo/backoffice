<?php
/**
 * Modelo para gerenciar devoluções
*
*/
class ReturnsModel extends MainModel
{


	public $id;

	public $store_id;

	public $shipping_id;
	
	public $id_nota_saida; 
	
	public $fiscal_key;
	
	public $pedido_id;
	
	public $order_id;
	
	public $check_in;
	
	public $type_return; 
	
	public $validates;
	
	public $reasons;
	
	public $created;
	
	public $updated;
	
	public $user;
	
	public $status;
	
	public $checked;
	
	public $user_check_in; 
	
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

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty ( $_POST['order-returns'] ) ) {
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


		$query = $this->db->query('SELECT id FROM `order_returns`  WHERE `store_id` = ?
    				AND order_id = ?',
				array($this->store_id, friendlyText($this->order_id))
				);
		
		$res = $query->fetch(PDO::FETCH_ASSOC);
		
		if(isset($res['id'])){
			
			$this->id =  $res['id'];
			
			$data = array(
					'shipping_id' => $this->shipping_id,
					'id_nota_saida' => $this->id_nota_saida,
					'fiscal_key' => $this->fiscal_key,
					'check_in' => $this->check_in,
					'type_return' => $this->type_return,
					'validates' => $this->validates,
					'reasons' => $this->reasons,
					'updated' => date('Y-m-d H:i:s'),
					'user_check_in' => $this->user_check_in
			);

			$query = $this->db->update('order_returns', array('store_id', 'id'), array($this->store_id, $res['id']), $data);
			 
			if ( ! $query ) {
				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
				return;
			} else {
				 
				$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro atualizado com sucesso.</div>';
				
				return ;
			}
		} else {
			
			 $data = array(
					'store_id' => $this->store_id,
					'shipping_id' => $this->shipping_id,
					'id_nota_saida' => $this->id_nota_saida,
					'fiscal_key' => $this->fiscal_key,
					'pedido_id' => $this->pedido_id,
			 		'order_id' => $this->order_id,
					'check_in' => $this->check_in,
					'type_return' => $this->type_return,
					'validates' => $this->validates,
					'reasons' => $this->reasons,
			 		'status' => $this->status,
					'created' => date('Y-m-d H:i:s'),
			 		'updated' => date('Y-m-d H:i:s'),
					'user' => $this->user
			);
			 
			$query = $this->db->insert('order_returns', $data);
			
			if ( ! $query ) {
				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Data has not been sent.</div>';
				return;
			} else {
				 
				$this->form_msg = '<div class="alert alert-success alert-dismissable">Registro cadastrado com sucesso.</div>';
				$this->id =  $this->db->last_id;
				return;
			}
				 

		}


	}

	
	public function TotalOrderReturns(){
		
		$sql = "SELECT count(*) as total FROM order_returns WHERE store_id = {$this->store_id}";
		
		$query = $this->db->query( $sql);
		$total =  $query->fetch(PDO::FETCH_ASSOC);
		return $total['total'];
		
	}
	
	
	public function ListOrderReturns()
	{
		$query = $this->db->query("SELECT * FROM `order_returns`  WHERE `store_id` = {$this->store_id} ORDER BY created DESC 
				LIMIT {$this->linha_inicial}, {$this->records}");

		if ( ! $query ) {
			return array();
		}
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		 

		return $res;

	}

	public function GetOrderReturnsFilter()
	{
	
		$where_fields = "";
		$values = array();
		$class_vars = get_class_vars(get_class($this));
		foreach($class_vars as $key => $value){
			if(!empty($this->{$key})){
				switch($key){
					case 'store_id': $where_fields .= "order_returns.{$key} = {$this->$key} AND ";break;
					case 'shipping_id': $where_fields .= "order_returns.{$key} LIKE '".trim($this->$key)."' AND ";break;
					case 'pedido_id': $where_fields .= "order_returns.{$key} LIKE '".trim($this->$key)."' AND ";break;
				}
			}
		}
		
		$where_fields = substr($where_fields, 0,-4);
		
		return $where_fields;
	}
	
	public function GetOrderReturns()
	{
		$where_fields = $this->GetOrderReturnsFilter();
	
	
		$sql = "SELECT * FROM `order_returns` WHERE {$where_fields}
		ORDER BY order_returns.created DESC
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

			$query = $this->db->query('SELECT * FROM order_returns WHERE store_id = ? AND `id`= ?', array($this->store_id, $id ) );

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

			$query = $this->db->query('DELETE FROM order_returns WHERE store_id = ? AND  `id`= ?', array($this->store_id, $id ) );

			if ( ! $query ) {
				$this->form_msg = '<div class="alert alert-danger alert-dismissable">Internal error. Não foi possivel deletar o registro.</div>';
				return;
			}


		} else {

			return;

		}

	}

}