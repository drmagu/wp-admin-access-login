<?php

class LoginView {
	
	private $dbs_load_css = false;
	private $model;
	
	public function  __construct(LoginModel $model) {
		$this->model = $model;
		
		$this->init();
	}
	
	private function init() {
		add_action('init', [$this, 'dbs_register_css'] );
		add_action('wp_footer', [$this, 'dbs_print_css'] );
	}
	
	public function login_form() {
		$this->dbs_load_css = true;
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
	
	/* HTML Markup and Stuff */
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
	
	// displays error messages from form submissions
	private function dbs_show_error_messages() {
		if($codes = $this->model->get_error_codes()) {
			echo '<div class="dbs_errors">';
				// Loop error codes and display errors
			   foreach($codes as $code){
					$message = $this->model->get_error_message($code);
					echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
				}
			echo '</div>';
		}	
	}
	
}