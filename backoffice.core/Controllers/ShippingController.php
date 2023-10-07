<?php

class ShippingController extends MainController
{

	/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = true;
	
	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required = 'any';
	
	/**
	 * $panel
	 *
	 * Painel de controle
	 *
	 * @access public
	 */
	public $panel = 'Expedição';
	
	

	
	
	
	
    public function Send(){
        
    	$this->title = 'Envios';
    	
    	$this->menu = array("Orders" => "active", "Shipping" => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();

        $shippingSendModel = $this->load_model('Shipping/SendModel');
        
        if($shippingSendModel->ValidateForm()){
            
            $listShippingSend = $shippingSendModel->Save();
            
        }
        
        $listShippingSend = $shippingSendModel->ListShippingPacks();
        
        if(isset($shippingSendModel->id)){
            
            $listShippingSendCode = $shippingSendModel->ListShippingCode();
        }
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Shipping/SendView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    
    public function ShippingSendDetail(){
        
        $this->title = 'Informações Detalhadas da Remessa';
        
        $this->menu = array("Shipping" => "active", "Send" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $shippingSendModel = $this->load_model('Shipping/SendModel');
        
        $storeModel = $this->load_model('Admin/StoreModel');
        
        $shippingSendModel->ValidateForm();
        
        $shippingSendModel->Load();
        
        $storeModel->id = $shippingSendModel->store_id;
        
        $storeModel->Load();
        
        if(isset($shippingSendModel->id)){
            
            $listShippingSendCode = $shippingSendModel->ListShippingCode();
        }
        
        
        require ABSPATH . '/Views/_includes/header_popup.php';
        
        require ABSPATH . '/Views/Shipping/ShippingSendDetailView.php';
        
        require ABSPATH . '/Views/_includes/footer_popup.php';
        
    }
    
    
    public function Packing(){
        
        $this->panel = 'Pacote';
        
        $this->title = 'Lista';
        
        $this->menu = array("Orders" => "active", "Packing" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $pickingModel = $this->load_model('Shipping/PickingModel');
        
        $packingModel = $this->load_model('Shipping/PackingModel');
        
        $storeModel = $this->load_model('Admin/StoreModel');
        
//         if($packingModel->ValidateForm()){
            
            
//             $packingModel->Save();
            
//         }
        
//         $pickingModel->ValidateForm();

        if ( in_array('id', $this->parametros )) {
            
            $key = array_search('id', $this->parametros);
            
            $pickingId = get_next($this->parametros, $key);
            $pickingModel->id  = is_numeric($pickingId) ? $pickingId :  '';
            
            if(!empty($this->id)){
                $pickingModel->Load();
                
            }
            
        }

//         $pickingModel->id = 1152;

//         $packingModel->ValidateForm();
        
        
        
        $storeModel->id = $pickingModel->store_id;
        
        $storeModel->Load();
        
        if(isset($pickingModel->id)){
            
            $listPickingProductOrders = $pickingModel->ListPickingProductOrders();
        }
        
        $this->includes = array("js" => "/Views/js/shipping.js");
        
        require ABSPATH . '/Views/_includes/header.php';
        
        
        
        require ABSPATH . '/Views/Shipping/PackingView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    
    public function Picking(){
        
        $this->panel = 'Separação';
        
        $this->title = 'Lista de Coleta';
        
        $this->menu = array("Orders" => "active", "Picking" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $pickingModel = $this->load_model('Shipping/PickingModel');
       
        if($pickingModel->ValidateForm()){
            
            
            $pickingModel->Save();
            
        }
        
        $listPicking = $pickingModel->ListPicking();
        
        if(isset($pickingModel->id)){
            
            $listPickingProducts = $pickingModel->ListPickingProductOrders();
        }
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Shipping/PickingView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    public function PickingDetail(){
        
        $this->title = 'Informações Detalhadas da Separação';
        
        $this->menu = array("Orders" => "active", "Picking" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $pickingModel = $this->load_model('Shipping/PickingModel');
        
        $storeModel = $this->load_model('Admin/StoreModel');
        
        $pickingModel->ValidateForm();
        
        $pickingModel->Load();
        
        $storeModel->id = $pickingModel->store_id;
        
        $storeModel->Load();
        
        if(isset($pickingModel->id)){
            
            $listPickingProductOrders = $pickingModel->ListPickingProductOrders();
        }
        
        
        require ABSPATH . '/Views/_includes/header_popup.php';
        
        require ABSPATH . '/Views/Shipping/PickingDetailView.php';
        
        require ABSPATH . '/Views/_includes/footer_popup.php';
        
    }
    
    
    
    
 
}
