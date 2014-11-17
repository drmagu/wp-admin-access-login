<?php
/*
Plugin Name: Limit wp-admin access
Plugin URI: http://www.drmagu.com/using-wordpress-for-simple-websites-plugin-684.htm
Description: Limits access to wp-admin and wp-login.php also provides a sign-in shortcut
Version: 0.9.3
Author: DrMagu
Author URI: http://www.drmagu.com
*/

require_once(__DIR__.'/includes/LimitAdminAccess.class.php');
//require_once(__DIR__.'/includes/LoginForm.class.php');
require_once(__DIR__.'/includes/Controller.class.php');
require_once(__DIR__.'/includes/LoginModel.class.php');
require_once(__DIR__.'/includes/LoginView.class.php');

new LimitAdminAccess();
//new LoginForm($_POST);
$dbs_model = new LoginModel($_POST);
$dbs_view = new LoginView($dbs_model);
new Controller($_POST, $dbs_view, $dbs_model);


