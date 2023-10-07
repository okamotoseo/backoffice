<?php

class OrderRestModel extends Pluggto 
{
    
    public $db;
    public $store_id;
	public $id;
	public $external;
	public $original_id;
	public $channel;
	public $status;
	public $created;
	public $total;
	public $subtotal;
	public $shipping;
	public $discount;
	public $receiver_name;
	public $receiver_lastname;
	public $receiver_address;
	public $receiver_address_number;
	public $receiver_zipcode;
	public $receiver_address_complement;
	public $receiver_address_reference;
	public $receiver_additional_info;
	public $receiver_neighborhood;
	public $receiver_city;
	public $receiver_state;
	public $receiver_country;
	public $receiver_phone_area;
	public $receiver_phone;
	public $receiver_phone2_area;
	public $receiver_phone2;
	public $receiver_email;
	public $receiver_schedule_date;
	public $receiver_schedule_period;
	public $delivery_type;
	public $payer_name;
	public $payer_lastname;
	public $payer_address;
	public $payer_address_number;
	public $payer_zipcode;
	public $payer_address_complement;
	public $payer_address_reference;
	public $payer_additional_info;
	public $payer_neighborhood;
	public $payer_city;
	public $payer_state;
	public $payer_country;
	public $payer_phone_area;
	public $payer_phone;
	public $payer_phone2_area;
	public $payer_phone2;
	public $payer_email;
	public $payer_cpf;
	public $payer_cnpj;
	public $payer_razao_social;
	public $payer_ie;
	public $payer_gender;
	public $shipments;
	public $items;
	public $access_token;
	public $code;
	public $pluggRequest;
	public $limit = 50;
	public $next;
	
