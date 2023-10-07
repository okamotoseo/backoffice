<?php
class SuscriptionsController extends MainController
{
    
    

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
    }

    public function Suscriptions() 
	{
	    
	    
	    require_once  '/var/www/html/app_mvc/vendor/autoload.php';
	    
	    MercadoPago\SDK::initialize();
	    $config = MercadoPago\SDK::config(); 
	    
	    pre($config);
	    
	    $config->set('TEST-8416121659698012-011706-d38b416795d5e17f51b8c9bfcc1c6ca5-25710329', 'TEST-8416121659698012-011706-d38b416795d5e17f51b8c9bfcc1c6ca5-25710329');
	    
	    $plan = new MercadoPago\Plan();
	    
	    $plan->description = "Monthly premium package";
	    $plan->auto_recurring = array(
	        "frequency" => 1,
	        "frequency_type" => "months",
	        "transaction_amount" => 200
	    );
	    
	    $plan->save();
	    
	    echo pre ($plan);die;
	    
	    $this->title = 'Configuração  Mercadopago: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
	    
	    $SuscriptionsModel = parent::load_module_model('Mercadopago/Models/Suscriptions/SuscriptionsModel');
	    
	    $SuscriptionsModel->Load();
	    
	    if($SuscriptionsModel->ValidateForm()){
	        
	        $SuscriptionsModel->Save();
	        
	        $SuscriptionsModel->Load();
	    }
	    
        require ABSPATH . "/Modules/Mercadopago/Views/Suscriptions/SuscriptionsView.php";
        
	}

}
?>