<?php

namespace ACA\WC\Settings\ShopOrder;

use ACA\WC;
use ACA\WC\Column;
use ACA\WC\Field\ShopOrder;

/**
 * @since 3.0
 * @property Column\ShopOrder\OrderDate $column
 */
class OrderDate extends WC\Settings\DateType {

	public function __construct( Column\ShopOrder\OrderDate $column ) {
		parent::__construct( $column );
	}

	/**
	 * @return array
	 */
	protected function get_display_options() {
		$options = [];

		foreach ( $this->column->get_fields() as $field ) {
			/** @var ShopOrder\OrderDate $field */
			$options[ $field->get_key() ] = $field->get_label();
		}

		asort( $options );

		return $options;
	}

}