<?php
/*
Plugin Name: Limit wp-admin access
Plugin URI: http://www.drmagu.com/using-wordpress-for-simple-websites-plugin-684.htm
Description: Limits access to wp-admin and wp-login.php also provides a sign-in shortcut
Version: 0.9.2
Author: DrMagu
Author URI: http://www.drmagu.com
*/

require_once(__DIR__.'/includes/LimitAdminAccess.class.php');
require_once(__DIR__.'/includes/LoginForm.class.php');

$limit_admin_access = new LimitAdminAccess();
$login_form = new LoginForm($_POST);


