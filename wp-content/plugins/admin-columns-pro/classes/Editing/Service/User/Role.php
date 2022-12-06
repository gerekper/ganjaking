<?php

namespace ACP\Editing\Service\User;

use ACP\Editing;
use ACP\Editing\Service;
use ACP\Editing\Service\Editability;
use ACP\Editing\View;
use ACP\RolesFactory;
use ACP\Service\Storage;

class Role implements Service, Editability {

	/**
	 * By default, WordPress does not allow you to edit certain (3rd party) roles
	 * @var bool
	 */
	private $allow_non_editable_roles;

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( bool $allow_non_editable_roles ) {
		$this->allow_non_editable_roles = $allow_non_editable_roles;
		$this->storage = new Editing\Storage\User\Role( $allow_non_editable_roles );
	}

	public function get_view( string $context ): ?View {
		$view = new Editing\View\AdvancedSelect( $this->get_editable_roles() );
		$view->set_clear_button( false )
		     ->set_multiple( true );

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true );
		}

		return $view;
	}

	public function get_not_editable_reason( int $id ): string {
		return __( 'Current user can not change user role.', 'codepress-admin-columns' );
	}

	public function get_value( int $id ) {
		return $this->storage->get( $id );
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $data );
	}

	public function is_editable( int $id ): bool {
		return current_user_can( 'edit_users' ) && current_user_can( 'promote_user', $id );
	}

	private function get_translated_role_name( string $role ) {
		$role_names = wp_roles()->role_names;
		$role_name = $role_names[ $role ] ?? null;

		return $role_name
			? translate_user_role( $role_name )
			: $role;
	}

	private function get_editable_roles() {
		$options = [];

		$editable_roles = ( new RolesFactory() )->create( $this->allow_non_editable_roles );

		foreach ( $editable_roles as $role ) {
			$options[ $role ] = $this->get_translated_role_name( $role );
		}

		asort( $options );

		return $options;
	}

}