<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compatibility class for PIP.
 */
class WC_Pip_Compat {
	/**
	 * Constructor
	 *
	 * @since 3.6.13
	 */
	public function __construct() {
		add_action( 'wc_pip_after_body', array( $this, 'add_shipping_addresses_to_pip' ), 10, 4 );
		add_filter( 'wc_pip_document_show_shipping_address', array( $this, 'maybe_show_top_shipping_from_pip' ), 10, 3 );
	}

	/**
	 * Add multiple addresses at the bottom of the order invoice.
	 *
	 * @since 3.6.13
	 * @param string           $type document type.
	 * @param string           $action current action running on Document.
	 * @param \WC_PIP_Document $document document object.
	 * @param \WC_Order        $order order object.
	 * @return void
	 */
	public function add_shipping_addresses_to_pip( $type, $action, $document, $order ) {
		global $wcms;

		if ( 'invoice' === $type ) {
			$wcms->order->display_order_shipping_addresses( $order );
		}
	}
	/**
	 * Hide Shipping Address heading when there are multiple addresses.
	 *
	 * @since 3.6.13
	 * @param bool      $show_shipping_address show shipping address.
	 * @param string    $type current action running on Document.
	 * @param \WC_Order $order order object.
	 * @return bool
	 */
	public function maybe_show_top_shipping_from_pip( $show_shipping_address, $type, $order ) {

		if ( 'invoice' === $type && $this->has_multiple_shipping_and_multiple_packages( $order ) ) {
			return false;
		}
		return true;
	}
	/**
	 * True if the order contains multiple addresses and packages.
	 *
	 * @since 3.6.13
	 * @param \WC_Order $order order object.
	 * @return bool
	 */
	private function has_multiple_shipping_and_multiple_packages( $order ) {

		$order_id          = WC_MS_Compatibility::get_order_prop( $order, 'id' );
		$multiple_shipping = get_post_meta( $order_id, '_multiple_shipping', true );

		if ( $order instanceof WC_Order ) {
			$order_id = WC_MS_Compatibility::get_order_prop( $order, 'id' );
		} else {
			$order_id = $order;
			$order    = wc_get_order( $order );
		}
		$packages = get_post_meta( $order_id, '_wcms_packages', true );

		if ( 'yes' === $multiple_shipping && $packages && count( $packages ) > 1 ) {
			return true;
		}
		return false;
	}
}
