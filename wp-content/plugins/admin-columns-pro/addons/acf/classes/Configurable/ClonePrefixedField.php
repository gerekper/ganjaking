<?php

namespace ACA\ACF\Configurable;

use ACA\ACF\CloneColumnFactory;
use ACA\ACF\Configurable;
use ACA\ACF\FieldFactory;

final class ClonePrefixedField implements Configurable {

	/**
	 * @var FieldFactory
	 */
	private $field_factory;

	public function __construct( FieldFactory $field_factory ) {
		$this->field_factory = $field_factory;
	}

	private function remove_prefix( $column_type ) {
		return str_replace( CloneColumnFactory::CLONE_PREFIX, '', $column_type );
	}

	private function get_clone_hash( $column_type ) {
		$column_type = $this->remove_prefix( $column_type );

		$key_parts = explode( '_', $column_type );

		return sprintf( '%s_%s', $key_parts[0], $key_parts[1] );
	}

	private function get_field_hash( $column_type ) {
		$column_type = $this->remove_prefix( $column_type );

		$key_parts = explode( '_', $column_type );

		return sprintf( '%s_%s', $key_parts[2], $key_parts[3] );
	}

	public function create( $column_type ) {
		$clone_hash = $this->get_clone_hash( $column_type );
		$field_hash = $this->get_field_hash( $column_type );

		$clone_settings = acf_get_field( $clone_hash );
		$field_settings = acf_get_field( $field_hash );

		if ( ! $clone_settings || ! $field_settings ) {
			return null;
		}

		$clone_field = $this->field_factory->create( $clone_settings );
		$field = $this->field_factory->create( $field_settings );

		$meta_key = sprintf( '%s_%s', $clone_field->get_meta_key(), $field->get_meta_key() );

		return [
			self::FIELD      => $field,
			self::FIELD_TYPE => $field->get_type(),
			self::META_KEY   => $meta_key,
			self::FIELD_HASH => $field_hash,
		];
	}

}