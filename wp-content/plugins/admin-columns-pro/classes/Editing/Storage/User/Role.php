<?php

namespace ACP\Editing\Storage\User;

use ACP\Editing\Storage;
use ACP\RolesFactory;
use WP_User;

class Role implements Storage {

	/**
	 * @var bool $allow_non_editable_rows
	 */
	private $allow_non_editable_rows;

	public function __construct( bool $allow_non_editable_rows ) {
		$this->allow_non_editable_rows = $allow_non_editable_rows;
	}

	public function get( int $id ) {
		$roles = ac_helper()->user->get_user_field( 'roles', $id );

		if ( ! $roles || ! is_array( $roles ) ) {
			return false;
		}

		return array_values( array_filter( $roles, [ $this, 'is_editable_role' ] ) );
	}

	private function is_editable_role( string $role ): bool {
		$editable_roles = ( new RolesFactory() )->create( $this->allow_non_editable_rows );

		return in_array( $role, $editable_roles, true );
	}

	private function is_not_editable_role( string $role ): bool {
		return ! $this->is_editable_role( $role );
	}

	public function update( int $id, $data ): bool {
		$params = $data;

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

						// prevent the removal of existing non-editable roles
						$non_editable_roles = array_values( array_filter( $user->roles, [ $this, 'is_not_editable_role' ] ) );

						$user->set_role( array_pop( $roles ) );
						$this->add_roles( $user, array_merge( $roles, $non_editable_roles ) );
					}
			}
		}

		return true;
	}

	private function add_roles( WP_User $user, $roles ) {
		array_map( [ $user, 'add_role' ], $roles );
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