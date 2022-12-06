<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements YITH WooCommerce Subscription
 *
 * @class   YITH_WC_Subscription
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit;
	// Exit if accessed directly.
}

require_once YITH_YWSBS_INC . 'legacy/abstract.yith-wc-subscription-legacy.php';

if ( ! class_exists( 'YITH_WC_Subscription' ) ) {

	/**
	 * Class YITH_WC_Subscription
	 */
	class YITH_WC_Subscription extends YITH_WC_Subscription_Legacy {


		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Subscription
		 */
		protected static $instance;

		/**
		 * Subscription Admin.
		 *
		 * @var YITH_WC_Subscription_Admin
		 */
		public $admin;

		/**
		 * Subscription Frontend.
		 *
		 * @var YITH_WC_Subscription_Frontend
		 */
		public $frontend;

		/**
		 * Subscription Assets.
		 *
		 * @var YITH_WC_Subscription_Assets
		 */
		public $assets;

		/**
		 * Shortcodes.
		 *
		 * @var YWSBS_Subscription_Shortcodes
		 */
		public $shortcodes;

		/**
		 * Subscription post name
		 *
		 * @var string
		 */
		public $post_name = '';

		/**
		 * Subscriptions endpoint
		 *
		 * @var string
		 */
		public static $endpoint = '';

		/**
		 * Subscriptions view endpoint
		 *
		 * @var string
		 */
		public static $view_endpoint = '';


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Subscription
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();

		}//end get_instance()


		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			global $sitepress;

			$this->load();
			$this->post_name = YITH_YWSBS_POST_TYPE;

			// Common YITH hooks.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			// Register Endpoints.
			add_action( 'init', array( $this, 'add_endpoint' ), 15 );

			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );
			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );

			// security check.
			add_action( 'wp', array( $this, 'security_check' ), 1 );

			if ( apply_filters( 'ywsbs_needs_flushing', true ) && ! $sitepress && ! class_exists( 'BuddyPress' ) ) {
				add_filter( 'option_rewrite_rules', array( $this, 'rewrite_rules' ), 1 );
				function_exists( 'get_home_path' ) && flush_rewrite_rules();
			}

			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

		}//end __construct()


		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 2.0.0
		 */
		public function load() {
			include_once YITH_YWSBS_INC . 'class.yith-wc-subscription-autoloader.php';

			// load functions and deprecated functions.
			include_once YITH_YWSBS_INC . 'functions.yith-wc-subscription-updates.php';
			include_once YITH_YWSBS_INC . 'functions.yith-wc-subscription.php';
			include_once YITH_YWSBS_INC . 'functions.yith-wc-subscription-product.php';
			include_once YITH_YWSBS_INC . 'functions.yith-wc-subscription-deprecated.php';
			include_once YITH_YWSBS_INC . 'class.ywsbs-subscription-discounts.php';

			/*
			 * Rest
			 */
			if ( apply_filters( 'ywsbs_enable_report', true ) ) {
				include_once YITH_YWSBS_INC . 'rest-api/Loader.php';
				\YITH\Subscription\RestApi\Loader::get_instance();
			}

			if ( self::is_request( 'admin' ) || self::is_request( 'frontend' ) ) {
				$this->assets     = YITH_WC_Subscription_Assets::get_instance();
				$this->shortcodes = new YWSBS_Subscription_Shortcodes();
			}

			if ( self::is_request( 'admin' ) ) {
				$this->admin = YITH_WC_Subscription_Admin::get_instance();
				YWSBS_Product_Post_Type_Admin::get_instance();
				YWSBS_Shop_Order_Post_Type_Admin::get_instance();
				YWSBS_Subscription_Post_Type_Admin::get_instance();
				YWSBS_Subscription_List_Table::get_instance();

				// Privacy.
				YWSBS_Subscription_Privacy::get_instance();
			}

			if ( self::is_request( 'frontend' ) ) {
				$this->frontend = YITH_WC_Subscription_Frontend::get_instance();
				YITH_WC_Subscription_Limit::get_instance();
			}

			if ( 'yes' === get_option( 'woocommerce_enable_coupons' ) ) {
				YWSBS_Subscription_Coupons::get_instance();
			}

			YWSBS_Subscription_Helper::get_instance();
			YWSBS_Subscription_Order::get_instance();
			YITH_WC_Subscription_Ajax::get_instance();
			YITH_WC_Activity::get_instance();
			YWSBS_Subscription_Resubscribe::get_instance();
			YWSBS_Subscription_Switch::get_instance();
			YWSBS_Subscription_Synchronization::get_instance();
			YWSBS_Subscription_Delivery_Schedules::get_instance();
			YWSBS_Webhooks::get_instance();
			if ( ywsbs_scheduled_actions_enabled() ) {
				YWSBS_Subscription_Scheduler::get_instance();
				YWSBS_Subscription_Scheduler_Actions::get_instance();
			}

			YWSBS_Subscription_Cron::get_instance();

			// Gutenberg.
			include_once YITH_YWSBS_INC . 'builders/gutenberg/class.ywsbs-gutenberg.php';

			// Gateways.
			$this->load_gateway_integration();

			// Plugin integration.
			$this->load_plugin_integration();

		}//end load()


		/**
		 * Load the classes that support different plugins integration
		 */
		private function load_plugin_integration() {
			// YITH WooCommerce Multivendor compatibility.
			if ( defined( 'YITH_WPV_PREMIUM' ) ) {
				include_once YITH_YWSBS_INC . 'compatibility/yith-woocommerce-product-vendors.php';
				YWSBS_Multivendor();
			}

			// YITH WooCommerce Membership compatibility.
			if ( defined( 'YITH_WCMBS_PREMIUM' ) ) {
				include_once YITH_YWSBS_INC . 'compatibility/yith-woocommerce-membership.php';
				YWSBS_Membership();
			}

			// YITH Multi Currency Switcher for WooCommerce compatibility.
			if ( defined( 'YITH_WCMCS_VERSION' ) && ! function_exists( 'ywsbs_yith_wcmcs' ) ) {
				include_once YITH_YWSBS_INC . 'compatibility/class-ywsbs-multi-currency-switcher.php';
				ywsbs_yith_wcmcs();
			}

		}//end load_plugin_integration()


		/**
		 * Load the classes that support different gateway integration
		 */
		private function load_gateway_integration() {
			// PayPal Standard.
			include_once YITH_YWSBS_INC . 'gateways/paypal/class.yith-wc-subscription-paypal.php';
			YWSBS_Subscription_Paypal();

			// WooCommerce Stripe Gateway compatibility.
			if ( class_exists( 'WC_Stripe' ) && version_compare( WC_STRIPE_VERSION, '4.1.11', '>' ) ) {
				include_once YITH_YWSBS_INC . 'gateways/woocommerce-gateway-stripe/class.yith-wc-stripe-integration.php';
				include_once YITH_YWSBS_INC . 'gateways/woocommerce-gateway-stripe/class.yith-wc-subscription-wc-stripe.php';
				include_once YITH_YWSBS_INC . 'gateways/woocommerce-gateway-stripe/class.yith-wc-subscription-wc-stripe-sepa.php';
				YITH_WC_Stripe_Integration::get_instance();
			}

			// WooCommerce Amazon Pay compatibility.
			if ( apply_filters( 'ywsbs_enable_amazon_pay', true ) && class_exists( 'WC_Amazon_Payments_Advanced' ) && version_compare( WC_AMAZON_PAY_VERSION, '2.0.0', '>' ) ) {
				include_once YITH_YWSBS_INC . 'gateways/woocommerce-gateway-amazon-payments-advanced/class-ywsbs-amazon-pay.php';
				YWSBS_Amazon_Pay::get_instance();
			}

			// WooCommerce Payments.
			if ( apply_filters( 'ywsbs_enable_woocommerce_payments', true ) && class_exists( 'WC_Payments' ) && version_compare( WCPAY_VERSION_NUMBER, '2.4.0', '>=' ) ) {
				include_once YITH_YWSBS_INC . 'gateways/woocommerce-payments/class-ywsbs-wc-payments-integration.php';
				include_once YITH_YWSBS_INC . 'gateways/woocommerce-payments/class-ywsbs-wc-payments.php';
				YWSBS_WC_Payments_Integration::get_instance();
			}

			// WooCommerce eWAY Payment Gateway.
			if ( apply_filters( 'ywsbs_enable_woocommerce_eway_gateway', true ) && class_exists( 'WC_Gateway_EWAY' ) && version_compare( WOOCOMMERCE_GATEWAY_EWAY_VERSION, '3.2.1', '>=' ) ) {
				include_once YITH_YWSBS_INC . 'gateways/woocommerce-gateway-eway/class-ywsbs-wc-eway-integration.php';
				include_once YITH_YWSBS_INC . 'gateways/woocommerce-gateway-eway/class-ywsbs-wc-eway.php';
				YWSBS_WC_EWAY_Integration::get_instance();
			}

		}//end load_gateway_integration()


		/**
		 * What type of request is this?
		 *
		 * @param string $type admin, ajax, cron or frontend.
		 *
		 * @return boolean
		 */
		public static function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin() && ! defined( 'DOING_AJAX' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( ! isset( $_REQUEST['context'] ) || ( isset( $_REQUEST['context'] ) && 'frontend' !== $_REQUEST['context'] ) ) ); //phpcs:ignore

				//phpcs:ignore WordPress.Security.NonceVerification.Recommended
				case 'ajax':
					return defined( 'DOING_AJAX' );

				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}

			return false;

		}//end is_request()


		/**
		 * Add the endpoint for the pages in my account to manage the subscription list and view.
		 *
		 * @since 1.0.0
		 */
		public function add_endpoint() {
			self::$endpoint      = apply_filters( 'ywsbs_endpoint', 'my-subscription' );
			self::$view_endpoint = apply_filters( 'ywsbs_view_endpoint', 'view-subscription' );

			$endpoints = array(
				'subscriptions'     => self::$endpoint,
				'view-subscription' => self::$view_endpoint,
			);

			foreach ( $endpoints as $key => $endpoint ) {
				WC()->query->query_vars[ $key ] = $endpoint;
				add_rewrite_endpoint( $endpoint, WC()->query->get_endpoints_mask() );
			}

		}//end add_endpoint()


		/**
		 * Check if the permalink should be flushed.
		 *
		 * @param array $rules Rewrite Rules.
		 *
		 * @return array|boolean
		 */
		public function rewrite_rules( $rules ) {
			$ep = self::$endpoint;
			$vp = self::$view_endpoint;

			return isset( $rules[ "(.?.+?)/{$ep}(/(.*))?/?$" ] ) && isset( $rules[ "(.?.+?)/{$vp}(/(.*))?/?$" ] ) ? $rules : false;

		}//end rewrite_rules()


		/**
		 * Load YIT Plugin Framework
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					include_once $plugin_fw_file;
				}
			}

		}//end plugin_fw_loader()


		/**
		 * Check if is main site URL so we can disable some actions on Sandbox websites
		 *
		 * @access public
		 * @return boolean
		 */
		public function is_main_site() {
			return ! ( defined( 'WP_ENV' ) && WP_ENV );

		}//end is_main_site()


		/*
			|--------------------------------------------------------------------------
			|  Utility Methods
			|--------------------------------------------------------------------------
		*/


		/**
		 * Renew the subscription
		 *
		 * @param YWSBS_Subscription $subscription subscription to renew.
		 *
		 * @return void
		 * @throws Exception Return error.
		 * @since  1.0.0
		 */
		public function renew_the_subscription( $subscription ) {
			WC()->cart->add_to_cart( $subscription->get( 'product_id' ), $subscription->get( 'quantity' ), $subscription->get( 'variation_id' ) );

		}//end renew_the_subscription()


		/**
		 * Return the ids of user subscriptions
		 *
		 * @param integer $user_id User ID.
		 * @param string  $status  Status of Subscription.
		 *
		 * @return array|integer
		 */
		public function get_user_subscriptions( $user_id, $status = '' ) {
			$args = array(
				'post_type'      => 'ywsbs_subscription',
				'posts_per_page' => - 1,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
										   array(
											   'key'     => 'user_id',
											   'value'   => $user_id,
											   'compare' => '=',
										   ),
				),
			);

			if ( ! empty( $status ) ) {
				$args['meta_query'][] = array(
					'key'     => 'status',
					'value'   => $status,
					'compare' => '=',
				);
			}

			$posts = get_posts( $args );

			return $posts ? wp_list_pluck( $posts, 'ID' ) : 0;

		}//end get_user_subscriptions()


		/**
		 * Change the status of subscription manually
		 *
		 * @param string             $new_status   New Status.
		 * @param YWSBS_Subscription $subscription Subscription.
		 * @param string             $from         Who wants to change the status.
		 *
		 * @return boolean
		 * @since  1.0.0
		 */
		public function manual_change_status( $new_status, $subscription, $from = '' ) {
			switch ( $new_status ) {
				case 'active':
					if ( ! $subscription->can_be_active() ) {
						$this->add_notice( esc_html__( 'This subscription cannot be activated', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						$subscription->update_status( 'active', $from );
						$this->add_notice( esc_html__( 'This subscription is now active', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;

				case 'overdue':
					if ( ! $subscription->can_be_overdue() ) {
						$this->add_notice( esc_html__( 'This subscription cannot be in status overdue', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						$subscription->update_status( 'overdue', $from );
						$this->add_notice( esc_html__( 'This subscription is now in overdue status', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;

				case 'suspended':
					if ( ! $subscription->can_be_suspended() ) {
						$this->add_notice( esc_html__( 'This subscription cannot be in status suspended', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						$subscription->update_status( 'suspended', $from );
						$this->add_notice( esc_html__( 'This subscription is now suspended', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;

				case 'cancelled':
					if ( ! $subscription->can_be_cancelled() ) {
						$this->add_notice( esc_html__( 'This subscription cannot be cancelled', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						// filter added to gateway payments.
						if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
							$this->add_notice( esc_html__( 'This subscription cannot be cancelled', 'yith-woocommerce-subscription' ), 'error' );

							return false;
						}

						$subscription->update_status( 'cancelled', $from );
						$this->add_notice( esc_html__( 'This subscription is now cancelled', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;

				case 'cancel-now':
					if ( ! $subscription->can_be_cancelled() ) {
						$this->add_notice( esc_html__( 'This subscription cannot be cancelled', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						// filter added to gateway payments.
						if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
							$this->add_notice( esc_html__( 'This subscription cannot be cancelled', 'yith-woocommerce-subscription' ), 'error' );

							return false;
						}

						$subscription->update_status( 'cancel-now', $from );
						$this->add_notice( esc_html__( 'This subscription is now cancelled', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;

				case 'paused':
					if ( ! $subscription->can_be_paused() ) {
						$this->add_notice( esc_html__( 'This subscription cannot be paused', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						// filter added to gateway payments.
						if ( ! apply_filters( 'ywsbs_suspend_recurring_payment', true, $subscription ) ) {
							$this->add_notice( esc_html__( 'This subscription cannot be paused', 'yith-woocommerce-subscription' ), 'error' );

							return false;
						}

						$subscription->update_status( 'paused', $from );
						$subscription->status = 'paused';
						// todo: check if it necessary.
						$this->add_notice( esc_html__( 'This subscription is now paused', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;

				case 'resumed':
					if ( ! $subscription->can_be_resumed() ) {
						$this->add_notice( esc_html__( 'This subscription cannot be resumed', 'yith-woocommerce-subscription' ), 'error' );
					} else {
						// filter added to gateway payments.
						if ( ! apply_filters( 'ywsbs_resume_recurring_payment', true, $subscription ) ) {
							$this->add_notice( esc_html__( 'This subscription cannot be resumed', 'yith-woocommerce-subscription' ), 'error' );

							return false;
						}

						$subscription->update_status( 'resume', $from );
						$subscription->status = 'active';
						// todo: check if it necessary.
						$this->add_notice( esc_html__( 'This subscription is now active', 'yith-woocommerce-subscription' ), 'success' );
					}
					break;

				default:
			}//end switch

			return false;

		}//end manual_change_status()


		/**
		 * Print a WC message
		 *
		 * @param string $message Message to show.
		 * @param string $type    Type od message.
		 *
		 * @since 1.0.0
		 */
		public function add_notice( $message, $type ) {
			if ( ! is_admin() ) {
				wc_add_notice( $message, $type );
			}

		}//end add_notice()


		/**
		 * Check if in the order there are subscription
		 *
		 * @param WC_Order $order Order.
		 *
		 * @return boolean
		 * @since  1.0.0
		 */
		public function order_has_subscription( $order ) {
			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			$order_items = $order->get_items();
			if ( empty( $order_items ) ) {
				return false;
			}

			foreach ( $order_items as $key => $order_item ) {
				$id = ( $order_item['variation_id'] ) ? $order_item['variation_id'] : $order_item['product_id'];

				if ( ywsbs_is_subscription_product( $id ) ) {
					return true;
				}
			}

			return false;

		}//end order_has_subscription()


		/**
		 * Filters woocommerce available mails
		 *
		 * @param array $emails WooCommerce email list.
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function add_woocommerce_emails( $emails ) {
			include_once YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription.php';
			$emails['YITH_WC_Subscription_Status']                      = include YITH_YWSBS_INC . 'emails/class.yith-wc-subscription-status.php';
			$emails['YITH_WC_Customer_Subscription_Cancelled']          = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-cancelled.php';
			$emails['YITH_WC_Customer_Subscription_Suspended']          = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-suspended.php';
			$emails['YITH_WC_Customer_Subscription_Expired']            = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-expired.php';
			$emails['YITH_WC_Customer_Subscription_Before_Expired']     = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-before-expired.php';
			$emails['YITH_WC_Customer_Subscription_Paused']             = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-paused.php';
			$emails['YITH_WC_Customer_Subscription_Resumed']            = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-resumed.php';
			$emails['YITH_WC_Customer_Subscription_Request_Payment']    = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-request-payment.php';
			$emails['YITH_WC_Customer_Subscription_Renew_Reminder']     = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-renew-reminder.php';
			$emails['YITH_WC_Customer_Subscription_Payment_Done']       = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-payment-done.php';
			$emails['YITH_WC_Customer_Subscription_Payment_Failed']     = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-payment-failed.php';
			$emails['YITH_WC_Customer_Subscription_Delivery_Schedules'] = include YITH_YWSBS_INC . 'emails/class.yith-wc-customer-subscription-delivery-schedules.php';

			return $emails;

		}//end add_woocommerce_emails()


		/**
		 * Loads WC Mailer when needed
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function load_wc_mailer() {
			if ( 'yes' === get_option( 'ywsbs_site_staging', 'no' ) ) {
				return;
			}

			// Administrator.
			add_action( 'ywsbs_subscription_admin_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );

			// Customers.
			add_action(
				'ywsbs_customer_subscription_cancelled_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_expired_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_before_expired_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_suspended_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_resumed_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_paused_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_request_payment_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_renew_reminder_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_payment_done_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_payment_failed_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);
			add_action(
				'ywsbs_customer_subscription_delivery_schedules_mail',
				array(
					'WC_Emails',
					'send_transactional_email',
				),
				10
			);

		}//end load_wc_mailer()


		/*
			|--------------------------------------------------------------------------
			| Upgrade/Downgrade Methods
			|--------------------------------------------------------------------------
		*/


		/**
		 * Start the downgrade process
		 *
		 * @param integer            $from_id      current Variation id.
		 * @param integer            $to_id        Variation to switch.
		 * @param YWSBS_Subscription $subscription Current subscription.
		 *
		 * @return void
		 * @throws Exception Return error.
		 * @since  1.0.0
		 */
		public function downgrade_process( $from_id, $to_id, $subscription ) {
			// retrieve the days left to the next payment or to the expiration data.
			$left_time       = YWSBS_Subscription_Helper()->get_left_time_to_next_payment( $subscription );
			$days            = ywsbs_get_days( $left_time );
			$subscription_id = $subscription->get_id();

			if ( $left_time <= 0 && $days > 1 ) {
				add_user_meta(
					$subscription->get_user_id(),
					'ywsbs_upgrade_' . $to_id,
					array(
						'subscription_id' => $subscription_id,
						'pay_gap'         => 0,
					)
				);
			} elseif ( $left_time > 0 ) {
				$user_id = $subscription->get_user_id();
				add_user_meta( $user_id, 'ywsbs_downgrade_' . $to_id, $subscription_id );
				add_user_meta(
					$user_id,
					'ywsbs_trial_' . $to_id,
					array(
						'subscription_id' => $subscription_id,
						'trial_days'      => $days,
					)
				);
			}//end if

			$variation = wc_get_product( $to_id );

			if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $subscription->get( 'product_id' ), $subscription->get( 'quantity' ), $to_id, $variation->get_variation_attributes() ) ) {
				wc_add_notice( esc_html__( 'This subscription cannot be switched. Contact us for info', 'yith-woocommerce-subscription' ), 'error' );

				return;
			}

			WC()->cart->add_to_cart( $subscription->get( 'product_id' ), $subscription->get( 'quantity' ), $to_id, $variation->get_variation_attributes() );

			$checkout_url = wc_get_checkout_url();
			/**
			 * DO_ACTION: ywsbs_subscription_downgrade_process
			 *
			 * Action triggered during the downgrade option
			 *
			 * @param   int  $from_id  Current subscription product to switch.
			 * @param   int  $to_id  Final variation id.
			 * @param   YWSBS_Subscription  $subscription  Subscription object
			 */
			do_action( 'ywsbs_subscription_downgrade_process', $subscription->get_variation_id(), $to_id, $subscription );

			wp_safe_redirect( $checkout_url );
			exit;

		}//end downgrade_process()


		/**
		 * Start the upgrade process
		 *
		 * @param integer            $from_id      current Variation id.
		 * @param integer            $to_id        Variation to switch.
		 * @param YWSBS_Subscription $subscription Current subscription.
		 * @param float              $pay_gap      Gap Amount.
		 *
		 * @return void
		 * @throws Exception Return error.
		 * @since  1.0.0
		 */
		public function upgrade_process( $from_id, $to_id, $subscription, $pay_gap ) {
			add_user_meta(
				$subscription->get_user_id(),
				'ywsbs_upgrade_' . $to_id,
				array(
					'subscription_id' => $subscription->get_id(),
					'pay_gap'         => $pay_gap,
				),
				true
			);

			$variation = wc_get_product( $to_id );

			if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $subscription->get( 'product_id' ), $subscription->get( 'quantity' ), $to_id, $variation->get_variation_attributes() ) ) {
				wc_add_notice( esc_html__( 'This subscription cannot be switched. Contact us for info', 'yith-woocommerce-subscription' ), 'error' );

				return;
			}

			WC()->cart->add_to_cart( $subscription->get( 'product_id' ), $subscription->get( 'quantity' ), $to_id, $variation->get_variation_attributes() );

			$checkout_url = wc_get_checkout_url();
			do_action( 'ywsbs_subscription_upgrade_process', $subscription->get_variation_id(), $to_id, $subscription, $pay_gap );

			wp_safe_redirect( $checkout_url );
			exit;

		}//end upgrade_process()


		/**
		 * Cancel the subscription
		 *
		 * @param integer $subscription_id Subscription to cancel.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function cancel_subscription_after_upgrade( $subscription_id ) {
			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! apply_filters( 'ywsbs_cancel_recurring_payment', true, $subscription ) ) {
				$this->add_notice( esc_html__( 'This subscription cannot be cancelled. You cannot switch to related subscriptions', 'yith-woocommerce-subscription' ), 'error' );

				return;
			}

			$subscription->update_status( 'cancelled', 'customer' );
			$subscription->status = 'cancelled';
			// todo:check if it necessary.
			do_action( 'ywsbs_subscription_cancelled_mail', $subscription );

			YITH_WC_Activity()->add_activity( $subscription_id, 'switched', 'success', 0, esc_html__( 'Subscription cancelled due to switch', 'yith-woocommerce-subscription' ) );

		}//end cancel_subscription_after_upgrade()


		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				include_once YITH_YWSBS_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				include_once YITH_YWSBS_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_YWSBS_INIT, YITH_YWSBS_SECRET_KEY, YITH_YWSBS_SLUG );

		}//end register_plugin_for_activation()


		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				include_once YITH_YWSBS_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_YWSBS_SLUG, YITH_YWSBS_INIT );

		}//end register_plugin_for_updates()


		/**
		 * Checks whether plugin is currently active on the site it was originally installed
		 *
		 * If site url has changed from original one, it could happen that db was cloned on another installation
		 *
		 * @return void
		 * @since  2.2.0
		 */
		public function security_check() {

			if ( 'yes' === get_option( 'ywsbs_skip_security_check', 'no' ) ) {
				return;
			}

			// skip this check if WPML Language URL format is set like a different domain per language.
			global $sitepress_settings;
			if ( isset( $sitepress_settings, $sitepress_settings['language_negotiation_type'] ) && 2 === (int) $sitepress_settings['language_negotiation_type'] ) {
				return;
			}

			$registered_url = get_option( 'ywsbs_registered_url', '' );
			if ( ! $registered_url ) {
				update_option( 'ywsbs_registered_url', get_site_url() );

				return;
			}

			$registered_url = str_replace( array( 'https://', 'http://', 'www.' ), '', $registered_url );
			$current_url    = str_replace( array( 'https://', 'http://', 'www.' ), '', get_site_url() );
			$allowed_urls   = apply_filters( 'ywsbs_site_urls_allowed', array( $registered_url ) );

			if ( apply_filters( 'ywsbs_validate_site_url', ! in_array( $current_url, $allowed_urls, true ), $registered_url, $current_url, $allowed_urls ) ) {
				yith_subscription_log( 'YITH Subscription set to staging mode: Registered url ' . $registered_url . ' - Current url ' . $current_url );
				update_option( 'ywsbs_site_staging', 'yes' );
				update_option( 'ywsbs_site_changed', 'yes' );
			}

		}//end security_check()


	}//end class

}//end if

/**
 * Unique access to instance of YITH_WC_Subscription class
 *
 * @return YITH_WC_Subscription
 */
function YITH_WC_Subscription() { //phpcs:ignore
	return YITH_WC_Subscription::get_instance();

}//end YITH_WC_Subscription()
