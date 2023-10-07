 <?php

class LoginController extends MainController
{

	/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = false;
	
	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required = 'any';
	
	
    public function index() {
    	
		$this->title = 'Login';
// 		pre($_REQUEST);die;

		if (  $this->logged_in ) {
			echo '<meta http-equiv="Refresh" content="0; url=' .  HOME_URI . '/Home/Dashboard' . '">';
			echo '<script type="text/javascript">window.location.href = "' .  HOME_URI . '/Home/Dashboard' . '";</script>';
		}else{
			require ABSPATH . '/Views/Login/LoginView.php';
		}
		return ;
 
    }
    
    
    
 
}