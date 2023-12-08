<?php
namespace TheplusAddons;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Theplus_Element_Load {
	/**
		* Core singleton class
		* @var self - pattern realization
	*/
	private static $_instance;

	/**
	 * @var Manager
	 */
	private $_modules_manager;

	/**
	 * @deprecated
	 * @return string
	 */
	public function get_version() {
		return THEPLUS_VERSION;
	}
	
	/**
	* Cloning disabled
	*/
	public function __clone() {
	}
	
	/**
	* Serialization disabled
	*/
	public function __sleep() {
	}
	
	/**
	* De-serialization disabled
	*/
	public function __wakeup() {
	}
	
	/**
	* @return \Elementor\Theplus_Element_Loader
	*/
	public static function elementor() {
		return \Elementor\Plugin::$instance;
	}
	
	/**
	* @return Theplus_Element_Loader
	*/
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * we loaded module manager + admin php from here
	 * @return [type] [description]
	 */
	private function includes() {		
		/*remove backend cache	
		$option_name='on_first_load_cache';
		$value='1';
		if ( is_admin() && get_option( $option_name ) !== false ) {
		} else if( is_admin() ){
			l_theplus_library()->remove_backend_dir_files();
			$deprecated = null;
			$autoload = 'no';			
			add_option( $option_name,$value, $deprecated, $autoload );
		}
		remove backend cache*/
		
		/* @version 5.0.3*/
		$option_name = 'tp_key_random_generate';		
		if ( is_admin() && get_option( $option_name ) !== false ) {
		} else if( is_admin() ){
			$default_load=get_option( $option_name );
				if(empty($default_load)){
					$listofcharun = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					$generatedkey = substr(str_shuffle($listofcharun), 0, 12);
					
					$deprecated = null;
					$autoload = 'no';					
					add_option( $option_name,$generatedkey, $deprecated, $autoload );
				}
		}
		
		if( !class_exists( 'Theplus_SL_Plugin_Updater' ) && THEPLUS_TYPE=='store') {
			include( THEPLUS_PATH . 'includes/Theplus_SL_Plugin_Updater.php' );
		}
		
		require_once THEPLUS_INCLUDES_URL .'plus_addon.php';
		
		$megamenu = theplus_get_option('general','check_elements');
		if(isset($megamenu) && !empty($megamenu) && in_array("tp_navigation_menu", $megamenu) ){
			include THEPLUS_INCLUDES_URL . 'custom-nav-item/menu-item-custom-fields.php';
			include THEPLUS_INCLUDES_URL . 'custom-nav-item/plus-navigation-fields.php';
		}
		
		if ( class_exists( 'CMB2_Bootstrap_260_Develop') ) {
			require_once THEPLUS_INCLUDES_URL.'plus-options/includes.php';
		}
		
		require_once THEPLUS_INCLUDES_URL .'template-api.php';
		require THEPLUS_INCLUDES_URL.'theplus_options.php';
		
		if (defined("L_THEPLUS_VERSION") && version_compare( L_THEPLUS_VERSION, '5.0.6', '<' ) ) {
			require THEPLUS_PATH.'modules/theplus-core-cp.php';
		}
		
		require THEPLUS_PATH.'modules/theplus-integration.php';
		require THEPLUS_PATH.'modules/query-control/module.php';
		
		require THEPLUS_PATH.'modules/mobile_detect.php';
		require_once THEPLUS_PATH .'modules/helper-function.php';
			
		if( is_admin() ){
			if( empty( get_option( 'theplus-notice-dismissed' ) ) ) {
				add_action( 'admin_notices', array($this, 'thepluskey_verify_notify') );
			}
		}
	}
	
	/**
	* Widget Include required files
	*
	*/
	public function include_widgets() {			
		require_once THEPLUS_PATH.'modules/theplus-include-widgets.php';		
	}
	
	public function theplus_editor_styles() {
		wp_enqueue_style( 'theplus-ele-admin-pro', THEPLUS_ASSETS_URL .'css/admin/theplus-ele-admin.css', array(),THEPLUS_VERSION,false );
	}
	public function theplus_elementor_admin_css() {  
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( 'theplus-ele-admin-pro', THEPLUS_ASSETS_URL .'css/admin/theplus-ele-admin.css', array('wp-color-picker'),THEPLUS_VERSION,false );
		wp_enqueue_script( 'wp-color-picker', THEPLUS_ASSETS_URL . 'js/extra/wp-color-picker-alpha.min.js',array() , THEPLUS_VERSION, true );
		wp_enqueue_script( 'theplus-admin-js-pro', THEPLUS_ASSETS_URL .'js/admin/theplus-admin.js', array( 'wp-color-picker'),THEPLUS_VERSION,false );
	}