	protected $rules = [
	    'status' => [
	        'required' => true,
	        'allowed'  => [
	            'pending', 'paid', 'approved', 'waiting_invoice', 'invoiced', 'invoice_error', 'shipping_informed', 'shipped', 'shipping_error', 'delivered', 'canceled', 'under_review'
	        ],
	        'type'     => 'string'
	    ]
	];


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
	    if(isset($this->store_id)){
	        
	        parent::__construct($this->db, $this->store_id);
	        
	    }
	    
	}

	public function getDataPreparedToPlugg()
	{
		$response = [
			'external' => $this->external,
			'status' => $this->status,
		    'created' => $this->created,
			'channel' => $this->channel,
			'original_id' => $this->original_id,
			'total' => $this->total,
			'subtotal' => $this->subtotal,
			'shipping' => $this->shipping,
			'discount' => $this->discount,
			'receiver_name' => $this->receiver_name,
			'receiver_lastname' => $this->receiver_lastname,
			'receiver_address' => $this->receiver_address,
			'receiver_address_number' => $this->receiver_address_number,
			'receiver_zipcode' => $this->receiver_zipcode,
			'receiver_address_complement' => $this->receiver_address_complement,
			'receiver_address_reference' => $this->receiver_address_reference,
			'receiver_additional_info' => $this->receiver_additional_info,
			'receiver_neighborhood' => $this->receiver_neighborhood,
			'receiver_city' => $this->receiver_city,
			'receiver_state' => $this->receiver_state,
			'receiver_country' => $this->receiver_country,
			'receiver_phone_area' => $this->receiver_phone_area,
			'receiver_phone' => $this->receiver_phone,
			'receiver_phone2_area' => $this->receiver_phone2_area,
			'receiver_phone2' => $this->receiver_phone2,
			'receiver_email' => $this->receiver_email,
			'receiver_schedule_date' => $this->receiver_schedule_date,
			'receiver_schedule_period' => $this->receiver_schedule_period,
			'delivery_type' => $this->delivery_type,
			'payer_name' => $this->payer_name,
			'payer_lastname' => $this->payer_lastname,
			'payer_address' => $this->payer_address,
			'payer_address_number' => $this->payer_address_number,
			'payer_zipcode' => $this->payer_zipcode,
			'payer_address_complement' => $this->payer_address_complement,
			'payer_address_reference' => $this->payer_address_reference,
			'payer_additional_info' => $this->payer_additional_info,
			'payer_neighborhood' => $this->payer_neighborhood,
			'payer_city' => $this->payer_city,
			'payer_state' => $this->payer_state,
			'payer_country' => $this->payer_country,
			'payer_phone_area' => $this->payer_phone_area,
			'payer_phone' => $this->payer_phone,
			'payer_phone2_area' => $this->payer_phone2_area,
			'payer_phone2' => $this->payer_phone2,
			'payer_email' => $this->payer_email,
			'payer_cpf' => $this->payer_cpf,
			'payer_cnpj' => $this->payer_cnpj,
			'payer_razao_social' => $this->payer_razao_social,
			'payer_ie' => $this->payer_ie,
			'payer_gender' => $this->payer_gender,
			'items' => $this->items,
		    'limit' => $this->limit,
		    'next' => $this->next
		];
		
		if(isset($this->shipments) && !empty($this->shipments))
			$response['shipments'] = $this->shipments;

		$this->validate($response);

		$this->removeFieldsNull($response);
		pre($response);
		return $response;
	}
	
	public function validate($input)
	{
	    foreach ($input as $key => $value)
	    {
	        if (!isset($this->rules[$key]) && empty($this->rules[$key]))
	            continue;
	            
	            if (isset($this->rules[$key]['type']) && !empty($this->rules[$key]['type']))
	                $this->validateTypeField($key, $value);
	                
	                if (isset($this->rules[$key]['allowed']) && !empty($this->rules[$key]))
	                    $this->validateFieldsAlloweds($key, $value);
	                    
	                    if ($this->rules[$key]['required'] && empty($value))
	                        throw new Exception("The {$key} is required");
	    }
	}
	
	public function validateTypeField($key, $value)
	{
	    if ($this->rules[$key]['type'] == 'array')
	        $response = is_array($value);
	        
        if ($this->rules[$key]['type'] == 'string')
            $response = is_string($value);
            
        if ($this->rules[$key]['type'] == 'integer')
            $response = is_integer($value);
            
        if (!$response)
            throw new Exception("The {$key} contain a type not allowed expected " . $this->rules[$key]['type']);
            
        return true;
	}
	
	public function validateFieldsAlloweds($key, $value)
	{
	    if (!in_array($value, $this->rules[$key]['allowed']))
	        throw new Exception("The {$key} contain a value not allowed");
	        
	        return true;
	}
	
	public function removeFieldsNull(&$input)
	{
	    foreach ($input as $key => $value)
	    {
	        if (empty($value) || !isset($value))
	            unset($input[$key]);
	    }
	}
	public function list()
	{
	    $pluggRequest = new Pluggto($this->db, $this->store_id);
	    
	    $params = $this->getDataPreparedToPlugg();
	    
	    $url = $this->api_host."/orders/";
	    
	    $method = "get";
	    
	    if (empty($this->access_token)) {
	        $this->access_token = $pluggRequest->getAccesstoken($this->code);
	    }
	    
	    $params['access_token'] = $this->access_token;
	    $data = $pluggRequest->sendRequest($method, $url, $params);
	    return $data;
	}
	
	public function get()
	{
	    $pluggRequest = new Pluggto($this->db, $this->store_id);
	    
	    $url = $this->api_host."/orders/";
	    
	    $method = "get";
	    
	    if (empty($this->access_token)) {
	        
	        $this->access_token = $pluggRequest->getAccesstoken($this->code);
	        
	    }
	    
	    $params['access_token'] = $this->access_token;
	    
	    $data = $pluggRequest->sendRequest($method, $url, $params);
	    
	    return $data;
	}



	public function edit()
	{
	    $params = $this->getDataPreparedToPlugg();

	    $url = "http://api.plugg.to/orders/" . trim($this->id);
	    
	    $method = "put";
	    
	    if (empty($this->access_token)) {
	    	$this->access_token = $this->pluggRequest->getAccesstoken($this->code);    	
	    }
	    
	    $url = $url . "?access_token=" . $this->access_token;
	    
	    $data = $this->pluggRequest->sendRequest($method, $url, $params);
	    
	    return $data;
	}

	public function add()
	{
	    $params = $this->getDataPreparedToPlugg();

	    $url = "http://api.plugg.to/orders";
	    
	    $method = "post";
	    
	    if (empty($this->access_token)) {
	    	$this->access_token = $this->pluggRequest->getAccesstoken($this->code);    	
	    }
	    
	    $url = $url . "?access_token=" . $this->access_token;
	    
	    $data = $this->pluggRequest->sendRequest($method, $url, $params, "orders");
	    
	    return $data;
	}

}