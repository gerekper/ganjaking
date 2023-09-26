<?php

namespace ACA\WC\Editing\EditValue\Product;

class Price {

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $price_type;

	/**
	 * @var string
	 */
	private $price;

	/**
	 * @var string
	 */
	private $percentage;

	/**
	 * @var bool
	 */
	private $rounding;

	/**
	 * @var string
	 */
	private $rounding_type;

	/**
	 * @var int
	 */
	private $rounding_decimals;

	/**
	 * @param array $value
	 */
	public function __construct( $value ) {
		$this->type = $value['type'];
		$this->price_type = $value['price']['type'];
		$this->price = $value['price']['value'];
		$this->percentage = (float) $value['price']['value'];
		$this->rounding = $value['rounding']['active'] === 'true';

		if ( $this->rounding ) {
			$this->rounding_type = $value['rounding']['type'];
			$this->rounding_decimals = absint( $value['rounding']['decimals'] );
		}
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function get_price_type() {
		return $this->price_type;
	}

	/**
	 * @return string
	 */
	public function get_price() {
		return $this->price;
	}

	/**
	 * @return float
	 */
	public function get_percentage() {
		return $this->percentage;
	}

	/**
	 * @return bool
	 */
	public function is_rounded() {
		return $this->rounding;
	}

	/**
	 * @return string
	 */
	public function get_rounding_type() {
		return $this->rounding_type;
	}

	/**
	 * @return int
	 */
	public function get_rounding_decimals() {
		return $this->rounding_decimals;
	}

}