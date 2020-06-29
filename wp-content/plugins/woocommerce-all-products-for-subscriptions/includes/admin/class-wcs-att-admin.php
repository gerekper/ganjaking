<?php
/**
 * WCS_ATT_Admin class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce All Products For Subscriptions
 * @since    1.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin includes and hooks.
 *
 * @class    WCS_ATT_Admin
 * @version  3.1.9
 */
class WCS_ATT_Admin {

	/**
  	 * Bundled selectSW library version.
  	 *
  	 * @var string
  	 */
  	private static $bundled_selectsw_version = '1.1.3';

	/**
	 * Initialize.
	 */
	public static function init() {
		self::add_hooks();
	}

	/**
	 * Add hooks.
	 */
	private static function add_hooks() {

		/*
		 * Single-Product settings.
		 */

		// Metabox includes.
		add_action( 'init', array( __CLASS__, 'admin_init' ) );

		// selectSW scripts.
 		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_register_selectsw' ), 0 );
 		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_load_selectsw' ), 1 );
 		add_action( 'admin_notices', array( __CLASS__, 'maybe_display_selectsw_notice' ), 0 );

		// Admin scripts and styles.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		// WP 5.3+ compatibility.
		add_filter( 'admin_body_class', array( __CLASS__, 'include_admin_body_class' ) );

		/*
		 * Subscribe-to-Cart settings.
		 */

		// Append "Subscribe to Cart/Order" section in the Subscriptions settings tab.
		add_filter( 'woocommerce_subscription_settings', array( __CLASS__, 'add_settings' ), 100 );

		// Save posted cart subscription scheme settings.
		add_action( 'woocommerce_update_options_subscriptions', array( __CLASS__, 'save_cart_level_settings' ) );

		// Display subscription scheme admin metaboxes in the "Subscribe to Cart/Order" section.
		add_action( 'woocommerce_admin_field_subscription_schemes', array( __CLASS__, 'subscription_schemes_content' ) );

		/*
		 * Extra 'Allow Switching' checkboxes.
		 */

		add_filter( 'woocommerce_subscriptions_allow_switching_options', array( __CLASS__, 'allow_switching_options' ) );

		// Add template override scan path in tracking info.
		add_filter( 'woocommerce_template_overrides_scan_paths', array( __CLASS__, 'template_scan_path' ) );

		// Add APFS debug data in the system status.
		add_action( 'woocommerce_system_status_report', array( __CLASS__ , 'render_system_status_items' ) );
	}

	/**
	 * Admin init.
	 */
	public static function admin_init() {
		self::includes();
	}

	/**
	 * Include classes.
	 */
	public static function includes() {

		if ( WCS_ATT_Core_Compatibility::is_wc_version_gte( '3.1' ) ) {
			require_once( 'export/class-wcs-att-product-export.php' );
			require_once( 'import/class-wcs-att-product-import.php' );
		}

		require_once( 'class-wcs-att-admin-ajax.php' );
		require_once( 'meta-boxes/class-wcs-att-meta-box-product-data.php' );
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
  			wp_register_script( 'sw-admin-select-init', WCS_ATT()->plugin_url() . '/assets/js/admin/select2-init' . $suffix . '.js', array( 'jquery', 'sw-admin-select' ), self::$bundled_selectsw_version );
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
  			wp_register_script( 'sw-admin-select', WCS_ATT()->plugin_url() . '/assets/js/admin/select2' . $suffix . '.js', array( 'jquery' ), self::$bundled_selectsw_version );

  			// Register selectSW styles.
  			wp_register_style( 'sw-admin-css-select', WCS_ATT()->plugin_url() . '/assets/css/admin/select2.css', array(), self::$bundled_selectsw_version );
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
  			$notice = __( 'The installed version of <strong>All Products for WooCommerce Subscriptions</strong> is not compatible with the <code>selectSW</code> library found on your system. Please update All Products for WooCommerce Subscriptions to the latest version.', 'woocommerce-all-products-for-subscriptions' );
  			WCS_ATT_Admin_Notices::add_notice( $notice, 'error' );
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

  		return strpos( $load_selectsw_from, WCS_ATT()->plugin_url() ) === 0;
  	}

