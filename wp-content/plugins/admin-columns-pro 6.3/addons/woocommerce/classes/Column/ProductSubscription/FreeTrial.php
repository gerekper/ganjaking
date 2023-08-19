<?php

namespace ACA\WC\Column\ProductSubscription;

use AC;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;
use WC_Product;
use WC_Product_Subscription;

/**
 * @since 3.4
 */
class FreeTrial extends AC\Column implements Formattable {

	use ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-subscription-free_trial' )
		     ->set_label( __( 'Free trial', 'woocommerce-subscriptions' ) )
		     ->set_group( 'woocommerce_subscriptions' );
	}

	public function get_value( $id ) {
		$value = $this->get_trial_label( wc_get_product( $id ) );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return string|null
	 */
	private function get_trial_label( WC_Product $product ) {
		if ( ! $product instanceof WC_Product_Subscription ) {
			return null;
		}

		$length = (int) $product->get_meta( '_subscription_trial_length' );

		if ( $length < 1 ) {
			return null;
		}

		$period = $product->get_meta( '_subscription_trial_period' );

		$periods = wcs_get_available_time_periods( 1 === $length ? 'singular' : 'plural' );

		if ( ! array_key_exists( $period, $periods ) ) {
			return null;
		}

		return sprintf( '%d %s', $length, $periods[ $period ] );
	}

}