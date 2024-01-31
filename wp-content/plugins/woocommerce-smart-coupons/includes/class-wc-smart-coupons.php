<?php
/**
 * Main class for Smart Coupons
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     6.9.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Smart_Coupons' ) ) {

	/**
	 *  Main WooCommerce Smart Coupons Class.
	 *
	 * @return object of WC_Smart_Coupons having all functionality of Smart Coupons
	 */
	class WC_Smart_Coupons {

		/**
		 * Text Domain
		 *
		 * @var $text_domain
		 */
		public static $text_domain = 'woocommerce-smart-coupons';

		/**
		 * Text Domain
		 *
		 * @var $text_domain
		 */
		public $plugin_data = array();

		/**
		 * Variable to hold instance of Smart Coupons
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Get single instance of Smart Coupons.
		 *
		 * @return WC_Smart_Coupons Singleton object of WC_Smart_Coupons
		 */
		public static function get_instance() {

			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 3.3.0
		 */
		private function __clone() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-smart-coupons' ), '3.3.0' );
		}

		/**
		 * Constructor
		 */
		private function __construct() {

			$this->includes();

			add_action( 'plugins_loaded', array( $this, 'load_action_scheduler' ), -1 );

			add_action( 'init', array( $this, 'process_activation' ) );
			add_action( 'init', array( $this, 'add_sc_options' ) );
			add_action( 'init', array( $this, 'define_label_for_store_credit' ) );

			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_smart_coupon_valid' ), 10, 3 );
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_user_usage_limit_valid' ), 10, 3 );
			add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'smart_coupons_is_valid_for_product' ), 10, 4 );
			add_filter( 'woocommerce_coupon_validate_expiry_date', array( $this, 'validate_expiry_time' ), 999, 3 );
			add_filter( 'woocommerce_apply_individual_use_coupon', array( $this, 'smart_coupons_override_individual_use' ), 10, 3 );
			add_filter( 'woocommerce_apply_with_individual_use_coupon', array( $this, 'smart_coupons_override_with_individual_use' ), 10, 4 );

			add_action( 'restrict_manage_posts', array( $this, 'woocommerce_restrict_manage_smart_coupons' ), 20 );
			add_action( 'admin_init', array( $this, 'woocommerce_export_coupons' ) );

			add_action( 'personal_options_update', array( $this, 'my_profile_update' ) );
			add_action( 'edit_user_profile_update', array( $this, 'my_profile_update' ) );

			add_action( 'wc_sc_new_coupon_generated', array( $this, 'smart_coupons_plugin_used' ) );

			// Actions used to insert a new endpoint in the WordPress.
			add_action( 'init', array( $this, 'sc_add_endpoints' ), 11 );

			add_action( 'admin_enqueue_scripts', array( $this, 'smart_coupon_styles_and_scripts' ), 20 );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );

			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'wc_smart_coupons_export_headers' ) );
			add_filter( 'woocommerce_email_footer_text', array( $this, 'email_footer_replace_site_title' ) );

			add_filter( 'is_protected_meta', array( $this, 'make_sc_meta_protected' ), 10, 3 );

			add_action( 'admin_notices', array( $this, 'minimum_woocommerce_version_requirement' ) );

			add_action( 'wp_loaded', array( $this, 'sc_handle_store_credit_application' ), 15 );

			add_filter( 'woocommerce_debug_tools', array( $this, 'clear_cache_tool' ) );

			add_action( 'woocommerce_checkout_update_order_review', array( $this, 'woocommerce_checkout_update_order_review' ) );

			add_action( 'woocommerce_cart_reset', array( $this, 'woocommerce_cart_reset' ) );

			// Actions used to schedule sending of coupons.
			add_action( 'wc_sc_send_scheduled_coupon_email', array( $this, 'send_scheduled_coupon_email' ), 10, 7 );
			add_action( 'wc_sc_import_send_scheduled_coupon_email', array( $this, 'import_send_scheduled_coupon_email' ), 10, 5 );
			add_action( 'publish_future_post', array( $this, 'process_published_scheduled_coupon' ) );
			add_action( 'before_delete_post', array( $this, 'delete_scheduled_coupon_actions' ) );
			add_action( 'admin_footer', array( $this, 'enqueue_admin_footer_scripts' ) );
			add_action( 'wp_ajax_wc_sc_check_scheduled_coupon_actions', array( $this, 'check_scheduled_coupon_actions' ) );

			// Filter to modify discount amount for percentage type coupon.
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_coupon_discount_amount' ), 10, 5 );

			// Filter to add default values to coupon meta fields.
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );

			// Filter to register Smart Coupons' email classes.
			add_filter( 'woocommerce_email_classes', array( $this, 'register_email_classes' ) );

			add_filter( 'woocommerce_hold_stock_for_checkout', array( $this, 'hold_stock_for_checkout' ) );

			add_action( 'wc_sc_generate_coupon', array( $this, 'generate_coupon' ) );
			add_action( 'wc_sc_paint_coupon', array( $this, 'paint_coupon' ) );

			add_filter( 'woocommerce_rest_api_get_rest_namespaces', array( $this, 'rest_namespace' ) );

			add_filter( 'woocommerce_shipping_free_shipping_is_available', array( $this, 'is_eligible_for_free_shipping' ), 10, 3 );

			add_action( 'woocommerce_system_status_report', array( $this, 'smart_coupons_system_status_report' ), 11 );

			add_action( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'rest_api_prepare_shop_order_object' ), 10, 3 );

			add_action( 'before_woocommerce_init', array( $this, 'hpos_compat_declaration' ) );
			add_action( 'before_woocommerce_init', array( $this, 'blocks_compat_declaration' ) );

			add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'format_sc_meta_data' ), 99, 2 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ) );

			add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, 'custom_meta_support_in_orders_query' ), 10, 2 );

			add_action( 'upgrader_process_complete', array( $this, 'upgrader_process_complete' ), 10, 2 );
		}

		/**
		 * Function to handle WC compatibility related function call from appropriate class
		 *
		 * @param string $function_name Function to call.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return mixed Result of function call.
		 */
		public function __call( $function_name, $arguments = array() ) {

			if ( ! is_callable( 'SA_WC_Compatibility_4_4', $function_name ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( 'SA_WC_Compatibility_4_4::' . $function_name, $arguments );
			} else {
				return call_user_func( 'SA_WC_Compatibility_4_4::' . $function_name );
			}

		}

		/**
		 * Include files
		 */
		public function includes() {

			include_once 'compat/class-sa-wc-compatibility-4-4.php';
			include_once 'compat/class-wc-sc-wpml-compatibility.php';
			include_once 'compat/class-wcopc-sc-compatibility.php';
			include_once 'compat/class-wcs-sc-compatibility.php';
			include_once 'compat/class-wc-sc-wmc-compatibility.php';
			include_once 'compat/class-wc-sc-aelia-cs-compatibility.php';
			include_once 'compat/class-wc-sc-kco-compatibility.php';
			include_once 'compat/class-wc-sc-pnr-compatibility.php';
			include_once 'compat/class-wc-sc-wscp-compatibility.php';

			include_once 'class-wc-sc-admin-welcome.php';
			include_once 'class-wc-sc-background-coupon-importer.php';
			include_once 'class-wc-sc-admin-pages.php';
			include_once 'class-wc-sc-admin-notifications.php';

			include_once 'class-wc-sc-ajax.php';
			include_once 'class-wc-sc-display-coupons.php';

			include_once 'class-wc-sc-settings.php';
			include_once 'class-wc-sc-shortcode.php';
			include_once 'class-wc-sc-purchase-credit.php';
			include_once 'class-wc-sc-url-coupon.php';
			include_once 'class-wc-sc-print-coupon.php';
			include_once 'class-wc-sc-coupon-fields.php';
			include_once 'class-wc-sc-auto-apply-coupon.php';
			include_once 'class-wc-sc-product-fields.php';
			include_once 'class-wc-sc-order-fields.php';
			include_once 'class-wc-sc-coupon-process.php';
			include_once 'class-wc-sc-global-coupons.php';
			include_once 'class-wc-sc-admin-coupons-dashboard-actions.php';
			include_once 'class-wc-sc-privacy.php';
			include_once 'class-wc-sc-coupon-actions.php';
			include_once 'class-wc-sc-coupon-columns.php';
			include_once 'class-wc-sc-product-columns.php';
			include_once 'class-wc-sc-coupons-by-location.php';
			include_once 'class-wc-sc-coupons-by-payment-method.php';
			include_once 'class-wc-sc-coupons-by-shipping-method.php';
			include_once 'class-wc-sc-coupons-by-user-role.php';
			include_once 'class-wc-sc-coupons-by-product-attribute.php';
			include_once 'class-wc-sc-coupons-by-taxonomy.php';
			include_once 'class-wc-sc-coupons-by-excluded-email.php';
			include_once 'class-wc-sc-coupon-message.php';
			include_once 'class-wc-sc-coupon-categories.php';
			include_once 'class-wc-sc-coupons-by-product-quantity.php';
			include_once 'class-wc-sc-coupon-refund-process.php';
			include_once 'class-wc-sc-background-upgrade.php';
			include_once 'blocks/class-wc-sc-gutenberg-coupon-block.php';

		}

		/**
		 * Process activation of the plugin
		 */
		public function process_activation() {

			if ( ! get_transient( '_smart_coupons_process_activation' ) ) {
				return;
			}

			delete_transient( '_smart_coupons_process_activation' );

			include_once 'class-wc-sc-act-deact.php';

			WC_SC_Act_Deact::process_activation();

		}

		/**
		 * Load action scheduler
		 */
		public function load_action_scheduler() {
			if ( ! class_exists( 'ActionScheduler' ) ) {
				include_once 'libraries/action-scheduler/action-scheduler.php';
			}
		}

		/**
		 * Set options
		 */
		public function add_sc_options() {

			$this->plugin_data = self::get_smart_coupons_plugin_data();

			add_option( 'woocommerce_delete_smart_coupon_after_usage', 'no', '', 'no' );
			add_option( 'woocommerce_smart_coupon_apply_before_tax', 'no', '', 'no' );
			add_option( 'woocommerce_smart_coupon_include_tax', 'no', '', 'no' );
			add_option( 'woocommerce_smart_coupon_show_my_account', 'yes', '', 'no' );
			add_option( 'smart_coupons_is_show_associated_coupons', 'no', '', 'no' );
			add_option( 'smart_coupons_show_coupon_description', 'no', '', 'no' );
			add_option( 'smart_coupons_is_send_email', 'yes', '', 'no' );
			add_option( 'smart_coupons_is_print_coupon', 'yes', '', 'no' );
			add_option( 'show_coupon_received_on_my_account', 'no', '', 'no' );
			add_option( 'pay_from_smart_coupon_of_original_order', 'yes', '', 'no' );
			add_option( 'stop_recursive_coupon_generation', 'no', '', 'no' );
			add_option( 'sc_gift_certificate_shop_loop_button_text', __( 'Select options', 'woocommerce-smart-coupons' ), '', 'no' );
			add_option( 'wc_sc_setting_max_coupon_to_show', '5', '', 'no' );
			add_option( 'smart_coupons_show_invalid_coupons_on_myaccount', 'no', '', 'no' );
			add_option( 'smart_coupons_sell_store_credit_at_less_price', 'no', '', 'no' );
			add_option( 'smart_coupons_display_coupon_receiver_details_form', 'yes', '', 'no' );

			// Convert SC admin email settings into WC email settings.
			$is_send_email = get_option( 'smart_coupons_is_send_email' );
			if ( false !== $is_send_email ) {
				$coupon_email_settings = get_option( 'woocommerce_wc_sc_email_coupon_settings' );
				if ( false === $coupon_email_settings ) {
					$coupon_email_settings            = array();
					$coupon_email_settings['enabled'] = $is_send_email;
					update_option( 'woocommerce_wc_sc_email_coupon_settings', $coupon_email_settings, 'no' );
				}
			}

			$is_combine_email = get_option( 'smart_coupons_combine_emails' );
			if ( false !== $is_combine_email ) {
				$combine_email_settings = get_option( 'woocommerce_wc_sc_combined_email_coupon_settings' );
				if ( false === $combine_email_settings ) {
					$combine_email_settings            = array();
					$combine_email_settings['enabled'] = $is_combine_email;
					update_option( 'woocommerce_wc_sc_combined_email_coupon_settings', $combine_email_settings, 'no' );
				}
			}

			$valid_designs = $this->get_valid_coupon_designs();

			$coupon_design = get_option( 'wc_sc_setting_coupon_design' );
			if ( false === $coupon_design ) {
				add_option( 'wc_sc_setting_coupon_design', 'basic', '', 'no' );
			} else {
				if ( 'custom-design' !== $coupon_design && ! in_array( $coupon_design, $valid_designs, true ) ) {
					update_option( 'wc_sc_setting_coupon_design', 'basic', 'no' );
				}
			}

			$coupon_background_color = get_option( 'wc_sc_setting_coupon_background_color' );
			if ( false === $coupon_background_color ) {
				add_option( 'wc_sc_setting_coupon_background_color', '#2b2d42', '', 'no' );
			} else {
				add_option( 'wc_sc_setting_coupon_third_color', $coupon_background_color, '', 'no' );
			}

			$coupon_foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color' );
			if ( false === $coupon_foreground_color ) {
				add_option( 'wc_sc_setting_coupon_foreground_color', '#edf2f4', '', 'no' );
			}

			$coupon_third_color = get_option( 'wc_sc_setting_coupon_third_color' );
			if ( false === $coupon_third_color ) {
				add_option( 'wc_sc_setting_coupon_third_color', '#d90429', '', 'no' );
			}

			$coupon_design_colors = get_option( 'wc_sc_setting_coupon_design_colors' );
			if ( false === $coupon_design_colors ) {
				if ( false !== $coupon_background_color && false !== $coupon_foreground_color ) {
					add_option( 'wc_sc_setting_coupon_design_colors', 'custom', '', 'no' );
				} else {
					add_option( 'wc_sc_setting_coupon_design_colors', '2b2d42-edf2f4-d90429', '', 'no' );
				}
			}

			$coupon_design_for_email = get_option( 'wc_sc_setting_coupon_design_for_email' );
			if ( false === $coupon_design_for_email ) {
				add_option( 'wc_sc_setting_coupon_design_for_email', 'email-coupon', '', 'no' );
			}

			$orders_prior_to_800 = $this->sc_get_option( 'wc_sc_old_orders_prior_to_800' );
			if ( false === $orders_prior_to_800 ) {
				$this->maybe_sync_orders_prior_to_800();
			}

		}

		/**
		 * Function to log messages generated by Smart Coupons plugin
		 *
		 * @param  string $level   Message type. Valid values: debug, info, notice, warning, error, critical, alert, emergency.
		 * @param  string $message The message to log.
		 */
		public function log( $level = 'notice', $message = '' ) {

			if ( empty( $message ) ) {
				return;
			}

			if ( function_exists( 'wc_get_logger' ) ) {
				$logger  = wc_get_logger();
				$context = array( 'source' => 'woocommerce-smart-coupons' );
				$logger->log( $level, $message, $context );
			} else {
				include_once plugin_dir_path( WC_PLUGIN_FILE ) . 'includes/class-wc-logger.php';
				$logger = new WC_Logger();
				$logger->add( 'woocommerce-smart-coupons', $message );
			}

		}

		/**
		 * Coupon's expiration date (formatted)
		 *
		 * @param int $expiry_date Expiry date of coupon.
		 * @return string $expires_string Formatted expiry date
		 */
		public function get_expiration_format( $expiry_date ) {

			if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
				$expiry_date = ( is_callable( array( $expiry_date, 'getTimestamp' ) ) ) ? $expiry_date->getTimestamp() : null;
			} elseif ( ! is_int( $expiry_date ) ) {
				$expiry_date = $this->strtotime( $expiry_date );
			}

			$expiry_date += $this->wc_timezone_offset();

			$expires_string = date_i18n( get_option( 'date_format', 'd-M-Y' ), $expiry_date );

			return apply_filters(
				'wc_sc_formatted_coupon_expiry_date',
				$expires_string,
				array(
					'source'      => $this,
					'expiry_date' => $expiry_date,
				)
			);

		}


		/**
		 * Function to send e-mail containing coupon code to receiver
		 *
		 * @param array   $coupon_title Associative array containing receiver's details.
		 * @param string  $discount_type Type of coupon.
		 * @param int     $order_id Associated order id.
		 * @param array   $gift_certificate_receiver_name Array of receiver's name.
		 * @param string  $message_from_sender Message added by sender.
		 * @param string  $gift_certificate_sender_name Sender name.
		 * @param string  $gift_certificate_sender_email Sender email.
		 * @param boolean $is_gift Whether it is a gift certificate or store credit.
		 */
		public function sa_email_coupon( $coupon_title, $discount_type, $order_id = '', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '', $is_gift = '' ) {

			$is_send_email  = $this->is_email_template_enabled();
			$combine_emails = $this->is_email_template_enabled( 'combine' );

			if ( 'yes' === $is_send_email ) {
				WC()->mailer();
			}

			$is_send_email = apply_filters(
				'wc_sc_is_send_coupon_email',
				$is_send_email,
				array(
					'source'                         => $this,
					'coupon_title'                   => $coupon_title,
					'discount_type'                  => $discount_type,
					'order_id'                       => $order_id,
					'gift_certificate_receiver_name' => $gift_certificate_receiver_name,
					'message_from_sender'            => $message_from_sender,
					'gift_certificate_sender_name'   => $gift_certificate_sender_name,
					'gift_certificate_sender_email'  => $gift_certificate_sender_email,
					'is_gift'                        => $is_gift,
				)
			);

			$order = ( ! empty( $order_id ) ) ? wc_get_order( $order_id ) : null;

			foreach ( $coupon_title as $email => $coupon ) {

				if ( empty( $email ) ) {
					$email = $gift_certificate_sender_email;
				}

				$amount      = $coupon['amount'];
				$coupon_code = strtolower( $coupon['code'] );

				if ( ! empty( $order_id ) ) {
					$coupon_receiver_details = $this->get_post_meta( $order_id, 'sc_coupon_receiver_details', true, false, $order );
					if ( ! is_array( $coupon_receiver_details ) || empty( $coupon_receiver_details ) ) {
						$coupon_receiver_details = array();
					}
					$coupon_receiver_details[] = array(
						'code'    => $coupon_code,
						'amount'  => $amount,
						'email'   => $email,
						'message' => $message_from_sender,
					);
					$this->update_post_meta( $order_id, 'sc_coupon_receiver_details', $coupon_receiver_details, false, $order );
				}

				$action_args = apply_filters(
					'wc_sc_email_coupon_notification_args',
					array(
						'order_id'                      => $order_id,
						'email'                         => $email,
						'coupon'                        => $coupon,
						'discount_type'                 => $discount_type,
						'receiver_name'                 => $gift_certificate_receiver_name,
						'message_from_sender'           => $message_from_sender,
						'gift_certificate_sender_name'  => $gift_certificate_sender_name,
						'gift_certificate_sender_email' => $gift_certificate_sender_email,
						'is_gift'                       => $is_gift,
					)
				);

				$schedule_gift_sending = 'no';
				if ( ! empty( $order_id ) ) {
					$schedule_gift_sending = ( $this->is_callable( $order, 'get_meta' ) ) ? $order->get_meta( 'wc_sc_schedule_gift_sending' ) : $this->get_post_meta( $order_id, 'wc_sc_schedule_gift_sending', true );
				}

				$is_schedule_gift_sending = 'no';
				if ( 'yes' === $schedule_gift_sending ) {
					$coupon_id               = wc_get_coupon_id_by_code( $coupon_code );
					$coupon_receiver_details = $this->get_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details', true );
					$scheduled_coupon_code   = ( ! empty( $coupon_receiver_details['coupon_details']['code'] ) ) ? strtolower( $coupon_receiver_details['coupon_details']['code'] ) : '';
					if ( $scheduled_coupon_code === $coupon_code ) {
						$is_schedule_gift_sending = 'yes';
					}
				}

				if ( 'yes' === $is_send_email && ( 'no' === $combine_emails || 'yes' === $is_schedule_gift_sending ) ) {
					$current_filter                    = current_filter();
					$order_actions_to_ignore_for_email = $this->order_actions_to_ignore_for_email();
					if ( ! in_array( $current_filter, $order_actions_to_ignore_for_email, true ) ) {
						// Trigger email notification.
						do_action( 'wc_sc_email_coupon_notification', $action_args );
					}
					if ( 'yes' === $is_schedule_gift_sending ) {
						// Delete receiver detail post meta as it is no longer necessary.
						$this->delete_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details' );
					}
				}
			}

		}

		/**
		 * Function to send combined e-mail containing coupon codes to receiver
		 *
		 * @param string $receiver_email receiver's email.
		 * @param array  $receiver_details receiver details(code,message etc).
		 * @param int    $order_id Associated order id.
		 * @param string $gift_certificate_sender_name Sender name.
		 * @param string $gift_certificate_sender_email Sender email.
		 */
		public function send_combined_coupon_email( $receiver_email = '', $receiver_details = array(), $order_id = 0, $gift_certificate_sender_name = '', $gift_certificate_sender_email = '' ) {

			$is_send_email  = $this->is_email_template_enabled();
			$combine_emails = $this->is_email_template_enabled( 'combine' );

			if ( 'yes' === $is_send_email && 'yes' === $combine_emails ) {
				$current_filter                    = current_filter();
				$order_actions_to_ignore_for_email = $this->order_actions_to_ignore_for_email();
				if ( ! in_array( $current_filter, $order_actions_to_ignore_for_email, true ) ) {
					WC()->mailer();

					$order = ( ! empty( $order_id ) ) ? wc_get_order( $order_id ) : null;

					$is_gift = '';
					if ( ! empty( $order_id ) ) {
						$is_gift = $this->get_post_meta( $order_id, 'is_gift', true );
					}

					if ( count( $receiver_details ) === 1 ) {
						$coupon_code         = ( ! empty( $receiver_details[0]['code'] ) ) ? $receiver_details[0]['code'] : '';
						$message_from_sender = ( ! empty( $receiver_details[0]['message'] ) ) ? $receiver_details[0]['message'] : '';

						$coupon        = new WC_Coupon( $coupon_code );
						$coupon_amount = $this->get_amount( $coupon, true, $order );
						$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
						$coupon_data   = $this->get_coupon_meta_data( $coupon );

						$coupon_detail = array(
							'amount' => $coupon_amount,
							'code'   => $coupon_code,
						);

						$action_args = apply_filters(
							'wc_sc_email_coupon_notification_args',
							array(
								'order_id'            => $order_id,
								'email'               => $receiver_email,
								'coupon'              => $coupon_detail,
								'discount_type'       => $discount_type,
								'receiver_name'       => '',
								'message_from_sender' => $message_from_sender,
								'gift_certificate_sender_name' => $gift_certificate_sender_name,
								'gift_certificate_sender_email' => $gift_certificate_sender_email,
								'is_gift'             => $is_gift,
							)
						);
						// Trigger single email notification.
						do_action( 'wc_sc_email_coupon_notification', $action_args );
						return;
					}

					$action_args = apply_filters(
						'wc_sc_email_coupon_notification_args',
						array(
							'order_id'                     => $order_id,
							'email'                        => $receiver_email,
							'receiver_details'             => $receiver_details,
							'gift_certificate_sender_name' => $gift_certificate_sender_name,
							'gift_certificate_sender_email' => $gift_certificate_sender_email,
							'is_gift'                      => $is_gift,
						)
					);

					// Trigger combined email notification.
					do_action( 'wc_sc_combined_email_coupon_notification', $action_args );
				}
			}
		}

		/**
		 * Function to schedule e-mail sending process containing coupon code to customer
		 *
		 * @param array  $action_args arguments for Action Scheduler.
		 * @param string $sending_timestamp timestamp for scheduling email.
		 * @return boolean email sending scheduled or not.
		 */
		public function schedule_coupon_email( $action_args = array(), $sending_timestamp = '' ) {

			if ( empty( $action_args ) || empty( $sending_timestamp ) ) {
				return false;
			}

			$coupon_id = 0;
			if ( isset( $action_args['coupon_id'] ) && ! empty( $action_args['coupon_id'] ) ) {
				$coupon_id = $action_args['coupon_id'];
			}

			$ref_key = '';
			if ( isset( $action_args['ref_key'] ) && ! empty( $action_args['ref_key'] ) ) {
				$ref_key = $action_args['ref_key'];
			}

			if ( ! empty( $coupon_id ) && ! empty( $ref_key ) && function_exists( 'as_schedule_single_action' ) ) {
				if ( ! empty( $action_args['order_id'] ) ) {
					$actions_id = as_schedule_single_action( $sending_timestamp, 'wc_sc_send_scheduled_coupon_email', $action_args );
				} else {
					$actions_id = as_schedule_single_action( $sending_timestamp, 'wc_sc_import_send_scheduled_coupon_email', $action_args );
				}
				if ( $actions_id ) {
					$scheduled_actions_ids = $this->get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );
					if ( empty( $scheduled_actions_ids ) || ! is_array( $scheduled_actions_ids ) ) {
						$scheduled_actions_ids = array();
					}
					$scheduled_actions_ids[ $ref_key ] = $actions_id;
					// Stored actions ids in coupons so that we can delete them when coupon gets deleted or email is sent successfully.
					$this->update_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', $scheduled_actions_ids );
					return true;
				}
			}
			return false;
		}

		/**
		 * Function to send scheduled coupon's e-mail containing coupon code to receiver. It is triggered through Action Scheduler
		 *
		 * @param string $auto_generate is auto generated coupon.
		 * @param int    $coupon_id Associated coupon id.
		 * @param int    $parent_id Associated parent coupon id.
		 * @param int    $order_id Associated order id.
		 * @param string $receiver_email receiver email.
		 * @param string $sender_message_index_key key containing index of sender's message from gift_receiver_message meta in order.
		 * @param string $ref_key timestamp based reference key.
		 */
		public function send_scheduled_coupon_email( $auto_generate = '', $coupon_id = '', $parent_id = '', $order_id = '', $receiver_email = '', $sender_message_index_key = '', $ref_key = '' ) {

			if ( ! empty( $coupon_id ) && ! empty( $order_id ) && ! empty( $receiver_email ) ) {

				$coupon = new WC_Coupon( $coupon_id );

				$coupon_status = ( $this->is_wc_greater_than( '6.1.2' ) && $this->is_callable( $coupon, 'get_status' ) ) ? $coupon->get_status() : get_post_status( $coupon_id );
				if ( 'publish' !== $coupon_status ) {
					return;
				}

				$order = wc_get_order( $order_id );
				if ( is_a( $coupon, 'WC_Coupon' ) && is_a( $order, 'WC_Order' ) ) {
					$is_callable_order_get_meta          = $this->is_callable( $order, 'get_meta' );
					$is_callable_coupon_get_meta         = $this->is_callable( $coupon, 'get_meta' );
					$is_callable_coupon_update_meta_data = $this->is_callable( $coupon, 'update_meta_data' );
					$sc_disable_email_restriction        = $this->get_post_meta( $parent_id, 'sc_disable_email_restriction', true );
					if ( $this->is_wc_gte_30() ) {
						$discount_type = $coupon->get_discount_type();
						$coupon_code   = $coupon->get_code();
					} else {
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}

					$coupon_amount = $this->get_amount( $coupon, true, $order );

					$coupon_details = array(
						$receiver_email => array(
							'parent' => $parent_id,
							'code'   => $coupon_code,
							'amount' => $coupon_amount,
						),
					);

					$receiver_name                 = '';
					$message_from_sender           = '';
					$gift_certificate_sender_name  = '';
					$gift_certificate_sender_email = '';

					$is_gift = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'is_gift' ) : $this->get_post_meta( $order_id, 'is_gift', true );

					// In case of auto generated coupons receiver's details are saved in generated coupon.
					if ( 'yes' === $auto_generate ) {
						$coupon_receiver_details = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'wc_sc_coupon_receiver_details' ) : $this->get_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details', true );
						if ( ! empty( $coupon_receiver_details ) && is_array( $coupon_receiver_details ) ) {
							$message_from_sender           = $coupon_receiver_details['message_from_sender'];
							$gift_certificate_sender_name  = $coupon_receiver_details['gift_certificate_sender_name'];
							$gift_certificate_sender_email = $coupon_receiver_details['gift_certificate_sender_email'];
						}
					} else {
						$receivers_messages = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'gift_receiver_message' ) : $this->get_post_meta( $order_id, 'gift_receiver_message', true );
						if ( strpos( $sender_message_index_key, ':' ) > 0 ) {
							$index_keys    = explode( ':', $sender_message_index_key );
							$coupon_index  = $index_keys[0];
							$message_index = $index_keys[1];
							if ( isset( $receivers_messages[ $coupon_index ][ $message_index ] ) ) {
								$message_from_sender = $receivers_messages[ $coupon_index ][ $message_index ];
							}
						}
					}

					$this->sa_email_coupon( $coupon_details, $discount_type, $order_id, $receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $is_gift );

					if ( ( 'no' === $sc_disable_email_restriction || empty( $sc_disable_email_restriction ) ) ) {
						$old_customers_email_ids   = (array) maybe_unserialize( ( $this->is_callable( $coupon, 'get_email_restrictions' ) ) ? $coupon->get_email_restrictions() : $this->get_post_meta( $coupon_id, 'customer_email', true ) );
						$old_customers_email_ids[] = $receiver_email;
						if ( true === $is_callable_coupon_update_meta_data ) {
							$coupon->set_email_restrictions( $old_customers_email_ids );
						} else {
							update_post_meta( $coupon_id, 'customer_email', $old_customers_email_ids );
						}
					}

					if ( ! empty( $ref_key ) ) {
						$scheduled_actions_ids = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'wc_sc_scheduled_actions_ids' ) : $this->get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );
						if ( isset( $scheduled_actions_ids[ $ref_key ] ) ) {
							unset( $scheduled_actions_ids[ $ref_key ] );
						}
						if ( ! empty( $scheduled_actions_ids ) ) {
							if ( true === $is_callable_coupon_update_meta_data ) {
								$coupon->update_meta_data( 'wc_sc_scheduled_actions_ids', $scheduled_actions_ids );
							} else {
								update_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', $scheduled_actions_ids );
							}
						} else {
							// Delete scheduled action ids meta since it is empty now.
							$this->delete_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', null, $coupon );
						}
					}
					if ( $this->is_callable( $coupon, 'save' ) ) {
						$coupon->save();
					}
				}
			}
		}

		/**
		 * Function to send scheduled coupon's e-mail containing coupon code to receiver. It is triggered through Action Scheduler
		 *
		 * @param string $auto_generate is auto generated coupon.
		 * @param int    $coupon_id Associated coupon id.
		 * @param int    $parent_id Associated parent coupon id.
		 * @param string $receiver_email receiver email.
		 * @param string $ref_key timestamp based reference key.
		 */
		public function import_send_scheduled_coupon_email( $auto_generate = '', $coupon_id = 0, $parent_id = 0, $receiver_email = '', $ref_key = '' ) {

			if ( ! empty( $coupon_id ) && ! empty( $receiver_email ) ) {

				$coupon = new WC_Coupon( $coupon_id );

				$coupon_status = ( $this->is_wc_greater_than( '6.1.2' ) && $this->is_callable( $coupon, 'get_status' ) ) ? $coupon->get_status() : get_post_status( $coupon_id );
				if ( ! in_array( $coupon_status, array( 'publish', 'future' ), true ) ) {
					return;
				}

				if ( is_a( $coupon, 'WC_Coupon' ) ) {
					$is_update_coupon                    = false;
					$is_callable_coupon_get_meta         = $this->is_callable( $coupon, 'get_meta' );
					$is_callable_coupon_update_meta_data = $this->is_callable( $coupon, 'update_meta_data' );
					if ( $this->is_wc_gte_30() ) {
						$discount_type = $this->is_callable( $coupon, 'get_discount_type' ) ? $coupon->get_discount_type() : '';
						$coupon_code   = $this->is_callable( $coupon, 'get_code' ) ? $coupon->get_code() : '';
					} else {
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}

					$coupon_amount = $this->get_amount( $coupon, true );

					$coupon_details = array(
						$receiver_email => array(
							'parent' => $parent_id,
							'code'   => $coupon_code,
							'amount' => $coupon_amount,
						),
					);

					$this->sa_email_coupon( $coupon_details, $discount_type, 0 );

					if ( ! empty( $ref_key ) ) {
						$scheduled_actions_ids = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'wc_sc_scheduled_actions_ids' ) : $this->get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );
						if ( isset( $scheduled_actions_ids[ $ref_key ] ) ) {
							unset( $scheduled_actions_ids[ $ref_key ] );
						}
						if ( ! empty( $scheduled_actions_ids ) ) {
							if ( true === $is_callable_coupon_update_meta_data ) {
								$coupon->update_meta_data( 'wc_sc_scheduled_actions_ids', $scheduled_actions_ids );
								$is_update_coupon = true;
							} else {
								$this->update_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', $scheduled_actions_ids );
							}
						} else {
							// Delete scheduled action ids meta since it is empty now.
							$this->delete_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', null, $coupon );
						}
					}
					if ( true === $is_update_coupon && $this->is_callable( $coupon, 'save' ) ) {
						$coupon->save();
					}
				}
			}
		}

		/**
		 * Function to process scheduled coupons.
		 *
		 * @param int $coupon_id published coupon's id.
		 */
		public function process_published_scheduled_coupon( $coupon_id = 0 ) {

			$post_type = $this->get_post_type( $coupon_id );
			if ( 'shop_coupon' !== $post_type ) {
				return false;
			}

			$coupon = new WC_Coupon( $coupon_id );
			if ( is_a( $coupon, 'WC_Coupon' ) ) {
				$is_callable_coupon_get_meta = $this->is_callable( $coupon, 'get_meta' );
				$order_id                    = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'generated_from_order_id' ) : $this->get_post_meta( $coupon_id, 'generated_from_order_id', true );
				$order                       = wc_get_order( $order_id );
				if ( is_a( $order, 'WC_Order' ) ) {
					$coupon_receiver_details = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'wc_sc_coupon_receiver_details' ) : $this->get_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details', true );
					if ( ! empty( $coupon_receiver_details ) && is_array( $coupon_receiver_details ) ) {
						$parent_id                     = $coupon_receiver_details['coupon_details']['parent'];
						$receiver_email                = $coupon_receiver_details['gift_certificate_receiver_email'];
						$gift_certificate_sender_name  = $coupon_receiver_details['gift_certificate_sender_name'];
						$gift_certificate_sender_email = $coupon_receiver_details['gift_certificate_sender_email'];
						$sending_timestamp             = get_post_time( 'U', true, $coupon_id ); // Get coupon publish timestamp.
						$action_args                   = array(
							'auto_generate'     => 'yes',
							'coupon_id'         => $coupon_id,
							'parent_id'         => $parent_id, // Parent coupon id.
							'order_id'          => $order_id,
							'receiver_email'    => $receiver_email,
							'message_index_key' => '',
							'ref_key'           => uniqid(), // A unique timestamp key to relate action schedulers with their coupons.
						);
						$is_scheduled                  = $this->schedule_coupon_email( $action_args, $sending_timestamp );
						if ( ! $is_scheduled ) {
							if ( $this->is_wc_gte_30() ) {
								$coupon_code = $coupon->get_code();
							} else {
								$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
							}
							/* translators: 1. Receiver email 2. Coupon code 3. Order id */
							$this->log( 'error', sprintf( __( 'Failed to schedule email to "%1$s" for coupon "%2$s" received from order #%3$s.', 'woocommerce-smart-coupons' ), $receiver_email, $coupon_code, $order_id ) );
						}
					}
				}
			}

		}

		/**
		 * Function to delete action schedulers when associated coupon is deleted.
		 *
		 * @param int $coupon_id coupon id.
		 */
		public function delete_scheduled_coupon_actions( $coupon_id = 0 ) {

			global $post_type;

			if ( 'shop_coupon' !== $post_type ) {
				return false;
			}

			$coupon = new WC_Coupon( $coupon_id );

			if ( is_a( $coupon, 'WC_Coupon' ) ) {

				$scheduled_actions_ids = ( $this->is_callable( $coupon, 'get_meta' ) ) ? $coupon->get_meta( 'wc_sc_scheduled_actions_ids' ) : $this->get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );

				if ( ! empty( $scheduled_actions_ids ) && is_array( $scheduled_actions_ids ) ) {

					if ( ! class_exists( 'ActionScheduler' ) || ! is_callable( array( 'ActionScheduler', 'store' ) ) ) {
						return false;
					}

					foreach ( $scheduled_actions_ids as $ref_key => $action_id ) {
						$action_scheduler = ActionScheduler::store()->fetch_action( $action_id );
						if ( is_a( $action_scheduler, 'ActionScheduler_Action' ) && is_callable( array( $action_scheduler, 'is_finished' ) ) ) {
							$is_action_complete = $action_scheduler->is_finished();

							// Delete only unfinished actions related to coupon.
							if ( ! $is_action_complete ) {
								ActionScheduler::store()->delete_action( $action_id );
							}
						}
					}
				}
			}

		}

		/**
		 * Function to check if passed timestamp is valid.
		 *
		 * @param string $timestamp timestamp.
		 * @return boolean is valid timestamp.
		 */
		public function is_valid_timestamp( $timestamp = '' ) {

			if ( empty( $timestamp ) || ! is_numeric( $timestamp ) ) {
				return false;
			}

			// Check if time is already passed.
			if ( time() > $timestamp ) {
				return false;
			}
			return true;
		}


		/**
		 * Function to enqueue scripts in footer.
		 */
		public function enqueue_admin_footer_scripts() {

			global $pagenow, $typenow;

			if ( empty( $pagenow ) || 'edit.php' !== $pagenow ) {
				return;
			}

			$coupon_status = ( ! empty( $_GET['post_status'] ) ) ? wc_clean( wp_unslash( $_GET['post_status'] ) ) : ''; // phpcs:ignore
			if ( 'edit.php' === $pagenow && 'shop_coupon' === $typenow && 'trash' === $coupon_status ) {
				if ( ! wp_script_is( 'jquery' ) ) {
					wp_enqueue_script( 'jquery' );
				}
				?>
				<script type="text/javascript">
					jQuery(function(){
						jQuery('body.post-type-shop_coupon .wp-list-table .delete a.submitdelete').click(function(e) {
							e.preventDefault();
							let coupon_delete_elem = jQuery(this);
							let coupon_delete_url = jQuery(coupon_delete_elem).attr('href');
							let coupon_id = jQuery(coupon_delete_elem).closest('.type-shop_coupon').find('[name="post[]"]').val();
							jQuery.ajax({
								url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
								type: 'post',
								dataType: 'json',
								data: {
									action: 'wc_sc_check_scheduled_coupon_actions',
									security: '<?php echo esc_html( wp_create_nonce( 'wc-sc-check-coupon-scheduled-actions' ) ); ?>',
									coupon_id: coupon_id
								},
								success: function( response ){
									if ( undefined !== response.has_scheduled_actions && '' !== response.has_scheduled_actions  && 'yes' === response.has_scheduled_actions ) {
										let confirm_delete = window.confirm( '<?php echo esc_js( __( 'This coupon has pending emails to be sent. Deleting it will delete those emails also. Are you sure to delete this coupon?', 'woocommerce-smart-coupons' ) ); ?>' );
										if( confirm_delete ) {
											window.location.href = coupon_delete_url;
										}
									} else {
										window.location.href = coupon_delete_url;
									}
								},
								error: function( jq_xhr, exception ) {
									alert( '<?php echo esc_js( __( 'An error has occurred. Please try again later.', 'woocommerce-smart-coupons' ) ); ?>' );
								}
							});
						});
					});
				</script>
				<?php
			}
		}

		/**
		 * Function to check if coupon has any pending scheduled actions.
		 */
		public function check_scheduled_coupon_actions() {

			check_ajax_referer( 'wc-sc-check-coupon-scheduled-actions', 'security' );

			$coupon_id = ( ! empty( $_POST['coupon_id'] ) ) ? wc_clean( wp_unslash( $_POST['coupon_id'] ) ) : ''; // phpcs:ignore

			$response = array(
				'has_scheduled_actions' => 'no',
			);

			if ( ! empty( $coupon_id ) ) {
				$coupon = new WC_Coupon( $coupon_id );
				if ( is_a( $coupon, 'WC_Coupon' ) ) {
					$scheduled_actions_ids = ( $this->is_callable( $coupon, 'get_meta' ) ) ? $coupon->get_meta( 'wc_sc_scheduled_actions_ids' ) : $this->get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );
					if ( is_array( $scheduled_actions_ids ) && ! empty( $scheduled_actions_ids ) ) {
						$response['has_scheduled_actions'] = 'yes';
					}
				}
			}

			wp_send_json( $response );
		}

		/**
		 * Register new endpoint to use inside My Account page.
		 */
		public function sc_add_endpoints() {

			if ( empty( WC_SC_Display_Coupons::$endpoint ) ) {
				WC_SC_Display_Coupons::$endpoint = WC_SC_Display_Coupons::get_endpoint();
			}

			if ( $this->is_wc_gte_26() ) {
				add_rewrite_endpoint( WC_SC_Display_Coupons::$endpoint, EP_ROOT | EP_PAGES );
				$this->sc_check_if_flushed_rules();
			}

		}

		/**
		 * To register Smart Coupons Endpoint after plugin is activated - Necessary
		 */
		public function sc_check_if_flushed_rules() {
			$sc_check_flushed_rules = get_option( 'sc_flushed_rules', 'notfound' );
			if ( 'notfound' === $sc_check_flushed_rules ) {
				flush_rewrite_rules(); // phpcs:ignore
				update_option( 'sc_flushed_rules', 'found', 'no' );
			}
		}

		/**
		 * Register & enqueue Smart Coupons CSS
		 */
		public function register_plugin_styles() {
			global $pagenow;

			$is_frontend         = ( ! is_admin() ) ? true : false;
			$is_valid_post_page  = ( ! empty( $pagenow ) && in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ), true ) ) ? true : false;
			$is_valid_admin_page = ( ( ! empty( $_GET['page'] ) && 'wc-smart-coupons' === wc_clean( wp_unslash( $_GET['page'] ) ) ) || ( ! empty( $_GET['tab'] ) && 'wc-smart-coupons' === wc_clean( wp_unslash( $_GET['tab'] ) ) ) ) ? true : false; // phpcs:ignore

			if ( $is_frontend || $is_valid_admin_page || $is_valid_post_page ) {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_register_style( 'smart-coupon', untrailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . '/assets/css/smart-coupon' . $suffix . '.css', array(), $this->plugin_data['Version'] );
				wp_register_style( 'smart-coupon-designs', untrailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . '/assets/css/smart-coupon-designs.css', array(), $this->plugin_data['Version'] );
			}
		}

		/**
		 * Get coupon style attributes
		 *
		 * @return string The coupon style attribute
		 */
		public function get_coupon_style_attributes() {

			$styles = array();

			$coupon_design = get_option( 'wc_sc_setting_coupon_design', 'basic' );

			if ( 'custom-design' !== $coupon_design ) {
				$styles = array(
					'background-color: var(--sc-color1) !important;',
					'color: var(--sc-color2) !important;',
					'border-color: var(--sc-color3) !important;',
				);
			}

			$styles = implode( ' ', $styles );

			return apply_filters( 'wc_sc_coupon_style_attributes', $styles );

		}

		/**
		 * Get coupon container classes
		 *
		 * @return string The coupon container classes
		 */
		public function get_coupon_container_classes() {

			return implode( ' ', apply_filters( 'wc_sc_coupon_container_classes', array( 'medium', get_option( 'wc_sc_setting_coupon_design', 'basic' ) ) ) );

		}

		/**
		 * Get coupon content classes
		 *
		 * @return string The coupon content classes
		 */
		public function get_coupon_content_classes() {

			return implode( ' ', apply_filters( 'wc_sc_coupon_content_classes', array( 'dashed', 'small' ) ) );

		}

		/**
		 * Formatted coupon data
		 *
		 * @param WC_Coupon $coupon Coupon object.
		 * @return array $coupon_data Associative array containing formatted coupon data.
		 */
		public function get_coupon_meta_data( $coupon ) {
			global $store_credit_label, $post;

			$order = null;

			if ( ! empty( $post->ID ) && 'shop_order' === $this->get_post_type( $post->ID ) ) {
				$order = wc_get_order( $post->ID );
			}

			$all_discount_types = wc_get_coupon_types();

			if ( $this->is_wc_gte_30() ) {
				$coupon_id     = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

			$coupon_amount = $this->get_amount( $coupon, true, $order );

			$coupon_data = array();
			switch ( $discount_type ) {
				case 'smart_coupon':
					$coupon_data['coupon_type']   = ! empty( $store_credit_label['singular'] ) ? ucwords( $store_credit_label['singular'] ) : __( 'Store Credit', 'woocommerce-smart-coupons' );
					$coupon_data['coupon_amount'] = wc_price( $coupon_amount );
					break;

				case 'fixed_cart':
					$coupon_data['coupon_type']   = __( 'Cart Discount', 'woocommerce-smart-coupons' );
					$coupon_data['coupon_amount'] = wc_price( $coupon_amount );
					break;

				case 'fixed_product':
					$coupon_data['coupon_type']   = __( 'Product Discount', 'woocommerce-smart-coupons' );
					$coupon_data['coupon_amount'] = wc_price( $coupon_amount );
					break;

				case 'percent_product':
					$coupon_data['coupon_type']   = __( 'Product Discount', 'woocommerce-smart-coupons' );
					$coupon_data['coupon_amount'] = $coupon_amount . '%';
					break;

				case 'percent':
					$coupon_data['coupon_type']   = ( $this->is_wc_gte_30() ) ? __( 'Discount', 'woocommerce-smart-coupons' ) : __( 'Cart Discount', 'woocommerce-smart-coupons' );
					$coupon_data['coupon_amount'] = $coupon_amount . '%';
					$max_discount                 = $this->get_post_meta( $coupon_id, 'wc_sc_max_discount', true, true, $order );
					if ( ! empty( $max_discount ) && is_numeric( $max_discount ) ) {
						/* translators: %s: Maximum coupon discount amount */
						$coupon_data['coupon_type'] .= ' ' . sprintf( __( ' upto %s', 'woocommerce-smart-coupons' ), wc_price( $max_discount ) );
					}
					break;

				default:
					$default_coupon_type          = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
					$coupon_data['coupon_type']   = apply_filters( 'wc_sc_coupon_type', $default_coupon_type, $coupon, $all_discount_types );
					$coupon_data['coupon_amount'] = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
					break;

			}
			return $coupon_data;
		}

		/**
		 * Generate coupon description
		 *
		 * @param array $args The arguments.
		 * @return string
		 */
		public function generate_coupon_description( $args = array() ) {
			$coupon            = ( ! empty( $args['coupon_object'] ) ) ? $args['coupon_object'] : null;
			$descriptions      = array();
			$descriptions_data = array();
			if ( $this->is_wc_gte_30() && is_object( $coupon ) ) {
				$discount_type = ( is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
				$expiry_time   = ( is_callable( array( $coupon, 'get_meta' ) ) ) ? $coupon->get_meta( 'wc_sc_expiry_time' ) : '';

				$expiry_needed_in_design = array( 'ticket', 'special' );
				$design                  = get_option( 'wc_sc_setting_coupon_design', 'basic' );

				$descriptions_data['minimum_amount']              = ( is_callable( array( $coupon, 'get_minimum_amount' ) ) ) ? $coupon->get_minimum_amount() : '';
				$descriptions_data['maximum_amount']              = ( is_callable( array( $coupon, 'get_maximum_amount' ) ) ) ? $coupon->get_maximum_amount() : '';
				$descriptions_data['exclude_sale_items']          = ( is_callable( array( $coupon, 'get_exclude_sale_items' ) ) ) ? $coupon->get_exclude_sale_items() : '';
				$descriptions_data['product_ids']                 = ( is_callable( array( $coupon, 'get_product_ids' ) ) ) ? $coupon->get_product_ids() : array();
				$descriptions_data['excluded_product_ids']        = ( is_callable( array( $coupon, 'get_excluded_product_ids' ) ) ) ? $coupon->get_excluded_product_ids() : array();
				$descriptions_data['product_categories']          = ( is_callable( array( $coupon, 'get_product_categories' ) ) ) ? $coupon->get_product_categories() : array();
				$descriptions_data['excluded_product_categories'] = ( is_callable( array( $coupon, 'get_excluded_product_categories' ) ) ) ? $coupon->get_excluded_product_categories() : array();

				$check_descriptions_data = array_filter( $descriptions_data );

				if ( in_array( $design, $expiry_needed_in_design, true ) || empty( $check_descriptions_data ) ) {
					$descriptions_data['date_expires'] = ( is_callable( array( $coupon, 'get_date_expires' ) ) ) ? $coupon->get_date_expires() : '';
				}

				$max_fields = apply_filters(
					'wc_sc_max_fields_to_show_in_coupon_description',
					2,
					array(
						'source'        => $this,
						'coupon_object' => $coupon,
					)
				);

				if ( ! empty( $descriptions_data ) ) {
					foreach ( $descriptions_data as $key => $data ) {
						if ( count( $descriptions ) > $max_fields ) {
							break;
						}
						if ( ! empty( $data ) ) {
							switch ( $key ) {
								case 'minimum_amount':
									/* translators: Formatted minimum amount */
									$descriptions[] = sprintf( __( 'Spend at least %s', 'woocommerce-smart-coupons' ), wc_price( $data ) );
									break;
								case 'maximum_amount':
									/* translators: Formatted maximum amount */
									$descriptions[] = sprintf( __( 'Spend up to %s', 'woocommerce-smart-coupons' ), wc_price( $data ) );
									break;
								case 'exclude_sale_items':
									/* translators: Formatted maximum amount */
									$descriptions[] = sprintf( __( 'Not valid for sale items', 'woocommerce-smart-coupons' ), wc_price( $data ) );
									break;
								case 'product_ids':
									$get_product_names = $this->get_coupon_product_names( $data );
									$product_names     = ( ! empty( $get_product_names ) && is_array( $get_product_names ) ) ? implode( ', ', $get_product_names ) : '';
									/* translators: Product names */
									$descriptions[] = sprintf( __( 'Valid for %s', 'woocommerce-smart-coupons' ), $product_names );
									break;
								case 'excluded_product_ids':
									$get_product_names = $this->get_coupon_product_names( $data );
									$product_names     = ( ! empty( $get_product_names ) && is_array( $get_product_names ) ) ? implode( ', ', $get_product_names ) : '';
									/* translators: Excluded product names */
									$descriptions[] = sprintf( __( 'Not valid for %s', 'woocommerce-smart-coupons' ), $product_names );
									break;
								case 'product_categories':
									$get_product_categories   = $this->get_coupon_category_names( $data );
									$product_categories       = ( ! empty( $get_product_categories ) ) ? implode( ', ', $get_product_categories ) : '';
									$count_product_categories = ( ! empty( $get_product_categories ) ) ? count( $get_product_categories ) : 1;
									/* translators: 1: The category names */
									$descriptions[] = sprintf( esc_html( _n( 'Valid for category %s', 'Valid for categories %s', $count_product_categories, 'woocommerce-smart-coupons' ) ), $product_categories );
									break;
								case 'excluded_product_categories':
									$get_product_categories   = $this->get_coupon_category_names( $data );
									$product_categories       = ( ! empty( $get_product_categories ) ) ? implode( ', ', $get_product_categories ) : '';
									$count_product_categories = ( ! empty( $get_product_categories ) ) ? count( $get_product_categories ) : 1;
									/* translators: 1: The category names excluded */
									$descriptions[] = sprintf( esc_html( _n( 'Not valid for category %s', 'Not valid for categories %s', $count_product_categories, 'woocommerce-smart-coupons' ) ), $product_categories );
									break;
								case 'date_expires':
									if ( $data instanceof WC_DateTime ) {
										$expiry_date = ( is_object( $data ) && is_callable( array( $data, 'getTimestamp' ) ) ) ? $data->getTimestamp() : null;
									} elseif ( ! is_int( $expiry_date ) ) {
										$expiry_date = $this->strtotime( $expiry_date );
									}
									if ( ! empty( $expiry_date ) && is_int( $expiry_date ) && ! empty( $expiry_time ) ) {
										$expiry_date += $expiry_time; // Adding expiry time to expiry date.
									}
									if ( ! empty( $expiry_date ) ) {
										/* translators: 1: The expiry date */
										$descriptions[] = sprintf( __( 'Expiry: %s', 'woocommerce-smart-coupons' ), $this->get_expiration_format( $expiry_date ) );
									}
									break;
							}
						}
					}
				}
			}
			if ( empty( $descriptions ) ) {
				$descriptions[] = __( 'Valid on entire range of products. Buy anything in the store.', 'woocommerce-smart-coupons' );
			}
			return apply_filters(
				'wc_sc_generated_coupon_description',
				implode( '. ', $descriptions ),
				array(
					'source'        => $this,
					'coupon_object' => $coupon,
				)
			);
		}

		/**
		 * Get coupon category names.
		 *
		 * @since 5.7.0
		 *
		 * @param array $category_ids Category IDs.
		 * @return array
		 */
		public function get_coupon_category_names( $category_ids = array() ) {
			$category_names = array();

			if ( empty( $category_ids ) || ! is_array( $category_ids ) ) {
				return $category_names;
			}

			$category_name_count_restriction = (int) apply_filters(
				'wc_sc_max_restricted_category_names',
				2,
				array(
					'source' => $this,
					'data'   => $category_ids,
				)
			);

			if ( count( $category_ids ) > $category_name_count_restriction ) {
				$category_ids = array_slice( $category_ids, 0, $category_name_count_restriction );
			}
			$category_names = get_terms(
				array(
					'taxonomy' => 'product_cat',
					'include'  => $category_ids,
					'fields'   => 'id=>name',
					'get'      => 'all',
				)
			);

			return array_filter( $category_names );
		}

		/**
		 * Get coupon product's names.
		 *
		 * @since 5.7.0
		 *
		 * @param array $product_ids Product IDs.
		 * @return array
		 */
		public function get_coupon_product_names( $product_ids = array() ) {
			$product_names = array();

			if ( empty( $product_ids ) || ! is_array( $product_ids ) ) {
				return $product_names;
			}

			$data_count         = count( $product_ids );
			$product_name_count = 0;

			$product_name_count_restriction = (int) apply_filters(
				'wc_sc_max_restricted_product_names',
				2,
				array(
					'source' => $this,
					'data'   => $product_ids,
				)
			);

			for ( $i = 0; $i < $data_count && $product_name_count < $product_name_count_restriction; $i++ ) {
				$product = wc_get_product( $product_ids[ $i ] );
				if ( is_object( $product ) && is_callable( array( $product, 'get_name' ) ) ) {
					$product_names[] = $product->get_name();
					$product_name_count++;
				}
			}

			return array_filter( $product_names );

		}

		/**
		 * Get valid coupon designs
		 *
		 * @return array
		 */
		public function get_valid_coupon_designs() {
			$valid_designs = array(
				'flat',
				'promotion',
				'ticket',
				'festive',
				'special',
				'shipment',
				'cutout',
				'deliver',
				'clipper',
				'basic',
				'deal',
				'custom-design',
			);
			return $valid_designs;
		}

		/**
		 * Get coupon design thumbnail src
		 *
		 * @param array $args The arguments.
		 * @return string
		 */
		public function get_coupon_design_thumbnail_src( $args = array() ) {
			$coupon       = ( ! empty( $args['coupon_object'] ) ) ? $args['coupon_object'] : null;
			$src          = '';
			$src_selected = '';
			$placeholder  = wc_placeholder_img_src();
			if ( is_object( $coupon ) ) {
				$coupon_product_ids = ( is_callable( array( $coupon, 'get_product_ids' ) ) ) ? $coupon->get_product_ids() : array();
				if ( ! empty( $coupon_product_ids ) ) {
					$product_id   = current( $coupon_product_ids );
					$product      = wc_get_product( $product_id );
					$thumbnail_id = ( is_object( $product ) && is_callable( array( $product, 'get_image_id' ) ) ) ? $product->get_image_id() : '';
				} else {
					$coupon_product_category_ids = ( is_callable( array( $coupon, 'get_product_categories' ) ) ) ? $coupon->get_product_categories() : array();
					if ( ! empty( $coupon_product_category_ids ) ) {
						$category_id  = current( $coupon_product_category_ids );
						$thumbnail_id = get_term_meta( $category_id, 'thumbnail_id', true );
					}
				}
				$src_array = ( ! empty( $thumbnail_id ) ) ? wp_get_attachment_image_src( $thumbnail_id, 'woocommerce_thumbnail' ) : array();
				$src       = ( ! empty( $src_array[0] ) ) ? $src_array[0] : wc_placeholder_img_src();
				if ( ! empty( $src ) && strpos( $src, $placeholder ) !== false ) {
					$src = '';
				}
			}
			if ( empty( $src ) ) {
				$src_set = array();
				if ( $this->is_wc_gte_30() ) {
					$discount_type    = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
					$is_free_shipping = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_free_shipping' ) ) ) ? ( ( $coupon->get_free_shipping() ) ? 'yes' : 'no' ) : '';
				} else {
					$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
				}

				if ( 'yes' === $is_free_shipping ) {
					$src_set = array(
						'delivery-motorcyle.svg',
					);
				} else {
					switch ( $discount_type ) {
						case 'smart_coupon':
							$src_set = array(
								'giftbox-color.svg',
							);
							break;

						case 'fixed_cart':
							$src_set = array(
								'sale-splash-tag.svg',
							);
							break;

						case 'fixed_product':
							$src_set = array(
								'product-package-box.svg',
							);
							break;

						case 'percent_product':
							$src_set = array(
								'cart-discount.svg',
							);
							break;

						case 'percent':
							$src_set = array(
								'cart-discount.svg',
							);
							break;

						default:
							$src_set = apply_filters(
								'wc_sc_coupon_design_thumbnail_src_set',
								array( 'discount-coupon.svg' ),
								array(
									'source'        => $this,
									'coupon_object' => $coupon,
								)
							);
							break;

					}
				}
				if ( ! empty( $src_set ) ) {
					$src_index    = array_rand( $src_set );
					$src_selected = $src_set[ $src_index ];
					$file         = trailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . 'assets/images/' . $src_selected;
					$src          = apply_filters(
						'wc_sc_coupon_design_thumbnail_src',
						$file,
						array(
							'source'        => $this,
							'selected'      => $src_selected,
							'coupon_object' => $coupon,
						)
					);
				}
			}
			if ( empty( $src ) ) {
				$design = ( ! empty( $args['design'] ) ) ? $args['design'] : '';
				if ( ! empty( $design ) ) {
					switch ( $design ) {
						case 'special':
							$src_selected = 'giftbox-color.svg';
							break;
						case 'shipment':
							$src_selected = 'product-package-box.svg';
							break;
						case 'cutout':
							$src_selected = 'cart-discount.svg';
							break;
						case 'deliver':
							$src_selected = 'delivery-motorcyle.svg';
							break;
						case 'deal':
							$src_selected = 'discount-coupon.svg';
							break;
						case 'flat':
						case 'promotion':
						case 'ticket':
						case 'festive':
						case 'clipper':
						case 'basic':
						default:
							$src_selected = 'discount-coupon.svg';
							break;
					}
					if ( ! empty( $src_selected ) ) {
						$file = trailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . 'assets/images/' . $src_selected;
						$src  = apply_filters(
							'wc_sc_coupon_design_thumbnail_src',
							$file,
							array(
								'source'        => $this,
								'selected'      => $src_selected,
								'coupon_object' => $coupon,
							)
						);
					} else {
						$src = '';
					}
				}
			}
			return $src;
		}

		/**
		 * Find if the discount type is percent
		 *
		 * @param array $args The arguments.
		 * @return boolean
		 */
		public function is_percent_coupon( $args = array() ) {
			$is_percent             = false;
			$coupon                 = ( ! empty( $args['coupon_object'] ) ) ? $args['coupon_object'] : null;
			$percent_discount_types = apply_filters(
				'wc_sc_percent_discount_types',
				array( 'percent_product', 'percent' ),
				array(
					'source'        => $this,
					'coupon_object' => $coupon,
				)
			);
			if ( $this->is_wc_gte_30() ) {
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}
			if ( in_array( $discount_type, $percent_discount_types, true ) ) {
				$is_percent = true;
			}
			return $is_percent;
		}

		/**
		 * Generate storewide offer coupon description
		 *
		 * @param array $args Arguments.
		 * @return string
		 */
		public function generate_storewide_offer_coupon_description( $args = array() ) {
			$coupon        = ( ! empty( $args['coupon_object'] ) ) ? $args['coupon_object'] : false;
			$coupon_amount = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount( 'edit' ) : 0;
			$coupon_code   = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';

			if ( empty( $coupon_amount ) || empty( $coupon_code ) ) {
				return '';
			}

			$description = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_description' ) ) ) ? $coupon->get_description() : '';

			if ( empty( $description ) ) {

				$is_percent      = $this->is_percent_coupon( array( 'coupon_object' => $coupon ) );
				$currency_symbol = get_woocommerce_currency_symbol();

				$before_heading = array(
					__( 'Great News!', 'woocommerce-smart-coupons' ),
					__( 'Super Savings!', 'woocommerce-smart-coupons' ),
					__( 'Ending Soon!', 'woocommerce-smart-coupons' ),
					__( 'Limited Time Offer!', 'woocommerce-smart-coupons' ),
					__( 'This Week Only!', 'woocommerce-smart-coupons' ),
					__( 'Attention!', 'woocommerce-smart-coupons' ),
					__( 'You don\'t want to miss this...', 'woocommerce-smart-coupons' ),
					__( 'This will be over soon! Hurry.', 'woocommerce-smart-coupons' ),
					__( 'Act before the offer expires.', 'woocommerce-smart-coupons' ),
					__( 'Don&#39;t Miss Out.', 'woocommerce-smart-coupons' ),
				);

				$heading = array(
					/* translators: 1. The discount text */
					__( '%s discount on anything you want.', 'woocommerce-smart-coupons' ),
					/* translators: 1. The discount text */
					__( '%s discount on entire store.', 'woocommerce-smart-coupons' ),
					/* translators: 1. The discount text */
					__( 'Pick any item today for %s off.', 'woocommerce-smart-coupons' ),
					/* translators: 1. The discount text */
					__( 'Buy as much as you want. Flat %s off everything.', 'woocommerce-smart-coupons' ),
					/* translators: 1. The discount text */
					__( 'Flat %s discount on everything today.', 'woocommerce-smart-coupons' ),
				);

				$before_heading_index = array_rand( $before_heading );
				$heading_index        = array_rand( $heading );

				if ( true === $is_percent ) {
					$discount_text = $coupon_amount . '%';
				} else {
					$discount_text = $currency_symbol . $coupon_amount;
				}

				$description = sprintf( '%s ' . $heading[ $heading_index ] . ' %s: %s', $before_heading[ $before_heading_index ], '<strong style="font-size: 1.1rem;">' . $discount_text . '</strong>', __( 'Use code', 'woocommerce-smart-coupons' ), '<code>' . $coupon_code . '</code>' );

				$description = apply_filters(
					'wc_sc_storewide_offer_coupon_description',
					$description,
					array_merge(
						$args,
						array(
							'before_heading' => $before_heading[ $before_heading_index ],
							'heading'        => $heading,
							'discount_text'  => $discount_text,
						)
					)
				);

			} else {
				/* translators: 1. The coupon code */
				$description .= ' ' . sprintf( __( 'Use code: %s', 'woocommerce-smart-coupons' ), '<code>' . $coupon_code . '</code>' );
			}

			return $description;

		}

		/**
		 * Update coupon's email id with the updation of customer profile
		 *
		 * @param int $user_id User ID of the user being saved.
		 */
		public function my_profile_update( $user_id ) {

			global $wpdb;

			if ( current_user_can( 'edit_user', $user_id ) ) {

				$current_user = get_userdata( $user_id );

				$old_customers_email_id = $current_user->data->user_email;

				$post_email = ( isset( $_POST['email'] ) ) ? wc_clean( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore

				if ( ! empty( $post_email ) && $post_email !== $old_customers_email_id ) {

					$result = wp_cache_get( 'wc_sc_customers_coupon_ids_' . sanitize_key( $old_customers_email_id ), 'woocommerce_smart_coupons' );

					if ( false === $result ) {
						$result = $wpdb->get_col( // phpcs:ignore
							$wpdb->prepare(
								"SELECT post_id
									FROM $wpdb->postmeta
									WHERE meta_key = %s
									AND meta_value LIKE %s
									AND post_id IN ( SELECT ID
														FROM $wpdb->posts
														WHERE post_type = %s)",
								'customer_email',
								'%' . $wpdb->esc_like( '"' . $old_customers_email_id . '"' ) . '%',
								'shop_coupon'
							)
						);
						wp_cache_set( 'wc_sc_customers_coupon_ids_' . sanitize_key( $old_customers_email_id ), $result, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_customers_coupon_ids_' . sanitize_key( $old_customers_email_id ) );
					}

					if ( ! empty( $result ) ) {

						foreach ( $result as $post_id ) {

							$coupon_meta           = $this->get_post_meta( $post_id, 'customer_email', true );
							$is_update_coupon_meta = false;

							if ( ! empty( $coupon_meta ) ) {
								foreach ( $coupon_meta as $key => $email_id ) {
									if ( $email_id === $old_customers_email_id ) {
										$coupon_meta[ $key ]   = $post_email;
										$is_update_coupon_meta = true;
									}
								}
							}

							if ( true === $is_update_coupon_meta ) {
								$this->update_post_meta( $post_id, 'customer_email', $coupon_meta );
							}
						} //end foreach
					}
				}
			}
		}

		/**
		 * Method to check whether 'pick_price_from_product' is set or not
		 *
		 * @param array $coupons Array of coupon codes.
		 * @return boolean
		 */
		public function is_coupon_amount_pick_from_product_price( $coupons ) {

			if ( empty( $coupons ) ) {
				return false;
			}

			if ( ! is_array( $coupons ) && is_scalar( $coupons ) ) {
				$coupons = array( $coupons );
			}

			foreach ( $coupons as $coupon_code ) {
				$coupon = new WC_Coupon( $coupon_code );
				if ( $this->is_wc_gte_30() ) {
					if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
						continue;
					}
					$coupon_id = $coupon->get_id();
					if ( empty( $coupon_id ) ) {
						continue;
					}
					$discount_type            = $coupon->get_discount_type();
					$is_pick_price_of_product = ( $this->is_callable( $coupon, 'get_meta' ) ) ? $coupon->get_meta( 'is_pick_price_of_product' ) : $this->get_post_meta( $coupon_id, 'is_pick_price_of_product', true );
				} else {
					$coupon_id                = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$discount_type            = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$is_pick_price_of_product = get_post_meta( $coupon_id, 'is_pick_price_of_product', true );
				}
				if ( 'smart_coupon' === $discount_type && 'yes' === $is_pick_price_of_product ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Function to find if order is discounted with store credit
		 *
		 * @param  WC_Order $order Order object.
		 * @return boolean
		 */
		public function is_order_contains_store_credit( $order = null ) {

			if ( empty( $order ) ) {
				return false;
			}

			$coupons = $order->get_items( 'coupon' );

			foreach ( $coupons as $item_id => $item ) {
				$code   = ( is_object( $item ) && is_callable( array( $item, 'get_name' ) ) ) ? $item->get_name() : trim( $item['name'] );
				$coupon = new WC_Coupon( $code );
				if ( $this->is_wc_gte_30() ) {
					$discount_type = $coupon->get_discount_type();
				} else {
					$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				}
				if ( 'smart_coupon' === $discount_type ) {
					return true;
				}
			}

			return false;

		}

		/**
		 * Function to validate smart coupon for product
		 *
		 * @param bool            $valid Coupon validity.
		 * @param WC_Product|null $product Product object.
		 * @param WC_Coupon|null  $coupon Coupon object.
		 * @param array|null      $values Values.
		 * @return bool           $valid
		 */
		public function smart_coupons_is_valid_for_product( $valid, $product = null, $coupon = null, $values = null ) {

			if ( empty( $product ) || empty( $coupon ) ) {
				return $valid;
			}

			if ( $this->is_wc_gte_30() ) {
				$product_id                         = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
				$product_parent_id                  = ( is_object( $product ) && is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0;
				$product_variation_id               = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
				$discount_type                      = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
				$coupon_product_ids                 = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_product_ids' ) ) ) ? $coupon->get_product_ids() : '';
				$coupon_product_categories          = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_product_categories' ) ) ) ? $coupon->get_product_categories() : '';
				$coupon_excluded_product_ids        = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_excluded_product_ids' ) ) ) ? $coupon->get_excluded_product_ids() : '';
				$coupon_excluded_product_categories = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_excluded_product_categories' ) ) ) ? $coupon->get_excluded_product_categories() : '';
				$is_exclude_sale_items              = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_exclude_sale_items' ) ) ) ? ( ( $coupon->get_exclude_sale_items() ) ? 'yes' : 'no' ) : '';
			} else {
				$product_id                         = ( ! empty( $product->id ) ) ? $product->id : 0;
				$product_parent_id                  = ( ! empty( $product ) && is_callable( array( $product, 'get_parent' ) ) ) ? $product->get_parent() : 0;
				$product_variation_id               = ( ! empty( $product->variation_id ) ) ? $product->variation_id : 0;
				$discount_type                      = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				$coupon_product_ids                 = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : array();
				$coupon_product_categories          = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : array();
				$coupon_excluded_product_ids        = ( ! empty( $coupon->exclude_product_ids ) ) ? $coupon->exclude_product_ids : array();
				$coupon_excluded_product_categories = ( ! empty( $coupon->exclude_product_categories ) ) ? $coupon->exclude_product_categories : array();
				$is_exclude_sale_items              = ( ! empty( $coupon->exclude_sale_items ) ) ? $coupon->exclude_sale_items : '';
			}

			if ( 'smart_coupon' === $discount_type ) {

				$product_cats = wc_get_product_cat_ids( $product_id );

				// Specific products get the discount.
				if ( count( $coupon_product_ids ) > 0 ) {

					if ( in_array( $product_id, $coupon_product_ids, true ) || ( isset( $product_variation_id ) && in_array( $product_variation_id, $coupon_product_ids, true ) ) || in_array( $product_parent_id, $coupon_product_ids, true ) ) {
						$valid = true;
					}

					// Category discounts.
				} elseif ( count( $coupon_product_categories ) > 0 ) {

					if ( count( array_intersect( $product_cats, $coupon_product_categories ) ) > 0 ) {
						$valid = true;
					}
				} else {
					// No product ids - all items discounted.
					$valid = true;
				}

				// Specific product ID's excluded from the discount.
				if ( count( $coupon_excluded_product_ids ) > 0 ) {
					if ( in_array( $product_id, $coupon_excluded_product_ids, true ) || ( isset( $product_variation_id ) && in_array( $product_variation_id, $coupon_excluded_product_ids, true ) ) || in_array( $product_parent_id, $coupon_excluded_product_ids, true ) ) {
						$valid = false;
					}
				}

				// Specific categories excluded from the discount.
				if ( count( $coupon_excluded_product_categories ) > 0 ) {
					if ( count( array_intersect( $product_cats, $coupon_excluded_product_categories ) ) > 0 ) {
						$valid = false;
					}
				}

				// Sale Items excluded from discount.
				if ( 'yes' === $is_exclude_sale_items ) {
					$product_ids_on_sale = wc_get_product_ids_on_sale();

					if ( in_array( $product_id, $product_ids_on_sale, true ) || ( isset( $product_variation_id ) && in_array( $product_variation_id, $product_ids_on_sale, true ) ) || in_array( $product_parent_id, $product_ids_on_sale, true ) ) {
						$valid = false;
					}
				}
			}

			return $valid;
		}

		/**
		 * Validate expiry time
		 *
		 * @param boolean      $expired Whether the coupon is expired or not.
		 * @param WC_Coupon    $coupon The coupon object.
		 * @param WC_Discounts $discounts The discount object.
		 * @return boolean
		 */
		public function validate_expiry_time( $expired = false, $coupon = null, $discounts = null ) {

			if ( ! $this->is_wc_gte_30() ) {
				return $expired; // Expiry time feature is not supported for lower version of WooCommerce.
			}

			$expiry_time = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_meta' ) ) ) ? (int) $coupon->get_meta( 'wc_sc_expiry_time' ) : 0;

			if ( ! empty( $expiry_time ) ) {
				$expiry_date = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_date_expires' ) ) ) ? $coupon->get_date_expires() : '';
				if ( ! empty( $expiry_date ) ) {
					if ( $expiry_date instanceof WC_DateTime ) {
						$expiry_date = ( is_callable( array( $expiry_date, 'getTimestamp' ) ) ) ? $expiry_date->getTimestamp() : null;
					} elseif ( ! is_int( $expiry_date ) ) {
						$expiry_date = $this->strtotime( $expiry_date );
					}
					if ( is_int( $expiry_date ) ) {
						$expiry_date += $expiry_time; // Adding expiry time to expiry date.
						if ( time() <= $expiry_date ) {
							$expired = false;
						} else {
							$expired = true;
						}
					}
				}
			}

			return $expired;
		}

		/**
		 * Wrapper function for strtotime
		 *
		 * @param string $date_time The date time.
		 * @return int
		 */
		public function strtotime( $date_time = '' ) {
			$parsed_date = ( is_string( $date_time ) ) ? date_parse( $date_time ) : array( 'warning_count' => 1 );
			if ( ! empty( $parsed_date['warning_count'] ) ) {
				return 0;
			}
			return strtotime( $date_time );
		}

		/**
		 * Function to keep valid coupons when individual use coupon is applied
		 *
		 * @param  array             $coupons_to_keep Coupons to keep.
		 * @param  WC_Coupon|boolean $the_coupon Coupon object.
		 * @param  array             $applied_coupons Array of applied coupons.
		 * @return array              $coupons_to_keep
		 */
		public function smart_coupons_override_individual_use( $coupons_to_keep = array(), $the_coupon = false, $applied_coupons = array() ) {

			if ( $this->is_wc_gte_30() ) {
				foreach ( $applied_coupons as $code ) {
					$coupon = new WC_Coupon( $code );
					if ( 'smart_coupon' === $coupon->get_discount_type() && ! $coupon->get_individual_use() && ! in_array( $code, $coupons_to_keep, true ) ) {
						$coupons_to_keep[] = $code;
					}
				}
			}

			return $coupons_to_keep;
		}

		/**
		 * Force apply store credit even if the individual coupon already exists in cart
		 *
		 * @param  boolean           $is_apply Apply with individual use coupon.
		 * @param  WC_Coupon|boolean $the_coupon Coupon object.
		 * @param  WC_Coupon|boolean $applied_coupon Coupon object.
		 * @param  array             $applied_coupons Array of applied coupons.
		 * @return boolean
		 */
		public function smart_coupons_override_with_individual_use( $is_apply = false, $the_coupon = false, $applied_coupon = false, $applied_coupons = array() ) {

			if ( $this->is_wc_gte_30() ) {
				if ( ! $is_apply && 'smart_coupon' === $the_coupon->get_discount_type() && ! $the_coupon->get_individual_use() ) {
					$is_apply = true;
				}
			}

			return $is_apply;
		}

		/**
		 * Function to add appropriate discount total filter
		 */
		public function smart_coupons_discount_total_filters() {
			if ( WCS_SC_Compatibility::is_cart_contains_subscription() && WCS_SC_Compatibility::is_wcs_gte( '2.0.0' ) ) {
				add_action( 'woocommerce_after_calculate_totals', array( $this, 'smart_coupons_after_calculate_totals' ), 9999 );
			} else {
				add_action( 'woocommerce_after_calculate_totals', array( $this, 'smart_coupons_after_calculate_totals' ), 9999 );
				global $current_screen;
				if ( ! empty( $current_screen ) && 'edit-shop_order' !== $current_screen ) {
					add_filter( 'woocommerce_order_get_total', array( $this, 'smart_coupons_order_discounted_total' ), 10, 2 );
				}
			}
		}

		/**
		 * Function to handle store credit application
		 */
		public function sc_handle_store_credit_application() {
			$apply_before_tax = get_option( 'woocommerce_smart_coupon_apply_before_tax', 'no' );

			if ( $this->is_wc_gte_30() && 'yes' === $apply_before_tax ) {
				include_once 'class-wc-sc-apply-before-tax.php';
			} else {
				add_action( 'wp_loaded', array( $this, 'smart_coupons_discount_total_filters' ), 20 );
				add_action( 'woocommerce_order_after_calculate_totals', array( $this, 'order_calculate_discount_amount' ), 10, 2 );
				add_action( 'woocommerce_store_api_checkout_update_order_meta', array( $this, 'wc_blocks_order_calculate_discount_amount' ) );
				add_filter( 'woocommerce_cart_totals_coupon_html', array( $this, 'cart_totals_coupon_html' ), 99, 3 );
			}
		}

		/**
		 * Function to set store credit amount for orders that are manually created and updated from backend
		 *
		 * @param bool     $and_taxes Calc taxes if true.
		 * @param WC_Order $order Order object.
		 */
		public function order_calculate_discount_amount( $and_taxes, $order ) {
			if ( ! is_object( $order ) || ! $order instanceof WC_Order ) {
				return;
			}
			$order_actions = array( 'woocommerce_add_coupon_discount', 'woocommerce_calc_line_taxes', 'woocommerce_save_order_items' );

			$order_id = ( $this->is_callable( $order, 'get_id' ) ) ? $order->get_id() : 0;

			$post_action    = ( ! empty( $_POST['action'] ) ) ? wc_clean( wp_unslash( $_POST['action'] ) ) : ''; // phpcs:ignore
			$post_post_type = ( $this->is_hpos() ) ? $this->get_post_type( $order_id ) : ( ( ! empty( $_POST['post_type'] ) ) ? wc_clean( wp_unslash( $_POST['post_type'] ) ) : '' ); // phpcs:ignore

			$order_created_via = ( is_callable( array( $order, 'get_created_via' ) ) ) ? $order->get_created_via() : '';

			if ( ( ! empty( $post_action ) && ( in_array( $post_action, $order_actions, true ) || ( ! empty( $post_post_type ) && 'shop_order' === $post_post_type && 'editpost' === $post_action ) ) ) || 'rest-api' === $order_created_via ) {
				if ( ! is_callable( array( $order, 'get_id' ) ) ) {
					return;
				}

				if ( empty( $order_id ) ) {
					return;
				}
				$coupons = $order->get_items( 'coupon' );

				if ( ! empty( $coupons ) ) {
					foreach ( $coupons as $item_id => $item ) {
						$coupon_code = ( is_object( $item ) && is_callable( array( $item, 'get_name' ) ) ) ? $item->get_name() : trim( $item['name'] );

						if ( empty( $coupon_code ) ) {
							continue;
						}

						$coupon        = new WC_Coupon( $coupon_code );
						$discount_type = $coupon->get_discount_type();

						if ( 'smart_coupon' === $discount_type ) {
							$total                      = $order->get_total();
							$discount_amount            = ( is_object( $item ) && is_callable( array( $item, 'get_discount' ) ) ) ? $item->get_discount() : $this->get_order_item_meta( $item_id, 'discount_amount', true, true );
							$smart_coupons_contribution = $this->get_post_meta( $order_id, 'smart_coupons_contribution', true, true );
							$smart_coupons_contribution = ! empty( $smart_coupons_contribution ) ? $smart_coupons_contribution : array();

							if ( ! empty( $smart_coupons_contribution ) && count( $smart_coupons_contribution ) > 0 && array_key_exists( $coupon_code, $smart_coupons_contribution ) ) {
								$discount = $smart_coupons_contribution[ $coupon_code ];
							} elseif ( ! empty( $discount_amount ) ) {
								$discount = $discount_amount;
							} else {
								$discount = $this->sc_order_get_discount_amount( $total, $coupon, $order );
							}

							$discount = min( $total, $discount );
							if ( is_object( $item ) && is_callable( array( $item, 'set_discount' ) ) ) {
								$item->set_discount( $discount );
							} else {
								$item['discount_amount'] = $discount;
							}

							$order->set_total( $total - $discount );

							$smart_coupons_contribution[ $coupon_code ] = $discount;

							$this->update_post_meta( $order_id, 'smart_coupons_contribution', $smart_coupons_contribution, true, $order );

						}
					}
					$pending_statuses = $this->get_pending_statuses();
					if ( 'woocommerce_add_coupon_discount' === $post_action && $order->has_status( $pending_statuses ) && did_action( 'sc_after_order_calculate_discount_amount' ) <= 0 ) {
						do_action( 'sc_after_order_calculate_discount_amount', $order_id );
					}
				}
			}
		}

		/**
		 * Function to set order discount for store credit for WC Checkout Blocks
		 *
		 * @param WC_Order $order Order object.
		 */
		public function wc_blocks_order_calculate_discount_amount( $order = null ) {
			if ( ! is_object( $order ) || ! $order instanceof WC_Order || ! is_callable( array( $order, 'get_id' ) ) ) {
				return;
			}

			$order_id = $order->get_id();
			if ( empty( $order_id ) ) {
				return;
			}
			$post_post_type    = ( $this->is_hpos() ) ? $this->get_post_type( $order_id ) : ( ( ! empty( $_POST['post_type'] ) ) ? wc_clean( wp_unslash( $_POST['post_type'] ) ) : '' ); // phpcs:ignore
			$order_created_via = ( is_callable( array( $order, 'get_created_via' ) ) ) ? $order->get_created_via() : '';
			$order_status      = ( is_callable( array( $order, 'get_status' ) ) ) ? $order->get_status() : '';
			if ( 'shop_order' === $post_post_type && in_array( $order_status, array( 'checkout-draft' ), true ) && in_array( $order_created_via, array( 'store-api' ), true ) ) {

				$coupons = ( is_callable( array( $order, 'get_items' ) ) ) ? $order->get_items( 'coupon' ) : array();
				if ( is_array( $coupons ) && ! empty( $coupons ) ) {
					foreach ( $coupons as $item_id => $item ) {
						$coupon_code = ( is_object( $item ) && is_callable( array( $item, 'get_name' ) ) ) ? $item->get_name() : trim( $item['name'] );
						if ( empty( $coupon_code ) ) {
							continue;
						}

						$coupon        = new WC_Coupon( $coupon_code );
						$discount_type = $coupon->get_discount_type();
						if ( 'smart_coupon' === $discount_type ) {
							$total                      = (float) $order->get_total();
							$discount_total             = is_callable( array( $order, 'get_discount_total' ) ) ? (float) $order->get_discount_total() : 0;
							$smart_coupons_contribution = $this->get_post_meta( $order_id, 'smart_coupons_contribution', true, true );
							$smart_coupons_contribution = ! empty( $smart_coupons_contribution ) ? $smart_coupons_contribution : array();

							if ( ! empty( $smart_coupons_contribution ) && count( $smart_coupons_contribution ) > 0 && array_key_exists( $coupon_code, $smart_coupons_contribution ) ) {
								$discount = $smart_coupons_contribution[ $coupon_code ];
							} else {
								$discount = $this->sc_order_get_discount_amount( $total, $coupon, $order );
							}

							$discount = (float) min( $total, $discount );
							if ( is_callable( array( $order, 'set_total' ) ) ) {
								$order->set_total( $total - $discount );
							}
							if ( is_callable( array( $order, 'set_discount_total' ) ) ) {
								$order->set_discount_total( $discount + $discount_total );
							}
							$smart_coupons_contribution[ $coupon_code ] = $discount;

							$this->update_post_meta( $order_id, 'smart_coupons_contribution', $smart_coupons_contribution, true, $order );
						}
					}
				}
			}
		}

		/**
		 * Function to get discount amount for orders
		 *
		 * @param  float     $total Order total.
		 * @param  WC_Coupon $coupon Coupon object.
		 * @param  WC_Order  $order Order object.
		 * @return float     $discount
		 */
		public function sc_order_get_discount_amount( $total, $coupon, $order ) {
			$discount = 0;

			if ( is_a( $coupon, 'WC_Coupon' ) && is_a( $order, 'WC_Order' ) ) {
				$discount_type             = $coupon->get_discount_type();
				$coupon_code               = $coupon->get_code();
				$coupon_product_ids        = $coupon->get_product_ids();
				$coupon_product_categories = $coupon->get_product_categories();

				if ( 'smart_coupon' === $discount_type ) {

					$coupon_amount = $this->get_amount( $coupon, true, $order );

					$calculated_total = $total;

					if ( count( $coupon_product_ids ) > 0 || count( $coupon_product_categories ) > 0 ) {

						$discount            = 0;
						$line_totals         = 0;
						$line_taxes          = 0;
						$discounted_products = array();

						$order_items = $order->get_items( 'line_item' );

						foreach ( $order_items as $order_item_id => $order_item ) {
							if ( $discount >= $coupon_amount ) {
								break;
							}

							$product_id   = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_product_id' ) ) ) ? $order_item->get_product_id() : $order_item['product_id'];
							$variation_id = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_variation_id' ) ) ) ? $order_item->get_variation_id() : $order_item['variation_id'];
							$line_total   = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_total' ) ) ) ? $order_item->get_total() : $order_item['line_total'];
							$line_tax     = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_total_tax' ) ) ) ? $order_item->get_total_tax() : $order_item['line_tax'];

							$product_cats = wc_get_product_cat_ids( $product_id );

							if ( count( $coupon_product_categories ) > 0 ) {

								$continue = false;

								if ( ! empty( $order_item_id ) && ! empty( $discounted_products ) && is_array( $discounted_products ) && in_array( $order_item_id, $discounted_products, true ) ) {
									$continue = true;
								}

								if ( ! $continue && count( array_intersect( $product_cats, $coupon_product_categories ) ) > 0 ) {

									$discounted_products[] = ( ! empty( $order_item_id ) ) ? $order_item_id : '';

									$line_totals += $line_total;
									$line_taxes  += $line_tax;

								}
							}

							if ( count( $coupon_product_ids ) > 0 ) {

								$continue = false;

								if ( ! empty( $order_item_id ) && ! empty( $discounted_products ) && is_array( $discounted_products ) && in_array( $order_item_id, $discounted_products, true ) ) {
									$continue = true;
								}

								if ( ! $continue && in_array( $product_id, $coupon_product_ids, true ) || in_array( $variation_id, $coupon_product_ids, true ) ) {

									$discounted_products[] = ( ! empty( $order_item_id ) ) ? $order_item_id : '';

									$line_totals += $line_total;
									$line_taxes  += $line_tax;

								}
							}
						}

						$calculated_total = round( ( $line_totals + $line_taxes ), wc_get_price_decimals() );

					}
					$discount = min( $calculated_total, $coupon_amount );
				}
			}

			return $discount;
		}

		/**
		 * Function to apply Smart Coupons discount
		 *
		 * @param  float   $total Cart total.
		 * @param  WC_Cart $cart Cart object.
		 * @param  boolean $cart_contains_subscription Is cart contains subscription.
		 * @param  string  $calculation_type           The calculation type.
		 * @return float   $total
		 */
		public function smart_coupons_discounted_totals( $total = 0, $cart = null, $cart_contains_subscription = false, $calculation_type = '' ) {

			if ( empty( $total ) ) {
				return $total;
			}

			$applied_coupons = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_applied_coupons' ) ) ) ? WC()->cart->get_applied_coupons() : array();

			$smart_coupon_credit_used = ( is_object( WC()->cart ) && isset( WC()->cart->smart_coupon_credit_used ) ) ? WC()->cart->smart_coupon_credit_used : array();

			if ( ! empty( $applied_coupons ) ) {
				$request_wc_ajax    = ( ! empty( $_REQUEST['wc-ajax'] ) ) ? wc_clean( wp_unslash( $_REQUEST['wc-ajax'] ) ) : ''; // phpcs:ignore
				$ignore_ajax_action = array( 'update_order_review', 'checkout' );
				foreach ( $applied_coupons as $code ) {
					if ( ! empty( $request_wc_ajax ) && in_array( $request_wc_ajax, $ignore_ajax_action, true ) && array_key_exists( $code, $smart_coupon_credit_used ) && true !== $cart_contains_subscription && ! isset( WC()->session->reload_checkout ) ) {
						continue;
					}
					$coupon   = new WC_Coupon( $code );
					$discount = $this->sc_cart_get_discount_amount( $total, $coupon );

					if ( ! empty( $discount ) ) {
						$discount = min( $total, $discount );
						$total    = $total - $discount;
						$this->manage_smart_coupon_credit_used( $coupon, $discount, $cart_contains_subscription, $calculation_type );
					}
				}
			}

			return $total;
		}

		/**
		 * Function to apply Smart Coupons discount after calculating tax
		 *
		 * @param  WC_Cart $cart Cart object.
		 */
		public function smart_coupons_after_calculate_totals( $cart = null ) {

			if ( empty( $cart ) || ! ( $cart instanceof WC_Cart ) ) {
				return;
			}

			// Check if AvaTax is active by checking for its main function.
			if ( function_exists( 'wc_avatax' ) ) {
				$wc_avatax = wc_avatax();
				if ( is_callable( array( $wc_avatax, 'get_tax_handler' ) ) ) {
					$ava_tax_handler = $wc_avatax->get_tax_handler();
					// Check if AvaTax is doing tax calculation.
					if ( is_callable( array( $ava_tax_handler, 'is_available' ) ) && true === $ava_tax_handler->is_available() ) {
						// Stop discount calculation till taxes from AvaTax have been calculated.
						if ( is_checkout() && ! did_action( 'wc_avatax_after_checkout_tax_calculated' ) ) {
							return;
						}
					}
				}
			}

			$cart_total = ( $this->is_wc_greater_than( '3.1.2' ) ) ? $cart->get_total( 'edit' ) : $cart->total;

			if ( ! empty( $cart_total ) ) {

				$stop_at = did_action( 'woocommerce_cart_reset' );

				$stop_at = apply_filters( 'wc_sc_calculate_totals_stop_at', $stop_at, $cart );

				if ( empty( $stop_at ) ) {
					$stop_at = 1;
				}

				$cart_contains_subscription = WCS_SC_Compatibility::is_cart_contains_subscription();
				$calculation_type           = '';

				if ( $cart_contains_subscription ) {
					$stop_at++;
					$calculation_type = WC_Subscriptions_Cart::get_calculation_type();
				}

				if ( did_action( 'smart_coupons_after_calculate_totals' ) > $stop_at ) {
					return;
				}

				if ( 'recurring_total' === $calculation_type ) {
					$total = $cart_total;
				} else {
					$total = $this->smart_coupons_discounted_totals( $cart_total, $cart, $cart_contains_subscription, $calculation_type );
				}

				if ( $this->is_wc_greater_than( '3.1.2' ) ) {
					$cart_contents_total        = $this->is_callable( $cart, 'get_cart_contents_total' ) ? $cart->get_cart_contents_total() : 0;
					$cart_contents_tax          = $this->is_callable( $cart, 'get_cart_contents_tax' ) ? $cart->get_cart_contents_tax() : 0;
					$cart_contents_taxes        = $this->is_callable( $cart, 'get_cart_contents_taxes' ) ? $cart->get_cart_contents_taxes() : array();
					$coupon_discount_totals     = $this->is_callable( $cart, 'get_coupon_discount_totals' ) ? $cart->get_coupon_discount_totals() : array();
					$coupon_discount_tax_totals = $this->is_callable( $cart, 'get_coupon_discount_tax_totals' ) ? $cart->get_coupon_discount_tax_totals() : array();
					$discount_total             = $this->is_callable( $cart, 'get_discount_total' ) ? $cart->get_discount_total() : 0;
					$discount_tax               = $this->is_callable( $cart, 'get_discount_tax' ) ? $cart->get_discount_tax() : 0;
					$shipping_total             = $this->is_callable( $cart, 'get_shipping_total' ) ? $cart->get_shipping_total() : 0;
					$shipping_tax               = $this->is_callable( $cart, 'get_shipping_tax' ) ? $cart->get_shipping_tax() : 0;
					$shipping_taxes             = $this->is_callable( $cart, 'get_shipping_taxes' ) ? $cart->get_shipping_taxes() : array();
					$coupon_discount            = 0;
					$coupon_discount_total      = $discount_total;
					$coupon_discount_tax        = $discount_tax;
					$coupon_discount_taxes      = $coupon_discount_tax_totals;
					$new_coupon_discount_tax    = 0;
					$shipping_discount          = 0;
					$shipping_discount_total    = 0;
					$shipping_discount_tax      = 0;
					$shipping_discount_taxes    = array();
					$credit_discount_tax        = 0;
					$credit_discount_taxes      = array();

					$smart_coupon_credit_used = ( ! empty( $cart->smart_coupon_credit_used ) ) ? $cart->smart_coupon_credit_used : array();

					if ( is_array( $smart_coupon_credit_used ) && ! empty( $smart_coupon_credit_used ) ) {
						$total = $this->is_callable( $cart, 'get_total' ) ? $cart->get_total( 'edit' ) : $total;
						foreach ( $smart_coupon_credit_used as $code => $coupon_credit ) {
							if ( $coupon_credit > 0 ) {
								$coupon_discount                 = min( $cart_contents_total, $coupon_credit );
								$coupon_discount_total          += $coupon_discount;
								$cart_contents_total            -= $coupon_discount;
								$coupon_discount_totals[ $code ] = $coupon_discount;
								$cart->set_cart_contents_total( $cart_contents_total );
								$cart->set_coupon_discount_totals( $coupon_discount_totals );
								$coupon_credit -= $coupon_discount;
								if ( $coupon_credit > 0 ) {
									if ( is_array( $cart_contents_taxes ) && ! empty( $cart_contents_taxes ) ) {
										if ( empty( $coupon_discount_taxes[ $code ] ) ) {
											$coupon_discount_taxes[ $code ] = 0;
										}
										if ( empty( $credit_discount_taxes[ $code ] ) ) {
											$credit_discount_taxes[ $code ] = 0;
										}
										foreach ( $cart_contents_taxes as $index => $tax ) {
											$new_coupon_discount_tax         = min( $tax, $coupon_credit );
											$coupon_credit                  -= $new_coupon_discount_tax;
											$coupon_discount_taxes[ $code ] += $new_coupon_discount_tax;
											$credit_discount_taxes[ $code ] += $new_coupon_discount_tax;
										}
										$credit_discount_tax                 = array_sum( $credit_discount_taxes );
										$coupon_discount_tax                 = array_sum( $coupon_discount_taxes ) - $credit_discount_tax;
										$coupon_discount_tax_totals[ $code ] = $coupon_discount_taxes[ $code ];
										$cart->set_coupon_discount_tax_totals( $coupon_discount_tax_totals );
									}
								}
								if ( $coupon_credit > 0 ) {
									$shipping_discount                = min( $shipping_total, $coupon_credit );
									$shipping_discount_total         += $shipping_discount;
									$coupon_discount_totals[ $code ] += $shipping_discount;
									$cart->set_coupon_discount_totals( $coupon_discount_totals );
									$coupon_credit -= $shipping_discount;
								}
								if ( $coupon_credit > 0 ) {
									if ( is_array( $shipping_taxes ) && ! empty( $shipping_taxes ) ) {
										foreach ( $shipping_taxes as $index => $s_tax ) {
											$shipping_discount_taxes[ $index ] = min( $s_tax, $coupon_credit );
											$coupon_credit                    -= $shipping_discount_taxes[ $index ];
										}
										$shipping_discount_tax                = array_sum( $shipping_discount_taxes );
										$coupon_discount_tax_totals[ $code ] += $shipping_discount_tax;
										$cart->set_coupon_discount_tax_totals( $coupon_discount_tax_totals );
									}
								}
							}
						}
						$discount_total = $coupon_discount_total + $credit_discount_tax + $shipping_discount_total + $shipping_discount_tax;
						$cart->set_discount_total( $discount_total );
						$discount_tax = $coupon_discount_tax;
						$cart->set_discount_tax( $discount_tax );
						$total_tax = $cart_contents_tax + $shipping_tax;
						$cart->set_total_tax( $total_tax );
						$total = $cart_contents_total + array_sum( $cart_contents_taxes ) + $shipping_total + array_sum( $shipping_taxes ) - array_sum( $credit_discount_taxes ) - $shipping_discount_total - $shipping_discount_tax;
					}
					$cart->set_total( $total );
					$cart->set_session();
				} else {
					$cart->total = $total;
				}

				do_action( 'smart_coupons_after_calculate_totals' );

			}

		}

		/**
		 * Function to show the discount amount applied by the store credit on the tax.
		 *
		 * @param string    $coupon_html The current HTML.
		 * @param WC_Coupon $coupon The coupon object.
		 * @param string    $discount_amount_html The discount amount HTML.
		 * @return string
		 */
		public function cart_totals_coupon_html( $coupon_html = '', $coupon = null, $discount_amount_html = '' ) {
			$cart = ( function_exists( 'WC' ) && isset( WC()->cart ) ) ? WC()->cart : null;
			if ( is_a( $cart, 'WC_Cart' ) ) {
				$tax_price_display_mode   = $cart->get_tax_price_display_mode();
				$smart_coupon_credit_used = ( isset( $cart->smart_coupon_credit_used ) ) ? $cart->smart_coupon_credit_used : array();
				$coupon_code              = ( $this->is_callable( $coupon, 'get_code' ) ) ? $coupon->get_code() : '';
				if ( ! empty( $coupon_code ) && array_key_exists( $coupon_code, $smart_coupon_credit_used ) ) {
					$coupon_discount_tax_totals = $this->is_callable( $cart, 'get_coupon_discount_tax_totals' ) ? $cart->get_coupon_discount_tax_totals() : array();
					if ( ! empty( $coupon_discount_tax_totals[ $coupon_code ] ) ) {
						if ( 'excl' === $tax_price_display_mode ) {
							/* translators: Discount amount applied on tax */
							$coupon_html = $coupon_html . ' <small>(' . sprintf( __( 'excludes -%s on tax', 'woocommerce-smart-coupons' ), wc_price( $coupon_discount_tax_totals[ $coupon_code ] ) ) . ')</small>';
						} else {
							/* translators: Discount amount applied on tax */
							$coupon_html = $coupon_html . ' <small>(' . sprintf( __( 'includes -%s on tax', 'woocommerce-smart-coupons' ), wc_price( $coupon_discount_tax_totals[ $coupon_code ] ) ) . ')</small>';
						}
					}
				}
			}
			return $coupon_html;
		}

		/**
		 * Function to do action 'smart_coupons_after_calculate_totals' since WooCommerce Services plugin triggers 'woocommerce_cart_reset' in its function for 'woocommerce_after_calculate_totals' action causing miscalculation in did_action( 'smart_coupons_after_calculate_totals' ) hook.
		 */
		public function woocommerce_cart_reset() {

			$cart_reset_action_count         = did_action( 'woocommerce_cart_reset' );
			$sc_after_calculate_action_count = did_action( 'smart_coupons_after_calculate_totals' );

			// This is to match counter for 'smart_coupons_after_calculate_totals' hook with 'woocommerce_cart_reset' counter since we are using these two counters to prevent store credit being applied multiple times.
			if ( $sc_after_calculate_action_count < $cart_reset_action_count ) {
				do_action( 'smart_coupons_after_calculate_totals' );
			}

		}

		/**
		 * Function to calculate total cart contents.
		 * skip the product if the product is a bundled product.
		 *
		 * @since 4.32.0
		 * @return int
		 */
		public function get_cart_contents_count() {
			$total_product = 0;
			$cart_data     = ( isset( WC()->cart ) && is_callable( array( WC()->cart, 'get_cart' ) ) ) ? WC()->cart->get_cart() : array();
			if ( ! empty( $cart_data ) ) {
				foreach ( $cart_data as $data ) {
					// Skip bundled products.
					if ( isset( $data['stamp'] ) && isset( $data['bundled_by'] ) ) {
						continue;
					}
					$total_product++;
				}
			}
			return $total_product;
		}

		/**
		 * Get coupon discount amount for percentage type coupon.
		 *
		 * @param  float      $discount Amount this coupon has discounted.
		 * @param  float      $discounting_amount Amount the coupon is being applied to.
		 * @param  array|null $cart_item Cart item being discounted if applicable.
		 * @param  bool       $single True if discounting a single qty item, false if its the line.
		 * @param  WC_Coupon  $coupon Coupon object.
		 * @return float      $discount
		 */
		public function get_coupon_discount_amount( $discount = 0, $discounting_amount = 0, $cart_item = array(), $single = false, $coupon = object ) {

			if ( is_a( $coupon, 'WC_Coupon' ) ) {

				$order = ( is_a( $cart_item, 'WC_Order_Item' ) && is_callable( array( $cart_item, 'get_order' ) ) ) ? $cart_item->get_order() : null;

				if ( $this->is_valid( $coupon, $order ) && is_object( $coupon ) && is_callable( array( $coupon, 'is_type' ) ) && $coupon->is_type( 'percent' ) ) {
					if ( $this->is_wc_gte_30() ) {
						$coupon_id = ( is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
					} else {
						$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					}

					if ( empty( $coupon_id ) ) {
						return $discount;
					}

					$max_discount = $this->get_post_meta( $coupon_id, 'wc_sc_max_discount', true, true, $order );

					if ( ! empty( $max_discount ) && is_numeric( $max_discount ) ) {

						$coupon_product_ids           = ( is_callable( array( $coupon, 'get_product_ids' ) ) ) ? $coupon->get_product_ids() : array();
						$coupon_excluded_product_ids  = ( is_callable( array( $coupon, 'get_excluded_product_ids' ) ) ) ? $coupon->get_excluded_product_ids() : array();
						$coupon_category_ids          = ( is_callable( array( $coupon, 'get_product_categories' ) ) ) ? $coupon->get_product_categories() : array();
						$coupon_excluded_category_ids = ( is_callable( array( $coupon, 'get_excluded_product_categories' ) ) ) ? $coupon->get_excluded_product_categories() : array();
						$cart_items_subtotal          = 0;
						$cart_contents_count          = 0;
						$max_discount_name            = 'wc_sc_max_discount_data';
						$inc_tax                      = wc_prices_include_tax();
						$is_restricted                = count( $coupon_product_ids ) > 0 || count( $coupon_excluded_product_ids ) > 0 || count( $coupon_category_ids ) > 0 || count( $coupon_excluded_category_ids ) > 0;
						$is_restricted                = apply_filters(
							'wc_sc_is_coupon_restriction_available',
							$is_restricted,
							array(
								'source'             => $this,
								'discount'           => $discount,
								'discounting_amount' => $discounting_amount,
								'cart_item'          => $cart_item,
								'single'             => $single,
								'coupon_object'      => $coupon,
							)
						);

						if ( is_a( $cart_item, 'WC_Order_Item_Product' ) ) {
							$order = $wc_cart_or_order_object = ( is_callable( array( $cart_item, 'get_order' ) ) ) ? $cart_item->get_order() : null;  // phpcs:ignore
							$wc_order_id = ( is_callable( array( $cart_item, 'get_order_id' ) ) ) ? $cart_item->get_order_id() : 0;
							if ( empty( $wc_order_id ) ) {
								return $discount;
							}
							$order_line_items = array();
							if ( is_a( $order, 'WC_Order' ) ) {
								$cart_items_subtotal = ( is_callable( array( $order, 'get_subtotal' ) ) ) ? $order->get_subtotal() : 0;
								$order_line_items    = ( is_callable( array( $order, 'get_items' ) ) ) ? $order->get_items() : array();
							}

							$max_discount_name = 'wc_sc_max_discount_data_for_' . $wc_order_id;
							if ( ! empty( $order_line_items ) ) {
								foreach ( $order_line_items as $order_line_item ) {
									$cart_contents_count++;
								}
							}

							$max_discount_data = function_exists( 'get_transient' ) ? get_transient( $max_discount_name ) : array();
						} else {
							$wc_cart      = $wc_cart_or_order_object = ! empty( WC()->cart ) ? WC()->cart : null; // phpcs:ignore
							$wc_session = ! empty( WC()->session ) ? WC()->session : null;
							if ( ! is_a( $wc_cart, 'WC_Cart' ) ) {
								return $discount;
							}
							if ( true === $inc_tax ) {
								$cart_items_subtotal = ! empty( $wc_cart->subtotal ) ? $wc_cart->subtotal : 0;
							} else {
								$cart_items_subtotal = ! empty( $wc_cart->subtotal_ex_tax ) ? $wc_cart->subtotal_ex_tax : 0;
							}

							$max_discount_data = ( ! empty( $wc_session ) && is_a( $wc_session, 'WC_Session' ) && is_callable( array( $wc_session, 'get' ) ) ) ? $wc_session->get( $max_discount_name ) : array();

							$cart_contents_count = $this->get_cart_contents_count();
						}

						$is_update_valid_item_count = false;

						if ( empty( $max_discount_data ) || ! is_array( $max_discount_data ) ) {
							$max_discount_data = array();
						}

						if ( empty( $max_discount_data[ $coupon_id ] ) || ! is_array( $max_discount_data[ $coupon_id ] ) ) {
							$max_discount_data[ $coupon_id ]           = array();
							$is_update_valid_item_count                = true;
							$max_discount_data[ $coupon_id ]['amount'] = $max_discount;
							$max_discount_data[ $coupon_id ]['count']  = $cart_contents_count;
						}

						if ( true === $is_restricted && class_exists( 'WC_Discounts' ) && ! empty( $wc_cart_or_order_object ) ) {

							$wc_discounts      = new WC_Discounts( $wc_cart_or_order_object );
							$items_to_validate = array();

							if ( is_callable( array( $wc_discounts, 'get_items_to_validate' ) ) ) {
								$items_to_validate = $wc_discounts->get_items_to_validate();
							} elseif ( is_callable( array( $wc_discounts, 'get_items' ) ) ) {
								$items_to_validate = $wc_discounts->get_items();
							} elseif ( ! empty( $wc_discounts->items ) && is_array( $wc_discounts->items ) ) {
								$items_to_validate = $wc_discounts->items;
							}

							if ( is_array( $items_to_validate ) ) {
								$valid_product_count = 0;
								foreach ( $items_to_validate as $item ) {
									$item_to_apply          = clone $item; // Clone the item so changes to wc_discounts item do not affect the originals.
									$valid_product_quantity = ( ! empty( $item_to_apply->quantity ) ) ? intval( $item_to_apply->quantity ) : 0;
									$product                = ( ! empty( $item_to_apply->product ) ) ? $item_to_apply->product : null;
									$item_to_apply_object   = ( ! empty( $item_to_apply->object ) ) ? $item_to_apply->object : null;

									if ( 0 === $wc_discounts->get_discounted_price_in_cents( $item_to_apply ) || 0 >= $valid_product_quantity ) {
										continue;
									}

									if ( ! $coupon->is_valid_for_product( $product, $item_to_apply_object ) && ! $coupon->is_valid_for_cart() ) {
										continue;
									}

									// Increment if the product is not a bundled product.
									$valid_product_count = ( isset( $item_to_apply_object['stamp'] ) && isset( $item_to_apply_object['bundled_by'] ) ) ? $valid_product_count : $valid_product_count + 1;
									$line_subtotal       = ! empty( $item_to_apply_object['line_subtotal'] ) ? intval( $item_to_apply_object['line_subtotal'] ) : 0;
									if ( true === $inc_tax ) {
										$line_subtotal_tax    = ! empty( $item_to_apply_object['line_subtotal_tax'] ) ? intval( $item_to_apply_object['line_subtotal_tax'] ) : 0;
										$cart_items_subtotal += $line_subtotal + $line_subtotal_tax;
									} else {
										$cart_items_subtotal += $line_subtotal;
									}
								}

								if ( true === $is_update_valid_item_count ) {
									$max_discount_data[ $coupon_id ]['count'] = $valid_product_count;
								}
							}
						}

						if ( 0 !== $cart_items_subtotal ) {

							$max_discount = ! empty( $max_discount_data[ $coupon_id ]['amount'] ) ? $max_discount_data[ $coupon_id ]['amount'] : 0;
							if ( $max_discount < 0 ) {
								$max_discount = 0;
							}

							$discount = min( $max_discount, $discount );

							if ( ! empty( $max_discount ) ) {
								$max_discount_data[ $coupon_id ]['amount'] -= $discount;
							}

							$max_discount_data[ $coupon_id ]['count']--;

							if ( 0 >= $max_discount_data[ $coupon_id ]['count'] ) {
								unset( $max_discount_data[ $coupon_id ] );
							}
							if ( 'wc_sc_max_discount_data' === $max_discount_name ) {
								if ( ! empty( $wc_session ) && is_a( $wc_session, 'WC_Session' ) && is_callable( array( $wc_session, 'set' ) ) ) {
									$wc_session->set( $max_discount_name, $max_discount_data );
								}
							} elseif ( function_exists( 'set_transient' ) ) {
								set_transient( $max_discount_name, $max_discount_data, DAY_IN_SECONDS );
							}
						}
					}
				}
			}
			return $discount;
		}

		/**
		 * Function to set default values to postmeta fields
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array
		 */
		public function postmeta_defaults( $defaults = array() ) {

			if ( $this->is_wc_gte_32() ) {
				$defaults['wc_sc_expiry_time'] = '';
			}

			return $defaults;
		}

		/**
		 * Function to get discount amount
		 *
		 * @param  float     $total The total.
		 * @param  WC_Coupon $coupon The coupon object.
		 * @return float     $discount
		 */
		public function sc_cart_get_discount_amount( $total = 0, $coupon = '' ) {

			$discount = 0;

			if ( is_a( $coupon, 'WC_Coupon' ) ) {

				if ( $this->is_valid( $coupon ) && $coupon->is_type( 'smart_coupon' ) ) {

					if ( $this->is_wc_gte_30() ) {
						$coupon_code               = $coupon->get_code();
						$coupon_product_ids        = $coupon->get_product_ids();
						$coupon_product_categories = $coupon->get_product_categories();
					} else {
						$coupon_code               = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
						$coupon_product_ids        = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : array();
						$coupon_product_categories = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : array();
					}

					$coupon_amount = $this->get_amount( $coupon, true );

					$calculated_total = $total;

					if ( count( $coupon_product_ids ) > 0 || count( $coupon_product_categories ) > 0 ) {

						$discount            = 0;
						$line_totals         = 0;
						$line_taxes          = 0;
						$discounted_products = array();

						foreach ( WC()->cart->cart_contents as $cart_item_key => $product ) {

							if ( $discount >= $coupon_amount ) {
								break;
							}

							$product_cats = wc_get_product_cat_ids( $product['product_id'] );

							if ( count( $coupon_product_categories ) > 0 ) {

								$continue = false;

								if ( ! empty( $cart_item_key ) && ! empty( $discounted_products ) && is_array( $discounted_products ) && in_array( $cart_item_key, $discounted_products, true ) ) {
									$continue = true;
								}

								if ( ! $continue && count( array_intersect( $product_cats, $coupon_product_categories ) ) > 0 ) {

									$discounted_products[] = ( ! empty( $cart_item_key ) ) ? $cart_item_key : '';

									$line_totals += $product['line_total'];
									$line_taxes  += $product['line_tax'];

								}
							}

							if ( count( $coupon_product_ids ) > 0 ) {

								$continue = false;

								if ( ! empty( $cart_item_key ) && ! empty( $discounted_products ) && is_array( $discounted_products ) && in_array( $cart_item_key, $discounted_products, true ) ) {
									$continue = true;
								}

								$parent_id = ( $this->is_wc_gte_30() ) ? $product['data']->get_parent_id() : $product['data']->get_parent();

								if ( ! $continue && in_array( $product['product_id'], $coupon_product_ids, true ) || in_array( $product['variation_id'], $coupon_product_ids, true ) || in_array( $parent_id, $coupon_product_ids, true ) ) {

									$discounted_products[] = ( ! empty( $cart_item_key ) ) ? $cart_item_key : '';

									$line_totals += $product['line_total'];
									$line_taxes  += $product['line_tax'];

								}
							}
						}

						$calculated_total = round( ( $line_totals + $line_taxes ), wc_get_price_decimals() );

					}

					$discount = min( $calculated_total, $coupon_amount );
				}
			}

			return $discount;
		}

		/**
		 * Function to manage store credit used
		 *
		 * @param WC_Coupon $coupon The coupon object.
		 * @param float     $discount The discount.
		 * @param bool      $cart_contains_subscription Is cart contains subscription.
		 * @param string    $calculation_type Calculation type.
		 */
		public function manage_smart_coupon_credit_used( $coupon = '', $discount = 0, $cart_contains_subscription = false, $calculation_type = '' ) {
			if ( is_object( $coupon ) && $coupon instanceof WC_Coupon ) {

				if ( $this->is_wc_gte_30() ) {
					$coupon_code = ( is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
				} else {
					$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}

				$coupon_amount = $this->get_amount( $coupon, true );

				if ( $cart_contains_subscription ) {
					if ( WCS_SC_Compatibility::is_wcs_gte( '2.0.10' ) ) {
						if ( $this->is_wc_greater_than( '3.1.2' ) ) {
							$coupon_discount_totals = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_coupon_discount_totals' ) ) ) ? WC()->cart->get_coupon_discount_totals() : array();
							if ( ! is_array( $coupon_discount_totals ) ) {
								$coupon_discount_totals = array();
							}
							if ( empty( $coupon_discount_totals[ $coupon_code ] ) ) {
								$coupon_discount_totals[ $coupon_code ] = $discount;
							} else {
								$coupon_discount_totals[ $coupon_code ] += $discount;
							}
							( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'set_coupon_discount_totals' ) ) ) ? WC()->cart->set_coupon_discount_totals( $coupon_discount_totals ) : '';
						} else {
							$coupon_discount_amounts = ( is_object( WC()->cart ) && isset( WC()->cart->coupon_discount_amounts ) ) ? WC()->cart->coupon_discount_amounts : array();
							if ( ! is_array( $coupon_discount_amounts ) ) {
								$coupon_discount_amounts = array();
							}
							if ( empty( $coupon_discount_amounts[ $coupon_code ] ) ) {
								$coupon_discount_amounts[ $coupon_code ] = $discount;
							} else {
								$coupon_discount_amounts[ $coupon_code ] += $discount;
							}
							WC()->cart->coupon_discount_amounts = $coupon_discount_amounts;
						}
					} elseif ( WCS_SC_Compatibility::is_wcs_gte( '2.0.0' ) ) {
						WC_Subscriptions_Coupon::increase_coupon_discount_amount( WC()->cart, $coupon_code, $discount );
					} else {
						WC_Subscriptions_Cart::increase_coupon_discount_amount( $coupon_code, $discount );
					}
				} else {
					if ( $this->is_wc_greater_than( '3.1.2' ) ) {
						$coupon_discount_totals = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_coupon_discount_totals' ) ) ) ? WC()->cart->get_coupon_discount_totals() : array();
						if ( ! is_array( $coupon_discount_totals ) ) {
							$coupon_discount_totals = array();
						}
						if ( empty( $coupon_discount_totals[ $coupon_code ] ) ) {
							$coupon_discount_totals[ $coupon_code ] = $discount;
						} else {
							$coupon_discount_totals[ $coupon_code ] += $discount;
						}
						( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'set_coupon_discount_totals' ) ) ) ? WC()->cart->set_coupon_discount_totals( $coupon_discount_totals ) : '';
					} else {
						$coupon_discount_amounts = ( is_object( WC()->cart ) && isset( WC()->cart->coupon_discount_amounts ) ) ? WC()->cart->coupon_discount_amounts : array();
						if ( ! is_array( $coupon_discount_amounts ) ) {
							$coupon_discount_amounts = array();
						}
						if ( empty( $coupon_discount_amounts[ $coupon_code ] ) ) {
							$coupon_discount_amounts[ $coupon_code ] = $discount;
						} else {
							$coupon_discount_amounts[ $coupon_code ] += $discount;
						}
						WC()->cart->coupon_discount_amounts = $coupon_discount_amounts;
					}
				}

				if ( isset( WC()->session->reload_checkout ) ) {        // reload_checkout is triggered when customer is registered from checkout.
					unset( WC()->cart->smart_coupon_credit_used );  // reset store credit used data for re-calculation.
				}

				$smart_coupon_credit_used = ( is_object( WC()->cart ) && isset( WC()->cart->smart_coupon_credit_used ) ) ? WC()->cart->smart_coupon_credit_used : array();

				if ( ! is_array( $smart_coupon_credit_used ) ) {
					$smart_coupon_credit_used = array();
				}
				if ( empty( $smart_coupon_credit_used[ $coupon_code ] ) || ( $cart_contains_subscription && ( 'combined_total' === $calculation_type || 'sign_up_fee_total' === $calculation_type ) ) ) {
					$smart_coupon_credit_used[ $coupon_code ] = $discount;
				} else {
					$smart_coupon_credit_used[ $coupon_code ] += $discount;
				}
				if ( floatval( $smart_coupon_credit_used[ $coupon_code ] ) > floatval( $coupon_amount ) ) {
					$smart_coupon_credit_used[ $coupon_code ] = $coupon_amount;
				}
				WC()->cart->smart_coupon_credit_used = $smart_coupon_credit_used;

			}
		}

		/**
		 * Apply store credit discount in order during recalculation
		 *
		 * @param  float    $total The total.
		 * @param  WC_Order $order The order object.
		 * @return float    $total
		 */
		public function smart_coupons_order_discounted_total( $total = 0, $order = null ) {

			if ( ! $this->is_wc_gte_30() ) {

				$is_proceed = check_ajax_referer( 'calc-totals', 'security', false );

				if ( ! $is_proceed ) {
					return $total;
				}

				$called_by = ( ! empty( $_POST['action'] ) ) ? wc_clean( wp_unslash( $_POST['action'] ) ) : ''; // phpcs:ignore

				if ( 'woocommerce_calc_line_taxes' !== $called_by ) {
					return $total;
				}
			}

			if ( empty( $order ) ) {
				return $total;
			}

			$coupons = ( is_object( $order ) && is_callable( array( $order, 'get_items' ) ) ) ? $order->get_items( 'coupon' ) : array();

			if ( ! empty( $coupons ) ) {
				foreach ( $coupons as $coupon ) {
					$code = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
					if ( empty( $code ) ) {
						continue;
					}
					$_coupon       = new WC_Coupon( $code );
					$discount_type = ( is_object( $_coupon ) && is_callable( array( $_coupon, 'get_discount_type' ) ) ) ? $_coupon->get_discount_type() : '';
					if ( ! empty( $discount_type ) && 'smart_coupon' === $discount_type ) {
						$discount         = $this->get_amount( $_coupon, true, $order );
						$applied_discount = min( $total, $discount );
						if ( $this->is_wc_gte_30() ) {
							$coupon->set_discount( $applied_discount );
							$coupon->save();
						}
						$total = $total - $applied_discount;
					}
				}
			}

			return $total;
		}

		/**
		 * Add tool for clearing cache
		 *
		 * @param  array $tools Existing tools.
		 * @return array $tools
		 */
		public function clear_cache_tool( $tools = array() ) {

			$tools['wc_sc_clear_cache'] = array(
				'name'     => __( 'WooCommerce Smart Coupons Cache', 'woocommerce-smart-coupons' ),
				'button'   => __( 'Clear Smart Coupons Cache', 'woocommerce-smart-coupons' ),
				'desc'     => __( 'This tool will clear the cache created by WooCommerce Smart Coupons.', 'woocommerce-smart-coupons' ),
				'callback' => array(
					$this,
					'clear_cache',
				),
			);

			return $tools;
		}

		/**
		 * Clear cache
		 *
		 * @return string $message
		 */
		public function clear_cache() {

			$message = ( is_callable( array( 'WC_SC_Act_Deact', 'clear_cache' ) ) ) ? WC_SC_Act_Deact::clear_cache() : '';

			return $message;
		}

		/**
		 * WooCommerce Checkout Update Order Review
		 *
		 * @param array $post_data The post data.
		 */
		public function woocommerce_checkout_update_order_review( $post_data = array() ) {

			wp_parse_str( $post_data, $posted_data );

			if ( ! empty( $posted_data['billing_email'] ) ) {
				$applied_coupons = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_applied_coupons' ) ) ) ? WC()->cart->get_applied_coupons() : array();
				if ( ! empty( $applied_coupons ) ) {
					if ( empty( $_REQUEST['billing_email'] ) ) { // phpcs:ignore
						$_REQUEST['billing_email'] = $posted_data['billing_email'];
					}
					foreach ( $applied_coupons as $coupon_code ) {
						$coupon   = new WC_Coupon( $coupon_code );
						$is_valid = $this->is_user_usage_limit_valid( true, $coupon );
						if ( true !== $is_valid ) {
							WC()->cart->remove_coupon( $coupon_code );
							/* translators: The coupon code */
							wc_add_notice( sprintf( __( 'Coupon %s is valid for a new user only, hence removed.', 'woocommerce-smart-coupons' ), '<code>' . $coupon_code . '</code>' ), 'error' );
						}
					}
				}
			}

		}

		/**
		 * Function to return validity of Store Credit / Gift Certificate
		 *
		 * @param boolean      $valid Coupon validity.
		 * @param WC_Coupon    $coupon Coupon object.
		 * @param WC_Discounts $discounts Discounts object.
		 * @return boolean  $valid TRUE if smart coupon valid, FALSE otherwise
		 */
		public function is_smart_coupon_valid( $valid, $coupon, $discounts = null ) {

			if ( $this->is_wc_gte_30() ) {
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
				$coupon_code   = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			} else {
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
			}

			$coupon_amount = $this->get_amount( $coupon, true );

			if ( 'smart_coupon' !== $discount_type ) {
				return $valid;
			}

			$applied_coupons = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_applied_coupons' ) ) ) ? WC()->cart->get_applied_coupons() : array();

			if ( empty( $applied_coupons ) || ( ! empty( $applied_coupons ) && ! in_array( $coupon_code, $applied_coupons, true ) ) ) {
				return $valid;
			}

			if ( is_wc_endpoint_url( 'order-received' ) ) {
				return $valid;
			}

			$is_valid_coupon_amount = ( $coupon_amount <= 0 ) ? false : true;

			$is_valid_coupon_amount = apply_filters(
				'wc_sc_validate_coupon_amount',
				$is_valid_coupon_amount,
				array(
					'coupon'        => $coupon,
					'discounts'     => $discounts,
					'coupon_amount' => $coupon_amount,
					'discount_type' => $discount_type,
					'coupon_code'   => $coupon_code,
				)
			);

			if ( $valid && ! $is_valid_coupon_amount ) {
				WC()->cart->remove_coupon( $coupon_code );
				/* translators: The coupon code */
				wc_add_notice( sprintf( __( 'Coupon removed. There is no credit remaining in %s.', 'woocommerce-smart-coupons' ), '<strong>' . $coupon_code . '</strong>' ), 'error' );
				return false;
			}

			return $valid;
		}

		/**
		 * Strict check if user is valid as per usage limit
		 *
		 * @param  boolean      $is_valid  Is valid.
		 * @param  WC_Coupon    $coupon    The coupon object.
		 * @param  WC_Discounts $discounts The discounts object.
		 * @return boolean
		 * @throws Exception When coupon is not valid as per the usage limit.
		 */
		public function is_user_usage_limit_valid( $is_valid = false, $coupon = null, $discounts = null ) {

			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || DOING_AJAX !== true ) ) {
				return $is_valid;
			}

			if ( true !== $is_valid ) {
				return $is_valid;
			}

			global $wpdb;

			if ( $this->is_wc_gte_30() ) {
				$coupon_id = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
			} else {
				$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
			}

			$for_new_user = ( $this->is_callable( $coupon, 'get_meta' ) ) ? $coupon->get_meta( 'sc_restrict_to_new_user' ) : get_post_meta( $coupon_id, 'sc_restrict_to_new_user', true );

			if ( 'yes' === $for_new_user ) {

				$failed_notice = __( 'This coupon is valid for the first order only.', 'woocommerce-smart-coupons' );

				$user_id_1 = 0;
				$user_id_2 = 0;
				$user_id_3 = 0;

				$current_user = wp_get_current_user();

				$email = ( ! empty( $current_user->data->user_email ) ) ? $current_user->data->user_email : '';

				$email = ( ! empty( $_REQUEST['billing_email'] ) ) ? sanitize_email( wp_unslash( $_REQUEST['billing_email'] ) ) : $email; // phpcs:ignore

				$valid_order_statuses = get_option( 'wc_sc_valid_order_statuses_for_coupon_auto_generation', array() );
				$statuses_placeholder = array();
				if ( ! empty( $valid_order_statuses ) ) {
					$valid_order_statuses = array_map(
						function( $status ) {
							return 'wc-' . $status;
						},
						$valid_order_statuses
					);
					$how_many_statuses    = count( $valid_order_statuses );
					$statuses_placeholder = array_fill( 0, $how_many_statuses, '%s' );
				}

				if ( ! empty( $email ) && is_email( $email ) ) {

					$order_id = wp_cache_get( 'wc_sc_order_id_by_billing_email_' . sanitize_key( $email ), 'woocommerce_smart_coupons' );

					if ( false === $order_id ) {
						if ( $this->is_hpos() ) {
							$query = $wpdb->prepare(
								"SELECT id
									FROM {$wpdb->prefix}wc_orders
									WHERE billing_email = %s",
								$email
							);
						} else {
							$query = $wpdb->prepare(
								"SELECT ID
									FROM $wpdb->posts AS p
									LEFT JOIN $wpdb->postmeta AS pm
										ON ( p.ID = pm.post_id AND pm.meta_key = %s )
									WHERE p.post_type = %s
										AND pm.meta_value = %s",
								'_billing_email',
								'shop_order',
								$email
							);
						}

						if ( ! empty( $valid_order_statuses ) && ! empty( $statuses_placeholder ) ) {
							// phpcs:disable
							if ( $this->is_hpos() ) {
								$query .= $wpdb->prepare(
									' AND status IN (' . implode( ',', $statuses_placeholder ) . ')',
									$valid_order_statuses
								);
							} else {
								$query .= $wpdb->prepare(
									' AND p.post_status IN (' . implode( ',', $statuses_placeholder ) . ')',
									$valid_order_statuses
								);
							}
							// phpcs:enable
						}

						$order_id = $wpdb->get_var( $query ); // phpcs:ignore

						wp_cache_set( 'wc_sc_order_id_by_billing_email_' . sanitize_key( $email ), $order_id, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_order_id_by_billing_email_' . sanitize_key( $email ) );
					}

					if ( ! empty( $order_id ) ) {
						if ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX === true ) {
							$is_valid = false;
						} else {
							throw new Exception( $failed_notice );
						}
					}

					$user_id_1 = wp_cache_get( 'wc_sc_user_id_by_user_email_' . sanitize_key( $email ), 'woocommerce_smart_coupons' );
					if ( false === $user_id_1 ) {
						$user_id_1 = $wpdb->get_var( // phpcs:ignore
							$wpdb->prepare(
								"SELECT ID
									FROM {$wpdb->base_prefix}users
									WHERE user_email = %s",
								$email
							)
						);
						wp_cache_set( 'wc_sc_user_id_by_user_email_' . sanitize_key( $email ), $user_id_1, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_user_id_by_user_email_' . sanitize_key( $email ) );
					}

					$user_id_2 = wp_cache_get( 'wc_sc_user_id_by_billing_email_' . sanitize_key( $email ), 'woocommerce_smart_coupons' );
					if ( false === $user_id_2 ) {
						$user_id_2 = $wpdb->get_var( // phpcs:ignore
							$wpdb->prepare(
								"SELECT user_id
									FROM {$wpdb->base_prefix}usermeta
									WHERE meta_key = %s
										AND meta_value = %s",
								'billing_email',
								$email
							)
						);
						wp_cache_set( 'wc_sc_user_id_by_billing_email_' . sanitize_key( $email ), $user_id_2, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_user_id_by_billing_email_' . sanitize_key( $email ) );
					}
				}

				$user_id_3 = get_current_user_id();

				$user_ids = array( $user_id_1, $user_id_2, $user_id_3 );
				$user_ids = array_unique( array_filter( $user_ids ) );

				if ( ! empty( $user_ids ) ) {

					$unique_user_ids = array_unique( $user_ids );

					$order_id = wp_cache_get( 'wc_sc_order_for_user_id_' . implode( '_', $unique_user_ids ), 'woocommerce_smart_coupons' );

					if ( false === $order_id ) {
						if ( $this->is_hpos() ) {
							$query = $wpdb->prepare(
								"SELECT id
									FROM {$wpdb->prefix}wc_orders
									WHERE %d",
								1
							);
						} else {
							$query = $wpdb->prepare(
								"SELECT ID
									FROM $wpdb->posts AS p
									LEFT JOIN $wpdb->postmeta AS pm
										ON ( p.ID = pm.post_id AND pm.meta_key = %s )
									WHERE p.post_type = %s",
								'_customer_user',
								'shop_order'
							);
						}

						if ( ! empty( $valid_order_statuses ) && ! empty( $statuses_placeholder ) ) {
							// phpcs:disable
							if ( $this->is_hpos() ) {
								$query .= $wpdb->prepare(
									' AND status IN (' . implode( ',', $statuses_placeholder ) . ')',
									$valid_order_statuses
								);
							} else {
								$query .= $wpdb->prepare(
									' AND p.post_status IN (' . implode( ',', $statuses_placeholder ) . ')',
									$valid_order_statuses
								);
							}
							// phpcs:enable
						}

						$how_many_user_ids = count( $user_ids );

						// phpcs:disable
						if ( $this->is_hpos() ) {
							$id_placeholder    = array_fill( 0, $how_many_user_ids, '%d' );
							$query .= $wpdb->prepare(
								' AND customer_id IN (' . implode( ',', $id_placeholder ) . ')',
								$user_ids
							);
						} else {
							$id_placeholder    = array_fill( 0, $how_many_user_ids, '%s' );
							$query .= $wpdb->prepare(
								' AND pm.meta_value IN (' . implode( ',', $id_placeholder ) . ')',
								$user_ids
							);
						}
						// phpcs:enable

						$order_id = $wpdb->get_var( $query ); // phpcs:ignore

						wp_cache_set( 'wc_sc_order_for_user_id_' . implode( '_', $unique_user_ids ), $order_id, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_order_for_user_id_' . implode( '_', $unique_user_ids ) );
					}

					if ( ! empty( $order_id ) ) {
						if ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX === true ) {
							$is_valid = false;
						} else {
							throw new Exception( $failed_notice );
						}
					}
				}
			}

			return $is_valid;
		}

		/**
		 * Locate template for Smart Coupons
		 *
		 * @param string $template_name The template name.
		 * @param mixed  $template Default template.
		 * @return mixed $template
		 */
		public function locate_template_for_smart_coupons( $template_name = '', $template = '' ) {

			$default_path = untrailingslashit( plugin_dir_path( WC_SC_PLUGIN_FILE ) ) . '/templates/';

			$plugin_base_dir = substr( plugin_basename( WC_SC_PLUGIN_FILE ), 0, strpos( plugin_basename( WC_SC_PLUGIN_FILE ), '/' ) + 1 );

			// Look within passed path within the theme - this is priority.
			$template = locate_template(
				array(
					'woocommerce/' . $plugin_base_dir . $template_name,
					$plugin_base_dir . $template_name,
					$template_name,
				)
			);

			// Get default template.
			if ( ! $template ) {
				$template = $default_path . $template_name;
			}

			return $template;
		}

		/**
		 * Function to get template base directory for Smart Coupons' email templates
		 *
		 * @param  string $template_name Template name.
		 * @return string $template_base_dir Base directory for Smart Coupons' email templates.
		 */
		public function get_template_base_dir( $template_name = '' ) {

			$template_base_dir = '';
			$plugin_base_dir   = substr( plugin_basename( WC_SC_PLUGIN_FILE ), 0, strpos( plugin_basename( WC_SC_PLUGIN_FILE ), '/' ) + 1 );
			$wc_sc_base_dir    = 'woocommerce/' . $plugin_base_dir;

			// First locate the template in woocommerce/woocommerce-smart-coupons folder of active theme.
			$template = locate_template(
				array(
					$wc_sc_base_dir . $template_name,
				)
			);

			if ( ! empty( $template ) ) {
				$template_base_dir = $wc_sc_base_dir;
			} else {
				// If not found then locate the template in woocommerce-smart-coupons folder of active theme.
				$template = locate_template(
					array(
						$plugin_base_dir . $template_name,
					)
				);
				if ( ! empty( $template ) ) {
					$template_base_dir = $plugin_base_dir;
				}
			}

			$template_base_dir = apply_filters( 'wc_sc_template_base_dir', $template_base_dir, $template_name );

			return $template_base_dir;
		}

		/**
		 * Check whether credit is sent or not
		 *
		 * @param string    $email_id The email address.
		 * @param WC_Coupon $coupon The coupon object.
		 * @return boolean
		 */
		public function is_credit_sent( $email_id, $coupon ) {

			global $smart_coupon_codes;

			if ( isset( $smart_coupon_codes[ $email_id ] ) && count( $smart_coupon_codes[ $email_id ] ) > 0 ) {
				if ( $this->is_wc_gte_30() ) {
					$coupon_id = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				} else {
					$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				}
				foreach ( $smart_coupon_codes[ $email_id ] as $generated_coupon_details ) {
					if ( $generated_coupon_details['parent'] === $coupon_id ) {
						return true;
					}
				}
			}

			return false;

		}

		/**
		 * Generate unique string to be used as coupon code. Also add prefix or suffix if already set
		 *
		 * @param string    $email The email address.
		 * @param WC_Coupon $coupon The coupon object.
		 * @return string $unique_code
		 */
		public function generate_unique_code( $email = '', $coupon = '' ) {
			$unique_code = '';
			srand( (double) microtime( true ) * 1000000 ); // phpcs:ignore

			$coupon_code_length = $this->get_coupon_code_length();

			$chars = array_diff( array_merge( range( 'a', 'z' ), range( '0', '9' ) ), array( 'a', 'e', 'i', 'o', 'u' ) );
			$chars = apply_filters(
				'wc_sc_coupon_code_allowed_characters',
				array_values( $chars ),
				array(
					'source'             => $this,
					'email'              => $email,
					'coupon_object'      => $coupon,
					'coupon_code_length' => $coupon_code_length,
				)
			);

			$numbers   = array_values( array_filter( $chars, 'is_numeric' ) );
			$alphabets = ( count( $chars ) !== count( $numbers ) ) ? array_values( array_diff( $chars, $numbers ) ) : array();

			if ( empty( $numbers ) || empty( $alphabets ) ) {
				$chars = array_values( array_merge( $alphabets, $numbers ) );
				for ( $rand = 1; $rand <= $coupon_code_length; $rand++ ) {
					$random       = rand( 0, count( $chars ) - 1 ); // phpcs:ignore
					$unique_code .= $chars[ $random ];
				}
			} else {
				for ( $rand = 1, $char_count = 0, $num_count = 0; $rand <= $coupon_code_length; $rand++ ) {
					if ( $char_count >= 2 ) {
						$random       = rand( 0, count( $numbers ) - 1 ); // phpcs:ignore
						$unique_code .= $numbers[ $random ];
						$char_count   = 0;
						$num_count++;
					} elseif ( $num_count >= 1 ) {
						$random       = rand( 0, count( $alphabets ) - 1 ); // phpcs:ignore
						$unique_code .= $alphabets[ $random ];
						$num_count    = 0;
						$char_count++;
					} else {
						$random        = rand( 0, count( $chars ) - 1 ); // phpcs:ignore
						$selected_char = $chars[ $random ];
						$unique_code  .= $selected_char;
						if ( is_numeric( $selected_char ) ) {
							$num_count++;
						} else {
							$char_count++;
						}
					}
				}
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_id = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
			} else {
				$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
			}

			if ( $this->is_callable( $coupon, 'get_meta' ) ) {
				if ( 'yes' === $coupon->get_meta( 'auto_generate_coupon' ) ) {
					$prefix      = $coupon->get_meta( 'coupon_title_prefix' );
					$suffix      = $coupon->get_meta( 'coupon_title_suffix' );
					$unique_code = $prefix . $unique_code . $suffix;
				}
			} else {
				if ( ! empty( $coupon_id ) && get_post_meta( $coupon_id, 'auto_generate_coupon', true ) === 'yes' ) {
					$prefix      = get_post_meta( $coupon_id, 'coupon_title_prefix', true );
					$suffix      = get_post_meta( $coupon_id, 'coupon_title_suffix', true );
					$unique_code = $prefix . $unique_code . $suffix;
				}
			}

			return apply_filters(
				'wc_sc_generate_unique_coupon_code',
				$unique_code,
				array(
					'email'  => $email,
					'coupon' => $coupon,
				)
			);
		}

		/**
		 * Function for generating Gift Certificate
		 *
		 * @param mixed     $email The email address.
		 * @param float     $amount The amount.
		 * @param int       $order_id The order id.
		 * @param WC_Coupon $coupon The coupon object.
		 * @param string    $discount_type The discount type.
		 * @param array     $gift_certificate_receiver_name Receiver name.
		 * @param string    $message_from_sender Message from sender.
		 * @param string    $gift_certificate_sender_name Sender name.
		 * @param string    $gift_certificate_sender_email Sender email.
		 * @param string    $sending_timestamp timestamp for scheduled sending.
		 * @return array of generated coupon details
		 */
		public function generate_smart_coupon( $email, $amount, $order_id = '', $coupon = '', $discount_type = 'smart_coupon', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '', $sending_timestamp = '' ) {
			return $this->generate_smart_coupon_action( $email, $amount, $order_id, $coupon, $discount_type, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $sending_timestamp );
		}

		/**
		 * Function for generating Gift Certificate
		 *
		 * @param mixed     $email The email address.
		 * @param float     $amount The amount.
		 * @param int       $order_id The order id.
		 * @param WC_Coupon $coupon The coupon object.
		 * @param string    $discount_type The discount type.
		 * @param array     $gift_certificate_receiver_name Receiver name.
		 * @param string    $message_from_sender Message from sender.
		 * @param string    $gift_certificate_sender_name Sender name.
		 * @param string    $gift_certificate_sender_email Sender email.
		 * @param string    $sending_timestamp timestamp for scheduled sending.
		 * @return array $smart_coupon_codes associative array containing generated coupon details
		 */
		public function generate_smart_coupon_action( $email, $amount, $order_id = '', $coupon = '', $discount_type = 'smart_coupon', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '', $sending_timestamp = '' ) {

			if ( '' === $email ) {
				return false;
			}

			global $smart_coupon_codes;

			$order_id = ( ! empty( $order_id ) ) ? absint( $order_id ) : 0;
			$order    = ( function_exists( 'wc_get_order' ) && ! empty( $order_id ) ) ? wc_get_order( $order_id ) : null;

			$is_callable_order_get_meta  = $this->is_callable( $order, 'get_meta' );
			$is_callable_coupon_get_meta = $this->is_callable( $coupon, 'get_meta' );

			if ( $this->is_wc_gte_30() ) {
				$coupon_id                          = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				$is_free_shipping                   = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_free_shipping' ) ) ) ? ( ( $coupon->get_free_shipping() ) ? 'yes' : 'no' ) : '';
				$discount_type                      = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
				$expiry_date                        = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_date_expires' ) ) ) ? $coupon->get_date_expires() : '';
				$coupon_product_ids                 = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_product_ids' ) ) ) ? $coupon->get_product_ids() : '';
				$coupon_product_categories          = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_product_categories' ) ) ) ? $coupon->get_product_categories() : '';
				$coupon_excluded_product_ids        = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_excluded_product_ids' ) ) ) ? $coupon->get_excluded_product_ids() : '';
				$coupon_excluded_product_categories = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_excluded_product_categories' ) ) ) ? $coupon->get_excluded_product_categories() : '';
				$coupon_minimum_amount              = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_minimum_amount' ) ) ) ? $coupon->get_minimum_amount() : '';
				$coupon_maximum_amount              = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_maximum_amount' ) ) ) ? $coupon->get_maximum_amount() : '';
				$coupon_usage_limit                 = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_usage_limit' ) ) ) ? $coupon->get_usage_limit() : '';
				$coupon_usage_limit_per_user        = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_usage_limit_per_user' ) ) ) ? $coupon->get_usage_limit_per_user() : '';
				$coupon_limit_usage_to_x_items      = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_limit_usage_to_x_items' ) ) ) ? $coupon->get_limit_usage_to_x_items() : '';
				$is_exclude_sale_items              = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_exclude_sale_items' ) ) ) ? ( ( $coupon->get_exclude_sale_items() ) ? 'yes' : 'no' ) : '';
				$is_individual_use                  = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_individual_use' ) ) ) ? ( ( $coupon->get_individual_use() ) ? 'yes' : 'no' ) : '';
			} else {
				$coupon_id                          = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$is_free_shipping                   = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
				$discount_type                      = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				$expiry_date                        = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
				$coupon_product_ids                 = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : '';
				$coupon_product_categories          = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : '';
				$coupon_excluded_product_ids        = ( ! empty( $coupon->exclude_product_ids ) ) ? $coupon->exclude_product_ids : '';
				$coupon_excluded_product_categories = ( ! empty( $coupon->exclude_product_categories ) ) ? $coupon->exclude_product_categories : '';
				$coupon_minimum_amount              = ( ! empty( $coupon->minimum_amount ) ) ? $coupon->minimum_amount : '';
				$coupon_maximum_amount              = ( ! empty( $coupon->maximum_amount ) ) ? $coupon->maximum_amount : '';
				$coupon_usage_limit                 = ( ! empty( $coupon->usage_limit ) ) ? $coupon->usage_limit : '';
				$coupon_usage_limit_per_user        = ( ! empty( $coupon->usage_limit_per_user ) ) ? $coupon->usage_limit_per_user : '';
				$coupon_limit_usage_to_x_items      = ( ! empty( $coupon->limit_usage_to_x_items ) ) ? $coupon->limit_usage_to_x_items : '';
				$is_exclude_sale_items              = ( ! empty( $coupon->exclude_sale_items ) ) ? $coupon->exclude_sale_items : '';
				$is_individual_use                  = ( ! empty( $coupon->individual_use ) ) ? $coupon->individual_use : '';
			}

			if ( ! is_array( $email ) ) {
				$emails = array( $email => 1 );
			} else {
				$temp_email = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'temp_gift_card_receivers_emails' ) : get_post_meta( $order_id, 'temp_gift_card_receivers_emails', true );
				if ( ! empty( $temp_email ) && count( $temp_email ) > 0 ) {
					$email = $temp_email;
				}
				$emails = ( ! empty( $coupon_id ) ) ? array_count_values( $email[ $coupon_id ] ) : array();
			}

			if ( ! empty( $order_id ) ) {
				$receivers_messages    = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'gift_receiver_message' ) : get_post_meta( $order_id, 'gift_receiver_message', true );
				$schedule_gift_sending = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'wc_sc_schedule_gift_sending' ) : get_post_meta( $order_id, 'wc_sc_schedule_gift_sending', true );
				$sending_timestamps    = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'gift_sending_timestamp' ) : get_post_meta( $order_id, 'gift_sending_timestamp', true );
			}

			foreach ( $emails as $email_id => $qty ) {

				if ( $this->is_credit_sent( $email_id, $coupon ) ) {
					continue;
				}

				$smart_coupon_code = $this->generate_unique_code( $email_id, $coupon );

				$coupon_post = ( ! empty( $coupon_id ) ) ? get_post( $coupon_id ) : new stdClass();

				$smart_coupon_args = array(
					'post_title'   => strtolower( $smart_coupon_code ),
					'post_excerpt' => ( ! empty( $coupon_post->post_excerpt ) ) ? $coupon_post->post_excerpt : '',
					'post_content' => '',
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'shop_coupon',
					'post_parent'  => ! empty( $coupon_id ) ? absint( $coupon_id ) : 0,
				);

				$should_schedule = isset( $schedule_gift_sending ) && 'yes' === $schedule_gift_sending && $this->is_valid_timestamp( $sending_timestamp ) ? true : false;

				if ( $should_schedule ) {
					$smart_coupon_args['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $sending_timestamp );
					$smart_coupon_args['post_date']     = gmdate( 'Y-m-d H:i:s', ( $sending_timestamp + $this->wc_timezone_offset() ) );
				}

				$smart_coupon = new WC_Coupon( $smart_coupon_args['post_title'] );
				$smart_coupon->set_description( $smart_coupon_args['post_excerpt'] );

				if ( $this->is_wc_greater_than( '6.1.2' ) && $this->is_callable( $smart_coupon, 'set_status' ) ) {
					$smart_coupon->set_status( $smart_coupon_args['post_status'] );
				}

				$smart_coupon_id = $smart_coupon->save();

				if ( ! empty( $smart_coupon_id ) ) {
					$smart_coupon_args       = array_diff_key( $smart_coupon_args, array_flip( array( 'post_excerpt', 'post_title', 'post_status', 'post_type' ) ) );
					$smart_coupon_args['ID'] = $smart_coupon_id;
					wp_update_post( $smart_coupon_args );
				}

				$smart_coupon_id = absint( $smart_coupon_id );

				$smart_coupon = new WC_Coupon( $smart_coupon );

				$is_callable_smart_coupon_update_meta = $this->is_callable( $smart_coupon, 'update_meta_data' );

				$type                         = ( ! empty( $discount_type ) ) ? $discount_type : 'smart_coupon';
				$individual_use               = ( ! empty( $is_individual_use ) ) ? $is_individual_use : 'no';
				$minimum_amount               = ( ! empty( $coupon_minimum_amount ) ) ? $coupon_minimum_amount : '';
				$maximum_amount               = ( ! empty( $coupon_maximum_amount ) ) ? $coupon_maximum_amount : '';
				$product_ids                  = ( ! empty( $coupon_product_ids ) ) ? implode( ',', $coupon_product_ids ) : '';
				$exclude_product_ids          = ( ! empty( $coupon_excluded_product_ids ) ) ? implode( ',', $coupon_excluded_product_ids ) : '';
				$usage_limit                  = ( ! empty( $coupon_usage_limit ) ) ? $coupon_usage_limit : '';
				$usage_limit_per_user         = ( ! empty( $coupon_usage_limit_per_user ) ) ? $coupon_usage_limit_per_user : '';
				$limit_usage_to_x_items       = ( ! empty( $coupon_limit_usage_to_x_items ) ) ? $coupon_limit_usage_to_x_items : '';
				$free_shipping                = ( ! empty( $is_free_shipping ) ) ? $is_free_shipping : 'no';
				$product_categories           = ( ! empty( $coupon_product_categories ) ) ? $coupon_product_categories : array();
				$exclude_product_categories   = ( ! empty( $coupon_excluded_product_categories ) ) ? $coupon_excluded_product_categories : array();
				$sc_coupon_validity           = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'sc_coupon_validity' ) : ( ( ! empty( $coupon_id ) ) ? get_post_meta( $coupon_id, 'sc_coupon_validity', true ) : '' );
				$is_disable_email_restriction = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'sc_disable_email_restriction' ) : ( ( ! empty( $coupon_id ) ) ? get_post_meta( $coupon_id, 'sc_disable_email_restriction', true ) : '' );
				$sc_restrict_to_new_user      = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'sc_restrict_to_new_user' ) : get_post_meta( $coupon_id, 'sc_restrict_to_new_user', true );
				$wc_sc_max_discount           = $this->get_post_meta( $coupon_id, 'wc_sc_max_discount', true, true, $order );

				if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
					$expiry_date = ( is_callable( array( $expiry_date, 'getTimestamp' ) ) ) ? $expiry_date->getTimestamp() : null;
				} elseif ( ! is_int( $expiry_date ) ) {
					$expiry_date = $this->strtotime( $expiry_date );
				}

				if ( ! empty( $coupon_id ) && ! empty( $sc_coupon_validity ) ) {
					$is_parent_coupon_expired = ( ! empty( $expiry_date ) && ( $expiry_date < time() ) ) ? true : false;
					if ( ! $is_parent_coupon_expired ) {
						$validity_suffix = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'validity_suffix' ) : get_post_meta( $coupon_id, 'validity_suffix', true );
						// In case of scheduled coupon, expiry date is calculated from scheduled publish date.
						if ( isset( $smart_coupon_args['post_date_gmt'] ) ) {
							$expiry_date = $this->strtotime( $smart_coupon_args['post_date_gmt'] . "+$sc_coupon_validity $validity_suffix" );
						} else {
							$expiry_date = $this->strtotime( "+$sc_coupon_validity $validity_suffix" );
						}
					}
				}

				if ( $this->is_wc_gte_30() ) {
					$expiry_date = $this->get_date_expires_value( $expiry_date );
					if ( true === $is_callable_smart_coupon_update_meta ) {
						$smart_coupon->set_date_expires( $expiry_date );
					} else {
						update_post_meta( $smart_coupon_id, 'date_expires', $expiry_date );
					}
				} else {
					$expiry_date = ( ! empty( $expiry_date ) ) ? gmdate( 'Y-m-d', intval( $expiry_date ) + $this->wc_timezone_offset() ) : '';
					if ( true === $is_callable_smart_coupon_update_meta ) {
						$smart_coupon->update_meta_data( 'expiry_date', $expiry_date );
					} else {
						update_post_meta( $smart_coupon_id, 'expiry_date', $expiry_date );
					}
				}

				if ( 'smart_coupon' === $type ) {
					$this->update_post_meta( $smart_coupon_id, 'wc_sc_original_amount', $amount, false, $order );
				}

				if ( true === $is_callable_smart_coupon_update_meta ) {
					$product_ids         = ( ! is_array( $product_ids ) ) ? explode( ',', $product_ids ) : $product_ids; // set_product_ids expects an array.
					$exclude_product_ids = ( ! is_array( $exclude_product_ids ) ) ? explode( ',', $exclude_product_ids ) : $exclude_product_ids; // set_excluded_product_ids expects an array.

					$smart_coupon->set_amount( $amount );
					$smart_coupon->set_excluded_product_ids( $exclude_product_ids );
					$smart_coupon->set_excluded_product_categories( $exclude_product_categories );
					$smart_coupon->set_discount_type( $type );
					$smart_coupon->set_individual_use( $this->wc_string_to_bool( $individual_use ) );
					$smart_coupon->set_minimum_amount( $minimum_amount );
					$smart_coupon->set_maximum_amount( $maximum_amount );
					$smart_coupon->set_product_ids( $product_ids );
					$smart_coupon->set_usage_limit( $usage_limit );
					$smart_coupon->set_usage_limit_per_user( $usage_limit_per_user );
					$smart_coupon->set_limit_usage_to_x_items( $limit_usage_to_x_items );
					$smart_coupon->set_free_shipping( $this->wc_string_to_bool( $free_shipping ) );
					$smart_coupon->set_product_categories( $product_categories );
					$smart_coupon->set_exclude_sale_items( $this->wc_string_to_bool( $is_exclude_sale_items ) );
					$smart_coupon->update_meta_data( 'sc_restrict_to_new_user', $sc_restrict_to_new_user );
					if ( ! empty( $order_id ) ) {
						$smart_coupon->update_meta_data( 'generated_from_order_id', $order_id );
					}
					if ( empty( $is_disable_email_restriction ) || 'no' === $is_disable_email_restriction ) {
						// Update customer_email now if coupon is not scheduled otherwise it would be updated by action scheduler later on.
						if ( ! $should_schedule ) {
							$smart_coupon->set_email_restrictions( array( $email_id ) );
						}
					}
				} else {
					update_post_meta( $smart_coupon_id, 'discount_type', $type );
					update_post_meta( $smart_coupon_id, 'coupon_amount', $amount );
					update_post_meta( $smart_coupon_id, 'individual_use', $individual_use );
					update_post_meta( $smart_coupon_id, 'minimum_amount', $minimum_amount );
					update_post_meta( $smart_coupon_id, 'maximum_amount', $maximum_amount );
					update_post_meta( $smart_coupon_id, 'product_ids', $product_ids );
					update_post_meta( $smart_coupon_id, 'exclude_product_ids', $exclude_product_ids );
					update_post_meta( $smart_coupon_id, 'usage_limit', $usage_limit );
					update_post_meta( $smart_coupon_id, 'usage_limit_per_user', $usage_limit_per_user );
					update_post_meta( $smart_coupon_id, 'limit_usage_to_x_items', $limit_usage_to_x_items );
					update_post_meta( $smart_coupon_id, 'free_shipping', $free_shipping );
					update_post_meta( $smart_coupon_id, 'product_categories', $product_categories );
					update_post_meta( $smart_coupon_id, 'exclude_product_categories', $exclude_product_categories );
					update_post_meta( $smart_coupon_id, 'exclude_sale_items', $is_exclude_sale_items );
					update_post_meta( $smart_coupon_id, 'sc_restrict_to_new_user', $sc_restrict_to_new_user );
					if ( ! empty( $order_id ) ) {
						update_post_meta( $smart_coupon_id, 'generated_from_order_id', $order_id );
					}
					if ( empty( $is_disable_email_restriction ) || 'no' === $is_disable_email_restriction ) {
						// Update customer_email now if coupon is not scheduled otherwise it would be updated by action scheduler later on.
						if ( ! $should_schedule ) {
							update_post_meta( $smart_coupon_id, 'customer_email', array( $email_id ) );
						}
					}
				}

				if ( ! $this->is_wc_gte_30() ) {
					$apply_before_tax = ( ! empty( $coupon->apply_before_tax ) ) ? $coupon->apply_before_tax : 'no';
					if ( true === $is_callable_smart_coupon_update_meta ) {
						$smart_coupon->update_meta_data( 'apply_before_tax', $apply_before_tax );
					} else {
						update_post_meta( $smart_coupon_id, 'apply_before_tax', $apply_before_tax );
					}
				}

				// Add terms to auto-generated if found in parent coupon.
				$coupon_terms = get_the_terms( $coupon_id, 'sc_coupon_category' );
				if ( ! empty( $coupon_terms ) ) {
					$term_ids = array_column( $coupon_terms, 'term_id' );
					wp_set_object_terms( $smart_coupon_id, $term_ids, 'sc_coupon_category', false );
				}

				if ( ! empty( $wc_sc_max_discount ) ) {
					$this->update_post_meta( $smart_coupon_id, 'wc_sc_max_discount', $wc_sc_max_discount, true, $order );
				}

				if ( $this->is_wc_gte_32() ) {
					$wc_sc_expiry_time = ( true === $is_callable_coupon_get_meta ) ? (int) $coupon->get_meta( 'wc_sc_expiry_time' ) : (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
					if ( ! empty( $wc_sc_expiry_time ) ) {
						if ( true === $is_callable_smart_coupon_update_meta ) {
							$smart_coupon->update_meta_data( 'wc_sc_expiry_time', $wc_sc_expiry_time );
						} else {
							update_post_meta( $smart_coupon_id, 'wc_sc_expiry_time', $wc_sc_expiry_time );
						}
					}
				}

				if ( $this->is_callable( $smart_coupon, 'save' ) ) {
					$smart_coupon->save();
				}

				/**
				 * Hook for 3rd party developers to add data in generated coupon
				 *
				 * New coupon id    new_coupon_id Newly generated coupon post id
				 * Reference coupon ref_coupon This is the coupon from which meta will be copied to newly created coupon
				 */
				do_action(
					'wc_sc_new_coupon_generated',
					array(
						'new_coupon_id' => $smart_coupon_id,
						'ref_coupon'    => $coupon,
					)
				);

				$generated_coupon_details = array(
					'parent' => ( ! empty( $coupon_id ) ) ? $coupon_id : 0,
					'code'   => $smart_coupon_code,
					'amount' => $amount,
				);

				$smart_coupon_codes[ $email_id ][] = $generated_coupon_details;

				if ( ! empty( $order_id ) ) {
					$is_gift = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'is_gift' ) : get_post_meta( $order_id, 'is_gift', true );
				} else {
					$is_gift = 'no';
				}

				if ( is_array( $email ) && ! empty( $coupon_id ) && isset( $email[ $coupon_id ] ) ) {
					$message_index = array_search( $email_id, $email[ $coupon_id ], true );
					if ( false !== $message_index && isset( $receivers_messages[ $coupon_id ][ $message_index ] ) && ! empty( $receivers_messages[ $coupon_id ][ $message_index ] ) ) {
						$message_from_sender = $receivers_messages[ $coupon_id ][ $message_index ];
						unset( $email[ $coupon_id ][ $message_index ] );
						$this->update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email, false, $order );
					}
				}

				if ( ( isset( $schedule_gift_sending ) && 'yes' === $schedule_gift_sending && $this->is_valid_timestamp( $sending_timestamp ) ) ) {
					$wc_sc_coupon_receiver_details = array(
						'coupon_details'                  => $generated_coupon_details,
						'gift_certificate_receiver_email' => $email_id,
						'gift_certificate_receiver_name'  => $gift_certificate_receiver_name,
						'message_from_sender'             => $message_from_sender,
						'gift_certificate_sender_name'    => $gift_certificate_sender_name,
						'gift_certificate_sender_email'   => $gift_certificate_sender_email,
					);
					if ( true === $is_callable_smart_coupon_update_meta ) {
						$smart_coupon->update_meta_data( 'wc_sc_coupon_receiver_details', $wc_sc_coupon_receiver_details );
					} else {
						update_post_meta( $smart_coupon_id, 'wc_sc_coupon_receiver_details', $wc_sc_coupon_receiver_details );
					}
				} else {
					$is_send_email  = $this->is_email_template_enabled();
					$combine_emails = $this->is_email_template_enabled( 'combine' );
					if ( 'yes' === $is_send_email ) {
						if ( 'yes' === $combine_emails ) {
							$coupon_receiver_details = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'sc_coupon_receiver_details' ) : get_post_meta( $order_id, 'sc_coupon_receiver_details', true );
							if ( empty( $coupon_receiver_details ) || ! is_array( $coupon_receiver_details ) ) {
								$coupon_receiver_details = array();
							}
							$coupon_receiver_details[] = array(
								'code'    => $generated_coupon_details['code'],
								'amount'  => $amount,
								'email'   => $email_id,
								'message' => $message_from_sender,
							);
							$this->update_post_meta( $order_id, 'sc_coupon_receiver_details', $coupon_receiver_details, false, $order );
						} else {
							$this->sa_email_coupon( array( $email_id => $generated_coupon_details ), $type, $order_id, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $is_gift );
						}
					} else {
						if ( ! empty( $order_id ) ) {
							$coupon_receiver_details = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'sc_coupon_receiver_details' ) : get_post_meta( $order_id, 'sc_coupon_receiver_details', true );
							if ( empty( $coupon_receiver_details ) || ! is_array( $coupon_receiver_details ) ) {
								$coupon_receiver_details = array();
							}
							$coupon_receiver_details[] = array(
								'code'    => $generated_coupon_details['code'],
								'amount'  => $amount,
								'email'   => $email_id,
								'message' => $message_from_sender,
							);
							$this->update_post_meta( $order_id, 'sc_coupon_receiver_details', $coupon_receiver_details, false, $order );
						}
					}
				}
				if ( $this->is_callable( $smart_coupon, 'save' ) ) {
					$smart_coupon->save();
				}
			}

			return $smart_coupon_codes;

		}

		/**
		 * Function to set that Smart Coupons plugin is used to auto generate a coupon
		 *
		 * @param array $args Data.
		 */
		public function smart_coupons_plugin_used( $args = array() ) {

			$is_show_review_notice = get_option( 'wc_sc_is_show_review_notice' );

			if ( false === $is_show_review_notice ) {
				update_option( 'wc_sc_is_show_review_notice', time(), 'no' );
			}

		}

		/**
		 * Add button to export coupons on Coupons admin page
		 */
		public function woocommerce_restrict_manage_smart_coupons() {
			global $typenow, $wp_query, $wp, $woocommerce_smart_coupon;

			if ( 'shop_coupon' !== $typenow ) {
				return;
			}

			$is_print  = get_option( 'smart_coupons_is_print_coupon', 'yes' );
			$is_print  = apply_filters( 'wc_sc_admin_show_print_button', wc_string_to_bool( $is_print ), array( 'source' => $woocommerce_smart_coupon ) );
			$print_url = add_query_arg(
				array(
					'print-coupons' => 'yes',
					'source'        => 'wc-smart-coupons',
					'coupon-codes'  => '',
				),
				home_url()
			);
			wc_product_dropdown_categories(
				array(
					'selected'           => isset( $wp_query->query_vars['sc_coupon_category'] ) ? $wp_query->query_vars['sc_coupon_category'] : '',
					'taxonomy'           => 'sc_coupon_category',
					'name'               => 'sc_coupon_category',
					'option_select_text' => __( 'Filter by category', 'woocommerce-smart-coupons' ),
					'hide_empty'         => 0,
				)
			);

			?>
			<script type="text/javascript">
				jQuery(function(){
					<?php if ( true === $is_print ) { ?>
					jQuery('body').on('click', 'a#wc_sc_print_coupons', function( e ){
						let selected = ﻿﻿jQuery('input[id^=cb-select-]').map(function(){
							if ( jQuery(this).is(':checked') ) {
								return jQuery(this).closest('tr').find('.coupon_code .row-title').text();
							}
						}).get();
						if ( selected && selected.length > 0 ) {
							let url = decodeURIComponent( '<?php echo rawurlencode( (string) $print_url ); ?>' );
								url += '=' + selected.join(',');
							window.open( url, '_blank' );
						} else {
							let sc_print_notice = decodeURIComponent( '<?php echo rawurlencode( (string) __( 'Please select at least one coupon to print.', 'woocommerce-smart-coupons' ) ); ?>' );
							alert( sc_print_notice );
						}
					});
					<?php } ?>
				});
			</script>
			<div class="alignright" style="margin-top: 1px;" >
				<?php
				if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
					echo '<input type="hidden" name="sc_export_query_args" value="' . esc_attr( wc_clean( wp_unslash( $_SERVER['QUERY_STRING'] ) ) ) . '">'; // phpcs:ignore
				}
				?>
				<button type="submit" class="button" id="export_coupons" name="export_coupons" value="<?php echo esc_attr__( 'Export', 'woocommerce-smart-coupons' ); ?>"><span class="dashicons dashicons-upload"></span><?php echo esc_html__( 'Export', 'woocommerce-smart-coupons' ); ?></button>
				<?php if ( true === $is_print ) { ?>
					<a class="button" id="wc_sc_print_coupons" href="javascript:void(0)" title="<?php echo esc_attr__( 'Print selected coupons', 'woocommerce-smart-coupons' ); ?>"><span class="dashicons dashicons-media-default"></span><?php echo esc_html__( 'Print', 'woocommerce-smart-coupons' ); ?></a>
				<?php } ?>
				<a class="button" id="sc-manage-category" title="" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=sc_coupon_category&post_type=shop_coupon' ) ); ?>"><span class="dashicons dashicons-admin-tools"></span><?php echo esc_attr__( 'Manage coupon categories', 'woocommerce-smart-coupons' ); ?></a>
			</div>
			<?php
		}

		/**
		 * Export coupons
		 */
		public function woocommerce_export_coupons() {
			global $typenow, $wp_query, $wp, $post;

			if ( is_admin() && isset( $_GET['export_coupons'] ) && current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore

				$args = array(
					'post_status'    => '',
					'post_type'      => '',
					'm'              => '',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				);

				if ( ! empty( $_REQUEST['sc_export_query_args'] ) ) { // phpcs:ignore
					parse_str( wc_clean( wp_unslash( $_REQUEST['sc_export_query_args'] ) ), $sc_args ); // phpcs:ignore
				}
				$args = array_merge( $args, $sc_args );

				$get_coupon_type = ( ! empty( $_GET['coupon_type'] ) ) ? wc_clean( wp_unslash( $_GET['coupon_type'] ) ) : ''; // phpcs:ignore
				$get_post        = ( ! empty( $_GET['post'] ) ) ? wc_clean( wp_unslash( $_GET['post'] ) ) : ''; // phpcs:ignore

				if ( isset( $get_coupon_type ) && '' !== $get_coupon_type ) {
					$args['meta_query'] = array( // phpcs:ignore
						array(
							'key'   => 'discount_type',
							'value' => $get_coupon_type,
						),
					);
				}

				if ( ! empty( $get_post ) ) {
					$args['post__in'] = $get_post;
				}

				foreach ( $args as $key => $value ) {
					if ( array_key_exists( $key, wc_clean( wp_unslash( $_GET ) ) ) ) { // phpcs:ignore
						$args[ $key ] = wc_clean( wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore
					}
				}

				if ( 'all' === $args['post_status'] ) {
					$args['post_status'] = array( 'publish', 'draft', 'pending', 'private', 'future' );

				}

				$query = new WP_Query( $args );

				$post_ids = $query->posts;

				$this->export_coupon( '', wc_clean( wp_unslash( $_GET ) ), $post_ids ); // phpcs:ignore
			}
		}

		/**
		 * Generate coupon code
		 *
		 * @param array $post POST.
		 * @param array $get GET.
		 * @param array $post_ids Post ids.
		 * @param array $coupon_postmeta_headers Coupon postmeta headers.
		 * @return array $data associative array of generated coupon
		 */
		public function generate_coupons_code( $post = array(), $get = array(), $post_ids = array(), $coupon_postmeta_headers = array() ) {
			global $wpdb, $wp, $wp_query;

			if ( ! empty( $post_ids ) ) {
				if ( ! is_array( $post_ids ) ) {
					$post_ids = array( $post_ids );
				}
				$post_ids = array_map( 'absint', $post_ids );
			}

			$data = array();
			if ( ! empty( $post ) && isset( $post['generate_and_import'] ) ) {

				$customer_emails = array();
				$unique_code     = '';
				if ( ! empty( $post['customer_email'] ) ) {
					$emails = explode( ',', $post['customer_email'] );
					if ( is_array( $emails ) && count( $emails ) > 0 ) {
						for ( $j = 1; $j <= $post['no_of_coupons_to_generate']; $j++ ) {
							$email                 = ( ! empty( $emails[ $j - 1 ] ) ) ? sanitize_email( $emails[ $j - 1 ] ) : '';
							$customer_emails[ $j ] = ( ! empty( $email ) && is_email( $email ) ) ? $email : '';
						}
					}
				}

				$all_discount_types     = wc_get_coupon_types();
				$generated_codes        = array();
				$refresh_global_coupons = false;

				for ( $i = 1; $i <= $post['no_of_coupons_to_generate']; $i++ ) {
					$customer_email = ( ! empty( $customer_emails[ $i ] ) ) ? $customer_emails[ $i ] : '';
					$unique_code    = $this->generate_unique_code( $customer_email );
					if ( ! empty( $generated_codes ) && in_array( $unique_code, $generated_codes, true ) ) {
						$max = ( $post['no_of_coupons_to_generate'] * 10 ) - 1;
						do {
							$unique_code_temp = $unique_code . wp_rand( 0, $max );
						} while ( in_array( $unique_code_temp, $generated_codes, true ) );
						$unique_code = $unique_code_temp;
					}
					$generated_codes[]           = $unique_code;
					$post['coupon_title_prefix'] = ( ! empty( $post['coupon_title_prefix'] ) ) ? $post['coupon_title_prefix'] : '';
					$post['coupon_title_suffix'] = ( ! empty( $post['coupon_title_suffix'] ) ) ? $post['coupon_title_suffix'] : '';
					$code                        = $post['coupon_title_prefix'] . $unique_code . $post['coupon_title_suffix'];

					$data[ $i ]['post_title'] = strtolower( $code );

					$discount_type = ( ! empty( $post['discount_type'] ) ) ? $post['discount_type'] : 'percent';

					if ( ! empty( $all_discount_types[ $discount_type ] ) ) {
						$data[ $i ]['discount_type'] = $all_discount_types[ $discount_type ];
					} else {
						if ( $this->is_wc_gte_30() ) {
							$data[ $i ]['discount_type'] = 'Percentage discount';
						} else {
							$data[ $i ]['discount_type'] = 'Cart % Discount';
						}
					}

					if ( $this->is_wc_gte_30() ) {
						$post['product_ids']         = ( ! empty( $post['product_ids'] ) ) ? ( ( is_array( $post['product_ids'] ) ) ? implode( ',', $post['product_ids'] ) : $post['product_ids'] ) : '';
						$post['exclude_product_ids'] = ( ! empty( $post['exclude_product_ids'] ) ) ? ( ( is_array( $post['exclude_product_ids'] ) ) ? implode( ',', $post['exclude_product_ids'] ) : $post['exclude_product_ids'] ) : '';
					}

					$data[ $i ]['coupon_amount']          = $post['coupon_amount'];
					$data[ $i ]['individual_use']         = ( isset( $post['individual_use'] ) ) ? 'yes' : 'no';
					$data[ $i ]['product_ids']            = ( isset( $post['product_ids'] ) ) ? str_replace( array( ',', ' ' ), array( '|', '' ), $post['product_ids'] ) : '';
					$data[ $i ]['exclude_product_ids']    = ( isset( $post['exclude_product_ids'] ) ) ? str_replace( array( ',', ' ' ), array( '|', '' ), $post['exclude_product_ids'] ) : '';
					$data[ $i ]['usage_limit']            = ( isset( $post['usage_limit'] ) ) ? $post['usage_limit'] : '';
					$data[ $i ]['usage_limit_per_user']   = ( isset( $post['usage_limit_per_user'] ) ) ? $post['usage_limit_per_user'] : '';
					$data[ $i ]['limit_usage_to_x_items'] = ( isset( $post['limit_usage_to_x_items'] ) ) ? $post['limit_usage_to_x_items'] : '';
					if ( empty( $post['expiry_date'] ) && ! empty( $post['sc_coupon_validity'] ) && ! empty( $post['validity_suffix'] ) ) {
						$data[ $i ]['expiry_date'] = gmdate( 'Y-m-d', $this->strtotime( '+' . $post['sc_coupon_validity'] . ' ' . $post['validity_suffix'] ) + $this->wc_timezone_offset() );
					} else {
						$data[ $i ]['expiry_date'] = $post['expiry_date'];
					}
					$data[ $i ]['free_shipping']                       = ( isset( $post['free_shipping'] ) ) ? 'yes' : 'no';
					$data[ $i ]['product_categories']                  = ( isset( $post['product_categories'] ) ) ? implode( '|', $post['product_categories'] ) : '';
					$data[ $i ]['exclude_product_categories']          = ( isset( $post['exclude_product_categories'] ) ) ? implode( '|', $post['exclude_product_categories'] ) : '';
					$data[ $i ]['exclude_sale_items']                  = ( isset( $post['exclude_sale_items'] ) ) ? 'yes' : 'no';
					$data[ $i ]['minimum_amount']                      = ( isset( $post['minimum_amount'] ) ) ? $post['minimum_amount'] : '';
					$data[ $i ]['maximum_amount']                      = ( isset( $post['maximum_amount'] ) ) ? $post['maximum_amount'] : '';
					$data[ $i ]['customer_email']                      = ( ! empty( $customer_emails ) ) ? $customer_emails[ $i ] : '';
					$data[ $i ]['sc_coupon_validity']                  = ( isset( $post['sc_coupon_validity'] ) ) ? $post['sc_coupon_validity'] : '';
					$data[ $i ]['validity_suffix']                     = ( isset( $post['validity_suffix'] ) ) ? $post['validity_suffix'] : '';
					$data[ $i ]['is_pick_price_of_product']            = ( isset( $post['is_pick_price_of_product'] ) ) ? 'yes' : 'no';
					$data[ $i ]['sc_disable_email_restriction']        = ( isset( $post['sc_disable_email_restriction'] ) ) ? 'yes' : 'no';
					$data[ $i ]['sc_is_visible_storewide']             = ( isset( $post['sc_is_visible_storewide'] ) ) ? 'yes' : 'no';
					$data[ $i ]['coupon_title_prefix']                 = ( isset( $post['coupon_title_prefix'] ) ) ? $post['coupon_title_prefix'] : '';
					$data[ $i ]['coupon_title_suffix']                 = ( isset( $post['coupon_title_suffix'] ) ) ? $post['coupon_title_suffix'] : '';
					$data[ $i ]['sc_restrict_to_new_user']             = ( isset( $post['sc_restrict_to_new_user'] ) ) ? $post['sc_restrict_to_new_user'] : '';
					$data[ $i ]['post_status']                         = 'publish';
					$data[ $i ]['post_excerpt']                        = ( isset( $post['excerpt'] ) ) ? $post['excerpt'] : '';
					$data[ $i ]['wc_sc_max_discount']                  = ( isset( $post['wc_sc_max_discount'] ) ) ? $post['wc_sc_max_discount'] : '';
					$data[ $i ]['wc_sc_expiry_time']                   = ( isset( $post['wc_sc_expiry_time'] ) ) ? $post['wc_sc_expiry_time'] : '';
					$data[ $i ]['wc_sc_product_attribute_ids']         = ( isset( $post['wc_sc_product_attribute_ids'] ) ) ? implode( '|', $post['wc_sc_product_attribute_ids'] ) : '';
					$data[ $i ]['wc_sc_exclude_product_attribute_ids'] = ( isset( $post['wc_sc_exclude_product_attribute_ids'] ) ) ? implode( '|', $post['wc_sc_exclude_product_attribute_ids'] ) : '';
					$data[ $i ]['sc_coupon_category']                  = ( isset( $post['tax_input']['sc_coupon_category'] ) ) ? implode( '|', $post['tax_input']['sc_coupon_category'] ) : '';

					$data[ $i ] = apply_filters( 'sc_generate_coupon_meta', $data[ $i ], $post );

					if ( false === $refresh_global_coupons && 'yes' === $data[ $i ]['sc_is_visible_storewide'] ) {
						$refresh_global_coupons = true;
					}
				}

				if ( true === $refresh_global_coupons ) {
					delete_option( 'sc_display_global_coupons' ); // Since there's an update in storewide coupon, refresh the global coupon's list.
				}
			}

			if ( ! empty( $get ) && isset( $get['export_coupons'] ) ) {

				$headers = array_keys( $coupon_postmeta_headers );
				if ( $this->is_wc_gte_30() ) {
					$headers[] = 'date_expires';
				}
				$headers            = esc_sql( $headers );
				$how_many_headers   = count( $headers );
				$header_placeholder = array_fill( 0, $how_many_headers, '%s' );

				$how_many_ids   = count( $post_ids );
				$id_placeholder = array_fill( 0, $how_many_ids, '%d' );

				$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len=%d', 999999 ) ); // phpcs:ignore

				$unique_post_ids = array_unique( $post_ids );

				$results = wp_cache_get( 'wc_sc_exported_coupon_data_' . implode( '_', $unique_post_ids ), 'woocommerce_smart_coupons' );

				if ( false === $results ) {
					$results = $wpdb->get_results( // phpcs:ignore
						// phpcs:disable
						$wpdb->prepare(
							"SELECT p.ID,
									p.post_title,
									p.post_excerpt,
									p.post_status,
									p.post_parent,
									p.menu_order,
									DATE_FORMAT(p.post_date,'%%d-%%m-%%Y %%H:%%i:%%s') AS post_date,
									GROUP_CONCAT(pm.meta_key order by pm.meta_id SEPARATOR '###') AS coupon_meta_key,
									GROUP_CONCAT(pm.meta_value order by pm.meta_id SEPARATOR '###') AS coupon_meta_value
								FROM {$wpdb->prefix}posts as p JOIN {$wpdb->prefix}postmeta as pm ON (p.ID = pm.post_id
									AND pm.meta_key IN (" . implode( ',', $header_placeholder ) . ') )
								WHERE p.ID IN (' . implode( ',', $id_placeholder ) . ') AND pm.meta_value IS NOT NULL
								GROUP BY p.id ORDER BY p.id',
							array_merge( $headers, $post_ids )
						),
						// phpcs:enable
						ARRAY_A
					);
					wp_cache_set( 'wc_sc_exported_coupon_data_' . implode( '_', $unique_post_ids ), $results, 'woocommerce_smart_coupons' );
					$this->maybe_add_cache_key( 'wc_sc_exported_coupon_data_' . implode( '_', $unique_post_ids ) );
				}

				foreach ( $results as $result ) {

					$coupon_meta_key   = explode( '###', $result['coupon_meta_key'] );
					$coupon_meta_value = explode( '###', $result['coupon_meta_value'] );

					unset( $result['coupon_meta_key'] );
					unset( $result['coupon_meta_value'] );

					if ( ! empty( $result['post_date'] ) ) {
						$timestamp           = $this->strtotime( $result['post_date'] ) + 1;
						$result['post_date'] = gmdate( 'd-m-Y H:i:s', $timestamp );
					}

					$id          = $result['ID'];
					$data[ $id ] = $result;

					foreach ( $coupon_meta_key as $index => $key ) {
						if ( 'product_ids' === $key || 'exclude_product_ids' === $key ) {
							$data[ $id ][ $key ] = ( isset( $coupon_meta_value[ $index ] ) ) ? str_replace( array( ',', ' ' ), array( '|', '' ), $coupon_meta_value[ $index ] ) : '';
						} elseif ( 'product_categories' === $key || 'exclude_product_categories' === $key ) {
							$data[ $id ][ $key ] = ( ! empty( $coupon_meta_value[ $index ] ) ) ? implode( '|', maybe_unserialize( stripslashes( $coupon_meta_value[ $index ] ) ) ) : '';
						} elseif ( '_used_by' === $key ) {
							if ( ! isset( $data[ $id ][ $key ] ) ) {
								$data[ $id ][ $key ] = '';
							}
							$data[ $id ][ $key ] .= '|' . $coupon_meta_value[ $index ];
							$data[ $id ][ $key ]  = trim( $data[ $id ][ $key ], '|' );
						} elseif ( 'date_expires' === $key && $this->is_wc_gte_30() ) {
							if ( ! empty( $coupon_meta_value[ $index ] ) ) {
								$data[ $id ]['expiry_date'] = gmdate( 'Y-m-d', intval( $coupon_meta_value[ $index ] ) + $this->wc_timezone_offset() );
							}
						} elseif ( 'ID' !== $key ) {
							if ( ! empty( $coupon_meta_value[ $index ] ) ) {
								if ( is_serialized( $coupon_meta_value[ $index ] ) ) {
									$temp_data         = maybe_unserialize( stripslashes( $coupon_meta_value[ $index ] ) );
									$current_temp_data = current( $temp_data );
									if ( ! is_array( $current_temp_data ) ) {
										$temp_data = implode( ',', $temp_data );
									} else {
										$temp_data = apply_filters(
											'wc_sc_export_coupon_meta_data',
											$temp_data,
											array(
												'coupon_id' => $id,
												'index'    => $index,
												'meta_key'    => $key, // phpcs:ignore
												'meta_keys' => $coupon_meta_key,
												'meta_values' => $coupon_meta_value,
											)
										);
									}
								} else {
									$temp_data = $coupon_meta_value[ $index ];
								}
								$data[ $id ][ $key ] = apply_filters(
									'wc_sc_export_coupon_meta',
									$temp_data,
									array(
										'coupon_id'   => $id,
										'index'       => $index,
										'meta_key'    => $key, // phpcs:ignore
										'meta_value'  => $coupon_meta_value[ $index ], // phpcs:ignore
										'meta_keys'   => $coupon_meta_key,
										'meta_values' => $coupon_meta_value,
									)
								);
							}
						}
					}
				}
			}

			return $data;

		}

		/**
		 * Export coupon CSV data
		 *
		 * @param array $columns_header Column header.
		 * @param array $data The data.
		 * @return array $file_data
		 */
		public function export_coupon_csv( $columns_header, $data ) {

			$getfield = '';

			foreach ( $columns_header as $key => $value ) {
				$getfield .= $key . ',';
			}

			$fields = substr_replace( $getfield, '', -1 );

			$csv_file_name = get_bloginfo( 'name' ) . gmdate( 'd-M-Y_H_i_s' ) . '.csv';

			$fields .= $this->get_coupon_csv_data( $columns_header, $data );

			$upload_dir = wp_get_upload_dir();

			$file_data                  = array();
			$file_data['wp_upload_dir'] = $upload_dir['basedir'] . '/woocommerce_uploads/';
			$file_data['file_name']     = $csv_file_name;
			$file_data['file_content']  = $fields;

			if ( isset( $upload_dir['error'] ) && ! empty( $upload_dir['error'] ) ) {
				$file_data['error'] = $upload_dir['error'];
			}

			return $file_data;
		}

		/**
		 * Export coupon CSV data
		 *
		 * @param array $columns_header Column header.
		 * @param array $data The data.
		 * @return array $file_data
		 */
		public function get_coupon_csv_data( $columns_header, $data ) {

			$each_field = array_keys( $columns_header );

			$csv_data = '';

			foreach ( (array) $data as $row ) {
				$count_columns_header = count( $columns_header );
				for ( $i = 0; $i < $count_columns_header; $i++ ) {
					if ( 0 === $i ) {
						$csv_data .= "\n";
					}

					if ( array_key_exists( $each_field[ $i ], $row ) ) {
						$row_each_field = $row[ $each_field[ $i ] ];
					} else {
						$row_each_field = '';
					}

					$array = str_replace( array( "\n", "\n\r", "\r\n", "\r" ), "\t", $row_each_field );

					$array = ( is_string( $array ) ) ? str_getcsv( $array, ',', '"', '\\' ) : array();

					$str = ( $array && is_array( $array ) ) ? implode( ', ', $array ) : '';

					$str = addslashes( $str );

					$csv_data .= '"' . $str . '",';
				}
				$csv_data = substr_replace( $csv_data, '', -1 );
			}

			return $csv_data;
		}

		/**
		 * Smart Coupons export headers
		 *
		 * @param array $coupon_postmeta_headers Existing.
		 * @return array $coupon_postmeta_headers Including additional headers.
		 */
		public function wc_smart_coupons_export_headers( $coupon_postmeta_headers = array() ) {

			$sc_postmeta_headers = array(
				'sc_coupon_validity'           => __( 'Coupon Validity', 'woocommerce-smart-coupons' ),
				'validity_suffix'              => __( 'Validity Suffix', 'woocommerce-smart-coupons' ),
				'auto_generate_coupon'         => __( 'Auto Generate Coupon', 'woocommerce-smart-coupons' ),
				'coupon_title_prefix'          => __( 'Coupon Title Prefix', 'woocommerce-smart-coupons' ),
				'coupon_title_suffix'          => __( 'Coupon Title Suffix', 'woocommerce-smart-coupons' ),
				'is_pick_price_of_product'     => __( 'Is Pick Price of Product', 'woocommerce-smart-coupons' ),
				'sc_disable_email_restriction' => __( 'Disable Email Restriction', 'woocommerce-smart-coupons' ),
				'sc_is_visible_storewide'      => __( 'Coupon Is Visible Storewide', 'woocommerce-smart-coupons' ),
				'sc_restrict_to_new_user'      => __( 'For new user only?', 'woocommerce-smart-coupons' ),
				'wc_sc_max_discount'           => __( 'Max discount', 'woocommerce-smart-coupons' ),
			);

			if ( $this->is_wc_gte_32() ) {
				$sc_postmeta_headers['wc_sc_expiry_time'] = __( 'Coupon expiry time', 'woocommerce-smart-coupons' );
			}

			return array_merge( $coupon_postmeta_headers, $sc_postmeta_headers );

		}

		/**
		 * Filter callback to replace {site_title} in email footer
		 *
		 * @param  string $string Email footer text.
		 * @return string         Email footer text with any replacements done.
		 */
		public function email_footer_replace_site_title( $string ) {
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			return str_replace( '{site_title}', $blogname, $string );
		}

		/**
		 *  Register Smart Coupons' email classes to WooCommerce's emails class list
		 *
		 * @param array $email_classes available email classes list.
		 * @return array $email_classes modified email classes list
		 */
		public function register_email_classes( $email_classes = array() ) {

			include_once 'emails/class-wc-sc-email.php';
			include_once 'emails/class-wc-sc-email-coupon.php';
			include_once 'emails/class-wc-sc-combined-email-coupon.php';
			include_once 'emails/class-wc-sc-acknowledgement-email.php';

			// Add the email class to the list of email classes that WooCommerce loads.
			$email_classes['WC_SC_Email_Coupon']          = new WC_SC_Email_Coupon();
			$email_classes['WC_SC_Combined_Email_Coupon'] = new WC_SC_Combined_Email_Coupon();
			$email_classes['WC_SC_Acknowledgement_Email'] = new WC_SC_Acknowledgement_Email();

			return $email_classes;
		}

		/**
		 * Whether to hold stock for checkout or not
		 *
		 * TODO: Rework to find perfect solution
		 *
		 * @param  boolean $is_hold Whether to hold or not.
		 * @return boolean
		 */
		public function hold_stock_for_checkout( $is_hold = true ) {
			$is_ignore = get_option( 'wc_sc_ignore_coupon_used_warning' );
			if ( 'yes' === $is_ignore ) {
				return false;
			}
			return $is_hold;
		}

		/**
		 * Function to generate a coupon
		 *
		 * @param array $args Additional data.
		 * @return string|WC_Coupon
		 */
		public function generate_coupon( $args = array() ) {
			if ( ! $this->is_wc_gte_30() ) {
				return;
			}

			$args = array_filter( $args );

			$return_type = ( ! empty( $args['return'] ) && in_array( $args['return'], array( 'code', 'object' ), true ) ) ? $args['return'] : 'object';

			$coupon = null;
			if ( ! empty( $args['coupon'] ) ) {
				if ( is_numeric( $args['coupon'] ) || is_string( $args['coupon'] ) ) {
					$coupon = new WC_Coupon( $args['coupon'] );
				} elseif ( $args['coupon'] instanceof WC_Coupon ) {
					$coupon = $args['coupon'];
				}
			} elseif ( ! empty( $args['id'] ) ) {
				$coupon = new WC_Coupon( $args['id'] );
			} elseif ( ! empty( $args['code'] ) ) {
				$coupon = new WC_Coupon( $args['code'] );
			}

			$internal_keys = array(
				'code',
				'amount',
				'discount_type',
				'description',
				'date_expires',
				'individual_use',
				'product_ids',
				'excluded_product_ids',
				'usage_limit',
				'usage_limit_per_user',
				'limit_usage_to_x_items',
				'free_shipping',
				'product_categories',
				'excluded_product_categories',
				'exclude_sale_items',
				'minimum_amount',
				'maximum_amount',
				'email_restrictions',
				'meta_data',
			);

			$email_restrictions = ( ! empty( $args['email_restrictions'] ) && count( $args['email_restrictions'] ) === 1 ) ? $args['email_restrictions'] : '';

			$new_code = '';
			if ( is_null( $coupon ) ) {
				if ( ! empty( $args['discount_type'] ) && ! empty( $args['amount'] ) ) {
					$new_code   = $this->generate_unique_code( $email_restrictions );
					$new_coupon = new WC_Coupon( $new_code );
					foreach ( $args as $key => $value ) {
						switch ( $key ) {
							case 'code':
								// do nothing.
								break;
							case 'meta_data':
								if ( is_array( $value ) && is_callable( array( $new_coupon, 'update_meta_data' ) ) ) {
									foreach ( $value as $meta ) {
										$new_coupon->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
									}
								}
								break;
							case 'description':
								$new_coupon->set_description( wp_filter_post_kses( $value ) );
								break;
							default:
								if ( is_callable( array( $new_coupon, "set_{$key}" ) ) ) {
									$new_coupon->{"set_{$key}"}( $value );
								}
								break;
						}
					}
					$new_coupon->save();
					return ( 'code' === $return_type ) ? $new_code : $new_coupon;
				} else {
					return;
				}
			} else {
				$is_auto_generate = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_meta' ) ) ) ? $coupon->get_meta( 'auto_generate_coupon' ) : 'no';
				if ( 'yes' !== $is_auto_generate ) {
					$code = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
					return ( 'code' === $return_type ) ? $code : $coupon;
				} else {
					$new_code   = $this->generate_unique_code( $email_restrictions );
					$new_coupon = new WC_Coupon( $new_code );
					foreach ( $internal_keys as $key ) {
						if ( ! is_object( $coupon ) ) {
							continue;
						}
						switch ( $key ) {
							case 'code':
								// do nothing.
								break;
							case 'meta_data':
								$meta_data = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_meta_data' ) ) ) ? $coupon->get_meta_data() : null;
								if ( ! empty( $meta_data ) ) {
									foreach ( $meta_data as $meta ) {
										if ( is_object( $meta ) && is_callable( array( $meta, 'get_data' ) ) ) {
											$data = $meta->get_data();
											if ( is_callable( array( $new_coupon, 'update_meta_data' ) ) ) {
												$new_coupon->update_meta_data( $data['key'], $data['value'] );
											}
										}
									}
								}
								break;
							case 'description':
								$description = ( is_callable( array( $coupon, 'get_description' ) ) ) ? $coupon->get_description() : '';
								if ( ! empty( $description ) && is_callable( array( $new_coupon, 'set_description' ) ) ) {
									$new_coupon->set_description( wp_filter_post_kses( $description ) );
								}
								break;
							default:
								$value = ( is_callable( array( $coupon, "get_{$key}" ) ) ) ? $coupon->{"get_{$key}"}() : '';
								if ( ! empty( $value ) && is_callable( array( $new_coupon, "set_{$key}" ) ) ) {
									$new_coupon->{"set_{$key}"}( $value );
								}
								break;
						}
					}
					$new_coupon->save();
					return ( 'code' === $return_type ) ? $new_code : $new_coupon;
				}
			}
		}

		/**
		 * Function to paint/draw coupon on a page in HTML format
		 *
		 * @param array $args Additional data including coupon info.
		 */
		public function paint_coupon( $args = array() ) {
			if ( ! $this->is_wc_gte_30() ) {
				return '';
			}
			if ( empty( $args['coupon'] ) ) {
				return '';
			}
			ob_start();

			if ( is_numeric( $args['coupon'] ) || is_string( $args['coupon'] ) ) {
				$coupon = new WC_Coupon( $args['coupon'] );
			} elseif ( $args['coupon'] instanceof WC_Coupon ) {
				$coupon = $args['coupon'];
			} else {
				return '';
			}

			$with_container = ( ! empty( $args['with_container'] ) ) ? $args['with_container'] : 'no';
			$with_css       = ( ! empty( $args['with_css'] ) ) ? $args['with_css'] : 'no';

			$design                  = get_option( 'wc_sc_setting_coupon_design', 'basic' );
			$background_color        = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
			$foreground_color        = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );
			$third_color             = get_option( 'wc_sc_setting_coupon_third_color', '#39cccc' );
			$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
			$coupon_id               = ( is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
			$coupon_code             = ( is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			$coupon_description      = ( is_callable( array( $coupon, 'description' ) ) ) ? $coupon->get_description() : '';
			$coupon_amount           = ( is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
			$is_free_shipping        = ( is_callable( array( $coupon, 'get_free_shipping' ) ) ) ? wc_bool_to_string( $coupon->get_free_shipping() ) : 'no';
			$expiry_date             = ( is_callable( array( $coupon, 'get_date_expires' ) ) ) ? $coupon->get_date_expires() : null;

			$is_callable_coupon_get_meta = $this->is_callable( $coupon, 'get_meta' );

			$coupon_data = $this->get_coupon_meta_data( $coupon );
			$coupon_type = ( ! empty( $coupon_data['coupon_type'] ) ) ? $coupon_data['coupon_type'] : '';

			if ( 'yes' === $is_free_shipping ) {
				if ( ! empty( $coupon_type ) ) {
					$coupon_type .= __( ' & ', 'woocommerce-smart-coupons' );
				}
				$coupon_type .= __( 'Free Shipping', 'woocommerce-smart-coupons' );
			}

			$coupon_description = ( 'yes' === $show_coupon_description ) ? $coupon_description : '';

			$is_percent = $this->is_percent_coupon( array( 'coupon_object' => $coupon ) );

			if ( $expiry_date instanceof WC_DateTime ) {
				$expiry_date = ( is_callable( array( $expiry_date, 'getTimestamp' ) ) ) ? $expiry_date->getTimestamp() : null;
			} elseif ( ! is_int( $expiry_date ) ) {
				$expiry_date = $this->strtotime( $expiry_date );
			}

			if ( ! empty( $expiry_date ) && is_int( $expiry_date ) ) {
				$expiry_time = ( $this->is_callable( $coupon, 'get_meta' ) ) ? (int) $coupon->get_meta( 'wc_sc_expiry_time' ) : (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
				if ( ! empty( $expiry_time ) ) {
					$expiry_date += $expiry_time; // Adding expiry time to expiry date.
				}
			}

			$args = array(
				'coupon_object'      => $coupon,
				'coupon_amount'      => $coupon_amount,
				'amount_symbol'      => ( true === $is_percent ) ? '%' : get_woocommerce_currency_symbol(),
				'discount_type'      => wp_strip_all_tags( $coupon_type ),
				'coupon_description' => ( ! empty( $coupon_description ) ) ? $coupon_description : wp_strip_all_tags( $this->generate_coupon_description( array( 'coupon_object' => $coupon ) ) ),
				'coupon_code'        => $coupon_code,
				'coupon_expiry'      => ( ! empty( $expiry_date ) ) ? $this->get_expiration_format( $expiry_date ) : __( 'Never expires', 'woocommerce-smart-coupons' ),
				'thumbnail_src'      => $this->get_coupon_design_thumbnail_src(
					array(
						'design'        => $design,
						'coupon_object' => $coupon,
					)
				),
				'classes'            => 'apply_coupons_credits',
				'template_id'        => $design,
				'is_percent'         => $is_percent,
			);

			if ( 'yes' === $with_css ) {
				?>
				<div>
					<style type="text/css">
						:root {
							--sc-color1: <?php echo esc_html( $background_color ); ?>;
							--sc-color2: <?php echo esc_html( $foreground_color ); ?>;
							--sc-color3: <?php echo esc_html( $third_color ); ?>;
						}
					</style>
					<style type="text/css"><?php echo esc_html( wp_strip_all_tags( $this->get_coupon_styles( $design ), true ) ); // phpcs:ignore ?></style>
				<?php
			}

			if ( 'yes' === $with_container ) {
				?>
					<div id="sc-cc">
						<div class="sc-coupons-list">
				<?php
			}

			wc_get_template( 'coupon-design/' . $design . '.php', $args, '', plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/' );

			if ( 'yes' === $with_container ) {
				?>
						</div>
					</div>
				<?php
			}

			if ( 'yes' === $with_css ) {
				?>
				</div>
				<?php
			}

			$html = apply_filters( 'wc_sc_coupon_html', ob_get_clean(), array_merge( $args, array( 'source' => $this ) ) );

			echo $html; // phpcs:ignore
		}

		/**
		 * Add Smart Coupons' REST API Controllers
		 *
		 * @param array $namespaces Existing namespaces.
		 * @return array
		 */
		public function rest_namespace( $namespaces = array() ) {
			include_once 'class-wc-sc-rest-coupons-controller.php';
			$namespaces['wc/v3/sc'] = array(
				'coupons' => 'WC_SC_REST_Coupons_Controller',
			);
			return $namespaces;
		}

		/**
		 * Get coupon column headers
		 *
		 * @return array
		 */
		public function get_coupon_column_headers() {

			$coupon_posts_headers = array(
				'post_title'   => __( 'Coupon Code', 'woocommerce-smart-coupons' ),
				'post_excerpt' => __( 'Post Excerpt', 'woocommerce-smart-coupons' ),
				'post_status'  => __( 'Post Status', 'woocommerce-smart-coupons' ),
				'post_parent'  => __( 'Post Parent', 'woocommerce-smart-coupons' ),
				'menu_order'   => __( 'Menu Order', 'woocommerce-smart-coupons' ),
				'post_date'    => __( 'Post Date', 'woocommerce-smart-coupons' ),
			);

			$coupon_postmeta_headers = apply_filters(
				'wc_smart_coupons_export_headers',
				array(
					'discount_type'              => __( 'Discount Type', 'woocommerce-smart-coupons' ),
					'coupon_amount'              => __( 'Coupon Amount', 'woocommerce-smart-coupons' ),
					'free_shipping'              => __( 'Free shipping', 'woocommerce-smart-coupons' ),
					'expiry_date'                => __( 'Expiry date', 'woocommerce-smart-coupons' ),
					'minimum_amount'             => __( 'Minimum Spend', 'woocommerce-smart-coupons' ),
					'maximum_amount'             => __( 'Maximum Spend', 'woocommerce-smart-coupons' ),
					'individual_use'             => __( 'Individual USe', 'woocommerce-smart-coupons' ),
					'exclude_sale_items'         => __( 'Exclude Sale Items', 'woocommerce-smart-coupons' ),
					'product_ids'                => __( 'Product IDs', 'woocommerce-smart-coupons' ),
					'exclude_product_ids'        => __( 'Exclude product IDs', 'woocommerce-smart-coupons' ),
					'product_categories'         => __( 'Product categories', 'woocommerce-smart-coupons' ),
					'exclude_product_categories' => __( 'Exclude Product categories', 'woocommerce-smart-coupons' ),
					'customer_email'             => __( 'Customer Email', 'woocommerce-smart-coupons' ),
					'usage_limit'                => __( 'Usage Limit', 'woocommerce-smart-coupons' ),
					'usage_limit_per_user'       => __( 'Usage Limit Per User', 'woocommerce-smart-coupons' ),
					'limit_usage_to_x_items'     => __( 'Limit Usage to X Items', 'woocommerce-smart-coupons' ),
					'usage_count'                => __( 'Usage Count', 'woocommerce-smart-coupons' ),
					'_used_by'                   => __( 'Used By', 'woocommerce-smart-coupons' ),
					'sc_restrict_to_new_user'    => __( 'For new user only?', 'woocommerce-smart-coupons' ),
				)
			);

			$coupon_term_headers = array(
				'sc_coupon_category' => __( 'Coupon Category', 'woocommerce-smart-coupons' ),
			);

			return array(
				'posts_headers'    => $coupon_posts_headers,
				'postmeta_headers' => $coupon_postmeta_headers,
				'term_headers'     => $coupon_term_headers,
			);
		}

		/**
		 * Write to file after exporting
		 *
		 * @param array $post POST.
		 * @param array $get GET.
		 * @param array $post_ids Post ids.
		 */
		public function export_coupon( $post = array(), $get = array(), $post_ids = array() ) {
			// Run a capability check before attempting to export coupons.
			if ( ! is_admin() && ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			$coupon_column_headers   = $this->get_coupon_column_headers();
			$coupon_posts_headers    = $coupon_column_headers['posts_headers'];
			$coupon_postmeta_headers = $coupon_column_headers['postmeta_headers'];

			$column_headers = array_merge( $coupon_posts_headers, $coupon_postmeta_headers );

			if ( ! empty( $post ) ) {
				$data = $this->generate_coupons_code( $post, '', '', array() );
			} elseif ( ! empty( $get ) ) {
				$data = $this->generate_coupons_code( '', $get, $post_ids, $coupon_postmeta_headers );
			}

			$file_data = $this->export_coupon_csv( $column_headers, $data );

			if ( ( isset( $post['generate_and_import'] ) && ! empty( $post['smart_coupons_generate_action'] ) && 'sc_export_and_import' === $post['smart_coupons_generate_action'] ) || isset( $get['export_coupons'] ) ) {

				if ( ob_get_level() ) {
					$levels = ob_get_level();
					for ( $i = 0; $i < $levels; $i++ ) {
						ob_end_clean();
					}
				} else {
					ob_end_clean();
				}
				nocache_headers();
				header( 'X-Robots-Tag: noindex, nofollow', true );
				header( 'Content-Type: text/x-csv; charset=UTF-8' );
				header( 'Content-Description: File Transfer' );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $file_data['file_name'] ) . '";' );

				echo $file_data['file_content']; // phpcs:ignore
				exit;
			} else {

				// Proceed only if there is no directory permission related issue.
				if ( ! isset( $file_data['error'] ) ) {
					// Create CSV file.
					$csv_folder  = $file_data['wp_upload_dir'];
					$filename    = str_replace( array( '\'', '"', ',', ';', '<', '>', '/', ':' ), '', $file_data['file_name'] );
					$csvfilename = $csv_folder . $filename;
					$fp          = fopen( $csvfilename, 'w' ); // phpcs:ignore
					if ( false !== $fp ) {
                        fwrite( $fp , $file_data['file_content'] ); // phpcs:ignore
                        fclose( $fp ); // phpcs:ignore
					}

					return $csvfilename;
				}
			}

		}

		/**
		 * Function to enqueue additional styles & scripts for Smart Coupons in admin
		 */
		public function smart_coupon_styles_and_scripts() {
			global $post, $pagenow;

			if ( ! empty( $pagenow ) ) {
				$show_css_for_smart_coupon_tab = false;
				$get_post_type                 = ( ! empty( $post->ID ) ) ? $this->get_post_type( $post->ID ) : ( ( ! empty( $_GET['post_type'] ) ) ? wc_clean( wp_unslash( $_GET['post_type'] ) ) : '' ); // phpcs:ignore
				$get_page                      = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
				if ( ( 'edit.php' === $pagenow || 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && in_array( $get_post_type, array( 'shop_coupon', 'product', 'product-variation' ), true ) ) {
					$show_css_for_smart_coupon_tab = true;
				}
				if ( 'admin.php' === $pagenow && in_array( $get_page, array( 'wc-smart-coupons', 'wc-orders' ), true ) ) {
					$show_css_for_smart_coupon_tab = true;
				}
				if ( $show_css_for_smart_coupon_tab ) {
					if ( ! wp_style_is( 'smart-coupon' ) ) {
						wp_enqueue_style( 'smart-coupon' );
					}
					$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
					wp_register_style( 'smart-coupons-admin', untrailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . '/assets/css/smart-coupons-admin' . $suffix . '.css', array(), $this->plugin_data['Version'] );
					wp_enqueue_style( 'smart-coupons-admin' );
				}
			}

			if ( ! empty( $post->post_type ) && 'product' === $post->post_type ) {
				if ( wp_script_is( 'select2' ) ) {
					wp_localize_script(
						'select2',
						'smart_coupons_select_params',
						array(
							'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce-smart-coupons' ),
							'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce-smart-coupons' ),
							'ajax_url'                  => admin_url( 'admin-ajax.php' ),
							'search_products_nonce'     => wp_create_nonce( 'search-products' ),
							'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
						)
					);
				}
			}

		}

		/**
		 * Add if cache key doesn't exists
		 *
		 * @param  string $key The cache key.
		 */
		public function maybe_add_cache_key( $key = '' ) {
			if ( ! empty( $key ) ) {
				$all_cache_key = get_option( 'wc_sc_all_cache_key' );
				if ( false !== $all_cache_key ) {
					if ( empty( $all_cache_key ) || ! is_array( $all_cache_key ) ) {
						$all_cache_key = array();
					}
					if ( ! in_array( $key, $all_cache_key, true ) ) {
						$all_cache_key[] = $key;
						update_option( 'wc_sc_all_cache_key', $all_cache_key, 'no' );
					}
				}
			}
		}

		/**
		 * Make meta data of this plugin, protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key the meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_sc_meta_protected( $protected, $meta_key, $meta_type ) {
			$sc_meta = array(
				'auto_generate_coupon',
				'coupon_sent',
				'coupon_title_prefix',
				'coupon_title_suffix',
				'generated_from_order_id',
				'gift_receiver_email',
				'gift_receiver_message',
				'gift_sending_timestamp',
				'is_gift',
				'is_pick_price_of_product',
				'sc_called_credit_details',
				'sc_coupon_receiver_details',
				'sc_coupon_validity',
				'sc_disable_email_restriction',
				'sc_is_visible_storewide',
				'send_coupons_on_renewals',
				'smart_coupons_contribution',
				'temp_gift_card_receivers_emails',
				'validity_suffix',
				'sc_restrict_to_new_user',
				'wc_sc_schedule_gift_sending',
				'wc_sc_max_discount',
				'wc_sc_expiry_time',
				'wc_sc_product_attribute_ids',
				'wc_sc_exclude_product_attribute_ids',
			);
			if ( in_array( $meta_key, $sc_meta, true ) ) {
				return true;
			}
			return $protected;
		}

		/**
		 * Get the order from the PayPal 'Custom' variable.
		 *
		 * Credit: WooCommerce
		 *
		 * @param  string $raw_custom JSON Data passed back by PayPal.
		 * @return bool|WC_Order object
		 */
		public function get_paypal_order( $raw_custom ) {

			if ( ! class_exists( 'WC_Gateway_Paypal' ) ) {
				include_once WC()->plugin_path() . '/includes/gateways/paypal/class-wc-gateway-paypal.php';
			}
			// We have the data in the correct format, so get the order.
			if ( ( $custom = json_decode( $raw_custom ) ) && is_object( $custom ) ) { // phpcs:ignore
				$order_id  = $custom->order_id;
				$order_key = $custom->order_key;

				// Fallback to serialized data if safe. This is @deprecated in 2.3.11.
			} elseif ( preg_match( '/^a:2:{/', $raw_custom ) && ! preg_match( '/[CO]:\+?[0-9]+:"/', $raw_custom ) && ( $custom = maybe_unserialize( $raw_custom ) ) ) { // phpcs:ignore
				$order_id  = $custom[0];
				$order_key = $custom[1];

				// Nothing was found.
			} else {
				WC_Gateway_Paypal::log( 'Error: Order ID and key were not found in "custom".' );
				return false;
			}

			if ( ! $order = wc_get_order( $order_id ) ) { // phpcs:ignore
				// We have an invalid $order_id, probably because invoice_prefix has changed.
				$order_id = wc_get_order_id_by_order_key( $order_key );
				$order    = wc_get_order( $order_id );
			}

			if ( $this->is_wc_gte_30() ) {
				$_order_key = ( ! empty( $order ) && is_callable( array( $order, 'get_order_key' ) ) ) ? $order->get_order_key() : '';
			} else {
				$_order_key = ( ! empty( $order->order_key ) ) ? $order->order_key : '';
			}

			if ( ! $order || $_order_key !== $order_key ) {
				WC_Gateway_Paypal::log( 'Error: Order Keys do not match.' );
				return false;
			}

			return $order;
		}

		/**
		 * Get all coupon styles
		 *
		 * @return array
		 */
		public function get_wc_sc_coupon_styles() {

			$all_styles = array(
				'inner'         => __( 'Style 1', 'woocommerce-smart-coupons' ),
				'round-corner'  => __( 'Style 2', 'woocommerce-smart-coupons' ),
				'round-dashed'  => __( 'Style 3', 'woocommerce-smart-coupons' ),
				'outer-dashed'  => __( 'Style 4', 'woocommerce-smart-coupons' ),
				'left'          => __( 'Style 5', 'woocommerce-smart-coupons' ),
				'bottom'        => __( 'Style 6', 'woocommerce-smart-coupons' ),
				'custom-design' => __( 'Custom Style', 'woocommerce-smart-coupons' ),
			);

			return apply_filters( 'wc_sc_get_wc_sc_coupon_styles', $all_styles );

		}

		/**
		 * Get coupon display styles
		 *
		 * @param  string $style_name The style name.
		 * @param  array  $args Additional arguments.
		 * @return string
		 */
		public function get_coupon_styles( $style_name = '', $args = array() ) {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$is_email = ( ! empty( $args['is_email'] ) ) ? $args['is_email'] : 'no';

			ob_start();

			if ( 'custom-design' === $style_name ) {
				$custom_design_css = get_option( 'wc_sc_custom_design_css', '' );
				echo $custom_design_css;  // phpcs:ignore
			} elseif ( 'email-coupon' === $style_name ) {
				$file = trailingslashit( WP_PLUGIN_DIR . '/' . WC_SC_PLUGIN_DIRNAME ) . 'assets/css/wc-sc-style-' . $style_name . $suffix . '.css';
				if ( file_exists( $file ) ) {
					include $file;
				} else {
					/* translators: File path */
					$this->log( 'error', sprintf( __( 'File not found %s', 'woocommerce-smart-coupons' ), '<code>' . $file . '</code>' ) . ' ' . __FILE__ . ' ' . __LINE__ );
				}
			} else {
				$file = trailingslashit( WP_PLUGIN_DIR . '/' . WC_SC_PLUGIN_DIRNAME ) . 'assets/css/smart-coupon-designs.css';
				if ( file_exists( $file ) ) {
					include $file;
				} else {
					/* translators: File path */
					$this->log( 'error', sprintf( __( 'File not found %s', 'woocommerce-smart-coupons' ), '<code>' . $file . '</code>' ) . ' ' . __FILE__ . ' ' . __LINE__ );
				}
			}

			$styles = ob_get_clean();

			if ( 'yes' === $is_email ) {
				$styles = str_replace( array( ':before', ':hover', ':focus', ':active' ), array( '-pseudo-before', '-pseudo-hover', '-pseudo-focus', '-pseudo-active' ), $styles );
			}

			return apply_filters( 'wc_sc_get_coupon_styles', $styles, $style_name, $args );

		}

		/**
		 * Insert a setting or an array of settings after another specific setting by its ID.
		 *
		 * @since 1.2.1
		 * @param array  $settings                The original list of settings.
		 * @param string $insert_after_setting_id The setting id to insert the new setting after.
		 * @param array  $new_setting             The new setting to insert. Can be a single setting or an array of settings.
		 * @param string $insert_type             The type of insert to perform. Can be 'single_setting' or 'multiple_settings'. Optional. Defaults to a single setting insert.
		 *
		 * @credit: WooCommerce Subscriptions
		 */
		public static function insert_setting_after( &$settings, $insert_after_setting_id, $new_setting, $insert_type = 'single_setting' ) {
			if ( ! is_array( $settings ) ) {
				return;
			}

			$original_settings = $settings;
			$settings          = array();

			foreach ( $original_settings as $setting ) {
				$settings[] = $setting;

				if ( isset( $setting['id'] ) && $insert_after_setting_id === $setting['id'] ) {
					if ( 'single_setting' === $insert_type ) {
						$settings[] = $new_setting;
					} else {
						$settings = array_merge( $settings, $new_setting );
					}
				}
			}
		}

		/**
		 * To generate unique id
		 *
		 * Credit: WooCommerce
		 */
		public function generate_unique_id() {

			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$hasher = new PasswordHash( 8, false );
			return md5( $hasher->get_random_bytes( 32 ) );

		}

		/**
		 * To get cookie life
		 */
		public function get_cookie_life() {

			$life = get_option( 'wc_sc_coupon_cookie_life', 180 );

			return apply_filters( 'wc_sc_coupon_cookie_life', time() + ( 60 * 60 * 24 * $life ) );

		}

		/**
		 * Show notice on admin panel about minimum required version of WooCommerce
		 */
		public function minimum_woocommerce_version_requirement() {
			if ( $this->is_wc_gte_30() ) {
				return;
			}

			$plugin_data = self::get_smart_coupons_plugin_data();
			$plugin_name = $plugin_data['Name'];
			?>
			<div class="updated error">
				<p>
				<?php
					echo '<strong>' . esc_html__( 'Important', 'woocommerce-smart-coupons' ) . ':</strong> ' . esc_html( $plugin_name ) . ' ' . esc_html__( 'is active but it will only work with WooCommerce 3.0.0+.', 'woocommerce-smart-coupons' ) . ' <a href="' . esc_url( admin_url( 'plugins.php?plugin_status=upgrade' ) ) . '" target="_blank" >' . esc_html__( 'Please update WooCommerce to the latest version', 'woocommerce-smart-coupons' ) . '</a>.';
				?>
				</p>
			</div>
			<?php
		}

		/**
		 * Function to fetch plugin's data
		 */
		public static function get_smart_coupons_plugin_data() {
			return get_plugin_data( WC_SC_PLUGIN_FILE );
		}

		/**
		 * Function to get plugin's version
		 */
		public function get_smart_coupons_version() {
			$plugin_data = self::get_smart_coupons_plugin_data();
			return isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : false;
		}


		/**
		 * Function to get singular/plural name for store credit
		 */
		public function define_label_for_store_credit() {
			global $store_credit_label;

			if ( empty( $store_credit_label ) || ! is_array( $store_credit_label ) ) {
				$store_credit_label = array();
			}

			if ( empty( $store_credit_label['singular'] ) ) {
				$store_credit_label['singular'] = get_option( 'sc_store_credit_singular_text' );
			}

			if ( empty( $store_credit_label['plural'] ) ) {
				$store_credit_label['plural'] = get_option( 'sc_store_credit_plural_text' );
			}

		}

		/**
		 * Function to get length of auto generated coupon code
		 */
		public function get_coupon_code_length() {
			$coupon_code_length = get_option( 'wc_sc_coupon_code_length' );
			return ! empty( $coupon_code_length ) ? $coupon_code_length : 13; // Default coupon code length is 13.
		}

		/**
		 * Function to get coupon codes used in an order
		 *
		 * @param mixed $order Order object or order ID.
		 * @return array $coupon_codes coupon codes used in order.
		 */
		public function get_coupon_codes( $order = '' ) {
			$coupon_codes = array();
			if ( ! empty( $order ) ) {
				// Try to load order using ID.
				if ( is_int( $order ) ) {
					$order = wc_get_order( $order );
				}

				if ( is_a( $order, 'WC_Order' ) ) {
					if ( $this->is_wc_gte_37() ) {
						$coupon_codes = is_callable( array( $order, 'get_coupon_codes' ) ) ? $order->get_coupon_codes() : array();
					} else {
						$coupon_codes = is_callable( array( $order, 'get_used_coupons' ) ) ? $order->get_used_coupons() : array();
					}
				}
			}
			return $coupon_codes;
		}

		/**
		 * Function to get default CSS for custom coupon design
		 *
		 * @return string $default_css Default custom CSS.
		 */
		public function get_custom_design_default_css() {
			$default_css = '/* Coupon style for custom-design */
.coupon-container.custom-design {
    background: #39cccc;
}

.coupon-container.custom-design .coupon-content {
    border: solid 1px lightgrey;
    color: #30050b;
}';
			return apply_filters( 'wc_sc_coupon_custom_design_default_css', $default_css );
		}

		/**
		 * Function to check if coupon email is enabled or not
		 *
		 * TODO: Can be removed in future
		 *
		 * @param  string $template The template's setting to check for.
		 * @return boolean $is_email_enabled Is email enabled
		 */
		public function is_email_template_enabled( $template = 'send' ) {

			if ( 'combine' === $template ) {
				$wc_email_settings_key = 'woocommerce_wc_sc_combined_email_coupon_settings';
				$sc_email_setting_key  = 'smart_coupons_combine_emails';
				$default               = 'no';
			} else {
				$wc_email_settings_key = 'woocommerce_wc_sc_email_coupon_settings';
				$sc_email_setting_key  = 'smart_coupons_is_send_email';
				$default               = 'yes';
			}

			$is_email_enabled = '';

			$wc_email_settings = get_option( $wc_email_settings_key );

			// If setting is not found in WC Email settings fetch it from SC admin settings.
			if ( false === $wc_email_settings ) {
				$is_email_enabled = get_option( $sc_email_setting_key, $default );
			} elseif ( is_array( $wc_email_settings ) && ! empty( $wc_email_settings ) ) {
				$is_email_enabled = ( isset( $wc_email_settings['enabled'] ) && ! empty( $wc_email_settings['enabled'] ) ) ? $wc_email_settings['enabled'] : $default;
			}

			return $is_email_enabled;
		}

		/**
		 * Function to check if store credit discount is inclusive of tax.
		 *
		 * @return string $sc_include_tax Is store credit includes tax
		 */
		public function is_store_credit_include_tax() {

			$sc_include_tax     = 'no';
			$prices_include_tax = wc_prices_include_tax();

			// Discount can only be inclusive of tax if prices are inclusive of tax and apply before tax is enabled.
			if ( true === $prices_include_tax ) {
				$apply_before_tax = get_option( 'woocommerce_smart_coupon_apply_before_tax', 'no' );
				if ( 'yes' === $apply_before_tax ) {
					// Get SC setting for include tax.
					$sc_include_tax = get_option( 'woocommerce_smart_coupon_include_tax', 'no' );
				}
			}
			return $sc_include_tax;
		}

		/**
		 * Whether to generate store credit including tax amount or not
		 *
		 * @return boolean
		 */
		public function is_generated_store_credit_includes_tax() {
			$is_include_tax = get_option( 'wc_sc_generated_store_credit_includes_tax', 'no' );
			return apply_filters( 'wc_sc_is_generated_store_credit_includes_tax', wc_string_to_bool( $is_include_tax ), array( 'source' => $this ) );
		}

		/**
		 * Get emoji
		 *
		 * @return string
		 */
		public function get_emoji() {
			$emojis = array(
				11088  => '⭐',
				127775 => '🌟',
				127873 => '🎁',
				127881 => '🎉',
				127882 => '🎊',
				127941 => '🏅',
				127942 => '🏆',
				127991 => '🏷',
				128075 => '👋',
				128076 => '👌',
				128077 => '👍',
				128079 => '👏',
				128081 => '👑',
				128142 => '💎',
				128165 => '💥',
				128276 => '🔔',
				128293 => '🔥',
				128640 => '🚀',
				129311 => '🤟',
				129321 => '🤩',
			);
			$key    = array_rand( $emojis );
			return $emojis[ $key ];
		}

		/**
		 * Get coupon titles for product
		 *
		 * @param array $args Additional data.
		 * @return array
		 */
		public function get_coupon_titles( $args = array() ) {
			$coupon_titles = array();
			if ( empty( $args ) ) {
				return $coupon_titles;
			}
			$product = ( ! empty( $args['product_object'] ) && is_a( $args['product_object'], 'WC_Product' ) ) ? $args['product_object'] : null;
			if ( is_null( $product ) ) {
				return $coupon_titles;
			}
			$coupon_titles = ( is_callable( array( $product, 'get_meta' ) ) ) ? $product->get_meta( '_coupon_title' ) : array();
			if ( empty( $coupon_titles ) ) {
				$parent_id = ( is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0;
				if ( empty( $parent_id ) ) {
					return array();
				}
				$parent_product = ( function_exists( 'wc_get_product' ) ) ? wc_get_product( $parent_id ) : null;
				$coupon_titles  = ( $this->is_callable( $parent_product, 'get_meta' ) ) ? $parent_product->get_meta( '_coupon_title' ) : $this->get_post_meta( $parent_id, '_coupon_title', true );
			}
			if ( empty( $coupon_titles ) && ! is_array( $coupon_titles ) ) {
				return array();
			}
			return $coupon_titles;
		}

		/**
		 * Function to copy coupon meta data and save to new coupon.
		 *
		 * @param  array $coupon_data  Array of new coupon id and old coupon object.
		 * @param  array $meta_keys   Meta keys.
		 */
		public function copy_coupon_meta_data( $coupon_data = array(), $meta_keys = array() ) {

			$new_coupon_id = ( ! empty( $coupon_data['new_coupon_id'] ) ) ? absint( $coupon_data['new_coupon_id'] ) : 0;
			$coupon        = ( ! empty( $coupon_data['ref_coupon'] ) ) ? $coupon_data['ref_coupon'] : false;

			if ( empty( $new_coupon_id ) || empty( $coupon ) ) {
				return;
			}

			if ( ! empty( $new_coupon_id ) && is_array( $meta_keys ) && ! empty( $meta_keys ) ) {
				$new_coupon                         = new WC_Coupon( $new_coupon_id );
				$is_callable_new_coupon_update_meta = $this->is_callable( $new_coupon, 'update_meta_data' );
				$is_callable_coupon_get_meta        = $this->is_callable( $coupon, 'get_meta' );
				// Save each meta to new coupon.
				foreach ( $meta_keys as $meta_key ) {
					$update = false;
					if ( $this->is_wc_gte_30() ) {
						$meta_value = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( $meta_key ) : '';
						$update     = true;
					} else {
						$old_coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						if ( ! empty( $old_coupon_id ) ) { // This will confirm that the coupon exists.
							$meta_value = get_post_meta( $old_coupon_id, $meta_key, true );
							$update     = true;
						}
					}
					if ( true === $update ) {
						if ( true === $is_callable_new_coupon_update_meta ) {
							$new_coupon->update_meta_data( $meta_key, $meta_value );
						} else {
							update_post_meta( $new_coupon_id, $meta_key, $meta_value );
						}
					}
				}
				if ( $this->is_callable( $new_coupon, 'save' ) ) {
					$new_coupon->save();
				}
			}
		}

		/**
		 * Check given coupon exists.
		 *
		 * @param string $coupon_code Coupon code.
		 * @return bool
		 *
		 * Credit: WooCommerce
		 */
		public function sc_coupon_exists( $coupon_code = '' ) {
			if ( empty( $coupon_code ) ) {
				return false;
			}
			$coupon = new WC_Coupon( $coupon_code );
			return (bool) $coupon->get_id() || $coupon->get_virtual();
		}

		/**
		 * Function to get pending order statuses
		 *
		 * @return array
		 */
		public function get_pending_statuses() {
			return apply_filters( 'wc_sc_pending_order_statuses', array( 'on-hold', 'auto-draft', 'pending' ), array( 'source' => $this ) );
		}

		/**
		 * Checking subtotal is eligible for free shipping method
		 *
		 * @param bool   $is_available true/false.
		 * @param array  $package Shipping package.
		 * @param object $free_shipping free shipping object.
		 * @return mixed|void
		 */
		public function is_eligible_for_free_shipping( $is_available = false, $package = array(), $free_shipping = null ) {

			// If free shipping is invalid already, no need for further checks.
			if ( false === $is_available || ! is_object( $free_shipping ) ) {
				return $is_available;
			}

			$apply_before_tax = get_option( 'woocommerce_smart_coupon_apply_before_tax', 'no' );

			if ( $this->is_wc_gte_30() && 'yes' === $apply_before_tax ) {
				return $is_available;
			}

			$has_coupon                     = false;
			$has_met_min_amount             = false;
			$has_smart_coupon               = false;
			$coupon_usable_amount           = 0;
			$coupons                        = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_coupons' ) ) ) ? WC()->cart->get_coupons() : array();
			$free_shipping_condition        = ! empty( $free_shipping->requires ) ? $free_shipping->requires : '';
			$free_shipping_ignore_discounts = ! empty( $free_shipping->ignore_discounts ) ? $free_shipping->ignore_discounts : '';
			$free_shipping_min_amount       = ! empty( $free_shipping->min_amount ) ? $free_shipping->min_amount : 0;

			if ( ! empty( $coupons ) ) {

				foreach ( $coupons as $coupon_code => $coupon ) {
					$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
					if ( 'smart_coupon' === $discount_type ) {
						$has_smart_coupon      = true;
						$coupon_usable_amount += $this->get_amount( $coupon, true );
					}
					if ( in_array( $free_shipping_condition, array( 'coupon', 'either', 'both' ), true ) ) {
						$coupon_is_valid          = $this->is_valid( $coupon );
						$coupon_get_free_shipping = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_free_shipping' ) ) ) ? $coupon->get_free_shipping() : false;
						if ( true === $coupon_is_valid && true === $coupon_get_free_shipping ) {
							$has_coupon = true;
						}
					}
				}
			}

			if ( false === $has_smart_coupon ) {
				return $is_available;
			}

			if ( in_array( $free_shipping_condition, array( 'min_amount', 'either', 'both' ), true ) ) {

				$total                        = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_displayed_subtotal' ) ) ) ? WC()->cart->get_displayed_subtotal() : 0;
				$display_prices_including_tax = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'display_prices_including_tax' ) ) ) ? WC()->cart->display_prices_including_tax() : false;

				if ( $display_prices_including_tax ) {
					$get_discount_tax = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_discount_tax' ) ) ) ? WC()->cart->get_discount_tax() : 0;
					$total            = $total - $get_discount_tax;
				}

				if ( 'no' === $free_shipping_ignore_discounts ) {
					$get_discount_total = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_discount_total' ) ) ) ? WC()->cart->get_discount_total() : 0;
					$total              = $total - $get_discount_total;
					$total              = $total - $coupon_usable_amount;
				}

				$total = round( ( $total ), get_option( 'woocommerce_price_num_decimals', 2 ) );

				if ( $total >= $free_shipping_min_amount ) {
					$has_met_min_amount = true;
				}
			}

			switch ( $free_shipping->requires ) {
				case 'min_amount':
					$is_available = $has_met_min_amount;
					break;
				case 'coupon':
					$is_available = $has_coupon;
					break;
				case 'both':
					$is_available = $has_met_min_amount && $has_coupon;
					break;
				case 'either':
					$is_available = $has_met_min_amount || $has_coupon;
					break;
				default:
					$is_available = true;
					break;
			}

			return $is_available;
		}

		/**
		 * Smart coupon system status section in wc system status.
		 *
		 * @return void
		 */
		public function smart_coupons_system_status_report() {
			$smart_coupons_settings = array();

			$max_coupon_to_show                 = get_option( 'wc_sc_setting_max_coupon_to_show' );
			$coupon_code_length                 = get_option( 'wc_sc_coupon_code_length' );
			$valid_order_statuses               = get_option( 'wc_sc_valid_order_statuses_for_coupon_auto_generation' );
			$is_include_tax                     = get_option( 'wc_sc_generated_store_credit_includes_tax' );
			$apply_before_tax                   = get_option( 'woocommerce_smart_coupon_apply_before_tax' );
			$sc_include_tax                     = get_option( 'woocommerce_smart_coupon_include_tax' );
			$is_delete_smart_coupon_after_usage = get_option( 'woocommerce_delete_smart_coupon_after_usage' );
			$is_send_email                      = get_option( 'smart_coupons_is_send_email' );
			$is_print                           = get_option( 'smart_coupons_is_print_coupon' );
			$sell_sc_at_less_price              = get_option( 'smart_coupons_sell_store_credit_at_less_price' );
			$pay_from_credit_of_original_order  = get_option( 'pay_from_smart_coupon_of_original_order' );
			$stop_recursive_coupon_generation   = get_option( 'stop_recursive_coupon_generation' );
			$is_show_coupon_receiver_form       = get_option( 'smart_coupons_display_coupon_receiver_details_form' );
			$schedule_store_credit              = get_option( 'smart_coupons_schedule_store_credit' );
			$combine_emails                     = get_option( 'smart_coupons_combine_emails' );
			$enable_taxes                       = get_option( 'woocommerce_calc_taxes' );
			$price_entered_with_tax_type        = get_option( 'woocommerce_prices_include_tax' );
			$round_at_subtotal                  = get_option( 'woocommerce_tax_round_at_subtotal' );
			$tax_display_shop                   = get_option( 'woocommerce_tax_display_shop' );
			$tax_display_cart                   = get_option( 'woocommerce_tax_display_cart' );
			$tax_total_display                  = get_option( 'woocommerce_tax_total_display' );
			$wc_enable_coupons                  = get_option( 'woocommerce_enable_coupons' );
			$calc_discounts_sequentially        = get_option( 'woocommerce_calc_discounts_sequentially' );
			$wc_sc_dashboard_endpoint           = get_option( 'woocommerce_myaccount_wc_sc_dashboard_endpoint', 'wc-smart-coupons' );

			if ( is_array( $valid_order_statuses ) && ! empty( $valid_order_statuses ) ) {
				$valid_order_statuses = implode( ', ', $valid_order_statuses );
			}

			$auto_generated_coupon_email   = $this->is_email_template_enabled();
			$combined_email_coupon_enabled = $this->is_email_template_enabled( 'combine' );

			$smart_coupons_settings = array(
				__( 'Number of coupons to show', 'woocommerce-smart-coupons' )                                                                           => $max_coupon_to_show,
				__( 'Number of characters in auto-generated coupon code', 'woocommerce-smart-coupons' )                                                  => $coupon_code_length,
				__( 'Valid order status for auto-generating coupon', 'woocommerce-smart-coupons' )                                                       => $valid_order_statuses,
				__( 'Include tax in the amount of the generated gift card', 'woocommerce-smart-coupons' )                                                => $is_include_tax,
				__( 'Deduct credit/gift before doing tax calculations', 'woocommerce-smart-coupons' )                                                    => $apply_before_tax,
				__( 'Gift Card discount is inclusive of tax', 'woocommerce-smart-coupons' )                                                              => $sc_include_tax,
				__( 'Automatic deletion', 'woocommerce-smart-coupons' )                                                                                  => $is_delete_smart_coupon_after_usage,
				__( 'Coupon emails', 'woocommerce-smart-coupons' )                                                                                       => $is_send_email,
				__( 'Printing coupons', 'woocommerce-smart-coupons' )                                                                                    => $is_print,
				__( 'Sell gift cards at less price?', 'woocommerce-smart-coupons' )                                                                      => $sell_sc_at_less_price,
				__( 'Use gift card applied in first subscription order for subsequent renewals until credit reaches zero', 'woocommerce-smart-coupons' ) => $pay_from_credit_of_original_order,
				__( 'Renewal orders should not generate coupons even when they include a product that issues coupons', 'woocommerce-smart-coupons' )     => $stop_recursive_coupon_generation,
				__( 'Allow sending of coupons to others', 'woocommerce-smart-coupons' )                                                                  => $is_show_coupon_receiver_form,
				__( 'Allow schedule sending of coupons?', 'woocommerce-smart-coupons' )                                                                  => $schedule_store_credit,
				__( 'Combine emails', 'woocommerce-smart-coupons' )                                                                                      => $combine_emails,
				__( 'Auto generated coupon email', 'woocommerce-smart-coupons' )                                                                         => $auto_generated_coupon_email,
				__( 'Combined auto generated coupons email', 'woocommerce-smart-coupons' )                                                               => $combined_email_coupon_enabled,
				__( 'Acknowledgement email', 'woocommerce-smart-coupons' )                                                                               => $auto_generated_coupon_email,
				__( 'Enable taxes', 'woocommerce-smart-coupons' )                                                                                        => $enable_taxes,
				__( 'Prices entered with tax', 'woocommerce-smart-coupons' )                                                                             => $price_entered_with_tax_type,
				__( 'Rounding', 'woocommerce-smart-coupons' )                                                                                            => $round_at_subtotal,
				__( 'Display prices in the shop', 'woocommerce-smart-coupons' )                                                                          => $tax_display_shop,
				__( 'Display prices during cart and checkout', 'woocommerce-smart-coupons' )                                                             => $tax_display_cart,
				__( 'Display tax totals', 'woocommerce-smart-coupons' )                                                                                  => $tax_total_display,
				__( 'Enable the use of coupon codes', 'woocommerce-smart-coupons' )                                                                      => $wc_enable_coupons,
				__( 'Calculate coupon discounts sequentially', 'woocommerce-smart-coupons' )                                                             => $calc_discounts_sequentially,
				__( 'Account endpoints > Coupons', 'woocommerce-smart-coupons' )                                                                         => $wc_sc_dashboard_endpoint,
			);

			?>
			<table class="wc_status_table widefat" cellspacing="0" id="wc-smart-coupons-settings">
				<thead>
				<tr>
					<th colspan="3" data-export-label="<?php echo esc_attr( __( 'Smart Coupons related settings', 'woocommerce-smart-coupons' ) ); ?>"><h2><?php esc_html_e( 'Smart Coupons related settings', 'woocommerce-smart-coupons' ); ?><?php echo wp_kses_post( wc_help_tip( __( 'This section shows settings that affects Smart Coupons\' functionalities.', 'woocommerce-smart-coupons' ) ) ); ?></h2></th>
				</tr>
				</thead>
				<tbody>
				<?php
				if ( ! empty( $smart_coupons_settings ) ) {
					foreach ( $smart_coupons_settings as $label => $value ) {
						?>
						<tr>
							<td data-export-label="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $label ); ?></td>
							<td class="help"></td>
							<td>
							<?php
								echo esc_html( $value );
							?>
							</td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
			<?php
		}

		/**
		 * An alternate(get_option()) way of fetching any option using query.
		 *
		 * @param  string $option_name Option name.
		 * @param  string $default Default value.
		 * @return string|null
		 */
		public function sc_get_option( $option_name = '', $default = 'no_default' ) {
			global $wpdb;

			if ( empty( $option_name ) ) {
				return false;
			}

			if ( 'no_default' === $default ) {
				$row = $wpdb->get_row( // phpcs:ignore
					$wpdb->prepare(
						"SELECT option_value 
							FROM {$wpdb->prefix}options 
							WHERE option_name = %s
						LIMIT %d",
						$option_name,
						1
					)
				);
			} else {
				$row = $wpdb->get_row( // phpcs:ignore
					$wpdb->prepare(
						"SELECT option_value 
							FROM {$wpdb->prefix}options 
							WHERE option_name = %s
						UNION SELECT %s
						LIMIT %d",
						$option_name,
						$default,
						1
					)
				);
			}

			return is_null( $row ) ? false : ( ( ! empty( $row->option_value ) ) ? maybe_unserialize( $row->option_value ) : '' );

		}

		/**
		 * Add total SC used in REST API shop order object.
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Response $response WP_REST_Response object.
		 * @param WC_Order         $order WC_Order object.
		 * @param WP_REST_Request  $request WP_REST_Response object.
		 * @return WP_REST_Response
		 */
		public function rest_api_prepare_shop_order_object( $response = null, $order = null, $request = null ) {
			if ( empty( $response ) || empty( $order ) ) {
				return $response;
			}

			$sc_order_fields = WC_SC_Order_Fields::get_instance();

			$total_credit_used = $sc_order_fields->get_total_credit_used_in_order( $order );

			if ( is_object( $response ) && ! empty( $response->data ) && is_array( $response->data ) ) {
				$response->data['store_credit_used'] = round( $total_credit_used, get_option( 'woocommerce_price_num_decimals', 2 ) );
			}

			return $response;
		}

		/**
		 * Function to get coupon amount considering currency
		 *
		 * @param WC_Coupon $coupon The coupon object.
		 * @param boolean   $convert Whether to convert or not.
		 * @param WC_Order  $order The order object.
		 *
		 * @throws Exception If $coupon is not an object of WC_Coupon.
		 * @return float
		 */
		public function get_amount( $coupon = null, $convert = false, $order = null ) {
			if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
				$error = __( '$coupon is not an object of WC_Coupon', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$coupon is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $coupon ) ) ? var_dump( $coupon ) : print_r( gettype( $coupon ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return floatval( 0 );
			}
			if ( $this->is_wc_gte_30() ) {
				$coupon_amount = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
			} else {
				$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
			}
			return $coupon_amount;
		}

		/**
		 * Maybe convert price read from database to current currency
		 *
		 * @param integer  $price The price to be converted.
		 * @param boolean  $convert Whether to convert or not.
		 * @param WC_Order $order The order object.
		 * @return float $price The converted price.
		 */
		public function read_price( $price = 0, $convert = false, $order = null ) {
			if ( true === $convert ) {
				$order_currency = '';
				if ( is_a( $order, 'WC_Order' ) ) {
					if ( $this->is_wc_gte_30() ) {
						$order_currency = ( is_callable( array( $order, 'get_currency' ) ) ) ? $order->get_currency() : ''; // phpcs:ignore
					} else {
						$order_currency = ( is_callable( array( $order, 'get_order_currency' ) ) ) ? $order->get_order_currency() : ''; // phpcs:ignore
					}
				}
				$current_currency = ( ! empty( $order_currency ) ) ? $order_currency : get_woocommerce_currency();
				$base_currency    = get_option( 'woocommerce_currency' );
				if ( $base_currency !== $current_currency ) {
					$price = $this->convert_price( $price, $current_currency, $base_currency );
				}
				$price = apply_filters(
					'wc_sc_read_price',
					$price,
					array(
						'source'              => $this,
						'currency_conversion' => $convert,
						'order_obj'           => $order,
					)
				);
			}
			return $price;
		}

		/**
		 * Maybe convert price to base currency before saving to the database
		 *
		 * @param integer  $price The price to be converted.
		 * @param boolean  $convert Whether to convert or not.
		 * @param WC_Order $order The order object.
		 * @return float $price The converted price.
		 */
		public function write_price( $price = 0, $convert = false, $order = null ) {
			if ( true === $convert ) {
				$order_currency = '';
				if ( is_a( $order, 'WC_Order' ) ) {
					if ( $this->is_wc_gte_30() ) {
						$order_currency = ( is_callable( array( $order, 'get_currency' ) ) ) ? $order->get_currency() : ''; // phpcs:ignore
					} else {
						$order_currency = ( is_callable( array( $order, 'get_order_currency' ) ) ) ? $order->get_order_currency() : ''; // phpcs:ignore
					}
				}
				$current_currency = ( ! empty( $order_currency ) ) ? $order_currency : get_woocommerce_currency();
				$base_currency    = get_option( 'woocommerce_currency' );
				if ( $base_currency !== $current_currency ) {
					$price = $this->convert_price( $price, $base_currency, $current_currency );
				}
				$price = apply_filters(
					'wc_sc_write_price',
					$price,
					array(
						'source'              => $this,
						'currency_conversion' => $convert,
						'order_obj'           => $order,
					)
				);
			}
			return $price;
		}

		/**
		 * Get post meta considering currency
		 *
		 * @param integer  $post_id The post id.
		 * @param string   $meta_key The meta key.
		 * @param boolean  $single Whether to get single value or not.
		 * @param boolean  $convert Whether to convert or not.
		 * @param WC_Order $order The order object.
		 *
		 * @throws Exception If Some values not passed for $post_id & $meta_key.
		 * @return mixed
		 */
		public function get_post_meta( $post_id = 0, $meta_key = '', $single = false, $convert = false, $order = null ) {
			if ( empty( $post_id ) || empty( $meta_key ) ) {
				$error = __( 'Some values required for $post_id & $meta_key', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$post_id is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $post_id ) ) ? var_dump( $post_id ) : print_r( gettype( $post_id ) ) . print_r( "\r\n" ); // phpcs:ignore
				esc_html_e( '$meta_key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $meta_key ) ) ? var_dump( $meta_key ) : print_r( gettype( $meta_key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return null;
			}
			$meta_value = '';
			$post_type  = ( $this->is_callable( $this, 'get_post_type' ) ) ? $this->get_post_type( $post_id ) : '';
			if ( in_array( $post_type, array( 'product', 'product_variation', 'shop_coupon', 'shop_order' ), true ) ) {
				$object     = null;
				$use_getter = false;
				$post_id    = absint( $post_id );
				switch ( $post_type ) {
					case 'product':
					case 'product_variation':
						$object = ( function_exists( 'wc_get_product' ) ) ? wc_get_product( $post_id ) : null;
						break;
					case 'shop_coupon':
						$object            = new WC_Coupon( $post_id );
						$meta_key_to_props = array(
							'coupon_amount'  => 'amount',
							'customer_email' => 'email_restrictions',
							'date_expires'   => 'date_expires',
							'discount_type'  => 'discount_type',
							'expiry_date'    => 'date_expires',
						);
						if ( array_key_exists( $meta_key, $meta_key_to_props ) ) {
							$function = 'get_' . $meta_key_to_props[ $meta_key ];
							if ( $this->is_callable( $object, $function ) ) {
								$use_getter = true;
							}
						}
						break;
					case 'shop_order':
						$object            = ( is_object( $order ) && is_a( $order, 'WC_Order' ) ) ? $order : ( function_exists( 'wc_get_order' ) ? wc_get_order( $post_id ) : null );
						$order             = $object;
						$meta_key_to_props = array(
							'_order_total'   => 'total',
							'_billing_email' => 'billing_email',
						);
						if ( array_key_exists( $meta_key, $meta_key_to_props ) ) {
							$function = 'get_' . $meta_key_to_props[ $meta_key ];
							if ( $this->is_callable( $object, $function ) ) {
								$use_getter = true;
							}
						}
						break;
				}
				if ( true === $use_getter ) {
					$meta_value = $object->{$function}();
				} elseif ( $this->is_callable( $object, 'get_meta' ) ) {
					$meta_value = $object->get_meta( $meta_key );
				} else {
					$meta_value = get_post_meta( $post_id, $meta_key, $single );
				}
				if ( in_array( $meta_key, array( 'coupon_amount', 'smart_coupons_contribution', 'wc_sc_max_discount', 'wc_sc_original_amount', 'sc_called_credit_details', '_order_discount', '_order_total' ), true ) ) {
					$order_currency = null;
					if ( ! is_a( $order, 'WC_Order' ) && ! empty( $post_type ) && 'shop_order' === $post_type ) {
						$order = ( ! empty( $post_id ) ) ? wc_get_order( $post_id ) : null;
					}
					if ( is_a( $order, 'WC_Order' ) ) {
						if ( $this->is_wc_gte_30() ) {
							$order_currency = ( is_callable( array( $order, 'get_currency' ) ) ) ? $order->get_currency() : ''; // phpcs:ignore
						} else {
							$order_currency = ( is_callable( array( $order, 'get_order_currency' ) ) ) ? $order->get_order_currency() : ''; // phpcs:ignore
						}
					}
					if ( true === $convert ) {
						$current_currency = ( ! is_null( $order_currency ) ) ? $order_currency : get_woocommerce_currency();
						$base_currency    = get_option( 'woocommerce_currency' );
						if ( $base_currency !== $current_currency ) {
							if ( is_scalar( $meta_value ) ) {
								$meta_value = $this->convert_price( $meta_value, $current_currency, $base_currency );
							} elseif ( is_array( $meta_value ) ) {
								array_walk(
									$meta_value,
									array( $this, 'array_convert_price' ),
									array(
										'to_currency'   => $current_currency,
										'from_currency' => $base_currency,
									)
								);
							}
						}
					}
					return apply_filters(
						'wc_sc_after_get_post_meta',
						$meta_value,
						array(
							'source'              => $this,
							'currency_conversion' => $convert,
							'post_id'             => $post_id,
							'meta_key'            => $meta_key, // phpcs:ignore
							'order_obj'           => $order,
						)
					);
				}
			}
			return $meta_value;
		}

		/**
		 * Update post meta considering currency
		 *
		 * @param integer  $post_id The post id.
		 * @param string   $meta_key The meta key.
		 * @param string   $meta_value The meta value.
		 * @param boolean  $convert Whether to convert or not.
		 * @param WC_Order $order The order object.
		 *
		 * @throws Exception If Some values not passed for $post_id & $meta_key.
		 */
		public function update_post_meta( $post_id = 0, $meta_key = '', $meta_value = '', $convert = false, $order = null ) {
			if ( empty( $post_id ) || empty( $meta_key ) ) {
				$error = __( 'Some values required for $post_id & $meta_key', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$post_id is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $post_id ) ) ? var_dump( $post_id ) : print_r( gettype( $post_id ) ) . print_r( "\r\n" ); // phpcs:ignore
				esc_html_e( '$meta_key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $meta_key ) ) ? var_dump( $meta_key ) : print_r( gettype( $meta_key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return false;
			}
			$post_type = ( $this->is_callable( $this, 'get_post_type' ) ) ? $this->get_post_type( $post_id ) : '';
			if ( in_array( $meta_key, array( 'coupon_amount', 'smart_coupons_contribution', 'wc_sc_max_discount', 'wc_sc_original_amount', 'sc_called_credit_details', '_order_discount', '_order_total' ), true ) ) {
				$order_currency = null;

				if ( ! is_a( $order, 'WC_Order' ) && ! empty( $post_type ) && 'shop_order' === $post_type ) {
					$order = ( ! empty( $post_id ) ) ? wc_get_order( $post_id ) : null;
				}
				if ( is_a( $order, 'WC_Order' ) ) {
					if ( $this->is_wc_gte_30() ) {
						$order_currency = ( is_callable( array( $order, 'get_currency' ) ) ) ? $order->get_currency() : ''; // phpcs:ignore
					} else {
						$order_currency = ( is_callable( array( $order, 'get_order_currency' ) ) ) ? $order->get_order_currency() : ''; // phpcs:ignore
					}
				}
				if ( true === $convert ) {
					$current_currency = ( ! is_null( $order_currency ) ) ? $order_currency : get_woocommerce_currency();
					$base_currency    = get_option( 'woocommerce_currency' );
					if ( $base_currency !== $current_currency ) {
						if ( is_scalar( $meta_value ) ) {
							$meta_value = $this->convert_price( $meta_value, $base_currency, $current_currency );
						} elseif ( is_array( $meta_value ) ) {
							array_walk(
								$meta_value,
								array( $this, 'array_convert_price' ),
								array(
									'to_currency'   => $base_currency,
									'from_currency' => $current_currency,
								)
							);
						}
					}
				}
				$meta_value = apply_filters(
					'wc_sc_before_update_post_meta',
					$meta_value,
					array(
						'source'              => $this,
						'currency_conversion' => $convert,
						'post_id'             => $post_id,
						'meta_key'            => $meta_key, // phpcs:ignore
						'order_obj'           => $order,
					)
				);
			}
			if ( in_array( $post_type, array( 'product', 'product_variation', 'shop_coupon', 'shop_order' ), true ) ) {
				$object     = null;
				$use_setter = false;
				$post_id    = absint( $post_id );
				switch ( $post_type ) {
					case 'product':
					case 'product_variation':
						$object = ( function_exists( 'wc_get_product' ) ) ? wc_get_product( $post_id ) : null;
						break;
					case 'shop_coupon':
						$object            = new WC_Coupon( $post_id );
						$meta_key_to_props = array(
							'coupon_amount'  => 'amount',
							'customer_email' => 'email_restrictions',
							'date_expires'   => 'date_expires',
							'discount_type'  => 'discount_type',
							'expiry_date'    => 'date_expires',
						);
						if ( array_key_exists( $meta_key, $meta_key_to_props ) ) {
							$function = 'set_' . $meta_key_to_props[ $meta_key ];
							if ( $this->is_callable( $object, $function ) && $this->is_callable( $object, 'save' ) ) {
								$use_setter = true;
							}
						}
						break;
					case 'shop_order':
						$object            = ( is_object( $order ) && is_a( $order, 'WC_Order' ) ) ? $order : ( function_exists( 'wc_get_order' ) ? wc_get_order( $post_id ) : null );
						$meta_key_to_props = array(
							'_order_total'   => 'total',
							'_billing_email' => 'billing_email',
						);
						if ( array_key_exists( $meta_key, $meta_key_to_props ) ) {
							$function = 'set_' . $meta_key_to_props[ $meta_key ];
							if ( $this->is_callable( $object, $function ) && $this->is_callable( $object, 'save' ) ) {
								$use_setter = true;
							}
						}
						break;
				}
				if ( true === $use_setter ) {
					$object->{$function}( $meta_value );
					$object->save();
				} elseif ( $this->is_callable( $object, 'update_meta_data' ) && $this->is_callable( $object, 'save' ) ) {
					$object->update_meta_data( $meta_key, $meta_value );
					$object->save();
				} else {
					update_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}

		/**
		 * Wrapper function for deleting post meta
		 *
		 * @param integer $post_id The post id.
		 * @param string  $meta_key The meta key to delete.
		 * @param string  $meta_value The meta value to delete.
		 * @param mixed   $object The object.
		 *
		 * @throws Exception If Some values not passed for $post_id & $meta_key.
		 */
		public function delete_post_meta( $post_id = 0, $meta_key = '', $meta_value = '', $object = null ) {
			if ( empty( $post_id ) || empty( $meta_key ) ) {
				$error = __( 'Some values required for $post_id & $meta_key', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$post_id is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $post_id ) ) ? var_dump( $post_id ) : print_r( gettype( $post_id ) ) . print_r( "\r\n" ); // phpcs:ignore
				esc_html_e( '$meta_key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $meta_key ) ) ? var_dump( $meta_key ) : print_r( gettype( $meta_key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return false;
			}
			if ( is_null( $object ) || ! ( $this->is_callable( $object, 'delete_meta_data' ) && $this->is_callable( $object, 'save' ) ) ) {
				$post_type = ( $this->is_callable( $this, 'get_post_type' ) ) ? $this->get_post_type( $post_id ) : '';
				if ( in_array( $post_type, array( 'product', 'product_variation', 'shop_coupon', 'shop_order' ), true ) ) {
					$post_id = absint( $post_id );
					switch ( $post_type ) {
						case 'product':
						case 'product_variation':
							$object = ( function_exists( 'wc_get_product' ) ) ? wc_get_product( $post_id ) : null;
							break;
						case 'shop_coupon':
							$object = new WC_Coupon( $post_id );
							break;
						case 'shop_order':
							$object = ( is_object( $order ) && is_a( $order, 'WC_Order' ) ) ? $order : ( function_exists( 'wc_get_order' ) ? wc_get_order( $post_id ) : null );
							break;
					}
				}
			}
			if ( $this->is_callable( $object, 'delete_meta_data' ) && $this->is_callable( $object, 'save' ) ) {
				$object->delete_meta_data( $meta_key );
				$object->save();
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}

		/**
		 * Get value from WooCommerce session
		 *
		 * @param string  $key The key.
		 * @param boolean $convert Whether to convert or not.
		 *
		 * @throws Exception If $key is not passed.
		 * @return mixed
		 */
		public function get_session( $key = '', $convert = false ) {
			if ( empty( $key ) ) {
				$error = __( '$key is required', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $key ) ) ? var_dump( $key ) : print_r( gettype( $key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return null;
			}
			if ( ! is_callable( 'WC' ) || ! is_object( WC() ) || ! is_object( WC()->session ) || ! is_callable( array( WC()->session, 'get' ) ) ) {
				return null;
			}
			$value = WC()->session->get( $key );
			if ( 'credit_called' !== $key ) {
				return $value;
			}
			if ( true === $convert ) {
				$current_currency = get_woocommerce_currency();
				$base_currency    = get_option( 'woocommerce_currency' );
				if ( $base_currency !== $current_currency ) {
					if ( ! empty( $value ) ) {
						if ( is_scalar( $value ) ) {
							$value = $this->convert_price( $value, $current_currency, $base_currency );
						} elseif ( is_array( $value ) ) {
							array_walk(
								$value,
								array( $this, 'array_convert_price' ),
								array(
									'to_currency'   => $current_currency,
									'from_currency' => $base_currency,
								)
							);
						}
					}
				}
			}
			return apply_filters(
				'wc_sc_after_get_session',
				$value,
				array(
					'source'              => $this,
					'currency_conversion' => $convert,
					'key'                 => $key,
				)
			);
		}

		/**
		 * Save a value in WooCommerce session
		 *
		 * @param string  $key The key.
		 * @param string  $value The value.
		 * @param boolean $convert Whether to convert or not.
		 *
		 * @throws Exception If $key is not passed.
		 */
		public function set_session( $key = '', $value = '', $convert = false ) {
			if ( empty( $key ) ) {
				$error = __( '$key is required', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $key ) ) ? var_dump( $key ) : print_r( gettype( $key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return false;
			}
			if ( ! is_callable( 'WC' ) || ! is_object( WC() ) || ! is_object( WC()->session ) || ! is_callable( array( WC()->session, 'set' ) ) ) {
				return;
			}
			if ( 'credit_called' !== $key ) {
				return;
			}
			if ( true === $convert ) {
				$current_currency = get_woocommerce_currency();
				$base_currency    = get_option( 'woocommerce_currency' );
				if ( $base_currency !== $current_currency ) {
					if ( ! empty( $value ) ) {
						if ( is_scalar( $value ) ) {
							$value = $this->convert_price( $value, $base_currency, $current_currency );
						} elseif ( is_array( $value ) ) {
							array_walk(
								$value,
								array( $this, 'array_convert_price' ),
								array(
									'to_currency'   => $base_currency,
									'from_currency' => $current_currency,
								)
							);
						}
					}
				}
			}
			$value = apply_filters(
				'wc_sc_before_set_session',
				$value,
				array(
					'source'              => $this,
					'currency_conversion' => $convert,
					'key'                 => $key,
				)
			);
			WC()->session->set( $key, $value );
		}

		/**
		 * Get order item meta considering currency
		 *
		 * @param integer $item_id The order item id.
		 * @param string  $item_key The order item key.
		 * @param boolean $single Whether to get single value or not.
		 * @param boolean $convert Whether to convert or not.
		 *
		 * @throws Exception If Some values not passed for $item_id & $item_key.
		 * @return mixed
		 */
		public function get_order_item_meta( $item_id = 0, $item_key = '', $single = false, $convert = false ) {
			if ( empty( $item_id ) || empty( $item_key ) ) {
				$error = __( 'Some values required for $item_id & $item_key', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$item_id is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_id ) ) ? var_dump( $item_id ) : print_r( gettype( $item_id ) ) . print_r( "\r\n" ); // phpcs:ignore
				esc_html_e( '$item_key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_key ) ) ? var_dump( $item_key ) : print_r( gettype( $item_key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return null;
			}
			$item_value = wc_get_order_item_meta( $item_id, $item_key, $single );
			if ( in_array( $item_key, array( 'discount', 'discount_amount', 'discount_amount_tax', 'sc_refunded_discount', 'sc_refunded_discount_tax', 'sc_called_credit' ), true ) ) {
				if ( $this->is_wc_gte_30() ) {
					$order_id = ( ! empty( $item_id ) ) ? wc_get_order_id_by_order_item_id( $item_id ) : 0;
				} else {
					$order_id = ( ! empty( $item_id ) ) ? $this->get_order_id_by_order_item_id_wclt30( $item_id ) : 0;
				}
				$order = ( ! empty( $order_id ) ) ? wc_get_order( $order_id ) : null;
				$item  = ( is_object( $order ) && is_callable( array( $order, 'get_item' ) ) ) ? $order->get_item( $item_id ) : null;
				if ( true === $convert ) {
					if ( $this->is_wc_gte_30() ) {
						$order_currency = ( is_object( $order ) && is_callable( array( $order, 'get_currency' ) ) ) ? $order->get_currency() : ''; // phpcs:ignore
					} else {
						$order_currency = ( is_object( $order ) && is_callable( array( $order, 'get_order_currency' ) ) ) ? $order->get_order_currency() : ''; // phpcs:ignore
					}
					if ( ! empty( $order_currency ) ) {
						$base_currency = get_option( 'woocommerce_currency' );
						$item_value    = $this->convert_price( $item_value, $order_currency, $base_currency );
					}
				}
				return apply_filters(
					'wc_sc_after_get_order_item_meta',
					$item_value,
					array(
						'source'              => $this,
						'currency_conversion' => $convert,
						'order_item_obj'      => $item,
						'order_item_id'       => $item_id,
						'order_item_key'      => $item_key,
					)
				);
			}
			return $item_value;
		}

		/**
		 * Add order item meta considering currency
		 *
		 * @param integer $item_id The order item id.
		 * @param string  $item_key The order item key.
		 * @param string  $item_value The order item value.
		 * @param boolean $convert Whether to convert or not.
		 *
		 * @throws Exception If Some values not passed for $item_id & $item_key.
		 */
		public function add_order_item_meta( $item_id = 0, $item_key = '', $item_value = '', $convert = false ) {
			if ( empty( $item_id ) || empty( $item_key ) ) {
				$error = __( 'Some values required for $item_id & $item_key', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$item_id is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_id ) ) ? var_dump( $item_id ) : print_r( gettype( $item_id ) ) . print_r( "\r\n" ); // phpcs:ignore
				esc_html_e( '$item_key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_key ) ) ? var_dump( $item_key ) : print_r( gettype( $item_key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return 0;
			}
			if ( in_array( $item_key, array( 'discount', 'discount_amount', 'discount_amount_tax', 'sc_refunded_discount', 'sc_refunded_discount_tax', 'sc_called_credit' ), true ) ) {
				$item_value = $this->get_item_value( $item_id, $item_value, $convert );
			}
			if ( $this->is_wc_gte_30() ) {
				wc_add_order_item_meta( $item_id, $item_key, $item_value );
			} else {
				woocommerce_add_order_item_meta( $item_id, $item_key, $item_value );
			}
		}

		/**
		 * Update order item meta considering currency
		 *
		 * @param integer $item_id The order item id.
		 * @param string  $item_key The order item key.
		 * @param string  $item_value The order item value.
		 * @param boolean $convert Whether to convert or not.
		 *
		 * @throws Exception If Some values not passed for $item_id & $item_key.
		 */
		public function update_order_item_meta( $item_id = 0, $item_key = '', $item_value = '', $convert = false ) {
			if ( empty( $item_id ) || empty( $item_key ) ) {
				$error = __( 'Some values required for $item_id & $item_key', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$item_id is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_id ) ) ? var_dump( $item_id ) : print_r( gettype( $item_id ) ) . print_r( "\r\n" ); // phpcs:ignore
				esc_html_e( '$item_key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_key ) ) ? var_dump( $item_key ) : print_r( gettype( $item_key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return false;
			}
			if ( in_array( $item_key, array( 'discount', 'discount_amount', 'discount_amount_tax', 'sc_refunded_discount', 'sc_refunded_discount_tax', 'sc_called_credit' ), true ) ) {
				$item_value = $this->get_item_value( $item_id, $item_value, $convert );
			}
			wc_update_order_item_meta( $item_id, $item_key, $item_value );
		}

		/**
		 * Delete order item meta
		 *
		 * @param integer $item_id The order item id.
		 * @param string  $item_key The order item key.
		 * @param boolean $convert Whether to convert or not.
		 *
		 * @throws Exception If Some values not passed for $item_id & $item_key.
		 */
		public function delete_order_item_meta( $item_id = 0, $item_key = '', $convert = false ) {
			if ( empty( $item_id ) || empty( $item_key ) ) {
				$error = __( 'Some values required for $item_id & $item_key', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$item_id is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_id ) ) ? var_dump( $item_id ) : print_r( gettype( $item_id ) ) . print_r( "\r\n" ); // phpcs:ignore
				esc_html_e( '$item_key is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_key ) ) ? var_dump( $item_key ) : print_r( gettype( $item_key ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return false;
			}
			if ( in_array( $item_key, array( 'discount', 'discount_amount', 'discount_amount_tax', 'sc_refunded_discount', 'sc_refunded_discount_tax', 'sc_called_credit' ), true ) ) {
				if ( $this->is_wc_gte_30() ) {
					wc_delete_order_item_meta( $item_id, $item_key );
				} else {
					woocommerce_delete_order_item_meta( $item_id, $item_key );
				}
			}
		}

		/**
		 * Get order item meta
		 *
		 * @param WC_Order_item $item The order item object.
		 * @param string        $item_key The order item meta key.
		 * @param boolean       $convert Whether to convert or not.
		 *
		 * @throws Exception If $item is not an object of WC_Order_Item.
		 * @return mixed
		 */
		public function get_meta( $item = null, $item_key = '', $convert = false ) {
			if ( ! is_a( $item, 'WC_Order_Item' ) ) {
				$error = __( '$item is not an object of WC_Order_Item', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$item is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item ) ) ? var_dump( $item ) : print_r( gettype( $item ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return null;
			}
			$item_value = ( is_callable( array( $item, 'get_meta' ) ) ) ? $item->get_meta( $item_key ) : ( ( ! empty( $item[ $item_key ] ) ) ? $item[ $item_key ] : '' );
			if ( in_array( $item_key, array( 'discount', 'discount_amount', 'discount_amount_tax', 'sc_refunded_discount', 'sc_refunded_discount_tax', 'sc_called_credit' ), true ) ) {
				$item_id = ( is_callable( array( $item, 'get_id' ) ) ) ? $item->get_id() : 0;
				if ( true === $convert ) {
					$order = ( is_callable( array( $item, 'get_order' ) ) ) ? $item->get_order() : null;
					if ( $this->is_wc_gte_30() ) {
						$order_currency = ( is_object( $order ) && is_callable( array( $order, 'get_currency' ) ) ) ? $order->get_currency() : ''; // phpcs:ignore
					} else {
						$order_currency = ( is_object( $order ) && is_callable( array( $order, 'get_order_currency' ) ) ) ? $order->get_order_currency() : ''; // phpcs:ignore
					}
					if ( ! empty( $order_currency ) ) {
						$base_currency = get_option( 'woocommerce_currency' );
						$item_value    = $this->convert_price( $item_value, $order_currency, $base_currency );
					}
				}
				return apply_filters(
					'wc_sc_after_get_order_item_meta',
					$item_value,
					array(
						'source'              => $this,
						'currency_conversion' => $convert,
						'order_item_obj'      => $item,
						'order_item_id'       => $item_id,
						'order_item_key'      => $item_key,
					)
				);
			}
			return $item_value;
		}

		/**
		 * Update order item meta considering currency
		 *
		 * @param WC_Order_item $item The order item object.
		 * @param string        $item_key The order item meta key.
		 * @param string        $item_value The order item value.
		 * @param boolean       $convert Whether to convert or not.
		 *
		 * @throws Exception If $item is not an object of WC_Order_Item.
		 */
		public function update_meta_data( &$item = null, $item_key = '', $item_value = '', $convert = false ) {
			if ( ! is_a( $item, 'WC_Order_Item' ) ) {
				$error = __( '$item is not an object of WC_Order_Item', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$item is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item ) ) ? var_dump( $item ) : print_r( gettype( $item ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return false;
			}
			if ( in_array( $item_key, array( 'discount', 'discount_amount', 'discount_amount_tax', 'sc_refunded_discount', 'sc_refunded_discount_tax', 'sc_called_credit' ), true ) ) {
				$item_id    = ( is_callable( array( $item, 'get_id' ) ) ) ? $item->get_id() : 0;
				$item_value = $this->get_item_value( $item_id, $item_value, $convert );
				if ( is_callable( array( $item, 'update_meta_data' ) ) ) {
					$item->update_meta_data( $item_key, $item_value );
				} else {
					$item[ $item_key ] = $item_value;
				}
			}
		}

		/**
		 * Check & convert price
		 *
		 * @since 6.0.0
		 *
		 * @param float  $price The price need to be converted.
		 * @param string $to_currency The price will be converted to this currency.
		 * @param string $from_currency The price will be converted from this currency.
		 * @return float
		 */
		public function convert_price( $price = 0, $to_currency = null, $from_currency = null ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( is_plugin_active( 'woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php' ) ) {
				if ( ! class_exists( 'WC_SC_Aelia_CS_Compatibility' ) ) {
					include_once 'compat/class-wc-sc-aelia-cs-compatibility.php';
				}
				return WC_SC_Aelia_CS_Compatibility::get_instance()->convert_price( $price, $to_currency, $from_currency );
			}
			return $price;
		}

		/**
		 * Callback function for array_walk to apply convert price on each element of array
		 *
		 * @param mixed $value The array element.
		 * @param mixed $key The array key.
		 * @param array $args The additional arguments.
		 */
		public function array_convert_price( &$value = null, $key = null, $args = null ) {
			if ( ! is_null( $value ) && ! is_null( $key ) && ! is_null( $args ) ) {
				$to_currency   = ( ! empty( $args['to_currency'] ) ) ? $args['to_currency'] : '';
				$from_currency = ( ! empty( $args['from_currency'] ) ) ? $args['from_currency'] : '';
				if ( ! empty( $to_currency ) && ! empty( $from_currency ) ) {
					$value = $this->convert_price( $value, $to_currency, $from_currency );
				}
			}
		}

		/**
		 * Get item value for saving/updating in DB considering currency
		 *
		 * @param integer $item_id The order item id.
		 * @param string  $item_value The item value.
		 * @param boolean $convert Whether to convert or not.
		 *
		 * @throws Exception If $item is not passed.
		 * @return mixed
		 */
		public function get_item_value( $item_id = 0, $item_value = '', $convert = false ) {
			if ( empty( $item_id ) ) {
				$error = __( '$item_id is required', 'woocommerce-smart-coupons' );
				ob_start();
				esc_html_e( '$item_id is: ', 'woocommerce-smart-coupons' ) . ( is_scalar( $item_id ) ) ? var_dump( $item_id ) : print_r( gettype( $item_id ) ) . print_r( "\r\n" ); // phpcs:ignore
				$this->log( 'notice', print_r( $error, true ) . ' ' . __FILE__ . ' ' . __LINE__ . print_r( "\r\n" . ob_get_clean(), true ) ); // phpcs:ignore
				return null;
			}
			if ( $this->is_wc_gte_30() ) {
				$order_id = ( ! empty( $item_id ) ) ? wc_get_order_id_by_order_item_id( $item_id ) : 0;
			} else {
				$order_id = ( ! empty( $item_id ) ) ? $this->get_order_id_by_order_item_id_wclt30( $item_id ) : 0;
			}
			$order = ( ! empty( $order_id ) ) ? wc_get_order( $order_id ) : null;
			if ( true === $convert ) {
				if ( $this->is_wc_gte_30() ) {
					$order_currency = ( is_object( $order ) && is_callable( array( $order, 'get_currency' ) ) ) ? $order->get_currency() : '';  // phpcs:ignore
				} else {
					$order_currency = ( is_object( $order ) && is_callable( array( $order, 'get_order_currency' ) ) ) ? $order->get_order_currency() : ''; // phpcs:ignore
				}
				if ( ! empty( $order_currency ) ) {
					$base_currency = get_option( 'woocommerce_currency' );
					if ( $base_currency !== $order_currency ) {
						$item_value = $this->convert_price( $item_value, $base_currency, $order_currency );
					}
				}
			}
			return apply_filters(
				'wc_sc_before_update_order_item_meta',
				$item_value,
				array(
					'source'              => $this,
					'currency_conversion' => $convert,
					'order_obj'           => $order,
					'order_item_id'       => $item_id,
				)
			);

		}

		/**
		 * Get order id by order item id for WooCommerce version lower than 3.0.0
		 *
		 * @param integer $item_id The order item id.
		 * @return mixed
		 */
		public function get_order_id_by_order_item_id_wclt30( $item_id = 0 ) {
			global $wpdb;
			return $wpdb->get_var( // phpcs:ignore
				$wpdb->prepare(
					"SELECT order_id
						FROM {$wpdb->prefix}woocommerce_order_items
						WHERE order_item_id = %d",
					absint( $item_id )
				)
			);
		}

		/**
		 * Convert a date string to a WC_DateTime.
		 *
		 * Wrapper function to give support for store running WooCommerce 3.0.0.
		 *
		 * Credit: WooCommerce
		 *
		 * @since  6.3.0
		 * @param  string $time_string Time string.
		 * @return WC_DateTime
		 */
		public function wc_string_to_datetime( $time_string ) {

			if ( function_exists( 'wc_string_to_datetime' ) ) {
				return wc_string_to_datetime( $time_string );
			}

			// Strings are defined in local WP timezone. Convert to UTC.
			if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $time_string, $date_bits ) ) {
				$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : $this->wc_timezone_offset();
				$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
			} else {
				$timestamp = wc_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', wc_string_to_timestamp( $time_string ) ) ) );
			}
			$datetime = new WC_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );

			// Set local timezone or offset.
			if ( get_option( 'timezone_string' ) ) {
				$datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
			} else {
				$datetime->set_utc_offset( $this->wc_timezone_offset() );
			}

			return $datetime;
		}

		/**
		 * Get timezone offset in seconds.
		 *
		 * Wrapper function for giving support to lower version of WooCommerce
		 *
		 * Credit: WooCommerce
		 *
		 * @since  6.3.0
		 * @return float
		 */
		public function wc_timezone_offset() {

			if ( function_exists( 'wc_timezone_offset' ) ) {
				return wc_timezone_offset();
			}

			$timezone = get_option( 'timezone_string' );

			if ( $timezone ) {
				$timezone_object = new DateTimeZone( $timezone );
				return $timezone_object->getOffset( new DateTime( 'now' ) );
			} else {
				return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
			}
		}

		/**
		 * Get timestamp from date string
		 *
		 * @param string $date_string The date in string format.
		 * @return int
		 */
		public function wc_string_to_datetime_to_timestamp( $date_string = '' ) {
			$timestamp = null;
			if ( ! empty( $date_string ) ) {
				$datetime  = $this->wc_string_to_datetime( $date_string );
				$timestamp = ( is_object( $datetime ) && is_callable( array( $datetime, 'getTimestamp' ) ) ) ? $datetime->getTimestamp() : null;
			}
			return $timestamp;
		}

		/**
		 * Maybe convert to correct value for meta 'date_expires'
		 *
		 * @param mixed $date_expires The current date_expires value.
		 * @return mixed
		 */
		public function get_date_expires_value( $date_expires = null ) {
			$date_expires = intval( $date_expires );
			$date_expires = ( ! empty( $date_expires ) ) ? $date_expires : null;
			return $date_expires;
		}

		/**
		 * Converts a string (e.g. 'yes' or 'no') to a bool.
		 *
		 * Credit: WooCommerce
		 *
		 * @since 7.2.0
		 * @param string|bool $string String to convert. If a bool is passed it will be returned as-is.
		 * @return bool
		 */
		public function wc_string_to_bool( $string ) {

			if ( function_exists( 'wc_string_to_bool' ) ) {
				return wc_string_to_bool( $string );
			}

			return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
		}

		/**
		 * Converts a bool to a 'yes' or 'no'.
		 *
		 * Credit: WooCommerce
		 *
		 * @since 7.2.0
		 * @param bool|string $bool Bool to convert. If a string is passed it will first be converted to a bool.
		 * @return string
		 */
		public function wc_bool_to_string( $bool ) {

			if ( function_exists( 'wc_bool_to_string' ) ) {
				return wc_bool_to_string( $bool );
			}

			if ( ! is_bool( $bool ) ) {
				$bool = $this->wc_string_to_bool( $bool );
			}
			return true === $bool ? 'yes' : 'no';
		}

		/**
		 * Compatible function for mb_detect_encoding.
		 *
		 * (c) Fabien Potencier <fabien@symfony.com>
		 *
		 * @author Nicolas Grekas <p@tchwork.com>
		 *
		 * @param string  $string The string to be checked.
		 * @param mixed   $encodings List of encoding.
		 * @param boolean $strict Strict checking or not.
		 * @return mixed
		 */
		public function mb_detect_encoding( $string, $encodings = null, $strict = false ) {

			if ( function_exists( 'mb_detect_encoding' ) ) {
				return mb_detect_encoding( $string, $encodings, $strict );
			}

			if ( null === $encodings ) {
				$encodings = array( 'ASCII', 'UTF-8' );
			} else {
				if ( ! is_array( $encodings ) ) {
					$encodings = array_map( 'trim', explode( ',', $encodings ) );
				}
				$encodings = array_map( 'strtoupper', $encodings );
			}

			foreach ( $encodings as $enc ) {
				switch ( $enc ) {
					case 'ASCII':
						if ( ! preg_match( '/[\x80-\xFF]/', $string ) ) {
							return $enc;
						}
						break;

					case 'UTF8':
					case 'UTF-8':
						if ( preg_match( '//u', $string ) ) {
							return 'UTF-8';
						}
						break;

					default:
						if ( 0 === strncmp( $enc, 'ISO-8859-', 9 ) ) {
							return $enc;
						}
				}
			}

			return false;
		}

		/**
		 * Check if a method is callable w.r.t. given object or not
		 *
		 * @param mixed  $object The object.
		 * @param string $method The method name.
		 * @return boolean
		 */
		public function is_callable( $object = null, $method = '' ) {
			if ( empty( $object ) || empty( $method ) ) {
				return false;
			}
			$type = gettype( $object );
			if ( ! in_array( $type, array( 'string', 'object' ), true ) ) {
				return false;
			}
			if ( 'string' === $type && ! class_exists( $object ) ) {
				return false;
			}
			return is_callable( array( $object, $method ) );
		}

		/**
		 * Wrapper function for checking if a coupon is valid for the cart.
		 *
		 * @param WC_Coupon        $coupon The coupon object.
		 * @param WC_Cart|WC_Order $cart_or_order Cart or order object.
		 * @return bool
		 */
		public function is_valid( $coupon = null, $cart_or_order = null ) {
			if ( is_null( $coupon ) ) {
				return false;
			}
			if ( is_null( $cart_or_order ) ) {
				$cart_or_order = WC()->cart;
			}
			$discounts = new WC_Discounts( $cart_or_order );
			$valid     = $discounts->is_coupon_valid( $coupon );

			if ( is_wp_error( $valid ) ) {
				$coupon->error_message = $valid->get_error_message();
				return false;
			}

			return $valid;
		}

		/**
		 * Generate link for filter by passed email
		 *
		 * @param string $email The email address.
		 * @return string $link
		 */
		public function filter_by_email_link( $email = '' ) {
			$html = '';
			if ( ! empty( $email ) && is_email( $email ) ) {
				$link = add_query_arg(
					array(
						'post_type'   => 'shop_coupon',
						'post_status' => 'all',
						's'           => 'email:' . $email,
					),
					admin_url( 'edit.php' )
				);
				/* translators: Email address of users */
				$html = '<a href="' . esc_url( $link ) . '" title="' . sprintf( esc_attr__( 'Find coupons restricted to %s', 'woocommerce-smart-coupons' ), esc_attr( $email ) ) . '">' . esc_html( $email ) . '</a>';
			}
			return $html;
		}

		/**
		 * Get post by post_title
		 *
		 * @param array    $titles The titles to be searched.
		 * @param constant $return_type The return type.
		 * @param array    $post_types The post types to be considered for searching.
		 * @return array $posts Found posts
		 */
		public function get_post_by_title( $titles = array(), $return_type = OBJECT, $post_types = array() ) {
			global $wpdb;

			$query = $wpdb->prepare(
				"SELECT ID, post_title
					FROM $wpdb->posts
					WHERE %d",
				1
			);

			if ( ! is_array( $titles ) ) {
				$titles = array( $titles );
			}

			$how_many     = count( $titles );
			$placeholders = array_fill( 0, $how_many, '%s' );

			if ( ! empty( $how_many ) ) {
				if ( $how_many > 1 ) {
					$query .= $wpdb->prepare(
						' AND post_title IN (' . implode( ',', $placeholders ) . ')', // phpcs:ignore
						$titles
					);
				} else {
					if ( is_array( $titles ) ) {
						$titles = current( $titles );
					}
					$query .= $wpdb->prepare(
						' AND post_title = %s',
						$titles
					);
				}
			}

			if ( ! is_array( $post_types ) ) {
				$post_types = array( $post_types );
			}

			$how_many     = count( $post_types );
			$placeholders = array_fill( 0, $how_many, '%s' );

			if ( ! empty( $how_many ) ) {
				if ( $how_many > 1 ) {
					$query .= $wpdb->prepare(
						' AND post_type IN (' . implode( ',', $placeholders ) . ')', // phpcs:ignore
						$post_types
					);
				} else {
					if ( is_array( $post_types ) ) {
						$post_types = current( $post_types );
					}
					$query .= $wpdb->prepare(
						' AND post_type = %s',
						$post_types
					);
				}
			}

			$results = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore

			if ( ! is_wp_error( $results ) && ! empty( $results ) ) {
				$id_to_post_titles  = wp_list_pluck( $results, 'post_title', 'ID' );
				$id_to_title_slug   = array_map( 'sanitize_title', $id_to_post_titles );
				$title_slug_to_id   = array_flip( $id_to_title_slug );
				$title_slug_to_post = array();
				foreach ( $title_slug_to_id as $slug => $id ) {
					if ( ! empty( $slug ) && ! empty( $id ) ) {
						$title_slug_to_post[ $slug ] = get_post( $id, $return_type );
					}
				}
				return $title_slug_to_post;
			}

			return null;

		}

		/**
		 * Wrapper function to get post type
		 *
		 * @param integer $post_id The post id.
		 * @return string|boolean
		 */
		public function get_post_type( $post_id = null ) {
			if ( ! empty( $post_id ) && $this->is_hpos_order( $post_id ) ) {
				return 'shop_order';
			}
			return get_post_type( $post_id );
		}

		/**
		 * Function to check whether the order was created prior to version 8.0.0 or not.
		 *
		 * @param WC_Order|int $order The order to be checked.
		 * @return boolean
		 */
		public function is_old_sc_order( $order = null ) {

			if ( empty( $order ) || is_array( $order ) ) {
				return false;
			}

			$orders_prior_to_800 = $this->sc_get_option( 'wc_sc_old_orders_prior_to_800' );

			if ( empty( $orders_prior_to_800 ) || 'no' === $orders_prior_to_800 ) {
				return false;
			}

			if ( is_a( $order, 'WC_Order' ) ) {
				$order_id = $this->is_callable( $order, 'get_id' ) ? $order->get_id() : 0;
			} else {
				$order_id = $order;
			}
			$order_id = absint( $order_id );

			$from_to_groups = array_chunk( $orders_prior_to_800, 2, true );

			foreach ( $from_to_groups as $group ) {
				$group = array_flip( $group );
				$from  = ( ! empty( $group['from'] ) ) ? absint( $group['from'] ) : 0;
				$to    = ( ! empty( $group['to'] ) ) ? absint( $group['to'] ) : 0;
				if ( $from <= $to && $order_id >= $from && $order_id <= $to ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Function to find & record orders that were created when Smart Coupons older than 8.0.0 was active & in which store credit was applied
		 */
		public function maybe_sync_orders_prior_to_800() {
			$orders_prior_to_800 = $this->sc_get_option( 'wc_sc_old_orders_prior_to_800' );

			if ( 'no' === $orders_prior_to_800 ) {
				return;
			}

			$args      = array(
				'return'                     => 'ids',
				'orderby'                    => 'ID',
				'order'                      => 'ASC',
				'limit'                      => -1,
				'smart_coupons_contribution' => array(
					'value'   => array( '', 'a:0:{}' ),
					'compare' => 'NOT IN',
				),
			);
			$order_ids = wc_get_orders( $args );

			if ( empty( $order_ids ) ) {
				return;
			}

			if ( ! is_array( $orders_prior_to_800 ) || empty( $orders_prior_to_800 ) ) {
				$orders_prior_to_800 = array();
				if ( ! empty( $order_ids ) ) {
					$from                                   = min( $order_ids );
					$to                                     = max( $order_ids );
					$orders_prior_to_800[ absint( $from ) ] = 'from';
					$orders_prior_to_800[ absint( $to ) ]   = 'to';
					update_option( 'wc_sc_old_orders_prior_to_800', $orders_prior_to_800, 'no' );
				}
				return;
			}

			if ( ! empty( $orders_prior_to_800 ) ) {
				krsort( $orders_prior_to_800, SORT_NUMERIC );
			}

			$last_800_order  = array_search( '8.0.0', $orders_prior_to_800, true );
			$last_from_order = array_search( 'from', $orders_prior_to_800, true );
			$last_to_order   = array_search( 'to', $orders_prior_to_800, true );
			$max             = max( $last_800_order, $last_from_order, $last_to_order );

			if ( ! array_key_exists( $max, $orders_prior_to_800 ) ) {
				return;
			}

			$status    = $orders_prior_to_800[ $max ];
			$remaining = array_slice( $order_ids, ( array_search( $max, $order_ids, true ) + 1 ) );

			if ( empty( $remaining ) ) {
				return;
			}

			switch ( $status ) {
				case '8.0.0':
					$from                                   = min( $remaining );
					$to                                     = max( $remaining );
					$orders_prior_to_800[ absint( $from ) ] = 'from';
					$orders_prior_to_800[ absint( $to ) ]   = 'to';
					break;
				case 'from':
					$to                                   = max( $remaining );
					$orders_prior_to_800[ absint( $to ) ] = 'to';
					break;
				case 'to':
					$to = max( $remaining );
					unset( $orders_prior_to_800[ $max ] );
					$orders_prior_to_800[ absint( $to ) ] = 'to';
					break;
			}

			$orders_prior_to_800 = array_diff( $orders_prior_to_800, array( '8.0.0' ) );

			ksort( $orders_prior_to_800, SORT_NUMERIC );

			update_option( 'wc_sc_old_orders_prior_to_800', $orders_prior_to_800, 'no' );

		}

		/**
		 * Function to be executed after the plugin is upgraded.
		 *
		 * @param object $upgrader The upgrader object.
		 * @param array  $hook_extra Additional params.
		 */
		public function upgrader_process_complete( $upgrader = null, $hook_extra = array() ) {
			if ( ! empty( $hook_extra['type'] ) && 'plugin' === $hook_extra['type']
				&& ! empty( $upgrader->result['destination_name'] ) && 'woocommerce-smart-coupons' === $upgrader->result['destination_name']
				&& isset( $upgrader->new_plugin_data['Woo'] ) && ! empty( $upgrader->new_plugin_data['Woo'] && '18729:05c45f2aa466106a466de4402fff9dde' === $upgrader->new_plugin_data['Woo'] )
			) {
				if ( $this->is_sc_gte( '8.0.0' ) ) {
					$this->maybe_sync_orders_prior_to_800();
				} else {
					$this->record_latest_800_order();
				}
			}
		}

		/**
		 * Add support for custom meta from Smart Coupons to search for Orders
		 *
		 * @param array $query The query.
		 * @param array $query_vars The query vars.
		 * @return array
		 */
		public function custom_meta_support_in_orders_query( $query = array(), $query_vars = array() ) {
			if ( ! empty( $query_vars['smart_coupons_contribution'] ) ) {
				$query['meta_query'][] = array(
					'key'     => 'smart_coupons_contribution',
					'value'   => array_map( 'esc_attr', $query_vars['smart_coupons_contribution']['value'] ),
					'compare' => esc_attr( $query_vars['smart_coupons_contribution']['compare'] ),
				);
			}
			return $query;
		}

		/**
		 * Function to record latest order which was created when Smart Coupons 8.0.0+ was active & store credit was applied to the order.
		 */
		public function record_latest_800_order() {
			$orders_prior_to_800 = $this->sc_get_option( 'wc_sc_old_orders_prior_to_800' );

			if ( false !== $orders_prior_to_800 && 'no' !== $orders_prior_to_800 ) {
				$args      = array(
					'return'                     => 'ids',
					'orderby'                    => 'ID',
					'order'                      => 'DESC',
					'limit'                      => 1,
					'smart_coupons_contribution' => array(
						'value'   => array( '', 'a:0:{}' ),
						'compare' => 'NOT IN',
					),
				);
				$order_ids = wc_get_orders( $args );
				if ( ! empty( $order_ids ) ) {
					$last_order_id = current( $order_ids );
					if ( ! is_array( $orders_prior_to_800 ) || empty( $orders_prior_to_800 ) ) {
						$orders_prior_to_800 = array();
					}
					if ( ! array_key_exists( $last_order_id, $orders_prior_to_800 ) ) {
						$last_array_element  = array( absint( $last_order_id ) => '8.0.0' );
						$orders_prior_to_800 = $orders_prior_to_800 + $last_array_element;
					}
					update_option( 'wc_sc_old_orders_prior_to_800', $orders_prior_to_800, 'no' );
				}
			}
		}

		/**
		 * Format order item meta added by Smart Coupons.
		 *
		 * @param array         $formatted_metas Existsing metas.
		 * @param WC_Order_Item $order_item The order item.
		 * @return array
		 */
		public function format_sc_meta_data( $formatted_metas = array(), $order_item = null ) {
			if ( ! empty( $formatted_metas ) ) {
				$sc_metas_label = array(
					'_wc_sc_product_source' => __( 'Added by coupon', 'woocommerce-smart-coupons' ),
				);
				foreach ( $formatted_metas as $meta_id => $meta ) {
					if ( ! empty( $meta->key ) && array_key_exists( $meta->key, $sc_metas_label ) ) {
						switch ( $meta->key ) {
							default:
								$formatted_metas[ $meta_id ]->display_key = $sc_metas_label[ $meta->key ];
								break;
						}
					}
				}
			}
			return $formatted_metas;
		}

		/**
		 * Hide order item metas that are added by the Smart Coupons plugin.
		 *
		 * @param array $metas The existing metas.
		 * @return array
		 */
		public function hidden_order_itemmeta( $metas = array() ) {
			if ( ! is_array( $metas ) || empty( $metas ) ) {
				$metas = array();
			}
			$sc_metas  = array(
				'sc_called_credit',
				'sc_refunded_discount',
				'sc_refunded_discount_tax',
				'sc_refunded_user_id',
				'sc_refunded_timestamp',
				'sc_refunded_coupon_id',
				'sc_revoke_refunded_discount',
				'sc_revoke_refunded_discount_tax',
				'sc_revoke_refunded_user_id',
				'sc_revoke_refunded_timestamp',
				'sc_revoke_refunded_coupon_id',
			);
			$intersect = array_intersect( $metas, $sc_metas );
			if ( count( $sc_metas ) !== count( $intersect ) ) {
				$metas = array_merge( $metas, $sc_metas );
				$metas = array_filter( array_unique( $metas ) );
			}
			return $metas;
		}

		/**
		 * Check if the order has at least one product that can generate coupon
		 *
		 * @param WC_Order $order The order object.
		 * @return boolean
		 */
		public function would_order_generate_coupons( $order = null ) {
			if ( empty( $order ) ) {
				return false;
			}
			if ( ! is_a( $order, 'WC_Order' ) && is_numeric( $order ) ) {
				$order = wc_get_order( absint( $order ) );
			}
			$order_items = $this->is_callable( $order, 'get_items' ) ? $order->get_items() : array();
			if ( empty( $order_items ) || is_scalar( $order_items ) ) {
				return false;
			}
			foreach ( $order_items as $item ) {
				if ( $item->is_type( 'line_item' ) ) {
					$product = $this->is_callable( $item, 'get_product' ) ? $item->get_product() : ( $this->is_callable( $order, 'get_product_from_item' ) ? $order->get_product_from_item( $item ) : null );
					if ( ! is_a( $product, 'WC_Product' ) ) {
						continue;
					}
					$linked_coupons = $this->is_callable( $product, 'get_meta' ) ? $product->get_meta( '_coupon_title' ) : array();
					if ( empty( $linked_coupons ) && $this->is_callable( $product, 'is_type' ) && true === $product->is_type( 'variation' ) ) {
						$parent_id = $this->is_callable( $product, 'get_parent_id' ) ? $product->get_parent_id() : 0;
						if ( ! empty( $parent_id ) ) {
							$product        = wc_get_product( $parent_id );
							$linked_coupons = $this->is_callable( $product, 'get_meta' ) ? $product->get_meta( '_coupon_title' ) : array();
						}
					}
					if ( ! empty( $linked_coupons ) ) {
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * Resend coupons for an order
		 *
		 * @param integer $order_id The order id.
		 */
		public function resend_coupons( $order_id = 0 ) {

			if ( empty( $order_id ) ) {
				return; // TODO: Show admin notice that no coupons are generated hence can't send email.
			}

			$order = wc_get_order( $order_id );

			$is_callable_order_get_meta = is_object( $order ) && $this->is_callable( $order, 'get_meta' );
			$sc_coupon_receiver_details = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'sc_coupon_receiver_details' ) : array();

			if ( empty( $sc_coupon_receiver_details ) ) {
				return; // TODO: Show admin notice that no coupons are generated hence can't send email.
			}

			$order_billing_email      = ( is_object( $order ) && $this->is_callable( $order, 'get_billing_email' ) ) ? $order->get_billing_email() : '';
			$order_billing_first_name = ( is_object( $order ) && $this->is_callable( $order, 'get_billing_first_name' ) ) ? $order->get_billing_first_name() : '';
			$order_billing_last_name  = ( is_object( $order ) && $this->is_callable( $order, 'get_billing_last_name' ) ) ? $order->get_billing_last_name() : '';

			$is_gift = ( true === $is_callable_order_get_meta ) ? $order->get_meta( 'is_gift' ) : '';

			$gift_certificate_sender_email = $order_billing_email;
			$gift_certificate_sender_name  = $order_billing_first_name . ' ' . $order_billing_last_name;

			$coupon_receiver_details = array();
			foreach ( $sc_coupon_receiver_details as $detail ) {
				if ( empty( $detail['email'] ) || empty( $detail['code'] ) ) {
					continue;
				}
				$email = $detail['email'];
				if ( empty( $coupon_receiver_details[ $email ] ) || ! is_array( $coupon_receiver_details[ $email ] ) ) {
					$coupon_receiver_details[ $email ] = array();
				}
				$coupon_receiver_details[ $email ][] = $detail;
			}

			if ( empty( $coupon_receiver_details ) ) {
				return; // TODO: Show admin notice that no coupons are generated hence can't send email.
			}

			$is_send_email  = $this->is_email_template_enabled();
			$combine_emails = $this->is_email_template_enabled( 'combine' );

			if ( 'yes' === $is_send_email ) {
				WC()->mailer();
				foreach ( $coupon_receiver_details as $receiver_email => $receiver_details ) {
					if ( 'yes' === $combine_emails ) {
						if ( count( $receiver_details ) === 1 ) {
							$coupon_code         = ( ! empty( $receiver_details[0]['code'] ) ) ? $receiver_details[0]['code'] : '';
							$message_from_sender = ( ! empty( $receiver_details[0]['message'] ) ) ? $receiver_details[0]['message'] : '';

							$coupon        = new WC_Coupon( $coupon_code );
							$coupon_amount = $this->get_amount( $coupon, true, $order );
							$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';

							$coupon_detail = array(
								'amount' => $coupon_amount,
								'code'   => $coupon_code,
							);

							$action_args = apply_filters(
								'wc_sc_email_coupon_notification_args',
								array(
									'order_id'            => $order_id,
									'email'               => $receiver_email,
									'coupon'              => $coupon_detail,
									'discount_type'       => $discount_type,
									'receiver_name'       => '',
									'message_from_sender' => $message_from_sender,
									'gift_certificate_sender_name' => $gift_certificate_sender_name,
									'gift_certificate_sender_email' => $gift_certificate_sender_email,
									'is_gift'             => $is_gift,
								)
							);
							// Trigger single email notification.
							do_action( 'wc_sc_email_coupon_notification', $action_args );
							return;
						}

						$action_args = apply_filters(
							'wc_sc_email_coupon_notification_args',
							array(
								'order_id'         => $order_id,
								'email'            => $receiver_email,
								'receiver_details' => $receiver_details,
								'gift_certificate_sender_name' => $gift_certificate_sender_name,
								'gift_certificate_sender_email' => $gift_certificate_sender_email,
								'is_gift'          => $is_gift,
							)
						);

						// Trigger combined email notification.
						do_action( 'wc_sc_combined_email_coupon_notification', $action_args );
					} else {
						foreach ( $receiver_details as $receiver_detail ) {
							$coupon_code         = ( ! empty( $receiver_detail['code'] ) ) ? $receiver_detail['code'] : '';
							$message_from_sender = ( ! empty( $receiver_detail['message'] ) ) ? $receiver_detail['message'] : '';

							$coupon        = new WC_Coupon( $coupon_code );
							$coupon_amount = $this->get_amount( $coupon, true, $order );
							$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';

							$coupon_detail = array(
								'amount' => $coupon_amount,
								'code'   => $coupon_code,
							);

							$action_args = apply_filters(
								'wc_sc_email_coupon_notification_args',
								array(
									'order_id'            => $order_id,
									'email'               => $receiver_email,
									'coupon'              => $coupon_detail,
									'discount_type'       => $discount_type,
									'receiver_name'       => '',
									'message_from_sender' => $message_from_sender,
									'gift_certificate_sender_name' => $gift_certificate_sender_name,
									'gift_certificate_sender_email' => $gift_certificate_sender_email,
									'is_gift'             => $is_gift,
								)
							);
							// Trigger single email notification.
							do_action( 'wc_sc_email_coupon_notification', $action_args );
						}
					}
				}
			}
		}

		/**
		 * Order action to ignore for email
		 *
		 * @return array
		 */
		public function order_actions_to_ignore_for_email() {
			return apply_filters(
				'wc_sc_order_actions_to_ignore_for_email',
				array(
					'woocommerce_order_action_wc_sc_regenerate_coupons',
				),
				array( 'source' => $this )
			);
		}

		/**
		 * Get current version of the plugin.
		 *
		 * @return string
		 */
		public function get_version() {
			if ( empty( $this->plugin_data ) ) {
				$this->plugin_data = self::get_smart_coupons_plugin_data();
			}
			return $this->plugin_data['Version'];
		}

		/**
		 * Function to compare with current version of Smart Coupons
		 *
		 * @param string $version Version number to compare.
		 * @return bool
		 */
		public function is_sc_gte( $version ) {
			return version_compare( $this->get_version(), $version, '>=' );
		}

		/**
		 * Get the url of path with respect to plugin.
		 *
		 * @param string $path The path.
		 * @return string
		 */
		public function get_plugin_directory_url( $path = '' ) {
			return plugins_url( $path, WC_SC_PLUGIN_FILE );
		}

		/**
		 * Get the path with respect to plugin.
		 *
		 * @param string $path The path.
		 * @return string
		 */
		public function get_plugin_directory( $path = '' ) {
			return plugin_dir_path( WC_SC_PLUGIN_FILE ) . trim( $path, '/' );
		}

		/**
		 * Function to compare with current version of PHP
		 *
		 * @param string $version Version number to compare.
		 * @return boolean
		 */
		public function is_php_gte( $version ) {
			return version_compare( PHP_VERSION, $version, '>=' );
		}

		/**
		 * Function to check if the passed id is of an order or not
		 *
		 * @param integer $post_id The post id.
		 * @return boolean
		 */
		public function is_hpos_order( $post_id = 0 ) {
			if ( ! empty( $post_id ) && is_numeric( $post_id ) && $this->is_hpos() && $this->is_callable( '\Automattic\WooCommerce\Utilities\OrderUtil', 'is_order' ) ) {
				return \Automattic\WooCommerce\Utilities\OrderUtil::is_order( $post_id, wc_get_order_types() );
			}
			return false;
		}

		/**
		 * Wrapper function to check if HPOS is enabled or not
		 *
		 * @return boolean
		 */
		public function is_hpos() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && $this->is_callable( '\Automattic\WooCommerce\Utilities\OrderUtil', 'custom_orders_table_usage_is_enabled' ) ) {
				return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
			}
			return false;
		}

		/**
		 * Function to declare WooCommerce HPOS related compatibility status
		 */
		public function hpos_compat_declaration() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-smart-coupons/woocommerce-smart-coupons.php', true );
			}
		}

		/**
		 * Function to declare WooCommerce Blocks related compatibility status
		 */
		public function blocks_compat_declaration() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', 'woocommerce-smart-coupons/woocommerce-smart-coupons.php', false );
			}
		}

	}//end class

} // End class exists check
