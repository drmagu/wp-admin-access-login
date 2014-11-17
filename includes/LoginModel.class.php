<?php

class LoginModel {
	
	private $wp_error;
	
	public function __construct() {
		/* used for tracking error messages */
		/* uses the WP global $wp_error */
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
	
	public function login_user() {
		echo "Logging in";
	}
	
	public function logout_user() { }
}