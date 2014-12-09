<?php
/*
Plugin Name: Limit wp-admin access
Plugin URI: http://www.drmagu.com/using-wordpress-for-simple-websites-plugin-684.htm
Description: Limits access to wp-admin and wp-login.php also provides a sign-in shortcut
Version: 1.0.5
Author: DrMagu
Author URI: http://www.drmagu.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
namespace drmagu\limit_admin_access_login;

/* only allow WP environment */
defined('ABSPATH') or die("Oops.  Not here buddy!");
/*
 * Check version of WordPress and PHP
 * The VersionCheck class is not namespaced
 */
 
require_once (__DIR__.'/classes/VersionCheck.class.php');
new \VersionCheck(__FILE__, '3.5', '5.3');
		
/*
 * Setup the autoloader
 * Looks for class files in the "classes/" directory
 */
 
require_once (__DIR__.'/classes/Autoloader.class.php');
new Autoloader(__DIR__.'/classes/');

/*
 * Main Plugin Class 
 */
 
class Main {
	
	public function	__construct() {
		$this->main();
	}
		
	private function main() {
		/* limit the access */
		new LimitAdminAccess();

		/* login & logout functionality */
		$dbs_model = new LoginModel($_POST);
		$dbs_view = new LoginView($dbs_model, plugin_dir_url( __FILE__ ));
		new LoginController($_POST, $dbs_view, $dbs_model);
	}
 	
}

new Main();
