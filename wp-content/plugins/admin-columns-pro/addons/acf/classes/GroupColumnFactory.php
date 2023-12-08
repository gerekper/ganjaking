<?php

namespace ACA\ACF;

class GroupColumnFactory {

	public const GROUP_PREFIX = 'acfgroup__';

	private $column_factory;

	public function __construct( ColumnFactory $column_factory ) {
		$this->column_factory = $column_factory;
	}

	public function create( array $settings ): ?Column {
		$parts = explode( '-', $settings['key'] );
		$group_field = acf_get_field( $parts[0] );
		$sub_field = acf_get_field( $parts[1] );

		if ( ! $group_field || ! $sub_field || ! isset( $sub_field['type'] ) ) {
			return null;
		}

		// Add prefix for the correct column config
		$sub_field['key'] = self::GROUP_PREFIX . $settings['key'];

		// Add group label
		$sub_field['label'] = sprintf( '%s - %s', $group_field['label'], $sub_field['label'] );

		if ( $sub_field['type'] === 'group' ) {
			return null;
		}

		return $this->column_factory->create( $sub_field );
	}

}