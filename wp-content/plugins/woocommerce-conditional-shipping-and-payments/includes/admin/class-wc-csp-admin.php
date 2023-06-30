<?php
/**
 * WC_CSP_Admin class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product CSP Restrictions Admin Class.
 *
 * Loads admin tabs and adds related hooks / filters.
 *
 * @version  1.15.2
 */
class WC_CSP_Admin {

	/**
	 * Bundled selectSW library version.
	 *
	 * @var string
	 */
	private static $bundled_selectsw_version = '1.2.1';

	/**
	 * @var array
	 */
	private $save_errors = array();

	/*
	 * Setup admin class.
	 */
	public function __construct() {

		// Admin initializations.
		add_action( 'init', array( $this, 'admin_init' ) );

		// selectSW scripts.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_register_selectsw' ), 0 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_load_selectsw' ), 1 );
		add_action( 'admin_notices', array( __CLASS__, 'maybe_display_selectsw_notice' ), 0 );

		// Admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 11 );

		// Add body class for WP 5.3 compatibility.
		add_filter( 'admin_body_class', array( __CLASS__, 'include_admin_body_class' ) );

		/*
		 * Product Settings.
		 */

		// Creates the "Restrictions" tab.
		add_action( 'woocommerce_product_data_tabs', array( $this, 'product_data_tabs' ) );

		// Creates the panel for configuring product options.
		if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ) {
			add_action( 'woocommerce_product_data_panels', array( $this, 'product_data_panel' ) );
		} else {
			add_action( 'woocommerce_product_write_panels', array( $this, 'product_data_panel' ) );
		}

