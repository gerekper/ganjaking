<?php

namespace ACA\ACF\Configurable;

use ACA\ACF\Configurable;
use ACA\ACF\FieldFactory;

final class Column implements Configurable {

	/**
	 * @var FieldFactory
	 */
	private $field_factory;

	public function __construct( FieldFactory $field_factory ) {
		$this->field_factory = $field_factory;
	}

	public function create( $column_type ) {
		$settings = acf_get_field( $column_type );

		if ( ! $settings ) {
			return null;
		}

		$field = $this->field_factory->create( $settings );

		return [
			self::FIELD      => $field,
			self::FIELD_TYPE => $field->get_type(),
			self::META_KEY   => $field->get_meta_key(),
			self::FIELD_HASH => $field->get_hash(),
		];
	}

}