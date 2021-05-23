<?php
/**
 * Main class for Smart Coupons
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     2.4.0
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
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 3.3.0
		 */
		public function __wakeup() {
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
			add_filter( 'woocommerce_coupon_validate_expiry_date', array( $this, 'validate_expiry_time' ), 10, 3 );
			add_filter( 'woocommerce_apply_individual_use_coupon', array( $this, 'smart_coupons_override_individual_use' ), 10, 3 );
			add_filter( 'woocommerce_apply_with_individual_use_coupon', array( $this, 'smart_coupons_override_with_individual_use' ), 10, 4 );

			add_action( 'restrict_manage_posts', array( $this, 'woocommerce_restrict_manage_smart_coupons' ), 20 );
			add_action( 'admin_init', array( $this, 'woocommerce_export_coupons' ) );

			add_action( 'personal_options_update', array( $this, 'my_profile_update' ) );
			add_action( 'edit_user_profile_update', array( $this, 'my_profile_update' ) );

			add_filter( 'generate_smart_coupon_action', array( $this, 'generate_smart_coupon_action' ), 1, 10 );

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
			add_action( 'publish_future_post', array( $this, 'process_published_scheduled_coupon' ) );
			add_action( 'before_delete_post', array( $this, 'delete_scheduled_coupon_actions' ) );
			add_action( 'admin_footer', array( $this, 'enqueue_admin_footer_scripts' ) );
			add_action( 'wp_ajax_wc_sc_check_scheduled_coupon_actions', array( $this, 'check_scheduled_coupon_actions' ) );

			// Filter to modify discount amount for percentage type coupon.
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_coupon_discount_amount' ), 10, 5 );

			// Filter to add default values to coupon meta fields.
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );

			// Filter to validate coupon expiry time.
			add_filter( 'woocommerce_coupon_validate_expiry_date', array( $this, 'validate_coupon_expiry_time' ), 10, 2 );

			// Filter to register Smart Coupons' email classes.
			add_filter( 'woocommerce_email_classes', array( $this, 'register_email_classes' ) );

			add_filter( 'woocommerce_hold_stock_for_checkout', array( $this, 'hold_stock_for_checkout' ) );

			add_action( 'wc_sc_generate_coupon', array( $this, 'generate_coupon' ) );
			add_action( 'wc_sc_paint_coupon', array( $this, 'paint_coupon' ) );

			add_filter( 'woocommerce_rest_api_get_rest_namespaces', array( $this, 'rest_namespace' ) );

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

			include_once 'compat/class-sa-wc-compatibility-2-5.php';
			include_once 'compat/class-sa-wc-compatibility-2-6.php';
			include_once 'compat/class-sa-wc-compatibility-3-0.php';
			include_once 'compat/class-sa-wc-compatibility-3-1.php';
			include_once 'compat/class-sa-wc-compatibility-3-2.php';
			include_once 'compat/class-sa-wc-compatibility-3-3.php';
			include_once 'compat/class-sa-wc-compatibility-3-4.php';
			include_once 'compat/class-sa-wc-compatibility-3-5.php';
			include_once 'compat/class-sa-wc-compatibility-3-6.php';
			include_once 'compat/class-sa-wc-compatibility-3-7.php';
			include_once 'compat/class-sa-wc-compatibility-3-8.php';
			include_once 'compat/class-sa-wc-compatibility-3-9.php';
			include_once 'compat/class-sa-wc-compatibility-4-0.php';
			include_once 'compat/class-sa-wc-compatibility-4-1.php';
			include_once 'compat/class-sa-wc-compatibility-4-2.php';
			include_once 'compat/class-sa-wc-compatibility-4-3.php';
			include_once 'compat/class-sa-wc-compatibility-4-4.php';
			include_once 'compat/class-wc-sc-wpml-compatibility.php';
			include_once 'compat/class-wcopc-sc-compatibility.php';
			include_once 'compat/class-wcs-sc-compatibility.php';
			include_once 'compat/class-wc-sc-wmc-compatibility.php';

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
			include_once 'class-wc-sc-coupons-by-location.php';
			include_once 'class-wc-sc-coupons-by-payment-method.php';
			include_once 'class-wc-sc-coupons-by-shipping-method.php';
			include_once 'class-wc-sc-coupons-by-user-role.php';
			include_once 'class-wc-sc-coupons-by-product-attribute.php';
			include_once 'class-wc-sc-coupons-by-taxonomy.php';
			include_once 'class-wc-sc-coupon-message.php';
			include_once 'class-wc-sc-coupon-categories.php';

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
		 * @param int $expiry_date Expirty date of coupon.
		 * @return string $expires_string Formatted expiry date
		 */
		public function get_expiration_format( $expiry_date ) {

			if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
				$expiry_date = $expiry_date->getTimestamp();
			} elseif ( ! is_int( $expiry_date ) ) {
				$expiry_date = strtotime( $expiry_date );
			}

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

			foreach ( $coupon_title as $email => $coupon ) {

				if ( empty( $email ) ) {
					$email = $gift_certificate_sender_email;
				}

				$amount      = $coupon['amount'];
				$coupon_code = strtolower( $coupon['code'] );

				if ( ! empty( $order_id ) ) {
					$coupon_receiver_details = get_post_meta( $order_id, 'sc_coupon_receiver_details', true );
					if ( ! is_array( $coupon_receiver_details ) || empty( $coupon_receiver_details ) ) {
						$coupon_receiver_details = array();
					}
					$coupon_receiver_details[] = array(
						'code'    => $coupon_code,
						'amount'  => $amount,
						'email'   => $email,
						'message' => $message_from_sender,
					);
					update_post_meta( $order_id, 'sc_coupon_receiver_details', $coupon_receiver_details );
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
					$schedule_gift_sending = get_post_meta( $order_id, 'wc_sc_schedule_gift_sending', true );
				}

				$is_schedule_gift_sending = 'no';
				if ( 'yes' === $schedule_gift_sending ) {
					$coupon_id               = wc_get_coupon_id_by_code( $coupon_code );
					$coupon_receiver_details = get_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details', true );
					$scheduled_coupon_code   = ( ! empty( $coupon_receiver_details['coupon_details']['code'] ) ) ? strtolower( $coupon_receiver_details['coupon_details']['code'] ) : '';
					if ( $scheduled_coupon_code === $coupon_code ) {
						$is_schedule_gift_sending = 'yes';
					}
				}

				if ( 'yes' === $is_send_email && ( 'no' === $combine_emails || 'yes' === $is_schedule_gift_sending ) ) {
					// Trigger email notification.
					do_action( 'wc_sc_email_coupon_notification', $action_args );
					if ( 'yes' === $is_schedule_gift_sending ) {
						// Delete receiver detail post meta as it is no longer necessary.
						delete_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details' );
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
				WC()->mailer();

				$is_gift = '';
				if ( ! empty( $order_id ) ) {
					$is_gift = get_post_meta( $order_id, 'is_gift', true );
				}

				$action_args = apply_filters(
					'wc_sc_email_coupon_notification_args',
					array(
						'order_id'                      => $order_id,
						'email'                         => $receiver_email,
						'receiver_details'              => $receiver_details,
						'gift_certificate_sender_name'  => $gift_certificate_sender_name,
						'gift_certificate_sender_email' => $gift_certificate_sender_email,
						'is_gift'                       => $is_gift,
					)
				);

				// Trigger email notification.
				do_action( 'wc_sc_combined_email_coupon_notification', $action_args );
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
				$actions_id = as_schedule_single_action( $sending_timestamp, 'wc_sc_send_scheduled_coupon_email', $action_args );
				if ( $actions_id ) {
					$scheduled_actions_ids = get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );
					if ( empty( $scheduled_actions_ids ) || ! is_array( $scheduled_actions_ids ) ) {
						$scheduled_actions_ids = array();
					}
					$scheduled_actions_ids[ $ref_key ] = $actions_id;
					// Stored actions ids in coupons so that we can delete them when coupon gets deleted or email is sent successfully.
					update_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', $scheduled_actions_ids );
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

				$coupon_status = get_post_status( $coupon_id );
				if ( 'publish' !== $coupon_status ) {
					return;
				}

				$coupon = new WC_Coupon( $coupon_id );
				$order  = wc_get_order( $order_id );
				if ( is_a( $coupon, 'WC_Coupon' ) && is_a( $order, 'WC_Order' ) ) {
					$sc_disable_email_restriction = get_post_meta( $parent_id, 'sc_disable_email_restriction', true );
					if ( $this->is_wc_gte_30() ) {
						$coupon_amount = $coupon->get_amount();
						$discount_type = $coupon->get_discount_type();
						$coupon_code   = $coupon->get_code();
					} else {
						$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}

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

					$is_gift = get_post_meta( $order_id, 'is_gift', true );

					// In case of auto generated coupons receiver's details are saved in generated coupon.
					if ( 'yes' === $auto_generate ) {
						$coupon_receiver_details = get_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details', true );
						if ( ! empty( $coupon_receiver_details ) && is_array( $coupon_receiver_details ) ) {
							$message_from_sender           = $coupon_receiver_details['message_from_sender'];
							$gift_certificate_sender_name  = $coupon_receiver_details['gift_certificate_sender_name'];
							$gift_certificate_sender_email = $coupon_receiver_details['gift_certificate_sender_email'];
						}
					} else {
						$receivers_messages = get_post_meta( $order_id, 'gift_receiver_message', true );
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
						$old_customers_email_ids   = (array) maybe_unserialize( get_post_meta( $coupon_id, 'customer_email', true ) );
						$old_customers_email_ids[] = $receiver_email;
						update_post_meta( $coupon_id, 'customer_email', $old_customers_email_ids );
					}

					if ( ! empty( $ref_key ) ) {
						$scheduled_actions_ids = get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );
						if ( isset( $scheduled_actions_ids[ $ref_key ] ) ) {
							unset( $scheduled_actions_ids[ $ref_key ] );
						}
						if ( ! empty( $scheduled_actions_ids ) ) {
							update_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', $scheduled_actions_ids );
						} else {
							// Delete scheduled action ids meta since it is empty now.
							delete_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids' );
						}
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

			$post_type = get_post_type( $coupon_id );
			if ( 'shop_coupon' !== $post_type ) {
				return false;
			}

			$coupon = new WC_Coupon( $coupon_id );
			if ( is_a( $coupon, 'WC_Coupon' ) ) {
				$order_id = get_post_meta( $coupon_id, 'generated_from_order_id', true );
				$order    = wc_get_order( $order_id );
				if ( is_a( $order, 'WC_Order' ) ) {
					$coupon_receiver_details = get_post_meta( $coupon_id, 'wc_sc_coupon_receiver_details', true );
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

				$scheduled_actions_ids = get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );

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

			$current_timestamp = time(); // Get GMT timestamp.

			// Check if time is already passed.
			if ( $current_timestamp > $timestamp ) {
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
					$scheduled_actions_ids = get_post_meta( $coupon_id, 'wc_sc_scheduled_actions_ids', true );
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
			global $store_credit_label;

			$all_discount_types = wc_get_coupon_types();

			if ( $this->is_wc_gte_30() ) {
				$coupon_id     = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				$coupon_amount = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

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
					$max_discount                 = get_post_meta( $coupon_id, 'wc_sc_max_discount', true );
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
								case 'excluded_product_ids':
									$product_names      = array();
									$data_count         = count( $data );
									$product_name_count = 0;
									for ( $i = 0; $i < $data_count && $product_name_count < 2; $i++ ) {
										$product = wc_get_product( $data[ $i ] );
										if ( is_object( $product ) && is_callable( array( $product, 'get_name' ) ) ) {
											$product_names[] = $product->get_name();
											$product_name_count++;
										}
									}
									/* translators: 1: Valid or Invalid 2: Product names */
									$descriptions[] = sprintf( __( '%1$s for %2$s', 'woocommerce-smart-coupons' ), ( ( 'product_ids' === $key ) ? __( 'Valid', 'woocommerce-smart-coupons' ) : __( 'Not valid', 'woocommerce-smart-coupons' ) ), implode( ', ', $product_names ) );
									break;
								case 'product_categories':
								case 'excluded_product_categories':
									if ( count( $data ) > 2 ) {
										$data = array_slice( $data, 0, 2 );
									}
									$terms = get_terms(
										array(
											'taxonomy' => 'product_cat',
											'include'  => $data,
											'fields'   => 'id=>name',
											'get'      => 'all',
										)
									);
									/* translators: 1: Valid or Invalid 2: category or categories 3: Product category names */
									$descriptions[] = sprintf( __( '%1$s for %2$s %3$s', 'woocommerce-smart-coupons' ), ( ( 'product_categories' === $key ) ? __( 'Valid', 'woocommerce-smart-coupons' ) : __( 'Not valid', 'woocommerce-smart-coupons' ) ), _n( 'category', 'categories', count( $data ), 'woocommerce-smart-coupons' ), implode( ', ', $terms ) );
									break;
								case 'date_expires':
									if ( $data instanceof WC_DateTime ) {
										$expiry_date = ( is_object( $data ) && is_callable( array( $data, 'getTimestamp' ) ) ) ? $data->getTimestamp() : 0;
										if ( ! empty( $expiry_date ) && is_int( $expiry_date ) && ! empty( $expiry_time ) ) {
											$expiry_date += $expiry_time; // Adding expiry time to expiry date.
										}
										if ( ! empty( $expiry_date ) ) {
											/* translators: 1: The expiry date */
											$descriptions[] = sprintf( __( 'Expiry: %s', 'woocommerce-smart-coupons' ), $this->get_expiration_format( $expiry_date ) );
										}
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
			$coupon_amount = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
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
					__( 'You don’t want to miss this...', 'woocommerce-smart-coupons' ),
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
								'%' . $wpdb->esc_like( $old_customers_email_id ) . '%',
								'shop_coupon'
							)
						);
						wp_cache_set( 'wc_sc_customers_coupon_ids_' . sanitize_key( $old_customers_email_id ), $result, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_customers_coupon_ids_' . sanitize_key( $old_customers_email_id ) );
					}

					if ( ! empty( $result ) ) {

						foreach ( $result as $post_id ) {

							$coupon_meta           = get_post_meta( $post_id, 'customer_email', true );
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
								update_post_meta( $post_id, 'customer_email', $coupon_meta );
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
					$discount_type = $coupon->get_discount_type();
				} else {
					$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				}
				if ( 'smart_coupon' === $discount_type && 'yes' === get_post_meta( $coupon_id, 'is_pick_price_of_product', true ) ) {
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
			// If coupon is not expired, no need to check it further.
			if ( false === $expired ) {
				return $expired;
			}

			$coupon_date_expires = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_date_expires' ) ) ) ? $coupon->get_date_expires() : ( ( is_object( $coupon ) && is_callable( array( $coupon, 'get_meta' ) ) ) ? $coupon->get_meta( 'date_expires' ) : 0 );
			if ( is_object( $coupon_date_expires ) && is_callable( array( $coupon_date_expires, 'getTimestamp' ) ) ) {
				$expiry_date = $coupon_date_expires->getTimestamp();
			} elseif ( ! empty( $coupon_date_expires ) && is_numeric( $coupon_date_expires ) ) {
				$expiry_date = intval( $coupon_date_expires );
			} else {
				return $expired;
			}

			if ( ! empty( $expiry_date ) ) {
				$expiry_time = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_meta' ) ) ) ? $coupon->get_meta( 'wc_sc_expiry_time' ) : null;
				if ( is_null( $expiry_time ) || in_array( $expiry_time, array( '', 0, '0' ), true ) ) {
					return $expired;
				} elseif ( ! empty( $expiry_time ) ) {
					$expiry_timestamp = intval( $expiry_date ) + intval( $expiry_time );
					return time() > $expiry_timestamp;
				}
			}

			return $expired;
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
				add_action( 'woocommerce_after_calculate_totals', array( $this, 'smart_coupons_after_calculate_totals' ), 999 );
			} else {
				add_action( 'woocommerce_after_calculate_totals', array( $this, 'smart_coupons_after_calculate_totals' ), 999 );
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
			}
		}

		/**
		 * Function to set store credit amount for orders that are manually created and updated from backend
		 *
		 * @param bool     $and_taxes Calc taxes if true.
		 * @param WC_Order $order Order object.
		 */
		public function order_calculate_discount_amount( $and_taxes, $order ) {

			$order_actions = array( 'woocommerce_add_coupon_discount', 'woocommerce_calc_line_taxes', 'woocommerce_save_order_items' );

			$post_action    = ( ! empty( $_POST['action'] ) ) ? wc_clean( wp_unslash( $_POST['action'] ) ) : ''; // phpcs:ignore
			$post_post_type = ( ! empty( $_POST['post_type'] ) ) ? wc_clean( wp_unslash( $_POST['post_type'] ) ) : ''; // phpcs:ignore

			if ( $order instanceof WC_Order && ! empty( $post_action ) && ( in_array( $post_action, $order_actions, true ) || ( ! empty( $post_post_type ) && 'shop_order' === $post_post_type && 'editpost' === $post_action ) ) ) {
				if ( ! is_object( $order ) || ! is_callable( array( $order, 'get_id' ) ) ) {
					return;
				}
				$order_id = $order->get_id();
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
							$discount_amount            = ( is_object( $item ) && is_callable( array( $item, 'get_discount' ) ) ) ? $item->get_discount() : wc_get_order_item_meta( $item_id, 'discount_amount', true );
							$smart_coupons_contribution = get_post_meta( $order_id, 'smart_coupons_contribution', true );
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

							update_post_meta( $order_id, 'smart_coupons_contribution', $smart_coupons_contribution );

							if ( 'woocommerce_add_coupon_discount' === $post_action && $order->has_status( array( 'on-hold', 'auto-draft', 'pending' ) ) && did_action( 'sc_after_order_calculate_discount_amount' ) <= 0 ) {
								do_action( 'sc_after_order_calculate_discount_amount', $order_id );
							}
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

			if ( $coupon instanceof WC_Coupon && $order instanceof WC_Order ) {
				$coupon_amount             = $coupon->get_amount();
				$discount_type             = $coupon->get_discount_type();
				$coupon_code               = $coupon->get_code();
				$coupon_product_ids        = $coupon->get_product_ids();
				$coupon_product_categories = $coupon->get_product_categories();

				if ( 'smart_coupon' === $discount_type ) {

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
		 * Function to apply smart coupons discount
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
				foreach ( $applied_coupons as $code ) {
					$request_wc_ajax    = ( ! empty( $_REQUEST['wc-ajax'] ) ) ? wc_clean( wp_unslash( $_REQUEST['wc-ajax'] ) ) : ''; // phpcs:ignore
					$ignore_ajax_action = array( 'update_order_review', 'checkout' );
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
		 * Function to apply smart coupons discount after calculating tax
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
					$cart->set_total( $total );
				} else {
					$cart->total = $total;
				}

				do_action( 'smart_coupons_after_calculate_totals' );

			}

		}


		/**
		 * Function to do action 'smart_coupons_after_calculate_totals' since WooCommerce Services plugin triggers 'woocommerce_cart_reset' in its function for 'woocommerce_after_calculate_totals' action causing miscalculation in did_action( 'smart_coupons_after_calculate_totals' ) hook.
		 */
		public function woocommerce_cart_reset() {

			$cart_reset_action_count         = did_action( 'woocommerce_cart_reset' );
			$sc_after_calculate_action_count = did_action( 'smart_coupons_after_calculate_totals' );

			// This is to match counter for 'smart_coupons_after_calculate_totals' hook with 'woocommerce_cart_reset' counter since we are using these two counters to prevent store credit being appplied multiple times.
			if ( $sc_after_calculate_action_count < $cart_reset_action_count ) {
				do_action( 'smart_coupons_after_calculate_totals' );
			}

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

				if ( $coupon->is_valid() && $coupon->is_type( 'percent' ) ) {
					if ( $this->is_wc_gte_30() ) {
						$coupon_id = $coupon->get_id();
					} else {
						$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					}
					$max_discount = get_post_meta( $coupon_id, 'wc_sc_max_discount', true );
					if ( ! empty( $max_discount ) && is_numeric( $max_discount ) && is_array( $cart_item ) && ! empty( $cart_item ) ) {
						$inc_tax                      = wc_prices_include_tax();
						$coupon_product_ids           = $coupon->get_product_ids();
						$coupon_excluded_product_ids  = $coupon->get_excluded_product_ids();
						$coupon_category_ids          = $coupon->get_product_categories();
						$coupon_excluded_category_ids = $coupon->get_excluded_product_categories();
						$cart_items_subtotal          = 0;
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
						$max_discount_session_start   = false;
						$max_discount_session         = WC()->session->get( 'wc_sc_max_discount_session' );
						if ( empty( $max_discount_session ) || ! is_array( $max_discount_session ) ) {
							$max_discount_session = array();
						}
						if ( empty( $max_discount_session[ $coupon_id ] ) || ! is_array( $max_discount_session[ $coupon_id ] ) ) {
							$max_discount_session[ $coupon_id ]           = array();
							$max_discount_session_start                   = true;
							$max_discount_session[ $coupon_id ]['amount'] = $max_discount;
							$max_discount_session[ $coupon_id ]['count']  = ( isset( WC()->cart ) && is_callable( array( WC()->cart, 'get_cart' ) ) ) ? count( WC()->cart->get_cart() ) : 0;
						}

						if ( true === $is_restricted ) {
							if ( class_exists( 'WC_Discounts' ) && isset( WC()->cart ) ) {
								$wc_cart           = WC()->cart;
								$wc_discounts      = new WC_Discounts( $wc_cart );
								$items_to_validate = array();
								if ( is_callable( array( $wc_discounts, 'get_items_to_validate' ) ) ) {
									$items_to_validate = $wc_discounts->get_items_to_validate();
								} elseif ( is_callable( array( $wc_discounts, 'get_items' ) ) ) {
									$items_to_validate = $wc_discounts->get_items();
								} elseif ( isset( $wc_discounts->items ) && is_array( $wc_discounts->items ) ) {
									$items_to_validate = $wc_discounts->items;
								}
								if ( ! empty( $items_to_validate ) && is_array( $items_to_validate ) ) {
									if ( true === $max_discount_session_start ) {
										$max_discount_session[ $coupon_id ]['count'] = count( $items_to_validate );
									}
									foreach ( $items_to_validate as $item ) {
										$item_to_apply = clone $item; // Clone the item so changes to wc_discounts item do not affect the originals.

										if ( 0 === $wc_discounts->get_discounted_price_in_cents( $item_to_apply ) || 0 >= $item_to_apply->quantity ) {
											continue;
										}

										if ( ! $coupon->is_valid_for_product( $item_to_apply->product, $item_to_apply->object ) && ! $coupon->is_valid_for_cart() ) {
											continue;
										}

										if ( true === $inc_tax ) {
											$cart_items_subtotal += $item_to_apply->object['line_subtotal'] + $item_to_apply->object['line_subtotal_tax'];
										} else {
											$cart_items_subtotal += $item_to_apply->object['line_subtotal'];
										}
									}
								}
							}
						} else {
							if ( true === $inc_tax ) {
								$cart_items_subtotal = WC()->cart->subtotal;
							} else {
								$cart_items_subtotal = WC()->cart->subtotal_ex_tax;
							}
						}

						if ( 0 !== $cart_items_subtotal ) {
							$cart_item_qty = isset( $cart_item['quantity'] ) ? $cart_item['quantity'] : 1;

							if ( true === $inc_tax ) {
								$discount_percent = ( wc_get_price_including_tax( $cart_item['data'] ) * $cart_item_qty ) / $cart_items_subtotal;
							} else {
								$discount_percent = ( wc_get_price_excluding_tax( $cart_item['data'] ) * $cart_item_qty ) / $cart_items_subtotal;
							}

							$discount_percent = round( $discount_percent, 4 ); // A percentage value should always be rounded with 4 decimal point.

							if ( $this->is_wc_gte_32() ) {
								$max_discount_amount = ( $max_discount * $discount_percent );
							} else {
								$max_discount_amount = ( $max_discount * $discount_percent ) / $cart_item_qty;
							}

							$max_discount_amount = wc_round_discount( $max_discount_amount, wc_get_price_decimals() );

							$is_round_max_discount = $max_discount_amount < $discount;

							$discount = min( $max_discount_amount, $discount );

							$max_discount_session[ $coupon_id ]['amount'] -= $discount;
							$max_discount_session[ $coupon_id ]['count']--;

							if ( 0 === $max_discount_session[ $coupon_id ]['count'] ) {
								if ( ! empty( $max_discount_session[ $coupon_id ]['amount'] ) && true === $is_round_max_discount ) {
									$discount += $max_discount_session[ $coupon_id ]['amount'];
								}
								unset( $max_discount_session[ $coupon_id ] );
							}

							WC()->session->set( 'wc_sc_max_discount_session', $max_discount_session );

						}
					}
				}
			}

			return $discount;
		}

		/**
		 * Function to check coupon expiry time

		 * @param  boolean $is_expired Is coupon expired.
		 * @param  object  $coupon   The coupon object.
		 * @return boolean  $is_expired Is coupon expired.
		 */
		public function validate_coupon_expiry_time( $is_expired = false, $coupon = object ) {

			// Proceed only if WooCommerce has flag this coupon as expired.
			if ( true === $is_expired ) {
				if ( $this->is_wc_gte_30() ) {
					$coupon_id = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				} else {
					$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				}

				if ( 0 !== $coupon_id ) {
					$expiry_date_timestamp = 0;
					if ( $this->is_wc_gte_30() ) {
						if ( is_object( $coupon ) && is_callable( array( $coupon, 'get_date_expires' ) ) ) {
							$expiry_date_object = $coupon->get_date_expires();
							if ( is_a( $expiry_date_object, 'WC_DateTime' ) && is_callable( array( $expiry_date_object, 'getTimestamp' ) ) ) {
								$expiry_date_timestamp = $expiry_date_object->getTimestamp();
							}
						}
					} else {
						$expiry_date_timestamp = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : 0;
						if ( ! is_int( $expiry_date_timestamp ) ) {
							$expiry_date_timestamp = strtotime( $expiry_date_timestamp );
						}
					}

					if ( 0 !== $expiry_date_timestamp ) {
						$expiry_time = (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
						if ( ! empty( $expiry_time ) ) {
							$expiry_date_timestamp = $expiry_date_timestamp + $expiry_time; // Adding expiry time to expiry date.
						}

						$current_timestamp = time();
						if ( $current_timestamp <= $expiry_date_timestamp ) {
							$is_expired = false;
						}
					}
				}
			}

			return $is_expired;
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

			if ( $coupon instanceof WC_Coupon ) {

				if ( $coupon->is_valid() && $coupon->is_type( 'smart_coupon' ) ) {

					if ( $this->is_wc_gte_30() ) {
						$coupon_amount             = $coupon->get_amount();
						$coupon_code               = $coupon->get_code();
						$coupon_product_ids        = $coupon->get_product_ids();
						$coupon_product_categories = $coupon->get_product_categories();
					} else {
						$coupon_amount             = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$coupon_code               = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
						$coupon_product_ids        = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : array();
						$coupon_product_categories = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : array();
					}

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

								if ( ! $continue && in_array( $product['product_id'], $coupon_product_ids, true ) || in_array( $product['variation_id'], $coupon_product_ids, true ) || in_array( $product['data']->get_parent(), $coupon_product_ids, true ) ) {

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
					$coupon_code = $coupon->get_code();
				} else {
					$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}

				if ( $cart_contains_subscription ) {
					if ( WCS_SC_Compatibility::is_wcs_gte( '2.0.10' ) ) {
						if ( $this->is_wc_greater_than( '3.1.2' ) ) {
							$coupon_discount_totals = WC()->cart->get_coupon_discount_totals();
							if ( empty( $coupon_discount_totals ) || ! is_array( $coupon_discount_totals ) ) {
								$coupon_discount_totals = array();
							}
							if ( empty( $coupon_discount_totals[ $coupon_code ] ) ) {
								$coupon_discount_totals[ $coupon_code ] = $discount;
							} else {
								$coupon_discount_totals[ $coupon_code ] += $discount;
							}
							WC()->cart->set_coupon_discount_totals( $coupon_discount_totals );
						} else {
							$coupon_discount_amounts = ( is_object( WC()->cart ) && isset( WC()->cart->coupon_discount_amounts ) ) ? WC()->cart->coupon_discount_amounts : array();
							if ( empty( $coupon_discount_amounts ) || ! is_array( $coupon_discount_amounts ) ) {
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
						$coupon_discount_totals = WC()->cart->get_coupon_discount_totals();
						if ( empty( $coupon_discount_totals ) || ! is_array( $coupon_discount_totals ) ) {
							$coupon_discount_totals = array();
						}
						if ( empty( $coupon_discount_totals[ $coupon_code ] ) ) {
							$coupon_discount_totals[ $coupon_code ] = $discount;
						} else {
							$coupon_discount_totals[ $coupon_code ] += $discount;
						}
						WC()->cart->set_coupon_discount_totals( $coupon_discount_totals );
					} else {
						$coupon_discount_amounts = ( is_object( WC()->cart ) && isset( WC()->cart->coupon_discount_amounts ) ) ? WC()->cart->coupon_discount_amounts : array();
						if ( empty( $coupon_discount_amounts ) || ! is_array( $coupon_discount_amounts ) ) {
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

				if ( empty( $smart_coupon_credit_used ) || ! is_array( $smart_coupon_credit_used ) ) {
					$smart_coupon_credit_used = array();
				}
				if ( empty( $smart_coupon_credit_used[ $coupon_code ] ) || ( $cart_contains_subscription && ( 'combined_total' === $calculation_type || 'sign_up_fee_total' === $calculation_type ) ) ) {
					$smart_coupon_credit_used[ $coupon_code ] = $discount;
				} else {
					$smart_coupon_credit_used[ $coupon_code ] += $discount;
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
						$discount         = ( is_object( $_coupon ) && is_callable( array( $_coupon, 'get_amount' ) ) ) ? $_coupon->get_amount() : 0;
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
		public function is_smart_coupon_valid( $valid, $coupon, $discounts ) {

			if ( $this->is_wc_gte_30() ) {
				$coupon_amount = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
				$coupon_code   = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			} else {
				$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
			}

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

			$for_new_user = get_post_meta( $coupon_id, 'sc_restrict_to_new_user', true );

			if ( 'yes' === $for_new_user ) {

				$user_id_1 = 0;
				$user_id_2 = 0;
				$user_id_3 = 0;

				$current_user = wp_get_current_user();

				$email = ( ! empty( $current_user->data->user_email ) ) ? $current_user->data->user_email : '';

				$email = ( ! empty( $_REQUEST['billing_email'] ) ) ? sanitize_email( wp_unslash( $_REQUEST['billing_email'] ) ) : $email; // phpcs:ignore

				if ( ! empty( $email ) && is_email( $email ) ) {

					$user_id_1 = wp_cache_get( 'wc_sc_user_id_by_user_email_' . sanitize_key( $email ), 'woocommerce_smart_coupons' );
					if ( false === $user_id_1 ) {
						$user_id_1 = $wpdb->get_var( // phpcs:ignore
							$wpdb->prepare(
								"SELECT ID
									FROM {$wpdb->prefix}users
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
									FROM {$wpdb->prefix}usermeta
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

				if ( ! empty( $user_ids ) ) {

					$unique_user_ids = array_unique( $user_ids );

					$order_id = wp_cache_get( 'wc_sc_order_for_user_id_' . implode( '_', $unique_user_ids ), 'woocommerce_smart_coupons' );

					if ( false === $order_id ) {
						$query = $wpdb->prepare(
							"SELECT ID
								FROM $wpdb->posts AS p
								LEFT JOIN $wpdb->postmeta AS pm
									ON ( p.ID = pm.post_id AND pm.meta_key = %s )
								WHERE p.post_type = %s",
							'_customer_user',
							'shop_order'
						);

						if ( ! empty( $valid_order_statuses ) && ! empty( $statuses_placeholder ) ) {
							// phpcs:disable
							$query .= $wpdb->prepare(
								' AND p.post_status IN (' . implode( ',', $statuses_placeholder ) . ')',
								$valid_order_statuses
							);
							// phpcs:enable
						}

						$how_many_user_ids = count( $user_ids );
						$id_placeholder    = array_fill( 0, $how_many_user_ids, '%s' );

						// phpcs:disable
						$query .= $wpdb->prepare(
							' AND pm.meta_value IN (' . implode( ',', $id_placeholder ) . ')',
							$user_ids
						);
						// phpcs:enable

						$order_id = $wpdb->get_var( $query ); // phpcs:ignore

						wp_cache_set( 'wc_sc_order_for_user_id_' . implode( '_', $unique_user_ids ), $order_id, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_order_for_user_id_' . implode( '_', $unique_user_ids ) );
					}

					if ( ! empty( $order_id ) ) {
						throw new Exception( __( 'This coupon is valid for the first order only.', 'woocommerce-smart-coupons' ) );
					}
				} elseif ( ! empty( $email ) ) {

					$order_id = wp_cache_get( 'wc_sc_order_id_by_billing_email_' . sanitize_key( $email ), 'woocommerce_smart_coupons' );

					if ( false === $order_id ) {
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

						if ( ! empty( $valid_order_statuses ) && ! empty( $statuses_placeholder ) ) {
							// phpcs:disable
							$query .= $wpdb->prepare(
								' AND p.post_status IN (' . implode( ',', $statuses_placeholder ) . ')',
								$valid_order_statuses
							);
							// phpcs:enable
						}

						$order_id = $wpdb->get_var( $query ); // phpcs:ignore

						wp_cache_set( 'wc_sc_order_id_by_billing_email_' . sanitize_key( $email ), $order_id, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_order_id_by_billing_email_' . sanitize_key( $email ) );
					}

					if ( ! empty( $order_id ) ) {
						throw new Exception( __( 'This coupon is valid for the first order only.', 'woocommerce-smart-coupons' ) );
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

			$chars = array_merge( range( 'a', 'z' ), range( '1', '9' ) );
			$chars = apply_filters(
				'wc_sc_coupon_code_allowed_characters',
				$chars,
				array(
					'source'             => $this,
					'email'              => $email,
					'coupon_object'      => $coupon,
					'coupon_code_length' => $coupon_code_length,
				)
			);
			for ( $rand = 1; $rand <= $coupon_code_length; $rand++ ) {
				$random       = rand( 0, count( $chars ) - 1 ); // phpcs:ignore
				$unique_code .= $chars[ $random ];
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_id = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
			} else {
				$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
			}

			if ( ! empty( $coupon_id ) && get_post_meta( $coupon_id, 'auto_generate_coupon', true ) === 'yes' ) {
				$prefix      = get_post_meta( $coupon_id, 'coupon_title_prefix', true );
				$suffix      = get_post_meta( $coupon_id, 'coupon_title_suffix', true );
				$unique_code = $prefix . $unique_code . $suffix;
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
			return apply_filters( 'generate_smart_coupon_action', $email, $amount, $order_id, $coupon, $discount_type, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $sending_timestamp );
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
				$temp_email = get_post_meta( $order_id, 'temp_gift_card_receivers_emails', true );
				if ( ! empty( $temp_email ) && count( $temp_email ) > 0 ) {
					$email = $temp_email;
				}
				$emails = ( ! empty( $coupon_id ) ) ? array_count_values( $email[ $coupon_id ] ) : array();
			}

			if ( ! empty( $order_id ) ) {
				$receivers_messages    = get_post_meta( $order_id, 'gift_receiver_message', true );
				$schedule_gift_sending = get_post_meta( $order_id, 'wc_sc_schedule_gift_sending', true );
				$sending_timestamps    = get_post_meta( $order_id, 'gift_sending_timestamp', true );
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
				);

				$should_schedule = isset( $schedule_gift_sending ) && 'yes' === $schedule_gift_sending && $this->is_valid_timestamp( $sending_timestamp ) ? true : false;

				if ( $should_schedule ) {
					$smart_coupon_args['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $sending_timestamp );
				}

				$smart_coupon_id = wp_insert_post( $smart_coupon_args );

				$type                   = ( ! empty( $discount_type ) ) ? $discount_type : 'smart_coupon';
				$individual_use         = ( ! empty( $is_individual_use ) ) ? $is_individual_use : 'no';
				$minimum_amount         = ( ! empty( $coupon_minimum_amount ) ) ? $coupon_minimum_amount : '';
				$maximum_amount         = ( ! empty( $coupon_maximum_amount ) ) ? $coupon_maximum_amount : '';
				$product_ids            = ( ! empty( $coupon_product_ids ) ) ? implode( ',', $coupon_product_ids ) : '';
				$exclude_product_ids    = ( ! empty( $coupon_excluded_product_ids ) ) ? implode( ',', $coupon_excluded_product_ids ) : '';
				$usage_limit            = ( ! empty( $coupon_usage_limit ) ) ? $coupon_usage_limit : '';
				$usage_limit_per_user   = ( ! empty( $coupon_usage_limit_per_user ) ) ? $coupon_usage_limit_per_user : '';
				$limit_usage_to_x_items = ( ! empty( $coupon_limit_usage_to_x_items ) ) ? $coupon_limit_usage_to_x_items : '';
				$sc_coupon_validity     = ( ! empty( $coupon_id ) ) ? get_post_meta( $coupon_id, 'sc_coupon_validity', true ) : '';

				if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
					$expiry_date = $expiry_date->getTimestamp();
				} elseif ( ! is_int( $expiry_date ) ) {
					$expiry_date = strtotime( $expiry_date );
				}

				if ( ! empty( $coupon_id ) && ! empty( $sc_coupon_validity ) ) {
					$is_parent_coupon_expired = ( ! empty( $expiry_date ) && ( $expiry_date < time() ) ) ? true : false;
					if ( ! $is_parent_coupon_expired ) {
						$validity_suffix = get_post_meta( $coupon_id, 'validity_suffix', true );
						// In case of scheduled coupon, expiry date is calculated from scheduled publish date.
						if ( isset( $smart_coupon_args['post_date_gmt'] ) ) {
							$expiry_date = strtotime( $smart_coupon_args['post_date_gmt'] . "+$sc_coupon_validity $validity_suffix" );
						} else {
							$expiry_date = strtotime( "+$sc_coupon_validity $validity_suffix" );
						}
					}
				}

				$free_shipping              = ( ! empty( $is_free_shipping ) ) ? $is_free_shipping : 'no';
				$product_categories         = ( ! empty( $coupon_product_categories ) ) ? $coupon_product_categories : array();
				$exclude_product_categories = ( ! empty( $coupon_excluded_product_categories ) ) ? $coupon_excluded_product_categories : array();

				update_post_meta( $smart_coupon_id, 'discount_type', $type );

				if ( 'smart_coupon' === $type ) {
					update_post_meta( $smart_coupon_id, 'wc_sc_original_amount', $amount );
				}

				update_post_meta( $smart_coupon_id, 'coupon_amount', $amount );
				update_post_meta( $smart_coupon_id, 'individual_use', $individual_use );
				update_post_meta( $smart_coupon_id, 'minimum_amount', $minimum_amount );
				update_post_meta( $smart_coupon_id, 'maximum_amount', $maximum_amount );
				update_post_meta( $smart_coupon_id, 'product_ids', $product_ids );
				update_post_meta( $smart_coupon_id, 'exclude_product_ids', $exclude_product_ids );
				update_post_meta( $smart_coupon_id, 'usage_limit', $usage_limit );
				update_post_meta( $smart_coupon_id, 'usage_limit_per_user', $usage_limit_per_user );
				update_post_meta( $smart_coupon_id, 'limit_usage_to_x_items', $limit_usage_to_x_items );

				if ( $this->is_wc_gte_30() ) {
					if ( ! empty( $expiry_date ) ) {
						update_post_meta( $smart_coupon_id, 'date_expires', $expiry_date );
					}
				} else {
					$expiry_date = ( ! empty( $expiry_date ) ) ? gmdate( 'Y-m-d', intval( $expiry_date ) ) : '';
					update_post_meta( $smart_coupon_id, 'expiry_date', $expiry_date );
				}

				$is_disable_email_restriction = ( ! empty( $coupon_id ) ) ? get_post_meta( $coupon_id, 'sc_disable_email_restriction', true ) : '';
				if ( empty( $is_disable_email_restriction ) || 'no' === $is_disable_email_restriction ) {
					// Update customer_email now if coupon is not scheduled otherwise it would be updated by action scheduler later on.
					if ( ! $should_schedule ) {
						update_post_meta( $smart_coupon_id, 'customer_email', array( $email_id ) );
					}
				}

				if ( ! $this->is_wc_gte_30() ) {
					$apply_before_tax = ( ! empty( $coupon->apply_before_tax ) ) ? $coupon->apply_before_tax : 'no';
					update_post_meta( $smart_coupon_id, 'apply_before_tax', $apply_before_tax );
				}

				// Add terms to auto-generated if found in parent coupon.
				$coupon_terms = get_the_terms( $coupon_id, 'sc_coupon_category' );
				if ( ! empty( $coupon_terms ) ) {
					$term_ids = array_column( $coupon_terms, 'term_id' );
					wp_set_object_terms( $smart_coupon_id, $term_ids, 'sc_coupon_category', false );
				}

				update_post_meta( $smart_coupon_id, 'free_shipping', $free_shipping );
				update_post_meta( $smart_coupon_id, 'product_categories', $product_categories );
				update_post_meta( $smart_coupon_id, 'exclude_product_categories', $exclude_product_categories );
				update_post_meta( $smart_coupon_id, 'exclude_sale_items', $is_exclude_sale_items );
				update_post_meta( $smart_coupon_id, 'generated_from_order_id', $order_id );

				$sc_restrict_to_new_user = get_post_meta( $coupon_id, 'sc_restrict_to_new_user', true );
				update_post_meta( $smart_coupon_id, 'sc_restrict_to_new_user', $sc_restrict_to_new_user );

				$wc_sc_max_discount = get_post_meta( $coupon_id, 'wc_sc_max_discount', true );
				if ( ! empty( $wc_sc_max_discount ) ) {
					update_post_meta( $smart_coupon_id, 'wc_sc_max_discount', $wc_sc_max_discount );
				}

				if ( $this->is_wc_gte_32() ) {
					$wc_sc_expiry_time = (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
					if ( ! empty( $wc_sc_expiry_time ) ) {
						update_post_meta( $smart_coupon_id, 'wc_sc_expiry_time', $wc_sc_expiry_time );
					}
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
					$is_gift = get_post_meta( $order_id, 'is_gift', true );
				} else {
					$is_gift = 'no';
				}

				if ( is_array( $email ) && ! empty( $coupon_id ) && isset( $email[ $coupon_id ] ) ) {
					$message_index = array_search( $email_id, $email[ $coupon_id ], true );
					if ( false !== $message_index && isset( $receivers_messages[ $coupon_id ][ $message_index ] ) && ! empty( $receivers_messages[ $coupon_id ][ $message_index ] ) ) {
						$message_from_sender = $receivers_messages[ $coupon_id ][ $message_index ];
						unset( $email[ $coupon_id ][ $message_index ] );
						update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
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
					update_post_meta( $smart_coupon_id, 'wc_sc_coupon_receiver_details', $wc_sc_coupon_receiver_details );
				} else {
					$is_send_email  = $this->is_email_template_enabled();
					$combine_emails = $this->is_email_template_enabled( 'combine' );
					if ( 'yes' === $is_send_email ) {
						if ( 'yes' === $combine_emails ) {
							$coupon_receiver_details = get_post_meta( $order_id, 'sc_coupon_receiver_details', true );
							if ( empty( $coupon_receiver_details ) || ! is_array( $coupon_receiver_details ) ) {
								$coupon_receiver_details = array();
							}
							$coupon_receiver_details[] = array(
								'code'    => $generated_coupon_details['code'],
								'amount'  => $amount,
								'email'   => $email_id,
								'message' => $message_from_sender,
							);
							update_post_meta( $order_id, 'sc_coupon_receiver_details', $coupon_receiver_details );
						} else {
							$this->sa_email_coupon( array( $email_id => $generated_coupon_details ), $type, $order_id, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $is_gift );
						}
					} else {
						if ( ! empty( $order_id ) ) {
							$coupon_receiver_details = get_post_meta( $order_id, 'sc_coupon_receiver_details', true );
							if ( empty( $coupon_receiver_details ) || ! is_array( $coupon_receiver_details ) ) {
								$coupon_receiver_details = array();
							}
							$coupon_receiver_details[] = array(
								'code'    => $generated_coupon_details['code'],
								'amount'  => $amount,
								'email'   => $email_id,
								'message' => $message_from_sender,
							);
							update_post_meta( $order_id, 'sc_coupon_receiver_details', $coupon_receiver_details );
						}
					}
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
				<a class="button" id="sc-manage-category" title="" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=sc_coupon_category&post_type=shop_coupon' ) ); ?>"><?php echo esc_attr__( 'Manage coupon categories', 'woocommerce-smart-coupons' ); ?></a>
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
							$customer_emails[ $j ] = ( isset( $emails[ $j - 1 ] ) && is_email( $emails[ $j - 1 ] ) ) ? $emails[ $j - 1 ] : '';
						}
					}
				}

				$all_discount_types = wc_get_coupon_types();
				$generated_codes    = array();

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
						$data[ $i ]['expiry_date'] = gmdate( 'Y-m-d', strtotime( '+' . $post['sc_coupon_validity'] . ' ' . $post['validity_suffix'] ) );
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

					$data[ $i ] = apply_filters( 'sc_generate_coupon_meta', $data[ $i ], $post );

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
						$timestamp           = strtotime( $result['post_date'] ) + 1;
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
								$data[ $id ]['expiry_date'] = gmdate( 'Y-m-d', intval( $coupon_meta_value[ $index ] ) );
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

					$array = str_getcsv( $array, ',', '"', '\\' );

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
					$new_coupon = new WC_Coupon();
					$new_coupon->set_code( $new_code );
					foreach ( $args as $key => $value ) {
						switch ( $key ) {
							case 'code':
								// do nothing.
								break;
							case 'meta_data':
								if ( is_array( $value ) ) {
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
					$new_coupon = new WC_Coupon();
					$new_coupon->set_code( $new_code );
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
				$expiry_date = $expiry_date->getTimestamp();
			} elseif ( ! is_int( $expiry_date ) ) {
				$expiry_date = strtotime( $expiry_date );
			}

			if ( ! empty( $expiry_date ) && is_int( $expiry_date ) ) {
				$expiry_time = (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
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

			return array(
				'posts_headers'    => $coupon_posts_headers,
				'postmeta_headers' => $coupon_postmeta_headers,
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
					file_put_contents( $csvfilename, $file_data['file_content'] ); // phpcs:ignore
					fclose( $fp ); // phpcs:ignore

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
				$get_post_type                 = ( ! empty( $post->post_type ) ) ? $post->post_type : ( ( ! empty( $_GET['post_type'] ) ) ? wc_clean( wp_unslash( $_GET['post_type'] ) ) : '' ); // phpcs:ignore
				$get_page                      = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
				if ( ( 'edit.php' === $pagenow || 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && in_array( $get_post_type, array( 'shop_coupon', 'product', 'product-variation' ), true ) ) {
					$show_css_for_smart_coupon_tab = true;
				}
				if ( 'admin.php' === $pagenow && 'wc-smart-coupons' === $get_page ) {
					$show_css_for_smart_coupon_tab = true;
				}
				if ( $show_css_for_smart_coupon_tab ) {
					if ( ! wp_style_is( 'smart-coupon' ) ) {
						wp_enqueue_style( 'smart-coupon' );
					}
					wp_register_style( 'smart-coupons-admin', untrailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . '/assets/css/smart-coupons-admin.css', array(), $this->plugin_data['Version'] );
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
				$coupon_titles = get_post_meta( $parent_id, '_coupon_title', true );
			}
			if ( empty( $coupon_titles ) && ! is_array( $coupon_titles ) ) {
				return array();
			}
			return $coupon_titles;
		}

	}//end class

} // End class exists check
