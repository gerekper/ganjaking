<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

use Stripe\SetupIntent;

if ( ! class_exists( 'YITH_WCStripe_Premium' ) ) {
	/**
	 * WooCommerce Stripe main class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Premium extends YITH_WCStripe {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCStripe_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * @var YITH_WCStripe_Customer Customers instance
		 * @since 1.0
		 */
		public $customer = null;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCStripe_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'init', array( __CLASS__, 'create_blacklist_table' ) );
			register_activation_hook( __FILE__, array( __CLASS__, 'create_blacklist_table' ) );

			// includes
			include_once( 'class-yith-stripe-customer.php' );
			include_once( 'functions-yith-stripe.php' );

			// admin includes
			if ( is_admin() ) {
				include_once( 'class-yith-stripe-admin-premium.php' );
				$this->admin = new YITH_WCStripe_Admin_Premium();
			}

			// hooks
			add_action( 'woocommerce_api_stripe_webhook', array( $this, 'handle_webhooks' ) );
			add_action( 'init', array( $this, 'convert_tokens' ) );

			// renew methods
			add_action( 'ywsbs_pay_renew_order_with_' . self::$gateway_id, array( $this, 'process_renew' ), 10, 2 );
			add_filter( 'ywsbs_renew_now_order_action', array( $this, 'show_manual_renew_button' ), 10, 2 );
			add_action( 'wp', array( $this, 'invoice_charged_notice' ) );

			// subscriptions
			add_filter( 'ywsbs_from_list', array( $this, 'add_from_list' ) );
			add_filter( 'ywsbs_suspend_recurring_payment', array( $this, 'suspend_subscription' ), 10, 2 );
			add_filter( 'ywsbs_resume_recurring_payment', array( $this, 'resume_subscription' ), 10, 2 );
			add_filter( 'ywsbs_cancel_recurring_payment', array( $this, 'cancel_subscription' ), 10, 2 );
			add_filter( 'ywsbs_subscription_status_expired', array( $this, 'expired_subscription' ), 10, 1 );
			add_filter( 'ywsbs_reactivate_suspended_subscription', array( $this, 'reactivate_subscription' ), 10, 3 );
			add_filter( 'ywsbs_renew_order_item_meta_data', array(
				$this,
				'remove_meta_from_subscription_renew'
			), 10, 4 );

			// blacklist table
			add_action( 'init', array( $this, 'blacklist_table_wpdbfix' ), 0 );
			add_action( 'switch_blog', array( $this, 'blacklist_table_wpdbfix' ), 0 );

			// token hooks
			add_action( 'woocommerce_payment_token_deleted', array( $this, 'delete_token_from_stripe' ), 10, 2 );
			add_action( 'woocommerce_payment_token_set_default', array( $this, 'set_default_token_on_stripe' ), 10, 2 );

			// Payment methods customize
			add_action( 'woocommerce_account_payment_methods_column_method', array(
				$this,
				'myaccount_method_column'
			) );
			add_filter( 'woocommerce_payment_methods_list_item', array( $this, 'myaccount_method' ), 10, 2 );

			// add custom endpoints
			add_filter( 'woocommerce_get_query_vars', array( $this, 'add_query_vars' ) );
			add_action( 'wp', array( $this, 'handle_card_confirmation' ), 20 );

			// ajax handling
			add_action( 'wp_ajax_yith_stripe_refresh_details', array( $this, 'send_checkout_details' ) );
			add_action( 'wp_ajax_nopriv_yith_stripe_refresh_details', array( $this, 'send_checkout_details' ) );

			add_action( 'wp_ajax_yith_stripe_refresh_intent', array( $this, 'refresh_intent' ) );
			add_action( 'wp_ajax_nopriv_yith_stripe_refresh_intent', array( $this, 'refresh_intent' ) );

			add_action( 'wc_ajax_yith_wcstripe_verify_intent', array( $this, 'verify_intent' ) );

			add_action( 'wp_ajax_yith_stripe_refresh_session', array( $this, 'refresh_session' ) );
			add_action( 'wp_ajax_nopriv_yith_stripe_refresh_session', array( $this, 'refresh_session' ) );

			// emails init
			add_filter( 'woocommerce_email_classes', array( $this, 'register_email_classes' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'register_email_actions' ) );
			add_filter( 'woocommerce_locate_core_template', array( $this, 'register_woocommerce_template' ), 10, 3 );

			// crons
			add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
			add_action( 'init', array( $this, 'schedule_crons' ) );
			add_action( 'yith_wcstripe_expiring_cards_reminders_scheduler', array(
				$this,
				'schedule_expiring_cards_reminders'
			) );
			add_action( 'yith_wcstripe_expiring_cards_reminders_dispatcher', array(
				$this,
				'dispatch_expiring_cards_reminders'
			) );
		}

		/* === BLACKLIST METHODS === */

		/**
		 * Set blacklist table name on £wpdb instance
		 *
		 * @since 1.1.3
		 */
		public function blacklist_table_wpdbfix() {
			global $wpdb;
			$blacklist_table = 'yith_wc_stripe_blacklist';

			$wpdb->{$blacklist_table} = $wpdb->prefix . $blacklist_table;
			$wpdb->tables[]           = $blacklist_table;
		}

		/**
		 * Create the {$wpdb->prefix}_yith_vendor_commissions table
		 *
		 * @return void
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @see    dbDelta()
		 */
		public static function create_blacklist_table() {
			global $wpdb;

			if ( true == get_option( 'yith_wc_stripe_blacklist_table_created' ) ) {
				return;
			}

			/**
			 * Check if dbDelta() exists
			 */
			if ( ! function_exists( 'dbDelta' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			$charset_collate = $wpdb->get_charset_collate();

			$create = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}yith_wc_stripe_blacklist (
                        `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
						`ip` VARCHAR(15) NOT NULL DEFAULT '',
						`user_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
						`order_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
						`ban_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
						`ban_date_gmt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
						`ua` VARCHAR(255) NULL DEFAULT '',
						`unbanned` TINYINT(1) NOT NULL DEFAULT '0',
						PRIMARY KEY (`ID`),
						INDEX `user_id` (`user_id`),
						INDEX `order_id` (`order_id`),
						INDEX `ip` (`ip`)
                        ) $charset_collate;";
			dbDelta( $create );

			update_option( 'yith_wc_stripe_blacklist_table_created', true );
		}

		/* === SUBSCRIPTION METHODS === */

		/**
		 * Detect if installed some external addons for ecommerce, to give them compatibility with stripe
		 *
		 * @param string $addon
		 *
		 * @return bool If defined $addon, returns if the addon is installed or not. If not defined it, return if any of addon compatible is installed
		 */
		public static function addons_installed( $addon = '' ) {
			$checks = array(
				'yith-subscription' => function_exists( 'YITH_WC_Subscription' )
			);

			if ( ! empty( $addon ) ) {
				return isset( $checks[ $addon ] ) ? $checks[ $addon ] : false;
			}

			foreach ( $checks as $check ) {
				if ( $check ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Adds Stripe Gateway to payment gateways available for woocommerce checkout
		 *
		 * @param $methods array Previously available gataways, to filter with the function
		 *
		 * @return array New list of available gateways
		 * @since 1.0.0
		 */
		public function add_to_gateways( $methods ) {
			self::$gateway_id = apply_filters( 'yith_wcstripe_gateway_id', self::$gateway_id );

			include_once( 'class-yith-stripe-gateway.php' );
			include_once( 'class-yith-stripe-gateway-advanced.php' );

			if ( self::addons_installed() ) {
				include_once( 'class-yith-stripe-gateway-addons.php' );
				$methods[] = 'YITH_WCStripe_Gateway_Addons';
			} else {
				$methods[] = 'YITH_WCStripe_Gateway_Advanced';
			}

			return $methods;
		}

		/**
		 * Cancel recurring payment if the subscription has a stripe subscription
		 *
		 * @param bool               $result
		 * @param YWSBS_Subscription $subscription
		 *
		 * @return bool
		 */
		public function cancel_subscription( $result, $subscription ) {
			if ( $subscription->get_payment_method() != self::$gateway_id || ! isset( $subscription->stripe_subscription_id ) || $subscription->stripe_subscription_id == '' ) {
				return $result;
			}

			$gateways = WC()->payment_gateways()->get_available_payment_gateways();

			if ( ! isset( $gateways['yith-stripe'] ) || ! $gateways['yith-stripe'] ) {
				return false;
			}

			/** @var $gateway YITH_WCStripe_Gateway|YITH_WCStripe_Gateway_Advanced|YITH_WCStripe_Gateway_Addons */
			$gateway = $gateways['yith-stripe'];

			try {

				// load SDK
				$gateway->init_stripe_sdk();

				$gateway->api->cancel_subscription( $subscription->stripe_customer_id, $subscription->stripe_subscription_id );

				// remove the invoice from failed invoices list, if it exists
				$order           = wc_get_order( $subscription->order_id );
				$failed_invoices = get_user_meta( $order->get_user_id(), 'failed_invoices', true );
				if ( isset( $failed_invoices[ $subscription->id ] ) ) {
					unset( $failed_invoices[ $subscription->id ] );
					update_user_meta( $order->get_user_id(), 'failed_invoices', $failed_invoices );
				}

				$gateway->log( 'YSBS - Stripe Subscription Cancel Request ' . $subscription->id . ' with success.' );
				YITH_WC_Activity()->add_activity( $subscription->id, 'cancelled', "success" );

				return $result;

			} catch ( Stripe\Exception\ApiErrorException $e ) {
				$gateway->log( 'Stripe Subscription Error: ' . $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );
				YITH_WC_Activity()->add_activity( $subscription->id, 'cancelled', 'error', '', $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );

				return false;
			}
		}

		/**
		 * Cancel subscription when plan expires
		 *
		 * @param $subscription_id
		 *
		 * @return void
		 */
		public function expired_subscription( $subscription_id ) {
			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! $subscription || $subscription->get_payment_method() != self::$gateway_id ) {
				return;
			}

			$this->cancel_subscription( true, $subscription );
		}

		/**
		 * Suspend a subscription, by update the subscription on stripe and setting "trial_end" to undefined date
		 *
		 * @param $result
		 * @param $subscription YWSBS_Subscription
		 *
		 * @return bool
		 */
		public function suspend_subscription( $result, $subscription ) {
			if ( $subscription->get_payment_method() != self::$gateway_id || ! isset( $subscription->stripe_subscription_id ) || $subscription->stripe_subscription_id == '' ) {
				return true;
			}

			$gateway = $this->get_gateway();

			if ( ! $gateway ) {
				return false;
			}

			try {

				// load SDK
				$gateway->init_stripe_sdk();

				// set trial to undefined date, so any payment is triggered from stripe, without cancel subscription
				$gateway->api->update_subscription( $subscription->stripe_customer_id, $subscription->stripe_subscription_id, array(
					'trial_end' => strtotime( '+730 days' )  // max supported by stripe
				) );

				$gateway->log( 'YSBS - Stripe Subscription ' . $subscription->id . ' Pause Request with success.' );
				YITH_WC_Activity()->add_activity( $subscription->id, 'paused', "success" );

				return true;

			} catch ( Stripe\Exception\ApiErrorException $e ) {
				$gateway->log( 'Stripe Subscription Error: ' . $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );
				YITH_WC_Activity()->add_activity( $subscription->id, 'paused', 'error', '', $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );

				return false;
			}
		}

		/**
		 * Resume the subscription, updated it and set "trial_end" to now value
		 *
		 * @param $result
		 * @param $subscription YWSBS_Subscription
		 *
		 * @return bool
		 *
		 * @since 1.2.9
		 */
		public function resume_subscription( $result, $subscription ) {
			if ( $subscription->get_payment_method() != self::$gateway_id || ! isset( $subscription->stripe_subscription_id ) || $subscription->stripe_subscription_id == '' ) {
				return true;
			}

			$gateway = $this->get_gateway();

			if ( ! $gateway ) {
				return false;
			}

			try {

				// load SDK
				$gateway->init_stripe_sdk();

				// set trial to undefined date, so any payment is triggered from stripe, without cancel subscription
				$gateway->api->update_subscription( $subscription->stripe_customer_id, $subscription->stripe_subscription_id, array(
					'trial_end' => ( $subscription->payment_due_date > current_time( 'timestamp' ) ) ? $subscription->payment_due_date + $subscription->get_payment_due_date_paused_offset() : current_time( 'timestamp' )
				) );

				$gateway->log( 'YSBS - Stripe Subscription ' . $subscription->id . ' Resumed with success.' );
				YITH_WC_Activity()->add_activity( $subscription->id, 'resumed', "success" );

				return true;

			} catch ( Stripe\Exception\ApiErrorException $e ) {
				$gateway->log( 'Stripe Subscription Error: ' . $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );
				YITH_WC_Activity()->add_activity( $subscription->id, 'resumed', 'error', '', $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );

				return false;
			}
		}

		/**
		 * Process payment of the subscription immediately after reactivation
		 *
		 * @param $status       string Current autorenew status
		 * @param $subscription \YWSBS_Subscription Subscription data
		 * @param $order        \WC_order Renew order
		 *
		 * @return void
		 */
		public function reactivate_subscription( $status, $subscription, $order ) {
			if ( $status == 'yes' && $subscription->get_payment_method() == self::$gateway_id ) {
				$gateway = $this->get_gateway();

				if ( ! $gateway instanceof YITH_WCStripe_Gateway_Addons ) {
					return;
				}

				$gateway->process_renew( $order );
			}
		}

		/**
		 * Add this gateway in the list "from" to understand from where the
		 * update status is requested.
		 *
		 * @param $list
		 *
		 * @return mixed
		 */
		public function add_from_list( $list ) {
			$gateway = $this->get_gateway();

			if ( $gateway instanceof YITH_WCStripe_Gateway ) {
				$list[ self::$gateway_id ] = $gateway->get_method_title();
			}

			return $list;
		}

		/* === RENEW METHODS === */

		/**
		 * Process renew
		 *
		 * @param $order        WC_Order Renew order
		 * @param $manual_renew bool Whether renew is manual or no
		 *
		 * @return bool Status of operation
		 */
		public function process_renew( $order, $manual_renew ) {
			$gateway = $this->get_gateway();

			if ( ! $gateway instanceof YITH_WCStripe_Gateway_Addons ) {
				return false;
			}

			return ! $manual_renew ? $gateway->process_renew( $order ) : $gateway->process_manual_renew( $order );
		}

		/**
		 * Skip all metas related to stripe subscription, when creating renews order
		 *
		 * @param $save          bool Whether to save meta or not
		 * @param $order_item_id int Order item id
		 * @param $key           string Meta key
		 * @param $value         mixed Meta value
		 *
		 * @return bool Whether to save meta or not
		 */
		public function remove_meta_from_subscription_renew( $save, $order_item_id, $key, $value ) {
			if ( $key == '_subscription_charge_id' ) {
				return false;
			}

			return $save;
		}

		/**
		 * Add a notice on My Account when a renewal goes failed
		 *
		 * @since 1.2.9
		 */
		public function notify_failed_renewal() {

			if ( ! is_account_page() ) {
				return;
			}

			if ( ! defined( 'YITH_YWSBS_VERSION' ) ) {
				return;
			}

			$user_id = get_current_user_id();

			$attemps = array(
				'',
				__( 'first', 'yith-woocommerce-stripe' ),
				__( 'second', 'yith-woocommerce-stripe' ),
				__( 'third', 'yith-woocommerce-stripe' ),
				__( 'fourth', 'yith-woocommerce-stripe' )
			);

			$failed_invoices = get_user_meta( $user_id, 'failed_invoices', true );
			$failed_invoices = is_array( $failed_invoices ) ? $failed_invoices : array();

			if ( empty( $failed_invoices ) ) {
				return;
			}

			$message              = false;
			$payment_methods_url  = wc_get_endpoint_url( version_compare( WC()->version, '2.6', '<' ) ? 'saved-cards' : 'payment-methods' );
			$my_subscriptions_url = wc_get_endpoint_url( 'my-subscriptions' );

			if ( count( $failed_invoices ) > 1 ) {
				$message = sprintf( __( 'Oops! The renewal payment for your subscriptions didn\'t go as expected and your subscriptions have been suspended. Please %2$scheck your credit card details%1$s or contact your bank. After this, you can attempt a manual renewal by clicking %3$shere%1$s.', 'yith-woocommerce-stripe' ), '</a>', '<a href="' . $payment_methods_url . '" class="link">', '<a href="' . $my_subscriptions_url . '" class="link">' );

			} else if ( count( $failed_invoices ) == 1 ) {
				$failed_subscriptions = array_keys( $failed_invoices );
				$subscription_id      = array_shift( $failed_subscriptions );
				$subscription         = ywsbs_get_subscription( $subscription_id );
				$order                = wc_get_order( $subscription->order_id );
				$failed_attempts      = $subscription->has_failed_attempts();
				$order_payment        = yit_get_prop( $order, '_payment_method' );
				$product_name         = $subscription->product_name;
				$renew_link           = add_query_arg( 'yith_renew_sbs', $subscription_id, wc_get_page_permalink( 'myaccount' ) );

				if ( $subscription->status == 'cancelled' ) {
					return;
				}

				$num_of_attemp_string = $attemps[ $failed_attempts['num_of_failed_attemps'] ];
				$day_btw              = ywsbs_get_num_of_days_between_attempts();
				$days                 = ( isset( $day_btw[ $order_payment ] ) ) ? $day_btw[ $order_payment ] : 5;
				$date_of_attemp       = yit_get_prop( $order, 'next_payment_attempt' );

				if ( empty( $date_of_attemp ) || $date_of_attemp <= current_time( 'timestamp' ) ) {
					$date_of_attemp = intval( $subscription->payment_due_date ) + ( ( $days * DAY_IN_SECONDS ) * ( $failed_attempts['num_of_failed_attemps'] ) );
				}

				$date_of_attemp_string = strtoupper( date_i18n( get_option( 'date_format' ), $date_of_attemp ) );

				$message = sprintf( __( 'The %1$s payment attempt to renew your %2$s failed. A new attempt will be made <strong>ON %3$s</strong>.<br>
				Please verify your available funds in the card specified during the subscription and/or verify that your card is not expired. In case it is expired, please, update your credit card details %4$s. 
				<br>If you already fixed the issue with your Credit Card and don\'t want to wait for the next automatic renewal attempt performed by our system, click %5$s to force a new attempt and re-activate your subscription right now!', 'yith-woocommerce-stripe' ),
					$num_of_attemp_string, $product_name, $date_of_attemp_string, '<a href="' . $payment_methods_url . '" class="link">THROUGH THE FOLLOWING LINK</a>', '<a href="' . $renew_link . '" class="link">HERE</a>' );
			}

			if ( $message && ! wc_has_notice( $message, 'error' ) ) {
				wc_add_notice( $message, 'error' );
			}
		}

		/**
		 * Trigger manual subscription renew from notice message on my account page
		 *
		 * @return void
		 */
		public function trigger_subscription_renew() {
			if ( ! empty( $_REQUEST['yith_renew_sbs'] ) ) {

				$user            = get_current_user_id();
				$failed_invoices = get_user_meta( $user, 'failed_invoices', true );
				$subscription_id = intval( $_REQUEST['yith_renew_sbs'] );
				$redirect_url    = remove_query_arg( 'yith_renew_sbs' );

				if ( empty( $failed_invoices ) || ! isset( $failed_invoices[ $subscription_id ] ) ) {
					wp_safe_redirect( esc_url_raw( $redirect_url ) );
					exit();
				}

				wp_safe_redirect( esc_url_raw( wp_nonce_url( add_query_arg( array(
					'stripe-action' => 'pay-renewal',
					'id'            => $failed_invoices[ $subscription_id ]
				), $redirect_url ), 'stripe-pay-renewal' ) ) );
				exit();
			}
		}

		/**
		 * Add "Renew now" button for the renewal order with failed attempts
		 *
		 * @param $actions
		 * @param $order WC_Order
		 *
		 * @return array
		 *
		 * @since 1.2.9
		 */
		public function add_try_now_renewal_my_orders( $actions, $order ) {
			if ( 'yes' != yit_get_prop( $order, 'is_a_renew' ) || $order->get_status() == 'completed' ) {
				return $actions;
			}

			$order_subscriptions = (array) yit_get_prop( $order, 'subscriptions' );
			$subscription_id     = array_shift( $order_subscriptions );
			$failed_invoices     = get_user_meta( $order->get_user_id(), 'failed_invoices', true );

			if ( isset( $failed_invoices[ $subscription_id ] ) ) {
				$renew_now = array(
					'renew-now' => array(
						'url'  => wp_nonce_url( add_query_arg( array(
							'stripe-action' => 'pay-renewal',
							'id'            => $failed_invoices[ $subscription_id ]
						) ), 'stripe-pay-renewal' ),
						'name' => __( 'Renew now', 'yith-woocommerce-stripe' )
					)
				);

				$actions = $actions + $renew_now;
			}

			return $actions;
		}

		/**
		 * Add "Renew now" button for the renewal order with failed attempts
		 *
		 * @param $actions
		 * @param $subscription YWSBS_Subscription
		 *
		 * @return array
		 *
		 * @since 1.2.9
		 */
		public function add_try_now_renewal_my_subscriptions( $actions, $subscription ) {
			$subscription_id = $subscription->id;
			$user_id         = $subscription->user_id;
			$failed_invoices = get_user_meta( $user_id, 'failed_invoices', true );

			if ( isset( $failed_invoices[ $subscription_id ] ) ) {
				$renew_now = array(
					'renew-now' => array(
						'url'  => wp_nonce_url( add_query_arg( array(
							'stripe-action' => 'pay-renewal',
							'id'            => $failed_invoices[ $subscription_id ]
						) ), 'stripe-pay-renewal' ),
						'name' => __( 'Renew now', 'yith-woocommerce-stripe' )
					)
				);

				$actions = $renew_now + $actions;
			}

			return $actions;
		}

		/**
		 * Renew subscription manually
		 *
		 * @return void
		 * @throws Stripe\Exception\ApiErrorException
		 * @since 1.2.9
		 */
		public function renew_subscription_manually() {
			if (
				! isset( $_REQUEST['stripe-action'], $_REQUEST['id'] ) ||
				$_REQUEST['stripe-action'] != 'pay-renewal' ||
				! wp_verify_nonce( $_REQUEST['_wpnonce'], 'stripe-pay-renewal' )
			) {
				return;
			}

			$gateway = $this->get_gateway();

			try {

				if ( $gateway ) {
					// Initializate SDK and set private key
					$gateway->init_stripe_sdk();

					// pay invoice
					$invoice_id = wc_clean( $_REQUEST['id'] );
					$gateway->api->pay_invoice( $invoice_id );

					// remove notification invoice failed
					$failed_invoices = get_user_meta( get_current_user_id(), 'failed_invoices', true );
					$subscription_id = array_search( $invoice_id, $failed_invoices );
					unset( $failed_invoices[ $subscription_id ] );
					update_user_meta( get_current_user_id(), 'failed_invoices', $failed_invoices );

					// success
					wc_add_notice( __( 'Your renew request is sent successfully. We will notify you if the payment is charged properly.', 'yith-woocommerce-stripe' ) );
				}

				wp_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit();

			} catch ( Stripe\Exception\CardException $e ) {
				$gateway->error_handling( $e );
			}
		}

		/**
		 * Print a success notice if the invoice is charged successfully
		 *
		 * @since 1.2.9
		 */
		public function invoice_charged_notice() {
			if ( get_user_meta( get_current_user_id(), 'invoice_charged', true ) && function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( __( 'Subscription renewed successfully!', 'yith-woocommerce-stripe' ) );
				delete_user_meta( get_current_user_id(), 'invoice_charged' );
			}
		}

		/**
		 * Shows renew button for old subscriptions, where failed invoices are registered in user meta
		 *
		 * @param $show_button bool Whether to show manual renew button for old subscriptions or not
		 * @param $order       \WC_Order
		 *
		 * @return bool Filtered value
		 */
		public function show_manual_renew_button( $show_button, $order ) {
			$gateway = $this->get_gateway();

			$order_id        = $order->get_id();
			$user_id         = $order->get_user_id();
			$subscriptions   = $order->get_meta( 'subscriptions' );
			$subscription_id = ! empty( $subscriptions ) ? array_pop( $subscriptions ) : false;

			if ( $user_id && $subscription_id && $gateway instanceof YITH_WCStripe_Gateway_Addons && $gateway->has_active_subscription( $subscription_id ) ) {
				$subscription                 = ywsbs_get_subscription( $subscription_id );
				$parent_order_id              = $subscription ? $subscription->order_id : 0;
				$parent_order_failed_attempts = get_post_meta( $parent_order_id, 'failed_attemps', true );
				$failed_invoices              = get_user_meta( $user_id, 'failed_invoices', true );

				$show_button = ( $order->get_meta( 'failed_attemps' ) > 0 || $parent_order_failed_attempts > 0 ) && is_array( $failed_invoices ) && isset( $failed_invoices[ $subscription_id ] ) && $subscription->renew_order == $order_id;
			}

			return $show_button;
		}

		/* === UTILITY METHODS === */

		/**
		 * Cancel the order if there is missing capture within 7 days
		 *
		 * @todo  review this method
		 *
		 * @since 1.0.0
		 */
		public function check_missing_capture() {
			$orders = get_posts( array(
				'posts_per_page' => 1,
				'post_type'      => 'shop_order',
				'orderby'        => 'date',
				'order'          => 'desc',
				'post_status'    => 'wc-processing',
				'meta_query'     => array(
					array(
						'key'     => '_customer_date',
						'value'   => '',
						'compare' => '>'
					)
				),
				'fields'         => 'ids'
			) );
		}

		/**
		 * Get customer object
		 *
		 * @return YITH_WCStripe_Customer
		 */
		public function get_customer() {
			return YITH_WCStripe_Customer();
		}

		/**
		 * Handle the webhooks from stripe account
		 *
		 * @since 1.0.0
		 */
		public function handle_webhooks() {
			include_once( 'class-yith-stripe-webhook.php' );

			YITH_WCStripe_Webhook::route();
		}

		/**
		 * Add confirmation endpoint for payment methods
		 *
		 * @param $query_vars array Array of available endpoints
		 *
		 * @return array Array of filtered endpoints
		 */
		public function add_query_vars( $query_vars ) {
			$query_vars['confirm-payment-method'] = 'confirm-payment-method';

			return $query_vars;
		}

		/**
		 * Handle confirmation endpoint
		 * When we call this endpoint, we suppose that method was correctly confirmed; anyway, if any server process is needed,
		 * yith_wcstripe_method_correctly_confirmed filter is available for further processing
		 *
		 * @return void
		 */
		public function handle_card_confirmation() {
			global $wp;

			if ( isset( $wp->query_vars['confirm-payment-method'] ) ) {
				wc_nocache_headers();

				$token_id = absint( $wp->query_vars['confirm-payment-method'] );
				$token    = WC_Payment_Tokens::get( $token_id );

				if ( is_null( $token ) || get_current_user_id() !== $token->get_user_id() || ! isset( $_REQUEST['_wpnonce'] ) || false === wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'confirm-payment-method-' . $token_id ) ) {
					wc_add_notice( __( 'Invalid payment method.', 'yith-woocommerce-stripe' ), 'error' );
				} else {
					if ( apply_filters( 'yith_wcstripe_method_correctly_confirmed', true, $token ) ) {
						$token->update_meta_data( 'confirmed', true );
						$token->save();
						wc_add_notice( __( 'This payment method was successfully confirmed.', 'yith-woocommerce-stripe' ) );
					} else {
						wc_add_notice( __( 'There was an error while confirming the payment method; please, try again later.', 'yith-woocommerce-stripe' ) );
					}
				}

				wp_safe_redirect( wc_get_account_endpoint_url( 'payment-methods' ) );
				exit();
			}
		}

		/* === HANDLE TOKENS METHODS === */

		/**
		 * Handle setting a token as default
		 *
		 * @param                     $token_id
		 * @param WC_Payment_Token_CC $token
		 *
		 * @return bool
		 */
		public function set_default_token_on_stripe( $token_id, $token = null ) {
			// retrieve token when not provided
			if ( empty( $token ) ) {
				$token = WC_Payment_Tokens::get( $token_id );
			}

			// check if token was registered by Stripe before trying to delete it
			if ( $token->get_gateway_id() != self::$gateway_id ) {
				return false;
			}

			$gateway = $this->get_gateway();

			try {

				// Initializate SDK and set private key
				if ( $gateway ) {
					$gateway->init_stripe_sdk();

					$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $token->get_user_id() );

					if ( empty( $customer ) ) {
						return false;
					}

					$customer = $gateway->api->update_customer( $customer['id'], array_merge(
						array(
							'invoice_settings' => array(
								'default_payment_method' => $token->get_token()
							)
						),
						strpos( $token->get_token(), 'card' ) === 0 ? array(
							'default_source' => $token->get_token()
						) : array()
					) );

					// backward compatibility
					YITH_WCStripe()->get_customer()->update_usermeta_info( $customer->metadata->user_id, array_merge(
						array(
							'id'             => $customer->id,
							'cards'          => $customer->sources->data,
							'default_source' => $token->get_token()
						)
					) );
				}

				return true;

			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Handle the card removing from stripe databases for the customer
		 *
		 * @param                     $token_id
		 * @param WC_Payment_Token_CC $token
		 *
		 * @return bool
		 */
		public function delete_token_from_stripe( $token_id, $token ) {

			// check if token was registered by Stripe before trying to delete it
			if ( $token->get_gateway_id() != self::$gateway_id ) {
				return false;
			}

			$gateway = $this->get_gateway();

			try {

				// Initializate SDK and set private key
				if ( $gateway ) {
					$gateway->init_stripe_sdk();

					// delete card
					$gateway->api->delete_payment_method( $token->get_token() );

					// get customer
					$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $token->get_user_id() );

					if ( $token->is_default() && $customer ) {
						// set arbitrarily last saved card as default
						$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id() );
						$token  = end( $tokens );

						if ( $token ) {
							$customer = $gateway->api->update_customer( $customer['id'], array_merge(
								array(
									'invoice_settings' => array(
										'default_payment_method' => $token->get_token()
									)
								),
								strpos( $token->get_token(), 'card' ) === 0 ? array(
									'default_source' => $token->get_token()
								) : array()
							) );

							$token->set_default( true );
							$token->save();
						}
					}

					$customer = $gateway->api->get_customer( $customer['id'] );

					// backward compatibility
					YITH_WCStripe()->get_customer()->update_usermeta_info( $customer->metadata->user_id, array(
						'id'             => $customer->id,
						'cards'          => $customer->sources->data,
						'default_source' => $token ? $token->get_token() : false
					) );
				}

				return true;

			} catch ( Stripe\Exception\ApiErrorException $e ) {
				return false;

			}
		}

		/**
		 * Convert tokens to new system of WC 2.6, as soon as the user is logged in
		 */
		public function convert_tokens( $user_id = null ) {
			$user_id = $user_id ? $user_id : get_current_user_id();
			if ( class_exists( 'WC_Payment_Tokens' ) && $user_id && '' === get_user_meta( $user_id, '_tokens_converted', true ) ) {
				$gateway = $this->get_gateway();

				// Initialize SDK and set private key

				if ( $gateway ) {

					$gateway->init_stripe_sdk();

					$customer = (array) YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id );
					$tokens   = WC_Payment_Tokens::get_customer_tokens( $user_id );

					if ( ! empty( $customer['cards'] ) && empty( $tokens ) ) {
						foreach ( $customer['cards'] as $card ) {
							$token = new WC_Payment_Token_CC();
							$token->set_token( $card->id );
							$token->set_gateway_id( $gateway->id );
							$token->set_user_id( $user_id );

							$token->set_card_type( strtolower( $card->brand ) );
							$token->set_last4( $card->last4 );
							$token->set_expiry_month( ( 1 === strlen( $card->exp_month ) ? '0' . $card->exp_month : $card->exp_month ) );
							$token->set_expiry_year( $card->exp_year );

							$token->set_default( (bool) ( $customer['default_source'] == $card->id ) );

							$token->save();
						}
					}

					update_user_meta( $user_id, '_tokens_converted', 'yes' );

				}
			}
		}

		/**
		 * Customize column method on Payment Methods my account page
		 *
		 * @param $method
		 */
		public function myaccount_method_column( $method ) {
			if ( self::$gateway_id != $method['method']['gateway'] ) {
				if ( ! empty( $method['method']['last4'] ) ) {
					/* translators: 1: credit card type 2: last 4 digits */
					echo sprintf( __( '%1$s ending in %2$s', 'woocommerce' ), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) );
				} else {
					echo esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
				}

				return;
			}

			$icon_brands   = array(
				'american express' => 'amex'
			);
			$icon          = WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/' . str_replace( array_keys( $icon_brands ), array_values( $icon_brands ), strtolower( $method['method']['brand'] ) ) . '.png' );
			$dots          = apply_filters( 'yith_wcstripe_card_number_dots', "&bull;&bull;&bull;&bull;" );
			$current_year  = date( 'y' );
			$current_month = date( 'm' );
			list( $exp_month, $exp_year ) = explode( '/', $method['expires'] );

			printf( '<img src="%s" alt="%s" style="width:40px;"/>', esc_url( $icon ), esc_attr( strtolower( $method['method']['brand'] ) ) );
			printf(
				'<span class="card-type"><strong>%s</strong></span> <span class="card-number"><small><em>%s</em>%s</small></span>',
				esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ),
				$dots,
				esc_html( $method['method']['last4'] )
			);

			if ( $method['is_default'] ) {
				printf( '<span class="tag-label default">%s</span>', esc_html__( 'default', 'yith-woocommerce-stripe' ) );
			}

			if ( $exp_year < $current_year || ( $exp_year == $current_year && $exp_month < $current_month ) ) {
				printf( '<span class="tag-label expired">%s</span>', esc_html__( 'expired', 'yith-woocommerce-stripe' ) );
			}
		}

		/**
		 * @param $method array Method array
		 * @param $token  \WC_Payment_Token Token
		 */
		public function myaccount_method( $method, $token ) {
			if ( self::$gateway_id != $method['method']['gateway'] ) {
				return $method;
			}

			$current_year  = date( 'y' );
			$current_month = date( 'm' );
			list( $exp_month, $exp_year ) = explode( '/', $method['expires'] );

			// unset Set Default button, when card is expired
			if ( $exp_year < $current_year || ( $exp_year == $current_year && $exp_month < $current_month ) ) {
				unset( $method['actions']['default'] );
			}

			if ( ! $token->get_meta( 'confirmed' ) ) {
				$method['actions']['confirm'] = array(
					'url'  => wp_nonce_url( wc_get_endpoint_url( 'confirm-payment-method', $token->get_id() ), 'confirm-payment-method-' . $token->get_id() ),
					'name' => __( 'Confirm', 'yith-woocommerce-stripe' )
				);
			}

			return $method;
		}

		/* === WC EMAILS === */

		/**
		 * Register email classes for stripe
		 *
		 * @param $classes mixed Array of email class instances
		 *
		 * @return mixed Filtered array of email class instances
		 * @since 1.0.0
		 */
		public function register_email_classes( $classes ) {
			$classes['YITH_WCStripe_Expiring_Card_Email']      = include_once( YITH_WCSTRIPE_INC . 'emails/class-yith-stripe-expiring-card-email.php' );
			$classes['YITH_WCStripe_Renew_Needs_Action_Email'] = include_once( YITH_WCSTRIPE_INC . 'emails/class-yith-stripe-renew-needs-action-email.php' );

			return $classes;
		}

		/**
		 * Register email action for stripe
		 *
		 * @param $emails mixed Array of registered actions
		 *
		 * @return mixed Filtered array of registered actions
		 * @since 1.0.0
		 */
		public function register_email_actions( $emails ) {
			$emails = array_merge(
				$emails,
				array(
					'yith_wcstripe_expiring_card',
					'yith_wcstripe_renew_intent_requires_action'
				)
			);

			return $emails;
		}

		/**
		 * Locate default templates of woocommerce in plugin, if exists
		 *
		 * @param $core_file     string
		 * @param $template      string
		 * @param $template_base string
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function register_woocommerce_template( $core_file, $template, $template_base ) {
			$located = yith_wcstripe_locate_template( $template );

			if ( $located && file_exists( $located ) ) {
				return $located;
			} else {
				return $core_file;
			}
		}

		/* === CRONS === */

		/**
		 * Add new schedules for stripe crons
		 *
		 * @param $schedules array Array of currently defined schedules
		 *
		 * @return array Filtered array of schedules
		 */
		public function add_schedules( $schedules ) {
			$schedules['yith_wcstripe_3_times_for_hour'] = array(
				'interval' => 20 * MINUTE_IN_SECONDS,
				'display'  => __( 'Every 20 minutes', 'yith-woocommerce-stripe' )
			);
			$schedules['yith_wcstripe_every_15_days']    = array(
				'interval' => 15 * DAY_IN_SECONDS,
				'display'  => __( 'Every 15 days', 'yith-woocommerce-stripe' )
			);

			return $schedules;
		}

		/**
		 * Set up crons for the plugin
		 *
		 * @return void
		 */
		public function schedule_crons() {
			// schedule cron for expiring cards reminder
			$expiring_card_email_preferences = get_option( 'woocommerce_expiring_card_settings', array() );

			if ( isset( $expiring_card_email_preferences['enabled'] ) && 'yes' == $expiring_card_email_preferences['enabled'] ) {
				if ( ! wp_next_scheduled( 'yith_wcstripe_expiring_cards_reminders_scheduler' ) ) {
					wp_schedule_event( time(), 'yith_wcstripe_every_15_days', 'yith_wcstripe_expiring_cards_reminders_scheduler' );
				}
				if ( ! wp_next_scheduled( 'yith_wcstripe_expiring_cards_reminders_dispatcher' ) ) {
					wp_schedule_event( time(), 'yith_wcstripe_3_times_for_hour', 'yith_wcstripe_expiring_cards_reminders_dispatcher' );
				}
			} else {
				if ( function_exists( 'wp_unschedule_hook' ) ) {
					wp_unschedule_hook( 'yith_wcstripe_expiring_cards_reminders_scheduler' );
					wp_unschedule_hook( 'yith_wcstripe_expiring_cards_reminders_dispatcher' );
				}
			}
		}

		/**
		 * Schedule reminder emails to send
		 *
		 * @return void
		 */
		public function schedule_expiring_cards_reminders() {
			global $wpdb;

			$last_execution = get_option( 'yith_wcstripe_expiring_card_reminder_last_execution' );
			$current_month  = date( 'Y-m' );

			if ( $last_execution && $last_execution >= $current_month ) {
				return;
			}

			$options                  = get_option( 'woocommerce_expiring_card_settings', array() );
			$months_before_expiration = isset( $options['months_before_expiration'] ) ? $options['months_before_expiration'] : 1;

			$expiration_month = date( 'Y-m', strtotime( "+{$months_before_expiration} MONTHS" ) );
			list( $exp_year, $exp_month ) = explode( '-', $expiration_month );

			$query      = "SELECT token_id, token, user_id from {$wpdb->prefix}woocommerce_payment_tokens AS t 
                      LEFT JOIN {$wpdb->prefix}woocommerce_payment_tokenmeta as tm1 ON t.token_id = tm1.payment_token_id
                      LEFT JOIN {$wpdb->prefix}woocommerce_payment_tokenmeta as tm2 ON t.token_id = tm2.payment_token_id
                      WHERE tm1.meta_key = %s AND tm1.meta_value = %d AND tm2.meta_key = %s AND tm2.meta_value = %d";
			$query_args = array(
				'expiry_month',
				$exp_month,
				'expiry_year',
				$exp_year
			);

			$results = $wpdb->get_results( $wpdb->prepare( $query, $query_args ), ARRAY_A );

			if ( ! empty( $results ) ) {
				$queue = get_option( 'yith_wcstripe_expiring_card_reminder_queue', array() );
				$queue = is_array( $queue ) ? $queue : array();
				$queue = array_merge( $queue, array_combine( wp_list_pluck( $results, 'token' ), $results ) );

				update_option( 'yith_wcstripe_expiring_card_reminder_queue', $queue );
			}

			update_option( 'yith_wcstripe_expiring_card_reminder_last_execution', $current_month );
		}

		/**
		 * Dispatch reminder emails
		 *
		 * @return void
		 */
		public function dispatch_expiring_cards_reminders() {
			$options = get_option( 'woocommerce_expiring_card_settings', array() );
			$queue   = get_option( 'yith_wcstripe_expiring_card_reminder_queue', array() );
			$counter = 0;

			if ( empty( $queue ) ) {
				return;
			}

			foreach ( $queue as $id => $item ) {
				if ( $counter ++ >= 20 ) {
					break;
				}

				unset( $queue[ $id ] );

				if (
					isset( $options['subscribed_only'] ) &&
					'yes' == $options['subscribed_only'] &&
					function_exists( 'YITH_WC_Subscription' ) &&
					method_exists( YITH_WC_Subscription(), 'get_user_subscriptions' )
				) {
					$subscriptions = YITH_WC_Subscription()->get_user_subscriptions( $item['user_id'], 'active' );

					if ( ! $subscriptions ) {
						continue;
					}
				}

				$exclusions = isset( $options['exclusions'] ) ? explode( ',', $options['exclusions'] ) : array();
				$userdata   = get_userdata( $item['user_id'] );

				if ( ! empty( $exclusions ) && in_array( $userdata->user_email, $exclusions ) ) {
					continue;
				}

				do_action( 'yith_wcstripe_expiring_card', $item['user_id'], new WC_Payment_Token_CC( $item['token_id'] ) );
			}

			update_option( 'yith_wcstripe_expiring_card_reminder_queue', $queue );

		}

		/* === AJAX METHODS === */

		/**
		 * Returns order details for hosted checkout
		 */
		public function send_checkout_details() {
			check_ajax_referer( 'refresh-details', 'yith_stripe_refresh_details', true );

			WC()->cart->calculate_totals();

			wp_send_json( array(
				'amount'   => $this->get_amount( WC()->cart->total ),
				'currency' => strtolower( get_woocommerce_currency() )
			) );
		}

		/**
		 * Refresh intent before moving forward with checkout process
		 *
		 * @return void
		 */
		public function refresh_intent() {
			check_ajax_referer( 'refresh-intent', 'yith_stripe_refresh_intent', true );

			do_action( 'yith_wcstripe_before_refresh_intent' );

			$token       = isset( $_POST['selected_token'] ) ? intval( $_POST['selected_token'] ) : false;
			$is_checkout = isset( $_POST['is_checkout'] ) ? intval( $_POST['is_checkout'] ) : false;
			$order       = isset( $_POST['order'] ) ? $_POST['order'] : false;
			$gateway     = $this->get_gateway();

			wc_maybe_define_constant( 'YITH_WCSTRIPE_DOING_CHECKOUT', $is_checkout );

			try {
				$intent = $gateway->update_session_intent( $token, $order );
			} catch ( Exception $e ) {
				wp_send_json( array(
					'res'   => false,
					'error' => array(
						'code'    => $e->getCode(),
						'message' => $e->getMessage()
					)
				) );
			}

			if ( ! $intent ) {
				wp_send_json( array(
					'res'   => false,
					'error' => array(
						'code'    => 0,
						'message' => __( 'There was an error during payment; please, try again later', 'yith-woocommerce-stripe' )
					)
				) );
			}

			wp_send_json( array(
				'res'           => true,
				'amount'        => isset( $intent->amount ) ? $intent->amount : 0,
				'currency'      => isset( $intent->currency ) ? $intent->currency : '',
				'intent_secret' => $intent->client_secret,
				'intent_id'     => $intent->id,
				'is_setup'      => $intent instanceof SetupIntent,
				'refresh'       => apply_filters( 'yith_wcstripe_reload_after_refresh_intent', false )
			) );
		}

		/**
		 * Verify intent after customer authentication
		 * Process actions required after authentication; if everything was fine redirect to thank you page, otherwise redirects
		 * to checkout with an error message
		 *
		 * @return void
		 */
		public function verify_intent() {
			$gateway  = $this->get_gateway();
			$order_id = isset( $_GET['order'] ) ? intval( $_GET['order'] ) : false;

			try {
				if ( ! $gateway ) {
					throw new Exception( __( 'Error while initializing gateway', 'yith-woocommerce-stripe' ) );
				}

				// Retrieve the order.
				$order = wc_get_order( $order_id );

				if ( ! $order ) {
					throw new Exception( __( 'Missing order ID for payment confirmation', 'yith-woocommerce-stripe' ) );
				}

				wc_maybe_define_constant( 'YITH_WCSTRIPE_DOING_CHECKOUT', true );

				$result = $gateway->pay_ajax( $order );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				}

				if ( ! isset( $_GET['is_ajax'] ) ) {
					$redirect_url = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : $gateway->get_return_url( $order );

					wp_safe_redirect( $redirect_url );
				}

				exit;

			} catch ( Exception $e ) {
				wc_add_notice( sprintf( __( 'Payment verification error: %s', 'woocommerce-gateway-stripe' ), $e->getMessage() ), 'error' );

				$redirect_url = WC()->cart->is_empty() ? wc_get_cart_url() : wc_get_checkout_url();

				if ( isset( $_GET['is_ajax'] ) ) {
					exit;
				}

				wp_safe_redirect( $redirect_url );
				exit;
			}
		}

		/**
		 * Refresh intent before moving forward with checkout process
		 *
		 * @return void
		 */
		public function refresh_session() {
			check_ajax_referer( 'refresh-session', 'yith_stripe_refresh_session', true );

			$is_checkout = isset( $_POST['is_checkout'] ) ? intval( $_POST['is_checkout'] ) : false;
			$order       = isset( $_POST['order'] ) ? $_POST['order'] : false;
			$gateway     = $this->get_gateway();

			wc_maybe_define_constant( 'YITH_WCSTRIPE_DOING_CHECKOUT', $is_checkout );

			if ( $is_checkout && ! $order ) {
				if ( ! class_exists( 'YITH_WCStripe_Checkout' ) ) {
					include_once( YITH_WCSTRIPE_DIR . 'includes/class-yith-stripe-checkout.php' );
				}

				$checkout = new YITH_WCStripe_Checkout();

				if ( ! $checkout->is_checkout_valid() ) {
					wp_send_json( array(
						'res'            => false,
						'checkout_valid' => false
					) );
				}
			}

			try {
				$args = array();

				if ( $order ) {
					$args['order_id'] = $order;
				}

				$session = $gateway->create_checkout_session( $args );
			} catch ( Exception $e ) {
				wp_send_json( array(
					'res'   => false,
					'error' => array(
						'code'    => $e->getCode(),
						'message' => $e->getMessage()
					)
				) );
			}

			if ( ! $session ) {
				wp_send_json( array(
					'res'   => false,
					'error' => array(
						'code'    => 0,
						'message' => __( 'There was an error during payment; please, try again later', 'yith-woocommerce-stripe' )
					)
				) );
			}

			wp_send_json( array(
				'res'        => true,
				'session_id' => $session->id,
			) );
		}
	}
}