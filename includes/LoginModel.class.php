<?php
namespace drmagu\limit_admin_access_login;
use \WP_Error;

class LoginModel {
	
	private $post_array;
	private $wp_error;
	
	private $username = "";
	private $password = "";
	private $nonce = "";
	
	public function __construct($post_array) {
		$this->post_array = $post_array;
		if( isset( $post_array['dbs_slname'] ) ) $this->username = $post_array['dbs_slname'];
		if( isset( $post_array['dbs_user_pass'] ) ) $this->password = $post_array['dbs_user_pass'];
		if( isset( $post_array['dbs_login_nonce'] ) ) $this->nonce = $post_array['dbs_login_nonce'];
		
		/* used for tracking error messages */
		/* uses the WP global WP_Error class */
		if ( isset($wp_error) ) {
			$this->wp_error = $wp_error;
		} else {
			$this->wp_error = new WP_Error(null, null, null);
		}
	}
	
	public function get_error_codes() {
		return $this->wp_error->get_error_codes();	
	}
	
	public function get_error_message( $code ) {
		return $this->wp_error->get_error_message( $code );
	}
	
	public function get_username() {
		return $this->username;	
	}
	
	public function login_user() {

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
				$this->wp_error->add('empty_username', __('Invalid username'));
			}
			
			if(!isset($this->post_array['dbs_user_pass']) || $this->post_array['dbs_user_pass'] == '') {
				// if no password was entered
				$this->wp_error->add('empty_password', __('Please enter a password'));
			}
			
			if ($user) {
				// check the user's login with their password
				if(!wp_check_password($this->post_array['dbs_user_pass'], $user->user_pass, $user->ID)) {
					// if the password is incorrect for the specified user
					$this->wp_error->add('empty_password', __('Incorrect password'));
				}
			}
			
			// retrieve all error messages
			$errors = $this->wp_error->get_error_messages();
			
			// only log the user in if there are no errors
			if(empty($errors)) {
				
				wp_set_auth_cookie($user->ID, true);
				wp_set_current_user($user->ID, $user_login);	
				do_action('wp_login', $user_login);
				
				wp_redirect(home_url()); exit;
			}
		}
	}
	
	
	public function logout_user() { }
}