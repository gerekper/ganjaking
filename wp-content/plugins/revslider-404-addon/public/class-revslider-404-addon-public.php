<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_404_Addon
 * @subpackage Revslider_404_Addon/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Revslider_404_Addon
 * @subpackage Revslider_404_Addon/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_404_Addon_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revslider_404_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_404_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-404-addon-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Revslider_404_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Revslider_404_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-404-addon-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Redirect in case of 404
	 *
	 * @since    1.0.0
	 */
	public function redirect_404() {
		
	    if (is_404()) {
	    	//saved values
			$revslider_404_addon_values = array();
			parse_str(get_option('revslider_404_addon'), $revslider_404_addon_values);

			//defaults
			$revslider_404_addon_values['revslider-404-addon-type'] = isset($revslider_404_addon_values['revslider-404-addon-type']) ? $revslider_404_addon_values['revslider-404-addon-type'] : 'slider';
			// $revslider_404_addon_values['revslider-404-addon-active'] = isset($revslider_404_addon_values['revslider-404-addon-active']) ? $revslider_404_addon_values['revslider-404-addon-active'] : '0';
			$revslider_404_addon_values['revslider-404-addon-slider'] = isset($revslider_404_addon_values['revslider-404-addon-slider']) ? $revslider_404_addon_values['revslider-404-addon-slider'] : '';
			$revslider_404_addon_values['revslider-404-addon-page'] = isset($revslider_404_addon_values['revslider-404-addon-page']) ? $revslider_404_addon_values['revslider-404-addon-page'] : '';
	        
	        //if($revslider_404_addon_values['revslider-404-addon-active']){
				//header( 'HTTP/1.1 Service Unavailable', true, 503 );
				header("HTTP/1.0 404 Not Found");
				header( 'Content-Type: text/html; charset=utf-8' );
				if ( file_exists( plugin_dir_path( __FILE__ ) . 'partials/revslider-404-addon-public-display.php' ) ) {
					require_once( plugin_dir_path( __FILE__ ) . 'partials/revslider-404-addon-public-display.php' );
				}
				die();
			//}
	    }
	}

}
