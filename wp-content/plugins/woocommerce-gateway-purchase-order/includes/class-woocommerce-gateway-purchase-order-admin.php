<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Woocommerce_Gateway_Purchase_Order_Admin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Woocommerce_Gateway_Purchase_Order_Admin
 */
function Woocommerce_Gateway_Purchase_Order_Admin() {
	return Woocommerce_Gateway_Purchase_Order_Admin::instance();
}

/**
 * Main Woocommerce_Gateway_Purchase_Order_Admin Class
 *
 * @class Woocommerce_Gateway_Purchase_Order_Admin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Woocommerce_Gateway_Purchase_Order_Admin
 * @author Matty
 */
final class Woocommerce_Gateway_Purchase_Order_Admin {
	/**
	 * Woocommerce_Gateway_Purchase_Order_Admin The single instance of Woocommerce_Gateway_Purchase_Order_Admin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'display_purchase_order_number' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'display_purchase_order_number' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'display_purchase_order_number' ) );

		// Print Invoices/Packing Lists Integration
		add_action( 'wc_pip_after_body', array( $this, 'add_po_number_to_pip' ), 10, 4 );
	}

	/**
	 * Display the Purchase Order number on a Print Invoices/Packing lists output.
	 *
	 * @param string $type
	 * @param string $action
	 * @param object $document
	 * @param object $order
	 * @return void
	 */
	public function add_po_number_to_pip( $type, $action, $document, $order ) {
		if ( 'invoice' != $type ) {
			return;
		}

		$payment_method = $order->get_payment_method();

		if ( 'woocommerce_gateway_purchase_order' === $payment_method ) {
			$po_number = $order->get_meta( '_po_number', true );
			/* translators: Placeholder: %1$s - opening <strong> tag, %2$s - coupons count (used in order), %3$s - closing </strong> tag - %4$s - coupons list */
			printf( '<div class="purchase-order-number">' . __( '%1$sPurchase order number:%2$s %3$s', 'woocommerce-gateway-purchase-order' ) . '</div>', '<strong>', '</strong>', esc_html( $po_number ) );
		}
	}

	/**
	* Purchase order HTML output.
	* @access public
	* @since 1.0.0
	* @param $order
	* @return void
	*/
	public function display_purchase_order_number ( $order ) {
		$payment_method = $order->get_payment_method();

		if ( 'woocommerce_gateway_purchase_order' === $payment_method ) {
			$po_number = $order->get_meta( '_po_number', true );
			if ( '' != $po_number ) {
				if ( 'woocommerce_order_details_after_order_table' == current_filter() ) {
					echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">';
					echo '<li class="woocommerce-order-overview__purchase-order purchase-order">' . __( 'Purchase Order Number:', 'woocommerce-gateway-purchase-order' ) . '<strong>' . $po_number . '</strong></li>';
					echo '</ul>';
				} else {
					echo '<p class="form-field form-field-wide"><strong>' . __( 'Purchase Order Number:', 'woocommerce-gateway-purchase-order' ) . '</strong><h2>' . $po_number . '</h2></p>' . "\n";
				}
			}
		}
	}

	/**
	 * Main Woocommerce_Gateway_Purchase_Order_Admin Instance
	 *
	 * Ensures only one instance of Woocommerce_Gateway_Purchase_Order_Admin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Woocommerce_Gateway_Purchase_Order_Admin()
	 * @return Main Woocommerce_Gateway_Purchase_Order_Admin instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}
}
