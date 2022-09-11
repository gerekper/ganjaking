<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Assets class. This is used to load script and styles.
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YITH_WC_Subscription_Assets' ) ) {
	/**
	 * Class that handles the assets
	 *
	 * @class  YITH_WC_Subscription_Assets
	 */
	class YITH_WC_Subscription_Assets {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Subscription_Assets
		 */
		private static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WC_Subscription_Assets
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WC_Subscription_Assets constructor.
		 */
		private function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'register_common_scripts' ), 11 );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_common_scripts' ), 11 );

			if ( YITH_WC_Subscription::is_request( 'admin' ) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 11 );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 11 );
			} else {
				add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_scripts' ), 11 );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ), 11 );
			}

			// Script Translations.
			add_filter( 'pre_load_script_translations', array( $this, 'script_translations' ), 10, 4 );

		}

		/**
		 * Return the suffix of script.
		 *
		 * @return string
		 */
		private function get_suffix() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			return $suffix;
		}

		/**
		 * Register common scripts
		 */
		public function register_common_scripts() {

		}

		/**
		 * Register admin scripts
		 */
		public function register_admin_scripts() {
			$suffix = $this->get_suffix();
			$screen = get_current_screen();

			wp_register_style( 'yith-ywsbs-backend', YITH_YWSBS_ASSETS_URL . '/css/backend.css', array( 'woocommerce_admin_styles', 'jquery-ui-style' ), YITH_YWSBS_VERSION );
			wp_register_style( 'yith-ywsbs-product', YITH_YWSBS_ASSETS_URL . '/css/ywsbs-product-editor.css', array( 'yith-plugin-fw-fields' ), YITH_YWSBS_VERSION );
			wp_register_style( 'yith-ywsbs-order', YITH_YWSBS_ASSETS_URL . '/css/ywsbs-order-editor.css', false, YITH_YWSBS_VERSION );
			wp_register_script( 'yith-ywsbs-timepicker', YITH_YWSBS_ASSETS_URL . '/js/jquery-ui-timepicker-addon.min.js', array( 'jquery', 'jquery-ui-datepicker' ), YITH_YWSBS_VERSION, true );
			wp_register_script( 'yith-ywsbs-admin', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-admin' . $suffix . '.js', array( 'jquery', 'yith-ywsbs-timepicker', 'jquery-ui-dialog' ), YITH_YWSBS_VERSION, true );
			wp_register_script( 'jquery-blockui', YITH_YWSBS_ASSETS_URL . '/js/jquery.blockUI.min.js', array( 'jquery' ), YITH_YWSBS_VERSION, true );
			wp_register_script( 'ywsbs-subscription-admin', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-subscription-admin' . $suffix . '.js', array( 'jquery', 'yith-ywsbs-timepicker', 'jquery-blockui', 'woocommerce_admin', 'jquery-ui-dialog' ), YITH_YWSBS_VERSION, true );
			wp_register_script( 'yith-ywsbs-product', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-product-editor' . $suffix . '.js', array( 'jquery' ), YITH_YWSBS_VERSION, true );
			wp_register_script( 'yith-ywsbs-order', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-order-editor' . $suffix . '.js', array( 'jquery' ), YITH_YWSBS_VERSION, true );
			wp_register_script( 'yith-ywsbs-admin-notices', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-admin-notices' . $suffix . '.js', array( 'jquery' ), YITH_YWSBS_VERSION, true );
			wp_register_script( 'yith-ywsbs-admin-coupon', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-subscription-coupon' . $suffix . '.js', array( 'jquery' ), YITH_YWSBS_VERSION, true );

			$dashboard_deps = array( 'wp-api-fetch', 'wp-components', 'wp-element', 'wp-hooks', 'wp-i18n', 'wp-data', 'wc-components' );
			$dashboard_js   = version_compare( WC()->version, '6.7.0', '>=' ) ? 'dist/dashboard/index.js' : 'dist/legacy/dashboard/index.js';
			wp_register_script( 'yith-ywsbs-admin-dashboard', YITH_YWSBS_URL . $dashboard_js, $dashboard_deps, YITH_YWSBS_VERSION, true );
			wp_register_script( 'datatables', 'https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js', array( 'jquery' ), true, false );

			$delivery_args = array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'delivery_nonce' => wp_create_nonce( 'delivery_nonce' ),
				'continue'       => esc_html_x( 'Continue', 'Label button of a dialog popup', 'yith-woocommerce-subscription' ),
				'cancel'         => esc_html_x( 'Cancel', 'Label button of a dialog popup', 'yith-woocommerce-subscription' ),
			);

			$args = array(
				'ajaxurl'                      => admin_url( 'admin-ajax.php' ),
				'block_loader'                 => apply_filters( 'yith_ywsbs_block_loader_admin', YITH_YWSBS_ASSETS_URL . '/images/block-loader.gif' ),
				'time_format'                  => apply_filters( 'ywsbs_time_format', 'Y-m-d H:i:s' ),
				'copy_billing'                 => esc_html__( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'yith-woocommerce-subscription' ),
				'load_billing'                 => esc_html__( 'Load the customer\'s billing information? This will remove any currently entered billing information.', 'yith-woocommerce-subscription' ),
				'no_customer_selected'         => esc_html__( 'User is not registered', 'yith-woocommerce-subscription' ),
				'get_customer_details_nonce'   => wp_create_nonce( 'get-customer-details' ),
				'save_item_nonce'              => wp_create_nonce( 'save-item-nonce' ),
				'recalculate_nonce'            => wp_create_nonce( 'recalculate_nonce' ),
				'load_shipping'                => esc_html__( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', 'yith-woocommerce-subscription' ),
				'back_to_all_subscription'     => esc_html__( 'back to all subscriptions', 'yith-woocommerce-subscription' ),
				'url_back_to_all_subscription' => add_query_arg( array( 'post_type' => YITH_YWSBS_POST_TYPE ), admin_url( 'edit.php' ) ),
				'datatable_lengthMenu'         => esc_html_x( 'Items per page:', 'Metabox table length menu', 'yith-woocommerce-subscription' ),
				'add_coupon_text'              => esc_html_x( 'Enter a coupon code to apply. Discounts are applied to line totals, before taxes.', 'text displayed on a popup in administrator subscription detail', 'yith-woocommerce-subscription' ),
			);

			wp_localize_script( 'ywsbs-subscription-admin', 'ywsbs_subscription_admin', array_merge( $args, $delivery_args ) );
			wp_localize_script( 'yith-ywsbs-admin', 'ywsbs_admin', $delivery_args );

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'ywsbs-plans-editor-script', 'yith-woocommerce-subscription', YITH_YWSBS_DIR . 'languages' );
				wp_set_script_translations( 'yith-ywsbs-admin-dashboard', 'yith-woocommerce-subscription', YITH_YWSBS_DIR . 'languages' );
			}
		}

		/**
		 * Register frontend scripts
		 */
		public function register_frontend_scripts() {

			wp_register_style( 'yith_ywsbs_frontend', YITH_YWSBS_ASSETS_URL . '/css/frontend.css', false, YITH_YWSBS_VERSION );
			wp_register_script( 'yith_ywsbs_frontend', YITH_YWSBS_ASSETS_URL . '/js/ywsbs-frontend' . YITH_YWSBS_SUFFIX . '.js', array( 'jquery', 'wc-add-to-cart-variation', 'jquery-blockui' ), YITH_YWSBS_VERSION, true );

			wp_localize_script(
				'yith_ywsbs_frontend',
				'yith_ywsbs_frontend',
				array(
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'add_to_cart_label'  => apply_filters( 'ywsbs_add_to_cart_variation_label', get_option( 'ywsbs_add_to_cart_label' ) ),
					'default_cart_label' => apply_filters( 'ywsbs_add_to_cart_default_label', __( 'Add to cart', 'yith-woocommerce-subscription' ) ),
				)
			);
		}

		/**
		 * Enqueue admin scripts
		 */
		public function enqueue_admin_scripts() {

			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( 'edit-shop_coupon' === $screen_id || ywsbs_check_valid_admin_page( 'shop_coupon' ) ) {
				wp_enqueue_script( 'yith-ywsbs-admin-coupon' );
			}

			if ( 'edit-' . YITH_YWSBS_POST_TYPE === $screen_id || ywsbs_check_valid_admin_page( YITH_YWSBS_POST_TYPE ) ) {
				wp_enqueue_style( 'yith-ywsbs-backend' );
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_script( 'wc-enhanced-select' );
				wp_enqueue_script( 'ywsbs-subscription-admin' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'datatables' );

				$locale  = localeconv();
				$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

				$params = array(
					/* translators: %s: decimal */
					'i18n_decimal_error'                => sprintf( __( 'Please enter with one decimal point (%s) without thousand separators.', 'yith-woocommerce-subscription' ), $decimal ),
					/* translators: %s: price decimal separator */
					'i18n_mon_decimal_error'            => sprintf( __( 'Please enter with one monetary decimal point (%s) without thousand separators and currency symbols.', 'yith-woocommerce-subscription' ), wc_get_price_decimal_separator() ),
					'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'yith-woocommerce-subscription' ),
					'i18n_sale_less_than_regular_error' => __( 'Please enter in a value less than the regular price.', 'yith-woocommerce-subscription' ),
					'i18n_delete_product_notice'        => __( 'This product has produced sales and may be linked to existing orders. Are you sure you want to delete it?', 'yith-woocommerce-subscription' ),
					'i18n_remove_personal_data_notice'  => __( 'This action cannot be reversed. Are you sure you wish to erase personal data from the selected orders?', 'yith-woocommerce-subscription' ),
					'decimal_point'                     => $decimal,
					'mon_decimal_point'                 => wc_get_price_decimal_separator(),
					'ajax_url'                          => admin_url( 'admin-ajax.php' ),
					'strings'                           => array(
						'import_products' => __( 'Import', 'yith-woocommerce-subscription' ),
						'export_products' => __( 'Export', 'yith-woocommerce-subscription' ),
					),
					'nonces'                            => array(
						'gateway_toggle' => wp_create_nonce( 'woocommerce-toggle-payment-gateway-enabled' ),
					),
					'urls'                              => array(
						'import_products' => current_user_can( 'import' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ) : null,
						'export_products' => current_user_can( 'export' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ) : null,
					),
				);

				wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
			}

			if ( ( isset( $_GET['page'] ) && $_GET['page'] === 'yith_woocommerce_subscription' ) ) { //phpcs:ignore
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'yith-ywsbs-admin' );
				wp_enqueue_script( 'jquery-blockui' );
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_script( 'wc-enhanced-select' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_style( 'yith-ywsbs-backend' );
				wp_enqueue_style( 'yit-plugin-style' );
			}

			if ( ywsbs_check_valid_admin_page( 'product' ) || ywsbs_check_valid_admin_page( 'product_variable' ) ) {
				wp_enqueue_style( 'yith-ywsbs-product' );
				wp_enqueue_script( 'yith-ywsbs-product' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
			}

			if ( ywsbs_check_valid_admin_page( 'shop_order' ) ) {

				global $post;
				$order = wc_get_order( $post->ID );
				if ( $order ) {
					$subscriptions = $order->get_meta( 'subscriptions' );
					if ( ! empty( $subscriptions ) ) {
						$is_a_renew = $order->get_meta( 'is_a_renew' );
						$args       = array(
							'order_label' => $is_a_renew ? esc_html__( 'Subscription Renew', 'yith-woocommerce-subscription' ) : esc_html__( 'Subscription Main Order', 'yith-woocommerce-subscription' ),
						);

						wp_localize_script( 'yith-ywsbs-order', 'ywsbs_order_admin', $args );
						wp_enqueue_script( 'yith-ywsbs-order' );
						wp_enqueue_style( 'yith-ywsbs-order' );
						wp_add_inline_style( 'yith-ywsbs-order', $this->get_subscription_status_inline_style() );
					}
				}
			}

			if ( apply_filters( 'ywsbs_enable_report', true ) ) {
				$is_dashboard_page = 'yith-plugins_page_yith_woocommerce_subscription' === $screen_id && ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && 'dashboard' === $_GET['tab'] ); //phpcs:ignore

				if ( $is_dashboard_page && yith_ywsbs_is_wc_admin_enabled() ) {
					wp_enqueue_style( 'wc-components' );
					wp_enqueue_style( defined( 'WC_ADMIN_APP' ) ? WC_ADMIN_APP : 'wc-admin-app' );
					wp_enqueue_script( 'yith-ywsbs-admin-dashboard' );
					wp_enqueue_script( 'wc-material-icons' );

					wp_localize_script( 'yith-ywsbs-admin-dashboard', 'ywsbsSettings', $this->get_dashboard_settings() );

				}
			}

			wp_add_inline_style( 'yith-ywsbs-backend', $this->get_subscription_status_inline_style() );

		}

		/**
		 * Enqueue frontend scripts
		 */
		public function enqueue_frontend_scripts() {

			if ( ! apply_filters( 'ywsbs_load_assets', true ) ) {
				return;
			}

			wp_enqueue_style( 'yith_ywsbs_frontend' );
			wp_enqueue_script( 'yith_ywsbs_frontend' );

			wp_add_inline_style( 'yith_ywsbs_frontend', $this->get_frontend_inline_style() );
		}

		/**
		 * Generate custom css.
		 *
		 * @return string
		 */
		public function get_frontend_inline_style() {
			$status_colors = ywsbs_get_status_colors();
			$trial_color   = get_option( 'ywsbs_show_trial_period_color', '#467484' );
			$fee_color     = get_option( 'ywsbs_show_fee_color_color', '#467484' );

			$css  = '.ywsbs-signup-fee{color:' . $fee_color . ';}';
			$css .= '.ywsbs-trial-period{color:' . $trial_color . ';}';

			foreach ( $status_colors as $status => $colors ) {
				$css .= 'span.status.' . $status . '{ color:' . $colors['background-color'] . ';} ';
			}
			return $css;
		}

		/**
		 * Generate custom css.
		 *
		 * @return string
		 */
		public function get_subscription_status_inline_style() {
			$status_colors = ywsbs_get_status_colors();
			$css           = '';

			foreach ( $status_colors as $status => $colors ) {
				$css .= 'span.status.' . $status . '{ color:' . $colors['color'] . ';background-color:' . $colors['background-color'] . ';} ';
			}
			return $css;
		}

		/**
		 * Create the json translation through the PHP file
		 * so it's possible using normal translations (with PO files) also for JS translations
		 *
		 * @param string|null $json_translations Json translation.
		 * @param string      $file File.
		 * @param string      $handle Handle.
		 * @param string      $domain Domain.
		 *
		 * @return string|null
		 */
		public function script_translations( $json_translations, $file, $handle, $domain ) {
			if ( 'yith-woocommerce-subscription' === $domain && in_array( $handle, array( 'ywsbs-plans-editor-script', 'yith-ywsbs-admin-dashboard' ), true ) ) {
				$path = YITH_YWSBS_DIR . 'languages/yith-woocommerce-subscription.php';
				if ( file_exists( $path ) ) {
					$translations = include $path;

					$json_translations = wp_json_encode(
						array(
							'domain'      => 'yith-woocommerce-subscription',
							'locale_data' => array(
								'messages' =>
									array(
										'' => array(
											'domain'       => 'yith-woocommerce-subscription',
											'lang'         => get_locale(),
											'plural-forms' => 'nplurals=2; plural=(n != 1);',
										),
									)
									+
									$translations,
							),
						)
					);

				}
			}

			return $json_translations;
		}

		/**
		 * Return the list of settings useful to the Subscription Dashboard
		 */
		private function get_dashboard_settings() {
			$settings = array(
				'wc' => self::get_wc_data(),
			);

			return apply_filters( 'ywsbs_dashboard_settings', $settings );
		}

		/** -------------------------------------------------------
		 * Public Static Getters - to get specific settings
		 */

		/**
		 * Get the WC data
		 *
		 * @return array
		 */
		public static function get_wc_data() {
			$currency_code = get_woocommerce_currency();

			$wc_settings = array(
				'currency'      => array(
					'code'               => $currency_code,
					'precision'          => wc_get_price_decimals(),
					'symbol'             => html_entity_decode( get_woocommerce_currency_symbol( $currency_code ) ),
					'position'           => get_option( 'woocommerce_currency_pos' ),
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'price_format'       => html_entity_decode( get_woocommerce_price_format() ),
				),
				'date_format'   => wc_date_format(),
				'status_labels' => ywsbs_get_status_label_counter(),

			);

			return $wc_settings;
		}


	}
}
