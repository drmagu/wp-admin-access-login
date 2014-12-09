<?php
if ( !class_exists( 'VersionCheck' ) ):
class VersionCheck {
	
	private $plugin_file;
	private $wp_version;
	private $php_version;
	private $is_compatible = true;
	private $plugin_data;
	
	public function __construct( $plugin_file, $wp = '3.5', $php ='5.3' ) {

		$this->plugin_file = $plugin_file;
		$this->wp_version = $wp;
		$this->php_version = $php;
		
		$this->init();
	}
	
	private function init() {
		register_activation_hook( $this->plugin_file, array( $this, 'check_version' ) );
		add_action( 'admin_init', array( $this, 'check_version' ) );
		add_action( 'admin_notices', array( $this, 'version_notice' ) );
	}
	
	public function check_version() {
		$this->plugin_data = get_plugin_data($this->plugin_file);
		$this->is_compatible();
		if ( ! $this->is_compatible ) {
			deactivate_plugins( plugin_basename( $this->plugin_file ) );		
		}
	}
	
	public function version_notice() {
		$the_notice = '';
		$the_notice .= '<div id="message" class="error">';
		$the_notice .= '<p>The <strong>'.$this->plugin_data['Name'].'</strong> plugin has <strong style="color:red" >BEEN DEACTIVATED</strong></p>';
		$the_notice .= '<p>';
		$the_notice .= 'This plugin requires <strong style="color:red" >WordPress ' . $this->wp_version;
		$the_notice .= '+ </strong> ';
		$the_notice .= 'AND <strong style="color:red" >PHP '.$this->php_version.'+ </strong>to work';
		$the_notice .= '</p>';
		$the_notice .= '<p>You are running <strong>WordPress '.$GLOBALS['wp_version'].' </strong>';
		$the_notice .= 'and <strong>PHP '.PHP_VERSION.'  </strong></p>';
		$the_notice .= '</div>';

		if (! $this->is_compatible) {
			echo $the_notice;
		}
	}
	
	private function is_compatible() {
		
		$wp_required = $this->wp_version;
		$php_required = $this->php_version;
		if ( ! version_compare( $GLOBALS['wp_version'], $wp_required, '>=' ) ) {
			$this->is_compatible = false;
		}
		if ( ! version_compare( PHP_VERSION, $php_required, '>=' ) ) {
			$this->is_compatible = false;
		}
		
		return $this->is_compatible;
	}
}
endif;