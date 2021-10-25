<?php
/**
 * PAPRO Addons Integration.
 */

namespace PremiumAddonsPro\Includes;

use PremiumAddonsPro\Admin\Includes\Admin_Helper as Papro_Helper;

// Premium Addons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Class Addons_Integration.
 */
class Addons_Integration {

	/**
	 * Class instance
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Modules
	 *
	 * @var modules
	 */
	private static $modules = null;

	/**
	 * Class Constructor
	 */
	public function __construct() {

		self::$modules = Admin_Helper::get_enabled_elements();

		// Load plugin icons font.
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'enqueue_icon_font' ) );

		// Load widgets files.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_register' ) );

		// Enqueue Editor assets.
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

		// Enqueue Preview CSS files.
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );

		// Register Frontend CSS files.
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ) );

		// Enqueue Frontend CSS files.
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_frontend_styles' ) );

		// Registers Frontend JS files.
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ) );

		// Registers AJAX Hooks.
		add_action( 'wp_ajax_handle_table_data', array( $this, 'handle_table_data' ) );
		add_action( 'wp_ajax_nopriv_handle_table_data', array( $this, 'handle_table_data' ) );

		add_action( 'wp_ajax_get_fb_page_token', array( $this, 'get_fb_page_token' ) );

		add_action( 'wp_ajax_get_instagram_token', array( $this, 'get_instagram_token' ) );

		add_action( 'wp_ajax_check_instagram_token', array( $this, 'check_instagram_token' ) );
		add_action( 'wp_ajax_nopriv_check_instagram_token', array( $this, 'check_instagram_token' ) );

		add_action( 'wp_ajax_clear_reviews_data', array( $this, 'clear_reviews_data' ) );

	}

	/**
	 * Loads widgets font CSS file
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_icon_font() {

		wp_enqueue_style(
			'premium-pro-elements',
			PREMIUM_PRO_ADDONS_URL . 'assets/editor/css/style.css',
			array(),
			PREMIUM_PRO_ADDONS_VERSION
		);

	}

	/**
	 * Get Facebook page token for Facebook Reviews
	 *
	 * @since 1.5.9
	 * @access public
	 *
	 * @return void
	 */
	public function get_fb_page_token() {

		check_ajax_referer( 'papro-social-elements', 'security' );

		$api_url = 'https://appfb.premiumaddons.com/wp-json/fbapp/v2/pages';

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
			)
		);

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );

		wp_send_json_success( $body );

	}

	/**
	 * Get Instagram account token for Instagram Feed widget
	 *
	 * @since 1.9.1
	 * @access public
	 *
	 * @return void
	 */
	public function get_instagram_token() {

		check_ajax_referer( 'papro-social-elements', 'security' );

		$api_url = 'https://appfb.premiumaddons.com/wp-json/fbapp/v2/instagram';

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
			)
		);

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );

		$transient_name = 'papro_insta_feed_' . $body;

		$expire_time = 59 * DAY_IN_SECONDS;

		set_transient( $transient_name, true, $expire_time );

		wp_send_json_success( $body );

	}

	/**
	 * Check Instagram token expiration
	 *
	 * @since 1.9.1
	 * @access public
	 *
	 * @return void
	 */
	public function check_instagram_token() {

		check_ajax_referer( 'papro-feed', 'security' );

		if ( ! isset( $_GET['token'] ) ) {
			wp_send_json_error();
		}

		$token = sanitize_text_field( wp_unslash( $_GET['token'] ) );

		$transient_name = 'papro_insta_feed_' . $token;

		// Search for cached data.
		$cache = get_transient( $transient_name );

		$refreshed_token = '';

		if ( false === $cache ) {

			$response = wp_remote_retrieve_body(
				wp_remote_get( 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $token )
			);

			$response = json_decode( $response );

			$refreshed_token = $response->access_token;
		}

		$data = array(
			'isValid'  => $cache,
			'newToken' => $refreshed_token,
		);

		wp_send_json_success( $data );

	}

	/**
	 * Handle Table Data
	 *
	 * Check if table data is cached, send request if not
	 *
	 * @since 2.0.6
	 * @access public
	 */
	public function handle_table_data() {

		check_ajax_referer( 'papro-feed', 'security' );

		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$widget_id = sanitize_text_field( wp_unslash( $_POST['id'] ) );

		$transient_name = sprintf( 'papro_table_%s', $widget_id );

		$response = get_transient( $transient_name );

		// We need to check if there are saved data.
		if ( ! isset( $_POST['expire'] ) ) {
			wp_send_json_success( json_decode( $response ) );
		}

		// We need to save data.
		if ( ! isset( $_POST['tableData'] ) ) {
			wp_send_json_error();
		}

		$expire = sanitize_text_field( wp_unslash( $_POST['expire'] ) );

		// If no data is cached, then send a new request.
		if ( false === $response ) {

			$response = $_POST['tableData'];

			$expire_time = Helper_Functions::transient_expire( $expire );

			set_transient( $transient_name, $response, $expire_time );

		}

		wp_send_json_success();

	}

	/**
	 * Clear Reviews Data
	 *
	 * @since 2.4.2
	 * @access public
	 */
	public function clear_reviews_data() {

		check_ajax_referer( 'papro-social-elements', 'security' );

		if ( ! isset( $_POST['transient'] ) ) {
			wp_send_json_error();
		}

		$transient = sanitize_text_field( wp_unslash( $_POST['transient'] ) );

		delete_transient( $transient );

		wp_send_json_success();

	}

	/**
	 * Enqueue Editor assets
	 *
	 * @since 1.4.5
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_editor_scripts() {

		$fb_reviews = self::$modules['premium-facebook-reviews'];

		$fb_feed = self::$modules['premium-facebook-feed'];

		$instagram_feed = self::$modules['premium-instagram-feed'];

		wp_enqueue_script(
			'papro-editor',
			PREMIUM_PRO_ADDONS_URL . 'assets/editor/js/editor.js',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		$data = array(
			'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'   => wp_create_nonce( 'papro-social-elements' ),
			'key'     => Papro_Helper::get_license_key(),
		);

		if ( $fb_reviews || $fb_feed || $instagram_feed ) {

			wp_register_script(
				'papro-fb-helper',
				PREMIUM_PRO_ADDONS_URL . 'assets/editor/js/fb-helper-min.js',
				array(),
				PREMIUM_PRO_ADDONS_VERSION,
				false
			);

			wp_register_script(
				'papro-fb-connect',
				PREMIUM_PRO_ADDONS_URL . 'assets/editor/js/fb-connect.js',
				array( 'papro-fb-helper' ),
				PREMIUM_PRO_ADDONS_VERSION,
				false
			);

			wp_localize_script( 'papro-fb-connect', 'settings', $data );

			wp_enqueue_script( 'papro-fb-helper' );
			wp_enqueue_script( 'papro-fb-connect' );

		}

		wp_enqueue_script(
			'papro-reviews-cache',
			PREMIUM_PRO_ADDONS_URL . 'assets/editor/js/fb-cache.js',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_localize_script( 'papro-reviews-cache', 'settings', $data );
	}

	/**
	 * Register Front CSS files
	 *
	 * @since 1.2.8
	 * @access public
	 */
	public function register_frontend_styles() {

		$dir    = Helper_Functions::get_styles_dir();
		$suffix = Helper_Functions::get_assets_suffix();

		wp_register_style(
			'tooltipster',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/tooltipster' . $suffix . '.css',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			'all'
		);

	}

	/**
	 * Enqueue Preview CSS files
	 *
	 * @since 1.2.8
	 * @access public
	 */
	public function enqueue_preview_styles() {

		wp_enqueue_style( 'tooltipster' );

	}

	/**
	 * Load widgets require function
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widgets_register() {
		$this->premium_pro_widgets_area();
	}

	/**
	 * Enqueue required CSS files
	 *
	 * @since 1.2.7
	 * @access public
	 */
	public function enqueue_frontend_styles() {

		$dir    = Helper_Functions::get_styles_dir();
		$suffix = Helper_Functions::get_assets_suffix();

		$is_rtl = is_rtl() ? '-rtl' : '';

		wp_enqueue_style(
			'premium-pro',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-addons' . $is_rtl . $suffix . '.css',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			'all'
		);

	}

	/**
	 * Premium PRO Widgets Area
	 *
	 * Register PAPRO widgets
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function premium_pro_widgets_area() {

		$widget_settings = self::$modules;

		foreach ( glob( PREMIUM_PRO_ADDONS_PATH . 'widgets/*.php' ) as $file ) {
			$slug = basename( $file, '.php' );

			$enabled = isset( $widget_settings[ $slug ] ) ? $widget_settings[ $slug ] : '';

			if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $widget_settings ) {
				$this->register_addon( $file );
			}
		}

	}

	/**
	 * Registers required JS files
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_frontend_scripts() {

		$dir    = Helper_Functions::get_scripts_dir();
		$suffix = Helper_Functions::get_assets_suffix();

		$magic_section = self::$modules['premium-magic-section'];

		wp_register_script(
			'premium-pro',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-addons' . $suffix . '.js',
			array(
				'jquery',
				'jquery-ui-draggable',
				'jquery-ui-sortable',
				'jquery-ui-resizable',
			),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		$data = array(
			'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'        => wp_create_nonce( 'papro-feed' ),
			'magicSection' => $magic_section ? true : false,
		);

		wp_localize_script( 'premium-pro', 'PremiumProSettings', $data );

		wp_register_script(
			'codebird',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/codebird' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'tabs-slick',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/slick' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'social-dot',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/doT' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'jquery-socialfeed',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/socialfeed' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-instafeed',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/instafeed' . $suffix . '.js',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-charts',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/charts' . $suffix . '.js',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			false
		);

		wp_register_script(
			'event-move',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/event-move' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-imgcompare',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/imgcompare' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'premium-behance',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/embed-behance' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-anime',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/anime' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-tweenmax',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/TweenMax' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'table-sorter',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/tablesorter' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'tooltipster-bundle',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/tooltipster' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'multi-scroll',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/multiscroll' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-gsap',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/gsap' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'papro-hscroll',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-hscroll' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		// Localize jQuery with required data for Section Add-ons.
		wp_localize_script(
			'elementor-frontend',
			'papro_addons',
			array(
				'url'           => admin_url( 'admin-ajax.php' ),
				'particles_url' => PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/particles' . $suffix . '.js',
				'kenburns_url'  => PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/cycle' . $suffix . '.js',
				'gradient_url'  => PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-gradient' . $suffix . '.js',
				'parallax_url'  => PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-parallax' . $suffix . '.js',
				'lottie_url'    => PREMIUM_ADDONS_URL . 'assets/frontend/' . $dir . '/lottie' . $suffix . '.js',
			)
		);

	}

	/**
	 * Register addon by file name
	 *
	 * @param string $file widget file
	 * .
	 * @return void
	 */
	public function register_addon( $file ) {

		$widget_manager = \Elementor\Plugin::instance()->widgets_manager;

		$base  = basename( str_replace( '.php', '', $file ) );
		$class = ucwords( str_replace( '-', ' ', $base ) );
		$class = str_replace( ' ', '_', $class );
		$class = sprintf( 'PremiumAddonsPro\Widgets\%s', $class );

		require $file;

		if ( 'PremiumAddonsPro\Widgets\Premium_Trustpilot_Reviews' === $class || 'PremiumAddonsPro\Widgets\Premium_Facebook_Reviews' === $class || 'PremiumAddonsPro\Widgets\Premium_Google_Reviews' === $class || 'PremiumAddonsPro\Widgets\Premium_Instagram_Feed' === $class ) {
			require_once PREMIUM_ADDONS_PATH . 'widgets/dep/urlopen.php';

			require_once PREMIUM_PRO_ADDONS_PATH . 'includes/deps/reviews.php';

		}

		if ( class_exists( $class ) ) {
			$widget_manager->register_widget_type( new $class() );
		}
	}

	/**
	 *
	 * Creates and returns an instance of the class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;
	}
}
