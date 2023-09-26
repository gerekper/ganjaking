<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACA\WC\Column;
use ACA\WC\Filtering\ShopOrder;

/**
 * @property Column\ShopOrder\Status $column
 */
class Status extends ShopOrder {

	public function __construct( Column\ShopOrder\Status $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_vars( $vars ) {
		$vars['post_status'] = ( strpos( $this->get_filter_value(), 'wc-' ) === 0 ) ? $this->get_filter_value() : 'wc-' . $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => $this->column->get_order_status_options(),
		];
	}

}