	/**
	 * Include admin classes.
	 *
	 * @since  2.4.3
	 *
	 * @param  String  $classes
	 * @return String
	 */
	public static function include_admin_body_class( $classes ) {

		if ( strpos( $classes, 'sw-wp-version-gte-53' ) !== false ) {
			return $classes;
		}

		if ( WCS_ATT_Core_Compatibility::is_wp_version_gte( '5.3' ) ) {
			$classes .= ' sw-wp-version-gte-53';
		}

		return $classes;
	}

	/**
	 * Add extra 'Allow Switching > 'Between Subscription Plans' option.
	 * In the past there was no option to turn off this feature.
	 *
	 * @param  array  $data
	 * @return array
	 */
	public static function allow_switching_options( $data ) {

		$switch_option_product_plans = get_option( 'woocommerce_subscriptions_allow_switching_product_plans', '' );

		if ( '' === $switch_option_product_plans ) {
			update_option( 'woocommerce_subscriptions_allow_switching_product_plans', 'yes' );
		}

		return array_merge( $data, array(
			array(
				'id'    => 'product_plans',
				'label' => __( 'Between Subscription Plans', 'woocommerce-all-products-for-subscriptions' )
			)
		) );
	}

	/**
	 * Subscriptions schemes admin metaboxes.
	 *
	 * @param  array  $values
	 * @return void
	 */
	public static function subscription_schemes_content( $values ) {

		$subscription_schemes = get_option( 'wcsatt_subscribe_to_cart_schemes', array() );

		?><tr valign="top">
			<th scope="row" class="titledesc"><?php echo esc_html( $values[ 'title' ] ) ?></th>
			<td class="forminp forminp-subscription_schemes_metaboxes">
				<p class="description"><?php echo esc_html( $values[ 'desc' ] ) ?></p>
				<div id="wcsatt_data" class="wc-metaboxes-wrapper <?php echo empty( $subscription_schemes ) ? 'planless' : ''; ?>">
					<div class="subscription_schemes wc-metaboxes ui-sortable" data-count=""><?php

						$i = 0;

						foreach ( $subscription_schemes as $subscription_scheme ) {
							do_action( 'wcsatt_subscription_scheme', $i, $subscription_scheme, '' );
							$i++;
						}

					?></div>
					<p class="subscription_schemes_add_wrapper">
						<button type="button" class="button add_subscription_scheme"><?php _e( 'Add Plan', 'woocommerce-all-products-for-subscriptions' ); ?></button>
					</p>
				</div>
			</td>
		</tr><?php
	}

