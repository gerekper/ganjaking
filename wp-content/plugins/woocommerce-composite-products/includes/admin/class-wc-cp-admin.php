<?php
/**
 * WC_CP_Admin class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    2.2.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup admin hooks.
 *
 * @class    WC_CP_Admin
 * @version  7.0.4
 */
class WC_CP_Admin {

	/**
	 * Bundled selectSW library version.
	 *
	 * @var string
	 */
	private static $bundled_selectsw_version = '1.1.6';

	/**
	 * Setup admin hooks.
	 */
	public static function init() {

		// Admin initializations.
		add_action( 'init', array( __CLASS__, 'admin_init' ) );

		// Add a message in the WP Privacy Policy Guide page.
		add_action( 'admin_init', array( __CLASS__, 'add_privacy_policy_guide_content' ) );

		// selectSW scripts.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_register_selectsw' ), 0 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_load_selectsw' ), 1 );
		add_action( 'admin_notices', array( __CLASS__, 'maybe_display_selectsw_notice' ), 0 );

		// Admin scripts.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'composite_admin_scripts' ) );

		// Template override scan path.
		add_filter( 'woocommerce_template_overrides_scan_paths', array( __CLASS__, 'composite_template_scan_path' ) );

		// Add CP debug data in the system status.
		add_action( 'woocommerce_system_status_report', array( __CLASS__ , 'render_system_status_items' ) );

		// Add body class for WP 5.3 compatibility.
		add_filter( 'admin_body_class', array( __CLASS__, 'include_admin_body_class' ) );
	}

	/**
	 * Admin init.
	 */
	public static function admin_init() {
		self::includes();
	}

