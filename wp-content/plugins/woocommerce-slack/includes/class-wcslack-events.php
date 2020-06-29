<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Slack_Events Class
 *
 * @package  WooCommerce Slack
 * @author   Bryce <bryce@bryce.se>
 * @since    1.0.0
 */

if ( ! class_exists( 'WC_Slack_Events' ) ) {

	class WC_Slack_Events {

		protected static $instance = null;

		public function __construct() {

			$wrapper = $this->wrapper();

			// Event for post being published
			if ( $wrapper['post_new'] == 'yes' ) {
				add_action( 'transition_post_status', array( $this, 'post_new' ), 10, 3 );
			}

			// Event for new order made
			if ( $wrapper['order_new'] == 'yes' ) {
				if ( version_compare( WC_VERSION, '3.7.0', '<' ) ) {
					add_action( 'woocommerce_checkout_order_processed', array( $this, 'new_order' ), 10, 2 );
				} else {
					add_action( 'woocommerce_new_order', array( $this, 'new_order' ), 10, 2 );
				}
			}

			// Event for back order made
			if ( $wrapper['back_order'] == 'yes' ) {
				add_action( 'woocommerce_product_on_backorder', array( $this, 'back_order' ), 10, 1 );
			}

			// Event for low on stock
			if ( $wrapper['low_stock'] == 'yes' ) {
				add_action( 'woocommerce_low_stock', array( $this, 'low_stock' ), 10, 1 );
			}

			// Event for out of stock
			if ( $wrapper['out_stock'] == 'yes' ) {
				add_action( 'woocommerce_no_stock', array( $this, 'out_stock' ), 10, 1 );
			}

			// Event for new review
			if ( $wrapper['new_review'] == 'yes' ) {
				add_action( 'comment_post', array( $this, 'new_review' ), 10, 1 );
			}

			// Event for customer created
			if ( $wrapper['new_customer'] == 'yes' ) {
				add_action( 'woocommerce_created_customer', array( $this, 'new_customer' ), 10, 3 );
			}

		}

		/**
		 * Start the Class when called
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}


		/**
		 * New Post Published
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function post_new( $new_status, $old_status, $post ) {

			$wcslack_api = new WC_Slack_API();

			$wrapper = $this->wrapper();

			$api_key = $wrapper['api_key'];

			$channel = $this->get_channel( 'post_new' );
			$emoji = $this->get_emoji( 'post_new' );

			// By default only send on post and page publishing
			$allowed_post_types = apply_filters( 'wcslack_post_publish_types', array( 'post', 'page' ) );

			// Only do this when a post transitions to being published
			if ( in_array( $post->post_type, $allowed_post_types ) && $new_status == 'publish' && $old_status != 'publish' ) {

				// Post Prefix
				$prefix = apply_filters( 'wcslack_new_post_prefix', __( 'New Post:', 'woocommerce-slack' ) );

				// Post Author Name
				$author_data = get_userdata( $post->post_author );
				$author_name = apply_filters( 'wcslack_new_post_author', esc_attr( $author_data->display_name ) );

				/* Post Title
				 * - we use get_post_field to stop automatic get_the_title encoding
				 */
				$title = apply_filters( 'wcslack_new_post_title', get_post_field( 'post_title', $post->ID ) );

				/* Post Content
				 * - we use get_post_field as get_the_content won't work here (we need to feed the post ID)
				 */
				$post_content = apply_filters( 'wcslack_new_post_content', get_post_field( 'post_content', $post->ID ) );

				// Post URL
				$url = apply_filters( 'wcslack_new_post_url', esc_url( get_permalink( $post->ID ) ) );

				// New Post Message
				if ( $wrapper['post_new_message'] ) {
					$message_raw = $wrapper['post_new_message'];
					$tags = array( '{post_title}', '{post_content}', '{post_author}', '{post_link}', '[[', ']]' );
					$replace = array( $title, $post_content, $author_name, $url, '<', '>' );
					$message = str_replace( $tags, $replace, $message_raw );
				} else {
					$message = '*' . $prefix . '* ' . $title . __( ' by ', 'woocommerce-slack' ) . $author_name;
					$message .= ' - <'. $url . '|' . __( 'View Post', 'woocommerce-slack' ) . '>';
				}

				// Filter for the Message
				$message = apply_filters( 'wcslack_new_post_message', $message );

				// Attachment
				$fields = apply_filters( 'wcslack_new_post_attachment', array(
					0 => array(
						'title'	=> __( 'Post', 'woocommerce-slack' ),
						'value'	=> '<' . $url . '|' . $title . '>',
						'short'	=> true,
					),
					1 => array(
						'title'	=> __( 'Author', 'woocommerce-slack' ),
						'value'	=> $author_name,
						'short'	=> true,
					),
					2 => array(
						'title'	=> __( 'Post Content', 'woocommerce-slack' ),
						'value'	=> $post_content,
						'short'	=> false,
					),
				) );

				// Only give $attachment content if the setting for 'extended notification' is on
				if ( $wrapper['post_new_extend'] == 'yes' ) {
					$attachment = $wcslack_api->attachment( $fields );
				} else {
					$attachment = '';
				}

				$this->log( $message, $channel, $emoji );

				return $wcslack_api->send_message( $api_key, $channel, $emoji, $message, $attachment );

			}

		}


		/**
		 * New Order Made
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function new_order( $order_id, $maybe_order = null ) {

			// Set up Settings Wrapper
			$wcslack_api = new WC_Slack_API();
			$wrapper = $this->wrapper();

			// Settings
			$api_key = $wrapper['api_key'];
			$free_order = $wrapper['order_new_free'];

			// Channel & Emoji
			$channel = $this->get_channel( 'order_new' );
			$emoji = $this->get_emoji( 'order_new' );

			// Currency Symbol
			$currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );

			// Order Variable
			$order = is_a( $maybe_order, 'WC_Order' ) ? $maybe_order : new WC_Order( $order_id );

			// Order Items
			$order_items = $this->prepare_order_item_titles( $order->get_items() );

			// Order Total
			$order_total = $currency_symbol . $order->get_total();

			// Order Attachment Line Items
			$order_line_items = $this->get_order_line( $order_id );

			// Order Attachment Shipping
			$order_shipping = $this->get_order_shipping( $order_id );

			// Order Attachment Total
			$order_attach_total = $this->get_order_total( $order_id );

			// Order URL @TODO Check this is valid with Sequential Order Numbers Pro
			$url = admin_url( 'post.php?post=' . $order_id . '&action=edit' );

			// Send notifications if order total is greater than 0 or if free order notification is enabled
			if ( 0 < $order->get_total() || ( $free_order == 'yes' ) ) {

				// New Order Prefix
				$prefix = apply_filters( 'wcslack_new_order_prefix', sprintf( __( 'New Order #%d:', 'woocommerce-slack' ), $order_id ) );

				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					$first_name = $order->billing_first_name;
					$last_name = $order->billing_last_name;
				} else {
					$first_name = $order->get_billing_first_name();
					$last_name = $order->get_billing_last_name();
				}

				// New Order Message
				if ( $wrapper['order_new_message'] ) {
					$message_raw = $wrapper['order_new_message'];
					$tags = array( '{order_id}', '{first_name}', '{last_name}', '{order_items}', '{order_total}', '{order_link}', '[[', ']]' );
					$replace = array( $order->get_order_number(), $first_name, $last_name, $order_items, $order_total, $url, '<', '>' );
					$message = str_replace( $tags, $replace, $message_raw );
				} else {
					$message = '*' . $prefix . '* ' . $first_name . ' ' . $last_name . __( ' ordered ', 'woocommerce-slack' );
					$message .= $order_items . __( ' for ', 'woocommerce-slack' ) . $order_total;
					$message .= ' - <' . $url . '|' . __( 'View Order', 'woocommerce-slack' ) . '>';
				}

				// Filter for the Message
				$message = apply_filters( 'wcslack_new_order_message', $message );

				// Attachment
				$fields = apply_filters( 'wcslack_new_order_attachment', array(
					0 => array(
						'title'	=> __( 'Order', 'woocommerce-slack' ),
						'value'	=> '<' . $url . '|#' . $order_id . '>',
						'short'	=> true,
					),
					1 => array(
						'title'	=> __( 'Customer', 'woocommerce-slack' ),
						'value'	=> $first_name . ' ' . $last_name,
						'short'	=> true,
					),
					2 => array(
						'title'	=> __( 'Order Items', 'woocommerce-slack' ),
						'short'	=> false,
					),
				) );

				$fields = array_merge(
					(array) $fields,
					(array) $order_line_items,
					(array) $order_shipping,
					(array) $order_attach_total
				);

				// Only give $attachment content if the setting for 'extended notification' is on
				if ( $wrapper['order_new_extend'] == 'yes' ) {
					$attachment = $wcslack_api->attachment( $fields );
				} else {
					$attachment = '';
				}

				$this->log( $message, $channel, $emoji );

				return $wcslack_api->send_message( $api_key, $channel, $emoji, $message, $attachment );

			}

		}


		/**
		 * Back Order Made
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function back_order( $args ) {

			global $woocommerce;

			$wcslack_api = new WC_Slack_API();

			$wrapper = $this->wrapper();

			$api_key = $wrapper['api_key'];

			$channel = $this->get_channel( 'back_order' );
			$emoji = $this->get_emoji( 'back_order' );

			// Back Order Prefix
			$prefix = apply_filters( 'wcslack_back_order_prefix', __( 'Back Order:', 'woocommerce-slack' ) );

			// Product on Backorder
			$product = $args['product'];
			$product_backorder = sprintf( __( 'Product (#%d %s) is on backorder.', 'woocommerce-slack'), $product->get_id(), $product->get_title() );

			// Product URL
			$url = admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' );

			// Back Order Message
			if ( $wrapper['back_order_message'] ) {
				$message_raw = $wrapper['back_order_message'];
				$tags = array( '{product_name}', '{product_id}', '{product_link}', '[[', ']]' );
				$replace = array( $product->get_title(), $product->get_id(), $url, '<', '>' );
				$message = str_replace( $tags, $replace, $message_raw );
			} else {
				$message = '*' . $prefix . '* ' . $product_backorder;
				$message .= ' - <' . $url . '|' . __( 'View Product', 'woocommerce-slack' ) . '>';
			}

			// Filter for the Message
			$message = apply_filters( 'wcslack_back_order_message', $message );

			$this->log( $message, $channel, $emoji );

			return $wcslack_api->send_message( $api_key, $channel, $emoji, $message );

		}


		/**
		 * Item Low on Stock
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function low_stock( $product ) {

			global $woocommerce;

			$wcslack_api = new WC_Slack_API();

			$wrapper = $this->wrapper();

			$api_key = $wrapper['api_key'];

			$channel = $this->get_channel( 'low_stock' );
			$emoji = $this->get_emoji( 'low_stock' );

			// Low on Stock Prefix
			$prefix = apply_filters( 'wcslack_low_stock_prefix', __( 'Low on Stock:', 'woocommerce-slack' ) );

			// Product Low on Stock
			$product_low_stock = sprintf( __( 'Product (#%d %s) is low on stock.', 'woocommerce-slack'), $product->get_id(), $product->get_title() );

			// Product URL
			$url = admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' );

			// Low on Stock Message
			if ( $wrapper['low_stock_message'] ) {
				$message_raw = $wrapper['low_stock_message'];
				$tags = array( '{product_name}', '{product_id}', '{product_link}', '[[', ']]' );
				$replace = array( $product->get_title(), $product->get_id(), $url, '<', '>' );
				$message = str_replace( $tags, $replace, $message_raw );
			} else {
				$message = '*' . $prefix . '* ' . $product_low_stock;
				$message .= ' - <' . $url . '|' . __( 'View Product', 'woocommerce-slack' ) . '>';
			}

			// Filter for the Message
			$message = apply_filters( 'wcslack_low_stock_message', $message );

			$this->log( $message, $channel, $emoji );

			return $wcslack_api->send_message( $api_key, $channel, $emoji, $message );

		}


		/**
		 * Item Out of Stock
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function out_stock( $product ) {

			global $woocommerce;

			$wcslack_api = new WC_Slack_API();

			$wrapper = $this->wrapper();

			$api_key = $wrapper['api_key'];

			$channel = $this->get_channel( 'out_stock' );
			$emoji = $this->get_emoji( 'out_stock' );

			// Out of Stock Prefix
			$prefix = apply_filters( 'wcslack_out_stock_prefix', __( 'Out of Stock:', 'woocommerce-slack' ) );

			// Product Out of Stock
			$product_out_stock = sprintf( __( 'Product (#%d %s) is out of stock.', 'woocommerce-slack'), $product->get_id(), $product->get_title() );

			// Product URL
			$url = admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' );

			// Out of Stock Message
			if ( $wrapper['out_stock_message'] ) {
				$message_raw = $wrapper['out_stock_message'];
				$tags = array( '{product_name}', '{product_id}', '{product_link}', '[[', ']]' );
				$replace = array( $product->get_title(), $product->get_id(), $url, '<', '>' );
				$message = str_replace( $tags, $replace, $message_raw );
			} else {
				$message = '*' . $prefix . '* ' . $product_out_stock;
				$message .= ' - <' . $url . '|' . __( 'View Product', 'woocommerce-slack' ) . '>';
			}

			// Filter for the Message
			$message = apply_filters( 'wcslack_out_stock_message', $message );

			$this->log( $message, $channel, $emoji );

			return $wcslack_api->send_message( $api_key, $channel, $emoji, $message );

		}


		/**
		 * New Review on Product
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function new_review( $comment_id ) {

			global $woocommerce;

			$wcslack_api = new WC_Slack_API();

			$wrapper = $this->wrapper();

			$api_key = $wrapper['api_key'];

			$channel = $this->get_channel( 'new_review' );
			$emoji = $this->get_emoji( 'new_review' );

			// Get Comment Data bsed on the ID
			$comment_data = get_comment( $comment_id );

			// We really don't want to send notifications about SPAM
			if ( $comment_data->comment_approved == 'spam' ) {
				return;
			}

			// Get WP_Post Class based on the review
			$product_data = get_post( $comment_data->comment_post_ID );

			// We only want comments that are on products, ie. reviews
			if ( $product_data->post_type != 'product' ) {
				return;
			}

			// Reviewer Name
			$reviewer = apply_filters( 'wcslack_new_review_reviewer_name', $comment_data->comment_author );

			// Product Name
			$product_name = apply_filters( 'wcslack_new_review_product_name', $product_data->post_title );

			// Review Rating
			$rating = get_comment_meta( $comment_id, 'rating', true );
			$rating_star = apply_filters( 'wcslack_new_review_rating_star', ':star:' );
			$display_rating = str_repeat( $rating_star, $rating );

			// Review Content
			$review_content = apply_filters( 'wcslack_new_review_content', $comment_data->comment_content );

			// New Review Prefix
			$prefix = apply_filters( 'wcslack_new_review_prefix', __( 'New Review:', 'woocommerce-slack' ) );

			// Review URL
			$url = apply_filters( 'wcslack_new_review_url', get_comment_link( $comment_data ) );

			// New Review Message
			if ( $wrapper['new_review_message'] ) {
				$message_raw = $wrapper['new_review_message'];
				$tags = array( '{reviewer_name}', '{product_name}', '{review_content}', '{review_rating}', '{review_link}', '[[', ']]' );
				$replace = array( $reviewer, $product_name, $review_content, $display_rating, $url, '<', '>' );
				$message = str_replace( $tags, $replace, $message_raw );
			} else {
				$message = '*' . $prefix . '* ' . $reviewer . __( ' on ', 'woocommerce-slack' ) . $product_name;
				$message .= ' ' . $display_rating;
				$message .= ' - <' . $url . '|' . __( 'View Review', 'woocommerce-slack' ) . '>';
			}

			// Filter for the Message
			$message = apply_filters( 'wcslack_new_review_message', $message );

			// Attachment
			$fields = apply_filters( 'wcslack_new_review_attachment', array(
				0 => array(
					'title'	=> __( 'Reviewer', 'woocommerce-slack' ),
					'value'	=> $reviewer,
					'short'	=> true,
				),
				1 => array(
					'title'	=> __( 'Rating', 'woocommerce-slack' ),
					'value'	=> $display_rating,
					'short'	=> true,
				),
				2 => array(
					'title' => __( 'Product', 'woocommerce-slack' ),
					'value' => '<' . esc_url( get_permalink( $product_data->ID ) ) . '|' . $product_name . '>',
					'short' => false,
				),
				3 => array(
					'title'	=> __( 'Review', 'woocommerce-slack' ),
					'value'	=> $review_content,
					'short'	=> false,
				),
			) );

			// Only give $attachment content if the setting for 'extended notification' is on
			if ( $wrapper['new_review_extend'] == 'yes' ) {
				$attachment = $wcslack_api->attachment( $fields );
			} else {
				$attachment = '';
			}

			// Write message contents to log
			$this->log( $message, $channel, $emoji );

			return $wcslack_api->send_message( $api_key, $channel, $emoji, $message, $attachment );

		}


		/**
		 * New Customer Created
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function new_customer( $customer_id, $new_customer_data, $password_generated ) {

			$wcslack_api = new WC_Slack_API();

			$wrapper = $this->wrapper();

			$api_key = $wrapper['api_key'];

			$channel = $this->get_channel( 'new_customer' );
			$emoji = $this->get_emoji( 'new_customer' );

			// User Data by Customer ID
			$user_data = get_user_by( 'id', $customer_id );

			// Customer Name
			$user_name = apply_filters( 'wcslack_new_customer_name', $user_data->display_name );

			// Customer Prefix
			$prefix = apply_filters( 'wcslack_new_customer_prefix', __( 'New Customer:', 'woocommerce-slack' ) );

			// Customer URL
			$url = apply_filters( 'wcslack_new_customer_url', esc_url( admin_url( 'user-edit.php?user_id=' . $customer_id ) ) );

			// New Customer Message
			if ( $wrapper['new_customer_message'] ) {
				$message_raw = $wrapper['new_customer_message'];
				$tags = array( '{customer_name}', '{customer_link}', '[[', ']]' );
				$replace = array( $user_name, $url, '<', '>' );
				$message = str_replace( $tags, $replace, $message_raw );
			} else {
				$message = '*' . $prefix . '* ' . $user_name . __( ' Registered', 'woocommerce-slack' );
				$message .= ' - <' . $url . '|' . __( 'View Customer', 'woocommerce-slack' ) . '>';
			}

			// Filter for the Message
			$message = apply_filters( 'wcslack_new_customer_message', $message );

			$this->log( $message, $channel, $emoji );

			return $wcslack_api->send_message( $api_key, $channel, $emoji, $message );

		}


		/**
		 * Settings Wrapper
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function wrapper() {

			$WC_Slack_Settings = new WC_Slack_Settings();
			return $WC_Slack_Settings->wrapper();

		}


		/**
		 * Get channel to Send Notification too (based on settings)
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function get_channel( $event ) {

			$WC_Slack_Settings = new WC_Slack_Settings();
			$wrapper = $WC_Slack_Settings->wrapper();

			$channel_default 	= $wrapper['channel'];
			$channel_order 	= $wrapper[$event . '_channel'];

			if ( ! empty( $channel_order ) && ( $channel_order != 'select' ) ) {
				return $channel_order;
			} else {
				return $channel_default;
			}

		}


		/**
		 * Get Emoji for Notification (based on settings)
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function get_emoji( $event ) {

			$WC_Slack_Settings = new WC_Slack_Settings();
			$wrapper = $WC_Slack_Settings->wrapper();

			$emoji_default	= $wrapper['emoji'];
			$emoji_custom	= $wrapper[$event . '_emoji'];

			if ( isset( $emoji_custom ) && ( $emoji_custom != '' ) ) {
				return $emoji_custom;
			} else {
				return $emoji_default;
			}

		}


		/**
		 * Get Order Line Items
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 * @return 	array
		 */

		public function get_order_line( $order_id ) {

			$order = new WC_Order( $order_id );

			if ( sizeof( $order->get_items() ) > 0 ) {

				$items = array();

				foreach( $order->get_items() as $item ) {

					$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );

					// Item Name
					$item_name = sprintf( '<%s|%s>', get_permalink( $item['product_id'] ), $item['name'] );

					// Item Quantity
					$item_quantity = 'x ' . $item['qty'];

					// Item Total
					$item_total = html_entity_decode( get_woocommerce_currency_symbol() ) . number_format( (float)$order->get_line_total( $item, true ), 2, '.', '' );

					// Add to $items var
					$items[] = array(
						'value' => $item_name . ' ' . $item_quantity . ' - ' . $item_total,
						'short'	=> false,
					);

				}

				return $items;
			}

		}


		/**
		 * Get Order Total
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function get_order_shipping( $order_id ) {

			$order = new WC_Order( $order_id );

			$currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );

			$shipping_data = array();

			$shipping_total = version_compare( WC_VERSION, '3.0', '<' ) ? $order->get_total_shipping() : $order->get_shipping_total();

			if ( $shipping_total ) {

				$shipping_data[] = array(
					'title'	=> __( 'Shipping', 'woocommerce-slack' ) . ' - ' . $currency_symbol . number_format( (float)$shipping_total, 2, '.', '' ),
					'short'	=> false,
				);

			}

			return $shipping_data;

		}


		/**
		 * Get Order Total
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function get_order_total( $order_id ) {

			$order = new WC_Order( $order_id );

			$currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );

			$totals_data = array();

			if ( $totals = $order->get_total() ) {

				$totals_data[] = array(
					'title'	=> __( 'Order Total', 'woocommerce-slack' ) . ' - ' . $currency_symbol . number_format( (float)$order->get_total(), 2, '.', '' ),
					'short'	=> false,
				);

			}

			return $totals_data;

		}


		/**
		 * Log Message Wrapper
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 *
		 * @param $message
		 * @param $channel
		 * @param $emoji
		 */

		public function log( $message, $channel, $emoji ) {

			$wcslack_init = new WC_Slack_Init();

			$wcslack_init->add_debug_message( print_r( array(
				'Channels' => $channel,
				'Emoji'    => $emoji,
				'Message'  => $message
			), true ) );

		}

		/**
		 * Prepare order items titles for use in notifications
		 *
		 * @package WooCommerce Slack
		 * @author  Matty Cohen
		 * @since   1.1.4
		 *
		 * @param $order_items uses WC_Order->get_items()
		 */

		private function prepare_order_item_titles ( $order_items ) {

			$order_item_titles = '';

			if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
				$order_item_titles = implode(', ', wp_list_pluck( $order_items, 'name' ) );
			} else {
				$order_items_array = array();
				if ( is_array( $order_items ) ) {
					foreach ( $order_items as $item ) {
						$order_items_array[] = $item->get_name();
					}
					$order_item_titles = implode(', ', $order_items_array );
					unset( $order_items_array );
				}
			}

			return $order_item_titles;

		}

	}

}
