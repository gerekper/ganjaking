<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_Status extends Mfn_API {

	private $data 	= array();
	private $status = array();

	/**
	 * Mfn_Status constructor
	 */
	public function __construct(){

		parent::__construct();

		// It runs after the basic admin panel menu structure is in place.
		add_action( 'admin_menu', array( $this, 'init' ), 14 );

	}

	/**
	 * Add admin page & enqueue styles
	 */
	public function init(){

		$title = __( 'System Status','mfn-opts' );

		$this->page = add_submenu_page(
			'betheme',
			$title,
			$title,
			'edit_theme_options',
			'be-status',
			array( $this, 'template' )
		);

		// Fires when styles are printed for a specific admin page based on $hook_suffix.
		add_action( 'admin_print_styles-'. $this->page, array( $this, 'enqueue' ) );

		$this->set_status();
	}

	/**
	 * Status template
	 */
	public function template(){
		include_once get_theme_file_path('/functions/admin/templates/status.php');
	}

	/**
	 * Enqueue styles and scripts
	 */
	public function enqueue(){
		wp_enqueue_style( 'mfn-dashboard', get_theme_file_uri('/functions/admin/assets/dashboard.css'), array(), MFN_THEME_VERSION );
	}

	/**
	 * Get system status array
	 */
	public function set_status(){

		global $wpdb;

		$data 	= array(
			'php'							=> phpversion(),
			'mysql'						=> $wpdb->db_version(),
			'memory_limit' 		=> wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) ),
			'time_limit' 			=> ini_get( 'max_execution_time' ),
			'max_input_vars' 	=> ini_get( 'max_input_vars' ),
			'max_upload_size'	=> size_format( wp_max_upload_size() ),

			'home'						=> home_url(),
			'siteurl'					=> get_option( 'siteurl' ),
			'wp_version'			=> get_bloginfo( 'version' ),
			'language'				=> get_locale(),
			'rtl'							=> is_rtl() ? 'RTL' : 'LTR',
		);

		$status = array(
			'php'							=> version_compare( PHP_VERSION, '7.0' ) >= 0,
			'suhosin'					=> extension_loaded( 'suhosin' ),
			'memory_limit'		=> $data['memory_limit'] >= 268435456,
			'time_limit'			=> ( ( $data['time_limit'] >= 180 ) || ( $data['time_limit'] == 0 ) ),
			'max_input_vars'	=> $data['max_input_vars'] >= 5000,
			'curl'						=> extension_loaded( 'curl' ),
			'dom'							=> class_exists( 'DOMDocument' ),

			'siteurl'					=> false,
			'wp_version'			=> version_compare( get_bloginfo( 'version' ), '4.9' ) >= 0,
			'multisite'				=> is_multisite(),
			'debug'						=> defined( 'WP_DEBUG' ) && WP_DEBUG,
		);

		$parse = array(
			'home' 		=> parse_url( $data['home'] ),
			'siteurl' => parse_url( $data['siteurl'] ),
		);

		if( isset( $parse['home']['host'] ) && isset( $parse['siteurl']['host'] ) ){
			if( $parse['home']['host'] == $parse['siteurl']['host'] ){
				$status['siteurl'] = true;
			}
		} elseif( isset( $parse['home']['path'] ) && isset( $parse['siteurl']['path'] ) ){
			if( $parse['home']['path'] == $parse['siteurl']['path'] ){
				$status['siteurl'] = true;
			}
		}

		$this->data		= $data;
		$this->status = $status;

	}

}

$mfn_status = new Mfn_Status();
