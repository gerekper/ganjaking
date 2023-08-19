<?php

namespace ACA\WC\Column\ProductSubscription;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;
use WC_Product;
use WC_Product_Subscription;

/**
 * @since 3.4
 */
class LimitSubscription extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-subscription-limit' )
		     ->set_label( __( 'Limit subscription', 'woocommerce-subscriptions' ) )
		     ->set_group( 'woocommerce_subscriptions' );
	}

	public function get_meta_key() {
		return '_subscription_limit';
	}

	public function get_value( $product_id ) {
		$value = $this->get_limit_label( wc_get_product( $product_id ) );

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
	protected function get_limit_label( WC_Product $product ) {
		if ( ! $product instanceof WC_Product_Subscription ) {
			return null;
		}

		$limit = $product->get_meta( $this->get_meta_key() );

		if ( ! $limit ) {
			return null;
		}

		$options = $this->get_limit_options();

		if ( ! array_key_exists( $limit, $options ) ) {
			return null;
		}

		return $options[ $limit ];
	}

	private function get_limit_options() {
		return [
			'no'     => __( 'Do not limit', 'woocommerce-subscriptions' ),
			'active' => __( 'Limit to one active subscription', 'woocommerce-subscriptions' ),
			'any'    => __( 'Limit to one of any status', 'woocommerce-subscriptions' ),
		];
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function editing() {
		return new Editing\ProductSubscription\Limit( $this->get_limit_options() );
	}

	public function search() {
		return new Search\ProductSubscription\Options( $this->get_meta_key(), $this->get_limit_options() );
	}

}