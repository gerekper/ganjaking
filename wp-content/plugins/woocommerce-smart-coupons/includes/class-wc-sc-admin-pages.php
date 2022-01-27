<?php
/**
 * Smart Coupons Admin Pages
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.5.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Admin_Pages' ) ) {

	/**
	 * Class for handling admin pages of Smart Coupons
	 */
	class WC_SC_Admin_Pages {

		/**
		 * Variable to hold instance of WC_SC_Admin_Pages
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_filter( 'views_edit-shop_coupon', array( $this, 'smart_coupons_views_row' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'generate_coupon_styles_and_scripts' ), 99 );
			add_action( 'admin_notices', array( $this, 'woocommerce_show_import_message' ) );

			add_action( 'admin_menu', array( $this, 'woocommerce_coupon_admin_menu' ) );
			add_action( 'admin_head', array( $this, 'woocommerce_coupon_admin_head' ) );

			add_action( 'admin_footer', array( $this, 'smart_coupons_script_in_footer' ) );
			add_action( 'admin_init', array( $this, 'woocommerce_coupon_admin_init' ) );

			add_action( 'smart_coupons_display_views', array( $this, 'smart_coupons_display_views' ) );

			add_action( 'parse_request', array( $this, 'filter_coupons_using_meta' ) );
			add_filter( 'get_search_query', array( $this, 'filter_coupons_using_meta_label' ), 100 );

			add_filter( 'woocommerce_navigation_is_connected_page', array( $this, 'woocommerce_navigation_is_connected_page' ), 10, 2 );
			add_filter( 'woocommerce_navigation_get_breadcrumbs', array( $this, 'woocommerce_navigation_breadcrumbs' ), 10, 2 );

		}

		/**
		 * Get single instance of WC_SC_Admin_Pages
		 *
		 * @return WC_SC_Admin_Pages Singleton object of WC_SC_Admin_Pages
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Function to trigger an additional hook while creating different views
		 *
		 * @param array $views Available views.
		 * @return array $views
		 */
		public function smart_coupons_views_row( $views = null ) {

			global $typenow;

			if ( 'shop_coupon' === $typenow ) {
				do_action( 'smart_coupons_display_views' );
			}

			return $views;

		}

		/**
		 * Function to add tabs to access Smart Coupons' feature
		 */
		public function smart_coupons_display_views() {
			global $store_credit_label;

			?>
			<div id="smart_coupons_tabs">
				<h2 class="nav-tab-wrapper">
					<?php
						echo '<a href="' . esc_url( add_query_arg( array( 'post_type' => 'shop_coupon' ), admin_url( 'edit.php' ) ) ) . '" class="nav-tab nav-tab-active">' . esc_html__( 'Coupons', 'woocommerce-smart-coupons' ) . '</a>';
						echo '<a href="' . esc_url( add_query_arg( array( 'page' => 'wc-smart-coupons' ), admin_url( 'admin.php' ) ) ) . '" class="nav-tab">' . esc_html__( 'Bulk Generate', 'woocommerce-smart-coupons' ) . '</a>';
						echo '<a href="' . esc_url(
							add_query_arg(
								array(
									'page' => 'wc-smart-coupons',
									'tab'  => 'import-smart-coupons',
								),
								admin_url( 'admin.php' )
							)
						) . '" class="nav-tab">' . esc_html__( 'Import Coupons', 'woocommerce-smart-coupons' ) . '</a>';
						echo '<a href="' . esc_url(
							add_query_arg(
								array(
									'page' => 'wc-smart-coupons',
									'tab'  => 'send-smart-coupons',
								),
								admin_url( 'admin.php' )
							)
							/* translators: %s: singular name for store credit */
						) . '" class="nav-tab">' . ( ! empty( $store_credit_label['singular'] ) ? sprintf( esc_html__( 'Send %s', 'woocommerce-smart-coupons' ), esc_html( ucwords( $store_credit_label['singular'] ) ) ) : esc_html__( 'Send Store Credit', 'woocommerce-smart-coupons' ) ) . '</a>';
						echo '<div class="sc-quick-links"><a href="' . esc_url(
							add_query_arg(
								array(
									'page' => 'wc-settings',
									'tab'  => 'wc-smart-coupons',
								),
								admin_url( 'admin.php' )
							)
						) . '" target="_blank">' . esc_html__( 'Smart Coupons Settings', 'woocommerce-smart-coupons' ) . '</a> | <a href="' . esc_url(
							add_query_arg(
								array(
									'page' => 'sc-faqs',
								),
								admin_url( 'admin.php' )
							)
						) . '" target="_blank">' . esc_html__( 'FAQ\'s', 'woocommerce-smart-coupons' ) . '</a></div>';
					?>
				</h2>
			</div>
			<?php
		}

		/**
		 * Function to include styles & script for 'Generate Coupon' page
		 */
		public function generate_coupon_styles_and_scripts() {
			global $pagenow, $wp_scripts;
			if ( empty( $pagenow ) || 'admin.php' !== $pagenow ) {
				return;
			}

			$get_page = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
			if ( 'wc-smart-coupons' !== $get_page ) {
				return;
			}

			$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			$locale  = localeconv();
			$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

			wp_enqueue_style( 'woocommerce_admin_menu_styles', WC()->plugin_url() . '/assets/css/menu.css', array(), WC()->version );
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC()->version );
			if ( ! wp_style_is( 'jquery-ui-style', 'registered' ) ) {
				wp_register_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui' . $suffix . '.css', array(), WC()->version );
			}
			if ( ! wp_style_is( 'jquery-ui-style' ) ) {
				wp_enqueue_style( 'jquery-ui-style' );
			}

			$woocommerce_admin_params = array(
				/* translators: Decimal point */
				'i18n_decimal_error'               => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'woocommerce-smart-coupons' ), $decimal ),
				/* translators: Decimal point */
				'i18n_mon_decimal_error'           => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'woocommerce-smart-coupons' ), wc_get_price_decimal_separator() ),
				'i18n_country_iso_error'           => __( 'Please enter in country code with two capital letters.', 'woocommerce-smart-coupons' ),
				'i18_sale_less_than_regular_error' => __( 'Please enter in a value less than the regular price.', 'woocommerce-smart-coupons' ),
				'decimal_point'                    => $decimal,
				'mon_decimal_point'                => wc_get_price_decimal_separator(),
				'strings'                          => array(
					'import_products' => __( 'Import', 'woocommerce-smart-coupons' ),
					'export_products' => __( 'Export', 'woocommerce-smart-coupons' ),
				),
				'urls'                             => array(
					'import_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ),
					'export_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
				),
			);

			$woocommerce_admin_meta_boxes_params = array(
				'remove_item_notice'            => __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'woocommerce-smart-coupons' ),
				'i18n_select_items'             => __( 'Please select some items.', 'woocommerce-smart-coupons' ),
				'i18n_do_refund'                => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'woocommerce-smart-coupons' ),
				'i18n_delete_refund'            => __( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'woocommerce-smart-coupons' ),
				'i18n_delete_tax'               => __( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'woocommerce-smart-coupons' ),
				'remove_item_meta'              => __( 'Remove this item meta?', 'woocommerce-smart-coupons' ),
				'remove_attribute'              => __( 'Remove this attribute?', 'woocommerce-smart-coupons' ),
				'name_label'                    => __( 'Name', 'woocommerce-smart-coupons' ),
				'remove_label'                  => __( 'Remove', 'woocommerce-smart-coupons' ),
				'click_to_toggle'               => __( 'Click to toggle', 'woocommerce-smart-coupons' ),
				'values_label'                  => __( 'Value(s)', 'woocommerce-smart-coupons' ),
				'text_attribute_tip'            => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce-smart-coupons' ),
				'visible_label'                 => __( 'Visible on the product page', 'woocommerce-smart-coupons' ),
				'used_for_variations_label'     => __( 'Used for variations', 'woocommerce-smart-coupons' ),
				'new_attribute_prompt'          => __( 'Enter a name for the new attribute term:', 'woocommerce-smart-coupons' ),
				'calc_totals'                   => __( 'Calculate totals based on order items, discounts, and shipping?', 'woocommerce-smart-coupons' ),
				'calc_line_taxes'               => __( 'Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.', 'woocommerce-smart-coupons' ),
				'copy_billing'                  => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce-smart-coupons' ),
				'load_billing'                  => __( 'Load the customer\'s billing information? This will remove any currently entered billing information.', 'woocommerce-smart-coupons' ),
				'load_shipping'                 => __( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', 'woocommerce-smart-coupons' ),
				'featured_label'                => __( 'Featured', 'woocommerce-smart-coupons' ),
				'prices_include_tax'            => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
				'round_at_subtotal'             => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
				'no_customer_selected'          => __( 'No customer selected', 'woocommerce-smart-coupons' ),
				'plugin_url'                    => WC()->plugin_url(),
				'ajax_url'                      => admin_url( 'admin-ajax.php' ),
				'order_item_nonce'              => wp_create_nonce( 'order-item' ),
				'add_attribute_nonce'           => wp_create_nonce( 'add-attribute' ),
				'save_attributes_nonce'         => wp_create_nonce( 'save-attributes' ),
				'calc_totals_nonce'             => wp_create_nonce( 'calc-totals' ),
				'get_customer_details_nonce'    => wp_create_nonce( 'get-customer-details' ),
				'search_products_nonce'         => wp_create_nonce( 'search-products' ),
				'grant_access_nonce'            => wp_create_nonce( 'grant-access' ),
				'revoke_access_nonce'           => wp_create_nonce( 'revoke-access' ),
				'add_order_note_nonce'          => wp_create_nonce( 'add-order-note' ),
				'delete_order_note_nonce'       => wp_create_nonce( 'delete-order-note' ),
				'calendar_image'                => WC()->plugin_url() . '/assets/images/calendar.png',
				'post_id'                       => '',
				'base_country'                  => WC()->countries->get_base_country(),
				'currency_format_num_decimals'  => wc_get_price_decimals(),
				'currency_format_symbol'        => get_woocommerce_currency_symbol(),
				'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
				'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
				'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS.
				'rounding_precision'            => WC_ROUNDING_PRECISION,
				'tax_rounding_mode'             => WC_TAX_ROUNDING_MODE,
				'product_types'                 => array_map(
					'sanitize_title',
					get_terms(
						'product_type',
						array(
							'hide_empty' => false,
							'fields'     => 'names',
						)
					)
				),
				'i18n_download_permission_fail' => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'woocommerce-smart-coupons' ),
				'i18n_permission_revoke'        => __( 'Are you sure you want to revoke access to this download?', 'woocommerce-smart-coupons' ),
				'i18n_tax_rate_already_exists'  => __( 'You cannot add the same tax rate twice!', 'woocommerce-smart-coupons' ),
				'i18n_product_type_alert'       => __( 'Your product has variations! Before changing the product type, it is a good idea to delete the variations to avoid errors in the stock reports.', 'woocommerce-smart-coupons' ),
			);

			if ( ! wp_script_is( 'wc-admin-coupon-meta-boxes' ) ) {
				wp_enqueue_script( 'wc-admin-coupon-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-coupon' . $suffix . '.js', array( 'woocommerce_admin', 'wc-enhanced-select', 'wc-admin-meta-boxes' ), WC()->version, false );
			}
			wp_localize_script(
				'wc-admin-coupon-meta-boxes',
				'woocommerce_admin_meta_boxes_coupon',
				array(
					'generate_button_text' => esc_html__( 'Generate coupon code', 'woocommerce-smart-coupons' ),
					'characters'           => apply_filters( 'woocommerce_coupon_code_generator_characters', 'ABCDEFGHJKMNPQRSTUVWXYZ23456789' ),
					'char_length'          => apply_filters( 'woocommerce_coupon_code_generator_character_length', 8 ),
					'prefix'               => apply_filters( 'woocommerce_coupon_code_generator_prefix', '' ),
					'suffix'               => apply_filters( 'woocommerce_coupon_code_generator_suffix', '' ),
				)
			);
			wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $woocommerce_admin_meta_boxes_params );

			if ( ! wp_script_is( 'woocommerce_admin' ) ) {
				wp_enqueue_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC()->version, false );
			}
			wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $woocommerce_admin_params );

		}

		/**
		 * Function to show import message
		 */
		public function woocommerce_show_import_message() {
			global $pagenow, $typenow;

			$get_show_import_message = ( ! empty( $_GET['show_import_message'] ) ) ? wc_clean( wp_unslash( $_GET['show_import_message'] ) ) : ''; // phpcs:ignore
			$get_imported            = ( ! empty( $_GET['imported'] ) ) ? wc_clean( wp_unslash( $_GET['imported'] ) ) : 0; // phpcs:ignore
			$get_skipped             = ( ! empty( $_GET['skipped'] ) ) ? wc_clean( wp_unslash( $_GET['skipped'] ) ) : 0; // phpcs:ignore

			if ( empty( $get_show_import_message ) ) {
				return;
			}

			if ( 'true' === $get_show_import_message ) {
				if ( 'edit.php' === $pagenow && 'shop_coupon' === $typenow ) {

					$imported = $get_imported;
					$skipped  = $get_skipped;

					echo '<div id="message" class="updated fade"><p>' . esc_html__( 'Import complete - imported', 'woocommerce-smart-coupons' ) . ' <strong>' . esc_html( $imported ) . '</strong>, ' . esc_html__( 'skipped', 'woocommerce-smart-coupons' ) . ' <strong>' . esc_html( $skipped ) . '</strong></p></div>';
				}
			}
		}

		/**
		 * Function to include script in admin footer
		 */
		public function smart_coupons_script_in_footer() {

			global $pagenow;
			if ( empty( $pagenow ) ) {
				return;
			}

			$get_page  = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
			$post_type = ( ! empty( $_GET['post_type'] ) ) ? wc_clean( wp_unslash( $_GET['post_type'] ) ) : ''; // phpcs:ignore

			if ( in_array( $get_page, array( 'wc-smart-coupons', 'sc-about', 'sc-faqs' ), true ) || 'shop_coupon' === $post_type ) {
				?>
				<script type="text/javascript">
					jQuery(function(){
						let is_marketing = decodeURIComponent( '<?php echo rawurlencode( ( $this->is_wc_gte_44() ) ? 'yes' : 'no' ); ?>' );
						// Highlight Coupons menu when visiting Bulk Generate/Import Coupons/Send Store Credit/Coupon category tab.
						let sa_wc_menu_selector           = 'toplevel_page_woocommerce';
						let sa_wc_marketing_menu_selector = 'toplevel_page_woocommerce-marketing';
						let element = jQuery('li#' + sa_wc_menu_selector);
						if ( 'yes' === is_marketing ) {
							element = jQuery('li#' + sa_wc_marketing_menu_selector);
						}
						element.find('ul li a[href="edit.php?post_type=shop_coupon"]').addClass('current');
						element.find('ul li a[href="edit.php?post_type=shop_coupon"]').parent().addClass('current');
						// Show notification about coupon CSV export
						jQuery(window).on('load', function(){
							let target_element = jQuery('#wc_sc_coupon_background_progress');
							let is_move = ( target_element ) ? target_element.parent().hasClass('woocommerce-layout__notice-list-hide') : false;
							if ( true === is_move ) {
								jQuery('#smart_coupons_tabs').before( target_element );
							}
						});
					});
				</script>
				<?php
			}

		}

		/**
		 * Funtion to register the coupon importer
		 */
		public function woocommerce_coupon_admin_init() {

			$get_import = ( isset( $_GET['import'] ) ) ? wc_clean( wp_unslash( $_GET['import'] ) ) : ''; // phpcs:ignore
			$get_page   = ( isset( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
			$get_action = ( isset( $_GET['action'] ) ) ? wc_clean( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore

			$post_smart_coupon_email   = ( isset( $_POST['customer_email'] ) ) ? wc_clean( wp_unslash( $_POST['customer_email'] ) ) : ''; // phpcs:ignore
			$post_smart_coupon_amount  = ( isset( $_POST['coupon_amount'] ) ) ? wc_clean( wp_unslash( $_POST['coupon_amount'] ) ) : 0; // phpcs:ignore
			$post_smart_coupon_message = ( isset( $_POST['smart_coupon_message'] ) ) ? wp_kses_post( wp_unslash( $_POST['smart_coupon_message'] ) ) : ''; // phpcs:ignore

			if ( 'wc-sc-coupons' === $get_import || 'wc-smart-coupons' === $get_page ) {
				ob_start();
			}

			if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
				register_importer( 'wc-sc-coupons', __( 'WooCommerce Coupons (CSV)', 'woocommerce-smart-coupons' ), __( 'Import <strong>coupons</strong> to your store via a csv file.', 'woocommerce-smart-coupons' ), array( $this, 'coupon_importer' ) );
			}

			if ( 'sent_gift_certificate' === $get_action && 'wc-smart-coupons' === $get_page ) {
				$email   = $post_smart_coupon_email;
				$amount  = $post_smart_coupon_amount;
				$message = $post_smart_coupon_message;
				$this->send_gift_certificate( $email, $amount, $message );
			}
		}

		/**
		 * Function to process & send gift certificate
		 *
		 * @param string $email Comma separated email address.
		 * @param float  $amount Coupon amount.
		 * @param string $message Optional.
		 */
		public function send_gift_certificate( $email, $amount, $message = '' ) {

			$emails           = explode( ',', $email );
			$location         = add_query_arg(
				array(
					'page' => 'wc-smart-coupons',
					'tab'  => 'send-smart-coupons',
				),
				admin_url( 'admin.php' )
			);
			$validation_error = '';

			// Check for valid amount.
			if ( ! $amount || ! is_numeric( $amount ) ) {
				$validation_error = 'amount_error';
			}

			if ( empty( $validation_error ) ) {
				foreach ( $emails as $email ) {
					$email = trim( $email );
					// Check for valid email address.
					if ( ( ! $email || ! is_email( $email ) ) ) {
						$validation_error = 'email_error';
						break;
					}
				}
			}

			// Proceed to bulk generate if there isn't any validation error.
			if ( empty( $validation_error ) ) {

				// Set required $_POST data for bulk generate.
				$_POST['no_of_coupons_to_generate']     = count( $emails );
				$_POST['discount_type']                 = 'smart_coupon';
				$_POST['smart_coupons_generate_action'] = 'send_store_credit';

				// includes.
				require 'class-wc-sc-coupon-import.php';
				require 'class-wc-sc-coupon-parser.php';

				$coupon_importer  = WC_SC_Coupon_Import::get_instance();
				$action_processed = $coupon_importer->process_bulk_generate_action();

				if ( false === $action_processed ) {
					$location = add_query_arg(
						array(
							'process_error' => 'yes',
						),
						$location
					);
				}
			} elseif ( 'amount_error' === $validation_error ) {
				$location = add_query_arg(
					array(
						'amount_error' => 'yes',
					),
					$location
				);
			} elseif ( 'email_error' === $validation_error ) {
				$location = add_query_arg(
					array(
						'email_error' => 'yes',
					),
					$location
				);
			}

			if ( ! empty( $location ) ) {
				wp_safe_redirect( $location );
				exit;
			}
		}

		/**
		 * Funtion to perform importing of coupon from csv file
		 */
		public function coupon_importer() {

			if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
				wp_safe_redirect(
					add_query_arg(
						array(
							'page' => 'wc-smart-coupons',
							'tab'  => 'import-smart-coupons',
						),
						admin_url( 'admin.php' )
					)
				);
				exit;
			}

			// Load Importer API.
			require_once ABSPATH . 'wp-admin/includes/import.php';

			if ( ! class_exists( 'WP_Importer' ) ) {

				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if ( file_exists( $class_wp_importer ) ) {
					require $class_wp_importer;
				}
			}

			// includes.
			require 'class-wc-sc-coupon-import.php';
			require 'class-wc-sc-coupon-parser.php';

			$wc_csv_coupon_import = new WC_SC_Coupon_Import();

			$wc_csv_coupon_import->dispatch();

		}

		/**
		 * Function to add submenu page for Coupon CSV Import
		 */
		public function woocommerce_coupon_admin_menu() {
			if ( $this->is_wc_gte_44() ) {
				add_submenu_page( 'woocommerce-marketing', __( 'Smart Coupon', 'woocommerce-smart-coupons' ), __( 'Smart Coupon', 'woocommerce-smart-coupons' ), 'manage_woocommerce', 'wc-smart-coupons', array( $this, 'admin_page' ) );
			} else {
				add_submenu_page( 'woocommerce', __( 'Smart Coupon', 'woocommerce-smart-coupons' ), __( 'Smart Coupon', 'woocommerce-smart-coupons' ), 'manage_woocommerce', 'wc-smart-coupons', array( $this, 'admin_page' ) );
			}
		}

		/**
		 * Function to remove submenu link for Smart Coupons
		 */
		public function woocommerce_coupon_admin_head() {
			if ( $this->is_wc_gte_44() ) {
				remove_submenu_page( 'woocommerce-marketing', 'wc-smart-coupons' );
			} else {
				remove_submenu_page( 'woocommerce', 'wc-smart-coupons' );
			}
		}

		/**
		 * Funtion to show content on the Coupon CSV Importer page
		 */
		public function admin_page() {
			global $store_credit_label;

			$tab = ( ! empty( $_GET['tab'] ) ? ( $_GET['tab'] == 'send-smart-coupons' ? 'send-smart-coupons' : 'import-smart-coupons' ) : 'generate_bulk_coupons' ); // phpcs:ignore

			?>

			<div class="wrap woocommerce">
				<h2>
					<?php echo esc_html__( 'Coupons', 'woocommerce-smart-coupons' ); ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'post_type' => 'shop_coupon' ), admin_url( 'post-new.php' ) ) ); ?>" class="add-new-h2"><?php echo esc_html__( 'Add Coupon', 'woocommerce-smart-coupons' ); ?></a>
				</h2>
				<div id="smart_coupons_tabs">
					<h2 class="nav-tab-wrapper">
						<a href="<?php echo esc_url( add_query_arg( array( 'post_type' => 'shop_coupon' ), admin_url( 'edit.php' ) ) ); ?>" class="nav-tab"><?php echo esc_html__( 'Coupons', 'woocommerce-smart-coupons' ); ?></a>
						<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'wc-smart-coupons' ), admin_url( 'admin.php' ) ) ); ?>" class="nav-tab <?php echo ( 'generate_bulk_coupons' === $tab ) ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Bulk Generate', 'woocommerce-smart-coupons' ); ?></a>
						<?php
							$import_tab_url = add_query_arg(
								array(
									'page' => 'wc-smart-coupons',
									'tab'  => 'import-smart-coupons',
								),
								admin_url( 'admin.php' )
							);
						?>
						<a href="<?php echo esc_url( $import_tab_url ); ?>" class="nav-tab <?php echo ( 'import-smart-coupons' === $tab ) ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Import Coupons', 'woocommerce-smart-coupons' ); ?></a>
						<?php
							$send_credit_tab_url = add_query_arg(
								array(
									'page' => 'wc-smart-coupons',
									'tab'  => 'send-smart-coupons',
								),
								admin_url( 'admin.php' )
							);
						?>
						<a href="<?php echo esc_url( $send_credit_tab_url ); ?>" class="nav-tab <?php echo ( 'send-smart-coupons' === $tab ) ? 'nav-tab-active' : ''; ?>">
							<?php
								/* translators: %s: sigular name for store credit */
								echo ( ! empty( $store_credit_label['singular'] ) ? sprintf( esc_html__( 'Send %s', 'woocommerce-smart-coupons' ), esc_html( ucwords( $store_credit_label['singular'] ) ) ) : esc_html__( 'Send Store Credit', 'woocommerce-smart-coupons' ) );
							?>
						</a>
						<div class="sc-quick-links">
							<a target="_blank" href="
							<?php
							echo esc_url(
								add_query_arg(
									array(
										'page' => 'wc-settings',
										'tab'  => 'wc-smart-coupons',
									),
									admin_url( 'admin.php' )
								)
							);
							?>
								">
								<?php echo esc_html__( 'Smart Coupons Settings', 'woocommerce-smart-coupons' ); ?>
							</a> |
							<a target="_blank" href="
							<?php
							echo esc_url(
								add_query_arg(
									array(
										'page' => 'sc-faqs',
									),
									admin_url( 'admin.php' )
								)
							);
							?>
								">
								<?php echo esc_html__( 'FAQ\'s', 'woocommerce-smart-coupons' ); ?>
							</a>
						</div>
					</h2>
				</div>
				<?php
				if ( ! function_exists( 'mb_detect_encoding' ) && 'send-smart-coupons' !== $tab ) {
					echo '<div class="message error"><p><strong>' . esc_html__( 'Required', 'woocommerce-smart-coupons' ) . ':</strong> ' . esc_html__( 'Please install and enable PHP extension', 'woocommerce-smart-coupons' ) . ' <code>mbstring</code> <a href="http://www.php.net/manual/en/mbstring.installation.php" target="_blank">' . esc_html__( 'Click here', 'woocommerce-smart-coupons' ) . '</a> ' . esc_html__( 'for more details.', 'woocommerce-smart-coupons' ) . '</p></div>';
				}

				switch ( $tab ) {
					case 'send-smart-coupons':
						$this->admin_send_certificate();
						break;
					case 'import-smart-coupons':
						$this->admin_import_page();
						break;
					default:
						$this->admin_generate_bulk_coupons_and_export();
						break;
				}
				?>

			</div>
			<?php

		}

		/**
		 * Coupon Import page content
		 */
		public function admin_import_page() {

			// Load Importer API.
			require_once ABSPATH . 'wp-admin/includes/import.php';

			if ( ! class_exists( 'WP_Importer' ) ) {

				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if ( file_exists( $class_wp_importer ) ) {
					require $class_wp_importer;
				}
			}

			// includes.
			require 'class-wc-sc-coupon-import.php';
			require 'class-wc-sc-coupon-parser.php';

			$coupon_importer = WC_SC_Coupon_Import::get_instance();
			$coupon_importer->dispatch();

		}

		/**
		 * Send Gift Certificate page content
		 */
		public function admin_send_certificate() {
			global $store_credit_label;

			$get_sent          = ( ! empty( $_GET['sent'] ) ) ? wc_clean( wp_unslash( $_GET['sent'] ) ) : ''; // phpcs:ignore
			$get_email_error   = ( ! empty( $_GET['email_error'] ) ) ? wc_clean( wp_unslash( $_GET['email_error'] ) ) : ''; // phpcs:ignore
			$get_amount_error  = ( ! empty( $_GET['amount_error'] ) ) ? wc_clean( wp_unslash( $_GET['amount_error'] ) ) : ''; // phpcs:ignore
			$get_process_error = ( ! empty( $_GET['process_error'] ) ) ? wc_clean( wp_unslash( $_GET['process_error'] ) ) : ''; // phpcs:ignore

			if ( 'yes' === $get_sent ) {
				/* translators: %s: singular name for store credit */
				$ack_message = ! empty( $store_credit_label['singular'] ) ? sprintf( esc_html__( '%s sent successfully.', 'woocommerce-smart-coupons' ), esc_html( ucfirst( $store_credit_label['singular'] ) ) ) : esc_html__( 'Store Credit / Gift Certificate sent successfully.', 'woocommerce-smart-coupons' );
				echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $ack_message ) . '</strong></p></div>';
			} elseif ( 'yes' === $get_process_error ) {
				/* translators: %s: singular name for store credit */
				$error_message = ! empty( $store_credit_label['singular'] ) ? sprintf( esc_html__( 'There has been an error in sending %s.', 'woocommerce-smart-coupons' ), esc_html( ucfirst( $store_credit_label['singular'] ) ) ) : esc_html__( 'There has been an error in sending Store Credit / Gift Certificate.', 'woocommerce-smart-coupons' );
				$error_message = $error_message . ' ' . esc_html__( 'Please try again later.', 'woocommerce-smart-coupons' );
				echo '<div id="message" class="error fade"><p><strong>' . esc_html( $error_message ) . '</strong></p></div>';
			}

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			$message     = '';
			$editor_args = array(
				'textarea_name' => 'smart_coupon_message',
				'textarea_rows' => 10,
				'editor_class'  => 'wp-editor-message',
				'media_buttons' => true,
				'tinymce'       => true,
			);
			$editor_id   = 'edit_smart_coupon_message';

			?>
			<style type="text/css">
				.sc-required-mark {
					color: red !important;
				}
				.sc-send-smart-coupon-container {
					margin-top: 1em;
				}
				.sc-send-smart-coupon-container form {
					padding: 0 1.5em;
				}
				.sc-preview-email-container {
					margin: 1em 0 2em;
				}
				.sc-email-content {
					padding: 1.5em;
				}
				textarea#<?php echo 'edit_smart_coupon_message'; ?> {
					width: 100%;
				}
				.sc-send-smart-coupon-container form table tbody tr td #amount {
					vertical-align: initial;
				}
			</style>

			<?php
				$this->get_preview_email_js( $editor_id );
			?>
			<p class="description">
			<?php
			if ( ! empty( $store_credit_label['singular'] ) ) {
				/* translators: %s: singular name for store credit */
				echo sprintf( esc_html__( 'Quickly create and email %s to one or more people.', 'woocommerce-smart-coupons' ), esc_html( strtolower( $store_credit_label['singular'] ) ) );
			} else {
				echo esc_html__( 'Quickly create and email Store Credit or Gift Card to one or more people.', 'woocommerce-smart-coupons' );
			}

			$import_step_2_url = add_query_arg(
				array(
					'page' => 'wc-smart-coupons',
					'tab'  => 'import-smart-coupons',
					'step' => '2',
				),
				admin_url( 'admin.php' )
			);
			?>
			</p>

			<div class="tool-box postbox sc-send-smart-coupon-container woo-sc-form-wrapper">

				<form action="
				<?php
				echo esc_url(
					add_query_arg(
						array(
							'page'   => 'wc-smart-coupons',
							'action' => 'sent_gift_certificate',
						),
						admin_url( 'admin.php' )
					)
				);
				?>
								" method="post">
					<?php wp_nonce_field( 'import-woocommerce-coupon' ); ?>
					<table class="form-table">
						<tr>
							<th>
								<label for="smart_coupon_email"><?php echo esc_html__( 'Send to', 'woocommerce-smart-coupons' ); ?><span class="sc-required-mark">*</span></label>
							</th>
							<td>
								<input type="text" name="customer_email" id="email" required class="input-text" style="width: 100%;" placeholder="johnsmith@example.com" />
							</td>
							<td>
								<?php
								if ( 'yes' === $get_email_error ) {
									echo '<div id="message" class="error fade"><p><strong>' . esc_html__( 'Invalid email address.', 'woocommerce-smart-coupons' ) . '</strong></p></div>';
								}
								?>
								<span class="description"><?php echo esc_html__( 'Use comma "," to separate multiple email addresses', 'woocommerce-smart-coupons' ); ?></span>
							</td>
						</tr>

						<tr>
							<th>
								<label for="smart_coupon_amount"><?php echo esc_html__( 'Worth', 'woocommerce-smart-coupons' ); ?><span class="sc-required-mark">*</span></label>
							</th>
							<td>
								<?php
									$price_format = get_woocommerce_price_format();
									echo sprintf( $price_format, '<span class="woocommerce-Price-currencySymbol">' . esc_html( get_woocommerce_currency_symbol() ) . '</span>', '&nbsp;<input type="text" name="coupon_amount" id="amount" required placeholder="' . esc_attr__( '0.00', 'woocommerce-smart-coupons' ) . '" class="input-text" style="width: 100px;" />&nbsp;' ); // phpcs:ignore
								?>
							</td>
							<td>
								<?php
								if ( 'yes' === $get_amount_error ) {
									echo '<div id="message" class="error fade"><p><strong>' . esc_html__( 'Invalid amount.', 'woocommerce-smart-coupons' ) . '</strong></p></div>';
								}
								?>
							</td>
						</tr>

						<tr>
							<th>
								<label for="smart_coupon_expiry_date">
									<?php
										echo esc_html__( 'Expiry Date', 'woocommerce-smart-coupons' );
									?>
								</label>
							</th>
							<td>
								<input type="text" class="date-picker" style="width: 150px;" name="expiry_date" id="expiry_date" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
								<?php
									$tooltip_text = esc_html__( 'The store credit will expire at 00:00:00 of this date.', 'woocommerce-smart-coupons' );
									echo wc_help_tip( $tooltip_text ); // phpcs:ignore
								?>
							</td>
						</tr>

						<tr>
							<th>
								<label for="smart_coupon_message"><?php echo esc_html__( 'Message', 'woocommerce-smart-coupons' ); ?> <small><?php echo esc_html__( '(optional)', 'woocommerce-smart-coupons' ); ?></small></label>
							</th>
							<td colspan="2">
								<?php wp_editor( $message, $editor_id, $editor_args ); ?>
							</td>
						</tr>

					</table>

					<p class="submit">
						<input type="submit" name="generate_and_import" id="generate_and_import" class="button button-primary" value="<?php echo esc_attr__( 'Send', 'woocommerce-smart-coupons' ); ?>">
						<?php

						$coupon_code = $this->get_sample_coupon_code();
						if ( ! empty( $coupon_code ) ) {
							?>
								<input type="button" id="sc-preview-email" class="button button-secondary" value="<?php echo esc_attr__( 'Preview Email', 'woocommerce-smart-coupons' ); ?>">
								<?php
						}
						?>
					</p>
				</form>
			</div>
			<?php
			if ( ! empty( $coupon_code ) ) {
				$this->get_preview_email_html( $coupon_code );
			}
		}

		/**
		 * Function to get sample coupon code
		 */
		public function get_sample_coupon_code() {
			global $wpdb;
			$coupon_code = wp_cache_get( 'wc_sc_any_coupon_code', 'woocommerce_smart_coupons' );
			if ( false === $coupon_code ) {
				$coupon_code = $wpdb->get_var( // phpcs:ignore
					$wpdb->prepare(
						"SELECT post_title
								FROM $wpdb->posts AS p
									LEFT JOIN $wpdb->postmeta AS pm
										ON (p.ID = pm.post_id)
								WHERE post_status = %s
									AND post_type = %s
									AND ( pm.meta_key = %s AND pm.meta_value = %s )
								LIMIT 1",
						'publish',
						'shop_coupon',
						'discount_type',
						'smart_coupon'
					)
				);
				wp_cache_set( 'wc_sc_any_coupon_code', $coupon_code, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_any_coupon_code' );
			}
			return $coupon_code;
		}

		/**
		 * Function to get preview html for Coupon Emails
		 *
		 * @param  string $coupon_code Coupon code.
		 */
		public function get_preview_email_html( $coupon_code = '' ) {
			?>
			<div class="sc-preview-email-container postbox" style="display: none;">
				<div class="sc-email-content">
					<?php
					if ( ! empty( $coupon_code ) ) {
						WC()->mailer();
						if ( class_exists( 'WC_SC_Email_Coupon' ) ) {
							$email_coupon             = new WC_SC_Email_Coupon();
							$email_args               = array(
								'coupon' => array(
									'code'   => $coupon_code,
									'amount' => wc_price( 0 ),
								),
							);
							$email_coupon->email_args = wp_parse_args( $email_args, $email_coupon->email_args );
							$email_coupon->set_placeholders();
							$email_content = $email_coupon->get_content();
							// Replace placeholders with values in the email content.
							$email_content = ( is_callable( array( $email_coupon, 'format_string' ) ) ) ? $email_coupon->format_string( $email_content ) : $email_content;

							ob_start();
							wc_get_template( 'emails/email-styles.php' );
							$css = ob_get_clean();
							$css = apply_filters( 'woocommerce_email_styles', $css, $email_coupon );
							ob_start();
							echo '<div id="wc-sc-preview-email-template-css" data-css="' . esc_attr( $css ) . '"></div>'; // phpcs:ignore
							echo $email_content; // phpcs:ignore
							echo ob_get_clean(); // phpcs:ignore
						}
					}
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Form to show 'Auto generate Bulk Coupons' with other fields
		 */
		public function admin_generate_bulk_coupons_and_export() {

			global $woocommerce_smart_coupon, $post;

			$empty_reference_coupon = get_option( 'empty_reference_smart_coupons' );

			if ( false === $empty_reference_coupon ) {
				$args              = array(
					'post_status' => 'auto-draft',
					'post_type'   => 'shop_coupon',
				);
				$reference_post_id = wp_insert_post( $args );
				update_option( 'empty_reference_smart_coupons', $reference_post_id, 'no' );
			} else {
				$reference_post_id = $empty_reference_coupon;
			}

			$post = get_post( $reference_post_id ); // phpcs:ignore

			if ( empty( $post ) ) {
				$args              = array(
					'post_status' => 'auto-draft',
					'post_type'   => 'shop_coupon',
				);
				$reference_post_id = wp_insert_post( $args );
				update_option( 'empty_reference_smart_coupons', $reference_post_id, 'no' );
				$post = get_post( $reference_post_id ); // phpcs:ignore
			}

			if ( ! class_exists( 'WC_Meta_Box_Coupon_Data' ) ) {
				require_once WC()->plugin_path() . '/includes/admin/meta-boxes/class-wc-meta-box-coupon-data.php';
			}
			if ( ! class_exists( 'WC_Admin_Post_Types' ) ) {
				require_once WC()->plugin_path() . '/includes/admin/class-wc-admin-post-types.php';
			}
			$admin_post_types = new WC_Admin_Post_Types();

			$is_post_generate_and_import        = ( isset( $_POST['generate_and_import'] ) ) ? true : false; // phpcs:ignore
			$post_smart_coupons_generate_action = ( ! empty( $_POST['smart_coupons_generate_action'] ) ) ? wc_clean( wp_unslash( $_POST['smart_coupons_generate_action'] ) ) : ''; // phpcs:ignore

			$message     = '';
			$editor_args = array(
				'textarea_name' => 'smart_coupon_message',
				'textarea_rows' => 7,
				'editor_class'  => 'wp-editor-message',
				'media_buttons' => true,
				'tinymce'       => true,
			);
			$editor_id   = 'edit_smart_coupon_message';
			?>

			<script type="text/javascript">
				jQuery(function(){
					jQuery('input#generate_and_import').on('click', function(){
						if( jQuery( this ).hasClass('disabled') ) {
							jQuery('html, body').animate({
								scrollTop: jQuery('#wc_sc_folder_permission_warning').offset().top - 100 // Scroll to admin notice.
							}, 'slow');
							return false;
						} else if( jQuery('input#no_of_coupons_to_generate').val() == "" ){
							jQuery("div#message").removeClass("updated fade").addClass("error fade");
							jQuery('div#message p').html( "<?php echo esc_html__( 'Please enter a valid value for Number of Coupons to Generate', 'woocommerce-smart-coupons' ); ?>");
							scrollTop();
							return false;
						} else {
							jQuery("div#message").removeClass("error fade").addClass("updated fade").hide();
							return true;
						}
					});

					var showHideBulkSmartCouponsOptions = function() {
						jQuery('input#sc_coupon_validity').parent('p').show();
						jQuery('div#for_prefix_suffix').show();
					};

					setTimeout(function(){
						showHideBulkSmartCouponsOptions();
					}, 101);

					jQuery('select#discount_type').on('change', function(){
						setTimeout(function(){
							showHideBulkSmartCouponsOptions();
						}, 101);
					});

					jQuery('body').on('click', '#woo_sc_is_email_imported_coupons', function() {
						jQuery('span#sc_note_about_emailing_recipients,#wc_sc_bulk_email_metabox').show();
					});
					jQuery('body').on('click', '#add_to_store, #sc_export_and_import', function() {
						jQuery('span#sc_note_about_emailing_recipients,#wc_sc_bulk_email_metabox').hide();
					});
				});


				jQuery(document).ready(function() {
					var syncChecks,
						noSyncChecks = false;

					/**
					 * Handle display of category tabs 'All coupon categories' and 'Most used', showing & hiding
					 *
					 * sc_coupon_category postbox tab show-hide
					 */
					jQuery('#sc_coupon_category-tabs a').on( 'click', function(){
						let tab = jQuery(this).attr('href');
						jQuery(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
						jQuery('.tabs-panel').hide();
						jQuery(tab).show();
						return false;
					});

					/**
					 * Synchronize category checkboxes.
					 *
					 * This function makes sure that the checkboxes are synced between the 'All coupon categories' tab
					 * and the 'Most used' tab.
					 */
					syncChecks = function() {
						if ( noSyncChecks ) {
							return;
						}
						noSyncChecks = true;
						var current_element = jQuery(this),
							is_checked      = current_element.is(':checked'),
							term_id         = current_element.val().toString();
						jQuery( '#in-sc_coupon_category-' + term_id + ', #in-popular-sc_coupon_category-' + term_id ).prop( 'checked', is_checked );
						noSyncChecks = false;
					};

					/**
					 * Synchronize category checkboxes for sc_coupon_category postbox
					 */
					jQuery('.categorychecklist :checkbox').on( 'change', syncChecks ).filter( ':checked' ).trigger( 'change' );
				});



			</script>
			<div class="woo-sc-form-wrapper">
				<div id="message"><p></p></div>
				<div class="tool-box">

					<p class="description"><?php echo esc_html__( 'Need a lot of coupons? You can easily do that with Smart Coupons.', 'woocommerce-smart-coupons' ); ?></p>

					<style type="text/css">
						.coupon_actions {
							margin-left: 14px;
						}
						#smart-coupon-action-panel p label {
							width: 30%;
						}
						#smart-coupon-action-panel {
							width: 100% !important;
						}
						.sc-required-mark {
							color: red;
						}
						#wc_sc_bulk_email_metabox {
							padding: 0 1.5em;
						}
						#wc_sc_bulk_email_metabox table.form-table td:first-child {
							width: 25%;
						}
						#wc_sc_bulk_email_metabox textarea#edit_smart_coupon_message {
							width: 100%;
						}
						.sc-bulk-generate-coupon-data-main{
							display: grid;
							grid-template-columns: 75% 24%;
							grid-gap: 1%;
						}
					</style>
					<?php
						$import_step_2_url = add_query_arg(
							array(
								'page' => 'wc-smart-coupons',
								'tab'  => 'import-smart-coupons',
								'step' => '2',
							),
							admin_url( 'admin.php' )
						);
					?>
					<form id="generate_coupons" action="<?php echo esc_url( $import_step_2_url ); ?>" method="post">
						<?php wp_nonce_field( 'import-woocommerce-coupon' ); ?>
						<div id="poststuff">
							<div id="woocommerce-coupon-data" class="postbox " >
								<h3><span class="coupon_actions"><?php echo esc_html__( 'Action', 'woocommerce-smart-coupons' ); ?></span></h3>
								<div class="inside">
									<div class="panel-wrap">
										<div id="smart-coupon-action-panel" class="panel woocommerce_options_panel">

											<p class="form-field">
												<label for="no_of_coupons_to_generate"><?php echo esc_html__( 'Number of coupons to generate', 'woocommerce-smart-coupons' ); ?>&nbsp;<span title="<?php echo esc_attr__( 'Required', 'woocommerce-smart-coupons' ); ?>" class="sc-required-mark">*</span></label>
												<input type="number" name="no_of_coupons_to_generate" id="no_of_coupons_to_generate" placeholder="<?php echo esc_attr__( '10', 'woocommerce-smart-coupons' ); ?>" class="short" min="1" required />
											</p>

											<p class="form-field">
												<label><?php echo esc_html__( 'Generate coupons and', 'woocommerce-smart-coupons' ); ?></label>
												<input type="radio" name="smart_coupons_generate_action" value="add_to_store" id="add_to_store" checked="checked"/>&nbsp;
												<strong><?php echo esc_html__( 'Add to store', 'woocommerce-smart-coupons' ); ?></strong>
											</p>

											<p class="form-field">
												<label for="sc_export_and_import"><?php echo '&nbsp;'; ?></label>
												<input type="radio" name="smart_coupons_generate_action" value="sc_export_and_import" id="sc_export_and_import" />&nbsp;
												<strong><?php echo esc_html__( 'Export to CSV', 'woocommerce-smart-coupons' ); ?></strong>
												<?php
													$import_tab_url = add_query_arg(
														array(
															'page' => 'wc-smart-coupons',
															'tab'  => 'import-smart-coupons',
														),
														admin_url( 'admin.php' )
													);
												?>
												<span class="description">
												<?php
												echo esc_html__( '(Does not add to store, but creates a .csv file, that you can', 'woocommerce-smart-coupons' ) . ' <a href="' . esc_url( $import_tab_url ) . '">' . esc_html__( 'import', 'woocommerce-smart-coupons' ) . '</a> ' . esc_html__( 'later', 'woocommerce-smart-coupons' ) . ')';
												?>
												</span>
											</p>

											<p class="form-field">
												<label><?php echo '&nbsp;'; ?></label>
												<input type="radio" name="smart_coupons_generate_action" value="woo_sc_is_email_imported_coupons" id="woo_sc_is_email_imported_coupons" />&nbsp;
												<strong><?php echo esc_html__( 'Email to recipients', 'woocommerce-smart-coupons' ); ?></strong>
												<span class="description">
													<?php echo esc_html__( '(Add to store and email generated coupons to recipients)', 'woocommerce-smart-coupons' ); ?>
												</span><br>
												<span class="description wc-sc-description-container" id="sc_note_about_emailing_recipients" style="display: none;">
													<span class="wc-sc-description">
													<?php
													/* translators: 1: Path to setting 2: Setting to set email address 3: Setting for number of coupons to generate */
													echo sprintf( esc_html__( 'Enter the email addresses of the recipients separated by comma under %1$1s. Make sure to match the count of email addresses in %2$2s to %3$3s', 'woocommerce-smart-coupons' ), '<strong>' . esc_html__( 'Send to', 'woocommerce-smart-coupons' ) . '</strong>', '<strong>' . esc_html__( 'Send to', 'woocommerce-smart-coupons' ) . '</strong>', '<strong>' . esc_html__( 'Number of coupons to generate', 'woocommerce-smart-coupons' ) . '</strong>' );
													?>
													</span>
												</span>
											</p>
										</div>
									</div>
								</div>
							</div>
							<div id="wc_sc_bulk_email_metabox" class="postbox" style="display:none;">
								<h3>
									<?php
										echo esc_html__( 'Email to ', 'woocommerce-smart-coupons' );
									?>
								</h3>
								<table class="form-table">
									<tr>
										<th>
											<label for="smart_coupon_email"><?php echo esc_html__( 'Send to', 'woocommerce-smart-coupons' ); ?><span class="sc-required-mark">*</span></label>
										</th>
										<td>
											<input type="text" name="smart_coupon_email" id="email" class="input-text" cols="50" rows="5" placeholder="johnsmith@example.com">
										</td>
										<td>
											<span class="description"><?php echo esc_html__( 'Use comma "," to separate multiple email addresses', 'woocommerce-smart-coupons' ); ?></span>
										</td>
									</tr>
									<tr>
										<th>
											<label for="smart_coupon_message"><?php echo esc_html__( 'Message', 'woocommerce-smart-coupons' ); ?> <small><?php echo esc_html__( '(optional)', 'woocommerce-smart-coupons' ); ?></small></label>
										</th>
										<td colspan="2">
											<?php
												wp_editor( $message, $editor_id, $editor_args );
											?>
										</td>
									</tr>
									<tr>
										<th></th>
										<td>
											<?php
												$sample_coupon_code = $this->get_sample_coupon_code();
											if ( ! empty( $sample_coupon_code ) ) {
												?>
														<input type="button" id="sc-preview-email" class="button button-secondary" value="<?php echo esc_attr__( 'Preview Email', 'woocommerce-smart-coupons' ); ?>">
													<?php
											}
											?>
										</td>
									</tr>
								</table>
								<div class="form-field wc-sc-email-preview-html">
									<?php
									if ( ! empty( $sample_coupon_code ) ) {
										$this->get_preview_email_html( $sample_coupon_code );
									}
									?>
								</div>
							</div>
							<?php
								$this->get_preview_email_js( $editor_id );
							?>
							<div id="woocommerce-coupon-data" class="postbox" >
								<h3>
									<span class="coupon_actions">
										<?php
											echo esc_html__( 'Coupon Description ', 'woocommerce-smart-coupons' );
											/* translators: 1: HTML small tag start 2: HTML small tag end */
											echo sprintf( esc_html__( '%1$s(This will add the same coupon description in all the bulk generated coupons)%2$s', 'woocommerce-smart-coupons' ), '<small>', '</small>' );
										?>
									</span>
								</h3>
								<div class="sc_bulk_description">
									<?php $admin_post_types->edit_form_after_title( $post ); ?>
								</div>
							</div>
							<div class="sc-bulk-generate-coupon-data-main">
								<div class="sc-bulk-generate-coupon-data">
									<div id="woocommerce-coupon-data" class="postbox">
										<h3>
									<span class="coupon_actions">
										<?php echo esc_html__( 'Coupon Data', 'woocommerce-smart-coupons' ); ?>
									</span>
										</h3>
										<div class="inside">
											<?php WC_Meta_Box_Coupon_Data::output( $post ); ?>
										</div>
									</div>
								</div>
								<div class="sc-bulk-generate-coupon-category">
									<div id="sc_coupon_categorydiv" class="postbox ">
									<div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php echo esc_html__( 'Coupon categories', 'woocommerce-smart-coupons' ); ?></h2>
										</div><div class="inside">
										<div id="taxonomy-sc_coupon_category" class="categorydiv">
											<div class="sc-manage-category">
												<a target="_blank" title="" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=sc_coupon_category&post_type=shop_coupon' ) ); ?>"><?php echo esc_html__( 'Manage coupon categories', 'woocommerce-smart-coupons' ); ?></a>
											</div>

											<?php
											$args     = array( 'taxonomy' => 'sc_coupon_category' );
											$tax_name = esc_attr( $args['taxonomy'] );
											$taxonomy = get_taxonomy( $args['taxonomy'] );
											?>
											<div id="taxonomy-<?php echo esc_attr( $tax_name ); ?>" class="categorydiv">
												<ul id="<?php echo esc_attr( $tax_name ); ?>-tabs" class="category-tabs">
													<li class="tabs"><a href="#<?php echo esc_attr( $tax_name ); ?>-all"><?php echo esc_html( $taxonomy->labels->all_items ); ?></a></li>
													<li class="hide-if-no-js"><a href="#<?php echo esc_attr( $tax_name ); ?>-pop"><?php echo esc_html( $taxonomy->labels->most_used ); ?></a></li>
												</ul>

												<div id="<?php echo esc_attr( $tax_name ); ?>-pop" class="tabs-panel" style="display: none;">
													<ul id="<?php echo esc_attr( $tax_name ); ?>checklist-pop" class="categorychecklist form-no-clear" >
														<?php $popular_ids = wp_popular_terms_checklist( $tax_name ); ?>
													</ul>
												</div>

												<div id="<?php echo esc_attr( $tax_name ); ?>-all" class="tabs-panel">
													<ul id="<?php echo esc_attr( $tax_name ); ?>checklist" data-wp-lists="list:<?php echo esc_attr( $tax_name ); ?>" class="categorychecklist form-no-clear">
														<?php
														wp_terms_checklist(
															$post->ID,
															array(
																'taxonomy'     => $tax_name,
																'popular_cats' => $popular_ids,
															)
														);
														?>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
								</div>
							</div>

						</div>

						<p class="submit"><input id="generate_and_import" name="generate_and_import" type="submit" class="button button-primary button-hero" value="<?php echo esc_attr__( 'Apply', 'woocommerce-smart-coupons' ); ?>" /></p>

					</form>
				</div>
			</div>
			<?php

		}

		/**
		 * Function to get common js code for Bulk Generate Tab and Send Store Credit Tab
		 *
		 * @param  string $editor_id ID for wysiwyg editor.
		 */
		public function get_preview_email_js( $editor_id = 0 ) {
			?>
			<script type="text/javascript">
				jQuery(function(){
					var editor_id = decodeURIComponent( '<?php echo rawurlencode( (string) $editor_id ); ?>' );
					var sc_check_decimal = function( amount ){
						var ex = /^\d*\.?(\d{1,2})?$/;
						if ( ex.test( amount ) == false ) {
							amt = amount.substring( 0, amount.length - 1 );
							return amt;
						}
						return amount;
					};
					function tinymce_apply_changes() {
						if ( jQuery('#wp-' + editor_id + '-wrap').hasClass('tmce-active') ){
							tinyMCE.editors[ editor_id ].save();
							content = tinyMCE.editors[ editor_id ].getContent();
							jQuery('#' + editor_id).text( content ).trigger('change');
						}
					}
					function wc_sc_bind_event_to_handle_changes_in_editor() {
						var content;
						if ( jQuery('#wp-' + editor_id + '-wrap').hasClass('tmce-active') ){
							tinyMCE.activeEditor.on('change', function(ed) {
								tinymce_apply_changes();
							});
						}
					}
					jQuery('#sc-preview-email').on('click', function(){
						jQuery('body #wc-sc-email-style').remove();
						setTimeout(wc_sc_bind_event_to_handle_changes_in_editor, 100);
						tinymce_apply_changes();
						if ( ! jQuery('.sc-preview-email-container').is(':visible') ) {
							let email_css = jQuery('#wc-sc-preview-email-template-css').data('css');
							if( '' !== email_css ) {
								let email_style = '<style type="text/css" id="wc-sc-email-style">' + email_css + '</style>';
								jQuery('body').append(email_style);
							}
							jQuery('.sc-preview-email-container').slideDown();
							jQuery('html, body').animate( { scrollTop: jQuery('#sc-preview-email').offset().top }, 'slow' );
						} else {
							jQuery('.sc-preview-email-container').slideUp();
						}
					});
					jQuery('.sc-send-smart-coupon-container #amount').on('keypress keyup change', function(){
						var el = jQuery(this);
						var amount = el.val().toString();
						var new_amount = sc_check_decimal( amount );
						if ( new_amount != amount ) {
							el.val( new_amount );
						}
					});
					let email_fields = jQuery('#email,#customer_email');
					jQuery(email_fields).keyup(function(){
						jQuery(email_fields).val(jQuery(this).val());
					});
					jQuery('.sc-send-smart-coupon-container #amount,#coupon_amount').on('keyup change', function(){
						var price_content = jQuery('.sc-email-content h1 span.woocommerce-Price-amount.amount').contents();
						if( price_content.length > 0 ) {
							price_content[price_content.length-1].nodeValue = parseFloat(jQuery(this).val()).toFixed(2);
						}
						var html = jQuery('.sc-email-content span.woocommerce-Price-amount.amount').html();
						jQuery('.sc-email-content span.woocommerce-Price-amount.amount').html(html);
						var price_html = '<span class="woocommerce-Price-amount amount">' + html + '</span>';
						jQuery('.sc-email-content .discount-info').html(price_html + ' <?php echo ! empty( $store_credit_label['singular'] ) ? esc_html( ucwords( $store_credit_label['singular'] ) ) : esc_html__( 'Store Credit', 'woocommerce-smart-coupons' ); ?>');
					});
					setTimeout(wc_sc_bind_event_to_handle_changes_in_editor, 100);
					jQuery('.sc-email-content #body_content_inner').prepend('<p class="sc-credit-message"></p>');
					jQuery('#' + editor_id).on('keyup change', function(){
						var element = jQuery(this);
						var content = '';
						if ( jQuery('#wp-' + editor_id + '-wrap').hasClass('tmce-active') ){
							content = element.text();
						} else {
							content = element.val();
						}
						jQuery('.sc-email-content .sc-credit-message').html(content);
					});
				});
			</script>
			<?php
		}

		/**
		 * Funtion to show search result based on email in coupon's usage restrictions
		 *
		 * @param  object $wp WP object.
		 */
		public function filter_coupons_using_meta( $wp ) {
			global $pagenow, $wpdb;

			if ( 'edit.php' !== $pagenow ) {
				return;
			}
			if ( ! isset( $wp->query_vars['s'] ) ) {
				return;
			}
			if ( 'shop_coupon' !== $wp->query_vars['post_type'] ) {
				return;
			}

			$e = substr( $wp->query_vars['s'], 0, 5 );

			if ( 'email:' === strtolower( substr( $wp->query_vars['s'], 0, 6 ) ) ) {

				$email = trim( substr( $wp->query_vars['s'], 6 ) );

				if ( ! $email ) {
					return;
				}

				$post_ids = wp_cache_get( 'wc_sc_get_coupon_ids_by_email_' . sanitize_key( $email ), 'woocommerce_smart_coupons' );

				if ( false === $post_ids ) {
					$post_ids = $wpdb->get_col(
						$wpdb->prepare(
							"SELECT pm.post_id
								FROM {$wpdb->postmeta} AS pm
									LEFT JOIN {$wpdb->posts} AS p
									ON (p.ID = pm.post_id AND p.post_type = 'shop_coupon')
								WHERE pm.meta_key = 'customer_email'
									AND pm.meta_value LIKE %s",
							'%' . $wpdb->esc_like( $email ) . '%'
						)
					); // WPCS: db call ok.
					wp_cache_set( 'wc_sc_get_coupon_ids_by_email_' . sanitize_key( $email ), $post_ids, 'woocommerce_smart_coupons' );
					$this->maybe_add_cache_key( 'wc_sc_get_coupon_ids_by_email_' . sanitize_key( $email ) );
				}

				if ( empty( $post_ids ) ) {
					return;
				}

				unset( $wp->query_vars['s'] );

				$wp->query_vars['post__in'] = $post_ids;

				$wp->query_vars['email'] = $email;
			}

		}

		/**
		 * Function to show label of the search result on coupon
		 *
		 * @param  mixed $query Query.
		 * @return mixed $query
		 */
		public function filter_coupons_using_meta_label( $query ) {
			global $pagenow, $typenow, $wp;

			if ( 'edit.php' !== $pagenow ) {
				return $query;
			}
			if ( 'shop_coupon' !== $typenow ) {
				return $query;
			}

			$s = get_query_var( 's' );
			if ( ! empty( $s ) ) {
				return $query;
			}

			$email = get_query_var( 'email' );

			if ( ! empty( $email ) ) {

				$post_type = get_post_type_object( $wp->query_vars['post_type'] );
				/* translators: 1: Singular name for post type 2: Email */
				return sprintf( __( '[%1$s restricted with email: %2$s]', 'woocommerce-smart-coupons' ), $post_type->labels->name, $email );
			}

			return $query;
		}

		/**
		 * WooCommerce Navigation Is Connected Page
		 *
		 * @param  boolean $is_connected_page Is connected page.
		 * @param  string  $current_page      The current page.
		 * @return boolean
		 */
		public function woocommerce_navigation_is_connected_page( $is_connected_page = false, $current_page = '' ) {
			$get_page = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
			if ( empty( $is_connected_page ) && 'wc-smart-coupons' === $get_page ) {
				return true;
			}
			return $is_connected_page;
		}

		/**
		 * WooCommerce Navigation Is Connected Page
		 *
		 * @param  boolean $breadcrumbs  The breadcrumbs.
		 * @param  string  $current_page The current page.
		 * @return boolean
		 */
		public function woocommerce_navigation_breadcrumbs( $breadcrumbs = array(), $current_page = '' ) {
			global $store_credit_label;
			$get_page = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
			if ( 'wc-smart-coupons' === $get_page ) {
				$breadcrumbs = $this->get_default_breadcrumbs();
				$get_tab = ( ! empty( $_GET['tab'] ) ) ? wc_clean( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore
				switch ( $get_tab ) {
					case 'import-smart-coupons':
						$breadcrumbs[] = __( 'Import Coupons', 'woocommerce-smart-coupons' );
						break;
					case 'send-smart-coupons':
						/* translators: Store Credit label */
						$breadcrumbs[] = sprintf( __( 'Send %s', 'woocommerce-smart-coupons' ), ( ( ! empty( $store_credit_label['singular'] ) ) ? ucwords( $store_credit_label['singular'] ) : __( 'Store Credit', 'woocommerce-smart-coupons' ) ) );
						break;
					default:
						$breadcrumbs[] = __( 'Bulk Generate', 'woocommerce-smart-coupons' );
						break;
				}
			}
			return $breadcrumbs;
		}

		/**
		 * Default breadcrums
		 *
		 * @return array
		 */
		public function get_default_breadcrumbs() {
			$breadcrumbs   = array();
			$breadcrumbs[] = array(
				'admin.php?page=wc-admin',
				__( 'WooCommerce', 'woocommerce-smart-coupons' ),
			);
			if ( $this->is_wc_gte_44() ) { // To make sure that the WooCommerce is 4.4 or greater.
				$breadcrumbs[] = array(
					'admin.php?page=wc-admin&path=/marketing',
					__( 'Marketing', 'woocommerce-smart-coupons' ),
				);
			}
			$breadcrumbs[] = array(
				'edit.php?post_type=shop_coupon',
				__( 'Coupons', 'woocommerce-smart-coupons' ),
			);
			return $breadcrumbs;
		}

	}

}

WC_SC_Admin_Pages::get_instance();