	/**
	 * Append "Subscribe to Cart/Order" section in the Subscriptions settings tab.
	 *
	 * @since  2.1.0
	 *
	 * @param  array  $settings
	 * @return array
	 */
	public static function add_settings( $settings ) {

		// Insert before miscellaneous settings.
		$misc_section_start = wp_list_filter( $settings, array( 'id' => 'woocommerce_subscriptions_miscellaneous', 'type' => 'title' ) );

		$spliced_array = array_splice( $settings, key( $misc_section_start ), 0, array(
			array(
				'name' => __( 'Subscribe to Cart', 'woocommerce-all-products-for-subscriptions' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wcsatt_subscribe_to_cart_options'
			),
			array(
				'name' => __( 'Cart Subscription Plans', 'woocommerce-all-products-for-subscriptions' ),
				'desc' => __( 'Subscription plans offered on the cart page.', 'woocommerce-all-products-for-subscriptions' ),
				'id'   => 'wcsatt_subscribe_to_cart_schemes',
				'type' => 'subscription_schemes'
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wcsatt_subscribe_to_cart_options'
			),
			array(
				'name' => __( 'Add to Subscription', 'woocommerce-all-products-for-subscriptions' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wcsatt_add_to_subscription_options'
			),
			array(
				'name'     => __( 'Products', 'woocommerce-all-products-for-subscriptions' ),
				'desc'     => __( 'Allow customers to add products to existing subscriptions.', 'woocommerce-all-products-for-subscriptions' ),
				'id'       => 'wcsatt_add_product_to_subscription',
				'type'     => 'select',
				'options'  => array(
					'off'              => _x( 'Off', 'adding a product to an existing subscription', 'woocommerce-all-products-for-subscriptions' ),
					'matching_schemes' => _x( 'On For Products With Subscription Plans', 'adding a product to an existing subscription', 'woocommerce-all-products-for-subscriptions' ),
					'on'               => _x( 'On', 'adding a product to an existing subscription', 'woocommerce-all-products-for-subscriptions' ),
				),
				'desc_tip' => true
			),
			array(
				'name'     => __( 'Carts', 'woocommerce-all-products-for-subscriptions' ),
				'desc'     => __( 'Allow customers to add their cart to an existing subscription.', 'woocommerce-all-products-for-subscriptions' ),
				'id'       => 'wcsatt_add_cart_to_subscription',
				'type'     => 'select',
				'options'  => array(
					'off'      => _x( 'Off', 'adding a cart to an existing subscription', 'woocommerce-all-products-for-subscriptions' ),
					'on'       => _x( 'On', 'adding a cart to an existing subscription', 'woocommerce-all-products-for-subscriptions' ),
				),
				'desc_tip' => true
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wcsatt_add_to_subscription_options'
			)
		) );

		return $settings;
	}

	/**
	 * Save subscription scheme option from the WooCommerce > Settings > Subscriptions administration screen.
	 *
	 * @return void
	 */
	public static function save_cart_level_settings() {

		if ( isset( $_POST[ 'wcsatt_schemes' ] ) ) {
			$posted_schemes = $_POST[ 'wcsatt_schemes' ];
		} else {
			$posted_schemes = array();
		}

		$posted_schemes = stripslashes_deep( $posted_schemes );
		$unique_schemes = array();

		foreach ( $posted_schemes as $posted_scheme ) {

			/**
			 * Allow third parties to add custom data to schemes.
			 *
			 * @since  3.1.0
			 *
			 * @param  array       $posted_scheme
			 * @param  WC_Product  $product
			 */
			$posted_scheme = apply_filters( 'wcsatt_processed_cart_scheme_data', $posted_scheme );

			// Construct scheme id.
			$scheme_id = $posted_scheme[ 'subscription_period_interval' ] . '_' . $posted_scheme[ 'subscription_period' ] . '_' . $posted_scheme[ 'subscription_length' ];

			$unique_schemes[ $scheme_id ]         = $posted_scheme;
			$unique_schemes[ $scheme_id ][ 'id' ] = $scheme_id;
		}

		update_option( 'wcsatt_subscribe_to_cart_schemes', $unique_schemes );
	}

	/**
	 * Load scripts and styles.
	 *
	 * @return void
	 */
	public static function admin_scripts() {

		global $post;

		// Get admin screen id.
		$screen      = get_current_screen();
		$screen_id   = $screen ? $screen->id : '';
		$add_scripts = false;
		$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( in_array( $screen_id, array( 'edit-product', 'product' ) ) ) {
			$add_scripts             = true;
			$writepanel_dependencies = array( 'jquery', 'jquery-ui-datepicker', 'wc-admin-meta-boxes', 'wc-admin-product-meta-boxes' );
		} elseif ( $screen_id === 'woocommerce_page_wc-settings' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'subscriptions' ) {
			$add_scripts             = true;
			$writepanel_dependencies = array( 'jquery', 'jquery-ui-datepicker' );
		}

		if ( $add_scripts ) {
			wp_register_script( 'wcsatt-writepanel', WCS_ATT()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', $writepanel_dependencies, WCS_ATT::VERSION );
			wp_register_style( 'wcsatt-writepanel-css', WCS_ATT()->plugin_url() . '/assets/css/admin/meta-boxes.css', array( 'woocommerce_admin_styles' ), WCS_ATT::VERSION );
			wp_style_add_data( 'wcsatt-writepanel-css', 'rtl', 'replace' );
			wp_enqueue_style( 'wcsatt-writepanel-css' );
		}

		// Always needed.
		wp_register_style( 'wcsatt-admin-css', WCS_ATT()->plugin_url() . '/assets/css/admin/admin.css', array(), WCS_ATT::VERSION );
		wp_enqueue_style( 'wcsatt-admin-css' );

		// WooCommerce admin pages.
		if ( in_array( $screen_id, array( 'product', 'woocommerce_page_wc-settings' ) ) ) {

			wp_enqueue_script( 'wcsatt-writepanel' );

			$params = array(
				'add_subscription_scheme_nonce'      => wp_create_nonce( 'wcsatt_add_subscription_scheme' ),
				'subscription_lengths'               => wcs_get_subscription_ranges(),
				'i18n_do_no_sync'                    => __( 'Disabled', 'woocommerce-all-products-for-subscriptions' ),
				'i18n_inherit_option'                => __( 'Inherit from product', 'woocommerce-all-products-for-subscriptions' ),
				'i18n_inherit_option_variable'       => __( 'Inherit from chosen variation', 'woocommerce-all-products-for-subscriptions' ),
				'i18n_override_option'               => __( 'Override product', 'woocommerce-all-products-for-subscriptions' ),
				'i18n_override_option_variable'      => __( 'Override all variations', 'woocommerce-all-products-for-subscriptions' ),
				'i18n_discount_description'          => __( 'Discount applied over the <strong>Regular Price</strong> of the product.', 'woocommerce-all-products-for-subscriptions' ),
				'i18n_discount_description_variable' => __( 'Discount applied over the <strong>Regular Price</strong> of the chosen variation.', 'woocommerce-all-products-for-subscriptions' ),
				'is_onboarding'                      => isset( $_GET[ 'wcsatt_onboarding' ] ) ? 'yes' : 'no',
				'wc_ajax_url'                        => admin_url( 'admin-ajax.php' ),
				'post_id'                            => is_object( $post ) ? $post->ID : '',
			);

			wp_localize_script( 'wcsatt-writepanel', 'wcsatt_admin_params', $params );
		}

		// Oboarding-only (expanding buttons).
		if ( WCS_ATT_Admin_Notices::is_maintenance_notice_visible( 'welcome' ) && ! WCS_ATT_Admin_Notices::is_dismissible_notice_dismissed( 'welcome' ) ) {
			// If already enqueued, WP should prevent these from doing anything.
			wp_enqueue_script( 'sw-admin-select-init' );
			wp_enqueue_style( 'sw-admin-css-select' );
		}
	}

	/**
	 * Support scanning for template overrides in extension.
	 *
	 * @since 3.1.8
	 *
	 * @param  array  $paths
	 * @return array
	 */
	public static function template_scan_path( $paths ) {

		$paths[ 'All Products for WooCommerce Subscriptions' ] = WCS_ATT()->plugin_path() . '/templates/';

		return $paths;
	}

	/**
	 * Add APFS debug data in the system status.
	 *
	 * @since 3.1.8
	 */
	public static function render_system_status_items() {

		$debug_data = array(
			'overrides' => self::get_template_overrides()
		);

		include( 'views/html-admin-page-status-report.php' );
	}

	/**
	 * Determine which of our files have been overridden by the theme.
	 *
	 * @since  3.1.8
	 *
	 * @return array
	 */
	private static function get_template_overrides() {

		$template_path    = WCS_ATT()->plugin_path() . '/templates/';
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

WCS_ATT_Admin::init();
