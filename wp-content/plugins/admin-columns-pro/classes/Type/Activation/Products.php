<?php

namespace ACP\Type\Activation;

class Products {

	/**
	 * @var array e.g. `ac-addon-acf`
	 */
	private $product_slugs;

	public function __construct( array $product_slugs ) {
		$this->product_slugs = $product_slugs;
	}

	/**
	 * @return array
	 */
	public function get_value() {
		return $this->product_slugs;
	}

}