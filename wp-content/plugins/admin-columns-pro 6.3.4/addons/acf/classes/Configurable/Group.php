<?php

namespace ACA\ACF\Configurable;

use ACA\ACF\Configurable;
use ACA\ACF\FieldFactory;
use ACA\ACF\GroupColumnFactory;

class Group implements Configurable {

	/**
	 * @var FieldFactory
	 */
	private $field_factory;

	public function __construct( FieldFactory $field_factory ) {
		$this->field_factory = $field_factory;
	}

	private function get_group_field_key_by_type( $column_type ) {
		$column_type = str_replace( GroupColumnFactory::GROUP_PREFIX, '', $column_type );

		$parts = explode( '-', $column_type );

		return $parts[0];
	}

	private function get_sub_field_key_by_type( $column_type ) {
		$column_type = str_replace( GroupColumnFactory::GROUP_PREFIX, '', $column_type );

		$parts = explode( '-', $column_type );

		return $parts[1];
	}

	public function create( $column_type ) {

		$group_field_key = $this->get_group_field_key_by_type( $column_type );
		$sub_field_key = $this->get_sub_field_key_by_type( $column_type );

		$group_settings = acf_get_field( $group_field_key );
		$sub_field_settings = acf_get_field( $sub_field_key );

		if ( ! $group_settings || ! $sub_field_settings ) {
			return null;
		}

		$group_field = $this->field_factory->create( $group_settings );
		$sub_field = $this->field_factory->create( $sub_field_settings );

		$meta_key = sprintf( '%s_%s', $group_field->get_meta_key(), $sub_field->get_meta_key() );

		return [
			self::FIELD      => $sub_field,
			self::FIELD_TYPE => $sub_field->get_type(),
			self::META_KEY   => $meta_key,
			self::FIELD_HASH => $sub_field_key,
		];
	}

}