<?php

class FeedController extends MainController
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
	public $panel = 'Feed';
	
	

	
    public function ManageFeed(){
    	
    	$this->title = 'Gerenciar Feed';
    	
    	$this->menu = array("Feed" => "active", "ManageFeed" => "active");
    	
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
        
        $feedModel = $this->load_model('Feed/ManageFeedModel');
//         pre($_REQUEST);die;
        if($feedModel->ValidateForm()){
            $feedModel->Save();
        
        
        }
//         pre($feedModel->id);die;
        $list = $feedModel->ListFeed();
        
        $categoryModel = $this->load_model('Products/CategoryModel');
         
        $listCategory = $categoryModel->ListCategory();
        
        $brandsModel = $this->load_model('Products/BrandsModel');
        
        $listBrands = $brandsModel->ListBrands();
        
        require ABSPATH . '/Views/_includes/header.php';
        
        require ABSPATH . '/Views/Feed/ManageFeedView.php';
        
        require ABSPATH . '/Views/_includes/footer.php';
        
    }
    
 
}
