<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce Slack Notifications Settings Class
 *
 * @package  WooCommerce Slack
 * @author   Bryce <bryce@bryce.se>
 * @since    1.1.0
 */

if ( ! class_exists( 'WC_Slack_Settings' ) ) {

	class WC_Slack_Settings extends WC_Integration {

		private static $notices_triggered = false;

		/**
		 * Init and hook in the integration.
		 */
		public function __construct() {

			$this->id                 = 'wcslack';
			$this->method_title       = __( 'Slack', 'woocommerce-slack' );
			$this->method_description = __( 'Configure the settings below to send notifications to your Slack channel based on WooCommerce events.', 'woocommerce-slack' );

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			/**
			 * Define user set variables.
			 **/

			// General
			$this->api_key              	= $this->get_option( 'api_key' );
			$this->channel                  = $this->get_option( 'channel' );
			$this->emoji                	= $this->get_option( 'emoji' );
			$this->name                 	= $this->get_option( 'name' );
			$this->debug                	= $this->get_option( 'debug' );

			// New Post
			$this->post_new             	= $this->get_option( 'notif-post-new' );
			$this->post_new_channel         = $this->get_option( 'notif-post-new-channel' );
			$this->post_new_emoji       	= $this->get_option( 'notif-post-new-emoji' );
			$this->post_new_extend		    = $this->get_option( 'notif-post-new-extend' );
			$this->post_new_message     	= $this->get_option( 'notif-post-new-message' );

			// New Orders
			$this->order_new            	= $this->get_option( 'notif-order-new' );
			$this->order_new_channel        = $this->get_option( 'notif-order-new-channel' );
			$this->order_new_emoji      	= $this->get_option( 'notif-order-new-emoji' );
			$this->order_new_free       	= $this->get_option( 'notif-order-new-free' );
			$this->order_new_extend      	= $this->get_option( 'notif-order-new-extend' );
			$this->order_new_message    	= $this->get_option( 'notif-order-new-message' );

			// Back Orders
			$this->back_order           	= $this->get_option( 'notif-back-order' );
			$this->back_order_channel       = $this->get_option( 'notif-back-order-channel' );
			$this->back_order_emoji     	= $this->get_option( 'notif-back-order-emoji' );
			$this->back_order_message   	= $this->get_option( 'notif-back-order-message' );

			// Low on Stock
			$this->low_stock            	= $this->get_option( 'notif-low-stock' );
			$this->low_stock_channel        = $this->get_option( 'notif-low-stock-channel' );
			$this->low_stock_emoji      	= $this->get_option( 'notif-low-stock-emoji' );
			$this->low_stock_message    	= $this->get_option( 'notif-low-stock-message' );

			// Out of Stock
			$this->out_stock            	= $this->get_option( 'notif-out-stock' );
			$this->out_stock_channel        = $this->get_option( 'notif-out-stock-channel' );
			$this->out_stock_emoji      	= $this->get_option( 'notif-out-stock-emoji' );
			$this->out_stock_message    	= $this->get_option( 'notif-out-stock-message' );

			// New Review
			$this->new_review           	= $this->get_option( 'notif-new-review' );
			$this->new_review_channel       = $this->get_option( 'notif-new-review-channel' );
			$this->new_review_emoji     	= $this->get_option( 'notif-new-review-emoji' );
			$this->new_review_extend     	= $this->get_option( 'notif-new-review-extend' );
			$this->new_review_message   	= $this->get_option( 'notif-new-review-message' );

			// New Customer
			$this->new_customer         	= $this->get_option( 'notif-new-customer' );
			$this->new_customer_channel     = $this->get_option( 'notif-new-customer-channel' );
			$this->new_customer_emoji   	= $this->get_option( 'notif-new-customer-emoji' );
			$this->new_customer_message 	= $this->get_option( 'notif-new-customer-message' );

			// Actions.
			add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'init', array( $this, 'test_init' ), 10 );
			add_action( 'init', array( $this, 'test_clear_key' ), 10 );
			add_action( 'init', array( $this, 'scripts' ) );

			add_action( 'init', array( $this, 'available_channels' ) );

			// Filters.
			add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );

			// This constructor is called multiple times from WC core, let's keep track of it
			if ( ! self::$notices_triggered ) {
				add_action( 'admin_notices', array( $this, 'show_legacy_notice' ) );
				self::$notices_triggered = true;
			}

		}


		/**
		 * Wrapper containing all settings for easy access
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 * @return  array
		 */

		public function wrapper() {

			$wrapper = array(
				// General
				'api_key'             	=> $this->get_option( 'api_key' ),
				'channel'             	=> $this->get_option( 'channel' ),
				'emoji'               	=> $this->get_option( 'emoji' ),
				'name'               	=> $this->get_option( 'name' ),
				'debug'               	=> $this->get_option( 'debug' ),

				// New Post
				'post_new'            	=> $this->get_option( 'notif-post-new' ),
				'post_new_channel'    	=> $this->get_option( 'notif-post-new-channel' ),
				'post_new_emoji'      	=> $this->get_option( 'notif-post-new-emoji' ),
				'post_new_extend'		=> $this->get_option( 'notif-post-new-extend' ),
				'post_new_message'    	=> $this->get_option( 'notif-post-new-message' ),

				// New Orders
				'order_new'           	=> $this->get_option( 'notif-order-new' ),
				'order_new_channel'   	=> $this->get_option( 'notif-order-new-channel' ),
				'order_new_emoji'     	=> $this->get_option( 'notif-order-new-emoji' ),
				'order_new_free'      	=> $this->get_option( 'notif-order-new-free' ),
				'order_new_extend'     	=> $this->get_option( 'notif-order-new-extend' ),
				'order_new_message'   	=> $this->get_option( 'notif-order-new-message' ),

				// Back Orders
				'back_order'          	=> $this->get_option( 'notif-back-order' ),
				'back_order_channel'  	=> $this->get_option( 'notif-back-order-channel' ),
				'back_order_emoji'    	=> $this->get_option( 'notif-back-order-emoji' ),
				'back_order_message'  	=> $this->get_option( 'notif-back-order-message' ),

				// Low on Stock
				'low_stock'           	=> $this->get_option( 'notif-low-stock' ),
				'low_stock_channel'   	=> $this->get_option( 'notif-low-stock-channel' ),
				'low_stock_emoji'     	=> $this->get_option( 'notif-low-stock-emoji' ),
				'low_stock_message'   	=> $this->get_option( 'notif-low-stock-message' ),

				// Out of Stock
				'out_stock'           	=> $this->get_option( 'notif-out-stock' ),
				'out_stock_channel'   	=> $this->get_option( 'notif-out-stock-channel' ),
				'out_stock_emoji'     	=> $this->get_option( 'notif-out-stock-emoji' ),
				'out_stock_message'   	=> $this->get_option( 'notif-out-stock-message' ),

				// New Review
				'new_review'          	=> $this->get_option( 'notif-new-review' ),
				'new_review_channel'  	=> $this->get_option( 'notif-new-review-channel' ),
				'new_review_emoji'    	=> $this->get_option( 'notif-new-review-emoji' ),
				'new_review_extend'    	=> $this->get_option( 'notif-new-review-extend' ),
				'new_review_message'  	=> $this->get_option( 'notif-new-review-message' ),

				// New Customer
				'new_customer'        	=> $this->get_option( 'notif-new-customer' ),
				'new_customer_channel'	=> $this->get_option( 'notif-new-customer-channel' ),
				'new_customer_emoji'  	=> $this->get_option( 'notif-new-customer-emoji' ),
				'new_customer_message' 	=> $this->get_option( 'notif-new-customer-message' ),
			);

			return $wrapper;

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

			$this->form_fields = array(
				'api-title-slack' => array(
					'title'             => __( 'Authentication & Defaults', 'woocommerce-slack' ),
					'type'              => 'title',
					'description'       => __( 'Please authenticate your slack account here and choose the defaults for sending messages.', 'woocommerce-slack' ),
					'class'			  => 'wcslack-section-title-main',
				),
				'api_key' => array(
					'title'             => __( 'API Key', 'woocommerce-slack' ),
					'type'              => 'text',
					'description'       => sprintf( __( 'Go to your %s and copy the token provided. If there is no token yet, you\'ll need to press the "Create Token" button in the Authentication section. You may need to refresh after saving to see your rooms.', 'woocommerce-slack' ), '<a href="https://api.slack.com/custom-integrations/legacy-tokens" target="_blank"><strong>' . __( 'Slack Account\'s API / Token Settings', 'woocommerce-slack' ) . '</strong></a>' ),
					'default'           => '',
				),
				'channel' => array(
					'title'             => __( 'Default Channel(s)', 'woocommerce-slack' ),
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'description'       => __( 'Which channel or group do you want the notifications to be sent to by default?', 'woocommerce-slack' ),
					'options'           => $this->available_channels(),
				),
				'emoji' => array(
					'title'             => __( 'Default Emoji', 'woocommerce-slack' ),
					'type'              => 'text',
					'placeholder'		  => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
					'description'       => sprintf( __( 'Enter the code for an emoji you want to include with each message by default. You can find all codes on a site like %s or you can even upload your own icon as an emoji in %s', 'woocommerce-slack' ), '<a href="https://emoji.muan.co/" target="_blank">Emoji Searcher</a>', '<a href="https://slack.com/customize/emoji" target="_blank">Slack</a>!' ),
					'default'           => '',
				),
				'name' => array(
					'title'             => __( 'Sender Name', 'woocommerce-slack' ),
					'type'              => 'text',
					'placeholder'		  => get_bloginfo( 'name' ),
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
					'class'			  => 'wcslack-section-title',
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
					'options'           => $this->available_channels(),
					'class'             => 'wcslack-post-new-field wc-enhanced-select',
					'default'           => 'select',
				),
				'notif-post-new-emoji' => array(
					'type'              => 'text',
					'placeholder'		  => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
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
					'default'			  => '*New Post:* {post_title} by {post_author} - [[{post_link}|View Post]]',
					'placeholder'		  => '*New Post:* {post_title} by {post_author} - [[{post_link}|View Post]]',
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
					'options'           => $this->available_channels(),
					'class'             => 'wcslack-order-new-field wc-enhanced-select',
					'default'           => 'select',
				),
				'notif-order-new-emoji' => array(
					'type'              => 'text',
					'placeholder'		  => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
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
					'default'			  => '*New Order #{order_id}:* {first_name} {last_name} ordered {order_items} for {order_total} - [[{order_link}|View Order]]',
					'placeholder'		  => '*New Order #{order_id}:* {first_name} {last_name} ordered {order_items} for {order_total} - [[{order_link}|View Order]]',
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
					'options'           => $this->available_channels(),
					'class'             => 'wcslack-back-order-field wc-enhanced-select',
					'default'           => 'select',
				),
				'notif-back-order-emoji' => array(
					'type'              => 'text',
					'placeholder'		  => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
					'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
					'default'           => '',
					'class'             => 'wcslack-back-order-field',
				),
				'notif-back-order-message' => array(
					'type'              => 'textarea',
					'default'			  => '*Backorder:* Product (#{product_id} {product_name}) is on backorder - [[{product_link}|View Product]]',
					'placeholder'		  => '*Backorder:* Product (#{product_id} {product_name}) is on backorder - [[{product_link}|View Product]]',
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
					'options'           => $this->available_channels(),
					'class'             => 'wcslack-low-stock-field wc-enhanced-select',
					'default'           => 'select',
				),
				'notif-low-stock-emoji' => array(
					'type'              => 'text',
					'placeholder'		  => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
					'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
					'default'           => '',
					'class'             => 'wcslack-low-stock-field',
				),
				'notif-low-stock-message' => array(
					'type'              => 'textarea',
					'default'			  => '*Low on Stock:* Product (#{product_id} {product_name}) is low on stock - [[{product_link}|View Product]]',
					'placeholder'		  => '*Low on Stock:* Product (#{product_id} {product_name}) is low on stock - [[{product_link}|View Product]]',
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
					'options'           => $this->available_channels(),
					'class'             => 'wcslack-out-stock-field wc-enhanced-select',
					'default'           => 'select',
				),
				'notif-out-stock-emoji' => array(
					'type'              => 'text',
					'placeholder'		  => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
					'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
					'default'           => '',
					'class'             => 'wcslack-out-stock-field',
				),
				'notif-out-stock-message' => array(
					'type'              => 'textarea',
					'default'			  => '*Out of Stock:* Product (#{product_id} {product_name}) is out of stock - [[{product_link}|View Product]]',
					'placeholder'		  => '*Out of Stock:* Product (#{product_id} {product_name}) is out of stock - [[{product_link}|View Product]]',
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
					'options'           => $this->available_channels(),
					'class'             => 'wcslack-review-new-field wc-enhanced-select',
					'default'           => 'select',
				),
				'notif-new-review-emoji' => array(
					'type'              => 'text',
					'placeholder'		  => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
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
					'default'			  => '*New Review:* {reviewer_name} on {product_name} {review_rating} - [[{review_link}|View Review]]',
					'placeholder'		  => '*New Review:* {reviewer_name} on {product_name} {review_rating} - [[{review_link}|View Review]]',
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
					'options'           => $this->available_channels(),
					'class'             => 'wcslack-customer-new-field wc-enhanced-select',
					'default'           => 'select',
				),
				'notif-new-customer-emoji' => array(
					'type'              => 'text',
					'placeholder'		  => __( 'Emoji Code Here eg. :cop:', 'woocommerce-slack' ),
					'description'       => __( 'Enter the code for an emoji you want to include with this type of notification', 'woocommerce-slack' ),
					'default'           => '',
					'class'             => 'wcslack-customer-new-field',
				),
				'notif-new-customer-message' => array(
					'type'              => 'textarea',
					'default'			  => '*New Customer:* {customer_name} Registered - [[{customer_link}|View Customer]]',
					'placeholder'		  => '*New Customer:* {customer_name} Registered - [[{customer_link}|View Customer]]',
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
		 * Scripts / CSS Needed
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
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
		 * Check whether clear_key was initiated.
		 *
		 */
		public function test_clear_key() {

			if ( isset( $_GET['wcslack_clear_key'] ) && ( 1 == $_GET['wcslack_clear_key'] ) ) {
				$this->update_option( 'api_key', '' );

				$redirect_args = array(
					'page'      => 'wc-settings',
					'tab'       => 'integration',
					'section'   => 'wcslack',
				);

				wp_redirect( add_query_arg( $redirect_args, admin_url( '/admin.php?' ) ), 301 );
			}
		}


		/**
		 * List all channels
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.1.0
		 */

		public function test_init() {

			$wcslack_api 	= new WC_Slack_API();

			$wrapper 		= $this->wrapper();

			$api_key 		= $wrapper['api_key'];
			$channel  	    = $wrapper['channel'];
			$emoji   		= $wrapper['emoji'];

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
						'title'	  => __( 'Support', 'woocommerce-slack' ),
						'value'	  => sprintf( __( 'If you need help, please don\'t be afraid to ask %s.', 'woocommerce-slack' ), '<https://woocommerce.com/my-account/tickets/|' . __( 'WooCommerce Support', 'woocommerce-slack' ) . '>' ),
						'short'	  => true,
					),
					3 => array(
						'title'	  => __( 'Back to Work!', 'woocommerce-slack' ),
						'value'	  => __( 'Now, get back to work over at', 'woocommerce-slack' ) . ' <' . admin_url() . '|' . get_bloginfo( 'name' ) . '>!',
						'short'	  => true,
					),
				);

				$attachment = $wcslack_api->attachment( $fields );

				return $wcslack_api->send_message( $api_key, $channel, $emoji, $message, $attachment );

				wp_safe_redirect( get_admin_url() . 'admin.php?page=wc-settings&tab=integration&section=wcslack' );

			}

			if ( isset( $_GET['wcslack_reload_channels'] ) && ( $_GET['wcslack_reload_channels'] == 1 ) ) {

				delete_transient( 'wcslack_all_channels' );

				wp_safe_redirect( get_admin_url() . 'admin.php?page=wc-settings&tab=integration&section=wcslack' );

			}

		}


		/**
		 * Test Button HTML
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
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
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
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
		 * Show available channels
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function available_channels() {

			$wcslack_api 	= new WC_Slack_API();

			$wrapper 		= $this->wrapper();
			$api_key 		= $wrapper['api_key'];

			if ( $api_key ) {
				$all_channels = get_transient( 'wcslack_all_channels' ) ?: array();

				if ( empty( $all_channels ) ) {
					$all_channels = (array) $wcslack_api->all_channels( $api_key );
					set_transient( 'wcslack_all_channels', $all_channels, 86400 );
				}

				return $all_channels;

			} else {

				return array( 'Reload Page to See Channels' );

			}

		}


		/**
		 * Processes and saves options.
		 * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
		 *
		 * Overload this method to directly redirect users to the new integration when they empty the API key.
		 *
		 * @return bool was anything saved?
		 */
		public function process_admin_options() {
			parent::process_admin_options();

			$updated_api_key = $this->get_option( 'api_key' );

			if ( empty( $updated_api_key ) ) {

				$redirect_args = array(
					'page'      => 'wc-settings',
					'tab'       => 'integration',
					'section'   => 'wcslack',
				);

				wp_redirect( add_query_arg( $redirect_args, admin_url( '/admin.php?' ) ), 301 );
			}

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

		/**
		 * Display legacy notices.
		 */
		public function show_legacy_notice() {
			$redirect_args = array(
				'page'      => 'wc-settings',
				'tab'       => 'integration',
				'section'   => 'wcslack',
				'wcslack_clear_key' => 1,
			);

			$redirect_url = add_query_arg( $redirect_args, admin_url( '/admin.php?' ) );

			$message = __( 'We noticed you\'re using Legacy API authentication for slack. Please clear your legacy API key! On the next screen, you\'ll enter your Slack app\'s Client ID and Client Secret.', 'woocommerce-slack' );
			$message .= ' <a target=_blank href="' . esc_url( $redirect_url ) . '">' . __( 'Clear Key', 'woocommerce-slack' ) . '</a>';

			echo '<div class="error fade"><p>' . $message . '</p></div>' . "\n";
		}

	}

}
