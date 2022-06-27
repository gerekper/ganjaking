<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\Storage\Meta;
use ACP\Editing\View;
use RuntimeException;

class SerializedMeta implements Service {

	/**
	 * @var Meta
	 */
	private $storage;

	/**
	 * @var array
	 */
	private $keys;

	public function __construct( Meta $storage, array $keys ) {
		$this->storage = $storage;
		$this->keys = $keys;
	}

	public function get_view( $context ) {
		return new View\Text();
	}

	public function get_value( $id ) {
		$value = $this->storage->get( $id );

		if ( empty( $value ) ) {
			return false;
		}

		$values = ac_helper()->array->get_nested_value( $value, $this->keys );

		// Only scaler values are editable
		return $values && is_scalar( $values )
			? $values
			: null;
	}

	private function is_integer( $string ) {
		return strval( (int) $string ) === $string;
	}

	public function update( Request $request ) {
		$value = $request->get( 'value' );

		if ( ! is_scalar( $value ) ) {
			throw new RuntimeException( 'Invalid value.' );
		}

		if ( $this->is_integer( $value ) ) {
			$value = (int) $value;
		}

		$id = (int) $request->get( 'id' );

		$values = $this->storage->get( $id );

		if ( $values && ! is_array( $values ) ) {
			throw new RuntimeException( 'Currently stored data is not an array.' );
		}

		$values = ac_helper()->array->add_nested_value( $this->keys, $value, $values ?: [] );

		$this->storage->update( $id, $values );
	}

}