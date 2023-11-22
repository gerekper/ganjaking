<?php
/**
 * PAPRO Addons Integration.
 */

namespace PremiumAddonsPro\Includes;

use PremiumAddonsPro\Admin\Includes\Admin_Helper as Papro_Helper;

// Premium Addons Classes.
use PremiumAddons\Admin\Includes\Admin_Helper;
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Assets_Manager;

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

		// Load widgets files.
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			add_action( 'elementor/widgets/register', array( $this, 'widgets_register' ) );
		}

		// Enqueue Editor assets.
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

		// Enqueue Preview CSS files.
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );

		// Register Frontend CSS files.
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ) );

		// Registers Frontend JS files.
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ) );

		// Registers AJAX Hooks.
		add_action( 'wp_ajax_handle_table_data', array( $this, 'handle_table_data' ) );
		add_action( 'wp_ajax_nopriv_handle_table_data', array( $this, 'handle_table_data' ) );

		add_action( 'wp_ajax_get_fb_page_token', array( $this, 'get_fb_page_token' ) );

		add_action( 'wp_ajax_get_instagram_token', array( $this, 'get_instagram_token' ) );

		add_action( 'wp_ajax_clear_reviews_data', array( $this, 'clear_reviews_data' ) );
		add_action( 'wp_ajax_get_papro_key', array( $this, 'get_papro_key' ) );

		add_action( 'elementor/frontend/widget/after_render', array( $this, 'handle_facebook_feed' ) );

	}

	public function handle_facebook_feed( $widget ) {

		$name = $widget->get_name();

		$assets_gen_enabled = self::$modules['premium-assets-generator'] ? true : false;

		if ( ! $assets_gen_enabled || ( 'premium-facebook-feed' !== $name && 'premium-behance-feed' !== $name ) ) {
			return;
		}

		$dir    = Helper_Functions::get_scripts_dir();
		$suffix = Helper_Functions::get_assets_suffix();

		if ( 'premium-facebook-feed' === $name ) {

			wp_enqueue_script(
				'papro-fbfeed',
				PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-facebook-feed' . $suffix . '.js',
				array(),
				PREMIUM_PRO_ADDONS_VERSION,
				true
			);

			$fb_data = apply_filters( 'pa_facebook_feed', array() );

			wp_localize_script( 'papro-fbfeed', 'PaFbFeed', $fb_data );

		}

		if ( 'premium-behance-feed' === $name ) {

			wp_enqueue_script(
				'papro-behance',
				PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-behance-feed' . $suffix . '.js',
				array(),
				PREMIUM_PRO_ADDONS_VERSION,
				true
			);

			$behance_data = apply_filters( 'pa_behance_feed', array() );

			wp_localize_script( 'papro-behance', 'PaBehFeed', $behance_data );

		}

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

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'User is not authorized!' );
		}

		if ( ! isset( $_POST['transient'] ) ) {
			wp_send_json_error();
		}

		$transient = sanitize_text_field( wp_unslash( $_POST['transient'] ) );

		delete_transient( $transient );

		delete_option( $transient );

		wp_send_json_success();

	}

	/**
	 * Get PAPRO Key
	 *
	 * @since 2.9.1
	 * @access public
	 */
	public function get_papro_key() {

		check_ajax_referer( 'papro-social-elements', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'User is not authorized!' );
		}

		$data = array(
			'key' => Papro_Helper::get_license_key(),
		);

		wp_send_json_success( $data );

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

		wp_enqueue_script(
			'pa-blob-path',
			PREMIUM_PRO_ADDONS_URL . 'assets/editor/js/generate-path.js',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		$data = array(
			'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'   => wp_create_nonce( 'papro-social-elements' ),
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

		$is_rtl = is_rtl() ? '-rtl' : '';

		wp_register_style(
			'pa-global',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/pa-global' . $suffix . '.css',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			'all'
		);

		if ( wp_style_is( 'premium-addons', 'enqueued' ) ) {
			wp_enqueue_style(
				'premium-pro',
				PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-addons' . $is_rtl . $suffix . '.css',
				array(),
				PREMIUM_PRO_ADDONS_VERSION,
				'all'
			);

		}

		if ( defined( 'ELEMENTOR_PRO_ASSETS_URL' ) ) {
			wp_register_style(
				'pa-loop-item',
				ELEMENTOR_PRO_ASSETS_URL . 'css/loop-grid-cta.min.css',
				array(),
				ELEMENTOR_PRO_VERSION,
				'all'
			);
		}

	}

	/**
	 * Enqueue Preview CSS files
	 *
	 * @since 1.2.8
	 * @access public
	 */
	public function enqueue_preview_styles() {

		wp_enqueue_style( 'tooltipster' );

		wp_enqueue_style( 'premium-pro' );

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

		$current_page = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		$depends = array( 'jquery' );
		if ( false != strpos( $current_page, 'elementor-preview' ) ) {

			$depends = array_merge(
				$depends,
				array(
					'jquery-ui-draggable',
					'jquery-ui-sortable',
					'jquery-ui-resizable',
				)
			);

		}

		$magic_section       = self::$modules['premium-magic-section'];
		$site_cursor_enabled = $this->is_site_cursor_enabled();

		$data = array(
			'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'        => wp_create_nonce( 'papro-feed' ),
			'magicSection' => $magic_section ? true : false,
		);

		if ( $site_cursor_enabled ) {

			if ( ! wp_style_is( 'font-awesome-5-all', 'enqueued' ) ) {
				wp_enqueue_style( 'font-awesome-5-all' );
			}

			if ( ! wp_style_is( 'pa-global', 'enqueued' ) ) {
				wp_enqueue_style( 'pa-global' );
			}

			if ( ! wp_script_is( 'lottie-js', 'enqueued' ) ) {
				wp_enqueue_script( 'lottie-js' );
			}

			if ( ! wp_script_is( 'pa-tweenmax', 'enqueued' ) ) {
				wp_enqueue_script( 'pa-tweenmax' );
			}

			if ( ! wp_script_is( 'premium-cursor-handler', 'enqueued' ) ) {
				wp_enqueue_script( 'premium-cursor-handler' );
			}
		}

		wp_register_script(
			'pa-magazine',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-smart-post-listing' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_localize_script( 'pa-magazine', 'PremiumProSettings', $data );
		wp_localize_script(
			'pa-magazine',
			'PremiumSettings',
			array(
				'nonce' => wp_create_nonce( 'pa-blog-widget-nonce' ),
			)
		);

		if ( ! wp_script_is( 'pa-frontend', 'enqueued' ) ) {
			wp_register_script(
				'premium-pro',
				PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-addons' . $suffix . '.js',
				$depends,
				PREMIUM_PRO_ADDONS_VERSION,
				true
			);

			wp_localize_script( 'premium-pro', 'PremiumProSettings', $data );

		} else {
			wp_localize_script( 'pa-frontend', 'PremiumProSettings', $data );

			// We already localize pa-frontend in the free version.

			// if ( isset( self::$modules['premium-smart-post-listing'] ) && self::$modules['premium-smart-post-listing'] ) {

			// wp_localize_script(
			// 'pa-frontend',
			// 'PremiumSettings',
			// array(
			// 'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
			// 'nonce'   => wp_create_nonce( 'pa-blog-widget-nonce' ),
			// )
			// );
			// }
		}

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
			'premium-cursor-handler',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/min-js/pa-cursor.min.js',
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
			'pa-wordcloud',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/wordCloud' . $suffix . '.js',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			false
		);

		wp_register_script(
			'pa-awesomecloud',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/awesomecloud' . $suffix . '.js',
			array(),
			PREMIUM_PRO_ADDONS_VERSION,
			false
		);

		wp_register_script(
			'pa-tagcanvas',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/tagcanvas' . $suffix . '.js',
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
			'pa-behance',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/embed-behance' . $suffix . '.js',
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
			'multi-scroll',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/multiscroll' . $suffix . '.js',
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

		wp_register_script(
			'pa-particles',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/particles' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-gradient',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-gradient' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-kenburns',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-kenburns' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-cursor',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-cursor' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-parallax',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-parallax' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-blob',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-blob' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-blob-path',
			PREMIUM_PRO_ADDONS_URL . 'assets/editor/js/generate-path.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-badge',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-badge' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'pa-mscroll',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-mscroll' . $suffix . '.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

		// TODO: Move this to the free version.
		wp_register_script(
			'pa-scrolltrigger',
			PREMIUM_PRO_ADDONS_URL . 'assets/frontend/js/scrollTrigger.js',
			array( 'jquery' ),
			PREMIUM_PRO_ADDONS_VERSION,
			true
		);

	}

	/**
	 * Check if site cursor is enabled.
	 *
	 * @access public
	 * @since 2.8.7
	 *
	 * @return bool
	 */
	public function is_site_cursor_enabled() {

		$cursor_module_enabled = self::$modules['premium-global-cursor'];

		if ( ! $cursor_module_enabled ) {
			return false;
		}

		$is_edit_mode = Helper_Functions::is_edit_mode();

		if ( $is_edit_mode ) {
			return true;
		}

		$gcursor_settings = get_option( 'pa_site_custom_cursor', false );

		if ( ! $gcursor_settings || ! isset( $gcursor_settings['enabled'] ) ) {
			return false;
		}

		$site_cusror_enabled = $gcursor_settings['enabled'];

		return $site_cusror_enabled;
	}

	/**
	 * Register addon by file name
	 *
	 * @param string $file widget file
	 * .
	 * @return void
	 */
	public function register_addon( $file ) {

		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		$base           = basename( str_replace( '.php', '', $file ) );
		$class          = ucwords( str_replace( '-', ' ', $base ) );
		$class          = str_replace( ' ', '_', $class );
		$class          = sprintf( 'PremiumAddonsPro\Widgets\%s', $class );
		$social_classes = array(
			'PremiumAddonsPro\Widgets\Premium_Facebook_Reviews',
			'PremiumAddonsPro\Widgets\Premium_Google_Reviews',
			'PremiumAddonsPro\Widgets\Premium_Instagram_Feed',
		);

		require $file;

		if ( in_array( $class, $social_classes, true ) ) {
			require_once PREMIUM_ADDONS_PATH . 'widgets/dep/urlopen.php';

			if ( in_array( $class, $social_classes, true ) ) {
				require_once PREMIUM_PRO_ADDONS_PATH . 'includes/deps/reviews.php';
			}
		}

		if ( 'PremiumAddonsPro\Widgets\Premium_Smart_Post_Listing' === $class ) {
			require_once PREMIUM_PRO_ADDONS_PATH . 'includes/pa-smart-post-listing-helper.php';
		}

		if ( class_exists( $class ) ) {

			$widgets_manager->register( new $class() );

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
