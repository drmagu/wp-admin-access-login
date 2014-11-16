<?php
/*
Plugin Name: Limit wp-admin access
Plugin URI: http://www.drmagu.com/using-wordpress-for-simple-websites-plugin-684.htm
Description: Limits access to wp-admin and wp-login.php also provides a sign-in shortcut
Version: 0.9
Author: DrMagu
Author URI: http://www.drmagu.com
*/

add_action('login_head', 'dbs_no_wp_login' );
add_action('admin_init', 'dbs_restrict_admin_with_redirect');
add_action('init', 'dbs_disable_adminbar' );

/*
* ADMIN BAR 
* returns: Removes Admin bar for all Except ADMIN
*/			
function dbs_disable_adminbar(){
	global $wpdb, $current_user, $user;
	
	if(!is_admin() && !current_user_can('manage_options')){
		wp_dequeue_style('admin-bar');
		wp_dequeue_script('admin-bar');
		add_filter('show_admin_bar', '__return_false');
	}
}

/*
* Restrict access to backend for all except ADMIN
* redirects users to home page
*/
function dbs_restrict_admin_with_redirect() {
	global $wpdb, $wp_query;
	
	if(!current_user_can('manage_options') && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php') {
		wp_redirect(site_url() ); 
		exit;
	}
}

/*
* Redirect All trying to access native wp-login pages directly
*
*/
function dbs_no_wp_login() {
			wp_redirect(site_url());
			exit;
}

// home page on logout
add_action('wp_logout','dbs_go_home');
function dbs_go_home(){
  wp_redirect( home_url() );
  exit();
}

// Handle user login
// user login form
function dbs_login_form() {
	$output = '';
	if(!is_user_logged_in()) {
		
		global $dbs_load_css;
		
		// set this to true so the CSS is loaded
		$dbs_load_css = true;
		
		$output = dbs_login_form_fields();
	} else {
		// could show some logged in user info here
		global $current_user;
		get_currentuserinfo();
		$output = 'You are currently signed in as "<span style="color:#0A0">'.$current_user->user_login.'</span>"';
	}
	return $output;
}
add_shortcode('login_form', 'dbs_login_form');

// login form fields
function dbs_login_form_fields() {
		
	ob_start(); ?>
    <?php if (false) { ?>
		<h3 class="dbs_header"><?php _e('Login'); ?></h3>
	<?php } ?>	
		
		<?php
		// show any error messages after form submission
		dbs_show_error_messages(); ?>
		
		<form id="dbs_login_form"  class="dbs_form" action="" method="post">
			<fieldset>
				<p>
					<label for="dbs_slname">User Name</label>
					<input name="dbs_slname" id="dbs_slname" class="required" type="text"/>
				</p>
				<p>
					<label for="dbs_user_pass">Password</label>
					<input name="dbs_user_pass" id="dbs_user_pass" class="required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="dbs_action" value="login" />
					<input type="hidden" name="dbs_login_nonce" value="<?php echo wp_create_nonce('dbs-login-nonce'); ?>"/>
					<input id="dbs_login_submit" type="submit" value="Login"/>
				</p>
			</fieldset>
		</form>
        
	<?php
	return ob_get_clean();
}

// logs a member in after submitting a form
function dbs_login_member() {
//	echo '<pre>';print_r($_POST);echo '</pre>';
	if(isset($_POST['dbs_slname']) && wp_verify_nonce($_POST['dbs_login_nonce'], 'dbs-login-nonce')) {
				
		$user_login_array = explode(" ", $_POST['dbs_slname']);
		if (sizeof($user_login_array) == 2) 
			$user_login = strtolower($user_login_array[0]).".".strtolower($user_login_array[1]);
		else
			$user_login = $_POST['dbs_slname'];
		// this returns the user ID and other info from the user name
		$user = get_user_by('login',$user_login);
		
		if(!$user) {
			// if the user name doesn't exist
			dbs_errors()->add('empty_username', __('Invalid username'));
		}
		
		if(!isset($_POST['dbs_user_pass']) || $_POST['dbs_user_pass'] == '') {
			// if no password was entered
			dbs_errors()->add('empty_password', __('Please enter a password'));
		}
		
		if ($user) {
			// check the user's login with their password
			if(!wp_check_password($_POST['dbs_user_pass'], $user->user_pass, $user->ID)) {
				// if the password is incorrect for the specified user
				dbs_errors()->add('empty_password', __('Incorrect password'));
			}
		}
		
		// retrieve all error messages
		$errors = dbs_errors()->get_error_messages();
		
		// only log the user in if there are no errors
		if(empty($errors)) {
			
//			wp_setcookie($user_login, $_POST['dbs_user_pass'], true);
			wp_set_auth_cookie($user->ID, true);
			wp_set_current_user($user->ID, $user_login);	
			do_action('wp_login', $user_login);
			
			wp_redirect(home_url()); exit;
		}
	}
}
if (isset($_POST['dbs_action'])) if ($_POST['dbs_action'] == "login") add_action('init', 'dbs_login_member');

// displays error messages from form submissions
function dbs_show_error_messages() {
	if($codes = dbs_errors()->get_error_codes()) {
		echo '<div class="dbs_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = dbs_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}

// used for tracking error messages
function dbs_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
} 

// displays info messages from form submissions
function dbs_show_info_messages() {
	if($codes = dbs_info()->get_error_codes()) {
		echo '<div class="dbs_info">';
		    // Loop info codes and display messages
		   foreach($codes as $code){
		        $message = dbs_info()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Info') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}

// used for tracking info messages
function dbs_info(){
    static $wp_info; // Will hold global variable safely
    return isset($wp_info) ? $wp_info : ($wp_info = new WP_Error(null, null, null));
} 

// register our form css
function dbs_register_css() {
	wp_register_style('dbs-form-css', plugin_dir_url( __FILE__ ) . '/css/forms.css');
}
add_action('init', 'dbs_register_css');
 
// load our form css
function dbs_print_css() {
	global $dbs_load_css;
 
	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $dbs_load_css )
		return; // this means that neither short code is present, so we get out of here

	wp_print_styles('dbs-form-css');
}
add_action('wp_footer', 'dbs_print_css');

?>