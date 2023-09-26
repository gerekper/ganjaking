<?php

namespace ACA\ACF;

class FieldsFactory {

	/**
	 * @param array $field
	 *
	 * @return array All fields (incl. subfields from grouped or cloned)
	 */
	public function create( array $field ) {
		switch ( $field['type'] ) {
			case FieldType::TYPE_GROUP:
				return $this->get_fields_from_group( $field );

			case FieldType::TYPE_CLONE:
				return (array) $field['sub_fields'];

			default:
				return [ $field ];
		}
	}

	private function get_fields_from_group( array $field ) {
		$fields = [];

		foreach ( $field['sub_fields'] as $sub_field ) {
			$group_field = $field;
			$group_field['key'] = sprintf( '%s-%s', $field['key'], $sub_field['key'] );
			$group_field['label'] = sprintf( '%s - %s', $field['label'], $sub_field['label'] );

			unset( $group_field['sub_fields'] );

			$fields[] = $group_field;
		}

		return $fields;
	}

}