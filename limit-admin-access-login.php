<?php
/*
Plugin Name: Limit wp-admin access
Plugin URI: http://www.drmagu.com/using-wordpress-for-simple-websites-plugin-684.htm
Description: Limits access to wp-admin and wp-login.php also provides a sign-in shortcut
Version: 0.9.4
Author: DrMagu
Author URI: http://www.drmagu.com
*/
namespace drmagu\limit_admin_access_login;

/*
 * Main Plugin Class 
 */
 
class Main {
	
	public function	__construct() {
		$this->main();
	}
	
	private function main() {
		require_once(__DIR__.'/includes/LimitAdminAccess.class.php');
		require_once(__DIR__.'/includes/LoginController.class.php');
		require_once(__DIR__.'/includes/LoginModel.class.php');
		require_once(__DIR__.'/includes/LoginView.class.php');

		new LimitAdminAccess();

		$dbs_model = new LoginModel($_POST);
		$dbs_view = new LoginView($dbs_model);
		new LoginController($_POST, $dbs_view, $dbs_model);
	}
 	
}

new Main();
