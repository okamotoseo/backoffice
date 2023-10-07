<?php

class ModulesController extends MainController
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
	public $panel = 'Módulos';
	
	

	public function Available() {
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = 'Configurações';
	    
	    $this->menu = array("Modules" => "active");
	    
	    $moduleModel = $this->load_model('Modules/ModulesModel');
	    
	    $modules = $moduleModel->ListModules();
	    
	    $storeModel = $this->load_model('Admin/StoreModel');
	    
	    $storeModel->id = $this->userdata['store_id'];
	    
	    $storeModel->Load();
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    require ABSPATH . '/Views/Modules/ModulesView.php';
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
	
	public function Configuration()
	{
	    
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = 'Configurações';
	    
	    $this->menu = array("Modules" => "active");
	    
	    $this->includes = array();
	    
	    $storeModel = $this->load_model('Admin/StoreModel');
	    
	    $storeModel->id = $this->userdata['store_id'];
	    
	    $storeModel->Load();
	    
	    $moduleModel = $this->load_model('Modules/ModulesModel');
	    
	    $modules = $moduleModel->ListModules();
	    
	    $module = $this->load_module_controller("{$parametros[2]}/Controllers/{$parametros[3]}/{$parametros[3]}Controller");
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    $module->{$parametros[3]}();
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}
	
	public function Seta() 
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $parametros[3] = empty($parametros[3]) ? $parametros[2] : $parametros[3] ;
	    
	    $this->panel = "Seta ERP";
	    
	    $this->title = 'Seta';
	    
	    $this->menu = array($parametros[1] => "active", $parametros[2] => "active", $parametros[3] => "active");
	    
	    $this->includes = array("js" => "/Modules/Seta/Views/js/ModuleSeta.js");
	    
	    $module = $this->load_module_controller("{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller");
	    
	    require ABSPATH . '/Views/_includes/header.php';
        
	    $this->title = $module->title;
	    
	    $module->{$parametros[3]}();
	    
	    $this->includes = isset($module->includes) ?$module->includes :  $this->includes ;
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}
	
	public function Adj()
	{
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		
		$this->panel = "Adj SIG";
		
		$this->title = 'Adj SIG';
		 
		$this->menu = array($parametros[1] => "active", 'ProductsAdj' => "active");
		
		 
		$this->includes = array("js" => "/Modules/Adj/Views/js/ModuleAdj.js");
		 
		$module = $this->load_module_controller("{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[2]}Controller");
		
		require ABSPATH . '/Views/_includes/header.php';
	
		$this->title = $module->title;
		
		$module->{$parametros[2]}();
		 
		$this->includes = isset($module->includes) ? $module->includes :  $this->includes ;
		 
		require ABSPATH . '/Views/_includes/footer.php';
		 
		 
	}
	
	public function Onbi()
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->panel = "Onbi";
	    
	    $this->title = 'Onbi Ecommerce';
	    
	    $this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	    
	    $this->includes = array("js" => "/Modules/Onbi/Views/js/ModuleOnbi.js");
	    
	    require ABSPATH.'/Modules/Onbi/Class/class-Soap.php';
	    
	    $loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
	    
	    $module = $this->load_module_controller($loadController);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
