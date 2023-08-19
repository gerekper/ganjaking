<?php
declare( strict_types=1 );

namespace ACP;

class RolesFactory {

	public function create( bool $allow_non_editable_roles = false ): array {
		switch ( $allow_non_editable_roles ) {
			case true:
				if ( ! function_exists( 'wp_roles' ) ) {
					return [];
				}

				return array_keys( wp_roles()->roles );
			default:
				if ( ! function_exists( 'get_editable_roles' ) ) {
					return [];
				}

				return array_keys( get_editable_roles() );
		}
	}

}