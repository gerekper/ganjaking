<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;

class Date extends Model\CustomField {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'date' );
	}

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			$options[ $value ] = $this->column->get_formatted_value( $value );
		}

		krsort( $options );

		return [
			'empty_option' => true,
			'order'        => false,
			'options'      => $options,
		];
	}

}