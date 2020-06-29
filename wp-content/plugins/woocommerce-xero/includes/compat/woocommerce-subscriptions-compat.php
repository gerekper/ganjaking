<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compatibility class for WooCommerce Subscriptions
 */
class Woocommerce_Subscriptions_Compat extends WC_XR_Invoice_Manager {
	/**
	 * Constructor
	 */
	public function __construct( WC_XR_Settings $settings ) {
		parent::__construct( $settings );

		$this->settings = $settings;

		add_filter( 'wcs_new_order_created', array( $this, 'order_created' ), 10, 3 );
	}

	public function order_created( $order, $subscription, $type ) {
		$option = $this->settings->get_option( 'send_invoices' );

		if ( 'creation' === $option ) {
			$this->send_invoice( version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->id : $order->get_id() );
		}

		return $order;
	}
}
