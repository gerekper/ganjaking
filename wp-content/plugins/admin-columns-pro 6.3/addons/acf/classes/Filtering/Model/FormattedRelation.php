<?php

namespace ACA\ACF\Filtering\Model;

use ACP;

class FormattedRelation extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$values = $this->get_meta_values();
		$options = [];

		foreach ( $values as $value ) {
			$options[ $value ] = $this->column->get_formatted_value( $value );
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}