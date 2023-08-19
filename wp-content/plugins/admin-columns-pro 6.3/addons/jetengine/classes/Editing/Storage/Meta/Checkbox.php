<?php

namespace ACA\JetEngine\Editing\Storage\Meta;

use AC\MetaType;
use ACA\JetEngine\Utils\FieldOptions;
use ACP;

class Checkbox extends ACP\Editing\Storage\Meta {

	/**
	 * @var array
	 */
	private $choices;

	/**
	 * @var boolean
	 */
	private $store_array;

	public function __construct( $meta_key, MetaType $meta_type, array $choices, $store_array = false ) {
		parent::__construct( $meta_key, $meta_type );

		$this->choices = $choices;
		$this->store_array = (bool) $store_array;
	}

	public function get( int $id ) {
		$value = parent::get( $id );

		if ( empty( $value ) ) {
			return false;
		}

		return $this->store_array
			? $value
			: FieldOptions::get_checked_options( (array) $value );
	}

	public function update( int $id, $data ): bool {
		$save_value = [];

		if ( $this->store_array || empty( $data ) ) {
			return parent::update( $id, $data );
		}

		$checked_values = array_intersect( array_keys( $this->choices ), $data );

		foreach ( $this->choices as $key => $label ) {
			$save_value[ $key ] = in_array( $key, $checked_values ) ? 'true' : 'false';
		}

		return parent::update( $id, $save_value );
	}

}