<?php

namespace ACA\EC\Filtering\Event\Field;

use ACA\EC\Column;
use ACP;

/**
 * @property Column\Event\Field $column
 */
class Checkbox extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		return [
			'options'      => $this->get_options(),
			'empty_option' => true,
		];
	}

	public function get_filtering_vars( $vars ) {
		$vars['meta_query'][] = [
			'key'     => $this->column->get_meta_key(),
			'value'   => $this->get_filter_value(),
			'compare' => 'LIKE',
		];

		return $this->get_filtering_vars_empty_nonempty( $vars );
	}

	/**
	 * @return array
	 */
	private function get_options() {
		$options = explode( "\r\n", $this->column->get( 'values' ) );

		return array_combine( $options, $options );
	}

}