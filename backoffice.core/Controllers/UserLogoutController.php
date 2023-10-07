<?php

class UserLogoutController extends MainController
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
	

	public function out() {
		$this->title = 'Logout';
		
		if ( $this->logged_in ) {
// 			echo "logout";die;
			$this->logout(true);
// 			echo '<meta http-equiv="Refresh" content="0; url=' .  HOME_URI . '/Login' . '">';
// 			echo '<script type="text/javascript">window.location.href = "' .  HOME_URI . '/Login' . '";</script>';
			return;
		}
		
		

	}

}