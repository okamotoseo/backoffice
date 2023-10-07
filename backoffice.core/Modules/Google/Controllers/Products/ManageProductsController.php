<?php
class ManageProductsController extends MainController
{
    
    
    public function __construct($db = false, $controller = null)
    {
        
        $this->db = $db;
        
        $this->parametros = $controller->parametros;
        
        $this->userdata = $controller->userdata;
        
        $this->store_id = $controller->userdata['store_id'];
        
        
    }
    
    public function ManageProducts()
    {
        
        $this->title = 'Integração de Produtos Google: <b>'.$this->userdata['stores'][$this->userdata['store_id']]."</b>";
        
        $productsModel = parent::load_module_model('Google/Models/Products/ProductsModel');
        
        
        if($productsModel->ValidateForm()){
            
            $products = $productsModel->GetProducts();
            
            $totalReg = $productsModel->TotalGetProducts();
            
        }else{
            
            $products = $productsModel->ListProducts();
            
            $totalReg = $productsModel->TotalProducts();
        }
        
        require ABSPATH . "/Modules/Google/Views/Products/ManageProductsView.php";
        
        
    }
    
}
?>