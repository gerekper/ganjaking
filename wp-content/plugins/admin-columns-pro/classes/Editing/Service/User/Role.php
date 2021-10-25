<?php

namespace ACP\Editing\Service\User;

use AC\Request;
use ACP\Editing;

class Role implements Editing\Service {

	public function get_view( $context ) {
		$options = [];

		if ( function_exists( 'get_editable_roles' ) ) {
			foreach ( get_editable_roles() as $k => $role ) {
				$options[ $k ] = translate_user_role( $role['name'] );
			}
		}

		return ( new Editing\View\AdvancedSelect( $options ) )
			->set_clear_button( false )
			->set_multiple( true );
	}

	public function get_value( $id ) {
		if ( ! current_user_can( 'promote_user', $id ) ) {
			return null;
		}

		$roles = ac_helper()->user->get_user_field( 'roles', $id );

		if ( ! $roles || ! is_array( $roles ) ) {
			return false;
		}

		return $roles;
	}

	public function update( Request $request ) {
		$id = (int) $request->get( 'id' );
		$roles = $request->get( 'value' );

		if ( current_user_can( 'edit_users' ) && current_user_can( 'promote_user', $id ) ) {

			// prevent the removal of your own admin role
			if ( current_user_can( 'administrator' ) && get_current_user_id() == $id ) {
				$roles[] = 'administrator';
			}

			$user = get_user_by( 'id', $id );

			if ( ! empty( $roles ) ) {
				$user->set_role( array_pop( $roles ) );

				foreach ( $roles as $key ) {
					$user->add_role( $key );
				}
			} else {
				foreach ( $user->roles as $role ) {
					$user->remove_role( $role );
				}
			}
		}
	}
}