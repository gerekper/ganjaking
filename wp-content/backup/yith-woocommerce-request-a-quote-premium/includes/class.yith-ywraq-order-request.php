<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YITH_YWRAQ_Order_Request class.
 *
 * @class    YITH_YWRAQ_Order_Request
 * @since    1.0.0
 * @author   YITH
 * @package  YITH
 */
class YITH_YWRAQ_Order_Request {

	/**
	 * Array with Quote List data
	 *
	 * @var $data
	 */
	protected $_data = array();

	/**
	 * Name of dynamic coupon
	 *
	 * @var $label_coupon
	 */
	protected $label_coupon = 'quotediscount';


	/**
	 * Payment Info
	 *
	 * @var array
	 */
	protected $order_payment_info = array();

	/**
	 * Message Args
	 *
	 * @var array
	 */
	private $args_message = array();

	/**
	 * Status Changed
	 *
	 * @var bool
	 */
	private $status_changed = false;

	/**
	 * Quote Sent
	 *
	 * @var bool
	 */
	private $quote_sent = false;

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_YWRAQ_Order_Request
	 */
	protected static $instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_YWRAQ_Order_Request
	 * @since 1.0.0
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * Initialize plugin and registers actions and filters to be used
	 *
	 * @since  1.0.0
	 * @author Emanuela Castorina
	 */
	public function __construct() {

		$this->_data['raq_order_status'] = array(
			'wc-ywraq-new',
			'wc-ywraq-pending',
			'wc-ywraq-expired',
			'wc-ywraq-rejected',
			'wc-ywraq-accepted',
		);

		// Order Customization *******************************.
		add_action( 'init', array( $this, 'register_order_status' ) );
		add_filter( 'wc_order_statuses', array( $this, 'add_custom_status_to_order_statuses' ) );
		add_filter( 'wc_order_is_editable', array( $this, 'order_is_editable' ), 10, 2 );

		// add custom metabox.
		add_action( 'admin_init', array( $this, 'add_metabox' ), 10 );
		add_action( 'woocommerce_before_order_object_save', array( $this, 'save_quote' ) );
		add_action( 'wp_insert_post', array( $this, 'raq_order_action' ), 100, 2 );

		if ( 'yes' === get_option( 'ywraq_enable_order_creation', 'yes' ) ) {
			add_action( 'ywraq_process', array( $this, 'create_order' ), 10, 1 );
			// fix the price if the product has price empty.
			add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 10, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'get_price' ), 10, 2 );
		}

		// Backend Quotes *******************************.
		add_filter( 'views_edit-shop_order', array( $this, 'show_add_new_quote' ) );
		add_action( 'add_meta_boxes_shop_order', array( $this, 'set_new_quote' ) );
		add_action( 'woocommerce_before_order_object_save', array( $this, 'save_new_quote' ) );

		// Ajax Action *******************************.
		add_action( 'wc_ajax_yith_ywraq_order_action', array( $this, 'ajax' ) );

		// My Account *******************************.
		if ( is_user_logged_in() && ! is_admin() ) {
			add_action( 'woocommerce_account_view-quote_endpoint', array( $this, 'load_view_quote_page' ) );
			add_filter( 'woocommerce_endpoint_view-quote_title', array( $this, 'load_view_quote_page_title' ) );
		}

		// my account list quotes.
		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'my_account_my_orders_query' ) );
		add_action( 'woocommerce_before_my_account', array( $this, 'my_account_my_quotes' ) );

		// User Action *******************************.
		add_filter( 'nonce_user_logged_out', array( $this, 'wpnonce_filter' ), 10, 2 );
		add_action( 'wp_loaded', array( $this, 'change_order_status' ) );
		add_action( 'ywraq_raq_message', array( $this, 'print_message' ), 10 );

		// Quote Accepted *******************************.
		add_filter( 'woocommerce_add_cart_item', array( $this, 'set_new_product_price' ), 20 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'set_new_product_price' ), 100 ); // the priority have to be 100 or greater (Booking support).
		add_filter( 'woocommerce_get_item_data', array( $this, 'add_custom_item_meta' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_custom_item_meta_checkout' ), 10, 3 );
		add_filter( 'woocommerce_display_item_meta', array( $this, 'sanitize_raq_item_meta' ) );

		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_cart_fee' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'checkout_save_billing_shipping' ), 10, 3 );

		// coupons.
		add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'label_coupon' ), 10, 2 );
		add_filter( 'woocommerce_coupon_message', array( $this, 'manage_coupon_message' ), 10, 3 );

		// remove coupon if cart total changed.
		add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'remove_quote_coupon' ), 10 );
		add_action( 'woocommerce_cart_item_removed', array( $this, 'remove_quote_coupon' ), 20, 1 );
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'coupon_validation' ), 10, 3 );
		// shipping.
		add_action( 'woocommerce_review_order_before_shipping', array( $this, 'set_shipping_methods' ) );
		add_filter( 'woocommerce_package_rates', array( $this, 'set_package_rates' ) );

		// add the cart_hash as post meta of order to process the same order.
		add_filter( 'woocommerce_create_order', array( $this, 'set_cart_hash' ), 1 );

		// if the customer cancel the order empty the cart.
		add_action( 'woocommerce_cancelled_order', array( $this, 'empty_cart' ) );

		// override the checkout fields if this option is enabled.
		add_action( 'template_redirect', array( $this, 'checkout_fields_manage' ) );

		// check if a customer is paying a quote.
		add_action( 'wp_loaded', array( $this, 'check_quote_in_cart' ) );

		// remove meta of quote after order processed.
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'raq_processed' ), 10, 2 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'set_order_as_quote' ), 20, 3 );

		// pay for order.
		add_filter( 'woocommerce_order_needs_payment', array( $this, 'set_quote_ready_for_pay_now' ), 10, 2 );
		add_filter( 'woocommerce_order_has_status', array( $this, 'set_quote_ready_for_pay_now' ), 10, 3 );

		add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array( $this, 'add_pending_quote_to_valid_statuses' ), 20, 1 );

		// quote button to checkout.
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_order_as_quote' ) );

		add_action( 'woocommerce_order_status_ywraq-pending_to_processing', array( $this, 'send_woocommerce_processing_email' ), 10, 1 );
		add_action( 'woocommerce_order_status_ywraq-pending_to_on-hold', array( $this, 'send_woocommerce_on_hold_email' ), 10, 1 );

		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_payment_gateways' ), 20 );
	}


	/**
	 * Check if the quantity changed on cart.
	 *
	 * @param string $cart_item_key .
	 * @param int    $quantity      .
	 * @param int    $old_quantity  .
	 */
	public function coupon_validation( $cart_item_key, $quantity, $old_quantity ) {
		if ( $quantity < $old_quantity ) {
			$this->remove_quote_coupon();
		}
	}

	/**
	 * Remove the quote coupon
	 */
	public function remove_quote_coupon() {
		$coupon = WC()->session->get( 'request_quote_discount' );

		if ( $coupon ) {
			$cart_coupons = wc()->cart->get_applied_coupons();
			if ( $cart_coupons ) {
				foreach ( $cart_coupons as $cart_coupon ) {
					if ( $cart_coupon === $coupon->get_code() ) {
						wc_add_notice( esc_html__( 'The discount has been removed because you have changed the quote contents.', 'yith-woocommerce-request-a-quote' ), 'error' );
						wc()->cart->remove_coupon( $cart_coupon );
					}
				}
			}
		}
	}

	/**
	 * Save Order as Quote
	 *
	 * @param int $order_id .
	 *
	 * @throws exception .
	 */
	public function save_order_as_quote( $order_id ) {

		if ( isset( $_REQUEST['ywraq_checkout_quote'] ) && yith_plugin_fw_is_true( $_REQUEST['ywraq_checkout_quote'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$order = wc_get_order( $order_id );

			$raq = array(
				'user_name'     => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
				'user_email'    => $order->get_billing_email(),
				'user_message'  => $order->get_customer_note(),
				'from_checkout' => 'yes',
			);

			$order->update_meta_data( 'ywraq_customer_name', $raq['user_name'] );
			$order->update_meta_data( 'ywraq_customer_email', $raq['user_email'] );
			$order->update_meta_data( 'ywraq_customer_message', $raq['user_message'] );
			$order->update_meta_data( '_ywraq_from_checkout', 1 );
			$order->update_meta_data( '_ywraq_pay_quote_now', apply_filters( 'ywraq_pay_quote_now_value_on_save_order', 1, $order_id ) );

			$order->set_status( 'ywraq-new' );

			$this->add_order_meta( $order, array() );

			WC()->session->set( 'raq_new_order', $order_id );

			$order->save();
			do_action( 'send_raq_mail', $raq );
			do_action( 'send_raq_customer_mail', $raq );

			WC()->cart->empty_cart( true );
			WC()->cart->persistent_cart_destroy();
			$order->add_order_note( esc_html__( 'This quote has been submitted from the checkout page.', 'yith-woocommerce-request-a-quote' ) );

			do_action( 'ywraq_after_create_order_from_checkout', $raq, $order );
			if ( ! is_ajax() ) {
				wp_safe_redirect(
					apply_filters(
						'woocommerce_checkout_no_payment_needed_redirect',
						YITH_Request_Quote()->get_redirect_page_url(
							array(
								'hidem' => 1,
								'order' => $order->get_id(),
							)
						),
						$order
					)
				);
				exit;
			}

			wp_send_json(
				array(
					'result'   => 'success',
					'redirect' => apply_filters(
						'woocommerce_checkout_no_payment_needed_redirect',
						YITH_Request_Quote()->get_redirect_page_url(
							array(
								'hidem' => 1,
								'order' => $order->get_id(),
							)
						),
						$order
					),
				)
			);
			exit;
		}
	}

	/**
	 * If the order is a quote in pending can be paid
	 *
	 * @param bool     $response .
	 * @param WC_Order $order    .
	 * @param string   $status   .
	 *
	 * @return bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function set_quote_ready_for_pay_now( $response, $order, $status = '' ) {

		if ( yith_plugin_fw_is_true( $order->get_meta( '_ywraq_pay_quote_now' ) ) && isset( $_GET['pay_for_order'] ) &&
		     in_array( $order->get_status(), array(
			     'ywraq-pending',
			     'pending',
			     'ywraq-accepted'
		     ), true ) ) {

			$response = true;
		}

		return $response;
	}


	/**
	 * Fix the price during the creation of order.
	 *
	 * If a product on list has price empty, return 0.
	 *
	 * @param float      $price   .
	 * @param WC_Product $product .
	 *
	 * @return float
	 * @since  2.0.7
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function get_price( $price, $product ) {
		if ( defined( 'DOING_CREATE_RAQ_ORDER' ) ) {
			$price = empty( $price ) ? 0 : $price;
		}

		return $price;
	}


	/**
	 * Save billing and shipping in the order
	 *
	 * @param int      $order_id .
	 * @param array    $data     .
	 * @param WC_Order $order    .
	 */
	public function checkout_save_billing_shipping( $order_id, $data, $order ) {

		$was_raq = yit_get_prop( $order, 'ywraq_raq' );

		if ( $was_raq ) {

			foreach ( $data as $key => $value ) {
				if ( is_callable( array( $order, "set_{$key}" ) ) ) {
					$order->{"set_{$key}"}( $value );
					// Store custom fields prefixed with wither shipping_ or billing_. This is for backwards compatibility with 2.6.x.
				} elseif ( 0 === stripos( $key, 'billing_' ) || 0 === stripos( $key, 'shipping_' ) ) {
					$order->update_meta_data( '_' . $key, $value );
				}
			}

			$order->save();

		}

	}

	/**
	 * Manage the checkout fields when a quote is accepted. Override the shipping or billing info in the checkout page.
	 *
	 * Called by the hook 'template_redirect'.
	 *
	 * @return void
	 * @since 1.6.3
	 */
	public function checkout_fields_manage() {

		if ( ! is_checkout() ) {
			return;
		}

		$order_id = $this->get_current_order_id();

		if ( ! $order_id ) {
			return;
		}

		$order                                     = wc_get_order( $order_id );
		$checkout_info                             = yit_get_prop( $order, '_ywraq_checkout_info', true );
		$this->order_payment_info['order_id']      = $order_id;
		$this->order_payment_info['checkout_info'] = apply_filters( 'ywraq_override_checkout_fields', $checkout_info );
		$ywraq_lock_editing                        = yit_get_prop( $order, '_ywraq_lock_editing', true );

		if ( 'both' === $this->order_payment_info['checkout_info'] || 'billing' === $this->order_payment_info['checkout_info'] ) {
			foreach ( WC()->countries->get_address_fields( '', 'billing_' ) as $key => $value ) {
				$this->order_payment_info[ $key ] = get_post_meta( $order_id, '_' . $key, true );
				add_filter( 'woocommerce_customer_get_' . $key, array( $this, 'checkout_fields_override' ) );
			}

			if ( apply_filters( 'ywraq_lock_editing_billing', true ) && yith_plugin_fw_is_true( $ywraq_lock_editing ) ) {
				add_filter( 'yith_ywraq_frontend_localize', array( $this, 'lock_billing' ) );
			}
		}

		if ( 'both' === $this->order_payment_info['checkout_info'] || 'shipping' === $this->order_payment_info['checkout_info'] ) {
			foreach ( WC()->countries->get_address_fields( '', 'shipping_' ) as $key => $value ) {
				$this->order_payment_info[ $key ] = get_post_meta( $order_id, '_' . $key, true );
				add_filter( 'woocommerce_customer_get_' . $key, array( $this, 'checkout_fields_override' ) );
			}

			if ( apply_filters( 'ywraq_lock_editing_shipping', true ) && yith_plugin_fw_is_true( $ywraq_lock_editing ) ) {
				add_filter( 'yith_ywraq_frontend_localize', array( $this, 'lock_shipping' ) );
				add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
			}
		}
	}

	/**
	 * Called by WC_Customer filter for change the field in the checkout page
	 *
	 * @param mixed $value .
	 *
	 * @return mixed
	 * @since 1.7.0
	 */
	public function checkout_fields_override( $value ) {
		$current_filter = current_filter();
		$key            = str_replace( 'woocommerce_customer_get_', '', $current_filter );
		if ( isset( $this->order_payment_info[ $key ] ) ) {
			return $this->order_payment_info[ $key ];
		}

		return $value;
	}

	/**
	 * If the customer cancel the order empty the cart
	 *
	 * @param int $order_id .
	 */
	public function empty_cart( $order_id ) {
		if ( $this->is_quote( $order_id ) && ! is_admin() ) {
			WC()->cart->empty_cart();
			$order = wc_get_order( $order_id );
			if ( $order && $this->is_quote( $order_id ) ) {
				$order->update_status( 'ywraq-accepted' );
			}

			WC()->session->set( 'order_awaiting_payment', 0 );
			WC()->session->set( 'chosen_shipping_methods', array() );
		}
	}

	/**
	 * Save the quote in backend
	 *
	 * @param WC_Order $order .
	 */
	public function save_quote( $order ) {

		if ( ! isset( $_POST['yit_metaboxes'] ) || ! isset( $_POST['yit_metaboxes']['_ywraq_safe_submit_field'] ) || empty( $_POST['yit_metaboxes']['ywraq_customer_email'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		$metaboxes = $_REQUEST['yit_metaboxes']; //phpcs:ignore


		yit_set_prop( $order, $metaboxes );
	}

	/**
	 * Save new quote from backend
	 *
	 * @param WC_Order $order .
	 *
	 * @since    1.7.0
	 * @internal param $post
	 * @internal param $post_id
	 */
	public function save_new_quote( $order ) {

		if ( empty( $_REQUEST['yit_metaboxes'] ['ywraq_customer_name'] ) || empty( $_REQUEST['yit_metaboxes']['ywraq_customer_email'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$billing_name  = $order->get_billing_first_name();
		$billing_email = $order->get_billing_email();
		$metaboxes     = $_REQUEST['yit_metaboxes']; //phpcs:ignore
		$props         = array( 'ywraq_raq' => 'yes' );

		if ( $ov_field = apply_filters( 'ywraq_override_order_billing_fields', true ) ) {
			$props['_billing_first_name'] = empty( $billing_name ) ? $metaboxes['ywraq_customer_name'] : $billing_name;
			if ( is_email( $metaboxes['ywraq_customer_email'] ) && empty( $billing_email ) ) {
				$props['_billing_email'] = $metaboxes['ywraq_customer_email'];
			}
		}


		yit_set_prop( $order, $props );
	}

	/**
	 * Set the new status of an order to New Quote Request status
	 *
	 * @throws WC_Data_Exception .
	 * @since 1.7.0
	 */
	public function set_new_quote() {
		global $post;

		$request = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $request['new_quote'] ) && $request['new_quote'] && 'shop_order' === $request['post_type'] ) {

			$order = wc_get_order( $post->ID );
			$order->set_status( 'ywraq-new' );

			// compatibility for WooCommerce 3.3 .
			if ( version_compare( '3.3.0', WC()->version, '<=' ) && $order->get_date_created() === null ) {
				$order->set_date_created( current_time( 'timestamp', true ) );
			}

			yit_set_prop( $order, array( '_ywraq_pdf_crypt' => true ) );
			$order->save();
			wp_cache_set( 'order-' . $order->get_id(), $order, 'orders' );
		}

	}

	/**
	 * Add post meta _cart_hash to the order in waiting payment
	 *
	 * @param string $value string .
	 *
	 * @return string
	 * @since 1.3.0
	 */
	public function set_cart_hash( $value ) {
		$order_id = $this->get_current_order_id();
		$order    = wc_get_order( $order_id );

		if ( $order_id && $this->is_quote( $order_id ) ) {
			$hash = method_exists( WC()->cart, 'get_cart_hash' ) ? WC()->cart->get_cart_hash() : md5( wp_json_encode( wc_clean( WC()->cart->get_cart_for_session() ) ) . WC()->cart->total );
			yit_save_prop( $order, 'cart_hash', $hash, false, true );
		}

		return $value;
	}

	/**
	 * Add fee into cart after that the request was accepted
	 *
	 * @return void
	 * @since 1.3.0
	 */
	public function add_cart_fee() {
		$fees = WC()->session->get( 'request_quote_fee' );
		if ( $fees && ( ! defined( 'YITH_WCDP_PROCESS_SUBORDERS' ) || ! YITH_WCDP_PROCESS_SUBORDERS ) ) {
			foreach ( $fees as $fee ) {
				WC()->cart->add_fee( $fee->get_name(), $fee->get_total(), (bool) $fee->get_tax_status(), $fee->get_tax_class() );
			}
		}
	}

	/**
	 * Filter the wpnonce
	 *
	 * @param int    $uid    .
	 * @param string $action .
	 *
	 * @return string
	 * @since 1.3.0
	 */
	public function wpnonce_filter( $uid, $action ) {
		$action = strval( $action );
		if ( ! empty( $action ) && ( strpos( 'accept-request-quote-', $action ) || strpos( 'reject-request-quote-', $action ) ) ) {
			return '';
		}

		return $uid;
	}

	/**
	 * Return a $property defined in this class
	 *
	 * @param string $property .
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function __get( $property ) {
		$data = false;

		if ( isset( $this->_data[ $property ] ) ) {
			$data = $this->_data[ $property ];
		}

		return $data;
	}

	/**
	 * Load the quote page
	 *
	 * @since 1.0.0
	 */
	public function load_view_quote_page() {
		$this->view_quote();
	}


	/**
	 * Change the title of the endpoint.
	 *
	 * @param string $title .
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function load_view_quote_page_title( $title ) {
		global $wp;

		$view_quote = get_option( 'woocommerce_myaccount_view_quote_endpoint', 'view-quote' );
		$order_id   = ! empty( $wp->query_vars[ $view_quote ] ) ? $wp->query_vars[ $view_quote ] : 0;

		if ( ! $order_id ) {
			return $title;
		}

		/* translators: $%s quote number */

		return wp_kses_post( sprintf( esc_html__( 'Quote #%s', 'yith-woocommerce-request-a-quote' ), apply_filters( 'ywraq_quote_number', $order_id ) ) );
	}

	/**
	 * Show the quote detail
	 *
	 * @since 1.0.0
	 */
	public function view_quote() {
		global $wp;
		if ( ! is_user_logged_in() ) {
			wc_get_template( 'myaccount/form-login.php' );
		} else {
			$view_quote = get_option( 'woocommerce_myaccount_view_quote_endpoint', 'view-quote' );
			$order_id   = $wp->query_vars[ $view_quote ];

			wc_get_template(
				'myaccount/view-quote.php',
				array(
					'order_id'     => $order_id,
					'current_user' => get_user_by( 'id', get_current_user_id() ),
				),
				false,
				YITH_YWRAQ_TEMPLATE_PATH . '/'
			);
		}
	}

	/**
	 * Show the quote list
	 *
	 * @since   1.0.0
	 */
	public function view_quote_list() {
		wc_get_template( 'myaccount/my-quotes.php', null, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
	}

	/**
	 * Register Order Status
	 *
	 * @return void
	 * @since  1.0
	 * @author Emanuela Castorina
	 */
	public function register_order_status() {
		register_post_status(
			'wc-ywraq-new',
			array(
				'label'                     => esc_html__( 'New Quote Request', 'yith-woocommerce-request-a-quote' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'New Quote Request <span class="count">(%s)</span>', 'New Quote Requests <span class="count">(%s)</span>', 'yith-woocommerce-request-a-quote' ),
			)
		);

		register_post_status(
			'wc-ywraq-pending',
			array(
				'label'                     => esc_html__( 'Pending Quote', 'yith-woocommerce-request-a-quote' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Pending Quote <span class="count">(%s)</span>', 'Pending Quote <span class="count">(%s)</span>', 'yith-woocommerce-request-a-quote' ),
			)
		);

		register_post_status(
			'wc-ywraq-expired',
			array(
				'label'                     => esc_html__( 'Expired Quote', 'yith-woocommerce-request-a-quote' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Expired Quote <span class="count">(%s)</span>', 'Expired Quotes <span class="count">(%s)</span>', 'yith-woocommerce-request-a-quote' ),
			)
		);

		register_post_status(
			'wc-ywraq-accepted',
			array(
				'label'                     => esc_html__( 'Accepted Quote', 'yith-woocommerce-request-a-quote' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Accepted Quote <span class="count">(%s)</span>', 'Accepted Quote <span class="count">(%s)</span>', 'yith-woocommerce-request-a-quote' ),
			)
		);

		register_post_status(
			'wc-ywraq-rejected',
			array(
				'label'                     => esc_html__( 'Rejected Quote', 'yith-woocommerce-request-a-quote' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Rejected Quote <span class="count">(%s)</span>', 'Rejected Quote <span class="count">(%s)</span>', 'yith-woocommerce-request-a-quote' ),
			)
		);

	}

	/**
	 * Add Custom Order Status to WC_Order
	 *
	 * @param array $order_statuses_old .
	 *
	 * @return array
	 * @since    1.0
	 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @internal param $order_statuses
	 */
	public function add_custom_status_to_order_statuses( $order_statuses_old ) {
		$order_statuses['wc-ywraq-new']      = esc_html__( 'New Quote Request', 'yith-woocommerce-request-a-quote' );
		$order_statuses['wc-ywraq-pending']  = esc_html__( 'Pending Quote', 'yith-woocommerce-request-a-quote' );
		$order_statuses['wc-ywraq-expired']  = esc_html__( 'Expired Quote', 'yith-woocommerce-request-a-quote' );
		$order_statuses['wc-ywraq-accepted'] = esc_html__( 'Accepted Quote', 'yith-woocommerce-request-a-quote' );
		$order_statuses['wc-ywraq-rejected'] = esc_html__( 'Rejected Quote', 'yith-woocommerce-request-a-quote' );

		$request = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $request['new_quote'] ) && $request['new_quote'] && 'shop_order' === $request['post_type'] ) {
			$new_status = array_merge( $order_statuses, $order_statuses_old );
		} else {
			$new_status = array_merge( $order_statuses_old, $order_statuses );
		}

		return $new_status;
	}

	/**
	 * Get Quote Order Status
	 *
	 * @return array
	 */
	public function get_quote_order_status() {
		return array(
			'wc-ywraq-new'      => esc_html__( 'New Quote Request', 'yith-woocommerce-request-a-quote' ),
			'wc-ywraq-pending'  => esc_html__( 'Pending Quote', 'yith-woocommerce-request-a-quote' ),
			'wc-ywraq-expired'  => esc_html__( 'Expired Quote', 'yith-woocommerce-request-a-quote' ),
			'wc-ywraq-accepted' => esc_html__( 'Accepted Quote', 'yith-woocommerce-request-a-quote' ),
			'wc-ywraq-rejected' => esc_html__( 'Rejected Quote', 'yith-woocommerce-request-a-quote' ),
		);
	}

	/**
	 * Set custom status order editable
	 *
	 * @param bool     $editable .
	 * @param WC_Order $order    .
	 *
	 * @return bool
	 * @since  1.0
	 * @author Emanuela Castorina
	 */
	public function order_is_editable( $editable, $order ) {

		$accepted_statuses = apply_filters(
			'ywraq_quote_accepted_statuses_edit',
			array(
				'ywraq-new',
				'ywraq-accepted',
				'ywraq-pending',
				'ywraq-expired',
				'ywraq-rejected',
			)
		);

		if ( in_array( $order->get_status(), $accepted_statuses, true ) ) {
			return true;
		}

		return $editable;
	}

	/**
	 * Create order from Request a quote list
	 *
	 * @param array $raq .
	 *
	 * @return int
	 * @throws Exception .
	 * @since  1.0.0
	 * @author Emanuela Castorina
	 */
	public function create_order( $raq ) {

		if ( ! defined( 'DOING_CREATE_RAQ_ORDER' ) ) {
			define( 'DOING_CREATE_RAQ_ORDER', true );
		}

		$raq_content = $raq['raq_content'];

		if ( empty( $raq_content ) || 'yes' !== get_option( 'ywraq_enable_order_creation', 'yes' ) ) {
			return false;
		}

		if ( isset( $raq['customer_id'] ) ) {
			$customer_id = $raq['customer_id'];
		} else {
			$customer_id = get_current_user_id();
		}

		if ( class_exists( 'WC_Subscriptions_Coupon' ) ) {
			remove_filter( 'woocommerce_get_discounted_price', 'WC_Subscriptions_Coupon::apply_subscription_discount_before_tax', 10 );
			remove_filter( 'woocommerce_get_discounted_price', 'WC_Subscriptions_Coupon::apply_subscription_discount', 10 );
		}

		do_action( 'ywraq_before_create_order', $raq );

		// Ensure shipping methods are loaded early.
		WC()->shipping();

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		$args = array(
			'status'      => 'ywraq-new',
			'customer_id' => apply_filters( 'ywraq_customer_id', $customer_id ),
		);

		$order = wc_create_order( $args );

		$order_id = $order->get_id();

		yit_save_prop( $order, '_current_user', $customer_id, false, true );

		// Add order meta to new RAQ order.
		$this->add_order_meta( $order, $raq );

		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}

		$current_cart = WC()->session->get( 'cart' );

		$new_cart = WC()->cart;

		if ( ! is_null( $new_cart ) && ! $new_cart->is_empty() ) {
			$new_cart->empty_cart( true );
		}

		if ( ywraq_allow_raq_out_of_stock() ) {
			add_filter( 'woocommerce_variation_is_in_stock', '__return_true' );
			add_filter( 'woocommerce_product_is_in_stock', '__return_true' );
			add_filter( 'woocommerce_product_backorders_allowed', '__return_true' );
		}

		add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 9999 );

		foreach ( $raq_content as $item => $values ) {

			$new_cart_item_key = $new_cart->add_to_cart(
				$values['product_id'],
				$values['quantity'],
				( isset( $values['variation_id'] ) ? $values['variation_id'] : '' ),
				( isset( $values['variations'] ) ? $values['variations'] : '' ),
				$values
			);


			$new_cart = apply_filters( 'ywraq_add_to_cart_from_request', $new_cart, $values, $item, $new_cart_item_key );

		}

		remove_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 9999 );

		$new_cart->calculate_totals();  // fix tax.

		foreach ( $new_cart->get_cart() as $cart_item_key => $values ) {

			$args = array();

			$args['variation'] = ( ! empty( $values['variation'] ) ) ? $values['variation'] : array();

			if ( isset( $values['line_subtotal'] ) ) {
				$args['totals']['subtotal'] = $values['line_subtotal'];
			}

			if ( isset( $values['line_total'] ) ) {
				$args['totals']['total'] = $values['line_total'];
			}

			if ( isset( $values['line_subtotal_tax'] ) ) {
				$args['totals']['subtotal_tax'] = $values['line_subtotal_tax'];
			}

			if ( isset( $values['line_tax'] ) ) {
				$args['totals']['tax'] = $values['line_tax'];
			}

			if ( isset( $values['line_tax_data'] ) ) {
				$args['totals']['tax_data'] = $values['line_tax_data'];
			}

			$values['quantity'] = ( $values['quantity'] <= 0 ) ? 1 : $values['quantity'];

			$args = apply_filters( 'ywraq_cart_to_order_args', $args, $cart_item_key, $values, $new_cart );

			$item_id = $order->add_product(
				$values['data'],
				$values['quantity'],
				$args
			);


			do_action( 'ywraq_from_cart_to_order_item', $values, $cart_item_key, $item_id, $order );
		}

		$calculate_shipping = apply_filters( 'ywraq_calculate_shipping_from_request', get_option( 'ywraq_calculate_default_shipping_quote', 'no' ) );

		if ( 'yes' === $calculate_shipping ) {
			if ( $new_cart->needs_shipping() ) {

				$new_cart->calculate_shipping();
				$packages        = WC()->shipping->get_packages();
				$shipping_method = apply_filters( 'ywraq_filter_shipping_methods', WC()->session->get( 'chosen_shipping_methods' ) );

				// Store shipping for all packages.
				foreach ( $packages as $package_key => $package ) {
					if ( isset( $package['rates'][ $shipping_method [ $package_key ] ] ) ) {

						$item = new WC_Order_Item_Shipping();
						$item->set_shipping_rate( $package['rates'][ $shipping_method[ $package_key ] ] );
						$item_id = $item->save();

						if ( ! $item_id ) {
							/* translators: %d Error message */
							throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create the order. Please try again.', 'yith-woocommerce-request-a-quote' ), 404 ) );
						}

						$order->add_item( $item );
						// Allows plugins to add order item meta to shipping.
						do_action( 'woocommerce_add_shipping_order_item', $order_id, $item_id, $package_key );
					}
				}
			}
		}

		$order->save();

		$order->calculate_taxes();
		$order->calculate_totals();

		$new_cart->empty_cart( true );

		WC()->session->set( 'cart', $current_cart );
		WC()->cart->get_cart_from_session();
		WC()->cart->set_session();
		WC()->session->set( 'raq_new_order', $order_id );

		$order->update_status( 'ywraq-new' );

		do_action( 'ywraq_after_create_order', $order_id, $_POST, $raq ); //phpcs:ignore WordPress.Security.NonceVerification.Missing

		return $order_id;

	}

	/**
	 * Add the order meta to new RAQ order
	 *
	 * @param WC_order $order .
	 * @param array    $raq   .
	 *
	 * @throws exception .
	 * @author Andrea Grillo
	 */
	public function add_order_meta( $order, $raq ) {

		$attr                     = array();
		$order_id                 = $order->get_id();
		$attr['ywraq_raq_status'] = 'pending';
		$attr['ywraq_raq']        = 'yes';

		if ( isset( $raq['lang'] ) ) {
			$attr['wpml_language'] = $raq['lang'];
		}

		// since 1.8.0.
		$attr['_ywraq_version']   = YITH_YWRAQ_VERSION;
		$attr['_ywraq_pdf_crypt'] = apply_filters( 'ywraq_pdf_file_name_crypt', 'yes', $order_id );

		do_action( 'ywraq_add_order_meta', $order_id, $raq );

		$attr = apply_filters( 'ywraq_order_meta_list', $attr, $order_id, $raq );

		if ( 0 !== intval( get_option( 'ywraq_expired_time', 0 ) ) ) {
			$expire_time                  = time() + get_option( 'ywraq_expired_time', 0 ) * DAY_IN_SECONDS;
			$expire_date                  = apply_filters( 'ywraq_expire_date_format', date( 'Y-m-d', $expire_time ), $expire_time );
			$attr['_ywcm_request_expire'] = $expire_date;
		}

		if ( $attr ) {
			foreach ( $attr as $key => $item ) {
				$function = 'set' . $key;
				if ( is_callable( array( $order, $function ) ) ) {
					$order->$function( $item );
				} elseif ( '_order_comments' === $key && '' !== $item ) {
					$order->set_customer_note( $item );
				} else {
					$order->update_meta_data( $key, $item );
				}
			}
			$order->save();
		}
	}

	/**
	 * Add to cart the products in the request, add also a coupon with the discount applied
	 *
	 * @param int $order_id .
	 *
	 * @throws Exception .
	 * @since  1.0.0
	 * @author Emanuela Castorina
	 */
	public function order_accepted( $order_id ) {

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		// Clear current cart.
		WC()->cart->empty_cart( true );
		WC()->cart->get_cart_from_session();
		WC()->session->set( 'order_awaiting_payment', $order_id );
		WC()->cart->set_session();

		// Load the previous order - Stop if the order does not exist.
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		do_action( 'ywraq_before_order_accepted', $order_id );

		$order->update_status( 'pending' );

		$prop = array( 'ywraq_raq_status' => 'accepted' );

		add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 9999 );

		// Copy products from the order to the cart.
		foreach ( $order->get_items() as $item ) {

			$product_id   = (int) apply_filters( 'woocommerce_add_to_cart_product_id', $item->get_product_id() );
			$quantity     = $item->get_quantity();
			$variation_id = (int) $item->get_variation_id();
			$variations   = array();

			// todo:check 3.0.8.
			foreach ( $item['item_meta'] as $meta_name => $meta_value ) {
				if ( taxonomy_is_product_attribute( $meta_name ) ) {
					$variations[ $meta_name ] = $meta_value;
				} elseif ( meta_is_product_attribute( $meta_name, $meta_value[0], $product_id ) ) {
					$variations[ $meta_name ] = $meta_value;
				}
			}

			if ( function_exists( 'YITH_WCTM' ) ) {
				remove_filter( 'woocommerce_add_to_cart_validation', array( YITH_WCTM(), 'avoid_add_to_cart' ), 10 );
			}

			$cart_item_data = apply_filters( 'woocommerce_order_again_cart_item_data', array(), $item, $order );
			$cart_item_data = apply_filters( 'ywraq_order_cart_item_data', $cart_item_data, $item, $order );

			if ( $quantity ) {
				if ( 'yes' === get_option( 'woocommerce_prices_include_tax', 'no' ) ) {
					$cart_item_data['ywraq_price'] = ( $item['line_subtotal'] + $item['line_subtotal_tax'] ) / $quantity;
				} else {
					$cart_item_data['ywraq_price'] = ( $item['line_subtotal'] ) / $quantity;
				}
			}

			if ( ! apply_filters( 'ywraq_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations, $cart_item_data ) ) {
				continue;
			}

			// Add to cart validation.
			remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cart_validation' ), 10 );

			if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations, $cart_item_data ) ) {
				continue;
			}

			$meta_data = $item->get_meta_data();

			if ( ! empty( $meta_data ) ) {

				foreach ( $meta_data as $meta ) {
					$cart_item_data['meta'][] = $meta->get_data();
				}
			}

			WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations, $cart_item_data );
		}

		$fees = $order->get_fees();
		WC()->session->set( 'request_quote_fee', $fees );

		$tax_display    = $order->get_prices_include_tax( 'edit' );
		$order_discount = $order->get_total_discount( ! $tax_display );

		if ( $order_discount > 0 ) {

			$coupon       = new WC_Coupon( $this->label_coupon . '_' . $order_id );
			$wc_discounts = new WC_Discounts( $order );
			$valid        = $wc_discounts->is_coupon_valid( $coupon );
			$valid        = is_wp_error( $valid ) ? false : $valid;

			$order_items = array();
			if ( apply_filters( 'ywraq_bind_coupon', false ) ) {
				foreach ( $order->get_items() as $item ) {
					$order_items[] = $item->get_data()['product_id'];
				}
			}

			if ( $valid ) {
				$coupon->set_amount( $order_discount );
				$coupon->set_product_ids( $order_items );
			} else {
				$args = array(
					'id'             => false,
					'discount_type'  => 'fixed_cart',
					'amount'         => $order_discount,
					'individual_use' => false,
					'usage_limit'    => '1',
					'product_ids'    => $order_items,
					'expiry_date'    => yit_get_prop( $order, '_ywcm_request_expire', true ),
				);

				$coupon->read_manual_coupon( $this->label_coupon . '_' . $order_id, $args );
			}


			$coupon->save();


			WC()->session->set( 'request_quote_discount', $coupon );
			WC()->cart->add_discount( $this->label_coupon . '_' . $order_id );
		}


		remove_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 9999 );

		do_action( 'ywraq_after_order_accepted', $order_id );

		if ( 'accepted' !== $order->get_meta( 'ywraq_raq_status' ) ) {
			do_action(
				'change_status_mail',
				array(
					'order'  => $order,
					'status' => 'accepted',
				)
			);
		}
		WC()->cart->calculate_totals();

		yit_save_prop( $order, $prop, false, false, true );

	}

	/**
	 * Add custom item meta in the cart session
	 *
	 * @param array $item_data .
	 * @param array $cart_item .
	 *
	 * @return array
	 */
	public function add_custom_item_meta( $item_data, $cart_item ) {
		if ( empty( $cart_item['meta'] ) ) {
			return $item_data;
		}

		foreach ( $cart_item['meta'] as $meta ) {

			$key_prefix = apply_filters( 'ywraq_meta_key_prefix', 'RAQ_' );

			if ( strpos( $meta['key'], $key_prefix ) === false ) {
				continue;
			}

			$item_data[] = array(
				'key'     => str_replace( $key_prefix, '', $meta['key'] ),
				'value'   => $meta['value'],
				'display' => '',
			);
		}

		return $item_data;
	}

	/**
	 * Add custom item meta in the cart session
	 *
	 * @param array  $item_product .
	 * @param string $key          .
	 * @param array  $cart_item    .
	 *
	 * @return void
	 */
	public function add_custom_item_meta_checkout( $item_product, $key, $cart_item ) {

		if ( ! empty( $cart_item['meta'] ) ) {

			foreach ( $cart_item['meta'] as $meta ) {
				$key_prefix = apply_filters( 'ywraq_meta_key_prefix', 'RAQ_' );
				if ( strpos( $meta['key'], $key_prefix ) === false ) {
					continue;
				}
				$item_product->add_meta_data( $meta['key'], $meta['value'] );
			}
		}

	}

	/**
	 * Removes the prefix before printing it on emails and pages
	 *
	 * @param string $html .
	 *
	 * @return string
	 */
	public function sanitize_raq_item_meta( $html ) {

		$key_prefix = apply_filters( 'ywraq_meta_key_prefix', 'RAQ_' );

		$html = str_replace( $key_prefix, '', $html );

		return $html;
	}

	/**
	 * Update the price in the cart session
	 *
	 * @param array $cart_item .
	 *
	 * @return array
	 * @internal param $cart_item_data
	 */
	public function set_new_product_price( $cart_item ) {
		if ( isset( $cart_item['ywraq_price'] ) ) {
			$cart_item['data']->set_price( $cart_item['ywraq_price'] );
		}

		return $cart_item;
	}

	/**
	 * Add actions to WC Order Editor
	 *
	 * @param array $actions .
	 *
	 * @return array
	 * @since  1.0.0
	 * @author Emanuela Castorina
	 */
	public function add_order_actions( $actions ) {
		$actions['ywraq-send-quote'] = esc_html__( 'Send the Quote', 'yith-woocommerce-request-a-quote' );

		return $actions;
	}

	/**
	 * Add metabox in the order editor
	 *
	 * @return  void
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function add_metabox() {

		$request = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post    = isset( $request['post'] ) ? $request['post'] : ( isset( $request['post_ID'] ) ? $request['post_ID'] : 0 );

		$is_quote = get_post_meta( $post, 'ywraq_raq', true );
		$post     = get_post( $post );

		$get          = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_new_quote = isset( $get['new_quote'] ) && $get['new_quote'];

		if ( ( $post && 'shop_order' === $post->post_type || ( isset( $get['post_type'] ) && 'shop_order' === $get['post_type'] ) ) && ( $is_new_quote || 'yes' === $is_quote ) ) {

			$args = require_once YITH_YWRAQ_DIR . 'plugin-options/metabox/ywraq-metabox-order.php';
			if ( ! function_exists( 'YIT_Metabox' ) ) {
				require_once YITH_YWRAQ_DIR . 'plugin-fw/yit-plugin.php';
			}
			$metabox = YIT_Metabox( 'yith-ywraq-metabox-order' );
			$metabox->init( $args );
		}

	}

	/**
	 * Add a button add to quote in the orders list
	 *
	 * @param string $view .
	 *
	 * @return string
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function show_add_new_quote( $view ) {

		$link = esc_url( admin_url( 'post-new.php?post_type=shop_order&new_quote=1' ) );

		/* translators: %s: url */
		wp_kses_post( printf( __( '<p><a href="%s" class="page-title-action">Add a new Quote</a></p>', 'yith-woocommerce-request-a-quote' ), esc_url( $link ) ) ); //phpcs:ignore

		return $view;

	}

	/**
	 * Send the quote to the customer or create a psd preview
	 *
	 * @param int     $post_id .
	 * @param WP_Post $post    .
	 *
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function raq_order_action( $post_id, $post = null ) {

		$order = wc_get_order( $post_id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		if ( 'ywraq-new' === $order->get_status() ) {
			update_post_meta( $post_id, 'ywraq_raq', 'yes' );
		}

		if ( $this->quote_sent || ! isset( $_POST['yit_metaboxes'] ) || ! isset( $_POST['yit_metaboxes']['_ywraq_safe_submit_field'] ) || empty( $_POST['yit_metaboxes']['_ywraq_safe_submit_field'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		$this->quote_sent = true;

		switch ( $_POST['yit_metaboxes']['_ywraq_safe_submit_field'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			case 'send_quote':
				$order->update_status( 'ywraq-pending' );
				yit_save_prop( $order, '_ywraq_author', get_current_user_id() );

				if ( get_option( 'ywraq_enable_pdf', 'yes' ) ) {
					do_action( 'create_pdf', $post_id );
				}

				do_action( 'send_quote_mail', $post_id );
				break;
			case 'create_preview_pdf':
				do_action( 'create_pdf', $post_id );
				break;
			default:
		}
	}

	/**
	 * Change the status of the quote
	 *
	 * @param int $post_id .
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function change_status_quote( $post_id ) {

		if ( ! isset( $_POST['yit_metaboxes'] ) || ! isset( $_POST['yit_metaboxes']['_ywraq_safe_submit_field'] ) || 'send_quote' !== $_POST['yit_metaboxes']['_ywraq_safe_submit_field'] || $this->status_changed ) {//phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		$order = wc_get_order( $post_id );

		if ( $order ) {
			$order->update_status( 'ywraq-pending' );
			// check if the status has changed.
			$this->status_changed = true;
		}

	}

	/**
	 * Remove Quotes from Order query
	 *
	 * @param array $args .
	 *
	 * @return array
	 * @since  1.0.0
	 * @author Emanuela Castorina
	 */
	public function my_account_my_orders_query( $args ) {
		$args['status'] = array_keys( array_diff( wc_get_order_statuses(), $this->get_quote_order_status() ) );

		return $args;
	}

	/**
	 * Add quotes list to my-account page
	 *
	 * @return  void
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function my_account_my_quotes() {
		wc_get_template( 'myaccount/my-quotes.php', null, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
	}

	/**
	 * Switch a ajax call
	 *
	 * @return  void
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function ajax() {
		$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $posted['ywraq_order_action'] ) ) {
			if ( method_exists( $this, 'ajax_' . $posted['ywraq_order_action'] ) ) {
				$s = 'ajax_' . $posted['ywraq_order_action'];
				$this->$s();
			}
		}
	}

	/**
	 * Ajax Mail Sent for Created Order
	 */
	public function ajax_mail_sent_order_created() {

		if ( 'no' === get_option( 'ywraq_enable_order_creation', 'yes' ) ) {
			if ( apply_filters( 'ywraq_clear_list_after_send_quote', true ) ) {
				YITH_Request_Quote()->clear_raq_list();
			}
			yith_ywraq_add_notice( ywraq_get_message_after_request_quote_sending(), 'success' );
			wp_send_json(
				array(
					'rqa_url' => YITH_Request_Quote()->get_redirect_page_url(),
				)
			);
			exit;
		}

		$order_id = isset( $_COOKIE['yith_ywraq_order_id'] ) ? intval( $_COOKIE['yith_ywraq_order_id'] ) : 0;

		if ( ! $order_id ) {
			yith_ywraq_add_notice( esc_html__( 'An error occurred creating your request. Please try again.', 'yith-woocommerce-request-a-quote' ), 'error' );
			wp_send_json(
				array(
					'rqa_url' => YITH_Request_Quote()->get_raq_page_url(),
				)
			);
		}
		$request = $_REQUEST;//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $request['current_user_id'] ) ) {
			$order = wc_get_order( $order_id );
			yit_save_prop( $order, '_customer_id', $request['current_user_id'] );
		}

		wc_setcookie( 'yith_ywraq_order_id', 0, time() - HOUR_IN_SECONDS );
		yith_ywraq_add_notice( ywraq_get_message_after_request_quote_sending( $order_id ), 'success' );

		if ( apply_filters( 'ywraq_clear_list_after_send_quote', true ) ) {
			YITH_Request_Quote()->clear_raq_list();
		}

		wp_send_json(
			array(
				'rqa_url' => apply_filters( 'ywraq_redirect_page_after_send_email', YITH_Request_Quote()->get_redirect_page_url(), $order_id ),
			)
		);
	}

	/**
	 * Called to create an order from a request sent with forms like contact form 7 or gravity form
	 *
	 * @param bool $mail_sent_order_created .
	 *
	 * @throws Exception .
	 * @since  1.0.0
	 * @author Emanuela Castorina
	 */
	public function ajax_create_order( $mail_sent_order_created = true ) {

		$other_email_content = '';
		$current_customer_id = 0;

		if ( is_user_logged_in() ) {
			$current_customer_id = get_current_user_id();
		} elseif ( $current_customer = get_user_by( 'email', $_POST['your-email'] ) ) {
			$current_customer_id = $current_customer->ID;
		}

		$args = array(
			'other_email_content' => $other_email_content,
			'raq_content'         => YITH_Request_Quote()->get_raq_return(),
			'customer_id'         => $current_customer_id,
		);

		$args = apply_filters( 'ywraq_ajax_create_order_args', $args, $_POST ); //phpcs:ignore WordPress.Security.NonceVerification.Missing

		$new_order = $this->create_order( $args );

		if ( $new_order ) {
			wc_setcookie( 'yith_ywraq_order_id', $new_order, 0 );
		}

		if ( $mail_sent_order_created ) {
			$this->ajax_mail_sent_order_created();
		}
	}

	/**
	 * Change the status of Quote in 'accepted'
	 *
	 * @return  void
	 * @throws WC_Data_Exception Data exception .
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function ajax_accept_order() {
		$posted = $_POST;//phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $posted['order_id'] ) ) {
			$message = __( 'An error occurred. Please, contact site administrator', 'yith-woocommerce-request-a-quote' );
			$result  = array(
				'result'  => 0,
				'message' => $message,
			);
		} else {
			$accepted_page_id = YITH_Request_Quote()->get_accepted_page();
			$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
			$cart_page_id     = get_option( 'woocommerce_cart_page_id' );

			$order_id = $posted['order_id'];
			$order    = wc_get_order( $order_id );

			if ( $order ) {

				if ( $accepted_page_id === $checkout_page_id || $accepted_page_id === $cart_page_id ) {
					$this->order_accepted( $order_id );
					$url    = $accepted_page_id === $cart_page_id ? wc_get_cart_url() : wc_get_checkout_url();
					$result = array(
						'result'  => 1,
						'rqa_url' => apply_filters( 'ywraq_accepted_redirect_url', $url, $order_id ),
					);

				} else {
					$this->accept_reject_order( 'accepted', $order_id );

					$result = array(
						'result'  => 1,
						'rqa_url' => apply_filters( 'ywraq_accepted_redirect_url', get_permalink( $accepted_page_id ), $order_id ),
					);
				}
			} else {
				$message = esc_html__( 'An error occurred. Please, contact site administrator', 'yith-woocommerce-request-a-quote' );
				$result  = array(
					'result'  => 0,
					'message' => $message,
				);
			}
		}

		wp_send_json(
			$result
		);
	}

	/**
	 * Reject the quote
	 *
	 * @return  void
	 * @throws  WC_Data_Exception Exception .
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function ajax_reject_order() {
		$order_id = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $order_id ) ) {
			$message = esc_html__( 'An error occurred. Please, contact site administrator', 'yith-woocommerce-request-a-quote' );
			$result  = array(
				'result'  => 0,
				'message' => $message,
			);
		} else {
			$this->accept_reject_order( 'rejected', $order_id );

			$result = array(
				'result'  => 1,
				'status'  => esc_html__( 'rejected', 'yith-woocommerce-request-a-quote' ),
				'rqa_url' => '',
			);
		}

		wp_send_json(
			$result
		);
	}

	/**
	 * Change the status of the quote
	 *
	 * @param string $status   .
	 * @param int    $order_id .
	 *
	 * @throws WC_Data_Exception Exception .
	 */
	public function accept_reject_order( $status, $order_id ) {

		$order = wc_get_order( $order_id );
		$order->update_meta_data( 'ywraq_raq_status', $status );

		// return if the status is the same.
		if ( $order->get_status() === 'ywraq-' . $status ) {
			return;
		}

		$order->update_status( 'ywraq-' . $status );
		$args = array(
			'order'  => $order,
			'status' => $status,
		);

		if ( isset( $_REQUEST['reason'] ) ) {          //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$reason = wc_clean( $_REQUEST['reason'] ); //phpcs:ignore
			$order->set_customer_note( $reason );
			$args['reason'] = $reason;
		}

		do_action( 'change_status_mail', $args );

		$order->save();
	}

	/**
	 * Delete post meta ywraq_status
	 *
	 * @param int $order_id .
	 *
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function raq_processed( $order_id ) {
		$order = wc_get_order( $order_id );
		yit_delete_prop( $order, 'ywraq_raq_status' );
	}

	/**
	 * Change the label of coupon
	 *
	 * @param string $string .
	 * @param string $code   .
	 *
	 * @return string
	 * @since    1.0.0
	 * @author   Emanuela Castorina
	 *
	 * @internal param $coupon
	 */
	public function label_coupon( $string, $code ) {

		if ( ! $code instanceof WC_Coupon ) {
			return $string;
		}

		// change the label if the order is generated from a quote.
		if ( strpos( $this->label_coupon, $code->get_code() ) !== false ) {
			return $string;
		}

		$label = esc_html( esc_html__( 'Discount:', 'yith-woocommerce-request-a-quote' ) );

		return $label;
	}

	/**
	 * Manage the request from the email of customer
	 *
	 * @return  void
	 * @throws  WC_Data_Exception Exception .
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function change_order_status() {
		$request = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $request['raq_nonce'] ) || ! isset( $request['status'] ) || ! isset( $request['request_quote'] ) ) {
			return;
		}


		$status   = $request['status'];
		$order_id = $request['request_quote'];

		$order = wc_get_order( $request['request_quote'] );
		if ( ! $order ) {
			return;
		}
		$user_email = yit_get_prop( $order, 'ywraq_customer_email', true );

		$this->is_expired( $order_id );
		$current_status = $order->get_status();
		$status_name    = wc_get_order_status_name( $current_status );

		$args = array(
			'message' => '',
		);

		if ( ! ywraq_verify_token( $request['raq_nonce'], 'accept-request-quote', $order_id, $user_email ) && ! ywraq_verify_token( $request['raq_nonce'], 'reject-request-quote', $order_id, $user_email ) ) {
			$args['message']    = sprintf( __( 'You do not have permission to read the quote', 'yith-woocommerce-request-a-quote' ), $order_id );
			$this->args_message = $args;

			return;
		}

		if ( 'accepted' === $status && ywraq_verify_token( $request['raq_nonce'], 'accept-request-quote', $order_id, $user_email ) ) {

			$statuses = array(
				'ywraq-pending',
				'pending',
				'ywraq-accepted',
			);

			if ( isset( $request['pay_for_order'] ) && $request['pay_for_order'] && in_array( $current_status, $statuses, true ) ) {

				if ( $status !== $order->get_meta( 'ywraq_raq_status' ) ) {
					do_action( 'change_status_mail', array( 'order' => $order, 'status' => 'accepted' ) );
					$order->update_status( 'pending' );
					$order->update_meta_data( 'ywraq_raq_status', $status );
					$order->save();
				}

				return;
			}


			if ( in_array( $current_status, $statuses, true ) ) {
				$accepted_page_id = YITH_Request_Quote()->get_accepted_page();
				if ( YITH_Request_Quote()->enabled_checkout() ) {
					$this->order_accepted( $order_id );
				} else {
					$this->accept_reject_order( 'accepted', $order_id );
				}

				$redirect = get_permalink( $accepted_page_id );

				wp_safe_redirect( apply_filters( 'ywraq_accepted_redirect_url', $redirect, $order_id ) );
				wp_safe_redirect( $redirect );

				exit;
			} else {
				switch ( $current_status ) {
					case 'ywraq-rejected':
						/* translators: %d order id */
						$args['message'] = sprintf( esc_html__( 'Quote n. %d has been rejected and is not available', 'yith-woocommerce-request-a-quote' ), $order_id );
						break;
					case 'ywraq-expired':
						/* translators: %d order id */
						$args['message'] = sprintf( esc_html__( 'Quote n. %d has expired and is not available', 'yith-woocommerce-request-a-quote' ), $order_id );
						break;
					default:
						/* translators: %d order id */
						$args['message'] = sprintf( esc_html__( 'Quote n. %d can\'t be accepted because its status is: %s', 'yith-woocommerce-request-a-quote' ), $order_id, $status_name );
						break;
				}
			}
		} else {
			if ( 'ywraq-rejected' === $current_status && 'rejected' === $status ) {
				/* translators: %d order id */
				$args['message'] = sprintf( apply_filters( 'ywraq_rejected_quote_message', esc_html__( 'Quote n. %d has been rejected', 'yith-woocommerce-request-a-quote' ) ), $order_id );
			} elseif ( 'ywraq-expired' === $current_status ) {
				/* translators: %d order id */
				$args['message'] = sprintf( esc_html__( 'Quote n. %d has expired and is not available', 'yith-woocommerce-request-a-quote' ), $order_id );
			} elseif ( 'ywraq-pending' !== $current_status && 'pending' !== $current_status ) {
				/* translators: %d order id */
				$args['message'] = sprintf( esc_html__( 'Quote n. %d can\'t be rejected because its status is: %s', 'yith-woocommerce-request-a-quote' ), $order_id, $status_name );
			} else {
				if ( ! isset( $request['raq_confirm'] ) && ! isset( $request['confirm'] ) && 'rejected' === $status && ywraq_verify_token( $request['raq_nonce'], 'reject-request-quote', $order_id, $user_email ) ) {
					$args = array(
						'status'        => $status,
						'raq_nonce'     => $request['raq_nonce'],
						'request_quote' => $order_id,
						'raq_confirm'   => 'no',
					);

					wp_safe_redirect( add_query_arg( $args, YITH_Request_Quote()->get_raq_page_url() ) );

					exit;
				} else {

					if ( ! isset( $request['confirm'] ) ) {
						$args = array(
							'status'    => 'rejected',
							'raq_nonce' => $request['raq_nonce'],
							'order_id'  => $request['request_quote'],
							'confirm'   => 'no',
						);

					} else {

						$this->accept_reject_order( 'rejected', $order_id );

						/* translators: %d order id */
						$args['message'] = sprintf( esc_html__( 'The quote n. %d has been rejected', 'yith-woocommerce-request-a-quote' ), $order_id );
					}
				}
			}
		}

		$this->args_message = $args;
	}

	/**
	 * Print message in Request a Quote after that function change_order_status is called
	 *
	 * @return  void
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function print_message() {
		wc_get_template( 'request-quote-message.php', $this->args_message, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
	}

	/**
	 * Manage coupon message
	 *
	 * @param string    $msg      .
	 * @param int       $msg_code .
	 * @param WC_Coupon $obj      .
	 *
	 * @return string
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function manage_coupon_message( $msg, $msg_code, $obj ) {

		if ( ! $this->get_current_order_id() ) {
			return $msg;
		}

		$msg = '';
		if ( 200 === $msg_code ) {
			$msg = esc_html__( 'Discount applied successfully.', 'yith-woocommerce-request-a-quote' );
		} elseif ( 201 === $msg_code ) {
			$msg = esc_html__( 'Discount removed successfully.', 'yith-woocommerce-request-a-quote' );
		}

		return $msg;
	}

	/**
	 * Check if an order is created from a request quote
	 *
	 * @param int $order_id .
	 *
	 * @return bool
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function is_quote( $order_id ) {

		$order = wc_get_order( $order_id );

		return yit_get_prop( $order, 'ywraq_raq', true ) === 'yes';
	}

	/**
	 * Check if an order is created from a request quote
	 *
	 * @param int $order_id .
	 *
	 * @return bool
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 *
	 */
	public function is_expired( $order_id ) {
		$order = wc_get_order( $order_id );


		if ( ! $order || 'ywraq-pending' !== $order->get_status() ) {
			return false;
		}

		$current_status = $order->get_status();
		$ex_opt         = yit_get_prop( $order, '_ywcm_request_expire', true );

		if ( 'ywraq-expired' === $current_status ) {
			return true;
		}

		// check if expired.
		if ( '' !== $ex_opt ) {
			$expired_data = strtotime( $ex_opt ) + ( 24 * 60 * 60 ) - 1;
			if ( $expired_data < time() ) {
				$order->update_status( 'ywraq-expired' );

				return true;
			}
		}

		return false;
	}

	/**
	 * Return the quote detail page
	 *
	 * @param int  $order_id .
	 * @param bool $admin    .
	 *
	 * @return string
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 */
	public function get_view_order_url( $order_id, $admin = false ) {
		if ( $admin ) {
			$view_order_url = admin_url( 'post.php?post=' . $order_id . '&action=edit' );
		} else {
			$view_quote     = get_option( 'woocommerce_myaccount_view_quote_endpoint', 'view-quote' );
			$view_order_url = wc_get_endpoint_url( $view_quote, $order_id, wc_get_page_permalink( 'myaccount' ) );
		}

		return apply_filters( 'ywraq_get_quote_order_url', $view_order_url, $order_id );
	}

	/**
	 * Set the shipping method as choosen
	 *
	 * @return void
	 * @since 1.4.4
	 */
	public function set_shipping_methods() {

		if ( isset( $_POST['shipping_method'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		$order_id = $this->get_current_order_id();

		if ( ! $order_id ) {
			return;
		}

		$shipping_items = $this->get_shipping_items();

		if ( ! empty( $shipping_items ) ) {

			foreach ( $shipping_items as $shipping_item ) {
				$chosen_shipping_methods[] = $shipping_item->get_method_id();
			}

			WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		}
	}

	/**
	 * Change cost to shipping
	 *
	 * @param array $rates .
	 *
	 * @return mixed
	 * @since 1.4.4
	 */
	public function set_package_rates( $rates ) {

		$order_id = $this->get_current_order_id();

		if ( ! $order_id ) {
			return $rates;
		}

		$order = wc_get_order( $order_id );

		if ( yit_get_prop( $order, 'ywraq_raq', true ) !== 'yes' ) {
			return $rates;
		}

		$override_shipping = yit_get_prop( $order, '_ywraq_disable_shipping_method', true );
		$override_shipping = '' !== $override_shipping ? $override_shipping : 1;
		$new_rates         = array();
		$shipping_items    = $this->get_shipping_items();

		if ( apply_filters( 'ywraq_override_shipping_method', ywraq_is_true( $override_shipping ) ) ) {

			$new_rates   = array();
			$cost        = 0;
			$label       = '';
			$method_id   = '';
			$taxes_value = array();
			if ( $shipping_items ) {
				foreach ( $shipping_items as $shipping_item ) {
					$method_id = $shipping_item->get_method_id();
					$cost      += $shipping_item->get_total();
					$comma     = empty( $label ) ? '' : ', ';
					$label     .= $comma . $shipping_item->get_name();
					$shipping_item['name'];

					$tt = $shipping_item->get_taxes();
					if ( ! empty( $tt['total'] ) ) {
						foreach ( $tt['total'] as $key => $t ) {
							if ( isset( $taxes_value[ $key ] ) ) {
								$taxes_value[ $key ] = $taxes_value[ $key ] + $t;
							} else {
								$taxes_value[ $key ] = $t;
							}
						}
					}
				}

				foreach ( $rates as $key => $rate ) {
					if ( $rate->id === $method_id || $rate->method_id === $method_id || empty( $method_id ) ) {
						$new_rates[ $key ]        = $rates[ $key ];
						$new_rates[ $key ]->cost  = $cost;
						$new_rates[ $key ]->label = $label;
						$taxes_value              = array_filter( $taxes_value );
						if ( is_array( $taxes_value ) && ! empty( $taxes_value ) ) {
							$new_rates[ $key ]->taxes = $taxes_value;
						}

						break;
					}
				}
			}
		} else {
			$new_rates = $rates;
			foreach ( $rates as $key => $rate ) {
				if ( $shipping_items ) {
					foreach ( $shipping_items as $shipping_item ) {
						$method_id = $shipping_item->get_method_id();
						if ( $rate->method_id === $method_id ) {
							if ( isset( $new_rates[ $key ] ) ) {
								if ( get_option( 'ywraq_sum_multiple_shipping_costs' ) === 'yes' ) {
									$new_rates[ $key ]->cost  += $shipping_item['cost'];
									$new_rates[ $key ]->label .= ',' . $shipping_item['name'];
								} else {
									$new_rates[ $key ]->cost  = $shipping_item['cost']; //$new_rates[ $key ]->cost += $shipping_item['cost']; (original code)
									$new_rates[ $key ]->label = $shipping_item['name']; //$new_rates[ $key ]->label .= ',' . $shipping_item['name']; (original code)
								}
								$new_rates[ $key ]->taxes = maybe_unserialize( $shipping_item['taxes'] ) + $new_rates[ $key ]->taxes;

							} else {
								$new_rates[ $key ]        = $rates[ $key ];
								$new_rates[ $key ]->cost  = $shipping_item['cost'];
								$new_rates[ $key ]->label = $shipping_item['name'];
								$new_rates[ $key ]->taxes = maybe_unserialize( $shipping_item['taxes'] );

							}
						}
					}
				}
			}
		}

		return $new_rates;
	}

	/**
	 * Return the shipping items of the order in awaiting payment
	 *
	 * @return array
	 * @since 1.4.4
	 */
	public function get_shipping_items() {

		$order_id = $this->get_current_order_id();

		if ( ! $order_id ) {
			return array();
		}

		$order          = wc_get_order( $order_id );
		$shipping_items = $order->get_items( 'shipping' );

		return $shipping_items;
	}

	/**
	 * Callable in frontend return the order-quote that is in the cart
	 *
	 * @return bool|mixed
	 */
	public function get_current_order_id() {

		if ( is_null( WC()->session ) ) {
			return 0;
		}
		$order_id = absint( WC()->session->get( 'order_awaiting_payment' ) );

		if ( $order_id && ! $this->is_quote( $order_id ) ) {
			return false;
		}

		return $order_id;
	}

	/**
	 * Add a variable to localize script to lock the shipping fields in the checkout page
	 *
	 * @param array $localize_args .
	 *
	 * @return mixed
	 * @since 1.6.3
	 */
	public function lock_billing( $localize_args ) {
		$localize_args['lock_billing'] = true;

		return $localize_args;
	}

	/**
	 * Add a variable to localize script to lock the shipping fields in the checkout page
	 *
	 * @param array $localize_args .
	 *
	 * @return mixed
	 * @since 1.6.3
	 */
	public function lock_shipping( $localize_args ) {
		$localize_args['lock_shipping'] = true;

		return $localize_args;
	}

	/**
	 * Check if the quote is in the cart
	 *
	 * @return void
	 * @since 1.6.3
	 */
	public function check_quote_in_cart() {
		if ( is_admin() ) {
			return;
		}
		$order = $this->get_current_order_id();
		if ( $order ) {
			add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 9999 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cart_validation' ), 10, 2 );
			add_filter( 'woocommerce_update_cart_validation', array( $this, 'cart_update_validation' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cart_remove_item' ) );
			add_filter( 'woocommerce_product_is_in_stock', '__return_true', 99 );
		} else {
			remove_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 9999 );
			remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cart_validation' ), 10 );
			remove_filter( 'woocommerce_update_cart_validation', array( $this, 'cart_update_validation' ), 10 );
			remove_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cart_remove_item' ) );
			remove_filter( 'woocommerce_product_is_in_stock', '__return_true', 99 );
		}
	}

	/**
	 * Disallow the add to cart when the order-quote is in the cart
	 *
	 * @param bool $result     .
	 * @param int  $product_id .
	 *
	 * @return bool
	 * @since 1.6.3
	 */
	public function cart_validation( $result, $product_id ) {
		$order_id = WC()->session->order_awaiting_payment;
		if ( $order_id && $this->is_quote( $order_id ) && 'no' === get_option( 'ywraq_allow_add_to_cart', 'no' ) ) {
			$result = false;
			wc_add_notice( esc_html__( 'It\'s not possible to add products to the cart since you have already accepted a quote.', 'yith-woocommerce-request-a-quote' ), 'error' );
		}

		return $result;
	}

	/**
	 * Disallow change the quantity in the cart when the order-quote is in the cart
	 *
	 * @param bool $result        .
	 * @param      $cart_item_key .
	 *
	 * @return bool
	 */
	public function cart_update_validation( $result, $cart_item_key ) {
		$order_id = WC()->session->order_awaiting_payment;
		if ( $order_id && $this->is_quote( $order_id ) && 'no' === get_option( 'ywraq_allow_add_to_cart', 'no' ) ) {
			$result = false;
			wc_add_notice( esc_html__( 'It\'s not possible change the quantity since you have already accepted a quote.', 'yith-woocommerce-request-a-quote' ), 'error' );
		}

		return $result;
	}

	/**
	 * Remove the "remove item buttom" in the cart when the order-quote is in the cart
	 *
	 * @param string $value .
	 *
	 * @return string
	 */
	public function cart_remove_item( $value ) {

		$order_id = WC()->session->order_awaiting_payment;
		if ( $order_id && $this->is_quote( $order_id ) && 'no' === get_option( 'ywraq_allow_add_to_cart', 'no' ) ) {
			$value = '';
		}

		return $value;
	}

	/**
	 * If the product is without price, for catalog sites
	 *
	 * @return bool
	 * @since 1.6.3
	 */
	function is_purchasable() {
		return true;
	}


	/**
	 * Set the order as a quote.
	 *
	 * @param int    $order_id    .
	 * @param string $status_from .
	 * @param string $status_to   .
	 *
	 * @throws
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function set_order_as_quote( $order_id, $status_from, $status_to ) {
		if ( 'ywraq-new' == $status_to ) {
			update_post_meta( $order_id, 'ywraq_raq', 'yes' );
			$o = wc_get_order( $order_id );
			$o->set_created_via( 'ywraq' );
			$o->save();
		}
	}

	/**
	 * add the pending quote status to valid status list for order
	 *
	 * @param array $statuses
	 *
	 * @return array
	 *
	 */
	public function add_pending_quote_to_valid_statuses( $statuses ) {

		$statuses[] = 'ywraq-pending';

		return $statuses;
	}

	/***
	 * send email when the quote status change from ywraq-pending to processing
	 *
	 * @param $order_id
	 */
	public function send_woocommerce_processing_email( $order_id ) {

		WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger( $order_id );
		WC()->mailer()->emails['WC_Email_New_Order']->trigger( $order_id );
	}

	/***
	 * send email when the quote status change from ywraq-pending to on-hold with PayPal gateway
	 *
	 * @param $order_id
	 */
	public function send_woocommerce_on_hold_email( $order_id ) {

		WC()->mailer()->emails['WC_Email_New_Order']->trigger( $order_id );
		WC()->mailer()->emails['WC_Email_Customer_On_Hold_Order']->trigger( $order_id );

	}

	/**
	 * @param array $gateways
	 *
	 * @return array
	 */
	public function available_payment_gateways( $gateways ) {

		$choose_gateways = get_option( 'ywraq_select_gateway', array() );
		$order_id        = ! is_null( WC()->session ) ? WC()->session->get( 'order_awaiting_payment' ) : false;

		if ( $order_id ) {

			$order = wc_get_order( $order_id );

			$is_raq = ! empty( $order ) && $order->get_id() ? $order->get_meta( 'ywraq_raq' ) : '';
			if ( 'yes' === $is_raq && ! empty( $choose_gateways ) ) {
				foreach ( $gateways as $key => $gateway ) {
					if ( ! in_array( $key, $choose_gateways ) ) {
						unset( $gateways[ $key ] );
					}
				}
			}
		}

		return $gateways;
	}
}

/**
 * Unique access to instance of YITH_YWRAQ_Order_Request class
 *
 * @return \YITH_YWRAQ_Order_Request
 */
function YITH_YWRAQ_Order_Request() {
	return YITH_YWRAQ_Order_Request::get_instance();
}

YITH_YWRAQ_Order_Request();
