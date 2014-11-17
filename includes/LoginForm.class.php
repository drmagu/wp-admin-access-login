<?php 

/*
* Handle user login with a login form
*
*/
class LoginForm {
	private $dbs_load_css = false;
	private $post_array;
	
	public function __construct($post_array) {
		
		$this->post_array = $post_array; 
		$this->init();
	}

	private function init() {
		add_shortcode('login_form', [ $this,'dbs_login_form' ] );
		if (isset($this->post_array['dbs_action'])) if ($this->post_array['dbs_action'] == "login") {
			add_action( 'init', [$this, 'dbs_login_member'] );
		}
		// register our form css
		add_action('init', [$this, 'dbs_register_css'] );
		add_action('wp_footer', [$this, 'dbs_print_css'] );
	}
	
	public function dbs_login_form() {
		$output = '';
		if(!is_user_logged_in()) {
						
			// set this to true so the CSS is loaded
			$this->dbs_load_css = true;
			
			$output = $this->dbs_login_form_fields();
		} else {
			// could show some logged in user info here
			$current_user = wp_get_current_user();
			$output = 'You are currently signed in as "<span style="color:#0A0">'.$current_user->user_login.'</span>"';
			$output .= '<br /><a href="/wp-login.php?action=logout">'.'Click here to logout'.'</a>';
		}
		return $output;

	}
	
	// register our form css
	public function dbs_register_css() {
		wp_register_style('dbs-form-css', plugin_dir_url( __FILE__ ) . '/css/forms.css');
	}
	
	// load our form css
	public function dbs_print_css() {
	 
		// this variable is set to TRUE if the short code is used on a page/post
		if ( ! $this->dbs_load_css )
			return; // this means that neither short code is present, so we get out of here
	
		wp_print_styles('dbs-form-css');
	}
	
	// login form fields
	private function dbs_login_form_fields() {
			
		ob_start(); 
			
			
			// show any error messages after form submission
			$this->dbs_show_error_messages(); ?>
			
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

	/* logs a member in after submitting a form */
	public function dbs_login_member() {

		if(isset($this->post_array['dbs_slname']) && wp_verify_nonce($this->post_array['dbs_login_nonce'], 'dbs-login-nonce')) {
					
			$user_login_array = explode(" ", $this->post_array['dbs_slname']);
			if (sizeof($user_login_array) == 2) 
				$user_login = strtolower($user_login_array[0]).".".strtolower($user_login_array[1]);
			else
				$user_login = $this->post_array['dbs_slname'];
			// this returns the user ID and other info from the user name
			$user = get_user_by('login',$user_login);
			
			if(!$user) {
				// if the user name doesn't exist
				$this->dbs_errors()->add('empty_username', __('Invalid username'));
			}
			
			if(!isset($this->post_array['dbs_user_pass']) || $this->post_array['dbs_user_pass'] == '') {
				// if no password was entered
				$this->dbs_errors()->add('empty_password', __('Please enter a password'));
			}
			
			if ($user) {
				// check the user's login with their password
				if(!wp_check_password($this->post_array['dbs_user_pass'], $user->user_pass, $user->ID)) {
					// if the password is incorrect for the specified user
					$this->dbs_errors()->add('empty_password', __('Incorrect password'));
				}
			}
			
			// retrieve all error messages
			$errors = $this->dbs_errors()->get_error_messages();
			
			// only log the user in if there are no errors
			if(empty($errors)) {
				
				wp_set_auth_cookie($user->ID, true);
				wp_set_current_user($user->ID, $user_login);	
				do_action('wp_login', $user_login);
				
				wp_redirect(home_url()); exit;
			}
		}
	}
	
	// used for tracking error messages
	// uses the WP global $wp_error
	private function dbs_errors(){
		static $wp_error; // Will hold global variable safely
		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	} 

	// displays error messages from form submissions
	private function dbs_show_error_messages() {
		if($codes = $this->dbs_errors()->get_error_codes()) {
			echo '<div class="dbs_errors">';
				// Loop error codes and display errors
			   foreach($codes as $code){
					$message = $this->dbs_errors()->get_error_message($code);
					echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
				}
			echo '</div>';
		}	
	}

	

}