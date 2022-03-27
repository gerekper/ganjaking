<?php

namespace ACP\Editing\Service\User;

use AC\Request;
use ACP\Editing;
use WP_User;

class Role implements Editing\Service {

	public function get_view( $context ) {
		$options = [];

		if ( function_exists( 'get_editable_roles' ) ) {
			foreach ( get_editable_roles() as $k => $role ) {
				$options[ $k ] = translate_user_role( $role['name'] );
			}
		}

		asort( $options );

		$view = ( new Editing\View\AdvancedSelect( $options ) )
			->set_clear_button( false )
			->set_multiple( true );

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true );
		}

		return $view;
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
		$params = $request->get( 'value' );

		if ( ! isset( $params['method'] ) ) {
			$params = [
				'method' => 'replace',
				'value'  => $params,
			];
		}

		$user = get_user_by( 'id', $id );
		$roles = $params['value'];

		if ( current_user_can( 'edit_users' ) && current_user_can( 'promote_user', $id ) ) {

			switch ( $params['method'] ) {
				case 'add':
					$this->add_roles( $user, $roles );

					break;
				case 'remove':
					$this->remove_roles( $user, $roles );

					break;
				default:

					if ( empty( $roles ) ) {
						foreach ( $user->roles as $role ) {
							$user->remove_role( $role );
						}
					} else {
						// prevent the removal of your own admin role
						if ( current_user_can( 'administrator' ) && get_current_user_id() === $id ) {
							$roles[] = 'administrator';
						}

						$user->set_role( array_pop( $roles ) );
						$this->add_roles( $user, $roles );
					}
			}
		}

	}

	private function add_roles( WP_User $user, $roles ) {
		foreach ( $roles as $key ) {
			$user->add_role( $key );
		}
	}

	private function remove_roles( WP_User $user, $roles ) {
		if ( current_user_can( 'administrator' ) && get_current_user_id() == $user->ID ) {
			$key = array_search( 'administrator', $roles );

			if ( $key !== false ) {
				unset( $roles[ $key ] );
			}
		}

		foreach ( $roles as $key ) {
			$user->remove_role( $key );
		}
	}

}