<?php
/*
 * Handle the browser requests and route them
 * There are two reauests types 
 * -> respond to the shortcode 
 * -> respond to a form submission
 */
class Controller {
	private $post_array;
	private $view;
	private $model;
	
	public function __construct($post_array, LoginView $view, LoginModel $model) {
		$this->post_array = $post_array;
		$this->view = $view;
		$this->model = $model;
		
		$this->init();
	}

	private function init() {
		/* handle the shortcode */
		add_shortcode('login_form', [ $this->view,'login_form' ] );
		
		/* handle the form post */
		if (isset($this->post_array['dbs_action'])) if ($this->post_array['dbs_action'] == "login") {
			add_action( 'init', [$this->model, 'login_user'] );
		}
		if (isset($this->post_array['dbs_action'])) if ($this->post_array['dbs_action'] == "logout") {
			add_action( 'init', [$this->model, 'logout_user'] );
		}
	}
		
}