	/**
	 * Register own version of select2 library.
	 *
	 * @since 5.1.0
	 */
	public static function maybe_register_selectsw() {

		$is_registered      = wp_script_is( 'sw-admin-select-init', $list = 'registered' );
		$registered_version = $is_registered ? wp_scripts()->registered[ 'sw-admin-select-init' ]->ver : '';
		$register           = ! $is_registered || version_compare( self::$bundled_selectsw_version, $registered_version, '>' );

		if ( $register ) {

			if ( $is_registered ) {
				wp_deregister_script( 'sw-admin-select-init' );
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Register own select2 initialization library.
			wp_register_script( 'sw-admin-select-init', WC_CP()->plugin_url() . '/assets/js/admin/select2-init' . $suffix . '.js', array( 'jquery', 'sw-admin-select' ), self::$bundled_selectsw_version );
		}
	}

	/**
	 * Load own version of select2 library.
	 *
	 * @since 5.1.0
	 */
	public static function maybe_load_selectsw() {

		// Responsible for loading selectsw?
		if ( self::load_selectsw() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Register selectSW library.
			wp_register_script( 'sw-admin-select', WC_CP()->plugin_url() . '/assets/js/admin/select2' . $suffix . '.js', array( 'jquery' ), self::$bundled_selectsw_version );

			// Enqueue selectSW styles.
			wp_register_style( 'sw-admin-css-select', WC_CP()->plugin_url() . '/assets/css/admin/select2.css', array(), self::$bundled_selectsw_version );
			wp_style_add_data( 'sw-admin-css-select', 'rtl', 'replace' );
		}
	}

	/**
	 * Display notice when selectSW library is unsupported.
	 *
	 * @since 5.1.0
	 */
	public static function maybe_display_selectsw_notice() {

		if ( ! wp_scripts()->query( 'sw-admin-select-init' ) ) {
			return;
		}

		$registered_version       = wp_scripts()->registered[ 'sw-admin-select-init' ]->ver;
		$registered_version_major = strstr( $registered_version, '.', true );
		$bundled_version_major    = strstr( self::$bundled_selectsw_version, '.', true );

		if ( version_compare( $bundled_version_major, $registered_version_major, '<' ) ) {
			$notice = __( 'The installed version of <strong>Composite Products</strong> is not compatible with the <code>selectSW</code> library found on your system. Please update Composite Products to the latest version.', 'woocommerce-composite-products' );
			WC_CP_Admin_Notices::add_notice( $notice, 'error' );
		}
	}

	/**
	 * Whether to load own version of select2 library or not.
	 *
	 * @since   5.1.0
	 *
	 * @return  boolean
	 */
	private static function load_selectsw() {

		$load_selectsw_from = wp_scripts()->registered[ 'sw-admin-select-init' ]->src;

		return strpos( $load_selectsw_from, WC_CP()->plugin_url() ) === 0;
	}

	/**
	 * Include classes.
	 */
	public static function includes() {

		// Product Import/Export.
		if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.1' ) ) {
			require_once( WC_CP_ABSPATH . 'includes/admin/export/class-wc-cp-product-export.php' );
			require_once( WC_CP_ABSPATH . 'includes/admin/import/class-wc-cp-product-import.php' );
		}

		// Metaboxes.
		require_once( WC_CP_ABSPATH . 'includes/admin/meta-boxes/class-wc-cp-meta-box-product-data.php' );

		// Post type stuff.
		require_once( WC_CP_ABSPATH . 'includes/admin/class-wc-cp-admin-post-types.php' );

		// Admin AJAX.
		require_once( WC_CP_ABSPATH . 'includes/admin/class-wc-cp-admin-ajax.php' );

		// Admin edit-order screen.
		if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.2' ) ) {
			require_once( WC_CP_ABSPATH . 'includes/admin/class-wc-cp-admin-order.php' );
		}
	}

	/**
	 * Include admin classes.
	 *
	 * @since  5.0.5
	 *
	 * @param  String  $classes
	 * @return String
	 */
	public static function include_admin_body_class( $classes ) {

		if ( strpos( $classes, 'sw-wp-version-gte-53' ) !== false ) {
			return $classes;
		}

		if ( WC_CP_Core_Compatibility::is_wp_version_gte( '5.3' ) ) {
			$classes .= ' sw-wp-version-gte-53';
		}

		return $classes;
	}

	/**
	 * Message to add in the WP Privacy Policy Guide page.
	 *
	 * @since  3.13.10
	 *
	 * @return string
	 */
	protected static function get_privacy_policy_guide_message() {

		$content = '
			<div contenteditable="false">' .
				'<p class="wp-policy-help">' .
					__( 'Composite Products does not collect, store or share any personal data.', 'woocommerce-composite-products' ) .
				'</p>' .
			'</div>';

		return $content;
	}

	/**
	 * Add a message in the WP Privacy Policy Guide page.
	 *
	 * @since  3.13.10
	 */
	public static function add_privacy_policy_guide_content() {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content( 'WooCommerce Composite Products', self::get_privacy_policy_guide_message() );
		}
	}

	/**
	 * Include scripts.
	 */
	public static function composite_admin_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc-composite-admin-product-panel', WC_CP()->plugin_url() . '/assets/js/admin/meta-boxes-product' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-util', 'wc-admin-product-meta-boxes', 'sw-admin-select-init' ), WC_CP()->version );
		wp_register_script( 'wc-composite-admin-order-panel', WC_CP()->plugin_url() . '/assets/js/admin/meta-boxes-order' . $suffix . '.js', array( 'wc-admin-order-meta-boxes', 'sw-admin-select-init' ), WC_CP()->version );

		wp_register_style( 'wc-composite-admin-css', WC_CP()->plugin_url() . '/assets/css/admin/admin.css', array(), WC_CP()->version );
		wp_style_add_data( 'wc-composite-admin-css', 'rtl', 'replace' );

		wp_register_style( 'wc-composite-writepanel-css', WC_CP()->plugin_url() . '/assets/css/admin/meta-boxes-product.css', array( 'woocommerce_admin_styles', 'sw-admin-css-select' ), WC_CP()->version );
		wp_style_add_data( 'wc-composite-writepanel-css', 'rtl', 'replace' );

		wp_register_style( 'wc-composite-edit-order-css', WC_CP()->plugin_url() . '/assets/css/admin/meta-boxes-order.css', array( 'woocommerce_admin_styles', 'sw-admin-css-select' ), WC_CP()->version );
		wp_style_add_data( 'wc-composite-edit-order-css', 'rtl', 'replace' );

		wp_enqueue_style( 'wc-composite-admin-css' );

		// Get admin screen ID.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		/*
		 * Enqueue styles.
		 */
		if ( in_array( $screen_id, array( 'edit-product', 'product' ) ) ) {
			wp_enqueue_style( 'wc-composite-writepanel-css' );
		} elseif ( in_array( $screen_id, array( 'shop_order', 'edit-shop_order', 'shop_subscription', 'edit-shop_subscription' ) ) ) {
			wp_enqueue_style( 'wc-composite-edit-order-css' );
		}

		/*
		 * Enqueue scripts.
		 */
		if ( 'product' === $screen_id ) {

			wp_enqueue_script( 'wc-composite-admin-product-panel' );

			$params = array(
				'save_composite_nonce'         => wp_create_nonce( 'wc_bto_save_composite' ),
				'add_component_nonce'          => wp_create_nonce( 'wc_bto_add_component' ),
				'add_scenario_nonce'           => wp_create_nonce( 'wc_bto_add_scenario' ),
				'add_state_nonce'              => wp_create_nonce( 'wc_bto_add_state' ),
				'get_product_categories_nonce' => wp_create_nonce( 'wc_bto_get_product_categories' ),
				'layouts'                      => array_keys( WC_Product_Composite::get_layout_options() ),
				'wc_placeholder_img_src'       => wc_placeholder_img_src(),
				'is_first_composite'           => isset( $_GET[ 'wc_cp_first_composite' ] ) ? 'yes' : 'no',
				'is_wc_version_gte_3_2'        => WC_CP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? 'yes' : 'no',
				// Strings.
				'i18n_save_error'              => __( 'Your settings could not be saved. Please refresh the page and try again.', 'woocommerce-composite-products' ),
				'i18n_no_default'              => __( 'No default option&hellip;', 'woocommerce-composite-products' ),
				'i18n_none'                    => _x( 'No selection', 'optional component property controlled in scenarios', 'woocommerce-composite-products' ),
				'i18n_choose_component_image'  => __( 'Choose a Component Image', 'woocommerce-composite-products' ),
				'i18n_set_component_image'     => __( 'Set Component Image', 'woocommerce-composite-products' ),
				'i18n_defaults_unset'          => __( 'Please ensure that a Default Option is set in all non-optional Components before choosing \'Use Defaults\' as the preferred Catalog Price display method for this Composite.', 'woocommerce-composite-products' ),
				'i18n_set_defaults_static'     => __( 'The Default Option field cannot be cleared &ndash; you have added a single <strong>Component Option</strong> without checking the <strong>Optional</strong> box.', 'woocommerce-composite-products' ),
				'i18n_set_defaults'            => __( 'A Default Option must be chosen in all non-optional Components when the <strong>Catalog Price</strong> display method is set to <strong>Use Defaults</strong>.', 'woocommerce-composite-products' ),
				'i18n_scenarios_panel_blocked' => __( 'To configure Scenarios, the changes you made under the Components tab must be saved. Save changes now?', 'woocommerce-composite-products' ),
				'i18n_states_panel_blocked'    => __( 'To configure States, the changes you made under the Components tab must be saved. Save changes now?', 'woocommerce-composite-products' ),
				// Strings duplicated from core.
				'i18n_matches_1'               => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
				'i18n_matches_n'               => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
				'i18n_no_matches'              => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
				'i18n_ajax_error'              => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_short_1'       => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_short_n'       => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_long_1'        => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_long_n'        => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
				'i18n_selection_too_long_1'    => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
				'i18n_selection_too_long_n'    => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
				'i18n_load_more'               => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
				'i18n_searching'               => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' )
			);

			wp_localize_script( 'wc-composite-admin-product-panel', 'wc_composite_admin_params', $params );

		} elseif ( in_array( $screen_id, array( 'shop_order', 'shop_subscription' ) ) ) {

			wp_enqueue_script( 'wc-composite-admin-order-panel' );

			$params = array(
				'edit_composite_nonce'           => wp_create_nonce( 'wc_bto_edit_composite' ),
				'is_wc_version_gte_3_4'          => WC_CP_Core_Compatibility::is_wc_version_gte( '3.4' ) ? 'yes' : 'no',
				'is_wc_version_gte_3_6'          => WC_CP_Core_Compatibility::is_wc_version_gte( '3.6' ) ? 'yes' : 'no',
				'i18n_configure'                 => __( 'Configure', 'woocommerce-composite-products' ),
				'i18n_edit'                      => __( 'Edit', 'woocommerce-composite-products' ),
				'i18n_form_error'                => __( 'Failed to initialize form. If this issue persists, please reload the page and try again.', 'woocommerce-composite-products' ),
				'i18n_validation_error'          => __( 'Failed to validate configuration. If this issue persists, please reload the page and try again.', 'woocommerce-composite-products' ),
				'i18n_selection_request_timeout' => __( 'Your selection could not be updated. If the issue persists, please refresh the page and try again.', 'woocommerce-composite-products' )
			);

			wp_localize_script( 'wc-composite-admin-order-panel', 'wc_composite_admin_order_params', $params );
		}
	}

	/**
	 * Support scanning for template overrides in extension.
	 *
	 * @param  array  $paths
	 * @return array
	 */
	public static function composite_template_scan_path( $paths ) {
		$paths[ 'WooCommerce Composite Products' ] = WC_CP()->plugin_path() . '/templates/';
		return $paths;
	}

	/**
	 * Add CP debug data in the system status.
	 *
	 * @since  3.13.9
	 */
	public static function render_system_status_items() {

		$debug_data = array(
			'db_version'           => get_option( 'woocommerce_composite_products_db_version', null ),
			'loopback_test_result' => WC_CP_Notices::get_notice_option( 'loopback', 'last_result', '' ),
			'overrides'            => self::get_template_overrides()
		);

		include( WC_CP_ABSPATH . 'includes/admin/views/html-admin-page-status-report.php' );
	}

	/**
	 * Determine which of our files have been overridden by the theme.
	 *
	 * @since  3.13.9
	 *
	 * @return array
	 */
	private static function get_template_overrides() {

		$template_path    = WC_CP()->plugin_path() . '/templates/';
		$templates        = WC_Admin_Status::scan_template_files( $template_path );
		$wc_template_path = trailingslashit( WC()->template_path() );
		$theme_root       = trailingslashit( get_theme_root() );

		$overridden = array();

		foreach ( $templates as $file ) {

			$found_location  = false;
			$check_locations = array(
				get_stylesheet_directory() . "/{$file}",
				get_stylesheet_directory() . "/{$wc_template_path}{$file}",
				get_template_directory() . "/{$file}",
				get_template_directory() . "/{$wc_template_path}{$file}"
			);

			foreach ( $check_locations as $location ) {
				if ( is_readable( $location ) ) {
					$found_location = $location;
					break;
				}
			}

			if ( ! empty( $found_location ) ) {

				$core_version  = WC_Admin_Status::get_file_version( $template_path . $file );
				$found_version = WC_Admin_Status::get_file_version( $found_location );
				$is_outdated   = $core_version && ( empty( $found_version ) || version_compare( $found_version, $core_version, '<' ) );

				if ( false !== strpos( $found_location, '.php' ) ) {
					$overridden[] = array(
						'file'         => str_replace( $theme_root, '', $found_location ),
						'version'      => $found_version,
						'core_version' => $core_version,
						'is_outdated'  => $is_outdated,
					);
				}
			}
		}

		return $overridden;
	}
}

WC_CP_Admin::init();
