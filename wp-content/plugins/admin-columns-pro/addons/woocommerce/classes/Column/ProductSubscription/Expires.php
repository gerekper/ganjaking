<?php

namespace ACA\WC\Column\ProductSubscription;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;
use WC_Product;
use WC_Product_Subscription;

/**
 * @since 3.4
 */
class Expires extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Sorting\Sortable {

	public function __construct() {
		$this->set_type( 'column-wc-subscription-expires' )
		     ->set_label( __( 'Expire after', 'woocommerce-subscriptions' ) )
		     ->set_group( 'woocommerce_subscriptions' );
	}

	public function get_meta_key() {
		return '_subscription_length';
	}

	public function get_value( $id ) {
		$value = $this->get_period_label( wc_get_product( $id ) );

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
	protected function get_period_label( WC_Product $product ) {
		if ( ! $product instanceof WC_Product_Subscription ) {
			return null;
		}

		$length = $product->get_meta( $this->get_meta_key() );

		if ( ! $length ) {
			return null;
		}

		$ranges = (array) wcs_get_subscription_ranges( $product->get_meta( '_subscription_period' ) );

		if ( ! array_key_exists( $length, $ranges ) ) {
			return null;
		}

		return ucfirst( $ranges[ $length ] );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function editing() {
		return new Editing\ProductSubscription\Expires();
	}

}