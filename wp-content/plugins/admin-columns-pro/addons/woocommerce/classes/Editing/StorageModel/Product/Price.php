<?php

namespace ACA\WC\Editing\StorageModel\Product;

use ACA\WC\Editing\EditValue;
use ACA\WC\Helper\Price\Rounding;
use WC_Product;
use WP_Error;

class Price {

	/**
	 * @var WC_Product
	 */
	protected $product;

	/**
	 * @var EditValue\Product\Price
	 */
	protected $value;

	/**
	 * @var Rounding
	 */
	protected $rounding;

	public function __construct( WC_Product $product, EditValue\Product\Price $value ) {
		$this->product = $product;
		$this->value = $value;
		$this->rounding = new Rounding();
	}

	/**
	 * @return int|WP_Error
	 */
	public function save() {
		$price = $this->get_calculated_price();

		if ( $price <= 0 ) {
			return new WP_Error( 'invalid-price', __( 'Price can not be zero or lower.', 'codepress-admin-columns' ) );
		}

		if ( $this->value->is_rounded() || 'flat' !== $this->value->get_price_type() ) {
			$price = $this->round_price( $price );
		}

		$this->product->set_regular_price( $price );

		return $this->product->save();
	}

	/**
	 * @param float $price
	 *
	 * @return float
	 */
	protected function round_price( $price ) {

		switch ( $this->value->get_rounding_type() ) {
			case 'roundup':
				return $this->rounding->up( $price, $this->value->get_rounding_decimals() );

			case 'rounddown':
				return $this->rounding->down( $price, $this->value->get_rounding_decimals() );

			default:
				return round( $price, apply_filters( 'acp/wc/editing/price/rounding_decimals', 2 ) );
		}
	}

	/**
	 * @return float
	 */
	protected function get_calculated_price() {
		switch ( $this->value->get_price_type() ) {
			case 'increase_percentage':
				return $this->increase_price_by_percentage( (float) $this->get_price(), $this->value->get_percentage() );

			case 'decrease_percentage':
				return $this->decrease_price_by_percentage( (float) $this->get_price(), $this->value->get_percentage() );

			case 'increase_price':
				return ( $this->get_price() + (float) $this->value->get_price() );

			case 'decrease_price':
				return ( $this->get_price() - (float) $this->value->get_price() );

			default:
				return $this->value->get_price();
		}
	}

	/**
	 * @param float $price
	 * @param float $percentage
	 *
	 * @return float
	 */
	protected function increase_price_by_percentage( $price, $percentage ) {
		return $price + ( ( $price / 100 ) * $percentage );
	}

	/**
	 * @param float $price
	 * @param float $percentage
	 *
	 * @return float
	 */
	protected function decrease_price_by_percentage( $price, $percentage ) {
		return $price - ( ( $price / 100 ) * $percentage );
	}

	/**
	 * @return float
	 */
	protected function get_price() {
		return (float) $this->product->get_regular_price();
	}

}