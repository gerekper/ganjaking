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

		// Print Invoices/Packing Lists Integration.
		add_action( 'wc_pip_after_body', array( $this, 'add_po_number_to_pip' ), 10, 4 );
	}

	/**
	 * Display the Purchase Order number on a Print Invoices/Packing lists output.
	 *
	 * @param string   $type Type.
	 * @param string   $action Action.
	 * @param object   $document Document.
	 * @param WC_Order $order Order.
	 * @return void
	 */
	public function add_po_number_to_pip( $type, $action, $document, $order ) {
		if ( 'invoice' !== $type ) {
			return;
		}

		$payment_method = $order->get_payment_method();

		if ( 'woocommerce_gateway_purchase_order' === $payment_method ) {
			$po_number = $order->get_meta( '_po_number', true );
			/* translators: %s = Purchase order number */
			echo '<div class="purchase-order-number"><strong>' . esc_html( printf( __( 'Purchase order number: %s', 'woocommerce-gateway-purchase-order' ), $po_number ) ) . '</strong></div>';
		}
	}

	/**
	 * Purchase order HTML output.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param WC_Order $order Order.
	 * @return void
	 */
	public function display_purchase_order_number( $order ) {
		$payment_method = $order->get_payment_method();

		if ( 'woocommerce_gateway_purchase_order' === $payment_method ) {
			$po_number = $order->get_meta( '_po_number', true );
			if ( '' !== $po_number ) {
				if ( 'woocommerce_order_details_after_order_table' === current_filter() ) {
					echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">';
					/* Translators: %s = Purchase order number */
					echo '<li class="woocommerce-order-overview__purchase-order purchase-order">' . wp_kses_post( sprintf( __( 'Purchase Order Number: %s', 'woocommerce-gateway-purchase-order' ), '<strong>' . esc_html( $po_number ) . '</strong>' ) ) . '</li>';
					echo '</ul>';
				} else {
					/* Translators: %s = Purchase order number */
					echo '<p class="form-field form-field-wide"><strong>' . wp_kses_post( sprintf( __( 'Purchase Order Number: %s', 'woocommerce-gateway-purchase-order' ), '<h2>' . esc_html( $po_number ) . '</h2>' ) ) . '</strong></p>' . "\n";
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
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
