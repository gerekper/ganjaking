<?php

/**
 * Main Plugin Class
 *
 * @since 1.0
 */
class WC_Pre_Orders {
	/**
	 * The single instance of the class.
	 *
	 * @var $_instance
	 * @since 1.13.0
	 */
	protected static $_instance = null;

	/**
	 * Plugin file path without trailing slash
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * Plugin url without trailing slash
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * WC_Logger instance
	 *
	 * @var object
	 */
	private $logger;

	/**
	 * @var string __FILE__ of the base plugin file
	 */
	private $base_file;

	/**
	 * Setup main plugin class
	 *
	 * @return \WC_Pre_Orders
	 * @since  1.0
	 */
	public function __construct( $base_file ) {
		$this->base_file = $base_file;

		// load core classes
		$this->load_classes();

		// load classes that require WC to be loaded
		add_action( 'woocommerce_init', array( $this, 'init' ) );

		// add pre-order notification emails
		add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );

		// add 'pay later' payment gateway
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_pay_later_gateway' ) );

		// Declare compatibility with High-Performance Order Storage.
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );

		// Hook up emails
		$emails = array(
			'wc_pre_order_status_new_to_active',
			'wc_pre_order_status_completed',
			'wc_pre_order_status_active_to_cancelled',
			'wc_pre_orders_pre_order_date_changed',
			'wc_pre_orders_pre_ordered',
			'wc_pre_orders_pre_order_available',
		);
		foreach ( $emails as $action ) {
			add_action( $action, array( $this, 'send_transactional_email' ), 10, 2 );
			add_action(
				'woocommerce_order_action_send_email_' . $action,
				function ( $order ) use ( $action ) {
					return $this->sendmail( $action, $order );
				}
			);
		}

		// Un-schedule events on plugin deactivation
		register_deactivation_hook( $this->base_file, array( $this, 'deactivate' ) );
	}

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @return WC_Pre_Orders
	 * @since 1.5.25
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * This function will send email based on action name.
	 *
	 * @param string $email Action name
	 * @param WC_Order $order
	 */
	public function sendmail( $email, $order ) {
		// Convert action name to class name
		$email = implode( '_', array_map( 'ucfirst', explode( '_', $email ) ) );
		$email = str_replace( 'Wc_Pre_Orders_', 'WC_Pre_Orders_Email_', $email );

		$emails = WC()->mailer->emails;

		if ( ! isset( $emails[ $email ] ) ) {
			return;
		}

		$mail = $emails[ $email ];

		$mail->trigger( $order->get_id() );

		/* translators: %s: email title */
		$order->add_order_note( sprintf( __( '%s email notification manually sent.', 'wc-pre-orders' ), $mail->title ), false, true );
	}

	/**
	 * Load core classes
	 *
	 * @since 1.0
	 */
	public function load_classes() {

		// load wp-cron hooks for scheduled events
		require 'class-wc-pre-orders-cron.php';
		$this->cron = new WC_Pre_Orders_Cron();

		// load manager class to process pre-order actions
		require 'class-wc-pre-orders-manager.php';
		$this->manager = new WC_Pre_Orders_Manager();

		// load product customizations / tweaks
		require 'class-wc-pre-orders-product.php';
		$this->product = new WC_Pre_Orders_Product();

		// Load cart customizations / overrides
		require 'class-wc-pre-orders-cart.php';
		$this->cart = new WC_Pre_Orders_Cart();

		// Load checkout customizations / overrides
		require 'class-wc-pre-orders-checkout.php';
		$this->checkout = new WC_Pre_Orders_Checkout();

		// Load order hooks
		require 'class-wc-pre-orders-order.php';
		$this->order = new WC_Pre_Orders_Order();

		include_once 'class-wc-pre-orders-my-pre-orders.php';
	}

	/**
	 * Loads the classes for the integration with WooCommerce Blocks.
	 */
	public static function load_block_classes() {

		if ( WC_PRE_ORDERS_GUTENBERG_EXISTS ) {
			require_once __DIR__ . '/blocks/class-wc-pre-orders-blocks-integration.php';
			require_once __DIR__ . '/blocks/class-wc-pre-orders-extend-store-api.php';
			new WooCommerce\Pre_Orders\Blocks\WC_Pre_Orders_Blocks_Integration();
		}
	}

	/**
	 * Load actions and filters that require WC to be loaded
	 *
	 * @since 1.0
	 */
	public function init() {

		if ( is_admin() ) {
			// Load admin.
			if ( defined( 'DOING_AJAX' ) ) {
				require 'admin/class-wc-pre-orders-admin-ajax.php';
			} else {
				require 'admin/class-wc-pre-orders-admin.php';
			}

			// add a 'Configure' link to the plugin action links
			add_filter(
				'plugin_action_links_' . plugin_basename( $this->base_file ),
				array(
					$this,
					'plugin_action_links',
				)
			);
		} else {

			// Watch for cancel URL action
			add_action( 'init', array( $this->manager, 'check_cancel_pre_order' ) );

			// add countdown shortcode
			add_shortcode( 'woocommerce_pre_order_countdown', array( $this, 'pre_order_countdown_shortcode' ) );
		}
	}

	/**
	 * Add the 'pay later' gateway, which replaces gateways that do not support pre-orders when the pre-order is charged
	 * upon release
	 *
	 * @since 1.0
	 */
	public function add_pay_later_gateway( $gateways ) {
		require_once 'gateways/class-wc-pre-orders-gateway-pay-later.php';

		$gateways[] = 'WC_Pre_Orders_Gateway_Pay_Later';

		return $gateways;
	}

	/**
	 * Pre-order countdown shortcode
	 *
	 * @param array $atts associative array of shortcode parameters
	 *
	 * @return string shortcode content
	 */
	public function pre_order_countdown_shortcode( $atts ) {

		require_once 'shortcodes/class-wc-pre-orders-shortcode-countdown.php';

		return WC_Shortcodes::shortcode_wrapper(
			array(
				'WC_Pre_Orders_Shortcode_Countdown',
				'output',
			),
			$atts,
			array( 'class' => 'woocommerce-pre-orders' )
		);
	}

	/**
	 * Adds Pre-Order email classes
	 *
	 * @since 1.0
	 */
	public function add_email_classes( $email_classes ) {

		foreach (
			array(
				'new-pre-order',
				'pre-order-available',
				'pre-order-cancelled',
				'admin-pre-order-cancelled',
				'pre-order-date-changed',
				'pre-ordered',
			) as $class_file_name
		) {
			require_once "emails/class-wc-pre-orders-email-{$class_file_name}.php";
		}

		$email_classes['WC_Pre_Orders_Email_New_Pre_Order']             = new WC_Pre_Orders_Email_New_Pre_Order();
		$email_classes['WC_Pre_Orders_Email_Pre_Ordered']               = new WC_Pre_Orders_Email_Pre_Ordered();
		$email_classes['WC_Pre_Orders_Email_Pre_Order_Date_Changed']    = new WC_Pre_Orders_Email_Pre_Order_Date_Changed();
		$email_classes['WC_Pre_Orders_Email_Pre_Order_Cancelled']       = new WC_Pre_Orders_Email_Pre_Order_Cancelled();
		$email_classes['WC_Pre_Orders_Email_Admin_Pre_Order_Cancelled'] = new WC_Pre_Orders_Email_Admin_Pre_Order_Cancelled();
		$email_classes['WC_Pre_Orders_Email_Pre_Order_Available']       = new WC_Pre_Orders_Email_Pre_Order_Available();

		return $email_classes;
	}

	/**
	 * Sends transactional email by hooking into pre-order status changes
	 *
	 * @since 1.0
	 */
	public function send_transactional_email( $args = array(), $message = '' ) {
		global $woocommerce;

		$woocommerce->mailer();

		do_action( current_filter() . '_notification', $args, $message );
	}

	/**
	 * Remove terms and scheduled events on plugin deactivation
	 *
	 * @since 1.0
	 */
	public function deactivate() {

		// Remove scheduling function before removing scheduled hook, or else it will get re-added
		remove_action( 'init', array( $this->cron, 'add_scheduled_events' ) );

		// clear pre-order completion check event
		wp_clear_scheduled_hook( 'wc_pre_orders_completion_check' );
	}

	/**
	 * Return the plugin action links.
	 *
	 * @param array $actions Associative array of action names to anchor tags.
	 *
	 * @return array          Associative array of plugin action links.
	 */
	public function plugin_action_links( $actions ) {
		$plugin_actions = array(
			'settings' => sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=wc-settings&tab=pre_orders' ) ), __( 'Settings', 'wc-pre-orders' ) ),
			'manage'   => sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=wc_pre_orders' ) ), __( 'Manage pre-orders', 'wc-pre-orders' ) ),
			'support'  => '<a href="https://woocommerce.com/my-account/create-a-ticket/">' . __( 'Support', 'wc-pre-orders' ) . '</a>',
			'docs'     => '<a href="https://docs.woocommerce.com/document/pre-orders/">' . __( 'Docs', 'wc-pre-orders' ) . '</a>',
		);

		return array_merge( $plugin_actions, $actions );
	}

	/**
	 * Returns the plugin's path without a trailing slash
	 *
	 * @return string the plugin path
	 * @since  1.0
	 *
	 */
	public function get_plugin_path() {
		if ( $this->plugin_path ) {
			return $this->plugin_path;
		}

		$this->plugin_path = untrailingslashit( plugin_dir_path( $this->base_file ) );

		return $this->plugin_path;
	}


	/**
	 * Returns the plugin's url without a trailing slash
	 *
	 * @return string the plugin url
	 * @since  1.0
	 *
	 */
	public function get_plugin_url() {
		if ( $this->plugin_url ) {
			return $this->plugin_url;
		}

		$this->plugin_url = plugins_url( basename( plugin_dir_path( $this->base_file ) ), basename( $this->base_file ) );

		return $this->plugin_url;
	}

	/**
	 * Log errors to WooCommerce log
	 *
	 * @param string $message message to log
	 *
	 * @since 1.0
	 *
	 */
	public function log( $message ) {
		global $woocommerce;

		if ( ! is_object( $this->logger ) ) {
			if ( class_exists( 'WC_Logger' ) ) {
				$this->logger = new WC_Logger();
			} else {
				$this->logger = $woocommerce->logger();
			}
		}

		$this->logger->add( 'pre-orders', $message );
	}

	/**
	 * Get supported product types.
	 *
	 * @return array
	 */
	public static function get_supported_product_types() {
		$product_types = array(
			'simple',
			'variable',
			'composite',
			'bundle',
			'booking',
			'mix-and-match',
		);

		return apply_filters( 'wc_pre_orders_supported_product_types', $product_types );
	}

	/**
	 * Declare compatibility with High-Performance Order Storage.
	 *
	 * @since x.x.x
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->base_file, true );
		}
	}

	/**
	 * Check if High-performance Order Storage ("HPOS") enable
	 *
	 * @return bool Whether enabled or not
	 */
	public static function is_hpos_enabled() {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) ) {
			return Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
		}
		return false;
	}
}