// 	    $this->title = $module->title;
	    
	    $module->{$parametros[3]}();
	    
	    $this->includes = isset($module->includes) ?$module->includes :  $this->includes ;
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}

	public function Mercadolivre()
	{
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();

		$this->panel = "Mercadolivre";
		
		$this->title = 'Mercadolivre';
		 
		$this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
		
		$this->includes = array("js" => "/Modules/Mercadolivre/Views/js/Mercadolivre.js");
		
		require ABSPATH.'/Modules/Mercadolivre/Library/php-sdk-master/Meli/meli.php';
		
		$loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
		
		$module = $this->load_module_controller($loadController);
		
		require ABSPATH . '/Views/_includes/header.php';
		 
		$module->{$parametros[3]}();
		 
		require ABSPATH . '/Views/_includes/footer.php';
		 
		 
	}
	
	public function Mercadopago()
	{
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		$this->panel = "Mercadopago";
	
		$this->title = 'Mercadopago';
			
		$this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	
		$loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
	
		$module = $this->load_module_controller($loadController);
	
		require ABSPATH . '/Views/_includes/header.php';
			
		$module->{$parametros[3]}();
			
		require ABSPATH . '/Views/_includes/footer.php';
			
			
	}
	
	public function Sysemp()
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $parametros[3] = empty($parametros[3]) ? $parametros[2] : $parametros[3] ;
	    
	    $this->title = "Sysemp";
	    
	    $this->panel = "Sysemp ERP";
	    
	    $this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	    
	    $this->includes = array("js" => "/Modules/Sysemp/Views/js/ModuleSeta.js");
	    
	    $module = $this->load_module_controller("{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller");
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    $this->title = $module->title;
	    
	    $module->{$parametros[3]}();
	    
	    $this->includes = isset($module->includes) ?$module->includes :  $this->includes ;
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}
	
	public function Amazon()
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = "Amazon";
	    
	    $this->panel = "Amazon";
	    
	    $this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	    
	    $this->includes = array("js" => "/Modules/Amazon/Views/js/Amazon.js");
	    
	    $loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
	    
	    $module = $this->load_module_controller($loadController);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    $module->{$parametros[3]}();
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}
	
	public function Skyhub()
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = "Skyhub";
	    
	    $this->panel = "Skyhub";
	    
	    $this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	    
	    $this->includes = array("js" => "/Modules/Skyhub/Views/js/ModuleSkyhub.js");
	    
	    if($parametros[2] == 'Shipments'){
	       require ABSPATH.'/vendor/autoload.php';
	       
	    }
	    $loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
	    
	    $module = $this->load_module_controller($loadController);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    $module->{$parametros[3]}();
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}
	
	public function Viavarejo()
	{
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		 
		$this->panel = "Viavarejo";
		 
		$this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
		 
		$this->includes = array("js" => "/Modules/Viavarejo/Views/js/Viavarejo.js");

		$loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
		 
		$module = $this->load_module_controller($loadController);
		 
		require ABSPATH . '/Views/_includes/header.php';
		 
		$module->{$parametros[3]}();
		 
		require ABSPATH . '/Views/_includes/footer.php';
		 
	}
	
	public function Tray()
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = "Tray";
	    
	    $this->panel = "Tray";
	    
	    $this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	    
	    $this->includes = array("js" => "/Modules/Tray/Views/js/ModuleTray.js");

	    $loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
	    
	    $module = $this->load_module_controller($loadController);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    $module->{$parametros[3]}();
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}
	public function Vtex()
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->panel = "Vtex";
	    
	    $this->title = 'Vtex';
	    
	    $this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	    
	    $this->includes = array("js" => "/Modules/Vtex/Views/js/Vtex.js");
	    
	    $loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
	    
	    $module = $this->load_module_controller($loadController);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    $module->{$parametros[3]}();
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	}
	
	public function Google()
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = "Google XML";
	    
	    $this->panel = "Google";
	    
	    $this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	    
	    
	    $this->includes = array("js" => "/Modules/Google/Views/js/ModuleGoogle.js");
	    
	    $loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";

	    $module = $this->load_module_controller($loadController);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    $module->{$parametros[3]}();
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}
	
	public function Shopee()
	{
	    $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	    
	    $this->title = "Shopee";
	    
	    $this->panel = "Shopee";
	    
	    $this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
	    
	    $this->includes = array("js" => "/Modules/Shopee/Views/js/ModuleShopee.js");
	    
	    $loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
	    
	    $module = $this->load_module_controller($loadController);
	    
	    require ABSPATH . '/Views/_includes/header.php';
	    
	    $module->{$parametros[3]}();
	    
	    require ABSPATH . '/Views/_includes/footer.php';
	    
	    
	}
	
	public function Marketplace()
	{
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		 
		$this->title = "Marketplace"; 
		
		$this->panel = "Marketplace Sysplace";
		 
		$this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
		 
		$this->includes = array("js" => "/Modules/Marketplace/Views/js/Marketplace.js");
		 
		$loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
		 
		$module = $this->load_module_controller($loadController);
		 
		require ABSPATH . '/Views/_includes/header.php';
		 
		$module->{$parametros[3]}();
		 
		require ABSPATH . '/Views/_includes/footer.php';
		 
		 
	}
	
	public function Magento2()
	{
		
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		 
		$this->title = "Magento2";
		 		
		$this->panel = "Magento2 Ecommerce";
		 
		$this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
		 
		$this->includes = array("js" => "/Modules/Magento2/Views/js/ModuleMagento2.js");
		 
		require ABSPATH.'/Modules/Magento2/Class/class-REST.php';
		 
		$loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
		 
		$module = $this->load_module_controller($loadController);
		 
		require ABSPATH . '/Views/_includes/header.php';
		 
		// 	    $this->title = $module->title;
		 
		$module->{$parametros[3]}();
		 
		$this->includes = isset($module->includes) ?$module->includes :  $this->includes ;
		 
		require ABSPATH . '/Views/_includes/footer.php';
		 
		 
	}
	
	public function Plugnotas()
	{
	
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		
		$this->title = "PlugNotas";
			
		$this->panel = "PlugNotas Fiscal";
			
		$this->menu = array($parametros[1] => "active",  $parametros[3] => "active");
			
// 		$this->includes = array("js" => "/Modules/Magento2/Views/js/ModuleMagento2.js");
			
// 		require ABSPATH.'/Modules/Magento2/Class/class-REST.php';
			
		$loadController = "{$parametros[1]}/Controllers/{$parametros[2]}/{$parametros[3]}Controller";
			
		$module = $this->load_module_controller($loadController);
			
		require ABSPATH . '/Views/_includes/header.php';
			
		// 	    $this->title = $module->title;
			
		$module->{$parametros[3]}();
			
		$this->includes = isset($module->includes) ?$module->includes :  $this->includes ;
			
		require ABSPATH . '/Views/_includes/footer.php';
			
			
	}
	
}
?>
