<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP {

		/**
		 * Plugin version
		 *
		 * @const string
		 * @since 1.0.0
		 */
		const YITH_WCDP_VERSION = '1.3.7';

		/**
		 * Single instance of support cart
		 *
		 * @var \YITH_WCDP_Support_Cart
		 */
		protected $_support_cart = null;

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCDP
		 * @since 1.0.0
		 */
		public function __construct() {
			do_action( 'yith_wcdp_startup' );

			add_action( 'init', array( $this, 'install' ) );

			// load plugin-fw
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			// change checkout process
			add_filter( 'yith_wcdp_virtual_on_deposit', array( $this, 'set_virtual_on_deposit' ) );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'update_cart_item' ), 30, 3 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'update_cart_item_data' ), 30, 3 );
			add_filter( 'woocommerce_get_cart_item_from_session', array(
				$this,
				'get_cart_item_from_session'
			), 110, 2 );
			add_filter( 'woocommerce_add_to_cart_sold_individually_found_in_cart', array(
				$this,
				'deposit_found_in_cart'
			), 10, 5 );
			add_filter( 'woocommerce_payment_complete_order_status', array(
				$this,
				'deposit_payment_complete_status'
			), 10, 3 );

			// fix held stock, that should not include balance orders
			add_action( 'init', array( $this, 'fix_held_stock' ) );

			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
				add_filter( 'woocommerce_checkout_create_order_line_item', array(
					$this,
					'update_order_item_data_wc_3'
				), 30, 3 );
			} else {
				add_filter( 'woocommerce_add_order_item_meta', array( $this, 'update_order_item_data' ), 30, 2 );
			}

			// ajax call handling
			add_action( 'wp_ajax_yith_wcdp_calculate_shipping', array( $this, 'ajax_calculate_shippings' ) );
			add_action( 'wp_ajax_nopriv_yith_wcdp_calculate_shipping', array( $this, 'ajax_calculate_shippings' ) );
			add_action( 'wp_ajax_yith_wcdp_change_location', array( $this, 'ajax_change_location' ) );
			add_action( 'wp_ajax_nopriv_yith_wcdp_change_location', array( $this, 'ajax_change_location' ) );

			// handle downloads for partially-paid orders
			add_filter( 'woocommerce_order_is_download_permitted', array( $this, 'download_partially_paid' ), 10, 2 );

			// change templates that appear both on frontend and backend operations (checkout/cart/my-account/emails)
			add_action( 'woocommerce_order_item_meta_end', array( $this, 'print_deposit_order_item' ), 10, 3 );
			add_action( 'woocommerce_order_item_meta_end', array( $this, 'print_quick_deposit_action' ), 10, 3 );
		}

		/**
		 * Install plugin
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function install() {
			YITH_WCDP_Suborders();

			if ( ! is_admin() ) {
				YITH_WCDP_Frontend();
			}
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/* === SUPPORT CART METHODS === */

		/**
		 * Creates single instance of support cart
		 *
		 * @return \YITH_WCDP_Support_Cart
		 * @since 1.3.0
		 */
		public function get_support_cart() {
			if ( is_null( $this->_support_cart ) ) {
				$this->_support_cart = new YITH_WCDP_Support_Cart();
			}

			$this->_support_cart->empty_cart();

			return $this->_support_cart;
		}

		/* === HELPER METHODS === */

		/**
		 * Return true if deposit is enabled on product
		 *
		 * @param $product_id int|bool Product id, if specified; false otherwise. If no product id is provided, global $product will be used
		 *
		 * @return bool Whether deposit is enabled for product
		 * @since 1.0.0
		 */
		public function is_deposit_enabled_on_product( $product_id = false ) {
			global $product;

			$product = ! $product_id ? $product : ( is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id );

			// get global options
			$plugin_enabled = get_option( 'yith_wcdp_general_enable', 'yes' );

			// get product specific option
			$deposit_enabled = yit_get_prop( $product, '_enable_deposit', true );
			$deposit_enabled = ! empty( $deposit_enabled ) ? $deposit_enabled : 'default';
			$deposit_enabled = ( $deposit_enabled == 'default' ) ? get_option( 'yith_wcdp_general_deposit_enable', 'no' ) : $deposit_enabled;

			return apply_filters( 'yith_wcdp_is_deposit_enabled_on_product', $plugin_enabled == 'yes' && $deposit_enabled == 'yes', $product_id, false );
		}

		/**
		 * Return true in deposit is mandatory for a product
		 *
		 * @param $product_id int|bool Product id, if specified; false otherwise. If no product id is provided, global $product will be used
		 *
		 * @return bool Whether deposit is enabled for product
		 * @since 1.0.0
		 */
		public function is_deposit_mandatory( $product_id = false ) {
			global $product;

			$product = ! $product_id ? $product : ( is_numeric( $product_id ) ? wc_get_product( $product_id ) : $product_id );

			// get product specific option
			$deposit_mandatory = yit_get_prop( $product, '_force_deposit', true );
			$deposit_mandatory = ! empty( $deposit_mandatory ) ? $deposit_mandatory : 'default';
			$deposit_mandatory = ( $deposit_mandatory == 'default' ) ? get_option( 'yith_wcdp_general_deposit_force', 'no' ) : $deposit_mandatory;

			return apply_filters( 'yith_wcdp_is_deposit_mandatory', $deposit_mandatory == 'yes', $product_id, false );
		}

		/**
		 * Retrieve deposit amount (needed on amount deposit type)
		 *
		 * @return string Amount
		 * @since 1.0.0
		 */
		public function get_deposit_amount() {
			$deposit_amount = get_option( 'yith_wcdp_general_deposit_amount', 0 );

			return $deposit_amount;
		}

		/**
		 * Calculate deposit for product and variation passed as param
		 *
		 * @param $product_id int Product id
		 * @param $price      double|bool Current product price (often third party plugin changes cart item price); false to use price from product object
		 *
		 * @return double Deposit amount for specified product and variation
		 * @since 1.0.4
		 */
		public function get_deposit( $product_id, $price = false, $context = 'edit' ) {
			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				return 0;
			}

			$price = 'view' == $context ? yith_wcdp_get_price_to_display( $product, array_merge( array( 'qty' => 1 ), $price ? array( 'price' => $price ) : array() ) ) : ( $price ? $price : $product->get_price() );

			$deposit_amount = $this->get_deposit_amount();
			$deposit_amount = 'view' == $context ? yith_wcdp_get_price_to_display( $product, array(
				'qty'   => 1,
				'price' => $deposit_amount
			) ) : $deposit_amount;

			$deposit_value = min( $deposit_amount, $price );

			return $deposit_value;
		}

		/* === CHECKOUT PROCESS METHODS === */

		/**
		 * Set virtual on deposit depending on backend option
		 *
		 * @param $virtual_on_deposit bool Whether deposits product should be virtual or not
		 *
		 * @return bool Whether deposits product should be virtual or not
		 *
		 * @since 1.2.5
		 */
		public function set_virtual_on_deposit( $virtual_on_deposit ) {
			$virtual_on_deposit = 'yes' == get_option( 'yith_wcdp_general_deposit_virtual', 'yes' );

			return $virtual_on_deposit;
		}

		/**
		 * Update cart item when deposit is selected
		 *
		 * @param $cart_item mixed Current cart item
		 *
		 * @return mixed Filtered cart item
		 * @since 1.0.0
		 */
		public function update_cart_item( $cart_item ) {
			/**
			 * @var $product \WC_Product
			 */
			$product = $cart_item['data'];

			if ( $this->is_deposit_enabled_on_product( $product->get_id() ) && ! yit_get_prop( $product, 'yith_wcdp_deposit', true ) ) {
				$deposit_forced = $this->is_deposit_mandatory( $product->get_id() );
				$deposit_value  = $this->get_deposit( $product->get_id(), $product->get_price() );

				if (
					apply_filters( 'yith_wcdp_process_cart_item_product_change', true, $cart_item ) &&
					isset( $_REQUEST['add-to-cart'] ) &&
					( ( $deposit_forced && ! defined( 'YITH_WCDP_PROCESS_SUBORDERS' ) ) || ( isset( $_REQUEST['payment_type'] ) && $_REQUEST['payment_type'] == 'deposit' ) )
				) {
					yit_set_prop( $cart_item['data'], 'price', $deposit_value );
					yit_set_prop( $cart_item['data'], 'yith_wcdp_deposit', true );

					if ( apply_filters( 'yith_wcdp_virtual_on_deposit', true, null ) ) {
						yit_set_prop( $cart_item['data'], 'virtual', 'yes' );
					}
				}
			}

			return $cart_item;
		}

		/**
		 * Add cart item data when deposit is selected, to store info to save with order
		 *
		 * @param $cart_item_data mixed Currently saved cart item data
		 * @param $product_id     int   Product id
		 *
		 * @return mixed Filtered cart item data
		 * @since 1.0.0
		 */
		public function update_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
			$product_id = ! empty( $variation_id ) ? $variation_id : $product_id;
			$product    = wc_get_product( $product_id );

			if ( $this->is_deposit_enabled_on_product( $product_id ) ) {
				$deposit_forced = $this->is_deposit_mandatory( $product_id );

				$deposit_amount  = $this->get_deposit_amount();
				$deposit_value   = $this->get_deposit( $product_id, $product->get_price() );
				$deposit_balance = max( $product->get_price() - $deposit_value, 0 );

				$process_deposit = ( $deposit_forced && ! defined( 'YITH_WCDP_PROCESS_SUBORDERS' ) ) || ( isset( $_REQUEST['payment_type'] ) && $_REQUEST['payment_type'] == 'deposit' );

				if ( apply_filters( 'yith_wcdp_process_deposit', $process_deposit, $cart_item_data ) ) {
					$cart_item_data['deposit']                 = true;
					$cart_item_data['deposit_type']            = 'amount';
					$cart_item_data['deposit_amount']          = $deposit_amount;
					$cart_item_data['deposit_rate']            = 0;
					$cart_item_data['deposit_value']           = $deposit_value;
					$cart_item_data['deposit_balance']         = $deposit_balance;
					$cart_item_data['deposit_shipping_method'] = isset( $_POST['shipping_method'] ) ? $_POST['shipping_method'] : false;
				}
			}

			return $cart_item_data;
		}

		/**
		 * When product is sold individually, before adding it to cart, checks whether there isn't any other item with a different cart_id
		 * that is just a deposit (non-deposit) version of the simple product (deposit product) being added to cart
		 *
		 * @param $found_in_cart  bool Whether item is already in cart
		 * @param $product_id     int Id of the product being added to cart
		 * @param $variation_id   int Id of the variation being added to cart
		 * @param $cart_item_data array Array of cart item data for item being added to cart
		 * @param $cart_id        string Cart item id of the item being added to cart
		 *
		 * @return bool Whether item is already in cart
		 * @since 1.1.2
		 */
		public function deposit_found_in_cart( $found_in_cart, $product_id, $variation_id, $cart_item_data, $cart_id ) {
			$cart               = WC()->cart;
			$new_cart_item_data = $cart_item_data;

			if ( isset( $cart_item_data['deposit'] ) ) {
				unset( $new_cart_item_data['deposit'] );
				unset( $new_cart_item_data['deposit_type'] );
				unset( $new_cart_item_data['deposit_amount'] );
				unset( $new_cart_item_data['deposit_rate'] );
				unset( $new_cart_item_data['deposit_value'] );
				unset( $new_cart_item_data['deposit_balance'] );
				unset( $new_cart_item_data['deposit_shipping_method'] );
			} else {
				$old_payment_type_value   = isset( $_REQUEST['payment_type'] ) ? $_REQUEST['payment_type'] : false;
				$_REQUEST['payment_type'] = 'deposit';

				$new_cart_item_data = $this->update_cart_item_data( $cart_item_data, $product_id, $variation_id );

				$_REQUEST['payment_type'] = $old_payment_type_value;
			}

			if ( isset( $new_cart_item_data['variation'] ) ) {
				$new_cart_id = $cart->generate_cart_id( $product_id, $variation_id, $new_cart_item_data['variation'], $new_cart_item_data );
			} else {
				$new_cart_id = 0;
			}

			$related_found = $cart->find_product_in_cart( $new_cart_id );

			return $found_in_cart || $related_found;
		}

		/**
		 * Set order to completed after payment if it only contains deposits, and if deposits are virtual
		 *
		 * @param $complete_status string Order status after payment
		 * @param $order_id        int Current order it
		 * @param $order           \WC_Order Current order
		 *
		 * @return string Filtered status
		 * @since 1.2.1
		 */
		public function deposit_payment_complete_status( $complete_status, $order_id, $order ) {
			$deposit_only = true;

			if ( $order ) {
				$items = $order->get_items();

				if ( ! empty( $items ) ) {
					foreach ( $items as $item ) {
						if ( ! isset( $item['deposit'] ) ) {
							$deposit_only = false;
							break;
						}
					}
				}
			}
			if ( $deposit_only && apply_filters( 'yith_wcdp_virtual_on_deposit', true, $order ) ) {
				return 'completed';
			}

			return $complete_status;
		}

		/**
		 * Update cart item when retrieving cart from session
		 *
		 * @param $session_data mixed Session data to add to cart
		 * @param $values       mixed Values stored in session
		 *
		 * @return mixed Session data
		 * @since 1.0.0
		 */
		public function get_cart_item_from_session( $session_data, $values ) {
			if ( isset( $values['deposit'] ) && $values['deposit'] ) {

				$session_data['deposit']                 = true;
				$session_data['deposit_type']            = isset( $values['deposit_type'] ) ? $values['deposit_type'] : '';
				$session_data['deposit_amount']          = isset( $values['deposit_amount'] ) ? $values['deposit_amount'] : '';
				$session_data['deposit_rate']            = isset( $values['deposit_rate'] ) ? $values['deposit_rate'] : '';
				$session_data['deposit_value']           = isset( $values['deposit_value'] ) ? apply_filters( 'yith_wcdp_deposit_value', $values['deposit_value'], $session_data['product_id'], $session_data['variation_id'], $session_data ) : '';
				$session_data['deposit_balance']         = isset( $values['deposit_balance'] ) ? apply_filters( 'yith_wcdp_deposit_balance', $values['deposit_balance'], $session_data['product_id'], $session_data['variation_id'], $session_data ) : '';
				$session_data['deposit_shipping_method'] = isset( $values['deposit_shipping_method'] ) ? $values['deposit_shipping_method'] : '';

				if (
					apply_filters( 'yith_wcdp_process_cart_item_product_change', true, $session_data ) &&
					isset( $values['deposit_value'] )
				) {
					yit_set_prop( $session_data['data'], 'price', $values['deposit_value'] );
					yit_set_prop( $session_data['data'], 'yith_wcdp_deposit', true );

					if ( apply_filters( 'yith_wcdp_virtual_on_deposit', true, null ) ) {
						yit_set_prop( $session_data['data'], 'virtual', 'yes' );
					}
				}
			}


			return $session_data;
		}

		/**
		 * Store deposit cart item data as order item meta, on process checkout
		 *
		 * @param $item_id int   Currently created order item id
		 * @param $values  mixed Cart item data
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_order_item_data( $item_id, $values ) {
			if ( isset( $values['deposit'] ) && $values['deposit'] ) {
				wc_add_order_item_meta( $item_id, '_deposit', true );
				wc_add_order_item_meta( $item_id, '_deposit_type', $values['deposit_type'] );
				wc_add_order_item_meta( $item_id, '_deposit_amount', $values['deposit_amount'] );
				wc_add_order_item_meta( $item_id, '_deposit_rate', $values['deposit_rate'] );
				wc_add_order_item_meta( $item_id, '_deposit_value', $values['deposit_value'] );
				wc_add_order_item_meta( $item_id, '_deposit_balance', $values['deposit_balance'] );
				wc_add_order_item_meta( $item_id, '_deposit_shipping_method', $values['deposit_shipping_method'] );
			}
		}

		/**
		 * Store deposit cart item data as order item meta, on process checkout (for WC >= 3.0.0)
		 *
		 * @param $item_id int   Currently created order item id
		 * @param $values  mixed Cart item data
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_order_item_data_wc_3( $item, $cart_item_key, $values ) {

			/**
			 * @var $item \WC_Order_Item_Product current item object
			 */

			if ( isset( $values['deposit'] ) && $values['deposit'] ) {
				$item->add_meta_data( '_deposit', true );
				$item->add_meta_data( '_deposit_type', $values['deposit_type'] );
				$item->add_meta_data( '_deposit_amount', $values['deposit_amount'] );
				$item->add_meta_data( '_deposit_rate', $values['deposit_rate'] );
				$item->add_meta_data( '_deposit_value', $values['deposit_value'] );
				$item->add_meta_data( '_deposit_balance', $values['deposit_balance'] );
				$item->add_meta_data( '_deposit_shipping_method', $values['deposit_shipping_method'] );
			}
		}

		/* === TEMPLATE CHANGES (FRONTEND/BACKEND) === */

		/**
		 * Print item data on cart / checkout views, to inform user about deposit & balance he's going to pay
		 *
		 * @param $item_id int Order item id
		 * @param $item    mixed Order item assoc array
		 * @param $order   \WC_Order Order object
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_deposit_order_item( $item_id, $item, $order ) {
			if ( isset( $item['deposit'] ) && $item['deposit'] ) {
				$full_amount = $item['deposit_value'] + $item['deposit_balance'];
				$product     = is_object( $item ) ? $item->get_product() : $order->get_product_from_item( $item );

				$template = '';

				$template .= '<p style=" margin: 0;padding: 0;"><small style="display: block !important;">' . wp_kses_post( apply_filters( 'yith_wcdp_full_price_filter', __( 'Full price', 'yith-woocommerce-deposits-and-down-payments' ) ) ) . ': ' . wc_price( yith_wcdp_get_price_to_display( $product, array(
						'qty'   => intval( $item['qty'] ),
						'price' => $full_amount,
						'order' => $order
					) ) ) . '</small></p>';
				$template .= '<p style=" margin: 0;padding: 0;"><small style="display: block !important;">' . wp_kses_post( apply_filters( 'yith_wcdp_balance_filter', __( 'Balance', 'yith-woocommerce-deposits-and-down-payments' ) ) ) . ': ' . wc_price( yith_wcdp_get_price_to_display( $product, array(
						'qty'   => intval( $item['qty'] ),
						'price' => $item['deposit_balance'],
						'order' => $order
					) ) ) . '</small></p>';

				echo $template;
			}
		}

		/**
		 * Print quick actions available for deposit items on view-order / thank-you pages
		 *
		 * @param $item_id int Current item id
		 * @param $item    mixed Current item content
		 * @param $order   \WC_Order Current wc order
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_quick_deposit_action( $item_id, $item, $order ) {
			if ( isset( $item['deposit'] ) && $item['deposit'] ) {
				$actions  = array();
				$suborder = wc_get_order( $item['full_payment_id'] );

				if ( ! $suborder ) {
					return;
				}

				if ( $suborder->needs_payment() ) {
					$actions['pay'] = array(
						'url'  => $suborder->get_checkout_payment_url(),
						'name' => __( 'Pay', 'woocommerce' )
					);
				}

				$actions['view'] = array(
					'url'  => $suborder->get_view_order_url(),
					'name' => __( 'View', 'woocommerce' )
				);

				$actions = apply_filters( 'yith_wcdp_my_account_print_quick_deposit_action', $actions, $suborder );

				$template = '<small style="display: block;">';
				foreach ( $actions as $key => $action ) {
					$template .= '<a href="' . esc_url( $action['url'] ) . '" class="yith-wcdp-order-actions button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a> ';
				}
				$template .= '</small>';

				echo $template;
			}
		}

		/* === AJAX METHODS === */

		/**
		 * Calculate shipping methods for currently selected product, and print them json-encoded for ajax requests
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_calculate_shippings() {
			if ( ! isset( $_POST['product_id'] ) ) {
				wp_send_json( array( 'template' => '' ) );
			}

			$product_id = $_POST['product_id'];
			$qty        = isset( $_POST['qty'] ) ? $_POST['qty'] : 1;

			$product = wc_get_product( $product_id );

			if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
				wp_send_json( array( 'template' => '' ) );
			}

			$product_id   = $product->is_type( 'variation' ) ? yit_get_prop( $product, 'parent_id' ) : $product_id;
			$variation_id = $product->is_type( 'variation' ) ? $product->get_id() : '';
			$variations   = $product->is_type( 'variation' ) ? $product->get_variation_attributes() : array();

			$support_cart = $this->get_support_cart();
			$support_cart->add_to_cart( $product_id, $qty, $variation_id, $variations );
			$support_cart->calculate_shipping();

			ob_start();
			wc_cart_totals_shipping_html();
			$shipping_template = ob_get_clean();

			// print notices in current session, to send them with response
			ob_start();
			wc_print_notices();
			$notices = ob_get_clean();

			wp_send_json( array(
				'template' => $shipping_template,
				'notices'  => $notices
			) );
		}

		/**
		 * Change location and calculate shipping method for currently selected product
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_change_location() {
			WC_Shortcode_Cart::calculate_shipping();

			$this->ajax_calculate_shippings();
		}

		/* === DOWNLOAD PARTIALLY PAID === */

		/**
		 * Let customers download files on deposit when deposit can be downloadable
		 *
		 * @param $is_download_permitted bool Whether order is downloadable or not
		 * @param $order                 \WC_Abstract_Order current order
		 *
		 * @return bool Whether order is downloadable or not
		 */
		public function download_partially_paid( $is_download_permitted, $order ) {
			if ( ! apply_filters( 'yith_wcdp_not_downloadable_on_deposit', true ) && 'partially-paid' == $order->get_status() ) {
				return true;
			}

			return $is_download_permitted;
		}

		/* === FIX HELD STOCK === */

		/**
		 * Register actions that will add held stock fix
		 *
		 * @return void
		 */
		public function fix_held_stock() {
			add_action( 'woocommerce_check_cart_items', array( $this, 'add_held_stock_fix' ), 0 );
			add_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'remove_held_stock_fix' ), 15 );
		}

		/**
		 * Enqueue stock fix, just before retrieving stock quantity
		 *
		 * @return void
		 */
		public function add_held_stock_fix() {
			add_filter( 'woocommerce_product_get_stock_quantity', array(
				$this,
				'increase_stock_of_held_amount'
			), 10, 2 );
		}

		/**
		 * Dequeue stock fix, just after retrieving stock quantity
		 *
		 * @param $stock_qty int Original sotck quantity
		 *
		 * @return int Stock quantity
		 */
		public function remove_held_stock_fix( $stock_qty ) {
			remove_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'increase_stock_of_held_amount' ) );

			return $stock_qty;
		}

		/**
		 * Increase stock quantity just before checking cart items
		 * This allows us to stock back  balance items, that normally would be considered as held items and removed from current stock
		 * This happens only during cart items check, and for this reason this fix is limited to that specific execution
		 *
		 * @param $stock_qty int Original stock quantity
		 * @param $product   \WC_Product Current product
		 *
		 * @return int Filtered stock quantity
		 */
		public function increase_stock_of_held_amount( $stock_qty, $product ) {
			global $wpdb;

			// count stock of balances
			$balance_pending_stock = $wpdb->get_var(
				$wpdb->prepare(
					"
					 SELECT SUM( order_item_meta.meta_value ) AS held_qty
					 FROM {$wpdb->posts} AS posts
					 LEFT JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
					 LEFT JOIN {$wpdb->prefix}woocommerce_order_items as order_items ON posts.ID = order_items.order_id
					 LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
					 LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta2 ON order_items.order_item_id = order_item_meta2.order_item_id
					 WHERE 	order_item_meta.meta_key    = '_qty'
					 AND 	order_item_meta2.meta_key   = %s
					 AND 	order_item_meta2.meta_value = %d
					 AND 	posts.post_type             IN ( '" . implode( "','", wc_get_order_types() ) . "' )
					 AND 	posts.post_status           = 'wc-pending'
					 AND    posts.post_parent           != 0
					 AND    postmeta.meta_key = %s
					 AND    postmeta.meta_value = %s",
					'variation' === get_post_type( $product->get_stock_managed_by_id() ) ? '_variation_id' : '_product_id',
					$product->get_stock_managed_by_id(),
					'_created_via',
					'yith_wcdp_balance_order'
				)
			);

			return $stock_qty + (int) $balance_pending_stock;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCDP class
 *
 * @return \YITH_WCDP
 * @since 1.0.0
 */
function YITH_WCDP() {
	return YITH_WCDP::get_instance();
}