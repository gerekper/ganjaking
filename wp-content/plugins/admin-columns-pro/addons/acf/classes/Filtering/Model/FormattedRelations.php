<?php

namespace ACA\ACF\Filtering\Model;

use AC;
use ACP;

class FormattedRelations extends ACP\Filtering\Model\Meta {

	public function __construct( AC\Column\Meta $column ) {
		parent::__construct( $column, true );
	}

	public function get_filtering_data() {
		$values = $this->get_meta_values_unserialized();
		$options = [];

		foreach ( $values as $value ) {
			foreach ( (array) $value as $user_id ) {
				$options[ $user_id ] = $this->column->get_formatted_value( $user_id );
			}
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}