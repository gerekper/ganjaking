<?php

namespace ACA\ACF;

class Helper {

	/**
	 * @param string $field_hash
	 *
	 * @return string|null
	 */
	public function get_field_edit_link( $field_hash ) {
		$group = $this->get_field_group( $field_hash );

		if ( empty( $group['ID'] ) ) {
			return null;
		}

		return acf_get_field_group_edit_link( $group['ID'] );
	}

	/**
	 * @param string $field_hash
	 *
	 * @return array|null
	 */
	public function get_field_group( $field_hash ) {
		$field = acf_get_field( $field_hash );

		if ( empty( $field['parent'] ) ) {
			return null;
		}

		if ( ! function_exists( 'acf_get_raw_field_group' ) ) {
			return null;
		}

		$group = acf_get_raw_field_group( $field['parent'] );

		if ( ! $group ) {
			return $this->get_field_group( $field['parent'] );
		}

		return $group;
	}

}