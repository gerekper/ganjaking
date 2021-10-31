<?php
/*
Plugin Name: Porto Theme - Functionality
Plugin URI: http://themeforest.net/user/p-themes
Description: Adds functionality such as Shortcodes, Post Types and Widgets to Porto Theme
Version: 2.2.1
Author: P-Themes
Author URI: http://themeforest.net/user/p-themes
License: GPL2
Text Domain: porto-functionality
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Porto_Functionality {

	private $widgets     = array( 'block', 'recent_posts', 'recent_portfolios', 'twitter_tweets', 'contact_info', 'follow_us' );
	private $woo_widgets = array( 'price_filter_list' );

	/**
	 * Constructor
	 *
	 * @since 1.0
	 *
	*/
	public function __construct() {

		// Load text domain
		add_action( 'plugins_loaded', array( $this, 'load' ) );

		add_action( 'init', array( $this, 'init' ), 20 );

		add_action( 'redux/page/porto_settings/enqueue', array( $this, 'fix_redux_styles' ) );

		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_flip( get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		$porto_old_plugins = ( in_array( 'porto-content-types/porto-content-types.php', $active_plugins ) ||
					in_array( 'porto-shortcodes/porto-shortcodes.php', $active_plugins ) ||
					in_array( 'porto-widgets/porto-widgets.php', $active_plugins ) );
		if ( $porto_old_plugins ) {
			add_action( 'admin_notices', array( $this, 'notice_to_remove_old_plugins' ) );
			add_action( 'network_admin_notices', array( $this, 'notice_to_remove_old_plugins' ) );
		}

		// define contants
		$this->define_constants( $active_plugins );

		// add shortcodes
		if ( ! in_array( 'porto-shortcodes/porto-shortcodes.php', $active_plugins ) ) {
			$this->load_shortcodes();
		}

		// add porto content types
		if ( ! in_array( 'porto-content-types/porto-content-types.php', $active_plugins ) ) {
			$this->load_content_types();
		}

		// add porto builders
		require_once PORTO_BUILDERS_PATH . 'init.php';

		// add meta library
		require_once( PORTO_META_BOXES_PATH . 'lib/meta_values.php' );
		require_once( PORTO_META_BOXES_PATH . 'lib/meta_fields.php' );
	}

	// load plugin text domain
	public function load() {
		load_plugin_textdomain( 'porto-functionality', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		// load porto widgets
		$this->load_widgets();
		if ( class_exists( 'Woocommerce' ) ) {
			$this->load_woocommerce_widgets();
		}

		// add metaboxes
		require_once( PORTO_META_BOXES_PATH . 'meta_boxes.php' );

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			/**
			 * Register Elementor widgets and settings
			 */
			require_once( dirname( PORTO_META_BOXES_PATH ) . '/elementor/init.php' );
		}

		if ( defined( 'VCV_VERSION' ) ) {
			/**
			 * Register Visual Composer elements and settings
			 */
			require_once( dirname( PORTO_META_BOXES_PATH ) . '/visualcomposer/init.php' );
		}
	}

	public function init() {
		// add async attribute
		add_filter( 'script_loader_tag', array( $this, 'script_add_async_attribute' ), 10, 2 );

		// fix yith woocommerce ajax navigation issue
		if ( defined( 'YITH_WCAN' ) ) {
			add_filter( 'the_post', array( $this, 'woocommerce_yith_ajax_filter' ), 16, 2 );
		}

		if ( class_exists( 'WC_Vendors' ) ) {
			global $porto_settings;
			if ( isset( $porto_settings['porto_wcvendors_product_tab'] ) && $porto_settings['porto_wcvendors_product_tab'] ) {
				remove_filter( 'woocommerce_product_tabs', array( 'WCV_Vendor_Shop', 'seller_info_tab' ) );
			}
		}

		add_filter( 'dynamic_sidebar_params', array( $this, 'add_classes_to_subscription_widget' ) );

		if ( is_admin() ) {
			require_once( PORTO_BUILDERS_PATH . 'lib/class-block-check.php' );
		}
	}

	public function woocommerce_yith_ajax_filter( $posts, $query = false ) {
		remove_filter( 'the_posts', array( YITH_WCAN()->frontend, 'the_posts' ), 15 );
		return $posts;
	}

	public function script_add_async_attribute( $tag, $handle ) {
		// add script handles to the array below
		$scripts_to_async = array( 'jquery-magnific-popup', 'modernizr', 'porto-theme-async', 'jquery-flipshow', 'porto_shortcodes_flipshow_loader_js' );
		if ( in_array( $handle, $scripts_to_async ) ) {
			return str_replace( ' src', ' async="async" src', $tag );
		}
		return $tag;
	}

	public function add_classes_to_subscription_widget( $params ) {
		if ( __( 'MailPoet Subscription Form', 'wysija-newsletters' ) == $params[0]['widget_name'] || 'MailPoet Subscription Form' == $params[0]['widget_name'] ) {
			$params[0]['before_widget'] = $params[0]['before_widget'] . '<div class="box-content">';
			$params[0]['after_widget']  = '</div>' . $params[0]['after_widget'];
		}
		return $params;
	}

	public function notice_to_remove_old_plugins() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		/* translators: opening and closing bold tags */
		echo '<div class="error"><p>' . sprintf( esc_html__( '%1$sImportant:%2$s Please deactivate Porto Shortcodes, Porto Content Types and Porto Widgets plugins from old Porto 3.x version.', 'porto-functionality' ), '<b>', '</b>' ) . '</p></div>';
	}

	public function fix_redux_styles() {
		// *****************************************************************
		// Select2 JS
		// *****************************************************************
		// JWp6 plugin giving us problems.  They need to update.
		if ( wp_script_is( 'jquerySelect2' ) ) {
			wp_deregister_script( 'jquerySelect2' );
			wp_dequeue_script( 'jquerySelect2' );
			wp_dequeue_style( 'jquerySelect2Style' );
		}
	}

	protected function define_constants( $active_plugins ) {

		define( 'PORTO_FUNC_FILE', __FILE__ );
		define( 'PORTO_META_BOXES_PATH', dirname( __FILE__ ) . '/meta_boxes/' );
		define( 'PORTO_BUILDERS_PATH', dirname( __FILE__ ) . '/builders/' );
		define( 'PORTO_FUNC_URL', plugin_dir_url( __FILE__ ) );
		if ( ! in_array( 'porto-shortcodes/porto-shortcodes.php', $active_plugins ) ) {
			define( 'PORTO_SHORTCODES_URL', PORTO_FUNC_URL . 'shortcodes/' );
			define( 'PORTO_SHORTCODES_PATH', dirname( __FILE__ ) . '/shortcodes/shortcodes/' );
			define( 'PORTO_SHORTCODES_WOO_PATH', dirname( __FILE__ ) . '/shortcodes/woo_shortcodes/' );
			define( 'PORTO_SHORTCODES_LIB', dirname( __FILE__ ) . '/shortcodes/lib/' );
			define( 'PORTO_SHORTCODES_TEMPLATES', dirname( __FILE__ ) . '/shortcodes/templates/' );
			define( 'PORTO_SHORTCODES_WOO_TEMPLATES', dirname( __FILE__ ) . '/shortcodes/woo_templates/' );
		}
		if ( ! in_array( 'porto-content-types/porto-content-types.php', $active_plugins ) ) {
			define( 'PORTO_CONTENT_TYPES_PATH', dirname( __FILE__ ) . '/content-types/' );
			define( 'PORTO_CONTENT_TYPES_LIB', dirname( __FILE__ ) . '/content-types/lib/' );
		}
		if ( ! in_array( 'porto-widgets/porto-widgets.php', $active_plugins ) ) {
			define( 'PORTO_WIDGETS_PATH', dirname( __FILE__ ) . '/widgets/' );
		}
	}

	// Load Shortcodes
	protected function load_shortcodes() {
		require_once( PORTO_SHORTCODES_PATH . '../porto-shortcodes.php' );
	}

	// Load Content Types
	protected function load_content_types() {
		require_once( PORTO_CONTENT_TYPES_PATH . 'porto-content-types.php' );
	}

	// Load widgets
	protected function load_widgets() {
		foreach ( $this->widgets as $widget ) {
			require_once( PORTO_WIDGETS_PATH . $widget . '.php' );
		}
	}

	// Load Woocommerce widgets
	protected function load_woocommerce_widgets() {
		foreach ( $this->woo_widgets as $widget ) {
			require_once( PORTO_WIDGETS_PATH . $widget . '.php' );
		}
	}
}

/**
 * Instantiate the Class
 *
 * @since     1.0
 * @global    object
 */
$porto_functionality = new Porto_Functionality();