		// Processes and saves the necessary post meta from the selections made above.
		if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ) {
			add_action( 'woocommerce_admin_process_product_object', array( $this, 'process_product_data' ) );
		} else {
			add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta' ) );
		}

		// Add a notice if product-level restrictions are disabled.
		add_action( 'admin_notices', array( $this, 'maybe_add_disabled_restrictions_notice' ), 0 );

		/*
		 * Global Settings.
		 */

		// Add global 'Restrictions' tab to WooCommerce settings.
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_restrictions_settings_page' ) );

		/*
		 * Product Filters.
		 */

		// Add products dropdown filter by restriction type.
		add_filter( 'woocommerce_product_filters', array( $this, 'add_restrictions_product_filter' ) );

		// Apply the filter to the product list.
		add_filter( 'request', array( $this, 'request_query' ) );

		/*
		 * WP Privacy Guide.
		 */

 		// Add a message in the WP Privacy Policy Guide page.
		add_action( 'admin_init', array( $this, 'add_privacy_policy_guide_content' ) );

		/*
		 * Notices.
		 */

		// Dismiss notices.
		add_action( 'wp_ajax_woocommerce_dismiss_csp_notice', array( __CLASS__ , 'dismiss_notice' ) );
	}

	/**
	 * Do stuff on the admin init hook.
	 *
	 * @since  1.4.0
	 * @return void
	 */
	public function admin_init() {
		$this->includes();
	}

	/**
	 * Include stuff on the admin init hook.
	 *
	 * @since  1.4.0
	 * @return void
	 */
	private function includes() {

		// Product Import/Export.
		if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '3.1' ) ) {
			require_once( WC_CSP_ABSPATH . 'includes/admin/class-wc-csp-product-import-export.php' );
		}

		// Notices.
		require_once( WC_CSP_ABSPATH . 'includes/admin/class-wc-csp-admin-notices.php' );

		// Admin AJAX.
		require_once( WC_CSP_ABSPATH . 'includes/admin/class-wc-csp-admin-ajax.php' );
	}

	/**
	 * Include admin classes.
	 *
	 * @since  1.5.9
	 *
	 * @param  String  $classes
	 * @return String
	 */
	public static function include_admin_body_class( $classes ) {

		if ( strpos( $classes, 'sw-wp-version-gte-53' ) !== false ) {
			return $classes;
		}

		if ( WC_CSP_Core_Compatibility::is_wp_version_gte( '5.3' ) ) {
			$classes .= ' sw-wp-version-gte-53';
		}

		return $classes;
	}

	/**
	 * Register own version of select2 library.
	 *
	 * @since 1.6.0
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
			wp_register_script( 'sw-admin-select-init', WC_CSP()->plugin_url() . '/assets/js/admin/select2-init' . $suffix . '.js', array( 'jquery', 'sw-admin-select' ), self::$bundled_selectsw_version );
		}
	}

	/**
	 * Load own version of select2 library.
	 *
	 * @since 1.6.0
	 */
	public static function maybe_load_selectsw() {

		// Responsible for loading selectsw?
		if ( self::load_selectsw() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Register selectSW library.
			wp_register_script( 'sw-admin-select', WC_CSP()->plugin_url() . '/assets/js/admin/select2' . $suffix . '.js', array( 'jquery' ), self::$bundled_selectsw_version );

			// Register selectSW styles.
			wp_register_style( 'sw-admin-css-select', WC_CSP()->plugin_url() . '/assets/css/admin/select2.css', array(), self::$bundled_selectsw_version );
			wp_style_add_data( 'sw-admin-css-select', 'rtl', 'replace' );
		}
	}

	/**
	 * Display notice when selectSW library is unsupported.
	 *
	 * @since 1.6.0
	 */
	public static function maybe_display_selectsw_notice() {

		if ( ! wp_scripts()->query( 'sw-admin-select-init' ) ) {
			return;
		}

		$registered_version       = wp_scripts()->registered[ 'sw-admin-select-init' ]->ver;
		$registered_version_major = strstr( $registered_version, '.', true );
		$bundled_version_major    = strstr( self::$bundled_selectsw_version, '.', true );

		if ( version_compare( $bundled_version_major, $registered_version_major, '<' ) ) {
			$notice = __( 'The installed version of <strong>Conditional Shipping and Payments</strong> is not compatible with the <code>selectSW</code> library found on your system. Please update Conditional Shipping and Payments to the latest version.', 'woocommerce-conditional-shipping-and-payments' );
			WC_CSP_Admin_Notices::add_notice( $notice, 'error' );
		}
	}

	/**
	 * Whether to load own version of select2 library or not.
	 *
	 * @since   1.6.0
	 *
	 * @return  boolean
	 */
	private static function load_selectsw() {

		$load_selectsw_from = wp_scripts()->registered[ 'sw-admin-select-init' ]->src;

		return strpos( $load_selectsw_from, WC_CSP()->plugin_url() ) === 0;
	}

	/**
	 * Admin product writepanel scripts.
	 *
	 * @return void
	 */
	public function admin_scripts() {

		global $post;

		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );

		// Get admin screen id.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		$is_product_page              = in_array( $screen_id, array( 'product' ) );
		$is_edit_product_page         = in_array( $screen_id, array( 'edit-product' ) );
		$is_restrictions_settings_tab = $screen_id === $wc_screen_id . '_page_wc-settings' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'restrictions';

		// Product metaboxes.
		if ( $is_product_page || $is_edit_product_page ) {

			wp_register_script( 'wc-restrictions-writepanel', WC_CSP()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-util', 'wc-admin-meta-boxes', 'sw-admin-select-init' ), WC_Conditional_Shipping_Payments::VERSION );
			wp_register_style( 'wc-restrictions-css', WC_CSP()->plugin_url() . '/assets/css/admin/meta-boxes.css', array( 'woocommerce_admin_styles', 'sw-admin-css-select' ), WC_Conditional_Shipping_Payments::VERSION );
			wp_style_add_data( 'wc-restrictions-css', 'rtl', 'replace' );

		} elseif ( $is_restrictions_settings_tab ) {

			wp_register_script( 'wc-global-restrictions-writepanel', WC_CSP()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-util', 'sw-admin-select-init' ), WC_Conditional_Shipping_Payments::VERSION );
			wp_register_style( 'wc-restrictions-css', WC_CSP()->plugin_url() . '/assets/css/admin/meta-boxes.css', array( 'woocommerce_admin_styles', 'sw-admin-css-select' ), WC_Conditional_Shipping_Payments::VERSION );
			wp_style_add_data( 'wc-restrictions-css', 'rtl', 'replace' );
		}

		wp_register_style( 'wc-restrictions-admin-css', WC_CSP()->plugin_url() . '/assets/css/admin/admin.css', array(), WC_Conditional_Shipping_Payments::VERSION );
		wp_style_add_data( 'wc-restrictions-admin-css', 'rtl', 'replace' );
		wp_enqueue_style( 'wc-restrictions-admin-css' );

		$params = array(
			'add_restriction_nonce'       => wp_create_nonce( 'wc_restrictions_add_restriction' ),
			'toggle_restriction_nonce'    => wp_create_nonce( 'wc_restrictions_toggle_restriction' ),
			'wc_ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'post_id'                     => isset( $post->ID ) && ! $is_restrictions_settings_tab ? $post->ID : '',
			'wc_plugin_url'               => WC()->plugin_url(),
			'is_wc_version_gte_3_0'       => WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ? 'yes' : 'no',
			'is_wc_version_gte_3_2'       => WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? 'yes' : 'no',
			'billing_states_data'         => WC()->countries->get_allowed_country_states(),
			'shipping_states_data'        => WC()->countries->get_shipping_country_states(),
			'continents_data'             => WC()->countries->get_shipping_continents(),
			'countries_data'              => WC()->countries->get_shipping_countries(),
			'i18n_delete_rule_warning'    => __('This rule will be permanently deleted from your system. Are you sure?', 'woocommerce-conditional-shipping-and-payments'),
			'i18n_toggle_session_expired' => _x( 'Something went wrong. Please refresh your browser and try again.', 'active toggler', 'woocommerce' ),
			'i18n_matches_1'              => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
			'i18n_matches_n'              => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
			'i18n_no_matches'             => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
			'i18n_ajax_error'             => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_short_1'      => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_short_n'      => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_long_1'       => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_long_n'       => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
			'i18n_selection_too_long_1'   => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
			'i18n_selection_too_long_n'   => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
			'i18n_load_more'              => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
			'i18n_searching'              => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
		);

		if ( $is_product_page ) {
			wp_enqueue_script( 'wc-restrictions-writepanel' );
			wp_localize_script( 'wc-restrictions-writepanel', 'wc_restrictions_admin_params', $params );

		} elseif ( $is_restrictions_settings_tab ) {
			wp_enqueue_script( 'wc-global-restrictions-writepanel' );
			wp_localize_script( 'wc-global-restrictions-writepanel', 'wc_restrictions_admin_params', $params );
			wp_enqueue_style( 'wc-restrictions-css' );
		}

		if ( $is_edit_product_page || $is_product_page ) {
			wp_enqueue_style( 'wc-restrictions-css' );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Global Settings.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Add 'Restrictions' tab to WooCommerce Settings tabs.
	 *
	 * @since  1.0
	 * @param  array $settings
	 * @return array $settings
	 */
	public function add_restrictions_settings_page( $settings ) {

		$settings[] = include( WC_CSP_ABSPATH . 'includes/admin/settings/class-wc-csp-settings-restrictions.php' );

		return $settings;
	}

	/*
	|--------------------------------------------------------------------------
	| Product Meta-Boxes.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Restrictions writepanel tab.
	 *
	 * @param  array  $tabs
	 * @return array
	 */
	public function product_data_tabs( $tabs ) {

		$tabs[ 'csp_restrictions' ] = array(
			'label'    => __( 'Restrictions', 'woocommerce-conditional-shipping-and-payments' ),
			'target'   => 'restrictions_data',
			'class'    => array( 'restrictions_options', 'restrictions_tab' ),
			'priority' => 1000
		);

		return $tabs;
	}

	/**
	 * Product writepanel for Restrictions.
	 *
	 * @return void
	 */
	public function product_data_panel() {

		global $post, $product_object;

		if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ) {
			$product_restrictions_meta = is_object( $product_object ) ? $product_object->get_meta( '_wccsp_restrictions', true ) : false;
		} else {
			$product_restrictions_meta = get_post_meta( $post->ID, '_wccsp_restrictions', true );
		}

		$restrictions         = WC_CSP()->restrictions->get_admin_product_field_restrictions();
		$applied_restrictions = WC_CSP()->restrictions->maybe_update_restriction_data( $product_restrictions_meta, 'product' );

		// Generate data hash for dirty checking.
		$data_hash = md5( json_encode( $applied_restrictions ) );

		?>
		<div id="restrictions_data" class="panel csp_product_panel woocommerce_options_panel wc-metaboxes-wrapper <?php echo empty( $restrictions ) ? 'restrictions_data--empty' : ''; ?> <?php echo esc_attr( WC_CSP_Core_Compatibility::get_versions_class() ); ?>" style="display:none">

			<div class="hr-section hr-section--conditions-and temp-placeholder">
				<?php esc_html_e( 'And', 'woocommerce-conditional-shipping-and-payments' ); ?>
			</div>

			<div class="options_group">

				<div class="toolbar toolbar--product">

					<select name="_restriction_type" class="restriction_type">
						<option value=""><?php esc_html_e( 'Choose restriction&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<?php

						foreach ( $restrictions as $restriction_id => $restriction ) {
							echo '<option value="' . esc_attr( $restriction_id ) . '">' . esc_html( $restriction->get_title() ) . '</option>';
						}

						?>
					</select>
					<button type="button" class="button add_restriction"><?php esc_html_e( 'Add', 'woocommerce-conditional-shipping-and-payments' ); ?></button>

					<span class="bulk_toggle_wrapper <?php echo empty( $applied_restrictions ) ? 'disabled' : ''; ?>">
						<a href="#" class="expand_all"><?php esc_html_e( 'Expand all', 'woocommerce' ); ?></a>
						<a href="#" class="close_all"><?php esc_html_e( 'Close all', 'woocommerce' ); ?></a>
					</span>
				</div>

				<div class="woocommerce_restrictions wc-metaboxes ui-sortable" data-hash="<?php echo esc_attr( $data_hash ); ?>">
					<?php

					if ( $applied_restrictions ) {
						foreach ( $applied_restrictions as $index => $restriction_data ) {

							$restriction_id = $restriction_data[ 'restriction_id' ];
							$restriction    = WC_CSP()->restrictions->get_restriction( $restriction_id );

							if ( $restriction ) {
								$restriction->get_admin_product_metaboxes_content( $index, $restriction_data );
							}
						}
					}
					// Empty state.
					?>
					<div class="woocommerce_restrictions__boarding">
						<div class="woocommerce_restrictions__boarding__message product_level_boarding">
							<p><?php esc_attr_e( 'This product does not exclude any shipping or payment options.', 'woocommerce-conditional-shipping-and-payments' ); ?></p>
						</div>
					</div>
				</div>

			</div>

		</div>
		<?php
	}

	/**
	 * Gets posted product restriction data.
	 */
	public function get_posted_product_restriction_data() {

		$restriction_data = array();
		$count            = 0;
		$loop             = 0;

		if ( isset( $_POST[ 'restriction' ] ) ) {

			$posted_restrictions_data = $_POST[ 'restriction' ]; // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			uasort( $posted_restrictions_data, array( $this, 'cmp' ) );

			foreach ( $posted_restrictions_data as &$posted_restriction_data ) {

				$posted_restriction_data[ 'index' ] = $loop + 1;

				if ( isset( $posted_restriction_data[ 'restriction_id' ] ) ) {

					$restriction_id = stripslashes( $posted_restriction_data[ 'restriction_id' ] );
					$restriction    = WC_CSP()->restrictions->get_restriction( $restriction_id );

					if ( $restriction && $restriction->has_admin_product_fields() ) {
						$processed_data = $restriction->process_admin_product_fields( $posted_restriction_data );
					}

					if ( $processed_data ) {

						$processed_data = apply_filters( 'woocommerce_csp_process_admin_product_fields', $processed_data, $posted_restriction_data, $restriction_id );

						$processed_data[ 'restriction_id' ] = $restriction_id;
						$processed_data[ 'index' ]          = $count;
						$processed_data[ 'enabled' ]        = ( $posted_restriction_data[ 'enabled' ] === 'yes' ) ? 'yes' : 'no';

						$processed_data[ 'wc_26_shipping' ] = 'yes';

						$restriction_data[ $count ] = $processed_data;
						$count++;
					}

					$loop++;
				}
			}
		}

		return $restriction_data;
	}

	/**
	 * Process, verify and save restriction product data.
	 *
	 * @param  int  $post_id
	 */
	public function process_product_meta( $post_id ) {

		$restriction_data = $this->get_posted_product_restriction_data();

		if ( ! empty( $restriction_data ) ) {
			update_post_meta( $post_id, '_wccsp_restrictions', $restriction_data );
		} else {
			delete_post_meta( $post_id, '_wccsp_restrictions' );
		}

		// Clear cached shipping rates.
		WC_CSP_Core_Compatibility::clear_cached_shipping_rates();
	}

	/**
	 * Process, verify and save restriction product data.
	 *
	 * @param  WC_Product  $product
	 */
	public function process_product_data( $product ) {

		$restriction_data = $this->get_posted_product_restriction_data();

		if ( ! empty( $restriction_data ) ) {
			$product->update_meta_data( '_wccsp_restrictions', $restriction_data );
		} else {
			$product->delete_meta_data( '_wccsp_restrictions' );
		}

		// Clear cached shipping rates.
		WC_CSP_Core_Compatibility::clear_cached_shipping_rates();
	}

	/**
	 * Add a notice if product-level restrictions are globally disabled.
	 *
	 * @return void
	 */
	public function maybe_add_disabled_restrictions_notice() {

		global $post_id;

		// Get admin screen ID.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product' !== $screen_id ) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$restrictions = get_post_meta( $post_id, '_wccsp_restrictions', true );;

		if ( ! $restrictions ) {
			return;
		}

		$disable_restrictions = 'yes' === get_option( 'wccsp_restrictions_disable_product', false );

		if ( $disable_restrictions ) {

			$enable_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=wc-settings&tab=restrictions#wccsp_restrictions_debug-description' ), __( 're-enable product-level restrictions', 'woocommerce-conditional-shipping-and-payments' ) );
			WC_CSP_Admin_Notices::add_notice( sprintf( __( 'Product restrictions are currently disabled globally. You can still edit, create and delete rules under <strong>Product Data > Restrictions</strong> &ndash; however, they will have no effect until you %s.', 'woocommerce-conditional-shipping-and-payments' ), $enable_link ), 'warning', false );

		} elseif ( 'yes' === get_post_meta( $post_id, '_virtual', true ) ) {

			$has_package_restrictions = false;

			foreach ( $restrictions as $restriction ) {
				if ( isset( $restriction[ 'restriction_id' ] ) && in_array( $restriction[ 'restriction_id' ], array( 'shipping_methods', 'shipping_countries' ) ) ) {
					$has_package_restrictions = true;
					break;
				}
			}

			if ( $has_package_restrictions ) {
				WC_CSP_Admin_Notices::add_notice( __( 'Product restrictions for <strong>Shipping Methods</strong> and <strong>Shipping Destinations</strong> do not work with <strong>Virtual</strong> products. You can still create and edit such restrictions under <strong>Product Data > Restrictions</strong> &ndash; however, they will have no effect when purchasing this product.', 'woocommerce-conditional-shipping-and-payments' ), 'warning', false );
			}
		}
	}

	/**
	 * Sort posted restriction data.
	 */
    private function cmp( $a, $b ) {

	    if ( $a[ 'position' ] == $b[ 'position' ] ) {
	        return 0;
	    }

	    return ( $a[ 'position' ] < $b[ 'position' ] ) ? -1 : 1;
	}

	/*
	|--------------------------------------------------------------------------
	| Privacy.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Message to add in the WP Privacy Policy Guide page.
	 *
	 * @since  1.3.4
	 *
	 * @return string
	 */
	protected static function get_privacy_policy_guide_message() {

		$content = '
			<div contenteditable="false">' .
				'<p class="wp-policy-help">' .
					__( 'Conditional Shipping and Payments does not collect, store or share any personal data.', 'woocommerce-conditional-shipping-and-payments' ) .
				'</p>' .
			'</div>' .
			'<p>' .
				__( 'However, WooCommerce collects personal data from customers which can be used by Conditional Shipping and Payments to conditionally disable specific shipping and payment options. Please keep in mind that, depending on the context, limiting the checkout options for specific customer groups may violate laws in your jurisdiction.', 'woocommerce-conditional-shipping-and-payments' ) .
			'</p>';


		return $content;
	}

	/**
	 * Add a message in the WP Privacy Policy Guide page.
	 *
	 * @since  1.3.4
	 */
	public static function add_privacy_policy_guide_content() {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content( 'WooCommerce Conditional Shipping and Payments', self::get_privacy_policy_guide_message() );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Product Filters.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Add a new filter by restictions inside the admin products list.
	 *
	 * @since 1.4.0
	 *
	 * @param string $post_type
	 * @return void
	 */
	public function add_restrictions_product_filter( $filters ) {

		// Init selected filter.
		$selected_filter = '';

		// Check for applied filter.
		if ( isset( $_GET[ 'product_restrictions' ] ) && in_array( $_GET[ 'product_restrictions' ], array_keys( $this->get_restriction_filters() ) ) ) {
			$selected_filter = wc_clean( $_GET[ 'product_restrictions' ] );
		}

		ob_start();

		// Add your filter input here. Make sure the input name matches the $_GET value you are checking above.
		echo '<select name="product_restrictions" id="dropdown_product_restrictions">';

		// Default value.
		echo '<option value>' . esc_html__( 'Filter by restriction', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';

		foreach ( $this->get_restriction_filters() as $filter_key => $filter ) {
			$selected = $filter_key === $selected_filter ? ' selected="selected"' : '';
			echo '<option value="' . esc_attr( $filter_key ) . '"' . $selected . '>' . esc_html( $filter[ 'label' ] ) . '</option>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</select>';

		$new_filters = ob_get_clean();

		$output = $filters . $new_filters;

		return $output;
	}

	/**
	 * Query new filter by restictions.
	 *
	 * @since 1.4.0
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	public function query_filters( $query_vars ) {

		// Restrictions filter query.
		if ( isset( $_GET[ 'product_restrictions' ] ) && in_array( $_GET[ 'product_restrictions' ], array_keys( $this->get_restriction_filters() ) ) ) {

			// Initialize variables.
			$current_filter = wc_clean( $_GET[ 'product_restrictions' ] );
			$term           = false;

			// Find the current term to search.
			foreach ( $this->get_restriction_filters() as $filter_key => $filter ) {
				if ( $current_filter === $filter_key ) {
					$term = $filter[ 'term' ];
				}
			}

			if ( $term ) {

				if ( $term !== 'any' ) {

					/**
					 * Note:
					 * It's important to keep all the restriction keys distict to each other and the condition keys, so
					 * the LIKE query won't fail to correctly distinct between restrictions.
					 *
					 * Ref: https://github.com/somewherewarm/woocommerce-conditional-shipping-and-payments/issues/28
					 */
					$meta_key_query = array(
						array(
							'key'     => '_wccsp_restrictions',
							'compare' => 'LIKE',
							'value'   => '"' . $term . '"'
						)
					);
				} else {

					// Just check if meta key exists.
					$meta_key_query = array(
						array(
							'key'     => '_wccsp_restrictions',
							'compare' => 'EXISTS'
						)
					);
				}

				if ( ! isset( $query_vars[ 'meta_query' ] ) ) {
					$query_vars[ 'meta_query' ] = array();
				}

				// Add on top of other filters as well.
				$query_vars[ 'meta_query' ][] = $meta_key_query;
			}
		}

		return $query_vars;
	}

	/**
	 * Handle any filters.
	 *
	 * @since 1.4.0
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	public function request_query( $query_vars ) {

		$current_screen = get_current_screen();
		$post_type      = is_object( $current_screen ) ? $current_screen->post_type : '';
		$base           = is_object( $current_screen ) ? $current_screen->base : '';

		if ( 'edit' === $base && 'product' === $post_type ) {
			return $this->query_filters( $query_vars );
		}

		return $query_vars;
	}

	/**
	 * Get all restriction filters.
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	public function get_restriction_filters() {

		// Prefix key.
		$key_prefix = 'restrict-';

		// Setup available filters.
		$filters = array(
			$key_prefix . 'all'   => array(
				'label' => __( 'Any restriction', 'woocommerce-conditional-shipping-and-payments' ),
				'term'  => 'any'
			)
		);

		// Get all restrictions.
		$restrictions = WC_CSP()->restrictions->get_restrictions();

		foreach ( $restrictions as $key => $retriction ) {

			$filter_key = $key_prefix . str_replace( '_', '-', $retriction->id );

			$filters[ $filter_key ] = array(
				'label' => $retriction->title,
				'term'  => $retriction->id
			);
		}

		return $filters;
	}

	/*
	|--------------------------------------------------------------------------
	| Notices.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Dismisses notices.
	 *
	 * @since  1.5.0
	 *
	 * @return void
	 */
	public static function dismiss_notice() {

		$failure = array(
			'result' => 'failure'
		);

		if ( ! check_ajax_referer( 'wc_csp_dismiss_notice_nonce', 'security', false ) ) {
			wp_send_json( $failure );
		}

		if ( empty( $_POST[ 'notice' ] ) ) {
			wp_send_json( $failure );
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json( $failure );
		}

		$dismissed = WC_CSP_Admin_Notices::dismiss_notice( wc_clean( $_POST[ 'notice' ] ) );

		if ( ! $dismissed ) {
			wp_send_json( $failure );
		}

		$response = array(
			'result' => 'success'
		);

		wp_send_json( $response );
	}
}
