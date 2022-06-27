<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once( 'class-wcslack-client.php' );

class WC_Slack_Settings extends WC_Integration {

	/**
	 * Slack Client Id.
	 *
	 * @var string
	 */
	protected $client_id;

	/**
	 * Slack client secret.
	 *
	 * @var string
	 */
	protected $client_secret;

	/**
	 * Flag to keep from notices being displayed twice as this class is constructed by WC multiple times.
	 */
	private static $notices_triggered = false;

	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {

		$this->id                 = 'wcslack';
		$this->method_title       = __( 'Slack', 'woocommerce-slack' );
		$this->method_description = __( 'Configure the settings below to send notifications to your Slack channel based on WooCommerce events.', 'woocommerce-slack' );

		// API.
		$this->redirect_uri = WC()->api_request_url( 'wc_slack' );

		// Define user set variables.
		$this->client_id       = $this->get_option( 'client_id' );
		$this->client_secret   = $this->get_option( 'client_secret' );

		// General.
		$this->debug = $this->get_option( 'debug' );

		if ( is_admin() ) {
			$this->init_form_fields();
			$this->init_settings();
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		// Actions.
		add_action( 'woocommerce_api_wc_slack', array( $this, 'oauth_redirect' ) );

		/**
		 * Define user set variables.
		 **/

		// General
		$this->channel              = $this->get_option( 'channel' );
		$this->emoji                = $this->get_option( 'emoji' );
		$this->name                 = $this->get_option( 'name' );

		// New Post
		$this->post_new             = $this->get_option( 'notif-post-new' );
		$this->post_new_channel     = $this->get_option( 'notif-post-new-channel' );
		$this->post_new_emoji       = $this->get_option( 'notif-post-new-emoji' );
		$this->post_new_extend      = $this->get_option( 'notif-post-new-extend' );
		$this->post_new_message     = $this->get_option( 'notif-post-new-message' );

		// New Orders
		$this->order_new            = $this->get_option( 'notif-order-new' );
		$this->order_new_channel    = $this->get_option( 'notif-order-new-channel' );
		$this->order_new_emoji      = $this->get_option( 'notif-order-new-emoji' );
		$this->order_new_free       = $this->get_option( 'notif-order-new-free' );
		$this->order_new_extend     = $this->get_option( 'notif-order-new-extend' );
		$this->order_new_message    = $this->get_option( 'notif-order-new-message' );

		// Back Orders
		$this->back_order           = $this->get_option( 'notif-back-order' );
		$this->back_order_channel   = $this->get_option( 'notif-back-order-channel' );
		$this->back_order_emoji     = $this->get_option( 'notif-back-order-emoji' );
		$this->back_order_message   = $this->get_option( 'notif-back-order-message' );

		// Low on Stock
		$this->low_stock            = $this->get_option( 'notif-low-stock' );
		$this->low_stock_channel    = $this->get_option( 'notif-low-stock-channel' );
		$this->low_stock_emoji      = $this->get_option( 'notif-low-stock-emoji' );
		$this->low_stock_message    = $this->get_option( 'notif-low-stock-message' );

		// Out of Stock
		$this->out_stock            = $this->get_option( 'notif-out-stock' );
		$this->out_stock_channel    = $this->get_option( 'notif-out-stock-channel' );
		$this->out_stock_emoji      = $this->get_option( 'notif-out-stock-emoji' );
		$this->out_stock_message    = $this->get_option( 'notif-out-stock-message' );

		// New Review
		$this->new_review           = $this->get_option( 'notif-new-review' );
		$this->new_review_channel   = $this->get_option( 'notif-new-review-channel' );
		$this->new_review_emoji     = $this->get_option( 'notif-new-review-emoji' );
		$this->new_review_extend    = $this->get_option( 'notif-new-review-extend' );
		$this->new_review_message   = $this->get_option( 'notif-new-review-message' );

		// New Customer
		$this->new_customer         = $this->get_option( 'notif-new-customer' );
		$this->new_customer_channel = $this->get_option( 'notif-new-customer-channel' );
		$this->new_customer_emoji   = $this->get_option( 'notif-new-customer-emoji' );
		$this->new_customer_message = $this->get_option( 'notif-new-customer-message' );

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'admin_init', array( $this, 'test_init' ), 10 );
		add_action( 'admin_init', array( $this, 'scripts' ) );

		add_action( 'woocommerce_slack_update_channels', array( $this, 'update_channels' ) );

		// Filters.
		add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );

		if ( isset( $_POST['wc_slack_redirect'] ) && $_POST['wc_slack_redirect'] && empty( $_POST['save'] ) ) {
			$this->process_slack_redirect();
		}

