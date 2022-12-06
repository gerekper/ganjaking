<?php

namespace ACP\Editing\Service;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class SerializedMeta implements Service, Editability {

	/**
	 * @var Storage\Meta
	 */
	private $storage;

	/**
	 * @var array
	 */
	private $keys;

	public function __construct( Storage\Meta $storage, array $keys ) {
		$this->storage = $storage;
		$this->keys = $keys;
	}

	public function get_view( string $context ): ?View {
		return new View\Text();
	}

	public function is_editable( int $id ): bool {
		$value = $this->storage->get( $id );

		if ( is_array( $value ) ) {
			$value = ac_helper()->array->get_nested_value( $value, $this->keys );
		}

		return ! ( $value && ! is_scalar( $value ) );
	}

	public function get_not_editable_reason( int $id ): string {
		return __( 'Data must be `scalar`.', 'codepress-admin-columns' );
	}

	public function get_value( int $id ) {
		$value = $this->storage->get( $id );

		if ( $value && is_array( $value ) ) {
			$value = ac_helper()->array->get_nested_value( $value, $this->keys );
		}

		return $value ?: false;
	}

	private function is_integer( $string ): bool {
		return strval( (int) $string ) === $string;
	}

	private function maybe_cast_value( $value ) {
		if ( $this->is_integer( $value ) ) {
			$value = (int) $value;
		}

		return $value;
	}

	public function update( int $id, $data ): void {
		$values = ac_helper()->array->add_nested_value(
			$this->keys,
			$this->maybe_cast_value( $data ),
			$this->storage->get( $id ) ?: []
		);

		$this->storage->update( $id, $values );
	}

}