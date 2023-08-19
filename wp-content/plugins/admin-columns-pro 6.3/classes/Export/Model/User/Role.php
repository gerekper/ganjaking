<?php

namespace ACP\Export\Model\User;

use ACP\Export\Service;
use ACP\RolesFactory;

class Role implements Service {

	private $allow_all_roles;

	/**
	 * @var RolesFactory
	 */
	private $roles_factory;

	public function __construct( bool $allow_all_roles ) {
		$this->allow_all_roles = $allow_all_roles;
		$this->roles_factory = new RolesFactory();
	}

	private function is_site_role( $role ) {
		$roles = $this->roles_factory->create( $this->allow_all_roles );

		return in_array( $role, $roles, true );
	}

	public function get_value( $id ) {
		$user = get_userdata( $id );

		$roles = $user
			? array_filter( $user->roles, [ $this, 'is_site_role' ] )
			: [];

		return implode(
			', ',
			ac_helper()->user->translate_roles( $roles )
		);
	}

}