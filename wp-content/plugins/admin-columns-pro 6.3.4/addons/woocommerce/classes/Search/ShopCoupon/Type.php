<?php

namespace ACA\WC\Search\ShopCoupon;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Type extends Comparison\Meta
	implements Comparison\Values {

	/** @var array [ $key => $label ] */
	private $types;

	public function __construct( $types ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		$this->types = $types;

		parent::__construct( $operators, 'discount_type', MetaType::POST );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( $this->types );
	}

}