	/*
	 * Admin notice text
	 */
	public function thepluskey_verify_notify() {
		$verify_api = theplus_check_api_status();		
		if( $verify_api != 1 ){
			echo '<div class="plus-key-notify notice notice-info is-dismissible">';
				echo '<h3>'.esc_html('Activation Required.', 'theplus' ) .'</h3>';
				echo '<p>'. esc_html__( '🤝 Thanks for Installation,', 'theplus' ) .' ';
				echo '<b>'. esc_html__( 'You are just one step away to supercharge your Elementor Page Builder with The Plus Addons.', 'theplus' ) .'</b>';
				echo ' <a href="'.admin_url('admin.php?page=theplus_purchase_code').'">'. esc_html__( 'Click Here to activate.', 'theplus' ) .'</a></p>';
			echo '</div>';
		}
	}	
	
	public function theplus_load_template( $single_template ) {
		
		global $post;

		if ( 'plus-mega-menu' == $post->post_type) {

			$elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

			if ( file_exists( $elementor_2_0_canvas ) ) {
				return $elementor_2_0_canvas;
			} else {
				return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
			}
		}

		return $single_template;
	}

	private function hooks() {
		$theplus_options = get_option('theplus_options');
		$plus_extras = theplus_get_option('general','extras_elements');
		
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'theplus_editor_styles' ] );
		
		// Include some backend files
		add_action( 'admin_enqueue_scripts', [ $this,'theplus_elementor_admin_css'] );
		add_filter( 'single_template', [ $this, 'theplus_load_template' ] );

		if( is_admin() ) {
			add_filter( 'plugin_action_links_' . THEPLUS_PBNAME, array( $this, 'tppro_add_settings_link' ) );
			add_filter( 'plugin_row_meta', array( $this, 'tppro_extra_links_plugin_row_meta' ), 10, 2 );
		}
	}
	
	/**
	 * Adds Links to the plugins page.
	 * @since 5.1.7
	 */
	public function tppro_add_settings_link( $links ) {
		// Need Help link.
		$Settings = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=theplus_options' ), __( 'Settings', 'theplus' ) );
		$need_help = sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url('https://theplusaddons.com/free-vs-pro/?utm_source=wpbackend&utm_medium=pluginpage&utm_campaign=links'), __( 'Need Help?', 'theplus' ) );
		$license = sprintf( '<a href="%s" style="color:green;font-weight:600;">%s</a>', admin_url( 'admin.php?page=theplus_purchase_code' ), __( 'License', 'theplus' ) );
		
		$links = (array) $links;
		$links[] = $Settings;
		$links[] = $need_help;
		$links[] = $license;

		return $links;
	}

	/**
	 * Adds Links to the plugins page.
	 * @since 5.1.7
	 */
	public function tppro_extra_links_plugin_row_meta( $plugin_meta, $plugin_file ) {

		if ( strpos( $plugin_file, THEPLUS_PBNAME ) !== false ) {
			$new_links = array(
				'docs' => '<a href="'.esc_url('https://theplusaddons.com/docs?utm_source=wpbackend&utm_medium=pluginpage&utm_campaign=links').'" target="_blank" rel="noopener noreferrer" style="color:green;">'.esc_html__( 'Docs', 'theplus' ).'</a>',
				'video-tutorials' => '<a href="'.esc_url('https://www.youtube.com/c/POSIMYTHInnovations/?sub_confirmation=1').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Video Tutorials', 'theplus' ).'</a>',
				'join-community' => '<a href="'.esc_url('https://www.facebook.com/groups/1331664136965680').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Join Community', 'theplus' ).'</a>',
				'whats-new' => '<a href="'.esc_url('https://roadmap.theplusaddons.com/updates?filter=Pro').'" target="_blank" rel="noopener noreferrer" style="color: orange;">'.esc_html__( 'What\'s New?', 'theplus' ).'</a>',
				'req-feature' => '<a href="'.esc_url('https://roadmap.theplusaddons.com/boards/feature-request').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Request Feature', 'theplus' ).'</a>',
				'rate-theme' => '<a href="'.esc_url('https://wordpress.org/support/plugin/the-plus-addons-for-elementor-page-builder/reviews/?filter=5').'" target="_blank" rel="noopener noreferrer">'.esc_html__( 'Share Review', 'theplus' ).'</a>'
			);

			$plugin_meta = array_merge( $plugin_meta, $new_links );
		}
			
		return $plugin_meta;
	}

	public static function nav_item_load() {
		add_filter( 'wp_edit_nav_menu_walker', array( __CLASS__, 'plus_filter_walker' ), 99 );
	}

	/**
	 * ThePlus_Load constructor.
	 */
	private function __construct() {
		
		// Register class automatically
		$this->includes();
		// Finally hooked up all things
		$this->hooks();		
		theplus_elements_integration()->init();
		
		if (defined("L_THEPLUS_VERSION") && version_compare( L_THEPLUS_VERSION, '5.0.6', '<' ) ) {
			theplus_core_cp()->init();
		}
		
		$this->include_widgets();		
		theplus_widgets_include();
	}
}

/**Get theplus_addon_load Running*/
function theplus_addon_load(){
	return Theplus_Element_Load::instance();
}
theplus_addon_load();