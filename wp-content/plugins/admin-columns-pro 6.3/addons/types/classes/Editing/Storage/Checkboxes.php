<?php

namespace ACA\Types\Editing\Storage;

use AC\MetaType;
use ACP;

class Checkboxes extends ACP\Editing\Storage\Meta {

	/**
	 * @var array
	 */
	private $options;

	public function __construct( $meta_key, MetaType $meta_type, $options ) {
		parent::__construct( $meta_key, $meta_type );

		$this->options = $options;
	}

	public function get( $id ) {
		return array_values( array_filter( array_map( [ $this, 'get_single_value' ], (array) parent::get( $id ) ) ) );
	}

	private function get_single_value( $value ) {
		return is_array( $value ) && count( $value ) > 0 ? $value[0] : null;
	}

	public function update( int $id, $data ): bool {
		$values = [];

		foreach ( $this->options as $key => $option ) {
			if ( in_array( $option['set_value'], $data ) ) {
				$values[ $key ][] = $option['set_value'];
			}
		}

		return parent::update( $id, $values );
	}

}