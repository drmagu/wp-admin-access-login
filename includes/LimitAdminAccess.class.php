<?php

class LimitAdminAccess {
	
	private $wpdb;
	private $user;
	private $current_user;
	
	public function __construct() {
		$this->add_actions();
		// $this->dbs_disable_adminbar();
	}
	
	private function add_actions() {
		add_action('login_head', [$this, 'dbs_no_wp_login'] );
		add_action('admin_init', [$this, 'dbs_restrict_admin_with_redirect'] );
		add_action('init', [$this , 'dbs_disable_adminbar'] );
		add_action('wp_logout', [$this, 'dbs_go_home']);
	}

	/*
	* Redirect All trying to access native wp-login pages directly
	*
	*/
	public function dbs_no_wp_login() {
				wp_redirect(site_url());
				exit;
	}

	/*
	* Restrict access to backend for all except ADMIN
	* redirects users to home page
	*/
	public function dbs_restrict_admin_with_redirect() {		
		if( ! current_user_can('manage_options') && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php' ) {
			wp_redirect( site_url() ); 
			exit;
		}
	}

	/*
	* ADMIN BAR 
	* returns: Removes Admin bar for all Except ADMIN
	*/			
	public function dbs_disable_adminbar(){		
		if( !current_user_can('manage_options') ){
			show_admin_bar(false);
		}
	}
	
	/*
	* Redirect to home page at logout
	*
	*/
	public function dbs_go_home(){
	  wp_redirect( home_url() );
	  exit();
	}
	
}

