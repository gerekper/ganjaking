<?php

namespace ACA\WC\Editing\StorageModel\Product;

use ACA\WC\Editing\EditValue;
use WC_Product;
use WP_Error;

/**
 * @property EditValue\Product\SalePrice $value
 */
class SalePrice extends Price {

	public function __construct( WC_Product $product, EditValue\Product\SalePrice $value ) {
		parent::__construct( $product, $value );
	}

	/**
	 * @return int|WP_Error
	 */
	public function save() {
		if ( 'clear' === $this->value->get_price_type() || ( 'flat' === $this->value->get_price_type() && in_array( $this->value->get_price(), [ '', '0' ], true ) ) ) {
			return $this->remove_sale_price();
		}

		$price = $this->get_calculated_price();

		if ( $price <= 0 ) {
			return new WP_Error( 'invalid-price', __( 'Sale price can not be zero or lower.', 'codepress-admin-columns' ) );
		}

		if ( $price >= (float) $this->product->get_regular_price() ) {
			return new WP_Error( 'invalid-price', __( 'Sale price can not be higher than the regular price.', 'codepress-admin-columns' ) );
		}

		if ( $this->value->is_rounded() || 'flat' !== $this->value->get_price_type() ) {
			$price = $this->round_price( $price );
		}

		$this->product->set_sale_price( $price );

		if ( $this->value->is_scheduled() ) {
			$this->product->set_date_on_sale_from( $this->value->get_schedule_from() );
			$this->product->set_date_on_sale_to( $this->value->get_schedule_to() );
		}

		return $this->product->save();
	}

	/**
	 * @return int
	 */
	private function remove_sale_price() {
		$this->product->set_sale_price( false );

		return $this->product->save();
	}

	/**
	 * @return string
	 */
	public function get_price() {
		return $this->value->is_price_based_on_regular()
			? $this->product->get_regular_price()
			: $this->product->get_sale_price();
	}

}