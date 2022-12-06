<?php

namespace ACA\WC\Editing\EditValue\Product;

class SalePrice extends Price {

	/**
	 * @var bool
	 */
	private $price_based_on_regular;

	/**
	 * @var bool
	 */
	private $scheduled;

	/**
	 * @var string
	 */
	private $schedule_from;

	/**
	 * @var string
	 */
	private $schedule_to;

	/**
	 * @param array $value
	 */
	public function __construct( $value ) {
		parent::__construct( $value );

		$this->price_based_on_regular = $value['price']['based_on_regular'] === 'true';
		$this->scheduled = $value['schedule']['active'] === 'true';

		if ( $this->scheduled ) {
			$this->schedule_from = $value['schedule']['from'];
			$this->schedule_to = $value['schedule']['to'];
		}
	}

	/**
	 * @return bool
	 */
	public function is_price_based_on_regular() {
		return $this->price_based_on_regular;
	}

	/**
	 * @return bool
	 */
	public function is_scheduled() {
		return $this->scheduled;
	}

	/**
	 * @return string
	 */
	public function get_schedule_from() {
		return $this->schedule_from;
	}

	/**
	 * @return string
	 */
	public function get_schedule_to() {
		return $this->schedule_to;
	}

}