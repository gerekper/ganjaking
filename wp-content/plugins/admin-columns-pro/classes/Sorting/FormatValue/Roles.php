<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Roles implements FormatValue {

	private function get_role_label( $capability ) {
		global $wp_roles;

		return isset( $wp_roles->roles[ $capability ] )
			? translate_user_role( $wp_roles->roles[ $capability ]['name'] )
			: false;
	}

	public function format_value( $value ) {
		$caps = maybe_unserialize( $value );

		if ( ! $caps || ! is_array( $caps ) ) {
			return false;
		}

		$capabilities = array_keys( array_filter( $caps ) );

		foreach ( $capabilities as $capability ) {
			$role = $this->get_role_label( $capability );

			if ( $role ) {
				return $role;
			}
		}

		return false;
	}

}