		if ( false === wp_get_scheduled_event( 'woocommerce_slack_update_channels' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'woocommerce_slack_update_channels' );
		}

	}

	/**
	 * Get WC_Logger if enabled.
	 *
	 * @return WC_Logger|null
	 */
	protected function get_logger() {
		if ( 'yes' !== $this->get_option( 'debug' ) ) {
			return null;
		}

		if ( empty( $this->logger ) ) {
			if ( class_exists( 'WC_Logger' ) ) {
				$this->logger = new WC_Logger();
			} else {
				$this->logger = WC()->logger();
			}
		}

		return $this->logger;
	}

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 * @param string $level   Log level.
	 *                        Available options: 'emergency', 'alert',
	 *                        'critical', 'error', 'warning', 'notice',
	 *                        'info' and 'debug'.
	 *                        Defaults to 'info'.
	 */
	private function log( $message, $context = array(), $level = WC_Log_Levels::NOTICE ) {
		$logger = $this->get_logger();
		if ( is_null( $logger ) ) {
			return;
		}

		if ( ! isset( $context['source'] ) ) {
			$context['source'] = $this->id;
		}

		$logger->log( $level, $message, $context );
	}


	/**
	 * Returns an authorized API client.
	 *
	 * @return WC_Slack_Client the authorized client object
	 */
	protected function get_client() {
		$client = new WC_Slack_Client();
		$client->setApplicationName( 'WooCommerce Slack Integration' );
		$client->setScopes( array( 'channels:read', 'groups:read', 'chat:write:bot' ) );

		$client->setClientId( $this->client_id );
		$client->setClientSecret( $this->client_secret );
		$client->setRedirectUri( $this->redirect_uri );

		$access_token = get_option( 'wc_slack_access_token' );

		if ( ! empty( $access_token ) ) {
			$client->setAccessToken( $access_token );
		} else {
			// Something is wrong with the access token, customer should try to connect again.
			$this->log( 'Access token is empty. Please reconnect with Slack.' );
		}

		return $client;
	}

	protected function test_client_connection() {
		$client = $this->get_client();

		$status = get_transient( 'wcslack_test_connection' );

		if ( $status ) {
			return 'yes' === $status;
		}

		try {
			$status = $client->testConnection();
		} catch ( Exception $e ) {
			$status = false;
		}

		set_transient( 'wcslack_test_connection', $status ? 'yes' : 'no', 86400 );

		return $status;
	}

	/**
	 * Wrapper containing all settings for easy access
	 *
	 * @return  array
	 */

	public function wrapper() {

		$wrapper = array(
			// General
			'api_key'              => get_option( 'wc_slack_access_token' ),
			'channel'              => $this->get_option( 'channel' ),
			'emoji'                => $this->get_option( 'emoji' ),
			'name'                 => $this->get_option( 'name' ),
			'debug'                => $this->get_option( 'debug' ),

			// New Post
			'post_new'             => $this->get_option( 'notif-post-new' ),
			'post_new_channel'     => $this->get_option( 'notif-post-new-channel' ),
			'post_new_emoji'       => $this->get_option( 'notif-post-new-emoji' ),
			'post_new_extend'      => $this->get_option( 'notif-post-new-extend' ),
			'post_new_message'     => $this->get_option( 'notif-post-new-message' ),

			// New Orders
			'order_new'            => $this->get_option( 'notif-order-new' ),
			'order_new_channel'    => $this->get_option( 'notif-order-new-channel' ),
			'order_new_emoji'      => $this->get_option( 'notif-order-new-emoji' ),
			'order_new_free'       => $this->get_option( 'notif-order-new-free' ),
			'order_new_extend'     => $this->get_option( 'notif-order-new-extend' ),
			'order_new_message'    => $this->get_option( 'notif-order-new-message' ),

			// Back Orders
			'back_order'           => $this->get_option( 'notif-back-order' ),
			'back_order_channel'   => $this->get_option( 'notif-back-order-channel' ),
			'back_order_emoji'     => $this->get_option( 'notif-back-order-emoji' ),
			'back_order_message'   => $this->get_option( 'notif-back-order-message' ),

			// Low on Stock
			'low_stock'            => $this->get_option( 'notif-low-stock' ),
			'low_stock_channel'    => $this->get_option( 'notif-low-stock-channel' ),
			'low_stock_emoji'      => $this->get_option( 'notif-low-stock-emoji' ),
			'low_stock_message'    => $this->get_option( 'notif-low-stock-message' ),

			// Out of Stock
			'out_stock'            => $this->get_option( 'notif-out-stock' ),
			'out_stock_channel'    => $this->get_option( 'notif-out-stock-channel' ),
			'out_stock_emoji'      => $this->get_option( 'notif-out-stock-emoji' ),
			'out_stock_message'    => $this->get_option( 'notif-out-stock-message' ),

			// New Review
			'new_review'           => $this->get_option( 'notif-new-review' ),
			'new_review_channel'   => $this->get_option( 'notif-new-review-channel' ),
			'new_review_emoji'     => $this->get_option( 'notif-new-review-emoji' ),
			'new_review_extend'    => $this->get_option( 'notif-new-review-extend' ),
			'new_review_message'   => $this->get_option( 'notif-new-review-message' ),

			// New Customer
			'new_customer'         => $this->get_option( 'notif-new-customer' ),
			'new_customer_channel' => $this->get_option( 'notif-new-customer-channel' ),
			'new_customer_emoji'   => $this->get_option( 'notif-new-customer-emoji' ),
			'new_customer_message' => $this->get_option( 'notif-new-customer-message' ),
		);

		return $wrapper;

	}

	/**
	 * Process Slack redirect.
	 */
	public function process_slack_redirect() {
		$client   = $this->get_client();
		$auth_url = $client->createAuthUrl();
		wp_redirect( $auth_url );
		exit;
	}

	/**
	 * Initialize integration settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		/**
		 * Use variables for frequently used text strings.
		 */
		$which_room_desc = __( 'Which channel or group do you want the notifications to be sent to?', 'woocommerce-slack' );

		$available_channels = $this->available_channels();

		$this->form_fields = array(
			'client_id'       => array(
				'title'       => __( 'Client ID', 'woocommerce-slack' ),
				'type'        => 'text',
				'description' => __( 'Enter your Slack client ID.', 'woocommerce-slack' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'client_secret'   => array(
				'title'       => __( 'Client Secret', 'woocommerce-slack' ),
				'type'        => 'text',
				'description' => __( 'Enter your Slack client secret.', 'woocommerce-slack' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'authorization'   => array(
				'title' => __( 'Authorization', 'woocommerce-slack' ),
				'type'  => 'slack_authorization',
			),
			'channel' => array(
				'title'             => __( 'Default Channel(s)', 'woocommerce-slack' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'description'       => __( 'Which channel or group do you want the notifications to be sent to by default?', 'woocommerce-slack' ),
				'options'           => $available_channels,
			),
			'emoji' => array(
				'title'             => __( 'Default Emoji', 'woocommerce-slack' ),
				'type'              => 'text',
				'placeholder'       => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
				'description'       => sprintf( __( 'Enter the code for an emoji you want to include with each message by default. You can find all codes on a site like %s or you can even upload your own icon as an emoji in %s', 'woocommerce-slack' ), '<a href="https://emoji.muan.co/" target="_blank">Emoji Seacher</a>', '<a href="https://slack.com/customize/emoji" target="_blank">Slack</a>!' ),
				'default'           => '',
			),
			'name' => array(
				'title'             => __( 'Sender Name', 'woocommerce-slack' ),
				'type'              => 'text',
				'placeholder'       => get_bloginfo( 'name' ),
				'description'       => __( 'You can enter a custom name that messages will sent from. By default it\'s your site name.', 'woocommerce-slack' ),
				'default'           => '',
			),
			'debug' => array(
				'title'             => __( 'Debug Mode', 'woocommerce-slack' ),
				'type'              => 'checkbox',
				'label'             => __( 'Enable Debug Mode. <br><strong>Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.</strong>', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => true,
				'description'       => __( 'Enable Logging. Find the logs under WooCommerce > System Status > Logs.', 'woocommerce-slack' ),
			),

			'notif-title-slack' => array(
				'title'             => __( 'Notification Events', 'woocommerce-slack' ),
				'type'              => 'title',
				'description'       => __( 'Check the events you would like to send notifications for.', 'woocommerce-slack' ),
				'class'             => 'wcslack-section-title',
			),

			// New Posts
			'notif-post-new' => array(
				'title'             => __( 'New Post', 'woocommerce-slack' ),
				'type'              => 'checkbox',
				'label'             => __( 'New Post Published', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => true,
				'description'       => __( 'Every time a new post is published', 'woocommerce-slack' ),
			),
			'notif-post-new-channel' => array(
				'type'              => 'multiselect',
				'description'       => $which_room_desc,
				'options'           => $available_channels,
				'class'             => 'wcslack-post-new-field wc-enhanced-select',
				'default'           => 'select',
			),
			'notif-post-new-emoji' => array(
				'type'              => 'text',
				'placeholder'       => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
				'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
				'default'           => '',
				'class'             => 'wcslack-post-new-field',
			),
			'notif-post-new-extend' => array(
				'type'              => 'checkbox',
				'label'             => __( 'Extended Notification', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => false,
				'description'       => __( 'Would you like to send extra details with this notification?', 'woocommerce-slack' ),
				'class'             => 'wcslack-post-new-field',
			),
			'notif-post-new-message' => array(
				'type'              => 'textarea',
				'default'           => '*New Post:* {post_title} by {post_author} - [[{post_link}|View Post]]',
				'placeholder'       => '*New Post:* {post_title} by {post_author} - [[{post_link}|View Post]]',
				'description'       => '<strong>Optional:</strong> You can write a custom notification message. <a href="https://docs.woocommerce.com/document/woocommerce-slack/#custom-notifications">Valid Slack Markup Allowed</a><br />
							<strong>Template Tags:</strong> <span class="wcslack-tag">{post_title}</span> | <span class="wcslack-tag">{post_content}</span> | <span class="wcslack-tag">{post_author}</span> | <span class="wcslack-tag">{post_link}</span>',
				'desc_tip'          => false,
				'class'             => 'wcslack-post-new-field wcslack-post-new-message',
			),

			// New Orders
			'notif-order-new' => array(
				'title'             => __( 'New Order', 'woocommerce-slack' ),
				'type'              => 'checkbox',
				'label'             => __( 'New Order', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => true,
				'description'       => __( 'Every time a new order is made', 'woocommerce-slack' ),
			),
			'notif-order-new-channel' => array(
				'type'              => 'multiselect',
				'description'       => $which_room_desc,
				'options'           => $available_channels,
				'class'             => 'wcslack-order-new-field wc-enhanced-select',
				'default'           => 'select',
			),
			'notif-order-new-emoji' => array(
				'type'              => 'text',
				'placeholder'       => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
				'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
				'default'           => '',
				'class'             => 'wcslack-order-new-field',
			),
			'notif-order-new-free' => array(
				'type'              => 'checkbox',
				'label'             => __( 'Free Orders', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => false,
				'description'       => sprintf( __( 'Notify when a %s0 order is made?', 'woocommerce-slack' ), get_woocommerce_currency_symbol() ),
				'class'             => 'wcslack-order-new-field',
			),
			'notif-order-new-extend' => array(
				'type'              => 'checkbox',
				'label'             => __( 'Extended Notification', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => false,
				'description'       => __( 'Would you like to send extra details with this notification?', 'woocommerce-slack' ),
				'class'             => 'wcslack-order-new-field',
			),
			'notif-order-new-message' => array(
				'type'              => 'textarea',
				'default'           => '*New Order #{order_id}:* {first_name} {last_name} ordered {order_items} for {order_total} - [[{order_link}|View Order]]',
				'placeholder'       => '*New Order #{order_id}:* {first_name} {last_name} ordered {order_items} for {order_total} - [[{order_link}|View Order]]',
				'description'       => '<strong>Optional:</strong> You can write a custom notification message. <a href="https://docs.woocommerce.com/document/woocommerce-slack/#custom-notifications">Valid Slack Markup Allowed</a>.<br />
							<strong>Template Tags:</strong> <span class="wcslack-tag">{order_id}</span> | <span class="wcslack-tag">{first_name}</span> | <span class="wcslack-tag">{last_name}</span> | <span class="wcslack-tag">{order_items}</span> | <span class="wcslack-tag">{order_total}</span> | <span class="wcslack-tag">{order_link}</span>',
				'desc_tip'          => false,
				'class'             => 'wcslack-order-new-field wcslack-order-new-message',
			),

			// Back Orders
			'notif-back-order' => array(
				'title'             => __( 'Back Order', 'woocommerce-slack' ),
				'type'              => 'checkbox',
				'label'             => __( 'Back Order', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => true,
				'description'       => __( 'Every time a new back order is made', 'woocommerce-slack' ),
			),
			'notif-back-order-channel' => array(
				'type'              => 'multiselect',
				'description'       => $which_room_desc,
				'options'           => $available_channels,
				'class'             => 'wcslack-back-order-field wc-enhanced-select',
				'default'           => 'select',
			),
			'notif-back-order-emoji' => array(
				'type'              => 'text',
				'placeholder'       => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
				'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
				'default'           => '',
				'class'             => 'wcslack-back-order-field',
			),
			'notif-back-order-message' => array(
				'type'              => 'textarea',
				'default'           => '*Backorder:* Product (#{product_id} {product_name}) is on backorder - [[{product_link}|View Product]]',
				'placeholder'       => '*Backorder:* Product (#{product_id} {product_name}) is on backorder - [[{product_link}|View Product]]',
				'description'       => '<strong>Optional:</strong> You can write a custom notification message. <a href="https://docs.woocommerce.com/document/woocommerce-slack/#custom-notifications">Valid Slack Markup Allowed</a>.<br />
							<strong>Template Tags:</strong> <span class="wcslack-tag">{product_name}</span> | <span class="wcslack-tag">{product_id}</span> | <span class="wcslack-tag">{product_link}</span>',
				'desc_tip'          => false,
				'class'             => 'wcslack-back-order-field wcslack-back-order-message',
			),

			// Low Stock
			'notif-low-stock' => array(
				'title'             => __( 'Low on Stock', 'woocommerce-slack' ),
				'type'              => 'checkbox',
				'label'             => __( 'Low on Stock', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => true,
				'description'       => __( 'Every time an item is low in stock (amount is defined in your inventory settings)', 'woocommerce-slack' ),
			),
			'notif-low-stock-channel' => array(
				'type'              => 'multiselect',
				'description'       => $which_room_desc,
				'options'           => $available_channels,
				'class'             => 'wcslack-low-stock-field wc-enhanced-select',
				'default'           => 'select',
			),
			'notif-low-stock-emoji' => array(
				'type'              => 'text',
				'placeholder'       => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
				'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
				'default'           => '',
				'class'             => 'wcslack-low-stock-field',
			),
			'notif-low-stock-message' => array(
				'type'              => 'textarea',
				'default'           => '*Low on Stock:* Product (#{product_id} {product_name}) is low on stock - [[{product_link}|View Product]]',
				'placeholder'       => '*Low on Stock:* Product (#{product_id} {product_name}) is low on stock - [[{product_link}|View Product]]',
				'description'       => '<strong>Optional:</strong> You can write a custom notification message. <a href="https://docs.woocommerce.com/document/woocommerce-slack/#custom-notifications">Valid Slack Markup Allowed</a>.<br />
							<strong>Template Tags:</strong> <span class="wcslack-tag">{product_name}</span> | <span class="wcslack-tag">{product_id}</span> | <span class="wcslack-tag">{product_link}</span>',
				'desc_tip'          => false,
				'class'             => 'wcslack-low-stock-field wcslack-low-stock-message',
			),

			// Out of Stock
			'notif-out-stock' => array(
				'title'             => __( 'Out of Stock', 'woocommerce-slack' ),
				'type'              => 'checkbox',
				'label'             => __( 'Out of Stock', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => true,
				'description'       => __( 'Every time an item is out of stock', 'woocommerce-slack' ),
			),
			'notif-out-stock-channel' => array(
				'type'              => 'multiselect',
				'description'       => $which_room_desc,
				'options'           => $available_channels,
				'class'             => 'wcslack-out-stock-field wc-enhanced-select',
				'default'           => 'select',
			),
			'notif-out-stock-emoji' => array(
				'type'              => 'text',
				'placeholder'       => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
				'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
				'default'           => '',
				'class'             => 'wcslack-out-stock-field',
			),
			'notif-out-stock-message' => array(
				'type'              => 'textarea',
				'default'           => '*Out of Stock:* Product (#{product_id} {product_name}) is out of stock - [[{product_link}|View Product]]',
				'placeholder'       => '*Out of Stock:* Product (#{product_id} {product_name}) is out of stock - [[{product_link}|View Product]]',
				'description'       => '<strong>Optional:</strong> You can write a custom notification message. <a href="https://docs.woocommerce.com/document/woocommerce-slack/#custom-notifications">Valid Slack Markup Allowed</a>.<br />
							<strong>Template Tags:</strong> <span class="wcslack-tag">{product_name}</span> | <span class="wcslack-tag">{product_id}</span> | <span class="wcslack-tag">{product_link}</span>',
				'desc_tip'          => false,
				'class'             => 'wcslack-out-stock-field wcslack-out-stock-message',
			),

			// New Review
			'notif-new-review' => array(
				'title'             => __( 'Reviews', 'woocommerce-slack' ),
				'type'              => 'checkbox',
				'label'             => __( 'New Review', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => true,
				'description'       => __( 'Every time a review is made', 'woocommerce-slack' ),
			),
			'notif-new-review-channel' => array(
				'type'              => 'multiselect',
				'description'       => $which_room_desc,
				'options'           => $available_channels,
				'class'             => 'wcslack-review-new-field wc-enhanced-select',
				'default'           => 'select',
			),
			'notif-new-review-emoji' => array(
				'type'              => 'text',
				'placeholder'       => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
				'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
				'default'           => '',
				'class'             => 'wcslack-review-new-field',
			),
			'notif-new-review-extend' => array(
				'type'              => 'checkbox',
				'label'             => __( 'Extended Notification', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => false,
				'description'       => __( 'Would you like to send extra details with this notification?', 'woocommerce-slack' ),
				'class'             => 'wcslack-review-new-field',
			),
			'notif-new-review-message' => array(
				'type'              => 'textarea',
				'default'           => '*New Review:* {reviewer_name} on {product_name} {review_rating} - [[{review_link}|View Review]]',
				'placeholder'       => '*New Review:* {reviewer_name} on {product_name} {review_rating} - [[{review_link}|View Review]]',
				'description'       => '<strong>Optional:</strong> You can write a custom notification message. <a href="https://docs.woocommerce.com/document/woocommerce-slack/#custom-notifications">Valid Slack Markup Allowed</a>.<br />
							<strong>Template Tags:</strong> <span class="wcslack-tag">{reviewer_name}</span> | <span class="wcslack-tag">{product_name}</span> | <span class="wcslack-tag">{review_content}</span> | <span class="wcslack-tag">{review_rating}</span> | <span class="wcslack-tag">{review_link}</span>',
				'desc_tip'          => false,
				'class'             => 'wcslack-review-new-field wcslack-review-new-message',
			),

			// New Customer
			'notif-new-customer' => array(
				'title'             => __( 'Customers', 'woocommerce-slack' ),
				'type'              => 'checkbox',
				'label'             => __( 'New Customer', 'woocommerce-slack' ),
				'default'           => 'no',
				'desc_tip'          => true,
				'description'       => __( 'Every time a new customer is registered / created', 'woocommerce-slack' ),
			),
			'notif-new-customer-channel' => array(
				'type'              => 'multiselect',
				'description'       => $which_room_desc,
				'options'           => $available_channels,
				'class'             => 'wcslack-customer-new-field wc-enhanced-select',
				'default'           => 'select',
			),
			'notif-new-customer-emoji' => array(
				'type'              => 'text',
				'placeholder'       => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
				'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
				'default'           => '',
				'class'             => 'wcslack-customer-new-field',
			),
			'notif-new-customer-message' => array(
				'type'              => 'textarea',
				'default'           => '*New Customer:* {customer_name} Registered - [[{customer_link}|View Customer]]',
				'placeholder'       => '*New Customer:* {customer_name} Registered - [[{customer_link}|View Customer]]',
				'description'       => '<strong>Optional:</strong> You can write a custom notification message. <a href="https://docs.woocommerce.com/document/woocommerce-slack/#custom-notifications">Valid Slack Markup Allowed</a>.<br />
							<strong>Template Tags:</strong> <span class="wcslack-tag">{customer_name}</span> | <span class="wcslack-tag">{customer_link}</span>',
				'desc_tip'          => false,
				'class'             => 'wcslack-customer-new-field wcslack-customer-new-message',
			),

			// Test Notification Button
			'test_button' => array(
				'type'        => 'test_button',
			),

			// Test Notification Button
			'reload_channels' => array(
				'type'        => 'reload_channels',
			),
		);

	}

	/**
	 * Validate the Slack Authorization field.
	 *
	 * @param mixed $key Current Key.
	 *
	 * @return string
	 */
	public function validate_slack_authorization_field( $key ) {
		return '';
	}

	/**
	 * Generate the Slack Authorization field.
	 *
	 * @param  mixed $key
	 * @param  array $data
	 *
	 * @return string
	 */
	public function generate_slack_authorization_html( $key, $data ) {
		$options       = $this->plugin_id . $this->id . '_';
		$client_id     = isset( $_POST[ $options . 'client_id' ] ) ? sanitize_text_field( $_POST[ $options . 'client_id' ] ) : $this->client_id;
		$client_secret = isset( $_POST[ $options . 'client_secret' ] ) ? sanitize_text_field( $_POST[ $options . 'client_secret' ] ) : $this->client_secret;
		$access_token  = $this->get_client()->getAccessToken();
		$connection    = $this->test_client_connection();

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo wp_kses_post( $data['title'] ); ?>
			</th>
			<td class="forminp">
				<input type="hidden" name="wc_slack_redirect" id="wc_slack_redirect">
				<?php if ( ! $client_id || ! $client_secret ) : ?>
					<input type="hidden" name="wc_slack_authenticated" id="wc_slack_authenticated" value="0">
					<p style="color:red;"><?php esc_html_e( 'Please fill out all required fields from above.', 'woocommerce-slack' ); ?></p>
				<?php elseif ( $access_token && $connection ) : ?>
					<input type="hidden" name="wc_slack_authenticated" id="wc_slack_authenticated" value="1">
					<p><?php esc_html_e( 'Successfully authenticated.', 'woocommerce-slack' ); ?></p>
					<p class="submit"><a class="button button-primary" href="<?php echo esc_url( add_query_arg( array( 'logout' => 'true' ), $this->redirect_uri ) ); ?>"><?php esc_html_e( 'Disconnect', 'woocommerce-slack' ); ?></a></p>
				<?php elseif ( $access_token ) : ?>
					<input type="hidden" name="wc_slack_authenticated" id="wc_slack_authenticated" value="0">
					<p><?php esc_html_e( 'Access token is not valid. Please re-connect.', 'woocommerce-slack' ); ?></p>
					<p class="submit"><a class="button button-primary wc-slack-connect"><?php esc_html_e( 'Connect with Slack', 'woocommerce-slack' ); ?></a></p>
				<?php else : ?>
					<input type="hidden" name="wc_slack_authenticated" id="wc_slack_authenticated" value="0">
					<p class="submit"><a class="button button-primary wc-slack-connect"><?php esc_html_e( 'Connect with Slack', 'woocommerce-slack' ); ?></a></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Admin Options.
	 */
	public function admin_options() {
		$slack_app       = '<a target=_blank href="https://api.slack.com/apps">' . __( 'Slack application', 'woocommerce-slack' ) . '</a>';
		$create_app_docs = '<a target=_blank href="https://docs.woocommerce.com/document/woocommerce-slack">' . __( 'WooCommerce Slack documentation.', 'woocommerce-slack' ) . '</a>';

		/* translators: 0: Slack apps URL 1: WP redirect uri 2: create app docs url */
		echo '<p>' . sprintf( __( 'To begin syncing with Slack, create a new %s. The callback URL to be added is <strong style="background-color:#ddd;">&nbsp;%s&nbsp;</strong>. Full details are available in the %s', 'woocommerce-slack' ), $slack_app, $this->redirect_uri, $create_app_docs ) . '</p>';

		/* translators: 0: Slack apps URL */
		echo '<p>' . sprintf( __( 'You may also copy the Client ID and Client Secret of an existing %s into the fields below.' ), $slack_app ) . '</p>';

		echo '<table class="form-table">';
			$this->generate_settings_html();
		echo '</table>';

		echo '<div><input type="hidden" name="section" value="' . $this->id . '" /></div>';
	}

	/**
	 * OAuth Logout.
	 *
	 * @return bool
	 */
	protected function oauth_logout() {
		if ( 'yes' === $this->debug ) {
			$this->log( 'Leaving the Slack app...' );
		}

		$client = $this->get_client();

		try {
			$client->revokeToken();
		} catch ( Exception $e ) {
			$this->log( $e->getMessage() );
		}

		delete_option( 'wc_slack_access_token' );

		if ( 'yes' === $this->debug ) {
			$this->log( 'Left the Slack App. successfully' );
		}

		return true;
	}

	/**
	 * Process the oauth redirect.
	 *
	 * @return void
	 */
	public function oauth_redirect() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Permission denied!', 'woocommerce-slack' ) );
		}

		$redirect_args = array(
			'page'      => 'wc-settings',
			'tab'       => 'integration',
			'section'   => 'wcslack',
		);

		// OAuth.
		if ( isset( $_REQUEST['code'] ) ) {
			$code         = sanitize_text_field( $_REQUEST['code'] );
			$client       = $this->get_client();

			try {
				$access_token = $client->fetchAccessTokenWithAuthCode( $code );
			} catch ( Exception $e ) {
				$this->log( $e->getMessage() );
			}

			if ( empty( $access_token ) ) {
				$redirect_args['wc_slack_oauth'] = 'fail';

				wp_redirect( add_query_arg( $redirect_args, admin_url( '/admin.php?' ) ), 301 );
				exit;
			}

			// Set access token and refresh channels on achieving a successful connection.
			update_option( 'wc_slack_access_token', $access_token );
			$this->update_channels();
			$redirect_args['wc_slack_oauth'] = 'success';

			wp_safe_redirect( add_query_arg( $redirect_args, admin_url( '/admin.php?' ) ), 301 );
			exit;
		}

		if ( isset( $_REQUEST['error'] ) ) {
			$redirect_args['wc_slack_oauth'] = 'fail';

			wp_redirect( add_query_arg( $redirect_args, admin_url( '/admin.php?' ) ), 301 );
			exit;
		}

		// Logout.
		if ( isset( $_REQUEST['logout'] ) ) {
			$logout                           = $this->oauth_logout();
			$redirect_args['wc_slack_logout'] = ( $logout ) ? 'success' : 'fail';

			wp_redirect( add_query_arg( $redirect_args, admin_url( '/admin.php?' ) ), 301 );
			exit;
		}

		wp_die( __( 'Invalid request!', 'woocommerce-slack' ) );
	}

	/**
	 * Display admin screen notices.
	 */
	public function admin_notices() {
		$screen = get_current_screen();

		if ( self::$notices_triggered ) {
			return;
		}

		self::$notices_triggered = true;

		delete_transient( 'wcslack_test_connection' );

		if ( 'woocommerce_page_wc-settings' == $screen->id && isset( $_GET['wc_slack_oauth'] ) ) {
			if ( 'success' == $_GET['wc_slack_oauth'] ) {
				echo '<div class="updated fade"><p><strong>' . __( 'Slack', 'woocommerce-slack' ) . '</strong> ' . __( 'Account connected successfully!', 'woocommerce-slack' ) . '</p></div>';
			} else {
				echo '<div class="error fade"><p><strong>' . __( 'Slack', 'woocommerce-slack' ) . '</strong> ' . __( 'Failed to connect to your account, please try again, if the problem persists, turn on Debug Log option and see what is happening.', 'woocommerce-slack' ) . '</p></div>';
			}
		}

		if ( 'woocommerce_page_wc-settings' == $screen->id && isset( $_GET['wc_slack_logout'] ) ) {
			if ( 'success' == $_GET['wc_slack_logout'] ) {
				echo '<div class="updated fade"><p><strong>' . __( 'Slack', 'woocommerce-slack' ) . '</strong> ' . __( 'Account disconnected successfully!', 'woocommerce-slack' ) . '</p></div>';
			} else {
				echo '<div class="error fade"><p><strong>' . __( 'Slack', 'woocommerce-slack' ) . '</strong> ' . __( 'Failed to disconnect to your account, please try again, if the problem persists, turn on Debug Log option and see what is happening.', 'woocommerce-slack' ) . '</p></div>';
			}
		}
	}

	/**
	 * Check if Slack settings are supplied and we're authenticated.
	 *
	 * @return bool
	 */
	public function is_integration_active() {
		$access_token = get_option( 'wc_slack_access_token' );

		return ! empty( $access_token ) && ! empty( $this->client_id ) && ! empty( $this->client_secret );
	}

	/**
	 * Scripts / CSS Needed
	 */

	public function scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Let's get some jQuery going
		wp_enqueue_script( 'jquery' );

		// Register Scripts / Styles
		wp_register_script( 'wcslack-admin-js', plugins_url( 'assets/js/wcslack-admin' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery') );

		// Enqueue Scripts / Styles
		wp_enqueue_script( 'wcslack-admin-js' );

	}

	/**
	 * List all channels
	 */

	public function test_init() {

		$wcslack_api = new WC_Slack_API();

		$wrapper     = $this->wrapper();

		$api_key     = $wrapper['api_key'];
		$channel     = $wrapper['channel'];
		$emoji       = $wrapper['emoji'];

		if ( isset( $_GET['wcslack_test'] ) && ( $_GET['wcslack_test'] == 1 ) ) {

			$prefix  = __( 'Test Notification: ', 'woocommerce-slack');
			$message = '*' . $prefix . '* ' . sprintf( __( 'This is a test notification from %s', 'woocommerce-slack' ), '<' . admin_url() . '|' . get_bloginfo( 'name' ) . '>' );

			$fields = array(
				0 => array(
					'title'   => __( 'Extended Notifications', 'woocommerce-slack' ),
					'value'   => __( 'Some notifications have the option to turn on extended notifications. They look this and will often contain extra information about the specific notification! Neat, huh?', 'woocommerce-slack' ),
					'short'   => false,
				),
				1 => array(
					'title'   => __( 'The Left Colored Bar', 'woocommerce-slack' ),
					'value'   => sprintf( __( 'It\'s possible to change the color of that bar on the left (green by default), using a simple filter. For more on this please see the %s.', 'woocommerce-slack' ), '<https://docs.woocommerce.com/document/woocommerce-slack/|' . __( 'WooCommerce Slack Documentation', 'woocommerce-slack' ) . '>' ),
					'short'   => false,
				),
				2 => array(
					'title'   => __( 'Support', 'woocommerce-slack' ),
					'value'   => sprintf( __( 'If you need help, please don\'t be afraid to ask %s.', 'woocommerce-slack' ), '<https://woocommerce.com/my-account/tickets/|' . __( 'WooCommerce Support', 'woocommerce-slack' ) . '>' ),
					'short'   => true,
				),
				3 => array(
					'title'   => __( 'Back to Work!', 'woocommerce-slack' ),
					'value'   => __( 'Now, get back to work over at', 'woocommerce-slack' ) . ' <' . admin_url() . '|' . get_bloginfo( 'name' ) . '>!',
					'short'   => true,
				),
			);

			$attachment = $wcslack_api->attachment( $fields );

			$wcslack_api->send_message( $api_key, $channel, $emoji, $message, $attachment );

			wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'wcslack_test' ), wp_get_referer() ) : admin_url() . 'admin.php?page=wc-settings&tab=integration&section=wcslack' );
			exit;
		}

		if ( isset( $_GET['wcslack_reload_channels'] ) && ( $_GET['wcslack_reload_channels'] == 1 ) ) {

			delete_transient( 'wcslack_all_channels' );

			wp_safe_redirect( admin_url() . 'admin.php?page=wc-settings&tab=integration&section=wcslack' );

		}

	}

	/**
	 * Test Button HTML
	 */

	public function generate_test_button_html() {

		ob_start();
		?>
		<tr valign="top" id="service_options">
			<th scope="row" class="titledesc"><?php _e( 'Send Test', 'woocommerce-slack' ); ?></th>
			<td>
				<p>
					<a href="<?php echo get_admin_url(); ?>admin.php?page=wc-settings&tab=integration&section=wcslack&wcslack_test=1" class="button" id="wcslack-test-button">
						<?php _e( 'Send Test Notification', 'woocommerce-slack' ); ?>
					</a>
				</p>
				<p>
					<em><?php _e( 'Save your settings first!', 'woocommerce-slack' ); ?></em>
				</p>
			</td>
		</tr>
		<?php
		return ob_get_clean();

	}

	/**
	 * Reload Channels Button HTML
	 */

	public function generate_reload_channels_html() {

		ob_start();
		?>
		<tr valign="top" id="service_options">
			<th scope="row" class="titledesc"><?php _e( 'Reload channels', 'woocommerce-slack' ); ?></th>
			<td>
				<p>
					<a href="<?php echo get_admin_url(); ?>admin.php?page=wc-settings&tab=integration&section=wcslack&wcslack_reload_channels=1" class="button" id="wcslack-reload-channel-button">
						<?php _e( 'Reload Available Channels', 'woocommerce-slack' ); ?>
					</a>
				</p>
				<p>
					<em><?php _e( 'If you have added a new channel and it\'s not showing, this will refresh the list.', 'woocommerce-slack' ); ?></em>
				</p>
			</td>
		</tr>
		<?php
		return ob_get_clean();

	}

	/**
	 * Retrieve list of available channels.
	 *
	 * @return array
	 */
	public function available_channels() {
		$all_channels = get_transient( 'wcslack_all_channels' );

		if ( ! is_array( $all_channels ) ) {
			$this->update_channels();
			$all_channels = get_transient( 'wcslack_all_channels' );
		}

		return (array) $all_channels;
	}

	/**
	 * Slack was return 429 (rate limiting) while the channel transient was empty, causing this to run
	 * multiple times on every pageload.
	 */
	public function update_channels() {
		$wcslack_api = new WC_Slack_API();

		$wrapper     = $this->wrapper();
		$api_key     = $wrapper['api_key'];
		$connection  = $this->test_client_connection();

		$all_channels = (array) $wcslack_api->all_channels( $api_key );
		set_transient( 'wcslack_all_channels', $all_channels, DAY_IN_SECONDS );
	}

	/**
	 * Santize our settings
	 * @see process_admin_options()
	 */
	public function sanitize_settings( $settings ) {

		// We're just going to make the api key all upper case characters since that's how our imaginary API works
		if ( isset( $settings ) && isset( $settings['api_key'] ) ) {
			esc_html( $settings['api_key'] );
		}
		return $settings;

	}

	/**
	 * Validate the API key
	 * @see validate_settings_fields()
	 */
	public function validate_api_key_field( $key ) {

		$wcslack_api = new WC_Slack_API();

		// get the posted value
		$value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];

		if ( isset( $value ) && ( $wcslack_api->valid( $value ) != 1 ) ) {
			$this->errors[] = $key;
		}

		return $value;

	}

	/**
	 * Display errors by overriding the display_errors() method
	 * @see display_errors()
	 */
	public function display_errors( ) {

		// loop through each error and display it
		foreach ( $this->errors as $key => $value ) { ?>

			<div class="error">
				<p><?php _e( 'The API Key is invalid. Please double check it and try again!', 'woocommerce-slack' ); ?></p>
			</div>

		<?php }

	}
}
