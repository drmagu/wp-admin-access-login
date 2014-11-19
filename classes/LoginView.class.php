<?php
namespace drmagu\limit_admin_access_login;

class LoginView {
	
	private $dbs_load_css = false;
	private $model;
	private $plugin_dir_url;
	
	public function  __construct(LoginModel $model, $plugin_dir_url) {
		$this->model = $model;
		$this->plugin_dir_url = $plugin_dir_url;
		
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
			
			$output = $this->login_form_view();
		} else {
			// could show some logged in user info here
			$current_user = wp_get_current_user();
			$output = $this->logout_form_view($current_user);
		}
		return $output;
	}
	
	// register our form css
	public function dbs_register_css() {
		wp_register_style('dbs-form-css', $this->plugin_dir_url . '/css/forms.css');
	}
	
	// load our form css
	public function dbs_print_css() {
	 
		// this variable is set to TRUE if the short code is used on a page/post
		if ( ! $this->dbs_load_css )
			return; // this means that the short code is not present, so we get out of here
	
		wp_print_styles('dbs-form-css');
	}
	
	/* HTML Markup and Stuff */
	/* displays error messages from form submissions */
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
	
	private function login_form_view() {
			
		ob_start(); 
			
			// show any error messages after form submission
			$this->dbs_show_error_messages(); ?>
			
			<form id="dbs_login_form"  class="dbs_form" action="" method="post">
				<fieldset>
					<p>
						<label for="dbs_slname">User Name</label>
						<input name="dbs_slname" id="dbs_slname" class="required" 
                        	type="text" value="<?= $this->model->get_username() ?>"/>
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
	
	private function logout_form_view($current_user) {
		ob_start();
		?>
        
        You are currently signed in as "<span style="color:#0A0"><?= $current_user->user_login ?></span>"
		
		<form id="dbs_logout_form"  class="dbs_form" action="" method="post">
        	<fieldset>
                <input type="hidden" name="dbs_action" value="logout" />
                <input type="hidden" name="dbs_login_nonce" value="<?php echo wp_create_nonce('dbs-login-nonce'); ?>"/>
                <div>
                <input id="dbs_login_submit" type="submit" value="Logout"/>
                </div>
        	</fieldset>
        </form>
        
		<?php
		return ob_get_clean();
	}
	
}
