<?php

namespace ACP\Editing\Model\User;

use ACP\Editing\Model;

class Role extends Model {

	public function get_edit_value( $id ) {
		if ( ! current_user_can( 'promote_user', $id ) ) {
			return null;
		}

		$roles = ac_helper()->user->get_user_field( 'roles', $id );

		if ( ! $roles || ! is_array( $roles ) ) {
			return false;
		}

		return $roles;
	}

	public function get_view_settings() {
		$options = [];
		if ( $_roles = get_editable_roles() ) {
			foreach ( $_roles as $k => $role ) {
				$options[ $k ] = translate_user_role( $role['name'] );
			}
		}

		return [
			'type'         => 'select2_dropdown',
			'multiple'     => true,
			'options'      => $options,
			'clear_button' => true,
		];
	}

	public function save( $id, $roles ) {

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

		return true;
	}

}