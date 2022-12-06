<?php

namespace ACA\WC\Filtering\ShopCoupon;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\ShopCoupon\Type $column
 */
class Type extends ACP\Filtering\Model\Meta {

	public function __construct( Column\ShopCoupon\Type $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_data() {
		$values = $this->get_meta_values();

		if ( ! $values ) {
			return [];
		}

		$options = [];

		$types = wc_get_coupon_types();
		foreach ( $values as $type ) {
			$options[ $type ] = $types[ $type ];
		}

		return [
			'options' => $options,
		];
	}

}