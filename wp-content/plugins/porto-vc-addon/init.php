<?php
/*
Plugin Name: Porto Theme - Visual Composer Addon
Plugin URI: http://themeforest.net/user/p-themes
Description: Adds Visual Composer functionality to Porto Theme
Version: 1.1.0
Author: P-Themes
Author URI: http://themeforest.net/user/p-themes
License: GPL2
Text Domain: porto-vc-addon
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Porto_VC_Addon {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 *
	 */
	public function __construct() {
		// define constants
		define( 'PORTO_VC_ADDON_VERSION', '1.0' );
		define( 'PORTO_VC_ADDON_PATH', plugin_dir_path( __FILE__ ) );
		define( 'PORTO_VC_ADDON_URL', plugin_dir_url( __FILE__ ) );

		// add actions
		add_action( 'plugins_loaded', array( $this, 'load' ), 20 );
	}

	/**
	 * load main funcitons
	 *
	 * @since 1.0
	 */
	public function load() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			add_action( 'admin_notices', array( $this, 'install_functionality_admin_notice' ) );
			return;
		}
		if ( ! defined( 'VCV_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'install_vc_admin_notice' ) );
			return;
		}
		// load plugin text domain
		load_plugin_textdomain( 'porto-vc-addon', false, PORTO_VC_ADDON_PATH . '/languages' );

		add_action( 'init', array( $this, 'init' ), 20 );
		add_action( 'porto_enqueue_css', array( $this, 'enqueue_frontend' ) );
		add_filter( 'porto_frontend_vars', array( $this, 'add_vc_vars' ) );
		add_filter( 'porto_used_shortcode_list', array( $this, 'add_vc_shortcodes' ) );

		// main setup
		require_once PORTO_VC_ADDON_PATH . 'inc/elements_setup.php';

		// init builders
		require_once PORTO_VC_ADDON_PATH . 'builders/init.php';
	}

	/**
	 * init action
	 *
	 * @since 1.0
	 */
	public function init() {

	}

	/**
	 * enqueue in frontend
	 *
	 * @since 1.0
	 */
	public function enqueue_frontend() {
		if ( is_singular() ) {
			$bundle_url = get_post_meta( get_the_ID(), 'vcvSourceCssFileUrl', true );
			if ( $bundle_url ) {
				$handle = 'vcv:assets:source:main:styles:' . vchelper( 'Str' )->slugify( $bundle_url );
				if ( wp_style_is( $handle ) ) {
					$vcv_style = wp_styles()->registered[ $handle ];
					wp_dequeue_style( $handle );
					wp_enqueue_style( $handle, $vcv_style->src, $vcv_style->deps, $vcv_style->ver );
				}
			}
		}
	}

	/**
	 * Add Visual Composer variables
	 *
	 * @since 1.0
	 */
	public function add_vc_vars( $porto_vars ) {
		global $porto_settings;

		$map_key                  = ( ! empty( $porto_settings['gmap_api'] ) ? 'key=' . $porto_settings['gmap_api'] . '&' : '' );
		$porto_vars['gmap_uri']   = esc_js( $map_key . 'language=' . substr( get_locale(), 0, 2 ) );
		$porto_vars['gmt_offset'] = esc_js( get_option( 'gmt_offset' ) );

		return $porto_vars;
	}

	/**
	 * Add Visual Composer shortcodes used through out the site
	 *
	 * @since 1.0
	 */
	public function add_vc_shortcodes( $used, $return_ids ) {
		if ( ! $return_ids ) {
			$widgets = array(
				'porto_info_box'                 => 'porto-sicon-box',
				'porto_interactive_banner'       => 'vce-element-porto-banner',
				'porto_interactive_banner_layer' => 'vce-element-porto-banner-layer',
				'porto_price_box'                => 'porto-price-box',
				'porto_ultimate_heading'         => 'porto-u-heading',
				'vc_progress_bar'                => 'porto-vc-progressbar',
				'vc_tabs'                        => 'vce-tab tabs',
				'porto_fancytext'                => 'word-rotator ',
			);
			global $wpdb;
			foreach ( $widgets as $widget => $cls ) {
				if ( in_array( $widget, $used ) ) {
					continue;
				}
				$post_id = $wpdb->get_col( 'SELECT ID FROM ' . esc_sql( $wpdb->posts ) . ' WHERE post_type not in ("revision", "attachment") AND post_status = "publish" and post_content LIKE \'%class="' . esc_sql( $cls ) . '%\' LIMIT 1' );
				if ( ! empty( $post_id ) ) {
					$used[] = $widget;
				}
			}
		}

		return $used;
	}

	/**
	 * Shows admin notice when plugin is activated without Porto Functionality
	 *
	 * @return void
	 * @since 1.0
	 */
	public function install_functionality_admin_notice() {
		?>
		<div class="error">
			<p><?php echo esc_html( 'Porto VC Addon ' . __( 'is enabled but not effective. It requires Porto Functionality to work.', 'porto-vc-addon' ) ); ?></p>
		</div>
		<?php
	}

	/**
	 * Shows admin notice when plugin is activated without Visual Composer
	 *
	 * @return void
	 * @since 1.0
	 */
	public function install_vc_admin_notice() {
		?>
		<div class="error">
			<p><?php echo esc_html( 'Porto VC Addon ' . __( 'is enabled but not effective. It requires Visual Composer to work.', 'porto-vc-addon' ) ); ?></p>
		</div>
		<?php
	}
}

/**
 * Instantiate the Class
 *
 * @since     1.0
 */
new Porto_VC_Addon;
