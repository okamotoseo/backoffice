<?php
class PricesController extends MainController
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
    public $panel = 'Preços';
    
    
    
    
    
    public function Price()
    {
        
        $this->title = 'Preços';
        
        $this->menu = array("Prices" => "active", "Price" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $priceModel = $this->load_model('Prices/RulesModel');
        
        if($priceModel->ValidateForm()){
            
            $priceModel->Save();
            
        }
        
        $list = $brandsModel->ListPrices();
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Prices/PricesView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
    public function Rules()
    {
        
        $this->title = 'Regras de Preços';
        
        $this->menu = array("Prices" => "active", "Rules" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $priceModel = $this->load_model('Prices/RulesModel');
        
        
        if($priceModel->ValidateForm()){
            
            $priceModel->Save();
            
        }
        
        $list = $priceModel->ListPriceRules();
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Prices/RulesView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
    }
    
    public function PriceManager(){
        
        $this->title = 'Atualizar Preços';
        
        $this->menu = array("Prices" => "active", "PriceManager" => "active");
        
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $brandsModel = $this->load_model('Products/BrandsModel');
        
        $brands = $brandsModel->ListBrands();
        
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Prices/PriceManagerView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
}
?>