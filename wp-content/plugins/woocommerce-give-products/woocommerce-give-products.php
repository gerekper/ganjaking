<?php
/**
 * Plugin Name: WooCommerce Give Products
 * Description: Allow shop owners to freely gift products to users.
 * Version: 1.1.15
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Requires at least: 4.0.0
 * Tested up to: 5.9
 * WC tested up to: 6.1
 * WC requires at least: 2.6
 *
 * Copyright: © 2022 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Woo: 521947:c76e4d6a4935f9d2ba635d2c459e813e
 *
 * @package woocommerce-give-products
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Give_Products' ) ) {

	define( 'WC_GIVE_PRODUCTS_VERSION', '1.1.15' ); // WRCS: DEFINED_VERSION.

	/**
	 * Main plugin class.
	 */
	class WC_Give_Products {

		/**
		 * Class instance.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Main plugin file.
		 *
		 * @var string
		 */
		public static $plugin_file = __FILE__;

		/**
		 * Initialize the plugin.
		 *
		 * @since 1.0
		 */
		private function __construct() {
			if ( class_exists( 'WooCommerce' ) ) {
				add_action( 'admin_init', array( $this, 'init' ), 20 );

				// Load chosen scripts.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_chosen_scripts' ), 20 );

				// Update menu.
				add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

				// Admin notices.
				add_action( 'admin_notices', array( $this, 'admin_notice' ) );

				// Notice on customers order screen.
				add_action( 'woocommerce_view_order', array( $this, 'display_given_status' ) );

				// Notice on edit order screen.
				add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'display_given_status_admin' ) );

				// Add AJAX functionality.
				add_action( 'wp_ajax_give_products_json_search_products_and_variations', array( $this, 'json_search_products_and_variations' ) );

				// Add Given Order email.
				add_action( 'woocommerce_email_classes', array( $this, 'add_emails' ), 20, 1 );

				// Add screen id.
				add_filter( 'woocommerce_screen_ids', array( $this, 'woocommerce_screen_ids' ) );

				// Includes.
				add_action( 'init', array( $this, 'includes' ) );
			}
		}

		/**
		 * Load necessary files.
		 */
		public function includes() {
			include_once __DIR__ . '/includes/class-wc-give-products-privacy.php';
		}

		/**
		 * Init.
		 *
		 * @since 1.0
		 */
		public function init() {
			// Make sure this processing runs on the right page.
			if ( isset( $_GET['page'] ) && 'give_products' === $_GET['page'] ) {

				// Process any post data.
				if ( isset( $_GET['action'] ) &&
					'give' === $_GET['action'] &&
					isset( $_GET['user_id'] ) &&
					$_GET['user_id'] &&
					isset( $_GET['products'] ) &&
					isset( $_GET['give_products_nonce'] ) &&
					wp_verify_nonce( $_GET['give_products_nonce'], 'give_products' )
				) {

					// we have all the data we need - create the order!
					$this->create_order( $_GET['user_id'], $_GET['products'] );

				} elseif ( isset( $_GET['action'] ) ) {
					// Error.
					$url = add_query_arg( array( 'post_type' => 'product', 'page' => 'give_products', 'message' => '2' ), admin_url( 'edit.php' ) );
					wp_safe_redirect( $url );
				}
			}

		}


		/**
		 * Enqueue JS & CSS for Chosen select boxes.
		 *
		 * @since 1.0
		 */
		public function enqueue_chosen_scripts() {
			global $current_screen;
			global $woocommerce;

			if ( 'product_page_give_products' === $current_screen->base ) {
				// Chosen JS.
				wp_enqueue_script( 'ajax-chosen' );
				wp_enqueue_script( 'chosen' );

				// Chosen CSS.
				wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), WC_GIVE_PRODUCTS_VERSION );
			}
		}


		/**
		 * Add Give Products to admin menu.
		 *
		 * @since 1.0
		 */
		public function add_menu_item() {
			if ( is_admin() ) {
				add_submenu_page( 'edit.php?post_type=product', __( 'Give Products', 'woocommerce-give-products' ), __( 'Give Products', 'woocommerce-give-products' ), 'edit_users', 'give_products', array( $this, 'display_page' ) );
			}
			return false;
		}


		/**
		 * Display Give Products admin page.
		 *
		 * @since 1.0
		 */
		public function display_page() {
			// Display page content.
			echo sprintf(
				'<div class="wrap">
				<h2>%s</h2>
				<p>%s</p>
				<form action="" method="get">
				<input type="hidden" name="post_type" value="product"/>
				<input type="hidden" name="page" value="give_products"/>
				<input type="hidden" name="action" value="give"/>',
				esc_html__( 'Give Products', 'woocommerce-give-products' ),
				wp_kses_post( __( '<b>Select a user</b> by typing their display name, email address or user ID here:', 'woocommerce-give-products' ) )
			);

			if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
				?>
				<select id="user_id" style="width: 50%;" class="wc-customer-search" name="user_id" data-allow_clear="true" data-placeholder="<?php esc_attr_e( 'Search for a user', 'woocommerce-give-products' ); ?>">
				<?php
					if ( isset( $_GET['user_id'] ) ) {
						$user = get_user_by( 'ID', intval( $_GET['user_id'] ) );
						if ( $user ) {
							echo '<option value="' . esc_attr( intval( $user->data->ID ) ) . '" selected="selected">' . esc_html( $user->data->display_name ) . '</option>' . "\n";
						}
					}
				?>
				</select>
				<?php
			} else {
				?>
				<input type="hidden" class="wc-customer-search" id="user_id" name="user_id" data-placeholder="<?php _e( 'Search for a user', 'woocommerce-give-products' ); ?>" value="" data-allow_clear="true" style="max-width: 400px;" />
				<?php
			}

			echo '<p>' . wp_kses_post( __( '<b>Select products</b> by typing in their names, variation details or IDs here (you can select as many products as you like):', 'woocommerce-give-products' ) ) . '</p>';

			if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
				?>
				<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="products[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-give-products' ); ?>" data-allow_clear="true" data-action="woocommerce_json_search_products_and_variations">
				<?php
					if ( isset( $_GET['products'] ) ) {
						$product_ids = is_array( $_GET['products'] ) ? $_GET['products'] : explode( ',', $_GET['products'] );
						$products    = array_map( 'intval', $product_ids );
						foreach ( $products as $k => $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product->get_id() ) . '" selected="selected">' . esc_html( $product->get_title() ) . '</option>' . "\n";
							}
						}
					}
				?>
				</select>
				<?php
			} else {
				?>
				<input type="hidden" class="wc-product-search" id="product_list" name="products[]" data-multiple="true" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce-give-products' ); ?>" value="" data-allow_clear="true" style="max-width: 400px;" />
				<?php
			}

			wp_nonce_field( 'give_products', 'give_products_nonce' );

			echo '<p><input type="submit" value="' . __( 'Give product(s)', 'woocommerce-give-products' ) . '" class="button-primary"/></p>
				</form>
			</div>';

			if ( version_compare( WC_VERSION, '2.3', '<' ) ) {
				$give_products_vars = array(
					'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'          => wp_create_nonce( 'search-customers' ),
				);

				// Make sure we detect preset user and product IDs, and pass them through.
				if ( isset( $_GET['user_id'] ) ) {
					$give_products_vars['user_id'] = intval( $_GET['user_id'] );
				}

				if ( isset( $_GET['products'] ) ) {
					$give_products_vars['products'] = implode( ',', array_map( 'intval', explode( ',', $_GET['products'] ) ) );
				}

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 'give-products', plugins_url( 'assets/js/give-products' . $suffix . '.js', __FILE__ ), array( 'jquery' ), WC_GIVE_PRODUCTS_VERSION, true );
				wp_localize_script( 'give-products', 'giveProducts', $give_products_vars );
			}
		}

		/**
		 * Set order address details, for use with the WooCommerce 2.6 compatibility.
		 *
		 * @param object $order
		 * @param int $user_id
		 * @return void
		 */
		public function maybe_set_order_address_details( $order, $user_id ) {
			$keys = array(
				'billing_first_name',
				'billing_last_name',
				'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_state',
				'billing_postcode',
				'billing_country',
				'billing_email',
				'billing_phone',
				'shipping_first_name',
				'shipping_last_name',
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
				'shipping_state',
				'shipping_postcode',
				'shipping_country',
				'shipping_email',
				'shipping_phone',
			);

			$meta_values = get_user_meta( intval( $user_id ) );

			/*
			 * Backwards compatibility for WooCommerce 2.6. Note: This method works with 3.0
			 * and higher as well, yet should not be used, in favour of the CRUD helpers in the "else" clause.
			 */
			if ( version_compare( '3.0', WC_VERSION, '<' ) ) {
				$billing_address = array();
				$shipping_address = array();

				foreach ( $keys as $k ) {
					if (
						isset( $meta_values[ $k ] ) &&
						false !== strpos( $k, 'billing_' )
					) {
						$index = str_replace( 'billing_', '', $k );
						$billing_address[ $index ] = $meta_values[ $k ][0];
					}

					if (
						isset( $meta_values[ $k ] ) &&
						false !== strpos( $k, 'shipping_' )
					) {
						$index = str_replace( 'shipping_', '', $k );
						$shipping_address[ $index ] = $meta_values[ $k ][0];
					}
				}

				$order->set_address( $billing_address, 'billing' );
				$order->set_address( $shipping_address, 'shipping' );
			} else {
				// WooCommerce 3.0 and beyond, using CRUD helpers.
				foreach ( $keys as $k ) {
					if ( isset( $meta_values[ $k ] ) && method_exists( $order, 'set_' . $k ) ) {
						call_user_func_array( array( $order, 'set_' . $k ), array( $meta_values[ $k ][0] ) );
					}
				}

				$order->save();
			}
		}

		/**
		 * Create new order based on selection.
		 *
		 * @param int   $user_id  User ID.
		 * @param array $products List of products.
		 * @since 1.0
		 */
		public function create_order( $user_id, $products ) {
			global $woocommerce;

			// Get customer info.
			$user = get_userdata( $user_id );

			if ( ! empty( $products ) ) {

				// Create new order.
				$args = array(
					'customer_id'   => $user_id,
				);
				$order = wc_create_order( $args );

				if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
					$order_id = $order->get_id();
					$products = ! empty( $products ) ? $products : array();
				} else {
					$order_id = $order->id;
					$products = isset( $products[0] ) ? explode( ',', $products[0] ) : array();
				}

				// Set the billing and shipping address details, if they exist for the selected customer.
				$this->maybe_set_order_address_details( $order, $user_id );

				// Set _given_order post meta to true.
				update_post_meta( $order_id, '_wcgp_given_order', 'yes' );

				// Track the order status - if we have products that need to be shipped we should change this to processing.
				$order_status = 'completed';

				// Loop through each product we want to give away.
				foreach ( $products as $key => $value ) {

					// Get product data.
					$product = wc_get_product( $value );

					if ( $product ) {

						// Add product to order.
						$item              = array(
							'order_item_name' => $product->get_title(),
						);
						$item_id           = wc_add_order_item( $order_id, $item );
						$price_without_tax = version_compare( WC_VERSION, '3.0', '<' ) ? $product->get_price_excluding_tax() : wc_get_price_excluding_tax( $product );

						$product_id = $product->get_id();
						if ( is_callable( array( $product, 'get_type' ) ) && $product->get_type() === 'variation' ) {
							$product_id = $product->get_parent_id();
						}

						// Now add all of the product meta.
						wc_add_order_item_meta( $item_id, '_qty', 1 );
						wc_add_order_item_meta( $item_id, '_tax_class', $product->get_tax_class() );
						wc_add_order_item_meta( $item_id, '_product_id', $product_id );
						wc_add_order_item_meta( $item_id, '_variation_id', ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $product->variation_id ) ) ? $product->variation_id : $product->get_id() );
						wc_add_order_item_meta( $item_id, '_line_subtotal', wc_format_decimal( $price_without_tax ) );
						wc_add_order_item_meta( $item_id, '_line_subtotal_tax', '' );
						wc_add_order_item_meta( $item_id, '_line_total', wc_format_decimal( 0 ) );
						wc_add_order_item_meta( $item_id, '_line_tax', '' );
						wc_add_order_item_meta( $item_id, '_line_tax_data', array( 'total' => array(), 'subtotal' => array() ) );

						// Store variation data in meta.
						if ( method_exists( $product, 'get_variation_attributes' ) ) {
							$variation_data = $product->get_variation_attributes();
							if ( $variation_data && is_array( $variation_data ) ) {
								foreach ( $variation_data as $key => $value ) {
									wc_add_order_item_meta( $item_id, str_replace( 'attribute_', '', $key ), $value );
								}
							}
						}

						// See if the product needs to be shipped.
						if ( ( 'completed' === $order_status ) && $product->needs_shipping() ) {
							$order_status = 'processing';
						}
					} // End if.
				} // End foreach.

				// Update the order status.
				$args = array(
					'order_id' => $order_id,
					'status'   => $order_status,
				);

				// Update the order.
				$order = wc_update_order( $args );

				// Add a note that this product was gifted.
				$order->add_order_note( __( 'This order was gifted.', 'woocommerce-give-products' ) );

				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					$order->reduce_order_stock();
				} else {
					wc_reduce_stock_levels( $order->get_id() );
				}

				// Give download permissions.
				wc_downloadable_product_permissions( $order_id );

				// Init the WooCommerce email classes.
				$woocommerce->mailer();

				do_action( 'woocommerce_order_given', $order_id );

				$redirect = esc_url_raw( $_SERVER['HTTP_REFERER'] );

				// Add a success message.
				$redirect = add_query_arg( 'message', '1', $redirect );
				$redirect = add_query_arg( 'order_id', $order_id, $redirect );
				$redirect = remove_query_arg( 'action', $redirect );

				wp_safe_redirect( $redirect );

			} // End if.

		}


		/**
		 * Add new WC emails.
		 *
		 * @param array $email_classes List of classes.
		 * @since 1.0
		 */
		public function add_emails( $email_classes ) {

			$email_classes['WC_Given_Order'] = include __DIR__ . '/includes/emails/class-wc-given-order.php';
			return $email_classes;
		}


		/**
		 * Display notice when products are given.
		 *
		 * @since 1.0
		 */
		public function admin_notice() {
			global $current_screen;
			if ( 'product_page_give_products' === $current_screen->base ) {
				if ( isset( $_GET['message'] ) ) {
					switch ( $_GET['message'] ) {
						case 1:
							$display_name = 'selected user';
							if ( isset( $_GET['userid'] ) && $_GET['userid'] > 0 ) {
								$user         = get_userdata( $_GET['userid'] );
								$display_name = $user->data->display_name;
							}
							$order_id  = '';
							$order_url = '';
							if ( isset( $_GET['order_id'] ) ) {
								$order_id  = $_GET['order_id'];
								$order_url = get_option( 'siteurl' ) . '/wp-admin/post.php?post=' . intval( $order_id ) . '&action=edit';
							}
							/* translators: 1: display name 2: order url 3: order id */
							$format = __( 'Product(s) given to %1$s in <a href="%2$s">order %3$s</a>.', 'woocommerce-give-products' );
							$format = sprintf( $format, $display_name, $order_url, $order_id );
							echo '<div class="updated"><p>' . wp_kses_post( $format ) . '</p></div>';
							break;
						case 2:
							$format = __( 'Make sure you select both a user to receive the gift and a product to give.', 'woocommerce-give-products' );
							echo '<div class="error"><p>' . wp_kses_post( $format ) . '</p></div>';
							break;
						case 3:
							$format = __( 'Processing error - please try again.', 'woocommerce-give-products' );
							echo '<div class="error"><p>' . wp_kses_post( $format ) . '</p></div>';
							break;
						default: break;
					}
				}
			}
		}


		/**
		 * Display message on front-end order view.
		 *
		 * @param int $order_id Order ID.
		 * @since 1.0
		 */
		public function display_given_status( $order_id ) {
			if ( $order_id ) {
				if ( 'yes' === get_post_meta( $order_id, '_wcgp_given_order', true ) ) {
					/* translators: 1: blog info name */
					echo "<div class='given_order'>" . sprintf( esc_html__( 'The products in this order were given to you by %s.', 'woocommerce-give-products' ), get_bloginfo( 'name' ) ) . '</div>';
				}
			}
		}


		/**
		 * Display message on back-end order view
		 *
		 * @param WC_Order $order Order object.
		 * @since 1.0
		 */
		public function display_given_status_admin( $order ) {
			if ( $order ) {
				if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
					$order_id = $order->get_id();
				} else {
					$order_id = $order->id;
				}

				if ( 'yes' === get_post_meta( $order_id, '_wcgp_given_order', true ) ) {
					echo "<p class='form-field form-field-wide'>" . esc_html__( 'This order was given free of charge', 'woocommerce-give-products' ) . '</p>';
				}
			}
		}


		/**
		 * Display message on back-end order view.
		 *
		 * @since 1.0
		 */
		public function json_search_products_and_variations() {
			$posts = array();

			check_ajax_referer( 'search-customers', 'security' );

			$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

			if ( empty( $term ) ) {
				die();
			}

			$post_types = array( 'product', 'product_variation' );

			if ( is_numeric( $term ) ) {

				$args = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post__in'       => array( 0, $term ),
					'fields'         => 'ids',
				);

				$args2 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post_parent'    => $term,
					'fields'         => 'ids',
				);

				$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ) ) );

			} else {

				$args = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					's'              => $term,
					'fields'         => 'ids',
				);

				$args2 = array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => '_sku',
							'value'   => $term,
							'compare' => 'LIKE',
						),
					),
					'fields'         => 'ids',
				);

				$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ) ) );

			} // End if.

			$found_products = array();

			foreach ( $posts as $post ) {

				$sku = get_post_meta( $post, '_sku', true );

				if ( isset( $sku ) && $sku ) {
					$sku = ' (SKU: ' . $sku . ')';
				}

				$post_type = get_post_type( $post );

				if ( 'product_variation' === $post_type ) {
					$variation = new WC_Product_Variation( $post );
					$atts      = $variation->get_variation_attributes();
					$attlist   = '';
					foreach ( $atts as $att ) {
						if ( '' !== $attlist ) {
							$attlist .= ', ';
						}
						$attlist .= $att;
					}
					$title = str_replace( 'Variation #' . $post . ' of ', '', get_the_title( $post ) ) . ': ' . ucwords( $attlist );
				} else {
					$title = get_the_title( $post );
				}

				$found_products[ $post ] = $title . ' &ndash; #' . $post . $sku;

			}

			echo wp_json_encode( $found_products );
			die();
		}

		/**
		 * Screen IDS
		 *
		 * @param  array $ids List of Screen IDs.
		 * @return array
		 */
		public function woocommerce_screen_ids( $ids ) {
			return array_merge(
				$ids,
				array(
					'product_page_give_products',
				)
			);
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 * @since  1.0
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
} // End if.

add_action( 'plugins_loaded', array( 'WC_Give_Products', 'get_instance' ) );
